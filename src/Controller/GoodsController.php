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
        return in_array($action, ['add']);
    }

    public function listGET()
    {
        return [
            'goods' => $this->db->select('SELECT * FROM goods'),
        ];
    }

    public function addGET()
    {

    }

    public function addPOST()
    {
        $this->db->insert('goods', [
            'bar_code'  => $_POST['bar_code'],
            'title'     => $_POST['title'],
        ]);
        return new RedirectResponse('/goods/list');
    }
}
