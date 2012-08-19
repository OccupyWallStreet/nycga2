<?php
global $booking, $wpdb, $wp_query;
$year = $wp_query->query_vars['event_year'];
$year = $year ? $year : date_i18n('Y'); // date_i18n uses current_time when called like this
$month = $wp_query->query_vars['event_monthnum'];
$month = $month ? $month : date_i18n('m');
$time = strtotime("{$year}-{$month}-01");

get_header( 'event' );
?>
	<div id="primary">
        <div id="wpmudevevents-wrapper">
            <h2><?php echo sprintf(
            	__('Events for %s', Eab_EventsHub::TEXT_DOMAIN),
            	date_i18n("F Y", $time)
			); ?></h2>
            <div class="wpmudevevents-list">
            <?php
            	if (!class_exists('Eab_CalendarTable_EventArchiveCalendar')) require_once EAB_PLUGIN_DIR . 'lib/class_eab_calendar_helper.php';
				$renderer = new Eab_CalendarTable_EventArchiveCalendar($wp_query->posts);
				echo $renderer->get_month_calendar($time);
            ?>
			
				<div class="event-pagination">
					<?php 
						$prev = $time - (28*86400); 
						$next = $time + (32*86400);
					?>
					<a href="<?php echo Eab_Template::get_archive_url($prev, true); ?>">Prev</a>
					<a href="<?php echo Eab_Template::get_archive_url($next, true); ?>">Next</a>
				</div>
			</div>
		</div>
	</div>
<?php get_footer( 'event' ); ?>
