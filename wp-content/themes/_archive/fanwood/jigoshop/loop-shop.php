<?php
/**
 * Jigoshop Products Loop Template
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
?>

<?php
global $columns, $per_page;

do_action('jigoshop_before_shop_loop');

$loop = 0;

if (!isset($columns) || !$columns) $columns = apply_filters('loop_shop_columns', 4);
//if (!isset($per_page) || !$per_page) $per_page = apply_filters('loop_shop_per_page', get_option('posts_per_page'));

//if ($per_page > get_option('posts_per_page')) query_posts( array_merge( $wp_query->query, array( 'posts_per_page' => $per_page ) ) );

ob_start();

if (have_posts()) : while (have_posts()) : the_post(); $_product = new jigoshop_product( $post->ID ); $loop++;

	?>
	<li class="product <?php if ($loop%$columns==0) echo 'last'; if (($loop-1)%$columns==0) echo 'first'; ?>">

		<?php do_action('jigoshop_before_shop_loop_item'); ?>

		<a href="<?php the_permalink(); ?>" class="product-image-link">

			<?php do_action('jigoshop_before_shop_loop_item_title', $post, $_product); ?>

		</a><!-- .product-image-link -->
		
		<h3 class="product-title">
			<a href="<?php echo get_permalink(); ?>" title="<?php the_title_attribute( 'echo=1' ); ?>"><?php the_title(); ?></a>
		</h3><!-- .product-title -->

		<?php do_action('jigoshop_after_shop_loop_item_title', $post, $_product); ?>

		<?php do_action('jigoshop_after_shop_loop_item', $post, $_product); ?>

	</li><?php

	if ($loop==$per_page) break;

endwhile; endif;

if ($loop==0) :

	echo '<p class="info">'.__('No products found which match your selection.', 'jigoshop').'</p>';

else :

	$found_posts = ob_get_clean();

	echo '<ul class="products">' . $found_posts . '</ul><div class="clear"></div>';

endif;

do_action('jigoshop_after_shop_loop');
