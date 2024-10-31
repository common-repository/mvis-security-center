<?php

function mvl_setHTTPError($value = true) {
	mvl_writeOption(MVIS_LITE_OPT_NAME, 'httpError', $value);
	return(false);
}

function mvl_updateSiteDetailsAndSync($siteDetails){
	if(!is_array($siteDetails))
		return false;
	if(!isset($siteDetails['id']) || !isset($siteDetails['name']) || !isset($siteDetails['status']))
		return false;
	
	mvl_writeOption(MVIS_LITE_OPT_NAME, 'siteDetails', $siteDetails);
	
	if($siteDetails['status'] == 'ACTIVE'){
		global $mvlState;
		$mvlState->siteActive = true;
		mvl_manualSync();
		return true;
	}else
		return false;
		
}

function mvl_Logout($nonce){
	if(mvl_verifyNonce($nonce,'mvl_logout')){
		mvl_clearData();
	}else
		return  __('Security Check failed, please try again!',MVLTD);	
}

function mvl_clearData(){
	mvl_deleteOption(MVIS_LITE_OPT_NAME, 'userName');
	mvl_deleteOption(MVIS_LITE_OPT_NAME, 'authToken');
	mvl_deleteOption(MVIS_LITE_OPT_NAME, 'siteDetails');
	mvl_deleteOption(MVIS_LITE_OPT_SITEALERTS, 'site_alerts');
	mvl_deleteOption(MVIS_LITE_OPT_VULNSTATUS, 'vuln_status');
	mvl_deleteOption(MVIS_LITE_OPT_NAME, 'vulnstatus_version');
}

function mvl_Login($email='',$password=''){
	if($email == '' || $password == '')
		return __('No username or password was supplied.',MVLTD);
	
	mvl_deleteOption(MVIS_LITE_OPT_NAME, 'userName');
	mvl_deleteOption(MVIS_LITE_OPT_NAME, 'authToken');
		
	$apiRes = mvl_getAuthToken($code, $email, $password);
	if ($code <> 200) {
		$err = mvl_getApiError('getAuthToken', $code);
		return $err;
	}
	
	$authToken = $apiRes;
	mvl_writeOption(MVIS_LITE_OPT_NAME, 'userName', $email);
	mvl_writeOption(MVIS_LITE_OPT_NAME, 'authToken', $authToken);
	
	$siteId = false;
	$apiRes = mvl_getSitesList($code, $email, $authToken);
	if ($code == 200 || $code == 404){
		if($code != 404){
			foreach($apiRes as $site) {
				//TODO: does a https siteurl make a differnce?
				//TODO: Network installations
				//TODO: Is this always accurate?
				if ($site['name'] == mvl_getSiteUrl()) {
					$siteId = $site['id'];
				}
			}
		}
		if ($siteId <> false) {
			$apiRes = mvl_getSiteDetails($code, $email, $authToken, $siteId);
			if ($code == 200) {
				if (!mvl_updateSiteDetailsAndSync($apiRes))
					return 'inactive';
				else 
					return 'active';
			}else{
				$err = mvl_getApiError('getSiteDetails', $code);
				return $err;
			}
		}else{//Registered user has no site yet, create Site
			  //This is either because he logged in at a new site, or 
			$thisSiteDetails = mvl_getThisSiteDetails(true);
			$apiRes = mvl_createSite($code, $email, $authToken, $thisSiteDetails);
			if ($code <> 201) {
				$err = mvl_getApiError('createSite', $code);
				return($err);
			}
			if ($code == 201) {
				$siteDetails = array();
				$siteDetails['id'] = $apiRes['id'];
				$siteDetails['name'] = $apiRes['name'];
				$siteDetails['status'] = $apiRes['status'];
				mvl_writeOption(MVIS_LITE_OPT_NAME, 'siteDetails', $siteDetails);
				return ('created');
				//Continue with the subscription process
				//or show a status message to the user that the site was created and needs to be subscribed
				
				
			}
		}
	}else{//Error communicating with the server, return error message
		$err = mvl_getApiError('getSitesList', $code);
		return $err;
	}
}

