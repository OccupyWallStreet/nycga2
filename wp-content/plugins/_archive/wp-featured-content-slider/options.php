<?php

$c_slider_location = $c_slider_options_page; // Form Action URI

$c_slider_direct_path =  get_bloginfo('wpurl')."/wp-content/plugins/wp-featured-content-slider";

?>

<div class="wrap">
	
	<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=d.nissle%40yahoo%2ede&item_name=Featured%20Content%20Slider&item_number=Support%20Open%20Source&no_shipping=0&no_note=1&tax=0&currency_code=USD&lc=US&bn=PP%2dDonationsBF&charset=UTF%2d8" style="text-decoration: none !important;" target="_blank">
	
	<div class="donate" style="background-color: #EE7679; padding: 10px; border: 1px solid #B93543; color: #671D25 !important; text-decoration: none !important; float: left; width: 350px; margin-top: 15px;" >
		
		<h3 style="margin: 0px; margin-bottom: 5px;">Support WP Featured Content Slider</h3>
		<span class="desc">Help this Plugin to stay alive and <u>donate</u> now!</span>
		
	</div>
	
	</a>
	
	<h2 style="clear: both;">Featured Content Slider Configuration</h2>
	<p>Use these field below to adjust the settings for the Slider. You can choose which Post/Page to show in the Slider while editing the Post/Page (Set "Feature in Featured Content Slider and then save).</p>
	
    <div style="margin-left:0px; float: left; width: 400px;">
	
	<form method="post" action="options.php"><?php wp_nonce_field('update-options'); ?>
	    
	    <div class="inside">
		    <table class="form-table">
			    <tr>
				    <th><label for="sort">Choose Sorting of Posts/Pages</label></th>
				    <td>
					    <select name="sort">
						    <option value="post_date" <?php if(get_option('sort') == "post_date") {echo "selected=selected";} ?>>Date</option>
						    <option value="title" <?php if(get_option('sort') == "title") {echo "selected=selected";} ?>>Title</option>
						    <option value="rand" <?php if(get_option('sort') == "rand") {echo "selected=selected";} ?>>Random</option>
					    </select>
				    </td>
			    </tr>
			    <tr>
				    <th><label for="order">Choose Order of Posts/Pages</label></th>
				    <td>
					    <select name="order">
						    <option value="ASC" <?php if(get_option('order') == "ASC") {echo "selected=selected";} ?>>Ascending</option>
						    <option value="DESC" <?php if(get_option('order') == "DESC") {echo "selected=selected";} ?>>Descending</option>
					    </select>
				    </td>
			    </tr>
			    <tr>
				    <th><label for="effect">Choose an Effect</label></th>
				    <td>
					    <select name="effect">
						    <option value="fade" <?php if(get_option('effect') == "fade") {echo "selected=selected";} ?>>Fade</option>
						    <option value="scrollLeft" <?php if(get_option('effect') == "scrollLeft") {echo "selected=selected";} ?>>Scroll Left</option>
						    <option value="scrollRight" <?php if(get_option('effect') == "scrollRight") {echo "selected=selected";} ?>>Scroll Right</option>
						    <option value="shuffle" <?php if(get_option('effect') == "shuffle") {echo "selected=selected";} ?>>Shuffle</option>
						    <option value="curtainX" <?php if(get_option('effect') == "curtainX") {echo "selected=selected";} ?>>CurtainX</option>
						    <option value="curtainY" <?php if(get_option('effect') == "curtainY") {echo "selected=selected";} ?>>CurtainY</option>
						    <option value="fadeZoom" <?php if(get_option('effect') == "fadeZoom") {echo "selected=selected";} ?>>FadeZoom</option>
						    <option value="scrollUp" <?php if(get_option('effect') == "scrollUp") {echo "selected=selected";} ?>>Scroll Up</option>
						    <option value="scrollDown" <?php if(get_option('effect') == "scrollDown") {echo "selected=selected";} ?>>Scroll Down</option>
						    <option value="toss" <?php if(get_option('effect') == "toss") {echo "selected=selected";} ?>>Toss</option>
						    <option value="wipe" <?php if(get_option('effect') == "wipe") {echo "selected=selected";} ?>>Wipe</option>
						    <option value="uncover" <?php if(get_option('effect') == "uncover") {echo "selected=selected";} ?>>Uncover</option>
					    </select>
				    </td>
			    </tr>
			    <tr>
				    <th><label for="chars">Limit Description (Number of chars)</label></th>
				    <td><input type="text" name="limit" value="<?php $c_slider_limit = get_option('limit'); if(!empty($c_slider_limit)) { echo $c_slider_limit; } else { echo "350"; } ?>"></td>
			    </tr>
			    <tr>
				    <th><label for="points">More Seperator</label></th>
				    <td><input type="text" name="points" value="<?php $c_slider_points = get_option('points'); if(!empty($c_slider_points)) { echo $c_slider_points; } else { echo "..."; } ?>"></td>
			    </tr>
			    <tr>
				    <th><label for="limit_posts">Limit Number of Posts (0 = unlimited)</label></th>
				    <td><input type="text" name="limit_posts" value="<?php $c_slider_limit_posts = get_option('limit_posts'); if(!empty($c_slider_limit_posts)) { echo $c_slider_limit_posts; } else { echo "0"; } ?>"></td>
			    </tr>
			    <tr>
				    <th><label for="timeout">Set Slider Timeout (in ms)</label></th>
				    <td><input type="text" name="timeout" value="<?php $c_slider_timeout = get_option('timeout'); if(!empty($c_slider_timeout)) {echo $c_slider_timeout;} else {echo "3000";}?>"></td>
			    </tr>
			    <tr>
				    <th><label for="feat_width">Set Slider Width</label></th>
				    <td><input type="text" name="feat_width" value="<?php $c_slider_width = get_option('feat_width'); if(!empty($c_slider_width)) {echo $c_slider_width;} else {echo "860";}?>"></td>
			    </tr>
			    <tr>
				    <th><label for="feat_height">Set Slider Height</label></th>
				    <td><input type="text" name="feat_height" value="<?php $c_slider_height = get_option('feat_height'); if(!empty($c_slider_height)) {echo $c_slider_height;} else {echo "210";}?>"></td>
			    </tr>
			    <tr>
				    <th><label for="feat_bg">Set BG Color (hexadecimal)</label></th>
				    <td><input type="text" name="feat_bg" value="<?php $c_slider_bg = get_option('feat_bg'); if(!empty($c_slider_bg)) {echo $c_slider_bg;} else {echo "FFF";}?>"></td>
			    </tr>
			    <tr>
				    <th><label for="feat_border">Set Slider Border (hexadecimal)</label></th>
				    <td><input type="text" name="feat_border" value="<?php $c_slider_border = get_option('feat_border'); if(!empty($c_slider_border)) {echo $c_slider_border;} else {echo "CCC";}?>"></td>
			    </tr>
			    <tr>
				    <th><label for="text_width">Set Text Width</label></th>
				    <td><input type="text" name="text_width" value="<?php $c_slider_text_width = get_option('text_width'); if(!empty($c_slider_text_width)) {echo $c_slider_text_width;} else {echo "450";}?>"></td>
			    </tr>
			    <tr>
				    <th><label for="text_color">Set Text Color</label></th>
				    <td><input type="text" name="text_color" value="<?php $c_slider_text_color = get_option('text_color'); if(!empty($c_slider_text_color)) {echo $c_slider_text_color;} else {echo "333";}?>"></td>
			    </tr>
			    <tr>
				    <th><label for="img_width">Set Image Width</label></th>
				    <td><input type="text" name="img_width" value="<?php $c_slider_img_width = get_option('img_width'); if(!empty($c_slider_img_width)) {echo $c_slider_img_width;} else {echo "320";}?>"></td>
			    </tr>
			    <tr>
				    <th><label for="img_height">Set Image Height</label></th>
				    <td><input type="text" name="img_height" value="<?php $c_slider_img_height = get_option('img_height'); if(!empty($c_slider_img_height)) {echo $c_slider_img_height;} else {echo "200";}?>"></td>
			    </tr>
		    </table>
	    </div>
	    
	    <input type="hidden" name="action" value="update" />
	    <input type="hidden" name="page_options" value="limit_posts, points, limit, feat_width, feat_height, order, sort, effect, timeout, feat_width, feat_height, feat_bg, feat_border, text_width, text_color, img_width, img_height" />
		    <p class="submit"><input type="submit" name="Submit" value="<?php _e('Update Options') ?>" /></p>
	    </form>
	
	</div>
    
	<div style="margin-left:0px; float: left; width: 300px;">
		
		<a href="http://www.wp-shopified.com" target="_blank"><img style="border: 2px solid #CCC;" src="<?php echo $c_slider_direct_path;?>/images/shopified_plugin.jpg" /></a>
		
	</div>
	
</div>