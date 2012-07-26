<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

// No direct access is allowed
if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display notifications
 *
 * @package Core
 * @since 	1.2.5
 */
function bpe_format_notifications( $action, $item_id, $secondary_item_id, $total_items, $format = 'string'  )
{
	switch ( $action )
	{
		case 'event_notification':
			if( $total_items > 1 )
				$text = sprintf( __( 'There are %d new events that meet your criteria.', 'events' ), (int)$total_items ) ;
			else
				$text = sprintf( __( 'There is %d new event that meets your criteria.', 'events' ), (int)$total_items ) ;

				return '<a href="'. bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/" title="' . __( 'New events', 'events' ) . '">' . $text . '</a>';
		break;
	}

	do_action( 'bpe_format_notifications', $action, $item_id, $secondary_item_id, $total_items );

	return false;
}

/**
 * Sanitize a string for keyword use
 *
 * @package Core
 * @since 	1.2.5
 */
function bpe_sanitize_for_keywords( $string, $asteriks = true )
{
	$string = strtolower( $string );
	$string = bpe_strip_words( $string );
	$string = wp_strip_all_tags( $string );
	$string = bpe_strip_punctuation( $string );
	$string = normalize_whitespace( $string );
	
	if( $asteriks === true )
		$string = bpe_add_asteriks( $string );
	
	return apply_filters( 'bpe_sanitize_for_keywords', $string );	
}

/**
 * Get rid of punctuation
 * @link 	http://nadeausoftware.com/articles/2007/9/php_tip_how_strip_punctuation_characters_web_page
 *
 * @package Core
 * @since 	1.2.5
 */
function bpe_strip_punctuation( $string )
{
	return preg_replace( array(
		'/[\p{Z}\p{Cc}\p{Cf}\p{Cs}\p{Pi}\p{Pf}]/u',
		'/\p{Po}(?<![\'"\*<>\x{002E}\x{FE52}\x{FF0E}\x{002C}\x{FE50}\x{FF0C}\x{066B}\x{066C}\.,:;\'\-_\*%@&\/\\\\\?!#\[\]\(\)\x{0023}\x{FE5F}\x{FF03}\x{066A}\x{0025}\x{066A}\x{FE6A}\x{FF05}\x{2030}\x{2031}\x{2032}\x{2033}\x{2034}\x{2057}])/u',
		'/[\p{Ps}\p{Pe}](?<![\[\]\(\)])/u',
		'/[\'"\*<>\x{002E}\x{FE52}\x{FF0E}\x{002C}\x{FE50}\x{FF0C}\x{066B}\x{066C}\.,:;\'\-_\*@&\/\\\\\?!#\[\]\(\)\p{Pd}\p{Pc}]+((?= )|$)/u',
		'/((?<= )|^)[\'"\*<>:;\'_\*%@&?!\[\]\(\)\p{Pc}]+/u', '/((?<= )|^)\p{Pd}+(?![\p{N}\p{Sc}])/u',
		'/ +/'
	), ' ', $string );
}

/**
 * List of english stop words
 *
 * @package Core
 * @since 	1.2.5
 */
