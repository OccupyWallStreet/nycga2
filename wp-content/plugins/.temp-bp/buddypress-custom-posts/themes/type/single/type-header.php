<?php 
	global $bp;
	$type = get_post_type_object( $bp->active_components[ $bp->current_component ] );
?>

<div id = 'item-actions'>
	<h3><?php echo $type->labels->type_creator ?></h3>
	<ul id = '<? echo $bp->current_component; ?>-creator'>
		<li><?php bpcp_the_author(); ?></li>
	</ul>
</div><!-- /#item-actions -->


<div id = 'item-header-avatar'>
	<a href = '<?php the_permalink(); ?>' title = '<?php the_title(); ?>'>
		<?php bpcp_type_avatar( Array( 'type' => 'full' ) ); ?>
	</a>
</div>


<div id="item-header-content">
	<h2><a href="<?php the_permalink() ?>" title="<?php the_title() ?>"><?php the_title() ?></a></h2>
	<span class="activity"><?php bpcp_last_active(); ?></span>

	<div id="item-meta">
		<?php
			if( bpcp_is_visible() ) {
				do_action( 'bpcp_single_before_header', $bp->active_components[ $bp->current_component ] );
				do_action( 'bpcp_' . $bp->active_components[ $bp->current_component ] . '_single_before_header' );
				if ( !bpcp_is_home() )
					the_excerpt();
			
				global $bp;
				do_action( 'bpcp_single_after_header', $bp->active_components[ $bp->current_component ] );
				do_action( 'bpcp_' . $bp->active_components[ $bp->current_component ] . '_single_after_header' );
			}

			if ( bpcp_is_forum() && is_user_logged_in() && !bpcp_is_forum_topic() ) : ?>
				<div class="generic-button event-button">
					<a href="#post-new" class=""><?php _e( 'New Topic', 'buddypress' ) ?></a>
				</div>
			<?php endif;
		?>

		<?php	global $bp;
			do_action( 'bpcp_single_item_actions', $bp->active_components[ $bp->current_component ] );
			do_action( 'bpcp_' . $bp->active_components[ $bp->current_component ] . '_single_item_actions' );
		?>
	</div>

</div><!-- #item-header-content -->

<?php do_action( 'template_notices' ) ?>
