<h1>Hello</h1>
<?php


//***********************************************************************************
// setup the Directory Root
define ('DS', DIRECTORY_SEPARATOR);
               //define ('DIR', 'p:' . DS . 'Projects' . DS . 'MikesCommandAndControl2' . DS . 'src' . DS );
if ( strripos (realpath('.'), 'src' ) <1 ){
	define('DIR', realpath('..') . DS . 'src' . DS);
} else {
	define('DIR', realpath('.') . DS );
}



include_once( DIR . 'autoload.php');

// set some usefull usings
use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\MessageLog as MessageLog;
use \php_base\Authenticate as Authenticate;
use \php_base\Resolver as Resolver;

use \php_base\utils\myCryption\myCryption as myCryption;
use \php_base\Utils\HTML\HTML as HTML;
use \php_base\Utils\myUtils\myUtils as myUtils;

//include_once(DIR . 'utils' . DS. 'myCryption.class.php');
//Dump::dumpClasses();
//include_once (DIR . 'utils' . DS . 'messagelog.class.php');
//










////////!!!!!!!!!!!!!this line #45 must alwaygs be this for the unit tests to work
//Dump::dump(__LINE__, '-This is a Title-',true);
////////!!!!!!!!!!!!!this line #45 must alwaygs be this for the unit tests to work


//************************************************************************************
//************************************************************************************
// setup everything
//************************************************************************************
include_once( DIR . 'autoload.php');
date_default_timezone_set('Canada/Yukon');
//include_once( DIR . 'utils' . DS . 'dump.class.php');
include_once( DIR . 'utils' . DS . 'settings.class.php');

require_once( DIR . '_config' . DS . '_Settings-General.php');
require_once( DIR . '_config' . DS . '_Settings-Database.php');
require_once( DIR . '_config' . DS . '_Settings-protected.php');

require_once( 'P:\Projects\_Private_Settings.php');

include_once( DIR . 'utils' . DS . 'ErrorHandler.php');           // has to be after the settings are initialized

Settings::SetPublic('Log_file', DIR . 'logs' . DS . Settings::GetPublic('App Name') . '_app.log' );
include_once( DIR . 'utils' . DS . 'Setup_Logging.php');

Settings::SetPublic( 'TEST that All is well', 'YES');


// check that setup.php worked properly
if ( Settings::GetPublic( 'TEST that All is well') != 'YES') {
	throw new exception('it seems that setup (or settings.class.php did not run properly');
}
if (Settings::GetRuntime('FileLog') ==null) {
	throw new exception('it seems that setup (or settings.class.php did not run properly');
}



session_name('SESSID_' . str_replace( ' ','_',Settings::GetPublic('App Name') ));
session_start();


$mLog = new MessageLog();
Settings::SetRunTime('MessageLog', $mLog);


Settings::SetRunTime('Benchmarks.start.executionTime',  microtime(true));

if ( Settings::GetPublic('IS_DEBUGGING')) {
	echo '<br>--Starting... :-) ...<br>';
	Dump::dumpLong( $_REQUEST);
	if ( !empty($_SESSION) ){
		Dump::dump( $_SESSION); //dumpLong
	}
	if ( !empty( $GLOBALS)) {
//		Dump::dump($GLOBALS); //dumpLong
	}
	if ( ! empty($_COOKIES)){
		Dump::dump( $_COOKIES);
	}
}

Settings::GetRunTime('MessageLog')->addNotice( 'Starting ....');

Settings::GetRuntime('FileLog')->addInfo('Staring...');

if ( extension_loaded('pdo_sqlsrvXXX')) {
	Settings::GetRuntime('DBLog')->addInfo( 'Starting....');
	Settings::GetRuntime('SecurityLog')->addInfo('Starting...');
} else {
	echo '<font color=white style="background-color:red;font-size:160%;" >', 'PDO_SQLSRV is not available!!- exiting ','</font>';
	throw new exception('PDO_SQLSRV is not available!!', 256);
}





// this is for testing the crash email
if (false) {
	Settings::GetRuntime('EmailLog')->addCritical( ' it blew up!', $_SERVER);
	Settings::GetRuntime('EmailLog')->addCritical('Hey, a critical log entry!', array( 'bt' => debug_backtrace(true), 'server' =>$_SERVER));
}


Settings::GetRunTime('MessageLog')->addNotice( 'Starting ..session..');


//Dump::dump( $_SESSION); //dumpLong

