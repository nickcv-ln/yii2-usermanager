<?php
/**
 * Contains the controller class triggered by the ```./yii usermanager/create-admin```
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
use nickcv\usermanager\enums\Scenarios;
use nickcv\usermanager\models\User;
use nickcv\usermanager\enums\Roles;

/**
 * Creates an Admin user for the usermanager module.
 * 
 * @author Nicola Puddu <n.puddu@outlook.com>
 * @version 1.0
 * 
 * @property \nickcv\usermanager\Module $module
 */
class CreateAdminController extends Controller
{
    
    /**
     * @var \nickcv\usermanager\models\User 
     */
    private $_admin;
    
    /**
     * Creates an Admin user for the usermanager module.
     */
    public function actionIndex()
    {
        $this->stdout("\nAdmin Creation.\n\n", Console::FG_YELLOW);
        
        $this->_admin = new User(['scenario' => Scenarios::ADMIN_CREATION]);
        
        $this->_admin->role = $this->confirm('Should this user be able to manage the whole module and not just the users?') ? Roles::SUPER_ADMIN : Roles::ADMIN;
        
        $this->requestInputs([
            'firstname',
            'lastname',
            'email',
            'password',
        ]);
        
        $this->_admin->save();
        $this->stdout("\nuser '" . $this->_admin->email . "' created.\n\n", Console::FG_GREEN);
        return 0;
    }
    
    private function requestInputs(array $inputs)
    {
        $requiredField = [
            'required' => true,
            'error' => 'Required field.'
        ];
        
        foreach ($inputs as $attribute) {
            $label = $this->_admin->getAttributeLabel($attribute);
            $this->_admin->$attribute = $this->prompt("\n$label:", $requiredField);
        }
        
        if (!$this->_admin->validate()) {
            $this->printOutValidationErrors();
        }
    }
    
    /**
     * Prints on screen the list of errors and requests the user to input
     * the data for the attribute with errors.
     */
    private function printOutValidationErrors()
    {
        echo $this->ansiFormat("\n\nthe following errors occurred:\n", Console::BOLD, Console::FG_RED);
        
        $inputs = [];
        
        foreach ($this->_admin->errors as $attributeName => $attribute) {
            $inputs[] = $attributeName;
            foreach ($attribute as $message) {
                $this->stdout("\n\t - " . $message, Console::FG_RED);
            }
        }
        
        $this->stdout("\n\n");
        
        $this->requestInputs($inputs);
    }
}
