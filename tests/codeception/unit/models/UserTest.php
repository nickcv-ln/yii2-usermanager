<?php

namespace nickcv\usermanager\tests\codeception\unit\models;

use yii\codeception\DbTestCase;
use nickcv\usermanager\tests\codeception\unit\fixtures\UserFixture;
use nickcv\usermanager\models\User;
use nickcv\usermanager\enums\UserStatus;
use nickcv\usermanager\enums\Scenarios;
use nickcv\usermanager\enums\Roles;

class UserTest extends DbTestCase
{

    public function fixtures()
    {
        return [
            'users' => UserFixture::className(),
        ];
    }

    public function testCanRetrieveAUserById()
    {
        $user = User::findIdentity(1);
        $this->assertInstanceOf('\nickcv\usermanager\models\User', $user);
        $this->assertEquals(1, $user->id);
        $this->assertEquals('jon@testing.com', $user->email);
        $this->assertEquals('Jon', $user->firstname);
        $this->assertEquals('Doe', $user->lastname);
        $this->assertEquals(UserStatus::ACTIVE, $user->status);
        $this->assertEquals('$2y$13$cwekf6.a3BGpUXkHLZZFjueUBrEEiFjItTNiliwfwwNU..G1rxNSq', $user->password);
        $this->assertEquals('pa510aHvNcT7cJ1kSy6zpr6JmIic0zGC', $user->authkey);
        $this->assertEquals('2015-02-04 12:40:32', $user->registration_date);
        $this->assertNull($user->token);
        
        $this->assertNull(User::findIdentity(999999));
    }
    
    public function testCanRetrieveAUserByEmail()
    {
        $user = User::findByEmail('jon@testing.com');
        $this->assertInstanceOf('\nickcv\usermanager\models\User', $user);
        $this->assertEquals(1, $user->id);
        $this->assertEquals('jon@testing.com', $user->email);
        $this->assertEquals('Jon', $user->firstname);
        $this->assertEquals('Doe', $user->lastname);
        $this->assertEquals(UserStatus::ACTIVE, $user->status);
        $this->assertEquals('$2y$13$cwekf6.a3BGpUXkHLZZFjueUBrEEiFjItTNiliwfwwNU..G1rxNSq', $user->password);
        $this->assertEquals('pa510aHvNcT7cJ1kSy6zpr6JmIic0zGC', $user->authkey);
        $this->assertEquals('2015-02-04 12:40:32', $user->registration_date);
        $this->assertNull($user->token);
        
        $this->assertNull(User::findByEmail('madeup@madeup.com'));
    }
    
    public function testCanValidateTheAuthkey()
    {
        $user = User::findIdentity(1);
        $this->assertTrue($user->validateAuthKey('pa510aHvNcT7cJ1kSy6zpr6JmIic0zGC'));
        $this->assertFalse($user->validateAuthKey('madeup'));
    }
    
    public function testCanValidatePassword()
    {
        $user = User::findIdentity(1);
        $this->assertTrue($user->validatePassword('easypassword'));
        $this->assertFalse($user->validatePassword('madeup'));
    }
    
    public function testAdminCreationRequiredFields()
    {
        $user = new User(['scenario' => Scenarios::ADMIN_CREATION]);
        $user->attributes = [];
        
        $this->assertFalse($user->save());
        $this->assertCount(5, $user->errors);
        $this->assertCount(1, $user->getErrors('firstname'));
        $this->assertContains('Firstname cannot be blank.', $user->getErrors('firstname'));
        $this->assertCount(1, $user->getErrors('lastname'));
        $this->assertContains('Lastname cannot be blank.', $user->getErrors('lastname'));
        $this->assertCount(1, $user->getErrors('email'));
        $this->assertContains('Email cannot be blank.', $user->getErrors('email'));
        $this->assertCount(1, $user->getErrors('password'));
        $this->assertContains('Password cannot be blank.', $user->getErrors('password'));
        $this->assertCount(1, $user->getErrors('role'));
        $this->assertContains('Role cannot be blank.', $user->getErrors('role'));
    }
    
    public function testAdminCreationEmailValidation()
    {
        $user = new User(['scenario' => Scenarios::ADMIN_CREATION]);
        $user->attributes = ['email' => 'madeup'];
        
        $this->assertFalse($user->save());
        $this->assertCount(1, $user->getErrors('email'));
        $this->assertContains('Email is not a valid email address.', $user->getErrors('email'));
        
        $user->clearErrors();
        $user->attributes = ['email' => 'jon@testing.com'];
        $this->assertFalse($user->save());
        $this->assertCount(1, $user->getErrors('email'));
        $this->assertContains('Email "jon@testing.com" has already been taken.', $user->getErrors('email'));
    }
    
    public function testAdminCreationRoleValidation()
    {
        $user = new User(['scenario' => Scenarios::ADMIN_CREATION]);
        $user->attributes = ['role' => 'madeup'];
        
        $this->assertFalse($user->save());
        $this->assertCount(1, $user->getErrors('role'));
        $this->assertContains('Role is invalid.', $user->getErrors('role'));
        
        $user->clearErrors();
        $user->attributes = ['role' => Roles::ADMIN];
        $this->assertFalse($user->save());
        $this->assertCount(0, $user->getErrors('role'));
        
        $user->clearErrors();
        $user->attributes = ['role' => Roles::SUPER_ADMIN];
        $this->assertFalse($user->save());
        $this->assertCount(0, $user->getErrors('role'));
    }

    public function testCanCreateNewAdmin()
    {
        $user = new User(['scenario' => Scenarios::ADMIN_CREATION]);
        $user->attributes = [
            'firstname' => 'Nic',
            'lastname' => 'Puddu',
            'email' => 'n.puddu@outlook.com',
            'password' => 'HardPassword12!',
            'role' => Roles::ADMIN,
        ];
        
        $this->assertTrue($user->save());
        
        $this->assertEquals(UserStatus::ACTIVE, $user->status);
        $this->assertNotEquals('HardPassword12!', $user->password);
        $this->assertNotNull($user->authkey);
        $this->assertTrue(\Yii::$app->security->validatePassword('HardPassword12!', $user->password));
        $this->assertEquals(3, $user->id);
        $this->assertTrue(\Yii::$app->authManager->checkAccess($user->id, Roles::ADMIN));
    }
    
    public function testCanCreateNewSuperAdmin()
    {
        $user = new User(['scenario' => Scenarios::ADMIN_CREATION]);
        $user->attributes = [
            'firstname' => 'Nic',
            'lastname' => 'Puddu',
            'email' => 'n.puddu@outlook.com',
            'password' => 'HardPassword12!',
            'role' => Roles::SUPER_ADMIN,
        ];
        
        $this->assertTrue($user->save());
        
        $this->assertEquals(UserStatus::ACTIVE, $user->status);
        $this->assertNotEquals('HardPassword12!', $user->password);
        $this->assertNotNull($user->authkey);
        $this->assertTrue(\Yii::$app->security->validatePassword('HardPassword12!', $user->password));
        $this->assertEquals(3, $user->id);
        $this->assertTrue(\Yii::$app->authManager->checkAccess($user->id, Roles::SUPER_ADMIN));
    }
}
