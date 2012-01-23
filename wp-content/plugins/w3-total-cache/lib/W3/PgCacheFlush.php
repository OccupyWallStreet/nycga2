<?php

/**
 * W3 PgCache flushing
 */

require_once W3TC_LIB_W3_DIR . '/PgCache.php';

/**
 * Class W3_PgCacheFlush
 */
class W3_PgCacheFlush extends W3_PgCache {
    /**
     * PHP5 Constructor
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * PHP4 Constructor
     */
    function W3_PgCacheFlush() {
        $this->__construct();
    }

    /**
     * Flushes all caches
     *
     * @return boolean
     */
    function flush() {
        $cache = & $this->_get_cache();

        return $cache->flush();
    }

    /**
     * Flushes post cache
     *
     * @param integer $post_id
     * @return boolean
     */
    function flush_post($post_id = null) {
        if (!$post_id) {
            $post_id = $this->_detect_post_id();
        }

        if ($post_id) {
            $uris = array();
            $domain_url = w3_get_domain_url();
            $feeds = $this->_config->get_array('pgcache.purge.feed.types');

            if ($this->_config->get_boolean('pgcache.purge.terms') || $this->_config->get_boolean('pgcache.purge.feed.terms')) {
                $taxonomies = get_post_taxonomies($post_id);
                $terms = wp_get_post_terms($post_id, $taxonomies);
            }

            switch (true) {
                case $this->_config->get_boolean('pgcache.purge.author'):
                case $this->_config->get_boolean('pgcache.purge.archive.daily'):
                case $this->_config->get_boolean('pgcache.purge.archive.monthly'):
                case $this->_config->get_boolean('pgcache.purge.archive.yearly'):
                case $this->_config->get_boolean('pgcache.purge.feed.author'):
                    $post = get_post($post_id);
            }

            /**
             * Home URL
             */
            if ($this->_config->get_boolean('pgcache.purge.home')) {
                $home_path = w3_get_home_path();
                $site_path = w3_get_site_path();

                $uris[] = $home_path;

                if ($site_path != $home_path) {
                    $uris[] = $site_path;
                }
            }

            /**
             * Post URL
             */
            if ($this->_config->get_boolean('pgcache.purge.post')) {
                $post_link = post_permalink($post_id);
                $post_uri = str_replace($domain_url, '', $post_link);

                $uris[] = $post_uri;
            }

            /**
             * Post comments URLs
             */
            if ($this->_config->get_boolean('pgcache.purge.comments') && function_exists('get_comments_pagenum_link')) {
                $comments_number = get_comments_number($post_id);
                $comments_per_page = get_option('comments_per_page');
                $comments_pages_number = @ceil($comments_number / $comments_per_page);

                for ($pagenum = 1; $pagenum <= $comments_pages_number; $pagenum++) {
                    $comments_pagenum_link = $this->_get_comments_pagenum_link($post_id, $pagenum);
                    $comments_pagenum_uri = str_replace($domain_url, '', $comments_pagenum_link);

                    $uris[] = $comments_pagenum_uri;
                }
            }

            /**
             * Post author URLs
             */
            if ($this->_config->get_boolean('pgcache.purge.author') && $post) {
                $posts_number = count_user_posts($post->post_author);
                $posts_per_page = get_option('posts_per_page');
                $posts_pages_number = @ceil($posts_number / $posts_per_page);

                $author_link = get_author_link(false, $post->post_author);
                $author_uri = str_replace($domain_url, '', $author_link);

                for ($pagenum = 1; $pagenum <= $posts_pages_number; $pagenum++) {
                    $author_pagenum_link = $this->_get_pagenum_link($author_uri, $pagenum);
                    $author_pagenum_uri = str_replace($domain_url, '', $author_pagenum_link);

                    $uris[] = $author_pagenum_uri;
                }
            }

            /**
             * Post terms URLs
             */
            if ($this->_config->get_boolean('pgcache.purge.terms')) {
                $posts_per_page = get_option('posts_per_page');

                foreach ($terms as $term) {
                    $term_link = get_term_link($term, $term->taxonomy);
                    $term_uri = str_replace($domain_url, '', $term_link);
                    $posts_pages_number = @ceil($term->count / $posts_per_page);

                    for ($pagenum = 1; $pagenum <= $posts_pages_number; $pagenum++) {
                        $term_pagenum_link = $this->_get_pagenum_link($term_uri, $pagenum);
                        $term_pagenum_uri = str_replace($domain_url, '', $term_pagenum_link);

                        $uris[] = $term_pagenum_uri;
                    }
                }
            }

            /**
             * Daily archive URLs
             */
            if ($this->_config->get_boolean('pgcache.purge.archive.daily') && $post) {
                $post_date = strtotime($post->post_date);
                $post_year = gmdate('Y', $post_date);
                $post_month = gmdate('m', $post_date);
                $post_day = gmdate('d', $post_date);

                $posts_per_page = get_option('posts_per_page');
                $posts_number = $this->_get_archive_posts_count($post_year, $post_month, $post_day);
                $posts_pages_number = @ceil($posts_number / $posts_per_page);

                $day_link = get_day_link($post_year, $post_month, $post_day);
                $day_uri = str_replace($domain_url, '', $day_link);

                for ($pagenum = 1; $pagenum <= $posts_pages_number; $pagenum++) {
                    $day_pagenum_link = $this->_get_pagenum_link($day_uri, $pagenum);
                    $day_pagenum_uri = str_replace($domain_url, '', $day_pagenum_link);

                    $uris[] = $day_pagenum_uri;
                }
            }

            /**
             * Monthly archive URLs
             */
            if ($this->_config->get_boolean('pgcache.purge.archive.monthly') && $post) {
                $post_date = strtotime($post->post_date);
                $post_year = gmdate('Y', $post_date);
                $post_month = gmdate('m', $post_date);

                $posts_per_page = get_option('posts_per_page');
                $posts_number = $this->_get_archive_posts_count($post_year, $post_month);
                $posts_pages_number = @ceil($posts_number / $posts_per_page);

                $month_link = get_month_link($post_year, $post_month);
                $month_uri = str_replace($domain_url, '', $month_link);

                for ($pagenum = 1; $pagenum <= $posts_pages_number; $pagenum++) {
                    $month_pagenum_link = $this->_get_pagenum_link($month_uri, $pagenum);
                    $month_pagenum_uri = str_replace($domain_url, '', $month_pagenum_link);

                    $uris[] = $month_pagenum_uri;
                }
            }

            /**
             * Yearly archive URLs
             */
            if ($this->_config->get_boolean('pgcache.purge.archive.yearly') && $post) {
                $post_date = strtotime($post->post_date);
                $post_year = gmdate('Y', $post_date);

                $posts_per_page = get_option('posts_per_page');
                $posts_number = $this->_get_archive_posts_count($post_year);
                $posts_pages_number = @ceil($posts_number / $posts_per_page);

                $year_link = get_year_link($post_year);
                $year_uri = str_replace($domain_url, '', $year_link);

                for ($pagenum = 1; $pagenum <= $posts_pages_number; $pagenum++) {
                    $year_pagenum_link = $this->_get_pagenum_link($year_uri, $pagenum);
                    $year_pagenum_uri = str_replace($domain_url, '', $year_pagenum_link);

                    $uris[] = $year_pagenum_uri;
                }
            }

            /**
             * Feed URLs
             */
            if ($this->_config->get_boolean('pgcache.purge.feed.blog')) {
                foreach ($feeds as $feed) {
                    $feed_link = get_feed_link($feed);
                    $feed_uri = str_replace($domain_url, '', $feed_link);

                    $uris[] = $feed_uri;
                }
            }

            if ($this->_config->get_boolean('pgcache.purge.feed.comments')) {
                foreach ($feeds as $feed) {
                    $post_comments_feed_link = get_post_comments_feed_link($post_id, $feed);
                    $post_comments_feed_uri = str_replace($domain_url, '', $post_comments_feed_link);

                    $uris[] = $post_comments_feed_uri;
                }
            }

            if ($this->_config->get_boolean('pgcache.purge.feed.author') && $post) {
                foreach ($feeds as $feed) {
                    $author_feed_link = get_author_feed_link($post->post_author, $feed);
                    $author_feed_uri = str_replace($domain_url, '', $author_feed_link);

                    $uris[] = $author_feed_uri;
                }
            }

            if ($this->_config->get_boolean('pgcache.purge.feed.terms')) {
                foreach ($terms as $term) {
                    foreach ($feeds as $feed) {
                        $term_feed_link = get_term_feed_link($term->term_id, $term->taxonomy, $feed);
                        $term_feed_uri = str_replace($domain_url, '', $term_feed_link);

                        $uris[] = $term_feed_uri;
                    }
                }
            }

            /**
             * Flush cache
             */
            if (count($uris)) {
                $cache = & $this->_get_cache();
                $mobile_groups = $this->_get_mobile_groups();
                $referrer_groups = $this->_get_referrer_groups();
                $encryptions = $this->_get_encryptions();
                $compressions = $this->_get_compressions();

                foreach ($uris as $uri) {
                    foreach ($mobile_groups as $mobile_group) {
                        foreach ($referrer_groups as $referrer_group) {
                            foreach ($encryptions as $encryption) {
                                foreach ($compressions as $compression) {
                                    $page_key = $this->_get_page_key($uri, $mobile_group, $referrer_group, $encryption, $compression);

                                    $cache->delete($page_key);
                                }
                            }
                        }
                    }
                }

                /**
                 * Purge varnish servers
                 */
                if ($this->_config->get_boolean('varnish.enabled')) {
                    $varnish = & w3_instance('W3_Varnish');

                    foreach ($uris as $uri) {
                        $varnish->purge($uri);
                    }
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Returns array of mobile groups
     *
     * @return array
     */
    function _get_mobile_groups() {
        $mobile_groups = array('');

        if ($this->_mobile) {
            $mobile_groups = array_merge($mobile_groups, array_keys($this->_mobile->groups));
        }

        return $mobile_groups;
    }

    /**
     * Returns array of referrer groups
     *
     * @return array
     */
    function _get_referrer_groups() {
        $referrer_groups = array('');

        if ($this->_referrer) {
            $referrer_groups = array_merge($referrer_groups, array_keys($this->_referrer->groups));
        }

        return $referrer_groups;
    }

    /**
     * Returns array of encryptions
     *
     * @return array
     */
    function _get_encryptions() {
        return array(false, 'ssl');
    }

    /**
     * Detects post ID
     *
     * @return integer
     */
    function _detect_post_id() {
        global $posts, $comment_post_ID, $post_ID;

        if ($post_ID) {
            return $post_ID;
        } elseif ($comment_post_ID) {
            return $comment_post_ID;
        } elseif (is_single() || is_page() && count($posts)) {
            return $posts[0]->ID;
        } elseif (isset($_REQUEST['p'])) {
            return (integer) $_REQUEST['p'];
        }

        return 0;
    }

    /**
     * Workaround for get_pagenum_link function
     *
     * @param string $url
     * @param int $pagenum
     * @return string
     */
    function _get_pagenum_link($url, $pagenum = 1) {
        $request_uri = $_SERVER['REQUEST_URI'];
        $_SERVER['REQUEST_URI'] = $url;

        $link = get_pagenum_link($pagenum);

        $_SERVER['REQUEST_URI'] = $request_uri;

        return $link;
    }

    /**
     * Workaround for get_comments_pagenum_link function
     *
     * @param integer $post_id
     * @param integer $pagenum
     * @param integer $max_page
     * @return string
     */
    function _get_comments_pagenum_link($post_id, $pagenum = 1, $max_page = 0) {
        if (isset($GLOBALS['post']) && is_object($GLOBALS['post'])) {
            $old_post = &$GLOBALS['post'];
        } else {
            @$GLOBALS['post'] = & new stdClass();
            $old_post = null;
        }

        $GLOBALS['post']->ID = $post_id;

        $link = get_comments_pagenum_link($pagenum, $max_page);

        if ($old_post) {
            $GLOBALS['post'] = &$old_post;
        }

        return $link;
    }

    /**
     * Returns number of posts in the archive
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @return int
     */
    function _get_archive_posts_count($year = 0, $month = 0, $day = 0) {
        global $wpdb;

        $filters = array(
            'post_type = "post"',
            'post_status = "publish"'
        );

        if ($year) {
            $filters[] = sprintf('YEAR(post_date) = %d', $year);
        }

        if ($month) {
            $filters[] = sprintf('MONTH(post_date) = %d', $month);
        }

        if ($day) {
            $filters[] = sprintf('DAY(post_date) = %d', $day);
        }

        $where = implode(' AND ', $filters);

        $sql = sprintf('SELECT COUNT(*) FROM %s WHERE %s', $wpdb->posts, $where);

        $count = (int) $wpdb->get_var($sql);

        return $count;
    }
}
