<?php

//**********************************************************************************************
//* FooterView.class.php
//*
//* $Id$
//* $Rev: 0000 $
//* $Date: 2019-09-12 09:46:20 -0700 (Thu, 12 Sep 2019) $
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
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;

//***********************************************************************************************
//***********************************************************************************************
class FooterView extends View {

   /** -----------------------------------------------------------------------------------------------
    *
    * @param type $parent
    * @return Response
    */
   public function doWork($parent = null): Response {

      //Settings::GetRunTimeObject('MessageLog')->addERROR('some error message');

      $exec_time = microtime(true) - Settings::GetRunTime('Benchmarks.start.executionTime');
      Settings::GetRunTimeObject('MessageLog')->addINFO('Execution Time was: ' . $exec_time);
      Settings::GetRunTimeObject('MessageLog')->showAllMessagesInBox();  // !! a!lways do this last so you get all the outstanding messages!!!!

      echo '<footer>';
      echo '<Br>--footer--<Br>';

      echo '</footer>';

      echo '</body>';
      //return new Response('ok', 0, true);
      return Response::NoError();
   }

}
