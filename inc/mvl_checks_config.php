<?php

function mvl_getChecksConfigFromFile() {

$mvl_checks_config = array();
$mvl_checks_config['version'] = 1370239680000; //03.06.2013

/*
 User Checks
*/
$mvl_checks_config['userChecks'][] = array(
		'name' => 'Check Users',
		'type' => 'username',
		'userNames' => array('admin', 'administrator', 'superuser', 'wp-admin', 'wpadmin', 'guest','anonymous','wp'),
		'message' => 'Choosing commonly used usernames for accounts with high priviledges makes it more likely for attackers to succeed in taking over the user accounts. ',
		'id' => 201
);

/*
 File Checks
*/
$mvl_checks_config['fileChecks'][] = array(
	'fileName' => '/wp-content/debug.log',
	'severity' => 2,
	'message' => 'This debug.log file might disclose sensitive information to attackers and should be removed from production environments.',
	'shouldExist' => 'no',
	'id' => 301 
);

$mvl_checks_config['fileChecks'][] = array(
	'fileName' => '/readme.html', 
	'message' => 'This file can disclose information about your WordPress installation to attackers.',
	'shouldExist' => 'no',
	'severity' => 2,
	'id' => 302
);

$mvl_checks_config['fileChecks'][] = array(
	'fileName' => '/wp-admin/install.php',
	'message' => 'This file is only needed for the installation and should be removed afterwards.',
	'shouldExist' => 'no',
	'severity' => 2,
	'id' => 303
);

$mvl_checks_config['fileChecks'][] = array(
	'type' => 'wpdangerousfiles',
	'name' => 'Potentially dangerous files',
	'fileName' => array('rebots.php','shell.php', 'madspotshell.php', 'c99.php','r57.php','r57-VIP.php','bord.php','ebypass.php','4e.php', 'ugdevil.php','c100.php','sniper.php','egy.php','cyb3r-sh3ll.php','entrika.php','uploader.php','phpjackal1.3.php','php_backdoor.php','phpshell.php','c37.php','execution_admin.php'), 
	'message' => 'The existence of any of these files strongly indicate that your site was successfully hacked.',
	'shouldExist' => 'no',
	'severity' => 3,
	'id' => 304
);

$mvl_checks_config['fileChecks'][] = array(
	'fileName' => '/license.txt', 
	'message' => 'This file can disclose information about your WordPress installation to attackers.',
	'shouldExist' => 'no',
	'severity' => 2,
	'id' => 305
);

/*
 * Thanks to: 
 * https://github.com/wpscanteam/wpscan/blob/master/lib/wpscan/wp_target/wp_config_backup.rb#L41 
*/

$mvl_checks_config['fileChecks'][] = array(
	'type' => 'wpconfigbackupfiles', 
	'name' => 'wp-config backup files',
	'fileName' => array('wp-config.php~','#wp-config.php#','wp-config.php.save','wp-config.php.swp','wp-config.php.swo','wp-config.php_bak','wp-config.bak','wp-config.php.bak','wp-config.save','wp-config.old','wp-config.php.old','wp-config.php.orig','wp-config.orig','wp-config.php.original','wp-config.original','wp-config.txt'), 
	'message' => 'Wp-config.php backup files might leak sensitive information to attackers.',
	'shouldExist' => 'no',
	'severity' => 2,
	'id' => 306
);

/*
File/Directory-Permissions
0 â none (i.e., all permissions specified are preserved)
1 â execute only
2 â write only
3 â write and execute
4 â read only
5 â read and execute
6 â read and write
7 â read, write and execute (i.e., no permissions are preserved)
*/
/*
 Permission Checks
*/
$mvl_checks_config['permissionChecks'][] = array(
	'fileName' => '/wp-content/', 
	'message' => 'The WorpPress generated content resides here, it needs write access for everyone.',
	'shouldNotBe' => array('0','0','rwx'),
	'id' => 401
);

$mvl_checks_config['permissionChecks'][] = array(
	'fileName' => '/wp-content/themes/', 
	'message' => 'The WordPress theme files reside here and it is recommended to only allow write access to your user account or additionally write access to your group if the built-in WordPress theme editor is used.',
	'shouldNotBe' => array('0','0','rwx'),
	'id' => 402
);

$mvl_checks_config['permissionChecks'][] = array(
	'fileName' => '/wp-content/plugins/', 
	'message' => 'The WordPress plugin files reside here and it is recommended to only allow write access by your user account.',
	'shouldNotBe' => array('0','-w-','rwx'),
	'id' => 403
);

$mvl_checks_config['permissionChecks'][] = array(
	'fileName' => '/wp-includes/', 
	'message' => 'The WordPress application logic resides here and all files should be writable only by your user account.',
	'shouldNotBe' => array('0','-w-','rwx'),
	'id' => 404
);

$mvl_checks_config['permissionChecks'][] = array(
	'fileName' => '/wp-admin/', 
	'message' => 'The WordPress admin area is in this directory and all files should be writable only by your user account.',
	'shouldNotBe' => array('0','-w-','rwx'),
	'id' => 405
);

$mvl_checks_config['permissionChecks'][] = array(
	'type'	=> 'wpconfig',	
	'fileName' => '/wp-config.php',
	'message' => 'This file contains the main configuration of WordPress and thus holds highly sensitive information that needs to be protected. ',
	'severity' => 3,
	'shouldNotBe' => array('0','-wx','rwx'),
	'id' => 406
);

/*
 Backend Checks
*/

$mvl_checks_config['backendChecks'][] = array(
		'name' => 'Administration over HTTPs',
		'type' => 'https', // checks, if backend is reachable via https only
		'message' => 'Protect sensitive information by enabling HTTPS for your admin interface.',
		'id' => 501
);

$mvl_checks_config['backendChecks'][] = array(
		'name' => 'HTTP Basic Authentication',
		'type' => 'wwwauthenticate', // checks, if backend is secured via WWW-Authenticate
		'message' => 'Protect your admin interface by adding an additional layer of authentication.',
		'severity' => 2,
		'id' => 502
);

$mvl_checks_config['backendChecks'][] = array(
		'name' => 'Directory Listing',
		'type' => 'dirlisting',
		'url' => 'wp-content/plugins/mvis-security-center/dirlisting/',
		'strings' => array('4815162342314159265'),
		'message' => 'Disable Directory Listing to prevent attackers to gather potentially sensitive information.',
		'severity' => 2,
		'id' => 503
);

$mvl_checks_config['WPbackendChecks'][] = array(
		'name' => 'User Registration',
		'type' => 'userreg',
		'message' => 'User registration should be disabled in production systems unless explicitly needed.',
		'id' => 601
);

$mvl_checks_config['WPbackendChecks'][] = array(
		'name' => 'Debugging',
		'type' => 'wpdebug',
		'message' => 'The WP_DEBUG setting should be disabled in production systems.',
		'id' => 602
);

$mvl_checks_config['WPbackendChecks'][] = array(
		'name' => 'Allowed MIME Types',
		'type' => 'wpmime',
		'extensions' => array('php'),
		'severity' => 3,
		'message' => 'Allowing dangerous MIME Types can allow attackers with privileged accounts to fully compromise your site.',
		'id' => 603
);

$mvl_checks_config['WPbackendChecks'][] = array(
		'name' => 'File Editing',
		'type' => 'fileedit',
		'severity' => 2,
		'message' => 'This is often the first feature that is abused by hackers to compromise a website, after they have gotten access to an administrative account..',
		'id' => 604
);

$mvl_checks_config['DBbackendChecks'][] = array(
		'name' => 'Database Prefix',
		'type' => 'dbprefix',
		'message' => 'The default database prefix is often used in automated attacks.',
		'severity' => 2,
		'id' => 701 /* further informations and examples */
);

$mvl_checks_config['DBbackendChecks'][] = array(
		'name' => 'Database User',
		'type' => 'dbroot',
		'severity' => 3,
		'message' => 'WordPress is configured to use the database user root, which poses high security risks.',
		'id' => 702
);

/*
PHP Settings
*/

$mvl_checks_config['phpSettingsChecks'][] = array(
	'name' => 'Register Globals',
	'setting' => 'register_globals',
	'message' => 'Enabling Register Globals poses a high risk to your system.',
	'shouldBe' => 0,
	'id' => 801
);

$mvl_checks_config['phpSettingsChecks'][] = array(
	'name' => 'Safe Mode',
	'setting' => 'safe_mode',
	'message' => 'Safe Mode is deprecated and should be disabled.',
	'shouldBe' => 0,
	'severity' => 2,	
	'id' => 802	
);

$mvl_checks_config['phpSettingsChecks'][] = array(
  	'name'	=> 'Dangerous PHP Functions',
	'type' => 'disabled_functions',
	'setting' => array('system', 'exec', 'passthru','shell_exec', 'proc_open'), 
	'message' => 'Dangerous functions make it possible for attackers to easily take over your system.',
	'shouldBe' => 0,
	'id' => 803	
);


$mvl_checks_config['phpSettingsChecks'][] = array(
	'name' => 'URL fopen/include',
	'setting' => array('allow_url_fopen','allow_url_include'), 
	'message' => 'These functionality is considered dangerous and should be disabled!',
	'shouldBe' => 0,
	'severity' => 2,
	'id' => 804
);

$mvl_checks_config['phpSettingsChecks'][] = array(
	'name' => 'Version information',
	'setting' => array('expose_php'),
	'message' => 'This setting allows attackers to get information about the installed PHP version.',
	'shouldBe' => 0,
	'severity' => 2,
	'id' => 805
);

$mvl_checks_config['phpSettingsChecks'][] = array(
	'name'	=> 'Error Reporting',
	'setting' => array('display_errors','html_errors','display_startup_errors'),
	'message' => 'Error Messages allow attackers to collect critical information.',
	'shouldBe' => 0,
	'severity' => 2,
	'id' => 806
);

$mvl_checks_config['phpSettingsChecks'][] = array(
	'name'	=> 'Error Reporting Level',
	'type' => 'errorreportinglevel',
	'setting' => 'error_reporting',
	'message' => 'All errors have to be reported.',
	'shouldBe' => 32767, //E_ALL starting from PHP 5.4
	'severity' => 2,
	'id' => 807
);

$mvl_checks_config['phpSettingsChecks'][] = array(
		'name'	=> 'Magic Quotes',
		'setting' => array('magic_quotes_gpc','magic_quotes_runtime', 'magic_quotes_sybase'),
		'message' => 'Magic Quotes have to be disabled!',
		'shouldBe' => 0,
		'severity' => 2,
		'id' => 808
);

$mvl_checks_config['phpSettingsChecks'][] = array(
		'name'	=> 'Log Errors',
		'setting' => 'log_errors',
		'message' => 'Error messages have to be logged.',
		'shouldBe' => 1,
		'severity' => 2,
		'id' => 809
);

/*
Bonus checks
*/

$mvl_checks_config['bonusChecks'][] = array(
	'category' => 'Security on Your Computer',
	'message' => 'I am making sure that the software installed on my computer is kept up-to-date with security updates and antivirus signatures.',
	'id' => 901
);

$mvl_checks_config['bonusChecks'][] = array(
	'category' => 'Security on Your Web Server',
	'message' => 'I have made my due diligence and am convinced that I am using a secure hosting provider, or that I have secured the web server myself.',
	'id' => 902
);

$mvl_checks_config['bonusChecks'][] = array(
	'category' => 'Network Security', 
	'message' => 'I am relying on encrypted alternatives such as FTPS or HTTPs and avoid insecure public networks.',
	'id' => 903 
);

$mvl_checks_config['bonusChecks'][] = array(
	'category' => 'Backup', 
	'message' => 'I have a backup process in place that has been tested and verified thoroughly.',
	'id' => 904
);

$mvl_checks_config['bonusChecks'][] = array(
	'category' => 'Monitoring',
	'message' => 'I am monitoring my WordPress setup in order to be able to detect security breaches and react in a timely manner.',
	'id' => 905
);



/*
http-Response Header Checks
*/
/*$mvl_checks_config['httpResponseChecks'][] = array(
	'name' => 'Check XPBy',
	'url' => '', 
	'header' => 'x-powered-by',
	'strings' => array('PHP/'),
	'message' => 'Server shows PHP-Version in Response-Header ...',
	'severity' => 2,
	'id' => 14000
);

$mvl_checks_config['httpResponseChecks'][] = array(
	'url' => '', 
	'header' => 'server',
	'strings' => array('Apache/', 'nginx/', 'PHP/'),
	'message' => 'Server shows too detailed information about itsself in Response-Header ...',
	'id' => 14001
);

$mvl_checks_config['httpResponseChecks'][] = array(
	'url' => '', 
	'header' => 'server',
	'strings' => array('empty test'),
	'message' => 'no message here, only for testing ...',
	'id' => 14002
);*/


return($mvl_checks_config);
}

?>
