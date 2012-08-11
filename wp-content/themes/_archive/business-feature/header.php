<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
 		<?php include (get_template_directory() . '/library/options/options.php'); ?>
	<title><?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?></title>
	<?php if($bp_existed == 'true') : ?>
	<?php do_action( 'bp_head' ) ?>	
	<?php endif; ?>
			<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
			<?php wp_head(); ?>
	</head>
	<body <?php body_class() ?>>
		<div id="site-wrapper">
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_before_header' ) ?>
		<?php endif; ?>
		<?php locate_template( array( '/library/components/branding-header.php' ), true ); ?>
		<?php if($bp_existed == 'true') : ?>
		<?php locate_template( array( '/library/components/buddypress/buddypress-panel.php' ), true ); ?>
		<?php endif; ?>
		</div>
		</div>
		</div>
		</div>
		</div>
		</div>
		</div>
				<?php if( $bp_existed == 'true' ) { //check if bp existed ?>
					<?php locate_template( array( '/library/components/buddypress/buddypress-navigation.php' ), true ); ?>
				<?php } else { // if not bp detected..let go normal ?>
					<?php locate_template( array( '/library/components/navigation.php' ), true ); ?>
				<?php } ?>
				
				<?php if ( is_front_page() ) : ?>
						<?php
							locate_template( array( '/library/components/featured-header.php' ), true );	
						?>
				<?php endif; ?>
				<div id="wrapper">
			<?php if($bp_existed == 'true') : ?>
				<?php do_action( 'bp_header' ) ?>
			<?php endif; ?>
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_after_header' ) ?>
			<?php do_action( 'bp_before_container' ) ?>
		<?php endif; ?>
		<div id="container">
			<div id="head-content">