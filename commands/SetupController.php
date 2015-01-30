<?php
/**
 * Contains the controller class triggered by the ```./yii usermanager```
 * console command.
 * 
 * @link http://www.creationgears.com/
 * @copyright Copyright (c) 2015 Nicola Puddu
 * @license http://www.gnu.org/copyleft/gpl.html
 * @package nickcv/yii2-usermanager
 * @author Nicola Puddu <n.puddu@outlook.com>
 */
namespace nickcv\usermanager\commands;

use yii\console\Controller;
use yii\web\View;
use yii\helpers\Console;
use nickcv\usermanager\enums\Permissions;
use nickcv\usermanager\enums\Roles;
use nickcv\usermanager\enums\Scenarios;
use nickcv\usermanager\helpers\StringHelper;
use nickcv\usermanager\models\User;
use nickcv\usermanager\services\ConfigFilesService;

/**
 * This command is used to manage the usermanager module.
 * 
 * @author Nicola Puddu <n.puddu@outlook.com>
 * @version 1.0
 */
class SetupController extends Controller
{
    public $defaultAction = 'help';
    
    /**
     * This action displays the list of commands available to manage the module.
     */
    public function actionHelp()
    {
        $this->stdout("\nthe following commands are available for ", Console::BOLD);
        echo $this->ansiFormat('./yii usermanager', Console::FG_CYAN, Console::BOLD);
        $this->stdout(":\n\n", Console::BOLD);
        
        $this->stdout(' - ');
        $this->stdout('usermanager/help', Console::FG_YELLOW);
        $this->stdout("\t\t".'Display this list.'."\n");
        
        $this->stdout(' - ');
        $this->stdout('usermanager/install', Console::FG_YELLOW);
        $this->stdout("\t\t".'Install the module.'."\n");
        
        $this->stdout(' - ');
        $this->stdout('usermanager/create-admin', Console::FG_YELLOW);
        $this->stdout("\t".'Creates an admin.'."\n");
        
        $this->stdout("\n");
        return 0;
    }
    
    /**
     * This action installs the module.
     */
    public function actionInstall()
    {
        echo $this->ansiFormat("\n" . $this->id . " installation\n\n", Console::BOLD, Console::FG_YELLOW);
        
        try {
            $this->installDatabaseTables();
        } catch (\Exception $exc) {
            echo $this->ansiFormat("An error occurred while trying to install the database tables.\n", Console::BOLD, Console::FG_RED);
            $this->stdout("\n\t" . $exc->getMessage() . "\n", Console::FG_RED);
            echo $this->ansiFormat("\nPlease check your database configuration in the db.php file.\n\n", Console::BOLD, Console::FG_RED);
            \Yii::$app->end(1);
        }

        $this->createRoles();
        $this->createConfigFile();
        return 0;
    }
    
    /**
     * This action creates an admin.
     */
    public function actionCreateAdmin()
    {
        $this->stdout("\nAdmin Creation.\n", Console::FG_YELLOW);
        $admin = new User(['scenario' => Scenarios::ADMIN_CREATION]);
        
        $requiredField = [
            'required' => true,
            'error' => 'Required field.'
        ];
        
        $admin->firstname = $this->prompt("\nFirstname:", $requiredField);
        $admin->lastname = $this->prompt("Lastname:", $requiredField);
        $admin->email = $this->prompt("Email:", $requiredField);
        $admin->password = $this->prompt("Password:", $requiredField);
        
        if (!$admin->validate()) {
            $additionalMessage = $this->ansiFormat("\nTo create an admin simply run the ./yii usermanager/create-admin command\n\n", Console::FG_CYAN);
            $this->printOutValidationErrors($admin->errors, $additionalMessage);
        }
            
        
        $admin->save();
        
        return 0;
    }
    
    /**
     * Installs the database tables using migration
     */
    private function installDatabaseTables()
    {
        if ($this->confirm("install RBAC tables", true)) {
            $this->stdout("\nInstalling RBAC tables.\n", Console::FG_YELLOW);
            
            \Yii::$app->runAction('migrate', [
                'migrationPath' => '@yii/rbac/migrations',
                'interactive' => false,
            ]);
            
        }
        
        $this->stdout("\nInstalling user tables.\n", Console::FG_YELLOW);
        
        \Yii::$app->runAction('migrate', [
            'migrationPath' => '@nickcv/migrations',
            'interactive' => false,
        ]);
    }
    
