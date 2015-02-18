<?php
/**
 * Contains the UserLogs entity for table "usermanager_user_logs".
 * 
 * @link http://www.creationgears.com/
 * @copyright Copyright (c) 2015 Nicola Puddu
 * @license http://www.gnu.org/copyleft/gpl.html
 * @package nickcv/yii2-usermanager
 * @author Nicola Puddu <n.puddu@outlook.com>
 */
namespace nickcv\usermanager\models;

use nickcv\usermanager\enums\Database;
use nickcv\usermanager\models\User;

/**
 * This is the model class for table "usermanager_user_logs".
 *
 * @property string $id
 * @property string $id_user
 * @property string $ip
 * @property string $login_date
 *
 * @property User $user
 */
class UserLogs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Database::USER_LOGS_TABLE;
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_user', 'ip'], 'required'],
            [['id_user'], 'integer'],
            ['id_user', 'exist', 'targetAttribute' => 'id', 'targetClass' => User::className()],
            ['login_date', 'default', 'value' => function() {
                return date('Y-m-d H:i:s');
            }],
            [['ip'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_user' => 'User ID',
            'ip' => 'IP',
            'login_date' => 'Login Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'id_user']);
    }
    
    /**
     * Add a new Log for the given user.
     * 
     * @param User $user
     * @return boolean
     */
    public static function addUserLogs(User $user)
    {
        $result = false;
        $newLog = new self;
        $newLog->id_user = $user->id;
        $newLog->ip = \Yii::$app->request->userIP;
        if ($newLog->validate()) {
            self::removeExceedingRecords($user);
            $result = $newLog->save();
        }
        
        unset($newLog);
        return $result;
    }
    
    /**
     * Removes the oldest log if there are already 10 or more.
     * 
     * @param User $user
     */
    private static function removeExceedingRecords(User $user)
    {
        if (UserLogs::find()->where(['id_user' => $user->id])->count() >= 10) {
            #var_dump(UserLogs::find()->where(['id_user' => $user->id])->orderBy('login_date ASC')->one());
            UserLogs::find()->where(['id_user' => $user->id])->orderBy('login_date ASC')->one()->delete();
        }
    }
}
