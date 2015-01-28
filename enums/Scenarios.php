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
 * Each contant contains the scenario unique name.
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
}
