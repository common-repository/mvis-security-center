<?php
function mvl_verifyNonce($nonce, $context = ''){
	if (wp_verify_nonce($nonce, $context) != 1)
		return false;
	else 
		return true;
}

function mvl_upgradeComponent($name,$type){
	include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	if($type == 'plugin' || $type == 'theme'){
		$nonce = 'upgrade-'.$type.'_' . $name;
		$url = 'update.php?action=upgrade-'.$type.'&'.$type.'=' . $name;
		
		if($type == 'plugin'){
			$title = __('Updating Plugin');
			$upgrader = new Plugin_Upgrader( new Plugin_Upgrader_Skin( compact('title', 'nonce', 'url', $type) ) );
		}else{
			$title = __('Updating Theme');
			$upgrader = new Theme_Upgrader( new Theme_Upgrader_Skin( compact('title', 'nonce', 'url', $type) ) );
		}
		$upgrader->upgrade($name);
	}
}

function mvl_getMVLInfo() {
	global $mvlState;	
	$res = array();
	$res['sys'] = 'Wordpress';
	$res['sys_version'] = mvl_getCMSVersion();
	$res['site'] = mvl_getSiteUrl();
	$res['mvl_version'] = MVIS_LITE_VERSION;
	$res['mvl_type'] = $mvlState->siteActive;
	$res['mvl_checkconfig_version'] = mvl_ReadOption(MVIS_LITE_OPT_NAME, 'checks_version');
	$res['mvl_vulnstatus_version'] = mvl_ReadOption(MVIS_LITE_OPT_NAME, 'vulnstatus_version');
	$res['php_version'] = mvl_getPHPVersion();
	$res['mysql_version'] = mvl_getMySQLVersion();
	return($res);
}


/*
used by getVersions and getVulnStatus
*/
function mvl_getMVLRequest($json = false) {
	$mvlRequest = array();
	$mvlRequest['info'] = mvl_getMVLInfo();

	$products = array();
	$item['displayname'] = 'Wordpress Wordpress ' . get_bloginfo('version');
	$item['productname'] = 'Wordpress';
	$item['vendor'] = 'Wordpress';
	$item['version'] = get_bloginfo('version');
	
	$products[] = $item;
	
	$plugins = mvl_getInstalledPlugins(true);
	foreach ($plugins as $plugin) {
		$item = array();
		$item['displayname'] = 'Wordpress ' . $plugin['product'] . ' ' . $plugin['version'];		
		$item['vendor'] = 'Wordpress';
		$item['productname'] = $plugin['product'];
		$item['version'] = $plugin['version'];
		$products[] = $item;
	}

	$themes = mvl_getInstalledThemes();
	foreach ($themes as $theme) {
		$item = array();
		$item['displayname'] = 'Wordpress ' . $theme['product'] . ' ' . $theme['version'];		
		$item['vendor'] = 'Wordpress';
		$item['productname'] = $theme['product'];
		$item['version'] = $theme['version'];
		$products[] = $item;
	}

	$mvlRequest['products'] = $products;
	if ($json) {
		return(json_encode($mvlRequest));
	} else {
		return($mvlRequest);		
	}
}

