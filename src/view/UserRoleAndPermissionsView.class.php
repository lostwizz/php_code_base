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



/** * **********************************************************************************************
 * does any output from the user roles and permissions controller
 */
class UserRoleAndPermissionsView extends View {

	public $parent = null;

	/** -----------------------------------------------------------------------------------------------
	 * constructor - the parent has the data
	 * @param type $parentObj
	 */
	public function __construct($parentObj) {
		$this->parent = $parentObj;
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

//		print_r($arRoleNames);
//		print_r($arOfRoleIds);

		echo 'THIS=';
		print_r($this->parent);
		echo HTML::BR();


		echo HTML::HR();
		echo '</pre>';
	}

	/** -----------------------------------------------------------------------------------------------
	 * sort the permissions array
	 */
	protected function sortPermissionsArray( ){
		usort($this->parent->userPermissions,
			function ($a, $b) {
			$sA = $this->parent->ArrayOfRoleNames[$a['ROLEID']] . $a['PROCESS'] . $a['TASK'] . $a['ACTION'] . $a['FIELD'] . $a['PERMISSION'];
			$sB = $this->parent->ArrayOfRoleNames[$b['ROLEID']] . $b['PROCESS'] . $b['TASK'] . $b['ACTION'] . $b['FIELD'] . $b['PERMISSION'];
			return $sA <=> $sB;
		});
	}

	/** -----------------------------------------------------------------------------------------------
	 * show a easily readable tabel with the permissions
	 */
	public function dumpPermissions() {
		$bt = debug_backtrace();

		$this->sortPermissionsArray();
		echo '<table border=1>';
		ECHO '<Tr>';
		echo '<th>', 'ROLEID', '</Th>';
		echo '<th>', 'PROCESS', '</Th>';
		echo '<th>', 'TASK', '</Th>';
		echo '<th>', 'ACTION', '</Th>';
		echo '<th>', 'FIELD', '</Th>';
		echo '<th>', 'PERMISSION', '</Th>';
		echo '</tr>';
		foreach ($this->parent->userPermissions as $perm) { /**/
			echo '<tr>';
			echo '<td>', $perm['ROLEID'], ': ', $this->parent->ArrayOfRoleNames[$perm['ROLEID']], '</TD>';
			echo '<td>', $perm['PROCESS'], '</TD>';
			echo '<td>', $perm['TASK'], '</TD>';
			echo '<td>', $perm['ACTION'], '</TD>';
			echo '<td>', $perm['FIELD'], '</TD>';
			echo '<td>', $perm['PERMISSION'], '</TD>';
			echo '</tr>';
		}
		echo '</table>';
		echo '<font size="-1">';
		echo HTML::Space(60);
		echo basename($bt[0]['file']);
		echo ': ';
		echo $bt[0]['line'];
		echo '</font>';
		echo '<BR>' . PHP_EOL;

	}

}
