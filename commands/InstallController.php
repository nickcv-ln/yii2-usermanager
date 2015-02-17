<?php
/**
 * Contains the controller class triggered by the ```./yii usermanager/install```
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
use yii\helpers\Console;
use nickcv\usermanager\enums\Permissions;
use nickcv\usermanager\enums\Roles;
use nickcv\usermanager\services\ConfigFilesService;
use nickcv\usermanager\enums\PasswordStrength;
use nickcv\usermanager\enums\GeneralSettings;
use nickcv\usermanager\enums\Registration;
use nickcv\usermanager\helpers\ArrayHelper;
use nickcv\usermanager\Module;

/**
 * Installs the usermanager module.
 * 
 * @author Nicola Puddu <n.puddu@outlook.com>
 * @version 1.0
 */
class InstallController extends Controller
{
    
    /**
     * Installs the usermanager module.
     */
    public function actionIndex()
    {
        echo $this->ansiFormat("\n" . $this->id . " installation\n\n", Console::BOLD, Console::FG_YELLOW);
        
        try {
            $this->installDatabaseTables();
        } catch (\Exception $exc) {
            echo $exc->getTraceAsString();
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
            'migrationPath' => '@nickcv/usermanager/migrations',
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
        
        $profileEditing = $auth->getPermission(Permissions::PROFILE_EDITING);
        
        if ($profileEditing === null) {
            $profileEditing = $auth->createPermission(Permissions::PROFILE_EDITING);
            $profileEditing->description = 'Profile Editing';
            $auth->add($profileEditing);
        }
        
        if ($auth->getRole(Roles::STANDARD_USER) === null) {
            $standardUser = $auth->createRole(Roles::STANDARD_USER);
            $standardUser->description = 'Standard User';
            $auth->add($standardUser);
            $auth->addChild($standardUser, $profileEditing);
        }

        if ($auth->getRole(Roles::ADMIN) === null) {
            $admin = $auth->createRole(Roles::ADMIN);
            $admin->description = 'User Admin';
            $auth->add($admin);
            $auth->addChild($admin, $usersManagement);
            $auth->addChild($admin, $standardUser);
        }
        
        if ($auth->getRole(Roles::SUPER_ADMIN) === null) {
            $superAdmin = $auth->createRole(Roles::SUPER_ADMIN);
            $superAdmin->description = 'Usermanager Super Admin';
            $auth->add($superAdmin);
            $auth->addChild($superAdmin, $moduleManagement);
            $auth->addChild($superAdmin, $usersManagement);
            $auth->addChild($superAdmin, $rolesManagement);
            $auth->addChild($superAdmin, $standardUser);
        }
        
        $this->stdout("\nBasic Roles and Permissions created.", Console::FG_GREEN);
    }
    
    /**
     * Creates the configuration file to be used in the app.
     */
    private function createConfigFile()
    {
        
        $this->stdout("\nCreating the config file.\n", Console::FG_YELLOW);
        
        $strength = PasswordStrength::getConstantDeclaration($this->select('Choose the minimum password strength', PasswordStrength::getLabels()));
        
        $registration = Registration::getConstantDeclaration($this->select('Do you want to enable manual user registration?', Registration::getLabels()));
        
        $activation = GeneralSettings::getConstantDeclaration($this->select('Do you want to enable account activation?', GeneralSettings::getLabels()));
        
        $passwordRecovery = GeneralSettings::getConstantDeclaration($this->select('Do you want to enable manual password recovery?', GeneralSettings::getLabels()));
        
        $data = [
            'class'=>'\nickcv\usermanager\Module',
            'passwordStrength' => ArrayHelper::PHP_CONTENT . $strength,
            'registration' => ArrayHelper::PHP_CONTENT . $registration,
            'activation' => ArrayHelper::PHP_CONTENT . $activation,
            'passwordRecovery' => ArrayHelper::PHP_CONTENT . $passwordRecovery,
        ];
        
        if (ConfigFilesService::init()->createFile(Module::CONFIG_FILENAME, $data) === false) {
            if (strpos(ConfigFilesService::init()->errors()['message'], 'already exists') !== false) {
                $this->updateConfigFile($data);
            } else {
                $this->stdout("\n" . ConfigFilesService::init()->errors()['details']['message'], Console::FG_RED);
                \Yii::$app->end(1);
            }
        }
        
        $this->printConfigFileSuccessMessage();
    }
    
    /**
     * Update the existing configuration file.
     *
     * @param array $data
     */
    private function updateConfigFile($data)
    {
        $this->stdout("\nThe configuration file already exists, but it can be updated.");
        $this->stdout("\nIf you desire to continue the existing file will be analized and you'll be notified if any data is going to be overwritten.", Console::BOLD);
        if ($this->confirm("\nDo you wish to continue?") === false) {
            \Yii::$app->end();
        }
        
        if (ConfigFilesService::init()->updateFile(Module::CONFIG_FILENAME, $data) === false) {
            $this->stdout("\nThe following keys will be edited:\n" . print_r(ConfigFilesService::init()->errors()['details'], true));
            if ($this->confirm("\nDo you wish to continue?") === false) {
                \Yii::$app->end();
            }
            
            ConfigFilesService::init()->updateFile(Module::CONFIG_FILENAME, $data, true);
        }
        
        $this->printConfigFileSuccessMessage();
        
        \Yii::$app->end();
    }
    
    /**
     * Print out the istruction on how to embed the configuration file in the
     * app.
     */
    private function printConfigFileSuccessMessage()
    {
        $filePath = ConfigFilesService::init()->getPath(Module::CONFIG_FILENAME);
        $this->stdout("\nconfig file generated in '$filePath'.\n", Console::FG_GREEN);
        $this->stdout("\nupdated the module configuration in your console config file and add it to your web config file:");
        echo $this->ansiFormat("\n\t'usermanager' => require(__DIR__ . DIRECTORY_SEPARATOR . '" . Module::CONFIG_FILENAME . "'),\n\n", Console::BOLD, Console::FG_PURPLE);
        $this->stdout("Afterwards remember to add to the bootstrap array the usermanager module into your web config file.\n\n");
    }
}
