<?php

use yii\db\Schema;
use yii\db\Migration;
use nickcv\usermanager\enums\Database;

class m150127_145713_usermanager_user extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        
        $this->createTable(Database::USER_TABLE, [
            'id' => Schema::TYPE_BIGINT . ' UNSIGNED AUTO_INCREMENT NOT NULL',
            'email' => Schema::TYPE_STRING . '(130) NOT NULL',
            'password' => Schema::TYPE_STRING . '(220) NOT NULL',
            'firstname' => Schema::TYPE_STRING . '(64)',
            'lastname' => Schema::TYPE_STRING . '(130)',
            'status' => Schema::TYPE_INTEGER . '(1) UNSIGNED NOT NULL',
            'token' => Schema::TYPE_STRING . '(130)',
            'registration_date' => Schema::TYPE_DATETIME,
            'PRIMARY KEY (id)',
        ], $tableOptions);

        $this->createTable(Database::USER_LOGS_TABLE, [
            'id_user' => Schema::TYPE_BIGINT . ' UNSIGNED NOT NULL',
            'ip' => Schema::TYPE_STRING . '(64) NOT NULL',
            'login_date' => Schema::TYPE_DATETIME,
            'FOREIGN KEY (id_user) REFERENCES ' . Database::USER_TABLE . ' (id) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);

        $this->createTable(Database::USER_BANS_TABLE, [
            'id_user' => Schema::TYPE_BIGINT . ' UNSIGNED NOT NULL',
            'message' => Schema::TYPE_STRING . '(200)',
            'expiration_date' => Schema::TYPE_DATETIME,
            'FOREIGN KEY (id_user) REFERENCES ' . Database::USER_TABLE . ' (id) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable(Database::USER_TABLE);
        $this->dropTable(Database::USER_LOGS_TABLE);
        $this->dropTable(Database::USER_BANS_TABLE);
    }
}
