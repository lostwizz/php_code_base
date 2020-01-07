<?php



namespace php_base\Model;

use \php_base\Control\MenuController as MenuController;


use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;

use \php_base\Resolver as Resolver;


class MenuModel extends Model{

	public $controller;

	//public $processedMenu;

	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';

	/** -----------------------------------------------------------------------------------------------
		 * basic constructor - we track where the controller is because it has the links to the data and view classes
		 * @param type $controller
		 */
	public function __construct($controller) {
		$this->controller = $controller;
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
	 *  basic default method called by the dispatcher
	 * @return Response
	 */
	public function doWork() : Response {
		return Response::GenericError();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return array
	 */
	public function prepareMenu():string{

		$menu = $this->get_menu_tree(0);
		$menu = str_replace('<ul></ul>', '', $menu);

		$menu = PHP_EOL . PHP_EOL . '<ul class="main-navigation">' . PHP_EOL .  $menu . PHP_EOL . '</ul>' . PHP_EOL . PHP_EOL;

		return $menu;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * from:
	 *    https://www.w3school.info/2015/12/22/steps-to-create-dynamic-multilevel-menu-using-php-and-mysql/
	 *
	 * maybe someday get this to show properly  . '<span class="expand">&raquo;</span>'
	 *
	 * @param int $parent_id
	 * @return string
	 */
	protected  function get_menu_tree( int $parent_id) :string {
		$menu = "";

		foreach($this->controller->data->Menu as $row ) {
			if( $row['PARENT_ITEM_NUMBER'] == $parent_id){
				if ( $this->hasRightsToMenuItem($row)){
					$menu .= '<li>' . $this->buildLink($row) . PHP_EOL;
					$menu .= "<ul>" . $this->get_menu_tree($row['ITEM_NUMBER'])  . "</ul>"  . PHP_EOL; //call  recursively
					$menu .= "</li>"  . PHP_EOL;
				}
			}
		}

		return $menu;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $item
	 * @return bool
	 */
	protected function hasRightsToMenuItem($item): bool {

		if ( empty ($item['ROLE_NAME_REQUIRED'])
			and empty( $item['PERMISSION_REQUIRED'])
			and empty( $item['PROCESS_PERMISSION_REQUIRED'])
			and empty( $item['TASK_PERMISSION_REQUIRED']) ){
			return true;
		}

//		dump::dump(Settings::GetRunTimeObject('userPermissionsController')->hasRole($item['ROLE_NAME_REQUIRED']), $item['ROLE_NAME_REQUIRED'] );

		if (!empty($item['ROLE_NAME_REQUIRED'])) {
			if (Settings::GetRunTimeObject('userPermissionsController')->hasRole($item['ROLE_NAME_REQUIRED'])) {
				return true;
			}
		} else {
			if (Settings::GetRunTime('userPermissionsController')->isAllowed(  $item['PERMISSION_REQUIRED'],
									$item['PROCESS_PERMISSION_REQUIRED'],
									$item['TASK_PERMISSION_REQUIRED'],
									$item['ACTION_PERMISSION_REQUIRED'],
									$item['FIELD_PERMMISSION_REQUIRED']
					) ){
				return true;
			}
		}
		return false;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $row
	 * @return string
	 */
	protected function buildLink($row) {
		$ptap = $row['PROCESS'] . '.' . $row['TASK'] . '.' . $row['ACTION'] . '.' . $row['PAYLOAD'];

		$s = '<a href="./index.php?' . Resolver::MENU_TERM . '=' . $ptap . '">' . $row['NAME'] . '</a>';

		return $s;
	}



}
