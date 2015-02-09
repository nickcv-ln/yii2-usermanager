<?php
/**
 * Contains the enum class used for the possible user status.
 * 
 * @link http://www.creationgears.com/
 * @copyright Copyright (c) 2015 Nicola Puddu
 * @license http://www.gnu.org/copyleft/gpl.html
 * @package nickcv/yii2-usermanager
 * @author Nicola Puddu <n.puddu@outlook.com>
 */
namespace nickcv\usermanager\enums;

/**
 * This enums contains a constant for each of the possible user status.
 * Each constant contains the status unique identifier.
 * 
 * @author Nicola Puddu <n.puddu@outlook.com>
 * @version 1.0
 */
class UserStatus extends BasicEnum
{
    const BANNED = 0;
    const PENDING = 1;
    const ACTIVE = 2;
}