function bpe_strip_words( $string )
{
    $strip = array( "-", "--", "a", "able", "about", "above", "abroad", "according", "accordingly", "across", "actually", "adj", "after", "afterwards", "again", "against", "ago", "ahead", "ain't", "all", "allow", "allows", "almost", "alone", "along", "alongside", "already", "also", "although", "always", "am", "amid", "amidst", "among", "amongst", "an", "and", "another", "any", "anybody", "anyhow", "anyone", "anything", "anyway", "anyways", "anywhere", "apart", "appear", "appreciate", "appropriate", "are", "aren't", "around", "as", "a's", "aside", "ask", "asking", "associated", "at", "available", "away", "awfully", "b", "back", "backward", "backwards", "be", "became", "because", "become", "becomes", "becoming", "been", "before", "beforehand", "begin", "behind", "being", "believe", "below", "beside", "besides", "best", "better", "between", "beyond", "both", "brief", "but", "by", "c", "came", "can", "cannot", "cant", "can't", "caption", "cause", "causes", "certain", "certainly", "changes", "clearly", "c'mon", "co", "co.", "com", "come", "comes", "concerning", "consequently", "consider", "considering", "contain", "containing", "contains", "corresponding", "could", "couldn't", "course", "c's", "currently", "d", "dare", "daren't", "definitely", "described", "despite", "did", "didn't", "different", "directly", "do", "does", "doesn't", "doing", "done", "don't", "down", "downwards", "during", "e", "each", "edu", "eg", "eight", "eighty", "either", "else", "elsewhere", "end", "ending", "enough", "entirely", "especially", "et", "etc", "even", "ever", "evermore", "every", "everybody", "everyone", "everything", "everywhere", "ex", "exactly", "example", "except", "f", "fairly", "far", "farther", "few", "fewer", "fifth", "first", "five", "followed", "following", "follows", "for", "forever", "former", "formerly", "forth", "forward", "found", "four", "from", "further", "furthermore", "g", "get", "gets", "getting", "given", "gives", "go", "goes", "going", "gone", "got", "gotten", "greetings", "h", "had", "hadn't", "half", "happens", "hardly", "has", "hasn't", "have", "haven't", "having", "he", "he'd", "he'll", "hello", "help", "hence", "her", "here", "hereafter", "hereby", "herein", "here's", "hereupon", "hers", "herself", "he's", "hi", "him", "himself", "his", "hither", "hopefully", "how", "howbeit", "however", "hundred", "i", "i'd", "ie", "if", "ignored", "i'll", "i'm", "immediate", "in", "inasmuch", "inc", "inc.", "indeed", "indicate", "indicated", "indicates", "inner", "inside", "insofar", "instead", "into", "inward", "is", "isn't", "it", "it'd", "it'll", "its", "it's", "itself", "i've", "j", "just", "k", "keep", "keeps", "kept", "know", "known", "knows", "l", "last", "lately", "later", "latter", "latterly", "least", "less", "lest", "let", "let's", "like", "liked", "likely", "likewise", "little", "look", "looking", "looks", "low", "lower", "ltd", "m", "made", "mainly", "make", "makes", "many", "may", "maybe", "mayn't", "me", "mean", "meantime", "meanwhile", "merely", "might", "mightn't", "mine", "minus", "miss", "more", "moreover", "most", "mostly", "mr", "mrs", "much", "must", "mustn't", "my", "myself", "n", "name", "namely", "nd", "near", "nearly", "necessary", "need", "needn't", "needs", "neither", "never", "neverf", "neverless", "nevertheless", "new", "next", "nine", "ninety", "no", "nobody", "non", "none", "nonetheless", "noone", "no-one", "nor", "normally", "not", "nothing", "notwithstanding", "novel", "now", "nowhere", "o", "obviously", "of", "off", "often", "oh", "ok", "okay", "old", "on", "once", "one", "ones", "one's", "only", "onto", "opposite", "or", "other", "others", "otherwise", "ought", "oughtn't", "our", "ours", "ourselves", "out", "outside", "over", "overall", "own", "p", "particular", "particularly", "past", "per", "perhaps", "placed", "please", "plus", "possible", "presumably", "probably", "provided", "provides", "q", "que", "quite", "qv", "r", "rather", "rd", "re", "really", "reasonably", "recent", "recently", "regarding", "regardless", "regards", "relatively", "respectively", "right", "round", "s", "said", "same", "saw", "say", "saying", "says", "second", "secondly", "see", "seeing", "seem", "seemed", "seeming", "seems", "seen", "self", "selves", "sensible", "sent", "serious", "seriously", "seven", "several", "shall", "shan't", "she", "she'd", "she'll", "she's", "should", "shouldn't", "since", "six", "so", "some", "somebody", "someday", "somehow", "someone", "something", "sometime", "sometimes", "somewhat", "somewhere", "soon", "sorry", "specified", "specify", "specifying", "still", "sub", "such", "sup", "sure", "t", "take", "taken", "taking", "tell", "tends", "th", "than", "thank", "thanks", "thanx", "that", "that'll", "thats", "that's", "that've", "the", "their", "theirs", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "there'd", "therefore", "therein", "there'll", "there're", "theres", "there's", "thereupon", "there've", "these", "they", "they'd", "they'll", "they're", "they've", "thing", "things", "think", "third", "thirty", "this", "thorough", "thoroughly", "those", "though", "three", "through", "throughout", "thru", "thus", "till", "to", "together", "too", "took", "toward", "towards", "tried", "tries", "truly", "try", "trying", "t's", "twice", "two", "u", "un", "under", "underneath", "undoing", "unfortunately", "unless", "unlike", "unlikely", "until", "unto", "up", "upon", "upwards", "us", "use", "used", "useful", "uses", "using", "usually", "v", "value", "various", "versus", "very", "via", "viz", "vs", "w", "want", "wants", "was", "wasn't", "way", "we", "we'd", "welcome", "well", "we'll", "went", "were", "we're", "weren't", "we've", "what", "whatever", "what'll", "what's", "what've", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "where's", "whereupon", "wherever", "whether", "which", "whichever", "while", "whilst", "whither", "who", "who'd", "whoever", "whole", "who'll", "whom", "whomever", "who's", "whose", "why", "will", "willing", "wish", "with", "within", "without", "wonder", "won't", "would", "wouldn't", "x", "y", "yes", "yet", "you", "you'd", "you'll", "your", "you're", "yours", "yourself", "yourselves", "you've", "z", "zero");
	
	$strip = apply_filters( 'bpe_stopwords', $strip );
	
	return preg_replace( '/\b('. implode( '|', $strip ) .')\b/', '', $string );
}

