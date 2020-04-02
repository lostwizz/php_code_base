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
use \php_base\Utils\HTML\HTML as HTML;
use \php_base\Utils\Dump\Dump as Dump;

define('AR_TEXT', 0);
define('AR_TimeStamp', 1);
define('AR_LEVEL', 2);
define('AR_CODEDETAILS', 3);

//
//if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])){
////if ( empty(get_included_files()) ) {
//	echo 'called directly';
//} else {
//	echo 'not called directly';
//	echo '<pre>';
//	//print_r( get_included_files());
//	print_r( __FILE__);
//	print_r($_SERVER) ;
//	echo '</pre>';
//}



//***********************************************************************************************
//***********************************************************************************************
/**
 * the message base class for AMessage
 *    - mainly to allow getting the name for the value thru the array $levels
 */
abstract class MessageBase {
	const ROCK_BOTTOM_ALL = -1;
	const ALL = 1;

	const DEBUG_1 = 101;
	const DEBUG_2 = 102;
	const DEBUG_3 = 103;
	const DEBUG_4 = 104;
	const DEBUG_5 = 105;
	const DEBUG_6 = 106;
	const DEBUG_7 = 107;
	const DEBUG_8 = 108;
	const DEBUG_9 = 109;
	const DEBUG = 110;

	const INFO_1 = 201;
	const INFO_2 = 202;
	const INFO_3 = 203;
	const INFO_4 = 204;
	const INFO_5 = 205;
	const INFO_6 = 206;
	const INFO_7 = 207;
	const INFO_8 = 208;
	const INFO_9 = 209;
	const INFO = 210;

	const NOTICE_1 = 251;
	const NOTICE_2 = 252;
	const NOTICE_3 = 253;
	const NOTICE_4 = 254;
	const NOTICE_5 = 255;
	const NOTICE_6 = 256;
	const NOTICE_7 = 257;
	const NOTICE_8 = 258;
	const NOTICE_9 = 259;
	const NOTICE = 260;

	const TODO = 275;
	const WARNING = 300;
	const ERROR = 400;
	const CRITICAL = 500;
	const ALERT = 550;
	const EMERGENCY = 600;

	/**
	 *
	 * @var array $levels - gives a text description of the error type
	 */
	public static $levels = array(
		self::ROCK_BOTTOM_ALL => 'ROCK_BOTTOM_ALL',
		self::ALL => 'All',
		self::DEBUG => 'DEBUG',
		self::DEBUG_1 => 'DEBUG_1',
		self::DEBUG_2 => 'DEBUG_2',
		self::DEBUG_3 => 'DEBUG_3',
		self::DEBUG_4 => 'DEBUG_4',
		self::DEBUG_5 => 'DEBUG_5',
		self::DEBUG_6 => 'DEBUG_6',
		self::DEBUG_7 => 'DEBUG_7',
		self::DEBUG_8 => 'DEBUG_8',
		self::DEBUG_9 => 'DEBUG_9',
		self::INFO => 'INFO',
		self::INFO_1 => 'INFO_1',
		self::INFO_2 => 'INFO_2',
		self::INFO_3 => 'INFO_3',
		self::INFO_4 => 'INFO_4',
		self::INFO_5 => 'INFO_5',
		self::INFO_6 => 'INFO_6',
		self::INFO_7 => 'INFO_7',
		self::INFO_8 => 'INFO_8',
		self::INFO_9 => 'INFO_9',
		self::NOTICE => 'NOTICE',
		self::NOTICE_1 => 'NOTICE_1',
		self::NOTICE_2 => 'NOTICE_2',
		self::NOTICE_3 => 'NOTICE_3',
		self::NOTICE_4 => 'NOTICE_4',
		self::NOTICE_5 => 'NOTICE_5',
		self::NOTICE_6 => 'NOTICE_6',
		self::NOTICE_7 => 'NOTICE_7',
		self::NOTICE_8 => 'NOTICE_8',
		self::NOTICE_9 => 'NOTICE_9',
		self::TODO => 'TODO',
		self::WARNING => 'WARNING',
		self::ERROR => 'ERROR',
		self::CRITICAL => 'CRITICAL',
		self::ALERT => 'ALERT',
		self::EMERGENCY => 'EMERGENCY',
		self::ROCK_BOTTOM_ALL => 'ROCK_BOTTOM_ALL',
	);

	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';

