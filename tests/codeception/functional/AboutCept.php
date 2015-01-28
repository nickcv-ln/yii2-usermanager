<?php

use nickcv\usermanager\tests\codeception\_pages\AboutPage;
use Codeception\Util\Debug;


$I = new FunctionalTester($scenario);

#Debug::debug($scenario);
$I->wantTo('ensure that about works');
AboutPage::openBy($I);
#$I->see('About', 'h1');
