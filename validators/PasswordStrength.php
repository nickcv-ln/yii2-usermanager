<?php
/**
 * Contains the validator class used to validate the password strength.
 * 
 * @link http://www.creationgears.com/
 * @copyright Copyright (c) 2015 Nicola Puddu
 * @license http://www.gnu.org/copyleft/gpl.html
 * @package nickcv/yii2-usermanager
 * @author Nicola Puddu <n.puddu@outlook.com>
 */
namespace nickcv\usermanager\validators;

use yii\validators\Validator;
use nickcv\usermanager\enums\PasswordStrength as PS;

/**
 * This validator is used to check whether the given attribute is passing the
 * current password strength setting.
 * 
 * To change this setting please edit the usermanager.php config file in your
 * application configuration.
 * 
 * The available settings are:
 * WEAK = 8 characters
 * MEDIUM = 8 characters with at least 1 number, and 1 letter
 * STRONG = 10 characters with at least 1 number, 1 uppercase letter, 1 lowercase letter
 * SECURE = 12 characters with at least 1 number, 1 uppercase letter, 1 lowercase letter, 1 special character
 * 
 * The default setting is SECURE.
 * 
 * 
 * @author Nicola Puddu <n.puddu@outlook.com>
 * @version 1.0
 */
class PasswordStrength extends Validator
{
    
    
    public function init()
    {
        parent::init();
        $this->message = 'The password must be of at least ' . PS::getStrengthDescription(\Yii::$app->getModule('usermanager')->passwordStrength) . '.';
    }
    
    /**
     * Validates the password contained in the given attribute against
     * the current module password strength configuration.
     * 
     * @param \yii\base\Model $model
     * @param string $attribute
     * @return boolean
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        $results = [];
        preg_match($this->getValidationRegEx(), $value, $results);
        
        if (!$results) {
            $model->addError($attribute, $this->message);
            return false;
        }
        
        return true;
        
    }
    
    public function clientValidateAttribute($model, $attribute, $view)
    {
        $regex = $this->getValidationRegEx();
        $message = json_encode($this->message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return <<<JS
if (!value.match($regex)) {
    messages.push($message);
}
JS;
    }
    
    private function getValidationRegEx()
    {
        switch (\Yii::$app->getModule('usermanager')->passwordStrength) {
            case PS::WEAK:
                return '/^.{8,}$/';
            case PS::MEDIUM:
                return '/^(?=.*\d)(?=.*[a-zA-Z]).{8,}$/';
            case PS::STRONG:
                return '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{10,}$/';
            case PS::SECURE:
            default:
                return '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{12,}$/';
        }
        
    }
}
