<?php
/**
 * Contains the enum class used for the permissions names.
 * 
 * @link http://www.creationgears.com/
 * @copyright Copyright (c) 2015 Nicola Puddu
 * @license http://www.gnu.org/copyleft/gpl.html
 * @package nickcv/yii2-usermanager
 * @author Nicola Puddu <n.puddu@outlook.com>
 */
namespace nickcv\usermanager\enums;

/**
 * This enums contains a constant for each of the module's permissions.
 * Each constant contains the permission unique name.
 * 
 * @author Nicola Puddu <n.puddu@outlook.com>
 * @version 1.0
 */
class Permissions extends BasicEnum
{
    const MODULE_MANAGEMENT = 'moduleManagement';
    const USER_MANAGEMENT = 'usersManagement';
    const ROLES_MANAGEMENT = 'rolesManagement';
    const PROFILE_EDITING = 'profileEditing';
}
