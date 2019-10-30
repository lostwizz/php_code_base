<?php

/** * ********************************************************************************************
 * resolver.class.php
 *
 * Summary (no period for file headers)
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.0.1
 * $Id$
 *
 * Description. (use period)
 *
 *
 * @link URL
 *
 * @package WordPress
 * @subpackage Component
 * @since x.x.x (when the file was introduced)
 *
 * @example path description
 * @see elementName
 *
 * @todo Description
 *
 */
//**********************************************************************************************


namespace php_base;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;

/** * **********************************************************************************************
 * Summary.
 *
 * Description.
 *
 * @since 0.0.2
 */
class Resolver {

   /**
    * Summary.
    *
    * @since x.x.x (if available)
    * @var type $var Description.
    */
   const REQUEST_PROCESS = 'ACTION_PROCESS';
   const REQUEST_TASK = 'ACTION_TASK';
   const REQUEST_ACTION = 'ACTION_ACTION';
   const REQUEST_PAYLOAD = 'ACTION_PAYLOAD';

   public $dispatcher;
   public $requestInfo;
   public $process = null;
   public $task = null;
   public $action = null;
   public $payload = null;

   /** -----------------------------------------------------------------------------------------------
    * Summary.
    *
    * Description.
    *
    * @since x.x.x
    *
    * @see Function/method/class relied on
    * @link URL
    * @global type $varname Description.
    * @global type $varname Description.
    *
    * @param type $var Description.
    * @param type $var Optional. Description. Default.
    * @return type Description.
    */
   public function __construct() {
      $this->dispatcher = new Dispatcher();
   }

   /** -----------------------------------------------------------------------------------------------
    * Summary.
    *
    * Description.
    *
    * @since x.x.x
    *
    * @see Function/method/class relied on
    * @link URL
    * @global type $varname Description.
    * @global type $varname Description.
    *
    * @param type $var Description.
    * @param type $var Optional. Description. Default.
    * @return type Description.
    */
   public function doWork(): Response {

      if (Settings::GetPublic('IS_DEBUGGING')) {
         Dump::dump(filter_input_array(\INPUT_POST, \FILTER_SANITIZE_STRING));
      }

      $this->AddHeader();
      $this->AddFooter();

      $this->AddSetupAuthenticateCheck();  // always start with login checks

      $this->AddSetupUserRoleAndPermissions(); // after they have logged in now setup the user permissions

      $this->decodeRequestinfo();

      $this->SetupDefaultController();    // this would usually be the menu starter
      // $r should be a ResponseClass
      $r = $this->StartDispatch();
      return $r;
   }

   /** -----------------------------------------------------------------------------------------------
    * Summary.
    *
    * Description.
    *
    * @since x.x.x
    *
    * @see Function/method/class relied on
    * @link URL
    * @global type $varname Description.
    * @global type $varname Description.
    *
    * @param type $var Description.
    * @param type $var Optional. Description. Default.
    * @return type Description.
    */
   protected function startDispatch(): Response {

      $r = $this->dispatcher->do_work($this);
      if ($r->hadFatalError()) {
         //echo 'Loggon failed';
         Settings::GetRunTimeObject('MessageLog')->addNotice('resolver got: ' . $r->toString());
         Settings::GetRuntimeObject('FileLog')->addNotice('resolver got:' . $r->toString());
         return $r;
      }
////		if ($r[2]== false){
////			//echo 'Post Queue failed? footer?';
////			Settings::GetRunTimeObject('MessageLog')->addNotice( 'resolver got a false on post' );
////			Settings::GetRuntimeObject('FileLog')->addNotice( 'resolver got a false on post');
////			return false;
////		}
////
////		if ( $r[1] == false){
////			//echo 'the dispach queue got a false';
////			Settings::GetRunTimeObject('MessageLog')->addNotice( 'resolver got a false on dispatchQ' );
////			Settings::GetRuntimeObject('FileLog')->addNotice( 'resolver got a false on dispatchQ');
      //echo 'all seems good to the resolver';
      Settings::GetRunTimeObject('MessageLog')->addNotice('resolver got a true');
      Settings::GetRuntimeObject('FileLog')->addNotice('resolver got a true');
      return $r;
   }

   /** -----------------------------------------------------------------------------------------------
    * Summary.
    *
    * Description.
    *
    * @since x.x.x
    *
    * @see Function/method/class relied on
    * @link URL
    * @global type $varname Description.
    * @global type $varname Description.
    *
    * @param type $var Description.
    * @param type $var Optional. Description. Default.
    * @return type Description.
    */
   protected function setupDefaultController() {
      $process = 'TEST';
      $task = 'doWork';
      $action = null;
      $payload = ['username' => Settings::GetRunTime('Currently Logged In User')];
      $this->dispatcher->addProcess($process, $task, $action, $payload);
   }

