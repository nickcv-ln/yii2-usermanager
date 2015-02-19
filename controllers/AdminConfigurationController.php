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
use nickcv\usermanager\Module;
use nickcv\usermanager\forms\ConfigurationForm;
use nickcv\usermanager\enums\Permissions;
use nickcv\usermanager\services\ConfigFilesService;

/**
 * Controller class containing the actions for the core module administration.
 * 
 * @author Nicola Puddu <n.puddu@outlook.com>
 * @version 1.0
 * 
 * @property \nickcv\usermanager\Module $module
 */
class AdminConfigurationController extends Controller
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
                        'roles' => [Permissions::MODULE_MANAGEMENT],
                    ],
                ],
            ],
        ];
    }

    /**
     * Updates the current module configuration.
     */
    public function actionIndex()
    {
        $model = new ConfigurationForm();
        $model->attributes = ConfigFilesService::init()->getConfigFile(Module::CONFIG_FILENAME);
        if ($model->load(\Yii::$app->request->post()) && $model->validate() && ConfigFilesService::init()->updateFile(Module::CONFIG_FILENAME, $model->getDefinedAttributesAsconstants(), true)) {
            \Yii::$app->session->setFlash('success', 'Configuration updated.');
            return $this->refresh();
        }
        return $this->render('index', [
            'model' => $model,
        ]);
    }

}
