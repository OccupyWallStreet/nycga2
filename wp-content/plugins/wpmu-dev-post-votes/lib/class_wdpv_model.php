<?php
/**
 * Handles data access, checks and sanitization.
 */
class Wdpv_Model {
	var $db;
	var $data;

	function __construct () {
		global $wpdb;
		$this->db = $wpdb;
		$this->data = new Wdpv_Options;
	}
	function Wdpv_Model () { $this->__construct(); }

	/**
	 * Returns all blogs on the current site.
	 */
	function get_blog_ids () {
		global $current_blog;
		$site_id = 0;
		if ($current_blog) {
			$site_id = $current_blog->site_id;
		}
		$sql = "SELECT blog_id FROM " . $this->db->blogs . " WHERE site_id={$site_id} AND public='1' AND archived= '0' AND spam='0' AND deleted='0' ORDER BY registered DESC";
		return $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * Fetches a list of top rated posts from current site.
	 *
	 * @param int Post fetching limit, defaults to 5
	 * @return array A list of posts with post data JOINED in.
	 */
	function get_popular_on_current_site ($limit=5, $posted_timeframe=false, $voted_timeframe=false) {
		global $current_blog;
		$site_id = $blog_id = 0;
		if ($current_blog) {
			$site_id = $current_blog->site_id;
			$blog_id = $current_blog->blog_id;
		}
		$limit = (int)$limit;

		return $this->get_popular_on_site($site_id, $blog_id, $limit, $posted_timeframe, $voted_timeframe);
	}

	/**
	 * Fetches a list of post from a specified network/blog.
	 *
	 * @param $site_id int Network ID
	 * @param $blog_id int Blog ID
	 * @param $limit int Post fetching limit, defaults to 5
	 * @return array A list of posts with post data JOINED in.
	 */
	function get_popular_on_site ($site_id, $blog_id, $limit, $posted_timeframe=false, $voted_timeframe=false) {
		if (defined('MULTI_DB_VERSION') && class_exists('m_wpdb')) return $this->get_popular_on_multidb_site($site_id, $blog_id, $limit, $posted_timeframe, $voted_timeframe);
		$site_id = (int)$site_id;
		$blog_id = (int)$blog_id;
		$blog_id = $blog_id ? $blog_id : 1;
		$limit = (int)$limit;
		if ($posted_timeframe) {
			list($start_date, $end_date) = $this->extract_timeframe($posted_timeframe);
		}
		if ($voted_timeframe) {
			list($voted_start_date, $voted_end_date) = $this->extract_timeframe($posted_timeframe);
		}

		// Woot, mega complex SQL
		$sql = "SELECT *, SUM(vote) as total FROM " . // SUM(vote) for getting the count - GROUP BY post_id to get to individual posts
				$this->db->base_prefix . "wdpv_post_votes LEFT JOIN " . // Get the post data too - LEFT JOIN what we need
				$this->db->prefix . "posts ON " . $this->db->prefix . "posts.ID=" .	$this->db->base_prefix . "wdpv_post_votes.post_id " .
				"WHERE site_id={$site_id} AND blog_id={$blog_id} " . // Only posts on set site/blog
				(
					$posted_timeframe ?
						"AND post_date > '{$start_date}' AND post_date < '{$end_date}' "
						: ''
				) .
				(
					$voted_timeframe ?
						"AND date > '{$voted_start_date}' AND date < '{$voted_end_date}' "
						: ''
				) .
				"GROUP BY post_id " . // Group by post_id so we get the proper vote sum in `total`
				"ORDER BY total DESC " . // Order them nicely
			"LIMIT {$limit}";

		$result = $this->db->get_results($sql, ARRAY_A);
		return $result;
	}

	/**
	 * Multi-DB compatibility layer.
	 */
	function get_popular_on_multidb_site ($site_id, $blog_id, $limit, $posted_timeframe=false, $voted_timeframe=false) {
		$site_id = (int)$site_id;
		$blog_id = (int)$blog_id;
		$limit = (int)$limit;
		if ($posted_timeframe) {
			list($start_date, $end_date) = $this->extract_timeframe($posted_timeframe);
		}
		if ($voted_timeframe) {
			list($voted_start_date, $voted_end_date) = $this->extract_timeframe($posted_timeframe);
		}
		// Woot, mega complex SQL
		$sql = "SELECT *, SUM(vote) as total FROM " . // SUM(vote) for getting the count - GROUP BY post_id to get to individual posts
				$this->db->base_prefix . "wdpv_post_votes " .
				"WHERE site_id={$site_id} AND blog_id={$blog_id} " .
				(
					$posted_timeframe ?
						"AND post_date > '{$start_date}' AND post_date < '{$end_date}' "
						: ''
				) .
				(
					$voted_timeframe ?
						"AND date > '{$voted_start_date}' AND date < '{$voted_end_date}' "
						: ''
				) .
				"GROUP BY post_id " . // Group by post_id so we get the proper vote sum in `total`
				"ORDER BY total DESC " . // Order them nicely
			"LIMIT {$limit}";

		$results = $this->db->get_results($sql, ARRAY_A);
		foreach ($results as $key=>$val) {
			$post = (array)get_blog_post($val['blog_id'], $val['post_id']);
			$results[$key] = array_merge($val, $post);
		}
		return $results;
	}

	/**
	 * Fetches a list of post from the current network.
	 *
	 * This method does NOT!! join post data - it is up to caller to fetch it
	 * using `get_blog_post()`/`get_blog_permalink()` etc.
	 *
	 * @param $limit int Post fetching limit, defaults to 5
	 * @return array A list of posts.
	 */
	function get_popular_on_network ($limit, $voted_timeframe=false) {
		global $current_blog;
		$site_id = 0;
		if ($current_blog) {
			$site_id = $current_blog->site_id;
		}
		if ($voted_timeframe) {
			list($voted_start_date, $voted_end_date) = $this->extract_timeframe($posted_timeframe);
		}
		$limit = (int)$limit;
		$sql = "SELECT *, SUM(vote) as total FROM " . // SUM(vote) for getting the count
			$this->db->base_prefix . "wdpv_post_votes " .
			"WHERE site_id={$site_id} AND blog_id<>0 " . // Only posts on multisite sites/blogs
			(
				$voted_timeframe ?
					"AND date > '{$voted_start_date}' AND date < '{$voted_end_date}' "
					: ''
			) .
			"GROUP BY post_id, site_id, blog_id " . // Group by post_id so we get the proper vote sum in `total`
			"ORDER BY total DESC " . // Order them nicely
		"LIMIT {$limit}";
		$result = $this->db->get_results($sql, ARRAY_A);
		return $result;
	}

	function get_stats ($post_id, $blog_id, $site_id) {
		$sql_up = "SELECT COUNT(id) FROM " . $this->db->base_prefix . "wdpv_post_votes " .
			"WHERE site_id={$site_id} AND blog_id={$blog_id} AND post_id={$post_id} AND vote>0 ";
		$sql_down = "SELECT COUNT(id) FROM " . $this->db->base_prefix . "wdpv_post_votes " .
			"WHERE site_id={$site_id} AND blog_id={$blog_id} AND post_id={$post_id} AND vote<0 ";
		return array (
			'up' => $this->db->get_var($sql_up),
			'down' => $this->db->get_var($sql_down),
		);
	}

	/**
	 * Gets recent votes by user.
	 *
	 * @param $uid int User ID
	 * @return array Array of recent votes
	 */
	function get_recent_votes_by ($uid) {
		$ret = array();
		$uid = (int)$uid;
		if (!$uid) return $ret;

		$limit = (int)$this->data->get_option('bp_profile_votes_limit');
		$limit = $limit ? "LIMIT {$limit}" : "";

		$time = (int)$this->data->get_option('bp_profile_votes_period');
		$unit = $this->data->get_option('bp_profile_votes_unit');

		$where = '';
		if ($time) {
			$valid_units = array('hour', 'day', 'week', 'month', 'year');
			$unit = in_array($unit, $valid_units) ? $unit : 'month';
			$where = "AND date > DATE_SUB(CURDATE(), INTERVAL {$time} {$unit})";
		}

		return $this->db->get_results(
			"SELECT * FROM " . $this->db->base_prefix . "wdpv_post_votes WHERE user_id={$uid} {$where} {$limit} ORDER BY date DESC",
			ARRAY_A
		);
	}

	/**
	 * Gets total sum of votes for a post.
	 *
	 * @param $post_id int Post ID
	 * @param $site_id int Network ID
	 * @param $blog_id int Blog ID
	 * @return int Number of votes.
	 */
	function get_votes_total ($post_id, $site_id=0, $blog_id=0) {
		global $current_blog;
		$post_id = (int)$post_id;
		if (!$post_id) return 0;

		if ((!$site_id || !$blog_id) && $current_blog) { // Requested current blog post
			if (!$site_id) $site_id = $current_blog->site_id;
			if (!$blog_id) $blog_id = $current_blog->blog_id;
		}
		$site_id = (int)$site_id;
		$blog_id = (int)$blog_id;

		$sql = "SELECT SUM(vote) FROM " . $this->db->base_prefix . "wdpv_post_votes WHERE post_id={$post_id} AND site_id={$site_id} AND blog_id={$blog_id}";
		return (int)$this->db->get_var($sql);
	}

	/**
	 * Updates the post votes.
	 *
	 * Also dispatches the permission checks
	 *
	 * @param $blog_id int Blog ID
	 * @param $post_id int Post ID
	 * @param $vote int Vote to be recorded
	 * @return (bool)false on permissions failure, or whatever database answers with.
	 */
	function update_post_votes ($blog_id, $post_id, $vote) {
		global $current_blog;
		$site_id = 0;

		if ($current_blog) {
			$site_id = $current_blog->site_id;
			if (!$blog_id) $blog_id = $current_blog->blog_id;
		}

		$post_id = (int)$post_id;
		$vote = (int)$vote;
		if (!$this->check_voting_permissions($site_id, $blog_id, $post_id)) return false;

		$user_id = $this->get_user_id();
		$user_ip = $this->get_user_ip();

		$date = current_time('mysql');
		$sql = "INSERT INTO " . $this->db->base_prefix . "wdpv_post_votes (" .
			"blog_id, site_id, post_id, user_id, user_ip, vote, date" .
			") VALUES (" .
			"{$blog_id}, {$site_id}, {$post_id}, {$user_id}, {$user_ip}, {$vote}, '{$date}'" .
		")";
		$res = $this->db->query($sql);

		if ($res) {
			$this->set_voted_cookie($site_id, $blog_id, $post_id);
			do_action('wdpv_voted', $site_id, $blog_id, $post_id, $vote); // Update listeners
		}

		return $res;
	}

	/**
	 * Removes all votes for a particular post.
	 *
	 * Also dispatches the permission checks
	 *
	 * @param $post_id int Post ID
	 * @param $site_id int Network ID
	 * @param $blog_id int Blog ID, defaults to current blog
	 * @return o.0 Whatever $wpdb returns.
	 */
	function remove_votes_for_post ($post_id, $site_id=false, $blog_id=false) {
		global $current_blog;
		$post_id = (int)$post_id;
		$site_id = (int)$site_id;
		$blog_id = (int)$blog_id;

		if ((!$site_id || !$blog_id) && $current_blog) { // Requested current blog post
			if (!$site_id) $site_id = $current_blog->site_id;
			if (!$blog_id) $blog_id = $current_blog->blog_id;
		} else if (!$site_id || !$blog_id) {
			if (!$site_id) $site_id = 0;
			if (!$blog_id) $blog_id = 1;
		}
		if (!$post_id || !$site_id || !$blog_id) return false;

		$sql = "DELETE FROM {$this->db->base_prefix}wdpv_post_votes WHERE site_id={$site_id} AND blog_id={$blog_id} AND post_id={$post_id}";
		return $this->db->query($sql);
	}

	/**
	 * Voting permissions checking method.
	 *
	 * Checks cookie (by calling `check_cookie_permissions()`),
	 * user ID and user IP (if allowed) for the current user.
	 *
	 * @param $site_id int Network ID
	 * @param $blog_id int Blog ID
	 * @param $post_id int Post ID
	 * @return bool true if all is good, false if voting not allowed
	 */
	function check_voting_permissions ($site_id, $blog_id, $post_id) {
		if (!$this->data->get_option('allow_voting')) return false;

		if (!$this->check_cookie_permissions($site_id, $blog_id, $post_id)) return false;

		$user_id = $this->get_user_id();
		if (!$user_id && !$this->data->get_option('allow_visitor_voting')) return false;

		$not_voted = true;
		if ($not_voted && $user_id) {
			$where = apply_filters('wdpv-sql-where-user_id_check', "WHERE user_id={$user_id} AND site_id={$site_id} AND blog_id={$blog_id} AND post_id={$post_id}");
			$result = $this->db->get_var("SELECT COUNT(*) FROM " . $this->db->base_prefix . "wdpv_post_votes {$where}");
			$not_voted = $result ? false : true;
		}

		if (!$this->data->get_option('use_ip_check')) return $not_voted;

		if ($not_voted) { // Either not registered user, or not voted yet. Check IPs
			$user_ip = $this->get_user_ip();
			$where = apply_filters('wdpv-sql-where-user_ip_check', "WHERE user_ip={$user_ip} AND site_id={$site_id} AND blog_id={$blog_id} AND post_id={$post_id}");
			$result = $this->db->get_var("SELECT COUNT(*) FROM " . $this->db->base_prefix . "wdpv_post_votes {$where}");
			return $result ? false : true;
		} else return false;
	}

	/**
	 * Checks cookie permissions specifically.
	 *
	 * @param $site_id int Network ID
	 * @param $blog_id int Blog ID
	 * @param $post_id int Post ID
	 * @return bool true if all is good, false if voting not allowed
	 */
	function check_cookie_permissions ($site_id, $blog_id, $post_id) {
		if (!isset($_COOKIE['wdpv_voted'])) return true; // No "voted" cookie, we're done here

		$votes = $this->decrypt_cookie_data_array($_COOKIE['wdpv_voted']);
		$str = $this->create_data_string($site_id, $blog_id, $post_id);

		$voted = @in_array($str, $votes);

		return !$voted;
	}

	/**
	 * Sets voted cookie for current network, blog and post.
	 *
	 * @param $site_id int Network ID
	 * @param $blog_id int Blog ID
	 * @param $post_id int Post ID
	 */
	function set_voted_cookie ($site_id, $blog_id, $post_id) {
		$voted = array();
		if (isset($_COOKIE['wdpv_voted'])) {
			$voted = $this->decrypt_cookie_data_array($_COOKIE['wdpv_voted']);
		}
		$voted[] = $this->create_data_string($site_id, $blog_id, $post_id);

		$expiration_time = apply_filters('wdpv-cookie-expiration_time', (time() + 30*24*3600));

		setcookie("wdpv_voted", $this->encrypt_cookie_data_array($voted), $expiration_time, COOKIEPATH, COOKIE_DOMAIN);//, "/", str_replace('http://', '', get_bloginfo('url')));
	}

	/**
	 * Helper method.
	 * Converts list of arguments into a string.
	 */
	function create_data_string () {
		$args = func_get_args();
		return join('|', $args);
	}

	/**
	 * Helper method.
	 * Encrypts cookie data.
	 */
	function encrypt_cookie_data_array ($arr) {
		return base64_encode(str_rot13(serialize($arr)));
	}

	/**
	 * Helper method.
	 * Decrypts cookie data.
	 */
	function decrypt_cookie_data_array ($str) {
		return unserialize(str_rot13(base64_decode(stripslashes($str))));
	}

	/**
	 * Helper method.
	 * Gets current WP user ID.
	 */
	function get_user_id () {
		$user = wp_get_current_user();
		return (int)$user->ID;
	}

	/**
	 * Helper method.
	 * Returns long representation of current users IP address.
	 */
	function get_user_ip () {
		return $user_ip = (int)sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
	}

	function extract_timeframe ($timeframe) {
		$start_date = $end_date = false;

		switch ($timeframe) {
			case "this_week":
				$start_date = date('Y-m-d', strtotime('this Monday', time() - 7*24*3600));
				$end_date = date('Y-m-d', strtotime('this Monday'));
				break;
			case "last_week":
				$start_date = date('Y-m-d', strtotime('this Monday', time() - 14*24*3600));
				$end_date = date('Y-m-d', strtotime('this Monday', time() - 7*24*3600));
				break;
			case "this_month":
				$start_date = date('Y-m-d', strtotime(date('Y-m-01')));
				$end_date = date('Y-m-d', strtotime(date('Y-m-') . date('t')));
				break;
			case "last_month":
				$month = (int)date('m') - 1;
				$start_date = date('Y-m-d', strtotime(date('Y-' . $month . '-01')));
				$end_date = date('Y-m-d', strtotime(date('Y-' . $month . '-') . date('t')));
				break;
			case "this_year":
			default:
				$start_date = date('Y-m-d', strtotime(date('Y-01-01')));
				$end_date = date('Y-m-d', strtotime(date('Y-12-31')));
				break;
		}

		return array (
			$start_date,
			$end_date
		);
	}
}