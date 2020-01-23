<?php

//**********************************************************************************************
//* UserRoleandPermissionsView.class.php
/**
 * sets up any output users roles and permissions
 *
 * @author  mike.merrett@whitehorse.ca
 * @license City of Whitehorse
 *
 * Description.
 * this shows any info from the userRolesAndPermissions - typically not much (some debug output)
 *
 *
 * @link URL
 *
 * @package ModelViewController - UserRoleAndPermissions View
 * @subpackage UserRoleAndPermissions
 * @since 0.3.0
 *
 * @example
 *
 * @see UserRoleAndPermissionsModel.class.php
 * @see UserInfoData.class.php
 * @see UserAttributedata.class.php
 * @see UserPermissionData.class.php
 * @see UserRoleData.class.php
 * @see UserRoleAndPermissionsController.class.php
 *
 * @todo Description
 *
 *
 *
 * https://www.php-fig.org/psr/
 *
 *
 */
//**********************************************************************************************

namespace php_base\View;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;
use \php_base\Utils\HTML\HTML as HTML;
use \php_base\Utils\SubSystemMessage as SubSystemMessage;



/** * **********************************************************************************************
 * does any output from the user roles and permissions controller
 */
class UserRoleAndPermissionsView extends View {

	public $controller;

	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';


	/** -----------------------------------------------------------------------------------------------
	 * constructor - the parent has the data
	 * @param type $controller
	 */
	public function __construct($controller) {

		Settings::getRunTimeObject('PERMISSION_DEBUGGING')->addInfo('constructor for UserRoleAndPermissionsView');

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
	 *
	 * @param type $data
	 * @return Response
	 */
	public function doWork($data = null): Response {

	}

	/** -----------------------------------------------------------------------------------------------
	 * dump the state  - which is basically the parent class and its properties
	 *
	 * @param type $arRoleNames
	 * @param type $arOfRoleIds
	 * @param type $forceShow
	 * @return void
	 */
	public function dumpState($arRoleNames = null, $arOfRoleIds = null, $forceShow = false): void {
		if (!$forceShow) {
			return;
		}
		echo HTML::HR();
		echo '<pre class="UserStateDump" >';
		$bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[0];
		echo '--'  . __METHOD__ .  '-- called from ' . $bt['file'] . '(line: '. $bt['line'] . ')' ;
		echo '<BR>';

//		print_r($arRoleNames);
//		print_r($arOfRoleIds);

		echo 'THIS=';
		print_r($this->controller);
		echo HTML::BR();


		echo HTML::HR();
		echo '</pre>';
	}

	/** -----------------------------------------------------------------------------------------------
	 * sort the permissions array
	 */
	protected function sortPermissionsArray( ){
		if (!empty($this->controller->userPermissions)){
			usort($this->controller->userPermissions,
				function ($a, $b) {
				$sA = $this->controller->ArrayOfRoleNames[$a['ROLEID']] . $a['PROCESS'] . $a['TASK'] . $a['ACTION'] . $a['FIELD'] . $a['PERMISSION'];
				$sB = $this->controller->ArrayOfRoleNames[$b['ROLEID']] . $b['PROCESS'] . $b['TASK'] . $b['ACTION'] . $b['FIELD'] . $b['PERMISSION'];
				return $sA <=> $sB;
			});
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * show a easily readable table with the permissions
	 */
	public function dumpPermissions($doEcho= false) {
		$s = '';
		//echo '--- ' . get_class() . '-dumpPermissions- -------------';
		$bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[0];
		$s .= '--'  . __METHOD__ .  '-- called from ' . $bt['file'] . '(line: '. $bt['line'] . ')' ;
		$s .= '<BR>';

		$bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

		$this->sortPermissionsArray();
		$s .= '<Table border=1>';
		$s .= '<Tr>';
		$s .= '<th>ROLEID</Th>';
		$s .= '<th>PROCESS</Th>';
		$s .= '<th>TASK</Th>';
		$s .= '<th>ACTION</Th>';
		$s .= '<th>FIELD</Th>';
		$s .= '<th>PERMISSION</Th>';
		$s .= '</tr>';
		if (!empty($this->controller->userPermissions )){
			foreach ($this->controller->userPermissions as $perm) { /**/
				$s .= '<tr>';
				$s .= '<td>'. $perm['ROLEID'] . ': ' . $this->controller->ArrayOfRoleNames[$perm['ROLEID']] . '</TD>';
				$s .= '<td>'. $perm['PROCESS'] . '</TD>';
				$s .= '<td>'. $perm['TASK'] . '</TD>';
				$s .= '<td>'. $perm['ACTION'] . '</TD>';
				$s .= '<td>'. $perm['FIELD'] . '</TD>';
				$s .= '<td>'. $perm['PERMISSION'] . '</TD>';
				$s .= '</tr>';
			}
		}
		$s .= '</table>';
		$s .= '<font size="-1">';
		$s .= HTML::Space(60);
		$s .= basename($bt[0]['file']);
		$s .= ': ';
		$s .= $bt[0]['line'];
		$s .= '</font>';
		$s .= '<BR>' . PHP_EOL;

		if ($doEcho) {
			echo $s;
		}
		return $s;

	}

}