function mvl_resend_Verification(&$code, $username, $captchaCode, $captchaId){
	$url = MVIS_LITE_API_URL . '/register/' . $username . '?resendverificationcode&captchaId=' . urlencode($captchaId) . '&captchaCode=' . urlencode(strtoupper($captchaCode));
	$http = new WP_Http();
	$params = array('method' => 'GET', 'timeout' => MVL_API_TIMEOUT, 'sslverify' => MVL_API_SSLVERIFY);
	$response = (array) @$http->request($url, $params);
	mvl_apiDebug($response);
	if (isset($response['response'])) {
		$code = isset($response['response']['code'])? $response['response']['code']: 0;
		mvl_setHTTPError(false);
		
	}else {
		$code = 0;
		return(mvl_setHTTPError());
	}
}
/*
returns captcha as base64 or false
id: captcha-id
code: either
http-Response
0: http-Error
-1: error decoding json response
*/
function mvl_get_captcha(&$code, &$id) {
	$url = MVIS_LITE_API_URL . '/captchas/';
	$http = new WP_Http();	
	$params = array('method' => 'GET', 'timeout' => MVL_API_TIMEOUT, 'sslverify' => MVL_API_SSLVERIFY);	
  	$response = (array) @$http->request($url, $params);
  	mvl_apiDebug($response);	
	if (isset($response['response'])) {
		$code = $response['response']['code'];
		$body = $response['body'];
		$id = isset($response['headers']['x-captcha-id'])? $response['headers']['x-captcha-id'] : 0;
		$captcha = base64_encode($body);
		mvl_apiDebug($captcha);
		if ($captcha == null) {
			$code = -1;
			return(false);
		}
		mvl_setHTTPError(false);
		return($captcha);
	}else {
		$code = 0;
		return(mvl_setHTTPError());
	}
}


/*
returns json object or false
code: either
http-Response
0: http-Error
-1: error decoding json response
*/
function mvl_get_seccheckconfig(&$code) {
	$url = MVIS_LITE_API_URL . '/info/seccheckconfig?platform=wordpress';
	$http = new WP_Http();	
	$params = array('method' => 'GET', 'timeout' => MVL_API_TIMEOUT, 'sslverify' => MVL_API_SSLVERIFY);	
  $response = (array) @$http->request($url, $params);
  mvl_apiDebug($response);	
	if (isset($response['response'])) {
		$code = $response['response']['code'];
		$body = $response['body'];
		$secCheckConfig = json_decode($body, true);
		mvl_apiDebug($secCheckConfig);
		if ($secCheckConfig == null) {
			$code = -1;
			return(false);
		}
		mvl_setHTTPError(false);
		return($secCheckConfig);
	}else {
		$code = 0;
		return(mvl_setHTTPError());
	}
}

/*
returns json object or false
code: either
http-Response
0: http-Error
-1: error decoding json response
*/
function mvl_get_versions(&$code) {
	$url = MVIS_LITE_API_URL . '/info/versions?platform=wordpress';
	$http = new WP_Http();
	$body = mvl_getMVLRequest(true);
	$params = array('method' => 'POST', 'body' => $body, 'timeout' => MVL_API_TIMEOUT, 'sslverify' => MVL_API_SSLVERIFY);
  $response = (array) @$http->request($url, $params);
  mvl_apiDebug($response);	
	if (isset($response['response'])) {
		mvl_setHTTPError(false);
		$code = $response['response']['code'];
		$body = $response['body'];
		$versions = json_decode($body, true);
		mvl_apiDebug($versions);
		if ($versions == null) {
			$code = -1;
			return(false);
		}
		return($versions);
	}else {
		$code = 0;
		return(mvl_setHTTPError());
	}
}


/*
returns json object or false
code: either
http-Response
0: http-Error
-1: error decoding json response
*/
function mvl_get_vulnstatus(&$code) {
	$url = MVIS_LITE_API_URL . '/info/vulnstatus';
	$http = new WP_Http();
	$body = mvl_getMVLRequest(true);
	$params = array('method' => 'POST', 'body' => $body, 'timeout' => MVL_API_TIMEOUT, 'sslverify' => MVL_API_SSLVERIFY);
  $response = (array) @$http->request($url, $params);

  mvl_apiDebug($response);
	if (isset($response['response'])) {
		$code = $response['response']['code'];
		$body = $response['body'];
		mvl_setHTTPError(false);
		if ($body == '[]') {
			$vulnstatus = array();
		} else {
			$vulnstatus = json_decode($body, true);
			mvl_apiDebug($vulnstatus);
			if ($vulnstatus == null) {
				$code = -1;
				return(false);
			}
		}
		return($vulnstatus);
	}else {
		$code = 0;
		return(mvl_setHTTPError());
	}
}


