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
use nickcv\usermanager\enums\Scenarios;
use nickcv\usermanager\models\User;
use nickcv\usermanager\models\UserSearch;
use nickcv\usermanager\helpers\AuthHelper;

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
    
    public function actionView($id)
    {
        $model = User::find()->where(['id' => $id])->with(['logs'])->one();
        
        if (!$model) {
            throw new \yii\web\NotFoundHttpException('user not found');
        }
        
        return $this->render('view', [
            'model' => $model,
        ]);
    }
    
    public function actionUpdate($id)
    {
        if (\Yii::$app->user->id == $id) {
            throw new \yii\web\ForbiddenHttpException('You cannot edit your own user.');
        }
        
        $model = User::findOne($id);
        $model->scenario = Scenarios::USER_EDITING;
        $model->password = null;
        
        if (AuthHelper::IsParentRole($model->role, AuthHelper::getUserRoleName(\Yii::$app->user->id))) {
            throw new \yii\web\ForbiddenHttpException('You cannot edit a user with a higher level than yours.');
        }
        
        if (!$model) {
            throw new \yii\web\NotFoundHttpException('user not found');
        }
        
        if ($model->load(\Yii::$app->request->post()) && $model->validate())
        {
            
        }
        
        return $this->render('update', [
            'model' => $model,
        ]);
    }

}
