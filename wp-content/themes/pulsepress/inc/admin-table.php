<?php

if(pulse_press_get_option( 'show_voting') ):
	add_filter( 'manage_posts_columns', 'pulse_press_modify_post_table' );
	add_filter( 'manage_posts_custom_column', 'pulse_press_modify_post_table_row', 10, 2 );
	
	add_filter( 'manage_users_columns', 'pulse_press_modify_post_table' );
	add_filter( 'manage_users_custom_column', 'pulse_press_modify_user_table_row', 10, 3 );
endif;

function pulse_press_modify_post_table( $columns ) {
			
  		$column_pulse_press_votes = array( 'pulse_press_votes' => ( pulse_press_get_option( 'vote_text' ) == ''  ? __("Votes",'pulse_press') : esc_html( pulse_press_get_option( 'vote_text' ) ) ) );
	    $columns = array_slice( $columns, 0, 5, true ) + $column_pulse_press_votes + array_slice( $columns, 5, NULL, true );
    	return $columns;
	}
	
function pulse_press_modify_post_table_row( $column_name, $post_id ) {
 
    $custom_fields = get_post_custom( $post_id );
 	
    switch ($column_name) {
    	case 'pulse_press_votes' :
    		
    		$sum = ( isset($custom_fields['updates_votes']) ? $custom_fields['updates_votes'][0] : 0 );
    		$total_num_votes = ( isset($custom_fields['total_votes'][0]) ? $custom_fields['total_votes'][0] : 0 );
    	
    		if($sum > 0) {
					$negative_votes = (($total_num_votes-$sum)/2);
					$positive_votes = $total_num_votes-$negative_votes;
				} else if( $sum == 0 ){
					$negative_votes = $total_num_votes/2;
					$positive_votes = $negative_votes;
				} else{
					$negative_votes = (($total_num_votes-$sum)/2);
					$positive_votes = $total_num_votes-$negative_votes;
				}

    
    
    
    
        	echo "sum: ".$sum;
        	if( pulse_press_get_option( 'voting_type') == 'two' )
        	echo " (+): ".$positive_votes." (-): ".$negative_votes;
       	
        break;
 
        default:
    }
}





function pulse_press_modify_user_table_row( $test, $column_name,$user_id ) {
	
 	$sum 				= pulse_press_get_sum_votes_by_user( $user_id );
 	$total_num_votes 	= pulse_press_get_total_votes_by_user($user_id);
 	
 	if($sum > 0) {
 		$negative_votes = (($total_num_votes-$sum)/2);
 		$positive_votes = $total_num_votes-$negative_votes;
 		
 	} else if( $sum == 0 ){
 		$negative_votes = $total_num_votes/2;
 		$positive_votes = $negative_votes;
 	} else{
 		$negative_votes = (($total_num_votes-$sum)/2);
 		$positive_votes = $total_num_votes-$negative_votes;
 	
 	}
 	
 	$back = "voted:".$total_num_votes;
 	if( pulse_press_get_option( 'voting_type') == 'two' )
        	$back .=" (+): ".$positive_votes." (-): ".$negative_votes;
 	return  $back;
    
}