/*
returns userDetails or validationFailure as json or false
code: either
http-Response
0: http-Error
-1: error decoding json response
*/

function mvl_registerUser(&$code, $username, $password, $captchaId, $captchaCode, $organisation = '', $firstname = '', $lastname ='') {
	$url = MVIS_LITE_API_URL . '/register/';
	$http = new WP_Http();	
	$body = array('username' => urlencode($username), 'password' => urlencode($password), 'captchaId' => urlencode($captchaId), 'captchaCode' => urlencode(strtoupper($captchaCode)), 'organisation' => urlencode($organisation), 'firstname' => urlencode($firstname), 'lastname' => urlencode($lastname));
	$params = array('method' => 'POST', 'body' => $body, 'timeout' => MVL_API_TIMEOUT, 'sslverify' => MVL_API_SSLVERIFY);
  	$response = (array) @$http->request($url, $params);
  	//print_r($response);
	mvl_apiDebug($response);
	if (isset($response['response'])) {
		$code = $response['response']['code'];
		mvl_setHTTPError(false);
		if ($code == 201) {
			$body = $response['body'];
			$userDetails = json_decode($body, true);
			mvl_apiDebug($userDetails);
			if ($userDetails == null) {
				$code = -1;
				return(false);
			}
			return($userDetails);
		}
		if ($code == 412) {
			$body = $response['body'];
			$validationFailure = json_decode($body, true);
			mvl_apiDebug($validationFailure);
			if ($validationFailure == null) {
				$code = -1;
				return(false);
			}
			return($validationFailure);
		}
		return(false);		
	}else {
		$code = 0;
		return(mvl_setHTTPError());
	}
}


/*
returns userDetails as json object or false
code: either
http-Response
0: http-Error
-1: error decoding json response
*/
function mvl_verifyUser(&$code, $username, $verification) {
	$url = MVIS_LITE_API_URL . '/register/' . urlencode($username) . '?verification=' . urlencode($verification);
	$http = new WP_Http();
	$params = array('method' => 'PUT', 'timeout' => MVL_API_TIMEOUT, 'sslverify' => MVL_API_SSLVERIFY); 
  $response = (array) @$http->request($url, $params);
  mvl_apiDebug($response);	
	if (isset($response['response'])) {
		$code = $response['response']['code'];
		if ($code == 200) {
			$body = $response['body'];
			$userDetails = json_decode($body, true);
			mvl_apiDebug($userDetails);
			if ($userDetails == null) {
				$code = -1;
				return(false);
			}
			mvl_setHTTPError(false);
			return($userDetails);
		}
	}else {
		$code = 0;
		return(mvl_setHTTPError());
	}
}



/*
returns authToken as string or false
code: either
http-Response
0: http-Error
-1: error reading authToken from response
*/

function mvl_getAuthToken(&$code, $username, $password) {
	$url = MVIS_LITE_API_URL . '/register/?username=' . urlencode($username) . '&password=' . urlencode($password);

	$http = new WP_Http();
	$params = array('method' => 'GET', 'timeout' => MVL_API_TIMEOUT, 'sslverify' => MVL_API_SSLVERIFY); 
  $response = (array) @$http->request($url, $params);
  mvl_apiDebug($response);	
	if (isset($response['response'])) {
		$code = $response['response']['code'];
		if ($code == 200) {
			$authToken = $response['body'];
			mvl_apiDebug($authToken);
			if ($authToken == '') {
				$code = -1;
				return(false);
			}
			mvl_setHTTPError(false);
			return($authToken);
		}
	}else {
		$code = 0;
		return(mvl_setHTTPError());
	}
}

function mvl_forgotPassword(&$code, $username, $captchaCode, $captchaId) {
	$url = MVIS_LITE_API_URL . '/register/' . urlencode($username) . '?forgotpassword&captchaId=' . urlencode($captchaId) . '&captchaCode=' . urlencode(strtoupper($captchaCode));
	$http = new WP_Http();
	$params = array('method' => 'GET', 'timeout' => MVL_API_TIMEOUT, 'sslverify' => MVL_API_SSLVERIFY);
	$response = (array) @$http->request($url, $params);
	mvl_apiDebug($response);
	if (isset($response['response'])) {
		$code = $response['response']['code'];
		mvl_setHTTPError(false);
	}else {
		$code = 0;
		return(mvl_setHTTPError());
	}
}


