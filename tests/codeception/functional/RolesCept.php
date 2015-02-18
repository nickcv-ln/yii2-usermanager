<?php

use nickcv\usermanager\tests\codeception\_pages\RolesPage;
use nickcv\usermanager\helpers\AuthHelper;
use nickcv\usermanager\enums\Permissions;
use nickcv\usermanager\enums\Roles;
AuthHelper::disableCache();

$testName = Yii::$app->authManager->getRole('TestName');
if ($testName) {
    Yii::$app->authManager->remove($testName);
}


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
$I->expectTo('see add role modal button and superAdmin role button');
$I->see('add child role');
$I->see('superAdmin', '#add-role-modal');
$I->click('superAdmin', '#add-role-modal');
$I->expectTo('see success message and not the modal or role button');
$I->see('The role "superAdmin" is now a child of the current role.', '.alert-success');
$I->dontSee('add child role');
$I->dontSee('superAdmin', '#add-role-modal');

$I->amGoingTo('remove the superAdmin child role');
$I->expectTo('see the role revoke button');
$I->seeElement('#revoke-role-form-superAdmin');
$I->submitForm('#revoke-role-form-superAdmin', []);
$I->expectTo('see success message and the modal and role button');
$I->see('The role "superAdmin" is not a child of this role anymore.', '.alert-success');
$I->see('add child role');
$I->see('superAdmin', '#add-role-modal');

$I->amGoingTo('test that only available roles appear');
$I->expectTo('see add role modal button and admin role button');
$I->see('add child role');
$I->see('admin', '#add-role-modal #add-role-form-admin');
$I->see('superAdmin', '#add-role-modal #add-role-form-superAdmin');
$I->see('standardUser', '#add-role-modal #add-role-form-standardUser');
$I->click('admin', '#add-role-modal');
$I->expectTo('see success message and not the roles button not available');
$I->see('The role "admin" is now a child of the current role.', '.alert-success');
$I->see('add child role');
$I->dontSee('admin', '#add-role-modal #add-role-form-admin');
$I->see('superAdmin', '#add-role-modal #add-role-form-superAdmin');
$I->dontSee('standardUser', '#add-role-modal #add-role-form-standardUser');

Yii::$app->authManager->remove(Yii::$app->authManager->getRole('TestName'));
AuthHelper::enableCache();
