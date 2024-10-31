<?php

function mvl_processSummary($p = '', $status = '') {
	$mainUrl = mvl_getMainUrl();
	global $mvlState;
	
	if ($p == -1 && !$mvlState->agreeTaC) {
		$res = mvl_getPageSummary('');
		return($res);
	}
	
	if ($p == 22) { // Delete Site
		if(mvl_verifyNonce(mvl_getRequestParam('_wpnonce'),'mvl_profile')){
			$userName = mvl_readOption(MVIS_LITE_OPT_NAME, 'userName');
			$authToken = mvl_readOption(MVIS_LITE_OPT_NAME, 'authToken');
			$siteDetails = mvl_readOption(MVIS_LITE_OPT_NAME, 'siteDetails');
			$siteId = $siteDetails['id'];
			$apiRes = mvl_deleteSite($code, $userName, $authToken, $siteId);
			if ($code == 200) {
				$status = 'deletedsite';
				mvl_clearData();
			} else {
				$err = mvl_getApiError('deleteSite', $code);
				$status =  __('There has been an error deleting the site.');
			}
		}else
			$status =  __('Security Check failed, please try again!',MVLTD);
	}
	
	if ($p == 23) { // Delete Account
		if(mvl_verifyNonce(mvl_getRequestParam('_wpnonce'),'mvl_profile')){
			$userName = mvl_readOption(MVIS_LITE_OPT_NAME, 'userName');
			$authToken = mvl_readOption(MVIS_LITE_OPT_NAME, 'authToken');
			$apiRes = mvl_deleteUser($code, $userName, $authToken);
			if ($code == 200) {
				$status = 'deletedaccount';
				mvl_clearData();
			} else {
				$err = mvl_getApiError('deleteUser', $code);
				$status =  __('There has been an error deleting your account.');
			}
		}else
			$status =  __('Security Check failed, please try again!',MVLTD);
	}
	
	if ($p == 41) { // rerun checks
		mvl_doAllChecks();
		mvl_initState();	
	}

	if ($p == 42) { // resync manually after httpError
		$res =  mvl_get_versions($code);
	}
	
	if ($p == 43){
		$captchaCode = mvl_getRequestParam('captcha_code');
		$captchaId = mvl_readOption(MVIS_LITE_OPT_NAME, 'captchaId');
		$userName = mvl_getRequestParam('username');
		mvl_forgotPassword($code, $userName, $captchaCode, $captchaId);
		if($code != 200){
			$err = mvl_getApiError('resetPassword', $code);
			$status = '<div class="error-message">' . $err . '</div>';
		}else{
			$status = '<div class="success-message">' . __('The password reset e-mail has been sent, please follow its instructions.') . '</div>';
		}
	}

// read results from option and use it to display
	$mvl_checks_result = mvl_readOption(MVIS_LITE_OPT_CHECKS_RESULT, 'checks_result');
	if (!isset($mvl_checks_result['lastRun'])) {
		mvl_doAllChecks();
		mvl_initState();
		$mvl_checks_result = mvl_readOption(MVIS_LITE_OPT_CHECKS_RESULT, 'checks_result');
	}

// 1. Update Check
	$updateElements = mvl_getUpdateElements(true);
	mvl_writeOption(MVIS_LITE_OPT_NAME, 'updateElements', $updateElements);
	$count = 0;
	$rows = '';
	$signal = 0;
	foreach($updateElements as $updateElement) {
		$count += 1;
		$type = $updateElement['type'];
		if ($type == 'theme')
			$type = __('Theme',MVLTD);
		$risk = $updateElement['risk'];
		if ($risk > $signal) $signal = $risk;
		$name = $updateElement['name'];
		$state = mvl_getStateStyle($risk);
		$rows .= '<tr><td>'. mvl_htmlEncode($type) . '</td><td>'. mvl_htmlEncode($name) .'</td><td class="align-center">' . $state . '</td></tr>';
		if ($count == 5) {
			break;
		}
	}

	$rows .= '<a href="'. 	$mainUrl . '&p=3#tabs-1"><input type="button" class="more-all" value="' . __('Go to Update Check',MVLTD) . '" title="'. __('Go to Update Check',MVLTD) . '"></a>';
	$table1 = mvl_getTabTable('<a data-mvltooltip="'.__('Find out which software is outdated or vulnerable.',MVLTD) .'">'.__('1. Update Check',MVLTD) .'</a>' , array(__('Type',MVLTD), __('Name',MVLTD), __('Risk',MVLTD)), $rows, false, $signal, "table-wrapper");

// 2. User Check
	$rows = '';
	$count = 0;
	$signal = 0;
	$usersData = mvl_doUserChecks();
	usort($usersData, 'mvl_risk_sort');
	foreach($usersData as $userData) {
		$count += 1;
		$login = $userData['login'];
		$roles = $userData['roles'];
		$state = $userData['risk'];
		if ($state > $signal) $signal = $state;
		$rows .= '<tr><td>' . mvl_htmlEncode($login) .'</td><td>'. mvl_htmlEncode($roles) .'</td><td class="align-center">' . mvl_getStateStyle($state) . '</td></tr>';
		if ($count == 5) {
			break;
		}
	}

	$rows .= '<a href="'. 	$mainUrl . '&p=3#tabs-2"><input type="button" class="more-all" value="' . __('Go to User Check',MVLTD) . '" title="'. __('Go to User Check',MVLTD) . '"></a>';
	$table2 = mvl_getTabTable('<a data-mvltooltip="'.__('Find out which users pose a risk.',MVLTD) .'">'.__('2. User Check',MVLTD) .'</a>', array(__('Username',MVLTD), __('Role',MVLTD), __('Risk',MVLTD)), $rows, false, $signal, "table-wrapper");
	
// 3. Core Check
	$coreChecks = array();
	$signal = 0;
	
	mvl_getSummaryChecks($coreChecks, $signal, $mvl_checks_result['fileChecks'], __('File',MVLTD), __('Check for dangerous files.',MVLTD));
	mvl_getSummaryChecks($coreChecks, $signal, $mvl_checks_result['permissionChecks'], __('Permission',MVLTD), __('Check for insecure file permissions.',MVLTD));
	mvl_getSummaryChecks($coreChecks, $signal, $mvl_checks_result['backendChecks'], __('Backend',MVLTD), __('Check for insecure web server settings.',MVLTD));
	mvl_getSummaryChecks($coreChecks, $signal, $mvl_checks_result['WPbackendChecks'], __('WP Setting',MVLTD), __('Check for insecure WordPress settings.',MVLTD));
	mvl_getSummaryChecks($coreChecks, $signal, $mvl_checks_result['DBbackendChecks'], __('DB Setting',MVLTD), __('Check for insecure database settings.',MVLTD));
	mvl_getSummaryChecks($coreChecks, $signal, $mvl_checks_result['phpSettingsChecks'], __('PHP Setting',MVLTD), __('Check for insecure PHP settings.',MVLTD));
	
	usort($coreChecks, "mvl_state_sort"); 

	$rows = '';
	$count = 0;
	foreach($coreChecks as $check) {
		$count += 1;
		$type = $check['type'];
		$name = $check['name'];
		$state = $check['state'];
		$rows .= "<tr><td>$type</td><td>$name</td><td>" . mvl_getStateStyle($state) . "</td></tr>";		
		if ($count == 5) { 
			break;
		}
	}	

	$rows .= '<a href="'. 	$mainUrl . '&p=3#tabs-3"><input type="button" class="more-all" value="' . __('Go to Core Check',MVLTD) . '" title="'. __('Go to Core Check',MVLTD) . '"></a>';
	$table3 = mvl_getTabTable('<a data-mvltooltip="'.__('Find out which settings are configured insecurely.',MVLTD) .'">'.__('3. Core Check',MVLTD).'</a>', array(__('Type',MVLTD), __('Name',MVLTD), __('Risk',MVLTD)), $rows, false, $signal, "table-wrapper");

	$content = "$table1\r\n$table2\r\n$table3\r\n";
	$res = mvl_getPageSummary($content, $status);
	return($res);
}



