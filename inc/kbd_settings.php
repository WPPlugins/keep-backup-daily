<div class="wrap kbd_settings">

<div class="icon32" id="icon-options-general"><br></div><h2>Keep Backup Daily <?php echo '('.$kbd_data['Version'].($kbd_pro?') Pro':')'); ?> - Settings</h2>

<?php echo $settings['notification']; $wpurl = get_bloginfo('wpurl'); ?>

<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">





<input type="hidden" name="kbd_key" value="<?php echo $settings['kbd_key']; ?>">

<table class="form-table">

<tbody><tr valign="top">

<th scope="row">Backup Required</th>

<td id="front-static-pages">
  

<div class="btn-group" data-toggle="buttons">
  <label class="btn btn-primary">
    <input type="radio" name="backup_required" id="cron_d" value="cron_d" <?php echo ($settings['backup_required']=='cron_d'?'checked="checked"':''); ?>> Daily
  </label>
  <label class="btn btn-primary">
    <input type="radio" name="backup_required" id="cron_w" value="cron_w" <?php echo ($settings['backup_required']=='cron_w'?'checked="checked"':''); ?>> Weekly
  </label>
  <label class="btn btn-primary">
    <input type="radio" name="backup_required" id="cron_m" value="cron_m" <?php echo ($settings['backup_required']=='cron_m'?'checked="checked"':''); ?>> Monthly
  </label>
  <label class="btn btn-primary">
    <input type="radio" name="backup_required" id="cron_y" value="cron_y" <?php echo ($settings['backup_required']=='cron_y'?'checked="checked"':''); ?>> Yearly
  </label>  
</div>
<div style="clear:both; float:left; margin:0px 0;">
<?php echo $settings['cron_d']['expected_backup']; ?>
<?php echo $settings['cron_w']['expected_backup']; ?>
<?php echo $settings['cron_m']['expected_backup']; ?>
<?php echo $settings['cron_y']['expected_backup']; ?>
&nbsp;
</div>

<fieldset>


	<p>
    <a id="cron_now" title="Click here to email your backup now">Email Backup Now</a>
    &nbsp;|&nbsp;
     <a id="kbd_backup_now" title="Click here to download your backup now">Download Backup Now</a>
    
    </p>
    
</fieldset></td>

<td colspan="2">
    <div class="kbd_rc_console">
    <strong>Recommended Links</strong>
    <?php global $kbd_rs;?>
    <ul class="kbd_rd">
        <li><?php echo implode('</li><li>', $kbd_rs); ?></li>
    </ul>    
    </div>  
</td>

</tr>

<tr valign="top">

<th scope="row"><label for="recpient_email_address">Recipient Email Address</label></th>

<td colspan="2">

<input type="text" class="medium-text" value="<?php echo is_array($settings['recpient_email_address'])?implode(',', $settings['recpient_email_address']):$settings['recpient_email_address']; ?>" step="1" name="recpient_email_address" id="recpient_email_address">

<p class="description">Default: <?php echo $default_email; ?></p>

</td>

<td rowspan="3">

  
  
  
  
  
  <div class="kbd_rc_console">
<strong>Requirements Console</strong>
<?php global $kbd_rc;?>
<ul class="kbd_rc">

	<li title="<?php echo $kbd_rc['is_writable']!=1?'Backup file can not be created in ['.$kbd_rc['writable_dir'].' ('.decoct(fileperms($kbd_rc['writable_dir']) & 0777).')]"'.' class="cross':'Everything is Good!"'.' class="tick';?>"> Write Permissions</li>

    <li class="hide" title="<?php echo $kbd_rc['ZipArchive']!=1?'Backup file can not be compressed, you will get .sql file as backup."'.' class="cross':'Everything is Good!"'.' class="tick';?>">Zip Library</li>  

    <li class="hide" title="<?php echo $kbd_rc['mcrypt_create_iv']!=1?'You are lacking an improved security measure but it will not affect in normal cases."'.' class="cross':'Everything is Good!"'.' class="tick';?>">MCRYPT Library</li>

    <li class="hide" title="<?php echo $kbd_rc['finfo']!=1?'You might will not be able to download database backup with Backup Now option."'.' class="cross':'Everything is Good!"'.' class="tick';?>">Fileinfo Library</li>

</ul>
<div class="bottom_links"><a class="kbd_comments" href="http://www.websitedesignwebsitedevelopment.com/website-development/php-frameworks/wordpress/plugins/wordpress-plugin-keep-backup-daily/1046/#reply-title" target="_blank">Need Help?</a></div>
</div>  
</td>
</tr>

<tr valign="top">

<th scope="row">Maintain Log</th>

<td colspan="2"><fieldset>

	<label for="maintain_log"><input type="checkbox" value="1" <?php echo ($settings['maintain_log']==1?'checked="checked"':''); ?> name="maintain_log" id="maintain_log">

	You will be able to view log with date and time.</label>

	<p class="description">Only log file will be stored on your server.</p>

    

    <p class="description">

    <?php if($settings['log']!=''): ?>

    <div style="height:160px; background-color:#F3F3F3; overflow:auto; width:64%;">

    <?php echo nl2br($settings['log']); ?>

    </div>

    <?php endif; ?>

    </p>

</fieldset></td>

</tr>

<tr valign="top">

<th scope="row">Cron Job Settings <span title="By default we will access cron file placed on your server for your convenience. Because most of the users don't have idea that how to set a cron or conscious about their server performance." style="color:red">(Important)</span></th>

<td colspan="2"><fieldset>

	<p><label for="kbd_cron_default">

		<input <?php echo ($settings['cron_server']=='default'?'checked="checked"':''); ?> type="radio" class="tog" id="kbd_cron_default" value="default" name="cron_server">Default</label>

	</p>

    <p><label for="kbd_cron_custom">

		<input <?php echo ($settings['cron_server']=='custom'?'checked="checked"':''); ?> type="radio" class="tog" id="kbd_cron_custom" value="custom" name="cron_server">Custom <a>(more)</a></label>

	</p>

    <p class="description cron_line" style="display:none">You have to run the following file, write the cron job command which is suitable on your server. <input type="text" class="large-text" value="<?php echo $wpurl; ?>/?kbd_cron_process=1"></p>

</fieldset></td>

</tr>
</tbody></table>

<p class="submit"><input type="submit" value="Save Changes" class="button button-primary" id="submit" name="submit">
</form>

</div>

<script type="text/javascript" language="javascript">
jQuery(document).ready(function($) {

});	
</script>
<style type="text/css">
#message{
	display:none;
}
#menu-settings li.current{
	background-color:#428bca;
}
<?php if($kbd_pro): ?>
.premium_link{ display:none; }
<?php else: ?>
.premium_link{ color:#F00; }
<?php endif; ?>
</style>