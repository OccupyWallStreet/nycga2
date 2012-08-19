<?php
$location = $options_page; // Form Action URI
?>

<div class="wrap">
	<h2>Featured Content Slider Configuration</h2>
	<p>Use these field below to adjust the settings for the Slider. You can choose which Post/Page to show in the Slider while editing the Post/Page (Set "Feature in Featured Content Slider and then save).</p>
	
    <div style="margin-left:0px;">
    <form method="post" action=""><?php //wp_nonce_field('update-options'); ?>
        
        <div class="inside">
		<table class="form-table">
			<tr>
				<th><label for="effect">Choose an Effect</label></th>
				<td>
					<select name="effect">
						<option value="fade" <?php if(get_option('effect') == "fade") {echo "selected=selected";} ?>>Fade</option>
						<option value="scrollLeft" <?php if(get_option('effect') == "scrollLeft") {echo "selected=selected";} ?>>Scroll Left</option>
						<option value="scrollRight" <?php if(get_option('effect') == "scrollRight") {echo "selected=selected";} ?>>Scroll Right</option>
						<option value="shuffle" <?php if(get_option('effect') == "shuffle") {echo "selected=selected";} ?>>Shuffle</option>
					</select>
				</td>
			</tr>
			<tr>
				<th><label for="timeout">Set Slider Timeout (in ms)</label></th>
				<td><input type="text" name="timeout" value="<?php $timeout = get_option('timeout'); if(!empty($timeout)) {echo $timeout;} else {echo "3000";}?>"></td>
			</tr>
			<tr>
				<th><label for="feat_width">Set Slider Width</label></th>
				<td><input type="text" name="feat_width" value="<?php $width = get_option('feat_width'); if(!empty($width)) {echo $width;} else {echo "860";}?>"></td>
			</tr>
			<tr>
				<th><label for="feat_height">Set Slider Height</label></th>
				<td><input type="text" name="feat_height" value="<?php $height = get_option('feat_height'); if(!empty($height)) {echo $height;} else {echo "210";}?>"></td>
			</tr>
			<tr>
				<th><label for="feat_bg">Set BG Color (hexadecimal)</label></th>
				<td><input type="text" name="feat_bg" value="<?php $bg = get_option('feat_bg'); if(!empty($bg)) {echo $bg;} else {echo "FFF";}?>"></td>
			</tr>
			<tr>
				<th><label for="feat_border">Set Slider Border (hexadecimal)</label></th>
				<td><input type="text" name="feat_border" value="<?php $border = get_option('feat_border'); if(!empty($border)) {echo $border;} else {echo "CCC";}?>"></td>
			</tr>
			<tr>
				<th><label for="text_width">Set Text Width</label></th>
				<td><input type="text" name="text_width" value="<?php $text_width = get_option('text_width'); if(!empty($text_width)) {echo $text_width;} else {echo "450";}?>"></td>
			</tr>
			<tr>
				<th><label for="text_color">Set Text Color</label></th>
				<td><input type="text" name="text_color" value="<?php $text_color = get_option('text_color'); if(!empty($text_color)) {echo $text_color;} else {echo "333";}?>"></td>
			</tr>
			<tr>
				<th><label for="img_width">Set Image Width</label></th>
				<td><input type="text" name="img_width" value="<?php $img_width = get_option('img_width'); if(!empty($img_width)) {echo $img_width;} else {echo "320";}?>"></td>
			</tr>
			<tr>
				<th><label for="img_height">Set Image Height</label></th>
				<td><input type="text" name="img_height" value="<?php $img_height = get_option('img_height'); if(!empty($img_height)) {echo $img_height;} else {echo "200";}?>"></td>
			</tr>
		</table>
	</div>
	
        <input type="hidden" name="action" value="update" />
       <?php // <input type="hidden" name="page_options" value="feat_width, feat_height, effect, timeout, feat_width, feat_height, feat_bg, feat_border, text_width, text_color, img_width, img_height" /> ?>
		<p class="submit"><input type="submit" name="Submit" value="<?php _e('Update') ?>" /></p>
	</form>      
</div>