<?php
global $booking, $wpdb, $wp_query;
get_header( 'event' );
?>
	<div id="primary">
		<div id="content" role="main" class="default">
            <div id="wpmudevevents-wrapper">
                <h2><?php _e('Events', Eab_EventsHub::TEXT_DOMAIN); ?></h2>
                <hr/>
                <?php if ( !have_posts() ) : ?>
                    <p><?php $event_ptype = get_post_type_object( 'incsub_event' ); echo $event_ptype->labels->not_found; ?></p>
                <?php else: ?>
                    <div class="wpmudevevents-list">
                   
                    <?php while ( have_posts() ) : the_post(); ?>
                        <div class="event <?php echo Eab_Template::get_status_class($post); ?>">
                            <div class="wpmudevevents-header">
                                <h3><?php echo Eab_Template::get_event_link($post); ?></h3>
                                <a href="<?php the_permalink(); ?>" class="wpmudevevents-viewevent"><?php _e('View event', Eab_EventsHub::TEXT_DOMAIN); ?></a>
                            </div>
                            <?php
                                echo Eab_Template::get_event_details($post);
                            ?>
                            <?php
                                echo Eab_Template::get_rsvp_form($post);
                            ?>
                            <hr />
                        </div>
                    <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </div>
		</div>
	</div>
<?php get_sidebar( 'event' ); ?>
<?php get_footer( 'event' ); ?>
