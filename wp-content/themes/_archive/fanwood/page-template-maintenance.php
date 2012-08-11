<?php
/**
 * Template Name: Maintenance
 *
 * This template uses it's own header and footer. Also, everything other than the main content is deactivated.
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

	<div id="container"><div class="container-wrap">

		<?php do_atomic( 'before_header' ); // fanwood_before_header ?>

		<div id="header">

			<?php do_atomic( 'open_header' ); // fanwood_open_header ?>

			<div class="wrap">

				<div id="branding">
					<?php hybrid_site_title(); ?>
					<?php hybrid_site_description(); ?>
				</div><!-- #branding -->

				<?php do_atomic( 'header' ); // fanwood_header ?>

			</div><!-- .wrap -->

			<?php do_atomic( 'close_header' ); // fanwood_close_header ?>

		</div><!-- #header -->

		<?php do_atomic( 'after_header' ); // fanwood_after_header ?>

		<?php do_atomic( 'before_main' ); // fanwood_before_main ?>

		<div id="main">

			<div class="wrap">

			<?php do_atomic( 'open_main' ); // fanwood_open_main ?>

				<?php do_atomic( 'before_content' ); // fanwood_before_content ?>

				<div id="content" class="multiple">

					<?php do_atomic( 'open_content' ); // fanwood_open_content ?>

					<div class="hfeed">
					
					<?php if ( have_posts() ) : ?>

						<?php while ( have_posts() ) : the_post(); ?>

							<?php do_atomic( 'before_entry' ); // fanwood_before_entry ?>

							<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">

								<?php do_atomic( 'open_entry' ); // fanwood_open_entry ?>

								<div class="entry-content">
									<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'fanwood' ) ); ?>
									<?php wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', 'fanwood' ), 'after' => '</p>' ) ); ?>
									
									<?php echo apply_atomic_shortcode( 'entry_edit_link', '[entry-edit-link before="<p>" after="</p>"]' ); ?>
								</div><!-- .entry-content -->

								<?php do_atomic( 'close_entry' ); // fanwood_close_entry ?>

							</div><!-- .hentry -->

							<?php do_atomic( 'after_entry' ); // fanwood_after_entry ?>

						<?php endwhile; ?>

					<?php endif; ?>

					</div><!-- .hfeed -->

					<?php do_atomic( 'close_content' ); // fanwood_close_content ?>
					
				</div><!-- #content -->

				<?php do_atomic( 'after_content' ); // fanwood_after_content ?>

				<?php do_atomic( 'close_main' ); // fanwood_close_main ?>

			</div><!-- .wrap -->

		</div><!-- #main -->

		<?php do_atomic( 'after_main' ); // fanwood_after_main ?>
		
		<?php do_atomic( 'before_footer' ); // fanwood_before_footer ?>

		<div id="footer">

			<?php do_atomic( 'open_footer' ); // fanwood_open_footer ?>

			<div class="wrap">
				
				<?php
				
					$footer_insert = hybrid_get_setting( 'fanwood_footer_insert' );
					
					if ( isset( $footer_insert ) ) {
					
						echo apply_atomic_shortcode( 'footer_content', '<div id="footer-branding">' . $footer_insert . '</div>' );
						
					} else {
					
						echo apply_atomic_shortcode( 'footer_content', '<div id="footer-branding"><p class="copyright">' . __( 'Copyright &#169; [the-year] [site-link].', 'fanwood' ) . '</p> <p class="credit">' . __( 'Powered by [wp-link] and [theme-link].', 'fanwood' ) . '</p></div>' );
					
					}
				?>

				<?php do_atomic( 'footer' ); // fanwood_footer ?>

			</div><!-- .wrap -->

			<?php do_atomic( 'close_footer' ); // fanwood_close_footer ?>

		</div><!-- #footer -->

		<?php do_atomic( 'after_footer' ); // fanwood_after_footer ?>

	</div></div><!-- #container -->

	<?php do_atomic( 'close_body' ); // fanwood_close_body ?>

	<?php wp_footer(); // wp_footer ?>

</body>
</html>