//
//$a = new Authenticate();
//echo 'does have right 1', $a->checkRights( 'fred', 'sam', 'john', Authenticate::DBA_RIGHT) ? 'yes':'no';
//echo 'does have right 2', $a->checkRights( 'fred', 'sam', 'john', Authenticate::READ_RIGHT)? 'yes':'no';


//$_SESSION['username'] = 'merrem';

// now start everything running
//include_once( DIR . 'resolver.class.php');
$resolver = new Resolver();
$resolver->doWork();


//  if(defined("CRYPT_BLOWFISH") && CRYPT_BLOWFISH) {
//    echo "CRYPT_BLOWFISH is enabled!";
//  } else {
//    echo "CRYPT_BLOWFISH is NOT enabled!";
//  }
//Dump::dumpClasses();

//$cc = new myCryption();
//$en = $cc->basicEnCrypt("This is some random string" );
//Dump::dump($en);
//$un = $cc->basicDeCrypt( $en);
//Dump::dump($un);
//
//$en = $cc->encrypt("This is some random string" );
//Dump::dump($en);
//$un = $cc->decrypt( $en);
//Dump::dump($un);

//Settings::saveAsINI("F:\TEMP\__TEMP.INI");

//Settings::nonDestructiveINIRestore( "F:\TEMP\__TEMP.INI");

Settings::destructiveINIRestore( "F:\TEMP\__TEMP.INI");

$s =Settings::dump(true, false);
Dump::dumpLong($s);
//
//
$ar = array( 'fred'=> array('fred1', 'fred2','fred3', 'fred4'), 'bob'=>array('a','b','c'), 'tony'=>array('a','b','c'),'sam'=>'sam1');
$s = Dump::arrayDisplayCompactor( $ar, array('fred','sam', 'tony'));
Dump::dump($s);


$s = Dump::arrayDisplayCompactor( $ar, array('fred','sam','tony'), array('bob'));
Dump::dump($s);


$s = HTML::Space(3);
//'theaction'
$s = HTML::FormOpen('fredform',  'invoice_form' );
//$s = htmlspecialchars($s);
Dump::dump($s);


$a = array('one'=>'oneX', 'two'=>'twoX', 'three'=>'threeX', 'four'=>'fourX');
$s = HTML::Select( 'sam', $a, 'three', true);
Dump::dump( $s);
echo $s;

echo '-------------1-';
echo HTML::BR(3);

$s = HTML::Select( 'sam', $a, 'three', false);
Dump::dump( $s);
echo $s;
echo '-------------2-';


$a = array('one'=>'oneX', 'two'=>'twoX', 'three'=>'threeX', 'four'=>'fourX');
$s = HTML::Select( 'sam', $a, null, true);
Dump::dump( $s);
echo $s;



$s = myUtils::ShowMoney(1.24);
Dump::dump($s);
echo '[' .  $s  .']';


$style =  array('width' => '15em', 'height' =>'15em');
// style="width: 2em; height: 2em;"

$s = HTML::Radio( 'fred', 'fred was here' , true,null, $style);
Dump::dump($s);
echo $s , 'some radioness';

echo HTML::Radio("FRED", 'somewhere over the rainbow');

$out = HTML::Image( 'http://gis/cityofwhitehorseresources/Images/fire_icon.png');
Dump::dump($out);


//$key = random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
//echo $key;
//$nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
//$ciphertext = sodium_crypto_secretbox('This is a secret!', $nonce, $key);
//


//$sodium = new myCryption();
//
//$key = $sodium->sodium_init();
//Dump::dump(  $sodium->safe_b64encode( $key));
//
//$s = 'This is some message to be encrypted and then decrypted for testng';
//$ex = $sodium->sodium_encrypt( $s);
//
//$macauth = $sodium->sodium_GetMessageAuthenticationCode();
//
//
//
//Dump::dump(SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
////Dump::dump($key);
////echo SODIUM_CRYPTO_SECRETBOX_KEYBYTES, '-' ,$key, '<Br>';
//echo $s, '<Br>';
////echo $ex, '<Br>';
//$ex64 = $sodium->safe_b64encode( $ex);
//echo $ex64, '<Br>';
//
//$x =  $sodium->safe_b64decode( $ex64);
//$orig = $sodium->sodium_decrypt( $x);
//echo $orig , '<Br>';
//
//
//$auth= $sodium->sodium_authenticate( $macauth, $s, $key);
//
//dump::dump($auth ?'t':'F');
//$s .= 'FRED WAS HERE';
//$auth= $sodium->sodium_authenticate( $macauth, $s, $key);
//dump::dump($auth ?'t':'F');
//
//
//Dump::dump(bin2hex($macauth));
//
//$newS = random_bytes(SODIUM_CRYPTO_AUTH_BYTES);
//Dump::dump(bin2hex($newS));
//


