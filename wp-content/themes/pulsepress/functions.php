<?php
/**
 * @package WordPress
 * @subpackage PulsePress
 */

define( 'PULSEPRESS_INC_PATH',  get_template_directory() . '/inc' );
define( 'PULSEPRESS_INC_URL', get_template_directory_uri().'/inc' );
define( 'PULSEPRESS_JS_PATH',  get_template_directory() . '/js' );
define( 'PULSEPRESS_JS_URL', get_template_directory_uri().'/js' );
define( 'PULSEPRESS_DB_VERSION',6);
define( 'PULSEPRESS_DB_TABLE', $wpdb->prefix . "pulse_press_user_post_meta");


if ( !class_exists( 'Services_JSON' ) ) require_once( PULSEPRESS_INC_PATH . '/JSON.php' );

$pulse_press_options = get_option( 'pulse_press_options' );

require_once( PULSEPRESS_INC_PATH . '/compat.php' );
require_once( PULSEPRESS_INC_PATH . '/pulse_press.php' );
require_once( PULSEPRESS_INC_PATH . '/js.php' );
require_once( PULSEPRESS_INC_PATH . '/options-page.php' );
require_once( PULSEPRESS_INC_PATH . '/template-tags.php' );
require_once( PULSEPRESS_INC_PATH . '/widgets/recent-tags.php' );
require_once( PULSEPRESS_INC_PATH . '/widgets/recent-comments.php' );
require_once( PULSEPRESS_INC_PATH . '/voting.php' );
require_once( PULSEPRESS_INC_PATH . '/star.php' );
require_once( PULSEPRESS_INC_PATH . '/db_helper.php' );
require_once( PULSEPRESS_INC_PATH . '/shortcodes.php' );
require_once( PULSEPRESS_INC_PATH . '/admin-table.php' );

$content_width = '632';

if ( function_exists( 'register_sidebar' ) ) {
	register_sidebar( array(
		'name' => __( 'Sidebar', 'pulse_press' ),
	) );
}

// Content Filters
function pulse_press_get_at_name_map() {
	global $wpdb;
	static $name_map = array();
	if ( $name_map ) // since $names is static, the stuff below will only get run once per page load.
 		return $name_map;
 		if( function_exists('get_users') ): // since WP 3.1 
			$users = get_users();
		else:
			return array(); // empty array
		endif;
	// get display names (can take out if you only want to handle nicenames)
	foreach ( $users as $user ) {
 		$name_map["@$user->user_login"]['id'] = $user->ID;
		$users_to_array[] = $user->ID;
	}
	// get nicenames (can take out if you only want to handle display names)
	$user_ids = join( ',', array_map( 'intval', $users_to_array ) );

	foreach ( $wpdb->get_results( "SELECT ID, display_name, user_nicename from $wpdb->users WHERE ID IN($user_ids)" ) as $user ) {
 		$name_map["@$user->display_name"]['id'] = $user->ID;
		$name_map["@$user->user_nicename"]['id'] = $user->ID;
	}

	foreach ( $name_map as $name => $values) {
		$username = get_userdata( $values['id'] )->user_login;
 		$name_map[$name]['replacement'] = '<a href="'.get_bloginfo('url') . esc_url( '/mentions/' . $username ) . '/">' . esc_html( $name ) . '</a>';
	}

	// remove any empty name just in case
	unset( $name_map['@'] );
	return $name_map;
}

add_action( 'init', 'pulse_press_mention_taxonomy', 0 ); // initialize the taxonomy

function pulse_press_mention_taxonomy() {
	register_taxonomy( 'mentions', 'post', array( 'show_ui' => false ) );
	pulse_press_flush_rewrites();
}

function pulse_press_flush_rewrites() {
	if ( false == pulse_press_get_option( 'rewrites_flushed' ) ) {
		pulse_press_update_option( 'rewrites_flushed', true );
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}
}

