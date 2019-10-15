<?php


// set some usefull usings
use \php_base\Settings\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;

if ( !defined('DS')){
	//define ('DS', DIRECTORY_SEPARATOR);
	//define ('DIR', 'P:' . DS . 'Projects' . DS . 'MikesCommandAndControl2' . DS . 'src' . DS );

	define ('DS', DIRECTORY_SEPARATOR);
	define('DIR', realpath('.') . DS );
	echo 'I WAS HERE!';
}


//echo '<pre>'; print_r($_SERVER); echo '</pre>';


echo '<pre>'; print_r(dirname(__DIR__)); echo '</pre>';

//dump(DIR . 'utils' . DS . 'setup' . DS . 'setup.php' );
echo 'SAMMY= ', DIR . 'utils' . DS . 'setup' . DS . 'setup.php';

include_once( DIR . 'utils' . DS . 'setup' . DS . 'setup.php');

include_once(DIR . 'view' . DS . 'view.class.php');
include_once(DIR . 'control' . DS . 'controller.class.php');
include_once(DIR . 'model' . DS . 'model.class.php');


echo '-------------';
$o = Settings::GetRuntime('Log_file');
echo '<pre>ll=', $o, '</pre>';

$model = new Model();
$controller = new Controller($model);
$view = new View($controller, $model);

if (isset($_GET['action']) && !empty($_GET['action'])) {
    $controller->{$_GET['action']}();
}

echo $view->output();
echo '-------------';



Settings::GetRunTime('QUEUE')->enqueue ('GHI');
$x = Settings::GetRunTime('QUEUE')->dequeue();
Dump::dump($x);

Dump::dump(Settings::GetRunTime('QUEUE'));
Dump::dump(Settings::GetRunTime('QUEUE')->key(), '', null,null,true);
Settings::GetRunTime('QUEUE')->add( 1, 'TUSV');

Settings::GetRunTime('QUEUE')->enqueue ('JKL');
Dump::dump3PrePost(Settings::GetRunTime('QUEUE'));