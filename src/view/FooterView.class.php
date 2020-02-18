<?php

/** * ********************************************************************************************
 * FooterView.class.php
 *
 * Summary: this shows stuff at the bottom of the page
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description: handles all footer output
 *
 *
 * @link URL
 *
 * @package ModelViewControllerData - FooterView
 * @subpackage View
 * @since 0.3.0
 *
 * @example
 *
 * @see FooterController.class.php
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

use \php_base\Utils\History as History;

/** * **********************************************************************************************
 *  show all the stuff at the bottom of the page
 */
class FooterView extends View {
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
	 *
	 * @param type $parent
	 * @return Response
	 */
	public function doWork($parent = null): Response {
		echo '<footer>' . PHP_EOL;

		echo HTML::BR(5);

		echo '<div class="footer_hr ">' . PHP_EOL;
		echo HTML::HR();
		echo '</div>' . PHP_EOL;
		echo '<div align=right class="footer_username">' . PHP_EOL;
		//echo Settings::GetRunTime('userPermissions')['USERNAME'];
		//dump::dump(Settings::GetRunTime('userPermissions'));
		echo 'Logged in as: ', Settings::GetRunTime('Currently Logged In User');
		echo '</div>' . PHP_EOL;

		//echo '<Br>--footer--<Br>' . PHP_EOL;

		$exec_time = microtime(true) - Settings::GetRunTime('Benchmarks.start.executionTime');
		Settings::GetRunTimeObject('MessageLog')->addINFO('Execution Time was: ' . $exec_time);

		if ( Settings::GetPublic('Show MessageLog in Footer')) {
			Settings::GetRunTimeObject('MessageLog')->showAllMessagesInBox();  // !! a!lways do this last so you get all the outstanding messages!!!!
		}

		if ( Settings::GetPublic('Show History in Footer')) {
			History::show();
		}

		echo '</footer>' . PHP_EOL;
		echo '</body>' . PHP_EOL;

		return Response::NoError();
	}

}
