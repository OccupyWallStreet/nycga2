<?php 

function rfp_admin() {
	global $bp;

	/* If the form has been submitted and the admin referrer checks out, save the settings */
	if ( isset( $_POST['submit'] ) && check_admin_referer('rfp_admin') ) {
		update_option( 'rfp_karma_hide', $_POST['rfp_karma_hide'] );
	
		$karma_levels = array( $_POST['rfp_k1'], $_POST['rfp_k2'], $_POST['rfp_k3'], $_POST['rfp_k4'] ); 
		update_option( 'rfp_karma_levels', maybe_serialize( $karma_levels ) );
		
		update_option( 'rfp_karma_label', $_POST['rfp_karma_label'] );
		update_option( 'rfp_help_text', $_POST['rfp_help_text'] );
		update_option( 'rfp_help_text_closed', $_POST['rfp_help_text_closed'] );
		
		update_option( 'rfp_karma_calc', $_POST['rfp_karma_calc'] );
		
		update_option( 'rfp_superboost', $_POST['rfp_superboost'] );
		update_option( 'rfp_boost', $_POST['rfp_boost'] );
		update_option( 'rfp_diminish', $_POST['rfp_diminish'] );
		update_option( 'rfp_hide', $_POST['rfp_hide'] );
		
		update_option( 'rfp_karma_never_minus', $_POST['rfp_karma_never_minus'] );
				
		$updated = true;
	}

	$rfp_k = maybe_unserialize( get_option( 'rfp_karma_levels' ) );
?>	
	<link rel='stylesheet' href='<?php echo WP_PLUGIN_URL.'/buddypress-karma/css/rating.css' ?>' type='text/css' /> 
	<style type="text/css">.rfp-admin { padding: 8px 40px; margin-left: 30px; border: 1px solid #CCC; } .rfp-admin .rfp-show { display:inline; }</style>

	<div class="wrap">
		<h2>Buddypress Rate Forum Posts</h2>

		<?php if ( isset($updated) ) : ?><div id='message' class='updated fade'><p>Settings Updated</p></div><?php endif; ?>

		<form action="<?php echo site_url() . '/wp-admin/admin.php?page=rfp_admin' ?>" name="rfp-settings-form" id="rfp-settings-form" method="post">

			<h3>Post Ratings</h3>
			
			Helper text that shows to the left of thumbs up and down buttons: <input name="rfp_help_text" type="text" value="<?php echo attribute_escape( get_option( 'rfp_help_text' ) ); ?>" size="30" /><br>
			Text to show for closed topics: <input name="rfp_help_text_closed" type="text" value="<?php echo attribute_escape( get_option( 'rfp_help_text_closed' ) ); ?>" size="30" />
			<br>
			<br>
			
			<h3>User Karma Levels</h3>
			<input type="radio" value="0" name="rfp_karma_hide" <?php if ( !get_option( 'rfp_karma_hide' ) ) echo 'checked="checked"' ?> /> Show &nbsp; 
			<input type="radio" value="1" name="rfp_karma_hide" <?php if ( get_option( 'rfp_karma_hide' ) ) echo 'checked="checked"' ?> /> Hide &nbsp; 
			(if karma is hidden, it is still recorded)
			<p>At each karma level, the user's points color will be highlighted to a more intense yellow. <br>
			Default values are 7, 19, 51, 138. Use lower values if you want people to progress faster. <br>
			Set any level to 0 to disable that level. <br>
			<br>
			Base: &nbsp;&nbsp; <input type="text" value="0" size="5" readonly="1" />  <span class='rfp-karma'>points</span><br>
			Level 1 <input name="rfp_k1" type="text" value="<?php echo attribute_escape( $rfp_k[0] ); ?>" size="5" /> <span class='rfp-karma rfp-k1'>points</span><br>
			Level 2 <input name="rfp_k2" type="text" value="<?php echo attribute_escape( $rfp_k[1] ); ?>" size="5" /> <span class='rfp-karma rfp-k2'>points</span><br>
			Level 3 <input name="rfp_k3" type="text" value="<?php echo attribute_escape( $rfp_k[2] ); ?>" size="5" /> <span class='rfp-karma rfp-k3'>points</span><br>
			Level 4 <input name="rfp_k4" type="text" value="<?php echo attribute_escape( $rfp_k[3] ); ?>" size="5" /> <span class='rfp-karma rfp-k4'>points</span><br>
			<br>
			
			Karma points label for Members page: <input name="rfp_karma_label" type="text" value="<?php echo attribute_escape( get_option( 'rfp_karma_label' ) ); ?>" size="30" />
			<br>
			<br>

			<b>Karma Calculation:</b><br>
			<?php $karma_calc = get_option( 'rfp_karma_calc' ); ?>
			<input type="radio" value="total" name="rfp_karma_calc" <?php if ( !$karma_calc || $karma_calc == 'total' ) echo 'checked="checked"' ?> /> Total Karma Points for quiet sites - this shows the simple total of karma points. 
				example: <span class='rfp-karma rfp-k3'>110</span><br> 
			<input type="radio" value="mixed2" name="rfp_karma_calc" <?php if ( $karma_calc== 'mixed2' ) echo 'checked="checked"' ?> /> Mixed Karma Points PLUS for normal sites - this is a higher value mix of total points and average points per post. example: <span class='rfp-karma rfp-k2'>60</span><br>
			<input type="radio" value="mixed" name="rfp_karma_calc" <?php if ( $karma_calc == 'mixed' ) echo 'checked="checked"' ?> /> Mixed Karma Points for busy sites - this is a lower value mix of total points and average points per post. example: <span class='rfp-karma rfp-k2'>30</span><br>
			<input type="radio" value="average" name="rfp_karma_calc" <?php if ( $karma_calc == 'average' ) echo 'checked="checked"' ?> /> Average Karma Points for busy sites - this shows the average karma points per post. example: <span class='rfp-karma rfp-k1'>9</span><br>
			<br>
			
			<b>Users with Negative Karma:</b><br>
			<input type="radio" value="0" name="rfp_karma_never_minus" <?php if ( !get_option( 'rfp_karma_never_minus' ) ) echo 'checked="checked"' ?> /> Show negative karma &nbsp; 
			<input type="radio" value="1" name="rfp_karma_never_minus" <?php if ( get_option( 'rfp_karma_never_minus' ) ) echo 'checked="checked"' ?> /> Don't show negative karma &nbsp; 			
			<br>
			<br>
			
			<h3>Post Highlighting and Diminishing</h3>
			Based on ratings, individual posts will be highlighted or diminished. <br>
			Diminish and Hide values should always be negative.<br>
			Default values are: superboost: 25, boost: 10, diminish: -3 and hide: -6. <br>
			Set any level to 0 to disable that level. <br>
			<br>
			Super Boost: <input name="rfp_superboost" type="text" value="<?php echo attribute_escape( get_option( 'rfp_superboost' )); ?>" size="5" /> <span class='rfp-admin rfp-superboost'> great post content </span><br>
			<br>
			Boost: <input name="rfp_boost" type="text" value="<?php echo attribute_escape( get_option( 'rfp_boost' )); ?>" size="5" /> <span class='rfp-admin rfp-boost'> good post content </span><br>
			<br>
			Normal: <input type="text" value="0" size="5" readonly="1"/> <span class='rfp-admin'> normal post content </span><br>
			<br>
			Diminish <input name="rfp_diminish" type="text" value="<?php echo attribute_escape( get_option( 'rfp_diminish' )); ?>" size="5" /> <span class='rfp-admin rfp-diminish'> poor post content </span><br>
			<br>
			Hide <input name="rfp_hide" type="text" value="<?php echo attribute_escape( get_option( 'rfp_hide' )); ?>" size="5" /> 
			
			<span class='rfp-admin rfp-hide'> <span class="rfp-admin-content">very bad post content</span></span><br>
			
			<p class="submit"><input type="submit" name="submit" value="Save Settings"/></p>
			
			<p>If you like this plugin <a href="http://wordpress.org/extend/plugins/buddypress-rate-forum-posts/" target="_blank">please rate it</a>. For new features or enhancements contact <a href="mailto:deryk@bluemandala.com">deryk@bluemandala.com</a> and I will provide a quote. 
			<p>If you use this plugin regularly consider making a donation to support continued development. Donate: 
			 <a href="https://www.paypal.com/xclick/business=deryk@bluemandala.com&item_name=BP-Rate-Forum-Posts-Plugin-Donation&amount=3" target="_blank">$3</a>, 
			 <a href="https://www.paypal.com/xclick/business=deryk@bluemandala.com&item_name=BP-Rate-Forum-Posts-Plugin-Donation&amount=7" target="_blank">$7</a>, 
			 <a href="https://www.paypal.com/xclick/business=deryk@bluemandala.com&item_name=BP-Rate-Forum-Posts-Plugin-Donation&amount=15" target="_blank">$15</a>, 
			 <a href="https://www.paypal.com/xclick/business=deryk@bluemandala.com&item_name=BP-Rate-Forum-Posts-Plugin-Donation&amount=30" target="_blank">$30</a> or
			 <a href="https://www.paypal.com/xclick/business=deryk@bluemandala.com&item_name=BP-Rate-Forum-Posts-Plugin-Donation" target="_blank">other amount</a> (will open paypal in a new window)
			 </p>
			 
	

			<?php
			/* This is very important, don't leave it out. */
			wp_nonce_field( 'rfp_admin' );
			?>
		</form>
	</div>
<?php
}

?>