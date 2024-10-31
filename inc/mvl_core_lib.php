<?php

/* The functions in this library are also usable for other Plugins.*/

function mvl_getPHPVersion() {
	$version = phpversion();
	$version = preg_replace('/[^0-9.].*/', '', $version);
	return($version);
}

function mvl_getMySQLVersion() {
	$res = mysql_get_server_info();
	if (!$res) {
		return('N/A');
	} else {
		$version = preg_replace('/[^0-9.].*/', '', $res);
		return($version);
	}
}

function mvl_getApacheVersion() {
	$res = @apache_get_version();
	if (!$res) {
		return('N/A');
	} else {
		$res = preg_replace('/[^0-9.].*/', '', $res);
		return($res);
	}
}

function mvl_htmlEncode($string){
	return htmlspecialchars($string,ENT_QUOTES,"UTF-8",true);
}

function mvl_renewSubscription(){
	if(strtotime ( '-14 days' , strtotime (mvl_readOption(MVIS_LITE_OPT_NAME, 'expiryDate'))) < time())
		return true;
	else 
		return false;
}

/*
returns the file/directory-permissions in format 755 or 644 etc.
false, if not found
*/
function mvl_getPermissions($fileName, $numeric= 0) {
	clearstatcache();
	$perms = @fileperms($fileName);
	if (!$perms) {
		return(false);
	}
	if($numeric){
		return($perms);
	}
	$res = substr(sprintf('%o', $perms), -3);
	return($res);
}

function mvl_checkPermissions($perms, $should='') {
	//taken from http://php.net/manual/en/function.fileperms.php
	$info = '';
	//USER
	$info .= (($perms & 0x0100) ? 'r' : '-');
	$info .= (($perms & 0x0080) ? 'w' : '-');
	$info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x' ) : (($perms & 0x0800) ? 'S' : '-'));
	// Group
	$info .= (($perms & 0x0020) ? 'r' : '-');
	$info .= (($perms & 0x0010) ? 'w' : '-');
	$info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x' ) : (($perms & 0x0400) ? 'S' : '-'));
	
	// World
	$info .= (($perms & 0x0004) ? 'r' : '-');
	$info .= (($perms & 0x0002) ? 'w' : '-');
	$info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x' ) : (($perms & 0x0200) ? 'T' : '-'));
	//echo $info;
	return($info);
}


function mvl_array2HTML($a) {
	if (!is_array($a)) {
		return('');
	}
	$html = '';
	foreach($a as $k=>$v) {
		$html .= "<strong>$k:</strong> $v<br/>\r\n";
	}
	return($html);
}

function getRecursiveDirListing($dir) {
   $path = '';
   $stack[] = $dir;
   while ($stack) {
       $thisdir = array_pop($stack);
       if ($dircont = scandir($thisdir)) {
           $i=0;
           while (isset($dircont[$i])) {
               if ($dircont[$i] !== '.' && $dircont[$i] !== '..') {
                   $current_file = "{$thisdir}/{$dircont[$i]}";
                   if (is_file($current_file)) {
                   	   if(strpos($current_file, 'wp-includes/Text/Diff/Engine/shell.php') === false) //remove the false positive in normal setups
                       	$path[] = "{$thisdir}/{$dircont[$i]}";
                   } elseif (is_dir($current_file)) {
                        $path[] = "{$thisdir}/{$dircont[$i]}";
                       $stack[] = $current_file;
                   }
               }
               $i++;
           }
       }
   }
   return $path;
}


?>