/**
 * Add a wildcard to each word
 *
 * @package Core
 * @since 	1.2.5
 */
function bpe_add_asteriks( $string )
{
	$array = explode( ' ', $string );
	$arr = array();
	
	foreach( (array)$array as $word )
		$arr[] = $word .'*';
		
	return implode( ' ', (array)$arr );
}

/**
 * Send email notifications
 *
 * @package Core
 * @since 	1.2.5
 */
function bpe_send_email_notifications( $uids, $event )
{
	global $bp, $bpe;
	
	if( ! bp_is_active( 'settings' ) )
		return false;

	$subject = '['. get_blog_option( Buddyvents::$root_blog, 'blogname' ) .'] '. __( 'New upcoming event!', 'events' );
	
	foreach( $uids as $id ) :
		$reciever = bp_core_get_user_displayname( $id, false );
		$data = get_userdata( $id );
		
		$events_link = site_url( bpe_get_base( 'root_slug' ) .'/'. bpe_get_event_slug( $event ) );
		$events_settings_link = site_url( $bp->members->root_slug . '/' . $data->user_login . '/'. bp_get_settings_slug() .'/'. bpe_get_base( 'slug' ) .'/' );

		$message = sprintf( __( "Hello %s,\n\na new event has beeen published that fits your specified keywords.\n\nName:\n%s\n\nLink:\n%s\n\nLocation:\n%s\n\nTo disable these notifications or modify your keywords please log in and go to:\n%s", 'events' ), $reciever, bpe_get_event_name( $event ), $events_link, bpe_get_event_location( $event ), $events_settings_link );
		
		wp_mail( $data->user_email, $subject, $message );
	endforeach;
}

/**
 * Send email reminders
 *
 * @package Core
 * @since 	1.3
 */
function bpe_send_email_reminders( $event_id )
{
	global $bp, $bpe;
	
	if( ! bp_is_active( 'settings' ) )
		return false;
	
	$event = new Buddyvents_Events( $event_id );
	
	if( ! bpe_get_event_id( $event ) )
		return false;

	$subject = '['. get_blog_option( Buddyvents::$root_blog, 'blogname' ) .'] '. __( 'Event reminder!', 'events' );
	
	$uids = bpe_get_attendee_ids( bpe_get_event_id( $event ) );
	
	// remove the event admin from the list
	if( is_numeric( $key = array_search( bpe_get_event_user_id( $event ), (array)$uids ) ) )
		unset( $uids[$key] );

	foreach( (array)$uids as $id ) :
		$reciever = bp_core_get_user_displayname( $id, false );
		$data = get_userdata( $id );

		$events_link = site_url( bpe_get_base( 'root_slug' ) .'/'. bpe_get_event_slug( $event ) );
		$events_settings_link = site_url( $bp->members->root_slug . '/' . $data->user_login . '/'. bp_get_settings_slug() .'/'. bpe_get_base( 'slug' ) .'/' );

		$message = sprintf( __( "Hello %s,\n\nthis email is being sent as a reminder for an event that will start tomorrow.\n\nName:\n%s\n\nLink:\n%s\n\nLocation:\n%s\n\nTo disable these notifications please log in and go to:\n%s", 'events' ), $reciever, bpe_get_event_name( $event ), $events_link, bpe_get_event_location( $event ), $events_settings_link );
		
		wp_mail( $data->user_email, $subject, $message );
	endforeach;
}
add_action( 'bpe_create_email_event_reminder_action', 'bpe_send_email_reminders' );

