<?php
/**
 * Caracol CMS. <http://www.caracol.ru>
 * Copyright (C) 2005-2012 Eurosigma Group  <http://www.eurosigma.ru>
 *
 * This file is part of Caracol CMS.
 *
 * Caracol CMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Caracol CMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Caracol CMS.  If not, see <http://www.gnu.org/licenses/>.
 *
 */



Error_Reporting(E_ALL & ~E_NOTICE);

// Constants definition  ------------------------------------------------
define('CRCL_ON', TRUE);
define( "CRCL_DOC_ROOT", dirname(__FILE__)."/");
define( "CRCL_MODULE_PATH", CRCL_DOC_ROOT."modules/");
define( "CRCL_PRIVATE_PATH", CRCL_DOC_ROOT."private/");
define( "CRCL_CONTENT_PATH", CRCL_DOC_ROOT."content/");
define( "CRCL_DESIGN_PATH", CRCL_DOC_ROOT."design/");
define( "CRCL_BLOCKS_PATH", CRCL_CONTENT_PATH."blocks/");

define( "CRCL_COMMON", "blocks");                       // common category
define( "CRCL_DM", "articles");                       // default module
define( "CRCL_DF", "index");                          // default function
define( "CRCL_DT", "index");                          // default template
define( "CRCL_ID", "an");                             // page id
define( "CRCL_MID", "m");                             // module id
define( "CRCL_TID", "t");                             // template id

define( "CRCLP", "CRCLP_");                             // parameter prefix



// Library & APIs loading  ------------------------------------------------
$sLibFile = CRCL_MODULE_PATH."lib.php";
if ( is_file( $sLibFile ) ) include_once( $sLibFile );
else  exit("Can't find library");

$sAuthAPI = CRCL_MODULE_PATH."auth/api.php";
$bAuthModule = is_file( $sAuthAPI );
if ( $bAuthModule  ) include_once( $sAuthAPI );

$sPriceAPI = CRCL_MODULE_PATH."shop/api.php";
$bPriceModule = is_file( $sPriceAPI );
if ( $bPriceModule ) include_once( $sPriceAPI );

// Page address processing  ------------------------------------------------
$sFunction = $_REQUEST[CRCL_ID] ? $_REQUEST[CRCL_ID] : CRCL_DF;
$sModule = $_REQUEST[CRCL_MID] ? $_REQUEST[CRCL_MID] : CRCL_DM;
$sTemplate = $_REQUEST[CRCL_TID] ? $_REQUEST[CRCL_TID] : false;

// Access control  ------------------------------------------------
if ( $bAuthModule  ) {
  if ( crcl_prohibited($sModule,$sFunction)  ) {
    $sModule = CRCL_DM;
    $sFunction = "403";
    }
}

// Path definition  ------------------------------------------------
define( "CRCL_MODULE_NAME", $sModule );
define( "CRCL_FUNC_NAME", $sFunction );
define( "CRCL_MODULE_CONTENT", CRCL_CONTENT_PATH.CRCL_MODULE_NAME."/");
define( "CRCL_PRIVATE_CONTENT", CRCL_PRIVATE_PATH.CRCL_MODULE_NAME."/");
define( "CRCL_PARAMETERS_FILE", CRCL_CONTENT_PATH.CRCL_MODULE_NAME."/parameters.txt");

// Parameters file processing  ------------------------------------------------

set_crcl_parameters();

// Template definition  ------------------------------------------------
if (!$sTemplate) {
  $sTemplate = crcl_parameter('template');
  $sTemplate = $sTemplate ? $sTemplate : CRCL_MODULE_NAME;
}
$sTemplateProc = CRCL_DESIGN_PATH."$sTemplate.php";


// Page preprocessing   ------------------------------------------------
$sInitFile =  CRCL_MODULE_PATH.CRCL_MODULE_NAME."/ini.php";
if ( is_file( $sInitFile ) )   include_once( $sInitFile );

// Page processing
ob_start();
if ( is_file( $sTemplateProc ) )  include_once( $sTemplateProc );
else include_once( CRCL_DESIGN_PATH.CRCL_DT.".php" );      
$sContent = ob_get_contents();
ob_end_clean();


//  OUTPUT  ------------------------------------------------------------
$xday=1;
$xstore=1;
$xlast=gmdate("D, d M Y H:i:s",floor(time()/$xday/$xstore)*$xday*$xstore)." GMT";
header("Last-Modified: $xlast");
header("Content-Type: text/html; charset=utf-8");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");

echo $sContent;

?>


