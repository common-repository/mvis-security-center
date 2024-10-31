<?php

function mvl_processProfile($p) {
	global $mvlState;
	$message = '';
	$code = '';
	if (!$mvlState->showProfile && $mvlState->agreeTaC == true){
		$res = mvl_processSummary(0, __('Your are not logged in. Login before accessing your profile.',MVLTD));
		return($res);
	}
	
	$userName = mvl_readOption(MVIS_LITE_OPT_NAME, 'userName');
	$authToken = mvl_readOption(MVIS_LITE_OPT_NAME, 'authToken');
	$siteDetails = mvl_readOption(MVIS_LITE_OPT_NAME, 'siteDetails');
	
	$siteDetails = mvl_getSiteDetails($code, $userName, $authToken, $siteDetails['id']);
	if ($code != 200){
		$siteDetails = mvl_readOption(MVIS_LITE_OPT_NAME, 'siteDetails');
	}
	
	if(!isset($siteDetails['name'])){
		$res = mvl_processSummary(0, __('Your are not logged in. Login before accessing your profile.',MVLTD));
		return($res);
	}
	
	if ($p == 21) { // Change Password
		if(mvl_verifyNonce(mvl_getRequestParam('_wpnonce'),'mvl_profile')){
			$oldPwd = mvl_getRequestParam('current_password');
			$newPwd = mvl_getRequestParam('new_password1');
			
			$apiRes = mvl_changePWD($code, $userName, $authToken, $oldPwd, $newPwd);
			if ($code == 200) {
				$apiRes = mvl_getAuthToken($code, $userName, $newPwd);
				if ($code == 200) {
					mvl_writeOption(MVIS_LITE_OPT_NAME, 'authToken', $apiRes);
					$message = '<div class="success-message">' . __('The password has been changed successfully.') . '</div>';
				} else {
					$err = mvl_getApiError('getAuthToken', $code);
					$message = '<div class="error-message">' . __('There has been an error while changing the password.') . '</div>';
				}
			} else {
				$err = mvl_getApiError('changePWD', $code);
				$message = '<div class="error-message">' . $err . '</div>';
			}
		}else
			$message = '<div class="error-message">' . __('Security Check failed, please try again!',MVLTD) .'</div>';		
	}	

	
	if ($p == 24){
		mvl_manualSync();
	}
	
	if ($p == 25){
		$captchaCode = mvl_getRequestParam('captcha_code');
		$captchaId = mvl_readOption(MVIS_LITE_OPT_NAME, 'captchaId');
		mvl_resend_Verification($code, $userName, $captchaCode, $captchaId);
		if($code != 200){
			$err = mvl_getApiError('resendVerification', $code);
			$message = '<div class="error-message">' . $err . '</div>';
		}else{
			$message = '<div class="success-message">' . __('The verification code has been sent successfully.') . '</div>';
		}
	}
	
	if ($p == 26) { // Toggle Status E-Mail 
		if(mvl_verifyNonce(mvl_getRequestParam('_wpnonce'),'mvl_profile')){
			$isStatusMail = intval(mvl_getRequestParam('status_mails'));
			if($isStatusMail)
				$userSettings = '{"weeklyStatus":true}';
			else
				$userSettings = '{"weeklyStatus":false}';
			
			$apiRes = mvl_updateUserSettings($code, $userName, $authToken, $userSettings);
			if ($code == 200)
				$message = '<div class="success-message">' . __('The change was processed successfully.') . '</div>';
			else
				$message = '<div class="error-message">' . __('There has been an error while processing the change.') . '</div>';
				
		}else
			$message = '<div class="error-message">' . __('Security Check failed, please try again!',MVLTD) .'</div>';
	}
	
	mvl_getUserDetails($code, $userName, $authToken);
	if ($code == 200)
		$userDetails  = mvl_readOption(MVIS_LITE_OPT_NAME, 'userDetails');
	else
		$userDetails ='';
	
	$res = mvl_getPageProfile($message, $userName, $siteDetails, $userDetails);
	return($res);

}


