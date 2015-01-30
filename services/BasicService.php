<?php
/**
 * Contains the BasicService class that every service has to extend.
 * This class makes every service a singleton.
 * 
 * @link http://www.creationgears.com/
 * @copyright Copyright (c) 2015 Nicola Puddu
 * @license http://www.gnu.org/copyleft/gpl.html
 * @package nickcv/yii2-usermanager
 * @author Nicola Puddu <n.puddu@outlook.com>
 */
namespace nickcv\usermanager\services;

/**
 * This class contains the logic that makes every class that extends it a singleton.
 * 
 * @author Nicola Puddu <n.puddu@outlook.com>
 * @version 1.0
 */
class BasicService
{
    private static $_instances = [];
    
    /**
     * Creates a new intance of the called class passing it the configuration
     * array.
     * 
     * @param array $configuration
     */
    protected function __construct($configuration = [])
    {
        
    }
    
    /**
     * Removes from memory every instance of every service.
     */
    final public static function clearAll()
    {
        self::$_instances = [];
    }
    
    /**
     * Returns the instance of the called call.
     * 
     * @param array $configuration
     * @return static
     */
    final public static function init($configuration = [])
    {
        $class = get_called_class();
        if (!isset(self::$_instances[$class]))
            self::$_instances[$class] = new static($configuration);
        
        return self::$_instances[$class];
    }
}
