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
 * @see UserData.class.php
 * @see UserAttributesdata.class.php
 * @see UserPermissionData.class.php
 * @see UserRolesData.class.php
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
	 *
	 * @param type $userList
	 * @param type $attributeList
	 * @param type $rolesList
	 * @param type $rolePermissionsList
	 * @return void
	 */
	public function showAllUsers( $userList, $attributeList, $rolesList, $rolePermissionsList ) :void {
		echo '<pre>';
		foreach($userList as $aUser){
			$this->showAUser($aUser, $attributeList, $rolesList, $rolePermissionsList);
		}
		echo '</pre>', PHP_EOL;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $aUser
	 * @param type $attributeList
	 * @param type $rolesList
	 * @param type $rolePermissionsList
	 */
	protected  function showAUser( $aUser, $attributeList, $rolesList, $rolePermissionsList ){
		//echo '---------------------------------------------', '<Br>';
		echo '<hr>';
		echo 'User Name=<B>', $aUser['USERNAME'], "</B>\t", 'UserId=', $aUser['USERID'],  "\t", 'Application=', $aUser['APP'], '<Br>';
		echo "\t\t", 'Logon Method=', $aUser['METHOD'], 'Last IP=', $aUser['IP'], '<Br>';
		echo "\t\t", 'Last Logon Time=', $aUser['LAST_LOGON_TIME'], '<Br>';
		echo "\t\t", 'Primary Role Name=', $aUser['PRIMARYROLENAME'], '<Br>';

		$listOfUserRoles = $this->showAUserAttributes( $aUser['USERID'], $attributeList);
		if ( ! in_array( $aUser['PRIMARYROLENAME'], $listOfUserRoles)) {
			$listOfUserRoles[] = $aUser['PRIMARYROLENAME'];
		}

		$this->showAUsersRolesAndPermissions($listOfUserRoles, $rolesList, $rolePermissionsList );
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $userId
	 * @param type $listOfAttribs
	 * @return array|null
	 */
	protected function showAUserAttributes( $userId, $listOfAttribs) : ?array{
		$ar = array();
		foreach( $listOfAttribs as $attrib) {
			if ( $attrib['USERID'] == $userId){
				echo "\t\t\t";
				echo  $attrib['ATTRIBUTENAME'];
				echo ' = ';
				echo $attrib['ATTRIBUTEVALUE'];
				echo '<br>';
				if (($attrib['ATTRIBUTENAME'] == 'SecondaryRole')  or ($attrib['ATTRIBUTENAME'] =='PrimaryRole' )) {
					$ar[] = $attrib['ATTRIBUTEVALUE'];
				}
			}
		}
		return $ar;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $listOfUserRoles
	 * @param type $rolesList
	 * @param type $rolePermissionsList
	 * @return void
	 */
	protected function showAUsersRolesAndPermissions($listOfUserRoles, $rolesList, $rolePermissionsList ) : void {
		foreach( $listOfUserRoles as $userRole) {
			echo "\t";
			echo $userRole;
			foreach( $rolesList as $aRole) {
				if ( $userRole == $aRole['NAME'] ) {
					$roleID= $aRole['ROLEID'];
				}
			}
			echo '<BR>';
			if ( !empty( $roleID)){
				echo '<table border=1 style="margin-left:85;width: 700px">';
				foreach($rolePermissionsList as $rolePerm ) {
					if ( $rolePerm['ROLEID'] == $roleID) {

						echo '<tr><td>';
						echo $rolePerm['PROCESS'];
						echo '</td><td>';
						echo $rolePerm['TASK'];
						echo '</td><td>';
						echo $rolePerm['ACTION'];
						echo '</td><td>';
						echo $rolePerm['FIELD'];
						echo '</td><td>';
						echo $rolePerm['PERMISSION'];
						echo '</td></tr>';
					}
				}
			}
			echo '<BR>';
		}
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
		if (!empty($this->controller->rolePermissions)){
			usort($this->controller->rolePermissions,
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
		if (!empty($this->controller->rolePermissions )){
			foreach ($this->controller->rolePermissions as $perm) { /**/
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
