<?php
/**
 * Contains ArrayHelper class that extends the Yii ArrayHelper.
 * 
 * @link http://www.creationgears.com/
 * @copyright Copyright (c) 2015 Nicola Puddu
 * @license http://www.gnu.org/copyleft/gpl.html
 * @package nickcv/yii2-usermanager
 * @author Nicola Puddu <n.puddu@outlook.com>
 */
namespace nickcv\usermanager\helpers;

use yii\helpers\ArrayHelper as YiiArrayHelper;

/**
 * This helper inherits all the methods from the Yii ArrayHelper adding a method
 * used to get the PHP code that generates the given array.
 * 
 * @author Nicola Puddu <n.puddu@outlook.com>
 * @version 1.0
 */
class ArrayHelper extends YiiArrayHelper
{
    const PHP_CONTENT = '#PHP#';
    
    /**
     * Iterates through an array and generates the file version of it.
     * 
     * @param array $array the array to iterate
     * @param integer $iteration the array current dimension
     * @return string the formatted array
     */
    public static function printForFile(array $array, $iteration = 1)
    {   
        $string = '[';
        
        foreach ($array as $key => $value) {
            $string .= "\n" . str_repeat("    ", $iteration) . '\'' . $key . '\' => ';
            if (is_array($value)) {
                $string .= self::printForFile($value, $iteration + 1);
            } elseif (is_numeric($value)) {
                $string .= $value;
            } elseif (strpos($value, self::PHP_CONTENT) === 0) {
                $string .= substr($value, strlen(self::PHP_CONTENT));
            } else {
                $string .= '\'' . $value . '\'';
            }
            
            $string .= ',';
        }
        
        $string .= "\n" . str_repeat("    ", $iteration - 1) . ']';
        if ($iteration === 1) {
            $string .= ';';
        }
        
        return $string;
    }
}
