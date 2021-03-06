<?php
/**
 * Contains the ConfigurationForm class used to edit the module configuration.
 * 
 * @link http://www.creationgears.com/
 * @copyright Copyright (c) 2015 Nicola Puddu
 * @license http://www.gnu.org/copyleft/gpl.html
 * @package nickcv/yii2-usermanager
 * @author Nicola Puddu <n.puddu@outlook.com>
 */
namespace nickcv\usermanager\forms;

use yii\base\Model;
use nickcv\usermanager\enums\GeneralSettings;
use nickcv\usermanager\enums\PasswordStrength;
use nickcv\usermanager\enums\Registration;
use nickcv\usermanager\helpers\ArrayHelper as AH;

/**
 * ConfigurationForm is the form behind the module configurations.
 * 
 * @author Nicola Puddu <n.puddu@outlook.com>
 * @version 1.0
 */
class ConfigurationForm extends Model
{
    public $passwordStrength;
    public $passwordRecovery;
    public $activation;
    public $registration;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['passwordStrength', 'in', 'range' => PasswordStrength::getList()],
            ['registration', 'in', 'range' => Registration::getList()],
            [['activation', 'passwordRecovery'], 'in', 'range' => GeneralSettings::getList()],
        ];
    }
    
    /**
     * Returns the constant value for each defined attribute.
     * This will be used to recreate the configuration file.
     * 
     * @return array
     */
    public function getDefinedAttributesAsconstants()
    {
        $return = [];
        
        if ($this->passwordStrength !== null) {
            $return['passwordStrength'] = AH::PHP_CONTENT . PasswordStrength::getConstantDeclaration($this->passwordStrength);
        }
        
        if ($this->passwordRecovery !== null) {
            $return['passwordRecovery'] = AH::PHP_CONTENT . GeneralSettings::getConstantDeclaration($this->passwordRecovery);
        }
        
        if ($this->activation !== null) {
            $return['activation'] = AH::PHP_CONTENT . GeneralSettings::getConstantDeclaration($this->activation);
        }
        
        if ($this->registration !== null) {
            $return['registration'] = AH::PHP_CONTENT . Registration::getConstantDeclaration($this->registration);
        }
        
        return $return;
    }
    
}
