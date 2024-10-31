<?php

function mvl_AjaxGuiPageStart($text = ''){
	$content = '
	<html>
		<body>
			<div class="mvis-container">
	';
	if ($text != '')
		$content .= '<h3>' . $text .'</h3>';
	return $content;
}

function mvl_AjaxGuiPageEnd(){
	$content = '
			</div>
		</body>
	</html>';
	return $content;
}

function mvl_AjaxGuiSolutions($header,$solutions){
	$content  = '<hr>';
	$content .= '<h3>' . $header . '</h3>';
	$content .= '<ol>';
	foreach($solutions as $solution)
		$content .= '<li>' . $solution . '</li>';
	$content .= '</ol>';
	return $content;
}

function mvl_AjaxGuiPageFurtherInformation($furtherText,$generalID, $checkID = ''){
	$content = '<br/>' . $furtherText . '<a href="'.trailingslashit(MVIS_LITE_FURTHER_INFORMATION_URL);
	if($checkID != '')
		$content .= $generalID . '.html#' . $checkID . '" ';
	else
		$content .= $generalID . '.html" '; 
	$content .= 'id="modalajax">' . __('here',MVLTD) . '</a>.';
	return $content;
}

function mvl_FormatLoginBox(){

	$loginUrl = mvl_getAbsoluteAdminUrl(29);
	$susbscribeUrl = mvl_getAbsoluteAdminUrl(10);
	
	$s = mvl_AjaxGuiPageStart();
	$s .= '
		<form id="loginBox" method="post" action="' . $loginUrl .'">
			<fieldset>
				<h2><span class="profile"></span>'.__('Login',MVLTD) .'</h2>
				<p><label for="email">'.__('E-mail', MVLTD) .'</label> <input type="text" id="email" name="email" placeholder="'.__('Email',MVLTD).'"  /></p>
				<p><label for="password">'.__('Password',MVLTD).'</label> <input type="password" id="password" name="password" placeholder="'.__('Password',MVLTD).'"  /></p>
				<p><input type="submit" value="'.__('Login',MVLTD).'" style="display: inline-block;
																			  text-align: center;
																			  line-height: 20px;
																			  min-width: 120px;
																			  padding: 4px 12px;
																			  background: #DCDC2A;
																			  border: 1px solid #BCBC20;
																			  border-radius: 5px;
																			  cursor: pointer;
																			  margin-left: 2px;
																			  text-decoration: none;" />
				&nbsp;&nbsp;&nbsp;&nbsp;' . __('No account yet?',MVLTD) .' <a href="'.$susbscribeUrl.'">' . __('Click here to register!') .'</a> 
				</p>
			</fieldset>
		</form>';
	$s .= mvl_AjaxGuiPageEnd();
	return($s);
}

function mvl_FormatResendBox(){
	$refreshcaptchaicon =  WP_PLUGIN_URL . '/mvis-security-center/images/refresh.png';
	$apiRes = mvl_get_captcha($code, $id);
	if ($code == 200) {
		mvl_writeOption(MVIS_LITE_OPT_NAME, 'captchaId', $id);
		$captcha = "<img id=\"mvis-captcha\" src=\"data:image/jpeg;base64,$apiRes\">\r\n";
		mvl_writeOption(MVIS_LITE_OPT_NAME, 'captcha', $captcha);
	} else {
		$err = mvl_getApiError('get_captcha', $code);
		$res = mvl_processSummary(0,__('There has been an error while requesting the captcha from the server. ',MVLTD). $err);
		return($res);
	}
	$resendUrl = mvl_getAbsoluteAdminUrl(25);

	$s = mvl_AjaxGuiPageStart();
	$s .= '
	<form id="resendBox" method="post" action="' . $resendUrl .'">
		<fieldset>
			<h3>'. __('Resend Verification Code',MVLTD) .'</h3>
			<p><label for="captcha">'. __('Captcha',MVLTD) .' &nbsp;&nbsp;&nbsp;<a href="#" onclick="mvl_refresh_captcha();return(false);"><img src="'.$refreshcaptchaicon.'" width="12px" height="12px"></a></label>'.$captcha.'</p>
		    <p><label for="captcha_code">' . __('Captcha code',MVLTD) .'</label> <input type="text" id="captcha_code" name="captcha_code" class="required" /></p>
		    <p><input type="submit" value="'. __('Resend Verification Code',MVLTD) .'" style="display: inline-block;
																			  text-align: center;
																			  line-height: 20px;
																			  min-width: 120px;
																			  padding: 4px 12px;
																			  background: #DCDC2A;
																			  border: 1px solid #BCBC20;
																			  border-radius: 5px;
																			  cursor: pointer;
																			  margin-left: 2px;
																			  text-decoration: none;" /></p>
		</fieldset>
	</form>';
	$s .= mvl_AjaxGuiPageEnd();
	return($s);
}

function mvl_FormatForgotPWD(){
	$refreshcaptchaicon =  WP_PLUGIN_URL . '/mvis-security-center/images/refresh.png';
	$apiRes = mvl_get_captcha($code, $id);
	if ($code == 200) {
		mvl_writeOption(MVIS_LITE_OPT_NAME, 'captchaId', $id);
		$captcha = "<img id=\"mvis-captcha\" src=\"data:image/jpeg;base64,$apiRes\">\r\n";
		mvl_writeOption(MVIS_LITE_OPT_NAME, 'captcha', $captcha);
	} else {
		$err = mvl_getApiError('get_captcha', $code);
		$res = mvl_processSummary(0,__('There has been an error while requesting the captcha from the server. ',MVLTD). $err);
		return($res);
	}
	$resendUrl = mvl_getAbsoluteAdminUrl(43);

	$s = mvl_AjaxGuiPageStart();
	$s .= '
		<form id="resendBox" method="post" action="' . $resendUrl .'">
		<fieldset>
			<h3>'. __('Reset Password',MVLTD) .'</h3>
			<p><label for="captcha">'. __('Captcha',MVLTD) .' &nbsp;&nbsp;&nbsp;<a href="#" onclick="mvl_refresh_captcha();return(false);"><img src="'.$refreshcaptchaicon.'" width="12px" height="12px"></a></label>'.$captcha.'</p>
		    <p><label for="captcha_code">' . __('Captcha code',MVLTD) .'</label> <input type="text" id="captcha_code" name="captcha_code" class="required" /></p>
		    <p><label for="username">' . __('Username',MVLTD) .'</label> <input type="text" id="username" name="username" class="required" /></p>
		    <p><input type="submit" value="'. __('Send Password Reset E-Mail',MVLTD) .'" style="display: inline-block;
																			  text-align: center;
																			  line-height: 20px;
																			  min-width: 120px;
																			  padding: 4px 12px;
																			  background: #DCDC2A;
																			  border: 1px solid #BCBC20;
																			  border-radius: 5px;
																			  cursor: pointer;
																			  margin-left: 2px;
																			  text-decoration: none;" /></p>
		</fieldset>
	</form>';
	$s .= mvl_AjaxGuiPageEnd();
	return($s);
}


