<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace php_base\View;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;
use \php_base\Utils\HTML\HTML as HTML;

/**
 * Description of MenuView
 *
 * @author merrem
 */
class MenuView extends View{

	protected $currentLevel = 0;


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

//	public function doWork( $parent = null) : Response {
//		return Response::NoError();
//	}

	public function showMenu (array $theMenu) {
		Settings::GetRunTimeObject('MENU_DEBUGGING')->addNotice( 'at show Menu');

		dump::dumpLong($theMenu);

		$s ='<ul>';
		foreach($theMenu as $item){
			$s .= $this->process($item);
		}
		$s .= '/ul>';

		echo $s;
	}

	protected function process( array $item ) : string{
		dump::dumpLong($item);
		$s ='';
		$x = \explode('.', $item);
		if ( count($x) > $this->currentLevel){
			$s .= '<li class="dropdown">';
			$s .= '<div class="dropdown-content">';
			$s .= 'a href="./index.php?MENU_SELECT=' . $item['ptap'] .'">' . $item['NAME'] . '</a>';
			$this->currentLevel ++;
		} elseif (count($x) == $this->currentLevel) {
			$s .= 'a href="./index.php?MENU_SELECT=' . $item['ptap'] .'">' . $item['NAME'] . '</a>';
		} else if (count($x) < $this->currentLevel) {
			$s .= '</div>';
			$s .= '</li>';
			$this->currentLevel --;
		}
		return $s;
	}



}

/*

<ul>
	<li><a href="./index.php?MENU_SELECT=home...">Home</a></li>
	<li><a href="./index.php?MENU_SELECT=news...">News</a></li>
	<li class="dropdown">
		<a href="javascript:void(0)" class="dropbtn">Dropdown</a>
		<div class="dropdown-content">
			<a href="./index.php?MENU_SELECT=link.one..">Link 1</a>
			<a href="./index.php?MENU_SELECT=link.two..">Link 2</a>
			<a href="./index.php?MENU_SELECT=link.three..">Link 3</a>
		</div>
	</li>
	<?php
		if ( Settings::GetRunTime('userPermissionsController')->hasRole('DBA') ){
?>	<li class="dropdown">
		<a href="javascript:void(0)" class="dropbtn">DBA</a>
		<div class="dropdown-content">
			<a href="./index.php?MENU_SELECT=dba.one..">DBA1</a>
			<a href="./index.php?MENU_SELECT=dba.two..">DBA2</a>
			<a href="./index.php?MENU_SELECT=dba.three..">DBA3</a>
		</div>
	</li>
<?php
		}
	?>
	<li class="dropdown">
		<a href="javascript:void(0)" class="dropbtn">Help</a>
		<div class="dropdown-content">
			<a href="./index.php?MENU_SELECT=help.one..">About</a>
			<a href="./index.php?MENU_SELECT=help.two..">Version</a>
			<a href="./index.php?MENU_SELECT=help.three..">Help</a>
		</div>
	</li>
</ul>

<h3>Dropdown Menu inside a Navigation Bar</h3>
<p>Hover over the "Dropdown" link to see the dropdown menu.</p>

<?php


	}

}


*/
