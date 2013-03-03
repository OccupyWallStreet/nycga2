<?php

function pulse_press_body_class( $classes ) {
	if ( is_tax( 'mentions' ) )
		$classes[] = 'mentions';

	return $classes;
}
add_filter( 'body_class', 'pulse_press_body_class' );
function pulse_press_no_sidebar($classes) {
	if(pulse_press_get_option( 'hide_sidebar'))
		$classes[] = 'no-sidebar';
	
	return $classes;
}
add_filter( 'body_class', 'pulse_press_no_sidebar' );

function pulse_press_user_can_post() {
	global $user_ID;
	if(pulse_press_get_option( 'remove_frontend_post'))
		return false;
		
	if ( current_user_can( 'publish_posts' ) || ( pulse_press_get_option( 'allow_users_publish' ) && current_user_can('read') )  )
		return true;

	return false;
}
function pulse_press_user_can_vote() {
	global $user_ID;
		
	if ( current_user_can( 'publish_posts' ) || ( pulse_press_get_option( 'allow_users_publish' ) && current_user_can('read') )  )
		return true;

	return false;
}

function pulse_press_show_comment_form() {
	global $post, $form_visible;
	if(!is_object($post))
		return false;
	$show = ( !isset( $form_visible ) || !$form_visible ) && 'open' == $post->comment_status;

	if ( $show )
		$form_visible = true;

	return $show;
}

function pulse_press_is_ajax_request() {
	global $post_request_ajax;

	return ( $post_request_ajax ) ? $post_request_ajax : false;
}

function pulse_press_posting_type() {
	echo pulse_press_get_posting_type();
}
function pulse_press_get_posting_type() {
	$p = isset( $_GET['p'] ) ? $_GET['p'] : 'status';
	return $p;
}


function pulse_press_user_display_name() {
	echo pulse_press_get_user_display_name();
}
	function pulse_press_get_user_display_name() {
		global $current_user;

		return apply_filters( 'pulse_press_get_user_display_name', isset( $current_user->first_name ) && $current_user->first_name ? $current_user->first_name : $current_user->display_name );
	}

