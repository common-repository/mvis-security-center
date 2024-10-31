<?php

$mvl_checks_config = null;

function mvl_initChecksConfig() {
	global $mvl_checks_config;	
	$mvl_checks_config = mvl_readOption(MVIS_LITE_OPT_CHECKS_CONFIG, 'checks_config');
	if (!isset($mvl_checks_config['version'])) {
		include('mvl_checks_config.php');
		$mvl_checks_config = mvl_getChecksConfigFromFile();
		mvl_writeOption(MVIS_LITE_OPT_CHECKS_CONFIG, 'checks_config', $mvl_checks_config);
		mvl_writeOption(MVIS_LITE_OPT_NAME, 'checks_version', $mvl_checks_config['version']);
	}
}


function mvl_doAllChecks() {
	global $mvl_checks_config;		
	mvl_doFileChecks();
	mvl_doPermissionChecks();
	mvl_doPhpSettingsChecks();
	mvl_doBonusChecks();
	mvl_doBackendChecks();
	mvl_doUserChecks(1);
	$mvl_checks_result = $mvl_checks_config;
	$mvl_checks_result['lastRun'] = time();
	mvl_writeOption(MVIS_LITE_OPT_CHECKS_RESULT, 'checks_result', $mvl_checks_result);
}

function mvl_doCoreChecks() {
	global $mvl_checks_config;
	mvl_doFileChecks();
	mvl_doPermissionChecks();
	mvl_doPhpSettingsChecks();
	mvl_doBackendChecks();
	$mvl_checks_result = $mvl_checks_config;
	$mvl_checks_result['lastRun'] = time();
	mvl_writeOption(MVIS_LITE_OPT_CHECKS_RESULT, 'checks_result', $mvl_checks_result);
}

function mvl_doUpdateChecks(){
	mvl_manualSync();
}
/*
every check has a status:
0, null or n/a: check did not run
1: OK (green)
2: warning (yellow)
3: error (red)
*/


function mvl_doFileChecks() {
	global $mvl_checks_config;
	$fileChecks = &$mvl_checks_config['fileChecks'];

	foreach($fileChecks as &$check) {
		$fileNames = $check['fileName'];
		$shouldExist = ($check['shouldExist'] == 'yes'); 
		$type = @$check['type'];
		$check['state'] = 0;
		$check['violations'] = array();
		$max_severity = 0;
		isset($check['severity']) ? $severity = $check['severity'] : $severity = 3;
		if(!is_array($fileNames))
			$fileNames = array($fileNames);
		
		if($type == 'wpdangerousfiles'){
			$recDirListing = getRecursiveDirListing(untrailingslashit(ABSPATH));
			$violations = array();
		}
		
		if($type == 'wpconfigbackupfiles'){
			$dirListing = glob(untrailingslashit(ABSPATH) . "/*");
			$violations = array();
		}
		
		$multiple = count($fileNames);
		foreach ($fileNames as $fileName){
			$exists = mvl_checkFileExists($fileName);
			
			if ($type == 'wpconfig') { // special check for config file
				$exists2 = mvl_checkFileExists("..$fileName");
				if (!$exists && $exists2) {
					$check['state'] = 1;
				} else {
					$check['state'] = $severity;
				}
			}elseif($type == 'wpdangerousfiles'){
				if(isset($recDirListing) && is_array($recDirListing)){
					$violation = preg_grep("/.*\/$fileName\$/", $recDirListing);
					if(!empty($violation)){
						foreach($violation as $entry)
							array_push($violations, $entry);
					}
				}
			}elseif($type == 'wpconfigbackupfiles'){
				if(isset($dirListing) && is_array($dirListing)){
					$violation = preg_grep("/.*\/$fileName\$/", $dirListing);
					if(!empty($violation)){
						foreach($violation as $entry)
							array_push($violations, $entry);
					}
				}
			}else {
				if ($exists and (!$shouldExist)) {
					$check['state'] = $severity;
					array_push($check['violations'],$fileName); 
				}
				if ((!$exists) and (!$shouldExist)) {
					$check['state'] = 1;
				}
				if ($exists and $shouldExist) {
					$check['state'] = 1;
				}
				if ((!$exists) and $shouldExist) {
					$check['state'] = $severity;
					array_push($check['violations'],$fileName);
				}
				if($multiple > 1){
					if ($max_severity < $check['state'])
						$max_severity = $check['state'];
				}
			}	
		}
		if($multiple > 1)
				$check['state'] = $max_severity;
		
		if(!empty($violations)){
			$check['violations'] = array();
			$check['state'] = $severity;
			$unique_violations = array_unique(array_values($violations));
			foreach($unique_violations as $entry)
				array_push($check['violations'],$entry);
			$violations = array();
		}elseif(($type == 'wpdangerousfiles' || $type == 'wpconfigbackupfiles') && empty($violations)){
			$check['state'] = 1;
		}
	}

}

