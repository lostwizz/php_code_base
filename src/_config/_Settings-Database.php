<?php

namespace php_base\Utils\Settings;

Settings::SetProtected('test', 'test_val');
//\whitehorse\MikesCommandAndControl2\Settings\Settings\Settings::SetProtected('test', 'test_val');

if (true) {
	/// standard debug logging
	Settings::SetProtected( 'Logging_Server', 'vm-db-prd4');
	Settings::SetProtected( 'Logging_Database', 'Mikes_Application_Store');
	Settings::SetProtected( 'Logging_DB_Username' , 'Mikes_DBLogging_User');
	Settings::SetProtected( 'Logging_DB_Password' , 'XXXXXXXXXXXXXXXXXXXX');
	Settings::SetProtected( 'Logging_DB_Table', 'Application_Log' );
	Settings::SetProtected( 'Logging_Type', 'sqlsrv');

	/// data only log into a db (i.e. for tracing who did what to a record and when
	Settings::SetProtected( 'Data_Logging_Server', 'vm-db-prd4');
	Settings::SetProtected( 'Data_Logging_Database', 'Mikes_Application_Store');
	Settings::SetProtected( 'Data_Logging_DB_Username' , 'Mikes_DBLogging_User');
	Settings::SetProtected( 'Data_Logging_DB_Password' , 'XXXXXXXXXXXXXXXXXXXX');
	Settings::SetProtected( 'Data_Logging_DB_Table', 'Application_Data_Log' );
	Settings::SetProtected( 'Data_Type', 'sqlsrv');

	Settings::SetProtected( 'DB_Server', 'vm-db-prd4');
	Settings::SetProtected( 'DB_Database', 'Mikes_Application_Store');
	Settings::SetProtected( 'DB_Username' , 'Mikes_DBLogging_User');
	Settings::SetProtected( 'DB_Password' , 'XXXXXXXXXXXXXXXXXX');
	Settings::SetProtected( 'DB_Type', 'sqlsrv');

} else {
	/// standard debug logging
	Settings::SetProtected( 'Logging_Server', '192.168.3.54');
	Settings::SetProtected( 'Logging_Database', 'Mikes_Application_Store');
	Settings::SetProtected( 'Logging_DB_Username' , 'Mikes_DBLogging_User');
	Settings::SetProtected( 'Logging_DB_Password' , 'XXXXXXXXXXXXXXXXXX');
	Settings::SetProtected( 'Logging_DB_Table', 'Application_Log' );
	Settings::SetProtected( 'Logging_Type', 'sqlsrv');

	/// data only log into a db (i.e. for tracing who did what to a record and when
	Settings::SetProtected( 'Data_Logging_Server', '192.168.3.54');
	Settings::SetProtected( 'Data_Logging_Database', 'Mikes_Application_Store');
	Settings::SetProtected( 'Data_Logging_DB_Username' , 'Mikes_DBLogging_User');
	Settings::SetProtected( 'Data_Logging_DB_Password' , 'XXXXXXXXXXXXXXXXX');
	Settings::SetProtected( 'Data_Logging_DB_Table', 'Application_Data_Log' );
	Settings::SetProtected( 'Data_Type', 'sqlsrv');

	Settings::SetProtected( 'DB_Server', '192.168.3.54');
	Settings::SetProtected( 'DB_Database', 'Mikes_Application_Store');
	Settings::SetProtected( 'DB_Username' , 'Mikes_DBLogging_User');
	Settings::SetProtected( 'DB_Password' , 'XXXXXXXXXXXXXXXXXXXXXXX');
	Settings::SetProtected( 'DB_Type', 'sqlsrv');
}

Settings::SetProtected( 'DB_Table_UserManager', 'UserManagement' );
Settings::SetProtected( 'DB_Table_RoleManager', 'RoleManagement' );
Settings::SetProtected( 'DB_Table_PermissionsManager', 'PermissionManagement' );
Settings::SetProtected( 'DB_Table_UserAttributes', 'UserAttributeManagement' );


Settings::SetProtected( 'Test_X', array( 'dsn' => 'sqlsrv:server=vm-db-prd4;database=Mikes_Application_Store',
										'username'  => 'Mikes_DBLogging_User',
										'password' => 'XXXXXXXXXXXXXXXXXXXXXXXX'
										)
					);


//$dsn =  'sqlsrv:server=' .  Settings::GetProtected( 'Logging_Server')   . ';database=' .  Settings::GetProtected( 'Logging_Database');

Settings::SetProtected( 'DB_DSN', "sqlsrv:server=VM-DB-PRD4;database=Mikes_Application_Store;username=Mikes_DBLogging_User;password=XXXXXXXXXXXXXX");
