
namespace php_base\Control;


use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;

//***********************************************************************************************
//***********************************************************************************************
class MenuController extends Controller {

	public $action;
	public $payload;

	//-----------------------------------------------------------------------------------------------
	public function __construct($action='', $payload = null) {
		//$this->model = new \php_base\model\AuthenticateModel($this);
		//$this->data = new \php_base\data\AuthenticateData($this);
		$this->view = new \php_base\view\AuthenticateView($this);

		$this->action = $action;
		$this->payload = $payload;
	}

	//-----------------------------------------------------------------------------------------------
	public function doWork(){
		echo 'menuController doWork hi - i am here!!';
		echo 'should never get here';
	}





}