/*
used by createSite, updateSite
*/
function mvl_getThisSiteDetails($json = true) {
	$siteDetails = array();
	$siteDetails['name'] = mvl_getSiteUrl();
	$updateElements = mvl_getUpdateElements(true);
	
	$products = array();
	
	$item['displayname'] = 'Wordpress Wordpress ' . get_bloginfo('version');
	$item['vendor'] = 'Wordpress';
	$item['productname'] = 'Wordpress';
	$item['version'] = get_bloginfo('version');	
	$item['latestversion'] = get_bloginfo('version');
	$item['url'] = '';
	$item['updateurl'] = '';
	
	$products[] = $item;
	
	$plugins = mvl_getInstalledPlugins(true);

	foreach ($plugins as $plugin) {
		$item = array();
		$item['displayname'] = 'Wordpress ' . $plugin['product'] . ' ' . $plugin['version'];		
		$item['vendor'] = 'Wordpress';
		$item['productname'] = $plugin['product'];
		$item['version'] = $plugin['version'];
		$item['latestversion'] = $plugin['version'];
		$item['url'] = '';
		$item['updateurl'] = '';
		
		$products[] = $item;
	}
	
	$themes = mvl_getInstalledThemes();
	foreach ($themes as $theme) {
		$item = array();
		$item['displayname'] = 'Wordpress ' . $theme['product'] . ' ' . $theme['version'];		
		$item['vendor'] = 'Wordpress';
		$item['productname'] = $theme['product'];
		$item['version'] = $theme['version'];
		$item['latestversion'] = $theme['version'];
		$item['url'] = '';
		$item['updateurl'] = '';
		$products[] = $item;
	}
	
	foreach($updateElements as $update)
		if($update['newVersion']!= '')
			foreach($products as $product => $value)
				if($products[$product]['productname'] == $update['name']){
					$products[$product]['latestversion'] = $update['newVersion'];
					$products[$product]['updateurl'] = $update['packageclean'];
					$products[$product]['url'] = $update['urlclean'];
				}
		
	$siteDetails['products'] = $products;
	if ($json) {
		return(json_encode($siteDetails));
	} else {
		return($siteDetails);		
	}
}


/*
$all: all installed Plugins or only active Plugins
*/
function mvl_getInstalledPlugins($all = true) {
	$res = array();
	$plugins = array();
	if(function_exists('get_plugins'))
		$plugins = get_plugins();
	
	if ($all) {
		foreach($plugins as $plugin=>$details) {
			$pi = array();
			$pi['product'] = $details['Name'];
			$pi['version'] = $details['Version'];
			$pi['active'] = in_array( $plugin, (array) get_option( 'active_plugins', array() )) ? true : false;
			$res[] = $pi;
		}
	} else {
		$active  = get_option('active_plugins', array());
		foreach($active as $a) {
			$pi = array();
			$pi['product'] = $details['Name'];
			$pi['version'] = $plugins[$a]['Version'];
			$pi['active'] = true;
			$res[] = $pi;
		}
	}	
	return($res);
}


function mvl_getInstalledThemes() {
	$res = array();
	if(function_exists('wp_get_themes'))
		$themes = wp_get_themes(); // WP >= 3.4
	else
		$themes = get_themes();
	
	foreach($themes as $theme=>$details) {
		$th = array();
		$th['product'] = $details['Name'];
		$th['version'] = $details['Version'];
		$res[] = $th;
	}
	return($res);
}

