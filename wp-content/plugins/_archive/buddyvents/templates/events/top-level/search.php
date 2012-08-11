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
<h3 class="pagetitle"><?php _e( 'Search', 'events' ) ?><?php if( ! bpe_is_restricted() ) : ?>&nbsp;<a class="button" href="<?php echo bp_get_root_domain() . '/' . bpe_get_base( 'root_slug' ) . '/'. bpe_get_option( 'create_slug' ) ?>"><?php _e( 'Create Event', 'events' ) ?></a><?php endif; ?></h3>

<div id="event-dir-search" class="dir-search no-ajax">
    <?php bpe_directory_events_search_form() ?>
</div>

<?php do_action( 'template_notices' ) ?>

<?php do_action( 'bpe_before_directory_events_content' ) ?>

<?php bpe_load_template( 'events/includes/navigation' ); ?>

<?php do_action( 'bpe_before_search_form' ) ?>

<form action="<?php bpe_directory_events_search_action() ?>" method="post" id="search-events-form" class="standard-form">

    <label for="location"><?php if( ! bpe_loggedin_user_has_location() ) : ?>* <?php endif; ?><?php _e( 'Location', 'events' ) ?></label>
    <input type="text" name="l" id="location" value="<?php bpe_display_cookie( 'loc', true, 'buddyvents_search' ) ?>" /><br />
    <small><?php _e( 'Enter a location you would like to use as the start point of your search.', 'events' ) ?> <?php if( bpe_loggedin_user_has_location() ) : ?><?php _e( 'Leave empty to use your current location.', 'events' ) ?><?php endif; ?></small>

    <label for="radius"><?php _e( '* Search Radius', 'events' ) ?></label>
    <select id="radius" name="r">
    	<?php foreach( bpe_get_config( 'distances' ) as $prox ) : ?>
        <option<?php if( bpe_display_cookie( 'radius', false, 'buddyvents_search' ) == $prox || ! isset( $_COOKIE['buddyvents_search'] ) && $prox == 10 ) echo ' selected="selected"'; ?> value="<?php echo esc_attr( $prox ) ?>"><?php echo $prox .' '. bpe_get_option( 'system' ) ?></option>
		<?php endforeach; ?>
    </select><br />
    <small><?php _e( 'Enter your search radius.', 'events' ) ?></small>

    <label for="search_term"><?php _e( 'Search Term', 'events' ) ?></label>
    <input type="text" name="s" id="search_term" value="<?php bpe_display_cookie( 'term', true, 'buddyvents_search' ) ?>" /><br />
    <small><?php _e( 'Enter a search term to fine-tune your search.', 'events' ) ?></small>
    
    <?php do_action( 'bpe_inside_search_form' ) ?>
   
    <div class="submit">
        <input type="submit" value="<?php _e( 'Search', 'events' ) ?>" id="search-events" name="search-events" />
    </div>

</form>

<?php do_action( 'bpe_after_search_form' ) ?>