function mvl_FormatDBCheck($DBCheck){
	//print_r($backendCheck);
	$type = isset($DBCheck['type'])?$DBCheck['type']:'';
	$s = mvl_AjaxGuiPageStart();	
	
	if ($type == 'dbprefix'){
		$s .= '<div class="warning-message"><strong>'. __('Warning:',MVLTD) .'</strong> ' .
				__('The WordPress database prefix has the default value!',MVLTD) . '</div>';
		$s .= '<h3>' . __('Summary',MVLTD) .'</h3>';
		$s .= __('The default database prefix is often used in automated attacks and changing it can potentially avoid some kind of automated attacks. Because there is no benefit in using the default WordPress database prefix, it should be changed to improve the security.',MVLTD);
		$solutions = array(__('Change the WordPress database prefix to something arbitrary.',MVLTD),
					__('Rerun the Core Checks to verify that the settings are correct.',MVLTD));
		$s .= mvl_AjaxGuiSolutions(__('Solution',MVLTD),$solutions);
		$s .= mvl_AjaxGuiPageFurtherInformation(__('Further information on how to change the WordPress database prefix can be found ', MVLTD), MVL_DBCHECK_GENERAL, $DBCheck['id']);
	}
	if ($type == 'dbroot'){
		$s .= '<div class="error-message"><strong>'. __('Warning:',MVLTD) .'</strong> ' .
				__('The root user is used to connect to the database!',MVLTD) . '</div>';
		$s .= '<hr>';
		$s .= '<h3>' . __('Summary ',MVLTD) .'</h3>';
		$s .= __('WordPress is configured to use the database user',MVLTD) . ' <strong>root</strong>' .__(', which puts your website at risk. ',MVLTD);
		$s .= __('Especially in combination with SQL injection vulnerabilities, attackers can fully compromise your website if the root database user is used.');
		$solutions = array(__('Change the WordPress database user and make sure only the minmum necessary privileges are assigned to it.',MVLTD),
					__('Rerun the Core Checks to verify that the settings are correct.',MVLTD));
		$s .= mvl_AjaxGuiSolutions(__('Solution',MVLTD),$solutions);
		$s .= mvl_AjaxGuiPageFurtherInformation(__('Further information about changing the database user and permissions can be found ', MVLTD), MVL_DBCHECK_GENERAL, $DBCheck['id']);
	}
	
	$s .= mvl_AjaxGuiPageEnd();
	return($s);
}

function mvl_FormatBackEndCheck($backendCheck){
	$type = isset($backendCheck['type'])?$backendCheck['type']:'';
	$s = mvl_AjaxGuiPageStart();
	
	if($type == 'https'){
		if(($backendCheck['force'] == 2 || $backendCheck['force'] == 3) && $backendCheck['httpsenforced']){
			$s .= '<div class="warning-message"><strong>'. __('Warning:',MVLTD) .'</strong> ' .  
			__('SSL is not configured perfectly which poses a threat to your website!',MVLTD) . '</div>';
			$s .= '<hr>';
			$s .= '<h3>' . __('Summary ',MVLTD) .'</h3>';
			$s .= __('The communication with the WordPress admin interface and the login mask is already encrypted over SSL (https://), but for the best protection it is recommended to use the FORCE_SSL_ADMIN constant.',MVLTD);
			$solutions = array(
					__('Set the FORCE_SSL_ADMIN constant to true in the wp-config.php file.',MVLTD),
					__('Rerun the Core Checks to verify that the settings are correct.',MVLTD)
					);
			
		}else{
			$s .= '<div class="error-message"><strong>'. __('Warning:',MVLTD) .'</strong> ' .  
			__('SSL is not enabled which puts your website at risk!',MVLTD) . '</div>';
			$s .= '<hr>';
			$s .= '<h3>' . __('Summary ',MVLTD) .'</h3>';
			$s .= __('The communication with the WordPress admin interface is not encrypted over SSL (https://). ',MVLTD);
			$s .= __('If you connect to your admin interface over insecure networks, such as an open WiFi, attackers can steal your credentials and take over your website. ',MVLTD);
			$solutions = array(
					__('Make sure that your webserver is configured to accept SSL connections.',MVLTD),
					__('Enable SSL for your WordPress website and verify that it is working properly.',MVLTD),
					__('Set the FORCE_SSL_ADMIN constant to true in the wp-config.php file.',MVLTD) ,
					__('Rerun the Core Checks to verify that the settings are correct.',MVLTD),
					);
		}
		$s .= mvl_AjaxGuiSolutions(__('Solution',MVLTD),$solutions);
		$s .= mvl_AjaxGuiPageFurtherInformation(__('Further information on setting up SSL correctly can be found ', MVLTD), MVL_BACKENDCHECK_GENERAL, $backendCheck['id']);
		
	}
	
	if ($type == 'wwwauthenticate'){
		$s .= '<div class="warning-message"><strong>'. __('Warning:',MVLTD) .'</strong> ' .  
			__('Basic Authentication is not enabled!',MVLTD) . '</div>';
		$s .= '<hr>';
		$s .= '<h3>' . __('Summary ',MVLTD) .'</h3>';
		$s .= __('Basic authentication serves as an additional authentication layer to protect your WordPress admin interface from password guessing attacks, which are very common and pose a high risk. Enabling Basic Authentication puts another barrier between attackers and your website.',MVLTD);
		$solutions = array(__('Enable Basic Authentication for your /wp-admin directory.',MVLTD),
				__('Rerun the Core Checks to verify that the settings are correct.',MVLTD));
		$s .= mvl_AjaxGuiSolutions(__('Solution',MVLTD),$solutions);
		$s .= mvl_AjaxGuiPageFurtherInformation(__('Further information on how to configure Basic Authentication for your website can be found ', MVLTD), MVL_BACKENDCHECK_GENERAL, $backendCheck['id']);
	}
	if ($type == 'dirlisting'){
		$s .= '<div class="warning-message"><strong>'. __('Warning:',MVLTD) .'</strong> ' .  
			__('Directory Listing is enabled!',MVLTD) . '</div>';
		$s .= '<hr>';
		$s .= '<h3>' . __('Summary ',MVLTD) .'</h3>';
		$s .= __('If Directory Listing is enabled, the webserver returns the contents of directories that don\'t have an index file such as index.php to the user. Depending on what information is availble in the exposed directories, attackers can gain sensitive data that might help them in further attacks against your website. There is normally no good reason to use Directory Listing and it should be disabled.',MVLTD);
		$solutions = array(__('Disable Directory Listing for your website.',MVLTD),
				__('Rerun the Core Checks to verify that the settings are correct.',MVLTD));
		$s .= mvl_AjaxGuiSolutions(__('Solution',MVLTD),$solutions);
		$s .= mvl_AjaxGuiPageFurtherInformation(__('Further information on how to disable Directory Listing for your website can be found ', MVLTD), MVL_BACKENDCHECK_GENERAL, $backendCheck['id']);
	}

	$s .= mvl_AjaxGuiPageEnd();
	return($s);
}

