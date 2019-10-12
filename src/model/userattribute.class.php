<?php

namespace php_base\model;


//***********************************************************************************************
//***********************************************************************************************
public class UserAttribute {
	private $id = -1;
	private $name = '';
	private $value = '';

	//-----------------------------------------------------------------------------------------------
	public function _construct( $username){
		LookupAttributesForUser($username);

	}

}