$style = array('one'=>'oneX', 'two'=>'twoX', 'three'=>'threeX', 'four'=>'fourX');

$out = HTML::Image( 'http://gis/cityofwhitehorseresources/Images/fire_icon.png',null, $style);

	$expect = '<img src="http://gis/cityofwhitehorseresources/Images/fire_icon.png" />';
Dump::dump($out);





//$en = Settings::mySimpleCrypt( "This is some random string" ,'e');
//Dump::dump( $en);
//
//$un = Settings::mySimpleCrypt( $en ,'d');
//Dump::dump($un);


////
////$queue = new SplQueue();
////Settings::SetRunTime('QUEUE', $queue);
////
////if ( ! Settings::GetRunTime('QUEUE')->isEmpty() ) {
////	$y =Settings::GetRunTime('QUEUE')->dequeue();
////	Dump::dump( $y);
////}
////Settings::GetRunTime('QUEUE')->enqueue('freddy');

//Dump::dump( Settings::GetProtected( 'Test_X'));
//Dump::dump( Settings::GetProtected( 'Test_X')['dsn']);
//sam();



//$x = 10/0;
//Dump::dump( 'hi');
//$o = Settings::GetPublic('DBLog');

//Dump::dump( $o);
//Dump::dump( $o == null ? 'isnull':'isnotnull');


//Dump::dump(Settings::GetRuntime('Log_file'));
//Dump::dump( $_FILES);
//Dump::dump($_ENV);

//Dump::dump(E_ALL);


///Settings::GetPublic('EmailLog')->addCritical('bad things happend', array(Settings::GetPublic( 'CRITICAL_EMAIL_PAYLOAD') => $_SERVER));

////echo '<pre>';
////		$i = Dump::GetIsInitialized();
////		if ($i) {
////			print_r( Dump::GetD('numLinesPre'));
////		} else {
////			print_r ( Dump::GetD('numLinesPre'));
////		}
////		Dump::Initialize();
////
////		//$i = Dump::GetIsInitialized();
////		print_r( Dump::GetIsInitialized());
////
////		$x =Dump::GetD('numLinesPre');
////		print_r($x);
////echo '</pre>';

//whitehorse\MikesCommandAndControl2\Settings\Logger\
//$log->Info( 'it worked from index.php');


//echo 'Critical_email_Subject=';
//echo Settings::GetProtected('Critical_email_Subject');
//echo '<br>';

//Settings::GetPublic('EmailLog')->addError('Starting');

//echo 'tried to email <br>';
//include_once( 'Email.class.php');


//Settings::GetPublic('EmailLog')->addCritical('This is a test');


//Dump::dump(__NAMESPACE__ );

//Dump::dumpLong(Settings::dump());




////
////$x = Settings::GetRunTime('QUEUE')->dequeue();
////Dump::dump($x);
////Dump::dump(Settings::GetRunTime('QUEUE'));
////
////
//////y1
//////y2
////
////Dump::dumpLong3PrePost(Settings::dump(true, true));
//y5

//y6

//Dump::dump( $_SESSION); //dumpLong
Settings::GetRunTime('MessageLog')->addNotice( '... Closing Session..');

session_write_close();
//Dump::dump( $_SESSION); //dumpLong

if ( Settings::GetPublic('IS_DEBUGGING')) {
	echo '<br>--...The End.--<Br>';
}

Settings::GetRunTime('MessageLog')->addERROR('something happend here !');

$exec_time = microtime(true) - Settings::GetRunTime('Benchmarks.start.executionTime');
Settings::GetRunTime('MessageLog')->addINFO('Execution Time was: '. $exec_time);


Settings::GetRunTime('MessageLog')->showAllMessagesInBox();  // !! a!lways do this last so you get all the outstanding messages!!!!



//function sam() {
//	tony();
//}
//
//function tony(){
//	Dump::dump('Tony', 'Tony has left the building', true);
//
//	Dump::dump( $_SERVER, 'Server');
//}