function mvl_getPageSummary($content2 = '', $status = '') {
	global $mvlState;
	global $mvl_checks_config;
	$mainUrl = mvl_getMainUrl();
	$text = '';
	
	if($status == 'active')
		$text .= '<div class="success-message">' .  __('You were successfully logged in and can now enjoy the full benefits of MVIS Security Center.',MVLTD) . '</div>';
	elseif($status == 'created' || $status == 'inactive')
		$text .= '<div class="notice-message">' .  __('You were successfully logged in, but are not subscribed yet. Subscribe now to get the full benefits immediately. ',MVLTD) . '</div>';
	elseif($status != '' && mvl_getRequestParam('p') == 43 )
		$text .= $status;
	elseif($status == 'deletedsite')
		$text .= '<div class="success-message">' . __('The site has been deleted successfully. You have been logged out.') . '</div>';
	elseif($status == 'deletedaccount')
		$text .= '<div class="success-message">' . $status = __('Your account has been marked for deletion, please follow the instructions you have received by mail. You have been logged out.',MVLTD) . '</div>';
	elseif($status != '')
		$text .= '<div class="error-message">' . $status . '</div>';
	elseif ($mvlState->httpError &&  $mvlState->agreeTaC && $mvlState->agreeAutoLoad && mvl_getRequestParam('p') != 42) {
		$reSyncUrl = $mainUrl . '&p=42';
		//TODO: set one variable that is set after one successful connection. so we know that the connection worked before.
		$text .= '
		<div class="error-message"><strong>' .
		__('Warning:',MVLTD) . '</strong><br/>' .
		__('The Plugin failed to communicate with the MVIS-Server. If this message remains after clicking the button below, please verify the connectivity settings on your server.',MVLTD) .'<br/>'.
		__('If this problem persists and you have never successfully synced with our site, then please check your network and firewall settings to allow the communcation with the MVIS-Server.',MVLTD) .'
		<form method="post" action="' . $reSyncUrl . '">
		<input type="submit" value="'.__('Try again',MVLTD).'" />
		</form>
		</div>';
	}
	$content = mvl_getPageStart('summary', __('Overview', MVLTD), '', $mvlState->showSubscribe, $mvlState->showProfile);
	
	if ($mvlState->agreeTaC != true) {
		$agreeTaC = mvl_readOption(MVIS_LITE_OPT_NAME, 'agreeTaC', false);
		$agreeTaC ? $checkedTaC = ' checked="checked" ' : $checkedTaC = '';
		
		$content .= '<div class="container"><br/>';
		$content .= __('Before you can use the Plugin, you have to agree to the ',MVLTD) . '<a href="https://mvis.sec-consult.com/mvis-sc/information/TERMS_AND_CONDITIONS.pdf" target="_blank">' . __('Terms and Conditions',MVLTD) .'</a> ' .__('for MVIS Security Center.', MVLTD) . '<br/>';
		$content .= __('To get access to the enhanced functionality and updates the plugin needs to communicate with our servers.',MVLTD). '&nbsp;'. mvl_getInfoLinkMod(MVL_COMMUNICATE,'',__('Find out why!',MVLTD),false).'<br/><br/>';
		$content .= '<input type="checkbox"' . mvl_htmlEncode($checkedTaC) . ' name="cbTaC" onclick="mvl_agree_tac(this.checked);" />&nbsp;';
		$content .= __('I agree to accept the Terms and Conditions and confirm that I have read the Privacy Policy.',MVLTD) .'<br/>';
	}else{
		$content .= $text;
		$content .= '<div class="container">';
		$content .= '<strong>'.  __('Any red or orange dots?',MVLTD) . '</strong>';
		$content .= '<br /><br/>' . __('Follow the instructions and turn them into green dots!',MVLTD);
		$content .= '<br /><br />';
		$content .= 
			$content2 .'
			<div class="clear"></div>';
	}
	if(!$mvlState->agreeAutoLoad){
		$mvlState->agreeAutoLoad ? $checkedAL = ' checked="checked" ' : $checkedAL = '';
		$content .= '<input type="checkbox"' . $checkedAL . ' name="cbAL" onclick="mvl_agree_autoload(this.checked);" />&nbsp;';
		$content .= __('I allow the MVIS Security Center Plugin to communicate with the servers of SEC Consult to get access to the full benefits of the plugin.',MVLTD);
	}

	if ($mvlState->agreeTaC != true) {
			$continueUrl = $mainUrl . '&p=0';
	$content .= '<br/><br/>
		<form method="post" action="'. $continueUrl.'">
		<input type="submit" value="'. __('Continue',MVLTD) .'" />
		<br/> <br/> <div id="loadingMsg"><strong>'. __('Note:',MVLTD) . '</strong> ' . __('Clicking "Continue" will run the checks for the first time - this might take a minute...',MVLTD) .'</div>
		</form>
		</div>';
	}


	if ($mvlState->agreeTaC == true) {
		
		if (defined('DISABLE_WP_CRON') &&(DISABLE_WP_CRON === true))
			$content .= mvl_showEnableCron();
	
		$reRunUrl = $mainUrl . '&p=41';
		
		$content .= '<div class="notice-message">
		<form method="post" action="'.$reRunUrl.'" style="float: left; padding-right: 5px;">
			<input type="submit" value="' .__('Rerun Checks', MVLTD) .'" />
		</form>
		'. __('The last run of the security checks was on ', MVLTD) . mvl_htmlEncode($mvlState->lastChecksRunDT) . '.<br />
		'. __('Rerun the checks after changes in your configuration.',MVLTD)  .'
		</div>
</div>';

	}

	$content .= mvl_getPageEnd();
	return($content);
}


?>