<?php


function pulse_press_vote_on_post()
{	
	if(pulse_press_get_option( 'show_voting')):
	$votes = pulse_press_sum_votes(get_the_ID());
	
		if(empty($votes))
			$votes = 0;
	
	switch(pulse_press_get_option( 'voting_type')){
		default:
		case "one": ?>
		<div class="vote" >
		<em title="total votes:<?php echo pulse_press_total_votes(get_the_ID()); ?>" ><strong id="votes-<?php the_ID();?>"><?php echo $votes; ?></strong> <?php  pulse_press_display_option(pulse_press_get_option( 'vote_text' ),'votes'); ?></em>
		<?php if( pulse_press_user_can_vote() ) : ?>
			<?php if(!pulse_press_is_vote(get_the_ID())) : ?>
			<a id="voteup-<?php the_ID();?>" href="?pid=<?php the_ID();?>&nononc=<?php echo wp_create_nonce('vote');?>&action=vote" class="vote-up" title="<?php esc_attr_e('Vote Up','pulse_press'); ?>"> <span><?php _e('Vote Up','pulse_press'); ?></span></a> 
			<?php else: ?>
			<a id="voteup-<?php the_ID();?>"href="?pid=<?php the_ID();?>&nononc=<?php echo wp_create_nonce('vote');?>&action=vote" class="vote-up vote-up-set" title="<?php esc_attr_e('Unvote','pulse_press'); ?>"> <span><?php _e('Unvote','pulse_press'); ?></span></a>
			<?php endif; ?>
		<?php endif; ?>
		</div>
		
		<?php 
		break;
		case "two": 
		
		$total = pulse_press_total_votes(get_the_ID());
		?>
		<div class="vote" >
		<em title="total votes:<?php echo $total; ?>" ><strong id="votes-<?php the_ID();?>" data-total="<?php echo $total; ?>"><?php echo $votes; ?></strong> <?php pulse_press_display_option(  pulse_press_get_option( 'vote_text' ),"votes" ); 
			if(pulse_press_get_option( 'show_vote_breakdown')):
			
				if($votes > 0) {
						$negative_votes = (($total-$votes)/2);
						$positive_votes = $total-$negative_votes;
					} else if( $votes	 == 0 ){
						$negative_votes = $total/2;
						$positive_votes = $negative_votes;
					} else{
						$negative_votes = (($total-$votes)/2);
						$positive_votes = $total-$negative_votes;
					}
				
					
			?> - <span><strong id="votes-up-<?php the_ID();?>"><?php echo $positive_votes; ?></strong>
				 <?php pulse_press_display_option( pulse_press_get_option( 'vote_up_text' ),"up"); ?></span>, 
				 <span><strong id="votes-down-<?php the_ID();?>"><?php echo $negative_votes; ?></strong> 
				 <?php pulse_press_display_option( pulse_press_get_option( 'vote_down_text' ) ,"down" ); ?></span>
			<?php
			endif; // end of breakdown
			?></em>
			<?php
		 if( pulse_press_user_can_post() ) : ?>
			<?php if( pulse_press_is_vote(get_the_ID()) == null ) :  // still need to vote ?>
			
			<a id="voteup-<?php the_ID();?>" href="?pid=<?php the_ID();?>&nononc=<?php echo wp_create_nonce('vote');?>&action=vote" class="vote-up" title="<?php esc_attr_e('Vote Up','pulse_press'); ?>"> <span><?php _e('Vote Up','pulse_press'); ?></span></a> 
			<a id="votedw-<?php the_ID();?>"href="?pid=<?php the_ID();?>&nononc=<?php echo wp_create_nonce('vote');?>&action=votedown" class="vote-down" title="<?php esc_attr_e('Vote Down','pulse_press'); ?>"> <span><?php _e('Vote Down','pulse_press'); ?></span></a>
			
			<?php elseif( pulse_press_is_vote(get_the_ID()) > 0 ): // voted up ?>
			
			<a id="voteup-<?php the_ID();?>" href="?pid=<?php the_ID();?>&nononc=<?php echo wp_create_nonce('vote');?>&action=vote" class="vote-up vote-up-set" title="<?php esc_attr_e('Unvote','pulse_press'); ?>"> <span><?php _e('Unvote','pulse_press'); ?></span></a> 
			<a id="votedw-<?php the_ID();?>"href="?pid=<?php the_ID();?>&nononc=<?php echo wp_create_nonce('vote');?>&action=votedown" class="vote-down" title="<?php esc_attr_e('Vote Down','pulse_press'); ?>"> <span><?php _e('Vote Down','pulse_press'); ?></span></a>
			
			<?php else: // voted down ?>
			
			<a id="voteup-<?php the_ID();?>" href="?pid=<?php the_ID();?>&nononc=<?php echo wp_create_nonce('vote');?>&action=vote" class="vote-up" title="<?php esc_attr_e('Vote Up','pulse_press'); ?>"> <span><?php _e('Vote Up','pulse_press'); ?></span></a> 
			<a id="votedw-<?php the_ID();?>"href="?pid=<?php the_ID();?>&nononc=<?php echo wp_create_nonce('vote');?>&action=votedown" class="vote-down vote-down-set" title="<?php esc_attr_e('Unvote','pulse_press'); ?>"> <span><?php _e('Unvote','pulse_press'); ?></span></a>
			
			<?php endif; ?>
		<?php endif; ?>
			
		</div>
		
		<?php
		break;
	
	}
	?>
	
	<?php endif; 
}