add_action( 'template_redirect', 'pulse_press_anonymous_feed', 0 ); // makethe feed info appear anonomous
function pulse_press_anonymous_feed(){
	
	if( pulse_press_get_option( 'show_anonymous' ) && is_feed() ):	
		add_filter('get_the_author_login',"pulse_press_anonymous_author");
		add_filter("the_author","pulse_press_anonymous_author");
	endif;
	
	
}
function pulse_press_anonymous_author( $author) { 
	global $post;
	if(get_post_custom_values('anonymous')):
		return "Anonymous";
	else:
		return $author;
	endif;
 }

add_action( 'init', 'pulse_press_register_menu', 0 ); // initialize the menu 
function pulse_press_register_menu(){
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'pulse_press' ),
	) );

}
function pulse_press_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'pulse_press_page_menu_args' );
function pulse_press_at_names( $content ) {
	global $post, $comment;
	$name_map = pulse_press_get_at_name_map(); // get users user_login and display_name map
	$content_original = $content; // save content before @names are found
	$users_to_add = array();

	foreach ( $name_map as $name => $values ) { //loop and...
		$content = str_ireplace( $name, $values['replacement'], $content ); // Change case to that in $name_map
		$content = strtr( $content, $name, $name ); // Replaces keys with values longest to shortest, without re-replacing pieces it's already done
		if ( $content != $content_original ) // if the content has changed, an @name has been found.
 			$users_to_add[] = get_userdata( $name_map[$name]['id'] )->user_login; // add that user to an array
		$content_original = $content;
	}
	if ( !empty( $users_to_add ) )
		$cache_data = implode($users_to_add); // if we've got an array, make it a comma delimited string
	if ( isset($cache_data) && $cache_data != wp_cache_get( 'mentions', $post->ID) ) {
		wp_set_object_terms( $post->ID, $users_to_add, 'mentions', true ); // tag the post.
		wp_cache_set( 'mentions', $cache_data, $post->ID);
	}
	return $content;
}

if ( !is_admin() ) add_filter( 'the_content', 'pulse_press_at_names' ); // hook into content
if ( !is_admin() ) add_filter( 'comment_text', 'pulse_press_at_names' ); // hook into comment text

function pulse_press_at_name_highlight( $c ) {

	if ( get_query_var( 'taxonomy' ) && 'mentions' != get_query_var( 'taxonomy' ) )
		return $c;

	$mention_name = '';
	$names = array();
	$name_map = pulse_press_get_at_name_map();

	if ( get_query_var( 'term' ) )
		$mention_name = get_query_var( 'term' );

	if ( isset( $name_map["@$mention_name"] ) ) {
		$names[] = get_userdata( $name_map["@$mention_name"]['id'] )->display_name;
		$names[] = get_userdata( $name_map["@$mention_name"]['id'] )->user_login;
	}

	foreach ( $names as $key => $name ) {
		$at_name = "@$name";
		$c = str_replace( $at_name, "<span class='mention-highlight'>$at_name</span>", $c );
	}

	return $c;
}

add_filter( 'the_content', 'pulse_press_at_name_highlight' );
add_filter( 'comment_text', 'pulse_press_at_name_highlight' );

// Widgets
function pulse_press_flush_tag_cache() {
	wp_cache_delete( 'pulse_press_theme_tag_list' );
}
add_action( 'save_post', 'pulse_press_flush_tag_cache' );

function pulse_press_get_avatar( $user_id, $email, $size ) {
	if ( $user_id )
		return get_avatar( $user_id, $size );
	else
		return get_avatar( $email, $size );
}

function pulse_press_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
?>
<li <?php comment_class(); ?> id="comment-<?php comment_ID( ); ?>">
	<?php echo get_avatar( $comment, 32 ); ?>
	<h4>
		<?php comment_author_link(); ?>
		<span class="meta"><?php comment_time(); ?> <?php _e( 'on', 'pulse_press' ); ?> <?php comment_date(); ?> <span class="actions"><a href="#comment-<?php comment_ID( ); ?>" class="permalink"><?php _e( 'Permalink', 'pulse_press' ); ?></a><?php echo comment_reply_link(array( 'depth' => $depth, 'max_depth' => $args['max_depth'], 'before' => ' | ' )); ?><?php edit_comment_link( __( 'Edit' , 'pulse_press' ), ' | ','' ); ?></span><br /></span>
	</h4>
	<div class="commentcontent<?php if (current_user_can( 'edit_post', $comment->comment_post_ID)) echo( ' comment-edit' ); ?>"  id="commentcontent-<?php comment_ID( ); ?>">
			<?php comment_text( ); ?>
	<?php if ( $comment->comment_approved == '0' ) : ?>
	<p><em><?php _e( 'Your comment is awaiting moderation.', 'pulse_press' ); ?></em></p>
	<?php endif; ?>
	</div>
