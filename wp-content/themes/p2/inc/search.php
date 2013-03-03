<?php
/**
 * Search and highlight.
 *
 * @package P2
 * @since unknown
 */

class P2_Search {

	function P2_Search() {
		// Bind search query filters
		add_filter( 'posts_distinct',   array( &$this, 'search_comments_distinct' ) );
		add_filter( 'posts_where',      array( &$this, 'search_comments_where'    ) );
		add_filter( 'posts_join',       array( &$this, 'search_comments_join'     ) );

		// Bind text highlighting filters
		add_filter( 'the_content',      array( &$this, 'highlight_terms_in_text'  ) );
		add_filter( 'the_excerpt',      array( &$this, 'highlight_terms_in_text'  ) );
		add_filter( 'comment_text',     array( &$this, 'highlight_terms_in_text'  ) );
		add_filter( 'get_the_tags',     array( &$this, 'highlight_terms_in_tags'  ) );
	}

	function search_comments_distinct( $distinct ) {
		global $wp_query;
		if ( ! empty( $wp_query->query_vars['s'] ) )
			return 'DISTINCT';
	}

	function search_comments_where( $where ) {
		global $wp_query, $wpdb;

		$q = $wp_query->query_vars;

		if ( empty( $q['s'] ) )
			return $where;

		$n = empty( $q['exact'] ) ? '%' : '';

		$search = array( "comment_post_ID = $wpdb->posts.ID AND comment_approved = '1'" );

		foreach( (array) $q['search_terms'] as $term ) {
			$term     = esc_sql( like_escape( $term ) );
			$search[] = "( comment_content LIKE '{$n}{$term}{$n}' )";
		}

		$search = " OR ( " . implode( " AND ", $search ) . " )";

		$where = preg_replace( "/\bor\b/i", "$search OR", $where, 1 );

		return $where;
	}

	function search_comments_join( $join ) {
		global $wp_query, $wpdb, $request;
		if (!empty($wp_query->query_vars['s']))
			$join .= " LEFT JOIN $wpdb->comments ON ( comment_post_ID = ID  AND comment_approved =  '1' )";
		return $join;
	}

	function get_search_terms() {
		$search = get_query_var( 's' );
		$search_terms = get_query_var( 'search_terms' );
		if ( !empty($search_terms) ) {
			return $search_terms;
		} else if ( !empty($search) ) {
			return array($search);
		}
		return array();
	}

	function highlight_terms_in_text( $text ) {
		$query_terms = array_filter( array_map( 'trim', $this->get_search_terms() ) );
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

	function highlight_terms_in_tags( $tags ) {
		$query_terms = array_filter( array_map( 'trim', $this->get_search_terms() ) );
		// tags are kept escaped in the db
		$query_terms = array_map( 'esc_html', $query_terms );
		foreach( array_filter((array)$tags) as $tag )
		    if ( in_array( trim($tag->name), $query_terms ) )
		        $tag->name ="<span class='hilite'>". $tag->name . "</span>";
		return $tags;
	}
}

?>