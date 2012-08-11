<?php
/**
 * Product Taxonomy Template
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
			
			<div class="hentry page">

				<?php $term = get_term_by( 'slug', get_query_var($wp_query->query_vars['taxonomy']), $wp_query->query_vars['taxonomy']); ?>

				<h1 class="entry-title page-title"><?php echo wptexturize($term->name); ?></h1>

				<div class="entry-content">
					<?php echo wpautop( wptexturize( $term->description ) ); ?>
					<?php jigoshop_get_template_part( 'loop', 'shop' ); ?>
				</div><!-- .entry-content -->

				<?php do_action('jigoshop_pagination'); ?>
			
			</div><!-- .hentry -->

			<?php do_action('jigoshop_after_main_content'); ?>
			
		</div><!-- .hfeed -->

		<?php do_atomic( 'close_content' ); // fanwood_close_content ?>

		<?php get_template_part( 'loop-nav' ); // Loads the loop-nav.php template. ?>

	</div><!-- #content -->

	<?php do_atomic( 'after_content' ); // fanwood_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>