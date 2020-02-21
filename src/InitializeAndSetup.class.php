<?php



namespace \php_base ;


use \php_base\Resolver as Resolver;
use \php_base\Utils\Cache;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\MessageLog as MessageLog;
use \php_base\Utils\Settings as Settings;
use \php_base\Utils\DebugHandler as DebugHandler;
use \php_base\Utils\History as History;





class InitializeAndSetup  {


	public function __construct(){

		$this->setupPHPEnvironment();
		$this->setupAutoLoad();
		$this->startSession();
		$this->loadConfigurations();
		$this->loadAndRunErrorHandler();
		$this->loadAndSetupLogging();


		dump::dumpClasses();
	}

	public function setupPHPEnvironment(){
		date_default_timezone_set('Canada/Yukon');

	}


	public function setupAutoLoad(){
		include_once( DIR . 'autoload.php');
	}

	public function startSession() {
		if (session_status() == \PHP_SESSION_NONE AND ! headers_sent()) {
			session_name('SESSID_' . str_replace(' ', '_', Settings::GetPublic('App Name')));
			session_start();
			//Settings::GetRunTimeObject('MessageLog')->addNotice('Starting ..session..'); // cant really do a messageLog because it isnt loaded yet
		}

		//think about how to use   session_regenerate_id(true);
		//    and/or session_destroy()
		// https://www.php.net/manual/en/function.session-destroy.php
	}

	public function loadConfigurations() {
		include_once( DIR . 'utils' . DSZ . 'settings.class.php');

		require_once( DIR . '_config' . DSZ . '_Settings-General.php');
		require_once( DIR . '_config' . DSZ . '_Settings-Database.php');
		require_once( DIR . '_config' . DSZ . '_Settings-protected.php');

		require_once( 'P:\Projects\_Private_Settings.php');

	}

	public function loadAndRunErrorHandler() {
		include_once( DIR . 'utils' . DSZ . 'ErrorHandler.php');	 // has to be after the settings are initialized
	}

	public function loadAndSetupLogging() {
		
	}

}