/**
 * Get and save the reminder timestamp
 *
 * @package Core
 * @since 	1.3
 */
function bpe_get_reminder_timestamp( $event )
{
	$stamp = ( ! bpe_get_option( 'timestamp' ) ) ? '-1 day' : bpe_get_option( 'timestamp' );
	
	$timestamp = strtotime( $stamp, strtotime( bpe_get_event_start_date_raw( $event ) .' '. bpe_get_event_start_time_raw( $event ) ) );
	
	bpe_update_eventmeta( bpe_get_event_id( $event ), 'bpe_reminder_timestamp', $timestamp );
	
	return $timestamp;
}

/**
 * Screen function for event settings
 *
 * @package Core
 * @since 	1.2.5
 */
function bpe_events_settings()
{
	global $bp_settings_updated;
	
	if( ! bp_is_active( 'settings' ) )
		return false;

	$bp_settings_updated = false;

	if( isset( $_POST['submit'] ) )
	{
		check_admin_referer( 'bpe_settings_events' );
		
		$data = $_POST['bpe'];
		$id = ( empty( $_POST['id'] ) ) ? null : $_POST['id'];
		
		// automatically deactivate if keywords are empty
		$keywords = '';
		
		if( empty( $bpe['keywords'] ) )
		{
			$data['email'] 	  = 0;
			$data['screen']   = 0;
			$data['keywords'] = '';
		}
		else
		{
			$keywords = strtolower( $data['keywords'] );
			$keywords = str_replace( array( ',', '.' ), ' ', $keywords );
			$keywords = normalize_whitespace( $keywords );
		}
		
		do_action( 'bpe_event_settings_save_extra', bp_loggedin_user_id() );

		// save to db
		bpe_add_notification( $id, bp_loggedin_user_id(), $keywords, $data['email'], $data['screen'], $data['remind'] );

		$bp_settings_updated = true;
	}

	add_action( 'bp_template_title', 'bpe_events_settings_title' );
	add_action( 'bp_template_content', 'bpe_events_settings_content' );

	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

/**
 * Screen function for event settings title
 *
 * @package Core
 * @since 	1.2.5
 */
function bpe_events_settings_title() 
{
	echo '<h3>'. __( 'Event Settings', 'events' ) .'</h3>';
}

/**
 * Screen function for event settings content
 *
 * @package Core
 * @since 	1.2.5
 */
function bpe_events_settings_content()
{
	global $bp, $bp_settings_updated;
	
	$note = new Buddyvents_Notifications( null, bp_loggedin_user_id() );
	?>
    
    <p><?php _e( 'By entering keywords and activating notifications you will be notified of any upcoming events matching your keywords.', 'events' ) ?></p>

	<?php if ( $bp_settings_updated ) { ?>
		<div id="message" class="updated fade">
			<p><?php _e( 'Changes Saved.', 'events' ) ?></p>
		</div>
	<?php } ?>

	<form action="<?php echo bp_loggedin_user_domain() . bp_get_settings_slug() . '/'. bpe_get_base( 'slug' ) .'/' ?>" method="post" id="settings-form" class="standard-form">

		<?php wp_nonce_field( 'bpe_settings_events' ) ?>
    	<input type="hidden" name="id" value="<?php echo $note->id ?>" />
        <table class="notification-settings zebra" id="bpe-event-settings">
        	<thead>
                <tr>
                    <th class="icon"></th>
                    <th class="title"><?php _e( 'Notifications', 'events' ) ?></th>
                    <th class="yes"><?php _e( 'Yes', 'events' ) ?></th>
                    <th class="no"><?php _e( 'No', 'events' )?></th>
                </tr>
            </thead>
            <tbody>
                <tr id="email-keywords">
                    <td></td>
                    <td><?php _e( 'Do you want to get notified by email when an event gets published that matches your keywords?', 'events' ) ?></td>
                    <td class="yes"><input type="radio" name="bpe[email]" value="1"<?php if( $note->email == 1 ) echo ' checked="checked"'; ?> /></td>
                    <td class="no"><input type="radio" name="bpe[email]" value="0"<?php if( $note->email == 0 || empty( $note->email ) ) echo ' checked="checked"'; ?> /></td>
                </tr>
                <tr id="screen-keywords">
                    <td></td>
                    <td><?php _e( 'Do you want to get notified by screen when an event gets published that matches your keywords?', 'events' ) ?></td>
                    <td class="yes"><input type="radio" name="bpe[screen]" value="1"<?php if( $note->screen == 1 ) echo ' checked="checked"'; ?> /></td>
                    <td class="no"><input type="radio" name="bpe[screen]" value="0"<?php if( $note->screen == 0 || empty( $note->screen ) ) echo ' checked="checked"'; ?> /></td>
                </tr>
                <tr id="event-reminder">
                    <td></td>
                    <td><?php _e( 'Do you want to be reminded of events you signed up for?', 'events' ) ?></td>
                    <td class="yes"><input type="radio" name="bpe[remind]" value="1"<?php if( $note->remind == 1 ) echo ' checked="checked"'; ?> /></td>
                    <td class="no"><input type="radio" name="bpe[remind]" value="0"<?php if( $note->remind == 0 || empty( $note->remind ) ) echo ' checked="checked"'; ?> /></td>
                </tr>
    		</tbody>
            <?php do_action( 'bpe_event_settings_action' ); ?>
        </table>
        
        <p>
        	<label for="bpe-keywords"><?php _e( '* Keywords', 'events' ) ?></label>
            <input type="text" name="bpe[keywords]" id="bpe-keywords" value="<?php echo $note->keywords ?>" /><br />
            <small><?php _e( 'Enter a space seperated list of lowercase keywords.', 'events' ) ?></small>
        </p>
        
        <?php do_action( 'bpe_event_settings_action_end', bp_loggedin_user_id()  ); ?>

		<div class="submit">
			<p><input type="submit" name="submit" value="<?php _e( 'Save Changes', 'events' ) ?>" id="submit" class="auto"/></p>
		</div>
	</form>
	<?php
}

/**
 * Send screen notifications
 *
 * @package Core
 * @since 	1.2.5
 */
function bpe_send_screen_notifications( $uids, $event )
{
	global $bp;
	
	foreach( $uids as $id ) :
		bp_core_add_notification( bpe_get_event_id( $event ), $id, bpe_get_base( 'id' ), 'event_notification' );
	endforeach;
}

/**
 * Send approval/denial emails
 *
 * @package Core
 * @since 	2.0
 */
function bpe_send_approval_status_mail( $event = false, $type = 'approved' )
{
	if( ! bpe_get_event_user_id( $event ) || ! bpe_get_event_id( $event ) || empty( $type ) )
		return false;
		
	switch( $type )
	{
		case 'approved':
			$message = sprintf( __( "Hello %s,\n\nwe are happy to inform you that your event %s has been approved and can now be viewed and managed here:\n%s\n\nYour %s Team", 'events' ), bp_core_get_user_displayname( bpe_get_event_user_id( $event ) ), bpe_get_event_name( $event ), bpe_get_event_link( $event ), get_bloginfo( 'name' ) );
			break;

		case 'deleted':
			$message = sprintf( __( "Hello %s,\n\nwe are sorry to inform you that your event %s has been declined and has therefore been deleted.\n\nYour %s Team", 'events' ), bp_core_get_user_displayname( bpe_get_event_user_id( $event ) ), bpe_get_event_name( $event ), get_bloginfo( 'name' ) );
			break;
	}
	
	wp_mail( bp_core_get_user_email( bpe_get_event_user_id( $event ) ), sprintf( __( '[%s] Status change for %s', 'events' ), get_bloginfo( 'name' ), bpe_get_event_name( $event ) ), $message );
}

/**
 * Notify the site admin about a new event
 *
 * @package Core
 * @since 	1.5
 */
function bpe_send_approve_mail( $event )
{
	$to 	 = get_blog_option( Buddyvents::$root_blog, 'admin_email' );
	$subject = '['. get_blog_option( Buddyvents::$root_blog, 'blogname' ) .'] '. __( 'New event to approve', 'events' );
	$message = sprintf( __( "Hello,\n\nan event is waiting to be approved.\n\nName:\n%s\n\nDescription:\n%s\n\nLocation:\n%s\n\nPlease follow the link below to deal with this event:\n%s", 'events' ),
								bpe_get_event_name( $event ), 
								bpe_get_event_description_excerpt_raw( $event ), 
								bpe_get_event_location( $event ), 
								admin_url( 'admin.php?page='. EVENT_FOLDER .'-approve' )
						);

	wp_mail( $to, $subject, $message );
}

/*****************************************************
 *       MESSAGE COMPATIBILITY FUNCTIONS BELOW       *
 *****************************************************/

if( ! function_exists( 'messages_new_message' ) ) :
	/**
	 * If message component is disabled
	 *
	 * @package Core
	 * @since 	1.5
	 */
	function messages_new_message( $args = '' )
	{
		$defaults = array(
			'thread_id' 	=> false,
			'sender_id' 	=> bp_loggedin_user_id(),
			'recipients' 	=> false,
			'subject' 		=> false,
			'content' 		=> false,
			'date_sent' 	=> false
		);
	
		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );
	
		if( ! $sender_id || ! $content || empty( $recipients ) )
			return false;
	
		if( $key = array_search( $sender_id, (array)$recipients ) )
			unset( $recipients[$key] );
	
		$recipient_ids = array_unique( (array)$recipients );
	
		if ( empty( $recipient_ids ) )
			return false;
	
		foreach( (array)$recipient_ids as $i => $recipient_id ) :
			wp_mail( bp_core_get_user_email( $recipient_id ), $subject, $content );
		endforeach;
	}
