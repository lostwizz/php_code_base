<?php

/** * ********************************************************************************************
 * dump.class.php
 *
 * Summary maintains 3 queues (Pre/Dispatcher/Post) and executes thing in the queues.  These classes should not depend on anything else
 *
 *
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.4.1
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
 * Dump::dump('hello world', 'the world');
 *
 * Dump::dump('!!!!!!!!!!!!! at TestController', 'now at', array('Show BackTrace Num Lines' => 5,'Beautify_BackgroundColor' => '#FFAA55') );
 *
 * dump::Dump('array("1"=>4)',null,array('Show BackTrace Num Lines' => 5,'Beautify_BackgroundColor' => '#FFAA55') );
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

	/**
	 * the different presets (based on how dump is called i.e.  - dumpLong, dump3PrePost etc.
	 */
	const NORMAL=1;
	const LONG =2;
	const PRE3POST3=3;
	const LONGPRE3POST3=4;
	const CLASSES_LOADED = 5;
	const MULTI_ARRAY =6;
	const SHORT = 7;
	const FUNCTION_LIST=8;

	/**
	 * this holds the current configuration on what the output looks like and behaves
	 * @var type dumpConfig
	 */
	public static $config =null;

	/**
	 * @var version number
	 */
	private const VERSION = '0.4.1';

	/** ----------------------------------------------------------------------------------------------
	 *  initializes the config (basically on each run of a dump
	 * @param type $array   - custom config settings that can be passed in the dump call
	 * @param type $whichPreset   - some presets
	 */
	public static function initConfig( $array = null, $whichPreset= 1){

		// if the config is not instantiated then do so
		if (empty(self::$config )) {
			self::$config = new dumpConfig();
		}
		self::setDefaultSettings( self::decodeAuxSettings($whichPreset) );

		// now add the passed config settings
		if (!empty( $array)){
			self::$config->updateFromArray($array);
		}
	}

	/** ----------------------------------------------------------------------------------------------
	 *
	 * @param int $whichPreset
	 * @return int
	 */
	protected static function decodeAuxSettings( int $whichPreset) {
			switch ($whichPreset){
			case self::LONG:  // long
				$auxArray = array(
					'FLAT_WINDOWS_LINES' => -1,
					'Beautify_BackgroundColor'=> '#F0FFD5',
					);
				break;
			case self::PRE3POST3:    //3pre, 3post
				$auxArray = array(
					'PRE_CodeLines' => 3,
					'POST_CodeLines'=> 3);
				break;
			case self::LONGPRE3POST3: // long and 3pre, 3post
				$auxArray = array(
					'PRE_CodeLines' => 3,
					'POST_CodeLines'=> 3,
					'FLAT_WINDOWS_LINES' => 50);
				break;
			case self::CLASSES_LOADED:
				$auxArray = array(
					'Beautify_BackgroundColor'=> '#E3FCFD',
					'FLAT_WINDOWS_LINES' => -1,
					'PRE_CodeLines' => 0,
					'POST_CodeLines' => 0,
					'Show BackTrace Num Lines' => 0,
					);
				break;
			case self::FUNCTION_LIST:
				$auxArray = array(
					'Beautify_BackgroundColor'=> '#E3C8EA',
					'FLAT_WINDOWS_LINES' => -1,
					'PRE_CodeLines' => 0,
					'POST_CodeLines' => 0,
					'Show BackTrace Num Lines' => 0,
					);
				break;

			case self::SHORT:
				$auxArray = array(
					'FLAT_WINDOWS_LINES' => 12);
				break;
			case self::MULTI_ARRAY:
			default:
			case self::NORMAL:  // default
				$auxArray = array(
				);
				break;
		}
		return $auxArray;
	}


	/** ----------------------------------------------------------------------------------------------
	 * setup the default config and if passed the presets
	 * @param array $configPreset
	 */
	public static function setDefaultSettings(?array $configPreset) {
		$defaults = array(
			'FLAT_WINDOWS_LINES' => 7, //  big a output block can be before adding scrollbars
			'PRE_CodeLines' => 0, // show the number of lines before the call
			'POST_CodeLines' => 0, // show the number of lines after the call
			'Show BackTrace Num Lines' => 0, // show the backtrace calls (how many lines in the history
			'Only Return Output String' => false, // dont print/echo anything just return a string with it all
			'skipNumLines' => false,
			'Area Border Color'=> '#950095',
			'Beautify is On' => true, // make the output look pretty
			'Beautify_BackgroundColor' => '#FFFDCC', //'#E3FCFD',		// set the background color
			'Beautify Text Color' =>  '#0000FF',
			'Beautify PreWidth' => '95%',
			'Beautify Padding-bottom' => '1px',
			'Beautify Margin-bottom' => '1px',
			'Beautify Overflow' => 'auto',
			'Beautify Border-style' => 'dashed',
			'Beautify Border-width' => '1px',
			'Beautify Var Name Font-size' => 'large',
			'Beautify Var Name BackgroundColor' => '#7DEEA2',
			'Beautify Var Text Color' => '#950095',
			'Beautify Var Font-weight' => '100',
			'Beautify Title Color' => 'green',
			'Beautify Title Font-weight' => '100',
			'Beautify Var Data Font-size' => 'large',
			'Beautify Var Data Font background Color' => '',//#ADFF2F', //#7DEEA2',
			'Beautify Var Data Text Color' => '#950095',
			'Beautify Var Data Font-weight' => 'normal',
			'Beautify Line Data Font-size' => 'small',
			'Beautify Line Data Font-style' => 'normal',  //italic
			'Beautify Line Data Text Color' => '#FF8000',
			'Beautify Line Data Basename Font-size' => 'medium',
			'Beautify Line Data Basename Font-style' => 'bold',
			'Beautify Line Data Basename Text Color' => '#8266F2', //#FF8000',
			'Beautify Line Data Basename Font-weight' => 'bolder',
			'Beautify PrePost Line Font-size' => 'small',
			'Beautify PrePost Line Font-style' => 'italic',
			'Beautify PrePost Line Text Color' => '#417232',
			'Beautify PrePost Line BackgroundColor' => 'LightGray',
			'Beautify PrePost Line Margin' => '25px',
			'Beautify PrePost Line Text-align' => 'left',
		);
		self::$config->updateFromArray($defaults);
		self::$config->updateFromArray($configPreset);
	}

	/** -----------------------------------------------------------------------------------------------
	 * gives a version number
	 * @static
	 * @return type
	 */
	public static function Version() {
		return self::VERSION;
	}

