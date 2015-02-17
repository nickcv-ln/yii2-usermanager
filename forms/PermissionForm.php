<?php
/**
 * Contains the PermissionsForm class used to add new and existing permissions to roles.
 * 
 * @link http://www.creationgears.com/
 * @copyright Copyright (c) 2015 Nicola Puddu
 * @license http://www.gnu.org/copyleft/gpl.html
 * @package nickcv/yii2-usermanager
 * @author Nicola Puddu <n.puddu@outlook.com>
 */
namespace nickcv\usermanager\forms;

use yii\base\Model;
use nickcv\usermanager\enums\Scenarios;
use nickcv\usermanager\helpers\AuthHelper;
use nickcv\usermanager\services\EnumFilesService;

/**
 * PermissionsForm is the form behind the new permissions creation and the existing
 * permissions binding.
 * 
 * @author Nicola Puddu <n.puddu@outlook.com>
 * @version 1.0
 */
class PermissionForm extends Model
{
    public $role;
    public $existingPermissions;
    public $name;
    public $description;
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[Scenarios::PERMISSION_NEW] = ['role', 'name', 'description'];
        $scenarios[Scenarios::PERMISSION_ADD] = ['role', 'existingPermissions'];
        
        return $scenarios;
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['role', 'name', 'description', 'existingPermissions'], 'required'],
            ['role', 'roleExists'],
            ['name', 'uniquePermission'],
            ['existingPermissions', 'missingPermission'],
        ];
    }
    
    /**
     * Validation rules that checks whether the given role exists or not.
     * 
     * @param string $attribute the attribute name
     */
    public function roleExists($attribute)
    {
        if (!\Yii::$app->authManager->getRole($this->$attribute)) {
            $this->addError($attribute, 'The given role "' . $this->$attribute . '" does not exist.');
        }
    }
    
    /**
     * Validation rules that checks whether the given list of missing permissions
     * is indeed a list and if the current role really does not already have
     * them.
     * 
     * @param string $attribute the attribute name
     */
    public function missingPermission($attribute)
    {
        if (!is_array($this->$attribute)) {
            return $this->addError($attribute, $this->getAttributeLabel($attribute) . ' should be a list of permissions.');
        }
        
        foreach ($this->$attribute as $permission) {
            if (!is_string($permission)) {
                return $this->addError($attribute, $this->getAttributeLabel($attribute) . ' should be a list of permissions.');
            }
            if (!array_key_exists($permission, AuthHelper::getMissingPermissions($this->role))) {
                $this->addError($attribute, 'The given role "' . $this->role . '" already has a permission named "' . $permission . '".');
            }
        }
    }
    
    /**
     * Validation rule that checks whether the given permission name does not
     * already exists.
     * 
     * @param string $attribute the attribute name
     */
    public function uniquePermission($attribute)
    {
        if (\Yii::$app->authManager->getPermission($this->$attribute)) {
            $this->addError($attribute, 'The permission name should be unique, permission "' . $this->$attribute .'" already exists.');
        }
    }
    
    /**
     * Adds the new permissions if the model scenario is 
     * nickcv\usermanager\enums\Scenarios::PERMISSION_ADD and it passes validation.
     * 
     * @return boolean
     */
    public function addExistingPermissions()
    {
        if ($this->scenario !== Scenarios::PERMISSION_ADD || !$this->validate()) {
            return false;
        }
        
        $role = \Yii::$app->authManager->getRole($this->role);
        foreach ($this->existingPermissions as $permission) {
            \Yii::$app->authManager->addChild($role, \Yii::$app->authManager->getPermission($permission));
        }
        
        return true;
    }
    
    /**
     * Creates a new permission and assigns it to the the current role if the
     * model scenario is nickcv\usermanager\enums\Scenarios::PERMISSION_NEW and
     * it passes validation.
     * 
     * @return boolean
     */
    public function createNewPermission()
    {
        if ($this->scenario !== Scenarios::PERMISSION_NEW || !$this->validate()) {
            return false;
        }
        
        $role = \Yii::$app->authManager->getRole($this->role);
        $permission = \Yii::$app->authManager->createPermission($this->name);
        $permission->description = $this->description;
        \Yii::$app->authManager->add($permission);
        \Yii::$app->authManager->addChild($role, $permission);
        
        EnumFilesService::init()->updateEnum('ExtendedPermissions', [
            $this->name => $this->name,
        ], '\nickcv\usermanager\enums\Permissions');
        
        return true;
    }
    
}
