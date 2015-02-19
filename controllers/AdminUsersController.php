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
use nickcv\usermanager\enums\Permissions;
use nickcv\usermanager\models\User;
use nickcv\usermanager\models\UserSearch;

/**
 * Controller class containing the actions for the user administration.
 * 
 * @author Nicola Puddu <n.puddu@outlook.com>
 * @version 1.0
 * 
 * @property \nickcv\usermanager\Module $module
 */
class AdminUsersController extends Controller
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
                    'update'
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index','update'],
                        'roles' => [Permissions::USER_MANAGEMENT],
                    ],
                ],
            ],
        ];
    }
    
    public function actionIndex()
    {
        $model = new UserSearch();
        $dataProvider = $model->search(\Yii::$app->request->queryParams);
        
        return $this->render('index', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionUpdate($id)
    {
        
    }
    
    public function actionView($id)
    {
        $model = User::find(['id' => $id])->with(['logs'])->one();
        
        if (!$model) {
            throw new \yii\web\NotFoundHttpException('user not found');
        }
        
        return $this->render('view', [
            'model' => $model
        ]);
    }

}
