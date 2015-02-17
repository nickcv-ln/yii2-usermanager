<?php

namespace nickcv\usermanager\tests\codeception\_pages;

use yii\codeception\BasePage;

/**
 * Represents configuration page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class PermissionsPage extends BasePage
{
    public $route = 'usermanager/admin/roles/admin';

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
    
    /**
     * @param string $name
     * @param string $description
     */
    public function createPermission($name, $description)
    {
        $this->actor->fillField('#permission-modal input[name="PermissionForm[name]"]', $name);
        $this->actor->fillField('#permission-modal input[name="PermissionForm[description]"]', $description);
        $this->actor->click('new-permission-button');
    }
    
    public function addExistingPermission($name)
    {
        $this->actor->checkOption('#permission-modal input[value="' . $name . '"]');
        $this->actor->click('existing-permission-button');
    }
}