function mvl_getWPUpdates() {
	$res = array();
	
	$wpUpdatesCore = get_site_transient('update_core');
	if (is_object($wpUpdatesCore)) {
		foreach($wpUpdatesCore->updates as $response) {		
			if ($response->response == 'upgrade') {
				$entry = array();
				$entry['type'] = 'Core';
				$entry['name'] = 'Wordpress ' . $response->locale;				
				$entry['url'] = 'http://wordpress.org';
				$entry['package'] = $response->download;				
				$entry['oldVersion'] = mvl_getCMSVersion();
				$entry['newVersion'] = $response->current;
				$res[] = $entry;
			}
		}
	}

	$wpUpdatesPlugins = get_site_transient('update_plugins');
	if (is_object($wpUpdatesPlugins)) {
		$response ='';
		$checked = isset($wpUpdatesPlugins->checked)?$wpUpdatesPlugins->checked:'';
		if (isset($wpUpdatesPlugins->response))
			$response = $wpUpdatesPlugins->response; 
		
		if($response != ''){
			foreach($response as $plugin=>$details) {
				$plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' .  $plugin);
				$entry = array();
				$entry['oldVersion'] = isset($checked[$plugin])?$checked[$plugin]:'';
				$entry['newVersion'] = $details->new_version;
				$entry['type'] = 'plugin';
				$entry['name'] = $name = $plugin_data['Name'];
				$entry['url'] = $details->url;
				$entry['package'] = $details->package;
				$entry['fileName'] = $plugin;
				$res[] = $entry;
			}	
		}
	}


	$wpUpdatesThemes = get_site_transient('update_themes');

	if (is_object($wpUpdatesThemes)) {
		$checked = $wpUpdatesThemes->checked;
		$response = $wpUpdatesThemes->response;
		foreach($response as $theme=>$details) {
			$entry = array();
			$entry['type'] = 'theme';
			if(function_exists('wp_get_theme')){ 		//for WP 3.4 and above
				$themename = @wp_get_theme($theme);
			}elseif(function_exists('get_theme_data')){	//deprecated call for WP < 3.4
				$themename = get_theme_data(get_theme_root() . '/' . $theme . '/style.css');
			}else{										// should never happen
				return(array());
			}
			$entry['name'] = $themename['Name'];
			//TODO: Verify that this works
			$entry['fileName'] = $theme;
			$entry['oldVersion'] = $themename['Version'];
  	  		$entry['newVersion'] = $details['new_version'];
		  	$entry['url'] = $details['url'];
	    	$entry['package'] = $details['package'];
			$res[] = $entry;
		}
	}
	return($res);
}



/*
Collect diverse System-Information
returns: array
*/
function mvl_getSysInfo() {
	$res = array();
//	$res['WordPress'] =	mvl_getCMSVersion();
	$res['PHP'] = 			mvl_getPHPVersion();
	$res['MySQL'] =			mvl_getMySQLVersion();
//	$res['Apache'] =	mvl_getApacheVersion(); 
//  Apache Version not used at the moment because dependent of Server Settings ... (ServerTokens) - not reliable
	return($res);
}

/*
Checks, if file (relative to ABSPATH) exists.
*/
function mvl_checkFileExists($filename) {
	$res = file_exists(ABSPATH . $filename);
	return($res);
}

function mvl_getCMSVersion() {
	$version = get_bloginfo('version');
	return($version);
}

function mvl_getSiteUrl($https = false) {
	$url = get_option('siteurl');
	if ($https){
		if(substr($url, 0,5) == 'http:'){
			$pure = substr($url,5);
			$url = 'https:' . $pure;
		}
	}
  return(trailingslashit($url));
  //if(NETWorksite)
  //return network_site_url();
  //else
  //return site_url();
}
 
/*
Returns:
option with name, array-element element (if not null)
if option is not found, it will be created and false will be returned except:
if default <> null it will be created and returned 
*/
function mvl_readOption($name, $element = null, $default = null) {
	$o = get_option($name);
// option does not exists
	if ($o == false) {
		$new = array();
		if ($element <> null) {
			$new[$element] = $default;
		}
		update_option($name, $new);
		return($new);
	}
//option already existed	...
	if ($element == null) {
		return($o);
	}
// ... and element was set 
	if (isset($o[$element])) {
		return($o[$element]);
	}
// ... and element was not set and should not be set
	if ($default == null) {
		return(null);
	}
// ... and element was not set and should be set
	$o[$element] = $default;
	update_option($name, $o);
	return($default);
}


function mvl_writeOption($name, $element, $value) {
	$o = get_option($name);
// option does not exists
	if ($o == false) {
		$new = array();
		$new[$element] = $value;
		update_option($name, $new);
	} else {
		$o[$element] = $value;
		update_option($name, $o);
	}
}

function mvl_deleteOption($name, $element) {
	$o = get_option($name);
	if ($o == false) {
// nothing to do		
	} else {
		if (isset($o[$element])) {
			unset($o[$element]);
			update_option($name, $o);
		}	
	}
}


?>