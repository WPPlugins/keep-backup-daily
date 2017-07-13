<?php ini_set('max_execution_time', 60*60*30);//ini_set('zlib.output_compression', 'Off');//ini_set('max_input_time', 60*60*11);

		/* backup the db OR just a table */

		function manage_big_iteration($table, $rows, $slice=1000, $cycle=1){

			global $wpdb, $kbd_buf;

			$limit = ($slice*$cycle);
			
			$res_query = 'SELECT * FROM '.$table.' LIMIT '.$limit;
			
			$cut = ceil($rows/$slice);
			
			if($cut>=1 && $cycle>1){								
				$res_query .= ', '.$slice;	
			}
			
			//pree($table.' > '.$rows.' > '.$cut.' > '.$res_query);
			
			$result = $wpdb->get_results($res_query);	
			
			if(!empty($result)){//  && 1==2){//
					//pree($num_fields);
					//for ($i = 0; $i < $num_fields; $i++) {
						//pree($result);

						$handle = fopen($kbd_buf,'a');
						//pree($table);exit;
						$return = 'INSERT INTO `'.$table.'`';
						$object=0;
						foreach($result as $key=>$obj){
							$object++;
						
		
							$j = 0;
							if(!empty($obj)){
								//pree($obj);
								if($object==1){
								$keys = array_keys((array)$obj);
								$num_fields = count($keys);
								if(!empty($keys))
									$return .= '(`'.implode('`,`', $keys).'`) VALUES';									
								}
								$return .= '(';
								
								foreach($obj as $key=>$val){
									$val = addslashes($val);
									$val = ereg_replace("\n","\\n",$val);
									//pree($j.'>'.$val);
									//echo $val.'<br />';exit;
									if($val!=''){ $return.= '"'.$val.'"' ; } else { $return.= '""'; }
									if ($j<($num_fields-1)) { $return.= ','; }
									$j++;
								}								
								$return.= ")";
								if($object<count($result)){
									$return.= ",";
								}
								if($object==count($result)){
									$return.= ";";
								}
								

								
								//pree($return);exit;
							}
			 
						  
						}
						
						
						$return.="\n\n\n";
						fwrite($handle, $return);
						fclose($handle);
													
					//}
				
			}			//pree($return);exit;
			
			if($cut>=1 && $limit<=$rows){
				$cycle++;
				$return .= manage_big_iteration($table, $rows, $slice, $cycle);
			}
			
			return $return;
		}
		
		function backup_tables_updated($name, $backup_file, $zip_file, $tables = '*'){ 
			global $wpdb, $kbd_buf;
			$kbd_rc = requirements_check();	
			$ret = false;
			$exists = file_exists($backup_file);
			$kbd_buf = $backup_file;
			if(!$exists){			
				ob_clean();	ob_start();				
				$cmd = "mysqldump --host=".DB_HOST." --user=".DB_USER." --password=".DB_PASSWORD." --databases ".DB_NAME." > $backup_file"; 
				passthru( $cmd );							
				if($kbd_rc['ZipArchive']){
					$ret = kbd_zip_it($zip_file, $backup_file);
				}
				
			}
			return $ret;
		}
		
		function backup_tables($name, $backup_file, $zip_file, $tables = '*'){ 
		global $wpdb, $kbd_buf;
		$kbd_rc = requirements_check();	
		$ret = false;
		$exists = file_exists($backup_file);
		$kbd_buf = $backup_file;
		
		//pree(!$exists);
		//pree($tables);
		//pree($kbd_rc);exit;
		//pree($name.' > '.$backup_file.' > '.$zip_file.' > '.$tables);exit;
		
		if(!$exists){
			if($tables == '*'){
				
				$tables = array();
				
				$result = $wpdb->get_results('SHOW TABLES');
				//pree($result);
				foreach($result as $obj){
	
				  $tables[] = current($obj);
	
				}
				

			}
			else{

				$tables = is_array($tables) ? $tables : explode(',',$tables);

			}
			
			//cycle through
			//pree($tables);//exit;
			//pree(ini_get('max_execution_time'));exit;
			//pree(ini_get('max_input_time'));exit;
			$t=0;
			foreach($tables as $table) { $t++;
				//if($t<=3){
				$handle = fopen($backup_file,'a');
				
				$count = 'SELECT COUNT(*) FROM '.$table;
				$res_count = $wpdb->get_var($count);
				
				//echo '<br />'.($t.'>'.$res_count);
				
				//pree($res_query);

					//pree($res_count);
				//if(!empty($result)){
								
					$num_fields = count($result);
		
					$return = 'DROP TABLE IF EXISTS `'.$table.'`;';
		
					$row2 =  $wpdb->get_results(('SHOW CREATE TABLE '.$table));
					$row2 = current($row2);			
					$row2 = (array)($row2);
					$row2 = end($row2);
					$return.= "\n".$row2.";\n";
					//pree($row2);//exit;
					$return.="\n";
					fwrite($handle, $return);
					fclose($handle);
					
							
					$table_rows = manage_big_iteration($table, $res_count);
					//pree($table_rows);
					//$return = $table_rows;
		
					
					
				//}else{
					//pree($table);
				//}

				//}
			}	
			//exit;
			//pree($return);exit;
			//$backup_file = $backup_file;

			

			

			
			
			$ret = true;

		}else{
			//pree($exists);exit;
		}

		if($kbd_rc['ZipArchive']){
			$ret = kbd_zip_it($zip_file, $backup_file);
		}else{
			//plaing file downloading
		}
        //pree(!$ret);exit;
		return $ret;

		}


		if(!function_exists('kbd_zip_it')){


		function kbd_zip_it($zip_file, $backup_file){		
		 
				$ret = false;
				if(!file_exists($zip_file) && file_exists($backup_file)){
		
			  		ob_clean();
					//echo $backup_file;exit;
					$zip = new ZipArchive;
		
					if ($zip->open($zip_file, ZIPARCHIVE::CREATE | ZipArchive::OVERWRITE) === TRUE) {
		
						$bkup_file = DB_NAME.'.sql';
						
						$zip->addFile($backup_file, $bkup_file);	
							
		
						$zip->close(); 
		
						$ret = true;
		
					} 
		
				}
		
				else
		
				{
		
					$ret = true;
		
				}
		
				if(file_exists($backup_file) && file_exists($zip_file)){
		
					$zip = new ZipArchive;
					$res = $zip->open($zip_file);
					if ($res === TRUE) {
						unlink($backup_file);
					}
		
				}
				
				return $ret;

			}
		}

		function clear_old_backups(){
			$dir = dirname(__FILE__).'/';
			if ($handle = opendir($dir)) {
					while (false !== ($entry = readdir($handle))) {
						if ($entry != "." && $entry != "..") {
								$ext = end(explode('.',$entry));
								switch($ext){
									case 'zip':
										unlink($dir.$entry);
									break;
								}
						}
					}
					closedir($handle);
			}
		}
	
				
		function kbd_cron_process(){
				$kbd_rc = requirements_check();	

				$settings = load_kbd_settings();	

                $default_email = get_bloginfo('admin_email');
				$configEmail = $settings['recpient_email_address'];

				$body = $_SERVER['HTTP_HOST'].' - Database Backup by Wordpress Plugin Keep Backup Daily';
				
				if($configEmail==''){
				$configEmail = $default_email;
				$body .= ' - "You are receiving backups on your Admin Email address." ';
				}
				
				$backup_stats = get_backup_stats();
				$attach_file = $backup_stats['attach_file'];
				$zip_created = $backup_stats['zip_created'];
				$zip_file = $backup_stats['zip_file'];					
					
				$subject = str_replace(array('.zip', '.gz'), '', basename($zip_file));
                
                //pree($attach_file); 
				//exit;
				//add_filter( 'wp_mail_content_type', 'set_html_content_type' );
				if(wp_mail( $configEmail, $subject, $body, '', array($attach_file) ))

				{
				
					$db_size = formatSizeUnits(filesize($attach_file));
					unlink($attach_file);
                                       

					if($settings['maintain_log'])

					{	

						$string = 'File size: '.$db_size.',  Sent to <a mailto="'.$configEmail.'">'.$configEmail.'</a> at '.date('d M, Y h:i:s a');

						log_kbd($string);

					}					
					
					echo '<span style="color:green">'.strtolower($_SERVER['HTTP_HOST']).'</span>';

				}else{
					echo '<span style="color:red">'.strtoupper($_SERVER['HTTP_HOST']).'</span>';
				}
				clear_old_backups();
				//remove_filter( 'wp_mail_content_type', 'set_html_content_type' ); 
				
			exit;
		}
	
		function get_backup_stats($stats_only = false){
			
			$ret =  array();			
			$backup_file = dirname(__FILE__).'/'.DB_NAME.'.sql';
			//echo $backup_file;exit;					
			$attach_file = $zip_file = dirname(__FILE__).'/'.DB_NAME.'_'.date('d_m_Y').'.sql.gz';
			$zip_created = 0;
			$zip_created = ($stats_only?false:backup_tables_updated(DB_NAME, $backup_file, $zip_file));	
			if(!$kbd_rc['ZipArchive'] && !$zip_created){
				$attach_file = $backup_file;
			}	
			$ret['backup_file'] = $backup_file;
			$ret['attach_file'] = $attach_file;
			$ret['zip_created'] = $zip_created;
			$ret['zip_file'] = $zip_file;
			//pree($ret);exit;
			return $ret;
		
		}
		
		function kbd_force_download()
		{
				ob_clean();	ob_start();
				$backup_stats = get_backup_stats(true);
				//pree($backup_stats);exit;
				$filename = basename($backup_stats['zip_file']);
				$cmd = "mysqldump --host=".DB_HOST." --user=".DB_USER." --password=".DB_PASSWORD." --databases ".DB_NAME." | gzip --best";   	
				//echo $filename; exit;
				//echo $cmd; exit;
				$mime = "application/x-gzip";	
				//pree($filename);exit;
				header( "Content-Type: " . $mime );
				header( 'Content-Disposition: attachment; filename="' . $filename . '"' );					
				passthru( $cmd );
				exit;
		}	
		