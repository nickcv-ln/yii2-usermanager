<?php
/**
 * Contains the User entity for table "usermanager_user" which is also used ad
 * the user identity.
 * 
 * @link http://www.creationgears.com/
 * @copyright Copyright (c) 2015 Nicola Puddu
 * @license http://www.gnu.org/copyleft/gpl.html
 * @package nickcv/yii2-usermanager
 * @author Nicola Puddu <n.puddu@outlook.com>
 */
namespace nickcv\usermanager\models;

use nickcv\usermanager\enums\Database;
use nickcv\usermanager\enums\Scenarios;
use nickcv\usermanager\enums\Roles;
use yii\web\IdentityInterface;

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
 * @property string $authkey
 * @property string $registration_date
 *
 * @property UserBans[] $userBans
 * @property UserLogs[] $userLogs
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * Role used during user creation.
     * 
     * @var string
     */
    public $role;
    
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
        $scenarios[Scenarios::ADMIN_CREATION] = ['firstname', 'lastname', 'email', 'password', 'role'];
        
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['firstname', 'lastname', 'email', 'password', 'role'], 'required'],
            ['email', 'email'],
            ['email', 'unique'],
            [['status'], 'integer'],
            [['registration_date'], 'safe'],
            [['email', 'lastname', 'token'], 'string', 'max' => 130],
            [['password'], 'string', 'max' => 220],
            ['password', '\nickcv\usermanager\validators\PasswordStrength'],
            [['firstname'], 'string', 'max' => 64],
            ['role', 'in', 'range' => [Roles::ADMIN, Roles::SUPER_ADMIN]],
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
    
    /**
     * @inheritdoc
     * @return \nickcv\usermanager\models\User;
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }
    
    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * Find a user by email
     * 
     * @param string $email
     * @return \nickcv\usermanager\models\User
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }
    
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authkey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
    
    /**
     * Validates the given password against the current user.
     * 
     * @param string $password
     * @return boolean
     */
    public function validatePassword($password)
    {
        return \Yii::$app->security->validatePassword($password, $this->password);
    }

}
