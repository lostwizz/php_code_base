<?php

namespace php_base\Utils;

Settings::SetProtected('test', 'test_val');
//\whitehorse\MikesCommandAndControl2\Settings\Settings\Settings::SetProtected('test', 'test_val');


//see ..\_private_settings.php


Settings::SetProtected( 'DB_Table_UserManager', 'Users' );
Settings::SetProtected( 'DB_Table_RoleManager', 'Roles' );
Settings::SetProtected( 'DB_Table_PermissionsManager', 'RolePermission' );
Settings::SetProtected( 'DB_Table_UserAttributes', 'UserAttributeManagement' );
