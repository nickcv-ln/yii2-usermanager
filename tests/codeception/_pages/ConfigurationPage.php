<?php

namespace nickcv\usermanager\tests\codeception\_pages;

use yii\codeception\BasePage;

/**
 * Represents configuration page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class ConfigurationPage extends BasePage
{
    public $route = 'usermanager/admin';

    /**
     * @param string $email
     * @param string $password
     */
    public function login($email, $password)
    {
        $this->actor->fillField('input[name="LoginForm[email]"]', $email);
        $this->actor->fillField('input[name="LoginForm[password]"]', $password);
        $this->actor->click('login-button');
    }
}
