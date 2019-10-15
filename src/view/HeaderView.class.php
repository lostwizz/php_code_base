<?php

namespace php_base\View;


use \php_base\Utils\Settings as Settings;

//***********************************************************************************************
//***********************************************************************************************
class HeaderView extends View {

//	//-----------------------------------------------------------------------------------------------
//	public function __construct($controller) {
//	}

	//-----------------------------------------------------------------------------------------------
	public function doWork(){

		echo 'header here';

		if ( Settings::GetPublic('IS_DEBUGGING')) {
			echo '<h1>We is Debugging!!! </h1><br>';
		}
		echo '<span>App: <b>' . Settings::GetPublic('App Name') . '</b>    On:<b>'. Settings::GetPublic( 'App Server') . '</b> Ver:<b>' . Settings::GetPublic('App Version') . '</b></span>';
		echo '<br>';
	}

}