<?php
/**
 * Contains the service class ConfigFilesService, used to handle config files
 * creation and updates.
 * 
 * @link http://www.creationgears.com/
 * @copyright Copyright (c) 2015 Nicola Puddu
 * @license http://www.gnu.org/copyleft/gpl.html
 * @package nickcv/yii2-usermanager
 * @author Nicola Puddu <n.puddu@outlook.com>
 */
namespace nickcv\usermanager\services;

use nickcv\usermanager\helpers\ArrayHelper;

/**
 * This service is used to handle config files creation and updates.
 * 
 * @author Nicola Puddu <n.puddu@outlook.com>
 * @version 1.0
 */
class ConfigFilesService extends BasicService
{   
    /**
     * Returns the array contained in the given config file in the given scope.
     * The \Yii::getAlias() method will be used on the $scope variable.
     * 
     * @param string $filename the file name including the extension
     * @param string $scope the scope, defaults to "@app"
     * @return array
     */
    public function getConfigFile($filename, $scope = '@app')
    {
        if (!$this->fileExists($filename, $scope)) {
            return [];
        }
        
        return require $this->getConfigFilePath($filename, $scope);
    }
    
    /**
     * This method returns the given config file path in the given scope.
     * The \Yii::getAlias() method will be used on the $scope variable.
     * 
     * @param string $filename the file name including the extension
     * @param string $scope the scope, defaults to "@app"
     * @return string the file path on disk
     */
    public function getPath($filename, $scope = '@app')
    {
        return $this->getConfigFilePath($filename, $scope);
    }
    
    /**
     * This method checks whether the given config file exists.
     * The \Yii::getAlias() method will be used on the $scope variable.
     * 
     * @param string $filename the file name including the extension
     * @param string $scope the scope, defaults to "@app"
     * @return boolean
     */
    public function fileExists($filename, $scope = '@app')
    {
        $this->clearErrors();
        
        if (file_exists($this->getConfigFilePath($filename, $scope))) {
            return true;
        }
        
        return false;
    }
    
    /**
     * This method creates a config file in the given scope, checking if one already exists before
     * overwriting it.
     * The \Yii::getAlias() method will be used on the $scope variable.
     * 
     * @param string $filename the config file name including the extension
     * @param array $data the data to inject on the template
     * @param string $scope the scope, defaults to "@app"
     * @return boolean
     */
    public function createFile($filename, array $data = [], $scope = '@app')
    {
        $this->clearErrors();
        
        if ($this->fileExists($filename, $scope)) {
            $this->addError('The configuration file "' . $filename . '" already exists in the given scope "' . $scope . '". To update a file please use the ' . __CLASS__ . '::updateFile() method.');
            return false;
        }
        
        if ($this->writeFile($filename, $scope, $data) === false) {
            return false;
        }
        
        return true;
    }
    
    /**
     * This method updates an existing config file in the given scope.
     * If $skipCheck is set to false the method will return false if the new
     * data overrides any of the existing. 
     * 
     * If that's the case ConfigFilesService::errors() will contain a list of the overriden values.
     * 
     * The \Yii::getAlias() method will be used on the $scope variable.
     * 
     * @param string $filename the file name with the extension
     * @param array $data the data to use to update the file
     * @param boolean $skipCheck whether should check for overrides.
     * @param string $scope the scope, defaults to "@app"
     * @return boolean
     */
    public function updateFile($filename, array $data, $skipCheck = false, $scope = '@app')
    {   
        if ($this->fileExists($filename, $scope) === false) {
            $this->addError('The configuration file "' . $filename . '" does not exists in the given scope "' . $scope . '". To create a file please use the ' . __CLASS__ . '::createFile() method.');
            return false;
        }
        
        $currentConfiguration = require($this->getConfigFilePath($filename, $scope));
        
        if ($skipCheck === false) {
            if ($this->compareConfiguration($currentConfiguration, $data) === false) {
                return false;
            }
        }
        
        $this->writeFile($filename, $scope, ArrayHelper::merge($currentConfiguration, $data));
        
        
        return true;
    }
    
    /**
     * Returns the file path of the given file name.
     * 
     * @param string $filename the file name
     * @param string $scope the scope
     * @return string the file path
     */
    private function getConfigFilePath($filename, $scope)
    {
        return \Yii::getAlias($scope.'/config/'.$filename);
    }
    
    /**
     * Creates a php file that returns the array.
     * 
     * @param string $filename the config file name
     * @param string $scope the scope that has to be used
     * @param array $data the array written in the file
     * @return boolean
     */
    private function writeFile($filename, $scope, $data)
    {
        $filePath = $this->getConfigFilePath($filename, $scope);
        
        $fileContent = '<?php' . "\n\nreturn " . ArrayHelper::printForFile($data);
        
        try {
            file_put_contents($filePath, $fileContent);
            return true;
        } catch (\Exception $exc) {
            $this->addError('The file "' . $filePath . '" could not be written. For more informations check the details.', [
                'message' => $exc->getMessage(),
                'file' => $exc->getFile(),
                'line' => $exc->getLine(),
            ]);
            return false;
        }
    }
    
    /**
     * This method compares the existing configuration with the intended new one
     * and returns false in case some overriding is taking place.
     * 
     * The ConfigFilesService::errors() will return a list of the overridden
     * values.
     * 
     * @param array $existingConfiguration
     * @param array $newConfiguration
     * @return boolean
     */
    private function compareConfiguration($existingConfiguration, $newConfiguration)
    {
        $overrides = [];
        
        foreach ($newConfiguration as $key => $value) {
            if (array_key_exists($key, $existingConfiguration) && $value !== $existingConfiguration[$key]) {
                $overrides[$key] = [
                    'current' => $existingConfiguration[$key],
                    'afterUpdate' => $this->cleanValue($value)
                ];
            }
        }
        
        if (count($overrides)) {
            $this->addError('Several values would be overridden. Check Details for a complete list.', $overrides);
            return false;
        }
        
        return true;
    }
    
    /**
     * Removes placeholders from every possible value
     * 
     * @param string|array $value
     * @return string|array
     */
    private function cleanValue($value) {
        if (is_array($value)) {
            foreach ($value as $key => $v) {
                $value[$key] = $this->cleanValue($v);
            }
        } else {
            if (strpos($value, ArrayHelper::PHP_CONTENT) === 0) {
                $value = substr($value, strlen(ArrayHelper::PHP_CONTENT));
            }
        }
        
        return $value;
    }
}
