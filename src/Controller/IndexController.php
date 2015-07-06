<?php
namespace Vtk13\Cc\Controller;

use Vtk13\Cc\BaseController;
use Vtk13\Mvc\Http\RedirectResponse;

class IndexController extends BaseController
{
    public function __construct()
    {
        parent::__construct('index');
    }

    public function indexGET()
    {
//        if (isset($_SESSION['userId'])) {
//            return new RedirectResponse('/');
//        } else {
//            return null;
//        }
    }
}
