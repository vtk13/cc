<?php
namespace Vtk13\Cc;

use Vtk13\Mvc\Http\RedirectResponse;

abstract class AuthenticatedController extends BaseController
{
    protected function beforeHandle($action, $params)
    {
        $res = parent::beforeHandle($action, $params);

        if (!$this->currentUser && $this->isAuthRequired($action)) {
            $_SESSION['login-redirect'] = $_SERVER['REQUEST_URI'];
            return new RedirectResponse('/auth/google-login');
        } else {
            return $res;
        }
    }

    protected function isAuthRequired($action)
    {
        return true;
    }
}
