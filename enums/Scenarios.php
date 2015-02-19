<?php
/**
 * Contains the enum class used for the scenarios names used by models.
 * 
 * @link http://www.creationgears.com/
 * @copyright Copyright (c) 2015 Nicola Puddu
 * @license http://www.gnu.org/copyleft/gpl.html
 * @package nickcv/yii2-usermanager
 * @author Nicola Puddu <n.puddu@outlook.com>
 */
namespace nickcv\usermanager\enums;

/**
 * This enums contains a constant for each of the module's models scenarios.
 * Each constant contains the scenario unique name.
 * 
 * @author Nicola Puddu <n.puddu@outlook.com>
 * @version 1.0
 */
class Scenarios extends BasicEnum
{
    const LOGIN = 'login';
    const USER_REGISTRATION = 'userRegistration';
    const USER_CREATION = 'userCreation';
    const ADMIN_CREATION = 'adminCreation';
    const USER_EDITING = 'userEditing';
    const PERMISSION_ADD = 'permissionAdd';
    const PERMISSION_NEW = 'permissionNew';
    const PERMISSION_DELETE = 'permissionDelete';
    const ROLE_ADD = 'roleAdd';
    const ROLE_NEW = 'roleNew';
    const ROLE_DELETE = 'roleDelete';
}
