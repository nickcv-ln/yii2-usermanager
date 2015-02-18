<?php
/**
 * Contains the RoleForm class used to add new and existing permissions to roles.
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
use nickcv\usermanager\enums\Roles;

/**
 * RoleForm is the form behind the new roles creation and the existing
 * roles binding.
 * 
 * @author Nicola Puddu <n.puddu@outlook.com>
 * @version 1.0
 */
class RoleForm extends Model
{
    public $existingRoles;
    public $name;
    public $description;
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[Scenarios::PERMISSION_NEW] = ['name', 'description'];
        $scenarios[Scenarios::PERMISSION_ADD] = ['existingRoles'];
        $scenarios[Scenarios::PERMISSION_DELETE] = ['name'];
        
        return $scenarios;
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'description', 'existingRoles'], 'required'],
            ['name', 'uniqueRole', 'on' => Scenarios::PERMISSION_NEW],
            ['name', 'roleExists', 'on' => Scenarios::PERMISSION_DELETE],
            ['name', 'roleIsCore', 'on' => Scenarios::PERMISSION_DELETE],
            ['existingRoles', 'missingRole'],
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
     * Validation rules that checks whether the given list of missing roles
     * is indeed a list and if the current role really does not already inherit
     * them.
     * 
     * @param string $attribute the attribute name
     */
    public function missingRole($attribute)
    {
        if (!is_array($this->$attribute)) {
            return $this->addError($attribute, $this->getAttributeLabel($attribute) . ' should be a list of roles.');
        }
        
        foreach ($this->$attribute as $role) {
            if (!is_string($role)) {
                return $this->addError($attribute, $this->getAttributeLabel($attribute) . ' should be a list of roles.');
            }
            if (!array_key_exists($role, AuthHelper::getMissingPermissions($this->name))) {
                $this->addError($attribute, 'The given role "' . $this->name . '" is already inheriting a role named "' . $role . '".');
            }
        }
    }
    
    /**
     * Validation rule that checks whether the role with the given name does not
     * already exists.
     * 
     * @param string $attribute the attribute name
     */
    public function uniqueRole($attribute)
    {
        if (\Yii::$app->authManager->getPermission($this->$attribute)) {
            $this->addError($attribute, 'The role name should be unique, role "' . $this->$attribute .'" already exists.');
        }
    }
    
    /**
     * Validation rule that checks whether or not the user is trying to remove
     * a protected core role.
     * 
     * @param string $attribute the attribute name
     */
    public function roleIsCore($attribute)
    {
        switch ($this->$attribute) {
            case Roles::STANDARD_USER:
            case Roles::ADMIN:
            case Roles::SUPER_ADMIN:
                $this->addError($attribute, 'The role "' . $this->$attribute . '" is a core role and cannot be removed.');
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
