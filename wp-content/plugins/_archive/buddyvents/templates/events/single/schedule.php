<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

bpe_load_template( 'events/includes/event-header' );

if( bpe_has_schedules() ) : ?>

	<?php do_action( 'bpe_single_schedule_before_table' ) ?>

	<table width="100%" border="0">
		<?php while ( bpe_schedules() ) : bpe_the_schedule(); ?>
			
			<?php bpe_check_day_display() ?>

			<tr id="schedule-<?php bpe_schedule_id() ?>" class="schedule-entry">
				<th scope="row"><?php bpe_schedule_start() ?><?php bpe_schedule_end() ?></th>
				<td><span class="description"><?php bpe_schedule_description() ?></span></td>
			</tr>

			<?php do_action( 'bpe_single_schedule_inside_table' ) ?>

		<?php endwhile; ?>
	</table>

	<?php do_action( 'bpe_single_schedule_after_table' ) ?>
<?php endif; ?>