function mvl_FormatWPBCheck($WPbackendCheck){
	$s = mvl_AjaxGuiPageStart();
	if(isset($WPbackendCheck['roleviolation']))
		$roleviolation = $WPbackendCheck['roleviolation'];
	
	if(isset($WPbackendCheck['type']) && $WPbackendCheck['type'] == 'wpmime'){
		$s .= '<div class="error-message"><strong>'. __('Warning:',MVLTD) .'</strong> ' .  __('WordPress allows the upload of dangerous file types for privileged users!',MVLTD) . '</div>';
		$s .= '<hr>';
		$s .= '<h3>' . __('Summary',MVLTD) . '</h3>';
		
		if(isset($WPbackendCheck['violations']) && is_array($WPbackendCheck['violations'])){
			$s .= __('Files with the following dangerous extensions can be uploaded by editors and authors:',MVLTD). '<br />';
			$s .= '<ul>';
			foreach($WPbackendCheck['violations'] as $violation)
				$s .=  '<li><strong>.'.$violation.'</strong></li>';
			$s .= '</ul>';
			$s .= __('This allows users with the author or editor roles to upload dangerous files that can harm users of your website or even give them full control to your website.',MVLTD);
		}
		$solutions = array(
				__('Change the WordPress settings to disallow the above mentioned files from being uploaded.',MVLTD),
				__('Rerun the Core Checks to verify that the settings have been changed correctly.'));
		$s .= mvl_AjaxGuiSolutions(__('Solution',MVLTD),$solutions);
		$s .= mvl_AjaxGuiPageFurtherInformation(__('Further information on disabling the upload of dangerous files for privileged users can be found ', MVLTD), MVL_WPBCHECK_GENERAL, $WPbackendCheck['id']);
					
	}elseif(isset($WPbackendCheck['type']) && $WPbackendCheck['type'] == 'wpdebug'){
		$s .= '<div class="warning-message"><strong>'. __('Warning:',MVLTD) .'</strong> ' .  __('WordPress Debug is enabled!',MVLTD) . '</div>';
		$s .= '<hr>';
		$s .= '<h3>' . __('Summary',MVLTD) . '</h3>';
		$s .= __('The WordPress DEBUG functionality is useful for developing new plugins or fine tuning the performance of requests. Once your website is live, this functionality has to be disabled because otherwise it might provide attackers with valuable information that can be used in attacks against your website.',MVLTD);
		$solutions = array(__('Disable the "debug" functionality by adding the line',MVLTD) . '<br/><strong>' . __('define(\'WP_DEBUG\', false);',MVLTD) .'</strong><br/>' . __('to the wp-config.php file.',MVLTD),
				__('Rerun the Core Checks to verify that the settings have been changed correctly.'));
		$s .= mvl_AjaxGuiSolutions(__('Solution',MVLTD),$solutions);
		$s .= mvl_AjaxGuiPageFurtherInformation(__('Further information on disabling the WordPress debug functionality can be found ', MVLTD), MVL_WPBCHECK_GENERAL, $WPbackendCheck['id']);
	}elseif((isset($WPbackendCheck['type']) && $WPbackendCheck['type'] == 'userreg')){
		if (isset($roleviolation))
			$s .= '<div class="error-message"> <strong>'. __('Warning:',MVLTD) .'</strong> ' . __('Users are able to register privileged WordPress accounts!',MVLTD) .'</div>';
		else 
			$s .= '<div class="warning-message"> <strong>'. __('Warning:',MVLTD) .'</strong> ' . __('Users are able to register WordPress accounts!',MVLTD) .'</div>';
		$s .= '<hr>';
		$s .= '<h3>' . __('Summary',MVLTD) . '</h3>';
		if (isset($roleviolation))
			$s .= __('Users can freely register WordPress accounts with the role ') . '<strong>'. mvl_htmlEncode($roleviolation) . '</strong>';
		else
			$s .= __('Users can freely register low-privileged WordPress accounts',MVLTD);
		if (isset($roleviolation))
			$s .= __(', this poses a very high risk to the system because attackers can easily get access to your website as privileged users.',MVLTD);
		else
			$s .= __('. User registration should be disabled if not needed.',MVLTD);
			$solutions = array(__('Disable User Registration in the admin menu "Settings->General".',MVLTD),
					__('Rerun the Core Checks to verify that the settings have been changed correctly.'));
			$s .= mvl_AjaxGuiSolutions(__('Solution',MVLTD),$solutions);
			$s .= mvl_AjaxGuiPageFurtherInformation(__('Further information on how to disable the user registration can be found ', MVLTD), MVL_WPBCHECK_GENERAL, $WPbackendCheck['id']);
	}elseif((isset($WPbackendCheck['type']) && $WPbackendCheck['type'] == 'fileedit')){
		$s .= '<div class="warning-message"><strong>'. __('Warning:',MVLTD) .'</strong> ' .  __('The WordPress "file edit" functionality is enabled!',MVLTD) . '</div>';
		$s .= '<hr>';
		$s .= '<h3>' . __('Summary',MVLTD) . '</h3>';
		$s .= __('The WordPress "file edit" functionality can be useful for making quick changes to plugins or themes, but generally it is not needed in live systems. This functionality is often exploited by attackers, that have gained access to the admin interface, to quickly compromise the website. On live systems, this functionality should be disabled if it is not explicitly needed.',MVLTD);
		$solutions = array(__('Disable the "file edit" functionality by adding the line',MVLTD) . '<br/><strong>' . __('define(\'DISALLOW_FILE_EDIT\', true);',MVLTD) .'</strong><br/>' . __('to the wp-config.php file.',MVLTD),
				__('Rerun the Core Checks to verify that the settings have been changed correctly.'));
		$s .= mvl_AjaxGuiSolutions(__('Solution',MVLTD),$solutions);
		$s .= mvl_AjaxGuiPageFurtherInformation(__('Further information on disabling the WordPress "file edit" functionality can be found ', MVLTD), MVL_WPBCHECK_GENERAL, $WPbackendCheck['id']);
	}
	
	$s .= mvl_AjaxGuiPageEnd();
	return($s);
}

