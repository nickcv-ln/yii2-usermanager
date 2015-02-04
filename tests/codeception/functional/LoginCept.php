<?php

use nickcv\usermanager\tests\codeception\_pages\LoginPage;

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that login works');

$loginPage = LoginPage::openBy($I);

$I->see('Login | My Application', '.breadcrumb .active');

$I->amGoingTo('try to login with empty credentials');
$loginPage->login('', '');
$I->expectTo('see validations errors');
$I->see('Email cannot be blank.');
$I->see('Password cannot be blank.');

$I->amGoingTo('try to login with wrong credentials');
$loginPage->login('jon@testing.com', 'wrong');
$I->expectTo('see validations errors');
$I->see('Incorrect email or password.');

$I->amGoingTo('try to login with correct credentials');
$loginPage->login('jon@testing.com', 'easypassword');
$I->expectTo('see user info');
$I->see('Logout (jon@testing.com)');
