<?php
//namespace UnitTestFiles\Test;

use PHPUnit\Framework\TestCase;

use \php_base\model\ARolePermission as ARolePermission;

class ARolePermission_Test extends TestCase {



	public function  XXXXX_test_aPermissionPropertyConstructAndGet () 	{
		//$this->assertTrue(false);

		$ar = [ 'id' => 1001,
				'roleid' => 1002,
				'model' => 'modelName',
				'task' => 'taskName',
				'field' => 'fieldName',
				'right' => 'rightName'
			];


		$aPermission = new ARolePermission($ar);

		$r = $aPermission->id;
		$expected = 1001;
		$this->assertEquals($expected, $r);

		$r= $aPermission->roleid;
		$expected = 1002;
		$this->assertEquals($expected, $r);

		$r= $aPermission->model;
		$expected = 'modelName';
		$this->assertEquals($expected, $r);

		$r= $aPermission->task;
		$expected = 'taskName';
		$this->assertEquals($expected, $r);

		$r= $aPermission->field;
		$expected = 'fieldName';
		$this->assertEquals($expected, $r);

		$r= $aPermission->right;
		$expected = 'rightName';
		$this->assertEquals($expected, $r);

	}

	public function  XXX_test_aPermissionPropertySetAndGet () 	{
		$ar = [ 'id' => 1001,
				'roleid' => 1002,
				'model' => 'modelName',
				'task' => 'taskName',
				'field' => 'fieldName',
				'right' => 'rightName'
			];

		$aPermission = new ARolePermission($ar);

		$r = $aPermission->id;
		$expected = 1001;
		$this->assertEquals($expected, $r);

		$aPermission->id = 2001;
		$r = $aPermission->id;
		$expected = 2001;
		$this->assertEquals($expected, $r);


		$r = $aPermission->roleid;
		$expected = 1002;
		$this->assertEquals($expected, $r);
		$aPermission->roleid = $expected= 2002;
		$r = $aPermission->roleid;
		$this->assertEquals($expected, $r);

		$r = $aPermission->model;
		$expected = 'modelName';
		$this->assertEquals($expected, $r);
		$aPermission->model = $expected = 'NewModel';
		$r = $aPermission->model;
		$this->assertEquals($expected, $r);

		$r = $aPermission->task;
		$expected = 'taskName';
		$this->assertEquals($expected, $r);
		$aPermission->task = $expected = 'NewTask';
		$r = $aPermission->task;
		$this->assertEquals($expected, $r);

		$r = $aPermission->field;
		$expected = 'fieldName';
		$this->assertEquals($expected, $r);
		$aPermission->field = $expected = 'NewField';
		$r = $aPermission->field;
		$this->assertEquals($expected, $r);

		$r = $aPermission->right;
		$expected = 'rightName';
		$this->assertEquals($expected, $r);
		$aPermission->right = $expected = 'NewRight';
		$r = $aPermission->right;
		$this->assertEquals($expected, $r);



	}

}