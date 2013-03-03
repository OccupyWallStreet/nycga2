<?php


function pulse_press_star_a_post()
{	
	if(pulse_press_user_can_post() && pulse_press_get_option( 'show_fav' )) : ?>
		<div class="action-star" >
		<?php if(!pulse_press_is_star(get_the_ID())) : ?>
		<a  id="star-<?php the_ID();?>" href="?pid=<?php the_ID();?>&nononc=<?php echo wp_create_nonce('star');?>&action=star" class="star" title="Star"> <span>Star</span></a>
		<?php else: ?>
		<a id="star-<?php the_ID();?>" href="?pid=<?php the_ID();?>&nononc=<?php echo wp_create_nonce('star');?>&action=star" class="unstar" title="Unstar"> <span>Unstar</span></a>
		<?php endif; ?>
		</div>
		<?php
	endif; 
}


function pulse_press_star_init($redirect=true)
{
	if( isset($_GET['nononc']) && wp_verify_nonce($_GET['nononc'], 'star') && ($_GET['action'] == "star")):
		$post_id = (int)$_GET['pid'];
		
		if(!pulse_press_is_star($post_id)):
			pulse_press_add_star($post_id);
		else:
			pulse_press_delete_star($post_id);
		endif;
		
		if($redirect==""):
			wp_redirect(pulse_press_curPageURL());
			die();
		else:
			return "vote";
		endif;
		
	endif;
	
	
}
if(!isset($_GET['do_ajax']))
	add_action('init','pulse_press_star_init');
	
	

if(isset($_GET['starred'])):
	
	add_filter('posts_where_paged', 'pulse_press_star_where_paged');
	
	
endif;


/** 
 * filter need to be applies to the main query to display the information in popularity order *
 * I tried to recreate the query query_posts('meta_key=updates_votes&orderby=meta_value&order=DESC&paged='.$paged);
 */
/* change the query when using the popular filter */
/* changes the main loop to make popular work */
function pulse_press_star_where_paged( $where ) {
	global $wpdb, $pulse_press_main_loop;

  	if($pulse_press_main_loop)
  		$where = " ".$wpdb->posts.".ID IN (".implode (",",pulse_press_get_user_starred_post_meta()).")  AND ". $where;
	
	return $where;
}