   /** -----------------------------------------------------------------------------------------------
    * Summary.
    *
    * Description.
    *
    * @since x.x.x
    *
    * @see Function/method/class relied on
    * @link URL
    * @global type $varname Description.
    * @global type $varname Description.
    *
    * @param type $var Description.
    * @param type $var Optional. Description. Default.
    * @return type Description.
    */
   public function decodeRequestinfo() {

      //$vv = filter_input(\INPUT_POST, 'REQUEST_PROCESS');
      $vv2 = filter_input_array(\INPUT_POST, \FILTER_SANITIZE_STRING);

      //dump::dump($vv);
//      dump::dump($vv2);
      $process = (!empty($vv2[self::REQUEST_PROCESS])) ? $vv2[self::REQUEST_PROCESS] : null;
      $task = (!empty($vv2[self::REQUEST_TASK])) ? $vv2[self::REQUEST_TASK] : null;
      $action = (!empty($vv2[self::REQUEST_ACTION])) ? $vv2[self::REQUEST_ACTION] : null;
      $payload = (!empty($vv2[self::REQUEST_PAYLOAD])) ? $vv2[self::REQUEST_PAYLOAD] : null;

      if (!( $process == 'Authenticate' and $task == 'CheckLogin' )) {
         $this->dispatcher->addProcess($process, $task, $action, $payload);
      } else {
         //already have an authenticate.checkLogin task - dont need another
      }
   }

   /** -----------------------------------------------------------------------------------------------
    * Summary.
    *
    * Description.
    *
    * @since x.x.x
    *
    * @see Function/method/class relied on
    * @link URL
    * @global type $varname Description.
    * @global type $varname Description.
    *
    * @param type $var Description.
    * @param type $var Optional. Description. Default.
    * @return type Description.
    */
   protected function addSetupUserRoleAndPermissions() {
      $process = 'UserRoleAndPermissions';
      $task = 'Setup';
      $action = null;
      $payload = null;
      $this->dispatcher->addPREProcess($process, $task, $action, $payload);
   }

   /** -----------------------------------------------------------------------------------------------
    * Summary.
    *
    * Description.
    *
    * @since x.x.x
    *
    * @see Function/method/class relied on
    * @link URL
    * @global type $varname Description.
    * @global type $varname Description.
    *
    * @param type $var Description.
    * @param type $var Optional. Description. Default.
    * @return type Description.
    */
   // always start with logged on check
   // now false cases -
   // nothing passed so show logon form
   //  loggin on for the first time
   // sucessfull
   // unsucsessful
   // have already logged on and just check session? still good
   // have already logged in and timed out OR some other reason they should login again
   protected function addSetupAuthenticateCheck() {
      $vv = filter_input_array(\INPUT_POST, \FILTER_SANITIZE_STRING);

      $payload = (!empty($vv[self::REQUEST_PAYLOAD])) ? $vv[self::REQUEST_PAYLOAD] : array();
      $process = 'Authenticate';
      $task = 'CheckLogin';

      if ($this->hasNoPassedInfo()) {
         $action = 'Need_Login';
         $payload = array_merge($payload, array('authAction' => $action));
      } else if ($this->passingFirstTimeCredentials()) {
         $action = 'do_the_logon_attempt';
         $payload = array_merge($payload, array('authAction' => $action));
      } else if ($this->passingOngoingDetails()) {
         $action = 'Check_Ongoing_Connection';
         $payload = array_merge($payload, array('authAction' => $action));
      } else if ($this->isChangePasswordRequest()) {

      } else if ($this->isForgotPasswordRequest()) {

      } else if ($this->isSignupRequest()) {

      } else {
         $action = 'no_Credentials';
         $payload = array_merge($payload, array('authAction' => $action));
      }
      $this->dispatcher->addPREProcess($process, $task, $action, $payload);
   }

   /** -----------------------------------------------------------------------------------------------
    * Summary.
    *
    * Description.
    *
    * @since x.x.x
    *
    * @see Function/method/class relied on
    * @link URL
    * @global type $varname Description.
    * @global type $varname Description.
    *
    * @param type $var Description.
    * @param type $var Optional. Description. Default.
    * @return type Description.
    */
   function isChangePasswordRequest() {
      return false;
   }

