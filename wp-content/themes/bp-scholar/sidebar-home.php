<?php include (get_template_directory() . '/options.php'); ?>
<?php if($bp_existed == 'true') : ?>
<?php do_action( 'bp_before_sidebar' ) ?>
<?php endif; ?> 
<?php include (get_template_directory() . '/options.php'); ?>
<div id="sidebar">
		<?php if( $bp_existed == 'true' ) { //check if bp existed ?>
<div class="h4-background"><h4>	<?php _e( 'Search our community', 'bp-scholar' ) ?></h4></div>
	<div class="widget-wrapper">
		<?php if ( bp_search_form_enabled() ) : ?>

			<form action="<?php echo bp_search_form_action() ?>" method="post" id="search-form">
				<input type="text" id="search-terms" name="search-terms" value="" size="24" />
				<?php echo bp_search_form_type_select() ?>

				<input type="submit" name="search-submit" id="search-submit" value="<?php _e( 'Search', 'bp-scholar' ) ?>" />
				<?php wp_nonce_field( 'bp_search_form' ) ?>
			</form><!-- #search-form -->

		<?php endif; ?>
		</div>
		<?php if ( is_user_logged_in() ) { ?>
						<?php if ( function_exists( 'bp_message_get_notices' ) ) : ?>
							<?php bp_message_get_notices(); /* Site wide notices to all users */ ?>
						<?php endif; ?>
		<?php } ?>
							<div class="clear"></div>
	
		<?php } ?>
		<?php 
			$show_followus = get_option('ne_buddyscholar_followuson');
			$url_twitter = get_option('ne_buddyscholar_twitter_url');
			$url_flickr = get_option('ne_buddyscholar_flickr_url');
			$url_facebook = get_option('ne_buddyscholar_facebook_url');
			$url_youtube = get_option('ne_buddyscholar_youtube_url');
		?>
		
		<?php
		if ($show_followus == "yes"){
		?>
		
		<div class="h4-background"><h4>	<?php _e( 'Follow us', 'bp-scholar' ) ?></h4></div>
			<div class="widget-wrapper">
				<?php if ($url_twitter != ""){
					?>
				<div id="url-twitter">
					<a href="<?php echo $url_twitter ?>" title="<?php _e( 'Twitter', 'bp-scholar' ) ?>"><?php _e( 'Follow us on Twitter', 'bp-scholar' ) ?></a>
				</div>	
				<?php } ?>
					<?php if ($url_flickr != ""){
						?>
					<div id="url-flickr">
						<a href="<?php echo $url_flickr ?>" title="<?php _e( 'Flickr', 'bp-scholar' ) ?>"><?php _e( 'Follow us on Flickr', 'bp-scholar' ) ?></a>
					</div>	
					<?php } ?>
						<?php if ($url_facebook != ""){
							?>
						<div id="url-facebook">
							<a href="<?php echo $url_facebook ?>" title="<?php _e( 'Facebook', 'bp-scholar' ) ?>"><?php _e( 'Follow us on Facebook', 'bp-scholar' ) ?></a>
						</div>	
						<?php } ?>
							<?php if ($url_youtube != ""){
								?>
							<div id="url-youtube">
								<a href="<?php echo $url_youtube ?>" title="<?php _e( 'YouTube', 'bp-scholar' ) ?>"><?php _e( 'Follow us on YouTube', 'bp-scholar' ) ?></a>
							</div>	
							<?php } ?>
			</div>
			
		<?php
		}
		?>
		<?php if ( is_active_sidebar( 'home-sidebar' ) ) : ?>
				<?php dynamic_sidebar( 'home-sidebar' ); ?>
		<?php endif; ?>
			<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_inside_after_sidebar' ) ?>
			<?php endif; ?>
</div>
<?php if($bp_existed == 'true') : ?>
<?php do_action( 'bp_after_sidebar' ) ?>
<?php endif; ?>