<?php
}

function pulse_press_title( $before = '<h2>', $after = '</h2>', $returner = false ) {
	if ( is_page() )
		return;

	if ( is_single() && false == pulse_press_the_title( '', '', true ) ) { ?>
		<h2 class="transparent-title"><?php echo the_title(); ?></h2><?php
		return true;
	} else {
		pulse_press_the_title( $before, $after, $returner );
	}
}

/**
 * Generate a nicely formatted post title
 *
 * Ignore empty titles, titles that are auto-generated from the
 * first part of the post_content
 *
 * @package WordPress
 * @subpackage PulsePress
 * @since 1.0.5
 *
 * @param    string    $before    content to prepend to title
 * @param    string    $after     content to append to title
 * @param    string    $echo      echo or return
 * @return   string    $out       nicely formatted title, will be empty string if no title
 */
function pulse_press_the_title( $before = '<h2>', $after = '</h2>', $returner = false ) {
	global $post;

	$temp = $post;
	
	$t = apply_filters( 'the_title', $temp->post_title );
	$title = $temp->post_title;
	$content = $temp->post_content;
	$pos = 0;
	$out = '';

	// Don't show post title if turned off in options or title is default text
	if ( 'Post Title' == $title )
		return false;

	$content = trim( $content );
	$title = trim( $title );
	$title = preg_replace( '/\.\.\.$/', '', $title );
	$title = str_replace( "\n", ' ', $title );
	$title = str_replace( '  ', ' ', $title);
	$content = str_replace( "\n", ' ', strip_tags( $content) );
	$content = str_replace( '  ', ' ', $content );
	$content = trim( $content );
	$title = trim( $title );

	// Clean up links in the title
	if ( false !== strpos( $title, 'http' ) )  {
		$split = @str_split( $content, strpos( $content, 'http' ) );
		$content = $split[0];
		$split2 = @str_split( $title, strpos( $title, 'http' ) );
		$title = $split2[0];
	}

	// Avoid processing an empty title
	if ( '' == $title )
		return false;

	// Avoid processing the title if it's the very first part of the post content
	// Which is the case with most "status" posts
	$pos = strpos( $content, $title );
	if ( false === $pos || 0 < $pos ) {
		if ( is_single() )
			$out = $before . $t . $after;
		else
			$out = $before . '<a href="' . get_permalink( $temp->ID ) . ' " class="permalink">' . $t . '&nbsp;</a>' . $after;

		if ( $returner )
			return $out;
		else
			echo $out;
	}
}

function pulse_press_loop() {
	global $looping;
	$looping = ($looping === 1 ) ? 0 : 1;
}
add_action( 'loop_start', 'pulse_press_loop' );
add_action( 'loop_end', 'pulse_press_loop' );


