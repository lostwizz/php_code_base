<?php

// this class exists so that the Settings::GetRuntimeObject(x) will return something that
//		wont error out if it was an object expecting to work when calling
// so with out this Settings::GetRuntime( 'x') will return false if 'x' doesnt exists
//	 	and then   Settings::GetRuntime( 'x')->addInfo('blah blah') will throw an exception
//				Fatal error: Uncaught Error: Call to a member function addInfo() on bool
//
// but with Settings::GetRuntimeObject('x') will return  a new myNullAbsorber() which will then
//     look like Settings::GetRuntimeObject('x')->addInfo('blah blah')
//       which translates to  myNullAbsorber->addInfo('blah blah') which  this class (via the __call) will take
//            and not throw an error -- it just does nothing
//
//     this way i can write code that does not depend on something existing -- say when i move between home and work
//                  on objects i have protected this way


namespace php_base\Utils;

//
//$x = new myNullAbsorber();
//
//$x->fred_was_Herr( 17);
//$x->SAmmy = 87;
//$y = $x->john;
//
//
//myNullAbsorber::tony();


Class myNullAbsorber {

	public function __call( $method, $arguments){
		//echo 'tryied to call', $method;
		return false;
	}
	public function __get($property){
		return false;
	}
	public function __set($property, $value){
		return null;
	}
//	public function __callStatic( $method, $args){
//		echo 'tryied to call', $method;
//	}
}
