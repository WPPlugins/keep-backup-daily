<?php 

/*

Plugin Name: Keep Backup Daily

Plugin URI: http://www.websitedesignwebsitedevelopment.com/website-development/php-frameworks/wordpress/plugins/wordpress-plugin-keep-backup-daily/1046

Description: This plugin will backup the mysql tables and email to a specified email address daily, weekly, monthly or even yearly.

Version: 1.8.3

Author: Fahad Mahmood 

Author URI: http://www.androidbubbles.com

License: GPL2

This WordPress Plugin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
This free software is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with this software. If not, see http://www.gnu.org/licenses/gpl-2.0.html.
*/ 

	if(
		(
			isset($_REQUEST['kbd_cron_process']) 
			&& $_REQUEST['kbd_cron_process']=1
		)
					
		||
		is_admin()
	){
	}else{
		return;
	}


	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	include('inc/functions.php');

	include('inc/kbd_cron.php');
	
	global $kbd_rc, $kbd_rs, $kbd_pro, $kbd_dir, $kbd_premium_link, $kbd_data, $kbd_buf;	
	
	$kbd_data = get_plugin_data(__FILE__);

	$kbd_dir = plugin_dir_path( __FILE__ );
	
	$kbd_pro = file_exists($kbd_dir.'pro/kbd_extended.php');
		
	$kbd_premium_link = 'http://shop.androidbubbles.com/product/keep-backup-daily-pro';

		 
	$kbd_rc = requirements_check();		

	$kbd_rs = array();
	$kbd_rs[] = '<a class="premium_link" target="_blank" href="'.$kbd_premium_link.'">Get premium version now!</a>';
	$kbd_rs[] = '<a target="_blank" href="http://www.websitedesignwebsitedevelopment.com/website-development/keep-backup-daily-how-to-restore-your-backup-files/1363/">How to restore backup files?</a>';
	$kbd_rs[] = '<a target="_blank" href="plugin-install.php?tab=search&s=wp+mechanic&plugin-search-input=Search+Plugins">Install WP Mechanic</a>';
	$kbd_rs[] = '<a target="_blank" href="http://www.websitedesignwebsitedevelopment.com/contact">Contact Developer</a>';
	
	

	

	
	
	

	register_activation_hook(__FILE__, 'kbd_start');

	//KBD END WILL REMOVE .DAT FILES	
	register_deactivation_hook(__FILE__, 'kbd_end' );

	add_action('init', 'init_sessions');	

	add_action( 'admin_menu', 'kbd_menu' );	

	add_action( 'admin_enqueue_scripts', 'register_kbd_styles' );
			
	if(isset($_REQUEST['kbd_cron_process']) && $_REQUEST['kbd_cron_process']=1)
	{		
		//ACTION TIME FOR BACKUP ACTIVITY
		add_action('init', 'kbd_cron_process', 1);	
	}


	
	if(is_admin()){
		$plugin = plugin_basename(__FILE__); 
		add_filter("plugin_action_links_$plugin", 'kbd_plugin_links' );	
		
	}
if(isset($_REQUEST['kbd_labs'])){

}