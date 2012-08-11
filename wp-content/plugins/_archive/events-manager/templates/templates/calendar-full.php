<?php 
/*
 * This file contains the HTML generated for full calendars. You can copy this file to yourthemefolder/plugins/events/templates and modify it in an upgrade-safe manner.
 * 
 * There are two variables made available to you: 
 * 
 * 	$calendar - contains an array of information regarding the calendar and is used to generate the content
 *  $args - the arguments passed to EM_Calendar::output()
 * 
 * Note that leaving the class names for the previous/next links will keep the AJAX navigation working.
 */
$cal_count = count($calendar['cells']); //to prevent an extra tr
$col_count = $tot_count = 1; //this counts collumns in the $calendar_array['cells'] array
$col_max = count($calendar['row_headers']); //each time this collumn number is reached, we create a new collumn, the number of cells should divide evenly by the number of row_headers
?>
<table class="em-calendar fullcalendar">
	<thead>
		<tr>
			<td><a class="em-calnav full-link em-calnav-prev" href="<?php echo $calendar['links']['previous_url']; ?>">&lt;&lt;</a></td>
			<td class="month_name" colspan="5"><?php echo ucfirst(date_i18n('M Y', $calendar['month_start'])); ?></td>
			<td><a class="em-calnav full-link em-calnav-next" href="<?php echo $calendar['links']['next_url']; ?>">&gt;&gt;</a></td>
		</tr>
	</thead>
	<tbody>
		<tr class="days-names">
			<td><?php echo implode('</td><td>',$calendar['row_headers']); ?></td>
		</tr>
		<tr>
			<?php
			foreach($calendar['cells'] as $date => $cell_data ){
				$class = ( !empty($cell_data['events']) && count($cell_data['events']) > 0 ) ? 'eventful':'eventless';
				if(!empty($cell_data['type'])){
					$class .= "-".$cell_data['type']; 
				}
				?>
				<td class="<?php echo $class; ?>">
					<?php if( !empty($cell_data['events']) && count($cell_data['events']) > 0 ): ?>
					<a href="<?php echo esc_url($cell_data['link']); ?>" title="<?php echo esc_attr($cell_data['link_title']); ?>"><?php echo date('j',$cell_data['date']); ?></a>
					<ul>
						<?php 
						$cell_events = array();
						if( get_option('dbem_display_calendar_events_limit') ){
							$count = 0;
							foreach($cell_data['events'] as $cell_event){
								$cell_events[] = $cell_event;
								$count++;
								if($count > get_option('dbem_display_calendar_events_limit')) break;
							}
						}else{
							$cell_events = $cell_data['events'];
						}
						?>
						<?php echo EM_Events::output($cell_events,array('format'=>get_option('dbem_full_calendar_event_format'))); ?>
						<?php if( count($cell_events) > get_option('dbem_display_calendar_events_limit',3) && get_option('dbem_display_calendar_events_limit_msg') != '' ): ?>
						<li><a href="<?php echo esc_url($cell_data['link']); ?>"><?php echo get_option('dbem_display_calendar_events_limit_msg'); ?></a></li>
						<?php endif; ?>
					</ul>
					<?php else:?>
					<?php echo date('j',$cell_data['date']); ?>
					<?php endif; ?>
				</td>
				<?php
				//create a new row once we reach the end of a table collumn
				$col_count= ($col_count == $col_max ) ? 1 : $col_count+1;
				echo ($col_count == 1 && $tot_count < $cal_count) ? '</tr><tr>':'';
				$tot_count ++; 
			}
			?>
		</tr>
	</tbody>
</table>