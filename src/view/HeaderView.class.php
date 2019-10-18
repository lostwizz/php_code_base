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
	public function doWork( $data = null){





		if ( ! Settings::GetRuntime('Surpress_Output')){  // probably because we are printing
			echo HTML::DocType(/* 'html5'*/ 'html4-trans' ) ;
			?>
				<html>
					<head>
						<title><?php echo $data->giveTitle();?></title>
						<meta http-equiv="content-type" content="text/html; charset=utf-8">
						<?php
							$ar = $data->giveStyleSheets();
							foreach( $ar as $styleUrl){
								echo '<link rel="stylesheet" href="' ;
								echo $styleUrl;
								echo '">' . PHP_EOL;
							}
							$ar = $data->giveJavaScriptFiles();
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
								<h1 class="header_h1">
									<?php
										echo $data->giveTitle(true);
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

			//echo date('r') ;
			echo '<pre class="pre_version">';
			echo 'Version:';
			echo $data->giveVersion();
			echo ' ';
			echo $data->giveServerAndDatabase();
			echo '</pre>';

			if ( Settings::GetPublic('IS_DEBUGGING')) {
				echo '<h1>We is Debugging!!! </h1><br>';
			}
			echo '<span>App: <b>' . Settings::GetPublic('App Name') . '</b>    On:<b>'. Settings::GetPublic( 'App Server') . '</b> Ver:<b>' . Settings::GetPublic('App Version') . '</b></span>';
			echo '<br>';
		}
	}

}