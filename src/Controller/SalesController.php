<?php
namespace Vtk13\Cc\Controller;

use Vtk13\Cc\AuthenticatedController;
use Vtk13\Mvc\Http\RedirectResponse;

class SalesController extends AuthenticatedController
{
    public function __construct()
    {
        parent::__construct('sales');
    }

    public function listGET()
    {
        return [
            'sales' => $this->db->select('SELECT * FROM sales WHERE user_id=' . $this->currentUser['id'])
        ];
    }

    public function addGET()
    {

    }

    public function addPOST()
    {
        $this->db->insert('sales', [
            'good_id'   => $_POST['good_id'],
            'shop_id'   => $_POST['shop_id'],
            'user_id'   => $this->currentUser['id'],
            'timestamp' => time(),
            'cost'      => $_POST['cost'],
            'amount'    => $_POST['amount'],
        ]);
        return new RedirectResponse('/sales/list');
    }
}
