<?php

/** * ********************************************************************************************
 * HeaderView.class.php
 *
 * Summary: this shows stuff at the top of the page
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description: handles all header output
 *
 *
 * @link URL
 *
 * @package ModelViewControllerData - HeaderView
 * @subpackage View
 * @since 0.3.0
 *
 * @example
 *
 * @see HeaderController.class.php
 *
 * @todo Description
 *
 */
//**********************************************************************************************

namespace php_base\View;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;
use \php_base\Utils\HTML\HTML as HTML;

/** * **********************************************************************************************
 * shows all the stuff at the top of the page
 */
class HeaderView extends View {

//	//-----------------------------------------------------------------------------------------------
//	public function __construct($controller) {
//	}
//
	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';

	/** -----------------------------------------------------------------------------------------------
	 * gives a version number
	 * @static
	 * @return type
	 */
	public static function Version() {
		return self::VERSION;
	}

	/** -----------------------------------------------------------------------------------------------
	 * this is the method that gets called to output the header
	 * @param type $parent
	 * @return Response
	 */
	public function doWork($parent = null): Response {
		if (!Settings::GetRuntime('Surpress_Output')) {  // probably because we are printing
			$this->showHTMLHeader();
			?>
			<body>
				<?php
				$this->showPageHeadTable();
				$this->showSubTitle();

				if (Settings::GetPublic('IS_DEBUGGING')) {
					echo '<h1>We is Debugging!!! </h1><br>';
				}
			}
			echo PHP_EOL;
			return Response::NoError();
		}

		/** -----------------------------------------------------------------------------------------------
		 * make it look pretty
		 */
		protected function showHTMLHeader() {
			echo HTML::DocType(/* 'html5' */ 'html4-trans');
			?>
		<html>
			<head>
				<title>
					<?php
					echo $this->giveTitle();
					?>
				</title>
				<meta http-equiv="content-type" content="text/html; charset=utf-8">
				<?php
				$this->showAllStyleSheets();
				$this->showAllJavaScriptFiles();
				?>
			</head>
		</html>
		<?php
	}

	/** -----------------------------------------------------------------------------------------------
	 * load up style sheets files
	 */
	protected function showAllStyleSheets() {
		$arCss = $this->giveStyleSheets();
		foreach ($arCss as $styleUrl) {
			echo '<link rel="stylesheet" href="';
			echo $styleUrl;
			echo '">' . PHP_EOL;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * load up all the java script files
	 */
	protected function showAllJavaScriptFiles() {
		$arJava = $this->giveJavaScriptFiles();
		foreach ($arJava as $javaScriptUrl) {
			require_once( $javaScriptUrl);
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * show the invisible table to allign things at the top of the page
	 */
	protected function showPageHeadTable() {
		?>
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
	}

	/** -----------------------------------------------------------------------------------------------
	 * show the "info bar" with app name and version type stuff
	 */
	protected function showSubTitle() {
		//class="header_h1"
		//echo date('r') ;
		echo '<pre class="pre_version">';
		echo 'Version:';
		echo $this->giveVersion();
		echo ' ';
		echo $this->giveTypeServerAndDatabase();
		echo '</pre>';
	}

	/** -----------------------------------------------------------------------------------------------
	 * give the title (or the app name and version and server
	 *
	 * @param type $short
	 * @return string
	 */
	public function giveTitle($short = false) {
		$out = Settings::GetPublic('App Name');
		if (!$short) {
			$out .= ' - '
					  . Settings::GetPublic('App Version')
					  . ' on '
					  . Settings::GetPublic('App Server');
		}
		return $out;
	}

	/** -----------------------------------------------------------------------------------------------
	 * give a list of style sheets
	 * @return type
	 */
	public function giveStyleSheets() {

		if ($handle = opendir('static\css')) {
			$entries = array();
		    while (false !== ($entry = readdir($handle))) {
				//echo "$entry\n";
				$entries[] = 'static\css\\' . $entry;
			}
			closedir($handle);
			return $entries;
		} else {
			return ['static\css\message_stack_style.cssXXX',
				'static\css\general_style.css',
				'static\css\animations_style.css',
				'static\css\menu_style.css'
			];
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * give all the javascript files
	 * @return type
	 */
	public function giveJavaScriptFiles() {
		return [
		];
	}

	/** -----------------------------------------------------------------------------------------------
	 * give the version
	 * @return type
	 */
	public function giveVersion() {
		return Settings::GetPublic('App Version');
	}

	/** -----------------------------------------------------------------------------------------------
	 * give the database info
	 * @return type
	 */
	public function giveTypeServerAndDatabase() {
		return 'Database: ' . Settings::GetProtected('DB_Type')
				  . ' On: ' . Settings::GetProtected('DB_Server')
				  . ' Using: ' . Settings::GetProtected('DB_Database');
	}

}
