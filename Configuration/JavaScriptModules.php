<?php

return [
    'dependencies' => ['backend'],
    'imports' => [
        '@p2media/be-acl/acl-permissions.js' => 'EXT:be_acl/Resources/Public/JavaScript/AclPermissions.js',
        '@p2media/be-acl/acl-permissions-extended.js' => 'EXT:be_acl/Resources/Public/JavaScript/AclPermissionsExtended.js',
    ],
];
