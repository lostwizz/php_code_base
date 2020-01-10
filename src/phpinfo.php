<?php

//$s .= HTML::Image(Resolver::REQUEST_PAYLOAD . '[sortAsc][' . $fldName . ']', '\static\images\A_to_Z_icon.png');


//$ex = '^(("(?:[^"]|"")*"|[^,]*)(,("(?:[^"]|"")*"|[^,]*))*)$';
//
//
//$s = "!!!!!!!!!!!!! at TestController', 'now at', array('Show BackTrace Num Lines' => 99,'Beautify_BackgroundColor' => '#FFAA55') ";
//
//$s = "!!!!!!!!!!!!! at TestController', 'now at', array('Show BackTrace Num Lines' => 99,'Beautify_BackgroundColor' => '#FFAA55') ";
//
//
//$s2=  "array(1,2,'3', 'bob'=>'sam'), array('Show BackTrace Num Lines' => 99,'Beautify_BackgroundColor' => '#FFAA55') ";
//
//$ex1 = "/\,/";
//$ex2 = '/,(?=(?:[^\"]*\"[^\"]*\")*(?![^\"]*\"))/';
//$ex3 = '(?=(?:[^\"])*(?![^\"]))/';
//
////$a = preg_split('(?=(?:[^\"])*(?![^\"]))/', $s);
//
//$a = preg_split($ex1, $s2);
//echo '<pre>';
//print_r($a);
//echo '</pre>';
//

//$a = preg_split($ex, $s);
//print_r( $a);

phpinfo();

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