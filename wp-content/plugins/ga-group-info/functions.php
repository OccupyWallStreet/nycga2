<?php
/*
Functions file
Contains prerequisite functions
*/

/*
 * Adds Twitter links to WP's built-in make-clickable function
 * @since 0.1
 * @author Louie McCoy <louie@louiemccoy.com>
 * @param string $string A String to be HTML-ified.
 * @return string The HTML-ified string.
 */
function gait_htmlify($string){
    $regex = array(
	'handle'	=>	'/\B@(\w+)\b/',
	'hashtag'	=>	'/\B#(\w+)\b/'
    );
    $format = array(
	'handle'	=>	'<a href="http://twitter.com/$1">@$1</a>',
	'hashtag'	=>	'<a href="https://twitter.com/#!/search?q=%23$1">#$1</a>'
    );
    $string = preg_replace( $regex, $format, $string );
    $string = make_clickable( htmlspecialchars_decode( $string ) );
    return $string;
}

/*
 * Filters Multiline content
 * @since 0.1
 * @author Louie McCoy <louie@louiemccoy.com>
 * @param unfiltered content
 * @return string The full filtered html of the multiline content
 */
function gait_content($content){
    $content = apply_filters('the_content', $content); // Standard WP the_content() filters
    $content = str_replace(']]>', ']]&gt;', $content);
    $content = gait_htmlify( $content ); // Info-tab specific function to make things clickable
    echo $content;
}
?>