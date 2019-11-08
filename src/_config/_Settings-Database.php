<?php

/** * ********************************************************************************************
 * _Settings_Database.php
 *
 * Summary: settings that are directly linked to the databases
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description.
 *   this holds and sets the settings related to database functions
 *         - note that there is a _private_settings file that holds the username and passwords - and DSN
 *
 *
 *
 * @link URL
 *
 * @package  AuthenticateController
 * @subpackage Controller
 * @since 0.3.0
 *
 * @example
 *
 * @see p:\projects\_Private_Settings.php
 * @see _Settings-General.php
 * @see _Settings-protected.php
 * @see utils\settings.class.php
 *

 *
 * @todo
 *
 */
//**********************************************************************************************


namespace php_base\Utils;

Settings::SetProtected('test', 'test_val');
//\whitehorse\MikesCommandAndControl2\Settings\Settings\Settings::SetProtected('test', 'test_val');



/**
 * these are the tablenames for the various tables used
 */

Settings::SetProtected( 'DB_Table_UserManager', 'Users' );
Settings::SetProtected( 'DB_Table_RoleManager', 'Roles' );
Settings::SetProtected( 'DB_Table_PermissionsManager', 'RolePermissions' );
Settings::SetProtected( 'DB_Table_UserAttributes', 'UserAttributes' );
Settings::SetProtected( 'DB_Table_Settings', 'Settings');

