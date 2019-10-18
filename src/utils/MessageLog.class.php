<?php
//**********************************************************************************************
//* messagelog.class.php
//*
//* $Id$
//* $Rev: 0000 $
//* $Date: 2019-08-30 12:01:00 -0700 (Fri, 30 Aug 2019) $
//*
//* DESCRIPTION:
//*
//* USAGE:
//*
//* HISTORY:
//* 30-Aug-19 M.Merrett - Created
//*
//* TODO:
//*
//***********************************************************************************************************
//***********************************************************************************************************


namespace php_base\Utils;

//if ( ! defined( "IS_PHPUNIT_TESTING")){
//	<link rel="stylesheet" href=".\static\css\message_stack_style.css"><?php
//}


use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;


define('AR_TEXT',0);
define('AR_TimeStamp', 1);
define('AR_LEVEL',2);


//***********************************************************************************************
//***********************************************************************************************
abstract class MessageBase {

	const DEBUG = 100;
	const INFO = 200;
	const NOTICE = 250;
	const WARNING = 300;
	const ERROR = 400;
	const CRITICAL = 500;
	const ALERT = 550;
	const EMERGENCY = 600;


 	protected static $levels = array(
        self::DEBUG     => 'DEBUG',
        self::INFO      => 'INFO',
        self::NOTICE    => 'NOTICE',
        self::WARNING   => 'WARNING',
        self::ERROR     => 'ERROR',
        self::CRITICAL  => 'CRITICAL',
        self::ALERT     => 'ALERT',
        self::EMERGENCY => 'EMERGENCY',
    );

	protected $text;	// the messageText message
	protected $timeStamp;		// time stamp for the message (for displaying the time)
	protected $level;				// level of the message (see defines at top)

	abstract function Show();
	abstract function Set($value=null);
	abstract function Get();
}



//***********************************************************************************************
//***********************************************************************************************
class AMessage extends MessageBase {

	public $timestamp;

	//-----------------------------------------------------------------------------------------------
	public function __construct( $text=null, $timestamp=null, $level=null) {
		$this->setText( $text);
		$this->setTimeStamp( $timestamp);
		$this->setLevel($level );
	}

	//-----------------------------------------------------------------------------------------------
	public function __toString() {
		return $this->timeStamp . ' (Level: ' . parent::$levels[ $this->level] .') '. $this->text;
	}

	//-----------------------------------------------------------------------------------------------
	// dump the contents of this message
	public function dump() {
		echo 'msg=',$this->text, ' time=', $this->timeStamp, ' level=', parent::$levels[ $this->level], '<Br>';
	}


	//-----------------------------------------------------------------------------------------------
	// set the contents of the message
	//      could be just a string or could be an array( string, timestamp, level)
	public function set($textOrArray=null, $timeStamp=null, $level=null){

		if ( !empty( $textOrArray) and is_array($textOrArray) ){
			$this->setFromArray($textOrArray);
		} else {
			$this->setFromArray([$textOrArray, $timeStamp, $level]);
		}
	}


///} elseif ( is_string($textOrArray)) {
///			$this->setText($textOrArray);


	//-----------------------------------------------------------------------------------------------
	protected function setFromArray( $ar = null ): void {
		if ( array_key_exists(AR_TEXT, $ar)){
			$this->setText( $ar[AR_TEXT]);
		}
		if ( array_key_exists(AR_TimeStamp, $ar)){
			$this->setTimeStamp( $ar[AR_TimeStamp]);
		}
		if ( array_key_exists(AR_LEVEL, $ar)){
			$this->setLevel($ar[AR_LEVEL] );
		}
	}

	//-----------------------------------------------------------------------------------------------
	protected function setText(string $textString= null) :void{
		if (empty( $textString)){
				$this->text = '';
		} else {
			$this->text = $textString;
		}
	}

	//-----------------------------------------------------------------------------------------------
	protected function setTimeStamp(string $timeStamp = null){
		if (  defined( "IS_PHPUNIT_TESTING")){
			$this->timeStamp = '23:55:30';
			if ( empty($timeStamp)) {
				$this->timeStamp = '23:55:30';
			} else {
				$this->timeStamp = $timeStamp;
			}
		}  else {
			if ( empty($timeStamp)) {
				$this->timeStamp = date( 'g:i:s');    // current timestamp
			} else {
				$this->timeStamp = $timeStamp;
			}
		}
	}

