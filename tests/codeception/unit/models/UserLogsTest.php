<?php

namespace nickcv\usermanager\tests\codeception\unit\models;

use yii\codeception\DbTestCase;
use nickcv\usermanager\tests\codeception\unit\fixtures\UserLogsFixture;
use nickcv\usermanager\models\UserLogs;
use nickcv\usermanager\models\User;

class UserLogsTest extends DbTestCase
{

    public function fixtures()
    {
        return [
            'userLogs' => UserLogsFixture::className(),
        ];
    }
    
    public function testCannotCreateALogIfUserIdNull()
    {
        $this->assertFalse(UserLogs::addUserLogs(new User));
    }
    
    public function testCannotCreateALogIfUserDoesNotExist()
    {
        $user = new User;
        $user->id = 450;
        
        $this->assertFalse(UserLogs::addUserLogs($user));
    }

    public function testCanCreateALog()
    {
        $user = new User;
        $user->id = 1;
        
        $this->assertTrue(UserLogs::addUserLogs($user));
        $this->assertEquals(1, UserLogs::find()->where(['id_user' => $user->id])->count());
    }
    
    public function testThereCannotBeMoreThanTenLogs()
    {
        $user = new User;
        $user->id = 1;
        
        for($i = 1; $i <= 13; $i++) {
            $this->assertTrue(UserLogs::addUserLogs($user));
            $currentTotal = $i > 10 ? 10 : $i;
            $this->assertEquals($currentTotal, UserLogs::find()->where(['id_user' => $user->id])->count());
            if ($i > 10) {
                $this->assertNull(UserLogs::findOne(['id' => $i - 10]));
            }
        }
        
        
        
        
    }
}