function pulse_press_comments( $comment, $args, $echo = true ) {
	$GLOBALS['comment'] = $comment;

	$depth = pulse_press_get_comment_depth( get_comment_ID() );
	$comment_text =  apply_filters( 'comment_text', $comment->comment_content );
	$comment_class = comment_class( '', null, null, false );
	$comment_time = get_comment_time();
	$comment_date = get_comment_date();
	$id = get_comment_ID();
	$avatar = get_avatar( $comment, 32 );
	$author_link = get_comment_author_link();
	$reply_link = pulse_press_get_comment_reply_link(
				array( 'depth' => $depth, 'max_depth' => $args['max_depth'], 'before' => ' | ', 'reply_text' => __( 'Reply', 'pulse_press' ) ),
				$comment->comment_ID, $comment->comment_post_ID );
	$can_edit = current_user_can( 'edit_post', $comment->comment_post_ID );
	$edit_comment_url = get_edit_comment_link( $comment->comment_ID );
	$edit_link = $can_edit? " | <a class='comment-edit-link' href='$edit_comment_url' title='".esc_attr__( 'Edit comment', 'pulse_press' )."'>".__( 'Edit', 'pulse_press' )."</a>" : '';
	$content_class = $can_edit? 'commentcontent comment-edit' : 'commentcontent';
	$awaiting_message = $comment->comment_approved == '0'? '<p><em>'.__( 'Your comment is awaiting moderation.', 'pulse_press' ).'</em></p>' : '';
	$permalink = esc_url( get_comment_link() );
	$permalink_text = __( 'Permalink', 'pulse_press' );
	$date_time = pulse_press_date_time_with_microformat( 'comment' );
	$html = <<<HTML
<li $comment_class id="comment-$id">
		$avatar
		<h4>
				$author_link
				<span class="meta">
						$date_time
						<span class="actions"><a href="$permalink" class="permalink">$permalink_text</a> $reply_link $edit_link</span>
				</span>
		</h4>
		<div class="$content_class" id="commentcontent-$id">
				$comment_text
		</div>
HTML;
/*
	if ( get_comment_type() != 'comment' )
		return false;
*/
	if ( $echo )
		echo $html;
	else
		return $html;
}

function pulse_press_get_tags_with_count( $post, $format = 'list', $before = '', $sep = '', $after = '' ) {
	$posttags = get_the_tags($post->ID, 'post_tag' );

	if ( !$posttags )
		return '';

	foreach ( $posttags as $tag ) {
		if ( $tag->count > 1 && !is_tag($tag->slug) ) {
			$tag_link = '<a href="' . get_term_link($tag, 'post_tag' ) . '" rel="tag">' . $tag->name . ' ( ' . number_format_i18n( $tag->count ) . ' )</a>';
		} else {
			$tag_link = $tag->name;
		}

		if ( $format == 'list' )
			$tag_link = '<li>' . $tag_link . '</li>';

		$tag_links[] = $tag_link;
	}

	return apply_filters( 'tags_with_count', $before . join( $sep, $tag_links ) . $after, $post );
}

function pulse_press_tags_with_count( $format = 'list', $before = '', $sep = '', $after = '' ) {
	global $post;
	echo pulse_press_get_tags_with_count( $post, $format, $before, $sep, $after );
}


function pulse_press_latest_post_permalink() {
	global $wpdb;
	$sql = "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish' ORDER BY post_date DESC LIMIT 1";
	$last_post_id = $wpdb->get_var($sql);
	$permalink = get_permalink($last_post_id);
	return $permalink;
}

function pulse_press_title_from_content( $content ) {

		static $strlen =  null;
		if ( !$strlen ) {
				$strlen = function_exists( 'mb_strlen' )? 'mb_strlen' : 'strlen';
		}
		$max_len = 40;
		$title = $strlen( $content ) > $max_len? wp_html_excerpt( $content, $max_len ) . '...' : $content;
		$title = trim( strip_tags( $title ) );
		$title = str_replace("\n", " ", $title);

	//Try to detect image or video only posts, and set post title accordingly
	if ( !$title ) {
		if ( preg_match("/<object|<embed/", $content ) )
			$title = __( 'Video Post', 'pulse_press' );
		elseif ( preg_match( "/<img/", $content ) )
			$title = __( 'Image Post', 'pulse_press' );
	}
		return $title;
}

function pulse_press_fix_empty_titles( $post_ID, $post ) {
	
	if ( is_object($post) && $post->post_title == '' && $post->post_type == "post" ) {
		$post->post_title = pulse_press_title_from_content( $post->post_content );
		$post->post_modified = current_time( 'mysql' );
		$post->post_modified_gmt = current_time( 'mysql', 1);
		return wp_update_post( $post );
	}
}
add_action( 'save_post', 'pulse_press_fix_empty_titles', 10, 2 );

