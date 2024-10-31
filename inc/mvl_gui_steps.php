<?php

function mvl_getPageTools() {
	global $mvl_checks_config;
	global $mvlState;
	$text = 'Description';
	$content = mvl_getPageStart('tools', 'Tools', $text, $mvlState->showSubscribe, $mvlState->showProfile);
	$content .= '<div class="container">
	<p>This is just some text of the TOOLS page ...</p>
	<p>It is - of course - configurable via the config-file and thus dynamically changeable.</p>
	</div>';
	$content .= mvl_getPageEnd();
	return($content);
}


function mvl_getPageSteps($p) {
	global $mvl_checks_config;
	global $mvlState;
	$tab1 = ''; $tab2 = ''; $tab3 = ''; $tab4 = '';
	$sig1 = 0; $sig2 = 0; $sig3 = 0; $sig4 = 0;

	// read results from option and use it to display
	$mvl_checks_result = mvl_readOption(MVIS_LITE_OPT_CHECKS_RESULT, 'checks_result');
	if (!isset($mvl_checks_result['lastRun'])) {
		mvl_doAllChecks();
		$mvl_checks_result = mvl_readOption(MVIS_LITE_OPT_CHECKS_RESULT, 'checks_result');
	}

	$text = '<strong>' . __('Security was never this simple!',MVLTD) . '</strong>';

	$content = mvl_getPageStart('steps', __('3 Steps to Secure WordPress',MVLTD), $text, $mvlState->showSubscribe, $mvlState->showProfile);

	$content .= '<br /><div class="container">';
	$content .= '' . __('Click on the actions of the orange and red dots and find out how to turn them into green ones.',MVLTD);


	// Update Check - Wordpress Core Version
	// ----------------
	$updateElements = mvl_getUpdateElements(true);
	mvl_writeOption(MVIS_LITE_OPT_NAME, 'updateElements', $updateElements);
	$rows = '';
	$core_update_or_vuln = 0;
	$risk = 1;
	$oldVersion = mvl_getCMSVersion();
	$newVersion = '';
	$url = '';
	$package = '';
	foreach($updateElements as $wpUpdate) {
		if ($wpUpdate['type'] == 'Core') {
			$core_update_or_vuln=1;
			$newVersion = $wpUpdate['newVersion']!= '' ? '<a class="mvltooltip" title="' . __('A new version is available, update as soon as possible!',MVLTD) .'">' . $wpUpdate['newVersion'] : '<a class="mvltooltip" title="' . __('There is no new version available.',MVLTD) .'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			$url = $wpUpdate['url'];
			$package = $wpUpdate['package'];
			$alertId = $wpUpdate['alertId'];
			$risk = ($wpUpdate['risk'] == 2 ? '<a class="mvltooltip" title="' . __('A new version is available, update as soon as possible.',MVLTD) .'">' . mvl_getStateStyle($wpUpdate['risk'], true) : '<a class="mvltooltip" title="' . __('The installed version contains known vulnerabilities, update immidiately!',MVLTD) .'">' . mvl_getStateStyle($wpUpdate['risk'], true));
			if ($alertId > 0){
				//$alertUrl = '<a href="#" onclick="mvl_infobox(' . "'" . $alertId ."'" . ",'sitealert'" . ');return(false);"><span class="action-arrow"></span></a>';
				$alertUrl = mvl_getFurtherInfo($alertId, 'sitealert');
				$rows .= "<tr><td>WordPress</td><td class=\"align-center\">$oldVersion</center></td><td><center>$newVersion</center></td><td class=\"align-center\">" .$risk ." </td><td class=\"align-right\">$alertUrl</td></tr>";
			}else{
				//$updateUrl = '<a href="#" onclick="mvl_infobox(' . "'" . $wpUpdate['name'] ."'" . ",'updatecheck'" . ');return(false);"><span class="action-arrow"></span></a>';
				$updateUrl = mvl_getFurtherInfo($wpUpdate['name'],'updatecheck');
				$rows .= "<tr><td>WordPress</td><td class=\"align-center\">$oldVersion</center></td><td><center>$newVersion</center></td><td class=\"align-center\">" .$risk ." </td><td class=\"align-right\">$updateUrl</td></tr>";
			}
				
			break;
		}
	}
	if(!$core_update_or_vuln){
		$oldVersion= mvl_getCMSVersion();
		$infoUrl = mvl_getInfoLinkMod(MVL_UPDATECHECK_GENERAL);
		$risk = 1;
		$rows .= '<tr><td>WordPress</td><td class="align-center">'. $oldVersion .'</center></td><td><center>-</center></td><td class="align-center"><a class="mvltooltip" title="' . __('No security risk has been identified.',MVLTD) ."\">" . mvl_getStateStyle($risk, true) . '</td><td class="align-right">'.$infoUrl.'</td></tr>';
	}
	$sig1 =$wpUpdate['risk'];

	// Display some Sysinfo for the moment
	$sysInfo = mvl_getSysInfo();
	foreach($sysInfo as $k=>$v) {
		$state = mvl_getStateStyle(0, true);
		$infoUrl = mvl_getInfoLinkMod(MVL_UPDATECHECK_GENERAL);
		$rows .= '<tr><td>'.mvl_htmlEncode($k).'</td><td><center>'.mvl_htmlEncode($v).'</center></td><td><center>-</center></td><td class="align-center"><a class="mvltooltip" title="' . __('No risk is known for this component.',MVLTD) .'">' . $state .'</td><td class="align-right">'.$infoUrl.'</td></tr>';
	}
	$tab1 .= __('The Update Check shows you what components of WordPress are vulnerable or need updating. This changes often, so keep checking back!',MVLTD);
	if ((!$mvlState->userRegistered || !$mvlState->siteActive))
		$tab1 .= mvl_getSubscribtionBenefits();
	else 
		$tab1 .= '<br/><br/>';
	$tab1 .= mvl_getTabTable(__('Core Versions', MVLTD), array(__('Name',MVLTD), __('Installed',MVLTD), __('Update',MVLTD), __('Risk',MVLTD), __('Actions',MVLTD)), $rows, false, $sig1);
	// ----------------


	// Update Check - WP Plugin and Themes
	// ----------------
	$count = 0;
	$showAll=0;
	$rows = '';
	$signal = 0;

	foreach($updateElements as $updateElement) {
		$showUrl = true;
		$showPackage = true;
		if ($updateElement['type'] == 'Core')
			continue;
		$count += 1;
		$type = $updateElement['type'];
		$risk = $updateElement['risk'];
		$vulnerable = $updateElement['risk'] == 3 ? 1 : 0;
		if ($risk > $sig1) $sig1 = $risk;
		if ($risk > $signal) $signal = $risk;
		$name = mvl_htmlEncode($updateElement['name']);
		$oldVersion = mvl_htmlEncode($updateElement['oldVersion']);
		$newVersion = $updateElement['newVersion']!= '' ? '<a class="mvltooltip" title="' . __('A new version is available, update as soon as possible!',MVLTD) .'">' . mvl_htmlEncode($updateElement['newVersion']) : '<a class="mvltooltip" title="' . __('There is no new version available.',MVLTD) .'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		$url = $updateElement['url'] != '' ? $updateElement['url'] : $showUrl=false;
		$package = $updateElement['package'] != '' ? $updateElement['package'] : $showPackage=false;

		if(isset($updateElement['active']))
			$showInfos = "<td class=\"align-right\">" . mvl_getInfoLinkMod(MVL_UPDATECHECK_GENERAL) . "</td>";

		if ($type == 'theme')
			$showInfos = "<td class=\"align-right\">" . mvl_getInfoLinkMod(MVL_UPDATECHECK_GENERAL) . "</td>";

		if($showUrl || $showPackage){
			$showInfos = '<td class="align-right">' . mvl_getFurtherInfo($updateElement['name'],'updatecheck',$vulnerable) .'</td>';
			
		}
			
		$state = mvl_getStateStyle($risk);
		$alertId = $updateElement['alertId'];

		if ($alertId > 0)
			$showInfos = '<td class="align-right">'. mvl_getFurtherInfo($alertId,'sitealert').'</td>';

		if($type == 'theme')
			$type = __('Theme',MVLTD);			
		$rows .= '<tr><td>'.mvl_htmlEncode($type).'</td><td>'.$name.'</td><td class="align-center">'.$oldVersion.'</td><td class="align-center">'.$newVersion.'</td><td class="align-center">'.$state.'</td>'.$showInfos.'</tr>';
		if ($count == 6)
			$rows .= '</tbody><tbody class="show-more">';
		elseif($count > 6)
		$showAll = 1;
	}
	$tab1 .= mvl_getTabTable(__('WordPress Plugin and Theme Versions',MVLTD), array(__('Type',MVLTD), __('Name',MVLTD), __('Installed',MVLTD), __('Update',MVLTD), __('Risk',MVLTD), __('Actions',MVLTD)), $rows, $showAll, $signal);
	$tab1 .= '<div class="clear"></div>
	<br />';
	$tab1 .= '
	<form method="post" action="'.mvl_getMainUrl() .'&p=9#tabs-1">
	<input type="submit" value="' .__('Changed something? Rerun Update Check!', MVLTD) .'" />
	</form>';
	// ----------------


	// User Check
	// ----------------
	$userChecks = $mvl_checks_config['userChecks'];
	$count = 0;
	$showAll=0;
	$rows = '';
	$tab2 = '';
	$check = $userChecks[0];
	$signal = 0;

	$message = $check['message'];
	$id = @$check['id'];
	$usersData = mvl_doUserChecks();
	usort($usersData, 'mvl_risk_sort');
	foreach($usersData as $userData) {
		$count +=1;
		$id = mvl_htmlEncode($userData['id']);
		$login = mvl_htmlEncode($userData['login']);
		$name = mvl_htmlEncode($userData['firstname'] . ' ' . $userData['lastname']);
		$name = $name != ' ' ? $name : '-';
		$email = mvl_htmlEncode($userData['email']);
		$registered = mvl_htmlEncode($userData['registered']);
		$roles = mvl_htmlEncode($userData['roles']);
		$state = $userData['risk'];
		if ($state > $sig2) $sig2 = $state;
		if ($state > $signal) $signal = $state;

		if ($state != 1 && $state != 0)
			$rows .= '<tr><td>' . $id . '</td><td>'.$login.'</td><td class="align-center">'.$name.'</td><td>'.$email.'</td><td>'.$registered.'</td><td>'.$roles.'</td><td class="align-center">' . mvl_getStateStyle($state) . '</td><td class="align-center">' . mvl_getFurtherInfo($userData['id'],'usercheck').' </td></tr>';
		else
			$rows .= "<tr><td>$id</td><td>$login</td><td class=\"align-center\">$name</td><td>$email</td><td>$registered</td><td>$roles</td><td class=\"align-center\">" . mvl_getStateStyle($state) . "</td><td class=\"align-center\">" . mvl_getInfoLinkMod(MVL_USERCHECK_GENERAL) . "</td></tr>";
		if ($count == 6)
			$rows .= '</tbody><tbody class="show-more">';
		elseif($count > 6)
			$showAll = 1;
	}
	$tab2 .= __('The User Check shows you which users have security problems that pose a risk to your website. Red is bad!',MVLTD) .'<br/><br/>';
	$tab2 .= mvl_getTabTable(__('User Check',MVLTD), array(__('Id',MVLTD), __('Username',MVLTD), __('Name',MVLTD), __('E-Mail',MVLTD), __('Registered',MVLTD), __('Roles',MVLTD), __('Risk',MVLTD), __('Actions',MVLTD)), $rows, $showAll, $signal);
	$tab2 .= '<div class="clear"></div>
	<br />';
	$tab2 .= '
	<form method="post" action="'.mvl_getMainUrl() .'&p=7#tabs-2">
	<input type="submit" value="' .__('Changed something? Rerun User Check!', MVLTD) .'" />
	</form>';

	// ----------------

	// Core Check
	// ----------------
	// File Existing Checks
	$fileChecks = $mvl_checks_result['fileChecks'];
	usort($fileChecks, "mvl_state_sort");
	$rows = '';
	$tab3 = '';
	$tab3 .= __('The Core Check shows you which files and settings put your website at risk. Go green!',MVLTD) . '<br/><br/>';
	$count = 0;
	$showAll=0;
	$signal = 0;
	foreach($fileChecks as $check) {
		$count += 1;
		$name = @$check['name'];
		if (!$name) $name = $check['fileName'];
		if(is_array($name)) $name = $name[0];
		$message = $check['message'];
		$state = $check['state'];
		if ($state > $sig3) $sig3 = $state;
		if ($state > $signal) $signal = $state;
		$id = @$check['id'];
		if(!$id)
			$actionLink = mvl_getInfoLinkMod(MVL_FILECHECK_GENERAL); //generic
		elseif($state == 1)
			$id ? $actionLink = mvl_getInfoLinkMod(MVL_FILECHECK_GENERAL) : $actionLink = '';
		elseif($state == 2 || $state == 3)
			$actionLink = mvl_getFurtherInfo($id,'filecheck');
		
		$rows .= '<tr><td><a class="mvltooltip" title="'.$message.'">'.mvl_htmlEncode($name).'</td><td class="align-center">' . mvl_getStateStyle($state) . '</td><td class="align-center">'.$actionLink.'</td></tr>';
		if ($count == 4)
			$rows .= '</tbody><tbody class="show-more">';
		elseif($count > 4)
			$showAll = 1;
	}
	$fsCheckParts = mvl_getTabTablePart(array(__('File Check',MVLTD), '<center>' . __('Risk',MVLTD) . '</center>','<center>' . __('Actions',MVLTD). '</center>'), $rows);

	//File Permission Checks
	$permissionChecks = $mvl_checks_result['permissionChecks'];
	usort($permissionChecks, "mvl_state_sort");
	$count = 0;
	$rows = '';
	foreach($permissionChecks as $check) {
		$count += 1;
		$name = @$check['name'];
		if (!$name) $name = $check['fileName'];
		$message = $check['message'];
		$state = $check['state'];
		if ($state > $sig3) $sig3 = $state;
		if ($state > $signal) $signal = $state;
		$id = @$check['id'];
		if(!$id)
			$actionLink = mvl_getInfoLinkMod(MVL_PERMCHECK_GENERAL); //generic
		elseif($state == 1)
			$id ? $actionLink = mvl_getInfoLinkMod(MVL_PERMCHECK_GENERAL) : $actionLink = '';
		elseif($state == 2 || $state == 3)
			$actionLink = mvl_getFurtherInfo($id,'permissioncheck');
		
		$rows .= '<tr><td><a class="mvltooltip" title="'.$message.'">'.mvl_htmlEncode($name).'</td><td class="align-center">' . mvl_getStateStyle($state) . '</td><td class="align-center">'.$actionLink.'</td></tr>';
		if ($count == 4)
			$rows .= '</tbody><tbody class="show-more">';
		elseif($count > 4)
			$showAll = 1;
	}
	$fsCheckParts .= mvl_getTabTablePart(array(__('Permission Check',MVLTD), '<center>' . __('Risk',MVLTD) . '</center>','<center>' . __('Actions',MVLTD). '</center>'), $rows);
	$tab3 .= mvl_getTabTableFromParts(__('File-system Check',MVLTD), $fsCheckParts, $showAll, $signal);

	// backendChecks
	$backendChecks = $mvl_checks_result['backendChecks'];
	usort($backendChecks, "mvl_state_sort");
	$count = 0;
	$showAll=0;
	$rows = '';
	$signal = 0;
	foreach($backendChecks as $check) {
		$count += 1;
		$name = @$check['name'];
		if (!$name) $name = $check['type'];
		$message = $check['message'];
		$state = $check['state'];
		if ($state > $sig3) $sig3 = $state;
		if ($state > $signal) $signal = $state;
		$id = @$check['id'];
		if(!$id)
			$actionLink = mvl_getInfoLinkMod(MVL_BACKENDCHECK_GENERAL); //generic
		elseif($state == 1 || $state == 0)
			$id ? $actionLink = mvl_getInfoLinkMod(MVL_BACKENDCHECK_GENERAL) : $actionLink = '';
		elseif($state == 2 || $state == 3)
			$actionLink = mvl_getFurtherInfo($id,'backendChecks');
		
		$rows .= '<tr><td><a class="mvltooltip" title="'.$message.'">'. mvl_htmlEncode($name).'</td><td class="align-center">' . mvl_getStateStyle($state) . '</td><td class="align-center">'.$actionLink.'</td></tr>';
		if ($count == 2)
			$rows .= '</tbody><tbody class="show-more">';
		elseif($count > 2)
			$showAll = 1;
	}
	$bsCheckParts = mvl_getTabTablePart(array(__('Backend Check',MVLTD), '<center>' . __('Risk',MVLTD) . '</center>','<center>' . __('Actions',MVLTD). '</center>'), $rows);

	//WPbackendChecks
	$WPbackendChecks = $mvl_checks_result['WPbackendChecks'];
	usort($WPbackendChecks, "mvl_state_sort");
	$count = 0;
	$rows = '';
	foreach($WPbackendChecks as $check) {
		$count += 1;
		$name = @$check['name'];
		if (!$name) $name = $check['type'];
		$message = $check['message'];
		$state = $check['state'];
		if ($state > $sig3) $sig3 = $state;
		if ($state > $signal) $signal = $state;
		$id = @$check['id'];
		if(!$id)
			$actionLink = mvl_getInfoLinkMod(MVL_WPBCHECK_GENERAL); //generic
		elseif($state == 1)
			$id ? $actionLink = mvl_getInfoLinkMod(MVL_WPBCHECK_GENERAL) : $actionLink = '';
		elseif($state == 2 || $state == 3)
			$actionLink =  mvl_getFurtherInfo($id,'wpbcheck');

		$rows .= '<tr><td><a class="mvltooltip" title="'.$message.'">'.mvl_htmlEncode($name).'</td><td class="align-center">' . mvl_getStateStyle($state) . '</td><td class="align-center">'.$actionLink.'</td></tr>';
		if ($count == 2)
			$rows .= '</tbody><tbody class="show-more">';
		elseif($count > 2)
			$showAll = 1;
	}
	$bsCheckParts .= mvl_getTabTablePart(array(__('WP Backend Check',MVLTD), '<center>' . __('Risk',MVLTD) . '</center>','<center>' . __('Actions',MVLTD). '</center>'), $rows);

	//DBbackendChecks
	$DBbackendChecks = $mvl_checks_result['DBbackendChecks'];
	usort($DBbackendChecks, "mvl_state_sort");
	$count = 0;
	$rows = '';
	foreach($DBbackendChecks as $check) {
		$count += 1;
		$name = @$check['name'];
		if (!$name) $name = $check['type'];
		$message = $check['message'];
		$state = $check['state'];
		if ($state > $sig3) $sig3 = $state;
		if ($state > $signal) $signal = $state;
		$id = @$check['id'];
		if(!$id)
			$actionLink = mvl_getInfoLinkMod(MVL_DBCHECK_GENERAL); //generic
		elseif($state == 1)
			$id ? $actionLink = mvl_getInfoLinkMod(MVL_DBCHECK_GENERAL) : $actionLink = '';
		elseif($state == 2 || $state == 3)
			$actionLink = mvl_getFurtherInfo($id,'dbcheck');
		
		$rows .= '<tr><td><a class="mvltooltip" title="'.$message.'">'.mvl_htmlEncode($name).'</td><td class="align-center">' . mvl_getStateStyle($state) . '</td><td class="align-center">'.$actionLink.'</td></tr>';
		if ($count == 2)
			$rows .= '</tbody><tbody class="show-more">';
		elseif($count > 2)
			$showAll = 1;
	}
	$bsCheckParts .= mvl_getTabTablePart(array(__('DB Backend Check',MVLTD), '<center>' . __('Risk',MVLTD) . '</center>','<center>' . __('Actions',MVLTD). '</center>'), $rows);
	// merge the parts
	$tab3 .= mvl_getTabTableFromParts(__('Backend-system Check',MVLTD), $bsCheckParts, $showAll, $signal);

	//	phpSettingsChecks
	$phpSettingsChecks = $mvl_checks_result['phpSettingsChecks'];
	usort($phpSettingsChecks, "mvl_state_sort");
	$count = 0;
	$showAll=0;
	$rows = '';
	$signal = 0;
	foreach($phpSettingsChecks as $check) {
		$count += 1;
		$name = @$check['name'];
		if (!$name) $name = $check['setting'];
		$message = $check['message'];
		$state = $check['state'];
		if ($state > $sig3) $sig3 = $state;
		if ($state > $signal) $signal = $state;
		$value = @$check['value'];
		$id = @$check['id'];
		if(!$id)
			$actionLink = mvl_getInfoLinkMod(MVL_PHPSETTINGCHECK_GENERAL); //generic
		elseif($state == 1)
			$id ? $actionLink = mvl_getInfoLinkMod(MVL_PHPSETTINGCHECK_GENERAL) : $actionLink = '';
		elseif($state == 2 || $state == 3)
			$actionLink = mvl_getFurtherInfo($id,'phpsettingcheck');

		$rows .= '<tr><td><a class="mvltooltip" title="'.$message.'">'.mvl_htmlEncode($name).'</td><td class="align-center">' . mvl_getStateStyle($state) . '</td><td class="align-center">'.$actionLink.'</td></tr>';
		if ($count == 8)
			$rows .= '</tbody><tbody class="show-more">';
		elseif($count > 8)
			$showAll = 1;
	}
	$scCheckParts = mvl_getTabTablePart(array(__('PHP-Settings Check',MVLTD), '<center>' . __('Risk',MVLTD) . '</center>','<center>' . __('Actions',MVLTD). '</center>'), $rows);

	$tab3 .= mvl_getTabTableFromParts(__('System Check',MVLTD), $scCheckParts, $showAll, $signal);
	$tab3 .= '<div class="clear"></div>
	<br />';
	$tab3 .= '
	<form method="post" action="'.mvl_getMainUrl() .'&p=8#tabs-3">
	<input type="submit" value="' .__('Changed Something? Rerun Core Check!', MVLTD) .'" />
	</form>';

	// bonusChecks
	$complied = mvl_readOption(MVIS_LITE_OPT_COMPLIED);
	$bonusChecks = $mvl_checks_result['bonusChecks'];
	$rows = '';
	$tab4 = '';
	$sig4 = 5;
	foreach($bonusChecks as $check) {
		$category = @$check['category'];
		$message = $check['message'];
		$state = isset($check['state'])?$check['state']:1;
		$id = @$check['id'];
		$id ? $infoLink = mvl_getInfoLinkMod(MVL_BONUSCHECK_GENERAL,$id, '', true, __('Click to learn more about this check.',MVLTD)) : $infoLink = '';
		if (isset($complied[$id]) and ($complied[$id] == 1)) {
			$checked = ' checked="checked" ';
		} else {
			$checked = '';
			$sig4 = 4;
		}
		$rows .= '<tr><td nowrap="nowrap">'.mvl_htmlEncode($category).'</td>'
		.  '<td>' . mvl_htmlEncode($message) . '</td>'
		. '<td class="align-center"><input type="checkbox"' . $checked . 'name="cb_' . $id . '" onclick="mvl_bonus_comply(' . $id . ', this.checked);" /></td>'
		. "<td class=\"align-center\">$infoLink</td>"
		. "</tr>";
	}
	$tab4 .= __('Security does not stop at your website, follow the descriptions in the Bonus Check to achieve an even higher level of security and awareness.',MVLTD) .'<br/><br/>';
	$tab4 .= mvl_getTabTable(__('Bonus Check',MVLTD), array(__('Category',MVLTD), __('Description',MVLTD) , __('Checked',MVLTD), __('Info',MVLTD)), $rows, false, $sig4);

	$activeTab = $p;
	$content .= mvl_getTabContainer($tab1, $tab2, $tab3, $tab4, $sig1, $sig2, $sig3, $sig4, $activeTab);
	$content .= mvl_getPageEnd();
	return($content);
}


