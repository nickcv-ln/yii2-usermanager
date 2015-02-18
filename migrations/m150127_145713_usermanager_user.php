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

        $this->createTable(Database::USER_TABLE, $this->getUserTableColumns(), $tableOptions);

        $this->createTable(Database::USER_LOGS_TABLE, $this->getUserLogsTableColumns(), $tableOptions);

        $this->createTable(Database::USER_BANS_TABLE, $this->getUserBansTableColumns(), $tableOptions);
    }

    public function down()
    {
        $this->dropTable(Database::USER_TABLE);
        $this->dropTable(Database::USER_LOGS_TABLE);
        $this->dropTable(Database::USER_BANS_TABLE);
    }

    /**
     * Returns the user table columns definition
     * 
     * @return array
     */
    private function getUserTableColumns()
    {
        switch ($this->db->driverName) {
            case 'sqlite':
                return [
                    'id' => Schema::TYPE_INTEGER . ' PRIMARY KEY AUTOINCREMENT NOT NULL',
                    'email' => Schema::TYPE_STRING . '(130) NOT NULL',
                    'password' => Schema::TYPE_STRING . '(220) NOT NULL',
                    'firstname' => Schema::TYPE_STRING . '(64)',
                    'lastname' => Schema::TYPE_STRING . '(130)',
                    'status' => Schema::TYPE_INTEGER . '(1) UNSIGNED NOT NULL',
                    'authkey' => Schema::TYPE_STRING . '(32) NOT NULL',
                    'token' => Schema::TYPE_STRING . '(130)',
                    'registration_date' => Schema::TYPE_DATETIME,
                ];
            default:
                return [
                    'id' => Schema::TYPE_BIGINT . ' UNSIGNED AUTO_INCREMENT NOT NULL',
                    'email' => Schema::TYPE_STRING . '(130) NOT NULL',
                    'password' => Schema::TYPE_STRING . '(220) NOT NULL',
                    'firstname' => Schema::TYPE_STRING . '(64)',
                    'lastname' => Schema::TYPE_STRING . '(130)',
                    'status' => Schema::TYPE_INTEGER . '(1) UNSIGNED NOT NULL',
                    'authkey' => Schema::TYPE_STRING . '(120) NOT NULL',
                    'token' => Schema::TYPE_STRING . '(130)',
                    'registration_date' => Schema::TYPE_DATETIME,
                    'PRIMARY KEY (id)'
                ];
        }
    }
    
    /**
     * Returns the user logs table columns definition
     * 
     * @return array
     */
    private function getUserLogsTableColumns()
    {
        switch ($this->db->driverName) {
            case 'sqlite':
                return [
                    'id' => Schema::TYPE_INTEGER . ' PRIMARY KEY AUTOINCREMENT NOT NULL',
                    'id_user' => $this->getIdUserDefinition(),
                    'ip' => Schema::TYPE_STRING . '(64) NOT NULL',
                    'login_date' => Schema::TYPE_DATETIME,
                    'FOREIGN KEY (id_user) REFERENCES ' . Database::USER_TABLE . ' (id) ON DELETE CASCADE ON UPDATE CASCADE',
                ];
            default:
                return [
                    'id' => Schema::TYPE_BIGINT . ' UNSIGNED AUTO_INCREMENT NOT NULL',
                    'id_user' => $this->getIdUserDefinition(),
                    'ip' => Schema::TYPE_STRING . '(64) NOT NULL',
                    'login_date' => Schema::TYPE_DATETIME,
                    'FOREIGN KEY (id_user) REFERENCES ' . Database::USER_TABLE . ' (id) ON DELETE CASCADE ON UPDATE CASCADE',
                    'PRIMARY KEY (id)'
                ];
        }
    }
    
    /**
     * Returns the user_bans table columns definition
     * 
     * @return array
     */
    private function getUserBansTableColumns()
    {
        switch ($this->db->driverName) {
            case 'sqlite':
                return [
                    'id' => Schema::TYPE_INTEGER . ' PRIMARY KEY AUTOINCREMENT NOT NULL',
                    'id_user' => $this->getIdUserDefinition(),
                    'message' => Schema::TYPE_STRING . '(200)',
                    'expiration_date' => Schema::TYPE_DATETIME,
                    'FOREIGN KEY (id_user) REFERENCES ' . Database::USER_TABLE . ' (id) ON DELETE CASCADE ON UPDATE CASCADE',
                ];
            default:
                return [
                    'id' => Schema::TYPE_BIGINT . ' UNSIGNED AUTO_INCREMENT NOT NULL',
                    'id_user' => $this->getIdUserDefinition(),
                    'message' => Schema::TYPE_STRING . '(200)',
                    'expiration_date' => Schema::TYPE_DATETIME,
                    'FOREIGN KEY (id_user) REFERENCES ' . Database::USER_TABLE . ' (id) ON DELETE CASCADE ON UPDATE CASCADE',
                    'PRIMARY KEY (id)'
                ];
        }
    }

    /**
     * Returns the id_user definition based on the driver
     * 
     * @return string
     */
    private function getIdUserDefinition()
    {
        switch ($this->db->driverName) {
            case 'sqlite':
                return Schema::TYPE_INTEGER . ' NOT NULL';
            default:
                return Schema::TYPE_BIGINT . ' UNSIGNED NOT NULL';
        }
    }
    
}
