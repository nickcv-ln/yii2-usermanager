<?php

use nickcv\usermanager\tests\codeception\_pages\ConfigurationPage;

$I = new AcceptanceTester($scenario);
$I->wantTo('ensure that configuration page works');

$loginPage = ConfigurationPage::openBy($I);

$I->dontSee('Module Configuration');
$I->see('Login | My Application', '.breadcrumb .active');

$I->amGoingTo('try to login with correct credentials');
$loginPage->login('jon@testing.com', 'easypassword');
if (method_exists($I, 'wait')) {
    $I->wait(3); // only for selenium
}
$I->expectTo('see configuration form');
$I->see('Module Configuration');
$I->dontSee('Configuration updated.', '.alert-success');
$I->expectTo('be able to update the configuration.');
$I->submitForm('#configuration-form', []);
$I->see('Configuration updated.', '.alert-success');
