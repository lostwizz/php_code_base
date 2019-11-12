<?php

/** * ********************************************************************************************
 * _Settings-protected.php
 *
 * Summary: General not for total public consumption
 *			- effectively this distinction is not truly enforced
 *       - note that these settings  are not comprehensive - the app can create  and remove settings as it pleases
 *       - note that these settings  are not comprehensive - the app can create  and remove settings as it pleases
 *
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
 * @see _Settings-Database.php
 * @see _Settings-General.php
 * @see utils\settings.class.php
 *

 *
 * @todo
 *
 */
//**********************************************************************************************

namespace php_base\Utils;

//Settings::SetProtected('test', 'test_val');

/**
 * the email address and subject that gets the emails
 */
Settings::SetProtected('Critical_email_TO_ADDR', 'mike.merrett@whitehorse.ca');

$sub = Settings::GetPublic('App Name') . '@' . Settings::GetPublic( 'App Server');
Settings::SetProtected('Critical_email_Subject', $sub);

Settings::SetProtected('Password_for_merrem' , '$2y$10$hFTDIH6d4RVohnWCpY5sKOVrRCesQzYprbixGYBN.lcL96vfODdRa');
Settings::SetProtected('Password_for_john'   , '$2y$10$DmWo6P2aZkZahtJwWMk5k.nUL8qsJoLwuy0ICZwgJqO5CIpMZ6XJC');