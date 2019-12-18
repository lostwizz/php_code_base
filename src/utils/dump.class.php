<?php

/** * ********************************************************************************************
 * dump.class.php
 *
 * Summary maintains 3 queues (Pre/Dispatcher/Post) and executes thing in the queues.
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.4.0
 * $Id$
 *
 * Description.
 * maintains 3 queues and then executes them in order -- and checks the response of the execution
 *    and may abort or continue on processing.
 *
 *
 *
 * @package ModelViewController - Dispatcher
 * @subpackage Dispatcher
 * @since 0.4.0
 *
 * @example
 *        use \php_base\Utils\Dump\Dump as Dump;
 *        useage: Dump::dump($fred);
 *
 *
 * @todo Description
 *
 */
//**********************************************************************************************
//https://docs.phpdoc.org/references/phpdoc/tags/index.html

namespace php_base\Utils\Dump;

/** * ********************************************************************************************
 * holding place for the semi processed data
 */
class DumpData {

	public $backTrace;
	public $variableName;
	public $fileName;
	public $lineNum;
	public $codeLine;
	public $serverName;
	public $title;
	public $variable;
	public $preCodeLines = array();
	public $postCodeLines = array();

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return array
	 */
	public function ToArray(): array {
		return array('backTrace' => $this->backTrace,
			'variableName' => $this->variableName,
			'fileName' => $this->fileName,
			'lineNum' => $this->lineNum,
			'codeLine' => $this->codeLine,
			'serverName' => $this->serverName,
			'title' => $this->title,
			'variable' => $this->variable,
			'preCodeLines' => $this->preCodeLines,
			'postCodeLines' => $this->postCodeLines,
		);
	}

}

//***********************************************************************************************************
//***********************************************************************************************************
//abstract class VarName{





/* * ******************************************************************************
 *  Dump
 *     - a utility to output a vaiable - and the line it was called from (usefull for tracing
 * 		  - it puts the output in a nice pale yellow box that is easy to spot
 *        - if the value of the variable is more thant x(10) lines then it will put scroll bars on it
 *
 * defined public methods:
 * 					dump
  dumpLong
  dump3PrePost
  dumpLong3PrePost
  dumpClasses
  arrayDisplayCompactor
 * @category Utility
 * @version 0.4.0
 */
abstract class Dump {

	// own big a output block can be before adding scrollbars
//	protected static $FLAT_WINDOW_LINES = 10;
//	protected static $PRE_CodeLines = 0;
//	protected static $POST_CodeLines = 0;

	public static $settings =null;

	/**
	 * @var version number
	 */
	private const VERSION = '0.4.0';

	/** ----------------------------------------------------------------------------------------------
	 *
	 * @param type $array
	 */
	public static function initSettings( $array = null){
		self::$settings = new dumpSettings();
		if (!empty( $array)){
			self::$settings->addArray($array);
		}
		self::setDefaultSettings();
	}

	/** ----------------------------------------------------------------------------------------------
	 *
	 */
	public static function setDefaultSettings() {
		self::$settings->add( 'FLAT_WINDOWS_LINES', 10);
		self::$settings->add( 'PRE_CodeLines', 0);
		self::$settings->add( 'POST_CodeLines', 0);
		self::$settings->add( 'Show BackTrace Num Lines', 0);
		self::$settings->add( 'Only Return Output String', false);
		self::$settings->add( 'skipNumLines', false);
		self::$settings->add( 'Beautify is On', true );
		self::$settings->add( 'Beautify_BackgroundColor', '#FFFDCC'); //'#E3FCFD');


		self::$settings->dump();
	}

	public static function setLongSettings() {
		self::$settings->update( 'FLAT_WINDOWS_LINES', 50);
		self::$settings->update( 'Beautify_BackgroundColor', '#EED6FE');

		self::$settings->dump();
	}

