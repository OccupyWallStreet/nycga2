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
?>
<table class="em-calendar fullcalendar">
	<thead>
		<tr>
			<td><a class="em-calnav full-link" href="<?php echo $calendar['links']['previous_url']; ?>">&lt;&lt;</a></td>
			<td class="month_name" colspan="5"><?php echo ucfirst(date_i18n('M Y', $calendar['month_start'])); ?></td>
			<td><a class="em-calnav full-link" href="<?php echo $calendar['links']['next_url']; ?>">&gt;&gt;</a></td>
		</tr>
	</thead>
	<tbody>
		<tr class="days-names">
			<td><?php echo implode('</td><td>',$calendar['row_headers']); ?></td>
		</tr>
		<tr>
			<?php
			$col_count = 1; //this counts collumns in the $calendar_array['cells'] array
			$col_max = count($calendar['row_headers']); //each time this collumn number is reached, we create a new collumn, the number of cells should divide evenly by the number of row_headers
			foreach($calendar['cells'] as $date => $cell_data ){
				$class = ( !empty($cell_data['events']) && count($cell_data['events']) > 0 ) ? 'eventful':'eventless';
				if(!empty($cell_data['type'])){
					$class .= "-".$cell_data['type']; 
				}
				?>
				<td class="<?php echo $class; ?>">
					<?php if( !empty($cell_data['events']) && count($cell_data['events']) > 0 ): ?>
					<a href="<?php echo esc_url($cell_data['link']); ?>" title="<?php echo esc_attr($cell_data['link_title']); ?>"><?php echo date('j',$cell_data['date']); ?></a>
					<ul><?php echo EM_Events::output($cell_data['events'],array('format'=>get_option('dbem_full_calendar_event_format'))); ?></ul>
					<?php else:?>
					<?php echo date('j',$cell_data['date']); ?>
					<?php endif; ?>
				</td>
				<?php
				//create a new row once we reach the end of a table collumn
				$col_count= ($col_count == $col_max ) ? 1 : $col_count+1;
				echo ($col_count == 1) ? '</tr><tr>':''; 
			}
			?>
		</tr>
	</tbody>
</table>
<script type="text/javascript">
	jQuery(document).ready( function($){
		/* Calendar AJAX */
		$('a.em-calnav, a.em-calnav').live('click', function(e){
			e.preventDefault();
			$(this).closest('.em-calendar-wrapper').prepend('<div class="loading" id="em-loading"></div>');
			var url = em_ajaxify($(this).attr('href'));
			$(this).closest('.em-calendar-wrapper').load(url);
		} ); 
	});
</script>