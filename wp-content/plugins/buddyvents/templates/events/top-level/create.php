<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */
?>
<h3 class="pagetitle"><?php _e( 'Create Event', 'events' ) ?>&nbsp;<a class="button" href="<?php echo bp_get_root_domain() . '/' . bpe_get_base( 'root_slug' ) . '/' ?>"><?php _e( 'Events Directory', 'events' ) ?></a></h3>

<div id="event-dir-search" class="dir-search no-ajax">
    <?php bpe_directory_events_search_form() ?>
</div>

<?php do_action( 'bpe_before_directory_events_content' ) ?>

<form action="<?php bpe_event_creation_form_action() ?>" method="post" enctype="multipart/form-data" id="create-event-form" class="standard-form">

    <div class="item-list-tabs no-ajax" id="group-create-tabs">
        <ul>
            <?php bpe_event_creation_tabs(); ?>
        </ul>
    </div>

	<?php do_action( 'template_notices' ) ?>
    
    <?php if( bpe_is_event_creation_step( bpe_get_option( 'general_slug' ) ) ) : ?>
    
    	<?php bpe_load_template( 'events/top-level/steps/general' ) ?>
    
	<?php endif; ?>
    
	<?php if( bpe_is_event_creation_step( bpe_get_option( 'logo_slug' ) ) ) : ?>

     	<?php bpe_load_template( 'events/top-level/steps/logo' ) ?>
                    
	<?php endif; ?>

	<?php if( bpe_is_event_creation_step( bpe_get_option( 'invite_slug' ) ) && bpe_get_option( 'enable_attendees' ) === true ) : ?>
    
     	<?php bpe_load_template( 'events/top-level/steps/invite' ) ?>

	<?php endif; ?>
    
    <input type="hidden" name="new_event_id" id="new_event_id" value="<?php echo bpe_get_displayed_event( 'id' ) ?>" />

	<?php do_action( 'bpe_custom_create_steps' ) ?>

	<?php if( bp_get_avatar_admin_step() != 'crop-image' ) : ?>
        <div class="submit" id="previous-next">
            <?php if( ! bpe_is_first_event_creation_step() ) : ?>
                <input type="button" value="&larr; <?php _e( 'Previous Step', 'events' ) ?>" id="event-creation-previous" name="previous" onclick="location.href='<?php bpe_event_creation_previous_link() ?>'" />
            <?php endif; ?>
    
            <?php if( ! bpe_is_last_event_creation_step() && ! bpe_is_first_event_creation_step() ) : ?>
                <input type="submit" value="<?php _e( 'Next Step', 'events' ) ?> &rarr;" id="event-creation-next" name="save-event" />
            <?php endif;?>
    
            <?php if( bpe_is_first_event_creation_step() && ! bpe_is_only_create_step() ) : ?>
                <input type="submit" value="<?php _e( 'Create Event and Continue', 'events' ) ?> &rarr;" id="event-creation-create" name="save-event" />
            <?php endif; ?>
    
            <?php if( bpe_is_last_event_creation_step() ) : ?>
                <input type="submit" value="<?php _e( 'Finish', 'events' ) ?> &rarr;" id="event-creation-finish" name="save-event" />
            <?php endif; ?>
        </div>
	<?php endif; ?>
</form><!-- #create-event-form -->