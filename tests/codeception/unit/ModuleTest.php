<?php
namespace nickcv\usermanager\tests\codeception\unit;

use yii\codeception\TestCase;
use nickcv\usermanager\Module;
use nickcv\usermanager\enums\PasswordStrength;
use nickcv\usermanager\enums\GeneralSettings;
use nickcv\usermanager\enums\Registration;


class BasicEnumTest extends TestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testCanCreateModuleWithDefaultValues()
    {
        $module = new Module('usermanager');
        $this->assertInstanceOf('\nickcv\usermanager\Module', $module);
        $this->assertEquals(PasswordStrength::SECURE, $module->passwordStrength);
        $this->assertEquals(GeneralSettings::ENABLED, $module->passwordRecovery);
        $this->assertEquals(GeneralSettings::ENABLED, $module->activation);
        $this->assertEquals(Registration::CAPTCHA, $module->registration);
    }
    
    /**
     * @expectedException        \yii\base\InvalidConfigException
     * @expectedExceptionMessage Only constants values of \nickcv\usermanager\enums\PasswordStrength are allowed for the $passwordStrength, "madeup" given.
     */
    public function testCannotCreateModuleWithInvalidPasswordStrength()
    {
        new Module('usermanager', null, [
            'passwordStrength' => 'madeup',
        ]);
    }
    
    public function testCanCreateModuleWithDifferentPasswordStrength()
    {
        $module = new Module('usermanager', null, [
            'passwordStrength' => PasswordStrength::WEAK,
        ]);
        $this->assertInstanceOf('\nickcv\usermanager\Module', $module);
        $this->assertEquals(PasswordStrength::WEAK, $module->passwordStrength);
    }
    
    /**
     * @expectedException        \yii\base\InvalidConfigException
     * @expectedExceptionMessage Only constants values of \nickcv\usermanager\enums\GeneralSettings are allowed for $passwordRecovery, "madeup" given.
     */
    public function testCannotCreateModuleWithInvalidPasswordRecovery()
    {
        new Module('usermanager', null, [
            'passwordRecovery' => 'madeup',
        ]);
    }
    
    public function testCanCreateModuleWithDifferentPasswordRecovery()
    {
        $module = new Module('usermanager', null, [
            'passwordRecovery' => GeneralSettings::DISABLED,
        ]);
        $this->assertInstanceOf('\nickcv\usermanager\Module', $module);
        $this->assertEquals(GeneralSettings::DISABLED, $module->passwordRecovery);
    }
    
    /**
     * @expectedException        \yii\base\InvalidConfigException
     * @expectedExceptionMessage Only constants values of \nickcv\usermanager\enums\GeneralSettings are allowed for $activation, "madeup" given.
     */
    public function testCannotCreateModuleWithInvalidActivation()
    {
        new Module('usermanager', null, [
            'activation' => 'madeup',
        ]);
    }
    
    public function testCanCreateModuleWithDifferentActivation()
    {
        $module = new Module('usermanager', null, [
            'activation' => GeneralSettings::DISABLED,
        ]);
        $this->assertInstanceOf('\nickcv\usermanager\Module', $module);
        $this->assertEquals(GeneralSettings::DISABLED, $module->activation);
    }
    
    /**
     * @expectedException        \yii\base\InvalidConfigException
     * @expectedExceptionMessage Only constants values of \nickcv\usermanager\enums\Registration are allowed for $registration, "madeup" given.
     */
    public function testCannotCreateModuleWithInvalidRegistration()
    {
        new Module('usermanager', null, [
            'registration' => 'madeup',
        ]);
    }
    
    public function testCanCreateModuleWithDifferentRegistration()
    {
        $module1 = new Module('usermanager', null, [
            'registration' => Registration::DISABLED,
        ]);
        $this->assertInstanceOf('\nickcv\usermanager\Module', $module1);
        $this->assertEquals(Registration::DISABLED, $module1->registration);
        
        $module2 = new Module('usermanager', null, [
            'registration' => Registration::ENABLED,
        ]);
        $this->assertInstanceOf('\nickcv\usermanager\Module', $module2);
        $this->assertEquals(Registration::ENABLED, $module2->registration);
        
        $module3 = new Module('usermanager', null, [
            'registration' => Registration::CAPTCHA,
        ]);
        $this->assertInstanceOf('\nickcv\usermanager\Module', $module3);
        $this->assertEquals(Registration::CAPTCHA, $module3->registration);
    }

}