function pulse_press_user_avatar( $args = '' ) {
	echo pulse_press_get_user_avatar( $args );
}
	function pulse_press_get_user_avatar( $args = '' ) {
		global $current_user;

		$defaults = array(
			'user_id' => false,
			'email' => ( isset( $current_user->user_email ) ) ? $current_user->user_email : '',
			'size' => 48
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		if ( !$user_id )
			$avatar = get_avatar( $email, $size );
		else
			$avatar = get_avatar( $user_id, $size );

	 	return apply_filters( 'pulse_press_get_user_avatar', $avatar, $r );
	}

function pulse_press_discussion_links() {
	echo pulse_press_get_discussion_links();
}
	function pulse_press_get_discussion_links() {
		global $post;
		$content = '';

		$comments = get_comments( array( 'post_id' => $post->ID ) );

		foreach ( $comments as $comment )
			$unique_commentors[$comment->comment_author_email] = $comment;

		$total_unique_commentors = count( $unique_commentors );

		$counter = 1;
		foreach ($unique_commentors as $comment) {
			if ( $counter > 3 )
				break;

			if ( 1 != $counter && $total_unique_commentors == $counter )
				$content .= __( ', and ', 'pulse_press' );
			else if ( 1 != $counter )
				$content .= ', ';

			$content .= get_avatar( $comment, 16 ) . ' ';

			if ( $comment->user_id )
				$content .= '<a href="' . site_url( 'author/' . esc_attr( $comment->comment_author ) ) . '">' . esc_attr( $comment->comment_author ) . '</a>';
			else {
				if ( $comment->comment_author_url )
					$content .= '<a href="' . esc_attr( $comment->comment_author_url ) . '">' . esc_attr( $comment->comment_author ) . '</a>';
				else
					$content .= esc_attr( $comment->comment_author );
			}

			$counter++;
		}

		if ( $total_unique_commentors > 3 )
			if ( ( $total_unique_commentors - 3 ) != 1 )
				$content .= sprintf( __( ' and %s others are discussing.', 'pulse_press' ), ( $total_unique_commentors - 3 ) );
			else
				$content .= __( ' and one other person are discussing.', 'pulse_press' );
		else {
			if ( $total_unique_commentors == 1 )
				$content .= __( ' is discussing.', 'pulse_press' );
			else
				$content .= __( ' are discussing.', 'pulse_press' );
		}

		return $content;
	}

function pulse_press_quote_content() {
	echo pulse_press_get_quote_content();
}
	function pulse_press_get_quote_content() {
		return apply_filters( 'pulse_press_get_quote_content', get_the_content( __( '(More ...)' , 'pulse_press' ) ) );
	}
	add_filter( 'pulse_press_get_quote_content', 'pulse_press_quote_filter_kses', 1 );
	add_filter( 'pulse_press_get_quote_content', 'wptexturize' );
	add_filter( 'pulse_press_get_quote_content', 'convert_smilies' );
	add_filter( 'pulse_press_get_quote_content', 'convert_chars' );
	add_filter( 'pulse_press_get_quote_content', 'prepend_attachment' );
	add_filter( 'pulse_press_get_quote_content', 'make_clickable' );

	function pulse_press_quote_filter_kses( $content ) {
		global $allowedtags;

		$quote_allowedtags = $allowedtags;
		$quote_allowedtags['cite'] = array();
		$quote_allowedtags['p'] = array();

		return wp_kses( $content, $quote_allowedtags );
	}

function pulse_press_the_category() {
	echo pulse_press_get_the_category();
}
	function pulse_press_get_the_category() {
		$categories = get_the_category();
		$slug = ( isset( $categories[0] ) ) ? $categories[0]->slug : '';
		return apply_filters( 'pulse_press_get_the_category', $slug );
	}

function pulse_press_user_prompt() {
	echo pulse_press_get_user_prompt();
}
	function pulse_press_get_user_prompt() {
		$prompt = pulse_press_get_option( 'prompt_text' );

		return apply_filters( 'pulse_press_get_user_prompt', sprintf ( __( 'Hi, %s. %s', 'pulse_press' ), esc_html( pulse_press_get_user_display_name() ), ( $prompt != '' ) ? stripslashes( $prompt ) : __( 'What&rsquo;s happening?', 'pulse_press' ) ) );
	}

function pulse_press_page_number() {
	echo pulse_press_get_page_number();
}

function pulse_press_get_page_number() {
	global $paged;
	return apply_filters( 'pulse_press_get_page_number', $paged );
}


function pulse_press_get_hide_sidebar() {
	return ( pulse_press_get_option( 'hide_sidebar' ) ) ? true : false;
}

function pulse_press_author_id() {
	echo pulse_press_get_author_id();
}
	function pulse_press_get_author_id() {
		global $authordata;
		return apply_filters( 'pulse_press_get_author_id', $authordata->ID );
	}
function pulse_press_archive_author() {
	echo pulse_press_get_archive_author();
}

function pulse_press_get_archive_author() {

	if ( get_query_var( 'author_name' ) )
	 		$curauth = get_userdatabylogin( get_query_var( 'author_name' ) );
	else
	 		$curauth = get_userdata( get_query_var( 'author' ) );

	if ( isset( $curauth->display_name ) )
		return apply_filters( 'pulse_press_get_archive_author', $curauth->display_name );
}

function pulse_press_author_name() {
	echo pulse_press_get_author_name();
}
	function pulse_press_get_author_name() {
		global $authordata;

		if ( isset( $authordata->display_name ) )
			return apply_filters( 'pulse_press_get_author_name', $authordata->display_name );
	}

function pulse_press_mention_name() {
	echo pulse_press_get_mention_name();
}
	function pulse_press_get_mention_name() {
		$name = '';
		$mention_name = get_query_var( 'term' );
		$name_map = pulse_press_get_at_name_map();

		if ( isset( $name_map["@$mention_name"] ) )
			$name = get_userdata( $name_map["@$mention_name"]['id'] )->display_name;

		return apply_filters( 'pulse_press_get_mention_name', $name );
	}

function pulse_press_author_feed_link() {
	echo pulse_press_get_author_feed_link();
}
	function pulse_press_get_author_feed_link() {

		if ( get_query_var( 'author_name' ) )
	   		$curauth = get_userdatabylogin( get_query_var( 'author_name' ) );
		else
	   		$curauth = get_userdata( get_query_var( 'author' ) );

		if ( isset( $curauth->ID ) )
			return apply_filters( 'pulse_press_get_author_feed_link', get_author_feed_link( $curauth->ID ) );
	}

function pulse_press_user_identity() {
	echo pulse_press_get_user_identity();
}
	function pulse_press_get_user_identity() {
		global $user_identity;
		return $user_identity;
	}

function pulse_press_load_entry() {
	global $withcomments;

	$withcomments = true;

	get_template_part( 'entry' );
}

function pulse_press_date_time_with_microformat( $type = 'post' ) {
	$d = 'comment' == $type ? 'get_comment_time' : 'get_post_time';
	return '<abbr title="'.$d( 'Y-m-d\TH:i:s\Z', true).'">'.sprintf( __( '%1$s <em>on</em> %2$s', 'pulse_press' ),  $d(get_option( 'time_format' )), $d( get_option( 'date_format' ) ) ).'</abbr>';
}

function pulse_press_curPageURL() {
 $pageURL = 'http';
 if (isset($_SERVER["HTTPS"])) {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].parse_url($_SERVER["REQUEST_URI"],PHP_URL_PATH);
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].parse_url($_SERVER["REQUEST_URI"],PHP_URL_PATH);
 }
 
 return $pageURL;
}


