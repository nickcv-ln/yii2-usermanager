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
use yii\filters\AccessControl;
use yii\data\ArrayDataProvider;
use nickcv\usermanager\Module;
use nickcv\usermanager\helpers\AuthHelper;
use nickcv\usermanager\forms\ConfigurationForm;
use nickcv\usermanager\forms\PermissionForm;
use nickcv\usermanager\forms\RoleForm;
use nickcv\usermanager\enums\Permissions;
use nickcv\usermanager\enums\Scenarios;
use nickcv\usermanager\services\ConfigFilesService;

/**
 * Controller class containing the actions for the core module administration.
 * 
 * @author Nicola Puddu <n.puddu@outlook.com>
 * @version 1.0
 * 
 * @property \nickcv\usermanager\Module $module
 */
class AdminController extends Controller
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
                    'configuration',
                    'roles',
                    'view-role',
                    'add-existing-permission',
                    'add-new-permission',
                    'revoke-permission',
                    'add-existing-role',
                    'add-new-role',
                    'revoke-role',
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['configuration'],
                        'roles' => [Permissions::MODULE_MANAGEMENT],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['roles', 'view-role'],
                        'roles' => [Permissions::ROLES_MANAGEMENT],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['add-existing-permission', 'add-existing-role'],
                        'verbs' => ['PUT'],
                        'roles' => [Permissions::ROLES_MANAGEMENT],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['add-new-permission', 'add-new-role'],
                        'verbs' => ['POST'],
                        'roles' => [Permissions::ROLES_MANAGEMENT],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['revoke-permission', 'revoke-role'],
                        'verbs' => ['DELETE'],
                        'roles' => [Permissions::ROLES_MANAGEMENT],
                    ],
                ],
            ],
        ];
    }
    
    public $defaultAction = 'configuration';

    /**
     * Updates the current module configuration.
     */
    public function actionConfiguration()
    {
        $model = new ConfigurationForm();
        $model->attributes = ConfigFilesService::init()->getConfigFile(Module::CONFIG_FILENAME);
        if ($model->load(\Yii::$app->request->post()) && $model->validate() && ConfigFilesService::init()->updateFile(Module::CONFIG_FILENAME, $model->getDefinedAttributesAsconstants(), true)) {
            \Yii::$app->session->setFlash('success', 'Configuration updated.');
            return $this->refresh();
        }
        return $this->render('configuration', [
            'model' => $model,
        ]);
    }
    
    /**
     * Lets you manage the existing application Roles.
     */
    public function actionRoles()
    {
        return $this->render('roles', [
            'roles' => new ArrayDataProvider([   
                'allModels' => \Yii::$app->authManager->getRoles(),
            ]),
            'roleForm' => new RoleForm(['scenario' => Scenarios::ROLE_NEW]),
        ]);
    }
    
    /**
     * Display the existing permission for a role and lets you add some others.
     * 
     * @param string $role the role name
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionViewRole($role)
    {
        if (\Yii::$app->authManager->getRole($role) === null) {
            throw new \yii\web\NotFoundHttpException('The given role was not found within this application.');
        }
        
        return $this->render('rolesView', [
            'permissionForm' => new PermissionForm(['role' => $role]),
            'directPermissions' => AuthHelper::getDirectPermissions($role, true),
            'childrenRoles' => AuthHelper::getChildrenRoles($role, true),
            'roleForm' => new RoleForm(['parentRole' => $role]),
        ]);
    }
    
    /**
     * Adds existing permissions to given role.
     */
    public function actionAddExistingPermission()
    {
        $model = new PermissionForm(['scenario' => Scenarios::PERMISSION_ADD]);
        if ($model->load(\Yii::$app->request->post()) && $model->addExistingPermissions()) {
            \Yii::$app->session->setFlash('success', 'The following permissions have been added to this role: ' . implode(', ', $model->existingPermissions));
        } else {
            \Yii::$app->session->setFlash('error', [
                'message' => 'The existing permission could have not been added for the following reasons:',
                'errors' => $model->getFirstErrors(),
            ]);
        }
        
        return $this->redirect(['admin/roles/' . $model->role]);
    }
    
    /**
     * Creates a new permission and adds it to the given role.
     */
    public function actionAddNewPermission()
    {
        $model = new PermissionForm(['scenario' => Scenarios::PERMISSION_NEW]);
        if ($model->load(\Yii::$app->request->post()) && $model->createNewPermission()) {
            \Yii::$app->session->setFlash('success', 'The permission "' . $model->name . '" was created and added to this role.');
        } else {
            \Yii::$app->session->setFlash('error', [
                'message' => 'The new permission could have not been created for the following reasons:',
                'errors' => $model->getFirstErrors(),
            ]);
        }
        
        return $this->redirect(['admin/roles/' . $model->role]);
    }
    
    /**
     * Remove permission from given role
     */
    public function actionRevokePermission()
    {
        $model = new PermissionForm(['scenario' => Scenarios::PERMISSION_DELETE]);
        if ($model->load(\Yii::$app->request->post()) && $model->removePermission()) {
            \Yii::$app->session->setFlash('success', 'The permission "' . $model->name . '" was removed from this role.');
        } else {
            \Yii::$app->session->setFlash('error', [
                'message' => 'The permission could have not been revoked for the following reasons:',
                'errors' => $model->getFirstErrors(),
            ]);
        }
        
        return $this->redirect(['admin/roles/' . $model->role]);
    }
    
    /**
     * Creates a new role.
     */
    public function actionAddNewRole()
    {
        $model = new RoleForm(['scenario' => Scenarios::ROLE_NEW]);
        if ($model->load(\Yii::$app->request->post()) && $model->createNewRole()) {
            \Yii::$app->session->setFlash('success', 'The role "' . $model->name . '" has been created.');
        } else {
            \Yii::$app->session->setFlash('error', [
                'message' => 'The new role could have not been created for the following reasons:',
                'errors' => $model->getFirstErrors(),
            ]);
        }
        
        return $this->redirect(['admin/roles']);
    }
    
    /**
     * Adds existing permissions to given role.
     */
    public function actionAddExistingRole()
    {
        $model = new RoleForm(['scenario' => Scenarios::ROLE_ADD]);
        if ($model->load(\Yii::$app->request->post()) && $model->addToParentRole()) {
            \Yii::$app->session->setFlash('success', 'The role "' . $model->name . '" is now a child of the current role.');
        } else {
            \Yii::$app->session->setFlash('error', [
                'message' => 'The role could have not been added for the following reasons:',
                'errors' => $model->getFirstErrors(),
            ]);
        }
        
        return $this->redirect(['admin/roles/' . $model->parentRole]);
    }
    
    /**
     * Remove permission from given role
     */
    public function actionRevokeRole()
    {
        $model = new RoleForm(['scenario' => Scenarios::ROLE_DELETE]);
        if ($model->load(\Yii::$app->request->post()) && $model->removeChildRole()) {
            \Yii::$app->session->setFlash('success', 'The role "' . $model->name . '" is not a child of this role anymore.');
        } else {
            \Yii::$app->session->setFlash('error', [
                'message' => 'The role could have not been revoked for the following reasons:',
                'errors' => $model->getFirstErrors(),
            ]);
        }
        
        return $this->redirect(['admin/roles/' . $model->parentRole]);
    }

}
