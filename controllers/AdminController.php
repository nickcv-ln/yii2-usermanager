<?php
/**
 * Contains the controller class for the module administration.
 * 
 * @link http://www.creationgears.com/
 * @copyright Copyright (c) 2015 Nicola Puddu
 * @license http://www.gnu.org/copyleft/gpl.html
 * @package nickcv/yii2-usermanager
 * @author Nicola Puddu <n.puddu@outlook.com>
 */
namespace nickcv\usermanager\controllers;

use yii\web\Controller;
use nickcv\usermanager\enums\Scenarios;
use nickcv\usermanager\models\User;

/**
 * Creates an Admin user for the usermanager module.
 * 
 * @author Nicola Puddu <n.puddu@outlook.com>
 * @version 1.0
 * 
 * @property \nickcv\usermanager\Module $module
 */
class AdminController extends Controller
{
    
    
    
    /**
     * Creates an Admin user for the usermanager module.
     */
    public function actionIndex()
    {
        echo 'ciao';
    }
    
}