/*
returns userDetails as json object or false
code: either
http-Response
0: http-Error
-1: error decoding json response
*/
function mvl_getUserDetails(&$code, $username, $authToken) {
	$url = MVIS_LITE_API_URL . '/users/' . urlencode($username);
	
	$http = new WP_Http();
	$headers = array( 'Authorization' => 'Basic '.base64_encode("$username:$authToken") );
	$params = array('method' => 'GET', 'headers' => $headers, 'timeout' => MVL_API_TIMEOUT, 'sslverify' => MVL_API_SSLVERIFY); 
	
  	$response = (array) @$http->request($url, $params);
  	mvl_apiDebug($response);	
	
	if (isset($response['response'])) {
		$code = $response['response']['code'];
		if ($code == 200) {
			$body = $response['body'];
			$userDetails = json_decode($body, true);
			mvl_apiDebug($userDetails);
			if ($userDetails == null) {
				$code = -1;
				return(false);
			}
			mvl_setHTTPError(false);
			mvl_writeOption(MVIS_LITE_OPT_NAME, 'userDetails', $userDetails);
		}
	}else {
		$code = 0;
		return(mvl_setHTTPError());
	}
}



/*
returns true or validationFailure as json object or false
code: either
http-Response
0: http-Error
-1: error decoding json response
*/
function mvl_changePWD(&$code, $username, $authToken, $oldpwd, $newpwd) {
	$url = MVIS_LITE_API_URL . '/users/' . urlencode($username) . '?changepwd';
	
	$http = new WP_Http();
	$body = array('oldpwd' => urlencode($oldpwd), 'newpwd' => urlencode($newpwd));
	$headers = array( 'Authorization' => 'Basic '.base64_encode("$username:$authToken") );
	$params = array('method' => 'PUT', 'body' => $body, 'headers' => $headers, 'timeout' => MVL_API_TIMEOUT, 'sslverify' => MVL_API_SSLVERIFY); 
  $response = (array) @$http->request($url, $params);
  mvl_apiDebug($response);	
	if (isset($response['response'])) {
		$code = $response['response']['code'];
		if ($code == 412) {
			$body = $response['body'];
			$validationFailure = json_decode($body, true);
			mvl_apiDebug($validationFailure);
			if ($validationFailure == null) {
				$code = -1;
				return(false);
			}
			mvl_setHTTPError(false);
			return($validationFailure);
		}
		if ($code == 200) {
			return(true);
		}
	}else {
		$code = 0;
		return(mvl_setHTTPError());
	}
}


/*
returns true or false
code: either
http-Response
0: http-Error
-1: error decoding json response
*/
function mvl_deleteUser(&$code, $username, $authToken) {
	$url = MVIS_LITE_API_URL . '/users/' . urlencode($username);
	$http = new WP_Http();
	$headers = array( 'Authorization' => 'Basic '.base64_encode("$username:$authToken") );
	$params = array('method' => 'DELETE', 'headers' => $headers, 'timeout' => MVL_API_TIMEOUT, 'sslverify' => MVL_API_SSLVERIFY); 
  $response = (array) @$http->request($url, $params);
  mvl_apiDebug($response);	
	if (isset($response['response'])) {
		mvl_setHTTPError(false);
		$code = $response['response']['code'];
		if ($code == 200) {
			return(true);
		} else {
			return(false);
		}
	}else {
		$code = 0;
		return(mvl_setHTTPError());
	}
}


