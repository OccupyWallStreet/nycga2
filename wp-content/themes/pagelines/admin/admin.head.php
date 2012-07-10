<?php 
	
	global $pl_foundry;
	$pl_foundry->setup_google_loaders();
	
	pagelines_register_hook( 'pagelines_admin_head' ); // Hook
?>
<script type="text/javascript">
/*<![CDATA[*/
jQuery(document).ready(function(){ 
<?php 

/**
 * AJAX Saving of framework settings
 * 
 * @package PageLines Framework
 * @subpackage AJAX
 * @since 1.2.0
 */
// Allow users to disable AJAX saving... 
if( !pagelines_option( 'disable_ajax_save' ) ): ?>	
jQuery("#pagelines-settings-form").submit(function() {
	
	var ajaxAction = "<?php echo admin_url( "admin-ajax.php" ); ?>";
	
	formData = jQuery("#pagelines-settings-form");
	serializedData = jQuery(formData).serialize();
	
	if(jQuery("#input-full-submit").val() == 1){
		return true;
	} else {
	
		jQuery('.ajax-saved').center('#pagelines-settings-form');
		var saveText = jQuery('.ajax-saved .ajax-saved-pad .ajax-saved-icon');
	
		jQuery.ajax({
			type: 'POST',
			url: 'options.php',
			data: serializedData,
			beforeSend: function(){
				
				jQuery('.ajax-saved').removeClass('success').show().addClass('uploading');

				saveText.text('Saving'); // text while saving
				
				// add some dots while saving.
				interval = window.setInterval(function(){
					var text = saveText.text();
					if (text.length < 10){	saveText.text(text + '.'); }
					else { saveText.text('Saving'); } 
				}, 400);
				
			},
		  	success: function(data){
				window.clearInterval(interval); // clear dots...
				jQuery('.ajax-saved').removeClass('uploading').addClass('success');
				saveText.text('Settings Saved!'); // change button text, when user selects file	
				
				// animate_pl_button();
				
				jQuery('.ajax-saved').show().delay(800).fadeOut('slow');
				
				
			}
			
		});
		return false;
	}
  
});
<?php endif;?>

}); 
/*]]>*/</script>
