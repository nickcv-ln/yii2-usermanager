<?php
namespace nickcv\usermanager\tests\codeception\unit\enums;

use yii\codeception\TestCase;
use nickcv\usermanager\enums;


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

    public function testGetConstantsListOfTwoEnums()
    {
        $userStatus = enums\UserStatus::getList();
        
        $this->assertCount(3, $userStatus);
        $this->assertArrayHasKey('BANNED', $userStatus);
        $this->assertArrayHasKey('PENDING', $userStatus);
        $this->assertArrayHasKey('ACTIVE', $userStatus);
        
        $this->assertEquals(enums\UserStatus::PENDING, $userStatus['PENDING']);
        $this->assertEquals(enums\UserStatus::ACTIVE, $userStatus['ACTIVE']);
        $this->assertEquals(enums\UserStatus::BANNED, $userStatus['BANNED']);
        
        $scenarios = enums\Scenarios::getList();
        $this->assertArrayHasKey('LOGIN', $scenarios);
        $this->assertArrayHasKey('USER_REGISTRATION', $scenarios);
        $this->assertArrayHasKey('USER_CREATION', $scenarios);
        $this->assertArrayHasKey('ADMIN_CREATION', $scenarios);
        
        $this->assertEquals(enums\Scenarios::LOGIN, $scenarios['LOGIN']);
        $this->assertEquals(enums\Scenarios::USER_REGISTRATION, $scenarios['USER_REGISTRATION']);
        $this->assertEquals(enums\Scenarios::USER_CREATION, $scenarios['USER_CREATION']);
        $this->assertEquals(enums\Scenarios::ADMIN_CREATION, $scenarios['ADMIN_CREATION']);
        
        $this->assertCount(3, enums\UserStatus::getList());
    }
    
    public function testGetLabelOfConstantValue()
    {
        $this->assertEquals('BANNED', enums\UserStatus::getLabel(enums\UserStatus::BANNED));
    }
    
    public function testGetLabelsOfAllConstants()
    {
        $labels = enums\UserStatus::getLabels();
        
        $this->assertCount(3, $labels);
        $this->assertEquals('BANNED', $labels[enums\UserStatus::BANNED]);
        $this->assertEquals('PENDING', $labels[enums\UserStatus::PENDING]);
        $this->assertEquals('ACTIVE', $labels[enums\UserStatus::ACTIVE]);
    }
    
    public function testGetConstantDeclaration()
    {
        $this->assertNull(enums\UserStatus::getConstantDeclaration('madeup'));
        $this->assertEquals('\nickcv\usermanager\enums\UserStatus::BANNED', enums\UserStatus::getConstantDeclaration(enums\UserStatus::BANNED));
    }
    
    public function testKnowIfEnumHasConstantWithValue()
    {
        $this->assertFalse(enums\UserStatus::hasConstantWithValue('madeup'));
        $this->assertTrue(enums\UserStatus::hasConstantWithValue(enums\UserStatus::BANNED));
    }
    
    public function testGetClassName()
    {
        $this->assertEquals('\nickcv\usermanager\enums\Scenarios', enums\Scenarios::getClassName());
    }

}