	public static function set3Pre3PostSettings() {
		self::$settings->update( 'PRE_CodeLines', 3);
		self::$settings->update( 'POST_CodeLines', 3);
		self::$settings->update( 'Beautify_BackgroundColor', '#F0FFD5');
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
	 * method to dump an array of declared classes and to optionally search for a string in the list
	 * @param string $search
	 * @return nothing
	 */
	public static function dumpClasses($search = null) {
		$bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
		$data = new DumpData();

		self::DumpClassesHelper($data, $bt, $search);

		$s = self::DumpClassBeautifierPRE($data);

		$s .= self::DumpClassGetData($search);

		$s .= self::DumpClassBeautifierPOST($data);
		$s .= self::BeautifyAreaEnd();
		echo $s;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $data
	 * @param type $bt
	 * @param type $search
	 */
	protected static function DumpClassesHelper($data, $bt, $search = null) {
		self::SetBackTrace($data, $bt);
		self::SetVariableName($data, 'DumpClasses', $bt);
		self::SetVariableValue($data, 'DumpClasses');

		if (empty($search)) {
			self::SetTitle($data, 'Classes Dump');
		} else {
			self::SetTitle($data, 'Classes Dump and Searching for: (' . $search . ') - Found:');
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $data
	 * @return type
	 */
	protected static function DumpClassBeautifierPRE($data) {
		$old = self::$settings->get( 'FLAT_WINDOWS_LINES');
		self::$settings->update( 'FLAT_WINDOWS_LINES' , -1);    // fake out the ten line scroll area - to show the whole array
		$b = self::BeautifyAreaStart($data, /*'#E3FCFD',*/ true);
		$t = self::BeautifyTitle($data);
		self::$settings->update( 'FLAT_WINDOWS_LINES', $old);
		return $b . $t;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $data
	 * @return type
	 */
	protected static function DumpClassBeautifierPOST($data) {
		return self::BeautifyLineData($data);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $search
	 * @return type
	 */
	protected static function DumpClassGetData($search) {

		if (empty($search)) {
			return print_r(get_declared_classes(), true);
		} else {
			$ar = get_declared_classes();
			foreach ($ar as $item) {
				if (stripos($item, $search) > 0) {
					return print_r($item, true);
					break;
				}
			}
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * method to dump a variable and output it so it is pretty and easy to find and where it came from
	 * @param $obj - some object - any type should work
	 * @param $title - some optional text that will show above the variable namespace
	 * @param $showBT - optionally show the back trace when the dump was called (it grabs the back trace as fist thing done
	 * @param $onlyReturnValue - normaly it will echo the results out - but if true then it will return a string ready to print instead
	 * @param $noBeautify - if true then no making it look pretty - output is unformatted
	 *
	 * @return either true or a string that can be printed for pretty (dependand on param $onlyReturnValue
	 */
	public static function dump($obj, $title = ''){   //, int $showBT = 0, $onlyReturnValue = false, $noBeautify = false) {
		if (empty(self::$settings)){
			self::initSettings();
		}
		$bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, self::$settings->get( 'Show BackTrace Num Lines')  );  // get it early

		return SELF::dumpHelper($bt, $obj, $title); //, $showBT, $onlyReturnValue, $noBeautify);
	}

	/** -----------------------------------------------------------------------------------------------
	 * method to expand the number of lines that show in the dump and then reset it back to the default
	 * @param $obj - some object - any type should work
	 * @param $title - some optional text that will show above the variable namespace
	 * @param $showBT - optionally show the back trace when the dump was called (it grabs the back trace as fist thing done
	 * @param $onlyReturnValue - normaly it will echo the results out - but if true then it will return a string ready to print instead
	 * @param $noBeautify - if true then no making it look pretty - output is unformatted
	 *
	 * @return either true or a string that can be printed for pretty (dependand on param $onlyReturnValue
	 *
	 */
	protected static function dumpHelper($bt, $obj, $title ){//, $showBT, $onlyReturnValue, $noBeautify, /*$bgColor = '#FFFDCC',*/ $skipNumLines = false) {
		if (empty(self::$settings)){
			self::initSettings();
		}

		$data = new DumpData($obj, $title);
		self::SetBackTrace($data, $bt);

		self::SetVariableName($data, $obj, $bt);
		self::SetTitle($data, $title);
		self::SetVariableValue($data, $obj);

		if ( self::$settings->get( 'Beautify is On')) {
			$s = self::BeautifyOutput($data );  //, $showBT,/*$bgColor,*/ $skipNumLines);
		} else {
			$s = self::plainOutput($data); //, $showBT);
		}

		if (self::$settings->get( 'Only Return Output String')) {
			return $s;
		} else {
			echo $s;
			return true;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * method to expand the number of lines that show in the dump and then reset it back to the default
	 * @param $obj - some object - any type should work
	 * @param $title - some optional text that will show above the variable namespace
	 * @param $showBT - optionally show the back trace when the dump was called (it grabs the back trace as fist thing done
	 * @param $onlyReturnValue - normaly it will echo the results out - but if true then it will return a string ready to print instead
	 * @param $noBeautify - if true then no making it look pretty - output is unformatted
	 *
	 * @return either true or a string that can be printed for pretty (dependand on param $onlyReturnValue
	 *
	 */
	public static function dumpLong($obj, $title = ''){ //, $showBT = false, $onlyReturnValue = false, $noBeautify = false) {
		$bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, self::$settings->get( 'Show BackTrace Num Lines'));  // get it early

		if (empty(self::$settings)){
			self::initSettings();
		}
		self::setLongSettings();


		//$old = self::$settings->get( 'FLAT_WINDOWS_LINES');
		//self::$settings->update( 'FLAT_WINDOWS_LINES', 50);

		$r = SELF::dumpHelper($bt, $obj, $title); //, $showBT, $onlyReturnValue, $noBeautify, '#FFFDCC', true);

		//self::$settings->update( 'FLAT_WINDOWS_LINES',  $old);
		return $r;
	}

	/** -----------------------------------------------------------------------------------------------
	 * method to expand the number of lines that show in the dump and then reset it back to the default
	 * @param $obj - some object - any type should work
	 * @param $title - some optional text that will show above the variable namespace
	 * @param $showBT - optionally show the back trace when the dump was called (it grabs the back trace as fist thing done
	 * @param $onlyReturnValue - normaly it will echo the results out - but if true then it will return a string ready to print instead
	 * @param $noBeautify - if true then no making it look pretty - output is unformatted
	 *
	 * @return either true or a string that can be printed for pretty (dependand on param $onlyReturnValue
	 *
	 */
	public static function dump3PrePost($obj, $title = '', $showBT = false, $onlyReturnValue = false, $noBeautify = false) {
		$bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, self::$settings->get( 'Show BackTrace Num Lines'));  // get it early

		if (empty(self::$settings)){
			self::initSettings();
		}
		self::set3Pre3PostSettings();

		//$old_pre = self::$settings->get( 'PRE_CodeLines');
		//$old_post = self::$settings->get( 'POST_CodeLines');
		//self::$settings->update( 'PRE_CodeLines', 3);
		//self::$settings->update( 'POST_CodeLines', 3);

		$r = SELF::dumpHelper($bt, $obj, $title);//, $showBT, $onlyReturnValue, $noBeautify);

		//self::$settings->update( 'PRE_CodeLines', $old_pre);
		//self::$settings->update( 'POST_CodeLines', $old_post);

		return $r;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $obj
	 * @param type $title
	 * @param type $showBT
	 * @param type $onlyReturnValue
	 * @param type $noBeautify
	 * @return type
	 */
	public static function dumpLong3PrePost($obj, $title = '', $showBT = false, $onlyReturnValue = false, $noBeautify = false) {
		$bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, self::$settings->get( 'Show BackTrace Num Lines'));  // get it early

		if (empty(self::$settings)){
			self::initSettings();
		}


		$old_pre = SELF::$PRE_CodeLines;
		$old_post = SELF::$POST_CodeLines;
		SELF::$PRE_CodeLines = 3;
		SELF::$POST_CodeLines = 3;

		$old = SELF::$FLAT_WINDOW_LINES;
		SELF::$FLAT_WINDOW_LINES = 50;

		$r = SELF::dumpHelper($bt, $obj, $title, $showBT, $onlyReturnValue, $noBeautify);

		SELF::$PRE_CodeLines = $old_pre;
		SELF::$POST_CodeLines = $old_post;

		SELF::$FLAT_WINDOW_LINES = $old;

		return $r;
	}

	/** -----------------------------------------------------------------------------------------------
	 * method to process the back trance and then add it to the data for later usefull
	 * @param $data - holding place for all the unbeautified data
	 * @param $bt - back trace array ready to be processed
	 *
	 * @return - nothing
	 */
	protected static function SetBackTrace($data, $bt) {
		$s = BackTraceProcessor::ProcessBackTrace($bt);
		$data->backTrace = $s;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $obj
	 * @param type $codeLine
	 * @return type
	 */
	protected static function ExtractVariableName($obj, $codeLine) {

		$firstBracketPos = strpos($codeLine, '(');
		$lastBracketPos = strrpos($codeLine, ')');
		return substr($codeLine, $firstBracketPos + 1, ( $lastBracketPos - $firstBracketPos - 1));
	}

	/** -----------------------------------------------------------------------------------------------
	 * method to figure things out from the SOURCE php code files
	 *     - figures out the file name of the source
	 * 	  - figures out the line number in the source fileName
	 *     - then extracts the line of code that called dump - from the SOURCE php codeLine
	 *     - then parses the line of code and tries to figure out the "NAMME of the variable that was passed
	 *     - then saves everything for later output
	 *
	 * @param $data - holding place for all the unbeautified data
	 * @param $obj  - the variable passed to dump
	 * @param $bt - back trace array ready to be processed

	 * @return - nothing
	 */
	protected static function SetVariableName($data, $obj, $bt) {
		list($fn, $lineNum) = BackTraceProcessor::ExtractCallingLine($bt);
		$data->fileName = $fn;
		$data->lineNum = $lineNum;
		$data->serverName = empty($_SERVER['SERVER_NAME']) ? 'aunknoen' : $_SERVER['SERVER_NAME'];
		$codeLine = BackTraceProcessor::ExtractCodeLine($fn, $lineNum);
		$data->codeLine = $codeLine;
		$varName = self::ExtractVariableName($obj, $codeLine);
		$data->variableName = $varName;


		$data->preCodeLines = BackTraceProcessor::ExtractPreLines($fn, $lineNum, self::$settings->get( 'PRE_CodeLines') );
		$data->postCodeLines = BackTraceProcessor::ExtractPostLines($fn, $lineNum, self::$settings->get( 'POST_CodeLines'));

		//echo 'PRELINES<pre>'; print_r( $data->preCodeLines);echo '</pre>';
		//echo 'POSTLINES<pre>'; print_r( $data->postCodeLines);echo '</pre>';
	}

	/** -----------------------------------------------------------------------------------------------
	 * saves the title for later processing
	 *
	 * @param $data - holding place for all the unbeautified data
	 * @param $title - simple string for title above the variale

	 * @return - nothing
	 */
	protected static function SetTitle($data, $title) {
		$data->title = $title;
	}

	/** -----------------------------------------------------------------------------------------------
	 * saves the object value  for later processing
	 *
	 * @param $data - holding place for all the unbeautified data
	 * @param $obj  - the variable passed to dump

	 * @return - nothing
	 */
	protected static function SetVariableValue($data, $obj) {
		if (is_string($obj)) {
			$obj = htmlspecialchars($obj, ENT_NOQUOTES);
			$obj = \wordwrap($obj, 80, "\n");
			$data->variable = print_r($obj, true);
		} elseif (is_bool($obj)) {
			$data->variable = $obj ? "-=True=-" : "-=False=-";
		} elseif (is_null($obj)) {
			$data->variable = '-=Null=-';
		} else {
			$data->variable = print_r($obj, true);
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $data
	 * @param type $showBT
	 * @return string
	 */
	protected static function plainOutput($data, $showBT) {
		$output = '';
		$output .= $data->title;
		$output .= '<BR>';
		$output .= $data->variableName;
		$output .= '<BR>';
		$output .= $data->variable;
		$output .= '<BR>';
		$output .= $data->fileName . ' (Line:' . $data->lineNum . ') '; // . $data->codeLine;
		$output .= '<BR>';
		if ($showBT >0 ) {
			$output .= $data->backTrace;
			$output .= '<BR>';
		}
		return $output;
	}

	/** -----------------------------------------------------------------------------------------------
	 * puts some html style codes aournd and in the output
	 *
	 * @param $data - holding place for all the unbeautified data
	 * @param $showBT  - flag to include the back trace or not

	 * @return - string with html formated output
	 */
	protected static function BeautifyOutput($data ){ //, $showBT, $bgColor = '#FFFDCC', $skipNumLines = false) {
		$output = '';
		$output .= self::BeautifyAreaStart($data, self::$settings->get( 'Beautify_BackgroundColor'), self::$settings->get( 'skipNumLines') );
		$output .= self::BeautifyTitle($data);
		$output .= self::BeautifyVariableName($data);
		$output .= self::BeautifyVariableData($data);
		$output .= self::BeautifyLineData($data);
		if (self::$settings->get( 'Show BackTrace Num Lines') > 0) {
			$output .= self::BeautifyBackTrace($data);
		}
		$output .= self::BeautifyAreaEnd();
		return $output;
	}

	/** -----------------------------------------------------------------------------------------------
	 * puts some html style codes at the beginning of the output - i.e. yellow background and scollbars if needed
	 *
	 * @param $data - holding place for all the unbeautified data

	 * @return - string with html formated output
	 */
	protected static function BeautifyAreaStart($data) {  //, /*$bgColor = '#FFFDCC',*/ $skipNumLines = false) {
		$numLinesInVariable = substr_count($data->variable, "\n");

		if ($numLinesInVariable > self::$settings->get( 'FLAT_WINDOWS_LINES')) {
			$s = "\n\n" . '<pre style="background-color: ' . self::$settings->get( 'Back') . '; border-style: dashed; border-width: 1px; border-color: #950095;'
					. ' overflow: auto;'
					. ' padding-bottom: 0px;'
					. ' margin-bottom: 0px;'
					. ' width: 100%;';
			if (!$skipNumLines) {
				$s .= ' height: ' . self::$settings->get( 'FLAT_WINDOWS_LINES') . 'em;';
			}
			$s .= '"' . ">\n";
			return $s;
		} else {
			//self::$settings->dump( 'Beautify_BackgroundColor');
			//self::$settings->dump( (self::$settings->get( 'Beautify_BackgroundColor')));
			return "\n" . '<pre style="background-color: ' . (self::$settings->get( 'Beautify_BackgroundColor')) . '; border-style: dashed; border-width: 1px; border-color: #950095;">' . "\n";
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * puts some html style codes at then end fo the dump block
	 *
	 *
	 * @return - string with html formated output to end the dump block
	 */
	protected static function BeautifyAreaEnd() {
		return '</pre>';
	}

	/** -----------------------------------------------------------------------------------------------
	 * puts some html style codes around the variable name
	 *
	 * @param $data - holding place for all the unbeautified data
	 *
	 * @return - string with html formated output
	 */
	protected static function BeautifyVariableName($data) {
		$s = '<font style="font-size: large; background-color: #7DEEA2; color: #950095"; font-weight: 999;>';
		$s .= $data->variableName;
		$s .= '</font>' . "\n";
		return $s;
	}

	/** -----------------------------------------------------------------------------------------------
	 * puts some html style code around the title
	 *
	 * @param $data - holding place for all the unbeautified data
	 *
	 * @return - string with html formated output
	 */
	protected static function BeautifyTitle($data) {
		if (empty($data->title)) {
			return '';
		} else {
			$s = '<B><font color=green>';
			$s .= $data->title;
			$s .= '</B></font>' . "\n";
			return $s;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * puts some html style code around the value
	 *
	 * @param $data - holding place for all the unbeautified data
	 *
	 * @return - string with html formated output
	 */
	protected static function BeautifyVariableData($data) {
		$s = '<font style="fine-size: large; background-color: #7DEEA2; color: #950095">';
		$s .= " ==>";
		$s .= "</font>";
		$s .= $data->variable;
		$s .= '</font>' . "\n";
		return $s;
	}

	/** -----------------------------------------------------------------------------------------------
	 * puts some html style code around the file and and line number
	 *
	 * @param $data - holding place for all the unbeautified data
	 *
	 * @return - string with html formated output
	 */
	protected static function BeautifyLineData($data) {
		$s = '<div align=right>';

		$s .= self::BeautifyPrePostLineData($data->preCodeLines);

		$s .= '<font style="font-size: small; font-style: italic; color:#FF8000"> ';
		$s .= ' server=' . $data->serverName;
		$s .= ' ';
		$s .= dirname($data->fileName) . DIRECTORY_SEPARATOR;
		$s .= '</font>';
		$s .= '<font style="font-style: italic; font-weight: bold; color:#FF8000">';
		$s .= basename($data->fileName);
		$s .= ' (line: ' . $data->lineNum . ')';
		$s .= '</font><BR>';

		$s .= self::BeautifyPrePostLineData($data->postCodeLines);

		$s .= '</div>'; // . "\n";
		return $s;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $ar
	 * @return string
	 */
	protected static function BeautifyPrePostLineData($ar) {
		$s = '<font style="font-size: small; font-style: italic; color:#417232"> ';
		foreach ($ar as $line => $aLine) {
			$s .= $aLine; //. ' (line:' . $line . ')' ;
		}
		$s .= '</font>';
		return $s;
	}

	/** -----------------------------------------------------------------------------------------------
	 * puts some html style code around the back trace
	 *
	 * @param $data - holding place for all the unbeautified data
	 *
	 * @return - string with html formated output
	 */
	protected static function BeautifyBackTrace($data) {
		$s = '<font color=#0000FF>';
		$s .= $data->backTrace;

		/*
		self::dump( $data->backTrace);
		foreach ($data->backTrace as $key => $value) {
			$s .= $value['file'];
			$s .= $value['line'];
			$s .= $value['function'];
			$s .= $value['class'];

		}
*/
		$s .= '</font>' . "\n";
		return $s;
	}

	/** -----------------------------------------------------------------------------------------------
	 * take an array and when you do a print_r it takes a lot of lines to show it all
	 * this function will attempt to shrink the contents of the subarrays down to a one line string
	 *     (if the key is in the $compress_elements array -- if not in that array then it keeps it and does nothing to it)
	 *    using implode (if simple strings)or using serialize if the sub array is more complicated
	 * - you can also just ignore the sub array if the key exists in the $eliminate_elements array
	 *
	 * @param type $ar
	 * @param array $compressElements
	 * @param type $eliminateElements
	 * @return type
	 */
	public static function arrayDisplayCompactor($ar, $compressElements = array(), $eliminateElements = array()) {
		if (is_null($compressElements)) {
			$compressElements = array();
		}
		if (is_null($eliminateElements)) {
			$elimnateElements = array();
		}
		if (!is_array($compressElements) or ! is_array($eliminateElements)) {
			return print_r($the_array, true);
		}

		$tempOutPut = array();
		foreach ($ar as $key => &$item) {
			if (!in_array($key, $eliminateElements)) {
				$tempOutPut[$key] = self::ShrinkOrNotItem($key, $item, $compressElements);
			}
		}
		// return the array as the printable expansion using print_r
		//return print_r( $tempOutPut, true);
		return $tempOutPut;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $key
	 * @param type $item
	 * @param type $compressElements
	 * @return type
	 */
	protected static function ShrinkOrNotItem($key, $item, $compressElements) {
		if (in_array($key, $compressElements) and is_array($item)) {
			$out = self::shrinkItem($item);
		} else {
			$out = $item;
		}
		return $out;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $item
	 * @return type
	 */
	protected static function shrinkItem($item) {
		// make sure the sub elements are simple strings with integer keys -- otherwise use serialize to show keys and such
		$useImplode = self::determinIfImplodeWillWork($item);
		if ($useImplode) {
			$out = implode(', ', $item);
		} else {
			$out = serialize($item);
		}
		return $out;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $item
	 * @return boolean
	 */
	protected static function determinIfImplodeWillWork($item) {
		$all_str = true;
		foreach ($item as $subKey => $subItem) {
			if (!is_string($subItem) or ! is_int($subKey)) {
				$all_str = false;
				break;
			}
		}
		return $all_str;
	}

}

//***********************************************************************************************************
//***********************************************************************************************************
//***********************************************************************************************************
//***********************************************************************************************************
//***********************************************************************************************************
//***********************************************************************************************************
//***********************************************************************************************************
//***********************************************************************************************************
//***********************************************************************************************************
//***********************************************************************************************************
/**
 *
 */
abstract class BackTraceProcessor {

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $bt
	 * @return type
	 */
	public static function ProcessBackTrace($bt) {
		$output = '';
		foreach ($bt as $btFunc) {
			$output .= self::ProcessBTFunc($btFunc);
		}
		return $output;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $bt
	 * @return type
	 */
	public static function ExtractCallingLine($bt) {
		$fn = $bt[0]['file'];
		$lineNum = $bt[0]['line'];
		return array($fn, $lineNum);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $fn
	 * @param type $lineNum
	 * @return type
	 */
	public static function ExtractCodeLine($fn, int $lineNum) {
		$lines = file($fn);
		return $lines[$lineNum - 1];   //zero based lines
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $fn
	 * @param type $lineNum
	 * @param type $preLines
	 * @return type
	 */
	public static function ExtractPreLines($fn, int $lineNum, int $preLines) {
		$lines = file($fn);
		$r = array();
		for ($i = ($lineNum - $preLines - 1); $i < ($lineNum - 1); $i++) {
			if (!empty($lines[$i])) {
				$r[$i] = $lines[$i];
			}
		}
		return $r;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $fn
	 * @param type $lineNum
	 * @param type $postLines
	 * @return type
	 */
	public static function ExtractPostLines($fn, int $lineNum, int $postLines) {
		//echo 'X=', 		$lineNum + $postLines+1,' - ' ,$postLines , '<br>';
		return SELF::ExtractPreLines($fn, $lineNum + $postLines + 1, $postLines);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $btFunc
	 * @return string
	 */
	protected static function ProcessBTFunc($btFunc) {
		// $btFunc['function'] != 'dump') {   // dont dump the dump
		$output = '';
		if (!empty($btFunc['file'])) {
			$output .= $btFunc['file'];
			$output .= ':' . $btFunc['line'];
			$output .= '(' . $btFunc['function'] . ')';
		}
		$args = array();

		if ( false) {   /** simplify the backtrace by ignoring the "Arguments" and/or variables */
			foreach ($btFunc['args'] as $anArg) {

				$args[] = self::ProcessBTArgs($anArg);
			}
			$output .= implode(', ', $args);
		}

		$output .= "\n";
		return $output;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $anArg
	 * @return string
	 */
	protected static function ProcessBTArgs($anArg): string {
		switch (gettype($anArg)) {
			case "boolean":
				$output = ($anArg) ? '-True-' : '-False-';
				break;
			case "integer":
			case "double":
				$output = (string) $anArg;
				break;
			case "string":
				$output = $anArg;
				break;
			case "array":
			case "object":
			case "resource":
			case "resource (closed)":
				$output = print_r($anArg, true);
				//$output = serialize( $anArg);
				break;
			case "NULL":
				$output = '-Null-';
			case "unknown type":
			default:
				$output = '-Unknown-';
				break;
		}
		return $output;
	}

}




//***********************************************************************************************************
//***********************************************************************************************************
//***********************************************************************************************************
//***********************************************************************************************************
//***********************************************************************************************************
//***********************************************************************************************************
//***********************************************************************************************************
//***********************************************************************************************************
//***********************************************************************************************************
//***********************************************************************************************************

class dumpSettings {
	private $settings = array();


	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';

	/** -----------------------------------------------------------------------------------------------
	 *
	 */
	function __construct(){
		$settings = array();
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
	 * @param string $name
	 * @param type $value
	 * @param bool $force
	 * @return bool
	 */

	public function add( string $name, $val ) :bool{
		if ($this->isSet($name)) {
			return false;
		} else {
			$this->settings[$name] = $val;
			return $this->settings[$name] == $val;
		}
		return false;
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $name
	 * @param type $val
	 * @return bool
	 */
	public function update( string $name, $val): bool {
		$this->settings[$name] = $val;
		return $this->settings[$name] == $val;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param array $array
	 * @param bool $force
	 */
	public function addArray( array $array, bool $force=false) {
		foreach ($array as $key => $value) {
			$this->add ( $key, $value, $force );
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $name
	 * @return bool
	 */
	public function remove( string $name) :bool {
		if ( $this->isSet($name)) {
			unset($this->settings[$name]);
			return ( ! $this->isSet($name));
		}
		return false;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return bool
	 */
	public function removeAll( ): bool{
		$this->settings = array();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $name
	 * @return bool
	 */
	public function isSet(string  $name ) : bool{
		return array_key_exists($name, $this->settings );
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function get(string $name){
		if ( isSet($name)) {
			return $this->settings[$name];
		} else {
			return false;
		}
	}


	public function dump($var=null) {
		echo '<pre style="background-color: #E3FCFD">' ;
		if( empty( $var) ) {
			print_r ( $this->settings);
		} else {
				print_r ( $var);
		}
		echo '</pre>';
	}



}