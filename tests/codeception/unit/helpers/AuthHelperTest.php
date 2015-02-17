<?php
namespace nickcv\usermanager\tests\codeception\unit\helpers;

use yii\codeception\TestCase;
use nickcv\usermanager\helpers\AuthHelper;
use nickcv\usermanager\enums\Roles;
use nickcv\usermanager\enums\Permissions;

class AuthHelperTest extends TestCase
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
    
    public function testGetGivenRoleDirectPermissions()
    {
        $permissions = AuthHelper::getDirectPermissions(Roles::ADMIN);
        
        $this->assertContainsOnlyInstancesOf('\yii\rbac\Permission', $permissions);
        
        $this->assertCount(3, $permissions);
        
        $this->assertEquals(Permissions::MODULE_MANAGEMENT, $permissions[Permissions::MODULE_MANAGEMENT]->name);
        $this->assertEquals(Permissions::ROLES_MANAGEMENT, $permissions[Permissions::ROLES_MANAGEMENT]->name);
        $this->assertEquals(Permissions::USER_MANAGEMENT, $permissions[Permissions::USER_MANAGEMENT]->name);
    }
    
    public function testGetGivenRoleDirectPermissionAsDataProvider()
    {
        $permissions = AuthHelper::getDirectPermissions(Roles::ADMIN, true);
        
        $this->assertInstanceOf('\yii\data\ArrayDataProvider', $permissions);
        
        $this->assertEquals(3, $permissions->count);
        
        $models = $permissions->getModels();
        $this->assertContainsOnlyInstancesOf('\yii\rbac\Permission', $models);
        
        $this->assertEquals(Permissions::MODULE_MANAGEMENT, $models[Permissions::MODULE_MANAGEMENT]->name);
        $this->assertEquals(Permissions::ROLES_MANAGEMENT, $models[Permissions::ROLES_MANAGEMENT]->name);
        $this->assertEquals(Permissions::USER_MANAGEMENT, $models[Permissions::USER_MANAGEMENT]->name);
    }
    
    public function testGetNotExistingRoleDirectPermissions()
    {
        $this->assertCount(0, AuthHelper::getDirectPermissions('madeup'));
        
        $arrayDataProvider = AuthHelper::getDirectPermissions('madeup', true);
        $this->assertInstanceOf('\yii\data\ArrayDataProvider', $arrayDataProvider);
        $this->assertEquals(0, $arrayDataProvider->count);
    }
    
    public function testGetChildrenRoles()
    {
        $roles = AuthHelper::getChildrenRoles(Roles::ADMIN);
        
        $this->assertContainsOnlyInstancesOf('\yii\rbac\Role', $roles);
        
        $this->assertCount(1, $roles);
        
        $this->assertEquals(Roles::STANDARD_USER, $roles[Roles::STANDARD_USER]->name);
    }
    
    public function testGetChildrenRolesAsDataProvider()
    {
        $dataProvider = AuthHelper::getChildrenRoles(Roles::ADMIN, true);
        
        $this->assertInstanceOf('\yii\data\ArrayDataProvider', $dataProvider);
        $this->assertEquals(1, $dataProvider->count);
        
        $roles = $dataProvider->getModels();
        $this->assertContainsOnlyInstancesOf('\yii\rbac\Role', $roles);
        $this->assertEquals(Roles::STANDARD_USER, $roles[Roles::STANDARD_USER]->name);
    }
    
    public function testGetChildrenRolesOfNotExistingRole()
    {
        $this->assertCount(0, AuthHelper::getChildrenRoles('madeup'));
        
        $arrayDataProvider = AuthHelper::getChildrenRoles('madeup', true);
        $this->assertInstanceOf('\yii\data\ArrayDataProvider', $arrayDataProvider);
        $this->assertEquals(0, $arrayDataProvider->count);
    }
    
    public function testGetMissingPermissions()
    {
        $admin = AuthHelper::getMissingPermissions(Roles::ADMIN);
        
        $this->assertContainsOnlyInstancesOf('\yii\rbac\Permission', $admin);
        
        $this->assertCount(0, $admin);
        
        $user = AuthHelper::getMissingPermissions(Roles::STANDARD_USER);
        
        $this->assertContainsOnlyInstancesOf('\yii\rbac\Permission', $user);
        
        $this->assertCount(3, $user);
        
        $this->assertEquals(Permissions::ROLES_MANAGEMENT, $user[Permissions::ROLES_MANAGEMENT]->name);
        $this->assertEquals(Permissions::USER_MANAGEMENT, $user[Permissions::USER_MANAGEMENT]->name);
        $this->assertEquals(Permissions::MODULE_MANAGEMENT, $user[Permissions::MODULE_MANAGEMENT]->name);
    }
    
    public function testGetMissingPermissionsAsDataProvider()
    {
        
        $adminData = AuthHelper::getMissingPermissions(Roles::ADMIN, true);
        $this->assertInstanceOf('\yii\data\ArrayDataProvider', $adminData);
        $this->assertEquals(0, $adminData->count);
        
        $userData = AuthHelper::getMissingPermissions(Roles::STANDARD_USER, true);
        $this->assertInstanceOf('\yii\data\ArrayDataProvider', $userData);
        $this->assertEquals(3, $userData->count);
        
        $user = $userData->getModels();
        $this->assertContainsOnlyInstancesOf('\yii\rbac\Permission', $user);
        
        $this->assertEquals(Permissions::MODULE_MANAGEMENT, $user[Permissions::MODULE_MANAGEMENT]->name);
        $this->assertEquals(Permissions::ROLES_MANAGEMENT, $user[Permissions::ROLES_MANAGEMENT]->name);
        $this->assertEquals(Permissions::USER_MANAGEMENT, $user[Permissions::USER_MANAGEMENT]->name);
    }
    
    public function testGetAllPermissions()
    {
        $admin = AuthHelper::getAllPermissions(Roles::ADMIN);
        
        $this->assertContainsOnlyInstancesOf('\yii\rbac\Permission', $admin);
        
        $this->assertCount(4, $admin);
        $this->assertEquals(Permissions::ROLES_MANAGEMENT, $admin[Permissions::ROLES_MANAGEMENT]->name);
        $this->assertEquals(Permissions::USER_MANAGEMENT, $admin[Permissions::USER_MANAGEMENT]->name);
        $this->assertEquals(Permissions::MODULE_MANAGEMENT, $admin[Permissions::MODULE_MANAGEMENT]->name);
        $this->assertEquals(Permissions::PROFILE_EDITING, $admin[Permissions::PROFILE_EDITING]->name);
    }
    
    public function testGetAllPermissionsAsDataProvider()
    {
        $adminData = AuthHelper::getAllPermissions(Roles::ADMIN, true);
        
        $this->assertInstanceOf('\yii\data\ArrayDataProvider', $adminData);
        $this->assertEquals(4, $adminData->count);
        
        $admin = $adminData->getModels();
        
        $this->assertContainsOnlyInstancesOf('\yii\rbac\Permission', $admin);
        
        $this->assertCount(4, $admin);
        $this->assertEquals(Permissions::ROLES_MANAGEMENT, $admin[Permissions::ROLES_MANAGEMENT]->name);
        $this->assertEquals(Permissions::USER_MANAGEMENT, $admin[Permissions::USER_MANAGEMENT]->name);
        $this->assertEquals(Permissions::MODULE_MANAGEMENT, $admin[Permissions::MODULE_MANAGEMENT]->name);
        $this->assertEquals(Permissions::PROFILE_EDITING, $admin[Permissions::PROFILE_EDITING]->name);
    }

}