<div class="wrap">
	<h2><?php echo _e('SOCIAL TOOLBAR','WPSOCIALTOOLBAR'); ?></h2>
	<?php include_once dirname(__FILE__).'/go_pro_ad.php'; ?>

	<?php
	if(isset($_POST['SOCIALTOOLBAROPTIONS']))
	{
		echo '<div class="updated fade" id="message"><p>';
		_e('Wordpress Social Toolbar Settings <strong>Updated</strong>');
		echo '</p></div>';
		unset($_POST['update']);
		update_option('SOCIALTOOLBAROPTIONS', $_POST['SOCIALTOOLBAROPTIONS']);

	}
	?>
	<!-- General Settings Start -->
	<table cellspacing="5" cellpadding="5" border="0" width="100%">
	<tr>
	<td width="70%" valign="top">
	<div class="maxi_left">
	<?php
	$options=get_option('SOCIALTOOLBAROPTIONS');
	$form_url=admin_url().'admin.php?page=social-toolbar/social-toolbar.php';
	?>
		<form name="DDST_settings_form" id="DDST_settings_form" method="POST" action="<?php echo $form_url;?>">
		<table cellspacing="5" cellpadding="5" class="widefat" width="400">
			<thead>
			<tr>
			<th scope="col" colspan="2"><?php _e('General Settings','WPSOCIALTOOLBAR'); ?>
			</th>
			</tr>
			</thead>
			<tr>
			<td><?php _e('Toolbar Position: ','WPSOCIALTOOLBAR'); ?></td>
			<td>
			<select name="SOCIALTOOLBAROPTIONS[position]" class="social_basic" style="width:120px;">
			<option value="bottom" <?php selected('bottom', $options['position']); ?>><?php _e('Bottom','WPSOCIALTOOLBAR'); ?></option>
			<option value="top" <?php selected('top', $options['position']); ?>><?php _e('Top','WPSOCIALTOOLBAR'); ?></option>
			</select> <span style="color:#666;"><small><?php _e('Select ToolBar Position. ','WPSOCIALTOOLBAR'); ?></small><small class="wpst_pro_only"><?php echo DDST_PRO_ONLY_TEXT; ?></small></span>
			</td>
			</tr>
			<tr>
			<td><?php _e('Icon Image Size: ','WPSOCIALTOOLBAR'); ?></td>
			<td>
			<select name="SOCIALTOOLBAROPTIONS[icon_size]" class="social_basic" style="width:120px;">
			<option value="normal" <?php selected('normal', $options['icon_size']); ?>><?php _e('Normal 40px','WPSOCIALTOOLBAR'); ?></option>
			<option value="small" <?php selected('small', $options['icon_size']); ?>><?php _e('Small 30px','WPSOCIALTOOLBAR'); ?></option>
			</select> <span style="color:#666;"><small><?php _e('Select Image Icon Size. ','WPSOCIALTOOLBAR'); ?></small><small class="wpst_pro_only"><?php echo DDST_PRO_ONLY_TEXT; ?></small></span>
			</td>
			</tr>

			<tr>
			<td><?php _e('Background Color: ','WPSOCIALTOOLBAR'); ?></td>
			<td><input type="text" class="DDST_colorpicker" name="SOCIALTOOLBAROPTIONS[background_color]" value="<?php echo $options['background_color']; ?>" size="15" /> <span style="color:#666;"><small><?php _e('Select Background Color','WPSOCIALTOOLBAR'); ?></small></span></td>
			</tr>
			<tr>
			<td><?php _e('Border Color: ','WPSOCIALTOOLBAR'); ?></td>
			<td><input type="text" class="DDST_colorpicker" name="SOCIALTOOLBAROPTIONS[border_color]" value="<?php echo $options['border_color']; ?>" size="15" />
			<span style="color:#666;"><small><?php _e('Select Border Color','WPSOCIALTOOLBAR'); ?></small></span>
			</td>
			</tr>
			
			<tr>
			<td><?php _e('Recent Tweet Background: ','WPSOCIALTOOLBAR'); ?></td>
			<td><input type="text" class="DDST_colorpicker" name="SOCIALTOOLBAROPTIONS[twitter_background]" value="<?php echo $options['twitter_background']; ?>" size="15" />
			<span style="color:#666;"><small><?php _e('Select Recent Tweet Background Color','WPSOCIALTOOLBAR'); ?></small></span>
			</td>
			</tr>
			<tr>
			<td><?php _e('Social Icon Hover Background: ','WPSOCIALTOOLBAR'); ?></td>
			<td><input type="text" class="DDST_colorpicker" name="SOCIALTOOLBAROPTIONS[hover_background]" value="<?php echo $options['hover_background']; ?>" size="15" />
			<span style="color:#666;"><small><?php _e('Select Icon Hover Color','WPSOCIALTOOLBAR'); ?></small></span>
			</td>
			</tr>
			<tr>
			<td><?php _e('Social Icon Color: ','WPSOCIALTOOLBAR'); ?></td>
			<td>
			<select name="SOCIALTOOLBAROPTIONS[icon_type]" id="maxi_position" style="width:120px;">
			<option value="white"  <?php selected('white', $options['icon_type']); ?>><?php _e('White','WPSOCIALTOOLBAR'); ?></option>
			<option value="black" <?php selected('black', $options['icon_type']); ?>><?php _e('Black','WPSOCIALTOOLBAR'); ?></option>
			<option value="gray" <?php selected('gray', $options['icon_type']); ?>><?php _e('Gray','WPSOCIALTOOLBAR'); ?></option>
			<option value="none" disabled="disabled" <?php selected('none', $options['icon_type']); ?>><?php _e('Blue (PRO Version Only)','WPSOCIALTOOLBAR'); ?></option>
			<option value="none" disabled="disabled" <?php selected('none', $options['icon_type']); ?>><?php _e('Red (PRO Version Only)','WPSOCIALTOOLBAR'); ?></option>
			<option value="none" disabled="disabled" <?php selected('none', $options['icon_type']); ?>><?php _e('Green (PRO Version Only)','WPSOCIALTOOLBAR'); ?></option>
			<option value="none" disabled="disabled" <?php selected('none', $options['icon_type']); ?>><?php _e('Pink (PRO Version Only)','WPSOCIALTOOLBAR'); ?></option>
			<option value="none" disabled="disabled" <?php selected('none', $options['icon_type']); ?>><?php _e('Orange (PRO Version Only)','WPSOCIALTOOLBAR'); ?></option>
			<option value="none" disabled="disabled" <?php selected('none', $options['icon_type']); ?>><?php _e('Violet (PRO Version Only)','WPSOCIALTOOLBAR'); ?></option>
			<option value="none" disabled="disabled" <?php selected('none', $options['icon_type']); ?>><?php _e('Yellow (PRO Version Only)','WPSOCIALTOOLBAR'); ?></option>
			</select> <span style="color:#666;"><small><?php _e('Select Social Icon Color','WPSOCIALTOOLBAR'); ?></small></span>
			</td>
			</tr>
			<tr>
			<td><?php _e('Hide / Show Icon Color: ','WPSOCIALTOOLBAR'); ?></td>
			<td>
			<select name="SOCIALTOOLBAROPTIONS[button_color]" id="maxi_position" style="width:120px;">
			<option value="white" <?php selected('white', $options['button_color']); ?>><?php _e('White','WPSOCIALTOOLBAR'); ?></option>
			<option value="black"  <?php selected('black', $options['button_color']); ?>><?php _e('Black','WPSOCIALTOOLBAR'); ?></option>
			<option value="gray" <?php selected('gray', $options['button_color']); ?>><?php _e('Gray','WPSOCIALTOOLBAR'); ?></option>
			<option value="none" disabled="disabled" <?php selected('none', $options['button_color']); ?>><?php _e('Blue (PRO Version Only)','WPSOCIALTOOLBAR'); ?></option>
			<option value="none" disabled="disabled" <?php selected('none', $options['button_color']); ?>><?php _e('Red (PRO Version Only)','WPSOCIALTOOLBAR'); ?></option>
			<option value="none"  disabled="disabled" <?php selected('none', $options['button_color']); ?>><?php _e('Green (PRO Version Only)','WPSOCIALTOOLBAR'); ?></option>
			<option value="none"  disabled="disabled" <?php selected('none', $options['button_color']); ?>><?php _e('Pink (PRO Version Only)','WPSOCIALTOOLBAR'); ?></option>
			<option value="none"  disabled="disabled" <?php selected('none', $options['button_color']); ?>><?php _e('Orange (PRO Version Only)','WPSOCIALTOOLBAR'); ?></option>
			<option value="none"  disabled="disabled" <?php selected('none', $options['button_color']); ?>><?php _e('Violet (PRO Version Only)','WPSOCIALTOOLBAR'); ?></option>
			<option value="none"  disabled="disabled" <?php selected('none', $options['button_color']); ?>><?php _e('Yellow (PRO Version Only)','WPSOCIALTOOLBAR'); ?></option>
			</select> <span style="color:#666;"><small><?php _e('Select Hide / Show Icon Color','WPSOCIALTOOLBAR'); ?></small></span>
			</td>
			</tr>
			
			<tr><td colspan="2">
			<input type="hidden" name="SOCIALTOOLBAROPTIONS[update]" value="UPDATED" />
            <input type="submit" class="button-primary" value="<?php _e('Save Settings','WPSOCIALTOOLBAR') ?>" />
			</td></tr>
		</table>
		<!-- General Settings ends -->
		<br />
		<!-- Recent Tweet Settings Starts -->
		<table cellspacing="5" cellpadding="5" class="widefat" width="400">
			<thead>
			<tr>
			<th scope="col" colspan="2"><?php _e('Recent Tweet Settings','WPSOCIALTOOLBAR'); ?>
			</th>
			</tr>
			</thead>
			<tr>
			<td>
			<?php _e("Show Recent Tweet", 'WPSOCIALTOOLBAR'); ?>:
			</td>
			<td>
			<select name="SOCIALTOOLBAROPTIONS[show_tweeter]" style="width:100px;">
			<option value="yes" <?php selected('yes', $options['show_tweeter']); ?>><?php _e('Yes','WPSOCIALTOOLBAR'); ?></option>
			<option value="no" <?php selected('no', $options['show_tweeter']); ?>><?php _e('No','WPSOCIALTOOLBAR'); ?></option>
			</select>
			</td>
			</tr>
			<tr>
			<td><?php _e('Twitter Bird Color: ','WPSOCIALTOOLBAR'); ?></td>
			<td>
			<select name="SOCIALTOOLBAROPTIONS[bird_color]" id="maxi_position" style="width:120px;">
			<option value="white" <?php selected('white', $options['bird_color']); ?>><?php _e('White','WPSOCIALTOOLBAR'); ?></option>
			<option value="black" <?php selected('black', $options['bird_color']); ?>><?php _e('Black','WPSOCIALTOOLBAR'); ?></option>
			<option value="gray" <?php selected('gray', $options['bird_color']); ?>><?php _e('Gray','WPSOCIALTOOLBAR'); ?></option>
			<option value="none" <?php selected('none', $options['bird_color']); ?> disabled ><?php _e('Blue (PRO Version Only)','WPSOCIALTOOLBAR'); ?></option>
			<option value="none" <?php selected('none', $options['bird_color']); ?> disabled ><?php _e('Red (PRO Version Only)','WPSOCIALTOOLBAR'); ?></option>
			<option value="none" <?php selected('none', $options['bird_color']); ?> disabled ><?php _e('Green (PRO Version Only)','WPSOCIALTOOLBAR'); ?></option>
			<option value="none" <?php selected('none', $options['bird_color']); ?> disabled ><?php _e('Pink (PRO Version Only)','WPSOCIALTOOLBAR'); ?></option>
			<option value="none" <?php selected('none', $options['bird_color']); ?> disabled ><?php _e('Orange (PRO Version Only)','WPSOCIALTOOLBAR'); ?></option>
			<option value="none" <?php selected('none', $options['bird_color']); ?> disabled ><?php _e('Violet (PRO Version Only)','WPSOCIALTOOLBAR'); ?></option>
			<option value="none" <?php selected('none', $options['bird_color']); ?> disabled ><?php _e('Yellow (PRO Version Only)','WPSOCIALTOOLBAR'); ?></option>
			</select> <span style="color:#666;"><small><?php _e('Select Twitter Bird Color','WPSOCIALTOOLBAR'); ?></small></span>
			</td>
			</tr>
			<tr>
			<td>
			<?php _e("Font Family", 'WPSOCIALTOOLBAR'); ?>:
			</td>
			<td><select name="SOCIALTOOLBAROPTIONS[font_family]"><option value="'Trebuchet MS', Helvetica, sans-serif" <?php selected("'Trebuchet MS', Helvetica, sans-serif", stripslashes($options['font_family'])); ?>>'Trebuchet MS', Helvetica, sans-serif</option><option value="Arial, Helvetica, sans-serif" <?php selected('Arial, Helvetica, sans-serif', stripslashes($options['font_family'])); ?>>Arial, Helvetica, sans-serif</option><option value="Tahoma, Geneva, sans-serif" <?php selected('Tahoma, Geneva, sans-serif', stripslashes($options['font_family'])); ?>>Tahoma, Geneva, sans-serif</option><option value="Verdana, Geneva, sans-serif" <?php selected('Verdana, Geneva, sans-serif', stripslashes($options['font_family'])); ?>>Verdana, Geneva, sans-serif</option><option value="Georgia, serif" <?php selected('Georgia, serif', stripslashes($options['font_family'])); ?>>Georgia, serif</option><option value="'Arial Black', Gadget, sans-serif" <?php selected("'Arial Black', Gadget, sans-serif", stripslashes($options['font_family'])); ?>>'Arial Black', Gadget, sans-serif</option><option value="'Bookman Old Style', serif" <?php selected("'Bookman Old Style', serif", stripslashes($options['font_family'])); ?>>'Bookman Old Style', serif</option><option value="'Comic Sans MS', cursive" <?php selected("'Comic Sans MS', cursive", stripslashes($options['font_family'])); ?>>'Comic Sans MS', cursive</option><option value="'Courier New', Courier, monospace" <?php selected("'Courier New', Courier, monospace", stripslashes($options['font_family'])); ?>>'Courier New', Courier, monospace</option><option value="Garamond, serif" <?php selected("Garamond, serif", stripslashes($options['font_family'])); ?>>Garamond, serif</option><option value="'Times New Roman', Times, serif" <?php selected("'Times New Roman', Times, serif", stripslashes($options['font_family'])); ?>>'Times New Roman', Times, serif</option><option value="Impact, Charcoal, sans-serif" <?php selected("Impact, Charcoal, sans-serif", stripslashes($options['font_family'])); ?>>Impact, Charcoal, sans-serif</option><option value="'Lucida Console', Monaco, monospace" <?php selected("'Lucida Console', Monaco, monospace", stripslashes($options['font_family'])); ?>>'Lucida Console', Monaco, monospace</option><option value="'MS Sans Serif', Geneva, sans-serif" <?php selected("'MS Sans Serif', Geneva, sans-serif", stripslashes($options['font_family'])); ?>>'MS Sans Serif', Geneva, sans-serif</option></select>
			</td>
			</tr>
			<tr>
			<td><?php _e('Font Size: ','WPSOCIALTOOLBAR'); ?></td>
			<td><input type="text"  name="SOCIALTOOLBAROPTIONS[font_size]" value="<?php echo $options['font_size']; ?>" size="30" /> <span style="color:#666;"><small><?php _e('Enter Font Size eg. 12px ','WPSOCIALTOOLBAR'); ?></small></span></td>
			</tr>
			<tr>
			<td><?php _e('Font Color: ','WPSOCIALTOOLBAR'); ?></td>
			<td><input type="text" id="WPSocialfontcolor" class="DDST_colorpicker" name="SOCIALTOOLBAROPTIONS[font_color]" value="<?php echo $options['font_color']; ?>" size="30" /></td>
			</tr>
			<tr>
			<td><?php _e('Link Color: ','WPSOCIALTOOLBAR'); ?></td>
			<td><input type="text" class="DDST_colorpicker" id="WPSociallinkcolor" name="SOCIALTOOLBAROPTIONS[link_color]" value="<?php echo $options['link_color']; ?>" size="30" /></td>
			</tr>
			<tr>
			<td><?php _e('Show Timestamp : ','WPSOCIALTOOLBAR'); ?></td>
			<td>
			<select name="SOCIALTOOLBAROPTIONS[twitter_timestamp]" class="social_basic" style="width:120px;">
			<option value="false"><?php _e('No','WPSOCIALTOOLBAR'); ?></option>
			<option value="false"><?php _e('Yes','WPSOCIALTOOLBAR'); ?></option>			
			</select> <span style="color:#666;"><small><?php _e('Select True if you want to display twitter timestamp. ','WPSOCIALTOOLBAR'); ?></small><small class="wpst_pro_only"><?php echo DDST_PRO_ONLY_TEXT; ?></small></span>
			</td>
			</tr>
			<tr>
			<td colspan="2">
			<span style="color:#666;"><small><?php _e('*Please Note: Recent Tweet feed pulls from the Twitter ID used on the "Social Profiles" settings page.','WPSOCIALTOOLBAR'); ?></small></span>

			</td>
			</tr>
			<tr><td colspan="2">
			<input type="hidden" name="SOCIALTOOLBAROPTIONS[update]" value="UPDATED" />
            <input type="submit" class="button-primary" value="<?php _e('Save Settings','WPSOCIALTOOLBAR') ?>" />
			</td></tr>
		</table>
		<br />
		<!-- Recent Tweet Settings Ends -->

		<!-- Display Settings Starts -->
		<table cellspacing="0" cellpadding="0" class="widefat" width="400">
			<thead>
			<tr>
			<th scope="col"><?php _e('WP Social Toolbar Display Settings','WPSOCIALTOOLBAR'); ?>
			</th>
			</tr>
			</thead>
			
			<tr>
			<td><?php _e('Display Throughout Entire Website: ','WPSOCIALTOOLBAR'); ?>

			<select name="SOCIALTOOLBAROPTIONS[whole_website]" id="wpst_whole_website" style="width:100px;"><option value="true" <?php selected('true', $options['whole_website']); ?>>Yes</option><option value="false" <?php selected('false', $options['whole_website']); ?>>No</option></select>			
			</td>
			</tr>

			<tr>
			<td width="100%">
			<br />
			<table cellspacing="0" cellpadding="0" border="0" border="0" class="widefat" id="wpst_page_options">
				<tr>
					<td width="25%">
								<label><?php _e('Display On Home Page: ','WPSOCIALTOOLBAR'); ?></label>
								<?php 
									if(isset($options['home_page']))			
									{
										$checked1="checked";
									}
									else
									{
										$checked1="";
									}
								?>
								<input type="checkbox" name="SOCIALTOOLBAROPTIONS[home_page]" value="<?php echo $options['home_page']; ?>" size="30" <?php echo $checked1; ?> />
					</td>
					<td width="35%">
								<label><?php _e('Display On Category Archive Pages: ','WPSOCIALTOOLBAR'); ?></label>
																<?php 
									if(isset($options['category_archive']))			
									{
										$checked2="checked";
									}
									else
									{
										$checked2="";
									}
								?>
								<input type="checkbox" name="SOCIALTOOLBAROPTIONS[category_archive]" value="<?php echo $options['category_archive']; ?>" size="30" <?php echo $checked2; ?> />
					</td>
					<td width="40%">								
							<label><?php _e('Display On Blog And Single Post Pages: ','WPSOCIALTOOLBAR'); ?></label>
																<?php 
									if(isset($options['blog_single_post']))			
									{
										$checked3="checked";
									}
									else
									{
										$checked3="";
									}
								?>
								<input type="checkbox" name="SOCIALTOOLBAROPTIONS[blog_single_post]" value="<?php echo $options['blog_single_post']; ?>" size="30" <?php echo $checked3; ?> />							
					</td>
				</tr>
				<tr>
					<td colspan="3"><?php _e('Display On Specific Pages','WPSOCIALTOOLBAR'); ?> <input type="text" name="SOCIALTOOLBAROPTIONS[specific_pages]" value="<?php echo $options['specific_pages']; ?>" size="60" /><br /><small><?php _e('(Enter (,) comma separated page ids.)','WPSOCIALTOOLBAR'); ?></small></td>
				</tr>
				<tr>
				<td colspan="3"><?php _e('Exclude Specific Pages','WPSOCIALTOOLBAR'); ?> <input type="text" name="SOCIALTOOLBAROPTIONS[exclude_pages]" value="<?php echo $options['exclude_pages']; ?>" size="60" /><br /><small><?php _e('(Enter (,) comma separated page ids.)','WPSOCIALTOOLBAR'); ?></small></td>
				</tr>
			</table>
			<br />
			</td>
			</tr>
			
			<tr><td>
			<input type="hidden" name="SOCIALTOOLBAROPTIONS[update]" value="UPDATED" />
            <input type="submit" class="button-primary" value="<?php _e('Save Settings','WPSOCIALTOOLBAR') ?>" />
			</td></tr>
		</table>	
		<br />
		<!-- Display Settings Ends -->

		
		<!-- Share Settings Starts -->
		<table cellspacing="0" cellpadding="0" class="widefat" width="400">
			<thead>
			<tr>
			<th scope="col"><?php _e('Share Settings','WPSOCIALTOOLBAR'); ?>
			</th>
			</tr>
			</thead>
			<tr>
			<td><?php _e('Share Home Page: ','WPSOCIALTOOLBAR'); ?>

			<select name="SOCIALTOOLBAROPTIONS[share_home]" style="width:150px;" ><option value="true" <?php selected('true', $options['share_home']); ?>><?php _e('Home','WPSOCIALTOOLBAR'); ?></option><option  value="false" <?php selected('false', $options['share_home']); ?>><?php _e('Individual Page / Post','WPSOCIALTOOLBAR'); ?></option></select>
			<br />
			<span style="color:#666;"><small><?php _e('Choose the page URL you want the Tweet , FB Like button to share.','WPSOCIALTOOLBAR'); ?></small></span>
			
			</td>
			</tr>
			<tr><td>
			<input type="hidden" name="SOCIALTOOLBAROPTIONS[update]" value="UPDATED" />
            <input type="submit" class="button-primary" value="<?php _e('Save Settings','WPSOCIALTOOLBAR') ?>" />
			</td></tr>
		</table>
		<br />
		<!-- Share Settings Ends -->
		<!-- Google Plus Settings Ends -->
				<!-- Google Plus Settings Starts -->
		<table cellspacing="0" cellpadding="0" class="widefat social_basic" width="400">
			<thead>
			<tr>
			<th scope="col"><?php _e('Google +1 Settings','WPSOCIALTOOLBAR'); ?>
			</th>
			</tr>
			</thead>
			<tr>
			<td><?php _e('Display Google +1 Button: ','WPSOCIALTOOLBAR'); ?>

			<select name="SOCIALTOOLBAROPTIONS[google_plus_one]" style="width:150px;"  class="social_basic">
			<option value="false"><?php _e('No','WPSOCIALTOOLBAR'); ?></option>
			<option value="false"><?php _e('Yes','WPSOCIALTOOLBAR'); ?></option>			
			</select><small class="wpst_pro_only"><?php echo DDST_PRO_ONLY_TEXT; ?></small>
			<br />
			<span style="color:#666;"><small><?php _e('Select Yes if you want to add Google +1 button to share.','WPSOCIALTOOLBAR'); ?></small></span>			
			</td>
			</tr>
			<tr><td>
			<input type="hidden" name="SOCIALTOOLBAROPTIONS[update]" value="UPDATED" />
            <input type="submit" class="button-primary social_basic" value="<?php _e('Save Settings','WPSOCIALTOOLBAR') ?>" />
			</td></tr>
		</table>
		<br />
		<!-- Google Plus Settings Ends -->
		<!-- Pinterest Settings Starts -->
		<table cellspacing="0" cellpadding="0" class="widefat social_basic" width="400">
			<thead>
			<tr>
			<th scope="col"><?php _e('Pinterest Settings','WPSOCIALTOOLBAR'); ?>
			</th>
			</tr>
			</thead>
			<tr>
			<td><?php _e('Display Pin it Button: ','WPSOCIALTOOLBAR'); ?>

			<select name="SOCIALTOOLBAROPTIONS[pinterest_button]" style="width:150px;"  class="social_basic">
			<option value="false"><?php _e('No','WPSOCIALTOOLBAR'); ?></option>
			<option value="false"><?php _e('Yes','WPSOCIALTOOLBAR'); ?></option>			
			</select><small class="wpst_pro_only"><?php echo DDST_PRO_ONLY_TEXT; ?></small>
			<br />
			<span style="color:#666;"><small><?php _e('Select Yes if you want to add Pin it button to share.','WPSOCIALTOOLBAR'); ?></small></span>			
			</td>
			</tr>
			<tr><td>
			<input type="hidden" name="SOCIALTOOLBAROPTIONS[update]" value="UPDATED" />
            <input type="submit" class="button-primary social_basic" value="<?php _e('Save Settings','WPSOCIALTOOLBAR') ?>" />
			</td></tr>
		</table>
		<br />
		<!-- Pinterest Plus Settings Ends -->

		

		<!-- Fanpage Settings Starts -->
		<table cellspacing="0" cellpadding="0" class="widefat social_basic" width="400">
			<thead>
			<tr>
			<th scope="col"><?php _e('Facebook Share Settings','WPSOCIALTOOLBAR'); ?>
			</th>
			</tr>
			</thead>
			<tr>
			<td><?php _e('Want to Share Fanpage ? : ','WPSOCIALTOOLBAR'); ?>
			<select name="SOCIALTOOLBAROPTIONS[facebook_setting]" style="width:150px;" id="ddst_facebook_option_select" class="social_basic" >
			<option value="false"><?php _e('No','WPSOCIALTOOLBAR'); ?></option>
			<option value="false"><?php _e('Yes','WPSOCIALTOOLBAR'); ?></option></select> <small class="wpst_pro_only"><?php echo DDST_PRO_ONLY_TEXT; ?></small>
			</td>
			</tr>
			<tr><td>
			<input type="hidden" name="SOCIALTOOLBAROPTIONS[update]" value="UPDATED" />
            <input type="submit" class="button-primary social_basic" value="<?php _e('Save Settings','WPSOCIALTOOLBAR') ?>" />
			</td></tr>
			</table>
		<!-- Fanpage Settings Ends -->
		<br />
		
		<table cellspacing="0" cellpadding="0" class="widefat" width="400">
			<thead>
			<tr>
			<th scope="col"><?php _e('Social Toolbar Credit Settings','WPSOCIALTOOLBAR'); ?>
			</th>
			</tr>
			</thead>
			<tr>
			<td><?php _e('Display Social Toolbar Logo & Link In Social Toolbar: ','WPSOCIALTOOLBAR'); ?>

			<select name="SOCIALTOOLBAROPTIONS[credit_logo]" style="width:150px;">
			<option value="true" <?php selected('true', $options['credit_logo']); ?>><?php _e('Yes','WPSOCIALTOOLBAR'); ?></option>
			<option value="false" <?php selected('false', $options['credit_logo']); ?>><?php _e('No','WPSOCIALTOOLBAR'); ?></option>
			</select>
			<br />
			<span style="color:#666;"><small><?php _e('Select Yes if you want to add Social Toolbar Logo in Toolbar.','WPSOCIALTOOLBAR'); ?></small></span>			
			</td>
			</tr>
			<tr><td>
			<input type="hidden" name="SOCIALTOOLBAROPTIONS[update]" value="UPDATED" />
            <input type="submit" class="button-primary" value="<?php _e('Save Settings','WPSOCIALTOOLBAR') ?>" />
			</td></tr>
		</table>
		<br />
		
		</td>
		<td width="30%" valign="top">
			<?php include_once dirname(__FILE__).'/our_feeds.php'; ?>
		</td>
		</tr>
	</table>
	<?php DDST_admin_footer_code();?>
</div>