	//-----------------------------------------------------------------------------------------------
	protected function setLevel( $level = null){
		if(empty($level) or $level <100){
			$this->level = AMessage::NOTICE;      //Default
		} else if ( array_key_exists($level, parent::$levels) ) {
			$this->level = $level;
		} else {
			$this->level = AMessage::NOTICE;      //Default
		}
	}


	//-----------------------------------------------------------------------------------------------
	// return the contents of this message in the form of an array
	public function get() {
		//$a = array();
		$a = array( $this->text,
					  $this->timeStamp,
					  $this->level
					);
		return $a;
	}


	//-----------------------------------------------------------------------------------------------
	protected function getShowStyle($level) {
		if (array_key_exists($level, parent::$levels) ){
			return 'msg_style_' . parent::$levels[$level];
		} else {
			return 'msg_style_UNKNOWN';
		}
	}

	//-----------------------------------------------------------------------------------------------
	protected function getShowTextLeader($level){
		if (array_key_exists($level, parent::$levels) ){
			return parent::$levels[$level] . ' ';
		} else {
			return 'UNKNOWN ';
		}
	}


	//-----------------------------------------------------------------------------------------------
	protected function getPrettyLine(){
		$s ='';
		$lineStyle = $this->getShowStyle( $this->level);
		$textLeader = $this->getShowTextLeader($this->level);

		$s .= '<span class="' . $lineStyle . '">';
		if ( !empty( $this->timeStamp)) {
			$s .= '['.  $this->timeStamp . '] ';
		}
		$s .= $textLeader;

		$x = str_replace( "\n", '<BR>', $this->text);
		$s .= $x;

		$s .= '</span>';
//Dump::dump($s);
		return $s;
	}
	//-----------------------------------------------------------------------------------------------
	// show the contents of this message -- with the approprieate formatting etc
	public function show() {
		echo  $this->getPrettyLine();
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
class MessageLog {

	protected static $messageQueue;

	//-----------------------------------------------------------------------------------------------
	function __construct(){
		if (empty( self::$messageQueue)) {
			self::$messageQueue = new \SplQueue();
		}
	}


	//-----------------------------------------------------------------------------------------------
	public function __toString() {

		$s = '';
		self::$messageQueue->rewind();

		while(self::$messageQueue->valid()){
    		$x = self::$messageQueue->current();
    		$s .= $x->__toString();
    		$s .= '<br />';
    		self::$messageQueue->next();//switch to next list item
		}
		return $s;
	}

	//-----------------------------------------------------------------------------------------------
	// add a new message to the stack ( may include some values passed down to the message class)
	public function addAndShow(  $obj_or_array=null, $val2=null, $val3=null) {
		$this->add(  $obj_or_array, $val2, $val3);

		$this->showAllMessages();
	}


	//-----------------------------------------------------------------------------------------------
	// add a new message to the stack ( may include some values passed down to the message class)
	public function add( $obj_or_array=null, $timestamp=null, $level=null) {
		if ( is_object( $obj_or_array) and ( $obj_or_array instanceof AMessage )){
			self::$messageQueue->enqueue( $obj_or_array);
		} else {
			$temp = new AMessage( $obj_or_array, $timestamp, $level);
			//$temp = new $this->a_msg_handler_class ( $obj_or_array, $val2, $val3);
			self::$messageQueue->enqueue( $temp);

			//if ( ! empty( $GLOBALS['log'])) $GLOBALS['log']->log( 'MSG>' . $obj_or_array);
		}
	}

	//-----------------------------------------------------------------------------------------------
	public function addDebug( $obj_or_array=null, $timestamp=null) {
		if (is_array($obj_or_array)  and !empty( $obj_or_array[2])) {
			$obj_or_array[2] = AMessage::DEBUG;
		}
		$this->add( $obj_or_array, $timestamp, AMessage::DEBUG);
	}

	//-----------------------------------------------------------------------------------------------
	public function addInfo( $obj_or_array=null, $timestamp=null) {
		if (is_array($obj_or_array)  and !empty( $obj_or_array[2])) {
			$obj_or_array[2] = AMessage::INFO;
		}
		$this->add( $obj_or_array, $timestamp, AMessage::INFO);
	}

	//-----------------------------------------------------------------------------------------------
	public function addNotice( $obj_or_array=null, $timestamp=null) {
		if (is_array($obj_or_array)  and !empty( $obj_or_array[2])) {
			$obj_or_array[2] = AMessage::NOTICE;
		}
		$this->add( $obj_or_array, $timestamp, AMessage::NOTICE);
	}

	//-----------------------------------------------------------------------------------------------
	public function addWarning( $obj_or_array=null, $timestamp=null) {
		if (is_array($obj_or_array)  and !empty( $obj_or_array[2])) {
			$obj_or_array[2] = AMessage::WARNING;
		}
		$this->add( $obj_or_array, $timestamp, AMessage::WARNING);
	}

	//-----------------------------------------------------------------------------------------------
	public function addError( $obj_or_array=null, $timestamp=null) {
		if (is_array($obj_or_array)  and !empty( $obj_or_array[2])) {
			$obj_or_array[2] = AMessage::ERROR;
		}
		$this->add( $obj_or_array, $timestamp, AMessage::ERROR);
	}

	//-----------------------------------------------------------------------------------------------
	public function addCritical( $obj_or_array=null, $timestamp=null) {
		if (is_array($obj_or_array)  and !empty( $obj_or_array[2])) {
			$obj_or_array[2] = AMessage::CRITICAL;
		}
		$this->add( $obj_or_array, $timestamp, AMessage::CRITICAL);
	}

	//-----------------------------------------------------------------------------------------------
	public function addAlert( $obj_or_array=null, $timestamp=null) {
		if (is_array($obj_or_array)  and !empty( $obj_or_array[2])) {
			$obj_or_array[2] = AMessage::ALERT;
		}
		$this->add( $obj_or_array, $timestamp, AMessage::ALERT);
	}

	//-----------------------------------------------------------------------------------------------
	public function addEmergency( $obj_or_array=null, $timestamp=null) {
		if (is_array($obj_or_array)  and !empty( $obj_or_array[2])) {
			$obj_or_array[2] = AMessage::EMERGENCY;
		}
		$this->add( $obj_or_array, $timestamp, AMessage::EMERGENCY);
	}

	//-----------------------------------------------------------------------------------------------
	public function hasMessages() {
		return (self::$messageQueue->count() >0);
	}

	//-----------------------------------------------------------------------------------------------
	public function stackSize() {
		return self::$messageQueue->count();
	}

	//-----------------------------------------------------------------------------------------------
	//  pop a message off the stack and return it
	public function getNextMessage() {
		if ( self::$messageQueue->count() >0) {
			//$temp = array_shift( $this->message_stack);
			$temp = self::$messageQueue->dequeue();
			return $temp;
		} else {
			return false;
		}
	}

	//-----------------------------------------------------------------------------------------------
	//  show the next item on the stack (causes a get_next_message which will remove it from the stack)
	public function showNextMessage() {
		$temp = $this->getNextMessage();
		if (!empty( $temp)) {
			$temp->show();
			return true;
		} else {
			return false;
		}
	}


	//-----------------------------------------------------------------------------------------------
	// show all the messages on the stack (efectivey emptying the stack
	public function showAllMessages( $messageText_after_each_line='<br>'){
		while( $temp = $this->showNextMessage() ){
			if ( !empty( $messageText_after_each_line)) {
				echo $messageText_after_each_line;
			}
		}
	}

	//-----------------------------------------------------------------------------------------------
	// show all the messages on the stack (efectivey emptying the stack)
	//  and do it in a pretty box :-)
	public function showAllMessagesInBox( $includeFieldSet= true) {
		if ($includeFieldSet) {
			?><fieldset class="msg_fieldset"><Legend id="message_box_show_all_in_box" class="msg_legend">Messages</legend><?php
		}
				if ( $this->hasMessages()) {
					$this->showAllMessages();
				} else {
					echo '&nbsp;';
				}
		if ($includeFieldSet) {
			?></fieldset><?php
		}
	}
}


/// some usage examples

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
