<?php
namespace nickcv\usermanager\tests\codeception\unit;

use yii\codeception\TestCase;
use nickcv\usermanager\enums;


class EnumsTest extends TestCase
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

    public function testGetConstantsListOfTwoEnums()
    {
        $userStatus = enums\UserStatus::getList();
        
        $this->assertCount(3, $userStatus);
        $this->assertArrayHasKey('BANNED', $userStatus);
        $this->assertArrayHasKey('PENDING', $userStatus);
        $this->assertArrayHasKey('ACTIVE', $userStatus);
        
        $this->assertEquals(0, $userStatus['BANNED']);
        $this->assertEquals(1, $userStatus['PENDING']);
        $this->assertEquals(2, $userStatus['ACTIVE']);
        
        $scenarios = enums\Scenarios::getList();
        $this->assertArrayHasKey('LOGIN', $scenarios);
        $this->assertArrayHasKey('USER_REGISTRATION', $scenarios);
        $this->assertArrayHasKey('USER_CREATION', $scenarios);
        $this->assertArrayHasKey('ADMIN_CREATION', $scenarios);
        
        $this->assertEquals('login', $scenarios['LOGIN']);
        $this->assertEquals('userRegistration', $scenarios['USER_REGISTRATION']);
        $this->assertEquals('userCreation', $scenarios['USER_CREATION']);
        $this->assertEquals('adminCreation', $scenarios['ADMIN_CREATION']);
        
        $this->assertCount(3, enums\UserStatus::getList());
    }
    
    public function testGetLabelOfConstantValue()
    {
        $this->assertEquals('LOGIN', enums\Scenarios::getLabel(enums\Scenarios::LOGIN));
    }

}