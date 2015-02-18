<?php

namespace nickcv\usermanager\tests\codeception\_pages;

use nickcv\usermanager\tests\codeception\_pages\LoginPage;

/**
 * Represents configuration page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class PermissionsPage extends LoginPage
{
    public $route = 'usermanager/admin/roles/admin';
    
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