function pulse_press_date_range($start_date,$end_date,$selected_date=null) {
		$start_date = strtotime($start_date);
		
		$end_date   = strtotime($end_date);
		
		
		
		$duration = pulse_press_duration($end_date - $start_date); // this is not very accurate
		
		$current_date_array['year'] = date('Y', $start_date);
		$current_date_array['month'] = date('n', $start_date);
		$current_date_array['day'] = date('j', $start_date);
		$current_date_array['date'] = $start_date;
		
		$selected = '';
		$end_date_array['year'] = date('Y', $end_date);
		$end_date_array['month'] = date('n', $end_date);
		$end_date_array['day'] = date('j', $end_date);
		
		// display the date range in days if we area dealing with roughly number of duration less then 8 months
		$same_year=false; $same_month = false;
		if(intval($duration['month']) < 8):
			
			if($selected_date)
			$selected_date = date('ymd',strtotime($selected_date));
						
						
			
			// loop untill you see that the current year is the same as the end year
			while( $current_date_array['year'] <= $end_date_array['year']):
				
				if($current_date_array['year'] == $end_date_array['year'])
					$same_year = true;
			
				// loop untill you find that the current month is the same as the end month 
				// or loop untill you find that the we are not in the same year and there are not more the 12 months
				
				while( ($current_date_array['month'] <= $end_date_array['month']) || (!$same_year && $current_date_array['month'] <= 12) ):
			
					if($same_year && $current_date_array['month'] == $end_date_array['month'])
						$same_month = true;
					
					echo '<optgroup label="'.date('Y M', strtotime($current_date_array['month'].'/'.$current_date_array['day'].'/'.$current_date_array['year']) ).'">';
					// loop untill you find that you have the same amount of days as you have 
					while( 
					($current_date_array['day'] <= $end_date_array['day']) || 
					( !$same_month && ( $current_date_array['day'] <= ( date('t', $current_date_array['date']) ) ) ) ):
						
						
						if($selected_date == date('ymd', $current_date_array['date'] ))
							$selected = "selected='selected'";
						else
							$selected = '';
						
						echo '<option value="'.$current_date_array['month'].'/'.$current_date_array['day']."/".$current_date_array['year'].'"'.$selected.'>'. date('M j', $current_date_array['date'] ).'</option>';
						 
						$current_date_array['day']++;
						$current_date_array['date'] = strtotime($current_date_array['month'].'/'.$current_date_array['day'].'/'.$current_date_array['year']);
					endwhile;
					echo '</optgroup>';
					$current_date_array['day']=1;
					
					$current_date_array['month']++;
					$current_date_array['date'] = strtotime($current_date_array['month'].'/'.$current_date_array['day'].'/'.$current_date_array['year']);
				endwhile;
				
				$current_date_array['month'] = 1;
				$current_date_array['year']++;
				$current_date_array['date'] = strtotime($current_date_array['month'].'/'.$current_date_array['day'].'/'.$current_date_array['year']);
			endwhile;
			
		else: // show the content if we are displaying a range larger then 8 months
			if($selected_date)
			$selected_date = date('ym',strtotime($selected_date));
			
			// loop untill you see that the current year is the same as the end year
			while( $current_date_array['year'] <= $end_date_array['year']):
				
				if($current_date_array['year'] == $end_date_array['year'])
					$same_year = true;
				
				// loop untill you find that the current month is the same as the end month 
				// or loop untill you find that the we are not in the same year and there are not more the 12 months
				echo '<optgroup label="'.date('Y M', strtotime($current_date_array['month'].'/'.$current_date_array['day'].'/'.$current_date_array['year']) ).'">';
				while( ($current_date_array['month'] <= $end_date_array['month']) || (!$same_year && $current_date_array['month'] <= 12) ):
			
					if($same_year && $current_date_array['month'] == $end_date_array['month'])
						$same_month = true;
					
					if($selected_date == $current_date_array['year'].$current_date_array['month'])
						$selected = "selected='selected'";
					else
						$selected = '';
							
					echo '<option value="'.$current_date_array['month'].'/1/'.$current_date_array['year'].'" '.$selected.'>'.date("M y",strtotime($current_date_array['month'].'/1/'.$current_date_array['year'])) .'</option>';
					
					$current_date_array['day']=1;
					
					$current_date_array['month']++;
					
				endwhile;
				echo '</optgroup>';
				$current_date_array['month'] = 1;
				$current_date_array['year']++;
				
			endwhile;

		
		
		
		endif;
		
}
function pulse_press_duration($seconds, $max_periods=4)
{
    $periods = array("year" => 31536000, "month" => 2419200,  "day" => 86400, "hour" => 3600, "minute" => 60, "second" => 1);
    $i = 1;
    foreach ( $periods as $period => $period_seconds )
    {
        $period_duration = floor($seconds / $period_seconds);
        $seconds = $seconds % $period_seconds;
        if ( $period_duration == 0 ) continue;
        $duration[$period] = $period_duration;
        $i++;
        if ( $i >  $max_periods ) break;
    }
    return $duration;
}

function pulse_press_media_buttons() {
	// If we're using http and the admin is forced to https, bail.
	if ( ! is_ssl() && ( force_ssl_admin() || get_user_option( 'use_ssl' ) )  ) {
		return;
	}

	include_once( ABSPATH . '/wp-admin/includes/media.php' );
	ob_start();
	do_action( 'media_buttons' );

	// Replace any relative paths to media-upload.php
	echo preg_replace( '/([\'"])media-upload.php/', '${1}' . admin_url( 'media-upload.php' ), ob_get_clean() );
}


function pulse_press_sticky_class($classes) {
	global $post;
	if(is_sticky($post->ID))
		$classes[] = "sticky";
		
	
	return $classes;}
add_filter('post_class', 'pulse_press_sticky_class');

