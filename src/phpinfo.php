<?php

//phpinfo();





class HTML_x_input implements \Iterator {

	public $obj;
	public $position = 0;
	public $reverse = false;


	public function __construct(){
		$possibeTypes = ['CHECKBOX','RADIO','Reset', 'Password', 'Submit', 'BUTTON', 'TEXT', 'HIDDEN'];
		$this->obj = new ArrayObject($possibeTypes);
		$position =0;
	}
	public function __destruct() {
		unset( $this->obj );
	}

	public function rewind() {
		$this->position = $this->reverse ? count($this->obj) -1 :0;
	}
	public function valid(){
		return isset($this->obj[$this->position]);
	}

	public function key(){
		return $this->position;
	}

	public function current(){
		return $this->obj[$this->position];
	}
	public function next() {
		$this->position = $this->position + ($this->reverse ? -1 : 1);
	}
}


$x = new HTML_x_input();
echo '<pre>';
print_r( $x);
echo '</pre>';
foreach( $x as $k => $v ){

	echo '<pre>';
	print_r($v);
	echo '</pre>';
}