function pulse_press_voting_init($redirect=true)
{
	if( isset($_GET['nononc']) &&  wp_verify_nonce( $_GET['nononc'], 'vote') && in_array( $_GET['action'], array("vote","votedown") ) ):
		$post_id = (int)$_GET['pid'];
		
		switch( pulse_press_get_option( 'voting_type' ) ) {
			default:
			case "one":
				if( pulse_press_is_vote($post_id) == null ):
					pulse_press_vote($post_id);
				else:
					pulse_press_delete_vote($post_id);
				endif;
			break;
			
			case "two":
				if( pulse_press_is_vote( $post_id ) == null ) : // still need to vote
					if( $_GET['action'] == "votedown"  ):
						pulse_press_vote_down($post_id);
					endif;
					
					if( $_GET['action'] == "vote" ):
						pulse_press_vote($post_id);
					endif;
	
				elseif(  pulse_press_is_vote( $post_id ) > 0 ): // the user previously voted up
					pulse_press_delete_vote($post_id); 
					
					if( $_GET['action'] == "votedown"  ):
						
						pulse_press_vote_down($post_id); // 
					endif;
				else:
					pulse_press_delete_vote($post_id); 
					
					if( $_GET['action'] == "vote"  ):
						pulse_press_vote($post_id); // 
					endif;
				endif; 
			break;
		}
		
		
		if($redirect==""):
			wp_redirect( pulse_press_curPageURL() );
			die();
		else:
			return "vote";
		endif;
		
		
	endif;
	
	
}
if(!isset($_GET['do_ajax']))
	add_action('init','pulse_press_voting_init');


if(isset($_GET['popular']) || isset($_GET['unpopular']) || isset($_GET['most-voted'])):

	add_filter('posts_groupby', 	'pulse_press_popular_groupby');
	add_filter('posts_join_paged', 	'pulse_press_popular_join_paged');
	add_action('pre_get_posts',		'pulse_press_main_loop_test');
	
endif;

if(isset($_GET['popular'])):
	add_filter('posts_where_paged', 'pulse_press_popular_where_paged');
	add_filter('posts_orderby', 	'pulse_press_popular_orderby');
endif;

if(isset($_GET['unpopular'])):
	add_filter('posts_where_paged', 'pulse_press_popular_where_paged');
	add_filter('posts_orderby', 	'pulse_press_unpopular_orderby');