function mvl_getPageProfile($message = '', $userName = '', $siteDetails = '', $userDetails = '') {
	global $mvl_checks_config;	
	global $mvlState;
	$text = __('Get an overview of your subscription and manage your profile.',MVLTD) .'<br/><br/>';	
	$content = mvl_getPageStart('profile', __('Profile',MVLTD), $text, $mvlState->showSubscribe, false, false);
	$active = false;
	$content .= '<div class="container">';
	$nonce = wp_create_nonce('mvl_profile');
	$mainUrl = mvl_getMainUrl();
	$changePWUrl = $mainUrl . '&p=21&_wpnonce=' . $nonce;
	$delSiteUrl = $mainUrl . '&p=22&_wpnonce=' . $nonce;
	$delAccountUrl = $mainUrl . '&p=23&_wpnonce=' . $nonce;
	$syncUrl = $mainUrl . '&p=24';
	$toggleEmails = $mainUrl . '&p=26&_wpnonce=' . $nonce;
	$subscribeUrl = $mainUrl . '&p=10';
	
	if(isset($siteDetails['name']))
		 $siteName = $siteDetails['name'];
	
	if(!isset($siteDetails['status']) || $siteDetails['status'] != 'ACTIVE'){
		$subscriptionText  = __('You are not subscribed to MVIS PROtection yet.',MVLTD) . '<br/><a href="'. $mainUrl .'&p=10" class="errormsg"><strong>' . __('Subscribe now',MVLTD) . '</strong></a> ' .__('to enjoy up-to-date protection!') ;
	}else{
		$active = true;
	}
	if(isset($userDetails['weeklyStatus']) && $userDetails['weeklyStatus'] == true){
		$weeklyStatus = true;
	}
	if(isset($userDetails['verified']) && $userDetails['verified'] == ''){
		$verificationText = __('Your e-mail address has not been verified yet.',MVLTD) . ' <a href="#" onclick="mvl_resendbox();return(false);">'. __('Resend Verification Code!',MVLTD).'</a>';
	}
  $content .= '
  	    <script type="text/javascript">
	    
		jQuery(document).ready(function() {

			jQuery.validator.addMethod("digit", function(value) {
				return value.match(/.*\d.*/);
			}, "'. __('Please enter at least one number',MVLTD) .'");
			jQuery.validator.addMethod("letter", function(value) {
				return value.match(/[a-z]/i);
			}, "'. __('Please enter at least one letter',MVLTD) .'");
			jQuery.validator.addMethod("special", function(value) {
				return value.match(/[!\"$%&\/()\._\-=?]/);
			}, "'. __('Enter one of these characters:',MVLTD) .' !\"$%/()._-=?");
		
			jQuery("#validation3").validate({
	       		rules: {
	        		current_password: "required",
	        		new_password1: {
	        			required: true,
		            	digit: true,
		            	letter: true,
		            	special: true,
		            	minlength: 8
            		},
            		confirm_password: {
	            		required: true,
	            		equalTo: "#new_password1"
          			},
				 },
	      		 messages: {
	      		 	current_password: {
			            required: "'. __('Please provide your current password',MVLTD).'"
          			},
	          		new_password1: {
			            required: "'. __('Please provide a password',MVLTD).'",
			            minlength: "'. __('Your password must be at least 8 characters',MVLTD).'",
          			},
	                confirm_password: {
			            required: "'. __('Please provide a password',MVLTD).'",
			            equalTo: "'. __('Please enter the same password as above',MVLTD).'"
          			},
	          	},
      		});
      	});
    	</script>';

	//if ($message != 'nouser'){
	$content .= $message;
	if(isset($subscriptionText) || isset($verificationText)){
		$content .= '
		<div class="error-message"><strong>Warning:</strong> ';
		if(isset($subscriptionText))
			$content .= $subscriptionText . '<br />';
		if(isset($verificationText))
			$content .= $verificationText  . '<br />' ;
		$content .= '</div>';
	}
	//}
	
    $content .=	'
    <div class="mvis-container">
	<div class="profile-page">
    
      <div class="left-column">';
			
	$content .= '
	
	   <h3>' .__('Subscription Details',MVLTD).'</h3>
	   
     <p>
     <strong>' . __('Username',MVLTD) . ':</strong> '.mvl_htmlEncode($userName).'<br/>
     <strong>' . __('Sitename',MVLTD) . ':</strong> '.mvl_htmlEncode($siteName).'<br/>
     <strong>' . __('Subscription valid until',MVLTD) . ':</strong> '.mvl_htmlEncode(date("Y/m/d",strtotime ( 'now' , strtotime ($siteDetails['expiryDate']))));
	     		
     $content .=
     '</p>
     
     <hr>
     
     <div class="actions">
     <h3>' . __('Actions',MVLTD) . '</h3>
     <form method="post" action="'.$syncUrl.'"><input type="submit" value="'.__('Sync Site',MVLTD).'")" />&nbsp;'. __('Last sync: ',MVLTD). mvl_htmlEncode($mvlState->lastSyncDT). '</form>
     
     <form method="post" action="'.$delSiteUrl.'"><input type="submit" value="'.__('Delete Site From Our Servers',MVLTD).'" onclick="return confirm(\''. __('Are you sure that you want to delete this site from our servers?',MVLTD) .'\')" />&nbsp;</form>
     
     <form method="post" action="'.$delAccountUrl.'"><input type="submit" value="'.__('Delete Account And All Sites From Our Servers',MVLTD).'" onclick="return confirm(\''. __('Are you sure that you want to delete this user and all associated sites from our servers?',MVLTD) .'\')" />&nbsp;</form>
     ';
     
     if($active)
     	if($weeklyStatus)
     		$content .= '<form method="post" action="'.$toggleEmails.'&status_mails=0"><input type="submit" value="'.__('Stop Receiving Weekly Status E-mails',MVLTD).'" onclick="return confirm(\''. __('Are you sure that you want to stop receiving status e-mails for your sites?',MVLTD) .'\')" />&nbsp;</form>';
     	else
     		$content .= '<form method="post" action="'.$toggleEmails.'&status_mails=1"><input type="submit" value="'.__('Start Receiving Weekly Status E-mails',MVLTD).'" onclick="return confirm(\''. __('Are you sure that you want to start receiving status e-mails for your sites?',MVLTD) .'\')" />&nbsp;</form>';
      
    $content .= '<form method="post" action="'.$subscribeUrl.'"><input type="submit" value="'.__('Purchase Additional Coupons or Extend Subscription',MVLTD).'" />&nbsp;</form>';
 	
    $content .= '</div>';
  
  	$content .= '</div>';	
  	
	$content .= '
	<div class="right-column">
          <form id="validation3" method="post" action="'. $changePWUrl .'">
           <fieldset>
             <legend>'. __('Change Password',MVLTD).'</legend>
             <p><label>'. __('Current Password',MVLTD) .'</label>
             <input type="password" id="current_password" name="current_password" class="required" /></p>          	   	 
          	 <p><label for="new_password1">'. __('Enter new password',MVLTD).'</label> <input type="password" id="new_password1" name="new_password1" class="password required" /></p>
             <p><label for="confirm_password">'. __('Repeat new password', MVLTD).'</label> <input type="password" id="confirm_password" name="confirm_password" class="required" /></p>	
             <input type="submit" value="'. __('Change Password',MVLTD) .'"/>
          </fieldset>
         </form>
     </div>
    </div>
   </div>
  ';
	
	$content .= mvl_getPageEnd();
	return($content);	
}

?>