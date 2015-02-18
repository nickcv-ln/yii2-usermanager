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
    public $parentRole;
    public $name;
    public $description;
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[Scenarios::ROLE_NEW] = ['name', 'description'];
        $scenarios[Scenarios::ROLE_ADD] = ['name', 'parentRole'];
        $scenarios[Scenarios::ROLE_DELETE] = ['name', 'parentRole'];
        
        return $scenarios;
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'description', 'parentRole'], 'required'],
            ['name', 'uniqueRole', 'on' => Scenarios::ROLE_NEW],
            ['name', 'match', 'pattern' => '/^[a-zA-Z_]+$/', 'message' => '{attribute} can only contain letters and underscore signs.'],
            [['parentRole', 'name'], 'roleExists', 'on' => [Scenarios::ROLE_ADD, Scenarios::ROLE_DELETE]],
            ['name', 'roleIsCore', 'on' => Scenarios::ROLE_DELETE],
            ['name', 'missingRole', 'on' => Scenarios::ROLE_ADD],
            ['parentRole', 'isParent', 'on' => Scenarios::ROLE_DELETE],
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
            $this->addError($attribute, 'The role "' . $this->$attribute . '" does not exists.');
        }
    }
    
    /**
     * Validation rules that checks whether the given missing role is not
     * inheriting or being inherited by the current role.
     * 
     * @param string $attribute the attribute name
     */
    public function missingRole($attribute)
    {   
        if (!is_string($this->$attribute)) {
            return $this->addError($attribute, $this->getAttributeLabel($attribute) . ' should be a role.');
        }
        if (!array_key_exists($this->$attribute, AuthHelper::getMissingRoles($this->parentRole))) {
            $this->addError($attribute, 'The given role "' . $this->parentRole . '" is already inheriting or being inherited by a role named "' . $this->$attribute . '".');
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
        if (\Yii::$app->authManager->getRole($this->$attribute)) {
            $this->addError($attribute, 'The role name should be unique, role "' . $this->$attribute .'" already exists.');
        }
        
        if (\Yii::$app->authManager->getPermission($this->$attribute)) {
            $this->addError($attribute, 'The role name should not match a permission name, a permission named "' . $this->$attribute .'" has been found.');
        }
    }
    
    /**
     * Validation rule that checks whether or not the user is trying to remove
     * a protected core child role.
     * 
     * @param string $attribute the attribute name
     */
    public function roleIsCore($attribute)
    {
        if (AuthHelper::isChildRoleProtected($this->parentRole, $this->$attribute)) {
            $this->addError($attribute, 'The role "' . $this->$attribute . '" is a core "' . $this->parentRole . '" child role and cannot be removed.');
        }
    }
    
    /**
     * Validation rule that checks wheter or not the existing role is a parent
     * of the current role.
     * 
     * @param string $attribute the attribute name
     */
    public function isParent($attribute)
    {
        if (!array_key_exists($this->name, AuthHelper::getChildrenRoles($this->$attribute))) {
            $this->addError($attribute, 'The role "' . $this->$attribute . '" is not a parent of role "' . $this->name . '".');
        }
    }
    
    /**
     * Adds the existing role if the model scenario is 
     * nickcv\usermanager\enums\Scenarios::ROLE_ADD and it passes validation.
     * 
     * @return boolean
     */
    public function addToParentRole()
    {
        if ($this->scenario !== Scenarios::ROLE_ADD || !$this->validate()) {
            return false;
        }
        
        $role = \Yii::$app->authManager->getRole($this->parentRole);
        \Yii::$app->authManager->addChild($role, \Yii::$app->authManager->getRole($this->name));
        
        return true;
    }
    
    /**
     * Creates a new role if the model scenario is 
     * nickcv\usermanager\enums\Scenarios::ROLE_NEW and it passes validation.
     * 
     * @return boolean
     */
    public function createNewRole()
    {
        if ($this->scenario !== Scenarios::ROLE_NEW || !$this->validate()) {
            return false;
        }
        
        $role = \Yii::$app->authManager->createRole($this->name);
        $role->description = $this->description;
        \Yii::$app->authManager->add($role);
        
        $roleClass = Module::EXTENDED_ROLES_CLASS;
        if (defined('YII_ENV') && YII_ENV === 'test') {
            $roleClass .= '_test';
        }
        
        EnumFilesService::init()->updateEnum($roleClass, [
            $this->name => $this->name,
        ], Roles::className());
        
        return true;
    }
    
    /**
     * Remove the given child role from the existing role if the current model scenario
     * is nickcv\usermanager\enums\Scenarios::ROLE_DELETE and it passes
     * validation.
     * 
     * @return boolean
     */
    public function removeChildRole()
    {
        if ($this->scenario !== Scenarios::ROLE_DELETE || !$this->validate()) {
            return false;
        }
        
        $role = \Yii::$app->authManager->getRole($this->parentRole);
        $child = \Yii::$app->authManager->getRole($this->name);
        \Yii::$app->authManager->removeChild($role, $child);
        
        return true;
    }
    
}
