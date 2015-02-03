<?php

namespace nickcv\usermanager\models;

use nickcv\usermanager\enums\Database;
use nickcv\usermanager\enums\Scenarios;

/**
 * This is the model class for table "usermanager_user".
 *
 * @property string $id
 * @property string $email
 * @property string $password
 * @property string $firstname
 * @property string $lastname
 * @property integer $status
 * @property string $token
 * @property string $registration_date
 *
 * @property UserBans[] $userBans
 * @property UserLogs[] $userLogs
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Database::USER_TABLE;
    }
    
    public function behaviors()
    {
        return [
            'user' => '\nickcv\usermanager\behaviors\UserBehavior',
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[Scenarios::ADMIN_CREATION] = ['firstname', 'lastname', 'email', 'password'];
        
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['firstname', 'lastname', 'email', 'password'], 'required'],
            ['email', 'email'],
            ['email', 'unique'],
            [['status'], 'integer'],
            [['registration_date'], 'safe'],
            [['email', 'lastname', 'token'], 'string', 'max' => 130],
            [['password'], 'string', 'max' => 220],
            ['password', '\nickcv\usermanager\validators\PasswordStrength'],
            [['firstname'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'password' => 'Password',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'status' => 'Status',
            'token' => 'Token',
            'registration_date' => 'Registration Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserBans()
    {
        return $this->hasMany(UserBans::className(), ['id_user' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserLogs()
    {
        return $this->hasMany(UserLogs::className(), ['id_user' => 'id']);
    }
}
