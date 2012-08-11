<?php
/**
 * The Sidebar containing the default widget area.
 *
 * If no active widgets in sidebar, hide it completely.
 *
 * @package views_base
 */
?>
	<?php 
	if ( is_active_sidebar( $name ) ) 
		{
			do_action( 'views_base_before_' . $name );
	?>
	<aside id="<?php echo $name;?>_id" class="<?php echo $name;?>">
		<?php dynamic_sidebar( $name ); ?>
	</aside><!-- #<?php echo $name?> -->
	<?php
			do_action( 'views_base_after_' . $name );
		}
	?>