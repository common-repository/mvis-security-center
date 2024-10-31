<?php
add_action('admin_head', 'mvl_action_javascript');

// Trigger request

function mvl_action_javascript() {
?>
<script type="text/javascript" >

function mvl_bonus_comply($id, $value) {
	var data = {
		action: 'mvl_comply',
		id: $id,
		val: $value
	};
	jQuery.post(ajaxurl, data, function(response) {
	});
}

function mvl_agree_autoload($value) {
	var data = {
		action: 'mvl_agree_al',
		val: $value
	};
	jQuery.post(ajaxurl, data, function(response) {
	});
}

function mvl_agree_selfregister($value) {
	var data = {
		action: 'mvl_agree_selfregister',
		val: $value
	};
	jQuery.post(ajaxurl, data, function(response) {
	});
}

function mvl_agree_tac($value) {
	var data = {
		action: 'mvl_agree_tac',
		val: $value
	};
	jQuery.post(ajaxurl, data, function(response) {
	});
}

function mvl_set_price($value) {
	var data = {
		action: 'mvl_set_price',
		val: $value
	};
	jQuery.post(ajaxurl, data, function(response) {});
}

function mvl_infobox($value,$infotype,$vulnerable) {
	var data = {
		action: 'mvl_infobox',
		val: $value,
		info: $infotype,
		vulnerable: $vulnerable
	};
	jQuery.post(ajaxurl, data, function(response) {jQuery.colorbox({html:response,iframe:false,width:"620px", height:"80%",scrolling:true,fastIframe:false},function(){jQuery('#modalajax').colorbox({iframe:true,width:"80%",height:"80%",scrolling:true,fastIframe:false});});});
	return(false);
}

function mvl_upgradebox($name, $type) {
	var data = {
		action: 'mvl_upgradebox',
		name: $name,
		type: $type
	};
	jQuery.post(ajaxurl, data, function(response) {jQuery.colorbox({html:response,iframe:false,width:"1px", height:"1px",scrolling:false});});
	return(false);
}

function mvl_loginbox() {
	var data = {
		action: 'mvl_loginbox',
	};
	jQuery.post(ajaxurl, data, function(response) {jQuery.colorbox({html:response,iframe:false,width:"490px", height:"240px",scrolling:false,fastIframe:false});});
	return(false);
}

function mvl_resendbox(){
	var data = {
		action: 'mvl_resendbox',
	};
	jQuery.post(ajaxurl, data, function(response) {jQuery.colorbox({html:response, iframe:false,width:"490px",height:"310px",scrolling:false,fastIframe:false});});
	return(false);
}

function mvl_forgotPWD(){
	var data = {
		action: 'mvl_forgotpwd',
	};
	jQuery.post(ajaxurl, data, function(response) {jQuery.colorbox({html:response, iframe:false,width:"490px",height:"330px",scrolling:false,fastIframe:false});});
	return(false);
}

function mvl_refresh_captcha() {
	jQuery('#mvis-captcha').attr('src', 'data:image/jpeg;base64,' + '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');
	var data = {
		action: 'mvl_refresh_captcha'
	};
	jQuery.post(ajaxurl, data, function(response) {
		var index = response.indexOf("XSECDELIMX");
		var captchaBody = response.substring(index+10);
		jQuery('#mvis-captcha').attr('src', 'data:image/jpeg;base64,' + captchaBody);
	}
	);
}
function mvl_refresh_price($value,$type) {
	jQuery('#mvis-pricing').text('Calculating...');
	var data = {
			action: 'mvl_refresh_price',
			value: $value,
			type: $type
		};
	jQuery.post(ajaxurl, data, function(response) {
		var index = response.indexOf("|");
		var pricetotal = response.substring(0,index);
		var pricesaved = response.substring(index+1);
		jQuery('#mvis-pricing').text(pricetotal);
		jQuery('#mvis-saving').text(pricesaved);
	}
	);
}
</script>
<?php

}

// Handler

