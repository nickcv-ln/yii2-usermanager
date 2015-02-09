<?php
/**
 * Contains the enum class used for the password strength validator.
 * 
 * @link http://www.creationgears.com/
 * @copyright Copyright (c) 2015 Nicola Puddu
 * @license http://www.gnu.org/copyleft/gpl.html
 * @package nickcv/yii2-usermanager
 * @author Nicola Puddu <n.puddu@outlook.com>
 */
namespace nickcv\usermanager\enums;

/**
 * This enums contains a constant for each of the possible password strenght
 * validator settings.
 * Each constant contains a security setting.
 * 
 * @author Nicola Puddu <n.puddu@outlook.com>
 * @version 1.0
 */
class PasswordStrength extends BasicEnum
{
    const WEAK = 1;
    const MEDIUM = 2;
    const STRONG = 3;
    const SECURE = 4;
    
    public static function getLabel($value)
    {
        $labels = [
            self::WEAK => 'Weak',
            self::MEDIUM => 'Medium',
            self::STRONG => 'Strong',
            self::SECURE => 'Secure',
        ];
        
        if (!array_key_exists($value, $labels)) {
            return null;
        }
        
        return $labels[$value] . ' [' . self::getStrengthDescription($value) . ']';
    }
    
    public static function getStrengthDescription($value)
    {
        $description = [
            self::WEAK => '8 characters',
            self::MEDIUM => '8 characters with at least 1 number, and 1 letter',
            self::STRONG => '10 characters with at least 1 number, 1 uppercase letter, 1 lowercase letter',
            self::SECURE => '12 characters with at least 1 number, 1 uppercase letter, 1 lowercase letter, 1 special character',
        ];
        
        if (!array_key_exists($value, $description)) {
            return null;
        }
        
        return $description[$value];
    }
}
