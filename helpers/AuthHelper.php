<?php
/**
 * Contains AuthHelper class used to extract auth data with additional rules.
 * 
 * @link http://www.creationgears.com/
 * @copyright Copyright (c) 2015 Nicola Puddu
 * @license http://www.gnu.org/copyleft/gpl.html
 * @package nickcv/yii2-usermanager
 * @author Nicola Puddu <n.puddu@outlook.com>
 */
namespace nickcv\usermanager\helpers;

use yii\rbac\Role;
use yii\rbac\Permission;
use yii\data\ArrayDataProvider;
use nickcv\usermanager\enums\Roles;
use nickcv\usermanager\enums\Permissions;

/**
 * Helper used to retrieve different set of informations from Auth.
 * 
 * @author Nicola Puddu <n.puddu@outlook.com>
 * @version 1.0
 */
class AuthHelper
{
    private static $_children = [];
    private static $_missingPermissions = [];
    private static $_missingRoles = [];
    private static $_caching = true;
    
    /**
     * Disables the cache, which causes issues during testing.
     */
    public static function disableCache()
    {
        self::$_caching = false;
    }
    
    /**
     * Enables the cache.
     */
    public static function enableCache()
    {
        self::$_caching = true;
        self::$_children = [];
        self::$_missingPermissions = [];
    }
    
    /**
     * Returns all the Children Roles of the given role.
     * 
     * @param string $role the role to check
     * @param boolean $returnDataProvider
     * @return \yii\rbac\Role[]|\yii\data\ArrayDataProvider
     */
    public static function getChildrenRoles($role, $returnDataProvider = false)
    {
        $roles = [];
        
        foreach (self::getAllChildrenOfRole($role) as $k => $authItem) {
            if ($authItem instanceof Role) {
                $roles[$k] = $authItem;
                $roles += self::getChildrenRoles($authItem->name);
            }
        }
        
        if ($returnDataProvider) {
            return new ArrayDataProvider([
                'allModels' => $roles,
            ]);
        }
        
        return $roles;
    }
    
    /**
     * Returns all the direct permissions granted to a given role.
     * 
     * @param string $role the role to check
     * @param boolean $returnDataProvider
     * @return \yii\rbac\Permission[]|\yii\data\ArrayDataProvider
     */
    public static function getDirectPermissions($role, $returnDataProvider = false)
    {
        $permissions = [];
        
        foreach (self::getAllChildrenOfRole($role) as $k => $authItem) {
            if ($authItem instanceof Permission) {
                $permissions[$k] = $authItem;
            }
        }
        
        if ($returnDataProvider) {
            return new ArrayDataProvider([
                'allModels' => $permissions,
            ]);
        }
        
        return $permissions;
    }
    
    /**
     * Returns a list of permissions that the given role does not have.
     * 
     * @param string $role
     * @param boolean $returnDataProvider
     * @return \yii\rbac\Permission[]|\yii\data\ArrayDataProvider
     */
    public static function getMissingPermissions($role, $returnDataProvider = false)
    {
        if (!isset(self::$_missingPermissions[$role]) || self::$_caching === false) {
            $allPermissions = \Yii::$app->authManager->getPermissions();
            $rolePermissions = \Yii::$app->authManager->getPermissionsByRole($role);

            self::$_missingPermissions[$role] = array_diff_key($allPermissions, $rolePermissions);
        }
        
        if ($returnDataProvider) {
            return new ArrayDataProvider([
                'allModels' => self::$_missingPermissions[$role],
            ]);
        }
        
        return self::$_missingPermissions[$role];
    }
    
    /**
     * Returns the list of roles that the given role is currently not hineriting
     * or that are not inheriting the given role.
     * 
     * @param string $role
     * @param boolean $returnDataProvider
     * @return \yii\rbac\Role[]|\yii\data\ArrayDataProvider
     */
    public static function getMissingRoles($role, $returnDataProvider = false)
    {
        if (!isset(self::$_missingRoles[$role]) || self::$_caching === false) {
            
            self::$_missingRoles[$role] = [];
            
            foreach (array_diff_key(\Yii::$app->authManager->getRoles(), self::getChildrenRoles($role)) as $k => $r) {
                if ($r->name === $role || array_key_exists($role, self::getChildrenRoles($r->name))) {
                    continue;
                }

                self::$_missingRoles[$role][$k] = $r;
            }
        }
        
        if ($returnDataProvider) {
            return new ArrayDataProvider([
                'allModels' => self::$_missingRoles[$role],
            ]);
        }
        
        return self::$_missingRoles[$role];
    }
    
    /**
     * Returns all direct and inherited permissions for the given role.
     * 
     * @param string $role
     * @param boolean $returnDataProvider
     * @return \yii\rbac\Permission[]|\yii\data\ArrayDataProvider
     */
    public static function getAllPermissions($role, $returnDataProvider = false)
    {
        $permissions = \Yii::$app->authManager->getPermissionsByRole($role);
        
        if ($returnDataProvider) {
            return new ArrayDataProvider([
                'allModels' => $permissions,
            ]);
        }
        
        return $permissions;
    }
    
    /**
     * Checks whether the given permission is protected for the given role.
     * 
     * @param string $role
     * @param string $permission
     * @return boolean
     */
    public static function isRolePermissionProtected($role, $permission)
    {
        switch ($role) {
            case Roles::STANDARD_USER:
                return self::isStandardUserPermissionProtected($permission);
            case Roles::ADMIN:
                return self::isAdminPermissionProtected($permission);
            case Roles::SUPER_ADMIN:
                return self::isSuperAdminPermissionProtected($permission);
            default:
                return false;
        }
    }
    
    /**
     * Returns all the children of a given role.
     * 
     * @param string $role the role name
     * @return array
     */
    private static function getAllChildrenOfRole($role)
    {
        if (!isset(self::$_children[$role]) || self::$_caching === false) {
            self::$_children[$role] = \Yii::$app->authManager->getChildren($role);
        }
        
        return self::$_children[$role];
    }
    
    /**
     * Returns whether the given permission is protected for the StandardUser role.
     * 
     * @param string $permission
     * @return boolean
     */
    private static function isStandardUserPermissionProtected($permission)
    {
        if ($permission === Permissions::PROFILE_EDITING) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Returns whether the given permission is protected for the Admin role.
     * 
     * @param string $permission
     * @return boolean
     */
    private static function isAdminPermissionProtected($permission)
    {
        if ($permission === Permissions::USER_MANAGEMENT) {
            return true;
        }
        
        return self::isStandardUserPermissionProtected($permission);
    }
    
    /**
     * Returns whether the given permission is protected for the SuperAdmin role.
     * 
     * @param string $permission
     * @return boolean
     */
    private static function isSuperAdminPermissionProtected($permission)
    {
        switch ($permission) {
            case Permissions::ROLES_MANAGEMENT:
            case Permissions::MODULE_MANAGEMENT:
                return true;
            default:
                return self::isAdminPermissionProtected($permission);
        }
    }
}
