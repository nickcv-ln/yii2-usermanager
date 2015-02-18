<?php

use nickcv\usermanager\tests\codeception\_pages\PermissionsPage;
use nickcv\usermanager\helpers\AuthHelper;
use nickcv\usermanager\enums\Permissions;
use nickcv\usermanager\enums\Roles;
AuthHelper::disableCache();

$testName = Yii::$app->authManager->getPermission('TestName');
if ($testName) {
    Yii::$app->authManager->remove($testName);
}


$I = new AcceptanceTester($scenario);
$I->wantTo('ensure that permissions page works');

$permissionPage = PermissionsPage::openBy($I);

$I->dontSee('Roles and Permissions');
$I->see('Login | My Application', '.breadcrumb .active');

$I->amGoingTo('login with correct credentials');
$permissionPage->login('jon@testing.com', 'easypassword');
$I->expectTo('see permissions page');
$I->see('Roles and Permissions');
$I->dontSeeElement('button[name="revoke-permission-button"]');

$I->amGoingTo('create a new permission');
$permissionPage->createPermission('TestName', 'Test Permission');
$I->expectTo('see the success message.');
$I->see('The permission "TestName" was created and added to this role.', '.alert-success');

$I->amGoingTo('revoke the new permission');
$I->seeElement('button[name="revoke-permission-button"]');
$I->click('revoke-permission-button');
$I->expectTo('see the success message.');
$I->see('The permission "TestName" was removed from this role.', '.alert-success');
$I->dontSeeElement('button[name="revoke-permission-button"]');

$I->amGoingTo('add an existing permission.');
$permissionPage->addExistingPermission('TestName');
$I->expectTo('see the success message.');
$I->see('The following permissions have been added to this role: TestName', '.alert-success');

$I->seeElement('button[name="revoke-permission-button"]');

$I->amGoingTo('try to create a permission without the required fields');
$permissionPage->createPermission('', '');
$I->expectTo('see validation errors');
$I->see('Name cannot be blank.');
$I->see('Description cannot be blank.');

$I->amGoingTo('try to create a permission with an invalid name');
$permissionPage->createPermission('Inv-alid', 'does not matter');
$I->expectTo('see validation errors');
$I->see('Name can only contain letters and underscore signs.');

$I->amGoingTo('try to create a permission with a role name');
$permissionPage->createPermission(Roles::ADMIN, 'does not matter');
$I->expectTo('see validation errors');
$I->see('The new permission could have not been created for the following reasons:');
$I->see('The permission name should not match a role name, a role named "admin" has been found.');

$I->amGoingTo('try to create a permission that already exists.');
$permissionPage->createPermission(Permissions::MODULE_MANAGEMENT, 'does not matter');
$I->expectTo('see validation errors');
$I->see('The new permission could have not been created for the following reasons:');
$I->see('The permission name should be unique, permission "moduleManagement" already exists.');

Yii::$app->authManager->remove(Yii::$app->authManager->getPermission('TestName'));
AuthHelper::enableCache();