/*
returns sitesList as json object or false
code: either
http-Response
0: http-Error
-1: error decoding json response
*/
function mvl_getSitesList(&$code, $username, $authToken) {
	$url = MVIS_LITE_API_URL . '/sites/';
	$http = new WP_Http();
	$headers = array( 'Authorization' => 'Basic '.base64_encode("$username:$authToken") );
	$params = array('method' => 'GET', 'headers' => $headers, 'timeout' => MVL_API_TIMEOUT, 'sslverify' => MVL_API_SSLVERIFY); 
  $response = (array) @$http->request($url, $params);
  mvl_apiDebug($response);	
	if (isset($response['response'])) {
		$code = $response['response']['code'];
		if ($code == 200) {
			$body = $response['body'];
			$sitesList = json_decode($body, true);
			mvl_apiDebug($sitesList);
			if ($sitesList == null) {
				$code = -1;
				return(false);
			}
			mvl_setHTTPError(false);
			return($sitesList);
		}
		return(false);
	}else {
		$code = 0;
		return(mvl_setHTTPError());
	}
}

/*
returns siteDetails as json object or false
code: either
http-Response
0: http-Error
-1: error decoding json response
*/
function mvl_getSiteDetails(&$code, $username, $authToken, $siteId) {
	$url = MVIS_LITE_API_URL . '/sites/' . urlencode($siteId);
	$http = new WP_Http();
	$headers = array( 'Authorization' => 'Basic '.base64_encode("$username:$authToken") );
	$params = array('method' => 'GET', 'headers' => $headers, 'timeout' => MVL_API_TIMEOUT, 'sslverify' => MVL_API_SSLVERIFY); 
  $response = (array) @$http->request($url, $params);
  mvl_apiDebug($response);	
	if (isset($response['response'])) {
		$code = $response['response']['code'];
		if ($code == 200) {
			$body = $response['body'];
			$siteDetails = json_decode($body, true);
			mvl_apiDebug($siteDetails);
			if ($siteDetails == null) {
				$code = -1;
				return(false);
			}
			if(isset($siteDetails['expiryDate']))
				mvl_writeOption(MVIS_LITE_OPT_NAME, 'expiryDate',$siteDetails['expiryDate']);
			mvl_setHTTPError(false);
			return($siteDetails);
		}
		return(false);
	}else {
		$code = 0;
		return(mvl_setHTTPError());
	}
}


/*
returns siteDetails or validationFailure as json object or false
code: either
http-Response
0: http-Error
-1: error decoding json response
*/
function mvl_createSite(&$code, $username, $authToken, $siteDetails) {
	$url = MVIS_LITE_API_URL . '/sites/';
	$http = new WP_Http();
	$headers = array( 'Authorization' => 'Basic '.base64_encode("$username:$authToken"));
	$params = array('method' => 'POST', 'headers' => $headers, 'body' => $siteDetails, 'redirection' => 0, 'timeout' => MVL_API_TIMEOUT, 'sslverify' => MVL_API_SSLVERIFY); 
  $response = (array) @$http->request($url, $params);
  mvl_apiDebug($response);
	if (isset($response['response'])) {
		$code = $response['response']['code'];
		mvl_setHTTPError(false);
		if ($code == 201) {
			$body = $response['body'];
			$siteDetails = json_decode($body, true);
			mvl_apiDebug($siteDetails);
			if ($siteDetails == null) {
				$code = -1;
				return(false);
			}
			return($siteDetails);
		}
		if ($code == 412) {
			$body = $response['body'];
			$validationFailure = json_decode($body, true);
			mvl_apiDebug($validationFailure);
			if ($validationFailure == null) {
				$code = -1;
				return(false);
			}
			return($validationFailure);
		}
		return(false);
	}else {
		$code = 0;
		return(mvl_setHTTPError());
	}
}


function mvl_updateUserSettings(&$code, $username, $authToken, $userSettings) {
	$url = MVIS_LITE_API_URL . '/users/' . urlencode($username);
	$http = new WP_Http();
	$headers = array( 'Authorization' => 'Basic '.base64_encode("$username:$authToken"));
	$params = array('method' => 'PUT', 'headers' => $headers, 'body' => $userSettings, 'timeout' => MVL_API_TIMEOUT, 'sslverify' => MVL_API_SSLVERIFY);
	$response = (array) @$http->request($url, $params);
	mvl_apiDebug($response);
	if (isset($response['response'])) {
		$code = $response['response']['code'];
		mvl_setHTTPError(false);
		if ($code == 200) 
			return (true);
		return(false);
	}else {
		$code = 0;
		return(mvl_setHTTPError());
	}

}

