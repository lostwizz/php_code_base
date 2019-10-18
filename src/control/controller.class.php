<?php
// Test file for GitWCRev
     /*
 "$WCREV$";
 "$WCREV=7$";
 "$WCMODS?Modified:Not modified$";
 "$WCUNVER?Unversioned items found:no unversioned items$";
 "$WCDATE$";
 "$WCDATEUTC$";
 "$WCDATE=%a, %d %B %Y$";
 "$WCDATE=$";
 "$WCDATE=%a, %c %B %Y$";
 "$WCDATEUTC=%a, %d %B %Y$";
 "$WCNOW$";
 "$WCNOWUTC$";
 "$WCISTAGGED?Tagged:Not tagged$";
 "$WCINGIT?versioned:not versioned$";
 "$WCFILEMODS?Modified:Not modified$";
 "$WCSUBMODULE?Working tree has at least one submodule:Working tree has no submodules$";
 "$WCSUBMODULEUP2DATE?All submodules are up2date (checked out HEAD):At least one submodule is not up2date (checked HEAD differs)$";
 "$WCMODSINSUBMODULE?At least one submodule has uncommitted items:No submodule has uncommitted items$";
 "$WCUNVERINSUBMODULE?At least one submodule has unversioned files:No submodule with unversioned files$";
 "$WCMODSFULL?Modified items found (recursively):No modified items found (also not in submodules)$";
 "$WCUNVERFULL?Unversioned items found (recursively):No unversioned items found (also not in submodules)$";

  $WCMODSFULL?1:0$
*/

namespace php_base\Control;

use php_base\Model;
use php_base\Data;
use php_base\View;

use \php_base\Settings\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;


//***********************************************************************************************
//***********************************************************************************************
abstract class Controller{
 	public $model;
	public $view;
	public $data;

	public $payload;


	//-----------------------------------------------------------------------------------------------
	abstract public function __construct($payload = null);


	//-----------------------------------------------------------------------------------------------
	abstract public static function controllerRequiredVars();

	//-----------------------------------------------------------------------------------------------
	public function doWork(){
		return false;
	}


}