function mvl_doPermissionChecks() {
	global $mvl_checks_config;
	$permissionChecks = &$mvl_checks_config['permissionChecks'];

	foreach($permissionChecks as &$check) {
		$check['perms'] = '';
		$check['permsunix'] ='';
		$check['state'] = 1;
		$fileName = $check['fileName'];
		$shouldNotBe = @$check['shouldNotBe'];
		$type = @$check['type'];
		isset($check['severity']) ? $severity = $check['severity'] : $severity = 2;
		$check['violations'] = array('no','no','no');
		
		if ($type == 'wpconfig') // special check for config file
			if (!mvl_checkFileExists("$fileName"))
				if(mvl_checkFileExists("..$fileName"))
					$fileName = '..'.$fileName;
		
			$bitcounter = 0;
			$check['perms'] = mvl_getPermissions(ABSPATH . $fileName);
			
			$perms = mvl_checkPermissions(mvl_getPermissions(ABSPATH . $fileName, 1));
			$check['permsunix'] = $perms;
			for($i=0;$i<count($shouldNotBe);$i++){
				if($shouldNotBe[$i]){
					for($j=0;$j<strlen($shouldNotBe[$i]);$j++){
						if($shouldNotBe[$i][$j] != '-' && $shouldNotBe[$i][$j] == $perms[$bitcounter]){
							if(!is_array($check['violations'][$i]))
								$check['violations'][$i] = array();
							array_push($check['violations'][$i],$perms[$bitcounter]);
							$check['state'] = $severity;
						}
						$bitcounter+=1;
					}		
				}else
					$bitcounter+=3;
			}
	}
}


function mvl_doBackendChecks() {
	global $mvl_checks_config;
	$backendChecks = &$mvl_checks_config['backendChecks'];
	
	if(isset($_SERVER['AUTH_TYPE']) && isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])){
		$authHeader = $_SERVER['AUTH_TYPE'] . ' '. base64_encode($_SERVER['PHP_AUTH_USER'].':'.$_SERVER['PHP_AUTH_PW']);
		$params = array('headers' => array('Authorization' => $authHeader), 'redirection' => 0,'timeout' => MVL_API_TIMEOUT, 'sslverify' => MVL_CHECK_SSLVERIFY);
	}else
		$params = array('redirection' => 0,'timeout' => MVL_API_TIMEOUT, 'sslverify' => MVL_CHECK_SSLVERIFY);

	$http = new WP_Http();
	
	foreach($backendChecks as &$check) {
		$type = $check['type'];
		isset($check['severity']) ? $severity = $check['severity'] : $severity = 3;

		// The "secure" response must be a 302 with location https://SERVER/wp-login.php
		if ($type == 'https') {
			$redirhttps = false;
			$check['httpsenforced'] = false;
					
			if (defined('FORCE_SSL_ADMIN') && (FORCE_SSL_ADMIN === true)) $check['force'] = 1;
			elseif (defined('FORCE_SSL_LOGIN') && (FORCE_SSL_LOGIN === true))	$check['force'] = 2;
			else $check['force'] = 3;
						
			
			$url = mvl_getSiteUrl() . 'wp-admin/';
			$response = (array) @$http->request($url, $params);
			//print_r($response);
			if (isset($response['headers']['location']) and (!(strpos($response['headers']['location'], 'https://') === false)))
				$redirhttps = true;
				
			if (isset($response['response'])) {
				$code = $response['response']['code'];
				if (($code == 302) and ($redirhttps)) {
					$check['state'] = 1; // Redirect to login via https
					$check['httpsenforced'] = true;
				} else 
					$check['state'] = $severity;
			}else
				$check['state'] = 0;  

			if($check['force']==2 || $check['force']==3){
				$url = mvl_getSiteUrl(true) . 'wp-login.php';
				$response = (array) @$http->request($url, $params);
				if (isset($response['response'])) {
					$code = $response['response']['code'];
					if ($code != 200 ) {
						$check['state'] = $severity;
						$check['httpsenforced'] = false;
					}
				}else
					$check['state'] = $severity;  
			}
			if($check['force']==1 && $check['httpsenforced'] && $check['state'] != 0)
				$check['state'] = 1; //perfect state
			
			elseif(($check['force']==2 || $check['force'] ==3) && $check['httpsenforced'] && $check['state'] != 0)
				$check['state'] = 2; // good state, but FORCE_SSL_ADMIN should be set
		  	
		}
		
		if ($type == 'wwwauthenticate') {
			$url = mvl_getSiteUrl() . 'wp-admin/';
			$params = array('timeout' => MVL_API_TIMEOUT, 'sslverify' => MVL_CHECK_SSLVERIFY);
		  	$response = (array) @$http->request($url, $params);
			if (isset($response['response'])) {
				$code = $response['response']['code'];
				if (isset($response['headers']['www-authenticate']) && $code == 401)  {
					$check['state'] = 1; // Header set
					mvl_writeOption(MVIS_LITE_OPT_NAME, 'wwwauthenticate', 'enabled');
				} else {
					$check['state'] = $severity; // header not set
				}
			} else {
					$check['state'] = 0; //http error
			}
		}		

		if ($type == 'dirlisting'){
			$stringFound = false;
			$url = $check['url'];
			$strings = $check['strings'];
			$url2 = mvl_getSiteUrl() . $url;
			$response = (array) @$http->request($url2, $params);
			if (isset($response['response']) && isset($response['body'])) {
				foreach ($strings as $string) {
					if (strpos($response['body'], $string) != false) {
						$stringFound = true;
					}
				}
				if ($stringFound) {
					$check['state'] = $severity;
				} else {
					$check['state'] = 1;
				}
			}	else {
				$check['state'] = 0;
			}
		}		
	}
	//Do WP and DB Backendchecks too
	mvl_doWPBackendChecks();
	mvl_doDBBackendChecks();
}

