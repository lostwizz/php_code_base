<?php

// HeaderData.class.php
//		- give data for the header


namespace php_base\Data;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;

class HeaderData extends Data {


//	public function doWork(){
//		return false;
//	}



	public function giveTitle( $short = false){
		$out =  Settings::GetPublic( 'App Name');
		if( ! $short){
			$out .= ' - '
					. Settings::GetPublic( 'App Version')
					. ' on '
					. Settings::GetPublic( 'App Server');
		}
		return $out;
	}

	public function giveVersion() {
		return Settings::GetPublic( 'App Version');
	}

	public function giveServerAndDatabase(){
		return 'Database: ' . Settings::GetProtected('DB_Type')
				. ' On: ' . Settings::GetProtected('DB_Server')
				. ' Using: ' . Settings::GetProtected('DB_Database');
	}

	public function giveStyleSheets(){
		//<link rel="stylesheet" href=".\static\css\message_stack_style.css"><?php
		return [ '..\static\css\message_stack_style.css',
				 '..\static\css\general_style.css'
				];
	}

	public function giveJavaScriptFiles(){
		return [

			];
	}

}