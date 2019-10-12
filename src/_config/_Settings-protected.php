<?php

namespace whitehorse\MikesCommandAndControl2\Settings;

//Settings::SetProtected('test', 'test_val');

Settings::SetProtected('Critical_email_TO_ADDR', 'mike.merrett@whitehorse.ca');

$sub = Settings::GetPublic('App Name') . '@' . Settings::GetPublic( 'App Server'); 
Settings::SetProtected('Critical_email_Subject', $sub);

