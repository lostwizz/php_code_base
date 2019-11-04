<?php
//**********************************************************************************************
//* myUtils.class.php
//*
//* $Id$
//* $Rev: 0000 $
//* $Date: 2019-09-12 09:46:20 -0700 (Thu, 12 Sep 2019) $
//*
//* DESCRIPTION:
//*
//* USAGE:
//*
//* HISTORY:
//* 12-Sep-19 M.Merrett - Created
//*
//* TODO:
//*
//***********************************************************************************************************
//***********************************************************************************************************

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


}