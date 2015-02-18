<?php

namespace nickcv\usermanager\tests\codeception\unit\models;

use yii\codeception\TestCase;
use nickcv\usermanager\forms\RoleForm;
use nickcv\usermanager\enums\Scenarios;
use nickcv\usermanager\enums\Roles;
use nickcv\usermanager\enums\Permissions;
use nickcv\usermanager\helpers\AuthHelper;

class RoleFormTest extends TestCase
{
    
    public function setUp()
    {
        parent::setUp();
        $this->destroyRole('madeup');
        AuthHelper::disableCache();
    }
    
    public function tearDown()
    {
        $this->destroyRole('madeup');
        parent::tearDown();
        AuthHelper::enableCache();
    }

    public function testNameIsRequiredWhenAddingExistingRole()
    {
        $model = new RoleForm(['scenario' => Scenarios::ROLE_ADD]);
        
        $this->assertFalse($model->validate());
        $this->assertCount(2, $model->getErrors());
        $this->assertCount(1, $model->getErrors('name'));
        $this->assertContains('Name cannot be blank.', $model->getErrors('name'));
    }
    
    public function testNameMustExistWhenAddingExistingRole()
    {
        $model = new RoleForm(['scenario' => Scenarios::ROLE_ADD]);
        $model->name = 'madeup';
        
        $this->assertFalse($model->validate());
        $this->assertCount(2, $model->getErrors());
        $this->assertCount(1, $model->getErrors('name'));
        $this->assertContains('The role "madeup" does not exists.', $model->getErrors('name'));
    }
    
    public function testExistingRoleMustBeAString()
    {
        $model = new RoleForm(['scenario' => Scenarios::ROLE_ADD]);
        
        $model->existingRole = true;
        
        $this->assertFalse($model->validate());
        $this->assertCount(2, $model->getErrors());
        $this->assertCount(1, $model->getErrors('existingRole'));
        $this->assertContains('Existing Role should be a role.', $model->getErrors('existingRole'));
        
        $model->existingRole = [];
        $model->clearErrors();
        $this->assertCount(0, $model->getErrors());
        
        $this->assertFalse($model->validate());
        $this->assertCount(2, $model->getErrors());
        $this->assertCount(1, $model->getErrors('existingRole'));
        
        $this->assertContains('Existing Role cannot be blank.', $model->getErrors('existingRole'));
    }
    
    public function testExistingRoleMustBeMissingForTheGivenRole()
    {
        $model = new RoleForm(['scenario' => Scenarios::ROLE_ADD]);
        
        $model->name = Roles::ADMIN;
        $model->existingRole = Roles::STANDARD_USER;
        
        $this->assertFalse($model->validate());
        $this->assertCount(1, $model->getErrors());
        $this->assertCount(1, $model->getErrors('existingRole'));
        $this->assertContains('The given role "admin" is already inheriting or being inherited by a role named "standardUser".', $model->getErrors('existingRole'));
        
        $model->existingRole = 'madeup';
        $model->clearErrors();
        
        $this->assertFalse($model->validate());
        $this->assertCount(1, $model->getErrors());
        $this->assertCount(1, $model->getErrors('existingRole'));
        $this->assertContains('The given role "admin" is already inheriting or being inherited by a role named "madeup".', $model->getErrors('existingRole'));
    }
    
    public function testAddExistingPermissionsToRole()
    {
        $model = new RoleForm(['scenario' => Scenarios::ROLE_ADD]);
        
        $currentChildren = AuthHelper::getChildrenRoles(Roles::STANDARD_USER);
        $this->assertCount(0, $currentChildren);
        
        $model->name = Roles::STANDARD_USER;
        $this->createRole('madeup');
        $model->existingRole = 'madeup';
        $this->assertTrue($model->addExistingRole());
        
        $newChildren = AuthHelper::getChildrenRoles(Roles::STANDARD_USER);
        $this->assertCount(1, $newChildren);
        $this->assertArrayHasKey('madeup', $newChildren);
        
        $this->destroyRole('madeup');
        
        $cleanChildren = AuthHelper::getChildrenRoles(Roles::STANDARD_USER);
        $this->assertCount(0, $cleanChildren);
    }
    
    public function testNameDescriptionAreRequiredWhenAddingNewRole()
    {
        $model = new RoleForm(['scenario' => Scenarios::ROLE_NEW]);
        
        $this->assertFalse($model->validate());
        $this->assertCount(2, $model->getErrors());
        $this->assertCount(1, $model->getErrors('name'));
        $this->assertContains('Name cannot be blank.', $model->getErrors('name'));
        $this->assertCount(1, $model->getErrors('description'));
        $this->assertContains('Description cannot be blank.', $model->getErrors('description'));
    }
    
    public function testNewRoleNameMustBeValid()
    {
        $model = new RoleForm(['scenario' => Scenarios::ROLE_NEW]);
        
        $model->name = 'Inva-lid';
        
        $this->assertFalse($model->validate());
        $this->assertCount(2, $model->getErrors());
        $this->assertCount(1, $model->getErrors('name'));
        $this->assertContains('Name can only contain letters and underscore signs.', $model->getErrors('name'));
    }
    
