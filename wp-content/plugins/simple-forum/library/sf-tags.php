<?php
/*
Simple:Press
Suggested tags
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

if ( $_GET['sfaction'] == 'tags_from_yahoo' ) {
	sf_suggest_yahoo_tags();
} elseif ( $_GET['sfaction'] == 'tags_from_tagthenet' ) {
	sf_suggest_ttn_tags();
} elseif ( $_GET['sfaction'] == 'tags_from_local_db' ) {
	sf_suggest_local_tags();
}

die();

function sf_suggest_local_tags()
{
	global $wpdb;

	# Send good header HTTP
	status_header(200);
	header("Content-Type: text/javascript; charset=" . get_bloginfo('charset'));

	# Get existing tags
	$tags  = $wpdb->get_col("SELECT DISTINCT tag_name FROM ".SFTAGS);
	if (empty($tags))   # No tags to suggest
	{
		echo '<p>'.__('There are no tags for Simple:Press.', 'sforum').'</p>';
		exit();
	}

	# Get topic name and data
	$content = $_POST['content'] .' '. $_POST['title'];
	$content = trim($content);

	if (empty($content))
	{
		echo '<p>'.__('You need to create a topic title and post content before tags can be suggested.', 'sforum').'</p>';
		exit();
	}

	$found = 0;
	foreach ((array) $tags as $tag)
	{
		$tag = $tag;
		if (is_string($tag) && !empty($tag) && stristr($content, $tag))
		{
			echo '<span class="local">'.$tag.'</span>'."\n";
			$found = 1;
		}
	}

	if (!$found)
	{
		echo '<p>'.__('No suggested tags from existing Simple:Press tags.', 'sforum').'</p>';
		exit();
	}

	echo '<div class="clear"></div>';

	exit();
}

function sf_suggest_ttn_tags()
{
	# Send good header HTTP
	status_header( 200 );
	header("Content-Type: text/javascript; charset=" . get_bloginfo('charset'));

	# Get topic name and data
	$content = $_POST['content'] .' '. $_POST['title'];
	$content = trim($content);
	if (empty($content))
	{
		echo '<p>'.__('You need to create a topic title and post content before tags can be suggested.', 'sforum').'</p>';
		exit();
	}

	# Get Tag This Net tags
	$data = '';
	$reponse = wp_remote_post('http://tagthe.net/api/?text='.urlencode($content).'&view=json&count=200');
	if (!is_wp_error($reponse) ) {
		$code = wp_remote_retrieve_response_code($reponse);
		if ($code == 200)
		{
			$data = maybe_unserialize(wp_remote_retrieve_body($reponse));
		}
	}

	$data = json_decode($data);
	$data = $data->memes[0];
	$data = $data->dimensions;

	if ( !isset($data->topic) && !isset($data->location) && !isset($data->person) ) {
		echo '<p>'.__('No suggested tags from Tag The Net service.', 'sforum').'</p>';
		exit();
	}

	$tags = array();
	# Get all topics
	foreach ((array) $data->topic as $topic)
	{
		$tags[] = '<span class="ttn_topic">'.$topic.'</span>';
	}

	# Get all locations
	foreach ( (array) $data->location as $location ) {
		$tags[] = '<span class="ttn_location">'.$location.'</span>';
	}

	# Get all persons
	foreach ((array) $data->person as $person)
	{
		$tags[] = '<span class="ttn_person">'.$person.'</span>';
	}

	# Remove empty terms
	$tags = array_filter($tags, 'sf_delete_empty_element');
	$tags = array_unique($tags);

	echo implode("\n", $tags);
	echo '<div class="clear"></div>';

	exit();
}

function sf_suggest_yahoo_tags()
{
	# Send good header HTTP
	status_header( 200 );
	header("Content-Type: text/javascript; charset=" . get_bloginfo('charset'));

	# Get topic name and data
	$content = $_POST['content'] .' '. $_POST['title'];
	$content = trim($content);
	if (empty($content))
	{
		echo '<p>'.__('You need to create a topic title and post content before tags can be suggested.', 'sforum').'</p>';
		exit();
	}

	# Application entrypoint -> http://code.google.com/p/simple-tags/
	# Yahoo ID : h4c6gyLV34Fs7nHCrHUew7XDAU8YeQ_PpZVrzgAGih2mU12F0cI.ezr6e7FMvskR7Vu.AA--
	$yahoo_id = 'h4c6gyLV34Fs7nHCrHUew7XDAU8YeQ_PpZVrzgAGih2mU12F0cI.ezr6e7FMvskR7Vu.AA--';

	# Build params
	$param = 'appid='.$yahoo_id; # Yahoo ID
	$param .= '&context='.urlencode($content); # Post content
	if (!empty($_POST['tags']))
	{
		$param .= '&query='.urlencode($_POST['tags']);  # Existing tags
	}
	$param .= '&output=php'; # Get PHP Array !

	$data = array();
	$reponse = wp_remote_post('http://search.yahooapis.com/ContentAnalysisService/V1/termExtraction?'.$param);
	if (!is_wp_error($reponse) && $reponse != null)
	{
		$code = wp_remote_retrieve_response_code($reponse);
		if ($code == 200)
		{
			$data = maybe_unserialize(wp_remote_retrieve_body($reponse));
		}
	}

	if (empty($data) || empty($data['ResultSet']) || is_wp_error($data))
	{
		echo '<p>'.__('No suggested tags from Yahoo! service.', 'sforum').'</p>';
		exit();
	}

	# Get result value
	$data = (array) $data['ResultSet']['Result'];

	# Remove empty terms
	$data = array_filter($data, 'sf_delete_empty_element');
	$data = array_unique($data);

	foreach ((array) $data as $tag)
	{
		echo '<span class="yahoo">'.$tag.'</span>'."\n";
	}

	echo '<div class="clear"></div>';

	exit();
}

function sf_delete_empty_element(&$element)
{
	$element = $element;
	$element = trim($element);
	if (!empty($element))
	{
		return $element;
	}
}

?>