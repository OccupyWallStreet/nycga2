<?php
/**
* The TEC template for a list of events. This includes the Past Events and Upcoming Events views 
* as well as those same views filtered to a specific category.
*
* You can customize this view by putting a replacement file of the same name (list.php) in the events/ directory of your theme.
*/

// Don't load directly
if ( !defined('ABSPATH') ) { die('-1'); }

?>

<?php
	   $taxonomy     = TribeEvents::TAXONOMY;
	   $orderby      = 'name'; 
	   $show_count   = 1;      // 1 for yes, 0 for no
	   $pad_counts   = 0;      // 1 for yes, 0 for no
	   $hierarchical = 1;      // 1 for yes, 0 for no
	   $title        = '';
	   
	   $args = array(
		 'taxonomy'     => $taxonomy,
		 'orderby'      => $orderby,
		 'show_count'   => $show_count,
		 'hierarchical' => $hierarchical,
		 'title_li'     => $title
	   );
?>
<form class="eventform" action="<?php bloginfo('url'); ?>" method="get">
	<div>
		<?php $cats = get_categories($args); ?>
		<select class="categories">
			<label title="Category"></label>
			<option value="">Select a Category</option>
		<?php foreach ($cats as $cat) : ?>
		    <option value="<?php echo get_term_link($cat, $cat->taxonomy) ?>"><?php echo $cat->name ?></option>
		<?php endforeach; ?>
		</select>
		
		<?php if ( bp_has_groups('type=alphabetical&per_page=0&page=0') ) : ?> 
		<select name="group_id" class="groups">
			<label title="Groups"></label>
			<option value=""><?php _e('Select a Group','tribe-events-calendar'); ?></option>
			<?php while ( bp_groups() ) : bp_the_group(); ?>
					 <option value="<?php echo bp_group_id(); ?>" <?php echo (!empty($tribe_ecp->group_id) && $tribe_ecp->group_id == bp_get_group_id()) ? 'selected="selected"':''; ?>><?php echo bp_group_name(); ?></option>
			<?php endwhile; ?>
		</select>
		<?php endif; ?>
	</div>
	<input type="submit" value="<?php _e('Search Events','tribe-events-calendar'); ?>" class="button" />
</form>


/*
<script language="javascript">
	jQuery(document).ready(function($){
		$('.categories').change(function()
		   {
		   	   window.location = jQuery(this).val();
			   return false;
		   });
		
	});
</script>
*/

<?php
    // $elements is your list of elements that you are adding to your string array
		global $bp;
    $myGroupList = array();
    foreach($groups as $group_id)
	    {
	    $myGroupList[] = $group_id;
	    print_r($groups);
	    }
?>

<?php