function pulse_press_init_at_names() {
	global $init_var_names, $name;

	// @names
	$init_var_names = array( 'comment_author', 'comment_author_email', 'comment_author_url' );
	foreach($init_var_names as $name)
		if (!isset($$name)) $$name = '';
}
add_action( 'template_redirect' , 'pulse_press_init_at_names' );

function pulse_press_add_head_content() {
	if ( is_home() && is_user_logged_in() ) {
		include ABSPATH . '/wp-admin/includes/media.php';
	}
}
add_action( 'wp_head', 'pulse_press_add_head_content' );

function pulse_press_new_post_noajax() {
	if ( 'POST' != $_SERVER['REQUEST_METHOD'] || empty( $_POST['action'] ) || $_POST['action'] != 'post' )
	    return;

	if ( !is_user_logged_in() )
		auth_redirect();

	if ( !current_user_can( 'publish_posts' ) ) {
		wp_redirect( home_url() . '/' );
		exit;
	}

	global $current_user;

	check_admin_referer( 'new-post' );

	$user_id		= $current_user->ID;
	$post_content	= $_POST['posttext'];
	$tags			= $_POST['tags'];

	$post_title = pulse_press_title_from_content( $post_content );

	$post_id = wp_insert_post( array(
		'post_author'	=> $user_id,
		'post_title'	=> $post_title,
		'post_content'	=> $post_content,
		'tags_input'	=> $tags,
		'post_status'	=> 'publish'
	) );

	wp_redirect( home_url() . '/' );

	exit;
}
add_filter( 'template_redirect', 'pulse_press_new_post_noajax' );

//Search related Functions

function pulse_press_search_comments_distinct( $distinct ) {
	global $wp_query;
	if (!empty($wp_query->query_vars['s']))
		return 'DISTINCT';
}
add_filter( 'posts_distinct', 'pulse_press_search_comments_distinct' );

function pulse_press_search_comments_where( $where ) {
	global $wp_query, $wpdb;
	if (!empty($wp_query->query_vars['s'])) {
			$or = " OR ( comment_post_ID = ".$wpdb->posts . ".ID  AND comment_approved =  '1' AND comment_content LIKE '%" . like_escape( $wpdb->escape($wp_query->query_vars['s'] ) ) . "%' ) ";
				$where = preg_replace( "/\bor\b/i", $or." OR", $where, 1 );
	}
	return $where;
}
add_filter( 'posts_where', 'pulse_press_search_comments_where' );

function pulse_press_search_comments_join( $join ) {
	global $wp_query, $wpdb, $request;
	if (!empty($wp_query->query_vars['s']))
		$join .= " LEFT JOIN $wpdb->comments ON ( comment_post_ID = ID  AND comment_approved =  '1' )";
	return $join;
}
add_filter( 'posts_join', 'pulse_press_search_comments_join' );

function pulse_press_get_search_query_terms() {
	$search = get_query_var( 's' );
	$search_terms = get_query_var( 'search_terms' );
	if ( !empty($search_terms) ) {
		return $search_terms;
	} else if ( !empty($search) ) {
		return array($search);
	}
	return array();
}

function pulse_press_hilite( $text ) {
	$query_terms = array_filter( array_map( 'trim', pulse_press_get_search_query_terms() ) );
	foreach ( $query_terms as $term ) {
	    $term = preg_quote( $term, '/' );
		if ( !preg_match( '/<.+>/', $text ) ) {
			$text = preg_replace( '/(\b'.$term.'\b)/i','<span class="hilite">$1</span>', $text );
		} else {
			$text = preg_replace( '/(?<=>)([^<]+)?(\b'.$term.'\b)/i','$1<span class="hilite">$2</span>', $text );
		}
	}
	return $text;
}

function pulse_press_hilite_tags( $tags ) {
	$query_terms = array_filter( array_map( 'trim', pulse_press_get_search_query_terms() ) );
	// tags are kept escaped in the db
	$query_terms = array_map( 'esc_html', $query_terms );
	foreach( array_filter((array)$tags) as $tag )
	    if ( in_array( trim($tag->name), $query_terms ) )
	        $tag->name ="<span class='hilite'>". $tag->name . "</span>";
	return $tags;
}

