<?php
namespace nickcv\usermanager\tests\codeception\unit\enums;

use yii\codeception\TestCase;
use nickcv\usermanager\enums\Roles;


class RolesTest extends TestCase
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
    
    public function testGetRoleLabel()
    {
        $this->assertEquals('Usermanager Super Admin', Roles::getLabel(Roles::SUPER_ADMIN));
        $this->assertNull(Roles::getLabel('madeup'));
    }

}