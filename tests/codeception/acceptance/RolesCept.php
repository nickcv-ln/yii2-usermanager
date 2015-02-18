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


$I = new AcceptanceTester($scenario);
$I->wantTo('ensure that permissions page works');

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

Yii::$app->authManager->remove(Yii::$app->authManager->getRole('TestName'));
AuthHelper::enableCache();
