<?php
/**
 * Contains the controller class for the basic actions of the module, like login
 * and logout.
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
use nickcv\usermanager\forms\LoginForm;


/**
 * Contains the core actions of the module like login and logout.
 * 
 * @author Nicola Puddu <n.puddu@outlook.com>
 * @version 1.0
 * 
 * @property \nickcv\usermanager\Module $module
 */
class DefaultController extends Controller
{
    public $defaultAction = 'login';
    
    /**
     * Creates an Admin user for the usermanager module.
     */
    public function actionLogin()
    {
        $model = new LoginForm;
        
        return $this->render('login', [
            'model' => $model,
        ]);
    }
    
}
