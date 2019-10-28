<?php

namespace php_base\Utils;

use \php_base\Utils\Dump\Dump as Dump;

abstract Class myUtils {

	//-----------------------------------------------------------------------------------------------
	// format a number in a currency format (i.e with dollar sign)
	public static function ShowMoney( $val, $leftPadding =10, $arOptions=null, $arStyle=null){
		//$mon = sprintf( '%10.2f', $val);
		$mon = '$' . number_format( round($val,2 ), 2, '.',',');
		$mon = str_pad( $mon, $leftPadding,' ', STR_PAD_LEFT);
		$mon = str_replace( ' ','&nbsp', $mon);
		return $mon;
	}


	//-----------------------------------------------------------------------------------------------
	public static function GiveTempFileName( $dir, $prefix='temp_'){
//		do{
//			$seed = substr(md5(microtime() ) , 0, 8);
//			$filename = $dir . $trailing_slash . $prefix . $seed . $postfix;
//		} while (file_exists($filename));
//		$fp = fopen($filename, "w");
//		fclose($fp);
		$filename = tempnam($dir, $prefix);
		return $filename;
	}

	//-----------------------------------------------------------------------------------------------
	public static function PrettyName( $aTime){

	}

	//-----------------------------------------------------------------------------------------------
	public static function GiveCurrentDate($withTime=false) {
		if ($withTime){
			$r = date( 'g:ia d-M-Y');
		} else {
			$r = date( 'd-M-Y');
		}
		return $r;
	}

	//-----------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------
	//////////////////////////////////////////////////////////////////////////////////////////
	// setup pdo connection to the database
	//////////////////////////////////////////////////////////////////////////////////////////
	public static function setup_PDO(){
		if ( ! extension_loaded(Settings::GetProtected('database_extension_needed'))) {
			throw new Exception ('NOT loaded');
		}
		if ( empty(Settings::GetProtected( 'DB_Username'))) {
			throw new Exception('Missing Config Data from Settings- DB_Username');
		}
		if ( empty(Settings::GetProtected( 'DB_Password'))) {
			throw new Exception('Missing Config Data from Settings- DB_Password');
		}

		$dsn = Settings::GetProtected( 'DB_DSN');
		$options= Settings::GetProtected( 'DB_DSN_OPTIONS');
		try {
			$conn = new \PDO($dsn,
							Settings::GetProtected('DB_Username'),
							Settings::GetProtected('DB_Password'),
							$options
							);
			$conn->setAttribute( \PDO::ATTR_CASE, \PDO::CASE_UPPER);
			$conn->setAttribute( \PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
		} catch (\PDOException $e)				{
			throw new \PDOException($e->getMessage(), (int)$e->getCode());
		}
		return $conn;
	}

}