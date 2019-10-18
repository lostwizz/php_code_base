<?php

namespace php_base\View;


use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;

use \php_base\Utils\HTML\HTML as HTML;

//***********************************************************************************************
//***********************************************************************************************
class HeaderView extends View {

//	//-----------------------------------------------------------------------------------------------
//	public function __construct($controller) {
//	}

	//-----------------------------------------------------------------------------------------------
	public function doWork( $parent =null){

		if ( ! Settings::GetRuntime('Surpress_Output')){  // probably because we are printing
			echo HTML::DocType(/* 'html5'*/ 'html4-trans' ) ;
			?>
				<html>
					<head>
						<title><?php echo $this->giveTitle();?></title>
						<meta http-equiv="content-type" content="text/html; charset=utf-8">
						<?php
							$ar = $this->giveStyleSheets();
							foreach( $ar as $styleUrl){
								echo '<link rel="stylesheet" href="' ;
								echo $styleUrl;
								echo '">' . PHP_EOL;
							}
							$ar = $this->giveJavaScriptFiles();
							foreach( $ar as $javaScriptUrl){
								require_once( $javaScriptUrl);
							}
						?>
					</head>
				</html>
				<body>
					<table class="header_table" >
						<tr>
							<td class="header_td_app_name">
								<h1 >
									<?php
										echo $this->giveTitle(true);
									?>
								</h1>
							</td>
							<td class="header_td_middle_box">
							</td>
							<td class="header_td_version">

							</td>

						</tr>
					</table>
			<?php
				//class="header_h1"
				//echo date('r') ;
			echo '<pre class="pre_version">';
			echo 'Version:';
			echo $this->giveVersion();
			echo ' ';
			echo $this->giveTypeServerAndDatabase();
			echo '</pre>';

			if ( Settings::GetPublic('IS_DEBUGGING')) {
				echo '<h1>We is Debugging!!! </h1><br>';
			}
		}
	}

	//-----------------------------------------------------------------------------------------------
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

	//-----------------------------------------------------------------------------------------------
	public function giveStyleSheets(){
		//<link rel="stylesheet" href=".\static\css\message_stack_style.css"><?php
		return [ '..\static\css\message_stack_style.css',
				 '..\static\css\general_style.css'
				];
	}

	//-----------------------------------------------------------------------------------------------
	public function giveJavaScriptFiles(){
		return [

			];
	}

	//-----------------------------------------------------------------------------------------------
	public function giveVersion() {
		return Settings::GetPublic( 'App Version');
	}

	//-----------------------------------------------------------------------------------------------
	public function giveTypeServerAndDatabase(){
		return 'Database: ' . Settings::GetProtected('DB_Type')
				. ' On: ' . Settings::GetProtected('DB_Server')
				. ' Using: ' . Settings::GetProtected('DB_Database');
	}



}