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

/** * **********************************************************************************************
 *  show all the stuff at the bottom of the page
 */
class FooterView extends View {

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $parent
	 * @return Response
	 */
	public function doWork($parent = null): Response {

		echo '<footer>';
		echo '<Br>--footer--<Br>';
		//Settings::GetRunTimeObject('MessageLog')->addERROR('some error message');

		$exec_time = microtime(true) - Settings::GetRunTime('Benchmarks.start.executionTime');
		Settings::GetRunTimeObject('MessageLog')->addINFO('Execution Time was: ' . $exec_time);
		Settings::GetRunTimeObject('MessageLog')->showAllMessagesInBox();  // !! a!lways do this last so you get all the outstanding messages!!!!


		echo '</footer>';

		echo '</body>';
		//return new Response('ok', 0, true);
		return Response::NoError();
	}

}