function mvl_getSignalSpan($state) {
	switch ($state) {
		case 1: $res = '<span class="signal signal-uptodate"></span>'; break;// green
		case 2: $res = '<span class="signal signal-warning"></span>'; break;// orange
		case 3: $res = '<span class="signal signal-alert"></span>'; break;// red
		case 4: $res = '<span class="signal signal-bonus-unchecked"></span>'; break;
		case 5: $res = '<span class="signal signal-bonus-checked"></span>'; break;
		default: $res = '';
	}
	return($res);
}


function mvl_getTabContainer($tab1 = '', $tab2 = '', $tab3 = '', $tab4 = '', $sig1 = 0, $sig2 = 0, $sig3 = 0, $sig4 = 0, $activeTab = 1) {
	global $mvlState;
	$ss1 = mvl_getSignalSpan($sig1);
	$ss2 = mvl_getSignalSpan($sig2);
	$ss3 = mvl_getSignalSpan($sig3);
	$ss4 = mvl_getSignalSpan($sig4);
	$content = <<<HEREDOC
<div id="tab-container">
  <div id="tabs">
    <ul>

HEREDOC;
	$content .='
	<li><a href="#tabs-1">' . __('1. Update Check',MVLTD) . $ss1. '</a></li>
	<li><a href="#tabs-2">' . __('2. User Check',MVLTD) . $ss2. '</a></li>
	<li><a href="#tabs-3">' . __('3. Core Check',MVLTD) . $ss3. '</a></li>
	<li><a href="#tabs-4">' . __('Bonus Check',MVLTD) . $ss4. '</a></li>
	</ul>';

	$content .= '
	<div id="tabs-1">';
    
	$content .= $tab1;
	
	$content .= '
    </div>
    <div id="tabs-2">
    '. $tab2 .'
    </div>
    <div id="tabs-3">
    '. $tab3 .'
    </div>
    <div id="tabs-4">
    '. $tab4 .'
    </div>
  </div>
</div>';
	return($content);
}


