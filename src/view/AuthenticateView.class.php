<?php
//**********************************************************************************************
//* AuthenticateView.class.php
//*
//* $Id$
//* $Rev: 0000 $
//* $Date: 2019-09-12 09:55:00 -0700 (Thu, 12 Sep 2019) $
//*
//* DESCRIPTION:
//*
//* USAGE:
//*
//* HISTORY:
//* 12-Sep-19 M.Merrett - Created
//*
//* TODO:
//*
//***********************************************************************************************************
//***********************************************************************************************************


namespace php_base\View;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\HTML\HTML as HTML;
use \php_base\Utils\Response as Response;

use \php_base\Resolver as Resolver;


use \php_base\Utils\Dump\Dump as Dump;

//***********************************************************************************************
//***********************************************************************************************
class AuthenticateView extends View{

	protected static $loginAttempts =0;

//	var $controller;

//	//-----------------------------------------------------------------------------------------------
//	public function __construct($controller) {
//		$this->controller = $controller;
//	}

	//-----------------------------------------------------------------------------------------------
	public  function doWork( $data =null) : Response{
		return true;
	}

	//-----------------------------------------------------------------------------------------------
	public function showLoginPage() : Response {
		self::$loginAttempts ++;

		if ( self::$loginAttempts > 4) {
			echo 'Sorry to many login attempts';
		}

		echo 'This is the login page - aint it pretty?';
		//echo 'uname = ', $this->controller->payload['username'];
		echo HTML::FormOpen( 'index.php',
						'LoginForm',
						'POST',
						null,
						NULL,
						NULL
					);
		echo HTML::Hidden( Resolver::REQUEST_PROCESS, 'Authenticate');
		echo HTML::Hidden( Resolver::REQUEST_TASK, 'CheckLogin');
		echo HTML::Hidden( Resolver::REQUEST_ACTION, 'Login_check');

		echo HTML::HIDDEN( Resolver::REQUEST_PAYLOAD,  Resolver::REQUEST_PAYLOAD .'[loginAttempts]');


		echo '<center>';
		echo 'Logon Form for ', Settings::GetPublic( 'App Name') ;
		echo '</center>';

		?> <table border=1 align=center>
				<tr>
					<td>Username: </td>
					<td colspan=2> <?php echo HTML::Text( Resolver::REQUEST_PAYLOAD . '[entered_username]', null, array( 'maxlength' => 30, 'size'=>30)); ?></td>
				</tr><tr>
					<td>Password: </td>
					<td colspan=2><?php echo HTML::Text( Resolver::REQUEST_PAYLOAD . '[entered_password]', null, array( 'maxlength' =>100, 'size'=>30)); ?></td>
				</tr><tr>
					<td align=center colspan=3><?php echo HTML::Submit('login_form_submit', 'Submit Logon');?></td>
				</tr><tr>
					<td><?php echo HTML::Submit('login_button', 'Change Password');?></td>
					<td><?php echo HTML::Submit('login_button', 'Add New Account');?></td>
					<td><?php echo HTML::Submit('login_button', 'Forgot Password');?></td>

				</tr>
			</table>
		<?php
		echo HTML::FormClose();
		return new Response( 'ok', 0, true);
	}

}
