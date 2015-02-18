<?php
/**
 * Contains the UserBans entity for table "usermanager_user_bans".
 * 
 * @link http://www.creationgears.com/
 * @copyright Copyright (c) 2015 Nicola Puddu
 * @license http://www.gnu.org/copyleft/gpl.html
 * @package nickcv/yii2-usermanager
 * @author Nicola Puddu <n.puddu@outlook.com>
 */
namespace nickcv\usermanager\models;

use nickcv\usermanager\enums\Database;

/**
 * This is the model class for table "usermanager_user_bans".
 *
 * @property string $id
 * @property string $id_user
 * @property string $message
 * @property string $expiration_date
 *
 * @property User $user
 */
class UserBans extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Database::USER_BANS_TABLE;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_user'], 'required'],
            [['id_user'], 'integer'],
            [['expiration_date'], 'safe'],
            [['message'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_user' => 'User ID',
            'message' => 'Message',
            'expiration_date' => 'Expiration Date',
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