endif;

if( ! function_exists( 'bp_message_get_recipient_usernames' ) ) :
	/**
	 * If message component is disabled
	 *
	 * @package Core
	 * @since 	1.5
	 */
	function bp_message_get_recipient_usernames()
	{
		echo bp_get_message_get_recipient_usernames();
	}
endif;

if( ! function_exists( 'bp_get_message_get_recipient_usernames' ) ) :
	/**
	 * If message component is disabled
	 *
	 * @package Core
	 * @since 	1.5
	 */
	function bp_get_message_get_recipient_usernames()
	{
		$recipients = isset( $_GET['r'] ) ? stripslashes( $_GET['r'] ) : '';
	
		return apply_filters( 'bp_get_message_get_recipient_usernames', $recipients );
	}
endif;

if( ! function_exists( 'bp_message_get_recipient_tabs' ) ) :
	/**
	 * If message component is disabled
	 *
	 * @package Core
	 * @since 	1.5
	 */
	function bp_message_get_recipient_tabs( $args = '' )
	{
		$recipients = explode( ' ', bp_get_message_get_recipient_usernames() );
	
		foreach ( $recipients as $recipient ) {
			$user_id = bp_is_username_compatibility_mode() ? bp_core_get_userid( $recipient ) : bp_core_get_userid_from_nicename( $recipient );
	
			if ( $user_id ) : ?>
	
				<li id="un-<?php echo esc_attr( $recipient ); ?>" class="friend-tab">
					<span><?php
						echo bp_core_fetch_avatar( array( 'item_id' => $user_id, 'type' => 'thumb', 'width' => 15, 'height' => 15 ) );
						echo bp_core_get_userlink( $user_id );
					?></span>
				</li>
	
			<?php endif;
		}
	}
endif;
?>