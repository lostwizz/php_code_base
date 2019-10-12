<?php

namespace whitehorse\MikesCommandAndControl2\Settings;

date_default_timezone_set('Canada/Yukon');

include_once( DIR . 'utils' . DS . 'dump.class.php');

include_once( DIR . 'utils' . DS . 'setup' . DS . 'settings.class.php');

Settings::SetPublic( 'TEST that All is well', 'YES');

require_once( DIR . '_config' . DS . '_Settings-General.php');
require_once( DIR . '_config' . DS . '_Settings-Database.php');
require_once( DIR . '_config' . DS . '_Settings-protected.php');

include_once( DIR . 'utils' . DS . 'setup' . DS . 'Setup_Logging.php');

include_once( DIR . 'utils' . DS . 'ErrorHandler.php');

//Settings::GetPublic('FileLog')->addInfo("hellow world");