add_action('wp_ajax_mvl_refresh_captcha', 'mvl_refresh_captcha_callback');
add_action('wp_ajax_mvl_comply', 'mvl_comply_callback');
add_action('wp_ajax_mvl_agree_selfregister', 'mvl_agree_selfregister_callback');
add_action('wp_ajax_mvl_agree_al', 'mvl_agree_al_callback');
add_action('wp_ajax_mvl_agree_tac', 'mvl_agree_tac_callback');
add_action('wp_ajax_mvl_infobox', 'mvl_infobox_callback');
add_action('wp_ajax_mvl_loginbox', 'mvl_loginbox_callback');
add_action('wp_ajax_mvl_set_price', 'mvl_set_price');
add_action('wp_ajax_mvl_resendbox', 'mvl_resendbox_callback');
add_action('wp_ajax_mvl_forgotpwd', 'mvl_forgotpwd_callback');
add_action('wp_ajax_mvl_upgradebox', 'mvl_upgradebox_callback');
add_action('wp_ajax_mvl_refresh_price', 'mvl_refresh_price_callback');

function mvl_agree_selfregister_callback() {
	if (isset($_POST['val'])) {
		$val = ($_POST['val']);
	} else {
		die();
	}
	if ($val == 'true') {
		mvl_writeOption(MVIS_LITE_OPT_NAME, 'SRchecked', 'checked');
		die();
	}
	if ($val == 'false') { 
		mvl_writeOption(MVIS_LITE_OPT_NAME, 'SRchecked', '');
		die();
	}
	die();
}


function mvl_refresh_price_callback(){
	if (isset($_POST['value']) && isset($_POST['type'])) {
		$p = mvl_readOption(MVIS_LITE_OPT_NAME, 'productsArray');
		$set_quantity = mvl_readOption(MVIS_LITE_OPT_NAME,'squantity');
		$set_duration = mvl_readOption(MVIS_LITE_OPT_NAME,'sduration');
		$set_currency = mvl_readOption(MVIS_LITE_OPT_NAME,'scurrency');
		
		if($_POST['type'] == 'q'){
			$set_quantity = $_POST['value'];
			if(!isset($set_duration)) $set_duration = 365;
			if(!isset($set_currency)) $set_currency = 'eur';
			for($i=1;$i<=intval($p['available']);$i++){
				if(intval($p['mvis'.$i.'quantity']) == intval($set_quantity) && $p['mvis'.$i.'duration'] == $set_duration){
					mvl_writeOption(MVIS_LITE_OPT_NAME, 'currentProduct', $i);
					$normal_price = intval(intval($p['mvis1'.$set_currency])*intval($set_duration/365)*intval($set_quantity));
					$current_price = intval($p['mvis'.$i.$set_currency]);
					$saving = $normal_price - $current_price;
					echo $p['mvis'.$i.$set_currency] . '|';
					echo $saving . '.00 ' . strtoupper($set_currency);
				}
					
			}
			mvl_writeOption(MVIS_LITE_OPT_NAME, 'squantity', $set_quantity);
		}
		
		if($_POST['type'] == 'd'){
			$set_duration = $_POST['value'];
			if(!isset($set_quantity)) $set_quantity = 1;
			if(!isset($set_currency)) $set_currency = 'eur';
			for($i=1;$i<=intval($p['available']);$i++){
				if(intval($p['mvis'.$i.'duration']) == intval($set_duration) && $p['mvis'.$i.'quantity'] == $set_quantity){
					mvl_writeOption(MVIS_LITE_OPT_NAME, 'currentProduct', $i);
					$normal_price = intval(intval($p['mvis1'.$set_currency])*intval($set_duration/365)*intval($set_quantity));
					$current_price = intval($p['mvis'.$i.$set_currency]);
					$saving = $normal_price - $current_price;
					echo $p['mvis'.$i.$set_currency] . '|';
					echo $saving . '.00 ' . strtoupper($set_currency);
				}
			}
			mvl_writeOption(MVIS_LITE_OPT_NAME, 'sduration', $set_duration);
		}
		
		if($_POST['type'] == 'c'){
			$set_currency = $_POST['value'];
			if(!isset($set_quantity)) $set_quantity = 1;
			if(!isset($set_duration)) $set_duration = 365;
			for($i=1;$i<=intval($p['available']);$i++){
				if(intval($p['mvis'.$i.'duration']) == intval($set_duration) && $p['mvis'.$i.'quantity'] == $set_quantity){
					mvl_writeOption(MVIS_LITE_OPT_NAME, 'currentProduct', $i);
					$normal_price = intval(intval($p['mvis1'.$set_currency])*intval($set_duration/365)*intval($set_quantity));
					$current_price = intval($p['mvis'.$i.$set_currency]);
					$saving = $normal_price - $current_price;
					echo $p['mvis'.$i.$set_currency] . '|';
					echo $saving . '.00 ' . strtoupper($set_currency); 
				}
					
			}
			mvl_writeOption(MVIS_LITE_OPT_NAME, 'scurrency', $set_currency);
		}
	}
	die();
}

