<?php

/** * ********************************************************************************************
 * AuthenticateController.class.php
 *
 * Summary user authentication and verification
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description.
 * this handles the initial logn on screen and its results - also resonsible for vwerifying the user is still "Logged on"
 *
 *
 *
 * @link URL
 *
 * @package  AuthenticateController
 * @subpackage Controller
 * @since 0.3.0
 *
 * @example
 *
 * @see AuthenticateModel
 * @see AuthenticateView
 * @see AuthenticateData
 *
 * @todo add forgot password
 * @todo add change password
 * @todo add signup (add new account)
 *
 */
//**********************************************************************************************

namespace php_base\Control;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;

/** * **********************************************************************************************
 * handles authentication and logon processes
 *
 * Description.
 *
 * @since 0.0.2
 */
class AuthenticateController extends Controller {

   public $process;
   public $task;
   public $action;
   public $payload;

   /**  -----------------------------------------------------------------------------------------------
    *
    * @param string $passedAction
    * @param type $passedPayload
    */
   public function __construct(string $passedAction = '', $passedPayload = null) {
      $this->model = new \php_base\model\AuthenticateModel($this);
      $this->data = new \php_base\data\AuthenticateData($this);
      $this->view = new \php_base\view\AuthenticateView($this);

      $this->action = $passedAction;
      $this->payload = $passedPayload;
   }

   /**  -----------------------------------------------------------------------------------------------
    *
    * @param type $process
    * @param type $task
    */
   public function setProcessAndTask($process, $task): void {
      $this->process = $process;
      $this->task = $task;
   }

   /**  -----------------------------------------------------------------------------------------------
    *
    * @return Response
    */
   public function doWork(): Response {
      echo 'authenticationController doWork hi - i am here!!';
      echo 'should never get here';
      return Response::TODO_Error();
   }

   /**  -----------------------------------------------------------------------------------------------
    * get the username and password then call the appropriate method
    * @param type $parent
    * @return Response
    */
   public function checkLogin($parent): Response {
      $username = (!empty($this->payload['entered_username'])) ? $this->payload['entered_username'] : null;
      $password = (!empty($this->payload['entered_password'])) ? $this->payload['entered_password'] : null;

      if ( !empty($this->action) ){
         $action = $this->action;
      } else {
         $action = 'Need_login';
      }

      return $this->$action($parent, $username, $password);
   }

   /**  -----------------------------------------------------------------------------------------------
    * handle no logon yet -i.e. show the login page
    *
    * @see AuthenticateView
    * @param type $parent
    * @param type $username
    * @param type $password
    * @return Response
    */
   protected function Need_login($parent, $username = null, $password = null): Response {
      return $this->view->showLoginPage();
   }

   /**  -----------------------------------------------------------------------------------------------
    *  verify password is good and therefor the user has logged on
    *
    * @see AuthenticateModel
    *
    * @param type $parent
    * @param type $username
    * @param type $password
    * @return Response
    */
   protected function Submit_Logon($parent, $username = null, $password = null): Response {
      return $this->model->tryToLogin($username, $password);
   }

   /**  -----------------------------------------------------------------------------------------------
    * ask the user for the old password and 2 repeats of the new one
    *
    * @todo !!!!!!!!!!!!build this
    *
    * @param type $parent
    * @param type $username
    * @param type $password
    * @return Response
    */
   protected function change_Password($parent, $username = null, $password = null): Response {
      return Response::TODO_Error();
   }

   /**  -----------------------------------------------------------------------------------------------
    * user has entered the old passwords and two identical (?) new  ones - so change it
    *
    * @param type $parent
    * @param type $username
    * @param type $password
    * @return Response
    */
   protected function password_change_submit($parent, $username = null, $password = null): Response {
      return Response::TODO_Error();
   }

   /**  -----------------------------------------------------------------------------------------------
    * add a new user to the authentication system
    * @todo !!!!!!!!!!!!build this
    *
    * @param type $parent
    * @param type $username
    * @param type $password
    * @return Response
    */
   protected function add_New_Account($parent, $username = null, $password = null): Response {
      return Response::TODO_Error();
   }

   /**  -----------------------------------------------------------------------------------------------
    * reset the password and email the user with the new one
    *
    * @todo !!!!!!!!!!!!build this
    *
    * @param type $parent
    * @param type $username
    * @param type $password
    * @return Response
    */
   protected function forgot_Password($parent, $username = null, $password = null): Response {
      return Response::TODO_Error();
   }

}
