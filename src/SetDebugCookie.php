<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


namespace php_base;


class SetDebugCookie {

	public static function setCookie() {
		echo 'hererrrrrrrr';
		setcookie( 'DEBUG', '78');
		ECHO 'Cookie written';
	}


	public static function checkLocalEnvIfDebugging() : bool {

		$get = filter_input_array(\INPUT_GET, \FILTER_SANITIZE_STRING);
		if ( !empty( $get) and in_array('DEBUG', $get) and $get['DEBUG']== 78 ){
			//echo '-debug true-'	;
			return true;
		}
		$cookie = filter_input_array(\INPUT_COOKIE,\FILTER_SANITIZE_STRING);
echo '<pre>';
echo ' - cookie - ';
print_r( $cookie);
echo '</pre>';

		if ( !empty( $cookie['DEBUG']) and ($cookie['DEBUG'] == '78') ){
			echo '-debug true by cookie-'	;
			return true;
		}

		//echo '-debug false (by cookie or get)-'	;
		return false;
	}


}