	/** -----------------------------------------------------------------------------------------------
	 * gives a version number
	 * @static
	 * @return type
	 */
	public static function Version() : string{
		return self::VERSION;
	}

	abstract function Show() :void;

	abstract function Set($value = null) : void;

	abstract function Get();
}



//***********************************************************************************************
//***********************************************************************************************

/**
 * a message class
 *     - the base has the text and level
 */
class AMessage extends MessageBase {
	protected $text; // the messageText message
	protected $timeStamp;  // time stamp for the message (for displaying the time)
	protected $level; // level of the message (see defines at top)

	protected $codeDetails;   //  usually something like: filename(line num)function/method name

	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';

	/** -----------------------------------------------------------------------------------------------
	 * construct a message
	 * @param type $text
	 * @param type $timestamp
	 * @param type $level
	 */
	public function __construct($text = null, $timestamp = null, $level = null, ?string $codeDetails = null) {
		$this->setText($text);
		$this->setTimeStamp($timestamp);
		$this->setLevel($level);
		$this->setCodeDetails($codeDetails);
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
	 * converts the message into a string which is formatted [time] level - text
	 * @return type
	 */
	public function __toString() : string {
		return $this->timeStamp . ' (Level: ' . parent::$levels[$this->level] . ') ' . $this->text;
	}

	/** -----------------------------------------------------------------------------------------------
	 * dump the contents of this message
	 * @return void or string
	 */
	public function dump( $returnString = false)  {
		$s =  'msg='. $this->text. ' time='. $this->timeStamp. ' level='. parent::$levels[$this->level] .  '<Br>';

		if ( $returnString){
			return $s;
		} else {
			echo $s ;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * set the contents of the message
	 *     -could be just a string or could be an array( string, timestamp, level)
	 * @param type $textOrArray
	 * @param type $timeStamp
	 * @param type $level
	 * @return void
	 */
	public function set($textOrArray = null, $timeStamp = null, $level = null, ?string $codeDetails = null) :void {
		if (!empty($textOrArray) and is_array($textOrArray)) {
			$this->setFromArray($textOrArray);
		} else {
			$this->setFromArray([$textOrArray, $timeStamp, $level, $codeDetails]);
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
		if (array_key_exists(AR_CODEDETAILS, $ar)) {
			$this->setCodeDetails($ar[AR_CODEDETAILS]);
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
	protected function setTimeStamp(string $timeStamp = null) : void {
		if (defined("IS_PHPUNIT_TESTING")) {
			$this->timeStamp = '23:55:30';
			if (empty($timeStamp)) {
				$this->timeStamp = '23:55:30';
			} else {
				$this->timeStamp = $timeStamp;
			}
		} else {
			if (empty($timeStamp)) {
				$this->timeStamp = date('g:i:s'); // current timestamp
			} else {
				$this->timeStamp = $timeStamp;
			}
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * set the level of the message
	 * @param type $level
	 */
	protected function setLevel($level = null) : void{
		if (empty($level) ) {
			$this->level = AMessage::NOTICE;   //Default
		} else if (array_key_exists($level, parent::$levels)) {
			$this->level = $level;
		} else {
			$this->level = AMessage::NOTICE;   //Default
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string|null $codeDetails
	 * @return void
	 */
	public function setCodeDetails( ?string $codeDetails = null) : void {
		if (empty( $codeDetails)) {
			$this->codeDetails ==null;
		} else {
			$this->codeDetails = $codeDetails;
		}
	}


	/** -----------------------------------------------------------------------------------------------
	 * return the contents of this message in the form of an array
	 *
	 * @return type
	 */
	public function get() : array{
		$a = array($this->text,
			$this->timeStamp,
			$this->level,
			$this->codeDetails
		);
		return $a;
	}

	/** -----------------------------------------------------------------------------------------------
	 * return  appropriate style
	 * @param type $level
	 * @return string
	 */
	protected function getShowStyle($level): string {
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
	protected function getShowTextLeader($level) : string{
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
	protected function getPrettyLine($style = null) : string {
//dump::dumpA($this,substr_count($this->text, '<BR>'), substr_count($this->text, chr(10)), strlen($this->text )  );

//    $string = $this->text;
//    $resultArr = [];
//    $strLength = strlen($string);
//    for ($i = 0; $i < $strLength; $i++) {
//        $resultArr[$i] = ord($string[$i]);
//    }
//    print_r($resultArr);

		$s = '';
		$textLeader = $this->getShowTextLeader($this->level);

		if (!empty($style)) {
			$lineStyle = $style;
		} else {
			$lineStyle = $this->getShowStyle($this->level);
		}

		/* look for multi line output */
		if ( ( ! is_string($this->text))
				or (substr_count($this->text, '<BR>') > 0)
				or (substr_count($this->text, chr(10)) > 0)
				or (strlen($this->text) > 90)
				or ( substr_count($this->text, ' Object ') > 0 )
				or ( substr_count( $this->text,' Array') > 0)
			) {
			$s .= '<div class="' . $lineStyle . '">';
		} else {
			$s .= '<div class="' . $lineStyle . '" style="display: inline;">';
		}

		if (!empty($this->timeStamp)) {
			$s .= '[' . $this->timeStamp . '] ';
		}
		$s .= $textLeader;

		if ( SETTINGS::getPublic('Show MessageLog Display Mode Short Color')){
			$s .= '</div>';
		}

		$s .= ': ';

		if (is_array($this->text)) {
			$this->text = \print_r($this->text, true);
			$x = str_replace("\n", '<BR>', $this->text);
			$y = str_replace(' ', '&nbsp;', $x);
			$z = str_replace("\t", '&nbsp;&nbsp;&nbsp;', $y);
			$s .= $z;
		} else if ( !empty($this->text) and is_string($this->text) and substr_count(strtolower($this->text), '/table' ) > 0){
			$s .= '<pre>';
			$s .= $this->text;
			$s .= '</pre>';
		} else {
			$x = str_replace("\n", '<BR>', $this->text);
			$y = str_replace(' ', '&nbsp;', $x);
			$z = str_replace("\t", '&nbsp;&nbsp;&nbsp;', $y);
			$s .= $z;
		}

		if ( !empty( $this->codeDetails)){
			$s .= '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; - &nbsp;  ';
			//$s .= '<span style="text-align: right;">';
			$s .= (!empty( $this->codeDetails) ? $this->codeDetails : '' );
			//$s .= '</span>';
		}

		if ( ! SETTINGS::getPublic('Show MessageLog Display Mode Short Color')){
			$s .= '</div>';
		}

		$s .= '<BR>';
		$s .= PHP_EOL;
		return $s;
	}

	/** -----------------------------------------------------------------------------------------------
	 * show the contents of this message -- with the appropriate formatting etc
	 *
	 * @param type $style
	 */
	public function show($style = null) :void {
		$r= $this->getPrettyLine($style);
		echo $r;
	}

}

//***********************************************************************************************
//***********************************************************************************************
//
//***********************************************************************************************
//***********************************************************************************************
class SubSystemMessage {
	PUBLIC $subSystem;

	public static $isSuspended = false;
	public static $suspended_SubSystem ='';


	/** -----------------------------------------------------------------------------------------------**/
	function __construct(string $passedSubSystem = MessageLog::DEFAULT_SUBSYSTEM , int $lvl = -9999 ) {  // AMessage::NOTICE){

		if ( $lvl == -9999  or $lvl ==0) {
			$lvl = Settings::getPublic('IS_DETAILED_DEFAULT_NOTIFICATION_LEVEL');       ///MessageLog::$DEFAULTLoggingLevel;
		}
		$this->subSystem = $passedSubSystem;
		Settings::GetRunTimeObject('MessageLog') -> setSubSystemLoggingLevel( $passedSubSystem, $lvl );
	}

	/** -----------------------------------------------------------------------------------------------**/
	public function isNotANullableClass() : bool {
		return true;
	}

	/** -----------------------------------------------------------------------------------------------**/
	public function isGoodLevelsAndSystem( $level = AMessage::NOTICE){
		return MessageLog::isGoodLevelsAndSystem( $level, $this->subSystem);
	}

	/** -----------------------------------------------------------------------------------------------
	 * if the name is something like      addNotice_2 - note the _2 (_33 would break this
	 *          and between the two is the level
	 * @param type $name
	 * @param type $args
	 * @return void
	 */
	public function __call($name, $args) : void{
		if ( substr( $name,-2,1 ) == '_'  and substr($name, 0,3) == 'add') {   //ends in _x and starts with add
			$new_lvl_name = strtoupper(substr($name, 3));
			$lvl_num = array_search($new_lvl_name, MessageBase::$levels);

			if ( ! self::$isSuspended) {
				Settings::GetRunTimeObject('MessageLog') -> add( $args[0], null, $lvl_num, $this->subSystem);
			}
		} else if ( $name == 'Suspend'){
			self::$isSuspended = true;
			Settings::GetRunTimeObject('MessageLog') -> add( 'Suspended MSG Log', null, LVL_DEBUG, $this->subSystem);
		} else if ( $name == 'Resume'){
			Settings::GetRunTimeObject('MessageLog') -> add( 'Resumed MSG Log', null, LVL_DEBUG, $this->subSystem);
			self::$isSuspended = false;
		} else {
			if ( ! self::$isSuspended) {
				Settings::GetRunTimeObject('MessageLog') -> $name(  $args[0], null, $this->subSystem );
			}
		}
	}

}


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
	public static $messageQueue;

	const DEFAULT_SUBSYSTEM = 'general';
	public static $DEFAULTLoggingLevel = MessageBase::WARNING;
	public static $LoggingLevels = null; //array( self::DEFAULT_SUBSYSTEM =>  MessageBase::WARNING);

	/**
	 * @var version number
	 */
	private const VERSION = '0.4.0';


	/** -----------------------------------------------------------------------------------------------
	 * construct a message log - i.e. the queue
	 */
	function __construct() {
		if (empty(self::$messageQueue)) {
			self::$messageQueue = new \SplQueue();
		}
		self::$DEFAULTLoggingLevel = Settings::getPublic('IS_DETAILED_DEFAULT_NOTIFICATION_LEVEL');
		self::$LoggingLevels = array(self::DEFAULT_SUBSYSTEM => Settings::getPublic('IS_DETAILED_DEFAULT_NOTIFICATION_LEVEL'));
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
	 *
	 * @return bool
	 */
	public function isNotANullableClass() : bool {
		return true;
	}

	/** -----------------------------------------------------------------------------------------------
	 * handle trying to show message log as a string
	 * @return string
	 */
	public function __toString() : string{
		$s = '';
		self::$messageQueue->rewind();

		while (self::$messageQueue->valid()) {
			$x = self::$messageQueue->current();
			$y = $x->__toString();
			$w = str_replace('&nbsp;', ' ', $y);
			$w2 = str_replace('  ', ' ', $w);
			$v  = strip_tags($w2);
			$result  = preg_replace('/[^a-zA-Z0-9_ :()-]/s','',$v);

			$s .= $result;
			$s .= PHP_EOL;
			self::$messageQueue->next(); //switch to next list item
		}
		$s .=  print_r(self::$LoggingLevels,true);
		return $s;
	}





	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return type
	 */
	public function __debugInfo() {
		//return [MessageLog::messageQueue, MessageLog::DEFAULTLoggingLevel, MessageLog::LoggingLevels];

		//		//Settings::SetPublic('Show MessageLog Adds', false);
		//Settings::SetPublic('Show MessageLog Adds_FileAndLine', false);

		$loglevelsAR = array();
		foreach(self::$LoggingLevels as $key =>$value) {
			$loglevelsAR[$key] = $value . ' (' . MessageBase::$levels[ $value] . ')';
		}

		return [
			'Default_level' => MessageLog::$DEFAULTLoggingLevel . ' (' .MessageBase::$levels[MessageLog::$DEFAULTLoggingLevel] . ')',
			//'Default_level_raw' =>MessageLog::$DEFAULTLoggingLevel,
			'default_subsystem' => self::DEFAULT_SUBSYSTEM,
			'Logging_levels' => $loglevelsAR, //print_r(self::$LoggingLevels, true),
			//'queue' => $this->giveUglyMessageQueue(),
			];
	}

	/** -----------------------------------------------------------------------------------------------
	 * add a new message to the stack ( may include some values passed down to the message class)
	 *
	 * @param type $obj_or_array
	 * @param type $val2
	 * @param type $val3
	 */
	public function addAndShow($obj_or_array = null, $val2 = null, $val3 = null) : void{
		$this->add($obj_or_array, $val2, $val3);
		$this->showAllMessages();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $level
	 * @param string $subSystem
	 * @return bool
	 */
	public static function isGoodLevelsAndSystem( $level, string $subSystem) : bool {
		if (key_exists($subSystem, self::$LoggingLevels)) {
			$lvl = self::$LoggingLevels[$subSystem];
		} else {
			self::$LoggingLevels[ $subSystem ] = self::$DEFAULTLoggingLevel;
			$lvl = self::$DEFAULTLoggingLevel;
		}
		//$x = ( ($level >= $lvl) ? '--True--':'--false--');
		return ( $level >= $lvl) ;
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $subSystem
	 * @param type $level
	 */
	public function setSubSystemLoggingLevel( string $subSystem, $level = null) : void{
		if (is_null( $level) ){
			$level = self::$DEFAULTLoggingLevel;
		}
		self::$LoggingLevels[$subSystem] = $level;
	}


	/** -----------------------------------------------------------------------------------------------
	 * figure out where to start showing the back trace
	 *   - if you called the MessageLog system directly then it is one back
	 *   - if you called it thru subSystemMesage than it may be further back - so look for the magic word and return it
	 *
	 * @param type $bt
	 * @param type $magicWord
	 * @return int
	 */
	protected function figureOutWhichBTisRelevant($bt, $magicWord = 'MessageLog.class.php') :int {
		$r = 0;

		for ($i = 0; $i <= count($bt); $i++) {
			if (basename($bt[$i]['file']) != $magicWord) { // look for the magic word in the file name
				$r = $i;
				break;
			}
		}
		if ($r >= count($bt)) {
			return $r - 1;  // not found so return one item before the last item (becuase of the btLvl+1 in generateGoodBT
		} else {
			return $r;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * make a back trace look pretty
	 * @param type $bt
	 * @param bool $includeSpan
	 * @return string
	 */
	protected function generateGoodBT($bt) :string{
		$btLvl = $this->figureOutWhichBTisRelevant($bt);
		$mid =  basename($bt[$btLvl]['file'])
				. ':'
				. $bt[$btLvl]['line']
				. ' ('
				. (empty($bt[$btLvl + 1]['class']) ? '' : basename($bt[$btLvl + 1]['class']) )
				. '.'
				. (empty($bt[$btLvl + 1]['function']) ? '' : $bt[$btLvl + 1]['function'] )
				. ')';


		return $mid;
	}

	/** -----------------------------------------------------------------------------------------------
	 * add a new message to the stack ( may include some values passed down to the message class)
	 *
	 * @param type $obj_or_array
	 * @param type $timestamp
	 * @param type $level
	 */
	public function add($obj_or_array = null, $timestamp = null, $level = null, string $subSystem = self::DEFAULT_SUBSYSTEM )  :void{
		if ( ! self::isGoodLevelsAndSystem( $level, $subSystem)) {
			return;  // if msg level is lower than setting then do nothing
		}

		$codeDetails ='';
		$bt = debug_backtrace(false, 5);
		$codeDetails =  $this->generateGoodBT($bt, false);

		if (is_object($obj_or_array) and ( $obj_or_array instanceof AMessage )) {
			$obj_or_array->setCodeDetails =  $codeDetails;
			self::$messageQueue->enqueue($obj_or_array);
			$temp = $obj_or_array;  // needed later for the show adds
		} else {
			if (Settings::GetPublic('Show MessageLog Adds_FileAndLine')) {
				$temp = new AMessage($obj_or_array, $timestamp, $level, $codeDetails);               //create the AMessage
			} else {
				$temp = new AMessage($obj_or_array, $timestamp, $level);
			}
			// add the item to the queue
			self::$messageQueue->enqueue($temp);
		}
		if (Settings::GetPublic('Show MessageLog Adds')) {
			$temp->show();
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * add a to do message to the log
	 * @param type $obj_or_array
	 * @param type $timestamp
	 */
	public function addToDo($obj_or_array = null, $timestamp = null, string $subSystem=self::DEFAULT_SUBSYSTEM ): void {
		if (is_array($obj_or_array) and ! empty($obj_or_array[2])) {
			$obj_or_array[2] = AMessage::TODO;
		}
		$this->add($obj_or_array, $timestamp, AMessage::TODO, $subSystem);
	}

	/** -----------------------------------------------------------------------------------------------
	 * add a debug message
	 * @param type $obj_or_array
	 * @param type $timestamp
	 */
	public function addDebug($obj_or_array = null, $timestamp = null, string $subSystem=self::DEFAULT_SUBSYSTEM ) : void{
		if (is_array($obj_or_array) and ! empty($obj_or_array[2])) {
			$obj_or_array[2] = AMessage::DEBUG;
		}
		$this->add($obj_or_array, $timestamp, AMessage::DEBUG, $subSystem);
	}

	/** -----------------------------------------------------------------------------------------------
	 * add a info message
	 * @param type $obj_or_array
	 * @param type $timestamp
	 */
	public function addInfo($obj_or_array = null, $timestamp = null, string $subSystem=self::DEFAULT_SUBSYSTEM ) :void {
		if (is_array($obj_or_array) and ! empty($obj_or_array[2])) {
			$obj_or_array[2] = AMessage::INFO;
		}
		$this->add($obj_or_array, $timestamp, AMessage::INFO, $subSystem);
	}

	/** -----------------------------------------------------------------------------------------------
	 * add a notice message
	 * @param type $obj_or_array
	 * @param type $timestamp
	 */
	public function addNotice($obj_or_array = null, $timestamp = null, string $subSystem=self::DEFAULT_SUBSYSTEM ) :void {
		if (is_array($obj_or_array) and ! empty($obj_or_array[2])) {
			$obj_or_array[2] = AMessage::NOTICE;
		}
		$this->add($obj_or_array, $timestamp, AMessage::NOTICE, $subSystem);
	}

	/** -----------------------------------------------------------------------------------------------
	 * add a warning message
	 * @param type $obj_or_array
	 * @param type $timestamp
	 */
	public function addWarning($obj_or_array = null, $timestamp = null, string $subSystem=self::DEFAULT_SUBSYSTEM ):void {
		if (is_array($obj_or_array) and ! empty($obj_or_array[2])) {
			$obj_or_array[2] = AMessage::WARNING;
		}
		$this->add($obj_or_array, $timestamp, AMessage::WARNING, $subSystem);
	}

	/** -----------------------------------------------------------------------------------------------
	 * add an error message
	 * @param type $obj_or_array
	 * @param type $timestamp
	 */
	public function addError($obj_or_array = null, $timestamp = null, string $subSystem=self::DEFAULT_SUBSYSTEM ) :void {
		if (is_array($obj_or_array) and ! empty($obj_or_array[2])) {
			$obj_or_array[2] = AMessage::ERROR;
		}
		$this->add($obj_or_array, $timestamp, AMessage::ERROR, $subSystem);
	}

	/** -----------------------------------------------------------------------------------------------
	 * add a critical message
	 * @param type $obj_or_array
	 * @param type $timestamp
	 */
	public function addCritical($obj_or_array = null, $timestamp = null, string $subSystem=self::DEFAULT_SUBSYSTEM ) {
		if (is_array($obj_or_array) and ! empty($obj_or_array[2])) {
			$obj_or_array[2] = AMessage::CRITICAL;
		}
		$this->add($obj_or_array, $timestamp, AMessage::CRITICAL, $subSystem);
	}

	/** -----------------------------------------------------------------------------------------------
	 * add an alert message
	 * @param type $obj_or_array
	 * @param type $timestamp
	 */
	public function addAlert($obj_or_array = null, $timestamp = null, string $subSystem=self::DEFAULT_SUBSYSTEM ) : void{
		if (is_array($obj_or_array) and ! empty($obj_or_array[2])) {
			$obj_or_array[2] = AMessage::ALERT;
		}
		$this->add($obj_or_array, $timestamp, AMessage::ALERT, $subSystem);
	}

	/** -----------------------------------------------------------------------------------------------
	 * add an emergency message
	 * @param type $obj_or_array
	 * @param type $timestamp
	 */
	public function addEmergency($obj_or_array = null, $timestamp = null, string $subSystem=self::DEFAULT_SUBSYSTEM ) : void{
		if (is_array($obj_or_array) and ! empty($obj_or_array[2])) {
			$obj_or_array[2] = AMessage::EMERGENCY;
		}
		$this->add($obj_or_array, $timestamp, AMessage::EMERGENCY, $subSystem);
	}

	/** -----------------------------------------------------------------------------------------------
	 * are there any messages in the queue
	 * @return type
	 */
	public function hasMessages() : bool {
		return (self::$messageQueue->count() > 0);
	}

	/** -----------------------------------------------------------------------------------------------
	 * how many messages are still in the queue
	 * @return type
	 */
	public function stackSize() : int {
		return self::$messageQueue->count();
	}

	/** -----------------------------------------------------------------------------------------------
	 *  give a string with as little formatting as possible
	 *	   if 'Show MessageLog Adds_FileAndLine' is set then there will be the file and line formatted
	 * @return string
	 */
	public function giveUglyMessageQueue() : string {
		$s ='';
		$i =1;
		self::$messageQueue->rewind();
		while(self::$messageQueue->valid()){
			$s .= ' (' . $i++ . ') '. self::$messageQueue->current()->dump(true);
			self::$messageQueue->next();
		}
		self::$messageQueue->rewind();
		return $s;
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
	public function showAllMessages($messageText_after_each_line = '<br>') : void {
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
	public function showAllMessagesInBox($includeFieldSet = true) : void{
		if ($includeFieldSet) {
			?><fieldset class="msg_fieldset"><Legend id="message_box_show_all_in_box" class="msg_legend">Messages</legend><?php
				}
				if ($this->hasMessages()) {
					$this->showAllMessages('');
				} else {
					echo '&nbsp;';
				}
				if ($includeFieldSet) {
					?></fieldset><?php
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return void
	 */
	public  function TestAllLevels() : void {

		//Settings::SetPublic('Show MessageLog Adds', false);
		//Settings::SetPublic('Show MessageLog Adds_FileAndLine', false);

		$this->setSubSystemLoggingLevel('TESTER_messages', MessageBase::ROCK_BOTTOM_ALL );

		self::$DEFAULTLoggingLevel = MessageBase::ROCK_BOTTOM_ALL;

		foreach(  MessageBase::$levels as $key => $value){

			//echo 'Key= ' , $key, ' value=' , $value;
			//echo '<BR>';

			$this->add( 'This is a test of: ' . $value . ' (' . $key . ')', null, $key, 'TESTER_messages');
			//echo '<BR>';
		}
		if ( ! Settings::GetPublic('Show MessageLog in Footer')) {
			$this->showAllMessagesInBox();
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
