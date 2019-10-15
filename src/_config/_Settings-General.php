<?php

namespace php_base\Utils;


Settings::SetPublic('IS_DEBUGGING', true);

//Settings::SetPublic( 'App Name', 'MikesCommandAndControl2');
Settings::SetPublic( 'App Name', 'TestApp');

Settings::SetPublic( 'App Version', '2.0.0');
Settings::SetPublic( 'App Server', $_SERVER['SERVER_NAME']);


Settings::SetPublic('Log_file',DIR . 'logs' . DS . Settings::GetPublic('App Name') . '_app.log' );
Settings::SetPublic('Security_Log_file',DIR . 'logs' . DS . Settings::GetPublic('App Name') . '_security.log' );

// these will be set in the "/Setup.php/SetupLogging.php"
Settings::SetRuntime( 'DBLog', null);
Settings::SetRuntime( 'DBdataLog', null);
Settings::SetRuntime( 'FileLog', null);
Settings::SetRuntime( 'SecurityLog', null);
Settings::SetRuntime( 'EmailLog', null);


Settings::SetPublic( 'CRITICAL_EMAIL_PAYLOAD', 'CRITICAL_EMAIL_PAYLOAD');

