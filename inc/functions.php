<?php

	//ENCRYPTION FUNCTION


	if(!function_exists('kbd_encrypt')){


		function kbd_encrypt($decrypted, $password, $salt=''){


		 // Build a 256-bit $key which is a SHA256 hash of $salt and $password.


		 $key = hash('SHA256', $salt . $password, true);


		 // Build $iv and $iv_base64.  We use a block size of 128 bits (AES compliant) and CBC mode.  (Note: ECB mode is inadequate as IV is not used.)


		 srand(); 


                 if(function_exists('mcrypt_create_iv'))
                 $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
                 else
                 $iv = '鶵�^)W�D';


		 if (strlen($iv_base64 = rtrim(base64_encode($iv), '=')) != 22) return false;


		 // Encrypt $decrypted and an MD5 of $decrypted using $key.  MD5 is fine to use here because it's just to verify successful decryption.


		 $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $decrypted . md5($decrypted), MCRYPT_MODE_CBC, $iv));


		 // We're done!


		 return $iv_base64 . $encrypted;


		 }


	}
	//FOR QUICK DEBUGGING


	if(!function_exists('pre')){
	function pre($data){
			if(isset($_GET['debug'])){
				pree($data);
			}
		}	 
	} 	
	if(!function_exists('pree')){
	function pree($data){
				echo '<pre>';
				print_r($data);
				echo '</pre>';	
		
		}	 
	} 

	
	function kbd_menu(){

		 add_options_page('Keep Backup Daily', 'KBD Settings', 'install_plugins', 'kbd_settings', 'kbd_settings',   plugin_dir_url(__FILE__).'/images/database_email.png', 66);
		 
		 add_options_page('Download Backup Daily', 'Backup Now', 'install_plugins', 'kbd_download', 'kbd_download',   plugin_dir_url(__FILE__).'/images/database_email.png', 66);

	}
	
	function kbd_download() { 
		if ( !current_user_can( 'install_plugins' ) )  {

			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}	
		kbd_force_download();
	}

	//if(isset($_GET['page']) && $_GET['page']=='kbd_download')
	//kbd_force_download();
	
	function kbd_settings() { 

		if ( !current_user_can( 'install_plugins' ) )  {

			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
 			
		}

		 

		global $wpdb, $kbd_data, $kbd_pro; 
		$blog_info = get_bloginfo('admin_email');

		$salt = date('YmddmY')+date('m');

		//DEFAULT BACKUP RECIPIENT EMAIL ADDRESS	
		$default_email = get_bloginfo('admin_email');
		
		$default_email = $default_email!=''?$default_email:'info@'.str_replace('www.','',$_SERVER['HTTP_HOST']); 

		$kbd_settings_file = dirname(__FILE__).'/settings.dat';		//SETTINGS PARAMS TO BE STORED IN .DAT FILE		$settings = array();

		$settings['recpient_email_address']=array();

		$settings['backup_required'] = 'cron_d';

		$settings['maintain_log'] = 1;

		$settings['cron_server'] = 'default';				$settings['notification'] = ''; 

		$settings['notification_class'] = '';

		


		$kbd_log_file = dirname(__FILE__).'/log.dat';

		$settings['log'] = file_exists($kbd_log_file)?file_get_contents($kbd_log_file):'';

		 		//ENSURING THE VALID EMAIL ADDRESS	
		if(isset($_POST['recpient_email_address']) && isValidEmail($_POST['recpient_email_address']))

		{  

				//PREVENTING CSRF		
		
		if(isset($_POST['kbd_key']) && $_SESSION['kbd_key']==$_POST['kbd_key']) 
		{			$data = array(

			'backup_required'=>$_POST['backup_required'],

			'recpient_email_address'=>($_POST['recpient_email_address']==$default_email?'KBD':$_POST['recpient_email_address']),

			'maintain_log'=>$_POST['maintain_log'],

			'cron_server'=>$_POST['cron_server']			

			);

			//ACTION URL FOR BACKUP & EMAIL ACTIVITY			
			$submitted_url = update_kbd_cron($data);

			//STORING SETTINGS IN .DAT FILE			
			$data = serialize($data);

			$handle = fopen($kbd_settings_file,'wb+');

			fwrite($handle, $data);

			fclose($handle);			$settings['notification'] = 'Settings saved.';
			copy($kbd_settings_file, WP_PLUGIN_DIR.'/kbd_settings.dat');
			$settings['notification_class'] = 'updated';
			
			//GETTING EXPECTED BACKUP EMAIL TIME FROM SERVER

			$remote_uri = 'http://www.androidbubbles.com/api/kbd.php?next_backup='.time().'&backup_time='.$_POST['backup_required'].'&domain_url='.base64_encode($submitted_url);

			$_SESSION['expected_backup']=@file_get_contents($remote_uri);

		}

		else

		{

			$settings['notification'] = 'Access Denied.';

			$settings['notification_class'] = 'error';

		}

		

		}

		elseif(isset($_POST['recpient_email_address']))

		{

			$settings['notification'] = 'Invalid Email Address.';

			$settings['notification_class'] = 'error';

		}

		//STORING ENCRYPTION KEY IN SESSION	
		$_SESSION['kbd_key'] = $settings['kbd_key'] = kbd_encrypt($_SERVER['HTTP_HOST'].date('m'), $_SERVER['HTTP_HOST'], $salt);

		//LOADING STORED SETTINGS FROM .DAT FILE		
		$settings = load_kbd_settings($settings);				
		//ENSURING THAT RECIPIENT IS ONLY ONE	
		if(count($settings['recpient_email_address'])==0)		{			$settings['recpient_email_address'][] = $default_email;		}		

						$settings['notification'] = $settings['notification_class']!=''?'<div class="'.$settings['notification_class'].' settings-error" id="setting-error-settings_updated"> 

<p><strong>'.$settings['notification'].'</strong></p></div>':'';		

		

            $expected_backup = isset($_SESSION['expected_backup'])?$_SESSION['expected_backup']:'';				//EXPECTED BACKUP EMAIL GENERATION TIME	
			$settings['cron_d']['expected_backup'] = '';
			$settings['cron_w']['expected_backup'] = '';
			$settings['cron_m']['expected_backup'] = '';
			$settings['cron_y']['expected_backup'] = '';
			$settings[$settings['backup_required']]['expected_backup'] = ($expected_backup!=''?'<div class="alert alert-success">
      <strong>Well done!</strong> Your backup will be in your inbox on time.</div>
':'');						

		include('kbd_settings.php');			

	}	
	
	
	function register_kbd_styles() {
		plugins_url('style.css', __FILE__);
		wp_register_style( 'kbd-style', plugins_url('css/style.css', dirname(__FILE__)) );
		wp_enqueue_style( 'kbd-style' );
		wp_enqueue_script(
			'kbd-scripts',
			plugins_url('js/scripts.js', dirname(__FILE__)),
			array('jquery')
		);	
	}
	
	if(!function_exists('init_sessions')){


		function init_sessions(){


			if (!session_id()){

				ob_start();
				@session_start();


			}


		}


	}

	if(!function_exists('load_kbd_settings')){

		function load_kbd_settings($settings=array()){

			$kbd_settings_file = dirname(__FILE__).'/settings.dat';

			if(!file_exists($kbd_settings_file) && file_exists(WP_PLUGIN_DIR.'/kbd_settings.dat')){
				copy(WP_PLUGIN_DIR.'/kbd_settings.dat', $kbd_settings_file);				
				//unlink(WP_PLUGIN_DIR.'/kbd_settings.dat');
			}
			
			if(file_exists($kbd_settings_file)){

				$data = file_get_contents($kbd_settings_file);

				if($data!=''){

					if(is_array(unserialize($data)))


					{

						$data = unserialize($data);

						

						$settings = array_merge($settings, $data);
						
						if($settings['recpient_email_address']=='KBD'){
						
						$settings['recpient_email_address'] = get_bloginfo('admin_email');
						}

					}

				}

				

			}	
			return $settings;

		}	

	}
	if(!function_exists('log_kbd')){

		function log_kbd($string){

			$kbd_log_file = dirname(__FILE__).'/log.dat';

			if($string!='')

			{				

				if(file_exists($kbd_log_file)){

					$string = $string.'<br>'.file_get_contents($kbd_log_file);					

				}

				

				$f = fopen($kbd_log_file, 'wb+');

				fwrite($f, $string);

				fclose($f);

				

			}

		}

	}
	if(!function_exists('kbd_start')){


		function kbd_start(){	

				

		}	


	}
	if(!function_exists('kbd_end')){

		function kbd_end(){	

			$kbd_log_file = dirname(__FILE__).'/log.dat';

			if(file_exists($kbd_log_file)){


				unlink($kbd_log_file);


			}
			
			$data = array();

			return update_kbd_cron($data);		
		}

		

	}	
	
	
	if(!function_exists('update_kbd_cron')){

		function update_kbd_cron($data){	


			$wpurl = get_bloginfo('wpurl');


			$return = $data['p']=$wpurl.'/?kbd_cron_process=1';

			$data = http_build_query($data);

			if(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']!='localhost'){


				$ch = curl_init();


				curl_setopt($ch, CURLOPT_POST, 1);


				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);


				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );


				curl_setopt( $ch, CURLOPT_URL, 'http://www.androidbubbles.com/api/kbd.php');


				


				curl_setopt( $ch, CURLOPT_POSTFIELDS, $data);


				$txResult = curl_exec( $ch );


				curl_close( $ch );


			}


			return $return;

		}

	}
	if(!function_exists('isValidEmail')){

		function isValidEmail($email){

	    //return preg_match("/^[_a-z0-9-]+(\.[_a-z0-9+-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i", $email);
		return is_email($email);

		}	

	}
	
	if(!function_exists('formatSizeUnits'))
	{
		function formatSizeUnits($bytes)
		{
			if ($bytes >= 1073741824)
			{
				$bytes = number_format($bytes / 1073741824, 2) . ' GB';
			}
			elseif ($bytes >= 1048576)
			{
				$bytes = number_format($bytes / 1048576, 2) . ' MB';
			}
			elseif ($bytes >= 1024)
			{
				$bytes = number_format($bytes / 1024, 2) . ' KB';
			}
			elseif ($bytes > 1)
			{
				$bytes = $bytes . ' bytes';
			}
			elseif ($bytes == 1)
			{
				$bytes = $bytes . ' byte';
			}
			else
			{
				$bytes = '0 bytes';
			}
		
			return $bytes;
		}
	}
	
	if(!function_exists('requirements_check'))
	{

		function requirements_check()
		{
			 $return = array();
			 $return['writable_dir'] = dirname(__FILE__);
			 
			 if(!is_writeable($return['writable_dir']))
			 @chmod($return['writable_dir'], 0777);
			 
			 $return['mcrypt_create_iv'] = function_exists('mcrypt_create_iv');
			 $return['ZipArchive'] = class_exists('ZipArchive');			 
			 $return['is_writable'] = is_writable($return['writable_dir']);
			 $return['finfo'] = class_exists('finfo');			 
			 
			 return $return;
		}
	}	
	
	if(!function_exists('set_html_content_type')){
		function set_html_content_type()
		{

			return 'text/html';

		}	

	}
	
	if ( ! function_exists("file_parts"))
	{
		function file_parts($url,$params="ext")
		{
		
			if($params=="ext")
			{
			$parts = explode(".",$url);
			return end($parts);
			}
			elseif($params=="name")
			{
			$parts = explode("/",$url);
			return end($parts);
			}
			else
			{
			$parts = explode("/",$url);
			$file_name_ext = explode(".",end($parts));
			$file_name = array_pop($file_name_ext);
			return implode(".",$file_name);
			}
		}
	}	

	if ( ! function_exists("kbd_plugin_links"))
	{
		function kbd_plugin_links($links) { 
			global $kbd_premium_link, $kbd_pro;
			
			$settings_link = '<a href="options-general.php?page=kbd_settings">Settings</a>';
			
			if($kbd_pro){
				array_unshift($links, $settings_link); 
			}else{
				 
				$kbd_premium_link = '<a href="'.$kbd_premium_link.'" title="Go Premium" target=_blank>Go Premium</a>'; 
				array_unshift($links, $settings_link, $kbd_premium_link); 
			
			}
			
			
			return $links; 
		}
	}