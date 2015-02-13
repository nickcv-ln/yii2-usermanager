<?php

namespace nickcv\usermanager\tests\codeception\unit\models;

use yii\codeception\TestCase;
use nickcv\usermanager\forms\ConfigurationForm;
use nickcv\usermanager\enums\PasswordStrength;
use nickcv\usermanager\enums\GeneralSettings;
use nickcv\usermanager\enums\Registration;
use nickcv\usermanager\helpers\ArrayHelper as AH;

class ConfigurationFormTest extends TestCase
{

    public function testPasswordStrengthValidity()
    {
        $model = new ConfigurationForm([
            'passwordStrength' => 'madeup',
        ]);

        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('passwordStrength'));
        $this->assertContains('Password Strength is invalid.', $model->getErrors('passwordStrength'));
        
        $model->clearErrors();
        $model->passwordStrength = PasswordStrength::WEAK;
        $this->assertTrue($model->validate());
        
        $model->clearErrors();
        $model->passwordStrength = PasswordStrength::MEDIUM;
        $this->assertTrue($model->validate());
        
        $model->clearErrors();
        $model->passwordStrength = PasswordStrength::STRONG;
        $this->assertTrue($model->validate());
        
        $model->clearErrors();
        $model->passwordStrength = PasswordStrength::SECURE;
        $this->assertTrue($model->validate());
    }

    public function testPasswordRecoveryValidity()
    {
        $model = new ConfigurationForm([
            'passwordRecovery' => 'madeup',
        ]);

        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('passwordRecovery'));
        $this->assertContains('Password Recovery is invalid.', $model->getErrors('passwordRecovery'));
        
        $model->clearErrors();
        $model->passwordRecovery = GeneralSettings::ENABLED;
        $this->assertTrue($model->validate());
        
        $model->clearErrors();
        $model->passwordRecovery = GeneralSettings::DISABLED;
        $this->assertTrue($model->validate());
    }
    
    public function testRegistrationValidity()
    {
        $model = new ConfigurationForm([
            'registration' => 'madeup',
        ]);

        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('registration'));
        $this->assertContains('Registration is invalid.', $model->getErrors('registration'));
        
        $model->clearErrors();
        $model->registration = Registration::CAPTCHA;
        $this->assertTrue($model->validate());
        
        $model->clearErrors();
        $model->registration = Registration::ENABLED;
        $this->assertTrue($model->validate());
        
        $model->clearErrors();
        $model->registration = Registration::DISABLED;
        $this->assertTrue($model->validate());
    }
    
    public function testActivationValidity()
    {
        $model = new ConfigurationForm([
            'activation' => 'madeup',
        ]);

        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('activation'));
        $this->assertContains('Activation is invalid.', $model->getErrors('activation'));
        
        $model->clearErrors();
        $model->activation = GeneralSettings::ENABLED;
        $this->assertTrue($model->validate());
        
        $model->clearErrors();
        $model->activation = GeneralSettings::DISABLED;
        $this->assertTrue($model->validate());
    }
    
    public function testGetDefinedConstantsNames()
    {
        $model = new ConfigurationForm;
        $this->assertCount(0, $model->getDefinedAttributesAsconstants());
        
        $model->registration = Registration::CAPTCHA;
        $this->assertCount(1, $model->getDefinedAttributesAsconstants());
        $this->assertEquals(AH::PHP_CONTENT . '\nickcv\usermanager\enums\Registration::CAPTCHA', $model->getDefinedAttributesAsconstants()['registration']);
        
        $model->passwordStrength = PasswordStrength::SECURE;
        $this->assertCount(2, $model->getDefinedAttributesAsconstants());
        $this->assertEquals(AH::PHP_CONTENT . '\nickcv\usermanager\enums\PasswordStrength::SECURE', $model->getDefinedAttributesAsconstants()['passwordStrength']);
        
        $model->passwordRecovery = GeneralSettings::ENABLED;
        $this->assertCount(3, $model->getDefinedAttributesAsconstants());
        $this->assertEquals(AH::PHP_CONTENT . '\nickcv\usermanager\enums\GeneralSettings::ENABLED', $model->getDefinedAttributesAsconstants()['passwordRecovery']);
        
        $model->activation = GeneralSettings::DISABLED;
        $this->assertCount(4, $model->getDefinedAttributesAsconstants());
        $this->assertEquals(AH::PHP_CONTENT . '\nickcv\usermanager\enums\GeneralSettings::DISABLED', $model->getDefinedAttributesAsconstants()['activation']);
    }

}
