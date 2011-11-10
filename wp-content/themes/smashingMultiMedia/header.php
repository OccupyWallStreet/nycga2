<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

	<head profile="http://gmpg.org/xfn/11">
		
		<title>
			<?php
			if ( is_single() ) { single_post_title(); }       
			elseif ( is_home() || is_front_page() ) { bloginfo('name'); print ' | '; bloginfo('description'); get_page_number(); }
			elseif ( is_page() ) { single_post_title(''); }
			elseif ( is_search() ) { bloginfo('name'); print ' | Search results for ' . wp_specialchars($s); get_page_number(); }
			elseif ( is_404() ) { bloginfo('name'); print ' | Not Found'; }
			else { bloginfo('name'); wp_title('|'); get_page_number(); }
			?>
		</title>

	    <meta http-equiv="content-type" content="<?php bloginfo('html_type') ?>; charset=<?php bloginfo('charset') ?>" />
		<meta name="description" content="<?php bloginfo('description') ?>" />
		<?php if(is_search()) { ?>
		<meta name="robots" content="noindex, nofollow" /> 
	    <?php }?>
		<?php if(is_archive()){ ?><meta name="robots" content="noindex" /><?php } ?>
	
		<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('stylesheet_url'); ?>" />
		<link rel="stylesheet" type="text/css" media="print" href="<?php bloginfo('template_url'); ?>/css/print.css" />
		
		<?php
			if ( is_singular() ) wp_enqueue_script( 'comment-reply' );
			wp_head(); 
		
			global $options;
			foreach ($options as $value) {
				 if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
			}
		?>
		
		<link rel="alternate" type="application/rss+xml" href="<?php bloginfo('rss2_url'); ?>" title="<?php printf( __( '%s latest posts', 'smashingMultiMedia' ), wp_specialchars( get_bloginfo('name'), 1 ) ); ?>" />
        <link rel="alternate" type="application/rss+xml" href="<?php bloginfo('comments_rss2_url') ?>" title="<?php printf( __( '%s latest comments', 'smashingMultiMedia' ), wp_specialchars( get_bloginfo('name'), 1 ) ); ?>" />
        <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" /> 
		
		<!--[if lt IE 7]>
			<script src="http://ie7-js.googlecode.com/svn/version/2.0(beta3)/IE7.js" type="text/javascript"></script>
		<![endif]-->
	</head>
	
	<body <?php body_class(); ?>>
		
	<div id="pg_wrap">
		<div id="header" class="clearfix noprint">
			<div class="container clearfix">
				<?php 
					$sortColumn 	= get_option('wps_pgNavi_sortOption');
					$include		= get_option('wps_pgNavi_inclOption');
					$exclude		= get_option('wps_pgNavi_exclOption');
					$showHome		= get_option('wps_pgNavi_homeOption');
					$pageMenuArg = array(
						'include'    	=>$include,
						'exclude'		=>$exclude,
						'show_home'		=>$showHome, 
						'sort_column'	=>$sortColumn,
						'depth'			=>1,
						'menu_class'	=>'main_navi clearfix'
					);
						
					wp_page_menu($pageMenuArg);
				?>
			
				
				<h1><a href="<?php bloginfo( 'url' ); ?>/" title="<?php bloginfo( 'name' ); ?>" rel="home"><?php bloginfo('name'); bloginfo( 'description' ); ?></a></h1>
				
				<?php include (TEMPLATEPATH . '/headerSearchform.php'); ?>
			</div><!-- container -->
		</div><!-- header-->	