// <editor-fold defaultstate="collapsed" desc="DumpClasses - fairly static ">

	/** -----------------------------------------------------------------------------------------------
	 * method to dump an array of declared classes and to optionally search for a string in the list
	 * @param string $search
	 * @return nothing
	 */
	public static function dumpClasses($search = null, ?array $options=null) {
		$bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
		$data = new DumpData();

		self::initConfig($options, self::CLASSES_LOADED);

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
		$b = self::BeautifyAreaStart($data, true);
		$t = self::BeautifyTitle($data);
		return $b . $t ;
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
			return nl2br( print_r(get_declared_classes(), true));
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
	 *
	 * @param type $allFunctions
	 * @param array $options
	 */
	public static function DumpFunctions($allFunctions = false, ?array $options=null) {
		self::initConfig($options, Dump::FUNCTION_LIST);

		$conf =	array('Show BackTrace Num Lines' => self::$config->get( 'Show BackTrace Num Lines'),
					'Beautify_BackgroundColor' => self::$config->get( 'Beautify_BackgroundColor'),
					'FLAT_WINDOWS_LINES' => self::$config->get( 'FLAT_WINDOWS_LINES'),
					'PRE_CodeLines' => self::$config->get( 'PRE_CodeLines'),
					'POST_CodeLines' => self::$config->get( 'POST_CodeLines')
					);


		$ar = get_defined_functions();
		if ($allFunctions) {
			self::dump($ar, 'Function List', $conf);
		} else {
			self::dump( $ar['user'], 'Function List (user only)', $conf);
		}
	}

	// </editor-fold>

	/** -----------------------------------------------------------------------------------------------
	 * method to dump a variable and output it so it is pretty and easy to find and where it came from
	 * @param $obj - some object - any type should work
	 * @param $title - some optional text that will show above the variable namespace
	 *
	 * @return either true or a string that can be printed for pretty (dependant on param $onlyReturnValue
	 *
	 * used to be dump($obj, $title, int $showBT = 0, $onlyReturnValue = false, $noBeautify = false){}
	 *
	 */
	public static function dump($obj, ?string $title = '', ?array $options=null){
		//self::initConfig($options, self::NORMAL);
		self::initConfig($options, self::LONG);
		$bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, self::$config->get( 'Show BackTrace Num Lines')  );  // get it early

		self::initConfig($options, self::LONG);

		return SELF::dumpHelper($bt, $obj, $title);
	}

	/** -----------------------------------------------------------------------------------------------
	 * method to expand the number of lines that show in the dump and then reset it back to the default
	 * @param type $bt - a back trace (calling function captured it
	 * @param $obj - some object - any type should work
	 * @param $title - some optional text that will show above the variable namespace
	 *
	 * @return either true or a string that can be printed for pretty (dependand on param $onlyReturnValue
	 *
	 * used to have $s = self::BeautifyOutput($data, $showBT, $bgColor, $skipNumLines);
	 *              $s = self::plainOutput($data, $showBT);
	 *		dumpHelper($bt, $obj, $title, $showBT, $onlyReturnValue, $noBeautify, $bgColor = '#FFFDCC', $skipNumLines = false) {
	 */
	protected static function dumpHelper($bt, $obj, $title ) {  //, ?array $options=null ){
		$data = new DumpData($obj, $title);
		self::SetBackTrace($data, $bt);

		self::SetVariableName($data, $obj, $bt);
		self::SetTitle($data, $title);
		self::SetVariableValue($data, $obj);

		if ( self::$config->get( 'Beautify is On')) {
			$s = self::BeautifyOutput($data );
		} else {
			$s = self::plainOutput($data);
		}

		if (self::$config->get( 'Only Return Output String')) {
			return $s;
		} else {
			echo $s;
			return true;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * method to expand the number of lines that show in the dump and then reset it back to the default
	 * @param $obj - some object - any type should work
	 * @param string|null $title - some optional text that will show above the variable namespace
	 * @param array $options
	 *
	 * @return either true or a string that can be printed for pretty (dependand on param $onlyReturnValue
	 *
	 */
	public static function dumpLong($obj, ?string $title = '', ?array $options=null) {
		self::initConfig($options, self::NORMAL);
		$bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, self::$config->get( 'Show BackTrace Num Lines'));  // get it early

		self::initConfig($options, self::LONG);

		$r = SELF::dumpHelper($bt, $obj, $title);

		return $r;
	}



	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $obj
	 * @param string|null $title
	 * @param array $options
	 * @return type
	 */
	public static function dumpshort($obj, ?string $title = '', ?array $options=null) {
		self::initConfig($options, self::SHORT);
		$bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, self::$config->get( 'Show BackTrace Num Lines'));  // get it early

		self::initConfig($options, self::SHORT);

		$r = SELF::dumpHelper($bt, $obj, $title);

		return $r;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $obj1
	 * @return type
	 */
	public static function dumpA( ...$obj){
		self::initConfig(null, self::MULTI_ARRAY);
		$bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, self::$config->get( 'Show BackTrace Num Lines'));  // get it early

		self::initConfig(null, self::MULTI_ARRAY);

		$r = SELF::dumpHelper($bt, $obj, '');

		return $r;

	}


	/** -----------------------------------------------------------------------------------------------
	 * method to expand the number of lines that show in the dump and then reset it back to the default
	 * @param $obj - some object - any type should work
	 * @param $title - some optional text that will show above the variable namespace
	 * @param array $options
	 *
	 * @return either true or a string that can be printed for pretty (dependand on param $onlyReturnValue
	 *
	 */
	public static function dump3PrePost($obj, ?string $title = '', ?array $options=null) {
		self::initConfig($options, self::NORMAL);
		$bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, self::$config->get( 'Show BackTrace Num Lines'));  // get it early

		self::initConfig($options, self::PRE3POST3);

		$r = SELF::dumpHelper($bt, $obj, $title);//, $showBT, $onlyReturnValue, $noBeautify);

		return $r;
	}

	/** -----------------------------------------------------------------------------------------------
	 * @param type $obj
	 * @param string|null $title
	 * @param array $options
	 * @return type
	 */
	public static function dumpLong3PrePost($obj, ?string $title = '', ?array $options=null) {
		self::initConfig($options, self::NORMAL);
		$bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, self::$config->get( 'Show BackTrace Num Lines'));  // get it early

		self::initConfig($options, self::LONGPRE3POST3 );

		$r = SELF::dumpHelper($bt, $obj, $title, $showBT, $onlyReturnValue, $noBeautify);

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
	 *
	 * @todo - if the dump call has an options array filter it out
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

		$data->preCodeLines = BackTraceProcessor::ExtractPreLines($fn, $lineNum, self::$config->get( 'PRE_CodeLines') );
		$data->postCodeLines = BackTraceProcessor::ExtractPostLines($fn, $lineNum, self::$config->get( 'POST_CodeLines'));
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
	protected static function plainOutput($data) {
		$output = '';
		$output .= $data->title;
		$output .= '<BR>';
		$output .= $data->variableName;
		$output .= '<BR>';
		$output .= $data->variable;
		$output .= '<BR>';
		$output .= $data->fileName . ' (Line:' . $data->lineNum . ') ';
		$output .= '<BR>';
		if (self::$config->get( 'Show BackTrace Num Lines') >0 ) {
			$output .= $data->backTrace;
			$output .= '<BR>';
		}
		return $output;
	}

	/** -----------------------------------------------------------------------------------------------
	 * puts some html style codes around and in the output
	 *
	 * @param $data - holding place for all the unbeautified data
	 * @param $showBT  - flag to include the back trace or not

	 * @return - string with html formatted output
	 */
	protected static function BeautifyOutput($data ){
		$output = '';
		$output ='<BR>';
		$output .= self::BeautifyAreaStart($data);
		$output .= self::BeautifyTitle($data);
		$output .= self::BeautifyVariableName($data);
		$output .= self::BeautifyVariableData($data);
		$output .= self::BeautifyLineData($data);
		if (self::$config->get( 'Show BackTrace Num Lines') > 0) {
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
	protected static function BeautifyAreaStart($data) {
		$numLinesInVariable = substr_count($data->variable, "\n");

		if ($numLinesInVariable > self::$config->get( 'FLAT_WINDOWS_LINES')) {
			$s = PHP_EOL . PHP_EOL
					. '<div id="dumpAreaStart_a"'
					. ' style="background-color: '	. self::$config->get( 'Beautify_BackgroundColor')	. ';'
					. ' border-style: '				. self::$config->get( 'Beautify Border-style')		. ';'
					. ' border-width: '				. self::$config->get( 'Beautify Border-width')		. ';'
					. ' border-color: '				. self::$config->get( 'Area Border Color')			. ';'
					. ' overflow: '					. self::$config->get( 'Beautify Overflow')			. ';'
					. ' padding-bottom: '			. self::$config->get( 'Beautify Margin-bottom' )	. ';'
					. ' margin-bottom: '			. self::$config->get( 'Beautify Padding-bottom' )	. ';'
					. ' width: '					. self::$config->get( 'Beautify PreWidth' )			. ';'
					;  // note style ends below
			if (!self::$config->get( 'skipNumLines')) {
				$s .= ' height: ' . self::$config->get( 'FLAT_WINDOWS_LINES') . 'em;';
			}
			$s .= '"' . ">";
			return $s;
		} else {
			return PHP_EOL
				. '<div id="dumpAreaStart_b"'
				. ' style="background-color: ' . (self::$config->get( 'Beautify_BackgroundColor')) . ';'
				. ' border-style: ' . self::$config->get( 'Beautify Border-style') . ';'
				. ' border-width: ' . self::$config->get( 'Beautify Border-width') . ';'
				. ' border-color: ' . self::$config->get( 'Area Border Color') . ';'
				. '">';
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * puts some html style codes at then end fo the dump block
	 *
	 *
	 * @return - string with html formatted output to end the dump block
	 */
	protected static function BeautifyAreaEnd() {
		return PHP_EOL . '</div>' . PHP_EOL. PHP_EOL;
	}

	/** -----------------------------------------------------------------------------------------------
	 * puts some html style codes around the variable name
	 *
	 * @param $data - holding place for all the unbeautified data
	 *
	 * @return - string with html formatted output
	 */
	protected static function BeautifyVariableName($data) {
		$s = '<span id="varName"'
				. ' style="font-size: ' . self::$config->get( 'Beautify Var Name Font-size')		. ';'
				. ' background-color: ' . self::$config->get( 'Beautify Var Name BackgroundColor' ) . ';'
				. ' color: '			. self::$config->get('Beautify Var Text Color' )			. ';'
				. ' font-weight: '		. self::$config->get('Beautify Var Font-weight' )			. ';">'
				;
		$s .= $data->variableName;
		$s .= '</span>';
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
			$s ='';
			$s .= '<span id="title"'
					. ' style="font-weight: '	. self::$config->get('Beautify Title Font-weight')	. ';'
					. ' color: '				. self::$config->get('Beautify Title Color')		. ';">';
			$s .= $data->title;
			$s .= '</span>';
			$s .= '<br>';
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
		$s = '<pre id="varData"'
				. ' style="font-size: ' . self::$config->get('Beautify Var Data Font-size')				. ';'
				. ' background-color: ' . self::$config->get('Beautify Var Data Font background Color') . ';'
				. ' color: '			. self::$config->get('Beautify Var Data Text Color')			. ';'
				. ' font-weight: '		. self::$config->get('Beautify Var Data Font-weight')			. ';'
				. '">';
		$s .= $data->variable;
		$s .= '</pre>';
		$s .= PHP_EOL;
		return $s;
	}

	/** -----------------------------------------------------------------------------------------------
	 * puts some html style code around the file and and line number
	 *
	 * @param $data - holding place for all the unbeautified data
	 *
	 * @return - string with html formatted output
	 */
	protected static function BeautifyLineData($data) {
		$s ='';
		$s = '<div style="text-align: right;">';

		$s .= self::BeautifyPrePostLineData($data->preCodeLines);

		$s .= '<span id="LineData_A"'
				. ' style="font-size: ' . self::$config->get('Beautify Line Data Font-size')	. ';'
				. ' font-style: '		. self::$config->get('Beautify Line Data Font-style')	. ';'
				. ' color:'				. self::$config->get('Beautify Line Data Text Color')	. ';'
				. ' text-align: right;'
				. '">';
		$s .= ' server=' . $data->serverName;
		$s .= ' ';
		$s .= dirname($data->fileName) . DIRECTORY_SEPARATOR;
		$s .= '</span>';
		$s .= PHP_EOL;
		$s .= '<span id="LineData_B"'
				. ' style="font-size: ' . self::$config->get('Beautify Line Data Basename Font-size')		. ';'
				. ' font-style: '		. self::$config->get('Beautify Line Data Basename Font-style')		. ';'
				. ' color:'				. self::$config->get('Beautify Line Data Basename Text Color')		. ';'
				. ' font-weight:'		. self::$config->get(  'Beautify Line Data Basename Font-weight')	. ';'
				. ' text-align: right;'
				. '">';
		$s .= basename($data->fileName);
		$s .= ' (line: ' . $data->lineNum . ')';
		$s .= '</span>';
		$s .= '</div>';
		$s .= self::BeautifyPrePostLineData($data->postCodeLines);
		return $s;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $ar
	 * @return string
	 */
	protected static function BeautifyPrePostLineData($ar) {
		if ( !empty($ar) and ( is_array($ar) ) or ( is_string($ar))){
			$s = '<pre id="PrePostLineData"'
					. ' style="font-size:'	. self::$config->get( 'Beautify PrePost Line Font-size')		. ';'
					. ' font-style: '		. self::$config->get( 'Beautify PrePost Line Font-style')		. ';'
					. ' background-color: ' . self::$config->get( 'Beautify PrePost Line BackgroundColor')	. ';'
					. ' margin: '			. self::$config->get( 'Beautify PrePost Line Margin')			. ';'
					. ' color:'				. self::$config->get( 'Beautify PrePost Line Text Color')		. ';'
					. ' text-align: '		. self::$config->get( 'Beautify PrePost Line Text-align')		. ';'
					. '"> ';
			foreach ($ar as $line => $aLine) {
				$s .= htmlspecialchars( $aLine, ENT_HTML5);
			}
			$s .= '</pre>';
			$s .= PHP_EOL;
			return $s;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * puts some html style code around the back trace
	 *
	 * @param $data - holding place for all the unbeautified data
	 *
	 * @return - string with html formatted output
	 */
	protected static function BeautifyBackTrace($data) {
		if ( !empty($data)){
			$s = '<pre id="BackTrace"'
					. ' style="color: ' .  self::$config->get( 'Beautify Text Color') . '">';
			$s .= $data->backTrace;
			$s .= '</pre>';
			return $s;
		}
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

		//if ( false) {   /** simplify the backtrace by ignoring the "Arguments" and/or variables */
		//	foreach ($btFunc['args'] as $anArg) {
		//		$args[] = self::ProcessBTArgs($anArg);
		//	}
		//	$output .= implode(', ', $args);
		//}

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

class dumpConfig {
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
			$this->add( $key, $value, $force );
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param array $array
	 */
	public function updateFromArray( array $array) {
		foreach ($array as $key => $value) {
			$this->update ( $key, $value);
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
		if ( !empty($name) and isSet($name)) {
			return $this->settings[$name];
		} else {
			return false;
		}
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $var
	 */
	public function dumpV($var=null, string $title = '') {
		echo '<pre style="background-color: #E3FCFD">' ;
		echo $title , ': ';

		print_r ( $var);

		echo '</pre>';
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $var
	 * @param string $title
	 */
	public function dump($var=null, string $title = '') {
		echo '<pre style="background-color: #E3FCFD">' ;
		echo $title , ': ';
		print_r ( $this->settings);

		echo '</pre>';
	}


}