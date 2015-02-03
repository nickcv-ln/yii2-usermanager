<?php
/**
 * Contains the basic enum abstract class that every other enum extends.
 * 
 * @link http://www.creationgears.com/
 * @copyright Copyright (c) 2015 Nicola Puddu
 * @license http://www.gnu.org/copyleft/gpl.html
 * @package nickcv/yii2-usermanager
 * @author Nicola Puddu <n.puddu@outlook.com>
 */
namespace nickcv\usermanager\enums;

/**
 * This class provides the methods to easily retrieve the enums constants list
 * and get the label of a constant value.
 * 
 * @author Nicola Puddu <n.puddu@outlook.com>
 * @version 1.0
 */
abstract class BasicEnum
{
    private static $_constants = [];
    
    /**
     * Returns the list of constants within the called class.
     * 
     * @return array list of constants
     */
    final public static function getList()
    {
        return self::getConstantsOfCalledClass();
    }
    
    /**
     * Returns the label of a constant value.
     * If not overridden this method will return the constant name.
     * 
     * @param string $value the constant value
     * @return string the constant label
     */
    public static function getLabel($value)
    {
        return array_search($value, self::getList());
    }
    
    /**
     * Checks whether the called class has a constant with the given value.
     * 
     * @param mixed $value
     * @return boolean
     */
    final public static function hasConstantWithValue($value)
    {
        if (array_search($value, self::getList()) === false) {
            return false;
        }
        
        return true;
    }
    
    final public static function getConstantDeclaration($value)
    {
        $key = array_search($value, self::getList());
        
        if (!$key) {
            return null;
        }
        
        return '\\' . get_called_class() . '::' . $key;
    }
    
    /**
     * Retrieve the list of constant for the called class from the
     * private static array. If the array does not contain the list for the
     * called class a reflection class will be used to retrieve it.
     * 
     * @return array list of constants
     */
    private static function getConstantsOfCalledClass()
    {
        $calledClass = get_called_class();
        
        if (!array_key_exists($calledClass, self::$_constants)) {
            $reflectionClass = new \ReflectionClass($calledClass);
            self::$_constants[$calledClass] = $reflectionClass->getConstants();
            unset($reflectionClass);
        }
        
        return self::$_constants[$calledClass];
    }
}
