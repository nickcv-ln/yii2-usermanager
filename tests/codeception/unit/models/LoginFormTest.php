<?php

namespace nickcv\usermanager\tests\codeception\unit\models;

use Yii;
use yii\codeception\DbTestCase;
use nickcv\usermanager\tests\codeception\unit\fixtures\UserFixture;
use nickcv\usermanager\forms\LoginForm;
use Codeception\Specify;

class LoginFormTest extends DbTestCase
{
    use Specify;
    
    public function fixtures()
    {
        return [
            'users' => UserFixture::className(),
        ];
    }

    protected function tearDown()
    {
        Yii::$app->user->logout();
        parent::tearDown();
    }

    public function testLoginNoUser()
    {
        $model = new LoginForm([
            'email' => 'not_existing_email',
            'password' => 'not_existing_password',
        ]);

        $this->specify('user should not be able to login, when there is no identity', function () use ($model) {
            expect('model should not login user', $model->login())->false();
            expect('user should not be logged in', Yii::$app->user->isGuest)->true();
        });
    }

    public function testLoginWrongPassword()
    {
        $model = new LoginForm([
            'email' => 'jon@testing.com',
            'password' => 'wrong_password',
        ]);

        $this->specify('user should not be able to login with wrong password', function () use ($model) {
            expect('model should not login user', $model->login())->false();
            expect('error message should be set', $model->errors)->hasKey('password');
            expect('user should not be logged in', Yii::$app->user->isGuest)->true();
        });
    }

    public function testLoginCorrect()
    {
        $model = new LoginForm([
            'email' => 'jon@testing.com',
            'password' => 'easypassword',
        ]);

        $this->specify('user should be able to login with correct credentials', function () use ($model) {
            expect('model should login user', $model->login())->true();
            expect('error message should not be set', $model->errors)->hasntKey('password');
            expect('user should be logged in', Yii::$app->user->isGuest)->false();
        });
    }

}
