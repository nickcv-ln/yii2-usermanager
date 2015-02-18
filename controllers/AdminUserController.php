<?php

/**
 * Contains the controller class for the user administration.
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
use yii\data\ActiveDataProvider;
use nickcv\usermanager\enums\Permissions;
use nickcv\usermanager\models\User;

/**
 * Controller class containing the actions for the user administration.
 * 
 * @author Nicola Puddu <n.puddu@outlook.com>
 * @version 1.0
 * 
 * @property \nickcv\usermanager\Module $module
 */
class AdminUserController extends Controller
{

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
                    'index',
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => [Permissions::USER_MANAGEMENT],
                    ],
                ],
            ],
        ];
    }
    
    public function actionIndex()
    {
        return $this->render('index', [
            'dataProvider' => new ActiveDataProvider([
                'query' => User::find(),
                'pagination' => [
                    'pageSize' => 20,
                ],
            ])
        ]);
    }

}