?>
<div id="tribe-events-content" class="upcoming">

	<?php if(!tribe_is_day()): // day view doesn't have a grid ?>
		<div id='tribe-events-calendar-header' class="clearfix">
		<span class='tribe-events-calendar-buttons'> 
			<a class='tribe-events-button-on' href='<?php echo tribe_get_listview_link(); ?>'><?php _e('Event List', 'tribe-events-calendar')?></a>
			<a class='tribe-events-button-off' href='<?php echo tribe_get_gridview_link(); ?>'><?php _e('Calendar', 'tribe-events-calendar')?></a>
		</span>

		</div><!--tribe-events-calendar-header-->
	<?php endif; ?>
	<div id="tribe-events-loop" class="tribe-events-events post-list clearfix">
	
	<?php if (have_posts()) : ?>
	<?php $hasPosts = true; $first = true; ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<?php global $more; $more = false; ?>
		<div id="post-<?php the_ID() ?>" <?php post_class('tribe-events-event clearfix') ?> itemscope itemtype="http://schema.org/Event">
			<?php if ( tribe_is_new_event_day() && !tribe_is_day() ) : ?>
				<h3 class="event-day"><?php echo tribe_get_start_date( null, false, 'l, F j'); ?></h3>
			<?php endif; ?>
			<?php if ( tribe_is_day() && $first ) : $first = false; ?>
				<h4 class="event-day"><?php echo tribe_event_format_date(strtotime(get_query_var('eventDate')), false); ?></h4>
			<?php endif; ?>
			<?php the_title('<h2 class="entry-title" itemprop="name"><a href="' . tribe_get_event_link() . '" title="' . the_title_attribute('echo=0') . '" rel="bookmark">', '</a></h2>'); ?>
			<?php tribe_meta_event_cats(); ?>
			<?php echo tribe_meta_event_category_name('',', '); ?>
			<!--<div class="entry-content tribe-events-event-entry" itemprop="description">
				<?php if (has_excerpt ()): ?>
					<?php the_excerpt(); ?>
				<?php else: ?>
					<?php the_content(); ?>
				<?php tribe_the_custom_fields(); ?>
				<?php endif; ?>
			</div>--> <!-- End tribe-events-event-entry -->

			<div class="tribe-events-event-list-meta" itemprop="location" itemscope itemtype="http://schema.org/Place">
				<table cellspacing="0" border="1">
					<?php if (tribe_is_multiday() || !tribe_get_all_day()): ?>
					<tr>
						<td rowspan="3"><?php echo tribe_get_start_date( $post->ID, true, 'D. M j, Y' );; ?></td>
						<td class="tribe-events-event-meta-desc"><?php _e('Start:', 'tribe-events-calendar') ?></td>
						<td class="tribe-events-event-meta-value" itemprop="startDate" content="<?php echo tribe_get_start_date(true); ?>"><?php echo tribe_get_start_date(true); ?></td>
					</tr>
					<tr>
						<td class="tribe-events-event-meta-desc"><?php _e('End:', 'tribe-events-calendar') ?></td>
						<td class="tribe-events-event-meta-value" itemprop="endDate" content="<?php echo tribe_get_end_date(true); ?>"><?php echo tribe_get_end_date(true); ?></td>
					</tr>
					<?php else: ?>
					<tr>
						<td class="tribe-events-event-meta-desc"><?php _e('Date:', 'tribe-events-calendar') ?></td>
						<td class="tribe-events-event-meta-value" itemprop="startDate" content="<?php echo tribe_get_start_date(true); ?>"><?php echo tribe_get_start_date(true); ?></td>
					</tr>
					<?php endif; ?>

					<?php
						$venue = tribe_get_venue();
						if ( !empty( $venue ) ) :
					?>
					<tr>
						<td class="tribe-events-event-meta-desc"><?php _e('Location:', 'tribe-events-calendar') ?></td>
						<td class="tribe-events-event-meta-value" itemprop="name">
							<? if( class_exists( 'TribeEventsPro' ) ): ?>
								<?php tribe_get_venue_link( get_the_ID(), class_exists( 'TribeEventsPro' ) ); ?>
							<? else: ?>
								<?php echo tribe_get_venue( get_the_ID() ) ?>
							<? endif; ?>
						</td>
					</tr>
					<?php endif; ?>
					<?php
						$phone = tribe_get_phone();
						if ( !empty( $phone ) ) :
					?>
					<tr>
						<td class="tribe-events-event-meta-desc"><?php _e('Phone:', 'tribe-events-calendar') ?></td>
						<td class="tribe-events-event-meta-value" itemprop="telephone"><?php echo $phone; ?></td>
					</tr>
					<?php endif; ?>
					<?php if (tribe_address_exists( get_the_ID() ) ) : ?>
					<tr>
						<td class="tribe-events-event-meta-desc"><?php _e('', 'tribe-events-calendar'); ?><br />
						<?php if( get_post_meta( get_the_ID(), '_EventShowMapLink', true ) == 'true' ) : ?>
							<a class="gmap" itemprop="maps" href="<?php echo tribe_get_map_link(); ?>" title="Click to view a Google Map" target="_blank"><?php _e('Google Map', 'tribe-events-calendar' ); ?></a>
						<?php endif; ?></td>
						<td class="tribe-events-event-meta-value"><?php echo tribe_get_address( get_the_ID() ); ?><br>
						<?php echo tribe_get_city( get_the_ID() ); ?></td>
					</tr>
					<?php endif; ?>
					<?php
						$cost = tribe_get_cost();
						if ( !empty( $cost ) ) :
					?>
					<tr>
						<td class="tribe-events-event-meta-desc"><?php _e('Cost:', 'tribe-events-calendar') ?></td>
						<td class="tribe-events-event-meta-value" itemprop="price"><?php echo $cost; ?></td>
					 </tr>
					<?php endif; ?>
				</table>
			</div>
		</div> <!-- End post -->
	<?php endwhile;// posts ?>
	<?php else :?>
		<?php 
			$tribe_ecp = TribeEvents::instance();
			if ( is_tax( $tribe_ecp->get_event_taxonomy() ) ) {
				$cat = get_term_by( 'slug', get_query_var('term'), $tribe_ecp->get_event_taxonomy() );
				if( tribe_is_upcoming() ) {
					$is_cat_message = sprintf(__(' listed under %s. Check out past events for this category or view the full calendar.','tribe-events-calendar'),$cat->name);
				} else if( tribe_is_past() ) {
					$is_cat_message = sprintf(__(' listed under %s. Check out upcoming events for this category or view the full calendar.','tribe-events-calendar'),$cat->name);
				}
			}
		?>
		<?php if(tribe_is_day()): ?>
			<?php printf( __('No events scheduled for <strong>%s</strong>. Please try another day.', 'tribe-events-calendar'), date_i18n('F d, Y', strtotime(get_query_var('eventDate')))); ?>
		<?php endif; ?>

		<?php if(tribe_is_upcoming()){ ?>
			<?php _e('No upcoming events', 'tribe-events-calendar');
			echo !empty($is_cat_message) ? $is_cat_message : ".";?>

		<?php }elseif(tribe_is_past()){ ?>
			<?php _e('No previous events' , 'tribe-events-calendar');
			echo !empty($is_cat_message) ? $is_cat_message : ".";?>
		<?php } ?>
		
	<?php endif; ?>


	</div><!-- #tribe-events-loop -->
	<div id="tribe-events-nav-below" class="tribe-events-nav clearfix">

		<div class="tribe-events-nav-previous"><?php 
		// Display Previous Page Navigation
		if( tribe_is_upcoming() && get_previous_posts_link() ) : ?>
			<?php previous_posts_link( '<span>'.__('&laquo; Previous Events', 'tribe-events-calendar').'</span>' ); ?>
		<?php elseif( tribe_is_upcoming() && !get_previous_posts_link( ) ) : ?>
			<a href='<?php echo tribe_get_past_link(); ?>'><span><?php _e('&laquo; Previous Events', 'tribe-events-calendar' ); ?></span></a>
		<?php elseif( tribe_is_past() && get_next_posts_link( ) ) : ?>
			<?php next_posts_link( '<span>'.__('&laquo; Previous Events', 'tribe-events-calendar').'</span>' ); ?>
		<?php endif; ?>
		</div>

		<div class="tribe-events-nav-next"><?php
		// Display Next Page Navigation
		if( tribe_is_upcoming() && get_next_posts_link( ) ) : ?>
			<?php next_posts_link( '<span>'.__('Next Events &raquo;', 'tribe-events-calendar').'</span>' ); ?>
		<?php elseif( tribe_is_past() && get_previous_posts_link( ) ) : ?>
			<?php previous_posts_link( '<span>'.__('Next Events &raquo;', 'tribe-events-calendar').'</span>' ); // a little confusing but in 'past view' to see newer events you want the previous page ?>
		<?php elseif( tribe_is_past() && !get_previous_posts_link( ) ) : ?>
			<a href='<?php echo tribe_get_upcoming_link(); ?>'><span><?php _e('Next Events &raquo;', 'tribe-events-calendar'); ?></span></a>
		<?php endif; ?>
		</div>

	</div>
	<?php if ( !empty($hasPosts) && function_exists('tribe_get_ical_link') ): ?>
		<a title="<?php esc_attr_e('iCal', 'tribe-events-calendar') ?>" class="ical" href="<?php echo tribe_get_ical_link(); ?>"><?php _e('iCal', 'tribe-events-calendar') ?></a>
	<?php endif; ?>
</div>
