<?php
namespace Vtk13\Cc\Controller;

use Vtk13\Cc\AuthenticatedController;
use Vtk13\Mvc\Http\RedirectResponse;

class ShopsController extends AuthenticatedController
{
    public function __construct()
    {
        parent::__construct('shops');
    }

    protected function isAuthRequired($action)
    {
        return in_array($action, ['add']);
    }

    public function listGET()
    {
        return [
            'shops' => $this->db->select('SELECT * FROM shops'),
        ];
    }

    public function addGET()
    {

    }

    public function addPOST()
    {
        $this->db->insert('shops', [
            'address_id'    => $_POST['address_id'],
            'title'         => $_POST['title'],
        ]);
        return new RedirectResponse('/shops/list');
    }
}
