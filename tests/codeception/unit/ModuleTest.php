<?php
namespace nickcv\usermanager\tests\codeception\unit;

use yii\codeception\TestCase;
use nickcv\usermanager\Module;
use nickcv\usermanager\enums\PasswordStrength;


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

}