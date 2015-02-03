<?php
/**
 * Contains the module class used by the whole yii2-usermanager module.
 * 
 * @link http://www.creationgears.com/
 * @copyright Copyright (c) 2015 Nicola Puddu
 * @license http://www.gnu.org/copyleft/gpl.html
 * @package nickcv/yii2-usermanager
 * @author Nicola Puddu <n.puddu@outlook.com>
 */
namespace nickcv\usermanager;

use yii\base\BootstrapInterface;
use nickcv\usermanager\enums\PasswordStrength;
use yii\base\InvalidConfigException;

/**
 * This class defines the usermanager module.
 * 
 * @author Nicola Puddu <n.puddu@outlook.com>
 * @version 1.0
 * 
 * @property integer $passwordStrength
 */
class Module extends \yii\base\Module implements BootstrapInterface
{   
    /**
     * Check \nickcv\usermanager\enums\PasswordStrength for possible values.
     *
     * @var integer
     */
    private $_passwordStrength;
    
    /**
     * Register defaults without use of magic numbers and magic letters.
     * 
     * @inheritdoc
     */
    public function __construct($id, $parent = null, $config = [])
    {
        $this->_passwordStrength = PasswordStrength::SECURE;
        parent::__construct($id, $parent, $config);
    }
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setAliases([
            '@nickcv/usermanager' =>  dirname(__FILE__),
        ]);
        parent::init();
    }

    /**
     * Sets a new password strength.
     * 
     * @param integer $value
     */
    public function setPasswordStrength($value)
    {
        if (!PasswordStrength::hasConstantWithValue($value)) {
            throw new InvalidConfigException('Only constants values of \nickcv\usermanager\enums\PasswordStrength are allowed for the $passwordStrength, "' . $value . '" given.');
        }
        
        $this->_passwordStrength = $value;
    }
    
    /**
     * Returns the current password strenght configuration.
     * 
     * @return integer
     */
    public function getPasswordStrength()
    {
        return $this->_passwordStrength;
    }
    
    
    
    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        if ($app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'nickcv\usermanager\commands';
            
            $this->setAliases([
                '@nickcv/usermanager' =>  dirname(__FILE__),
            ]);
            
        }
    }
}