<?php
/**
 * Header Template
 *
 * The header template is generally used on every page of your site. Nearly all other templates call it 
 * somewhere near the top of the file. It is used mostly as an opening wrapper, which is closed with the 
 * footer.php file. It also executes key functions needed by the theme, child themes, and plugins. 
 *
 * @package Fanwood
 * @subpackage Template
 */
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<title><?php hybrid_document_title(); ?></title>
	<link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>" type="text/css" media="all" />
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<?php wp_head(); // wp_head ?>
</head>

<body class="<?php hybrid_body_class(); ?>">

	<?php do_atomic( 'open_body' ); // fanwood_open_body ?>
	
	<?php get_template_part( 'menu', 'primary' ); // Loads the menu-primary.php template. ?>
	
	<?php do_atomic( 'after_menu_primary' ); // fanwood_before_header ?>

	<div id="container"><div class="container-wrap">

		<?php do_atomic( 'before_header' ); // fanwood_before_header ?>

		<div id="header">

			<?php do_atomic( 'open_header' ); // fanwood_open_header ?>

			<div class="wrap">

				<div id="branding">
					<?php hybrid_site_title(); ?>
					<?php hybrid_site_description(); ?>
				</div><!-- #branding -->
				
				<?php if ( is_active_sidebar( 'header' ) ) : ?>
				
					<?php get_sidebar( 'header' ); // Loads the sidebar-header.php template. ?>
					
				<?php else : ?>
				
					<?php if ( has_nav_menu( 'header-horizontal' ) ) : ?>
					
						<?php get_template_part( 'menu', 'header-horizontal' ); // Loads the menu-header-horizontal.php template. ?>
					
					<?php else : ?>
					
						<?php get_template_part( 'menu', 'header-primary' ); // Loads the menu-header-primary.php template. ?>
					
						<?php get_template_part( 'menu', 'header-secondary' ); // Loads the menu-header-secondary.php template. ?>
						
					<?php endif; ?>
					
				<?php endif; ?>

				<?php do_atomic( 'header' ); // fanwood_header ?>

			</div><!-- .wrap -->

			<?php do_atomic( 'close_header' ); // fanwood_close_header ?>

		</div><!-- #header -->

		<?php do_atomic( 'after_header' ); // fanwood_after_header ?>
		
		<?php get_template_part( 'menu', 'secondary' ); // Loads the menu-secondary.php template. ?>
		
		<?php get_sidebar( 'after-header' ); // Loads the sidebar-after-header.php template. ?>
		<?php get_sidebar( 'after-header-2c' ); // Loads the sidebar-after-header-2c.php template. ?>
		<?php get_sidebar( 'after-header-3c' ); // Loads the sidebar-after-header-3c.php template. ?>
		<?php get_sidebar( 'after-header-4c' ); // Loads the sidebar-after-header-4c.php template. ?>
		<?php get_sidebar( 'after-header-5c' ); // Loads the sidebar-after-header-5c.php template. ?>

		<?php do_atomic( 'before_main' ); // fanwood_before_main ?>

		<div id="main">

			<div class="wrap">

			<?php do_atomic( 'open_main' ); // fanwood_open_main ?>