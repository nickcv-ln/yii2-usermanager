<?php
/**
 * Contains the behavior class used by the user model to pre-populate some
 * fields when creating a new user.
 * 
 * @link http://www.creationgears.com/
 * @copyright Copyright (c) 2015 Nicola Puddu
 * @license http://www.gnu.org/copyleft/gpl.html
 * @package nickcv/yii2-usermanager
 * @author Nicola Puddu <n.puddu@outlook.com>
 */
namespace nickcv\usermanager\behaviors;

use yii\db\ActiveRecord;
use yii\base\Event;
use yii\base\Behavior;
use yii\base\InvalidCallException;
use nickcv\usermanager\enums\Scenarios;
use nickcv\usermanager\enums\UserStatus;
use nickcv\usermanager\enums\Roles;

/**
 * This Behavior is used to pre-populate some attributes when creating a new
 * user.
 * 
 * To attach this behavior to an ActiveRecord add the following code
 * ```php
 *
 * public function behaviors()
 *  {
 *      return [
 *          'user' => '\nickcv\usermanager\behaviors\UserBehavior',
 *      ];
 *  }
 * ```
 * 
 * @author Nicola Puddu <n.puddu@outlook.com>
 * @version 1.0
 * @property \nickcv\usermanager\models\User $owner
 */
class UserBehavior extends Behavior
{
    /**
     * Adds to the behavior the listeners for the following events:
     * BEFORE_INSERT
     * 
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'prepopulateBeforeCreation',
            ActiveRecord::EVENT_AFTER_INSERT => 'updateRoles',
        ];
    }

    /**
     * Prepopulates the attributes for the user before creating a new record
     * based on the current scenario.
     * 
     * @param Event $event
     */
    public function prepopulateBeforeCreation(Event $event)
    {
        $this->checkIfActiveRecordIsUsermanagerUser();
        
        if ($this->owner->scenario === Scenarios::ADMIN_CREATION) {
            $this->owner->status = UserStatus::ACTIVE;
        } else {
            $this->owner->status = UserStatus::PENDING;
        }
        
        $this->owner->password = \Yii::$app->security->generatePasswordHash($this->owner->password);
        $this->owner->authkey = \Yii::$app->security->generateRandomString();
        
        $this->owner->registration_date = date('Y-m-d H:i:s');
    }
    
    /**
     * Update roles for current user
     * 
     * @param Event $event
     */
    public function updateRoles(Event $event)
    {
        \Yii::$app->authManager->revokeAll($this->owner->id);
        
        switch ($this->owner->scenario) {
            case Scenarios::ADMIN_CREATION:
                $role = \Yii::$app->authManager->getRole($this->owner->role);
                break;
            default:
                $role = \Yii::$app->authManager->getRole(Roles::STANDARD_USER);
                break;
        }
        
        \Yii::$app->authManager->assign($role, $this->owner->id);
    }

    /**
     * Checks if the class implementing the behavior is the
     * nickcv\usermanager\models\User model class and throws an exception if
     * that's not the case.
     * 
     * @throws InvalidCallException
     */
    private function checkIfActiveRecordIsUsermanagerUser()
    {
        if (($this->owner instanceof \nickcv\usermanager\models\User) === false) {
            throw new InvalidCallException('This behavior should only be used by the "nickcv\usermanager\models\User", "' . get_class($this->owner) . '" given.');
        }
    }

}
