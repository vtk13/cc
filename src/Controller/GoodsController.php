<?php
namespace Vtk13\Cc\Controller;

use Vtk13\Cc\AuthenticatedController;
use Vtk13\Mvc\Http\RedirectResponse;

class GoodsController extends AuthenticatedController
{
    public function __construct()
    {
        parent::__construct('goods');
    }

    protected function isAuthRequired($action)
    {
        return in_array($action, ['add', 'edit']);
    }

    public function listGET()
    {
        return [
            'goods' => $this->db->select('SELECT * FROM goods WHERE level=0'),
        ];
    }

    public function addGET()
    {
        return [
            'units' => $this->db->select('SELECT * FROM units'),
        ];
    }

    public function addPOST()
    {
        $this->db->insert('goods', [
            'bar_code'      => $_POST['bar_code'],
            'title'         => $_POST['title'],
            'packed'        => $_POST['packed'],
            'unit'          => $_POST['unit'],
            'pack_volume'   => $_POST['pack_volume'],
        ]);
        $this->nodeSetParent($this->db->insertId(), isset($_POST['parent_id']) ? $_POST['parent_id'] : 0);
        return new RedirectResponse('/goods/list');
    }

    protected function listCosts($id)
    {
        $id = (int)$id;
        $node = $this->db->selectRow("SELECT * FROM goods WHERE id={$id}");
        if ($node['packed']) {
            return $this->db->select(
                "SELECT b.title, a.timestamp, ROUND(a.cost/a.amount, 2) as pack_cost, ROUND(a.cost/a.amount/sg.pack_volume, 2) as unit_cost
               FROM goods g
                    JOIN sales a ON g.id=a.good_id
                    JOIN goods sg ON a.good_id=sg.id
                    JOIN shops b ON a.shop_id=b.id
              WHERE {$node['node_left']}<=g.node_left AND g.node_right<={$node['node_right']}
           ORDER BY a.timestamp DESC"
            );
        } else {
            return $this->db->select(
                "SELECT b.title, a.timestamp, ROUND(a.cost/a.amount/sg.pack_volume, 2) as unit_cost
               FROM goods g
                    JOIN sales a ON g.id=a.good_id
                    JOIN goods sg ON a.good_id=sg.id
                    JOIN shops b ON a.shop_id=b.id
              WHERE {$node['node_left']}<=g.node_left AND g.node_right<={$node['node_right']}
           ORDER BY a.timestamp DESC"
            );
        }
    }

    public function viewGET($id)
    {
        $id = (int)$id;

        return [
            'good'      => $this->db->selectRow("SELECT * FROM goods WHERE id={$id}"),
            'parents'   => $this->listParents($id),
            'children'  => $this->listChildren($id),
            'costs'     => $this->listCosts($id),
        ];
    }

    public function editGET($id)
    {
        $id = (int)$id;

        $parents = $this->listParents($id);
        if ($parents) {
            $parent = array_pop($parents);
            $parent['selected'] = true;
            array_push($parents, $parent);
        }

        return [
            'good'      => $this->db->selectRow('SELECT * FROM goods WHERE id=' . (int)$id),
            'units'     => $this->db->select('SELECT * FROM units'),
            'parents'   => $parents,
        ];
    }

    public function editPOST($id)
    {
        $id = (int)$id;
        $data = [
            'bar_code'      => $_POST['bar_code'],
            'title'         => $_POST['title'],
            'packed'        => $_POST['packed'],
            'unit'          => $_POST['unit'],
            'pack_volume'   => $_POST['pack_volume'],
        ];
        $this->db->update('goods', $data, "id={$id}");
        $this->nodeSetParent($id, isset($_POST['parent_id']) ? $_POST['parent_id'] : 0);
        return new RedirectResponse('/goods/view/' . $id);
    }

