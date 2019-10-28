<?php

namespace php_base\Utils;

Settings::SetProtected('test', 'test_val');
//\whitehorse\MikesCommandAndControl2\Settings\Settings\Settings::SetProtected('test', 'test_val');


//see ..\_private_settings.php


Settings::SetProtected( 'DB_Table_UserManager', 'Users' );
Settings::SetProtected( 'DB_Table_RoleManager', 'Roles' );
Settings::SetProtected( 'DB_Table_PermissionsManager', 'RolePermissions' );
Settings::SetProtected( 'DB_Table_UserAttributes', 'UserAttributes' );



////SELECT TOP (1000) [UserId]
////      ,[app]
////      ,[method]
////      ,[username]
////      ,[password]
////      ,[PrimaryRoleName]
////      ,[ip]
////      ,[last_logon_time]
////  FROM [Mikes_Application_Store].[dbo].[Users]
////
////  UserId	app	method	username	password	PrimaryRoleName	ip	last_logon_time
////	1	TestApp	DB_Table	merrem	NULL	DBA	NULL	NULL
////	2	TestApp	DB_Table	merremtest	NULL	Clerk	NULL	NULL
////
////
////
////SELECT TOP (1000) [RoleId]
////      ,[name]
////  FROM [Mikes_Application_Store].[dbo].[Roles]
////
////  RoleId	name
////2	Clerk
////4	DBA
////1	StandardUser
////3	ViewOnly
////
////SELECT TOP (1000) [id]
////      ,[roleId]
////      ,[model]
////      ,[task]
////      ,[field]
////      ,[Permission]
////  FROM [Mikes_Application_Store].[dbo].
////  [RolePermissions]
////
////
////  id	roleId	model	task	field	right
////1	2	Change_Password	Read_Old_Password	Password	Write
////2	2	Add_Something	doSomething	SomeField	Write
////3	4	Change_Something	*	*	DBA
////4	2	Read_Something	*	*	Read
////
////
////  SELECT TOP (1000) [id]
////      ,[UserId]
////      ,[AttributeName]
////      ,[AttributeValue]
////  FROM [Mikes_Application_Store].[dbo].
////       [UserAttributes]
////
////
////  id	UserId	AttributeName	AttributeValue
////1	1	GivenName	Mike
////2	1	SurName	Merrett
////3	1	eMailAddress	mike.merrett@whitehorse.ca
////4	1	PrimaryRole	DBA
////5	1	SecondaryRole	Clerk
////6	1	SecondaryRole	ViewOnly
////
