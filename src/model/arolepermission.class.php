<?php
//**********************************************************************************************
//* APermission.class.php
//*
//* $Id$
//* $Rev: 0000 $
//* $Date: 2019-08-30 11:58:13 -0700 (Fri, 30 Aug 2019) $
//*
//* DESCRIPTION:
//*
//* USAGE:
//*
//* HISTORY:
//* 30-Aug-19 M.Merrett - Created
//*
//* TODO:
//*
//***********************************************************************************************************
//***********************************************************************************************************

namespace php_base\model;


use \php_base\Settings\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;



//***********************************************************************************************
//***********************************************************************************************
class ARolePermission {
	protected $id;
	protected $roleid;
	protected $model;
	protected $task;
	protected $field;
	protected $right;

	public function __construct( $permissionArray ) {
		if ( empty($permissionArray)) {
			throw new InvalidArgumentException('need some permission details before we can have a permission');
		}
		if (is_array($permissionArray)) {
			$this->id = $permissionArray['id'];
			$this->roleid = $permissionArray['roleid'];
			$this->model = $permissionArray['model'];
			$this->task = $permissionArray['task'];
			$this->field = $permissionArray['field'];
			$this->right = $permissionArray['right'];
		}
	}

	public function __get($propertyName){
		if ( property_exists( __class__, $propertyName)){
			return $this->$propertyName;
		}
	}

	public function __set($propertyName, $value){
		if ( property_exists( __class__, $propertyName)){
			$this->$propertyName = $value;
		}
	}

}