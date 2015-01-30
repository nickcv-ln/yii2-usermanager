<?php
namespace nickcv\usermanager\helpers;

use yii\base\InvalidParamException;

class StringHelper
{
    /**
     * Returns a randomly generated string with uppercase letter, lowercase
     * letters, numbers and special characters.
     * Lenght must be a value equal or above 8.
     * 
     * @param integer $length length of the randomly generated string
     * @return string random string
     * @throws InvalidParamException
     */
    public static function randomString($length = 12)
    {
        if (!is_int($length)) {
            throw new InvalidParamException('$lenght should be an integer, "' . gettype($length) . '" given.');
        }
        
        if ($length < 8) {
            throw new InvalidParamException('$lenght should not be less then 8, "' . $length . '" given.');
        }
        
        $stringWithNoSpecialChars = substr(str_shuffle(MD5(microtime())), 0, $length - 4);
        
        return str_shuffle(str_shuffle($stringWithNoSpecialChars.self::getSpecialCharacters()));
    }
    
    /**
     * Returns a random selection of 3 special characters
     * 
     * @return string 3 special characters
     */
    private static function getSpecialCharacters()
    {
        $specialCharacters = '!-_?.$:%;&,*/';
        
        return substr(str_shuffle($specialCharacters), 0, 4);
    }
}
