<?php
/**
 * Contains the PermissionForm class used to add new and existing permissions to roles.
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
use nickcv\usermanager\Module;
use nickcv\usermanager\enums\Permissions;

/**
 * PermissionForm is the form behind the new permissions creation and the existing
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
        $scenarios[Scenarios::PERMISSION_DELETE] = ['role', 'name'];
        
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
            ['name', 'uniquePermission', 'on' => Scenarios::PERMISSION_NEW],
            ['name', 'permissionExists', 'on' => Scenarios::PERMISSION_DELETE],
            ['name', 'permissionIsCore', 'on' => Scenarios::PERMISSION_DELETE],
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
     * Validation rule that checks whether the permission with the given name does not
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
     * Validation rule that checks whether the given permission exists or not.
     * 
     * @param string $attribute the attribute name
     */
    public function permissionExists($attribute)
    {
        if (\Yii::$app->authManager->getPermission($this->$attribute) === null) {
            $this->addError($attribute, 'The permission "' . $this->$attribute .'" does not exists.');
        }
    }
    
    /**
     * Validation rule that checks whether or not the user is trying to remove
     * a protected core permission.
     * 
     * @param string $attribute the attribute name
     */
    public function permissionIsCore($attribute)
    {
        if (AuthHelper::isRolePermissionProtected($this->role, $this->$attribute)) {
            $this->addError($attribute, 'The permission "' . $this->$attribute . '" is a core "' . $this->role . '" permission and cannot be removed.');
        }
    }
    
    /**
     * Adds the existing permissions if the model scenario is 
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
        
        $permissionClass = Module::EXTENDED_PERMISSIONS_CLASS;
        if (defined('YII_ENV') && YII_ENV === 'test') {
            $permissionClass .= '_test';
        }
        
        EnumFilesService::init()->updateEnum($permissionClass, [
            $this->name => $this->name,
        ], Permissions::className());
        
        return true;
    }
    
    /**
     * Remove the given permission from the given role if the current model scenario
     * is nickcv\usermanager\enums\Scenarios::PERMISSION_DELETE and it passes
     * validation.
     * 
     * @return boolean
     */
    public function removePermission()
    {
        if ($this->scenario !== Scenarios::PERMISSION_DELETE || !$this->validate()) {
            return false;
        }
        
        $role = \Yii::$app->authManager->getRole($this->role);
        $permission = \Yii::$app->authManager->getPermission($this->name);
        \Yii::$app->authManager->removeChild($role, $permission);
        
        return true;
    }
    
}
