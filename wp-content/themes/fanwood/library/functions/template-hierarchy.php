<?php
/**
 * The framework has its own template hierarchy that can be used instead of the default WordPress 
 * template hierarchy.  It is not much different than the default.  It was built to extend the default by 
 * making it smarter and more flexible.  The goal is to give theme developers and end users an 
 * easy-to-override system that doesn't involve massive amounts of conditional tags within files.
 *
 * @package HybridCore
 * @subpackage Functions
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2008 - 2012, Justin Tadlock
 * @link http://themehybrid.com/hybrid-core
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Filter the date template. */
add_filter( 'date_template', 'hybrid_date_template' );

/* Filter the author/user template. */
add_filter( 'author_template', 'hybrid_user_template' );

/* Filter the tag and category (taxonomy) templates. */
add_filter( 'tag_template', 'hybrid_taxonomy_template' );
add_filter( 'category_template', 'hybrid_taxonomy_template' );
add_filter( 'taxonomy_template', 'hybrid_taxonomy_template' );

/* Filter the single, page, and attachment (singular) templates. */
add_filter( 'single_template', 'hybrid_singular_template' );
add_filter( 'page_template', 'hybrid_singular_template' );
add_filter( 'attachment_template', 'hybrid_singular_template' );

/**
 * Overrides WP's default template for date-based archives. Better abstraction of templates than 
 * is_date() allows by checking for the year, month, week, day, hour, and minute.
 *
 * @since 0.6.0
 * @access private
 * @uses locate_template() Checks for template in child and parent theme.
 * @param string $template
 * @return string $template Full path to file.
 */
function hybrid_date_template( $template ) {
	$templates = array();

	/* If viewing a time-based archive. */
	if ( is_time() ) {

		/* If viewing a minutely archive. */
		if ( get_query_var( 'minute' ) )
			$templates[] = 'minute.php';

		/* If viewing an hourly archive. */
		elseif ( get_query_var( 'hour' ) )
			$templates[] = 'hour.php';

		/* Catchall for any time-based archive. */
		$templates[] = 'time.php';
	}

	/* If viewing a daily archive. */
	elseif ( is_day() )
		$templates[] = 'day.php';

	/* If viewing a weekly archive. */
	elseif ( get_query_var( 'w' ) )
		$templates[] = 'week.php';

	/* If viewing a monthly archive. */
	elseif ( is_month() )
		$templates[] = 'month.php';

	/* If viewing a yearly archive. */
	elseif ( is_year() )
		$templates[] = 'year.php';

	/* Catchall template for date-based archives. */
	$templates[] = 'date.php';

	/* Fall back to the basic archive template. */
	$templates[] = 'archive.php';

	/* Return the found template. */
	return locate_template( $templates );
}

/**
 * Overrides WP's default template for author-based archives. Better abstraction of templates than 
 * is_author() allows by allowing themes to specify templates for a specific author. The hierarchy is 
 * user-$nicename.php, $user-role-$role.php, user.php, author.php, archive.php.
 *
 * @since 0.7.0
 * @access private
 * @uses locate_template() Checks for template in child and parent theme.
 * @param string $template
 * @return string Full path to file.
 */
function hybrid_user_template( $template ) {
	$templates = array();

	/* Get the user nicename. */
	$name = get_the_author_meta( 'user_nicename', get_query_var( 'author' ) );

	/* Get the user object. */
	$user = new WP_User( absint( get_query_var( 'author' ) ) );

	/* Add the user nicename template. */
	$templates[] = "user-{$name}.php";

	/* Add role-based templates for the user. */
	if ( is_array( $user->roles ) ) {
		foreach ( $user->roles as $role )
			$templates[] = "user-role-{$role}.php";
	}

	/* Add a basic user template. */
	$templates[] = 'user.php';

	/* Add backwards compatibility with the WordPress author template. */
	$templates[] = 'author.php';

	/* Fall back to the basic archive template. */
	$templates[] = 'archive.php';

	/* Return the found template. */
	return locate_template( $templates );
}

/**
 * Overrides WP's default template for category- and tag-based archives. This allows better 
 * organization of taxonomy template files by making categories and post tags work the same way as 
 * other taxonomies. The hierarchy is taxonomy-$taxonomy-$term.php, taxonomy-$taxonomy.php, 
 * taxonomy.php, archive.php.
 *
 * @since 0.7.0
 * @access private
 * @uses locate_template() Checks for template in child and parent theme.
 * @param string $template
 * @return string Full path to file.
 */
function hybrid_taxonomy_template( $template ) {

	/* Get the queried term object. */
	$term = get_queried_object();

	/* Remove 'post-format' from the slug. */
	$slug = ( ( 'post_format' == $term->taxonomy ) ? str_replace( 'post-format-', '', $term->slug ) : $term->slug );

	/* Return the available templates. */
	return locate_template( array( "taxonomy-{$term->taxonomy}-{$slug}.php", "taxonomy-{$term->taxonomy}.php", 'taxonomy.php', 'archive.php' ) );
}

/**
 * Overrides the default single (singular post) template.  Post templates can be loaded using a custom 
 * post template, by slug, or by ID.
 *
 * Attachment templates are handled slightly differently. Rather than look for the slug
 * or ID, templates can be loaded by attachment-$mime[0]_$mime[1].php, 
 * attachment-$mime[1].php, or attachment-$mime[0].php.
 *
 * @since 0.7.0
 * @access private
 * @param string $template The default WordPress post template.
 * @return string $template The theme post template after all templates have been checked for.
 */
function hybrid_singular_template( $template ) {

	$templates = array();

	/* Get the queried post. */
	$post = get_queried_object();

	/* Check for a custom post template by custom field key '_wp_post_template'. */
	$custom = get_post_meta( get_queried_object_id(), "_wp_{$post->post_type}_template", true );
	if ( $custom )
		$templates[] = $custom;

	/* If viewing an attachment page, handle the files by mime type. */
	if ( is_attachment() ) {
		/* Split the mime_type into two distinct parts. */
		$type = explode( '/', get_post_mime_type() );

		$templates[] = "attachment-{$type[0]}_{$type[1]}.php";
		$templates[] = "attachment-{$type[1]}.php";
		$templates[] = "attachment-{$type[0]}.php";
	}

	/* If viewing any other type of singular page. */
	else {

		/* Add a post name (slug) template. */
		$templates[] = "{$post->post_type}-{$post->post_name}.php";

		/* Add a post ID template. */
		$templates[] = "{$post->post_type}-{$post->ID}.php";
	}

	/* Add a template based off the post type name. */
	$templates[] = "{$post->post_type}.php";

	/* Allow for WP standard 'single' templates for compatibility. */
	$templates[] = "single-{$post->post_type}.php";
	$templates[] = 'single.php';

	/* Add a general template of singular.php. */
	$templates[] = "singular.php";

	/* Return the found template. */
	return locate_template( $templates );
}

?>