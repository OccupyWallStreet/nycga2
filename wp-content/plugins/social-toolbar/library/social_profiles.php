<div class="wrap">
	<h2><?php echo _e("Social Profile URL's",'WPSOCIALTOOLBAR'); ?></h2>

	<?php include_once dirname(__FILE__).'/go_pro_ad.php'; ?>
	<?php
	if(isset($_POST['wpst_hidden_icon_profiles']))
	{

		unset($_POST['update']);
		
		ksort($_POST['social_profile']);
		

		$old_values=get_option('SOCIALTOOLBARICONS');
		
		
		for($i=0;$i<count($old_values);$i++)
		{
			$old_values[$i]['url']=$_POST['social_profile'][$i];
			if(in_array($i,$_POST['social_toolbar_enable']))
			{
				$old_values[$i]['enable']=1;
			}
			else
			{
				$old_values[$i]['enable']=0;
			}
		}
		update_option('SOCIALTOOLBARICONS', $old_values);


		echo '<div class="updated fade" id="message"><p>';
		_e('Wordpress Social Toolbar Settings <strong>Updated</strong>');
		echo '</p></div>';
		
	}
	?>


	<table cellspacing="5" cellpadding="5" border="0" width="100%">
	<tr>
	<td width="70%" valign="top">
	<?php
	$options=get_option('SOCIALTOOLBAROPTIONS');
	$form_url=admin_url().'admin.php?page=social_toolbar_profiles';
	?>
		<form name="social_toolbar_icons_form" id="social_toolbar_icons_form" method="POST" action="<?php echo $form_url;?>">
		<?php include_once dirname(__FILE__).'/social_icons.php'; ?>	
		</form>
		<br />
		<table cellspacing="5" cellpadding="5" class="widefat" width="400">
			<thead>
			<tr>
			<th scope="col" colspan="2"><?php _e('Custom Icon Settings','WPSOCIALTOOLBAR'); ?>
			</th>
			</tr>
			</thead>
			<tr>
			<td><label><?php _e('Name:','WPSOCIALTOOLBAR');?></label></td><td><input type="text" name="wpst_icon_name" class="social_basic" size="50" /><small class="wpst_pro_only"><?php echo DDST_PRO_ONLY_TEXT; ?></small></td>
			</tr>
			<tr>
			<td><label><?php _e('Image :','WPSOCIALTOOLBAR');?></label></td><td><input type="text" name="wpst_icon_image" class="social_basic" id="wpst_icon_image" size="50" /><input type="button" class="social_basic" id="wpst_custom_img_button" value="Upload Icon" />
			<p><span><small><?php _e('Enter an Icon url or upload your custom icon.','WPSOCIALTOOLBAR');?></small><small class="wpst_pro_only"><?php echo DDST_PRO_ONLY_TEXT; ?></small></span></p>
			</td>
			</tr>
			<tr>
			<td><label><?php _e('URL :','WPSOCIALTOOLBAR');?></label></td><td><input type="text" name="wpst_icon_url" class="social_basic" size="50" /><small class="wpst_pro_only"><?php echo DDST_PRO_ONLY_TEXT; ?></small></td>
			</tr>
			<tr>
			<td colspan="2">
            <input type="submit" class="button-primary social_basic"  value="<?php _e('Add Icon','WPSOCIALTOOLBAR') ?>" />
			</td>
		</table>
		
	</td>
	<td width="30%" valign="top">
	<?php include_once dirname(__FILE__).'/our_feeds.php'; ?>
	</td></tr>
	</table>
		<?php DDST_admin_footer_code();?>
</div>