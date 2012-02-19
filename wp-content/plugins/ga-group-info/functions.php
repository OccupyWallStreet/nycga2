<?php
/*
Functions file
Contains prerequisite functions
*/

/*
 * HTML-ifies Twitter info & tweets (hashtags, usernames, links, etc.)
 * @since 0.1
 * @author Louie McCoy <louie@louiemccoy.com>
 * @param string $tweet A string with the tweet to be output
 * @retun string The HTML-ified tweet
 */
function gait_htmlify_tweet($string){
    $regex = array(
	'handle'	=>	'/\B@(\w+)\b/',
	'hashtag'	=>	'/\B#(\w+)\b/'
    );
    $format = array(
	'handle'	=>	'<a href="http://twitter.com/$1">@$1</a>',
	'hashtag'	=>	'<a href="https://twitter.com/#!/search?q=%23$1">#$1</a>'
    );
    $replaced = preg_replace( $regex, $format, $string );
    return $replaced;
}

/*
 * Wrapper function for tweet, URL, & email HTML-ification
 * @since 0.1
 * @author Louie McCoy <louie@louiemccoy.com>
 * @param string $string A String to be HTML-ified.
 * @return string The HTML-ified string.
 */
function gait_htmlify($string){
    $string = gait_htmlify_tweet( $string );
    $string = make_clickable( htmlspecialchars_decode( $string ) );
    return $string;
}
?>