<?php
/**
 * Contains the User entity for table "usermanager_user" which is also used as
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
use nickcv\usermanager\helpers\AuthHelper;

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
 * @property UserBans[] $bans
 * @property UserLogs[] $logs
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
        $scenarios[Scenarios::USER_EDITING] = ['firstname', 'lastname', 'email', 'password', 'role'];
        
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
            ['role', 'in', 'range' => [Roles::ADMIN, Roles::SUPER_ADMIN], 'on' => Scenarios::ADMIN_CREATION],
            ['role', 'notParentOfCurrentUser'],
        ];
    }
    
    /**
     * Checks wheter the given role exists and is not a parent of the current
     * user's role.
     * 
     * @param string $attribute
     * @return boolean
     */
    public function notParentOfCurrentUser($attribute)
    {
        if (!\Yii::$app->authManager->getRole($this->$attribute)) {
            $this->addError($attribute, 'The given role "' . $this->$attribute . '" does not exists.');
            return false;
        }
        
        $currentUserRole = \Yii::$app->authManager->getRolesByUser(\Yii::$app->user->id);
        if (array_key_exists($currentUserRole, AuthHelper::getChildrenRoles($this->$attribute))) {
            $this->addError($attribute, 'You cannot assign to another user a role that is inheriting yours.');
        }
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
    public function getBans()
    {
        return $this->hasMany(UserBans::className(), ['id_user' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLogs()
    {
        return $this->hasMany(UserLogs::className(), ['id_user' => 'id'])->orderBy('login_date DESC');
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
    
    /**
     * Returns the user full name.
     * 
     * @return string
     */
    public function getFullName()
    {
        $data = [];
        if ($this->firstname) {
            $data[] = $this->firstname;
        }
        if ($this->lastname) {
            $data[] = $this->lastname;
        }
        
        return implode(' ', $data);
    }

}
