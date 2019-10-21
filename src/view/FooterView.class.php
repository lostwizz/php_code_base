<?php


namespace php_base\View;



use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;


//***********************************************************************************************
//***********************************************************************************************
class FooterView extends View {

	//-----------------------------------------------------------------------------------------------

	public function doWork( $parent  =null){

		//Settings::GetRunTimeObject('MessageLog')->addERROR('some error message');

		$exec_time = microtime(true) - Settings::GetRunTime('Benchmarks.start.executionTime');
		Settings::GetRunTimeObject('MessageLog')->addINFO('Execution Time was: '. $exec_time);
		Settings::GetRunTimeObject('MessageLog')->showAllMessagesInBox();  // !! a!lways do this last so you get all the outstanding messages!!!!

		echo '<footer>';
		//echo '<Br>--footer--<Br>';

		echo '</footer>';

		echo '</body>';

	}
}

