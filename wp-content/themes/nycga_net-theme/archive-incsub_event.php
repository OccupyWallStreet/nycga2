<?php
global $booking, $wpdb, $wp_query;
get_header( 'event' );
?>

  <div id="wpmudevevents-wrapper">
      <h2><?php _e('Events', Eab_EventsHub::TEXT_DOMAIN); ?></h2>
      <a href="/edit-event/" class="button new">Create an Event</a>
      <?php if ( !have_posts() ) : ?>
        <?php $event_group = groups_get_group( array( 'group_id' => $event->group_id ) ); ?>
          <p><?php $event_ptype = get_post_type_object( 'incsub_event' ); echo $event_ptype->labels->not_found; ?></p>
      <?php else: ?>
            <div class="wpmudevevents-list">

            <table>
	            <tbody>
                <?php while ( have_posts() ) : the_post(); ?>                        
				            <tr class="event">
					            <td><?php echo Eab_Template::get_event_dates($post); ?></td>
					            <td><h3 class="wpmudevevents-header"><?php echo Eab_Template::get_event_link($post); ?></h3></td>
					            <td><?php echo Eab_Template::get_event_details($post); ?><br>
					            Address: <?php echo $event_venue; ?></td>
					            <td><?php echo Eab_Template::get_rsvp_form($post); ?></td>
					            <td></td>
				            </tr>
                <?php endwhile; ?>
	            </tbody>
            </table>
          </div>
      <?php endif; ?>
  </div>

<?php get_footer( 'event' ); ?>
