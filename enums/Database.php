<?php
/**
 * Contains the enum class used for the database table names.
 * 
 * @link http://www.creationgears.com/
 * @copyright Copyright (c) 2015 Nicola Puddu
 * @license http://www.gnu.org/copyleft/gpl.html
 * @package nickcv/yii2-usermanager
 * @author Nicola Puddu <n.puddu@outlook.com>
 */
namespace nickcv\usermanager\enums;

/**
 * This enums contains a constant for each of the module's tables.
 * Each constant contains that table name.
 * 
 * @author Nicola Puddu <n.puddu@outlook.com>
 * @version 1.0
 */
class Database extends BasicEnum
{
    const USER_TABLE = 'usermanager_user';
    const USER_LOGS_TABLE = 'usermanager_user_logs';
    const USER_BANS_TABLE = 'usermanager_user_bans';
}
