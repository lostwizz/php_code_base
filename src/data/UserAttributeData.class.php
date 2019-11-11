<?php

/** * ********************************************************************************************
 * UserAttributeData.class.php
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
 * @package ModelViewController - UserRoleAndPermissions\UserAttributes
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
 * @see UserRoleData.class.php
 *
 * @todo Description
 *
 */
//**********************************************************************************************


namespace php_base\data;

use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Utils as Utils;
use \php_base\Utils\DBUtils as DBUtils;

use \php_base\Utils\Response as Response;
use \php_base\Utils\Settings as Settings;

/** * **********************************************************************************************
 * This any of the reads or writes to the UserAttributes table
 */
class UserAttributeData  extends data{

	public $UserAttributes= [];
	public $roleNames =[];

	/** -----------------------------------------------------------------------------------------------
	 * constructor - starts of the reading of data for that User Id
	 * @param type $userID
	 */
	public function __construct($userID) {
		$this->doReadFromDatabase($userID);
	}

	/** -----------------------------------------------------------------------------------------------
	 * returns the basic list of Roles (used for the processsing of roles to eliminate duplicates
	 *
	 * @return type
	 */
	public function getArrayOfRoleNames() {
		return $this->roleNames;
	}

	/** -----------------------------------------------------------------------------------------------
	 * take the primary roll and add it to the list of roles
	 *
	 * @param type $roleName
	 */
	public function AddPrimaryRole($roleName){
		if ( !in_array( $roleName, $this->roleNames ) and !empty($roleName)) {
			$this->roleNames[] = $roleName;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * take the raw data from the database and make an associative array for the attributes
	 *    - handle duplicates (dont add 2nd  copy)
	 *
	 * @param type $data
	 */
	public function ProcessAttributes( $data) {

//Dump::dump(	$this->roleNames);
		foreach ($data as $record){
			if (($record['ATTRIBUTENAME'] =='PrimaryRole' )
			 or ($record['ATTRIBUTENAME'] =='SecondaryRole' )
			 or ($record['ATTRIBUTENAME'] =='Role' )) {

			 	if (!empty($record['ATTRIBUTEVALUE'])
			 	and !in_array($record['ATTRIBUTEVALUE'] , $this->roleNames)){

			 		$this->roleNames[] = $record['ATTRIBUTEVALUE'];
			 	}
			} else {
				$this->UserAttributes[$record['ATTRIBUTENAME']] = $record['ATTRIBUTEVALUE'];
			}
		}
	}

	//-----------------------------------------------------------------------------------------------
////	public function AddPrimaryRole($roleName) {
////		if ( array_key_exists ( $roleName, $this->UserAttributes ) ) {
////			$this->UserAttributes;
////		}
////	}

	//-----------------------------------------------------------------------------------------------
	////  SELECT TOP (1000) [id]
////      ,[UserId]
////      ,[AttributeName]
////      ,[AttributeValue]
////  FROM [Mikes_Application_Store].[dbo].
////       [UserAttributes]
////  id	UserId	AttributeName	AttributeValue
////1	1	GivenName	Mike
////2	1	SurName	Merrett
////3	1	eMailAddress	mike.merrett@whitehorse.ca
////4	1	PrimaryRole	DBA
////5	1	SecondaryRole	Clerk
////6	1	SecondaryRole	ViewOnly
	/** -----------------------------------------------------------------------------------------------
	 * read the database for that user id
	 *
	 * @param type $userID
	 * @throws \PDOException
	 * @throws \Exception
	 */
	protected function doReadFromDatabase($userID) {

		try {
			$sql = 'SELECT Id
						,UserId
						,AttributeName
						,AttributeValue
					FROM ' .  Settings::GetProtected( 'DB_Table_UserAttributes')
					. ' WHERE  UserId = :userid';

			$params = array( ':userid' =>$userID);
			$data = DBUtils::doDBSelectMulti($sql, $params) ;

			$this->ProcessAttributes( $data);
		} catch (\PDOException $e)				{
			throw new \PDOException($e->getMessage(), (int)$e->getCode());
		} catch (\Exception $e){
			throw new \Exception($e->getMessage(), (int)$e->getCode());
		}
	}


}