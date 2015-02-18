<?php
/**
 * Contains the service class EnumFilesService, used to handle enum files
 * creation and updates.
 * 
 * @link http://www.creationgears.com/
 * @copyright Copyright (c) 2015 Nicola Puddu
 * @license http://www.gnu.org/copyleft/gpl.html
 * @package nickcv/yii2-usermanager
 * @author Nicola Puddu <n.puddu@outlook.com>
 */
namespace nickcv\usermanager\services;

use yii\base\InvalidParamException;

/**
 * This service is used to handle enum files creation and updates.
 * 
 * @author Nicola Puddu <n.puddu@outlook.com>
 * @version 1.0
 */
class EnumFilesService extends BasicService
{
    
    /**
     * Checks whether the enum file exists or not.
     * 
     * The namespace will be used to get the file path prepending the @ symbol
     * and using the \Yii::getAlias() method. The default is "app\enums"
     * 
     * @param string $classname class name without namespace
     * @param string $namespace the namespace of the enum
     * @return boolean
     */
    public function fileExists($classname, $namespace = 'app\enums')
    {
        if (file_exists($this->getFilePath($classname, $namespace))) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Updates the given enum, creating or updating the values of the existing
     * constants.
     * 
     * The keys of the data array will be the constant name, while the value
     * will be the constant value.
     * 
     * Extends can either be null or must be the full classname including 
     * the namespace.
     * 
     * The namespace will be used to get the file path prepending the @ symbol
     * and using the \Yii::getAlias() method. The default is "app\enums"
     * 
     * If the method returns false use the EnumFilesService::errors() method
     * to retrieve the error message.
     * 
     * @param string $classname class name without namespace
     * @param array $data the constants to add to the enum
     * @param string $extends the class to extend
     * @param string $namespace the namespace of the enum
     * @return boolean
     */
    public function updateEnum($classname, array $data, $extends = '\nickcv\usermanager\enums\BasicEnum', $namespace = 'app\enums')
    {
        if ($extends !== null && !class_exists($extends)) {
            $this->addError('The class to extend "' . $extends . '" does not exist.');
            return false;
        }
        
        if ($this->getDirectoryPath($namespace) === false) {
            return false;
        }
        
        $completeData = array_merge($this->getExistingEnumData($classname, $namespace), $this->getPrintableData($data));
        
        return $this->updateFileContent($classname, $completeData, $extends, $namespace);
    }
    
    /**
     * Returns the array of constants currently in the enum.
     * 
     * @param string $classname the class name without namespace
     * @param string $namespace the namespace of the class
     * @return array
     */
    private function getExistingEnumData($classname, $namespace)
    {
        if (!class_exists($namespace . '\\' . $classname)) {
            return [];
        }
        
        $enumReflection = new \ReflectionClass($namespace . '\\' . $classname);
        
        $constants = $enumReflection->getConstants();
        
        $extension = $enumReflection->getParentClass();
        if ($extension) {
            $constants = array_diff_key($constants, $extension->getConstants());
        }
        
        return $constants;
    }
    
    /**
     * Returns the printable version of the constant data.
     * 
     * @param array $data
     * @return array
     */
    private function getPrintableData($data)
    {
        $printable = [];
        foreach ($data as $constantName => $constantValue) {
            $printable[$this->getPrintableConstantName($constantName)] = $constantValue;
        }
        
        return $printable;
    }
    
    /**
     * Returns the uppercase version of the constant name with underscores
     * instead of spaces and camelcase.
     * 
     * @param string $constantName
     * @return string
     */
    private function getPrintableConstantName($constantName)
    {   
        $withoutSpaces = str_replace(' ', '_', $constantName);
        $withoutCamelcase = preg_replace('/([0-9A-Z]{1,})/', '_$1', $withoutSpaces);
        $withoutDoubleUnderscores = preg_replace('/([_]{1,})/', '_', $withoutCamelcase);
        $withoutInitialUnderscore = $withoutDoubleUnderscores{0} === '_' ? substr($withoutDoubleUnderscores, 1) : $withoutDoubleUnderscores;
        return strtoupper($withoutInitialUnderscore);
    }
    
    /**
     * Returns the printable version of the given constant value.
     * 
     * @param mixed $constantValue
     * @return mixed
     */
    private function getPrintableConstantValue($constantValue)
    {
        if (is_numeric($constantValue)) {
            return $constantValue;
        } elseif (is_bool($constantValue)) {
            return $constantValue;
        } elseif (is_null($constantValue)) {
            return 'null';
        }
        
        return '\'' . $constantValue . '\'';
    }
    
    /**
     * Generates the Enum code and stores it in the file.
     * 
     * @param string $classname the class name without namespace
     * @param array $data the constants for the file
     * @param string $extends the class to extend
     * @param string $namespace the class namespace
     * @return boolean
     */
    private function updateFileContent($classname, $data, $extends, $namespace)
    {
        $this->createDirectoryIfDoesNotExist($namespace);
        
        $content = '<?php' . "\n\n";
        $content .= 'namespace ' . $namespace . ";\n\n";
        $content .= 'class ' . $classname;
        if ($extends) {
            $content .= ' extends ' . $extends;
        }
        
        $content .= "\n{\n\n";
        
        foreach ($data as $constantName => $constantValue) {
            if (!$this->validateConstantName($constantName)) {
                return false;
            }
            $content .= '    const ' . $constantName . ' = ' . $this->getPrintableConstantValue($constantValue) . ";\n";
        }
        
        $content .= "\n}";
        
        return (bool)file_put_contents($this->getFilePath($classname, $namespace), $content);
    }
    
    /**
     * Creates the directory for the given namespace if one does not already exists.
     * 
     * @param string $namespace
     */
    private function createDirectoryIfDoesNotExist($namespace)
    {
        if (!file_exists($this->getDirectoryPath($namespace) . DIRECTORY_SEPARATOR)) {
            mkdir($this->getDirectoryPath($namespace), 0755, true);
        }
    }
    
    /**
     * Returns the full file path
     * 
     * @param string $classname
     * @param string $namespace
     * @return string
     */
    private function getFilePath($classname, $namespace)
    {
        return $this->getDirectoryPath($namespace) . DIRECTORY_SEPARATOR . $classname . '.php';
    }
    
    /**
     * Returns the enum file directory full path
     * 
     * @param string $namespace
     * @return string
     */
    private function getDirectoryPath($namespace)
    {
        try {
            return \Yii::getAlias('@' . str_replace('\\', '/', $namespace));
        } catch (InvalidParamException $exc) {
            $this->addError($exc->getMessage());
            return false;
        }
    }
    
    /**
     * Validates the constant names.
     * 
     * @param mixed $constantName
     * @return boolean
     */
    private function validateConstantName($constantName)
    {
        if (!is_string($constantName)) {
            $this->addError('Constant names should be a string, "' . gettype($constantName) . '" given.');
            return false;
        } else if (is_numeric($constantName{0})) {
            $this->addError('Constant names cannot start with numbers, "' . $constantName . '" given.');
            return false;
        }
        
        return true;
    }
    
}
