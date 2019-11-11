<?php

//**********************************************************************************************
//* UserRoleData.class.php
/** * ********************************************************************************************
 * UserRoleData.class.php
 *
 * Summary: reads the user's attributes from the database
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description:
 * Reads the userid and password from something - DB or file.
 *
 *
 * @link URL
 *
 * @package ModelViewController - UserRoleAndPermissions\UserRoleData
 * @subpackage UserRoleAndPermissions
 * @since 0.3.0
 *
 * @example
 *
 * @see UserRoleAndPermissionsController.class.php
 * @see UserRoleAndPermissionsModel.class.php
 * @see UserRoleAndPermissionsView.class.php
 * @see UserInfoData.class.php
 * @see UserPermissionData.class.php
 * @see UserAttributeData.class.php
 *
 * @todo Description
 *
 */
//**********************************************************************************************

namespace php_base\data;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;
use \php_base\Utils\Utils as Utils;
use \php_base\Utils\DBUtils as DBUtils;

/** * **********************************************************************************************
 * read and write the roles (by id) for the user
 */
Class UserRoleData extends Data {

//	public $action;
//	public $payload;

	public $RoleIDData = [];	// array with the keys begin the name and the values being the roleID #
	//		- only needed this way because of the RolePermissions needing the the list of ids
	public $RoleIDnames = []; // array with the keys being the roleID # and the values being the name

	/** -----------------------------------------------------------------------------------------------
	 *  constructor that initiates the reading of the database
	 * @param type $ArrayOfNames
	 */
	public function __construct($ArrayOfNames) {
		$this->doReadFromDatabase($ArrayOfNames);
	}

	/** -----------------------------------------------------------------------------------------------
	 * process the roles into arrays - one with keys being the name of the role and the other having the key the role's ID
	 * 			- the first is needed to read the role's permissions form the permission table - then it is dumped as not needed
	 * @param type $data
	 */
	public function ProcessRoleIDs($data) {
		foreach ($data as $record) {
			if (!empty($record['NAME']) and ! empty($record['ROLEID'])) {
				$this->RoleIDData[$record['NAME']] = $record['ROLEID'];
				$this->RoleIDnames[$record['ROLEID']] = $record['NAME'];
			}
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *  read the data from the database
	 *
	 * @param type $ArrayOfNames
	 * @throws \PDOException
	 * @throws \Exception
	 */
	protected function doReadFromDatabase($ArrayOfNames) {
		$names = "'" . implode("', '", $ArrayOfNames) . "'";
		try {
			$sql = 'SELECT RoleId
						,Name
					FROM ' . Settings::GetProtected('DB_Table_RoleManager')
					. ' WHERE  Name in ('
					. $names
					. ')';

			$params = null; ///array(Settings::GetPublic( 'RoleId') );
			$data = DBUtils::doDBSelectMulti($sql, $params);

			$this->ProcessRoleIDs($data);
		} catch (\PDOException $e) {
			throw new \PDOException($e->getMessage(), (int) $e->getCode());
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), (int) $e->getCode());
		}
	}

}
