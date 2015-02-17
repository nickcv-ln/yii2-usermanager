<?php

namespace nickcv\usermanager\tests\codeception\unit\models;

use yii\codeception\TestCase;
use nickcv\usermanager\forms\PermissionForm;
use nickcv\usermanager\enums\Scenarios;
use nickcv\usermanager\enums\Roles;
use nickcv\usermanager\enums\Permissions;

class PermissionFormTest extends TestCase
{

    public function testRoleIsRequiredWhenAddingExistingPermission()
    {
        $model = new PermissionForm(['scenario' => Scenarios::PERMISSION_ADD]);
        
        $this->assertFalse($model->validate());
        $this->assertCount(2, $model->getErrors());
        $this->assertCount(1, $model->getErrors('role'));
        $this->assertContains('Role cannot be blank.', $model->getErrors('role'));
    }
    
    public function testRoleMustExistWhenAddingExistingPermission()
    {
        $model = new PermissionForm(['scenario' => Scenarios::PERMISSION_ADD]);
        $model->role = 'madeup';
        
        $this->assertFalse($model->validate());
        $this->assertCount(2, $model->getErrors());
        $this->assertCount(1, $model->getErrors('role'));
        $this->assertContains('The given role "madeup" does not exist.', $model->getErrors('role'));
    }
    
    public function testExistingPermissionsMustBeAnArrayOfStrings()
    {
        $model = new PermissionForm(['scenario' => Scenarios::PERMISSION_ADD]);
        
        $model->existingPermissions = 'madeup';
        
        $this->assertFalse($model->validate());
        $this->assertCount(2, $model->getErrors());
        $this->assertCount(1, $model->getErrors('existingPermissions'));
        $this->assertContains('Existing Permissions should be a list of permissions.', $model->getErrors('existingPermissions'));
        
        $model->existingPermissions = [['madeup']];
        $model->clearErrors();
        $this->assertCount(0, $model->getErrors());
        
        $this->assertFalse($model->validate());
        $this->assertCount(2, $model->getErrors());
        $this->assertCount(1, $model->getErrors('existingPermissions'));
        $this->assertContains('Existing Permissions should be a list of permissions.', $model->getErrors('existingPermissions'));
        
        $model->existingPermissions = [];
        $model->clearErrors();
        $this->assertCount(0, $model->getErrors());
        
        $this->assertFalse($model->validate());
        $this->assertCount(2, $model->getErrors());
        $this->assertCount(1, $model->getErrors('existingPermissions'));
        
        $this->assertContains('Existing Permissions cannot be blank.', $model->getErrors('existingPermissions'));
    }
    
    public function testExistingPermissionsMustBeMissingForTheGivenRole()
    {
        $model = new PermissionForm(['scenario' => Scenarios::PERMISSION_ADD]);
        
        $model->role = Roles::ADMIN;
        $model->existingPermissions[] = Permissions::MODULE_MANAGEMENT;
        
        $this->assertFalse($model->validate());
        $this->assertCount(1, $model->getErrors());
        $this->assertCount(1, $model->getErrors('existingPermissions'));
        $this->assertContains('The given role "admin" already has a permission named "moduleManagement".', $model->getErrors('existingPermissions'));
    }
    
    public function testAddExistingPermissionsToRole()
    {
        $model = new PermissionForm(['scenario' => Scenarios::PERMISSION_ADD]);
        
        $auth = \Yii::$app->authManager;
        
        $currentPermissions = $auth->getPermissionsByRole(Roles::STANDARD_USER);
        $this->assertCount(1, $currentPermissions);
        $this->assertArrayHasKey(Permissions::PROFILE_EDITING, $currentPermissions);
        
        $model->role = Roles::STANDARD_USER;
        $model->existingPermissions[] = Permissions::MODULE_MANAGEMENT;
        $this->assertTrue($model->addExistingPermissions());
        
        $newPermissions = $auth->getPermissionsByRole(Roles::STANDARD_USER);
        $this->assertCount(2, $newPermissions);
        $this->assertArrayHasKey(Permissions::PROFILE_EDITING, $newPermissions);
        $this->assertArrayHasKey(Permissions::MODULE_MANAGEMENT, $newPermissions);
        
        $auth->removeChild($auth->getRole(Roles::STANDARD_USER), $auth->getPermission(Permissions::MODULE_MANAGEMENT));
        
        $cleanPermissions = $auth->getPermissionsByRole(Roles::STANDARD_USER);
        $this->assertCount(1, $cleanPermissions);
        $this->assertArrayHasKey(Permissions::PROFILE_EDITING, $cleanPermissions);
    }
    
    public function testRoleMustExistWhenAddingNewPermission()
    {
        $model = new PermissionForm(['scenario' => Scenarios::PERMISSION_NEW]);
        $model->role = 'madeup';
        
        $this->assertFalse($model->validate());
        $this->assertCount(3, $model->getErrors());
        $this->assertCount(1, $model->getErrors('role'));
        $this->assertContains('The given role "madeup" does not exist.', $model->getErrors('role'));
    }
    
    public function testRoleNameDescriptionAreRequiredWhenAddingNewPermission()
    {
        $model = new PermissionForm(['scenario' => Scenarios::PERMISSION_NEW]);
        $model->clearErrors();
        $this->assertCount(0, $model->getErrors());
        
        $this->assertFalse($model->validate());
        $this->assertCount(3, $model->getErrors());
        $this->assertCount(1, $model->getErrors('role'));
        $this->assertContains('Role cannot be blank.', $model->getErrors('role'));
        $this->assertCount(1, $model->getErrors('name'));
        $this->assertContains('Name cannot be blank.', $model->getErrors('name'));
        $this->assertCount(1, $model->getErrors('description'));
        $this->assertContains('Description cannot be blank.', $model->getErrors('description'));
    }
    
    public function testNewPermissionNameMustBeUnique()
    {
        $model = new PermissionForm(['scenario' => Scenarios::PERMISSION_NEW]);
        
        $model->role = Roles::ADMIN;
        $model->name = Permissions::MODULE_MANAGEMENT;
        
        $this->assertFalse($model->validate());
        $this->assertCount(2, $model->getErrors());
        $this->assertCount(1, $model->getErrors('name'));
        $this->assertContains('The permission name should be unique, permission "moduleManagement" already exists.', $model->getErrors('name'));
    }
    
    public function testCreateNewPermission()
    {
        $auth = \Yii::$app->authManager;
        
        $this->assertNull($auth->getPermission('madeUp'));
        
        $model = new PermissionForm(['scenario' => Scenarios::PERMISSION_NEW]);
        
        $model->role = Roles::ADMIN;
        $model->name = 'madeUp';
        $model->description = 'made up description';
        
        $this->assertTrue($model->createNewPermission());
        
        $permission = $auth->getPermission('madeUp');
        $this->assertEquals('madeUp', $permission->name);
        $this->assertEquals('made up description', $permission->description);
        
        $newPermissions = $auth->getPermissionsByRole(Roles::ADMIN);
        $this->assertCount(5, $newPermissions);
        $this->assertArrayHasKey('madeUp', $newPermissions);
        
        $auth->remove($permission);
        
        $cleanPermissions = $auth->getPermissionsByRole(Roles::ADMIN);
        $this->assertCount(4, $cleanPermissions);
        $this->assertArrayNotHasKey('madeUp', $cleanPermissions);
        
        $this->assertFileExists(\Yii::getAlias('@app/enums/ExtendedPermissions.php'));
        
        unlink(\Yii::getAlias('@app/enums/ExtendedPermissions.php'));
        rmdir(\Yii::getAlias('@app/enums'));
        
    }

}