function mvl_doWPBackendChecks() {
	global $mvl_checks_config;
	$WPbackendChecks = &$mvl_checks_config['WPbackendChecks'];
	
	foreach($WPbackendChecks as &$check) {
		$type = $check['type'];
		isset($check['severity']) ? $severity = $check['severity'] : $severity = 2;
		
		if ($type == 'userreg') {
			if (get_option('users_can_register')) {
				$check['state'] = $severity; //Users should not be able to register by default
				$role = get_option('default_role');
				if ($role != 'subscriber' && $role != 'contributor'){
					$check['state'] =3; //Highly critical because users can subscribe with privileged user rights.
					$check['roleviolation'] = $role;
				}
			} else {
				$check['state'] = 1;
			}
		}

		if ($type == 'wpdebug') {
			if (defined('WP_DEBUG') && (WP_DEBUG === false)) {
				$check['state'] = 1;
			} else {
				$check['state'] = $severity;
			}//TODO Enhance according to: http://codex.wordpress.org/Debugging_in_WordPress
		}
		
		if ($type == 'fileedit') {
			if ((defined('DISALLOW_FILE_EDIT') && (DISALLOW_FILE_EDIT === true))) {
				$check['state'] = 1;
			} elseif((defined('DISALLOW_FILE_EDIT') && (DISALLOW_FILE_EDIT === false)) || !defined('DISALLOW_FILE_EDIT')) {
				$check['state'] = $severity;
			}
		}
		if ($type == 'wpmime') {
			$check['state'] = 1;
			
			if(defined('ALLOW_UNFILTERED_UPLOADS') && ALLOW_UNFILTERED_UPLOADS === true){
				$check['state'] = $severity;
				$check['allow_unfiltered'] = true;
			}
			
			//Get check extensions
			$extensions = isset($check['extensions'])?$check['extensions']: '';
			
			if(is_array($extensions))
				$extensions = array_change_key_case($extensions,CASE_LOWER);
			
			//Get mime types
			$mime_types = get_allowed_mime_types();
			
			if(is_array($mime_types ))
				$mime_types = array_change_key_case($mime_types,CASE_LOWER);
			 
			//Check for violations
			foreach($extensions as $extension){
				if(isset($mime_types[$extension])){
					$check['state'] = $severity;
					if(!isset($check['violations']))
						$check['violations'] = array();
					array_push($check['violations'],$extension);
				}
			}
		}
	}
}

function mvl_doDBBackendChecks() {
	global $mvl_checks_config;
	$DBbackendChecks = &$mvl_checks_config['DBbackendChecks'];

	foreach($DBbackendChecks as &$check) {
		$type = $check['type'];
		isset($check['severity']) ? $severity = $check['severity'] : $severity = 3;

		if ($type == 'dbroot') {
		  if (defined('DB_USER') && (DB_USER === 'root')) {
				$check['state'] = $severity; 
		  } else {
				$check['state'] = 1; 
		  }
		}

		if ($type == 'dbprefix') {
			$db =& $GLOBALS['wpdb'];
			if ($db->prefix == 'wp_') {
				$check['state'] = $severity; // DB-Prefix set to default
			} else {
				$check['state'] = 1; // DB-Prefix not set to default
			}
		}
	}
}

