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

class Buddyvents_Forum_Tags extends WP_Widget
{
	function __construct()
	{
		$widget_ops = array( 'classname' => 'widget_event_forum_tags', 'description' => __( 'Displays event forum topic tags on event forum pages only.', 'events' ) );
		$this->WP_Widget( 'event_forum_tags', __( 'Event Forum Tags', 'events' ), $widget_ops );
	}

	function widget( $args, $instance )
	{
		if( ! bpe_is_forum() )
			return false;

		extract( $args );

		$forum_id  = bpe_get_eventmeta( bpe_get_displayed_event( 'id' ), 'forum_id' );
		$topic_ids = bbp_forum_query_topic_ids( $forum_id );	
		$ids 	   = wp_get_object_terms( $topic_ids, bbp_get_topic_tag_tax_id(), array( 'fields' => 'ids' ) );
		$title 	   = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Topic Tags', 'events' ) : $instance['title'], $instance, $this->id_base );
		
		$cloud = wp_tag_cloud( apply_filters( 'bpe_forums_tag_cloud', array(
			'taxonomy' => bbp_get_topic_tag_tax_id(),
			'include'  => array_unique( (array)$ids ),
			'echo' 	   => false
		) ) );
		
		if( ! empty( $cloud ) ) :
			echo $before_widget;
	
				if( $title )
					echo $before_title . $title . $after_title;
				
				echo '<div class="event-tag-cloud">'. $cloud .'</div>';
			echo $after_widget;
		endif;
	}

	function update( $new_instance, $old_instance )
	{
		$instance = $old_instance;
		
		$instance['title'] 	= wp_filter_kses( $new_instance['title'] );

		return $instance;
	}

	function form( $instance )
	{
		$instance 	= wp_parse_args( (array) $instance, array( 'title' => 'Topic Tags' ) );
		$title  	= esc_attr( $instance['title'] );
		?>
		<p>
        	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'events' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <?php
	}
}
add_action( 'widgets_init', create_function( '', 'return register_widget("Buddyvents_Forum_Tags");' ) );
?>