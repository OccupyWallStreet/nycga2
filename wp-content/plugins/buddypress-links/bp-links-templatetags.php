<?php

/*****************************************************************************
 * User Links Template Class/Tags
 **/

class BP_Links_Template {
	var $current_link = -1;
	var $link_count;
	var $links;
	var $link;
	
	var $in_the_loop;
	
	var $pag_page;
	var $pag_num;
	var $pag_links;
	var $total_link_count;
	
	var $sort_by;
	var $order;

	var $avatar_size;
	
	function bp_links_template( $user_id, $type, $page, $per_page, $max, $slug, $search_terms, $category_id = null, $group_id = null ) {
		global $bp;

		$this->pag_page = isset( $_REQUEST['lpage'] ) ? intval( $_REQUEST['lpage'] ) : $page;
		$this->pag_num = isset( $_REQUEST['num'] ) ? intval( $_REQUEST['num'] ) : $per_page;
		
		switch ( $type ) {

			default:
			case 'active':
				$this->links = bp_links_get_active( $this->pag_num, $this->pag_page, $user_id, $search_terms, $category_id, $group_id );
				break;

			case 'newest':
				$this->links = bp_links_get_newest( $this->pag_num, $this->pag_page, $user_id, $search_terms, $category_id, $group_id );
				break;

			case 'search':
				$this->links = bp_links_get_search( $this->pag_num, $this->pag_page, $user_id, $search_terms, $category_id, $group_id );
				break;

			case 'popular':
				$this->links = bp_links_get_popular( $this->pag_num, $this->pag_page, $user_id, $search_terms, $category_id, $group_id );
				break;

			case 'most-votes':
				$this->links = bp_links_get_most_votes( $this->pag_num, $this->pag_page, $user_id, $search_terms, $category_id, $group_id );
				break;

			case 'high-votes':
				$this->links = bp_links_get_high_votes( $this->pag_num, $this->pag_page, $user_id, $search_terms, $category_id, $group_id );
				break;

			case 'all':
				$this->links = bp_links_get_all( $this->pag_num, $this->pag_page, $user_id, $search_terms, $category_id, $group_id );
				break;
			
			case 'random':
				$this->links = bp_links_get_random( $this->pag_num, $this->pag_page );
				break;

			case 'single-link':
				$link = new stdClass;
				$link->link_id = BP_Links_Link::get_id_from_slug( $slug );
				$this->links = array( $link );
				break;
		}
		
		if ( 'single-link' == $type ) {
			$this->total_link_count = 1;
			$this->link_count = 1;
		} else {
			if ( !$max || $max >= (int)$this->links['total'] )
				$this->total_link_count = (int)$this->links['total'];
			else
				$this->total_link_count = (int)$max;

			$this->links = $this->links['links'];

			if ( $max ) {
				if ( $max >= count($this->links) )
					$this->link_count = count($this->links);
				else
					$this->link_count = (int)$max;
			} else {
				$this->link_count = count($this->links);
			}
		}

		$this->pag_links = paginate_links( array(
			'base' => add_query_arg( array( 'lpage' => '%#%', 'num' => $this->pag_num, 's' => $_REQUEST['s'], 'sortby' => $this->sort_by, 'order' => $this->order ) ),
			'format' => '',
			'total' => ceil($this->total_link_count / $this->pag_num),
			'current' => $this->pag_page,
			'prev_text' => '&laquo;',
			'next_text' => '&raquo;',
			'mid_size' => 1
		));
	}

	function has_links() {
		if ( $this->link_count )
			return true;
		
		return false;
	}
	
	function next_link() {
		$this->current_link++;
		$this->link = $this->links[$this->current_link];
			
		return $this->link;
	}
	
	function rewind_links() {
		$this->current_link = -1;
		if ( $this->link_count > 0 ) {
			$this->link = $this->links[0];
		}
	}
	
	function links() {
		if ( $this->current_link + 1 < $this->link_count ) {
			return true;
		} elseif ( $this->current_link + 1 == $this->link_count ) {
			do_action('loop_end');
			// Do some cleaning up after the loop
			$this->rewind_links();
		}

		$this->in_the_loop = false;
		return false;
	}
	
	function the_link() {
		global $link;

		$this->in_the_loop = true;
		$this->link = $this->next_link();

		if ( !$link = wp_cache_get( 'bp_links_link_nouserdata_' . $this->link->link_id, 'bp' ) ) {
			$link = new BP_Links_Link( $this->link->link_id, false, false );
			wp_cache_set( 'bp_links_link_nouserdata_' . $this->link->link_id, $link, 'bp' );
		}

		$this->link = $link;

		if ( 0 == $this->current_link ) // loop has just started
			do_action('loop_start');
	}

	function avatar_display_size() {

		$args = func_get_args();

		if ( count( $args ) == 1 ) {
			if ( in_array( $args[0], array(50,60,70,80,90,100,110,120,130), true ) ) {
				$this->avatar_size = $args[0];
			} else {
				$this->avatar_size = BP_LINKS_LIST_AVATAR_SIZE;
			}
		} else {
			return $this->avatar_size;
		}
	}
}


