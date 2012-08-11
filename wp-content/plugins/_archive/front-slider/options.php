<?php
$location = $options_page; // Form Action URI
?>

<div class="wrap">
	<h2>Front Slider Configuration</h2>
	<h3>General Adjustments</h3>
	<p>Give general Information concerning your Frontpage Slideshow</p>
	
	<div style="margin-left:0px; float: left; width: 400px;">
		<form method="post" action="options.php"><?php wp_nonce_field('update-options'); ?>
			<div class="inside">
				<table class="form-table">
					<tr>
						<th><label for="front_sl_slide_order">Order Content by:</label></th>
						<td>
							<select name="front_sl_slide_order" id="front_sl_slide_order">
								<option value="post_date" <?php if(get_option('front_sl_slide_order') == "post_date") {echo "selected='selected'";}?>>Date</option>
								<option value="title" <?php if(get_option('front_sl_slide_order') == "title") {echo "selected='selected'";}?>>Title</option>
								<option value="rand" <?php if(get_option('front_sl_slide_order') == "rand") {echo "selected='selected'";}?>>Random</option>
							</select>
						</td>
					</tr>
					<tr>
						<th><label for="front_sl_slide_sort">Sort Content:</label></th>
						<td>
							<select name="front_sl_slide_sort" id="front_sl_slide_sort">
								<option value="DESC" <?php if(get_option('front_sl_slide_sort') == "DESC") {echo "selected='selected'";}?>>Descending</option>
								<option value="ASC" <?php if(get_option('front_sl_slide_sort') == "ASC") {echo "selected='selected'";}?>>Ascending</option>
							</select>
						</td>
					</tr>
					<tr>
						<th><label for="timeout">Maximum number of posts/pages:</label></th>
						<td><input type="text" name="front_sl_slide_max" value="<?php $front_sl_slide_max = get_option('front_sl_slide_max'); if(!empty($front_sl_slide_max)) {echo $front_sl_slide_max;} else {echo "10";}?>"></td>
					</tr>
					<tr>
						<th><label for="timeout">Text length (chars):</label></th>
						<td><input type="text" name="front_sl_slide_chars" value="<?php $front_sl_slide_chars = get_option('front_sl_slide_chars'); if(!empty($front_sl_slide_chars)) {echo $front_sl_slide_chars;} else {echo "230";}?>"></td>
					</tr>
					<tr>
						<th><label for="timeout">Specific categories only (seperate by comma):</label></th>
						<td><input type="text" name="front_sl_slide_categories" value="<?php $front_sl_slide_categories = get_option('front_sl_slide_categories'); if(!empty($front_sl_slide_categories)) {echo $front_sl_slide_categories;} else {echo "0";}?>"></td>
					</tr>
					<tr>
						<th><label for="timeout">Big thumb width:</label></th>
						<td><input type="text" name="front_sl_img_width" value="<?php $front_sl_img_width = get_option('front_sl_img_width'); if(!empty($front_sl_img_width)) {echo $front_sl_img_width;} else {echo "250";}?>"></td>
					</tr>
					<tr>
						<th><label for="timeout">Big thumb height:</label></th>
						<td><input type="text" name="front_sl_img_height" value="<?php $front_sl_img_height = get_option('front_sl_img_height'); if(!empty($front_sl_img_height)) {echo $front_sl_img_height;} else {echo "150";}?>"> <p><i>Use "Regenerate Thumbnails" Plugin to apply new width/height.</i></p></td>
					</tr>
					<tr>
						<th><label for="timeout">Background colour:</label></th>
						<td><input type="text" name="front_sl_bg_color" value="<?php $front_sl_bg_color = get_option('front_sl_bg_color'); if(!empty($front_sl_bg_color)) {echo $front_sl_bg_color;} else {echo "EEE";}?>"></td>
					</tr>
					<tr>
						<th><label for="timeout">Border colour:</label></th>
						<td><input type="text" name="front_sl_border_color" value="<?php $front_sl_border_color = get_option('front_sl_border_color'); if(!empty($front_sl_border_color)) {echo $front_sl_border_color;} else {echo "CCC";}?>"></td>
					</tr>
					<tr>
						<th><label for="timeout">Title colour:</label></th>
						<td><input type="text" name="front_sl_title_color" value="<?php $front_sl_title_color = get_option('front_sl_title_color'); if(!empty($front_sl_title_color)) {echo $front_sl_title_color;} else {echo "333";}?>"></td>
					</tr>
					<tr>
						<th><label for="timeout">Text colour:</label></th>
						<td><input type="text" name="front_sl_text_color" value="<?php $front_sl_text_color = get_option('front_sl_text_color'); if(!empty($front_sl_text_color)) {echo $front_sl_text_color;} else {echo "333";}?>"></td>
					</tr>
					<tr>
						<th><label for="timeout">Date colour:</label></th>
						<td><input type="text" name="front_sl_date_color" value="<?php $front_sl_date_color = get_option('front_sl_date_color'); if(!empty($front_sl_date_color)) {echo $front_sl_date_color;} else {echo "8D8D8D";}?>"></td>
					</tr>
					<tr>
						<th><label for="timeout">Active thumb border colour:</label></th>
						<td><input type="text" name="front_sl_active_border" value="<?php $front_sl_active_border = get_option('front_sl_active_border'); if(!empty($front_sl_active_border)) {echo $front_sl_active_border;} else {echo "000";}?>"></td>
					</tr>
					<tr>
						<th><label for="timeout">Start Slideshow automatically:</label></th>
						<td>
							<select name="front_sl_slide_auto" id="front_sl_slide_auto">
								<option value="true" <?php if(get_option('front_sl_slide_auto') == "true") {echo "selected='selected'";}?>>Yes</option>
								<option value="false" <?php if(get_option('front_sl_slide_auto') == "false") {echo "selected='selected'";}?>>No</option>
							</select>
						</td>
					</tr>
					<tr>
						<th><label for="timeout">Slideshow speed:</label></th>
						<td><input type="text" name="front_sl_slide_speed" value="<?php $front_sl_slide_speed = get_option('front_sl_slide_speed'); if(!empty($front_sl_slide_speed)) {echo $front_sl_slide_speed;} else {echo "6";}?>"></td>
					</tr>
                                        <tr>
						<th><label for="timeout">Thumb scroll speed:</label></th>
						<td><input type="text" name="front_sl_scroll_speed" value="<?php $front_sl_scroll_speed = get_option('front_sl_scroll_speed'); if(!empty($front_sl_scroll_speed)) {echo $front_sl_scroll_speed;} else {echo "2";}?>"></td>
					</tr>
		  
	   
					<input type="hidden" name="action" value="update" />
					<input type="hidden" name="page_options" value="front_sl_slide_order, front_sl_img_width, front_sl_scroll_speed, front_sl_img_height, front_sl_slide_sort, front_sl_slide_max, front_sl_slide_chars, front_sl_slide_speed, front_sl_slide_auto, front_sl_slide_categories, front_sl_bg_color, front_sl_border_color, front_sl_title_color, front_sl_text_color, front_sl_date_color, front_sl_active_border" />
					<tr>
						<td><p class="submit"><input type="submit" name="Submit" value="<?php _e('Update Options') ?>" /></p></td>
					</tr>
				</table>
			</div>
			
		</form>
	</div>
	<div style="margin-left:0px; float: left; width: 300px;">
		
		<a href="http://www.wp-shopified.com" target="_blank"><img style="border: 2px solid #CCC;" src="http://www.wp-shopified.com/images/banners/shopified_plugin.jpg" /></a>
		
	</div>
</div>