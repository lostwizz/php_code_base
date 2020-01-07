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

	public $parent = null;

	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';


	/** -----------------------------------------------------------------------------------------------
	 * constructor - the parent has the data
	 * @param type $parentObj
	 */
	public function __construct($parentObj) {
		$this->parent = $parentObj;
	}

	/** -----------------------------------------------------------------------------------------------
	 * gives a version number
	 * @static
	 * @return type
	 */
	public static function Version() {
		return self::VERSION;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $data
	 * @return bool
	 */
	public function doWork($data = null) : Response {
		//return true;
		return Response::GenericError();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return void
	 */
	public function showLoginPage(): void {
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
		echo HTML::Hidden(Resolver::REQUEST_TASK, 'checkAuthentication');

		//echo HTML::HIDDEN(Resolver::REQUEST_PAYLOAD, Resolver::REQUEST_PAYLOAD . '[loginAttempts]');

		echo '<center>';
		echo 'Logon Form for ', Settings::GetPublic('App Name');
		echo '</center>';

		$this->showLoginBox(false, false, true);
		echo HTML::FormClose();
	}

	/** ----------------------------------------------------------------------------------------------
	 *
	 * @return void
	 */
	protected function showLoginBox(bool $showChangePwd =false, bool $showAddAcct = false, bool $showForgotPwd = true): void {
		$bottomLineSpan = 3 -($showChangePwd ? 1:0) + ($showAddAcct ? 1:0) +($showForgotPwd ? 1:0 );
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
			</tr><tr align=center>
				<?php
					if (  $showChangePwd){
						echo HTML::Open('TD');
						echo HTML::Submit(Resolver::REQUEST_ACTION, 'Change Password');
					} else {
						echo HTML::Open('TD');
						echo HTML::Space( 20);
					}
					echo HTML::Close('TD');

					if ($showAddAcct){
						echo HTML::Open('TD');
						echo HTML::Submit(Resolver::REQUEST_ACTION, 'Add New Account');
					} else {
						echo HTML::Open('TD');
						echo HTML::Space( 20);
					}
					echo HTML::Close('TD');

					if ( $showForgotPwd) {
					echo HTML::Open('TD');
						echo HTML::Submit(Resolver::REQUEST_ACTION, 'Forgot Password');
					} else {
						echo HTML::Open('TD');
						echo HTML::Space( 20);
					}
					echo HTML::Close('TD');
				?>
			</tr>
		</table>
		<?php
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return void
	 */
	public function showForgotPassword(): void {

		echo HTML::FormOpen('index.php',
				'ForgotPasswordForm',
				'POST',
				null,
				NULL,
				NULL
		);
		echo HTML::Hidden(Resolver::REQUEST_PROCESS, 'Authenticate');
		echo HTML::Hidden(Resolver::REQUEST_TASK, 'ChangeForgotPassword');

		echo '<center>';
		echo 'Forgot Password Form for ', Settings::GetPublic('App Name');
		echo '</center>';

		$this->showUserNameBox();
		echo HTML::FormClose();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return void
	 */
	protected function showUserNameBox(): void {
		?>
		<table border=1 align=center>
			<tr>
				<td>Username: </td>
				<td><?php echo HTML::Text(Resolver::REQUEST_PAYLOAD . '[entered_username]', null, array('maxlength' => 30, 'size' => 30)); ?></td>
			</tr><tr>
				<td align=center colspan=2><?php echo HTML::Submit(Resolver::REQUEST_ACTION, 'Submit Username for Forgot Password'); ?></td>
			</tr>
		</table>
		<?php
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return void
	 */
	public function showChangePassword(): void {
		echo HTML::FormOpen('index.php',
				'ChangePasswordForm',
				'POST',
				null,
				NULL,
				NULL
		);
		echo HTML::Hidden(Resolver::REQUEST_PROCESS, 'Authenticate');
		echo HTML::Hidden(Resolver::REQUEST_TASK, 'ChangePasswordTask');

		echo '<center>';
		echo 'Forgot Password Form for ', Settings::GetPublic('App Name');
		echo '</center>';

		$this->showUserOldAndNewPassword();
		echo HTML::FormClose();
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return void
	 */
	public function showNoEmailAddressError():void{
		echo '<div class="responseError">';
		echo 'Sorry Cannot Change Password - missing eMail address';
		echo '</div>';
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return void
	 */
	protected function showUserOldAndNewPassword(): void {
		?>
		<table border=1 align=center>
			<tr>
				<td>Username: </td>
				<td><?php echo HTML::Text(Resolver::REQUEST_PAYLOAD . '[entered_username]', null, array('maxlength' => 30, 'size' => 30)); ?>
				</td>
			</tr><tr>
				<td>Old Password: </td>
				<td>
		<?php echo HTML::Password(Resolver::REQUEST_PAYLOAD . '[old_password]', null, array('maxlength' => 100, 'size' => 30)); ?>
				</td>
			</tr><tr>
				<td>New Password: </td>
				<td>
		<?php echo HTML::Password(Resolver::REQUEST_PAYLOAD . '[new_password]', null, array('maxlength' => 100, 'size' => 30)); ?>
				</td>
			</tr><tr>
				<td align=center colspan=2><?php echo HTML::Submit(Resolver::REQUEST_ACTION, 'Submit Username for Password Change'); ?></td>
			</tr>
		</table>
		<?php
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return void
	 */
	public function showAddNewAccount(): void {
		echo HTML::FormOpen('index.php',
				'ChangePasswordForm',
				'POST',
				null,
				NULL,
				NULL
		);
		echo HTML::Hidden(Resolver::REQUEST_PROCESS, 'Authenticate');
		echo HTML::Hidden(Resolver::REQUEST_TASK, 'ChangePasswordTask');

		echo '<center>';
		echo 'Forgot Password Form for ', Settings::GetPublic('App Name');
		echo '</center>';

		$this->showNewAccountBox();
		echo HTML::FormClose();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return void
	 */
	protected function showNewAccountBox(): void {
		?>
		<table border="1" align=center>
			<tr>
				<td>Username: </td>
				<td><?php echo HTML::Text(Resolver::REQUEST_PAYLOAD . '[entered_username]', null, array('maxlength' => 30, 'size' => 30)); ?>
				</td>
			</tr><tr>
				<td>Password: </td>
				<td>
		<?php echo HTML::Password(Resolver::REQUEST_PAYLOAD . '[entered_password]', null, array('maxlength' => 100, 'size' => 30)); ?>
				</td>
			</tr><tr>
				<td>Email Address: </td>
				<td>
		<?php echo HTML::Text(Resolver::REQUEST_PAYLOAD . '[entered_email]', null, array('maxlength' => 255, 'size' => 30)); ?>
				</td>
			</tr><tr>
				<td align=center colspan=2><?php echo HTML::Submit(Resolver::REQUEST_ACTION, 'Submit New Account Info'); ?></td>
			</tr>
		</table>
		<?php
	}

}
