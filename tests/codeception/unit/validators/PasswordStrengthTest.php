<?php
namespace nickcv\usermanager\tests\codeception\unit\validators;

use yii\codeception\TestCase;
use nickcv\usermanager\Module;
use nickcv\usermanager\validators\PasswordStrength;
use nickcv\usermanager\models\User;
use nickcv\usermanager\enums\PasswordStrength as PS;
use nickcv\usermanager\services\ConfigFilesService;


class PasswordStrengthTest extends TestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testValidateWeakPassword()
    {
        \Yii::$app->getModule('usermanager')->passwordStrength = PS::WEAK;
        $validator = new PasswordStrength();
        $model = new User;
        $model->password = '';
        
        $this->assertFalse($validator->validateAttribute($model, 'password'));
        $this->assertTrue($model->hasErrors('password'));
        $this->assertContains('The password must be of at least 8 characters.', $model->getErrors('password'));
        
        $model->password = 'asdfqwer';
        $model->clearErrors();
        
        $this->assertTrue($validator->validateAttribute($model, 'password'));
        $this->assertFalse($model->hasErrors('password'));
    }
    
    public function testValidateMediumPassword()
    {
        \Yii::$app->getModule('usermanager')->passwordStrength = PS::MEDIUM;
        
        $validator = new PasswordStrength();
        $model = new User;
        
        $passwords = [
            '',
            '        ',
            'asdfqwer',
            '12345678',
            'asdfq4e',
        ];
        
        foreach ($passwords as $p) {
            $model->password = $p;
            $model->clearErrors();
            
            $this->assertFalse($validator->validateAttribute($model, 'password'));
            $this->assertTrue($model->hasErrors('password'));
            $this->assertContains('The password must be of at least 8 characters with at least 1 number, and 1 letter.', $model->getErrors('password'));
        }
        
        $model->password = 'asd5qwer';
        $model->clearErrors();
        
        $this->assertTrue($validator->validateAttribute($model, 'password'));
        $this->assertFalse($model->hasErrors('password'));
    }
    
    public function testValidateStrongPassword()
    {
        \Yii::$app->getModule('usermanager')->passwordStrength = PS::STRONG;
        
        $validator = new PasswordStrength();
        $model = new User;
        
        $passwords = [
            '',
            '          ',
            'asdfqwerty',
            'asdfqwe5ty',
            'asdfqweRty',
            'asdfq4eRt',
        ];
        
        foreach ($passwords as $p) {
            $model->password = $p;
            $model->clearErrors();

            $this->assertFalse($validator->validateAttribute($model, 'password'));
            $this->assertTrue($model->hasErrors('password'));
            $this->assertContains('The password must be of at least 10 characters with at least 1 number, 1 uppercase letter, 1 lowercase letter.', $model->getErrors('password'));
        }
        
        $model->password = 'asdf5wErty';
        $model->clearErrors();
        
        $this->assertTrue($validator->validateAttribute($model, 'password'));
        $this->assertFalse($model->hasErrors('password'));
    }
    
    public function testValidateSecurePassword()
    {
        \Yii::$app->getModule('usermanager')->passwordStrength = PS::SECURE;
        
        $validator = new PasswordStrength();
        $model = new User;
        
        $passwords = [
            '',
            '            ',
            'asdfqwertyzx',
            'asdf5wertyzx',
            'asdfdwerTyzx',
            'asdfdwer!yzx',
            'asdfDwer!yzx',
            'asdf5wer!yzx',
            'asdf5wEr!yz',
        ];
        
        foreach ($passwords as $p) {
            $model->password = $p;
            $model->clearErrors();
        
            $this->assertFalse($validator->validateAttribute($model, 'password'));
            $this->assertTrue($model->hasErrors('password'));
            $this->assertContains('The password must be of at least 12 characters with at least 1 number, 1 uppercase letter, 1 lowercase letter, 1 special character.', $model->getErrors('password'));
        }
        
        $model->password = 'asdf5weRty!x';
        $model->clearErrors();
        
        $this->assertTrue($validator->validateAttribute($model, 'password'));
        $this->assertFalse($model->hasErrors('password'));
    }

}