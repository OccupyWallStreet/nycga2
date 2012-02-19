<?php
/*
Functions file
Contains prerequisite functions
*/

/*
 * HTML-ifies links
 * @since 0.1
 * @author Louie McCoy <louie@louiemccoy.com>
 * @param string $string Any string containing a link to be html-ified
 * @return string The HTML-ified string
 */
function gait_htmlify_links($string) {
    $regex = '/((?:[\w\d]+\:\/\/)?(?:[\w\-\d]+\.)+[\w\-\d]+(?:\/[\w\-\d]+)*(?:\/|\.[\w\-\d]+)?(?:\?[\w\-\d]+\=[\w\-\d]+\&?)?(?:\#[\w\-\d]*)?)/';
    $format = '<a href="$1">$1</a>';
    $replaced = preg_replace( $regex, $format, $string );
    return $string;
}

/*
 * HTML-ifies & Obfuscates email addresses -
 * checks to see if [pluginname] is available for use before doing it manually
 * @since 0.1
 * @author Louie McCoy <louie@louiemccoy.com>
 * @param string $string The string containing an email address to be modified
 * @return string The string containing the html-ified & obfuscated email
 */
function gait_htmlify_email($string) {
    $regex = '/([\w\-\d]+\@[\w\-\d]+\.[\w\-\d]+)/';
    $format = '<a href="mailto:$1">$1</a>';
    $replaced = preg_replace( $regex, $format, $string );
    return $replaced;
}

/*
 * HTML-ifies Twitter info & tweets (hashtags, usernames, links, etc.)
 * @since 0.1
 * @author Louie McCoy <louie@louiemccoy.com>
 * @param string $tweet A string with the tweet to be output
 * @retun string The HTML-ified tweet
 */
function gait_htmlify_tweet($string){
    $string = gait_htmlify_links($string);
    $regex = array(
	'handle'	=>	'/\B@(\w+)\b/',
	'hashtag'	=>	'/#([a-zA-Z0-9]+)/'
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
    $string = gait_htmlify_links( $string );
    $string = gait_htmlify_email( $string );
    $string = gait_htmlify_tweet( $string );
    return $string;
}
?>