function mvl_upgradebox_callback(){
	if (isset($_POST['name']) && isset($_POST['type'])) {
		mvl_upgradeComponent($_POST['name'],$_POST['type']);
		echo '<script>window.location.href="' . mvl_getAbsoluteAdminUrl(9)  .'";</script>';
	}
	die();
}

function mvl_refresh_captcha_callback() {
	mvl_deleteOption(MVIS_LITE_OPT_NAME, 'captchaId');
	mvl_deleteOption(MVIS_LITE_OPT_NAME, 'captcha');
	mvl_deleteOption(MVIS_LITE_OPT_NAME, 'captchaSrc');
	$apiRes = mvl_get_captcha($code, $id);
	if ($code == 200) {
		mvl_writeOption(MVIS_LITE_OPT_NAME, 'captchaId', $id);
		$captcha = "<img id=\"mvis-captcha\" src=\"data:image/jpeg;base64,$apiRes\">\r\n";
		mvl_writeOption(MVIS_LITE_OPT_NAME, 'captcha', $captcha);
		mvl_writeOption(MVIS_LITE_OPT_NAME, 'captchaSrc', "data:image/jpeg;base64,".$apiRes);
		//Return the base64 encoded image to the javascript caller
		echo 'XSECDELIMX' . $apiRes;
	}else{
		mvl_writeOption(MVIS_LITE_OPT_NAME, 'captchaSrc', "Error: ".$apiRes);
		echo "Error has occured.";
	}
	die();
}

function mvl_set_price() {
	//global $wpdb;
	if (isset($_POST['val'])) {
		$currency = strtoupper($_POST['val']);
	} else {
		die();
	}
	if ($currency != 'EUR' || $currency != 'SGD' || $currency != 'USD'){
	}else{
		$currency = 'EUR';
	}
	mvl_writeOption(MVIS_LITE_OPT_NAME, 'currency', $currency);
	die();
}

function mvl_comply_callback() {
	//global $wpdb;
	if (isset($_POST['id'])) {
		$id = intval($_POST['id']);
	} else {
		die();
	}
	if (isset($_POST['val'])) {
		$val = ($_POST['val']);
	} else {
		die();
	}
	if ($val == 'true') {
		mvl_writeOption(MVIS_LITE_OPT_COMPLIED, $id, 1);
		die();
	}
	if ($val == 'false') {
		mvl_writeOption(MVIS_LITE_OPT_COMPLIED, $id, 0);
		die();
	}
	die();
}

function mvl_agree_al_callback() {
	//global $wpdb;
	if (isset($_POST['val'])) {
		$val = ($_POST['val']);
	} else {
		die();
	}
	if ($val == 'true') {
		mvl_writeOption(MVIS_LITE_OPT_NAME, 'agreeAutoLoad', true);
		if(mvl_readOption(MVIS_LITE_OPT_NAME,'agreeTaC'))
			mvl_writeOption(MVIS_LITE_OPT_NAME, 'doInitialSync', true);
		die();
	}
	if ($val == 'false') {
		mvl_writeOption(MVIS_LITE_OPT_NAME, 'agreeAutoLoad', false);
		die();
	}
	die();
}


function mvl_agree_tac_callback() {
	//global $wpdb;
	if (isset($_POST['val'])) {
		$val = ($_POST['val']);
	} else {
		die();
	}
	if ($val == 'true') {
		mvl_writeOption(MVIS_LITE_OPT_NAME, 'agreeTaC', true);
		mvl_writeOption(MVIS_LITE_OPT_NAME, 'newActivation', false);
		if(mvl_readOption(MVIS_LITE_OPT_NAME,'agreeAutoLoad'))
			mvl_writeOption(MVIS_LITE_OPT_NAME, 'doInitialSync', true);
		die();
	}
	if ($val == 'false') {
		mvl_writeOption(MVIS_LITE_OPT_NAME, 'agreeTaC', false);
		die();
	}
	die();
}

function mvl_loginbox_callback(){
	echo(mvl_FormatLoginBox());
	die();
}

function mvl_resendbox_callback(){
	echo(mvl_FormatResendBox());
	die();
}

function mvl_forgotpwd_callback(){
	echo(mvl_FormatForgotPWD());
	die();
}

