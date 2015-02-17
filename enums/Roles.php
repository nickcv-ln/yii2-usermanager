<?php
/**
 * Contains the enum class used for the roles names.
 * 
 * @link http://www.creationgears.com/
 * @copyright Copyright (c) 2015 Nicola Puddu
 * @license http://www.gnu.org/copyleft/gpl.html
 * @package nickcv/yii2-usermanager
 * @author Nicola Puddu <n.puddu@outlook.com>
 */
namespace nickcv\usermanager\enums;

/**
 * This enums contains a constant for each of the module's roles.
 * Each constant contains the role unique name.
 * 
 * @author Nicola Puddu <n.puddu@outlook.com>
 * @version 1.0
 */
class Roles extends BasicEnum
{
    const SUPER_ADMIN = 'superAdmin';
    const ADMIN = 'admin';
    const STANDARD_USER = 'standardUser';
}