   /** -----------------------------------------------------------------------------------------------
    * Summary.
    *
    * Description.
    *
    * @since x.x.x
    *
    * @see Function/method/class relied on
    * @link URL
    * @global type $varname Description.
    * @global type $varname Description.
    *
    * @param type $var Description.
    * @param type $var Optional. Description. Default.
    * @return type Description.
    */
   function isForgotPasswordRequest() {
      return false;
   }

   /** -----------------------------------------------------------------------------------------------
    * Summary.
    *
    * Description.
    *
    * @since x.x.x
    *
    * @see Function/method/class relied on
    * @link URL
    * @global type $varname Description.
    * @global type $varname Description.
    *
    * @param type $var Description.
    * @param type $var Optional. Description. Default.
    * @return type Description.
    */
   function isSignupRequest() {
      return false;
   }

   /** -----------------------------------------------------------------------------------------------
    * Summary.
    *
    * Description.
    *
    * @since x.x.x
    *
    * @see Function/method/class relied on
    * @link URL
    * @global type $varname Description.
    * @global type $varname Description.
    *
    * @param type $var Description.
    * @param type $var Optional. Description. Default.
    * @return type Description.
    */
   function hasNoPassedInfo() {
      $vv = filter_input_array(\INPUT_POST, \FILTER_SANITIZE_STRING);
      if (empty($vv[self::REQUEST_PROCESS])) {
         return true;
      }
      return false;
   }

   /** -----------------------------------------------------------------------------------------------
    * Summary.
    *
    * Description.
    *
    * @since x.x.x
    *
    * @see Function/method/class relied on
    * @link URL
    * @global type $varname Description.
    * @global type $varname Description.
    *
    * @param type $var Description.
    * @param type $var Optional. Description. Default.
    * @return type Description.
    */
   protected function passingOngoingDetails() {
      $vv = filter_input_array(\INPUT_POST, \FILTER_SANITIZE_STRING);
      if (!empty($vv)
              and ! empty($vv['payload'])
              and ! empty($vv['payload']['credentials'])
              and ! empty($vv['payload']['credentials']['username'])) {

         //////////!!!!!! and anything else that needs to be passes in an ongoin session - maybe session id?
         //Settings::GetRunTimeObject('MessageLog')->addNotice('ongoing details true');
         return true;
      } else {
         //Settings::GetRunTimeObject('MessageLog')->addNotice('ongoing details false');
         return false;
      }
   }

   /** -----------------------------------------------------------------------------------------------
    * Summary.
    *
    * Description.
    *
    * @since x.x.x
    *
    * @see Function/method/class relied on
    * @link URL
    * @global type $varname Description.
    * @global type $varname Description.
    *
    * @param type $var Description.
    * @param type $var Optional. Description. Default.
    * @return type Description.
    */
   protected function passingFirstTimeCredentials() {
      $vv = filter_input_array(\INPUT_POST, \FILTER_SANITIZE_STRING);
      if (!empty($vv[self::REQUEST_PROCESS])
              and $vv[self::REQUEST_PROCESS] == 'Authenticate'
              and ! empty($vv[self::REQUEST_TASK])
              and $vv[self::REQUEST_TASK] == 'CheckLogin'
      ) {
         //Settings::GetRunTimeObject('MessageLog')->addNotice('firsttime login true');
         return true;
      } else {
         //Settings::GetRunTimeObject('MessageLog')->addNotice('firsttime login false');
         return false;
      }
   }

   /** -----------------------------------------------------------------------------------------------
    * Summary.
    *
    * Description.
    *
    * @since x.x.x
    *
    * @see Function/method/class relied on
    * @link URL
    * @global type $varname Description.
    * @global type $varname Description.
    *
    * @param type $var Description.
    * @param type $var Optional. Description. Default.
    * @return type Description.
    */
   protected function addHeader() {
      $process = 'Header';
      $task = 'doWork';
      $action = null;
      $payload = null;

      $this->dispatcher->addPREProcess($process, $task, $action, $payload);
   }

   /** -----------------------------------------------------------------------------------------------
    * Summary.
    *
    * Description.
    *
    * @since x.x.x
    *
    * @see Function/method/class relied on
    * @link URL
    * @global type $varname Description.
    * @global type $varname Description.
    *
    * @param type $var Description.
    * @param type $var Optional. Description. Default.
    * @return type Description.
    */
   protected function addFooter() {
      $process = 'Footer';
      $task = 'doWork';
      $action = null;
      $payload = null;

      $this->dispatcher->addPOSTProcess($process, $task, $action, $payload);
   }

}
