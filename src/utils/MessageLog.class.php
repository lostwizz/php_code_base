<?php
/** * ********************************************************************************************
 * messagelog.class.php
 *
 * Summary: handles a message log queue for messages at the footer of a page
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description
 * handles a message log queue
 *
 *
 * @package utils
 * @subpackage Message Log
 * @since 0.3.0
 *
 * @see settings
 * @see myNullAbsorber
 *
 * @example
 *  Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher starting prequeue' );
 *
 * @todo Description
 *
 */
//**********************************************************************************************

namespace php_base\Utils;

//if ( ! defined( "IS_PHPUNIT_TESTING")){
//	<link rel="stylesheet" href=".\static\css\message_stack_style.css"><?php
//}


use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;

define('AR_TEXT', 0);
define('AR_TimeStamp', 1);
define('AR_LEVEL', 2);

//***********************************************************************************************
//***********************************************************************************************
/**
 * the message base class for AMessage
 */
abstract class MessageBase {

	const DEBUG = 100;
	const INFO = 200;
	const NOTICE = 250;
	const WARNING = 300;
	const ERROR = 400;
	const CRITICAL = 500;
	const ALERT = 550;
	const EMERGENCY = 600;
	const TODO = 999;

	/**
	 *
	 * @var array $levels - gives a text description of the error type
	 */
	protected static $levels = array(
		self::DEBUG => 'DEBUG',
		self::INFO => 'INFO',
		self::NOTICE => 'NOTICE',
		self::WARNING => 'WARNING',
		self::ERROR => 'ERROR',
		self::CRITICAL => 'CRITICAL',
		self::ALERT => 'ALERT',
		self::EMERGENCY => 'EMERGENCY',
		self::TODO => 'TODO'
	);
	protected $text; // the messageText message
	protected $timeStamp;  // time stamp for the message (for displaying the time)
	protected $level;	// level of the message (see defines at top)

	abstract function Show();

	abstract function Set($value = null);

	abstract function Get();
}

//***********************************************************************************************
//***********************************************************************************************
/**
 * a message class
 *     - the base has the text and level
 */
class AMessage extends MessageBase {

	public $timestamp;

	/** -----------------------------------------------------------------------------------------------
	 * construct a message
	 * @param type $text
	 * @param type $timestamp
	 * @param type $level
	 */
	public function __construct($text = null, $timestamp = null, $level = null) {
		$this->setText($text);
		$this->setTimeStamp($timestamp);
		$this->setLevel($level);
	}

	/** -----------------------------------------------------------------------------------------------
	 * converts the message into a string which is formatted [time] level - text
	 * @return type
	 */
	public function __toString() {
		return $this->timeStamp . ' (Level: ' . parent::$levels[$this->level] . ') ' . $this->text;
	}

	/** -----------------------------------------------------------------------------------------------
	 * dump the contents of this message
	 */
	public function dump() {
		echo 'msg=', $this->text, ' time=', $this->timeStamp, ' level=', parent::$levels[$this->level], '<Br>';
	}