endif;

if(isset($_GET['most-voted'])):
	add_filter('posts_where_paged', 'pulse_press_most_voted_where_paged');
	add_filter('posts_orderby', 	'pulse_press_popular_orderby');	
endif;

/** 
 * filter need to be applies to the main query to display the information in popularity order *
 * I tried to recreate the query query_posts('meta_key=updates_votes&orderby=meta_value&order=DESC&paged='.$paged);
 */
/* change the query when using the popular filter */
/* changes the main loop to make popular work */
function pulse_press_popular_where_paged( $where ) {
	global $wpdb, $pulse_press_main_loop;
	
  	if($pulse_press_main_loop)
    		$where = $where." AND ".$wpdb->postmeta.".meta_key = 'updates_votes' ";
	
	return $where;
}
function pulse_press_most_voted_where_paged( $where ) {
	global $wpdb, $pulse_press_main_loop;
	if($pulse_press_main_loop)
    	$where = $where." AND ".$wpdb->postmeta.".meta_key = 'total_votes' ";
	
	return $where;

}
function pulse_press_popular_groupby( $group ) {
	global $wpdb, $pulse_press_main_loop;
	
  	if($pulse_press_main_loop)
  		$group = $wpdb->posts.".ID ";
	
	return $group;
}
function pulse_press_popular_join_paged( $join ) {
	global $wpdb, $pulse_press_main_loop;
	
  	if($pulse_press_main_loop)
  		$join = $join." INNER JOIN ".$wpdb->postmeta." ON (".$wpdb->posts.".ID = ".$wpdb->postmeta.".post_id) ";
	
	return $join;
}
function pulse_press_popular_orderby( $orderby ) {
	global $wpdb, $pulse_press_main_loop;
	
  	if($pulse_press_main_loop)
  		$orderby = " CAST(".$wpdb->postmeta.".meta_value AS SIGNED) DESC, ".$wpdb->posts.".post_date DESC";

	return $orderby;

}

function pulse_press_unpopular_orderby( $orderby ) {
	global $wpdb, $pulse_press_main_loop;
	
  	if($pulse_press_main_loop)
  		$orderby = " CAST(".$wpdb->postmeta.".meta_value AS SIGNED) ASC, ".$wpdb->posts.".post_date DESC";
	return $orderby;

}


add_action('save_post',"pulse_press_save_post");
/* this will allow for quering stuff even though it has no votes */
function pulse_press_save_post($post_id)
{
	$sum = pulse_press_sum_votes( $post_id );
	$total = pulse_press_total_votes( $post_id );
	if(!$total):
		$sum = 0;
		$total = 0;
	endif;
	// save the number of votes to better get popular votes 
	add_post_meta( $post_id, 'updates_votes', $sum, true) or update_post_meta( $post_id, 'updates_votes', $sum );
	add_post_meta( $post_id, 'total_votes', $total, true) or update_post_meta( $post_id, 'total_votes', $total );

}
/* used for testing 
function pulse_press_orderby($order){
	global $wpdb, $pulse_press_main_loop;
	
	//var_dump ( $pulse_press_main_loop );
	if($pulse_press_main_loop );
	// echo $order."<br />";

	
	return $order;
}

add_action('posts_selection','pulse_press_orderby',100);
*/

function pulse_press_update_custom_field_from_table(){
	$all_posts = get_posts('posts_per_page=-1&post_type=post&post_status=any');
		foreach( $all_posts as $postinfo) {
			$sum = pulse_press_sum_votes( $postinfo->ID );
			$total = pulse_press_total_votes( $postinfo->ID );
			
			if(!$total):
				$sum = 0;
				$total = 0;
			endif;
			
			update_post_meta($postinfo->ID, 'updates_votes', $sum);
			update_post_meta($postinfo->ID, 'total_votes', $total );
		}
}
