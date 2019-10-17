<?php

namespace php_base\Utils;

Settings::SetProtected('test', 'test_val');
//\whitehorse\MikesCommandAndControl2\Settings\Settings\Settings::SetProtected('test', 'test_val');


//see ..\_private_settings.php



Settings::SetProtected( 'DB_Table_UserManager', 'UserManagement' );
Settings::SetProtected( 'DB_Table_RoleManager', 'RoleManagement' );
Settings::SetProtected( 'DB_Table_PermissionsManager', 'PermissionManagement' );
Settings::SetProtected( 'DB_Table_UserAttributes', 'UserAttributeManagement' );




//$dsn =  'sqlsrv:server=' .  Settings::GetProtected( 'Logging_Server')   . ';database=' .  Settings::GetProtected( 'Logging_Database');