function mvl_getTabTable($caption = '', $ths = null, $rows = '', $showAll = false, $signal = 0, $tablewrapper ='') {
	$signalSpan = mvl_getSignalSpan($signal);
	$content = <<<HEREDOC
<div class="tab-table $tablewrapper">
	<table class="tablesorter" style="width:100%">
		<caption>$signalSpan$caption</caption>
    	<thead>
         <tr>
HEREDOC;

	foreach($ths as $th) {
		$content .= "<th>$th</th>\r\n";
	}
	$content .= "</tr></thead><tbody>\r\n";
	$content .= $rows;
	$content .= "</tbody></table>";
	if ($showAll) {
		$content .= '<input type="button" id="showAll" class="more" value="show all" title="show all">';
	}
	$content .= "</div>\r\n";
	return($content);
}


function mvl_getTabTablePart($ths = null, $rows = '') {
	$content = <<<HEREDOC
    	<thead>
         <tr>
HEREDOC;
	foreach($ths as $th) {
		$content .= "<th>$th</th>\r\n";
	}
	$content .= "</tr></thead><tbody>\r\n";
	$content .= $rows;
	$content .= "</tbody>";
	return($content);
}

function mvl_getTabTableFromParts($caption = '', $parts = '', $showAll = false, $signal = 0) {
	$signalSpan = mvl_getSignalSpan($signal);
	$content = <<<HEREDOC
<div class="tab-table table-wrapper">
	<table class="table-core tablesorter">
		<caption>$signalSpan$caption</caption>
HEREDOC;
	$content .= $parts;
	$content .= "</table>";
	if ($showAll) {
		$content .= '<input type="button" id="showAll" class="more" value="show all" title="show all">';
	}
	$content .= "</div>\r\n";
	return($content);
}