function mvl_FormatPhpSettingCheck($phpSettingsCheck){
	$name ='';
	if(!isset($phpSettingsCheck['type']))
		$phpSettingsCheck['type'] = '';
	
	$s = mvl_AjaxGuiPageStart();
	

	if($phpSettingsCheck['state'] == 3)
		$s .= '<div class="error-message"><strong>'. __('Warning:',MVLTD) .'</strong> ' .  __('These PHP settings are configured insecurely!',MVLTD) . '</div>';
	else
		$s .= '<div class="warning-message"><strong>'. __('Warning:',MVLTD) .'</strong> ' .  __('These PHP settings violate security best practices!',MVLTD) . '</div>';
	
	$s .= '<hr>';
	$s .= '<h3>' . __('Summary',MVLTD) . '</h3>';
	if ($phpSettingsCheck['type']== 'disabled_functions'){
		$s_solution ='';
		$s .= __('PHP provides many powerful functions to developers, some of these functions allow the executing of operating system commands. The majority of websites do not need this powerful functionality. By disabling dangerous functions in live environments, the capabilities of potential attackers will be heavily limited.',MVLTD). '<br />';
		$s .= '<br/>';
		$s .= __('The following dangerous functions are enabled:',MVLTD);
		$s .= '<ol>'; 
		foreach($phpSettingsCheck['violations'] as $violation){
			$s .=  '<li><strong>'.$violation.'()</strong></li>';
			$s_solution .= ',' . $violation;
		}
		$s .= '</ol>';
		
		$solutions = array(
				__('Verify that you have a working and recent backup of your installation and that you can restore the current state if a problem occurs.',MVLTD),
				__('Modify your php.ini file and add the following string to the line starting with ',MVLTD) . '<strong>disable_functions=</strong>' . '<br/>' . '<ul><li>' . $s_solution . '</li></ul>',
				__('Rerun the Core Checks to verify that the settings have been changed correctly.')				
		);
		$s .= mvl_AjaxGuiSolutions(__('Solution ',MVLTD),$solutions);
	}else{
		if(!is_array($phpSettingsCheck['setting'])){
			$name = $phpSettingsCheck['setting'];
		}else{
			$count = 1;
			$sol = '<ul>';
			foreach($phpSettingsCheck['violations'] as $names){
				if(count($phpSettingsCheck['violations']) == 1 || count($phpSettingsCheck['violations']) == $count){
					$name .= $names;
					break;
				}else{
					$name .= $names . ', ';
					$sol .= '<li><strong>' . $name . ' = Off</strong></li>';
				}
				$count +=1;
			}
			$sol .= '</ul>';
		}
			
		if (!$phpSettingsCheck['type'] == 'errorreportinglevel'){
			$value = $phpSettingsCheck['value'] ? 'enabled': 'disabled';
			$shouldBe = $phpSettingsCheck['shouldBe'] ? 'enable': 'disable';			
		}else{
			$value = $phpSettingsCheck['value'];
			$shouldBe = $phpSettingsCheck['shouldBe'];
		}
			
		if($count > 1){
			if($phpSettingsCheck['id'] == 804) //URL FOPEN
				$s .= __('The PHP settings ',MVLTD) .'<strong>' .  $name . '</strong>'. __(' are currently ',MVLTD). '<strong>'.  __('enabled') . '</strong>. ' . __('This functionality is considered dangerous and vulnerabilities in it are often exploited to compromise websites. Disable these settings to increase the security of your website.',MVLTD);
			elseif($phpSettingsCheck['id'] == 806){ //Error Reporting
				$s .= __('The PHP settings ',MVLTD) .'<strong>' .  $name . '</strong>'. __(' are currently ',MVLTD). '<strong>'. __('enabled') . '</strong>,' . __(' this can provide attackers with information about your setup and help them in further attacks against your website.',MVLTD);
				$solution = __('Change the according lines in the php.ini to: ',MVLTD). $sol;
			}elseif($phpSettingsCheck['id'] == 808){ //Magic Quotes
				$s .= __('The PHP settings ',MVLTD) .'<strong>' .  $name . '</strong>'. __(' are currently ',MVLTD). '<strong>'.  __('enabled') . '</strong>. ' . __('These settings are deprecated and should be disabled in any case.',MVLTD);
				$solution = __('Change the according lines in the php.ini to: ',MVLTD). $sol;
			}else
				$s .= __('The PHP settings ',MVLTD) .'<strong>' .  $name . '</strong>'. __(' are currently ',MVLTD). '<strong>'. $value . '</strong>.'. ucwords($shouldBe) . __(' the settings to improve the security.',MVLTD);
		}else{
			if($phpSettingsCheck['id'] == 801){ //Register Globals
				$s .= __('The PHP setting ',MVLTD) .'<strong>' .  $name . '</strong>'. __(' is currently ',MVLTD). '<strong>'. __('enabled') . '</strong>,' . __(' this can potentially be exploited by attackers to take over your website. This setting is deprecated and should be disabled in any case.',MVLTD);
				$solution = mvl_getPHPSolution($name,'Off');
			}elseif($phpSettingsCheck['id'] == 802){ //Safe Mode
				$s .= __('The PHP setting ',MVLTD) .'<strong>' .  $name . '</strong>'. __(' is currently ',MVLTD). '<strong>'.  __('enabled') . '</strong>. ' . __('This setting is deprecated and should be disabled in any case.',MVLTD);
				$solution = mvl_getPHPSolution($name,'Off');
			}elseif($phpSettingsCheck['id'] == 804){ //URL FOPEN
				$s .= __('The PHP setting ',MVLTD) .'<strong>' .  $name . '</strong>'. __(' is currently ',MVLTD). '<strong>'.  __('enabled') . '</strong>. ' . __('This functionality is considered dangerous and vulnerabilities in it are often exploited to compromise websites. Disable this setting to increase the security of your website.',MVLTD);
				$solution = mvl_getPHPSolution($name,'Off');
			}elseif($phpSettingsCheck['id'] == 805){ //Version information
				$s .= __('The PHP setting ',MVLTD) .'<strong>' .  $name . '</strong>'. __(' is currently ',MVLTD). '<strong>'.  __('enabled') . '</strong>,' . __(' this can provide attackers with information about your setup and help them in further attacks against your website.',MVLTD);
				$solution = mvl_getPHPSolution($name,'Off');
			}elseif($phpSettingsCheck['id'] == 806){ //Error Reporting
				$s .= __('The PHP setting ',MVLTD) .'<strong>' .  $name . '</strong>'. __(' is currently ',MVLTD). '<strong>'.  __('enabled') . '</strong>,' . __(' this can provide attackers with information about your setup and help them in further attacks against your website.',MVLTD);
				$solution = mvl_getPHPSolution($name,'Off');
			}elseif($phpSettingsCheck['id'] == 807){ //Error Reporting Level
				$s .= __('The PHP setting ',MVLTD) .'<strong>' .  $name . '</strong>'. __(' is currently ',MVLTD). '<strong>'.  __('not') . '</strong>' . __(' configured according to security best practices. Reporting all errors is crucial for detecting suscpicious activities.',MVLTD);
				$solution = mvl_getPHPSolution($name,'E_ALL');
			}elseif($phpSettingsCheck['id'] == 808){ //Magic Quotes
				$s .= __('The PHP setting ',MVLTD) .'<strong>' .  $name . '</strong>'. __(' is currently ',MVLTD). '<strong>'.  __('enabled') . '</strong>. ' . __('This setting is deprecated and should be disabled in any case.',MVLTD);
				$solution = mvl_getPHPSolution($name,'Off');
			}elseif($phpSettingsCheck['id'] == 808){ //Log Errors
				$s .= __('The PHP setting ',MVLTD) .'<strong>' .  $name . '</strong>'. __(' is currently ',MVLTD). '<strong>'.  __('disabled') . '</strong>. ' . __('This setting is deprecated and should be disabled in any case.',MVLTD);
				$solution = mvl_getPHPSolution($name,'On');
			}else
				$s .= __('The PHP setting ',MVLTD) .'<strong>' .  $name . '</strong>'. __(' is currently ',MVLTD). '<strong>'. $value . '</strong>'. ucwords($shouldBe) . __(' the setting to improve the security.',MVLTD);		
		}
		$solutions = array(__('Verify that you have a working and recent backup of your installation and that you can restore the current state if a problem occurs.',MVLTD));
		if(isset($solution))
			array_push($solutions, $solution);
		else
			array_push($solutions,	__('Change the PHP Settings as proposed above.',MVLTD));
		array_push($solutions, __('Rerun the Core Checks to verify that the settings have been changed correctly.',MVLTD));
		
		$s .= mvl_AjaxGuiSolutions(__('Solution ',MVLTD),$solutions);
	}
	
	$s .= mvl_AjaxGuiPageFurtherInformation(__('Further information on how to change this PHP setting can be found ', MVLTD), MVL_PHPSETTINGCHECK_GENERAL, $phpSettingsCheck['id']);
	$s .= mvl_AjaxGuiPageEnd();
	return($s);
}

function mvl_getPHPSolution($name, $toggle){
	return (__('Change the according line in the php.ini to ',MVLTD). '<strong>' . $name . ' = ' . $toggle . '</strong>');
}

