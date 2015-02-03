<?php
namespace nickcv\usermanager\helpers;

use yii\helpers\ArrayHelper as YiiArrayHelper;

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
