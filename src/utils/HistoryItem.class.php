<?php

/** * ********************************************************************************************
 * HistoryItem.class.php
 *
 * Summary holds the text and a timestamp of the history item
 *
 *
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.4.0
 * $Id$
 *
 * Description.
 *   holds a text msg and a timestamp
 *
 *
 *
 * @package History
 * @subpackage HistoryItem
 * @since 0.4.0
 *
 * @example
 *
 * @todo Description
 *    - setup the levels so confidential info is not stored in the history when production
 *
 */
//**********************************************************************************************

namespace php_base\Utils;

use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Utils;


//***********************************************************************************************
//***********************************************************************************************
//***********************************************************************************************
class HistoryItem {
	public $text;
	public $timeStamp;
	public $level;
	/**
	 * @var version number
	 */
	private const VERSION = '0.4.0';

	/** -----------------------------------------------------------------------------------------------
	 *    create a new item and if not passed then generate a timestamp
	 * @param string $text
	 * @param type $timestamp
	 */
	public function __construct( string $text='', $timestamp= null, ?int $level =null) {
		$this->text = $text;
		$this->timeStamp = Utils::setTimeStamp( $timestamp);
		$this->level = $level;
	}

	/** -----------------------------------------------------------------------------------------------
	 * gives a version number
	 * @static
	 * @return type
	 */
	public static function Version() :string {
		return self::VERSION;
	}

	/** -----------------------------------------------------------------------------------------------
	 *  return the history item with its formatting
	 *
	 * @return string
	 */
	public function givePretty() : string {
		$s = '<div class="HistoryLine">';
		$s .= '[' . $this->timeStamp . '] ';
		$s .= $this->text;
		$s .= '<BR>' . PHP_EOL;
		$s .= '</div>';
		return $s;
	}


}