<?php
/** * ********************************************************************************************
 * AuthenticateView.class.php
 *
 * Summary any output from user authentication and verification
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
 * @subpackage View
 * @since 0.3.0
 *
 * @example
 *
 * @see AuthenticateModel
 * @see AuthenticateController
 * @see AuthenticateData
 *
 * @todo add forgot password
 * @todo add change password
 * @todo add signup (add new account)
 *
 */
//**********************************************************************************************

namespace php_base\View;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\HTML\HTML as HTML;
use \php_base\Utils\Response as Response;
use \php_base\Resolver as Resolver;
use \php_base\Utils\Dump\Dump as Dump;

/** * **********************************************************************************************
 * handles authentication and logon processes output
 *
 * Description.
 *
 * @since 0.0.2
 */
class AuthenticateView extends View {

   /** @todo - use this and let the user try again   */
   protected static $loginAttempts = 0;

//	var $controller;
//	//-----------------------------------------------------------------------------------------------
//	public function __construct($controller) {
//		$this->controller = $controller;
//	}

   /** -----------------------------------------------------------------------------------------------
    *
    * @param type $data
    * @return Response
    */
   public function doWork($data = null): Response {
      return true;
   }

   /** -----------------------------------------------------------------------------------------------
    *
    * @return Response
    */
   public function showLoginPage(): Response {
      self::$loginAttempts ++;

      if (self::$loginAttempts > 4) {
         echo 'Sorry to many login attempts';
      }

      //echo 'This is the login page - aint it pretty?';
      //echo 'uname = ', $this->controller->payload['username'];
      echo HTML::FormOpen('index.php',
              'LoginForm',
              'POST',
              null,
              NULL,
              NULL
      );
      echo HTML::Hidden(Resolver::REQUEST_PROCESS, 'Authenticate');
      echo HTML::Hidden(Resolver::REQUEST_TASK, 'CheckLogin');
      //echo HTML::Hidden( Resolver::REQUEST_ACTION, 'Login_check');

      echo HTML::HIDDEN(Resolver::REQUEST_PAYLOAD, Resolver::REQUEST_PAYLOAD . '[loginAttempts]');

      echo '<center>';
      echo 'Logon Form for ', Settings::GetPublic('App Name');
      echo '</center>';

      $this->showLoginBox();
      echo HTML::FormClose();
      return new Response('ok', 0, true);
   }

   /** -----------------------------------------------------------------------------------------------
    *
    */
   protected function showLoginBox() {
      ?>
      <table border=1 align=center>
         <tr>
            <td>Username: </td>
            <td colspan=2><?php echo HTML::Text(Resolver::REQUEST_PAYLOAD . '[entered_username]', null, array('maxlength' => 30, 'size' => 30)); ?></td>
         </tr><tr>
            <td>Password: </td>
            <td colspan=2><?php echo HTML::Password(Resolver::REQUEST_PAYLOAD . '[entered_password]', null, array('maxlength' => 100, 'size' => 30)); ?></td>
         </tr><tr>
            <td align=center colspan=3><?php echo HTML::Submit(Resolver::REQUEST_ACTION, 'Submit Logon'); ?></td>
         </tr><tr>
            <td><?php echo HTML::Submit(Resolver::REQUEST_ACTION, 'Change Password'); ?></td>
            <td><?php echo HTML::Submit(Resolver::REQUEST_ACTION, 'Add New Account'); ?></td>
            <td><?php echo HTML::Submit(Resolver::REQUEST_ACTION, 'Forgot Password'); ?></td>
         </tr>
      </table>
      <?php
   }

}