function mvl_FormatPermissionCheck($permissionCheck){
	$s = mvl_AjaxGuiPageStart();

	if($permissionCheck['state'] == 3)
		$s .= '<div class="error-message"><strong>'. __('Warning:',MVLTD) .'</strong> ' .  __('File permissions are set insecurely!',MVLTD) . '</div>';
	else 
		$s .= '<div class="warning-message"><strong>'. __('Warning:',MVLTD) .'</strong> ' .  __('File permissions are not as strict as they should be!',MVLTD) . '</div>';
	$s .= '<hr>';
	$s .= '<h3>' . __('Summary',MVLTD) . '</h3>';
	$s .= __('Especially in shared hosting environments, you have to protect the files and directories of your website from misuse by other people. ',MVLTD);
	$s .= __('Verify that "open_basedir" is set correctly to ensure that your data is protected. ',MVLTD);
	$s .= __('Malicious people that share the same hosting environment with you, might be able to read or even change important files of your WordPress setup and can, in the worst case, take over your site completely.',MVLTD);	

	$s .= '<hr>';
	$s .= '<h3>' . __('Details',MVLTD) . '</h3>';
	
	$name = untrailingslashit(ABSPATH) . $permissionCheck['fileName'];
	$fuid = fileowner($name);
	$fgid = filegroup($name);
	$userstr = posix_getpwuid($fuid);
	$groupstr = posix_getgrgid($fgid);
	$userstr['name'] == '' ? $userstr = __('n/a',MVLTD) : $userstr = $userstr['name']; 
	$groupstr['name'] == '' ? $groupstr = __('n/a',MVLTD) : $groupstr = $groupstr['name'];
	$user =  $userstr . ' ('. $fuid .')';
	$group =  $groupstr . ' ('. $fgid .')';
	
	is_file($name) ? $type= __('file',MVLTD) : $type = __('directory',MVLTD);
	$s .= __('The ',MVLTD). $type . ' <strong>' . $name . '</strong>'. __(' is owned by the user:',MVLTD). ' <strong>' . $user . '</strong> '. __('and belongs to the group:',MVLTD) .' <strong>'. $group .'</strong>. <br/><br/>';
	
	$s .= __('The following permissions are set for the file: ',MVLTD). '<br/>';
	$s .= '<ul>';
	$s .='<li><strong>'. $permissionCheck['permsunix'] . '</strong>'. ' '. __('(symbolic)',MVLTD). '/' . '<strong>'. $permissionCheck['perms'] . '</strong>'.' '. __('(octal)',MVLTD). '.</li>';
	$s .= '</ul>';
	$s .= __('The following permissions pose a risk to your site and should be removed: ',MVLTD) . '<br/>';
	
	for($i=0;$i<count($permissionCheck['violations']);$i++){
		if($permissionCheck['violations'][$i] != 'no' && is_array($permissionCheck['violations'][$i])){
			if($i==0) $category = __('User',MVLTD);
			if($i==1) $category = __('Group',MVLTD);
			if($i==2) $category = __('World',MVLTD);
			$s.= '<strong>' .$category. '</strong>';
			$s .= '<ul>';
			for($j=0;$j<count($permissionCheck['violations'][$i]);$j++){
				if($permissionCheck['violations'][$i][$j] == 'r')
					$right = __('Read',MVLTD);
				if($permissionCheck['violations'][$i][$j] == 'w')
					$right = __('Write',MVLTD);
				if($permissionCheck['violations'][$i][$j] == 'x')
					$right = __('Execute',MVLTD);
				$s .= '<li>' . $right . '</li>';
			}
			$s .= '</ul>';
		}
	}
	$s .= '<strong>' . __('Note:', MVLTD) . '</strong> ' . __('Changing the file permissions is only necessary if your website is in a shared hosting environment.') .'<br />';
	$basedir = ini_get('open_basedir');
	if($basedir == '')
		$s .= '<strong>' . __('Warning:', MVLTD) . '</strong> '. __('The PHP value "open_basedir" is not set. On a shared host the risk of others reading your files is high.',MVLTD) .'';
	else
		$s .= __('The value of open_basedir is set to: ',MVLTD). '<strong>'. mvl_htmlEncode($basedir) . '</strong><br /><br />';
	$s .= '<h3>' . __('Solution',MVLTD) . '</h3>';
	$s .= '<ol>';
	$s .= '<li>' . __('Verify that you have a working and recent backup of your installation.',MVLTD).'</li>';
	$s .= '<li>' . __('Be careful when changing permissions and make sure that you can change them back if a problem occurs.',MVLTD) .'</li>';
	$s .= '<li>' . __('Change the permissions of the ',MVLTD). $type . __(' accordingly',MVLTD) .'.</li>';
	$s .= '</ol>';
	
	$s .= mvl_AjaxGuiPageFurtherInformation(__('Further information on how to change file permissions can be found ', MVLTD), MVL_PERMCHECK_GENERAL, $permissionCheck['id']);
	$s .= mvl_AjaxGuiPageEnd();
	return($s);
}

function mvl_FormatFileCheck($fileCheck){
	$exists_but_shouldnt = $fileCheck['shouldExist'] == 'yes' ? true : false;
	$s = mvl_AjaxGuiPageStart();	
	$solutions = array();
	
	if(isset($fileCheck['type']) && $fileCheck['type'] == 'wpdangerousfiles'){
		$s .= '<div class="error-message"><strong>'. __('Warning:',MVLTD) .'</strong> ';
		$s .= __('Dangerous files have been identified in your WordPress installation!',MVLTD) . '<br/>';
		$s .= __('This is a strong indication that your website has been compromised!');
	}elseif(isset($fileCheck['type']) && $fileCheck['type'] == 'wpconfigbackupfiles'){
		$s .= '<div class="error-message"><strong>'. __('Warning:',MVLTD) .'</strong> ';
		$s .= __('Wp-config.php backup files have been identified in your WordPress installation!',MVLTD) . '<br/>';
		$s .= __('Backup files of wp-config.php likely contain sensitive information that can be accessed by attackers.');
	}else{
		$s .= '<div class="warning-message"><strong>'. __('Warning:',MVLTD) .'</strong> ';
		$s .= __('Files that can leak information to attackers have been identified!',MVLTD);
	}
	
	$s .= '</div>';
	$s .= '<hr>';
	$s .= '<h3>' . __('Summary',MVLTD) . '</h3>';
	
	if($fileCheck['type'] == 'wpdangerousfiles')
		$s .= __('Files have been identified in your WordPress installation, that are a strong sign that your website has been hacked. This is a big threat, so immediately follow the instructions below.');
	elseif($fileCheck['type'] == 'wpconfigbackupfiles')
		$s .= __('Wp-config.php backup files have been identified in your WordPress installation, these files may leak information to attackers and should be removed immediately.');
	else
		$s .= __('There are some files that part of an original WordPress installation, but they are not needed for your website. Attackers can use these files to get information about which WordPress version is installed and maybe use this information in further attacks. There is no good reason for these files to exist, so it is a safe and secure choice to delete them.');
		
	$s .= '<hr>';
	$s .= '<h3>' . __('Details and Solution ',MVLTD) . '</h3>';
	$s .= '<ol>';
	
	if(isset($fileCheck['type']) && $fileCheck['type'] == 'wpdangerousfiles'){
		if(sizeof($fileCheck['violations'])>1){
			$s .= '<li>' . __('Rename the following files: ',MVLTD) .'</li>';
			$s .= '<ul>';
			foreach ($fileCheck['violations'] as $violation){
				$s .= '<li>' . mvl_htmlEncode($violation) . __(' to ',MVLTD) . basename($violation,'.php').'<strong>.xyz</strong></li>';
			}
			$s .= '</ul>';
		}else{
			foreach ($fileCheck['violations'] as $violation){
				$s .= '<li>' . __('Rename the file ',MVLTD) .  mvl_htmlEncode($violation) . __(' to ',MVLTD) . basename($violation,'.php').'<strong>' .'.xyz</strong></li>';
			}			
		}
		
		$s .= '<li>'.__('Follow the instructions described at the ',MVLTD) . '<a href="http://codex.wordpress.org/FAQ_My_site_was_hacked" target="_blank">WordPress codex</a>.</li>';
	}elseif(isset($fileCheck['type']) && $fileCheck['type'] == 'wpconfigbackupfiles'){
		if(sizeof($fileCheck['violations'])>1){
			$s .= '<li>' . __('Delete the following files: ',MVLTD) .'</li>';
			$s .= '<ul>';
			foreach ($fileCheck['violations'] as $violation){
				$s .= '<li>' . mvl_htmlEncode($violation) . '</li>';
			}
			$s .= '</ul>';
		}else{
			foreach ($fileCheck['violations'] as $violation){
				$s .= '<li>' . __('Delete the file ',MVLTD) .  mvl_htmlEncode($violation) . '.</li>';
			}
		}
	}
	else{
			$s .= '<li>'. __('Remove the file  ',MVLTD) . '<strong>' .mvl_htmlEncode(untrailingslashit(ABSPATH) . $fileCheck[fileName]) .'</strong>.</li>'; 
	}
	
	$s .= '</ol>';

	$s .= mvl_AjaxGuiPageFurtherInformation(__('Further information on how to remove files can be found ', MVLTD), MVL_FILECHECK_GENERAL, $fileCheck['id']);
	$s .= mvl_AjaxGuiPageEnd();
	return($s);
}

