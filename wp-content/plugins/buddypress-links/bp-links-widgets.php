<?php

/* Register widgets for links component */
function bp_links_register_widgets() {
	add_action('widgets_init', create_function('', 'return register_widget("BP_Links_Widget");') );
}
add_action( 'bp_init', 'bp_links_register_widgets', 11 );

/*** LINKS WIDGET *****************/

class BP_Links_Widget extends WP_Widget {
	function bp_links_widget() {
		parent::WP_Widget( false, $name = __( 'Links', 'buddypress' ), array( 'description' => __( 'Your BuddyPress Links', 'buddypress-links' ) ) );

		if ( is_active_widget( false, false, $this->id_base ) ) {
			bp_links_setup_theme();
			wp_enqueue_script( 'bp_links_widget_links_list-js', BP_LINKS_THEME_URL_INC . '/widgets.js', array('jquery') );
		}
	}

	function widget($args, $instance) {
		global $bp;

	    extract( $args );

		echo $before_widget;
		echo $before_title
		   . $widget_name
		   . $after_title; ?>

		<?php if ( bp_has_links( 'type=popular&per_page=' . $instance['max_links'] . '&max=' . $instance['max_links'] ) ) : ?>
			<div class="item-options" id="links-list-options">
				<span class="ajax-loader" id="ajax-loader-links"></span>
				<a href="<?php echo site_url() . '/' . $bp->links->slug ?>" id="newest-links"><?php _e("Newest", 'buddypress') ?></a> |
				<!-- a href="<?php echo site_url() . '/' . $bp->links->slug ?>" id="recently-active-links"><?php _e("Active", 'buddypress') ?></a> | -->
				<a href="<?php echo site_url() . '/' . $bp->links->slug ?>" id="most-votes"><?php _e("Votes", 'buddypress-links') ?></a> |
				<a href="<?php echo site_url() . '/' . $bp->links->slug ?>" id="high-votes"><?php _e("Rating", 'buddypress-links') ?></a> |
				<a href="<?php echo site_url() . '/' . $bp->links->slug ?>" id="popular-links" class="selected"><?php _e("Popular", 'buddypress') ?></a>
			</div>

			<ul id="links-list" class="item-list">
				<?php while ( bp_links() ) : bp_the_link(); ?>
					<li>
						<div class="item-avatar">
							<a href="<?php bp_link_permalink() ?>"><?php bp_link_avatar_thumb() ?></a>
						</div>

						<div class="item">
							<div class="item-title"><a href="<?php bp_link_permalink() ?>" title="<?php bp_link_name() ?>"><?php bp_link_name() ?></a></div>
							<div class="item-meta"><span class="activity"><?php printf( __( '%+d rating', 'buddypress' ), bp_get_link_vote_total() ) ?></span></div>
						</div>
					</li>

				<?php endwhile; ?>
			</ul>
			<?php wp_nonce_field( 'bp_links_widget_links_list', '_wpnonce-links' ); ?>
			<input type="hidden" name="links_widget_max" id="links_widget_max" value="<?php echo attribute_escape( $instance['max_links'] ); ?>" />

		<?php else: ?>

			<div class="widget-error">
				<?php _e('There are no links to display.', 'buddypress') ?>
			</div>

		<?php endif; ?>

		<?php echo $after_widget; ?>
	<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['max_links'] = strip_tags( $new_instance['max_links'] );

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'max_links' => 5 ) );
		$max_links = strip_tags( $instance['max_links'] );
		?>

		<p><label for="bp-links-widget-links-max"><?php _e('Max links to show:', 'buddypress'); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'max_links' ); ?>" name="<?php echo $this->get_field_name( 'max_links' ); ?>" type="text" value="<?php echo attribute_escape( $max_links ); ?>" style="width: 30%" /></label></p>
	<?php
	}
}

function bp_links_ajax_widget_links_list() {
	global $bp;

	check_ajax_referer('bp_links_widget_links_list');

	switch ( $_POST['filter'] ) {
		case 'newest-links':
			$type = 'newest';
			break;
//		case 'recently-active-links':
//			$type = 'active';
//			break;
		case 'popular-links':
			$type = 'popular';
			break;
		case 'most-votes':
			$type = 'most-votes';
			break;
		case 'high-votes':
			$type = 'high-votes';
			break;
	}

	if ( bp_has_links( 'type=' . $type . '&per_page=' . $_POST['max_links'] . '&max=' . $_POST['max_links'] ) ) : ?>
		<?php echo "0[[SPLIT]]"; ?>

		<ul id="links-list" class="item-list">
			<?php while ( bp_links() ) : bp_the_link(); ?>
				<li>
					<div class="item-avatar">
						<a href="<?php bp_link_permalink() ?>"><?php bp_link_avatar_thumb() ?></a>
					</div>

					<div class="item">
						<div class="item-title"><a href="<?php bp_link_permalink() ?>" title="<?php bp_link_name() ?>"><?php bp_link_name() ?></a></div>
						<div class="item-meta">
							<span class="activity">
								<?php
								if ( 'newest-links' == $_POST['filter'] ) {
									printf( __( 'created %s ago', 'buddypress' ), bp_get_link_time_since_created() );
//								} else if ( 'recently-active-links' == $_POST['filter'] ) {
//									printf( __( 'active %s ago', 'buddypress' ), bp_get_link_last_active() );
								} else if ( 'popular-links' == $_POST['filter'] || 'high-votes' == $_POST['filter'] ) {
									printf( __( '%+d rating', 'buddypress' ), bp_get_link_vote_total() );
								} else {
									printf( __( '%s votes', 'buddypress' ), bp_get_link_vote_count() );
								}
								?>
							</span>
						</div>
					</div>
				</li>

			<?php endwhile; ?>
		</ul>
		<?php wp_nonce_field( 'bp_links_widget_links_list', '_wpnonce-links' ); ?>
		<input type="hidden" name="links_widget_max" id="links_widget_max" value="<?php echo attribute_escape( $_POST['max_links'] ); ?>" />

	<?php else: ?>

		<?php echo "-1[[SPLIT]]<li>" . __("No links matched the current filter.", 'buddypress'); ?>

	<?php endif;

}
add_action( 'wp_ajax_widget_links_list', 'bp_links_ajax_widget_links_list' );
?>
