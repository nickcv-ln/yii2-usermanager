<?php

namespace nickcv\usermanager\tests\codeception\_pages;

use nickcv\usermanager\tests\codeception\_pages\LoginPage;

/**
 * Represents configuration page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class RolesPage extends LoginPage
{
    public $route = 'usermanager/admin/roles';
    
    /**
     * @param string $name
     * @param string $description
     */
    public function createRole($name, $description)
    {
        $this->actor->fillField('#new-role-modal input[name="RoleForm[name]"]', $name);
        $this->actor->fillField('#new-role-modal input[name="RoleForm[description]"]', $description);
        $this->actor->click('new-role-button');
    }
}
