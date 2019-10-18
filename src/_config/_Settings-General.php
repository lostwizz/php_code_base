<?php


namespace php_base\Utils;

Settings::SetPublic('IS_DEBUGGING', true);
//Settings::SetPublic('IS_DEBUGGING', false);
//Settings::SetPublic('THE_DEBUGGING_LEVEL', 0); // debugging level is off
Settings::SetPublic('THE_DEBUGGING_LEVEL', 100);  // 100 = MessageLog::DEBUG;
//Settings::SetPublic('THE_DEBUGGING_LEVEL', 200);  // 200 = MessageLog::INFO;
//Settings::SetPublic('THE_DEBUGGING_LEVEL', 250);  //                   Notice = 250;
//Settings::SetPublic('THE_DEBUGGING_LEVEL', 300);  //                   WARNING = 300;
//Settings::SetPublic('THE_DEBUGGING_LEVEL', 400);  //                   ERROR = 400;
//Settings::SetPublic('THE_DEBUGGING_LEVEL', 500);  //                   CRITICAL = 500;
//Settings::SetPublic('THE_DEBUGGING_LEVEL', 550);  //                   ALERT = 550;
//Settings::SetPublic('THE_DEBUGGING_LEVEL', 600);  //                   EMERGENCY = 600;



//Settings::SetPublic( 'App Name', 'MikesCommandAndControl2');
Settings::SetPublic( 'App Name', 'TestApp');

Settings::SetPublic( 'App Version', '2.0.0');
Settings::SetPublic( 'App Server', $_SERVER['SERVER_NAME']);


Settings::SetPublic('Log_file',         DIR . 'logs' . DS . Settings::GetPublic('App Name') . '_app.log' );
Settings::SetPublic('Security_Log_file',DIR . 'logs' . DS . Settings::GetPublic('App Name') . '_security.log' );

// these will be set in the "/Setup.php/SetupLogging.php"
Settings::SetRuntime( 'DBLog', null);
Settings::SetRuntime( 'DBdataLog', null);
Settings::SetRuntime( 'FileLog', null);
Settings::SetRuntime( 'SecurityLog', null);
Settings::SetRuntime( 'EmailLog', null);

Settings::SetPublic( 'Use_MessageLog', true );  //true
Settings::SetPublic( 'Use_DBLog', false);
Settings::SetPublic( 'Use_DBdataLog', false);
Settings::SetPublic( 'Use_FileLog', false);  // true
Settings::SetPublic( 'Use_SecurityLog', false);
Settings::SetPublic( 'Use_EmailLog', false);      // true


//////////Settings::SetPublic( 'CRITICAL_EMAIL_PAYLOAD', 'CRITICAL_EMAIL_PAYLOAD');
Settings::SetPublic( 'CRITICAL_EMAIL_PAYLOAD_CONTEXT', false);  // could be string or array
Settings::SetPublic( 'CRITICAL_EMAIL_PAYLOAD_EXTRA', false);  // could be string or array