<?php

//phpinfo();

//print_r (include_once( 'P:\Projects\NB_projects\php_code_base\src\utils\Cache.class.php'));



//echo '<pre>';
//print_r ( get_declared_classes());
//echo '</pre>';

//php_base\Utils\Cache::Add('fred', 'sam');

//
//class HTML_x_input implements \Iterator {
//
//	public $obj;
//	public $position = 0;
//	public $reverse = false;
//
//
//	public function __construct(){
//		$possibeTypes = ['CHECKBOX'=> ['a','b'],
//			'RADIO'=> ['c','d'],
//			'Reset'=> ['e','f'],
//			'Password'=> ['g','h'],
//			'Submit'=> ['i','j'],
//			'BUTTON'=> ['k','l'],
//			'TEXT'=> ['m','n'],
//			'HIDDEN'=> ['o','p']];
//		$this->obj = new ArrayObject($possibeTypes);
//		$position =0;
//	}
//	public function __destruct() {
//		unset( $this->obj );
//	}
//
//	public function rewind() {
//		$this->position = $this->reverse ? count($this->obj) -1 :0;
//	}
//	public function valid(){
//		return isset($this->obj[$this->position]);
//	}
//
//	public function key(){
//		return $this->position;
//	}
//
//	public function current(){
//		return $this->obj[$this->position];
//	}
//	public function next() {
//		$this->position = $this->position + ($this->reverse ? -1 : 1);
//	}
//}
//
//$possibleTypes = ['CHECKBOX'=> ['a','b'],
//			'RADIO'=> ['c','d'],
//			'Reset'=> ['e','f'],
//			'Password'=> ['g','h'],
//			'Submit'=> ['i','j'],
//			'BUTTON'=> ['k','l'],
//			'TEXT'=> ['m','n'],
//			'HIDDEN'=> ['o','p']];
//
//
//$x = new ArrayIterator($possibleTypes);
////$x = new HTML_x_input();
//echo '<pre>';
//print_r( $x);
//echo '</pre>';
//
//echo '--------------<br>';
//foreach( $x as $k => $v ){
//
//	echo '<pre>';
//	print_r($v);
//	echo '</pre>';
//}