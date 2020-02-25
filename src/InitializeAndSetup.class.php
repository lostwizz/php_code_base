<?php

namespace php_base;

use \php_base\Utils\Utils as Utils;
use \php_base\Resolver as Resolver;
use \php_base\Utils\Cache;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\MessageLog as MessageLog;
use \php_base\Utils\Settings as Settings;
use \php_base\Utils\DebugHandler as DebugHandler;
use \php_base\Utils\History as History;

class InitializeAndSetup {

	public $hadFailure = false;

	public function __construct() {

		$this->setupPHPEnvironment();
		$this->setupAutoLoad();
		$this->startSession();
		$this->loadConfigurations();
		$this->loadAndRunErrorHandler();
		$this->loadAndSetupLogging();
		$this->runTheLoggySetup();
		$this->doSomeVersionTests();


		$this->doSomeSetupChecks_1();
		$this->doDatabaseRequirementTests();

		//////////$this->tryToUseMemcache();
		$this->testEmailLogging(false);

		$this->sendSartingMsgToLogsIfDebugging();

		$this->clearHistory();

		//dump::dumpClasses();
	}

	public function __destruct() {


		Cache::CleanupBeforSessionWrite();

		session_write_close();
		Settings::GetRunTimeObject('MessageLog')->addInfo('... Closing Session..');

		if (Settings::GetPublic('IS_DEBUGGING')) {
			echo '<br>--...The End.--<Br>';
		}
	}

	public function setupPHPEnvironment() {
		date_default_timezone_set('Canada/Yukon');
	}

	public function setupAutoLoad() {
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
		include_once( DIR . 'utils' . DSZ . 'ErrorHandler.php');  // has to be after the settings are initialized
	}

	public function loadAndSetupLogging() {
		Settings::SetPublic('Log_file', DIR . 'logs' . DSZ . Settings::GetPublic('App Name') . '_app.log');

		if (Settings::GetPublic('Use_MessageLog')) {
			$mLog = new MessageLog();
			Settings::SetRunTime('MessageLog', $mLog);
		}

		include_once( DIR . 'utils' . DSZ . 'Setup_Logging.php');
	}

	public function runTheLoggySetup() {
		//TODO: move the loggy code somewhere???
		\php_base\utils\setup_loggy();
	}

	public function doSomeVersionTests() {
		///////////////////////////////////////////////////////////////////////////////
		// verify versions of a few key items
		if (Utils::Version() != '0.3.0') {
			die;
		}
		if (Utils::isVersionGood('0.3.0', Dump::Version())) {
			//echo 'good dump version';
		} else {
			echo 'bad dump version';
		}
	}

	public function doSomeSetupChecks_1() {
		Settings::GetRunTimeObject('MessageLog')->addInfo('Starting read from db settings');
		Settings::dbReadAndApplySettings();

		Settings::SetPublic('TEST that All is well', 'YES');


		// check that setup.php worked properly
		if (Settings::GetPublic('TEST that All is well') != 'YES') {
			throw new exception('it seems that setup (or settings.class.php did not run properly');
		}
		if (Settings::GetRuntimeObject('FileLog') == null) {
			throw new exception('it seems that setup (or settings.class.php did not run properly');
		}
	}

	public function doDatabaseRequirementTests() {

		if (extension_loaded(Settings::GetProtected('database_extension_needed'))) {
			Settings::GetRuntimeObject('DBLog')->addInfo('Starting....');
		} else {
			echo '<font color=white style="background-color:red;font-size:160%;" >', 'PDO_SQLSRV is not available!!- exiting ', '</font>';
			$this->hadFailure = true;
			throw new exception(Settings::GetProtected('database_extension_needed') . ' is not available!!', 256);
		}
	}

	public function tryToUsememcache() {

		//https://libmemcached.org/libMemcached.html
		//https://www.php.net/manual/en/book.memcached.php


		$mc = new Memcached();
		$mc->addServer("localhost", 11211);

		$mc->set("foo", "Hello!");
		$mc->set("bar", "Memcached...");
	}

	public function testEmailLogging(bool $runTheTest = false) {
		if ($runTheTest) {
			Settings::GetRuntimeObject('EmailLog')->addCritical(' it blew up!', \filter_input_array(\INPUT_SERVER, \FILTER_SANITIZE_STRING));
			Settings::GetRuntimeObject('EmailLog')->addCritical('Hey, a critical log entry!', array('bt' => debug_backtrace(true), 'server' => \filter_input_array(\INPUT_SERVER, \FILTER_SANITIZE_STRING)));
		}
	}

	public function sendSartingMsgToLogsIfDebugging() {
		if (Settings::GetPublic('IS_DEBUGGING')) {
			Settings::GetRunTimeObject('MessageLog')->addNotice('Starting ....');
			Settings::GetRuntimeObject('FileLog')->addInfo('Staring...');
			Settings::GetRuntimeObject('SecurityLog')->addInfo('Starting...');
		}
	}

	public function clearHistory() {
		History::clear();
		//History::addMarker();
	}

}
