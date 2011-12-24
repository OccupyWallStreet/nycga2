<?php

if ($_SERVER['REQUEST_METHOD'] != 'POST') die();

require_once dirname(dirname(dirname(dirname(__FILE__)))).'/wp-load.php';

if (!check_admin_referer(q2w3_post_order::ID.'_post','wp_nonce')) wp_die(); 

if ($_SERVER['HTTP_REFERER']) wp_redirect($_SERVER['HTTP_REFERER']);

// Deactivate plugin

if (isset($_POST['deactivate'])) { 
				
	q2w3_post_order::deactivation();
					
} 

// Actions

$action = false;

$context['tax_name'] = false;

$context['post_type'] = false;

if (isset($_POST['action'])) $action = $_POST['action'];

/*if ($action == 'update_meta') {
	
	global $wpdb;
	
	$tax_name = $_POST['tax_name'];
	$terms = $_POST['terms'];
	
	$meta_saved = q2w3_post_order::get_meta($tax_name);
	
	foreach ($terms as $term_id => $meta) {
		
		if (!$meta['limit']) $meta['limit'] = 0;
		
		if ($meta_saved[$term_id] && ($meta_saved[$term_id]['val'] != $meta['val'] || $meta_saved[$term_id]['limit'] != $meta['limit'])) {
			
			// Update term meta
			
			$wpdb->update(q2w3_post_order::meta_table(), array('term_rank' => $meta['val'], 'limit_to' => $meta['limit'], 'term_type' => $tax_name), array('term_id' => $term_id, 'term_type' => $tax_name));
			
		} elseif (!$meta_saved[$term_id]) {
			
			// Insert term meta
			
			$res = $wpdb->insert(q2w3_post_order::meta_table(), array('term_id' => $term_id, 'term_rank' => $meta['val'], 'limit_to' => $meta['limit'], 'term_type' => $tax_name));
			
		}
		
	}
	
}*/

if ($action == 'update_sorted') {

	global $wpdb;
	
	$term_id = $_POST['term_id'];
	
	$posts = $_POST['posts'];
		
	if (!$term_id) $term_id = 0;
	
	foreach ($posts as $post_id => $post) {
			
		if ($post['tax_name']) $context['tax_name'] = $post['tax_name'];
		
		if ($post['post_type']) $context['post_type'] = $post['post_type'];
				
		if ($post['new_pos'] == '0') {
			
			$sql = 'DELETE FROM '. q2w3_post_order::posts_table() .' WHERE id = '. $post['id'];
			
			$sql = $wpdb->prepare($sql);
			
			$res = $wpdb->query($sql); 
			
			if ($res) {
				
				q2w3_post_order::change_positions($term_id, $context, 'down', $post['pos']); 
				
			}
			
		} elseif ((int)$post['new_pos'] > 0) {
		
			$data = array('post_rank' => $post['new_pos']);
				
			if ($post['tax_name']) $data['taxonomy'] = $post['tax_name'];
			
			$where = array('id' => $post['id']);
			
			if ($post['new_pos'] > $post['pos']) {
				
				q2w3_post_order::change_positions($term_id, $context, 'down', $post['pos'], $post['new_pos']);
			
				$wpdb->update(q2w3_post_order::posts_table(), $data, $where);
					
			}
			
			if ($post['new_pos'] < $post['pos']) {
				
				q2w3_post_order::change_positions($term_id, $context, 'up', $post['new_pos'], $post['pos']);
			
				$wpdb->update(q2w3_post_order::posts_table(), $data, $where);
							
			}
				
		}
		
	}
	
}

if ($action == 'update_unsorted') {

	global $wpdb;
	
	$term_id = $_POST['term_id'];
	
	$posts = $_POST['posts'];
	
	if (!$term_id) $term_id = 0;
	
	foreach ($posts as $post_id => $post) {
		
		if ((int)$post['new_pos'] > 0) {
		
			$data = array('post_id' => $post_id, 'term_id' => $term_id, 'post_rank' => $post['new_pos']);
			
			if ($post['tax_name']) {
				
				$data['taxonomy'] = $post['tax_name'];
			
				$context['tax_name'] = $post['tax_name'];
			
			}
			
			if ($post['post_type']) {
				
				$data['post_type'] = $post['post_type'];
			
				$context['post_type'] = $post['post_type'];
				
			}
			
			q2w3_post_order::change_positions($term_id, $context, 'up', $post['new_pos']);
			
			$wpdb->insert(q2w3_post_order::posts_table(), $data);
		
		}	
			
	}
	
}

?>