    public function testNewRoleNameMustBeUnique()
    {
        $model = new RoleForm(['scenario' => Scenarios::ROLE_NEW]);
        
        $model->name = Roles::ADMIN;
        
        $this->assertFalse($model->validate());
        $this->assertCount(2, $model->getErrors());
        $this->assertCount(1, $model->getErrors('name'));
        $this->assertContains('The role name should be unique, role "admin" already exists.', $model->getErrors('name'));
        
        $model->clearErrors();
        $model->name = Permissions::MODULE_MANAGEMENT;
        
        $this->assertFalse($model->validate());
        $this->assertCount(2, $model->getErrors());
        $this->assertCount(1, $model->getErrors('name'));
        $this->assertContains('The role name should not match a permission name, a permission named "moduleManagement" has been found.', $model->getErrors('name'));
    }
    
    public function testCreateNewRole()
    {
        $auth = \Yii::$app->authManager;
        
        $this->assertNull($auth->getRole('madeUp'));
        
        $model = new RoleForm(['scenario' => Scenarios::ROLE_NEW]);
        
        $model->name = 'madeUp';
        $model->description = 'made up description';
        
        $this->assertTrue($model->createNewRole());
        
        $role = $auth->getRole('madeUp');
        $this->assertEquals('madeUp', $role->name);
        $this->assertEquals('made up description', $role->description);
        
        $auth->remove($role);
        
        $this->assertNull($auth->getRole('madeUp'));
        
        $this->assertFileExists(\Yii::getAlias('@app/enums/ExtendedRoles_test.php'));
        
        unlink(\Yii::getAlias('@app/enums/ExtendedRoles_test.php'));
        rmdir(\Yii::getAlias('@app/enums'));
        
    }
    
    public function testNameExistingRoleAreRequiredWhenDeletingRole()
    {
        $model = new RoleForm(['scenario' => Scenarios::ROLE_DELETE]);
        $model->clearErrors();
        $this->assertCount(0, $model->getErrors());
        
        $this->assertFalse($model->validate());
        $this->assertCount(2, $model->getErrors());
        $this->assertCount(1, $model->getErrors('existingRole'));
        $this->assertContains('Existing Role cannot be blank.', $model->getErrors('existingRole'));
        $this->assertCount(1, $model->getErrors('name'));
        $this->assertContains('Name cannot be blank.', $model->getErrors('name'));
    }
    
    public function testWhenDeletingRoleItMustExist()
    {
        $model = new RoleForm(['scenario' => Scenarios::ROLE_DELETE]);
        
        $model->name = 'madeup';
        
        $this->assertFalse($model->validate());
        $this->assertCount(2, $model->getErrors());
        $this->assertCount(1, $model->getErrors('name'));
        $this->assertContains('The role "madeup" does not exists.', $model->getErrors('name'));
    }
    
    public function testCannotDeleteBasicAdminChildRoles()
    {
        $model = new RoleForm(['scenario' => Scenarios::ROLE_DELETE]);
        
        $model->existingRole = Roles::ADMIN;
        $model->name = Roles::STANDARD_USER;
        
        $this->assertFalse($model->validate());
        $this->assertCount(1, $model->getErrors());
        $this->assertCount(1, $model->getErrors('name'));
        $this->assertContains('The role "standardUser" is a core "admin" child role and cannot be removed.', $model->getErrors('name'));
    }
    
    public function testCannotDeleteBasicSuperAdminChildRoles()
    {
        $model = new RoleForm(['scenario' => Scenarios::ROLE_DELETE]);
        
        $model->existingRole = Roles::SUPER_ADMIN;
        $model->name = Roles::ADMIN;
        
        $this->assertFalse($model->validate());
        $this->assertCount(1, $model->getErrors());
        $this->assertCount(1, $model->getErrors('name'));
        $this->assertContains('The role "admin" is a core "superAdmin" child role and cannot be removed.', $model->getErrors('name'));
        
        $model->clearErrors();
        $model->name = Roles::STANDARD_USER;
        $this->assertFalse($model->validate());
        $this->assertCount(1, $model->getErrors());
        $this->assertCount(1, $model->getErrors('name'));
        $this->assertContains('The role "standardUser" is a core "superAdmin" child role and cannot be removed.', $model->getErrors('name'));
    }
    
    public function testCannotDeleteRoleIfExistingRoleIsNotParent()
    {
        $model = new RoleForm(['scenario' => Scenarios::ROLE_DELETE]);
        
        $model->existingRole = Roles::ADMIN;
        $model->name = Roles::SUPER_ADMIN;
        
        $this->assertFalse($model->validate());
        $this->assertCount(1, $model->getErrors());
        $this->assertCount(1, $model->getErrors('existingRole'));
        $this->assertContains('The role "admin" is not a parent of role "superAdmin".', $model->getErrors('existingRole'));
    }
    
    public function testRemoveChildRole()
    {   
        $this->createRole('madeup', Roles::STANDARD_USER);
        
        $newChildren = AuthHelper::getChildrenRoles(Roles::STANDARD_USER);
        $this->assertCount(1, $newChildren);
        $this->assertArrayHasKey('madeup', $newChildren);
        
        $model = new RoleForm(['scenario' => Scenarios::ROLE_DELETE]);
        
        $model->existingRole = Roles::STANDARD_USER;
        $model->name = 'madeup';
        $model->removeChildRole();
        
        $cleanChildren = AuthHelper::getChildrenRoles(Roles::STANDARD_USER);
        $this->assertCount(0, $cleanChildren);
        
        $this->assertNotNull(\Yii::$app->authManager->getRole('madeup'));
        
        $this->destroyRole('madeup');
    }
    
    private function createRole($roleName, $parent = null)
    {
        $newRole = \Yii::$app->authManager->createRole($roleName);
        \Yii::$app->authManager->add($newRole);
        
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
