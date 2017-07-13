// JavaScript Document
jQuery(document).ready(function($){
		
	$('.dismiss_link').click(function(){
			$(this).parent().slideUp();
			$('.useful_link').fadeIn();
		});
		$('.useful_link').click(function(){
			$('.dismiss_link').parent().slideDown();
			$(this).fadeOut();
		});
	
	$('#kbd_backup_now').click(function(){
		document.location.href = 'options-general.php?page=kbd_download';
	});
	
	$('#kbd_cron_custom').parent().find('a').click(function(){
	
		$('.cron_line').toggle();
	
	});$('#cron_now').click(function(){
		
		var dh = $(this).html();
	
		$(this).parent().append('<p class="sending_backup">Sending to '+$('#recpient_email_address').val()+'</p>');
		$(this).html('Please wait...');
		
		var jqxhr = $.get($('.cron_line input').val(), function() {
		
		})
		.done(function() { $('.sending_backup').html('Successfully sent.'); })
		.fail(function() { $('.sending_backup').html('Failed.'); })
		.always(function() { $('.sending_backup').html('Please check your inbox.'); });
		
		$(this).html(dh);
	
		
	});
		
		$('input[name="backup_required"][checked="checked"]').parent().addClass('active');
		
	$('label.btn.btn-primary').click(function(){
		$(this).parent().find('label.btn.btn-primary.active').find('input').removeAttr('checked');
		$(this).parent().find('label.btn.btn-primary.active').removeClass('active');
		$(this).addClass('active');
		$(this).find('input').attr('checked', 'checked');
	});
		
});