function mvl_getSimpleTabTable($caption = '', $ths = null, $rows = '') {
	$content = <<<HEREDOC
<div class="tab-table">
	<table>
		<caption>$caption</caption>
    	<thead>
         <tr>
HEREDOC;
	foreach($ths as $th) {
		$content .= "<th>$th</th>\r\n";
	}
	$content .= "</tr></thead><tbody>\r\n";
	$content .= $rows;
	$content .= "</tbody></table>";
	$content .= "</div>\r\n";
	return($content);
}

function mvl_getUpdateElements($includeCore = false) {
	$wpUpdates = mvl_getWPUpdates();
	$plugins = mvl_getInstalledPlugins(true);
	$themes = mvl_getInstalledThemes(true);
	$vulnStati = mvl_readOption(MVIS_LITE_OPT_VULNSTATUS, 'vuln_status');
	$siteAlerts = mvl_readOption(MVIS_LITE_OPT_SITEALERTS, 'site_alerts');
	if (!$vulnStati) $vulnStati = array();
	if (!$siteAlerts) $siteAlerts = array();
	$updateElements = array();

	if ($includeCore) {
		foreach($wpUpdates as $wpUpdate) {
			if($wpUpdate['type'] == 'Core'){
				$updateElement = array();
				$updateElement['type'] = 'Core';
				$updateElement['risk'] = 1;
				$updateElement['name'] = 'Wordpress';
				$updateElement['oldVersion'] = mvl_getCMSVersion();
				$updateElement['newVersion'] = '';
				$updateElement['url'] = '';
				$updateElement['package'] = '';
				$updateElement['alertId'] = 0;
				$updateElement['newVersion'] = $wpUpdate['newVersion'];
				$updateElement['url'] = mvl_getInfoLink($wpUpdate['url']);
				$updateElement['urlclean'] = $wpUpdate['url'];
				$updateElement['package'] = mvl_getDownloadLink($wpUpdate['package']);
				$updateElement['packageclean'] = $wpUpdate['package'];
				$updateElement['risk'] = 2;

				foreach($vulnStati as $vulnStatus) {
					if (strtolower($updateElement['name']) == strtolower($vulnStatus['productname'])) {
						$updateElement['risk'] = 3;
					}
				}
				foreach($siteAlerts as $id=>$siteAlert) {
					$products = $siteAlert['Products'];
					if ($products) {
						foreach($products as $product) {
							if (strtolower($updateElement['name']) == strtolower($product['productname'])) {
								$updateElement['risk'] = 3;
								$updateElement['alertId'] = $id+1;
							}
						}
					}
				}
				$updateElements[] = $updateElement;
			}
		}
	}

	foreach($plugins as $plugin) {
		$updateElement = array();
		$updateElement['type'] = __('Plugin', MVLTD);
		$updateElement['risk'] = 1;
		$updateElement['name'] = $plugin['product'];
		$updateElement['oldVersion'] = $plugin['version'];
		$updateElement['newVersion'] = '';
		$updateElement['url'] = '';
		$updateElement['package'] = '';
		$updateElement['alertId'] = 0;
		$updateElement['active'] = $plugin['active'];
		foreach($wpUpdates as $wpUpdate) {
			if ($updateElement['name'] == $wpUpdate['name']) {
				$updateElement['newVersion'] = $wpUpdate['newVersion'];
				$updateElement['fileName'] = $wpUpdate['fileName'];
				$updateElement['url'] = mvl_getInfoLink3($wpUpdate['url']);
				$updateElement['urlclean'] = $wpUpdate['url'];
				$updateElement['package'] = mvl_getDownloadLink($wpUpdate['package']);
				$updateElement['packageclean'] = $wpUpdate['package'];
				$updateElement['risk'] = 2;
			}
		}
		foreach($vulnStati as $vulnStatus) {
			if (strtolower($updateElement['name']) == strtolower($vulnStatus['productname'])) {
				$updateElement['risk'] = 3;
			}
		}
		foreach($siteAlerts as $id=>$siteAlert) {
			$products = $siteAlert['Products'];
			if ($products) {
				foreach($products as $product) {
					if (strtolower($updateElement['name']) == strtolower($product['productname'])) {
						$updateElement['risk'] = 3;
						$updateElement['alertId'] = $id+1;
					}
				}
			}
		}
		$updateElements[] = $updateElement;
	}

	foreach($themes as $theme) {
		$updateElement = array();
		$updateElement['type'] = 'theme';
		$updateElement['risk'] = 1;
		$updateElement['name'] = $theme['product'];
		$updateElement['oldVersion'] = $theme['version'];
		$updateElement['newVersion'] = '';
		$updateElement['url'] = '';
		$updateElement['package'] = '';
		$updateElement['alertId'] = 0;
		
		foreach($wpUpdates as $wpUpdate) {
			if ($updateElement['name'] == $wpUpdate['name']) {
				$updateElement['newVersion'] = $wpUpdate['newVersion'];
				$updateElement['fileName'] = $wpUpdate['fileName'];
				$updateElement['url'] = mvl_getInfoLink3($wpUpdate['url']);
				$updateElement['urlclean'] = $wpUpdate['url'];
				$updateElement['package'] = mvl_getDownloadLink($wpUpdate['package']);
				$updateElement['packageclean'] = $wpUpdate['package'];
				$updateElement['risk'] = 2;
			}
		}
		foreach($vulnStati as $vulnStatus) {
			if (strtolower($updateElement['name']) == strtolower($vulnStatus['productname'])) {
				$updateElement['risk'] = 3;
			}
		}
		foreach($siteAlerts as $id=>$siteAlert) {
			$products = $siteAlert['Products'];
			if ($products) {
				foreach($products as $product) {
					if (strtolower($updateElement['name']) == strtolower($product['productname'])) {
						$updateElement['risk'] = 3;
						$updateElement['alertId'] = $id+1;
					}
				}
			}
		}
		$updateElements[] = $updateElement;
	}

	usort($updateElements, 'mvl_risk_sort');
	return($updateElements);
}

/*
 0, null or n/a: check did not run
1: OK (green)
2: warning (yellow)
3: error (red)
*/
function mvl_getStateStyle($state, $preset = false) {
	if($preset){
		switch($state) {
			case 1: return('<span class="version version-uptodate"></span>'); break;
			case 2: return('<span class="version version-warning"></span>'); break;
			case 3: return('<span class="version version-dated"></span>'); break;
			default: return('<span class="action-remove"></span>'); break;
		}
	}else{
		switch($state) {
			case 1: return('<a class="mvltooltip" title="' . __('No security risk has been identified.',MVLTD) . '"><span class="version version-uptodate"></span>'); break;
			case 2: return('<a class="mvltooltip" title="' . __('A medium security risk has been identified; resolve it as soon as possible.',MVLTD) . '"><span class="version version-warning"></span>'); break;
			case 3: return('<a class="mvltooltip" title="' . __('A high security risk has been identified; resolve it immediately.',MVLTD) . '"><span class="version version-dated"></span>'); break;
			default: return('<a class="mvltooltip" title="' . __('No risk assessment can be given at the current time.',MVLTD) . '"><span class="action-remove"></span>'); break;
		}
	}
}
?>