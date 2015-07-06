<?php
namespace Vtk13\Cc;

use Vtk13\Mvc\Http\RedirectResponse;

class AuthenticatedController extends BaseController
{
    protected function beforeHandle($action, $params)
    {
        $res = parent::beforeHandle($action, $params);

        if (!$this->currentUser) {
            $_SESSION['login-redirect'] = $_SERVER['REQUEST_URI'];
            return new RedirectResponse('/auth/google-login');
        } else {
            return $res;
        }
    }
}
