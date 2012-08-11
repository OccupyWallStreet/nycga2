<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

if( bpe_has_events() ) : ?>
    
    <?php do_action( 'bpe_group_approve_before_loop' ) ?>

	<table id="approve-events" class="messages-notices">
    <thead>
        <tr>
            <th width="1%" scope="col"><input type="checkbox" onclick="checkAll(document.getElementById('group-settings-form'));"></th>
            <th width="1%" scope="col">&nbsp;</th>
            <th width="18%" scope="col"><?php _e( 'Title', 'events' ); ?></th>
            <th width="1%" scope="col">&nbsp;</th>
            <th width="18%" scope="col"><?php _e( 'Creator', 'events' ); ?></th>
            <th width="18%" scope="col"><?php _e( 'Category', 'events' ); ?></th>
            <th width="18%" scope="col"><?php _e( 'Date created', 'events' ); ?></th>
            <th width="18%" scope="col"><?php _e( 'Actions', 'events' ); ?></th>
        </tr>
    </thead>
	<?php while ( bpe_events() ) : bpe_the_event();	?>
        <tr class="<?php bpe_approve_css_class() ?>">
        	<td width="1%"><input name="be[]" type="checkbox" value="<?php bpe_event_id() ?>"></td>
            <td width="1%" scope="col"><?php bpe_event_image_thumb() ?></td>
            <td width="18%"><a class="toggle-desc" href="#"><?php bpe_event_name() ?></a></td>
            <td width="1%"><?php echo bp_core_fetch_avatar( array( 'item_id' => bpe_get_event_user_id(), 'type' => 'thumb' ) ) ?></td>
            <td width="18%"><?php echo bp_core_get_userlink( bpe_get_event_user_id() ) ?></td>
            <td width="18%"><?php bpe_event_category() ?></td>
            <td width="18%"><span class="activity"><?php echo bp_format_time( strtotime( bpe_get_event_date_created_be() ) ) ?></span></td>
            <td width="18%">
                <a class="button approve" href="<?php echo wp_nonce_url( bp_get_group_permalink( groups_get_current_group() ) .'admin/'. bpe_get_option( 'approve_slug' ) .'/approved/'. bpe_get_event_id() .'/', 'bpe_approved_event' ) ?>"><?php _e( 'Approve', 'events' ); ?></a>
                <a class="button decline" href="<?php echo wp_nonce_url( bp_get_group_permalink( groups_get_current_group() ) .'admin/'. bpe_get_option( 'approve_slug' ) .'/declined/'. bpe_get_event_id() .'/', 'bpe_declined_event' ) ?>"><?php _e( 'Decline', 'events' ); ?></a>
            </td>
        </tr>
        <tr class="bpe-desc <?php bpe_approve_css_class() ?>">
            <td colspan="8"><?php bpe_event_description_raw() ?></td>
        </tr>
	<?php endwhile; ?>
	</table>

	<div id="pag-bottom" class="pagination no-ajax">

		<div class="pag-count" id="events-count">
			<?php bpe_events_pagination_count() ?>
		</div>

		<div class="pagination-links" id="events-pag">
			<?php bpe_events_pagination_links() ?>
		</div>

	</div>

	<div class="submit">
        <select id="bulkapprove" name="bulkapprove">
            <option value="-1" selected="selected"><?php _e( 'Bulk Actions', 'events' ) ?></option>
            <option value="approve"><?php _e( 'Approve', 'events' ); ?></option>
            <option value="del"><?php _e( 'Decline', 'events' ); ?></option>
        </select>
		<input type="submit" id="save" name="save" value="<?php _e( 'Apply', 'events' ) ?>" />
    </div>

	<script type="text/javascript">
	function checkAll(form) {
		for (i = 0, n = form.elements.length; i < n; i++) {
			if(form.elements[i].type == "checkbox") {
				if(form.elements[i].name == "be[]") {
					if(form.elements[i].checked == true)
						form.elements[i].checked = false;
					else
						form.elements[i].checked = true;
				}
			}
		}
		i = null;
	}
    jQuery(document).ready(function(){
        jQuery('.toggle-desc').click(function(){
            jQuery(this).parent().parent().next('.bpe-desc').toggle();
            return false;
        });
    });
    </script>

    <?php do_action( 'bpe_group_approve_after_loop' ) ?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'No events to approve.', 'events' ) ?></p>
	</div>

<?php endif; ?>