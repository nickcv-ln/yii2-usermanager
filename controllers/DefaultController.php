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
use yii\filters\AccessControl;
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
     * Add the AccessControl behavior to the controller.
     * 
     * @return array behaviors in use
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => [
                    'logout',
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['logout'],
                        'verbs' => ['POST'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    
    /**
     * Creates an Admin user for the usermanager module.
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        
        $model = new LoginForm();
        if ($model->load(\Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }
    
    /**
     * Logs out the current user.
     */
    public function actionLogout()
    {
        \Yii::$app->user->logout();

        return $this->goHome();
    }
    
}
