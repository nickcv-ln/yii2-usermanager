<?php

namespace nickcv\usermanager\tests\codeception\unit\models;

use yii\codeception\DbTestCase;
use nickcv\usermanager\tests\codeception\unit\fixtures\UserFixture;
use nickcv\usermanager\models\User;
use nickcv\usermanager\enums\UserStatus;
use nickcv\usermanager\enums\Scenarios;

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

    public function testCanCreateNewAdmin()
    {
        $user = new User(['scenario' => Scenarios::ADMIN_CREATION]);
        $user->attributes = [];
        
        $this->assertFalse($user->save());
    }
}
