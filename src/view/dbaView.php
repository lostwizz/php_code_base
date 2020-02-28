<?php

namespace php_base\View;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;
use \php_base\Utils\HTML\HTML as HTML;
use \php_base\Resolver;

/**
 * Description of MenuView
 *
 * @author merrem
 */
class dbaView extends View{



	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';


	function __construct( $controller ){
		Settings::getRunTimeObject('MENU_DEBUGGING')->addInfo('constructor for dbaView');

	}

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
	 * @param type $ar
	 */
	public function showEditableIsDetailed( array $ar ) :void {
		echo HTML::FormOpen('EditableIsDetailed');
		echo HTML::Hidden(Resolver::REQUEST_PROCESS, 'dbaController');
		echo HTML::Hidden(Resolver::REQUEST_TASK, 'setAllDebugLevels');

		$ar[] = 'IS_DEBUGGING';
DUMP::dump($ar);

		echo '<table border=1>';
		foreach ($ar as $varName) {
			echo HTML::TR();
			echo HTML::TD();
			echo $varName;
			ECHO HTML::TDendTD();

//			$shortName = substr($varName, strlen('IS_DETAILED_'));
			echo HTML::Select(Resolver::REQUEST_PAYLOAD . '[' . $varName . ']',
					\php_base\Utils\AMessage::$levels,
					Settings::getPublic($varName)
					);

			//echo '<input type="range" id=points name="' . $varName .'" min=0 max=1000 value="' . Settings::getPublic($varName) .'">' ;
			//echo '<BR>' , PHP_EOL;
			echo HTML::TDend();
			echo HTML::TRend();
		}
		echo '</table>';

		echo HTML::Submit(Resolver::REQUEST_ACTION, 'Save Edit');
		echo HTML::FormClose();
	}

}