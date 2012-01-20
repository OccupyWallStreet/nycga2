<?php

/*--------------------------------------------------------------------------------------
*
*	Update - Ajax Functions
*
*	@author Elliot Condon
*	@since 3.0.0
* 
*-------------------------------------------------------------------------------------*/

$version = get_option('acf_version','1.0.5');
$next = false;

// list of starting points
if(version_compare($version,'3.0.0') < 0)
{
	$next = '3.0.0';
}
?>
	
<script type="text/javascript">
(function($){
	
	function add_message(messaage)
	{
		$('#wpbody-content').append('<p>' + messaage + '</p>');
	}
	
	function run_upgrade(version)
	{
		$.ajax({
			url: ajaxurl,
			data: { action : 'acf_upgrade', version : version },
			type: 'post',
			dataType: 'json',
			success: function(json){
				
				if(json)
				{
					if(json.status)
					{
						add_message(json.message);
						
						// next update?
						if(json.next)
						{
							run_upgrade(json.next);
						}
						else
						{
							// all done
							add_message('Upgrade Complete! <a href="<?php echo admin_url(); ?>edit.php?post_type=acf">Continue to ACF &raquo;</a>');
						}
					}
					else
					{
						// error!
						add_message('Error: ' + json.message);
					}
				}
				else
				{
					// major error!
					add_message('Sorry. Something went wrong during the upgrade process. Please report this on the support forum');
				}
			}
		});
	}
	
	<?php if($next){ echo 'run_upgrade("' . $next . '");'; } ?>
	
})(jQuery);
</script>
<style type="text/css">
	#message {
		display: none;
	}
</style>
<?php 

if(!$next)
{
	echo '<p>No Upgrade Required</p>';
}

?>