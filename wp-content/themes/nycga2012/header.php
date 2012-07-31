<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ) ?>; charset=<?php bloginfo( 'charset' ) ?>" />
		<title><?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?></title>

		<?php do_action( 'bp_head' ) ?>

		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ) ?>" />

		<?php
			if ( is_singular() && bp_is_blog_page() && get_option( 'thread_comments' ) ) wp_enqueue_script( 'comment-reply' );			
			wp_head();
		?>
		
		<script>
		/*
$(function(){ 
			$("select, input:checkbox, input:radio, input:file").uniform();
		});
*/
		</script>
	</head>

	<body <?php body_class() ?> id="bp-default">
	
	<div class="container_24">

		<?php do_action( 'bp_before_header' ) ?>

				<!--#search-bar-->
				<div id="search-bar">
	
					<!-- #search-form -->
					<form action="<?php echo bp_search_form_action() ?>" method="post" id="search-form" class="uniform">
						<label for="search-terms" class="accessibly-hidden"><?php _e( 'Search for:', 'buddypress' ); ?></label>
						<input type="search" id="search-terms" name="search-terms" value="<?php echo isset( $_REQUEST['s'] ) ? esc_attr( $_REQUEST['s'] ) : ''; ?>" />
						
						<?php echo bp_search_form_type_select() ?>
						
						<input class="button" type="submit" name="search-submit" id="search-submit" value="<?php _e( 'Search', 'buddypress' ) ?>" />
						
						<?php wp_nonce_field( 'bp_search_form' ) ?>
					
					</form><!-- /#search-form -->
					
					<?php do_action( 'bp_search_login_bar' ) ?>
	
	
				</div>
				<!-- /#search-bar-->

		<div id="header">

			<div id="masthead" class="container_24 clearfix">
				<h2 id="donate"><a class="button" href="https://www.wepay.com/xo71ir" class="bigBtn" style="display:block;">Donate!</a></h2>
			
				<div id="header-link"><a href="<?php echo home_url(); ?>" title="<?php _ex( 'Home', 'Home page banner link title', 'buddypress' ); ?>"></a></div>
				<h2 id="description" role="tagline"><a href="<?php echo home_url(); ?>" title="<?php _ex( 'Home', 'Home page banner link title', 'buddypress' ); ?>">#OccupyWallStreet</a></h2>
				<h1 id="logo" role="banner"><a href="<?php echo home_url(); ?>" title="<?php _ex( 'Home', 'Home page banner link title', 'buddypress' ); ?>"><?php bp_site_name(); ?></a></h1>
				<h3 id="description" role="description"><a href="<?php echo home_url(); ?>" title="<?php _ex( 'Home', 'Home page banner link title', 'buddypress' ); ?>"><?php bloginfo('description'); ?></a></h3>

				
				<!--
<h1 id="logo" role="banner" class="grid_13 alpha">
					<a href="<?php echo home_url(); ?>" title="<?php _ex( 'Home', 'Home page banner link title', 'buddypress' ); ?>">
						<span class='ows'><?php echo get_bloginfo ( 'description' );?></span><br/>
						<?php bp_site_name();?>
					</a>					
				</h1>
-->


			</div><!-- #masthead -->
			
			<div id="navigation" role="navigation" class="nav clearfix shadow">
				<div class="container_24">
				<?php wp_nav_menu( array( 'container' => false, 'menu_id' => 'nav', 'theme_location' => 'primary', 'fallback_cb' => 'bp_dtheme_main_nav' ) ); ?>
				</div>
			</div>

			<?php do_action( 'bp_header' ) ?>

		</div><!-- #header -->
		
			<!-- BEGIN: announcement bar -->
	
			<?php// locate_template( array( 'announcement.php' ), true ) ?>
	
			<!-- //END: announcement bar -->


		<?php do_action( 'bp_after_header' ) ?>
		<?php do_action( 'bp_before_container' ) ?>
		
		<div id="container" class="container_24 clearfix">
		