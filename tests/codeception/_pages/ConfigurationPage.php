<?php

namespace nickcv\usermanager\tests\codeception\_pages;

use nickcv\usermanager\tests\codeception\_pages\LoginPage;

/**
 * Represents configuration page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class ConfigurationPage extends LoginPage
{
    public $route = 'usermanager/admin';

}
