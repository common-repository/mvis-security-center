<?php

function mvl_getAdminBaseUrl($php){
	return(network_site_url('/wp-admin/'.$php));
}

function mvl_getAbsoluteAdminUrl($p) {
	return(network_site_url('/wp-admin/admin.php') . '?page=mvl_wp.php' . '&p=' . intval($p));
}

function mvl_getBaseUrl() {
	return(plugins_url('mvis-security-center/'));
}

function mvl_getMainUrl() {
	return(network_site_url('/wp-admin/admin.php') . '?page=mvl_wp.php');
}

function enqueue_mvl_scripts() {
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('mvl_3', mvl_getBaseUrl() . 'js/tooltip.js');
	wp_enqueue_script('mvl_5', mvl_getBaseUrl() . 'js/jquery.tablesorter.js');
	wp_enqueue_script('mvl_6', mvl_getBaseUrl() . 'js/jquery.validate.min.js');
	wp_enqueue_script('mvl_7', mvl_getBaseUrl() . 'js/jquery.pstrength-min.1.2.js');
	wp_enqueue_script('mvl_8', mvl_getBaseUrl() . 'js/jquery.colorbox-min.js');
	wp_enqueue_script('mvl_99', mvl_getBaseUrl() . 'js/mvl.js');
}


function enqueue_mvl_styles() {
	wp_enqueue_style('mvl_style_1', mvl_getBaseUrl() . 'css/styles.css');
	wp_enqueue_style('mvl_style_3', mvl_getBaseUrl() . 'css/colorbox.css');
}


function mvl_getPageStart($descClass = '', $descHeader = '', $descText = '', $showSubscribe=true, $showProfile=true, $showLogin=false) {
	global $mvlState;
	$siteDetails = mvl_readOption(MVIS_LITE_OPT_NAME, 'siteDetails');
	$mainUrl = mvl_getMainUrl();
	//User has no active subscriptions for this site
	if((isset($siteDetails['status']) && $siteDetails['status'] == 'INACTIVE') || !isset($siteDetails['status'])){
		$btntext = __('Activate Real-Time Vulnerability Alerts for This Site!',MVLTD);
		$subscribeDiv = '<div class="btnsub"><a href="' . $mainUrl . '&p=10">'. $btntext .'</a></div>';
	//The subscription is about to expire
	}elseif(mvl_renewSubscription()){
		$subscribeDiv = '<div class="btnsub"><a href="' . $mainUrl . '&p=10">'.__('Click Here to Renew Your Protection!',MVLTD) .'</a></div>';
	//If the site is active don't show the button
	}else {
		$subscribeDiv = '';	
	}
	$userName = mvl_readOption(MVIS_LITE_OPT_NAME, 'userName');
	$authToken = mvl_readOption(MVIS_LITE_OPT_NAME, 'authToken');
	if($subscribeDiv != '')
		$loginDiv = '<div class="your-profile">';
	else
		$loginDiv = '<div class="feedback">';
	
	if($userName == '' && $authToken =='') {
		$loginDiv .= '<a href="#" onclick="mvl_loginbox();return(false);"><span class="profile"></span>'. __('Login',MVLTD) . '</a> | <a href="#" onclick="mvl_forgotPWD();return(false);">'. __('Reset Password',MVLTD) . '</a>';
		$loginDiv .= '<br/>';
		$loginDiv .= '<a href="mailto:mvis_wp@sec-consult.com"target="_blank">'. __('Feedback, Bugs or Feature Requests? ',MVLTD). '</a></div>';
		$showProfile = false;
	}else{
		$showProfile = true;
		$loginDiv .= '<a href="mailto:mvis_wp@sec-consult.com"target="_blank">'. __('Feedback, Bugs or Feature Requests? ',MVLTD). '</a></div>'; 
	}
	$testMenu = '';
	if (defined('MVL_TEST')) {
		$testMenu = "<li><a href=\"$mainUrl&p=800\"><span class=\"nav-summary\"></span>TEST</a></li>";
	}
	$content = '
<div id="mvis-wrapper">
    <div id="mvis-header">
        <div id="mvis-logo">'.__('MVIS Security Center',MVLTD).'</div>
        <ul class="nav">
        	' . $testMenu;

	$content .= '
        	<li><a href="'. $mainUrl .'&p=0"><span class="nav-summary"></span>' . __('Overview',MVLTD) . '</a></li>';
	if($mvlState->agreeTaC == true)
		$content .= '<li><a href="'. $mainUrl .'&p=1"><span class="nav-steps"></span>' . __('3 Steps',MVLTD) . '</a></li>';
	if ($showProfile && $mvlState->agreeTaC == true)
		$content .= '<li><a href="'. $mainUrl .'&p=20"><span class="nav-profile"></span>' . __('Profile',MVLTD) . '</a></li>';

	$content .= '
            <li><a href="'. $mainUrl .'&p=6"><span class="nav-help"></span>' . __('Help',MVLTD) . '</a></li>';
	$content .= '
        </ul>
    </div>

    <div class="mvis-wrapper-inner">
      <div class="mvis-pro"> ';
	if((mvl_getRequestParam('p') >= 10 && mvl_getRequestParam('p') < 20) || $mvlState->agreeTaC != true){
		$subscribeDiv = '';
		$loginDiv = '';
	}elseif((mvl_getRequestParam('p') >= 20 && mvl_getRequestParam('p') <28) && mvl_getRequestParam('p') != 22 && $mvlState->showProfile && mvl_getRequestParam('p') != 23){
		if($subscribeDiv != '')
			$loginDiv = '<div class="your-profile">';
		else 
			$loginDiv = '<div class="feedback">';
		$loginDiv .= '<a href="'. mvl_getMainUrl() . '&p=28&_wpnonce=' . wp_create_nonce('mvl_logout') .'" onclick="return confirm(\''. __('Are you sure that you want to logout?',MVLTD) .'\')"><span class="profile"></span>'. __('Logout',MVLTD) . '</a>';
		$loginDiv .= '<br/>';
		$loginDiv .= '<a href="mailto:mvis_wp@sec-consult.com"target="_blank">'. __('Feedback, Bugs or Feature Requests? ',MVLTD). '</a>';
		$loginDiv .= '</div>';
	}

	$content .= '</div>

      <div class="mvis-description">
      	<div style="float:left;width:40%">
          <h2><span class="'. $descClass.'"></span>'. $descHeader .'</h2>
          '. $descText .'
        </div>'.
        $subscribeDiv . 
        $loginDiv .'
      </div><div class="clear"></div>';
	return($content);
}