    /**
     * Creates the roles inside the database.
     */
    private function createRoles()
    {
        $this->stdout("\nCreating Roles and Permissions.\n", Console::FG_YELLOW);
        
        $auth = \Yii::$app->authManager;
        
        if (($auth instanceof \yii\rbac\DbManager) === false) {
            echo $this->ansiFormat("\n" . 'The authManager component should be an instance of "yii\rbac\DbManager", "' . get_class($auth) . '" given.'."\n", Console::BOLD, Console::FG_RED);
            $this->stdout("Please use the following configuration for the component both on the web.php and console.php config file:\n\n");
            echo $this->ansiFormat("\t'authManager' => [\n\t\t'class' => 'yii\\rbac\\DbManager',\n\t],\n\n", Console::FG_PURPLE, Console::BOLD);
            \Yii::$app->end(1);
        }
        
        $moduleManagement = $auth->getPermission(Permissions::MODULE_MANAGEMENT);
        
        if ($moduleManagement === null) {
            $moduleManagement = $auth->createPermission(Permissions::MODULE_MANAGEMENT);
            $moduleManagement->description = 'Module management';
            $auth->add($moduleManagement);
        }
        
        $usersManagement = $auth->getPermission(Permissions::USER_MANAGEMENT);
        
        if ($usersManagement === null) {
            $usersManagement = $auth->createPermission(Permissions::USER_MANAGEMENT);
            $usersManagement->description = 'Users management';
            $auth->add($usersManagement);
        }
        
        $rolesManagement = $auth->getPermission(Permissions::ROLES_MANAGEMENT);
        
        if ($rolesManagement === null) {
            $rolesManagement = $auth->createPermission(Permissions::ROLES_MANAGEMENT);
            $rolesManagement->description = 'Roles Management';
            $auth->add($rolesManagement);
        }
        
        if ($auth->getRole(Roles::STANDARD_USER) === null) {
            $standardUser = $auth->createRole(Roles::STANDARD_USER);
            $auth->add($standardUser);
        }

        if ($auth->getRole(Roles::ADMIN) === null) {
            $admin = $auth->createRole(Roles::ADMIN);
            $auth->add($admin);
            $auth->addChild($admin, $moduleManagement);
            $auth->addChild($admin, $usersManagement);
            $auth->addChild($admin, $rolesManagement);
        }
        
        $this->stdout("\nBasic Roles and Permissions created.", Console::FG_GREEN);
    }
    
    /**
     * Creates the configuration file to be used in the app.
     */
    private function createConfigFile()
    {
        
        $this->stdout("\nCreating the config file.\n", Console::FG_YELLOW);
        
        $filename = 'usermanager.php';
        
        $data = [
            'class'=>'\nickcv\usermanager\Module',
            'salt' => StringHelper::randomString(),
        ];
        
        if (ConfigFilesService::init()->createFile($filename, $data) === false) {
            if (strpos(ConfigFilesService::init()->errors()['message'], 'already exists') !== false) {
                $this->updateConfigFile($filename, $data);
            } else {
                $this->stdout("\n" . ConfigFilesService::init()->errors()['details']['message'], Console::FG_RED);
                \Yii::$app->end(1);
            }
        }
        
        $this->printConfigFileSuccessMessage($filename);
    }
    
    /**
     * Update the existing configuration file.
     * 
     * @param string $filename
     * @param array $data
     */
    private function updateConfigFile($filename, $data)
    {
        $this->stdout("\nThe configuration file already exists, but it can be updated.");
        $this->stdout("\nIf you desire to continue the existing file will be analized and you'll be notified if any data is going to be overwritten.", Console::BOLD);
        if ($this->confirm("\nDo you wish to continue?") === false) {
            \Yii::$app->end();
        }
        
        if (ConfigFilesService::init()->updateFile($filename, $data) === false) {
            $this->stdout("\nThe following keys will be edited:\n" . print_r(ConfigFilesService::init()->errors()['details'], true));
            if ($this->confirm("\nDo you wish to continue?") === false) {
                \Yii::$app->end();
            }
            
            ConfigFilesService::init()->updateFile($filename, $data, true);
        }
        
        $this->printConfigFileSuccessMessage($filename);
        
        \Yii::$app->end();
    }
    
    /**
     * Print out the istruction on how to embed the configuration file in the
     * app.
     */
    private function printConfigFileSuccessMessage($filename)
    {
        $filePath = ConfigFilesService::init()->getPath($filename);
        $this->stdout("\nconfig file generated in '$filePath'.", Console::FG_GREEN);
        $this->stdout("\nadd this line in your web config file inside the components array and update the console config file as well:");
        echo $this->ansiFormat("\n\t'usermanager' => require(__DIR__ . DIRECTORY_SEPARATOR . 'usermanager.php'),\n\n", Console::BOLD, Console::FG_PURPLE);
    }
    
    /**
     * Prints on screen the list of errors returned by a Model.
     * 
     * @param array $errors the error list
     */
    private function printOutValidationErrors($errors, $additionalMessage = null)
    {
        echo $this->ansiFormat("\n\nthe following errors occurred:\n", Console::BOLD, Console::FG_RED);
        foreach ($errors as $attribute) {
            foreach ($attribute as $message) {
                $this->stdout("\n\t - " . $message, Console::FG_RED);
            }
        }
        
        $this->stdout("\n\n");
        
        if ($additionalMessage) {
            echo $additionalMessage;
        }
        
        \Yii::$app->end(1);
    }
}
