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
use nickcv\usermanager\enums\GeneralSettings;
use nickcv\usermanager\enums\Registration;
use yii\base\InvalidConfigException;
use yii\web\GroupUrlRule;

/**
 * This class defines the usermanager module.
 * 
 * @author Nicola Puddu <n.puddu@outlook.com>
 * @version 1.0
 * 
 * @property integer $passwordStrength
 * @property mixed $registration
 * @property boolean $passwordRecovery
 * @property boolean $activation
 */
class Module extends \yii\base\Module implements BootstrapInterface
{
    
    const CONFIG_FILENAME = 'usermanager.php';

    /**
     * Check \nickcv\usermanager\enums\PasswordStrength for possible values.
     *
     * @var integer
     */
    private $_passwordStrength;
    /**
     * Check \nickcv\usermanager\enums\Registration for possible values.
     * 
     * @var mixed
     */
    private $_registration;
    /**
     * @var boolean
     */
    private $_passwordRecovery;
    /**
     * @var boolean
     */
    private $_activation;

    /**
     * Register defaults without use of magic numbers and magic letters.
     * 
     * @inheritdoc
     */
    public function __construct($id, $parent = null, $config = [])
    {
        $this->_passwordStrength = PasswordStrength::SECURE;
        $this->_passwordRecovery = GeneralSettings::ENABLED;
        $this->_activation = GeneralSettings::ENABLED;
        $this->_registration = Registration::CAPTCHA;
        parent::__construct($id, $parent, $config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setAliases([
            '@nickcv/usermanager' => dirname(__FILE__),
        ]);
        
        parent::init();
    }
    
    public $urlPrefix = 'usermanager';
    
    public $urlRules = [
        'login' => 'default/login',
    ];

    /**
     * Sets a new password strength.
     * Check \nickcv\usermanager\enums\PasswordStrength for possible values.
     * 
     * @param integer $value
     * @throws InvalidConfigException
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
     * Sets the registration configuration.
     * Check \nickcv\usermanager\enums\Registration for possible values.
     * 
     * @param mixed $value
     * @throws InvalidConfigException
     */
    public function setRegistration($value)
    {
        if (!Registration::hasConstantWithValue($value)) {
            throw new InvalidConfigException('Only constants values of \nickcv\usermanager\enums\Registration are allowed for $registration, "' . $value . '" given.');
        }
        
        $this->_registration = $value;
    }
    
    /**
     * Returns the current registration configuration.
     * 
     * @return mixed
     */
    public function getRegistration()
    {
        return $this->_registration;
    }
    
    /**
     * Sets the password recovery configuration.
     * 
     * @param boolean $value
     * @throws InvalidConfigException
     */
    public function setPasswordRecovery($value)
    {
        if (!GeneralSettings::hasConstantWithValue($value)) {
            throw new InvalidConfigException('Only constants values of \nickcv\usermanager\enums\GeneralSettings are allowed for $passwordRecovery, "' . $value . '" given.');
        }
        
        $this->_passwordRecovery = $value;
    }
    
    /**
     * Returns the current passwordRecovery configuration.
     * 
     * @return boolean
     */
    public function getPasswordRecovery()
    {
        return $this->_passwordRecovery;
    }
    
    /**
     * Sets the activation configuration.
     * 
     * @param boolean $value
     * @throws InvalidConfigException
     */
    public function setActivation($value)
    {
        if (!GeneralSettings::hasConstantWithValue($value)) {
            throw new InvalidConfigException('Only constants values of \nickcv\usermanager\enums\GeneralSettings are allowed for $activation, "' . $value . '" given.');
        }
        
        $this->_activation = $value;
    }
    
    /**
     * Returns the current activation configuration.
     * 
     * @return boolean
     */
    public function getActivation()
    {
        return $this->_activation;
    }

    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        if ($app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'nickcv\usermanager\commands';

            $this->setAliases([
                '@nickcv/usermanager' => dirname(__FILE__),
            ]);
        } elseif ($app instanceof \yii\web\Application) {
            $app->urlManager->rules[] = new GroupUrlRule([
                'prefix' => 'usermanager',
                'rules' => [
                    'login' => 'default/login',
                ],
            ]);
        }
    }

}
