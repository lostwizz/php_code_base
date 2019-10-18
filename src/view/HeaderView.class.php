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
	public function doWork( $model = null){

		if ( ! Settings::GetRuntime('Surpress_Output')){  // probably because we are printing
			echo HTML::DocType(/* 'html5'*/ 'html4-trans' ) ;
			?>
				<html>
					<head>
						<title><?php echo $model->giveTitle();?></title>
						<meta http-equiv="content-type" content="text/html; charset=utf-8">
						<?php
							$ar = $model->giveStyleSheets();
							foreach( $ar as $styleUrl){
								echo '<link rel="stylesheet" href="' ;
								echo $styleUrl;
								echo '">' . PHP_EOL;
							}
							$ar = $model->giveJavaScriptFiles();
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
										echo $model->giveTitle(true);
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
			echo $model->giveVersion();
			echo ' ';
			echo $model->giveTypeServerAndDatabase();
			echo '</pre>';

			if ( Settings::GetPublic('IS_DEBUGGING')) {
				echo '<h1>We is Debugging!!! </h1><br>';
			}
		}
	}

}