<?php do_action( 'bp_before_events_loop' ) ?>

<?php
	$showevent = 1;
	$jes_adata = get_option( 'jes_events' );
	$eshowevent = $jes_adata[ 'jes_events_addnavicatalog_disable' ];
	$sortby = $jes_adata[ 'jes_events_sort_by' ]; ?>

<?php if ( bp_jes_has_events( bp_ajax_querystring( 'events' )) ) : ?>

	<div class="pagination">
		<div class="pag-count" id="group-dir-count">
			<?php jes_bp_events_pagination_count() ?>
		</div>
		<div class="pagination-links" id="group-dir-pag">
			<?php jes_bp_events_pagination_links() ?>
			<?php _e('Style:','jet-event-system'); ?> <?php _e($jes_adata['jes_events_style'],'jet-event-system'); ?>
		</div>
	</div>

<?php 
	if ( !is_user_logged_in() and !$eshowevent )
		{ ?>
			<div id="message" class="info">
				<p><?php _e('Private events are not shown, register to view','jet-event-system'); ?></p>
			</div>
	<?php } ?>

<?php if ($jes_adata['jes_events_style'] == 'Calendar') { ?>
<?php				require_once ( WP_PLUGIN_DIR . '/jet-event-system-for-buddypress/main/jet-events-themetemplate.php' ); ?>
	<?php // if ( bp_jes_has_events( 'type=calendar&per_page=' . $instance['max_events'] ) ) : ?>
<ul id="group-list" class="item-list">
<script type="text/javascript">
    var jqr = jQuery;
    jqr(document).ready(function($) {
	$(document).ready(function() {
	var date = new Date();
	$("#icalendar").fullCalendar({
	    editable: true,
		dayNamesShort: ['<?php _e('Sun'); ?>','<?php _e('Mon'); ?>','<?php _e('Tue'); ?>','<?php _e('Wed'); ?>','<?php _e('Thu'); ?>','<?php _e('Fri'); ?>','<?php _e('Sat'); ?>'],
		monthNames: ['<?php _e('January'); ?>','<?php _e('February'); ?>','<?php _e('March'); ?>','<?php _e('April'); ?>','<?php _e('May'); ?>','<?php _e('June'); ?>','<?php _e('July'); ?>','<?php _e('August'); ?>','<?php _e('September'); ?>','<?php _e('October'); ?>','<?php _e('November'); ?>','<?php _e('December'); ?>',],
		monthNamesShort: ['<?php _e('Jan'); ?>','<?php _e('Feb'); ?>','<?php _e('Mar'); ?>','<?php _e('Apr'); ?>','<?php _e('May'); ?>','<?php _e('Jun'); ?>','<?php _e('Jul'); ?>','<?php _e('Aug'); ?>','<?php _e('Sep'); ?>','<?php _e('Oct'); ?>','<?php _e('Nov'); ?>','<?php _e('Dec'); ?>',],
		header: {
			left: 'title',
			center: '',
			right: 'basicWeek,month,today,prev,next' },
	    	events: [

<?php while ( jes_bp_events() ) : bp_jes_the_event();

	$er = jes_bp_get_event_type();

// Admin Approve
	$shiftcan = 0;
	$showeventnona = $jes_adata[ 'jes_events_adminapprove_enable' ];

// Not Private Event?
	if ( !is_user_logged_in() and !$eshowevent and $er == 'Private Event' )
		{
			$showevent = 0;
		}
			else
		{
			$showevent = 1;
		}
// Check Approved Event?
		if ( !jes_bp_get_event_eventapproved() and $showeventnona )
			{
				if ( current_user_can('manage_options') )
					{ 
						$showevent = 1;
						$shiftcan = 1;
					}
						else
					{
						$showevent = 0;
						$shiftcan = 0;
					} 
			}
if ( $showevent )
	{
// Loop Templates

				jes_theme_template_calendar();
	}
endwhile; ?>
 	{
	    title:"end event",
	    start: new Date(2009,11,1),
	    end: new Date(2009,11,1)
	}
	]
    });
    });
});
</script>
<div id="loading" style="display:none">loading...</div>
<div id="icalendar"></div>
</ul>
<?php // endif;
 } else { ?>

<ul id="group-list" class="item-list">

<?php while ( jes_bp_events() ) : bp_jes_the_event();

	$er = jes_bp_get_event_type();

// Admin Approve
	$shiftcan = 0;                              
	$showeventnona = $jes_adata[ 'jes_events_adminapprove_enable' ];

// Not Private Event?
	if ( !is_user_logged_in() and !$eshowevent and $er == 'Private Event' )
		{
			$showevent = 0;
		}
			else
		{
			$showevent = 1;
		}
// Check Approved Event?
		if ( !jes_bp_get_event_eventapproved() and $showeventnona )
			{
				if ( current_user_can('manage_options') )
					{ 
						$showevent = 1;
						$shiftcan = 1;
					}
						else
					{
						$showevent = 0;
						$shiftcan = 0;
					} 
			}
		
if ( $showevent )
	{
// Loop Templates
		if ($jes_adata['jes_events_style'] != 'Custom' )
			{
				require_once ( WP_PLUGIN_DIR . '/jet-event-system-for-buddypress/main/jet-events-themetemplate.php' );
			}
	// Standart style Event Catalog 
		if ($jes_adata['jes_events_style'] == 'Standart') 
			{
				jes_theme_template_standart();
		}

	// Standard will full description
		if ($jes_adata['jes_events_style'] == 'Standard will full description') 
			{
				jes_theme_template_standartfull();
		}

	// Twitter style Event Catalog
		if ($jes_adata['jes_events_style'] == 'Twitter' )
			{
				jes_theme_template_twitter();
		}

	// Custom style Event Catalog
		if ($jes_adata['jes_events_style'] == 'Custom' ) 
			{
				if (file_exists(STYLESHEETPATH . '/events/looptemplates/custom.php'))
					{
						include('looptemplates/custom.php');
					}
						else
					{
						jes_theme_template_standart();
					}
		}
	}
endwhile; ?>	
</ul>
<?php } ?>
	<?php do_action( 'bp_after_events_loop' ) ?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'There were no events found.', 'jet-event-system' ) ?></p>
	</div>

<?php
endif;
?>