function mvl_FormatUserChecks($userData){
	$basic_auth = mvl_readOption(MVIS_LITE_OPT_NAME, 'wwwauthenticate') == 'enabled' ? true : false;
	$email = mvl_htmlEncode($userData['email']);
	$loginName = mvl_htmlEncode($userData['login']);
	$roles = mvl_htmlEncode($userData['roles']);
	$s = mvl_AjaxGuiPageStart();
	
	if($userData['weakPWBF'] == 1 && ($userData['privileged'] == 1 || $userData['privileged'] == 2 || $userData['superAdmin'] == true)){
		$s .= '<div class="error-message"><strong>'. __('Critical Warning:',MVLTD) .'</strong> ' .  __('This username and password combination is currently exploited by attackers to take over WordPress sites globally!',MVLTD) . '</div>';
		$summary = __('The username',MVLTD) . '<strong> '. $loginName .'</strong>' . __(' in combination with the currently set insecure password is actively exploited by attackers to give them full access to WordPress sites.',MVLTD);
		$solutions =  array(
				'<a href="'.mvl_getAdminBaseUrl('user-edit.php?user_id=' . intval($userData['id'])).'" target="_blank">' . __('Click here') . '</a>' .__(' to change the password of the user ',MVLTD) . '<strong>'. $loginName .'</strong>.',
				__('Choose a new password for the user. Not sure how to choose a good password?',MVLTD) . ' <a href="http://strongpasswordgenerator.com" target="_blank">'. __('Click here!',MVLTD) . '</a>',
				__('Log out if you are currently logged in with the ',MVLTD).'<strong>' . $loginName .'</strong>' .__(' user, and login with the new password.',MVLTD),
				__('Verify that the new user account works and has the minimum needed privileges.',MVLTD));
	}elseif($userData['weakPWBF'] == 1 && $userData['privileged'] != 1){
		$s .= '<div class="warning-message"><strong>'. __('Warning:',MVLTD) .'</strong> ' .  __('This username and password combination is currently exploited by attackers to take over WordPress sites globally!',MVLTD) . '</div>';
		$summary =  __('The username',MVLTD) . '<strong> '. $loginName .'</strong>' . __(' in combination with the currently set insecure password is actively exploited by attackers to give them access to WordPress sites. This user is not considered privileged, which reduces the impact a successful attack has on this site. It is still posing a high risk and has to be resolved immediately.',MVLTD);
		$solutions =  array(
				'<a href="'.mvl_getAdminBaseUrl('user-edit.php?user_id=' . intval($userData['id'])).'" target="_blank">' . __('Click here') . '</a>' .__(' to change the password of the user ',MVLTD) . '<strong>'. $loginName .'</strong>.',
				__('Choose a new password for the user. Not sure how to choose a good password?',MVLTD) . ' <a href="http://strongpasswordgenerator.com" target="_blank">'. __('Click here!',MVLTD) . '</a>',
				__('Log out if you are currently logged in with the ',MVLTD).'<strong>' . $loginName .'</strong>' .__(' user, and login with the new password.',MVLTD),
				__('Verify that the new user account works and has the minimum needed privileges.',MVLTD));
	}elseif($userData['weakPW'] != 1 && $userData['commonAdminName'] == 1){
		$s .= '<div class="warning-message"><strong>'. __('Warning:',MVLTD) .'</strong> ' .  __('A common username is used for an administrative user account!',MVLTD) . '</div>';
		$summary = '<strong>'. $loginName .'</strong>' . __(' is a well known username for administrative accounts. Attackers often target this username in password guessing attempts, because a successfully guessed password for this account gives them full access to the WordPress site.',MVLTD) ;
	    $solutions =  array(
	    		'<a href="'. mvl_getAdminBaseUrl('user-new.php').'" target="_blank">' . __('Create a new user',MVLTD) .'</a>'. __(' with a name that is not commonly used (e.g. xzyadm) and assign administrative rights if needed.',MVLTD),
	    		__('Choose a password for the user. Not sure how to choose a good password?',MVLTD) . ' <a href="http://strongpasswordgenerator.com" target="_blank">'. __('Click here!',MVLTD) . '</a>',
	    		__('Assign administrative rights to the user if needed.',MVLTD),
	    		__('Log out if you are currently logged in with the ',MVLTD).'<strong>' . $loginName .'</strong>' .__(' user, and login with the newly created user.',MVLTD),
	    		__('Verify that the new user account works and has the right privileges.',MVLTD),
	    		__('Delete the ',MVLTD).'<strong>' . $loginName .'</strong>' .__(' account.',MVLTD),
	    		__('Make sure that the administrative is not used for posting!',MVLTD));
	}elseif($userData['weakPW'] == 1 && $userData['commonAdminName'] == 1){
		$s .= '<div class="error-message"><strong>'. __('Warning:',MVLTD) . '</strong> ' .  __('A weak password for an administrative user account with a well known username has been identified!',MVLTD) . '</div>';
		$summary = __('The username ',MVLTD) . '<strong>'. $loginName .'</strong>' . __(' is a well known username for administrative accounts. Attackers often target this username in password guessing attempts, because a sucessfully guessed password gives them full access to the WordPress site. Additionally, a <strong>weak password</strong> has been identified for ',MVLTD) . '<strong>' .$loginName .'</strong>' . __(' which makes a successful attack very likely. This user poses an immensly high risk to your site.',MVLTD) ;
		$solutions =  array(
				'<a href="'. mvl_getAdminBaseUrl('user-new.php').'" target="_blank">' . __('Create a new user',MVLTD) .'</a>'. __(' with a name that is not commonly used (e.g. xzyadm).',MVLTD),
				__('Choose a password for the user. Not sure how to choose a good password?',MVLTD) . ' <a href="http://strongpasswordgenerator.com" target="_blank">'. __('Click here!',MVLTD) . '</a>',
				__('Assign administrative rights to the user if needed.',MVLTD),
				__('Log out if you are currently logged in with the ',MVLTD).'<strong>' . $loginName .'</strong>' .__(' user, and login with the newly created user.',MVLTD),		
				__('Verify that the new user account works and has the right privileges.',MVLTD),
				__('Delete the ',MVLTD).'<strong>' . $loginName .'</strong>' .__(' account.',MVLTD),
				__('Make sure that the administrative is not used for posting!',MVLTD));
	}else{
		if($userData['weakPW'] == 1 && $userData['privileged'] != 1 && $userData['commonAdminName'] != 1){
			$s .= '<div class="warning-message"><strong>'. __('Warning:',MVLTD) .'</strong> ' .  __('A weak password for an unprivileged user account has been identified!',MVLTD) . '</div>';
			$summary = __('The username ',MVLTD) . '<strong>'. $loginName .'</strong>' . __(' with the role(s) ',MVLTD). '<strong>'.$roles .'</strong>' . __(' uses a very weak password.',MVLTD) ;
		}if($userData['weakPW'] == 1 && $userData['privileged'] == 1){
			$s .= '<div class="error-message"><strong>'. __('Warning:',MVLTD) . '</strong> ' .  __('A weak password for a privileged user account has been identified!',MVLTD) . '</div>';
			$summary = __('The username ',MVLTD) . '<strong>'. $loginName .'</strong>' . __(' with the role(s) ',MVLTD). '<strong>'.$roles .'</strong>' . __(' uses a very weak password. Attackers that are targeting this user account in password guessing attacks have a very high chance of succeeding and thus getting access to your website as a privileged user.',MVLTD) ;
		}if($userData['weakPW'] == 1 && $userData['commonAdminName'] == 1){
			$s .= '<div class="error-message"><strong>'. __('Warning:',MVLTD) . '</strong> ' .  __('A weak password for an administrative user account with a well known username has been identified!',MVLTD) . '</div>';
			$summary = __('The username ',MVLTD) . '<strong>'. $loginName .'</strong>' . __(' is a well known username for administrative accounts. Attackers often target this username in password guessing attempts, because a sucessfully guessed password gives them full access to the WordPress site. Additionally, a <strong>weak password</strong> has been identified for ',MVLTD) . '<strong>' .$loginName .'</strong>' . __(' which makes a successful attack very likely. This user poses an immensly high risk to your site.',MVLTD) ;
		}
		$solutions =  array(
				'<a href="'.mvl_getAdminBaseUrl('user-edit.php?user_id=' . intval($userData['id'])).'" target="_blank">' . __('Click here') . '</a>' .__(' to change the password of the user ',MVLTD) . '<strong>'. $loginName .'</strong>.',
				__('Choose a strong password.',MVLTD) . ' '. __('Not sure how to choose a good password?',MVLTD) . ' <a href="http://strongpasswordgenerator.com" target="_blank">'. __('Click here!',MVLTD) . '</a>',
				__('If this account belongs to someone else, inform this person of the new password by sending an e-mail to ',MVLTD).$email .'.');
	}
	
	$s .= '<hr>';
	$s .= '<h3>' . __('Summary',MVLTD) . '</h3>';
	$s .= $summary;	

	$s .= mvl_AjaxGuiSolutions(__('Details and Solution: ',MVLTD),$solutions);

	if(!$basic_auth)
		$s .= '<br/>' . __('<strong>Note:</strong> Protecting the WordPress login mask with "Basic Authentication" reduces the risk of successful bruteforcing attacks.',MVLTD) .'<br/>';
	$s .= mvl_AjaxGuiPageFurtherInformation(__('Further information about solving this problem can be found ', MVLTD), MVL_USERCHECK_GENERAL);
	$s .= mvl_AjaxGuiPageEnd();
	return($s);
}


