<?php
$location = $options_page; // Form Action URI
?>

<div class="wrap">
	<h2>WP Content Slideshow Configurations</h2>
	<p>Adjust Design and Layout of WP-Content Slideshow. You can easily set a Post or a Page for the Slideshow by hitting "Feature in WP Content Slideshow" on Edit Page/Post.</p>
	
	<div style="margin-left:0px; float: left; width: 400px;">
		<form method="post" action="options.php"><?php wp_nonce_field('update-options'); ?>
			<div class="inside">
				<table class="form-table">
					<tr>
						<th><label for="content_sort">Choose Sorting of Posts/Pages</label></th>
						<td>
							<select name="content_sort">
								<option value="post_date" <?php if(get_option('content_sort') == "post_date") {echo "selected=selected";} ?>>Date</option>
								<option value="title" <?php if(get_option('content_sort') == "title") {echo "selected=selected";} ?>>Title</option>
								<option value="rand" <?php if(get_option('content_sort') == "rand") {echo "selected=selected";} ?>>Random</option>
							</select>
						</td>
					</tr>
					<tr>
						<th><label for="content_order">Choose Order of Posts/Pages</label></th>
						<td>
							<select name="content_order">
								<option value="ASC" <?php if(get_option('content_order') == "ASC") {echo "selected=selected";} ?>>Ascending</option>
								<option value="DESC" <?php if(get_option('content_order') == "DESC") {echo "selected=selected";} ?>>Descending</option>
							</select>
						</td>
					</tr>
					<tr>
						<th><label for="content_width">Set Slideshow Width</label></th>
						<td><input type="text" name="content_width" value="<?php $width = get_option('content_width'); if(!empty($width)) {echo $width;} else {echo "570";}?>"></td>
					</tr>
					<tr>
						<th><label for="content_height">Set Slideshow Height</label></th>
						<td><input type="text" name="content_height" value="<?php $height = get_option('content_height'); if(!empty($height)) {echo $height;} else {echo "250";}?>"></td>
					</tr>
					<tr>
						<th><label for="content_bg">Set BG Color (hexadecimal)</label></th>
						<td><input type="text" name="content_bg" value="<?php $bg = get_option('content_bg'); if(!empty($bg)) {echo $bg;} else {echo "FFF";}?>"></td>
					</tr>
					<tr>
						<th><label for="content_img_width">Set Image Width</label></th>
						<td><input type="text" name="content_img_width" value="<?php $img_width = get_option('content_img_width'); if(!empty($img_width)) {echo $img_width;} else {echo "300";}?>"></td>
					</tr>
					<tr>
						<th><label for="content_img_height">Set Image Height</label></th>
						<td><input type="text" name="content_img_height" value="<?php $height = get_option('content_height'); if(!empty($height)) {echo $height;} else {echo "250";}?>"></td>
					</tr>
					<tr>
						<th><label for="content_nav_width">Set Navigation Width</label></th>
						<td><input type="text" name="content_nav_width" value="<?php $content_nav_width = get_option('content_nav_width'); if(!empty($content_nav_width)) {echo $content_nav_width;} else {echo "270";}?>"></td>
					</tr>
					<tr>
						<th><label for="content_nav_height">Set Navigation Height</label></th>
						<td><input type="text" name="content_nav_height" value="<?php $content_nav_height = get_option('content_nav_height'); if(!empty($content_nav_height)) {echo $content_nav_height;} else {echo "31";}?>"></td>
					</tr>
					<tr>
						<th><label for="content_nav_bg">Set Navigation Background Color</label></th>
						<td><input type="text" name="content_nav_bg" value="<?php $content_nav_bg = get_option('content_nav_bg'); if(!empty($content_nav_bg)) {echo $content_nav_bg;} else {echo "EEE";}?>"></td>
					</tr>
					<tr>
						<th><label for="content_nav_active_bg">Set Navigation Background Active Color</label></th>
						<td><input type="text" name="content_nav_active_bg" value="<?php $nav_bg_active_color = get_option('content_nav_active_bg'); if(!empty($nav_bg_active_color)) {echo $nav_bg_active_color;} else {echo "CCC";}?>"></td>
					</tr>
					<tr>
						<th><label for="content_nav_color">Set Navigation Color</label></th>
						<td><input type="text" name="content_nav_color" value="<?php $content_nav_color = get_option('content_nav_color'); if(!empty($content_nav_color)) {echo $content_nav_color;} else {echo "333";}?>"></td>
					</tr>
					<tr>
						<th><label for="content_nav_active_color">Set Navigation Hover Color</label></th>
						<td><input type="text" name="content_nav_active_color" value="<?php $nav_color = get_option('content_nav_active_color'); if(!empty($nav_color)) {echo $nav_color;} else {echo "FFF";}?>"></td>
					</tr>
					
				</table>
			</div>
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="page_options" value="content_nav_active_bg, content_sort, content_order, content_width, content_height, content_bg, content_img_width, content_img_height, content_nav_width, content_nav_height, content_nav_bg, content_nav_color, content_nav_active_color" />
			<p class="submit"><input type="submit" name="Submit" value="<?php _e('Update Options') ?>" /></p>
		</form>
	</div>
	<div style="margin-left:0px; float: left; width: 300px;">
		
		<a href="http://www.wp-shopified.com" target="_blank"><img style="border: 2px solid #CCC;" src="http://www.wp-shopified.com/images/banners/shopified_plugin.jpg" /></a>
		
	</div>
</div>