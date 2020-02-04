<?php

/** * ********************************************************************************************
 * History.class.php
 *
 * Summary
 *    saves a history of things in the $_SESSION
 *               and will output a prettily formatted dump
 *
 *
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.4.0
 * $Id$
 *
 * Description.
 *   holds historyItems in the $_SESSION
 *
 *
 *
 * @package History
 * @subpackage History
 * @since 0.4.0
 *
 * @example
 *
 * @todo Description
 *
 */
//**********************************************************************************************

namespace php_base\Utils;

use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Utils;


//***********************************************************************************************
//***********************************************************************************************
//***********************************************************************************************
abstract Class History {
	//put your code here

	/**
	 * @var version number
	 */
	private const VERSION = '0.4.0';

	/** -----------------------------------------------------------------------------------------------
	 * gives a version number
	 * @static
	 * @return type
	 */
	public static function Version() :string {
		return self::VERSION;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $s
	 * @param string|null $ts
	 * @return void
	 */
	public static function add(string $s = '', ?string $ts =null ): void {
		$hItem = new HistoryItem( $s, $ts);
		$_SESSION['History'][] = $hItem;
	}

	public static function addMarker() :void{
		self:: add( '---------------------------------------------------------');
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return void
	 */
	public static function clear() : void{
		unset($_SESSION['History']);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return void
	 */
	public static function show(): void {
		//dump::dump($_SESSION['History']);
		echo '<fieldset class="HistoryFieldset"><Legend id="history_show_all_in_box" class="HistoryLegend">History</legend>';

		foreach ($_SESSION['History'] as $hItem) {
			try {
				echo $hItem->givePretty();
			} catch (Exception $ex) {
				dump($ex->getMessage());
			}
		}
		echo '</fieldset>';
	}

}
