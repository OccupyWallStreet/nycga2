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
	          
							<div class="search_box gradient">
														
								<form name="event_search">
															
									<input type="text" name="title" value="" name="s" id="s" />
									
				          <?php $args = array(
										'show_option_all'    => 'All Categories',
										'orderby'            => 'name',
										'order'              => 'ASC',
										'title_li'           => __( 'Categories' ),
										'pad_counts'         => 0,
										'taxonomy'           => 'eab_events_category',
										'name'          		 => 'eab_events_category',
										'class'							=> 'chzn-select',
									); ?>
									
									<?php wp_dropdown_categories( $args ); ?>
									
									<!--
									<select name="eab_events_category">
										<option value="">Category</option>
									<?php $categories = get_terms('eab_events_category');
										foreach ($categories as $category) { 
											echo '<option value="' . $category->name . '">' . $category->name . '</option>';
										}
									?>
									</select>-->
								 
									<select name="eab_event-bp-group_event" data-placeholder="Select a Group" class="chzn-select">
										<option value=''>
							      <?php _e('All Groups', Eab_EventsHub::TEXT_DOMAIN); ?>
							      </option>
										<?php if ( bp_has_groups('type=alphabetical&per_page=all') ) : ?>
											<?php while ( bp_groups() ) : bp_the_group(); ?>
												<option value="<?php echo bp_group_id(); ?>"><?php echo bp_group_name('show_option_all=All Group'); ?></option>
											<?php endwhile; ?>
										<?php endif; ?>
									</select>
									<input type="submit" class="button">
								</form>
							</div>
            
            <table>
	            <tbody>
                <?php while ( have_posts() ) : the_post(); ?>
                    
				            <tr class="event">
					            <!-- Figure out how to format the date to display in the form D, M j, g:ia -->
					            <td class="event_date"><?php echo Eab_Template::get_event_dates($post); ?></td>
					            <td class="event_title"><h2 class="wpmudevevents-header"><?php echo Eab_Template::get_event_link($post); ?></h2><div class="event-thumbnail">
					            <?php echo the_excerpt(); ?>
					            <!--<?php the_post_thumbnail(); ?>--></div></td>
					            <td class="event_address"><?php echo Eab_Template::get_event_details($post); ?></td>
					            <td class="event_category">				         
					             <!-- Display categories -->
					             <p>
					             <?php
										  $categories = get_terms('eab_events_category');
										  $output_categories = array();
										  foreach ($categories as $category):
											  echo '<a href="/events/' . $category->slug . '">' . $category->name . '</a> ';
										  endforeach; ?>
					             </p>

					            <!-- Display group -->
					            <p><?php
											global $post;
											$group_id = get_post_meta($post->ID, 'eab_event-bp-group_event', true);
											if ($group_id) {
												$group = groups_get_group(array('group_id' => $group_id));
												if ($group) echo '<a href="/' . $bp->groups->slug . '/' . $group->slug . '/group-events/">' . $group->name . '</a>';
											}
											?></p>
											
											</td>
					            <!-- <td class="rsvp_buttons"><?php echo Eab_Template::get_rsvp_form($post); ?></td> -->
					            
				            </tr>
                <?php endwhile; ?>
	            </tbody>
            </table>
          </div>
      <?php endif; ?>
  </div>
    
  <script type="text/javascript">
    $(".chzn-select").chosen();
    $(".chzn-select-deselect").chosen({allow_single_deselect:true});
  </script>

<?php get_footer( 'event' ); ?>
