<?php

use nickcv\usermanager\tests\codeception\_pages\ConfigurationPage;

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that configuration page works');

$configPage = ConfigurationPage::openBy($I);

$I->see('Login | My Application', '.breadcrumb .active');

$I->amGoingTo('login with correct credentials');
$configPage->login('jon@testing.com', 'easypassword');
$I->expectTo('see configuration form');
$I->see('Module Configuration');
$I->dontSee('Configuration updated.', '.alert-success');
$I->expectTo('be able to update the configuration.');
$I->submitForm('#configuration-form', []);
$I->see('Configuration updated.', '.alert-success');
