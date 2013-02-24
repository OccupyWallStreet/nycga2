<?php
// Broadcast Post
	class DiamondBCP {

		function DiamondBCP() {
			add_action('post_submitbox_start', array($this, 'widget_endView'));
			add_action('save_post', array($this, 'diamond_save_post'));
		}
	
	
		function diamond_save_post($post_ID) {
		
			
		
			global $switched;
			if ($switched	) {
				return;
			}		
			$datef = __( 'M j, Y @ G:i' );
		
			$post = get_post($post_ID, ARRAY_A);
			
			//print_r($post['post_type']);
			
			if ($post['post_type'] == 'revision')
				return;
				
			unset($post['ID']);			
			unset($post['post_parent']);
			$post['post_status'] = 'publish';
			$post['post_category'] = '';		 			
			unset($post['post_date'] );
			unset($post['post_date_gmt']) ;
			unset($post['post_name']);
			unset($post['guid']);
			unset($post['comment_count']);
			$post['post_type'] = 'post';
			
			
			//print_r($post);
			
			$blogarr = $_POST["diamond_blogs"];					
			
			//print_r($blogarr);
			
			$newshare = '';
			$sep = '';
			
			if ($blogarr){
				foreach ($blogarr as $b) {
					if ($b != 0 ) {							
						switch_to_blog($b);					
						wp_insert_post( $post, $wp_error );
						restore_current_blog();
						if ($wp_error) {
							print_r($wp_error);
						}
						else {
							$newshare .= $sep . $b;
							$sep = ';';
						}
					}
				}				
				$shared = get_post_custom_values('diamond_broadcast_blogs', $post_ID);
				if ($shared) {					
					$shared = $shared[0] . $sep . $newshare;
					update_post_meta($post_ID, 'diamond_broadcast_blogs', $shared);
				} else {
					add_post_meta($post_ID, 'diamond_broadcast_blogs', $newshare);	
				}
			}			
		}
		
		function widget_endView($args)
		{
			if (!is_super_admin())
				return;
				
			if (get_option('diamond_allow_broadcast') == 0)	
				return;
				
			global $wpdb;				
			
			echo '<fieldset><legend>';
			echo __('Broadcast this post', 'diamond');
			echo '</legend>';			
			echo '<label>';
			echo __('Select blogs where you want to copy this post', 'diamond');
			echo '<select name="diamond_blogs[]" id="diamond_blogs" style="height:120px; width: 100%"  multiple="multiple">';
			echo '<option value="0">';
			_e('--- No broadcast ---', 'diamond');
			echo '</option>';
			$blog_list = get_blog_list( ); 			
			$shared = get_post_custom_values('diamond_broadcast_blogs', ($_GET['post']) ? $_GET['post'] : 0);
			
			$sharr = split(";", $shared[0]);
			
			foreach ($blog_list AS $blog) {
				if ($blog['blog_id'] != $wpdb->blogid)
					echo '<option value="'.  $blog['blog_id'].'">'. get_blog_option( $blog['blog_id'], 'blogname' );
					if ($sharr && in_array($blog['blog_id'], $sharr))
						echo __(' (copied)', 'diamond');
					echo '</option>'	;		
			}
			echo '</select>';
			echo '</label>';
			//print_r($shared[0]);
			//print_r($sharr)	;
			//print_r($shared);
			echo '</fieldset>';
		}	
	}
	$newWidget3 = new DiamondBCP ();	
?>