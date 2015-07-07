<?php
namespace Vtk13\Cc\Controller;

use Vtk13\Cc\AuthenticatedController;
use Vtk13\Mvc\Http\JsonResponse;

class AjaxController extends AuthenticatedController
{
    public function __construct()
    {
        parent::__construct('ajax');
    }

    public function goodsGET()
    {
        $q = $this->db->escape($_GET['q']);
        return new JsonResponse([
            'goods' => $this->db->select("SELECT * FROM goods WHERE title LIKE '%{$q}%'"),
        ]);
    }

    public function shopsGET()
    {
        $q = $this->db->escape($_GET['q']);
        return new JsonResponse([
            'shops' => $this->db->select("SELECT * FROM shops WHERE title LIKE '%{$q}%'"),
        ]);
    }
}
