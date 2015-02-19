<?php
namespace nickcv\usermanager\tests\codeception\unit\enums;

use yii\codeception\TestCase;
use nickcv\usermanager\enums\Permissions;


class PermissionsTest extends TestCase
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
    
    public function testGetPermissionLabel()
    {
        $this->assertEquals('Module management', Permissions::getLabel(Permissions::MODULE_MANAGEMENT));
        $this->assertNull(Permissions::getLabel('madeup'));
    }

}