/*
returns sitesDetails or validationFailure as json object or false
code: either
http-Response
0: http-Error
-1: error decoding json response
*/
function mvl_updateSite(&$code, $username, $authToken, $siteId, $siteDetails) {
	$url = MVIS_LITE_API_URL . '/sites/' . urlencode($siteId);
	$http = new WP_Http();
	$headers = array( 'Authorization' => 'Basic '.base64_encode("$username:$authToken"));
	$params = array('method' => 'PUT', 'headers' => $headers, 'body' => $siteDetails, 'timeout' => MVL_API_TIMEOUT, 'sslverify' => MVL_API_SSLVERIFY); 
  $response = (array) @$http->request($url, $params);
  mvl_apiDebug($response);	
	if (isset($response['response'])) {
		$code = $response['response']['code'];
		mvl_setHTTPError(false);
		if ($code == 200) {
			$body = $response['body'];
			$siteDetails = json_decode($body, true);
			mvl_apiDebug($siteDetails);
			if ($siteDetails == null) {
				$code = -1;
				return(false);
			}
			return($siteDetails);
		}
		if ($code == 412) {
			$body = $response['body'];
			$validationFailure = json_decode($body, true);
			mvl_apiDebug($validationFailure);
			if ($validationFailure == null) {
				$code = -1;
				return(false);
			}
			return($validationFailure);
		}
		return(false);
	}else {
		$code = 0;
		return(mvl_setHTTPError());
	}
}


/*
returns true or false
code: either
http-Response
0: http-Error
*/
function mvl_deleteSite(&$code, $username, $authToken, $siteId) {
	$url = MVIS_LITE_API_URL . '/sites/' . urlencode($siteId);
	$http = new WP_Http();
	$headers = array( 'Authorization' => 'Basic '.base64_encode("$username:$authToken"));
	$params = array('method' => 'DELETE', 'headers' => $headers, 'timeout' => MVL_API_TIMEOUT, 'sslverify' => MVL_API_SSLVERIFY); 
  $response = (array) @$http->request($url, $params);
  mvl_apiDebug($response);	
	if (isset($response['response'])) {
		$code = $response['response']['code'];
		mvl_setHTTPError(false);
		return(true);
	}else {
		$code = 0;
		return(mvl_setHTTPError());
	}
}


/*
returns siteAlerts as json object or false
code: either
http-Response
0: http-Error
-1: error decoding json response
*/
function mvl_getSiteAlerts(&$code, $username, $authToken, $siteId) {
	$url = MVIS_LITE_API_URL . '/sites/' . urlencode($siteId) . '/alerts/';
	$http = new WP_Http();
	$headers = array( 'Authorization' => 'Basic '.base64_encode("$username:$authToken") );
	$params = array('method' => 'GET', 'headers' => $headers, 'timeout' => MVL_API_TIMEOUT, 'sslverify' => MVL_API_SSLVERIFY); 
  $response = (array) @$http->request($url, $params);
  mvl_apiDebug($response);	
	if (isset($response['response'])) {
		$code = $response['response']['code'];
		mvl_setHTTPError(false);
		if ($code == 200) {
			$body = $response['body'];
			if ($body == '[]') {
				$siteAlerts = array();
			} else {
				$siteAlerts = json_decode($body, true);
				mvl_apiDebug($siteAlerts);
				if ($siteAlerts == null) {
					$code = -1;
					return(false);
				}
			}
			return($siteAlerts);
		}
		return(false);
	}else {
		$code = 0;
		return(mvl_setHTTPError());
	}
}

/*
returns json data or false
code: either
http-Response
0: http-Error
-1: error decoding json response
*/
function mvl_getJsonData(&$code, $url) {
	$http = new WP_Http();
	$params = array('method' => 'GET', 'timeout' => MVL_API_TIMEOUT, 'sslverify' => MVL_API_SSLVERIFY); 
  $response = (array) @$http->request($url, $params);
  mvl_apiDebug($response);	
	if (isset($response['response'])) {
		$code = $response['response']['code'];
		mvl_setHTTPError(false);
		if ($code == 200) {
			$body = $response['body'];
			$data = json_decode($body, true);
			mvl_apiDebug($data);
			if ($data == null) {
				$code = -1;
				return(false);
			}
			return($data);
		}
		return(false);
	}else {
		$code = 0;
		return(mvl_setHTTPError());
	}
}