function mvl_infobox_callback() {
	global $wpdb;
	if (isset($_POST['val'])) {
		$val = ($_POST['val']);
	} else {
		echo(__('No ID was supplied.',MVLTD));
		die();
	}
	
	if (isset($_POST['info']))
		$info = $_POST['info'];
	else
		$info = 'info';
	
	if (isset($_POST['vulnerable']))
		$vulnerable = $_POST['vulnerable'];
	
	//TODO: Functionize the spaghetti
	if($info == 'sitealert'){
		$siteAlerts = mvl_readOption(MVIS_LITE_OPT_SITEALERTS, 'site_alerts');
		$siteAlert = @$siteAlerts[$val-1];
		$wpUpdates = mvl_readOption(MVIS_LITE_OPT_NAME, 'updateElements');
		foreach ($wpUpdates as $wpUpdate){ 
			if ($wpUpdate['name'] == $siteAlert['Products'][0]['productname'])
				break;
		}
		if ($siteAlert) {
			echo(mvl_FormatSiteAlert($siteAlert,$wpUpdate));
			die();
		}
	}elseif($info == 'updatecheck'){
		$wpUpdates = mvl_readOption(MVIS_LITE_OPT_NAME, 'updateElements');
		foreach ($wpUpdates as $wpUpdate){
			if ($wpUpdate['name'] == $val){
				break;
			}
		}
		
		echo(mvl_FormatUpdates($wpUpdate,$vulnerable));
		die();
	}elseif($info == 'usercheck'){
		$usersData = mvl_readOption(MVIS_LITE_OPT_NAME, 'userData');
		foreach ($usersData as $usersData){
			if ($usersData['id'] == $val){
				break;
			}
		}
		
		echo(mvl_FormatUserChecks($usersData));
		die();
	}elseif ($info == 'filecheck'){
		$mvl_checks_result = mvl_readOption(MVIS_LITE_OPT_CHECKS_RESULT, 'checks_result');
		$fileChecks = $mvl_checks_result['fileChecks'];
		foreach ($fileChecks as $check){
			if($check['id'] == $val){
				echo(mvl_FormatFileCheck($check));
				break;
			}
		}
		die();
	}elseif ($info == 'permissioncheck'){
		$mvl_checks_result = mvl_readOption(MVIS_LITE_OPT_CHECKS_RESULT, 'checks_result');
		$permChecks = $mvl_checks_result['permissionChecks'];
		foreach ($permChecks as $check){
			if($check['id'] == $val){
				echo(mvl_FormatPermissionCheck($check));
				break;
			}
		}
		die();
	}elseif ($info == 'backendChecks'){
		$mvl_checks_result = mvl_readOption(MVIS_LITE_OPT_CHECKS_RESULT, 'checks_result');
		$backendChecks = $mvl_checks_result['backendChecks'];
		foreach ($backendChecks as $check){
			if($check['id'] == $val){
				echo(mvl_FormatBackEndCheck($check));
				break;
			}
		}
		die();
	}elseif ($info == 'phpsettingcheck'){
		$mvl_checks_result = mvl_readOption(MVIS_LITE_OPT_CHECKS_RESULT, 'checks_result');
		$phpSettingsChecks = $mvl_checks_result['phpSettingsChecks'];
		foreach ($phpSettingsChecks as $check){
			if($check['id'] == $val){
				echo(mvl_FormatPhpSettingCheck($check));
				break;
			}
		}
		die();
	}elseif ($info == 'wpbcheck'){
		$mvl_checks_result = mvl_readOption(MVIS_LITE_OPT_CHECKS_RESULT, 'checks_result');
		$WPbackendChecks = $mvl_checks_result['WPbackendChecks'];
		foreach ($WPbackendChecks as $check){
			if($check['id'] == $val){
				echo(mvl_FormatWPBCheck($check));
				break;
			}
		}
		die();
	}elseif ($info == 'dbcheck'){
		$mvl_checks_result = mvl_readOption(MVIS_LITE_OPT_CHECKS_RESULT, 'checks_result');
		$DBChecks = $mvl_checks_result['DBbackendChecks'];
		foreach ($DBChecks as $check){
			if($check['id'] == $val){
				echo(mvl_FormatDBCheck($check));
				break;
			}
		}
		die();
	}elseif ($info == 'information'){
		die();
	}
	
	echo(__('An error has occured!',MVLTD));
	die();
}

