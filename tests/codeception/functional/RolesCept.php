<?php

use nickcv\usermanager\tests\codeception\_pages\RolesPage;
use nickcv\usermanager\helpers\AuthHelper;
use nickcv\usermanager\enums\Permissions;
use nickcv\usermanager\enums\Roles;
AuthHelper::disableCache();

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that roles page works');

$rolePage = RolesPage::openBy($I);

$I->dontSee('Roles and Permissions');
$I->see('Login | My Application', '.breadcrumb .active');

$I->amGoingTo('login with correct credentials');
$rolePage->login('jon@testing.com', 'easypassword');
$I->expectTo('see permissions page');
$I->see('Roles and Permissions');
$I->dontSeeElement('button[name="revoke-permission-button"]');

$I->amGoingTo('create a new permission');
$rolePage->createRole('TestName', 'Test Role');
$I->expectTo('see the success message.');
$I->see('The role "TestName" has been created.', '.alert-success');

$I->amGoingTo('try to create a role without the required fields');
$rolePage->createRole('', '');
$I->expectTo('see validation errors');
$I->see('Name cannot be blank.');
$I->see('Description cannot be blank.');

$I->amGoingTo('try to create a role with an invalid name');
$rolePage->createRole('Inv-alid', 'does not matter');
$I->expectTo('see validation errors');
$I->see('Name can only contain letters and underscore signs.');

$I->amGoingTo('try to create a role with a permission name');
$rolePage->createRole(Permissions::MODULE_MANAGEMENT, 'does not matter');
$I->expectTo('see validation errors');
$I->see('The new role could have not been created for the following reasons:');
$I->see('The role name should not match a permission name, a permission named "moduleManagement" has been found.');

$I->amGoingTo('try to create a role that already exists.');
$rolePage->createRole(Roles::ADMIN, 'does not matter');
$I->expectTo('see validation errors');
$I->see('The new role could have not been created for the following reasons:');
$I->see('The role name should be unique, role "admin" already exists.');

$I->amGoingTo('add a child role to the new role');
$I->amOnPage(['usermanager/admin/roles/TestName']);
$I->expectTo('see add role modal button and standardUser role button');
$I->see('add child role');
$I->see('standardUser', '#add-role-modal');
$I->click('standardUser', '#add-role-modal');
$I->expectTo('see success message and not the modal or role button');
$I->see('The role "standardUser" is now a child of the current role.', '.alert-success');
$I->dontSee('add child role');
$I->dontSee('standardUser', '#add-role-modal');

$I->amGoingTo('remove the superAdmin child role');
$I->expectTo('see the role revoke button');
$I->seeElement('#revoke-role-form-standardUser');
$I->submitForm('#revoke-role-form-standardUser', []);
$I->expectTo('see success message and the modal and role button');
$I->see('The role "standardUser" is not a child of this role anymore.', '.alert-success');
$I->see('add child role');
$I->see('standardUser', '#add-role-modal');

$I->amGoingTo('add three extra roles for tests and make them all child of each other');
$I->amOnPage(['usermanager/admin/roles']);
$rolePage->createRole('TestNameA', 'Test Role A');
$rolePage->createRole('TestNameB', 'Test Role B');
$rolePage->createRole('TestNameC', 'Test Role C');
$I->amOnPage(['usermanager/admin/roles/TestNameA']);
$I->click('TestNameB', '#add-role-modal');
$I->amOnPage(['usermanager/admin/roles/TestNameB']);
$I->click('TestNameC', '#add-role-modal');

$I->amGoingTo('test that only available roles appear');
$I->amOnPage(['usermanager/admin/roles/TestName']);
$I->expectTo('see add role modal button and Testname3 role button');
$I->see('add child role');
$I->see('TestNameA', '#add-role-modal #add-role-form-TestNameA');
$I->see('TestNameB', '#add-role-modal #add-role-form-TestNameB');
$I->see('TestNameC', '#add-role-modal #add-role-form-TestNameC');
$I->click('TestNameB', '#add-role-modal');
$I->expectTo('see success message and not the roles button not available');
$I->see('The role "TestNameB" is now a child of the current role.', '.alert-success');
$I->see('add child role');
$I->see('TestNameA', '#add-role-modal #add-role-form-TestNameA');
$I->dontSee('TestNameB', '#add-role-modal #add-role-form-TestNameB');
$I->dontSee('TestNameC', '#add-role-modal #add-role-form-TestNameC');

Yii::$app->authManager->remove(Yii::$app->authManager->getRole('TestName'));
Yii::$app->authManager->remove(Yii::$app->authManager->getRole('TestNameA'));
Yii::$app->authManager->remove(Yii::$app->authManager->getRole('TestNameB'));
Yii::$app->authManager->remove(Yii::$app->authManager->getRole('TestNameC'));
AuthHelper::enableCache();
