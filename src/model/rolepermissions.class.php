<?php


namespace php_base\model;


//***********************************************************************************************
//***********************************************************************************************
public class RolePermissions {
	protected $PermissionsList= array();


	public function __construct( $roleid ){
		if ( empty($roleid)) {
			throw new InvalidArgumentException('need roleID before we can get a permission');
		}

		readPermissionsFromTable($roleid);
	}

	protected function readPermissionsFromTable( $roleid){
		if ( Settings::GetPublic('IS_DEBUGGING')) {
			$queryResults =
		$x = array (
//									array('model'=>	'UserManagement', 'task'=> '*', 'field'=> 'RoleID', 'right'=> self::WRITE_RIGHT),
//									array('model'=>	'*', 'task'=> '*', 'field'=> '*', 'right'=> self::READ_RIGHT)
//								);
	}


}