function mvl_getPageEnd() {
	$content = '
	  		</div>
   </div>';
	return($content);
}

function mvl_showEnableCron(){
		return '<div class="notice-message">' .'<strong>'. __('Attention: WP_CRON is disabled!',MVLTD).'</strong><br/>'.
				__('Without WP_CRON your sitedetails are not sychronized automatically with our servers, which may result in you receiving outdated information.',MVLTD) .
				'<br/>'.
				__('Enable WP_CRON by removing the line "define(\'DISABLE_WP_CRON\', true)" in the wp-config.php file.',MVLTD) .
				'</div>';
}

function mvl_getSubscribtionBenefits(){
		
	$res = '<div class="error-message">' .'<strong>'. __('Warning:',MVLTD) . '</strong> ' .  
		   __('The vulnerability information for this system is 30 days old!',MVLTD).'<br/>
			<a class="errormsg" href="' . mvl_getAbsoluteAdminUrl(10) . '"><strong>' . __('Subscribe now',MVLTD) .'</strong></a> ' .__('to enjoy up-to-date protection!',MVLTD) .
			'</div>';
	return $res;

}

// Modal Info Box
function mvl_getInfoLinkMod($id,$aid='',$text = '',$tooltip = true, $tooltext = '') {
	if($tooltip && $tooltext == '')
		$res = '<a class="iframex mvltooltip" title="'.__('No risk was identified.<br/>Click to learn more about this check.',MVLTD).'" href="' . trailingslashit(MVIS_LITE_FURTHER_INFORMATION_URL) . $id;
	elseif($tooltip && $tooltext != '')
		$res = '<a class="iframex mvltooltip" title="'.$tooltext.'" href="' . trailingslashit(MVIS_LITE_FURTHER_INFORMATION_URL) . $id;
	else
		$res = '<a class="iframex" href="' . trailingslashit(MVIS_LITE_FURTHER_INFORMATION_URL) . $id;
	
	if ($aid == '')
		$res .= '.html"';
	else
		$res .= '.html#' . $aid . '"';
	
	if($text != '')
		$res .= '>'.$text.'</span></a>';
	else
		$res .= '><span class="action-info"></span></a>';
	return($res);
}

function mvl_getFurtherInfo($id, $name, $vulnerable = ''){
	$response = '<a href="#" class="mvltooltip" title="'. __('An issue has been identified.<br/> Click to learn more about this threat.',MVLTD) .'" onclick="mvl_infobox(' . "'" . $id ."'" . ",'" . $name. "'";
	if($vulnerable != '')
		$response .= ",'" . $vulnerable. "'";
	$response .= ');return(false);"><span class="action-arrow"></span></a>';
	
	return($response);
}

function mvl_directUpgrade($name, $type, $text = ''){
	$response = '<a href="#" class="mvltooltip" title="'. __('Click to upgrade to the newest version.',MVLTD) .'" onclick="mvl_upgradebox(' . "'" . $name ."','" . $type ."'";
	$response .= ');return(false);">';
	if ($response != '')
		$response .= $text . '</a>';
	else
		$response .= '<span class="action-arrow"></span></a>';
	return($response);
}


function mvl_getInfoLink3($url) {
	$res = '<a href="' . mvl_htmlEncode($url) . '" class="iframex"><span class="action-arrow"></span></a>';
	return($res);
}

function mvl_getDownloadLink($url) {
	$res = '<a href="' . mvl_htmlEncode($url) . '">'. __('here',MVLTD).'</a>';
	return($res);
}

function mvl_getInfoLink($url) {
	$res = '<a href="' . mvl_htmlEncode($url). '">'. __('here',MVLTD).'</a>';
	return($res);
}

function mvl_state_sort($a, $b) {
	return($a['state'] < $b['state']);
}

function mvl_risk_sort($a, $b) {
	return($a['risk'] < $b['risk']);
}

function mvl_getRequestParam($name, $default = false) {
	if (isset($_REQUEST[$name])) {
		return($_REQUEST[$name]);
	} else {
		return($default);
	}
}

function mvl_getSummaryChecks(&$coreChecks, &$signal, $check_array,$type, $tooltip){
	foreach($check_array as $check) {
		$coreCheck = array();
		$coreCheck['type'] = '<a href="#" class="mvltooltip" title="'.$tooltip .'">'. mvl_htmlEncode($type) .'</a>';
		if (isset($check['name'])){
			$name = $check['name'];
		}elseif(isset($check['fileName'])){
			$name = $check['fileName'];
		}elseif(isset($check['type'])){
			$name = $check['type'];
		}
		$coreCheck['name'] = '<a href="#" class="mvltooltip" title="'.$check['message'] .'">' .mvl_htmlEncode($name) .'</a>';
		if ($check['state'] > $signal) $signal = $check['state'];
		$coreCheck['state'] = $check['state'];
		$coreChecks[] = $coreCheck;
	}
}

?>