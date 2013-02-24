		<?php include (get_template_directory() . '/library/options/options.php'); ?>
<div id="discoveryBox" class="generic-box">
	<div class="searchBox">
	<?php if ($bp_existed == "true") { ?>
		<form action="<?php echo home_url(); ?>/search" method="post" id="search-form">
			<label><?php _e( 'Find:', 'network' ) ?></label>
			<input type="text" id="search-terms" name="search-terms" value="" />
			<select name="search-which" id="search-which" style="width: auto"><option value="members"><?php _e( 'Members', 'network' ) ?></option><option value="groups"><?php _e( 'Groups', 'network' ) ?></option><option value="forums"><?php _e( 'Forums', 'network' ) ?></option></select>
			<button type="submit" name="search-submit" id="search-submit" value="Search" ><?php _e( 'Search', 'network' ) ?></button>
			<input type="hidden" id="_wpnonce" name="_wpnonce" value="e077bd408a" /><input type="hidden" name="_wp_http_referer" value="/" />	
		</form>
	<?php } else { ?>
		<?php get_search_form(); ?>
	<?php } ?>
	</div> <!-- searchbox -->
	<div class="profileBox">
		<ul>
			<?php 
				if ( !is_user_logged_in() ) {
					 global $user_login ;
				?>
				<?php if ($bp_existed == "true") { ?>
					<li class="logout">
					<form name="login-form" id="loginform" action="<?php echo site_url( 'wp-login.php', 'login_post' ) ?>" method="post">
												<div class="username-panel"><?php _e( 'Username', 'network' ) ?>
												<input type="text" name="log" id="user_login" class="input" value="<?php echo esc_attr(stripslashes($user_login)); ?>" tabindex="97" size="20"/></div>
					
												<div class="password-panel"><?php _e( 'Password', 'network' ) ?>
												<input type="password" name="pwd" id="user_pass" class="input" value="" tabindex="98" size="20"/></div>
					
												<?php do_action( 'bp_sidebar_login_form' ) ?>
												<div class="submit-button"><input type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e('Log In'); ?>" tabindex="100" /></div>
												<input type="hidden" name="testcookie" value="1" />
											</form>
					</li>
				<?php } else { ?>
					<li class="logout">
						<form name="loginform" id="loginform" action="<?php echo get_option('siteurl'); ?>/wp-login.php" method="post">
							<div class="username-panel"><?php _e( 'Username', 'network' ) ?> <input value="Username" class="input" type="text" size="20" tabindex="10" name="log" id="user_login" onfocus="if (this.value == 'Username') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Username';}" /></div> 
        					<div class="password-panel"><?php _e( 'Password', 'network' ) ?> <input value="Password" class="input" type="password" size="20" tabindex="20" name="pwd" id="user_pass" onfocus="if (this.value == 'Password') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Password';}" /></div>
							<div class="submit-button"><input name="wp-submit" id="wp-submit" value="Log In" tabindex="100" type="submit"></div>
							<input name="redirect_to" value="<?php echo get_option('siteurl'); ?>/wp-admin/" type="hidden">
							<input name="testcookie" value="1" type="hidden">
						</form>
					</li>
			
				<?php } ?>
			<?php } else { ?>
				<?php 
					if ($bp_existed == "true") {
						global $bp; 
				?>
					<li><a href="<?php echo bp_loggedin_user_domain(); ?>"><?php bp_loggedin_user_fullname() ?></a></li>
					<li class="logout"><a href="<?php echo wp_logout_url( bp_get_root_domain() ) ?>"><?php _e( 'Log out', 'network' ) ?></a></li>
					<li class="avatar"><a href="<?php echo bp_loggedin_user_domain(); ?>"><?php echo bp_core_fetch_avatar( 'item_id='.$bp->loggedin_user->id ); ?></a></li>
				<?php } else { ?>
				
				   <?php 
				   	
				   		global $current_user;
				        get_currentuserinfo();

				   ?>
				   <?php if ($current_user->user_firstname != '') { ?>
					   <li><?php echo $current_user->user_firstname . " " . $current_user->user_lastname; ?></li>
				   <?php } else { ?>
					   <li><a href="<?php echo wp_login_url( ) ?>"><?php echo $current_user->user_nicename; ?></a></li>
				   <?php } ?>
				   <li class="logout"><a href="<?php echo wp_logout_url(); ?>"><?php _e( 'Log out', 'network' ) ?></a></li>
				   <li class="avatar"><?php echo get_avatar($current_user->user_email, '50'); ?></li>
				<?php } ?>
			<?php } ?>
		</ul>
	</div> <!-- profilebox -->
	<div class="clear"></div>
</div>