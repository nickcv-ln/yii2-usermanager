<?php

namespace nickcv\usermanager\models;

use nickcv\usermanager\enums\Database;

/**
 * This is the model class for table "usermanager_user_logs".
 *
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
            [['login_date'], 'safe'],
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
}