// Highlight text and comments:
add_filter( 'the_content', 'pulse_press_hilite' );
add_filter( 'get_the_tags', 'pulse_press_hilite_tags' );
add_filter( 'the_excerpt', 'pulse_press_hilite' );
add_filter( 'comment_text', 'pulse_press_hilite' );


/*
	Modified to replace query string with blog url in output string
*/
function pulse_press_get_comment_reply_link( $args = array(), $comment = null, $post = null ) {
	global $user_ID;

	if ( post_password_required() )
		return;

	$defaults = array( 'add_below' => 'comment', 'respond_id' => 'respond', 'reply_text' => __( 'Reply', 'pulse_press' ),
		'login_text' => __( 'Log in to Reply', 'pulse_press' ), 'depth' => 0, 'before' => '', 'after' => '' );

	$args = wp_parse_args($args, $defaults);
	if ( 0 == $args['depth'] || $args['max_depth'] <= $args['depth'] )
		return;

	extract($args, EXTR_SKIP);

	$comment = get_comment($comment);
	$post = get_post($post);

	if ( 'open' != $post->comment_status )
		return false;

	$link = '';

	$reply_text = esc_html( $reply_text );

	if ( get_option( 'comment_registration' ) && !$user_ID )
		$link = '<a rel="nofollow" href="' . site_url( 'wp-login.php?redirect_to=' . urlencode( get_permalink() ) ) . '">' . esc_html( $login_text ) . '</a>';
	else
		$link = "<a rel='nofollow' class='comment-reply-link' href='". get_permalink($post). "#" . urlencode( $respond_id ) . "' onclick='return addComment.moveForm(\"" . esc_js( "$add_below-$comment->comment_ID" ) . "\", \"$comment->comment_ID\", \"" . esc_js( $respond_id ) . "\", \"$post->ID\")'>$reply_text</a>";
	return apply_filters( 'comment_reply_link', $before . $link . $after, $args, $comment, $post);
}

function pulse_press_comment_depth_loop( $comment_id, $depth )  {
	$comment = get_comment( $comment_id );

	if ( isset( $comment->comment_parent ) && 0 != $comment->comment_parent ) {
		return pulse_press_comment_depth_loop( $comment->comment_parent, $depth + 1 );
	}
	return $depth;
}

function pulse_press_get_comment_depth( $comment_id ) {
	return pulse_press_comment_depth_loop( $comment_id, 1 );
}

function pulse_press_comment_depth( $comment_id ) {
	echo pulse_press_get_comment_depth( $comment_id );
}

function pulse_press_poweredby_link() {
	return apply_filters( 'pulse_press_poweredby_link', sprintf( __( '<strong>%1$s</strong> is proudly powered by %2$s.', 'pulse_press' ), get_bloginfo( 'name' ), '<a href="http://wordpress.org/" rel="generator">WordPress</a>' )	);
}

if ( defined( 'IS_WPCOM' ) && IS_WPCOM ) {
	add_filter( 'pulse_press_poweredby_link', pulse_press_returner( '<a href="http://wordpress.com/" rel="generator">Get a free blog at WordPress.com</a>' ) );
}



function pulse_press_background_color() {
	
	$background = get_background_image();
	$color = get_background_color();
	if ( ! $background && ! $color )
		return;

	$style = $color ? "background-color: #$color;" : '';

	if ( $background ) {
		$image = " background-image: url('$background');";

		$repeat = get_theme_mod( 'background_repeat', 'repeat' );
		if ( ! in_array( $repeat, array( 'no-repeat', 'repeat-x', 'repeat-y', 'repeat' ) ) )
			$repeat = 'repeat';
		$repeat = " background-repeat: $repeat;";

		$position = get_theme_mod( 'background_position_x', 'left' );
		if ( ! in_array( $position, array( 'center', 'right', 'left' ) ) )
			$position = 'left';
		$position = " background-position: top $position;";

		$attachment = get_theme_mod( 'background_attachment', 'scroll' );
		if ( ! in_array( $attachment, array( 'fixed', 'scroll' ) ) )
			$attachment = 'scroll';
		$attachment = " background-attachment: $attachment;";

		$style .= $image . $repeat . $position . $attachment;
	}
?>
<style type="text/css">
body { <?php echo trim( $style ); ?> }
#shell{
	margin: 30px auto;
	background: none repeat scroll 0 0 #FFFFFF;
    box-shadow: 0 2px 6px rgba(100, 100, 100, 0.3);}
    
    a, a:visited, h1 a:visited, a:active, #main .selected .actions a, #main .selected .actions a:link, #main .selected .actions a:visited, #help dt {
	color: <?php echo pulse_press_color_darken($color, 60); ?>;
}

