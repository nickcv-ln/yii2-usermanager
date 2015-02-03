<?php
namespace nickcv\usermanager\tests\codeception\unit\enums;

use yii\codeception\TestCase;
use nickcv\usermanager\enums\PasswordStrength;


class PasswordStrengthTest extends TestCase
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

    public function testGetStrengthDescription()
    {
        $this->assertEquals('10 characters with at least 1 number, 1 uppercase letter, 1 lowercase letter', PasswordStrength::getStrengthDescription(PasswordStrength::STRONG));
        $this->assertNull(PasswordStrength::getStrengthDescription('madeup'));
    }
    
    public function testGetStrengthLabel()
    {
        $this->assertEquals('Strong [10 characters with at least 1 number, 1 uppercase letter, 1 lowercase letter]', PasswordStrength::getLabel(PasswordStrength::STRONG));
        $this->assertNull(PasswordStrength::getStrengthDescription('madeup'));
    }

}