function bp_has_links( $args = '' ) {
	global $links_template, $bp;

	$defaults = array(
		'type' => 'active',
		'page' => 1,
		'per_page' => 10,
		'max' => false,
		'avatar_size' => false,

		'user_id' => false,
		'slug' => false,
		'search_terms' => false,
		'category' => false,
		'group_id' => false
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	if ( '' == $args ) {
		// The following code will auto set parameters based on the page being viewed.
		// for example on example.com/members/marshall/links/my-links/popular/
		// $type = 'popular'
		//
		if ( 'my-links' == $bp->current_action ) {
			$order = $bp->action_variables[0];
			if ( 'active' == $order )
				$type = 'active';
			elseif ( 'all' == $order )
				$type = 'all';
			elseif ( 'newest' == $order )
				$type = 'newest';
			else if ( 'popular' == $order )
				$type = 'popular';
			else if ( 'most-votes' == $order )
				$type = 'most-votes';
			else if ( 'high-votes' == $order )
				$type = 'high-votes';
		} else if ( $bp->links->current_link->slug ) {
			$type = 'single-link';
			$slug = $bp->links->current_link->slug;
		}

		// Auto set group_id if we are on a group home page
		if ( bp_links_is_groups_enabled() && $bp->groups->current_group->id ) {
			$group_id = $bp->groups->current_group->id;
		}
	}
	
	if ( isset( $_REQUEST['link-filter-box'] ) || isset( $_REQUEST['s'] ) )
		$search_terms = ( isset( $_REQUEST['link-filter-box'] ) ) ? $_REQUEST['link-filter-box'] : $_REQUEST['s'];
	
	$links_template = new BP_Links_Template( $user_id, $type, $page, $per_page, $max, $slug, $search_terms, $category, $group_id );
	$links_template->avatar_display_size( $avatar_size );
	return apply_filters( 'bp_has_links', $links_template->has_links(), &$links_template );
}


function bp_links() {
	global $links_template;
	return $links_template->links();
}

function bp_the_link() {
	global $links_template;
	return $links_template->the_link();
}

function bp_link_is_visible( $link = false ) {
	global $bp, $links_template;

	if ( !$link )
		$link =& $links_template->link;

	return bp_links_is_link_visibile( $link );
}

function bp_link_is_admin_page() {
	return bp_links_is_link_admin_page();
}

function bp_link_id() {
	echo bp_get_link_id();
}
	function bp_get_link_id( $link = false ) {
		global $links_template;

		if ( !$link )
			$link =& $links_template->link;

		return apply_filters( 'bp_get_link_id', $link->id );
	}

function bp_link_category_id() {
	echo bp_get_link_category_id();
}
	function bp_get_link_category_id( $link = false ) {
		global $bp, $links_template;

		if ( !$link )
			$link =& $links_template->link;

		$category_id = $link->category_id;

		return apply_filters( 'bp_get_link_category_id', $category_id );
	}

function bp_link_category_slug() {
	echo bp_get_link_category_slug();
}
	function bp_get_link_category_slug( $link = false ) {
		global $links_template;

		if ( !$link )
			$link =& $links_template->link;

		$category = $link->get_category();

		return apply_filters( 'bp_get_link_category_slug', $category->slug );
	}

function bp_link_category_name() {
	echo bp_get_link_category_name();
}
	function bp_get_link_category_name( $link = false ) {
		global $links_template;

		if ( !$link )
			$link =& $links_template->link;

		$category = $link->get_category();

		return apply_filters( 'bp_get_link_category_name', $category->name );
	}

function bp_link_url() {
	echo bp_get_link_url();
}
	function bp_get_link_url( $link = false ) {
		global $bp, $links_template;

		if ( !$link )
			$link =& $links_template->link;

		return apply_filters( 'bp_get_link_url', $link->url );
	}

function bp_link_url_domain() {
	echo bp_get_link_url_domain();
}
	function bp_get_link_url_domain( $link = false ) {
		global $links_template;

		if ( !$link )
			$link =& $links_template->link;

		$url_parts = parse_url( $link->url );

		if( isset( $url_parts['host'] ) ) {
			$domain = preg_replace( '/^www\./', '', $url_parts['host'] );
		} else {
			$domain = '';
		}

		return apply_filters( 'bp_get_link_url_domain', $domain );
	}

function bp_link_name() {
	echo bp_get_link_name();
}
	function bp_get_link_name( $link = false ) {
		global $bp, $links_template;

		if ( !$link )
			$link =& $links_template->link;

		$name = $link->name;

		return apply_filters( 'bp_get_link_name', $name );
	}
	
function bp_link_type() {
	echo bp_get_link_type();
}
	function bp_get_link_type( $link = false ) {
		global $links_template;

		if ( !$link )
			$link =& $links_template->link;

		if ( BP_Links_Link::STATUS_PUBLIC == $link->status ) {
			$type = __( 'Public Link', 'buddypress-links' );
		} else if ( BP_Links_Link::STATUS_FRIENDS == $link->status ) {
			$type = __( 'Friends Only Link', 'buddypress-links' );
		} else if ( BP_Links_Link::STATUS_HIDDEN == $link->status ) {
			$type = __( 'Hidden Link', 'buddypress-links' );
		} else {
			$type = ucwords( $link->status ) . ' ' . __( 'Link', 'buddypress-links' );
		}

		return apply_filters( 'bp_get_link_type', $type );
	}

function bp_link_has_avatar() {
	echo ( bp_get_link_has_avatar() ) ? 1 : 0;
}
	function bp_get_link_has_avatar() {
		return bp_links_check_avatar( bp_get_link_id() );
	}

function bp_link_avatar( $args = '', $link = null ) {
	echo bp_get_link_avatar( $args, $link );
}
	function bp_get_link_avatar( $args = '', $link = null ) {
		global $links_template;

		if ( !$link ) {
			$link = $links_template->link;
		}

		$defaults = array(
			'item_id' => $link->id
		);

		$new_args = wp_parse_args( $args, $defaults );

		return apply_filters( 'bp_get_link_avatar', bp_links_fetch_avatar( $new_args, $link ) );
	}

function bp_link_avatar_thumb() {
	echo bp_get_link_avatar_thumb();
}
	function bp_get_link_avatar_thumb( $link = false ) {
		return bp_get_link_avatar( 'type=thumb', $link );
	}

function bp_link_avatar_mini() {
	echo bp_get_link_avatar_mini();
}
	function bp_get_link_avatar_mini( $link = false ) {
		return bp_get_link_avatar( 'type=thumb&width=30&height=30' );
	}

function bp_link_avatar_display_size() {
	echo bp_get_link_avatar_display_size();
}
	function bp_get_link_avatar_display_size() {
		global $links_template;

		return apply_filters( 'bp_get_link_avatar_display_size', $links_template->avatar_display_size() );
	}

function bp_link_user_avatar() {
	echo bp_get_link_user_avatar();
}
	function bp_get_link_user_avatar( $args = '', $link = false ) {
		global $bp, $links_template;

		if ( !$link )
			$link =& $links_template->link;

		$defaults = array(
			'type' => 'full',
			'width' => false,
			'height' => false,
			'class' => 'owner-avatar',
			'id' => false,
			'alt' => false
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		return apply_filters( 'bp_get_link_user_avatar', bp_core_fetch_avatar( array( 'item_id' => $link->user_id, 'type' => $type, 'alt' => $alt, 'class' => $class, 'width' => $width, 'height' => $height ) ) );
	}

function bp_link_user_avatar_thumb() {
	echo bp_get_link_user_avatar_thumb();
}
	function bp_get_link_user_avatar_thumb( $link = false ) {
		return bp_get_link_user_avatar( 'type=thumb', $link );
	}

function bp_link_user_avatar_mini() {
	echo bp_get_link_user_avatar_mini();
}
	function bp_get_link_user_avatar_mini( $link = false ) {
		return bp_get_link_user_avatar( 'type=thumb&width=20&height=20', $link );
	}
	
function bp_link_last_active() {
		echo bp_get_link_last_active();
}
	function bp_get_link_last_active( $link = false ) {
		global $links_template;

		if ( !$link )
			$link =& $links_template->link;

		$last_active = bp_links_get_linkmeta( $link->id, 'last_activity' );

		if ( empty( $last_active ) ) {
			return __( 'not yet active', 'buddypress-links' );
		} else {
			return apply_filters( 'bp_get_link_last_active', bp_core_time_since( $last_active ) );
		}
	}

function bp_link_permalink() {
	echo bp_get_link_permalink();
}
	function bp_get_link_permalink( $link = false ) {
		global $links_template, $bp;

		if ( !$link )
			$link =& $links_template->link;

		return apply_filters( 'bp_get_link_permalink', $bp->root_domain . '/' . $bp->links->slug . '/' . $link->slug );
	}

function bp_link_userlink() {
	echo bp_get_link_userlink();
}
	function bp_get_link_userlink( $link = false ) {
		global $links_template, $bp;

		if ( !$link )
			$link =& $links_template->link;

		return apply_filters( 'bp_get_link_userlink', bp_core_get_userlink( $link->user_id ) );
	}

function bp_link_slug() {
	echo bp_get_link_slug();
}
	function bp_get_link_slug( $link = false ) {
		global $links_template;

		if ( !$link )
			$link =& $links_template->link;

		return apply_filters( 'bp_get_link_slug', $link->slug );
	}

function bp_link_has_description() {
	echo bp_get_link_has_description();
}
	function bp_get_link_has_description( $link = false ) {
		global $links_template;

		if ( !$link )
			$link =& $links_template->link;

		return apply_filters( 'bp_get_link_has_description', ( strlen( $link->description ) >= 1 ), $link->description );
	}

function bp_link_description() {
	echo bp_get_link_description();
}
	function bp_get_link_description( $link = false ) {
		global $links_template;

		if ( !$link )
			$link =& $links_template->link;

		return apply_filters( 'bp_get_link_description', stripslashes($link->description) );
	}

function bp_link_description_excerpt( $length = 55 ) {
	echo bp_get_link_description_excerpt( $length );
}
	function bp_get_link_description_excerpt( $link = false, $length = 55 ) {
		global $links_template;

		if ( !$link )
			$link =& $links_template->link;

		return apply_filters( 'bp_get_link_description_excerpt', bp_create_excerpt( $link->description, $length ) );
	}

function bp_link_vote_count() {
	echo bp_get_link_vote_count();
}
	function bp_get_link_vote_count( $link = false ) {
		global $links_template;

		if ( !$link )
			$link =& $links_template->link;

		return apply_filters( 'bp_get_link_vote_count', $link->vote_count );
	}

function bp_link_vote_count_text() {
	echo bp_get_link_vote_count_text();
}
	function bp_get_link_vote_count_text( $link = false ) {

		$vote_count = bp_get_link_vote_count( $link );

		if ( 1 == $vote_count )
			return apply_filters( 'bp_get_link_vote_count_text', sprintf( __( '%s vote', 'buddypress-links' ), $vote_count ) );
		else
			return apply_filters( 'bp_get_link_vote_count_text', sprintf( __( '%s votes', 'buddypress-links' ), $vote_count ) );
	}

function bp_link_vote_total() {
	echo bp_get_link_vote_total();
}
	function bp_get_link_vote_total( $link = false ) {
		global $links_template;

		if ( !$link )
			$link =& $links_template->link;

		return apply_filters( 'bp_get_link_vote_total', $link->vote_total );
	}

function bp_link_popularity() {
	echo bp_get_link_popularity();
}
	function bp_get_link_popularity( $link = false ) {
		global $links_template;

		if ( !$link )
			$link =& $links_template->link;

		return apply_filters( 'bp_get_link_popularity', $link->popularity );
	}

function bp_link_date_created() {
	echo bp_get_link_date_created();
}
	function bp_get_link_date_created( $link = false ) {
		global $links_template;

		if ( !$link )
			$link =& $links_template->link;

		return apply_filters( 'bp_get_link_date_created', date( get_option( 'date_format' ), $link->date_created ) );
	}

function bp_link_time_since_created() {
	echo bp_get_link_time_since_created();
}
	function bp_get_link_time_since_created( $link = false ) {
		global $links_template;

		if ( !$link )
			$link =& $links_template->link;

		return apply_filters( 'bp_get_link_time_since_created', bp_core_time_since( $link->date_created ) );
	}

function bp_link_share_has_profile_link() {
	echo bp_get_link_share_has_profile_link();
}
	function bp_get_link_share_has_profile_link( $link = false ) {
		global $links_template;

		if ( !$link )
			$link =& $links_template->link;

		return apply_filters( 'bp_get_link_share_has_profile_link', (boolean) bp_get_link_share_profile_link_user_id( $link ) );
	}

function bp_link_share_profile_link_user_id() {
	echo bp_get_link_share_profile_link_user_id();
}
	function bp_get_link_share_profile_link_user_id( $link = false ) {
		global $links_template;

		if ( !$link )
			$link =& $links_template->link;

		return apply_filters( 'bp_get_link_share_profile_link_user_id', $link->prlink_user_id );
	}

function bp_link_share_profile_link_date_created() {
	echo bp_get_link_share_profile_link_date_created();
}
	function bp_get_link_share_profile_link_date_created( $link = false ) {
		global $links_template;

		if ( !$link )
			$link =& $links_template->link;

		return apply_filters( 'bp_get_link_share_profile_link_date_created', date( get_option( 'date_format' ), $link->prlink_date_created ) );
	}

function bp_link_play_button() {
	echo bp_get_link_play_button();
}
	function bp_get_link_play_button( $link = false ) {

		global $links_template;

		if ( !$link )
			$link =& $links_template->link;

		$button_html = null;

		if ( $link->embed_status_enabled() ) {

			$class = null;

			if ( $link->embed()->avatar_play_video() === true )
				$class = 'link-play link-play-video';
			elseif ( $link->embed()->avatar_play_photo() === true )
				$class = 'link-play link-play-photo';

			if ( $class )
				$button_html = sprintf( '<a href="%s" id="link-play-%d" class="%s"></a>', bp_get_link_permalink( $link ), bp_get_link_id( $link ), $class );
		}

		return apply_filters( 'bp_get_link_play_button', $button_html );
	}

function bp_link_is_admin() {
	global $bp;
	
	return $bp->is_item_admin;
}

// this is for future use
function bp_link_is_mod() {
	global $bp;
	
	return $bp->is_item_mod;
}

function bp_link_show_no_links_message() {
	global $bp;
	
	if ( !bp_links_total_links_for_user( $bp->displayed_user->id ) )
		return true;
		
	return false;
}

function bp_links_pagination_links() {
	echo bp_get_links_pagination_links();
}
	function bp_get_links_pagination_links() {
		global $links_template;

		return apply_filters( 'bp_get_links_pagination_links', $links_template->pag_links );
	}

function bp_links_pagination_count() {
	echo bp_get_links_pagination_count();
}
	function bp_get_links_pagination_count() {
		global $bp, $links_template;

		$from_num = intval( ( $links_template->pag_page - 1 ) * $links_template->pag_num ) + 1;
		$to_num = ( $from_num + ( $links_template->pag_num - 1 ) > $links_template->total_link_count ) ? $links_template->total_link_count : $from_num + ( $links_template->pag_num - 1) ;

		return sprintf( __( 'Viewing link %1$d to %2$d (of %3$d links)', 'buddypress-links' ), $from_num, $to_num, $links_template->total_link_count ) . '&nbsp<span class="ajax-loader"></span>';
	}

function bp_links_total_link_count() {
	echo bp_get_links_total_link_count();
}
	function bp_get_links_total_link_count() {
		return apply_filters( 'bp_get_links_total_link_count', bp_links_total_links() );
	}

function bp_link_total_link_count_for_user() {
	echo bp_get_link_total_link_count_for_user();
}
	function bp_get_link_total_link_count_for_user( $user_id = false ) {
		return apply_filters( 'bp_get_link_total_link_count_for_user', bp_links_total_links_for_user( $user_id ) );
	}

function bp_link_activity_post_count() {
	echo bp_get_link_activity_post_count();
}
	function bp_get_link_activity_post_count( $link = false ) {
		global $links_template;

		if ( !$link )
			$link = $links_template->link;

		return apply_filters( 'bp_get_link_activity_post_count', $link->get_activity_post_count() );
	}

function bp_link_admin_tabs() {
	global $bp, $links_template;

	$link = ( $links_template->link ) ? $links_template->link : $bp->links->current_link;
	
	$current_tab = $bp->action_variables[0];
?>
	<?php if ( $bp->is_item_admin ) { ?>
		<li<?php if ( 'edit-details' == $current_tab || empty( $current_tab ) ) : ?> class="current"<?php endif; ?>><a href="<?php echo $bp->root_domain . '/' . $bp->links->slug ?>/<?php echo $link->slug ?>/admin/edit-details"><?php _e( 'Edit Details', 'buddypress-links' ) ?></a></li>
	<?php } ?>
	
	<?php
		if ( !$bp->is_item_admin )
			return false;
	?>
	<li<?php if ( 'link-avatar' == $current_tab ) : ?> class="current"<?php endif; ?>><a href="<?php echo $bp->root_domain . '/' . $bp->links->slug ?>/<?php echo $link->slug ?>/admin/link-avatar"><?php _e( 'Link Avatar', 'buddypress-links' ) ?></a></li>

	<?php do_action( 'bp_link_admin_tabs', $current_tab, $link->slug ) ?>
	
	<li<?php if ( 'delete-link' == $current_tab ) : ?> class="current"<?php endif; ?>><a href="<?php echo $bp->root_domain . '/' . $bp->links->slug ?>/<?php echo $link->slug ?>/admin/delete-link"><?php _e( 'Delete Link', 'buddypress-links' ) ?></a></li>
<?php
}

function bp_link_status_message( $link = false ) {
	global $links_template;
	
	if ( !$link )
		$link =& $links_template->link;
	
	if ( BP_Links_Link::STATUS_HIDDEN == $link->status ) {
		_e( 'This is a hidden link. Only the user who owns it can view it.', 'buddypress-links' );
	} elseif ( BP_Links_Link::STATUS_FRIENDS == $link->status ) {
		_e( 'This is a friends only link. Only the owner\'s friends can view it.', 'buddypress-links' );
	} else {
		_e( 'You do not have permission to access this link.', 'buddypress-links' );
	}
}

/***************************************************************************
 * Link Creation Process Template Tags
 **/

function bp_link_details_form_action() {
	echo bp_get_link_details_form_action();
}
	function bp_get_link_details_form_action() {
		global $bp;

		if ( bp_links_current_link_exists() ) {
			$form_action = bp_get_link_admin_form_action();
		} else {
			switch ( $bp->current_component ) {
				default:
				case $bp->links->slug:
					$form_action = $bp->loggedin_user->domain . $bp->links->slug . '/create';
					break;
				case $bp->groups->slug:
					$form_action = sprintf( '%s/%s/%s/%s/create', $bp->root_domain, $bp->groups->slug, $bp->groups->current_group->slug, $bp->links->slug ) ;
			}
			
		}

		return apply_filters( 'bp_get_link_details_form_action', $form_action, $admin_action );
	}

function bp_link_details_form_link_group_id() {
	echo bp_get_link_details_form_link_group_id();
}
	function bp_get_link_details_form_link_group_id() {
		global $bp;

		return ( $bp->groups->slug == $bp->current_component ) ? bp_get_group_id() : null;
	}

function bp_link_details_form_link_url_readonly() {
	echo bp_get_link_details_form_link_url_readonly();
}
	function bp_get_link_details_form_link_url_readonly() {
		global $bp;

		if ( isset( $_POST['link-url-readonly'] ) ) {
			return ( empty( $_POST['link-url-readonly'] ) ) ? 0 : 1;
		} elseif ( bp_links_current_link_embed_enabled() )  {
			return ( bp_links_current_link_embed_service() instanceof BP_Links_Embed_From_Url ) ? 1 : 0;
		} else {
			return 0;
		}
	}

function bp_link_details_form_name_desc_fields_display() {
	echo bp_get_link_details_form_name_desc_fields_display();
}
	function bp_get_link_details_form_name_desc_fields_display() {
		global $bp;

		if ( isset( $_POST['link-url-embed-data'] ) ) {
			return ( !empty( $_POST['link-url-embed-data'] ) && empty( $_POST['link-url-embed-edit-text'] ) ) ? 0 : 1;
		} elseif ( bp_links_current_link_embed_enabled() )  {
			return ( bp_links_current_link_embed_service() instanceof BP_Links_Embed_From_Url ) ? 0 : 1;
		} else {
			return 0;
		}
	}

function bp_link_details_form_avatar_fields_display() {
	echo bp_get_link_details_form_avatar_fields_display();
}
	function bp_get_link_details_form_avatar_fields_display() {
		return ( empty( $_POST['link-avatar-fields-display'] ) ) ? 0 : 1;
	}

function bp_link_details_form_avatar_option() {
	echo bp_get_link_details_form_avatar_option();
}
	function bp_get_link_details_form_avatar_option() {
		return ( empty( $_POST['link-avatar-option'] ) ) ? 0 : 1;
	}

function bp_link_details_form_settings_fields_display() {
	echo bp_get_link_details_form_settings_fields_display();
}
	function bp_get_link_details_form_settings_fields_display() {
		return ( empty( $_POST['link-settings-fields-display'] ) ) ? 0 : 1;
	}

function bp_link_details_form_category_id() {
	echo bp_get_link_details_form_category_id();
}
	function bp_get_link_details_form_category_id() {
		global $bp;

		if ( !empty( $_POST['link-category'] ) ) {
			$category_id = $_POST['link-category'];
		} else {
			$category_id = $bp->links->current_link->category_id;
		}

		return apply_filters( 'bp_get_link_details_form_category_id', $category_id );
	}

function bp_link_details_form_url() {
	echo bp_get_link_details_form_url();
}
	function bp_get_link_details_form_url() {
		global $bp;

		if ( !empty( $_POST['link-url'] ) ) {
			$link_url = $_POST['link-url'];
		} else {
			$link_url = $bp->links->current_link->url;
		}

		return apply_filters( 'bp_get_link_details_form_url', $link_url );
	}

function bp_get_link_details_form_embed_service() {
	global $bp;

	if ( !empty( $_POST['link-url-embed-data'] ) ) {
		try {
			// load service
			$service = BP_Links_Embed::LoadService( trim( $_POST['link-url-embed-data'] ) );
			// valid service?
			if ( $service instanceof BP_Links_Embed_Service ) {
				return $service;
			}
		} catch ( BP_Links_Embed_Exception $e ) {
			return false;
		}
	} elseif ( bp_links_current_link_embed_enabled() ) {
		return bp_links_current_link_embed_service();
	}

	return false;
}

function bp_link_details_form_url_embed_data() {
	echo bp_get_link_details_form_url_embed_data();
}
	function bp_get_link_details_form_url_embed_data() {

		$embed_data = null;
		$embed_service = bp_get_link_details_form_embed_service();

		if ( $embed_service instanceof BP_Links_Embed_From_Url ) {
			$embed_data = $embed_service->export_data();
		}

		return apply_filters( 'bp_get_link_details_form_url_embed_data', $embed_data );
	}

function bp_link_details_form_name() {
	echo bp_get_link_details_form_name();
}
	function bp_get_link_details_form_name() {
		global $bp;

		if ( !empty( $_POST['link-name'] ) ) {
			$link_name = $_POST['link-name'];
		} else {
			$link_name = $bp->links->current_link->name;
		}

		return apply_filters( 'bp_get_link_details_form_name', $link_name );
	}

function bp_link_details_form_description() {
	echo bp_get_link_details_form_description();
}
	function bp_get_link_details_form_description() {
		global $bp;

		if ( !empty( $_POST['link-desc'] ) ) {
			$link_description = $_POST['link-desc'];
		} else {
			$link_description = $bp->links->current_link->description;
		}

		return apply_filters( 'bp_get_link_details_form_description', $link_description );
	}

function bp_link_details_form_status() {
	echo bp_get_link_details_form_status();
}
	function bp_get_link_details_form_status() {
		global $bp;

		$link_status = null;

		if ( !empty( $_POST['link-status'] ) ) {
			if ( bp_links_is_valid_status( $_POST['link-status'] ) ) {
				$link_status = (integer) $_POST['link-status'];
			}
		} else {
			$link_status = $bp->links->current_link->status;
		}

		return apply_filters( 'bp_get_link_details_form_status', $link_status );
	}

function bp_link_details_form_avatar_thumb_default( $class = '' ) {
	echo bp_get_link_details_form_avatar_thumb_default( $class );
}
	function bp_get_link_details_form_avatar_thumb_default( $class = '' ) {
		return apply_filters( 'bp_get_link_details_form_avatar_thumb_default', bp_get_link_avatar( array( 'class' => $class, 'height' => 80, 'width' => 80 ) ) );
	}

function bp_link_details_form_avatar_thumb() {
	echo bp_get_link_details_form_avatar_thumb();
}
	function bp_get_link_details_form_avatar_thumb() {

		if ( bp_links_admin_current_action_variable() ) {

			return bp_get_link_avatar( 'width=100&height=100', bp_links_current_link() );

		} else {

			$embed_service = bp_get_link_details_form_embed_service();

			if ( $embed_service instanceof BP_Links_Embed_Service ) {
				return sprintf( '<img src="%1$s" class="avatar-current" alt="%2$s">', $embed_service->image_thumb_url(), $embed_service->title() );
			} else {
				return bp_get_link_details_form_avatar_thumb_default( 'avatar-current' );
			}
		}
	}

function bp_link_admin_form_action() {
	echo bp_get_link_admin_form_action();
}
	function bp_get_link_admin_form_action() {
		global $bp;

		$action = bp_links_admin_current_action_variable();

		if ( $action ) {
			return apply_filters( 'bp_get_link_admin_form_action', bp_get_link_permalink( $bp->links->current_link ) . '/admin/' . $action, $action );
		} else {
			die('Not an admin path!');
		}
	}

function bp_link_avatar_form_avatar() {
	echo bp_get_link_avatar_form_avatar();
}
	function bp_get_link_avatar_form_avatar() {
		return apply_filters( 'bp_get_link_avatar_form_avatar', bp_get_link_avatar( 'size=full', bp_links_current_link() ) );
	}

function bp_link_avatar_form_delete_link() {
	echo bp_get_link_avatar_form_delete_link();
}
	function bp_get_link_avatar_form_delete_link() {
		global $bp;

		return apply_filters( 'bp_get_link_avatar_delete_link', wp_nonce_url( bp_get_link_permalink( $bp->links->current_link ) . '/admin/link-avatar/delete', 'bp_link_avatar_delete' ) );
	}

function bp_link_avatar_form_embed_html() {
	echo bp_get_link_avatar_form_embed_html();
}
	function bp_get_link_avatar_form_embed_html() {

		$html = ( isset( $_POST['embed-html'] ) ) ? $_POST['embed-html'] : null;

		return apply_filters( 'bp_get_link_avatar_form_embed_html', $html );
	}

function bp_link_avatar_form_embed_html_display() {
	echo bp_get_link_avatar_form_embed_html_display();
}
	function bp_get_link_avatar_form_embed_html_display() {
		if ( bp_links_current_link_embed_enabled() ) {
			return ( bp_links_current_link_embed_service()->avatar_only() ) ? 1 : 0;
		} else {
			return 1;
		}
	}

function bp_link_user_group_options( $user_id = false, $max = 100 ) {
	echo bp_get_link_user_group_options( $user_id, $max );
}
	function bp_get_link_user_group_options( $user_id = false, $max = 100 ) {
		global $bp;

		if ( empty( $user_id ) )
			$user_id = $bp->loggedin_user->id;

		$html = null;
		$groups_tpl = new BP_Groups_Template( $user_id, 'alphabetical', 1, (integer) $max, (integer) $max, false, false, false );

		while ( $groups_tpl->groups() ) {
			$groups_tpl->the_group();
			$html .= sprintf( '<option value="%d">%s</option>', bp_get_group_id( $groups_tpl->group ), bp_get_group_name( $groups_tpl->group ) );
		}

		return $html;
	}

function bp_link_share_button( $link = false ) {
	global $bp, $links_template;

	if ( is_user_logged_in() ) {

		if ( !$link )
			$link = $links_template->link;

		$html = apply_filters( 'bp_link_share_button_html', '<input type="submit" name="link-share" id="linkshare-%d" value="%s">' );
		printf( $html, $link->id, __( 'Share', 'buddypress-links' ) );
	}
}

function bp_link_share_remove_button( $link, $object, $object_id ) {
	global $bp;

	if ( !is_user_logged_in() )
		return false;
	
	$remove_from = false;

	switch ( $object ) {

		default:
		case 'profile':
			// only display to share creator in the global context
			if ( bp_get_link_share_has_profile_link( $link ) )
				$remove_from = __( 'Remove from Profile', 'buddypress-links' );
			break;
		
		case 'group':
			// only display to share creator and group admins/mods in the group context
			if ( is_numeric( $object_id ) && $object_id >= 1 ) {
				$group = new BP_Groups_Group( (integer) $object_id );
			} else {
				break;
			}

			if ( $group->id ) {
				switch ( true ) {
					case ( $link->user_id == $bp->loggedin_user->id ):
					case $bp->is_item_admin:
					case $bp->is_item_mod:
						$remove_from = __( 'Remove from Group', 'buddypress-links' );
				}
			}
			break;
	}

	if ( $remove_from ) {
		$html = apply_filters( 'bp_link_share_remove_button_html', '<input type="submit" name="link-share-remove" id="linkshareremove-%d" value="%s">' );
		printf( $html, $link->id, $remove_from );
	}
}

function bp_link_hidden_fields() {
	if ( isset( $_REQUEST['s'] ) ) {
		echo '<input type="hidden" id="search_terms" value="' . attribute_escape( $_REQUEST['s'] ) . '" name="search_terms" />';
	}

	if ( isset( $_REQUEST['links_search'] ) ) {
		echo '<input type="hidden" id="search_terms" value="' . attribute_escape( $_REQUEST['links_search'] ) . '" name="search_terms" />';
	}
}

function bp_link_feed_item_guid() {
	echo bp_get_link_feed_item_guid();
}
	function bp_get_link_feed_item_guid() {
		return apply_filters( 'bp_get_link_feed_item_guid', bp_get_link_permalink() );
	}

function bp_link_feed_item_title() {
	echo bp_get_link_feed_item_title();
}
	function bp_get_link_feed_item_title() {
		return apply_filters( 'bp_get_link_feed_item_title', bp_get_link_name() );
	}

function bp_link_feed_item_link() {
	echo bp_get_link_feed_item_link();
}
	function bp_get_link_feed_item_link() {
		return apply_filters( 'bp_get_link_feed_item_link', bp_get_link_permalink() );
	}

function bp_link_feed_item_date( $link = false ) {
	echo bp_get_link_feed_item_date( $link );
}
	function bp_get_link_feed_item_date( $link = false ) {
		global $links_template;

		if ( !$link )
			$link =& $links_template->link;

		return apply_filters( 'bp_get_link_feed_item_date', date('D, d M Y H:i:s O', $link->date_created ) );
	}

function bp_link_feed_item_description() {
	echo bp_get_link_feed_item_description();
}
	function bp_get_link_feed_item_description() {
		return apply_filters( 'bp_get_link_feed_item_description', bp_get_link_description() );
	}

/********************************************************************************
 * Links Categories Template Tags
 **/

class BP_Links_Categories_Template {
	var $current_category = -1;
	var $category_count;
	var $categories;
	var $category;

	var $in_the_loop;

	var $pag_page;
	var $pag_num;
	var $pag_links;
	var $total_category_count;

	function bp_links_categories_template( $type, $per_page, $max ) {
		global $bp;

		$this->pag_page = isset( $_REQUEST['lcpage'] ) ? intval( $_REQUEST['lcpage'] ) : 1;
		$this->pag_num = isset( $_REQUEST['num'] ) ? intval( $_REQUEST['num'] ) : $per_page;

		if ( isset( $_REQUEST['s'] ) )
			$filter = $_REQUEST['s'];

		switch ( $type ) {
			case 'all':
			default:
				$this->categories = BP_Links_Category::get_all_filtered( $filter, $this->pag_num, $this->pag_page );
				break;
		}

		if ( !$max || $max >= (int)$this->categories['total'] )
			$this->total_category_count = (int)$this->categories['total'];
		else
			$this->total_category_count = (int)$max;

		$this->categories = $this->categories['categories'];

		if ( $max ) {
			if ( $max >= count($this->categories) )
				$this->category_count = count($this->categories);
			else
				$this->category_count = (int)$max;
		} else {
			$this->category_count = count($this->categories);
		}

		if ( (int) $this->total_category_count && (int) $this->pag_num ) {
			$this->pag_links = paginate_links( array(
				'base' => add_query_arg( 'lcpage', '%#%' ),
				'format' => '',
				'total' => ceil( (int) $this->total_category_count / (int) $this->pag_num ),
				'current' => (int) $this->pag_page,
				'prev_text' => '&laquo;',
				'next_text' => '&raquo;',
				'mid_size' => 1
			));
		}
	}

	function has_categories() {
		if ( $this->category_count )
			return true;

		return false;
	}

	function next_category() {
		$this->current_category++;
		$this->category = $this->categories[$this->current_category];

		return $this->category;
	}

	function rewind_categories() {
		$this->current_category = -1;
		if ( $this->category_count > 0 ) {
			$this->category = $this->categories[0];
		}
	}

	function categories() {
		if ( $this->current_category + 1 < $this->category_count ) {
			return true;
		} elseif ( $this->current_category + 1 == $this->category_count ) {
			do_action('loop_end');
			// Do some cleaning up after the loop
			$this->rewind_categories();
		}

		$this->in_the_loop = false;
		return false;
	}

	function the_category() {
		global $category;

		$this->in_the_loop = true;
		$this->category = $this->next_category();

		if ( !$category = wp_cache_get( 'bp_links_link_category_nouserdata_' . $this->category->category_id, 'bp' ) ) {
			$category = new BP_Links_Category( $this->category->category_id, false, false );
			wp_cache_set( 'bp_links_link_category_nouserdata_' . $this->category->category_id, $category, 'bp' );
		}

		$this->category = $category;

		if ( 0 == $this->current_category ) // loop has just started
			do_action('loop_start');
	}
}

function bp_rewind_links_categories() {
	global $links_categories_template;

	$links_categories_template->rewind_categories();
}

function bp_has_links_categories( $args = '' ) {
	global $bp, $links_categories_template;

	$defaults = array(
		'type' => 'all',
		'per_page' => 10,
		'max' => false
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	if ( $max ) {
		if ( $per_page > $max )
			$per_page = $max;
	}

	$links_categories_template = new BP_Links_Categories_Template( $type, $per_page, $max );
	return apply_filters( 'bp_has_links_categories', $links_categories_template->has_categories(), &$links_categories_template );
}

function bp_links_categories() {
	global $links_categories_template;

	return $links_categories_template->categories();
}

function bp_links_categories_category() {
	global $links_categories_template;

	return $links_categories_template->the_category();
}

function bp_links_categories_pagination_count() {
	global $bp, $links_categories_template;

	$from_num = intval( ( $links_categories_template->pag_page - 1 ) * $links_categories_template->pag_num ) + 1;
	$to_num = ( $from_num + ( $links_categories_template->pag_num - 1 ) > $links_categories_template->total_category_count ) ? $links_categories_template->total_category_count : $from_num + ( $links_categories_template->pag_num - 1) ;

	echo sprintf( __( 'Viewing category %1$d to %2$d (of %3$d categories)', 'buddypress-links' ), $from_num, $to_num, $links_categories_template->total_category_count ); ?> &nbsp;
	<span class="ajax-loader"></span><?php
}

function bp_links_categories_pagination_links() {
	echo bp_get_links_categories_pagination_links();
}
	function bp_get_links_categories_pagination_links() {
		global $links_categories_template;

		return apply_filters( 'bp_get_links_categories_pagination_links', $links_categories_template->pag_links );
	}

function bp_links_categories_category_id() {
	echo bp_get_links_categories_category_id();
}
	function bp_get_links_categories_category_id() {
		global $links_categories_template;

		return apply_filters( 'bp_get_links_categories_category_id', $links_categories_template->category->id );
	}

function bp_links_categories_category_name() {
	echo bp_get_links_categories_category_name();
}
	function bp_get_links_categories_category_name() {
		global $links_categories_template;

		return apply_filters( 'bp_get_links_categories_category_name', $links_categories_template->category->name );
	}

function bp_links_categories_category_description() {
	echo bp_get_links_categories_category_description();
}
	function bp_get_links_categories_category_description() {
		global $links_categories_template;

		return apply_filters( 'bp_get_links_categories_category_description', $links_categories_template->category->description );
	}

function bp_links_categories_category_slug() {
	echo bp_get_links_categories_category_slug();
}
	function bp_get_links_categories_category_slug() {
		global $links_categories_template;

		return apply_filters( 'bp_get_links_categories_category_slug', $links_categories_template->category->slug );
	}

function bp_links_categories_category_priority() {
	echo bp_get_links_categories_category_priority();
}
	function bp_get_links_categories_category_priority() {
		global $links_categories_template;

		return apply_filters( 'bp_get_links_categories_category_priority', $links_categories_template->category->priority );
	}

function bp_links_categories_category_link_count() {
	echo bp_get_links_categories_category_link_count();
}
	function bp_get_links_categories_category_link_count() {
		global $links_categories_template;

		return apply_filters( 'bp_get_links_categories_category_link_count', BP_Links_Category::get_link_count( $links_categories_template->category->id ) );
	}

function bp_links_categories_category_date_created() {
	echo bp_get_links_categories_category_date_created();
}
	function bp_get_links_categories_category_date_created() {
		global $links_categories_template;

		return apply_filters( 'bp_get_links_categories_category_date_created', date( get_option( 'date_format' ), $links_categories_template->category->date_created ) );
	}

function bp_links_categories_category_date_updated() {
	echo bp_get_links_categories_category_date_updated();
}
	function bp_get_links_categories_category_date_updated() {
		global $links_categories_template;

		return apply_filters( 'bp_get_links_categories_category_date_updated', date( get_option( 'date_format' ), $links_categories_template->category->date_updated ) );
	}

function bp_links_categories_hidden_fields() {
	if ( isset( $_REQUEST['s'] ) ) {
		echo '<input type="hidden" id="search_terms" value="' . attribute_escape( $_REQUEST['s'] ) . '" name="search_terms" />';
	}
}

function bp_links_category_select_options( $selected_category_id = null, $element_id = 'category', $element_class = '' ) {

	do_action( 'bp_before_links_category_select_options' );

	// grab all categories
	$categories = BP_Links_Category::get_all();

	$class_string = ( empty( $element_class ) ) ? null : sprintf( ' class="%s"', $element_class );

	foreach ( $categories as $category ) {
		// populate
		$category = new BP_Links_Category( $category->category_id );
		// is this one selected?
		$selected = ( $selected_category_id == $category->id ) ? ' selected="selected"' : null;
		// output it
		echo sprintf( '<option value="%d"%s />%s</option>', $category->id, $selected, $category->name ) . PHP_EOL;
	}

	do_action( 'bp_after_links_category_select_options' );
}

function bp_links_category_radio_options( $selected_category_id = 1, $element_name = 'category', $element_class = '' ) {

	do_action( 'bp_before_links_category_radio_options' );

	// grab all categories
	$categories = BP_Links_Category::get_all();

	foreach ( $categories as $category ) {
		// populate
		$category = new BP_Links_Category( $category->category_id );
		// is this one selected?
		$selected = ( $selected_category_id == $category->id ) ? ' checked="checked"' : null;
		// has class string?
		$class_string = ( empty( $element_class ) ) ? null : sprintf( ' class="%s"', $element_class );
		// output it
		echo sprintf( '<input type="radio" name="%s" value="%d"%s%s />%s ', $element_name, $category->id, $class_string, $selected, $category->name );
	}
	// print newline
	echo PHP_EOL;

	do_action( 'bp_after_links_category_radio_options' );

}

function bp_links_category_radio_options_with_all( $selected_category_id = 1, $element_name = 'category', $element_class = '' ) {

	do_action( 'bp_before_links_category_radio_options_with_all' );

	// is this one selected?
	$selected = ( empty( $selected_category_id ) ) ? ' checked="checked"' : null;
	// has class string?
	$class_string = ( empty( $element_class ) ) ? null : sprintf( ' class="%s"', $element_class );
	// output it
	echo sprintf( '<input type="radio" name="%s" value=""%s%s />%s ', $element_name, $class_string, $selected, __( 'All', 'buddypress-links' ) );

	do_action( 'bp_after_links_category_radio_options_with_all' );

	bp_links_category_radio_options();
}

/***
 * Links RSS Feed Template Tags
 */

// TODO need to handle 'all links' AND 'my links'
function bp_directory_links_feed_link() {
	echo bp_get_directory_links_feed_link();
}
	function bp_get_directory_links_feed_link() {
		global $bp;
		/*
		if ( !empty( $_POST['scope'] ) && $_POST['scope'] == 'mylinks' )
			return $bp->loggedin_user->domain . BP_LINKS_SLUG . '/my-links/feed/';
		else
		*/
		return apply_filters( 'bp_get_directory_links_feed_link', site_url( $bp->links->slug . '/feed' ) );
	}

function bp_link_activity_feed_link() {
	echo bp_get_link_activity_feed_link();
}
	function bp_get_link_activity_feed_link() {
		global $bp;

		return apply_filters( 'bp_get_link_activity_feed_link', bp_get_link_permalink( $bp->links->current_link ) . '/feed/' );
	}


/*******************************
 * Links Profile Template Tags
 **/

function bp_links_notification_settings() {
	global $current_user; ?>
	<table class="notification-settings" id="links-notification-settings">
		<tr>
			<th class="icon"></th>
			<th class="title"><?php _e( 'Links', 'buddypress-links' ) ?></th>
			<th class="yes"><?php _e( 'Yes', 'buddypress-links' ) ?></th>
			<th class="no"><?php _e( 'No', 'buddypress-links' )?></th>
		</tr>
		<tr>
			<td></td>
			<td><?php _e( 'A member posts a comment on a link you created', 'buddypress-links' ) ?></td>
			<td class="yes"><input type="radio" name="notifications[notification_links_activity_post]" value="yes" <?php if ( !get_usermeta( $current_user->id, 'notification_links_activity_post') || 'yes' == get_usermeta( $current_user->id, 'notification_links_activity_post') ) { ?>checked="checked" <?php } ?>/></td>
			<td class="no"><input type="radio" name="notifications[notification_links_activity_post]" value="no" <?php if ( 'no' == get_usermeta( $current_user->id, 'notification_links_activity_post') ) { ?>checked="checked" <?php } ?>/></td>
		</tr>
		<?php do_action( 'bp_links_notification_settings' ); ?>
	</table>
<?php
}


/***
 * Links Vote Panel Template Tags
 */

function bp_link_vote_panel( $show_count = true ) {
	echo bp_get_link_vote_panel( $show_count );
}
	function bp_get_link_vote_panel( $show_count = true ) {

		$show_count = apply_filters( 'bp_get_link_vote_panel_show_count', $show_count );

		return sprintf('
			<div class="link-vote-panel" id="link-vote-panel-%2$d">
				%1$s%3$s
				%4$s
			%5$s</div>',
			apply_filters( 'bp_before_link_vote_panel_content', '' ), // arg 1
			bp_get_link_id(), // arg 2
			bp_get_link_vote_panel_clickers(), // arg 3
			( $show_count ) ? bp_get_link_vote_panel_count() : null, // arg 4
			apply_filters( 'bp_after_link_vote_panel_content', '' )	// arg 5
		);
	}

function bp_link_vote_panel_clickers() {
	echo bp_get_link_vote_panel_clickers();
}
	function bp_get_link_vote_panel_clickers() {

		$html = '
			<div class="clickers">
				<a href="#vu" id="vote-up-%1$d" class="vote up"></a>
				<div id="vote-total-%1$d" class="vote-total">%2$+d</div>
				<a href="#vd" id="vote-down-%1$d" class="vote down"></a>
			</div>';

		$html_filtered = apply_filters( 'bp_get_link_vote_panel_clickers', $html );

		return sprintf(
			$html_filtered,
			bp_get_link_id(), // arg 1
			bp_get_link_vote_total() // arg 2
		);
	}

function bp_link_vote_panel_count() {
	echo bp_get_link_vote_panel_count();
}
	function bp_get_link_vote_panel_count() {

		$html = '<div class="vote-count"><span id="vote-count-%1$d">%2$d</span> %3$s</div>';
		
		$html_filtered = apply_filters( 'bp_get_link_vote_panel_count', $html );

		return sprintf(
			$html_filtered,
			bp_get_link_id(), // arg 1
			bp_get_link_vote_count(), // arg 2
			__('Votes', 'buddypress-links') // arg 3
		);
	}
	
function bp_link_vote_panel_form() {
	printf( '<form action="%s/" method="post" id="link-vote-form">', site_url() );
	wp_nonce_field( 'link_vote', '_wpnonce-link-vote' );
	echo '</form>' . PHP_EOL;
}

/*********************************
 * Links List Template Helper Tags
 **/

function bp_link_list_item_id() {
	echo bp_get_link_list_item_id();
}
	function bp_get_link_list_item_id() {
		return apply_filters( 'bp_get_link_list_item_id', 'linklistitem-' . bp_get_link_id() );
	}

function bp_link_list_item_class() {
	echo bp_get_link_list_item_class();
}
	function bp_get_link_list_item_class() {
		return apply_filters( 'bp_get_link_list_item_class', 'avmax-' . bp_get_link_avatar_display_size() );
	}

function bp_link_list_item_avatar() {
	echo bp_get_link_list_item_avatar();
}
	function bp_get_link_list_item_avatar() {
		switch ( bp_get_link_avatar_display_size() ) {
			case 50:
				return bp_get_link_avatar_thumb();
			default:
				return bp_get_link_avatar();
		}
	}

function bp_link_list_item_name() {
	echo bp_get_link_list_item_name();
}
	function bp_get_link_list_item_name() {
		return apply_filters( 'bp_get_link_list_item_name', bp_link_name() );
	}

function bp_link_list_item_category_name() {
	echo bp_get_link_list_item_category_name();
}
	function bp_get_link_list_item_category_name() {
		return apply_filters( 'bp_get_link_list_item_category_name', bp_link_category_name() );
	}

function bp_link_list_item_description() {
	echo bp_get_link_list_item_description();
}
	function bp_get_link_list_item_description() {
		return apply_filters( 'bp_get_link_list_item_description', bp_link_description() );
	}

function bp_link_list_item_url() {
	echo bp_get_link_list_item_url();
}
	function bp_get_link_list_item_url() {
		return apply_filters( 'bp_get_link_list_item_url', bp_link_permalink() );
	}

function bp_link_list_item_url_domain() {
	echo bp_get_link_list_item_url_domain();
}
	function bp_get_link_list_item_url_domain() {
		return apply_filters( 'bp_get_link_list_item_url_domain', bp_link_url_domain() );
	}

function bp_link_list_item_url_target() {
	echo bp_get_link_list_item_url_target();
}
	function bp_get_link_list_item_url_target() {
		$target = apply_filters( 'bp_get_link_list_item_url_target', '' );

		if ( !empty( $target ) ) {
			return sprintf( ' target="%s"', $target );
		}
	}

function bp_link_list_item_url_rel() {
	echo bp_get_link_list_item_url_rel();
}
	function bp_get_link_list_item_url_rel() {
		$rel = apply_filters( 'bp_get_link_list_item_url_rel', '' );

		if ( !empty( $rel ) ) {
			return sprintf( ' rel="%s"', $rel );
		}
	}

function bp_link_list_item_external() {
	echo bp_get_link_list_item_external();
}
	function bp_get_link_list_item_external() {
		return apply_filters( 'bp_get_link_list_item_external', __( 'External Link', 'buddypress-links' ) );
	}

function bp_link_list_item_external_url() {
	echo bp_get_link_list_item_external_url();
}
	function bp_get_link_list_item_external_url() {
		return apply_filters( 'bp_get_link_list_item_external_url', bp_link_url() );
	}

function bp_link_list_item_external_url_rel() {
	echo bp_get_link_list_item_external_url_rel();
}
	function bp_get_link_list_item_external_url_rel() {
		$rel = apply_filters( 'bp_get_link_list_item_external_url_rel', '' );

		if ( !empty( $rel ) )
			return sprintf( ' rel="%s"', $rel );
	}

function bp_link_list_item_external_url_target() {
	echo bp_get_link_list_item_external_url_target();
}
	function bp_get_link_list_item_external_url_target() {
		$target = apply_filters( 'bp_get_link_list_item_external_url_target', '' );

		if ( !empty( $target ) ) {
			return sprintf( ' target="%s"', $target );
		}
	}

function bp_link_list_item_continue() {
	echo bp_get_link_list_item_continue();
}
	function bp_get_link_list_item_continue() {
		return apply_filters( 'bp_get_link_list_item_continue', __( 'more...', 'buddypress-links' ) );
	}

function bp_link_list_item_continue_url() {
	echo bp_get_link_list_item_continue_url();
}
	function bp_get_link_list_item_continue_url( $link = false ) {
		return apply_filters( 'bp_get_link_list_item_continue_url', bp_link_permalink() );
	}

function bp_link_list_item_continue_url_rel() {
	echo bp_get_link_list_item_continue_url_rel();
}
	function bp_get_link_list_item_continue_url_rel() {
		$rel = apply_filters( 'bp_get_link_list_item_continue_url_rel', '' );
		
		if ( !empty( $rel ) )
			return sprintf( ' rel="%s"', $rel );
	}

function bp_link_list_item_continue_url_target() {
	echo bp_get_link_list_item_continue_url_target();
}
	function bp_get_link_list_item_continue_url_target() {
		$target = apply_filters( 'bp_get_link_list_item_continue_url_target', '' );

		if ( !empty( $target ) )
			return sprintf( ' target="%s"', $target );
	}

function bp_link_list_item_xtrabar_comments() {
	echo bp_get_link_list_item_xtrabar_comments();
}
	function bp_get_link_list_item_xtrabar_comments() {
		return apply_filters( 'bp_get_link_list_item_xtrabar_comments', __( 'Comments', 'buddypress-links' ) );
	}
	
function bp_link_list_item_xtrabar_userlink_created() {
	echo bp_get_link_list_item_xtrabar_userlink_created();
}
	function bp_get_link_list_item_xtrabar_userlink_created() {
		return apply_filters( 'bp_get_link_list_item_xtrabar_userlink_created', sprintf( __( 'created %s ago', 'buddypress-links' ), bp_get_link_time_since_created() ) );
	}

/****
 * Link list filter template tags
 */
function bp_links_link_order_options() { ?>

	<option value="active"><?php _e( 'Last Active', 'buddypress' ) ?></option>
	<option value="popular"><?php _e( 'Most Popular', 'buddypress-links' ) ?></option>
	<option value="newest"><?php _e( 'Newly Created', 'buddypress' ) ?></option>
	<option value="most-votes"><?php _e( 'Most Votes', 'buddypress-links' ) ?></option>
	<option value="high-votes"><?php _e( 'Highest Rated', 'buddypress-links' ) ?></option> <?php

	do_action( 'bp_links_link_order_options' );
}

/****
 * Links group extension template tags
 */

function bp_links_group_links_tabs( $group = false ) {
	global $bp, $groups_template;

	if ( !$group )
		$group = ( $groups_template->group ) ? $groups_template->group : $bp->groups->current_group;

	$current_tab = $bp->action_variables[0];
	?>

	<li<?php if ( '' == $current_tab ) : ?> class="current"<?php endif; ?>><a href="<?php echo $bp->root_domain . '/' . $bp->groups->slug ?>/<?php echo $group->slug ?>/<?php echo $bp->links->slug ?>/"><?php printf( __('All Group Links (%s)', 'buddypress-links'), bp_links_total_links_for_group() ) ?></a></li>
	<?php if ( bp_group_is_member() ): ?>
		<li<?php if ( 'my-links' == $current_tab ) : ?> class="current"<?php endif; ?>><a href="<?php echo $bp->root_domain . '/' . $bp->groups->slug ?>/<?php echo $group->slug ?>/<?php echo $bp->links->slug ?>/my-links/"><?php printf( __('My Group Links (%s)', 'buddypress-links'), bp_links_total_links_for_group_member() ) ?></a></li>
		<li<?php if ( 'create' == $current_tab ) : ?> class="current"<?php endif; ?>><a href="<?php echo $bp->root_domain . '/' . $bp->groups->slug ?>/<?php echo $group->slug ?>/<?php echo $bp->links->slug ?>/create/"><?php _e('Create Group Link', 'buddypress-links') ?></a></li>
	<?php endif; ?>

	<?php
	do_action( 'bp_links_group_links_tabs', $current_tab, $group->slug );
}

?>