function mvl_getServerURLPayPal($siteId, $authToken, $currency, $product, $callBackUrlSuccess, $callBackUrlError) {
	$authParam = mvl_genPplAuth($siteId, $authToken);
	if($currency == '' && $product == '')
		$url = MVIS_LITE_PP_URL . '/cart.jsp?site=' . urlencode($siteId) . '&auth=' . urlencode($authParam);
	else
		$url = MVIS_LITE_PP_URL . '/cart.jsp?site=' . urlencode($siteId) . '&auth=' . urlencode($authParam) . '&currency=' . urlencode($currency) . '&productId=' . urlencode(intval($product)); 
	$url .= '&callBackUrlSuccess=' . urlencode($callBackUrlSuccess);
	$url .= '&callBackUrlError=' . urlencode($callBackUrlError);
	return($url);
}


/*
generates the auth param for the cart.jsp page
incl. base64 encoding and urlencoding
*/
function mvl_genPplAuth($siteId, $authToken) {
	$content = "$siteId:$authToken";
	$hash = md5($content, true);
	$base64 = base64_encode($hash);
	return($base64);
}


function mvl_apiDebug($d) {
	if (MVL_API_DEBUG) {
		echo("<pre>");
		var_dump($d);
		echo("</pre>");
	}
}

function mvl_getApiError($func, $status) {
	if ($status == 0) {
		return(__('HTTP Error: Plugin cannot reach the server!',MVLTD));
	}
	if ($status == -1) {
		return(__('Decoding Error: Plugin cannot decode JSON response from the server',MVLTD));
	}
	$msgs = array();

	$msg = array(403 => __('Wrong CAPTCHA was supplied!',MVLTD), 409 => __('User already exists!',MVLTD), 412 => __('Invalid data, e.g. password not complex enough',MVLTD));
	$msgs['registerUser'] = $msg;

	$msg = array(403 => __('Wrong verification code was supplied!',MVLTD), 401 => __('The username does not exist or the user is already verified!',MVLTD));
	$msgs['verifyUser'] = $msg;
	
	$msg = array(403 => __('Wrong captcha was supplied!',MVLTD), 401 => __('The username does not exist or the user is already verified!',MVLTD));
	$msgs['resendVerification'] = $msg;
	
	$msg = array(403 => __('Wrong captcha was supplied!',MVLTD));
	$msgs['resetPassword'] = $msg;

	$msg = array(403 => __('Wrong credentials have been supplied!',MVLTD));
	$msgs['getAuthToken'] = $msg;

	$msg = array(401 => __('The user is not authorized to view this users details!',MVLTD));
	$msgs['getUserDetails'] = $msg;

	$msg = array(401 => __('Wrong credentials have been supplied!',MVLTD), 412 => __('One of the submitted fields is not valid',MVLTD));
	$msgs['changePWD'] = $msg;

	$msg = array(401 => __('Wrong credentials have been supplied!',MVLTD));
	$msgs['deleteUser'] = $msg;

	$msg = array(404 => __('No site is registered for this user!',MVLTD));
	$msgs['getSitesList'] = $msg;

	$msg = array(404 => __('This site is not registered for this user!',MVLTD));
	$msgs['getSiteDetails'] = $msg;

	$msg = array(402 => __('There is no active subscription available for this site!',MVLTD), 404 => __('This site is not registered for this user!',MVLTD));
	$msgs['getSiteAlerts'] = $msg;

	$msg = array(402 => __('Maximum number of sites per user reached!',MVLTD), 409 => __('A site with this name already exists',MVLTD), 412 => __('Invalid data transmitted!',MVLTD));
	$msgs['createSite'] = $msg;

	$msg = array(404 => __('This site is not registered for this user!',MVLTD), 412 => __('One of the supplied products is currently not supported!',MVLTD));
	$msgs['updateSite'] = $msg;

	$msg = array(404 => __('This site is not registered for this user!',MVLTD));
	$msgs['deleteSite'] = $msg;

	if (isset($msgs[$func][$status])) {
		//Todo: Test which error messages are html_encoded or move the html_encode to this function
		//return('<strong>' . __('Error: ',MVLTD).'</strong>' . $msgs[$func][$status]);
		return($msgs[$func][$status]);
	} else {
		return(__('Error: Status=',MVLTD) . $status . __(', an error has occured!',MVLTD));
	}
}

?>