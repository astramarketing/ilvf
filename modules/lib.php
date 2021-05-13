<?php
/**
 * Caracol CMS. <http://www.caracol.ru>
 * Copyright (C) 2005-2012 Eurosigma Group  <http://www.eurosigma.ru>
 *
 * This file is part of Caracol CMS.
 *
 */

if ( !defined('CRCL_ON')) exit("prohibited using");

//  Functions  ---------------------------------------------------------------

function crcl_block($name) {
	include(CRCL_BLOCKS_PATH."$name.htm");
} 

 function crcl_module() {
	 $sModuleProc = CRCL_MODULE_PATH.CRCL_MODULE_NAME."/index.php";
	 if ( is_file( $sModuleProc ) ) include($sModuleProc);
	 else {
		 if ( is_file( CRCL_MODULE_CONTENT.CRCL_FUNC_NAME.".htm" )  )  
			 include( CRCL_MODULE_CONTENT.CRCL_FUNC_NAME.".htm" );                     
		 else {    // Output 404 page
			 Header( "HTTP/1.0 404 Not Found" );
			 if ( is_file( CRCL_MODULE_CONTENT."404.htm" )  )
				 include( CRCL_MODULE_CONTENT."404.htm" );
			 else
				 include( CRCL_BLOCKS_PATH."404.htm" ); 
		}
	}  
}


function crcl_content($name) {
 if ( $name == 'current' ) {
   $sModuleProc = CRCL_MODULE_PATH.CRCL_MODULE_NAME."/index.php";
   if ( is_file( $sModuleProc ) ) 
     include($sModuleProc);
   else {
    if ( is_file( CRCL_MODULE_CONTENT.CRCL_FUNC_NAME.".htm" )  )  
       include( CRCL_MODULE_CONTENT.CRCL_FUNC_NAME.".htm" );                     
    else {    // Output 404 page
      Header( "HTTP/1.0 404 Not Found" );
      if ( is_file( CRCL_MODULE_CONTENT."404.htm" )  )
        include( CRCL_MODULE_CONTENT."404.htm" );
      else
        include( CRCL_DESIGN_PATH."404.htm" ); 
      }
    }  
   }
 else include(CRCL_CONTENT_PATH.CRCL_COMMON."/$name.htm");
 } 




function crcl_mail($sToEmail,$sFromEmail,$Subject,$Message) {
	$sHeaders = "From: $sFromEmail\r\n"."Reply-To: $sFromEmail\r\n"."MIME-Version: 1.0\r\n"."Content-Type: text/html; charset=UTF-8; format=flowed;";
return mail($sToEmail, $Subject, $Message, $sHeaders); 
}



function get_crcl_parameter($name) {

 if ( $name == 'title') {
    if ( is_file( CRCL_PARAMETERS_FILE ) ) { 
      $aParameters = parse_ini_file( CRCL_PARAMETERS_FILE, true );    
      if (  is_array($aParameters['index']) ) 
        return $aParameters['index']['title'];          
       }
   }
 }
 
function set_crcl_parameters() {
	define( "CRCL_C_PARAMETERS_FILE", CRCL_CONTENT_PATH.CRCL_COMMON."/parameters.txt");
	if ( is_file( CRCL_C_PARAMETERS_FILE ) ) { 
		$aAllParameters = parse_ini_file( CRCL_C_PARAMETERS_FILE, true ); 
		   $aParameters = $aAllParameters['index'];
		foreach ($aParameters as $gkey => $gvalue) 
				define(CRCLP.$gkey,$gvalue); 
	}
	if ( is_file( CRCL_PARAMETERS_FILE ) ) { 
		$aParameters = parse_ini_file( CRCL_PARAMETERS_FILE, true );    
		foreach ($aParameters as $gkey => $gvalue) { 
			if (  is_array($aParameters[$gkey]) ) {
				if (  $gkey == CRCL_FUNC_NAME ) 
				foreach ($aParameters[CRCL_FUNC_NAME] as $key => $value) {
					define(CRCLP.CRCL_MODULE_NAME."_".CRCL_FUNC_NAME."_".$key,$value);
				}
			}
			else 
				define(CRCLP.CRCL_MODULE_NAME."_".$gkey,$gvalue); 
		}          
	}
	return true;
} 
  
function crcl_parameter($name) {
 
	if ( defined(CRCLP.CRCL_MODULE_NAME.'_'.CRCL_FUNC_NAME.'_'.$name) )
		return constant(CRCLP.CRCL_MODULE_NAME.'_'.CRCL_FUNC_NAME.'_'.$name);
	if ( defined(CRCLP.CRCL_MODULE_NAME.'_'.$name) )
		return constant(CRCLP.CRCL_MODULE_NAME.'_'.$name);	
	if ( defined(CRCLP.$name) )
		return constant(CRCLP.$name);	
  return false;
  }