a:hover, h1 a:hover, #main .selected .actions a:hover, #main .selected .actions a:active {
	color: <?php echo pulse_press_color_darken($color, 100); ?>;
}
.ac_over,
#wp-calendar tbody td a{ background-color: <?php echo pulse_press_color_darken($color, 20); ?>; text-decoration: none;}
#wp-calendar tbody td a:hover{background-color: <?php echo pulse_press_color_darken($color, 60); ?>; }

#main .vote .vote-up:hover,
#main .vote .vote-up-set { background-color: <?php echo pulse_press_color_darken($color,60); ?>;  }

#main .vote .vote-down:hover,
#main .vote .vote-down-set{ background-color: <?php echo pulse_press_color_darken($color,60); ?>;   }
#wrapper{ border-color:#FFF;}
</style>
<?php
}

function pulse_press_color_darken($color, $dif=20){
 
    $color = str_replace('#', '', $color);
    if (strlen($color) != 6){ return '000000'; }
    $rgb = '';
 
    for ($x=0;$x<3;$x++){
        $c = hexdec(substr($color,(2*$x),2)) - $dif;
        $c = ($c < 0) ? 0 : dechex($c);
        $rgb .= (strlen($c) < 2) ? '0'.$c : $c;
    }
 
    return '#'.$rgb;
}





add_action( 'wp_head', 'pulse_press_show_twitter');

function pulse_press_hidden_sidebar_css() {
	$hide_sidebar = pulse_press_get_option( 'hide_sidebar' );
	
	$sleeve_margin = ( is_rtl() ) ? 'margin-left: 0;' : 'margin-right: 0;';
	if ( $hide_sidebar ) :
	?>
	<style type="text/css">
		.sleeve_main { <?php echo $sleeve_margin;?> }
	</style>
	<?php endif;
	
	
}
add_action( 'wp_head', 'pulse_press_hidden_sidebar_css' );
/**
 * pulse_press_breadcrumbs function.
 * Based on http://dimox.net/wordpress-breadcrumbs-without-a-plugin/
 * @access public
 * @return void
 */
