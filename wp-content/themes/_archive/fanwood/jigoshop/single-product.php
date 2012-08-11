<?php
/**
 * JIgoshop - Single Product
 *
 * DISCLAIMER
 *
 * Do not edit or add directly to this file if you wish to upgrade Jigoshop to newer
 * versions in the future. If you wish to customise Jigoshop core for your needs,
 * please use our GitHub repository to publish essential changes for consideration.
 *
 * @package		Jigoshop
 * @category	Catalog
 * @author		Jigowatt
 * @copyright	Copyright (c) 2011-2012 Jigowatt Ltd.
 * @license		http://jigoshop.com/license/commercial-edition
 */

get_header(); // Loads the header.php template. ?>

	<?php do_atomic( 'before_content' ); // fanwood_before_content ?>

	<div id="content">

		<?php do_atomic( 'open_content' ); // fanwood_open_content ?>

		<div class="hfeed">
		
			<?php get_sidebar( 'before-content' ); // Loads the sidebar-before-content.php template. ?>

			<?php do_action('jigoshop_before_main_content'); ?>

			<?php if ( have_posts() ) while ( have_posts() ) : the_post(); global $_product; $_product = new jigoshop_product( $post->ID ); ?>

				<?php do_action('jigoshop_before_single_product', $post, $_product); ?>

				<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">

					<div class="product-header">
					
						<?php do_action('jigoshop_before_single_product_summary', $post, $_product); ?>
						
						<h1 class="product-title"><?php the_title(); ?></h1>

						<div class="product-summary">
							<?php do_action( 'jigoshop_template_single_summary', $post, $_product ); ?>
							
							<?php echo apply_atomic_shortcode( 'entry_edit_link', '[entry-edit-link before="<p>" after="</p>"]' ); ?>
						</div><!-- .product-summary -->

					</div><!-- .product-header -->
					
					<div class="entry-content product-content">

					<?php do_action('jigoshop_after_single_product_summary', $post, $_product); ?>
					
					</div><!-- .entry-content -->

				</div><!-- .hentry -->

				<?php do_action('jigoshop_after_single_product', $post, $_product); ?>

			<?php endwhile; ?>

			<?php do_action('jigoshop_after_main_content'); ?>
		
			<?php get_sidebar( 'after-content' ); // Loads the sidebar-after-content.php template. ?>
		
		</div><!-- .hfeed -->

		<?php do_atomic( 'close_content' ); // fanwood_close_content ?>

	</div><!-- #content -->

	<?php do_atomic( 'after_content' ); // fanwood_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>