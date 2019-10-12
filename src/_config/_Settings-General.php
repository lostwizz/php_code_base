<?php

namespace php_base\Utils\Settings;


Settings::SetPublic('IS_DEBUGGING', true);

//Settings::SetPublic( 'App Name', 'MikesCommandAndControl2');
Settings::SetPublic( 'App Name', 'TestApp');

Settings::SetPublic( 'App Version', '2.0.0');
Settings::SetPublic( 'App Server', $_SERVER['SERVER_NAME']);


Settings::SetPublic('Log_file',DIR . 'logs' . DS . Settings::GetPublic('App Name') . '_app.log' );
Settings::SetPublic('Security_Log_file',DIR . 'logs' . DS . Settings::GetPublic('App Name') . '_security.log' );

// these will be set in the "/Setup.php/SetupLogging.php"
Settings::SetPublic( 'DBLog', null);
Settings::SetPublic( 'DBdataLog', null);
Settings::SetPublic( 'FileLog', null);
Settings::SetPublic( 'SecurityLog', null);
Settings::SetPublic( 'EmailLog', null);


Settings::SetPublic( 'CRITICAL_EMAIL_PAYLOAD', 'CRITICAL_EMAIL_PAYLOAD');

