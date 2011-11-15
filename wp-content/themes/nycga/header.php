<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ) ?>; charset=<?php bloginfo( 'charset' ) ?>" />
		<title><?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?></title>
		<meta name="description" content="The official website for the New York General Assembly and the Occupy Wall Street Movement." />
		<?php do_action( 'bp_head' ) ?>

		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ) ?>" />

		<?php
			if ( is_singular() && bp_is_blog_page() && get_option( 'thread_comments' ) )
				wp_enqueue_script( 'comment-reply' );

			wp_head();
		?>
	</head>

	<body <?php body_class() ?> id="bp-default">
		<div id="header-section">
		<?php do_action( 'bp_before_header' ) ?>
		<div id="search-bar" role="search">
		<form action="<?php echo bp_search_form_action() ?>" method="post" id="search-form">
			<label for="search-terms" class="accessibly-hidden"><?php _e( 'Search for:', 'buddypress' ); ?></label>
			<input type="text" id="search-terms" name="search-terms" value="<?php echo isset( $_REQUEST['s'] ) ? esc_attr( $_REQUEST['s'] ) : ''; ?>" />

			<?php echo bp_search_form_type_select() ?>

			<input type="submit" name="search-submit" id="search-submit" value="<?php _e( 'Search', 'buddypress' ) ?>" />

			<?php wp_nonce_field( 'bp_search_form' ) ?>

		</form><!-- #search-form -->

<?php do_action( 'bp_search_login_bar' ) ?>
	</div><!-- #search-bar -->
		<div id="header">
			
				<div class="padder">
					<div id="header-link"><a href="<?php echo home_url(); ?>" title="<?php _ex( 'Home', 'Home page banner link title', 'buddypress' ); ?>">&nbsp;</a>
										</div>
					<h1 id="logo" role="banner"><a href="<?php echo home_url(); ?>" title="<?php _ex( 'Home', 'Home page banner link title', 'buddypress' ); ?>"><?php bp_site_name(); ?></a></h1>
						
				</div><!-- .padder -->
			

			<div id="navigation" role="navigation">
				<?php wp_nav_menu( array( 'container' => false, 'menu_id' => 'nav', 'theme_location' => 'primary', 'fallback_cb' => 'bp_dtheme_main_nav' ) ); ?>
			</div>

			<?php do_action( 'bp_header' ) ?>

		</div><!-- #header -->
		</div> <!-- header-section -->
		<?php do_action( 'bp_after_header' ) ?>
		<?php do_action( 'bp_before_container' ) ?>
		
		<div id="blurb" style="height:170px;">
			<div style="float:left;">
				<iframe width="250" height="170" src="http://www.youtube.com/embed/jRAxu4flK1M?rel=0" frameborder="0" allowfullscreen></iframe>
			</div>
			<div style="margin-left:260px">
				<h3 style="margin-bottom:6px">As of 5:40 PM NYC time, Lilberty Plaza is open again.  The General Assembly has put out the call for everyone in NYC to mobilize and return to the park. </h3>
				<h3></h3>
				<h3 style="margin-bottom:6px;">At 1am NYPD raided the homes of non-violent occupiers in Liberty Plaza using excessive force and destroyed property, including the People's Library. Hundreds of arrests.</h3>
				<h4 style="margin-bottom:6px;">&nbsp;</h4>
				<h4 style="margin-bottom:6px;">For action steps both locally and globally: <a href="http://interoccupy.org/action/"><strong>interoccupy.org/action/</strong></a></h4>
				<p style="margin-bottom:6px;">&nbsp;</p>
              <h4><em>"You can't evict an idea whose time has come"</em></h4>
            </div> 
		</div>
		<? if( ! is_user_logged_in() ){ ?>
			<!-- <div style="float:left;">
				<iframe width="335" height="170" src="https://www.youtube.com/embed/6dtD8RnGaRQ?rel=0" frameborder="0" allowfullscreen></iframe>
			</div>
			<div style="margin-left:355px">
				<h3 style="margin-bottom:6px;">Welcome to the new <em>and improved</em> New York City General Assembly<br/>Currently occupying Zuccotti Park.</h3>
				Read our <a href="/resources/declaration/">Declaration</a><br />
				<a href="/register">Create an Account</a><br />
				Join some <a href="/groups">groups</a><br />
				or <a href="/how-to-help/">find other ways to help</a>
			</div>
		</div> -->
		<? } ?>

		<div id="container">
