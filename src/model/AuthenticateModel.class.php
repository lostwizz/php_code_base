<?php

//**********************************************************************************************
//* authenticateModel.class.php
//*
//* $Id$
//* $WCREV$
//* $Rev: 0000 $
//* $Date: 2019-08-30 12:00:20 -0700 (Fri, 30 Aug 2019) $
//*
//*   "$WCREV$";
//*   "$WCREV=7$";
//*   "$WCMODS?Modified:Not modified$";
//*   "$WCUNVER?Unversioned items found:no unversioned items$";
//*   "$WCDATE$";
//*   "$WCDATEUTC$";
//*   "$WCDATE=%a, %d %B %Y$";
//*   "$WCDATE=$";
//*   "$WCDATE=%a, %c %B %Y$";
//*   "$WCDATEUTC=%a, %d %B %Y$";
//*   "$WCNOW$";
//*   "$WCNOWUTC$";
//*   "$WCISTAGGED?Tagged:Not tagged$";
//*   "$WCINGIT?versioned:not versioned$";
//*   "$WCFILEMODS?Modified:Not modified$";
//*   "$WCSUBMODULE?Working tree has at least one submodule:Working tree has no submodules$";
//*   "$WCSUBMODULEUP2DATE?All submodules are up2date (checked out HEAD):At least one submodule is not up2date (checked HEAD differs)$";
//*   "$WCMODSINSUBMODULE?At least one submodule has uncommitted items:No submodule has uncommitted items$";
//*   "$WCUNVERINSUBMODULE?At least one submodule has unversioned files:No submodule with unversioned files$";
//*   "$WCMODSFULL?Modified items found (recursively):No modified items found (also not in submodules)$";
//*   "$WCUNVERFULL?Unversioned items found (recursively):No unversioned items found (also not in submodules)$";
//*
//* DESCRIPTION:
//*
//* USAGE:
//*
//* HISTORY:
//* 30-Aug-19 M.Merrett - Created
//*
//* TODO:
//*
//***********************************************************************************************************
//***********************************************************************************************************
// a basic mod to cause gitwcrev to run

namespace php_base\Model;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;

//***********************************************************************************************
//***********************************************************************************************
class AuthenticateModel extends Model {

   const GOD_RIGHT = 'GOD';
   const DBA_RIGHT = 'DBA';
   const WRITE_RIGHT = 'Write';
   const READ_RIGHT = 'Read';
   const WILDCARD_RIGHT = '*';
   const NO_RIGHT = '__NO__RIGHT__';

   ///////////const NOBODY = '__NO__BODY__';


   protected static $User;

   //-----------------------------------------------------------------------------------------------
   public function isLoggedIn($username, $password): Response {

      if (!empty($username)) {
         //echo 'Checking login:',  $username;
         //echo '<br>';
         if ($this->isGoodAuthentication($username, $password)) {
            Settings::GetRunTimeObject('MessageLog')->addInfo('User: ' . $username . ' is logged on');
            // user and password are good so they is logged in
            self::$Uname - $password;
            Settings::SetRunTime('Currently Logged In User', $username);
         } else {
            $username = null;
         }
      } else {
         $username = null;
      }
      Settings::GetRunTimeObject('MessageLog')->addNotice('username=' . $username . (empty($username) ? 'NOT-logged in' : 'Seems to be Loggedin'));
      return (!empty($username));
   }

//	//-----------------------------------------------------------------------------------------------
   public function tryToLogin($username, $password): Response {

      Settings::GetRunTimeObject('MessageLog')->addTODO('check the username against somthing -db or file or hard or whatever!!!');
      if (empty($username) or empty($password)) {
         return new Response('Missing Username or Password trying to login', -5, false);
      }

      //////////////// -DEBUG CODE
      if ($username == $password) {
         Settings::GetRunTimeObject('MessageLog')->addInfo('User: ' . $username . ' Sucessfully Logged in');
         self::$User = $username;
         Settings::SetRunTime('Currently Logged In User', $username);
         return Response::NoError();
      } else {
         Settings::GetRunTimeObject('MessageLog')->addNotice('User: ' . $username . ' UNSucessfully Logged in!!!!');
         self::$User = null;
         return new Response('Failed Trying to Login', -4, false);
      }
//		return false;
   }

   //-----------------------------------------------------------------------------------------------
   public function isGoodAuthentication($passedUsername) {

      ///////////////////// DEBUG CODE
      if ($passedUsername = self::Uname) {
         return true;
      } else {
         return false;
      }
   }

   //-----------------------------------------------------------------------------------------------
   public function doWork(): Response {
      // should never get here
      return Response::NoError();
   }

}
