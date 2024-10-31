<?php
function mvl_getPageHelp() {
	global $mvl_checks_config;
	global $mvlState;
	$content = mvl_getPageStart('help', 'Help', '', $mvlState->showSubscribe, $mvlState->showProfile);
	
	if ($mvlState->agreeTaC != true){
		$content .=
		'<div class="container">
			'. __('You have to agree to the Terms and Conditions before using this plugin.') .'
		</div>';
	}else{
		$content .= ' 
		<div class="container">
		<div id="tocdiv" class="tocdiv">
		<div id="toctitle"><h2>Contents</h2></div>
		<ul> 
			<li><a href="#About">'. __('About',MVLTD) .'</a></li>
			<li><a href="#Legend">'. __('Risk Legend',MVLTD) .'</a></li>
			<li><a href="#Summary">'. __('Summary',MVLTD) .'</a></li>
			<li><a href="#3_Steps">'. __('3 Steps',MVLTD) .'</a></li>
			<ol>
				<li><a href="#Update_Check">'. __('Update Check',MVLTD) .'</a></li>
				<li><a href="#User_Check">'. __('User Check',MVLTD) .'</a></li> 
				<li><a href="#Core_Check">'. __('Core Check',MVLTD) .'</a></li>
			</ol> 
			<li><a href="#Bonus_Check">'. __('Bonus Check',MVLTD) .'</a></li>
			<li><a href="#Profile">'. __('Profile',MVLTD) .'</a></li>
		</ul>
		</div>
			<a id="About"></a>
			<h4>'.__('About: ',MVLTD).'</h4><br/> 
			'. __('Our goal is to help you so you never have to say, "Help! I\'ve been hacked!"',MVLTD) . ' '. 
			__('Hacking is a very serious threat, but only few people think about it until it\s too late. Just like Windows computers, WordPress is a very attractive target for hackers because it is so popular. One attack can impact tens of millions of WordPress installations.',MVLTD) . ' '.  
			__('Unfortunately, you can never eliminate the risk of being hacked completely - even big corporations with million-dollar budgets get hacked often. However, taking three simple steps to secure your website can decrease your risk tremendously.', MVLTD) . ' '. 
			__('MVIS Security Center helps you understand the risks and supports you in locking down your WordPress installation in three simple and clear steps.',MVLTD) . 
			'<br/><br/>'.  
			__('Behind these three steps are two very important concepts that are responsible for raising and maintaining a secure WordPress website:',MVLTD) .'
			<br/><br/> <strong>'
				.__('1.) Keeping a secure configuration:',MVLTD).'</strong><br/>' . 
				'<blockquote>'.__('Often insecure configurations can be easily exploited by attackers.',MVLTD) . ' '. __('A secure and solid configuration strengthens your website and decreases the risk of successful hack attacks against your website.',MVLTD) . ' '. __('With MVIS Security Center you can see all identified security problems of your website at one glance. Each security problem comes with a detailed description and all the information needed so you can eliminate the problems and get secure.',MVLTD) .'</blockquote>
				<strong>'.
				__('2.) Keeping your system updated:',MVLTD) .'</strong><br/>' .
				'<blockquote>'.__('One of the major reasons that websites get hacked is because they are running outdated software - either the WordPress core software itself, or some plugins or themes contain security vulnerabilities and have to be updated. There are many sites on the Internet that share information about security vulnerabilities in WordPress and its eco-system. Hackers can abuse this information and easily exploit these vulnerabilities to take over websites. ',MVLTD) . 
				__('With the MVIS Security Center you can see which components are outdated and even which of them have known security vulnerabilities. ',MVLTD). 
				__('Subscribed users conveniently get e-mail alerts with vulnerability details for their specific and unique WordPress website, without even having to login to the admin interface.',MVLTD) .
				'</blockquote> 
			 
			<a id="Legend"></a>&nbsp;
			
			<h4>'.__('Risk Legend: ',MVLTD).'</h4>
			'. __('All security checks are assigned one of the following risks:',MVLTD).'<br/><br/> 
					<span class="action-remove">&nbsp;</span>'.__('The check did not run successfully and <strong>no risk assessment</strong> can be given.',MVLTD).'<br>
					<span class="version version-uptodate">&nbsp;</span>'.__('Everything is the way it should be, <strong>no violations</strong> have been recorded.',MVLTD).'<br>
					<span class="version version-warning">&nbsp;</span>'.__('A <strong>minor security risk</strong> has been identified. This issue should be resolved as soon as possible.',MVLTD).'<br>
					<span class="version version-dated">&nbsp;</span>'.__('A <strong>major security risk</strong> has been identified. This issue has to be resolved immediately.',MVLTD).'
			<h4>'.__('Action Legend: ',MVLTD).'</h4>
			'. __('All security checks have one of the following actions:',MVLTD).'<br/><br/> 
					<span class="action-info">&nbsp;&nbsp;</span>'.__('No risks have been identified, click on the icon to obtain more information about the specific security check.',MVLTD).'<br>
					<span class="action-arrow">&nbsp;&nbsp;</span>'.__('A vulnerability has been identified, click on the icon to obtain information on how to resolve the issue.',MVLTD).'<br>
				<a name="Summary" id="Summary"></a>
        <hr>&nbsp;
			<h3><span class="nav-summary"></span>'.__('Summary',MVLTD).'</h3>
				'.__('On the summary page you will find an overview of the check status of each of the three security check categories.',MVLTD).'<br /><br /> 
				<ol>
					<li><strong>'.__('Update Check',MVLTD).'</strong></li>
					<li><strong>'.__('User Check',MVLTD).'</strong></li>
					<li><strong>'.__('Core Check',MVLTD).'</strong></li>
				</ol>
				
        <p> 
				'.__('The summary check tables are sorted by risk and show the highest risk violations first.',MVLTD).'<br/>
				'.__('Further details on each of the checks can be found in the "3 Steps" section below.',MVLTD).'<br />
				'.__('Some of the checks are time intensive and thus can only be triggered manually by the user. ', MVLTD).'<br/>
				'.__('The summary page has button that allows you to rerun all checks manually.',MVLTD).'<br/>
				'.__('Additionally, the summary page allows you to easily control if the plugin is allowed to communicate with our servers.',MVLTD).'
				</p>
				
        <a id="3_Steps"></a>&nbsp;
        <hr>
        
			<h3><span class="nav-steps"></span>'.__('3 Steps',MVLTD).'</h3>
				'.__('On the "3 steps" page you will find all the details for all the checks of the three security check categories.',MVLTD).'<br />				 
				<a id="Update_Check"></a>&nbsp;
				<h4>'.__('1. Update Check',MVLTD).'</h4>
				<p>
				'.__('The <strong>"Update Check"</strong> analyses the status of the core WordPress installation including all installed plugins and themes.',MVLTD).'<br />
				'.__('It displays clearly if updates are available and <strong>even if security vulnerabilities are  publicly known</strong> for the installed WordPress Version, Plugins or Themes.',MVLTD).'
				</p>
				
				<div class="notice-message">';
					if(!$mvlState->siteActive){
						$content .= '<strong>'. __('You are not subscribed!',MVLTD).'</strong><br/>'. __('The vulnerability information for this system is 30 days old.',MVLTD) .'<br/><a href="' . mvl_getAbsoluteAdminUrl(10) . '">' . __('Subscribe now',MVLTD) .'</a> ' . __('to enjoy up-to-date protection and many other benefits!',MVLTD);
					}else{
						$content .=	__('<strong>Unsubscribed users</strong> get the vulnerability information with a 30 day delay from the time the vulnerability appears in our system. They also don\'t get the detailed vulnerability information exposed in the plugin, only the binary information if a vulnerability is known for the installed WordPress version, Plugin or Theme.',MVLTD);
					}
					$content .= '<br/><br/>
					'. __('<strong>Subscribed users</strong> get the vulnerability information as soon as the disclosed vulnerabilities show up in our systems.',MVLTD).'
					'.__('Additionally, subscribed users receive real-time email alerts with detailed information about the disclosed vulnerability.',MVLTD).'
					'.__('This is especially useful for people that are responsible for multiple sites, because they get the security alerts centrally delivered to their specified e-mail address, without even having to login to all the admin interfaces.',MVLTD).'
					'.__('The detailed vulnerability information is also available from within the plugin for subscribed users.',MVLTD) .'<br />
				</div>
				
        <p>
				<strong>'.__('Note:',MVLTD).' </strong>' .__('The MVIS Security Center Plugin needs to communicate with the server in order to keep our version information about your site synchronized to be able to provide you with relevant security updates in a timely manner.',MVLTD) .' ' . mvl_getInfoLinkMod(MVL_COMMUNICATE,'',__('Want to know more?'),false)   .'
				</p>
				<a id="User_Check"></a>&nbsp;
				<h4>'.__('2. User Check: ',MVLTD).'</h4>
				<p>
				'.__('The <strong>"User Check"</strong> analyses potentially dangerous combinations of weak passwords and common usernames for privileged users and administrative users.',MVLTD).'<br />
				'.__('Security violations in this category typically have a very high risk of being exploited, because they are the target of many attackers.',MVLTD).'
				</p>
			  
				<a id="Core_Check"></a>&nbsp;
				<h4>'.__('3. Core Check ',MVLTD).'</h4>
				<p>
				'.__('The <strong>"Core Check"</strong> combines multiple security checks from the following categories:',MVLTD).'<br />
				'.__('Mention the general impact of violations against this check?',MVLTD).'
				</p>
			  
        <p>
				<h4>'.__('3.1 File Check ',MVLTD).'</h4>				
				'.__('The <strong>"File Check"</strong> tests if dangerous files exist and warns the user accordingly by providing further information about a violation.',MVLTD).'<br />
				'.__('Mention the general impact of violations against this check?',MVLTD).'
				</p>
						
				<p>
				<h4>'.__('3.2 Permission Check ',MVLTD).'</h4>
				'.__('The <strong>"Permission Check"</strong> tests if the set file permissions for important files and directories of the WordPress installation are set according to security best practices.',MVLTD).'<br />
				'.__('Mention the general impact of violations against this check?',MVLTD).'
				</p>
						
				<p>
				<h4>'.__('3.3 Backend Check ',MVLTD).'</h4>
				'.__('The <strong>"Backend Check"</strong> tests if the set file permissions for important files and directories of the WordPress installation are set according to security best practices.',MVLTD).'<br />
				'.__('Mention the general impact of violations against this check?',MVLTD).'
				</p>
				
				<p>
				<h4>'.__('3.4 WordPress Backend Check ',MVLTD).'</h4>
				'.__('The <strong>"WordPress Backend Check"</strong> tests if the set file permissions for important files and directories of the WordPress installation are set according to security best practices.',MVLTD).'<br />
				'.__('Mention the general impact of violations against this check?',MVLTD).'
				</p>
						
				<p>
				<h4>'.__('3.5 Database Backend Check ',MVLTD).'</h4>
				'.__('The <strong>"Database Backend Check"</strong> tests if the set file permissions for important files and directories of the WordPress installation are set according to security best practices.',MVLTD).'<br />
				'.__('Mention the general impact of violations against this check?',MVLTD).'
        </p>
						
				<p>
				<h4>'.__('3.6 PHP Settings Check ',MVLTD).'</h4>
				'.__('The <strong>"PHP Settings Check"</strong> tests if the set file permissions for important files and directories of the WordPress installation are set according to security best practices.',MVLTD).'<br />
				'.__('Mention the general impact of violations against this check?',MVLTD).'
        </p>
		<a id="Bonus_Check"></a>&nbsp;
		<hr>
				<p>
				<h3><span class="signal signal-bonus-checked"></span>'.__('Bonus Check ',MVLTD).'</h3>
				'.__('The <strong>"Bonus Check"</strong> lists information about general security best practices that should be followed for additional security.',MVLTD).'
				</p>
				
        <a id="Profile"></a>&nbsp;
        <hr>
        		
			<h3><span class="nav-profile"></span>'.__('Profile',MVLTD).'</h3>
				'.__('On the profile page you can get an overview of your subscription and manage your profile using the following actions:',MVLTD).'<br /><br /> 
				<ol>
					<li><strong>'.__('Change Password',MVLTD).'</strong></li>
					<li><strong>'.__('Delete Site',MVLTD).'</strong></li>
					<li><strong>'.__('Delete Account',MVLTD).'</strong></li>
				</ol>
				<p>'.__('The profile page is only available for registered users.',MVLTD).'</p>
					
				
		</div>';
	}
	$content .= mvl_getPageEnd();
	return($content);
}

?>