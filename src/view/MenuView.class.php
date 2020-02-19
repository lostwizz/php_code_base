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

	protected $currentMenuLevel = 1;


	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';


	function __construct( $controller ){
		Settings::getRunTimeObject('MENU_DEBUGGING')->addInfo('constructor for MenuView');

	}

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

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $theMenu
	 */
	public function showMenu ($theMenu) {
		Settings::GetRunTimeObject('MENU_DEBUGGING')->addNotice_6( 'at show Menu');

		echo $theMenu;

		echo HTML::BR(10);

		//$this->controller->model->processedMenu;

	}



//
//
//	/** -----------------------------------------------------------------------------------------------
//	 *
//	 * @param array $item
//	 * @return string
//	 */
//	protected function process(array $item) /* : string */ {
//		dump::dump($item);
//
//		$s = '';
//		$x = explode('.', $item['ITEM_NUMBER']);
//		if (count($x) > $this->currentMenuLevel) {
//			$s .= '<li class="parent">';
//			$s .= '<a href="./index.php?' . Resolver::MENU_TERM . '=' . $item['ptap'] . '">' . $this->currentMenuLevel . $item['ITEM_NUMBER']  . '==' . $this->menuTitle($item) . '</a>';
//			$s .= PHP_EOL;
//			$s .= '<ul class="child">';
//			$s .= PHP_EOL;
//			$this->currentMenuLevel ++;
//		} elseif (count($x) == $this->currentMenuLevel) {
//			$s .= '<li>'; //  class="parent"
//			//$s .= '<a href="./index.php?' . Resolver::MENU_TERM . '=' . $item['ptap'] . '">' . $this->currentMenuLevel . $item['ITEM_NUMBER']  . '==' . $item['NAME'] . '</a>';
//			$s .= '<a href="./index.php?' . Resolver::MENU_TERM . '=' . $item['ptap'] . '">' . $this->currentMenuLevel . $item['ITEM_NUMBER']  . '==' . $this->menuTitle($item). '</a>';
//			$s .= '</li>';
//			$s .= PHP_EOL;
//		} else if (count($x) < $this->currentMenuLevel) {
//			$s .= '</ul>';
//			$s .= PHP_EOL;
//			$s .= '</li>';
//			$s .= PHP_EOL;
//			$s .= '<li class="parent">';
//			//$s .= '<a href="./index.php?' . Resolver::MENU_TERM . '=' . $item['ptap'] . '">' . $this->currentMenuLevel . $item['ITEM_NUMBER'] . '==' . $item['NAME'] . '</a>';
//			$s .= '<a href="./index.php?' . Resolver::MENU_TERM . '=' . $item['ptap'] . '">' . $this->currentMenuLevel . $item['ITEM_NUMBER'] . '==' . $this->menuTitle($item) . '</a>';
//			$s .= '</li>';
//			$this->currentMenuLevel --;
//		}
//		return $s;
//	}
//
//
//	protected function menuTitle($item) : string {
//		$s = $item['NAME'] ;
//	$s .= '###';
//		if ( !empty( $item['SETTINGS_FLAG'])) {
//			$s .= HTML::Space(2);
//			$s .= Settings::GetPublic($item['SETTINGS_FLAG']) ? '<on>' : '<off>';
//		}
//		return $s;
//	}








	public 	function fred() {

?>
		<ul id="menu">

			<li class="parent"> ><a href="#bb">22Barbies</a></li>

			<li class="parent"><a href="#">Popular Toys</a>
				<ul class="child">
					<li class="parent"><a href="#">Video Games <span class="expand">&raquo;</span></a>
						<ul class="child">
							<li><a href="#">Car</a></li>
							<li class="parent"><a href="#">Bike Race<span class="expand">&raquo;</span></a>
								<ul class="child">
									<li><a href="#">one</a></li>
									<li><a href="#">two</a></li>
									<li><a href="#">three</a></li>
									<li><a href="#">four</a></li>
								</ul>
							</li>
							<li><a href="#">Fishing</a></li>
						</ul>
					</li>
					<li><a href="#">Barbies</a></li>
					<li><a href="#">Teddy Bear</a></li>
					<li><a href="#">Golf Set</a></li>
				</ul>
			</li>
			<li class="parent"><a href="#">Recent Toys</a>
				<ul class="child">
					<li><a href="#">Yoyo</a></li>
					<li><a href="#">Doctor Kit</a></li>
					<li class="parent"><a href="#">Fun Puzzle<span class="expand">&raquo;</span></a>
						<ul class="child">
							<li><a href="#" nowrap>Cards</a></li>
							<li><a href="#" nowrap>Numbers</a></li>
						</ul>
					</li>
					<li><a href="#">Uno Cards</a></li>
				</ul>
			</li>

		</ul>

<?php
			/*
			 * 	<li class="parent"><a href="#">Toys Category</a>
				<ul class="child">
					<li><a href="#">Battery Toys</a></li>
					<li class="parent"><a href="#">Remote Toys <span class="expand">&raquo;</span></a>
						<ul class="child">
						<li><a href="#">Cars</a></li>
						<li><a href="#">Aeroplane</a></li>
						<li><a href="#">Helicopter</a></li>
						</ul>
					</li>
					<li><a href="#">Soft Toys</a>
					</li>
					<li><a href="#">Magnet Toys</a></li>
				</ul>
				</li>
			 */
	}




}



