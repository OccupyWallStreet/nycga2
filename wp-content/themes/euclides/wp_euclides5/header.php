<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />

	<title>
		<?php 
			global $page;
			
			$sitename = ci_setting('logotext');
			$site_description = ci_setting('slogan');
			$sep = ci_setting('title_separator');
			$sep = ((!empty($sep)) ? ' '.$sep.' ' : ' | ');
			
			wp_title($sep, true, 'right'); 
			
			echo (!empty($sitename) ? $sitename : get_bloginfo('name'));
			
			if ((is_home() or is_front_page()))
				echo $sep . (!empty($site_description) ? $site_description : get_bloginfo('description'));

			//If in a page, include it in the title, mostly for SEO and bookmarking purposes.
			if ( $paged >= 2 or $page >= 2 )
				echo $sep . sprintf( __( 'Page %s', CI_DOMAIN ), max( $paged, $page ) );
		?>
	</title>


    <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/colors/<?php ci_e_setting('stylesheet')?>.css" type="text/css" media="screen" />

	<?php if(ci_setting('favicon')): ?>
		<link rel="shortcut icon" type="image/x-icon" href="<?php ci_e_setting('favicon'); ?>" />
	<?php endif; ?>

	<?php 
		wp_enqueue_script('jquery'); 
		wp_enqueue_script('jquery-superfish', get_bloginfo('template_url').'/js/superfish.js', array('jquery'), false, true);
		wp_enqueue_script('jquery-cycle-all', get_bloginfo('template_url').'/js/jquery.cycle.all.min.js', array('jquery'), false, true);
	?>
    <!--[if IE 6]>
    	<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/css/ie6.css" media="screen" />
    <![endif]-->
    <!--[if IE 7]><link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/css/ie7.css" media="screen" /><![endif]-->
    <!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->

	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />


	<?php
		/* We add some JavaScript to pages with the comment form
		 * to support sites with threaded comments (when in use).
		 */
		
		if ( is_singular() && get_option( 'thread_comments' ) )
			wp_enqueue_script( 'comment-reply' );
	?>
	
	<?php wp_head(); ?>
</head>

<body>
	<?php do_action('after_open_body_tag'); ?>
<div id="wrap">
	<div id="page">
	
		<header id="header">
			<div id="logo">
				<?php ci_e_logo('', ''); ?>
				<?php ci_e_slogan('<em>', '</em>'); ?>
			</div>
	
			<nav id="nav">
				<?php 
					if(has_nav_menu('ci_main_menu'))
						wp_nav_menu( array(
							'theme_location' 	=> 'ci_main_menu',
							'fallback_cb' 		=> '',
							'container' 		=> '',
							'menu_id' 			=> '',
							'menu_class' 		=> 'main-nav group'
						));
					else
						wp_page_menu();
				?>
			</nav><!-- #nav -->
		</header><!-- #header -->