    protected function nodeSetParent($id, $parentId)
    {
        $id = (int)$id;
        $parentId = (int)$parentId;

        $node = $this->db->selectRow('SELECT * FROM goods WHERE id=' . (int)$id);
        switch ($node['node_right'] - $node['node_left']) {
            case 0: // new node, node_right == 0 and node_left == 0
                $this->nodeNewInsert($id, $parentId);
                break;
            case 1: // existent node without children
                $this->db->query('UPDATE goods SET node_left=node_left-2 WHERE node_left>' . (int)$node['node_right']);
                $this->db->query('UPDATE goods SET node_right=node_right-2 WHERE node_right>' . (int)$node['node_right']);
                $this->nodeNewInsert($id, $parentId);
                break;
            default: // existent node with children
                // prepare space
                $parent = $this->db->selectRow("SELECT * FROM goods WHERE id={$parentId}");
                if (empty($parent)) {
                    $parent = [
                        'level'         => 0,
                        'node_right'    => $this->db->selectValue('SELECT MAX(node_right) FROM goods') + 1,
                    ];
                }
                $n = $node['node_right'] - $node['node_left'] + 1; // size of subtree
                $this->db->query("UPDATE goods SET node_left=node_left+{$n} WHERE node_left>={$parent['node_right']}");
                $this->db->query("UPDATE goods SET node_right=node_right+{$n} WHERE node_right>={$parent['node_right']}");
                // fix level (before moving)
                $levelDiff = $node['level'] - $parent['level'] - 1;
                $this->db->query(
                    "UPDATE goods
                        SET level=level-({$levelDiff})
                      WHERE node_left>={$node['node_left']} AND node_right<={$node['node_right']}"
                );
                // move subtree
                $m = $node['node_left'] - $parent['node_right']; // items to shift
                $this->db->query(
                    "UPDATE goods
                        SET node_left=node_left-({$m}), node_right=node_right-({$m})
                      WHERE node_left>={$node['node_left']} AND node_right<={$node['node_right']}"
                );
                // clear old space
                $this->db->query("UPDATE goods SET node_left=node_left-{$n} WHERE node_left>{$node['node_right']}");
                $this->db->query("UPDATE goods SET node_right=node_right-{$n} WHERE node_right>{$node['node_right']}");
                break;
        }
    }

    protected function nodeNewInsert($id, $parentId)
    {
        $id = (int)$id;
        $parentId = (int)$parentId;

        if ($parentId == 0) {
            $max = $this->db->selectValue('SELECT MAX(node_right) FROM goods');
            $data = [
                'node_left'     => $max + 1,
                'node_right'    => $max + 2,
                'level'         => 0,
            ];
            $this->db->update('goods', $data, "id={$id}");
        } else {
            $parent = $this->db->selectRow("SELECT * FROM goods WHERE id={$parentId}");
            $this->db->query("UPDATE goods SET node_left=node_left+2 WHERE node_left>={$parent['node_right']}");
            $this->db->query("UPDATE goods SET node_right=node_right+2 WHERE node_right>={$parent['node_right']}");

            $data = [
                'node_left'     => $parent['node_right'],
                'node_right'    => $parent['node_right'] + 1,
                'level'         => $parent['level'] + 1,
            ];
            $this->db->update('goods', $data, "id={$id}");
        }
    }

    private function nodeRemove($id)
    {
        $node = $this->db->selectRow('SELECT * FROM goods WHERE id=' . (int)$id);
        if (($node['node_right'] - $node['node_left']) == 1) {
            $this->db->query("UPDATE goods SET node_left=node_left-2 WHERE node_left>{$node['node_right']}");
            $this->db->query("UPDATE goods SET node_right=node_right-2 WHERE node_right>{$node['node_right']}");

            $data = [
                'node_left'     => 0,
                'node_right'    => 0,
                'level'         => 0,
            ];
            $this->db->update('goods', $data, "id={$node['id']}");
        } else {
            throw new \Exception('Removing nodes with children not implemented yet');
        }
    }

    protected function listParents($id)
    {
        $node = $this->db->selectRow('SELECT * FROM goods WHERE id=' . (int)$id);
        return $this->db->select(
            "SELECT *
               FROM goods
              WHERE node_left<{$node['node_left']} AND node_right>{$node['node_right']}
           ORDER BY level ASC"
        );
    }

    protected function listChildren($id)
    {
        $node = $this->db->selectRow('SELECT * FROM goods WHERE id=' . (int)$id);
        $level = $node['level'] + 1;
        return $this->db->select(
            "SELECT *
               FROM goods
              WHERE {$node['node_left']}<node_left AND node_right<{$node['node_right']} AND level={$level}
           ORDER BY node_left ASC"
        );
    }
}