function mvl_doPhpSettingsChecks() {
	global $mvl_checks_config;
	$phpSettingsChecks = &$mvl_checks_config['phpSettingsChecks'];

	foreach($phpSettingsChecks as &$check) {
		$setting = $check['setting'];
		$shouldBe = $check['shouldBe'];
		$type = isset($check['type']) ? $check['type'] : '';
		$check['state'] = 0;
		isset($check['severity']) ? $severity = $check['severity'] : $severity = 3;
		
		if($type == 'disabled_functions'){
			$check['state'] = 1;
			$disabled = explode(',', ini_get('disable_functions'));
			foreach ($disabled as $disableFunction)
				$is_disabled[] = trim($disableFunction);
			foreach($check['setting'] as $func){
				if (!in_array($func,$is_disabled) || function_exists($func)) {
					if(!isset($check['violations']))
						$check['violations'] = array();
					array_push($check['violations'],$func);
					$check['state'] = $severity;
				}
			}
		}else{
			$check['state'] = 1;
			if(!is_array($setting))
				$setting = array($setting);
				
			foreach($setting as $s) { 
				$value = ini_get($s);
				if ($value != $shouldBe) {
					$check['value'] = $value;
					if(!isset($check['violations']))
						$check['violations'] = array();
					array_push($check['violations'],$s);			
					$check['state'] = $severity;
				}
			}
		}
	}
}


function mvl_doBonusChecks() {
	global $mvl_checks_config;
	$bonusChecks = &$mvl_checks_config['bonusChecks'];

	foreach($bonusChecks as &$check) {
		$check['state'] = 2;
	} 
}



function mvl_doUserChecks($rerunCheck = 0) {
	global $mvl_checks_config;
	$userChecks = &$mvl_checks_config['userChecks'];
	foreach($userChecks as &$check) { //TODO: Invalid argument supplied for foreach()
		if(isset($check['type']) && $check['type']== 'username'){
			$usersData = mvl_readOption(MVIS_LITE_OPT_NAME, 'userData');
			if($rerunCheck != 1 && $usersData != '')
				return ($usersData);
			
			$userNames = $check['userNames'];
			$usersData = array();
			$db =&$GLOBALS['wpdb'];
		
			$users = $db->get_results("SELECT users.* FROM $db->users as users");
			foreach ($users as $user) {
				$count = 1;
				$userData['superAdmin'] = false;
				$userData['id'] = $user->ID;
				$userData['login'] = $user->user_login;
				$userData['email'] = $user->user_email;
				$userData['registered'] = $user->user_registered;
				$userData['risk'] = 1;
				$userData['weakPW'] = '';
				$userData['privileged'] = '';
				$userData['commonAdminName'] = '';
				$u = new WP_User($user->ID);
				$userData['roles'] = '';
				if (!empty($u->roles)) {
					foreach ($u->roles as $role) {
						//some plugins assign additional roles
						if(count($u->roles) == 1 || count($u->roles) == $count)
							$userData['roles'] .= ucwords($role);
						else
							$userData['roles'] .= ucwords($role) . ', ';						
						
						if ($role === 'administrator') {				
							$userName = $user->user_login;
							$userData['privileged'] = 2;
							if(function_exists('is_super_admin') && is_super_admin($user->ID))
								$userData['superAdmin'] = true;
							if (in_array($userName, $userNames)) {
								//common admin name is medium risk
								$userData['risk'] = 2;
								$userData['commonAdminName'] = 1;
							}
						}
						if ($role === 'author' || $role === 'editor')
							$userData['privileged'] = 1;
						$count +=1;
					}
				}
				global $password_list;
				if($password_list != ''){
					foreach($password_list as $password){
						if(wp_check_password($password, $user->user_pass,  $user->ID)){
							$userData['weakPW'] = 1;
							//authors and editors with weak passwords pose a great risk to the site.
							if($userData['risk']==1 && $userData['privileged'] == 1)
								$userData['risk']=3;
							//users with weak passwords provide a medium risk to the site.
							elseif($userData['risk']==1)
							$userData['risk']=2;
							//Admin users with common admin name and a weak password pose the greatest risk to the system.
							elseif($userData['commonAdminName']=1)
							$userData['risk']=3;
						}
					}
				}
				
				global $bruteforce_pw_list;
				if($bruteforce_pw_list != ''){
					foreach($bruteforce_pw_list as $bf_credential){
						$parts = explode("|", $bf_credential);
						if($parts[0] == $user->user_login ){
							if(wp_check_password($parts[1], $user->user_pass,  $user->ID)){
								$userData['weakPWBF'] = 1;
								//authors and editors with weak passwords pose a great risk to the site.
								if($userData['privileged'] == 1 || $userData['privileged'] == 2 || $userData['superAdmin'] == true)
									$userData['risk']=3;
								else
									$userData['risk']=2;
							}
						}
					}
				}
				
				$u2 = get_userdata($user->ID);
				$userData['firstname'] = $u2->user_firstname;
				$userData['lastname'] = $u2->user_lastname;
				$usersData[] = $userData;	
			}
			mvl_writeOption(MVIS_LITE_OPT_NAME, 'userData', $usersData);
			return($usersData);
		}	
	}
}


?>