<?php
class Wdfb_CommentsImporter {

	var $model;

	function __construct () {
		$this->model = new Wdfb_Model();
	}

	function Wdfb_CommentsImporter () { $this->__construct(); }

	function process_comments ($post_id, $item_id) {
		$comments = $this->model->get_item_comments($item_id);
		if (!$comments || !isset($comments['data'])) return;
		$comments = $comments['data'];

		if (!count($comments)) return false;

		foreach ($comments as $comment) {
			if ($this->model->comment_already_imported($comment['id'])) continue; // We already have this comment, continue.
			$data = array (
				'comment_post_ID' => $post_id,
				'comment_date' => date('Y-m-d H:i:s', strtotime($comment['created_time'])),
				'comment_author' => $comment['from']['name'],
				'comment_author_url' => 'http://www.facebook.com/profile.php?id=' . $comment['from']['id'],
				'comment_content' => $comment['message'],
			);
			$meta = array (
				'fb_comment_id' => $comment['id'],
				'fb_author_id' => $comment['from']['id'],
			);
			$data = wp_filter_comment($data);
			$comment_id = wp_insert_comment($data);
			add_comment_meta($comment_id, 'wdfb_comment', $meta) ;

			if ($this->model->data->get_option('wdfb_comments', 'notify_authors')) {
				wp_notify_postauthor($comment_id, 'comment');
			}
		}
	}

	function process_commented_posts ($posts) {
		if (!count($posts)) return false;
		foreach ($posts as $post) {
			$post_id = url_to_postid($post['link']);
			if (!$post_id) continue; // Not a post on this blog. Continue.
			$this->process_comments($post_id, $post['id']);
		}
	}

	function import_comments () {
		$limit = (int)$this->model->data->get_option('wdfb_comments', 'comment_limit');
		$limit = $limit ? $limit : 10;

		$tokens = $this->model->data->get_option('wdfb_api', 'auth_tokens');
		$skips = $this->model->data->get_option('wdfb_comments', 'skip_import');
		$reverse = $this->model->data->get_option('wdfb_comments', 'reverse_skip_logic');
		$skips = is_array($skips) ? $skips : array();
		foreach ($tokens as $fb_uid=>$token) {
			if (!$fb_uid) continue;
			if ($reverse) {
				if (!in_array($fb_uid, $skips)) continue;		
			} else {
				if (in_array($fb_uid, $skips)) continue;				
			}
			$feed = $this->model->get_feed_for($fb_uid, $limit);
			if (!isset($feed['data'])) return false; // Nothing to import

			$commented_posts = array();
			foreach ($feed['data'] as $post) {
				if (!isset($post['comments']) || !@$post['comments']['count']) continue; // Skip uncommented posts
				$commented_posts[] = $post;
			}
			$this->process_commented_posts($commented_posts);
		}
	}

	/**
	 * @static
	 */
	function serve () {
		$me = new Wdfb_CommentsImporter;
		$me->import_comments();
	}
}