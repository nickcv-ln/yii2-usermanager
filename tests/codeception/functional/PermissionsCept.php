<?php

use nickcv\usermanager\tests\codeception\_pages\PermissionsPage;
use nickcv\usermanager\helpers\AuthHelper;
AuthHelper::disableCache();

$testName = Yii::$app->authManager->getPermission('TestName');
if ($testName) {
    Yii::$app->authManager->remove($testName);
}


$I = new FunctionalTester($scenario);
$I->wantTo('ensure that permissions page works');

$permissionPage = PermissionsPage::openBy($I);

$I->dontSee('Roles and Permissions');
$I->see('Login | My Application', '.breadcrumb .active');

$I->amGoingTo('login with correct credentials');
$permissionPage->login('jon@testing.com', 'easypassword');
$I->expectTo('see permissions page');
$I->see('Roles and Permissions');
$I->dontSeeElement('.revoke-permission-button');
$I->expectTo('be able to create a new permission.');
$permissionPage->createPermission('TestName', 'Test Permission');
$I->see('The permission "TestName" was created and added to this role.', '.alert-success');
$I->expectTo('be able to revoke the permission.');
$I->seeElement('.revoke-permission-button');
$I->click('.revoke-permission-button');
$I->see('The permission "TestName" was removed from this role.', '.alert-success');
