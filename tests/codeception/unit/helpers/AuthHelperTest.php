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
    
    protected function setUp()
    {
        parent::setUp();
        AuthHelper::disableCache();
        $this->destroyRole('madeup');
    }
    
    protected function tearDown()
    {
        parent::tearDown();
        AuthHelper::enableCache();
    }
    
    public function testGetGivenRoleDirectPermissions()
    {
        $permissions = AuthHelper::getDirectPermissions(Roles::SUPER_ADMIN);
        
        $this->assertContainsOnlyInstancesOf('\yii\rbac\Permission', $permissions);
        
        $this->assertCount(2, $permissions);
        
        $this->assertEquals(Permissions::MODULE_MANAGEMENT, $permissions[Permissions::MODULE_MANAGEMENT]->name);
        $this->assertEquals(Permissions::ROLES_MANAGEMENT, $permissions[Permissions::ROLES_MANAGEMENT]->name);
    }
    
    public function testGetGivenRoleDirectPermissionAsDataProvider()
    {
        $permissions = AuthHelper::getDirectPermissions(Roles::SUPER_ADMIN, true);
        
        $this->assertInstanceOf('\yii\data\ArrayDataProvider', $permissions);
        
        $this->assertEquals(2, $permissions->count);
        
        $models = $permissions->getModels();
        $this->assertContainsOnlyInstancesOf('\yii\rbac\Permission', $models);
        
        $this->assertEquals(Permissions::MODULE_MANAGEMENT, $models[Permissions::MODULE_MANAGEMENT]->name);
        $this->assertEquals(Permissions::ROLES_MANAGEMENT, $models[Permissions::ROLES_MANAGEMENT]->name);
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
        $roles = AuthHelper::getChildrenRoles(Roles::SUPER_ADMIN);
        
        $this->assertContainsOnlyInstancesOf('\yii\rbac\Role', $roles);
        
        $this->assertCount(2, $roles);
        
        $this->assertEquals(Roles::STANDARD_USER, $roles[Roles::STANDARD_USER]->name);
        $this->assertEquals(Roles::ADMIN, $roles[Roles::ADMIN]->name);
    }
    
    public function testGetChildrenRolesAsDataProvider()
    {
        $dataProvider = AuthHelper::getChildrenRoles(Roles::SUPER_ADMIN, true);
        
        $this->assertInstanceOf('\yii\data\ArrayDataProvider', $dataProvider);
        $this->assertEquals(2, $dataProvider->count);
        
        $roles = $dataProvider->getModels();
        $this->assertContainsOnlyInstancesOf('\yii\rbac\Role', $roles);
        $this->assertEquals(Roles::ADMIN, $roles[Roles::ADMIN]->name);
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
        $admin = AuthHelper::getMissingPermissions(Roles::SUPER_ADMIN);
        
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
        
        $adminData = AuthHelper::getMissingPermissions(Roles::SUPER_ADMIN, true);
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
        $admin = AuthHelper::getAllPermissions(Roles::SUPER_ADMIN);
        
        $this->assertContainsOnlyInstancesOf('\yii\rbac\Permission', $admin);
        
        $this->assertCount(4, $admin);
        $this->assertEquals(Permissions::ROLES_MANAGEMENT, $admin[Permissions::ROLES_MANAGEMENT]->name);
        $this->assertEquals(Permissions::USER_MANAGEMENT, $admin[Permissions::USER_MANAGEMENT]->name);
        $this->assertEquals(Permissions::MODULE_MANAGEMENT, $admin[Permissions::MODULE_MANAGEMENT]->name);
        $this->assertEquals(Permissions::PROFILE_EDITING, $admin[Permissions::PROFILE_EDITING]->name);
    }
    
    public function testGetAllPermissionsAsDataProvider()
    {
        $adminData = AuthHelper::getAllPermissions(Roles::SUPER_ADMIN, true);
        
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
    
    public function testStandardUserProtectedPermissions()
    {
        $this->assertFalse(AuthHelper::isRolePermissionProtected(Roles::STANDARD_USER, Permissions::USER_MANAGEMENT));
        $this->assertFalse(AuthHelper::isRolePermissionProtected(Roles::STANDARD_USER, Permissions::MODULE_MANAGEMENT));
        $this->assertFalse(AuthHelper::isRolePermissionProtected(Roles::STANDARD_USER, Permissions::ROLES_MANAGEMENT));
        $this->assertTrue(AuthHelper::isRolePermissionProtected(Roles::STANDARD_USER, Permissions::PROFILE_EDITING));
        $this->assertFalse(AuthHelper::isRolePermissionProtected(Roles::STANDARD_USER, 'madeup'));
    }
    
    public function testAdminProtectedPermissions()
    {
        $this->assertTrue(AuthHelper::isRolePermissionProtected(Roles::ADMIN, Permissions::USER_MANAGEMENT));
        $this->assertFalse(AuthHelper::isRolePermissionProtected(Roles::ADMIN, Permissions::MODULE_MANAGEMENT));
        $this->assertFalse(AuthHelper::isRolePermissionProtected(Roles::ADMIN, Permissions::ROLES_MANAGEMENT));
        $this->assertTrue(AuthHelper::isRolePermissionProtected(Roles::ADMIN, Permissions::PROFILE_EDITING));
        $this->assertFalse(AuthHelper::isRolePermissionProtected(Roles::ADMIN, 'madeup'));
    }
    
    public function testSuperAdminProtectedPermissions()
    {
        $this->assertTrue(AuthHelper::isRolePermissionProtected(Roles::SUPER_ADMIN, Permissions::USER_MANAGEMENT));
        $this->assertTrue(AuthHelper::isRolePermissionProtected(Roles::SUPER_ADMIN, Permissions::MODULE_MANAGEMENT));
        $this->assertTrue(AuthHelper::isRolePermissionProtected(Roles::SUPER_ADMIN, Permissions::ROLES_MANAGEMENT));
        $this->assertTrue(AuthHelper::isRolePermissionProtected(Roles::SUPER_ADMIN, Permissions::PROFILE_EDITING));
        $this->assertFalse(AuthHelper::isRolePermissionProtected(Roles::SUPER_ADMIN, 'madeup'));
    }
    
    public function testGetMissingRoles()
    {
        $superadmin = AuthHelper::getMissingRoles(Roles::SUPER_ADMIN);
        $this->assertCount(0, $superadmin);
        
        $admin = AuthHelper::getMissingRoles(Roles::ADMIN);
        $this->assertCount(0, $admin);
        
        $standardUser = AuthHelper::getMissingRoles(Roles::STANDARD_USER);
        $this->assertCount(0, $standardUser);
        
        $this->createRole('madeup', Roles::STANDARD_USER);
        $madeup = AuthHelper::getMissingRoles('madeup');
        $this->destroyRole('madeup');
        
        $this->assertCount(2, $madeup);
        $this->assertContainsOnlyInstancesOf('\yii\rbac\Role', $madeup);
        $this->assertEquals(Roles::ADMIN, $madeup[Roles::ADMIN]->name);
        $this->assertEquals(Roles::SUPER_ADMIN, $madeup[Roles::SUPER_ADMIN]->name);
        
        $this->createRole('madeup', Roles::ADMIN);
        $madeup2 = AuthHelper::getMissingRoles('madeup');
        $this->destroyRole('madeup');
        
        $this->assertCount(1, $madeup2);
        $this->assertContainsOnlyInstancesOf('\yii\rbac\Role', $madeup2);
        $this->assertEquals(Roles::SUPER_ADMIN, $madeup2[Roles::SUPER_ADMIN]->name);
        
        $this->createRole('madeup', null, Roles::SUPER_ADMIN);
        $madeup3 = AuthHelper::getMissingRoles('madeup');
        $this->destroyRole('madeup');
        
        $this->assertCount(2, $madeup3);
        $this->assertContainsOnlyInstancesOf('\yii\rbac\Role', $madeup3);
        $this->assertEquals(Roles::ADMIN, $madeup3[Roles::ADMIN]->name);
        $this->assertEquals(Roles::STANDARD_USER, $madeup3[Roles::STANDARD_USER]->name);
        
        $this->createRole('madeup', Roles::STANDARD_USER, Roles::SUPER_ADMIN);
        $madeup4 = AuthHelper::getMissingRoles('madeup');
        $this->destroyRole('madeup');
        
        $this->assertCount(1, $madeup4);
        $this->assertContainsOnlyInstancesOf('\yii\rbac\Role', $madeup4);
        $this->assertEquals(Roles::ADMIN, $madeup4[Roles::ADMIN]->name);
    }
    
    public function testGetMissingRolesAsDataProvider()
    {
        $superadminData = AuthHelper::getMissingRoles(Roles::SUPER_ADMIN, true);
        $this->assertInstanceOf('\yii\data\ArrayDataProvider', $superadminData);
        $this->assertEquals(0, $superadminData->count);
        
        $adminData = AuthHelper::getMissingRoles(Roles::ADMIN, true);
        $this->assertInstanceOf('\yii\data\ArrayDataProvider', $adminData);
        $this->assertEquals(0, $adminData->count);
        
        $standardUserData = AuthHelper::getMissingRoles(Roles::STANDARD_USER, true);
        $this->assertInstanceOf('\yii\data\ArrayDataProvider', $standardUserData);
        $this->assertEquals(0, $standardUserData->count);
        
        $this->createRole('madeup', Roles::STANDARD_USER);
        $madeupData = AuthHelper::getMissingRoles('madeup', true);
        $this->destroyRole('madeup');
        
        $this->assertInstanceOf('\yii\data\ArrayDataProvider', $madeupData);
        $this->assertEquals(2, $madeupData->count);
        $madeup = $madeupData->getModels();
        $this->assertContainsOnlyInstancesOf('\yii\rbac\Role', $madeup);
        $this->assertEquals(Roles::ADMIN, $madeup[Roles::ADMIN]->name);
        $this->assertEquals(Roles::SUPER_ADMIN, $madeup[Roles::SUPER_ADMIN]->name);
        
        $this->createRole('madeup', Roles::ADMIN);
        $madeup2Data = AuthHelper::getMissingRoles('madeup', true);
        $this->destroyRole('madeup');
        
        $this->assertInstanceOf('\yii\data\ArrayDataProvider', $madeup2Data);
        $this->assertEquals(1, $madeup2Data->count);
        $madeup2 = $madeup2Data->getModels();
        $this->assertContainsOnlyInstancesOf('\yii\rbac\Role', $madeup2);
        $this->assertEquals(Roles::SUPER_ADMIN, $madeup2[Roles::SUPER_ADMIN]->name);
        
        $this->createRole('madeup', null, Roles::SUPER_ADMIN);
        $madeup3Data = AuthHelper::getMissingRoles('madeup', true);
        $this->destroyRole('madeup');
        
        $this->assertInstanceOf('\yii\data\ArrayDataProvider', $madeup3Data);
        $this->assertEquals(2, $madeup3Data->count);
        $madeup3 = $madeup3Data->getModels();
        $this->assertContainsOnlyInstancesOf('\yii\rbac\Role', $madeup3);
        $this->assertEquals(Roles::ADMIN, $madeup3[Roles::ADMIN]->name);
        $this->assertEquals(Roles::STANDARD_USER, $madeup3[Roles::STANDARD_USER]->name);
        
        $this->createRole('madeup', Roles::STANDARD_USER, Roles::SUPER_ADMIN);
        $madeup4Data = AuthHelper::getMissingRoles('madeup', true);
        $this->destroyRole('madeup');
        
        $this->assertInstanceOf('\yii\data\ArrayDataProvider', $madeup4Data);
        $this->assertEquals(1, $madeup4Data->count);
        $madeup4 = $madeup4Data->getModels();
        $this->assertContainsOnlyInstancesOf('\yii\rbac\Role', $madeup4);
        $this->assertEquals(Roles::ADMIN, $madeup4[Roles::ADMIN]->name);
    }
    
    private function createRole($roleName, $child = null, $parent = null)
    {
        $newRole = \Yii::$app->authManager->createRole($roleName);
        \Yii::$app->authManager->add($newRole);
        
        $childObject = $child ? \Yii::$app->authManager->getRole($child) : null;
        
        if ($childObject) {
            \Yii::$app->authManager->addChild($newRole, $childObject);
        }
        
        $parentObject = $parent ? \Yii::$app->authManager->getRole($parent) : null;
        
        if ($parentObject) {
            \Yii::$app->authManager->addChild($parentObject, $newRole);
        }
    }
    
    private function destroyRole($roleName)
    {
        $role = \Yii::$app->authManager->getRole($roleName);
        
        if ($role) {
            \Yii::$app->authManager->remove($role);
        }
    }

}