function crcl_file($out,$file) {
// save $out to $file or create new $file
  if ( !$file ) return "не задано имя файла";
  if (!$fp = fopen($file, 'a'))  return "не могу открыть";  
  if (flock($fp, LOCK_EX)) { // выполнить эксклюзивное запирание     
    ftruncate ($fp,0);     //УДАЛЯЕМ СОДЕРЖИМОЕ ФАЙЛА
    if (fputs($fp, $out) === false) return "проблемы при записи"; 
    fflush ($fp);         //очищение файлового буфера и запись в файл  
    flock ($fp,LOCK_UN);  //снятие блокировки
    }
  else return "не могу запереть";    

  if (fclose($fp))  return false; 
  else return "не могу закрыть";  
  }

function crcl_get_object($sFile) {
	if ( is_file( $sFile ) ) {
	  $aObject = parse_ini_file( $sFile, true );
	  return $aObject; 
	  }
	return false; 
}


function crcl_par($arr) {
// convert parameters $arr to string
  $out = "";
  foreach ($arr as $section => $card) {
    if ( is_array($card) ) {
      $out .= "[".$section."]\n"; 
      foreach ($card as $parameter => $value) {
        $v1 = trim($value);
        $v1 = str_replace( '\\"',"'",$v1 );
        $out .= "$parameter = \"$v1\"\n";
		    }
		  }
		 else {
		   $v1 = trim($card);
       $v1 = str_replace( '\\"',"'",$v1 );
       $out .= "$section = \"$v1\"\n"; 
       }   
    } 
  return $out;
  }

function menuout($str) {
// menu $str output
  $out = "<ul>";
  $aParametersFile = CRCL_DESIGN_PATH."parameters.txt";
  if ( is_file( $aParametersFile ) ) $aParameters = parse_ini_file( $aParametersFile, true );
  foreach ($aParameters[$str] as $key => $value) {
    $out .= "<li><a href=?an=$key>$value</a></li>"; 
    }
  $out .= "</ul>";
  return $out;
  }

function crcl_alert($sAlert) {
    return "<h3>$sAlert</h3>";
    }
  


function encodestring($st) 
{ 

$arr = array(
' ' => '_', 
'А' => 'A', 
'Б' => 'B', 
'В' => 'V', 
'Г' => 'G', 
'Д' => 'D', 
'Е' => 'E', 
'Ё' => 'JO', 
'Ж' => 'ZH', 
'З' => 'Z', 
'И' => 'I', 
'Й' => 'JJ', 
'К' => 'K', 
'Л' => 'L', 
'М' => 'M', 
'Н' => 'N', 
'О' => 'O', 
'П' => 'P', 
'Р' => 'R', 
'С' => 'S', 
'Т' => 'T', 
'У' => 'U', 
'Ф' => 'F', 
'Х' => 'KH', 
'Ц' => 'C', 
'Ч' => 'CH', 
'Ш' => 'SH', 
'Щ' => 'SHH', 
'Ъ' => '"', 
'Ы' => 'Y', 
'Ь' => '\'', 
'Э' => 'EH', 
'Ю' => 'JU', 
'Я' => 'JA', 
'а' => 'a', 
'б' => 'b', 
'в' => 'v', 
'г' => 'g', 
'д' => 'd', 
'е' => 'e', 
'ё' => 'jo', 
'ж' => 'zh', 
'з' => 'z', 
'и' => 'i', 
'й' => 'jj', 
'к' => 'k', 
'л' => 'l', 
'м' => 'm', 
'н' => 'n', 
'о' => 'o', 
'п' => 'p', 
'р' => 'r', 
'с' => 's', 
'т' => 't', 
'у' => 'u', 
'ф' => 'f', 
'х' => 'kh', 
'ц' => 'c', 
'ч' => 'ch', 
'ш' => 'sh', 
'щ' => 'shh', 
'ъ' => '"', 
'ы' => 'y', 
'ь' => '\'', 
'э' => 'eh', 
'ю' => 'ju', 
'я' => 'ja'
); 
$key = array_keys($arr);
$val = array_values($arr);
$transl = str_replace($key,$val,$st ); 

    return nl2br(htmlspecialchars($transl)); 
} 

function check_email($sEmail) {
  return preg_match("|^[-0-9a-z_]+@[-0-9a-z^\.]+\.[a-z]{2,6}$|i",$sEmail) ;
  }

function check_name($sName) {
  return  preg_match("/^[а-я-\s]+$/ui",$sName);
  }
  
function check_tel($sTel) {
  return ! preg_match('/[^\d\+\s\(\)\-]/',$sTel);
  }

function clean_input($sStr) {
    return trim(htmlspecialchars($sStr));
    }

function download_pdf($filename) {
		  
//		  	  download_pdf("book.pdf");
		  
	    if (file_exists($filename)) {
	      /* Если файл существует */
	      header("Content-Disposition: attachment; filename='" . basename($filename) . "';"); // Указываем имя при сохранении в браузере
	        echo file_get_contents($filename); // Отдаём файл пользователю на скачивание
	    }
	    else echo "Not Found"; // Если файла не существует
	  }




?>
