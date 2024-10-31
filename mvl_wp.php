<?php
/*
Plugin Name: MVIS Security Center
Plugin URI: http://wordpress.org/extend/plugins/mvis-security-center/
Author: SEC Consult
Author URI: https://www.sec-consult.com/en 
Version: 1.3.5
Description: MVIS Security Center shows you exactly how to lock down your setup and sends subscribed users real-time vulnerability alerts for their site.  
License: GPLv2 or later
*/

/* Stefan Streichsbier (email : s.streichsbier@sec-consult.com)

 This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

require_once('inc/mvl_config.php');
require_once('inc/mvl_wp_lib.php');
require_once('inc/mvl_core_lib.php');
require_once('inc/mvl_wp_checks.php');
require_once('inc/mvl_api.php');
require_once('inc/mvl_ajax.php');
require_once('inc/mvl_gui_lib.php');
require_once('inc/mvl_gui_steps.php');
require_once('inc/mvl_gui_profile.php');
require_once('inc/mvl_gui_subscribe.php');
require_once('inc/mvl_gui_summary.php');
require_once('inc/mvl_gui_help.php');
require_once('inc/mvl_gui_ajax.php');


//try to include tests
//@include_once('inc/mvl_test.php');
	

class c_mvlState {
}

$mvlState = new c_mvlState();


function mvl_initState() {
	global $mvlState;
	$mvlState->userRegistered = ((mvl_readOption(MVIS_LITE_OPT_NAME, 'userName') <> '') && (mvl_readOption(MVIS_LITE_OPT_NAME, 'authToken') <> ''));
	$siteDetails = mvl_readOption(MVIS_LITE_OPT_NAME, 'siteDetails');
	$mvlState->siteActive = ((isset($siteDetails['status'])) && ($siteDetails['status'] == 'ACTIVE'));
	$mvlState->agreeAutoLoad = mvl_readOption(MVIS_LITE_OPT_NAME, 'agreeAutoLoad', false);
	$mvlState->agreeTaC = mvl_readOption(MVIS_LITE_OPT_NAME, 'agreeTaC', false);
	$mvlState->newActivation = mvl_readOption(MVIS_LITE_OPT_NAME, 'newActivation');
	
	if (($mvlState->agreeTaC) && ($mvlState->newActivation)) {
		mvl_writeOption(MVIS_LITE_OPT_NAME, 'newActivation', false);
	}
	
	$mvlState->showSubscribe = !$mvlState->siteActive;
	$mvlState->showProfile = $mvlState->userRegistered;
	
	$mvl_checks_result = mvl_readOption(MVIS_LITE_OPT_CHECKS_RESULT, 'checks_result');
	if (isset($mvl_checks_result['lastRun'])) {
		$mvlState->lastChecksRun = $mvl_checks_result['lastRun'];
		$mvlState->lastChecksRunDT = date('D, d. F Y \a\t H:i:s', $mvl_checks_result['lastRun']);
	} else {
		$mvlState->lastChecksRun = 0;
		$mvlState->lastChecksRunDT = 'NEVER';
	}

	$mvlState->lastSync = mvl_readOption(MVIS_LITE_OPT_NAME, 'lastSync', 0);
	if ($mvlState->lastSync == 0) {
		$mvlState->lastSyncDT = 'NEVER';
	} else {
		$mvlState->lastSyncDT = date('Y/m/d - H:i', $mvlState->lastSync);
	}
	$mvlState->httpError = mvl_readOption(MVIS_LITE_OPT_NAME, 'httpError', false);

}

function mvl_sync(&$message) {	
	$message = '';
	$succeeded = true;
	if (mvl_readOption(MVIS_LITE_OPT_NAME, 'agreeAutoLoad', false) != 1) {
		$message = 'agreeAutoLoad = false';
		return(false);
	}
	if (mvl_readOption(MVIS_LITE_OPT_NAME, 'agreeTaC', false) != 1) {
		$message = 'agreeTaC = false';
		return(false);
	}
	
	$siteDetails = mvl_readOption(MVIS_LITE_OPT_NAME, 'siteDetails');
	$siteActive = ((isset($siteDetails['status'])) && ($siteDetails['status'] == 'ACTIVE'));

	$code = 0;	
	$apiRes = mvl_get_versions($code);
	if ($code <> 200) {
		$err = mvl_getApiError('get_versions', $code);
		$message = "ERROR getting versions, status: $err; details: " . print_r($apiRes, true);
		$succeeded = false;
	}else{
		$message .= "getVersions OK\r\n";
		$versions = $apiRes;
		mvl_writeOption(MVIS_LITE_OPT_NAME, 'versions', $versions);
	}
	$check_version = mvl_readOption(MVIS_LITE_OPT_NAME, 'checks_version', 0);
	if (intval($versions['SecCheckConfig']) > intval($check_version)) {
		$message .= "loading new SecCheckConfig\r\n";
		$apiRes = mvl_get_seccheckconfig($code);
		if ($code <> 200) {
			$err = mvl_getApiError('', $code);
			$message .= "ERROR getting secCheckConfig, status: $err; details: " . print_r($apiRes, true);
			$succeeded = false;
		}else{
			$message .= "getSecCheckConfig OK\r\n";
			mvl_writeOption(MVIS_LITE_OPT_CHECKS_CONFIG, 'checks_config', $apiRes);
			mvl_writeOption(MVIS_LITE_OPT_NAME, 'checks_version', $versions['SecCheckConfig']);
			mvl_deleteOption(MVIS_LITE_OPT_CHECKS_RESULT, 'checks_result');
		}
	}
	
	$vulnstatus_version = mvl_readOption(MVIS_LITE_OPT_NAME, 'vulnstatus_version', 0);
		$message .= "loading new VulnStatus\r\n";
		$apiRes = mvl_get_vulnstatus($code);
		if ($code <> 200) {
			$err = mvl_getApiError('', $code);
			$message .= "ERROR getting VulnerabilityStatus, status: $err; details: " . print_r($apiRes, true);
			$succeeded = false;
		}else{
			$message .= "getVulnStatus OK\r\n";
			mvl_writeOption(MVIS_LITE_OPT_VULNSTATUS, 'vuln_status', $apiRes);
			mvl_writeOption(MVIS_LITE_OPT_NAME, 'vulnstatus_version', $versions['VulnStatus']);
		}

	if ($siteActive) {
		$oldSiteDetailsHash = mvl_readOption(MVIS_LITE_OPT_NAME, 'sitedetails_hash', '');
		$thisSiteDetails = mvl_getThisSiteDetails(true);
		$newSiteDetailsHash = sha1($thisSiteDetails);
		if ($oldSiteDetailsHash <> $newSiteDetailsHash) {
			$message .= "updating site\r\n";
			$userName = mvl_readOption(MVIS_LITE_OPT_NAME, 'userName');
			$authToken = mvl_readOption(MVIS_LITE_OPT_NAME, 'authToken');
			$siteDetails = mvl_readOption(MVIS_LITE_OPT_NAME, 'siteDetails');
			$apiRes = mvl_updateSite($code, $userName, $authToken, $siteDetails['id'], $thisSiteDetails);
			if ($code <> 200) {
				$err = mvl_getApiError('updateSite', $code);			
				$message .= "ERROR updating Site, $err; details: " . print_r($apiRes, true);
				$succeeded = false;
			}else{
				$message .= "updateSite OK\r\n";
				mvl_writeOption(MVIS_LITE_OPT_NAME, 'sitedetails_hash', $newSiteDetailsHash);
			}			
		}

		//Loading sitealerts
		$message .= "loading siteAlerts\r\n";
		$userName = mvl_readOption(MVIS_LITE_OPT_NAME, 'userName');
		$authToken = mvl_readOption(MVIS_LITE_OPT_NAME, 'authToken');
		$siteDetails = mvl_readOption(MVIS_LITE_OPT_NAME, 'siteDetails');
		$apiRes = mvl_getSiteAlerts($code, $userName, $authToken, $siteDetails['id']);
		if ($code <> 200) {
			$err = mvl_getApiError('getSiteAlerts', $code);			
			$message .= "ERROR getting Site Alerts, $err; details: " . print_r($apiRes, true);
			$succeeded = false;
		}else{
			$message .= "getSiteAlerts OK\r\n";
			mvl_writeOption(MVIS_LITE_OPT_SITEALERTS, 'site_alerts', $apiRes);
		}
	}	

	return($succeeded);
}

function mvl_manualSync($cron = true) {
	global $mvlState;
	$lastSync = mvl_readOption(MVIS_LITE_OPT_NAME, 'lastSync', 0);
	$now = time();
	$message = '';
	
	if ($now < ($lastSync + MVL_SYNC_INTERVAL) && !$cron){
		return;
	}
	
	
	$res = mvl_sync($message);
	if (!$res) {
		//TODO: Log errors in an object and inform user
		$err = $message;
	}
	mvl_writeOption(MVIS_LITE_OPT_NAME, 'lastSync', $now);
	$mvlState->lastSyncDT = date('Y/m/d - H:i', $now);
	mvl_initState();
}


function mvl_Main() {
	global $mvlState;
	mvl_initState();	
	mvl_initChecksConfig();
	
	//Schedule WP_CRON job
	if(!wp_next_scheduled('sync_daily'))
		wp_schedule_event(time(), 'daily', 'sync_daily');

	//Do inital Sync if allowed
	if(mvl_readOption(MVIS_LITE_OPT_NAME,'doInitialSync') == true){
		mvl_writeOption(MVIS_LITE_OPT_NAME, 'doInitialSync', false);
		mvl_manualSync();
	}else
		mvl_manualSync(false);
	
	isset($_REQUEST['p']) ? $p = intval($_REQUEST['p']) : $p = 0;
	if (defined('MVL_TEST') && ($p >= 800)) {
		$page = mvl_processTest($p);
	} else {
		if ($mvlState->agreeTaC != true) {
			if ($p <> 6) {
				$p = -1;
			}
		}
		//To catch the scenario that the user has not clicked on the "return to plugin" button in the iframe after the successful subscription
		if(!$mvlState->siteActive && $p != 15){ 
			$userName = mvl_readOption(MVIS_LITE_OPT_NAME, 'userName');
			$authToken = mvl_readOption(MVIS_LITE_OPT_NAME, 'authToken');
			$siteDetails = mvl_readOption(MVIS_LITE_OPT_NAME, 'siteDetails');
		
			if( $userName != '' && $authToken != '' && is_array($siteDetails) && isset($siteDetails['id'])){
				$apiRes = mvl_getSiteDetails($code, $userName, $authToken, $siteDetails['id']);
				if ($code == 200)
					mvl_updateSiteDetailsAndSync($apiRes);
			}
		}
		
		switch ($p) {
			case 1:
			case 2:
			case 3:
			case 4:
				$page = mvl_getPageSteps($p);
				break;
			//case 5:
			//	$page = mvl_getPageTools();
			//	break;
			case 6:
				$page = mvl_getPageHelp();
				break;
			case 7:
				mvl_doUserChecks(1);
				$page = mvl_getPageSteps(3);
				break;
			case 8:
				mvl_doCoreChecks(1);
				$page = mvl_getPageSteps(3);
				break;
			case 9:
				mvl_doUpdateChecks();
				$page = mvl_getPageSteps(3);
				break;
				
// Subscription Page
			case 10: // Subscribe Tab 1
			case 11: // Login
			case 12: // Sign Up
			case 13: // Chosen Product
			case 15: // callBack from Paypal ... SUCCESS
			case 16: // callBack from Paypal ... ERROR
				$page = mvl_processSubscribe($p);
				break;
	
// Profile Page
			case 20: // Profile Page
			case 21: // Change Password
			case 24: // Sync Site
			case 25: // Resend Verification
			case 26: // Toggle Summary E-Mails
				$page = mvl_processProfile($p);
				break;
			
			case 22: // Delete Site
			case 23: // Delete Account
				$page = mvl_processSummary($p);
				break;
				
			case 28: // Logout
				$err = mvl_Logout(mvl_getRequestParam('_wpnonce'));
				$page = mvl_processSummary(0,$err);
				break;
			
			case 29: // Login
				$err = mvl_Login(mvl_getRequestParam('email'),mvl_getRequestParam('password'));
				$page = mvl_processSummary(0,$err);
				break;
			
	
// Summary Page
			case 40:
			case 41: // rerun Checks
			case 42: // resync manually after httpError
			case 43: // reset Password
			case 44: 
				$page = mvl_processSummary($p);
				break;
	
			default:
				$page = mvl_processSummary($p);
		}
	}		
	echo($page);
}


function mvl_Menu() {
	if(is_multisite()){
		if(is_super_admin()){
			$plugin_page = add_menu_page('MVIS Security Center', 'Security Center', 'manage_options', 'mvl_wp.php', 'mvl_Main', plugins_url('mvis-security-center/images/mvis.png'));
			add_action( 'admin_footer-'. $plugin_page, 'mvl_admin_footer' );
		}
	}elseif(current_user_can('update_plugins')){
		$plugin_page = add_menu_page('MVIS Security Center', 'Security Center', 'manage_options', 'mvl_wp.php', 'mvl_Main', plugins_url('mvis-security-center/images/mvis.png'));
		add_action( 'admin_footer-'. $plugin_page, 'mvl_admin_footer' );
	}
}

function mvl_plugin_init() {
  load_plugin_textdomain(MVLTD, false, dirname(plugin_basename( __FILE__ )) . '/languages/'); 
}

function mvl_plugin_activate(){
	mvl_writeOption('mvl_core', 'newActivation', true);
}

function mvl_plugin_deactivate() {
	if(wp_next_scheduled( 'sync_daily' ))
		wp_clear_scheduled_hook('sync_daily');
}

function mvl_sync_daily(){
	mvl_manualSync();
}

function mvl_admin_footer(){
	$content = '<div class="logo-container">';
	$content .= __('powered by',MVLTD). ' <a href="https://www.sec-consult.com/en/" target="_blank"><span class="logo-sec"></span></a> ';
	$content .= __('and',MVLTD). ' <a href="https://www.sec-consult.com/en/mvis.html" target="_blank"><span class="logo-mvis"></span></a> ';
	$content .= ' designed by <a href="http://www.topart-media.at/" target="_blank"><span class="logo-topart"></span></a>';
	$content .= '</div>';
	echo $content;
}

register_activation_hook( __FILE__, 'mvl_plugin_activate' );
register_deactivation_hook(__FILE__, 'mvl_plugin_deactivate');
add_action('admin_menu', 'mvl_Menu');
add_action('admin_print_scripts', 'enqueue_mvl_scripts' );
add_action('admin_print_styles', 'enqueue_mvl_styles' ); 
add_action('plugins_loaded', 'mvl_plugin_init');
add_action('sync_daily', 'mvl_sync_daily');

?>