function pulse_press_breadcrumbs() {
 
  $delimiter = '&raquo;';
  $home = 'Home'; // text for the 'Home' link
  $before = '<span class="current">'; // tag before the current crumb
  $after = '</span>'; // tag after the current crumb
 
  if ( !is_home() && !is_front_page() || is_paged() ) {
 
    echo '<div id="crumbs">';
 
    global $post;
    $homeLink = home_url();
    echo '<a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ';
 
    if ( is_category() ) {
      global $wp_query;
      $cat_obj = $wp_query->get_queried_object();
      $thisCat = $cat_obj->term_id;
      $thisCat = get_category($thisCat);
      $parentCat = get_category($thisCat->parent);
      if ($thisCat->parent != 0) echo(get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' '));
      echo $before . 'Archive by category "' . single_cat_title('', false) . '"' . $after;
 
    } elseif ( is_day() ) {
      echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
      echo '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
      echo $before . get_the_time('d') . $after;
 
    } elseif ( is_month() ) {
      echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
      echo $before . get_the_time('F') . $after;
 
    } elseif ( is_year() ) {
      echo $before . get_the_time('Y') . $after;
 
    } elseif ( is_single() && !is_attachment() ) {
      if ( get_post_type() != 'post' ) {
        $post_type = get_post_type_object(get_post_type());
        $slug = $post_type->rewrite;
        echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a> ' . $delimiter . ' ';
        echo $before . get_the_title() . $after;
      } else {
        $cat = get_the_category(); $cat = $cat[0];
        echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
        echo $before . get_the_title() . $after;
      }
 
    } elseif ( !is_single() && !is_page() && get_post_type() != 'post' ) {
      $post_type = get_post_type_object(get_post_type());
      echo $before . $post_type->labels->singular_name . $after;
 
    } elseif ( is_attachment() ) {
      $parent = get_post($post->post_parent);
      $cat = get_the_category($parent->ID); $cat = $cat[0];
      echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
      echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a> ' . $delimiter . ' ';
      echo $before . get_the_title() . $after;
 
    } elseif ( is_page() && !$post->post_parent ) {
      echo $before . get_the_title() . $after;
 
    } elseif ( is_page() && $post->post_parent ) {
      $parent_id  = $post->post_parent;
      $breadcrumbs = array();
      while ($parent_id) {
        $page = get_page($parent_id);
        $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
        $parent_id  = $page->post_parent;
      }
      $breadcrumbs = array_reverse($breadcrumbs);
      foreach ($breadcrumbs as $crumb) echo $crumb . ' ' . $delimiter . ' ';
      echo $before . get_the_title() . $after;
 
    } elseif ( is_search() ) {
      echo $before . 'Search results for "' . get_search_query() . '"' . $after;
 
    } elseif ( is_tag() ) {
      echo $before . 'Posts tagged "' . single_tag_title('', false) . '"' . $after;
 
    } elseif ( is_author() ) {
       global $author;
      $userdata = get_userdata($author);
      echo $before . 'Articles posted by ' . $userdata->display_name . $after;
 
    } elseif ( is_404() ) {
      echo $before . 'Error 404' . $after;
    }
 
    if ( get_query_var('paged') ) {
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
      echo __('Page','pulse_press') . ' ' . get_query_var('paged');
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
    }
 
    echo '</div>';
 
  }
} // end dimox_breadcrumbs()

function pulse_press_show_twitter()
{
	$show_twitter = pulse_press_get_option( 'show_twitter' );
}

// Network signup form
function pulse_press_before_signup_form() {
	echo '<div class="sleeve_main"><div id="main">';
}
add_action( 'before_signup_form', 'pulse_press_before_signup_form' );

function pulse_press_after_signup_form() {
	echo '</div></div>';
}
add_action( 'after_signup_form', 'pulse_press_after_signup_form' );

// Enable background
add_custom_background("pulse_press_background_color");

add_theme_support( 'automatic-feed-links' );

// will need to be changed for 3.3 and is is_main_query
// only set this on the main loop
$pulse_press_main_loop = false;
function pulse_press_main_loop_test($query) {
  global $wp_the_query, $pulse_press_main_loop;
  
  if ($query === $wp_the_query && !is_page()) {
  	$pulse_press_main_loop = true;
  }else{
  	$pulse_press_main_loop = false;
  }
  if($pulse_press_main_loop):
  	 $query->set( 'ignore_sticky_posts', true );
  endif;
 
  
}


// add the ability to display sticky post on the archive pages. 
function pulse_press_include_sticky_category( $query ) {
	
   	 if( is_category() ){  		
   	 	 $sticky_posts = get_option('sticky_posts');	
   		 $query->set('post__not_in', $sticky_posts );	
   	 }
}
add_action( 'pre_get_posts', 'pulse_press_include_sticky_category' );


// limit coments to 140 characters
if( pulse_press_get_option( 'limit_comments' ) )
	add_filter('comment_form_defaults', 'pulse_press_limit_comments');

function pulse_press_limit_comments( $defaults  ) {
	// <p class="comment-form-comment"><label for="comment">Comment</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>
	$defaults["comment_field"] = str_replace( 'name="comment"', 'name="comment" maxlength="140"', $defaults["comment_field"] );
	
	return $defaults;
}
