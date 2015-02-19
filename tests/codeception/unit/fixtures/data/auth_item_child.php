<?php
use nickcv\usermanager\enums\Roles;
use nickcv\usermanager\enums\Permissions;

return [
    'child1' => [
        'parent' => Roles::SUPER_ADMIN,
        'child' => Roles::ADMIN,
    ],
    'child2' => [
        'parent' => Roles::SUPER_ADMIN,
        'child' => Permissions::MODULE_MANAGEMENT,
    ],
    'child3' => [
        'parent' => Roles::STANDARD_USER,
        'child' => Permissions::PROFILE_EDITING,
    ],
    'child4' => [
        'parent' => Roles::SUPER_ADMIN,
        'child' => Permissions::ROLES_MANAGEMENT,
    ],
    'child5' => [
        'parent' => Roles::ADMIN,
        'child' => Roles::STANDARD_USER,
    ],
    'child6' => [
        'parent' => Roles::ADMIN,
        'child' => Permissions::USER_MANAGEMENT,
    ],
];