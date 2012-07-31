<div class="wrap">
	<h2><?php echo _e('WP PageNavi Style Options','wp-pagenavi-style'); ?></h2>
	<script type="text/javascript" src="<?php echo WP_PAGENAVI_STYLE_PATH;?>js/jquery-1.5.min.js"></script>
	<script type="text/javascript" src="<?php echo WP_PAGENAVI_STYLE_PATH;?>js/colorpicker.js"></script>
	<script type="text/javascript" src="<?php echo WP_PAGENAVI_STYLE_PATH;?>js/script.js"></script>
	<link rel="stylesheet" href="<?php echo WP_PAGENAVI_STYLE_PATH;?>/style/colorpicker.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo WP_PAGENAVI_STYLE_PATH;?>/style/wp-pagenavi-style-admin.css" type="text/css" />
	<?php
	if(isset($_POST['WP_PAGENAVI_STYLE_OPTION']))
	{
		echo '<div class="updated fade" id="message"><p>';
		_e('WP PageNavi Style Options Settings <strong>Updated</strong>');
		echo '</p></div>';
		unset($_POST['update']);
		update_option('WP_PAGENAVI_STYLE_OPTION', $_POST['WP_PAGENAVI_STYLE_OPTION']);
	}
	?>
	<?php if(!function_exists('wp_pagenavi')) : ?>
	 <div style="border-radius:4px;-moz-border-radius:4px;-webkit-border-radius:4px;background:#feb1b1;border:1px solid #fe9090;color:#820101;font-size:12px;font-weight:bold;height:auto;margin:10px 15px 0 0;font-family:arial;overflow:hidden;padding:4px 10px 6px;" id="update_sb">
	 <div style="background:url(<?php echo WP_PAGENAVI_STYLE_PATH;?>images/error.png) no-repeat left;margin:2px 10px 0 0;float:left;line-height:18px;padding-left:22px; padding:10px 10px 10px 30px;">
	 NOTICE:  <a href="http://wordpress.org/extend/plugins/wp-pagenavi/" target="_blank" title="Wp PageNavi Plugin"> Wp-PageNavi</a>  Plugin is not installed on your website.  Please visit the <a style="color:#ca0c01" href="plugin-install.php?tab=search&type=term&s=pagenavi&plugin-search-input=Search+Plugins"> Wp PageNavi </a> and install WP-PageNavi Plugin.
	 </div>
	 </div>
	<?php endif;?>

	<table cellspacing="5" cellpadding="5" border="0" width="100%">
	<tr>
	<td width="70%" valign="top">
	<div class="maxi_left">
	<?php
	$options=get_option('WP_PAGENAVI_STYLE_OPTION');
	$form_url=admin_url().'admin.php?page=wp-pagenavi-style/wp-pagenavi-style.php';
	?>

	<form name="wp_pn_style_options_form" id="wp_pn_style_options_form" method="POST" action="<?php echo $form_url;?>">
		<table cellspacing="5" cellpadding="5" class="widefat" width="400">
			<thead>
			<tr>
			<th scope="col" colspan="2"><?php _e('Style Settings','wp-pagenavi-style'); ?>
			</th>
			</tr>
			</thead>
			<tr>
			<td><?php _e('Select StyleSheet : ','wp-pagenavi-style'); ?></td>
			<td>
			<select name="WP_PAGENAVI_STYLE_OPTION[stylesheet]" id="wp_pn_style_selection" style="width:120px;">
	<option value="template" <?php selected('template', $options['stylesheet']); ?>><?php _e('Existing Styles','WPSOCIALTOOLBAR'); ?></option>
	<option value="custom" <?php selected('custom', $options['stylesheet']); ?>><?php _e('Custom','WPSOCIALTOOLBAR'); ?></option>			

			</select> <span style="color:#666;"><small><?php _e('Select Hide / Show Icon Color','wp-pagenavi-style'); ?></small></span>

			
			</td>
			</tr>
			<tr>
			<td colspan="2" style="border:0px;">
			<div id="wp_pn_custom_template_style">
				<table cellspacing="5" cellpadding="5" style="border:0px;">
						<tr>
			<td style="border:0px;"><?php _e('Select Style From Our Collection : ','wp-pagenavi-style'); ?></td>
			<td style="border:0px;">
			<select name="WP_PAGENAVI_STYLE_OPTION[template]" id="wp_pn_style_select_box" style="width:200px;">
			<?php
	$filePath = WP_PAGENAVI_STYLE_CSS_PATH;/* Enter path to the folder */
	$string="";
	$fileCount=0;
	$dir = opendir($filePath);
	while ($file = readdir($dir)) {
	 
	  if (eregi("\.css",$file)) { /* Look for files with .png extension */
		$flie_name=explode('.',$file);
		echo '<option value="'.$flie_name[0].'"'; ?>
		<?php selected($flie_name[0], $options['template']); ?>>
		<?php _e(strtoupper(str_replace('_', ' ', $flie_name[0])),'wp-pagenavi-style'); ?></option>
		<?php
	  }
	}
	echo $string;
	?></select> <span style="color:#666;"><small><?php _e('Select Hide / Show Icon Color','wp-pagenavi-style'); ?></small></span>
			</td>
			</tr>
			<tr>
				<td colspan="2" style="border:0px;">
				<div id="wp_pn_style_IMG_preview_box">
				<h2><?php _e('Preview ','wp-pagenavi-style'); ?></h2>
				<div id="wp_pn_style_IMG_preview"></div>
				</div>
				</td>
			</tr>
				</table>
				</div>
			</td>
			</tr>
			<tr>
			<td colspan="2">
				<div id="wp_pn_style_custom_style_box">
					<table cellspacing="5" cellpadding="5" border="0" style="border:0px;">
						<tr>
						<td style="border:0px;"><?php _e('Heading Color: ','wp-pagenavi-style'); ?></td>
						<td style="border:0px;"><input type="text" class="wp_pn_color_picker" name="WP_PAGENAVI_STYLE_OPTION[heading_color]" value="<?php echo $options['heading_color']; ?>" size="15" /> <span style="color:#666;"><small><?php _e('Select Heading Color (e.g. Page 1 of 10)','wp-pagenavi-style'); ?></small></span></td>
						</tr>
						<tr>
						<td style="border:0px;"><?php _e('Background Color: ','wp-pagenavi-style'); ?></td>
						<td style="border:0px;"><input type="text" class="wp_pn_color_picker" name="WP_PAGENAVI_STYLE_OPTION[background_color]" value="<?php echo $options['background_color']; ?>" size="15" /> <span style="color:#666;"><small><?php _e('Select Background Color Of link','wp-pagenavi-style'); ?></small></span></td>
						</tr>
						<tr>
						<td style="border:0px;"><?php _e('Active / Current Background Color: ','wp-pagenavi-style'); ?></td>
						<td style="border:0px;"><input type="text" class="wp_pn_color_picker" name="WP_PAGENAVI_STYLE_OPTION[hover_color]" value="<?php echo $options['hover_color']; ?>" size="15" /> <span style="color:#666;"><small><?php _e('Select Background Color Of Active or link mouse hover','wp-pagenavi-style'); ?></small></span></td>
						</tr>
									<tr>
			<td><?php _e('Font Size : ','wp-pagenavi-style'); ?></td>
			<td>
			<select name="WP_PAGENAVI_STYLE_OPTION[font_size]"  style="width:120px;">
			<option value="8px" <?php selected('8px', $options['font_size']); ?>><?php _e('8px','wp-pagenavi-style'); ?></option>
			<option value="9px" <?php selected('9px', $options['font_size']); ?>><?php _e('9px','wp-pagenavi-style'); ?></option>
			<option value="10px" <?php selected('10px', $options['font_size']); ?>><?php _e('10px','wp-pagenavi-style'); ?></option>
			<option value="11px" <?php selected('11px', $options['font_size']); ?>><?php _e('11px','wp-pagenavi-style'); ?></option>
			<option value="12px" <?php selected('12px', $options['font_size']); ?>><?php _e('12px','wp-pagenavi-style'); ?></option>
			<option value="13px" <?php selected('13px', $options['font_size']); ?>><?php _e('13px','wp-pagenavi-style'); ?></option>
			<option value="14px" <?php selected('14px', $options['font_size']); ?>><?php _e('14px','wp-pagenavi-style'); ?></option>
			<option value="15px" <?php selected('15px', $options['font_size']); ?>><?php _e('15px','wp-pagenavi-style'); ?></option>
			<option value="16px" <?php selected('16px', $options['font_size']); ?>><?php _e('16px','wp-pagenavi-style'); ?></option>
			<option value="17px" <?php selected('17px', $options['font_size']); ?>><?php _e('17px','wp-pagenavi-style'); ?></option>
			<option value="18px" <?php selected('18px', $options['font_size']); ?>><?php _e('18px','wp-pagenavi-style'); ?></option>
			<option value="19px" <?php selected('19px', $options['font_size']); ?>><?php _e('19px','wp-pagenavi-style'); ?></option>
			<option value="20px" <?php selected('20px', $options['font_size']); ?>><?php _e('20px','wp-pagenavi-style'); ?></option>
			<option value="21px" <?php selected('21px', $options['font_size']); ?>><?php _e('21px','wp-pagenavi-style'); ?></option>
			<option value="22px" <?php selected('22px', $options['font_size']); ?>><?php _e('22px','wp-pagenavi-style'); ?></option>
			<option value="23px" <?php selected('23px', $options['font_size']); ?>><?php _e('23px','wp-pagenavi-style'); ?></option>
			<option value="24px" <?php selected('24px', $options['font_size']); ?>><?php _e('24px','wp-pagenavi-style'); ?></option>
			<option value="25px" <?php selected('25px', $options['font_size']); ?>><?php _e('25px','wp-pagenavi-style'); ?></option>
			</select> <span style="color:#666;"><small><?php _e('Select Font Size','wp-pagenavi-style'); ?></small></span>
			</td>
			</tr>			
			<tr>
			<td><?php _e('Link Color : ','wp-pagenavi-style'); ?></td>
			<td><input type="text" class="wp_pn_color_picker" name="WP_PAGENAVI_STYLE_OPTION[link_color]" value="<?php echo $options['link_color']; ?>" size="15" /> <span style="color:#666;"><small><?php _e('Select Link Color','wp-pagenavi-style'); ?></small></span></td>
			</tr>
			<tr>
			<td><?php _e('Link Mouse Hover / Active Color : ','wp-pagenavi-style'); ?></td>
			<td><input type="text" class="wp_pn_color_picker" name="WP_PAGENAVI_STYLE_OPTION[link_active_color]" value="<?php echo $options['link_active_color']; ?>" size="15" /> <span style="color:#666;"><small><?php _e('Select Link Mouse Hover / Active Color','wp-pagenavi-style'); ?></small></span></td>
			</tr>
			<tr>
			<td><?php _e('Link Border Color : ','wp-pagenavi-style'); ?></td>
			<td><input type="text" class="wp_pn_color_picker" name="WP_PAGENAVI_STYLE_OPTION[border_color]" value="<?php echo $options['border_color']; ?>" size="15" /> <span style="color:#666;"><small><?php _e('Select Link Border Color','wp-pagenavi-style'); ?></small></span></td>
			</tr>
			<tr>
			<td><?php _e('Link Border Mouse Hover/Active Color : ','wp-pagenavi-style'); ?></td>
			<td><input type="text" class="wp_pn_color_picker" name="WP_PAGENAVI_STYLE_OPTION[border_active_color]" value="<?php echo $options['border_active_color']; ?>" size="15" /> <span style="color:#666;"><small><?php _e('Select Link Border Mouse Hover/Active','wp-pagenavi-style'); ?></small></span></td>
			</tr>

					</table>
				</div>
			</td>
			</tr>
						<tr>
		<td><?php _e('Align Navigation : ','wp-pagenavi-style'); ?></td>
		<td>
		<select name="WP_PAGENAVI_STYLE_OPTION[align]"  style="width:120px;">
		<option value="left" <?php selected('left', $options['align']); ?>><?php _e('left','wp-pagenavi-style'); ?></option>
		<option value="right" <?php selected('right', $options['align']); ?>><?php _e('right','wp-pagenavi-style'); ?></option>
		<option value="center" <?php selected('center', $options['align']); ?>><?php _e('center','wp-pagenavi-style'); ?></option>
		</select> <span style="color:#666;"><small><?php _e('Align navigation using this option.','wp-pagenavi-style'); ?></small></span>
		</td>
	</tr>			
			<tr><td colspan="2">
			<input type="hidden" name="WP_PAGENAVI_STYLE_OPTION[update]" value="UPDATED" />
            <input type="submit" class="button-primary" value="<?php _e('Save Settings','wp-pagenavi-style') ?>" />
			</td></tr>
		</table>
	</td>
	<td width="30%" valign="top">
	<?php include_once dirname(__FILE__).'/our_feeds.php'; ?>
	</td></tr>
	</table>
</div>