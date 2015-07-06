<?php
namespace Vtk13\Cc;

use Vtk13\LibSql\IDatabase;
use Vtk13\Mvc\Handlers\AbstractController;

abstract class BaseController extends AbstractController
{
    /**
     * @Inject
     * @var IDatabase
     */
    protected $db;

    protected $currentUser;

    protected function beforeHandle($action, $params)
    {
        if (isset($_SESSION['userId'])) {
            $this->currentUser = $this->db->selectRow('SELECT * FROM user WHERE id=' . (int)$_SESSION['userId']);
        }
        return null;
    }

    public function actionResultToResponse($result, $template)
    {
        if (is_null($result) || is_array($result)) {
            $result['currentUser'] = $this->currentUser;
        }
        return parent::actionResultToResponse($result, $template);
    }
}