	/** -----------------------------------------------------------------------------------------------
	 * set the contents of the message
	 *     -could be just a string or could be an array( string, timestamp, level)
	 * @param type $textOrArray
	 * @param type $timeStamp
	 * @param type $level
	 */
	public function set($textOrArray = null, $timeStamp = null, $level = null) {
		if (!empty($textOrArray) and is_array($textOrArray)) {
			$this->setFromArray($textOrArray);
		} else {
			$this->setFromArray([$textOrArray, $timeStamp, $level]);
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *  format things
	 * @param type $ar
	 * @return void
	 */
	protected function setFromArray($ar = null): void {
		if (array_key_exists(AR_TEXT, $ar)) {
			$this->setText($ar[AR_TEXT]);
		}
		if (array_key_exists(AR_TimeStamp, $ar)) {
			$this->setTimeStamp($ar[AR_TimeStamp]);
		}
		if (array_key_exists(AR_LEVEL, $ar)) {
			$this->setLevel($ar[AR_LEVEL]);
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * set the text of the message
	 * @param type $textString
	 * @return void
	 */
	protected function setText($textString = null): void {
		if (empty($textString)) {
			$this->text = '';
		} else {
			$this->text = $textString;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * set the timestamp - it does not have to be a time it can be any string
	 *      - if timestamp it must be formatted properly before getting here
	 * @param string $timeStamp
	 */
	protected function setTimeStamp(string $timeStamp = null) {
		if (defined("IS_PHPUNIT_TESTING")) {
			$this->timeStamp = '23:55:30';
			if (empty($timeStamp)) {
				$this->timeStamp = '23:55:30';
			} else {
				$this->timeStamp = $timeStamp;
			}
		} else {
			if (empty($timeStamp)) {
				$this->timeStamp = date('g:i:s');	// current timestamp
			} else {
				$this->timeStamp = $timeStamp;
			}
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * set the level of the message
	 * @param type $level
	 */
	protected function setLevel($level = null) {
		if (empty($level) or $level < 100) {
			$this->level = AMessage::NOTICE;	  //Default
		} else if (array_key_exists($level, parent::$levels)) {
			$this->level = $level;
		} else {
			$this->level = AMessage::NOTICE;	  //Default
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * return the contents of this message in the form of an array
	 *
	 * @return type
	 */
	public function get() {
		$a = array($this->text,
			$this->timeStamp,
			$this->level
		);
		return $a;
	}

	/** -----------------------------------------------------------------------------------------------
	 * return  appropriate style
	 * @param type $level
	 * @return string
	 */
	protected function getShowStyle($level) {
		if (array_key_exists($level, parent::$levels)) {
			return 'msg_style_' . parent::$levels[$level];
		} else {
			return 'msg_style_UNKNOWN';
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * show the appropriate level text
	 * @param type $level
	 * @return string
	 */
	protected function getShowTextLeader($level) {
		if (array_key_exists($level, parent::$levels)) {
			return parent::$levels[$level] . ' ';
		} else {
			return 'UNKNOWN ';
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * return the message all pretty like spans with style
	 * @param type $style
	 * @return string
	 */
	protected function getPrettyLine($style = null) {
		$s = '';
		$textLeader = $this->getShowTextLeader($this->level);

		if (!empty($style)) {
			$lineStyle = $style;
		} else {
			$lineStyle = $this->getShowStyle($this->level);
		}

		$s .= '<span class="' . $lineStyle . '">';
		if (!empty($this->timeStamp)) {
			$s .= '[' . $this->timeStamp . '] ';
		}
		$s .= $textLeader;

		if (is_array($this->text)) {
			$this->text = \print_r($this->text, true);
		}

		$x = str_replace("\n", '<BR>', $this->text);
		$y = str_replace(' ', '&nbsp;', $x);
		$z = str_replace("\t", '&nbsp;&nbsp;&nbsp;', $y);
		$s .= $z;

		$s .= '</span>';
		return $s;
	}

	/** -----------------------------------------------------------------------------------------------
	 * show the contents of this message -- with the appropriate formatting etc
	 *
	 * @param type $style
	 */
	public function show($style = null) {
		echo $this->getPrettyLine($style);
	}

}

//***********************************************************************************************
//***********************************************************************************************
//***********************************************************************************************
//***********************************************************************************************
//***********************************************************************************************
//***********************************************************************************************
//***********************************************************************************************
//***********************************************************************************************
/**
 * the message log handler
 */
class MessageLog {

	/** the queue static so there is only one */
	protected static $messageQueue;

	/** -----------------------------------------------------------------------------------------------
	 * construct a message log - i.e. the queue
	 */
	function __construct() {
		if (empty(self::$messageQueue)) {
			self::$messageQueue = new \SplQueue();
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * handle trying to show message log as a string
	 * @return string
	 */
	public function __toString() {

		$s = '';
		self::$messageQueue->rewind();

		while (self::$messageQueue->valid()) {
			$x = self::$messageQueue->current();
			$s .= $x->__toString();
			$s .= '<br />';
			self::$messageQueue->next(); //switch to next list item
		}
		return $s;
	}

	/** -----------------------------------------------------------------------------------------------
	 * add a new message to the stack ( may include some values passed down to the message class)
	 *
	 * @param type $obj_or_array
	 * @param type $val2
	 * @param type $val3
	 */
	public function addAndShow($obj_or_array = null, $val2 = null, $val3 = null) {
		$this->add($obj_or_array, $val2, $val3);
		$this->showAllMessages();
	}

	/** -----------------------------------------------------------------------------------------------
	 * add a new message to the stack ( may include some values passed down to the message class)
	 *
	 * @param type $obj_or_array
	 * @param type $timestamp
	 * @param type $level
	 */
	public function add($obj_or_array = null, $timestamp = null, $level = null) {

		if (is_object($obj_or_array) and ( $obj_or_array instanceof AMessage )) {
			self::$messageQueue->enqueue($obj_or_array);
		} else {
			if (Settings::GetPublic('Show MessageLog Adds')) {
				$bt = debug_backtrace(false, 2);
				if (is_string($obj_or_array) and ! empty($bt[1])) {
					if (Settings::GetPublic('Show MessageLog Adds_FileAndLine')) {
						$obj_or_array .= '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;      <span class="msg_style_fn">';
						$obj_or_array .= '- ' . basename($bt[1]['file']) . ':' . $bt[1]['line'] . '</span>';
					}
				}
			}

			$temp = new AMessage($obj_or_array, $timestamp, $level);
			self::$messageQueue->enqueue($temp);

			if (Settings::GetPublic('Show MessageLog Adds')) {
				$temp->show();
				echo '<Br>' . PHP_EOL;
			}
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * add a to do message to the log
	 * @param type $obj_or_array
	 * @param type $timestamp
	 */
	public function addToDo($obj_or_array = null, $timestamp = null) {
		if (is_array($obj_or_array) and ! empty($obj_or_array[2])) {
			$obj_or_array[2] = AMessage::TODO;
		}
		$this->add($obj_or_array, $timestamp, AMessage::TODO);
	}

	/** -----------------------------------------------------------------------------------------------
	 * add a debug message
	 * @param type $obj_or_array
	 * @param type $timestamp
	 */
	public function addDebug($obj_or_array = null, $timestamp = null) {
		if (is_array($obj_or_array) and ! empty($obj_or_array[2])) {
			$obj_or_array[2] = AMessage::DEBUG;
		}
		$this->add($obj_or_array, $timestamp, AMessage::DEBUG);
	}

	/** -----------------------------------------------------------------------------------------------
	 * add a info message
	 * @param type $obj_or_array
	 * @param type $timestamp
	 */
	public function addInfo($obj_or_array = null, $timestamp = null) {
		if (is_array($obj_or_array) and ! empty($obj_or_array[2])) {
			$obj_or_array[2] = AMessage::INFO;
		}
		$this->add($obj_or_array, $timestamp, AMessage::INFO);
	}

	/** -----------------------------------------------------------------------------------------------
	 * add a notice message
	 * @param type $obj_or_array
	 * @param type $timestamp
	 */
	public function addNotice($obj_or_array = null, $timestamp = null) {
		if (is_array($obj_or_array) and ! empty($obj_or_array[2])) {
			$obj_or_array[2] = AMessage::NOTICE;
		}
		$this->add($obj_or_array, $timestamp, AMessage::NOTICE);
	}

	/** -----------------------------------------------------------------------------------------------
	 * add a warning message
	 * @param type $obj_or_array
	 * @param type $timestamp
	 */
	public function addWarning($obj_or_array = null, $timestamp = null) {
		if (is_array($obj_or_array) and ! empty($obj_or_array[2])) {
			$obj_or_array[2] = AMessage::WARNING;
		}
		$this->add($obj_or_array, $timestamp, AMessage::WARNING);
	}

	/** -----------------------------------------------------------------------------------------------
	 * add an error message
	 * @param type $obj_or_array
	 * @param type $timestamp
	 */
	public function addError($obj_or_array = null, $timestamp = null) {
		if (is_array($obj_or_array) and ! empty($obj_or_array[2])) {
			$obj_or_array[2] = AMessage::ERROR;
		}
		$this->add($obj_or_array, $timestamp, AMessage::ERROR);
	}

	/** -----------------------------------------------------------------------------------------------
	 * add a critical message
	 * @param type $obj_or_array
	 * @param type $timestamp
	 */
	public function addCritical($obj_or_array = null, $timestamp = null) {
		if (is_array($obj_or_array) and ! empty($obj_or_array[2])) {
			$obj_or_array[2] = AMessage::CRITICAL;
		}
		$this->add($obj_or_array, $timestamp, AMessage::CRITICAL);
	}

	/** -----------------------------------------------------------------------------------------------
	 * add an alert message
	 * @param type $obj_or_array
	 * @param type $timestamp
	 */
	public function addAlert($obj_or_array = null, $timestamp = null) {
		if (is_array($obj_or_array) and ! empty($obj_or_array[2])) {
			$obj_or_array[2] = AMessage::ALERT;
		}
		$this->add($obj_or_array, $timestamp, AMessage::ALERT);
	}

	/** -----------------------------------------------------------------------------------------------
	 * add an emergency message
	 * @param type $obj_or_array
	 * @param type $timestamp
	 */
	public function addEmergency($obj_or_array = null, $timestamp = null) {
		if (is_array($obj_or_array) and ! empty($obj_or_array[2])) {
			$obj_or_array[2] = AMessage::EMERGENCY;
		}
		$this->add($obj_or_array, $timestamp, AMessage::EMERGENCY);
	}

	/** -----------------------------------------------------------------------------------------------
	 * are there any messages in the queue
	 * @return type
	 */
	public function hasMessages() {
		return (self::$messageQueue->count() > 0);
	}

	/** -----------------------------------------------------------------------------------------------
	 * how many messages are still in the queue
	 * @return type
	 */
	public function stackSize() {
		return self::$messageQueue->count();
	}

	/** -----------------------------------------------------------------------------------------------
	 *  pop a message off the stack and return it
	 * @return boolean
	 */
	public function getNextMessage() {
		if (self::$messageQueue->count() > 0) {
			//$temp = array_shift( $this->message_stack);
			$temp = self::$messageQueue->dequeue();
			return $temp;
		} else {
			return false;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *  show the next item on the stack (causes a get_next_message which will remove it from the stack)
	 * @return boolean
	 */
	public function showNextMessage() {
		$temp = $this->getNextMessage();
		if (!empty($temp)) {
			$temp->show();
			return true;
		} else {
			return false;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * show all the messages on the stack (effectivey emptying the stack
	 * @param type $messageText_after_each_line
	 */
	public function showAllMessages($messageText_after_each_line = '<br>') {
		while ($temp = $this->showNextMessage()) {
			if (!empty($messageText_after_each_line)) {
				echo $messageText_after_each_line;
			}
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * show all the messages on the stack (effctivey emptying the stack)
	 *  and do it in a pretty box :-)
	 * @param type $includeFieldSet
	 */
	public function showAllMessagesInBox($includeFieldSet = true) {
		if ($includeFieldSet) {
			?><fieldset class="msg_fieldset"><Legend id="message_box_show_all_in_box" class="msg_legend">Messages</legend><?php
		}
		if ($this->hasMessages()) {
			$this->showAllMessages();
		} else {
			echo '&nbsp;';
		}
		if ($includeFieldSet) {
			?></fieldset><?php
		}
	}

}

/// some usage examples  - now you should use GetRunTimeObject !!!!!!!!!!! so it returns something callable (even if it does nothing)

////include_once(DIR . 'utils' . DS . 'messagelog.class.php');
////$mLog = new MessageLog();
////Settings::SetRunTime('MessageLog', $mLog);

////Settings::GetRunTime('MessageLog')->add( /*'now');
////Settings::GetRunTime('MessageLog')->add( 'now again');
////
////Settings::GetRunTime('MessageLog')->add( 'one more time');
////
////Settings::GetRunTime('MessageLog')->add( array( ' and and some more text DEBUG',null,  MessageBase::DEBUG));
////
////Settings::GetRunTime('MessageLog')->add( array( 'text',null,  MessageBase::ERROR));
////Settings::GetRunTime('MessageLog')->add( array( 'some more text INFO ',null,  MessageBase::INFO));
////Settings::GetRunTime('MessageLog')->add( array( ' and some more text NOTICE',null,  MessageBase::NOTICE));
////Settings::GetRunTime('MessageLog')->add( array( ' and and some more text WARNING',null,  MessageBase::WARNING));
////Settings::GetRunTime('MessageLog')->add( array( ' and and some more text ERROR',null,  MessageBase::ERROR));
////Settings::GetRunTime('MessageLog')->add( array( ' and and some more text CRITICAL',null,  MessageBase::CRITICAL));
////Settings::GetRunTime('MessageLog')->add( array( ' and and some more text ALERT',null,  MessageBase::ALERT));
////
////Settings::GetRunTime('MessageLog')->add( array( ' and and and some more text',null,  MessageBase::EMERGENCY));
////
////Settings::GetRunTime('MessageLog')->addInfo(array('some more text INFO 2', null, MessageBase::EMERGENCY));
////
////Settings::GetRunTime('MessageLog')->addNotice( 'another NOTICE');
////Settings::GetRunTime('MessageLog')->addWarning( 'another WARNING');
////Settings::GetRunTime('MessageLog')->addError(' another ERRROR');
////Settings::GetRunTime('MessageLog')->addCritical( 'another Critical');
////Settings::GetRunTime('MessageLog')->addAlert('another alert');
////Settings::GetRunTime('MessageLog')->addEmergency( 'another emergency');*/
