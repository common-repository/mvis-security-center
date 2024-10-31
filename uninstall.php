<?php

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();

delete_option('mvl_core');
delete_option('mvl_sitealerts');
delete_option('mvl_checks_config');
delete_option('mvl_checks_result');
delete_option('mvl_vulnstatus');
delete_option('mvl_complied');