function mvl_formatUpdates($wpUpdate,$vulnerable = false) {
  $siteDetails = mvl_readOption(MVIS_LITE_OPT_NAME, 'siteDetails');
  $active = ((isset($siteDetails['id'])) && ($siteDetails['status'] == 'ACTIVE'));
  //Update and component values:
  $name = $wpUpdate['name'];
  $type = $wpUpdate['type'];
  if($type == 'Core')
    $type = __('WordPress setup',MVLTD);
  $oldVersion = $wpUpdate['oldVersion'];
  $newVersion = $wpUpdate['newVersion'];
  $url = $wpUpdate['url'];
  $package = $wpUpdate['package'];
  
  $s = mvl_AjaxGuiPageStart();
  
  if(!$active)
  	$s .= mvl_getSubscribtionBenefits();
  if($vulnerable)
    $s .= '<div class="error-message"><strong>'. __('Warning:',MVLTD) .'</strong> ' . __('This ',MVLTD) . strtolower($type) . __(' is outdated and vulnerabilies are known for it that pose a threat to your website.',MVLTD) . '</div>';
  else
    $s .= '<div class="warning-message"><strong>'. __('Warning:',MVLTD) .'</strong> ' .  __('An update is available for this ',MVLTD) . $type . __(', we highly recommend you to upgrade to the latest version!',MVLTD) . '</div>';
  $s .= '<hr>';
  $s .= '<h3>' . __('Summary',MVLTD) .'</h3>';
  if($vulnerable)
  	$s .= __('An update is available for ') . (($wpUpdate['type'] != 'Core') ? (__('the ',MVLTD) . strtolower($type) . ' <strong>'. $name . '</strong>') : (' <strong>'. $name . '</strong>')). __(' which is currently installed in version ', MVLTD) .'<strong>' .$oldVersion. '.</strong> ' . __('This version also contains publicly known vulnerabilities and poses a risk to your WordPress site.');
  else
  	$s .= __('An update is available for ') . (($wpUpdate['type'] != 'Core') ? (__('the ',MVLTD) . strtolower($type) . ' <strong>'. $name . '</strong>') : (' <strong>'. $name . '</strong>')). __(' which is currently installed in version ', MVLTD) .'<strong>' .$oldVersion. '.</strong>';
  
 
  $solutions = array(
  		__('Verify that you have a working and recent backup of your installation and that you can restore the current state if a problem occurs during the upgrade.',MVLTD));
  if($wpUpdate['type']!= 'Core')
  	array_push($solutions,  __('Upgrade to the latest version (') . '<strong>'. $newVersion.'</strong>) ' . mvl_directUpgrade($wpUpdate['fileName'],strtolower($type), __('directly',MVLTD)). ', '. __('or download it ') . $package .'.');
  else
  	array_push($solutions,__('Download the latest version ',MVLTD) . $newVersion . ' ' . $package .', ' . __('or go to the update page ',MVLTD) . '<a href="'. network_admin_url('update-core.php') .'" target="_blank">'. __('here',MVLTD) .'</a> ' . __('and install it manually.',MVLTD));

  $s .= mvl_AjaxGuiSolutions(__('Details and solutions',MVLTD),$solutions);
  
  if ($wpUpdate['active']==false && $wpUpdate['type'] != 'Core')
  	$s.= '<strong>' . __('Note: ',MVLTD) . '</strong>' . __('Even though it is currently not active, the security vulnerability might still be triggered by an attacker. It is strongly recommended to remove all unused themes and plugins.',MVLTD) . '<br/>';
  
  $s .= mvl_AjaxGuiPageFurtherInformation(__('Further information on how to solve this problem can be found ', MVLTD), MVL_UPDATECHECK_GENERAL);
  $s .= mvl_AjaxGuiPageEnd();
  
  return($s);
  
}

