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
use nickcv\usermanager\models\User;

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
        
        $this->stdout("\n");
            
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
        $this->createAdmin();
        
        return;
        $configFile = \Yii::getAlias('@app').DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'encrypter.php';
        file_put_contents($configFile, $this->getConfigFileContent());
        $this->stdout("\nconfig file generated in '$configFile'.", Console::FG_GREEN);
        $this->stdout("\nadd this line in your web config file inside the components array:");
        echo $this->ansiFormat("\n\t'encrypter' => require(__DIR__ . DIRECTORY_SEPARATOR . 'encrypter.php'),\n", Console::BOLD, Console::FG_PURPLE);
    }
    
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
    
    private function createAdmin()
    {
        $admin = new User(['scenario' => Scenarios::ADMIN_CREATION]);
        
        $requiredField = [
            'required' => true,
            'error' => 'Required field.'
        ];
        
        $admin->firstname = $this->prompt("\nFirstname:", $requiredField);
        $admin->lastname = $this->prompt("\nLastname:", $requiredField);
        $admin->email = $this->prompt("\nEmail:", $requiredField);
        $admin->password = $this->prompt("\nPassword:", $requiredField);
        
        if (!$admin->validate())
            $this->printOutValidationErrors($admin->errors);
        
        
    }
    
    /**
     * Prints on screen the list of errors returned by a Model.
     * 
     * @param array $errors the error list
     */
    private function printOutValidationErrors($errors)
    {
        echo $this->ansiFormat("\n\nthe following errors occurred:\n", Console::BOLD, Console::FG_RED);
        foreach ($errors as $attribute) {
            foreach ($attribute as $message) {
                $this->stdout("\n\t - " . $message, Console::FG_RED);
            }
        }
        
        $this->stdout("\n\n");
        
        \Yii::$app->end(1);
    }
    
    /**
     * Returns the content of the config file that will be generated in the
     * config directory.
     * 
     * @return string the config file content
     */
    private function getConfigFileContent()
    {
        $view = new View();
        return $view->renderFile(__DIR__ .DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'config.php', [
            'password'=>$this->getRandomPassword(),
            'iv'=>$this->getRandomPassword(\nickcv\encrypter\components\Encrypter::IV_LENGTH),
        ], $this);
    }
    
    /**
     * Returns a randomly generated string with uppercase letter, lowercase
     * letters, numbers and special characters.
     * 
     * @param integer $length length of the randomly generated string
     * @return string random string
     */
    private function getRandomPassword($length = 12)
    {
        $stringWithNoSpecialChars = substr(str_shuffle(MD5(microtime())), 0, $length - 3);
        
        return str_shuffle(str_shuffle($stringWithNoSpecialChars.$this->getSpecialCharacters()));
    }
    
    /**
     * Returns a random selection of 3 special characters
     * 
     * @return string 3 special characters
     */
    private function getSpecialCharacters()
    {
        $specialCharacters = '!-_?.:;,/';
        
        return substr(str_shuffle($specialCharacters), 0, 3);   
    }
}
