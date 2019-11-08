<?php

//UserRoleandPermissionsView

namespace php_base\View;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;
use \php_base\Utils\HTML\HTML as HTML;



/** * **********************************************************************************************
 *
 */
class UserRoleAndPermissionsView extends View {

	public $parent = null;

	/** -----------------------------------------------------------------------------------------------
	 *
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
	 * show a easily readable tabel with the permissions
	 */
	public function dumpPermissions() {
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
	}

}