function mvl_formatSiteAlert($siteAlert,$wpUpdate) {
  $id = mvl_htmlEncode($siteAlert['id']);
  $title = mvl_htmlEncode($siteAlert['Title']);
  $src = mvl_htmlEncode($siteAlert['Source']);
  $type = $wpUpdate['type'];
  $solved = $siteAlert['Solved'];
  $solved = $solved ? __('Yes',MVLTD) : __('No update was available at the time of the vulnerability disclosure.',MVLTD);
  $description = mvl_htmlEncode($siteAlert['Description']);
  $alertType = mvl_htmlEncode(isset($siteAlert['Type'])?$siteAlert['Type']:'');
  $impact = mvl_htmlEncode(isset($siteAlert['Impact'])?$siteAlert['Impact']:'');
  $risk = mvl_htmlEncode(isset($siteAlert['Risk'])?$siteAlert['Risk']:'');
  $recommendation = mvl_htmlEncode($siteAlert['Recommendation']);
  $furtherInfo = mvl_htmlEncode($siteAlert['Source']);

  //Update and component values:
  $name = mvl_htmlEncode($wpUpdate['name']); 
  $oldVersion = mvl_htmlEncode($wpUpdate['oldVersion']);
  $newVersion = mvl_htmlEncode($wpUpdate['newVersion']);

  $s = mvl_AjaxGuiPageStart();

  $s .= '<div class="error-message"><strong>'. __('Warning:',MVLTD) .'</strong> ' . __('Vulnerabilities are known for this ',MVLTD) . ($wpUpdate['$type'] == 'Core' ? $type : strtolower($type)) . __(' that pose a threat to your website.', MVLTD). '</div>';
  $s .= '<hr>';
  $s .= '<h3>' . __('Summary',MVLTD) . '</h3>';
  if ($wpUpdate['type'] == 'Core' && $newVersion != ''){
    $s .= __('The installed WordPress version is outdated and contains known security vulnerabilities. ',MVLTD) . __('Attackers can abuse these security vulnerabilities against users of your site and the site itself. ',MVLTD);
    $s .= __('Update WordPress to the newest available version to eliminate these vulnerabilities.',MVLTD);
  }elseif ($wpUpdate['type'] == 'Core' && $newVersion == ''){
    $s .= __('The installed WordPress version contains known security vulnerabilities. ',MVLTD) . __('Attackers can abuse these security vulnerabilities against users of your site and the site itself. ',MVLTD);
    $s .= __('Currently there is no update available, please see the vulnerability details below for further information.',MVLTD);
  }elseif ($wpUpdate['type'] == 'Plugin' && $newVersion != ''){
    $s .= __('The installed version of the plugin is outdated and contains security vulnerabilities. ',MVLTD) . __('Attackers can abuse these security vulnerabilities against users of your site and the site itself. ',MVLTD);
    $s .= __('Update the plugin to the newest available version to eliminate these vulnerabilities.',MVLTD);
  }elseif ($wpUpdate['type'] == 'Plugin' && $newVersion == ''){
    $s .= __('The installed version of the plugin contains known security vulnerabilities. ',MVLTD) . __('Attackers can abuse these security vulnerabilities against users of your site and the site itself. ',MVLTD);
    $s .= __('Currently there is no update available, please see the vulnerability details below for further information.',MVLTD);
  }elseif ($wpUpdate['type'] == 'Theme' && $newVersion != ''){
    $s .= __('The installed version of the theme is outdated and contains known security vulnerabilities. ',MVLTD) . __('Attackers can abuse these security vulnerabilities against users of your site and the site itself. ',MVLTD);
    $s .= __('Update the theme to the newest available version to eliminate these vulnerabilities.',MVLTD);
  }elseif ($wpUpdate['type'] == 'Theme' && $newVersion == ''){
    $s .= __('The installed version of the Theme contains known security vulnerabilities. ',MVLTD) . __('Attackers can abuse these security vulnerabilities against users of your site and the site itself. ',MVLTD);
    $s .= __('Currently there is no update available, please see the vulnerability details below for further information.',MVLTD);
  }
  //$s .= '</ul>';
  //$s .= '<div class="clear"><br /><br/></div>';
  $s .= '<hr>';
  $s .= '<h3>' . __('Details',MVLTD) . '</h3>';
  $s .= '<div class="tab-table ">
        <table class="tablesorter" style="width:100%">
          <caption>' . __('Vulnerability Details', MVLTD) .'</caption>
        <tbody>
          <tr>
            <td nowrap="nowrap"><strong>'. __('Title:') . '</strong></td>
            <td colspan="3">'. $title . '</td>
          </tr>
          <tr>
            <td nowrap="nowrap"><strong>'. __('Name:', MVLTD) .'</strong></td>';
  if($alertType != ''){
    $s .= '<td>'.$name . ' ' . $oldVersion.'</td>
                  <td><strong>'.__('Impact:', MVLTD).'</strong></td>
                  <td>'.$impact.'</td>';
  }else{
    $s .= '<td colspan="3">'.$name . ' ' . $oldVersion.'</td>';
  }
  $s .= '</tr>';
  if($impact != '' && $risk != ''){
    if ($solved == 'Yes' && $wpUpdate['url'] != ''){
      $s .= '<tr>
                  <td><strong>'.__('Update available:', MVLTD).'</strong></td>';
      if($wpUpdate['type'] != 'Core')
      	$s .= ' <td><strong>'.$solved.' - </strong> ' . __('Click ',MVLTD). mvl_directUpgrade($wpUpdate['fileName'], strtolower($type), __('here ',MVLTD)). __('to upgrade directly, ',MVLTD) .'<br/>'. __('or download version ',MVLTD) . $newVersion . ' ' . $wpUpdate['package']. '.</td>';
      else 
      	$s .= ' <td><strong>'.$solved.' - </strong> ' . __('Download the latest version ',MVLTD) . $newVersion . ' ' . $package .',<br/>' . __('or go to the update page ',MVLTD) . '<a href="'. network_admin_url('update-core.php') .'">'. __('here',MVLTD) .'</a>.</td>';
      		
      $s .= '<td><strong>'.__('Risk:', MVLTD).'</strong></td>
             <td>'.$risk.'</td>
          </tr>';
    }else{
      $s .= '<tr>
                  <td><strong>'.__('Update available:', MVLTD).'</strong></td>
                    <td><strong>'.$solved.'</strong></td>
                    <td><strong>'.__('Risk:', MVLTD).'</strong></td>
                  <td>'.$risk.'</td>
                </tr>';
    }
  }
  $s .= ' <tr>
            <td nowrap="nowrap"><strong>'.__('Description:', MVLTD).'</strong></td>
            <td colspan="3">'.$description.'</td>
          </tr>';
  if($recommendation != ''){
    $s .= '<tr>
                <td nowrap="nowrap"><strong>'.__('Solution:', MVLTD).'</strong></td>
                <td colspan="3">' . $recommendation . '</td>
              </tr>';
  }
  if($furtherInfo != ''){
    $s .= '<tr>
                <td nowrap="nowrap"><strong>'.__('Source:', MVLTD).'</strong></td>
                <td colspan="3">' . $furtherInfo. '</td>
              </tr>';
  }
  $s .= '</tbody>
        </table>
      <div class="clear"></div>';
  
  if($wpUpdate['newVersion'] != ''){
  	$solutions = array(
  			__('Verify that you have a working and recent backup of your installation and that you can restore the current state if a problem occurs during the upgrade.',MVLTD));
  	if($wpUpdate['type'] != 'Core')
  		array_push($solutions, __('Click ',MVLTD). mvl_directUpgrade($wpUpdate['fileName'],strtolower($type), __('here ',MVLTD)). __('to upgrade directly, ',MVLTD) .' '. __('or download version ',MVLTD) . $newVersion . ' ' . $wpUpdate['package']. '.');
  	else
  		array_push($solutions, __('Download the latest version ',MVLTD) . $newVersion . ' ' . $package .', ' . __('or go to the update page ',MVLTD) . '<a href="'. network_admin_url('update-core.php') .'">'. __('here',MVLTD) .'</a>.');
  }else
  	$solutions = array(
  			__('Verify that you have a working and recent backup of your installation and that you can restore the current state if a problem occurs.',MVLTD),
  			__('Follow the instruction in the recommended solution section outlined above.',MVLTD)
  			);
  
  $s .= mvl_AjaxGuiSolutions(__('Solutions ',MVLTD),$solutions);
  
  if ($wpUpdate['active']==false && $wpUpdate['type'] != 'Core')
  	$s.= '<strong>' . __('Note: ',MVLTD) . '</strong>' . __('Even though this ',MVLTD) . strtolower($type) . __(' is currently not active, the security vulnerability might still be triggered by an attacker. It is strongly recommended to remove all unused themes and plugins.',MVLTD) . '<br/>';
  
  $s .= mvl_AjaxGuiPageFurtherInformation(__('Further information on how to solve this problem can be found ', MVLTD), MVL_UPDATECHECK_GENERAL);
  $s .= '
    </div>';
  $s .= mvl_AjaxGuiPageEnd();

  return($s);
}

?>