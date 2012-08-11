<?php
/**
 * Jigoshop Archive Template
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

			<?php do_action('jigoshop_before_main_content'); ?>
			
			<?php get_sidebar( 'before-content' ); // Loads the sidebar-before-content.php template. ?>
			
			<div class="loop-meta">
				<?php if (is_search()) : ?>
					<h1 class="loop-title"><?php _e('Search Results:', 'jigoshop'); ?> &ldquo;<?php the_search_query(); ?>&rdquo; <?php if (get_query_var('paged')) echo ' &mdash; Page '.get_query_var('paged'); ?></h1>
				<?php else : ?>
					<h1 class="loop-title"><?php _e('All Products', 'jigoshop'); ?></h1>
				<?php endif; ?>
			</div><!-- .loop-meta -->

			<div class="hentry page">
				
				<div class="entry-content">
					<?php
						$shop_page_id = jigoshop_get_page_id('shop');
						$shop_page = get_post($shop_page_id);
						echo apply_filters('the_content', $shop_page->post_content);
					?>
					<?php jigoshop_get_template_part( 'loop', 'shop' ); ?>
				</div><!-- .entry-content -->
			</div><!-- .hentry -->
			
			<?php get_sidebar( 'after-content' ); // Loads the sidebar-after-content.php template. ?>

			<?php do_action('jigoshop_after_main_content'); ?>

		</div><!-- .hfeed -->

		<?php do_atomic( 'close_content' ); // fanwood_close_content ?>

		<?php get_template_part( 'loop-nav' ); // Loads the loop-nav.php template. ?>

	</div><!-- #content -->

	<?php do_atomic( 'after_content' ); // fanwood_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>