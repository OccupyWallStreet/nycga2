<?php
/*
Simple:Press
Filters
$LastChangedDate: 2011-06-02 05:56:53 -0700 (Thu, 02 Jun 2011) $
$Rev: 6226 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# ===FILTERS - USEAGE ==============================================
#
#	sf_filter_content_save($content, $action)
#	sf_filter_content_display($content)
#	sf_filter_content_edit($content)
#		Used for main post/message content so includes html, images,
#		profanity etc.
#
#	sf_filter_text_save($content)
#	sf_filter_text_display($content)
#	sf_filter_text_edit($content)
#		Used for larger areas of text where html is allowed like
#		admin defined message areas etc.
#
#	sf_filter_title_save($content)
#	sf_filter_title_display($content)
#		Used for title text where no html allowed such as forum
#		titles, custom labels and links etc.
#
#	sf_filter_name_save($content)
#	sf_filter_name_display($content)
#		Used for user names such as guest name and display name etc.
#
#	sf_filter_email_save($email)
#	sf_filter_email_display($email)
#		Used for email addresses
#
#	sf_filter_url_save($url)
#	sf_filter_url_display($url)
#		Used for URLs
#
#	sf_filter_filename_save($filename)
#		used for all filenames - i.e., custom icons etc
#
#	sf_filter_signature_display($content)
#		special for siganture display
#
#	sf_filter_tooltip_display($content)
#		special for post tooltips
#
#	sf_filter_syntax_display($content)
#		special for synrax highlighting
#
#	sf_filter_rss_display($content)
#		Used for post content in rss feed
#
#	sf_filter_table_prefix($content)
#		removes prefix from tablename in seaches
#
# ==================================================================


# ===START OF SAVE FILTERS==========================================
#
# CONTENT - SAVE FILTERS UMBRELLA
#
# Used:	Forum Post (including Quick Reply)
#		Private Messages
#		Blog Linking Posts and Comments
#		?
# $action will be 'new' or 'edit'

function sf_filter_content_save($content, $action)
{
	global $sfglobals, $current_user;
	$sffilters = sf_get_option('sffilters');

	# 1: Swap smileys for img tags. Do it early before kses
	# NOTE Was a display filter - still needs to be a
	# display filter for backward compatibility.
	$content = sf_filter_display_smileys($content);

    # 2: prepare edits if using tinymce
    if ($action == 'edit' && $sfglobals['editor']['sfeditor'] == RICHTEXT)
    {
        $content = addslashes($content);
    }

	# 3: convert code tags to our own code display tags
	$content = sf_filter_save_codetags1($content, $sfglobals['editor']['sfeditor'], $action);

	# 4: run it through kses
	$content = sf_filter_save_kses($content);

	# 5: parse it for the wp oEmbed class
	$content = sf_filter_save_oEmbed($content);

	# 6: remove nbsp and p/br tags
	$content = sf_filter_save_linebreaks($content);

	# 7: revist code tags in case post edit save
	$content = sf_filter_save_codetags2($content, $sfglobals['editor']['sfeditor'], $action);

	# 8: remove 'pre' tags (optional)
	if ($sffilters['sffilterpre']) {
		$content = sf_filter_save_pre($content);
	}

	# 9: deal with single quotes (tinymce encodes them)
	$content = sf_filter_save_quotes($content);

	# 10: balance html tags
	$content = sf_filter_save_balancetags($content);

	# 11: format links (optional)
	if($sffilters['sfurlchars']) {
		$content = sf_filter_save_links($content, $sffilters['sfurlchars']);
	}

	# 12: add nofollow to links (optional)
	if ($sffilters['sfnofollow']) {
		$content = sf_filter_save_nofollow($content);
	}

	# 13: add target blank (optional)
	if ($sffilters['sftarget']) {
		$content = sf_filter_save_target($content);
	}

	# 14: profanity filter
	$content = sf_filter_save_profanity($content);

	# 15: escape it All
	$content = sf_filter_save_escape($content);

	# 16: strip spoiler shortcode if not allowed
	if (!$current_user->sfspoilers)
    {
	   $content = sf_filter_save_spoiler($content);
    }

	# 17: Try and determine images widths if not set
	$content = sf_filter_save_images($content);

	# 18: apply any users custom filters
	$content = apply_filters('sf_save_post_content', $content);

	return $content;
}

# ==================================================================
#
# TEXT - SAVE FILTERS UMBRELLA
#
# Used:	Profile Description
#		Blog Linking Link Text
#		Group Message
#		Forum Message
#		Email Messages
#		Signature Text
#		Announce Tag Heading/Text
#		Sneak Peak Message
#		Admin View Message
#		Custom Editor Messages
#		Registration/Privacy Messages
#		Custom Profile Message
#		Admins Off-Line Message
#		?

function sf_filter_text_save($content, $links=true)
{
	global $sfglobals;
	$sffilters = sf_get_option('sffilters');

	# Decode the entities first that were applied for display
	$content = html_entity_decode($content, ENT_COMPAT, SFCHARSET);

	# 1: run it through kses
	$content = sf_filter_save_kses($content);

	# 2: remove nbsp and p/br tags
	$content = sf_filter_save_linebreaks($content);

	# 3: deal with single quotes (tinymce encodes them)
	$content = sf_filter_save_quotes($content);

	# 4: balance html tags
	$content = sf_filter_save_balancetags($content);

    # are we altering links?
    if ($links)
    {
    	# 5: format links (optional)
    	if($sffilters['sfurlchars']) {
    		$content = sf_filter_save_links($content, $sffilters['sfurlchars']);
    	}

 		# 6: add nofollow to links (optional)
    	if ($sffilters['sfnofollow']) {
    		$content = sf_filter_save_nofollow($content);
    	}

    	# 7: add target blank (optional)
    	if ($sffilters['sftarget']) {
    		$content = sf_filter_save_target($content);
    	}

    }
	# 8: escape it All
	$content = sf_filter_save_escape($content);

	return $content;
}

# ==================================================================
#
# TITLE - SAVE FILTERS UMBRELLA
#
# Used:	Group Title/Description
#		Forum Title/Description
#		Topic Title
#		Message Title
#		Blog Linking Titles
#		Email Subject
#		Custom Meta Description/Keywords
#		Topic Status Name/List
#		Custom Icon Title
#		UserGroup Name/Description
#		Permission Name/Description
#		Profile Form Labels
#		?

function sf_filter_title_save($content)
{
	# 1: remove all html
	$content = sf_filter_save_nohtml($content);

	# 2: encode brackets
	$content = sf_filter_save_brackets($content);

	# 3: profanity filter
	$content = sf_filter_save_profanity($content);

	# 4: escape it All
	$content = sf_filter_save_escape($content);

	return $content;
}

# ==================================================================
#
# USER NAMES - SAVE FILTERS UMBRELLA
#
# Used:	Display Name
#		Guest Name
#		?

function sf_filter_name_save($content)
{
	# 1: Remove any html
	$content = sf_filter_save_nohtml($content);

	# 2: Encode
	$content = sf_filter_save_encode($content);

	# 3: escape it
	$content = sf_filter_save_escape($content);

	return $content;
}

# ==================================================================
#
# EMAIL ADDRESS - SAVE FILTERS UMBRELLA
#
# Used:	Guest posts
#		User profile
#		?

function sf_filter_email_save($email)
{
	# 1: Remove any html
	$email = sf_filter_save_nohtml($email);

	# 2: Validate and Sanitise Email
	$email = sf_filter_save_cleanemail($email);

	# 3: escape it
	$email = sf_filter_save_escape($email);

	return $email;
}

# ==================================================================
#
# URL - SAVE FILTERS UMBRELLA
#
# Used: All URLs
#		?

function sf_filter_url_save($url)
{
	# 1: clean up url for database
	$url = sf_filter_save_cleanurl($url);

	return $url;
}

# ==================================================================
#
# FILENAME - SAVE FILTERS UMBRELLA
#
# Used:	Avatar Upload
#		Avatar Pool
#		Signature Image
#		Custom Icons
#		Smileys
#		Editor Stylesheets
#		Registration/Privacy Documents
#		?

function sf_filter_filename_save($filename)
{
	# 1: clean up filename
	$filename = sf_filter_save_filename($filename);

	return $filename;
}


# ===START OF SAVE FILTERS==========================================

# ------------------------------------------------------------------
# sf_filter_save_codetags1()
#
# Try and change code tags to our code divs
#	$content:		Unfiltered post content
# ------------------------------------------------------------------
function sf_filter_save_codetags1($content, $editor, $action)
{
	global $sfglobals;

	switch($editor)
	{
		case RICHTEXT:
			# TinyMCE
			include_once (SF_PLUGIN_DIR.'/forum/parsers/sf-rtetohtml.php');
			$content = sf_RTE2Html(" ".$content);
			break;

		case BBCODE:
			# Send everything through bbCode below as some users use it in all editors
			break;

		default:
			# HTML and Plain Textaera
			include_once (SF_PLUGIN_DIR.'/forum/parsers/sf-rawtohtml.php');
			$content = sf_Raw2Html(" ".$content);
			break;
	}

    # strip excess slashes if editing for code segments
    if ($action == 'edit' && $sfglobals['editor']['sfeditor'] == RICHTEXT)
    {
        $content = stripslashes($content);
    }

	# Now perform the bbCode which will preserve above changes
	include_once (SF_PLUGIN_DIR.'/forum/parsers/sf-bbtohtml.php');
	$content = addslashes(sf_BBCode2Html(" ".stripslashes($content), false));

	# Shouldn't need any of these but there just in case...
	$content = str_replace('<code>', '<div class="sfcode">', $content);
	$content = str_replace('</code>', '</div>', $content);

	$content = str_replace('&lt;code&gt;', '<div class="sfcode">', $content);
	$content = str_replace('&lt;/code&gt;', '</div>', $content);

	return $content;
}

# ------------------------------------------------------------------
# sf_filter_save_codetags2()
#
# May be post edit save - so remove br's
#	$content:		Unfiltered post content
#	$editor:		Which editor
#	$action:		'new' or 'edit'
# ------------------------------------------------------------------
function sf_filter_save_codetags2($content, $editor, $action)
{
	# check if syntax highlighted - if so not needed
	$sfsyntax = sf_get_option('sfsyntax');
    if ($sfsyntax['sfsyntaxforum'] == true && strpos($content, 'brush-'))
	{
		return $content;
	}

	# ONLY used for a TintMCE RichText Save 'Edit'

	if($editor == RICHTEXT && $action == 'edit')
    {
		# recheck extra line breaks and p tags - might have come from an edit
		$content = preg_replace_callback('/\<div class=\"sfcode\"\>(.*?)\<\/div\>/ms', "sf_codetag_callback", stripslashes($content));
	}

	return $content;
}

function sf_codetag_callback($s)
{
	$content = str_replace("<br />", "", $s[1]);
	$content = str_replace("\n", "", $content);
	$content = '<div class="sfcode">'.$content.'</div>';

	return $content;
}

# ------------------------------------------------------------------
# sf_filter_save_kses()
#
# Run it through kses - needs to be unescaped first
#	$content:		Unfiltered post content
# ------------------------------------------------------------------
function sf_filter_save_kses($content)
{
	global $allowedforumtags, $allowedforumprotocols;

	if(!isset($allowedforumtags))
	{
		sf_kses_array();
		$allowedforumtags = apply_filters('sf_custom_kses', $allowedforumtags);
	}

	$content = wp_kses(stripslashes($content), $allowedforumtags, $allowedforumprotocols);

	return $content;
}

# ------------------------------------------------------------------
# sf_filter_save_linebreaks()
#
# Swap tinymce constructs with br's
#	$content:		Unfiltered post content
# ------------------------------------------------------------------
function sf_filter_save_linebreaks($content)
{
	$gap ='<p>'.chr(194).chr(160).'</p>'.chr(13).chr(10);
	$end ='<p>'.chr(194).chr(160).'</p>';

	# trim unwanted empty space
	$content = trim($content);

	while(substr($content, 0, 11) == $gap)
	{
		$content = substr_replace($content, '', 0, 11);
	}

	while(substr($content, (strlen($content)-9), 9) == $end)
	{
		$content = substr_replace($content, '', (strlen($content)-9), 9);
	}

	while(substr($content, (strlen($content)-11), 11) == $gap)
	{
		$content = substr_replace($content, '', (strlen($content)-11), 11);
	}

	# On savibng edit a 'br' may have a trailng line break which
	# will display like a paragraph break
	$content = str_replace("<br />".chr(13).chr(10), "\n", $content);

	# change br's to linebreaks
	$content = str_replace("<br />", "\n", $content);

	# change tiny blank line to a newline
	$content = str_replace($gap.$gap, $gap, $content);

	# same for blank line with p tags
	$content = str_replace("<p></p>", "\n\n", $content);
	$content = str_replace("<p> </p>", "\n\n", $content);

	$content = str_replace("<p>", "", $content);
	$content = str_replace("</p>", chr(13).chr(10), $content);

	return $content;
}

# ------------------------------------------------------------------
# sf_filter_save_pre()
#
# Remove html 'pre' and '/pre' tags
#	$content:		Unfiltered post content
# ------------------------------------------------------------------
function sf_filter_save_pre($content)
{
	# remove pre tags
	$content = str_replace("<pre>", "", $content);
	$content = str_replace("</pre>", "", $content);

	$content = str_replace('&lt;pre&gt;', '', $content);
	$content = str_replace('&lt;/pre&gt;', '', $content);

	return $content;
}

# ------------------------------------------------------------------
# sf_filter_save_quotes()
#
# Turn encoded single quote back
#	$content:		Unfiltered post content
# ------------------------------------------------------------------
function sf_filter_save_quotes($content)
{
	$content = str_replace("&#39;", "'", $content);
	# Replace those odd 0003 chars we have seen here and there
	$content = str_replace(chr(003), "'", $content);

	return $content;
}

# ------------------------------------------------------------------
# sf_filter_save_balancetags()
#
# Tried to balance html tags
#	$content:		Unfiltered post content
# ------------------------------------------------------------------
function sf_filter_save_balancetags($content)
{
	$content = balanceTags($content, true);

	return $content;
}

# ------------------------------------------------------------------
# sf_filter_save_nofollow()
#
# Adds nofollow to links at save post time
#	$content:		Unfiltered post content
# ------------------------------------------------------------------

function sf_filter_save_nofollow($content)
{
	$content = preg_replace_callback('|<a (.+?)>|i', 'sf_nofollow_callback', $content);
	return $content;
}

function sf_nofollow_callback($matches)
{
	$text = $matches[1];
	$text = str_replace(array(' rel="nofollow"', " rel='nofollow'", 'rel="nofollow"', "rel='nofollow'"), '', $text);
	return '<a '.$text.' rel="nofollow">';
}

# ------------------------------------------------------------------
# sf_filter_save_target()
#
# Forces target _blank to links at save post time
#	$content:		Unfiltered post content
# ------------------------------------------------------------------
function sf_filter_save_target($content)
{
	$content = preg_replace_callback('|<a (.+?)>|i', 'sf_target_callback', $content);
	return $content;
}

function sf_target_callback($matches)
{
	$text = $matches[1];
	if(strpos($text, 'javascript:void(0)')) return "<a ".$text.">";
	$text = str_replace(array(' target="_blank"', " target='_blank'", 'target="_blank"', "target='_blank'"), '', $text);
	return '<a '.$text.' target="_blank">';
}

# ------------------------------------------------------------------
# sf_filter_save_links()
#
# Turns urtls in posts to clickable links with shortened text
#	$content:		Unfiltered post content
# Thanks to Peter at http://www.theblog.ca/shorten-urls for this
# ------------------------------------------------------------------
function sf_filter_save_links($content, $charcount)
{
	# make links clickable
	$content = sf_make_clickable($content);

	# pad it with a space
	$content = ' ' . $content;

	# chunk those long urls
	sf_format_links($content, $charcount);

	$content = preg_replace("#(\s)([a-z0-9\-_.]+)@([^,< \n\r]+)#i", "$1<a href=\"mailto:$2@$3\">$2@$3</a>", $content);

	# Remove our padding..
	$content = substr($content, 1);

	return($content);
}

function sf_format_links(&$content, $charcount)
{
	$links = explode('<a', $content);
	$countlinks = count($links);
	for ($i = 0; $i < $countlinks; $i++)
	{
		$link = $links[$i];
		$link = (preg_match('#(.*)(href=")#is', $link)) ? '<a' . $link : $link;
		$begin = strpos($link, '>') + 1;
		$end = strpos($link, '<', $begin);
		$length = $end - $begin;
		$urlname = substr($link, $begin, $length);

		# We chunk urls that are longer than 50 characters. Just change
		# '50' to a value that suits your taste. We are not chunking the link
		# text unless if begins with 'http://', 'ftp://', or 'www.'
		$chunked = (strlen($urlname) > $charcount && preg_match('#^(http://|ftp://|www\.)#is', $urlname)) ? substr_replace($urlname, '.....', ($charcount - 10), -10) : $urlname;
		$content = str_replace('>' . $urlname . '<', '>' . $chunked . '<', $content);
	}
}

# ------------------------------------------------------------------
# sf_filter_save_profanity()
#
# Swaps any unwanted words for alternatives in post content
#	$content:		Unfiltered post content
# ------------------------------------------------------------------
function sf_filter_save_profanity($content)
{
	$badwords = explode("\n", stripslashes(sf_get_option('sfbadwords')));
	$replacementwords = explode("\n", stripslashes(sf_get_option('sfreplacementwords')));

	# need to add in delimiter for preg replace
	foreach ($badwords as $index => $badword)
	{
        if (!empty($badword))
        {
            $badwords[$index] = '/\b'.trim($badword).'\b/i';
            $replacementwords[$index] = trim($replacementwords[$index]);
        } else {
            unset($badwords[$index]);
        }
	}

	# filter the bad words
	$content = preg_replace($badwords, $replacementwords, $content);

	return $content;
}

# ------------------------------------------------------------------
# sf_filter_save_nohtml()
#
# Remove unwanted html
#	$title:		Unfiltered title content
# ------------------------------------------------------------------
function sf_filter_save_nohtml($content)
{
	$content = wp_kses(stripslashes($content), array());
	return $content;
}

# ------------------------------------------------------------------
# sf_filter_save_brackets()
#
# Remove square brackets from titles
#	$content:		Unfiltered post content
# ------------------------------------------------------------------
function sf_filter_save_brackets($content)
{
	$content = str_replace('[', '&#091;', $content);
	$content = str_replace(']', '&#093;', $content);

	return $content;
}

# ------------------------------------------------------------------
# sf_filter_save_escape()
#
# escape content before saving
#	$content:		Unfiltered post content
# ------------------------------------------------------------------
function sf_filter_save_escape($content)
{
	$content = esc_sql($content);

	return $content;
}

# ------------------------------------------------------------------
# sf_filter_save_filename()
#
# Sanitises a filename and makes it safe
#	$filename:		Unfiltered file name
# ------------------------------------------------------------------
function sf_filter_save_filename($filename)
{
	$filename_raw = $filename;
	$special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}", chr(0));
	$filename = str_replace($special_chars, '', $filename);
	$filename = preg_replace('/[\s-]+/', '-', $filename);
	$filename = trim($filename, '.-_');

	# Split the filename into a base and extension[s]
	$parts = explode('.', $filename);

	# Return if only one extension
	if ( count($parts) <= 2 )
		return $filename;

	# Process multiple extensions
	$filename = array_shift($parts);
	$extension = array_pop($parts);
	$mimes =  get_allowed_mime_types();

	# Loop over any intermediate extensions.  Munge them with a trailing underscore if they are a 2 - 5 character
	# long alpha string not in the extension whitelist.
	foreach ( (array) $parts as $part) {
		$filename .= '.' . $part;

		if ( preg_match("/^[a-zA-Z]{2,5}\d?$/", $part) ) {
			$allowed = false;
			foreach ( $mimes as $ext_preg => $mime_match ) {
				$ext_preg = '!(^' . $ext_preg . ')$!i';
				if ( preg_match( $ext_preg, $part ) ) {
					$allowed = true;
					break;
				}
			}
			if ( !$allowed )
				$filename .= '_';
		}
	}
	$filename = str_replace(' ', '_', $filename);
	$filename .= '.' . $extension;

	return $filename;
}

# ------------------------------------------------------------------
# sf_filter_save_encode()
#
# Encode atributes
#	$content:		usually a display name
# ------------------------------------------------------------------
function sf_filter_save_encode($content)
{
	$content = esc_attr($content);

	return $content;
}

# ------------------------------------------------------------------
# sf_filter_save_cleanemail()
#
# Sanitises am email address and makes it safe
#	$filename:		Unfiltered file name
# ------------------------------------------------------------------
function sf_filter_save_cleanemail($email)
{
	$email = sanitize_email($email);

	return $email;
}

# ------------------------------------------------------------------
# sf_filter_save_cleanurl()
#
# Sanitises an url for db  and makes it safe
#	$url:		Unfiltered url
# ------------------------------------------------------------------
function sf_filter_save_cleanurl($url)
{
	$url = esc_url_raw($url);

	return $url;
}

# ------------------------------------------------------------------
# sf_filter_save_spoiler() and support functions
#
# Remove spoilers from content if not allowed
#	$content:		Unfiltered post content
# ------------------------------------------------------------------
function sf_filter_save_spoiler($content)
{
    $content = preg_replace('/\[spoiler\][^>]*\[\/spoiler\]/', '' , $content);
	return $content;
}

# ------------------------------------------------------------------
# sf_filter_save_oEmbed() and support function
#
# Checks urls against the WP oEmbed class and pulls in the embed
# code if a match is found. Performed before other url checks
#	$content:		Unfiltered post content
# ------------------------------------------------------------------
function sf_filter_save_oEmbed($content)
{
	$content = preg_replace_callback('#(?<!=\')(?<!=")(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&:/~\+\#]*[\w\-\@?^=%&amp;/~\+\#])?#i', 'sf_check_save_oEmbed', $content);
	return $content;
}

function sf_check_save_oEmbed($match)
{
	$url = wp_oembed_get($match[0]);
	if(empty($url)) $url=$match[0];
	return $url;
}

# ------------------------------------------------------------------
# sf_filter_save_images() and support functions
#
# Set the width of images if possible at save time
#	$content:		Unfiltered post content
# ------------------------------------------------------------------
function sf_filter_save_images($content)
{
	return sf_check_save_image_width($content);
}

function sf_check_save_image_width($content)
{
	$content = preg_replace_callback('/<img[^>]*>/', 'sf_check_save_width' , $content);
	return $content;
}

function sf_check_save_width($match)
{
	global $SFPATHS;

	$out = '';
	$match[0] = stripslashes($match[0]);

	preg_match('/title\s*=\s*"([^"]*)"|title\s*=\s*\'([^\']*)\'/i', $match[0], $title);
	preg_match('/alt\s*=\s*"([^"]*)"|alt\s*=\s*\'([^\']*)\'/i', $match[0], $alt);
	preg_match('/width\s*=\s*"([^"]*)"|width\s*=\s*\'([^\']*)\'/i', $match[0], $width);
	preg_match('/src\s*=\s*"([^"]*)"|src\s*=\s*\'([^\']*)\'/i', $match[0], $src);
	preg_match('/style\s*=\s*"([^"]*)"|style\s*=\s*\'([^\']*)\'/i', $match[0], $style);
	preg_match('/class\s*=\s*"([^"]*)"|class\s*=\s*\'([^\']*)\'/i', $match[0], $class);

	if(isset($width[1])) return $match[0];

	if (isset($class[1])) return $match[0];

	if ((strpos($src[1], 'plugins/emotions')) || (strpos($src[1], 'images/smilies')) || (strpos($src[1], $SFPATHS['smileys'])))
	{
		$out = str_replace('img src', 'img class="sfsmiley" src', $match[0]);
		return $out;
	}

	# figure out whether its relative path (same server) or a url
    $parsed = parse_url($src[1]);
    if (array_key_exists('scheme', $parsed))
    {
    	$srcfile = $src[1];  # url, so leave it alone
    } else {
  		$srcfile = $_SERVER['DOCUMENT_ROOT'].$src[1];  # relative path, so add DOCUMENT_ROOT to path
  	}

	if (empty($width[1]))
	{
		global $gis_error;
		$gis_error = '';
		set_error_handler('sf_gis_error');

		$size = getimagesize($srcfile);

		restore_error_handler();
		if ($gis_error == '')
		{
			if ($size[0])
			{
				$width[1] = $size[0];
			} else {
				return '['.__('Image Can Not Be Found', 'sp').']';
			}
		}
	}

	if (isset($src[1])) 	$thissrc = 		'src="'.$src[1].'" '; 		else $thissrc = '';
	if (isset($title[1])) 	$thistitle = 	'title="'.$title[1].'" '; 	else $thistitle = '';
	if (isset($alt[1])) 	$thisalt = 		'alt="'.$alt[1].'" '; 		else $thisalt = '';
	if (isset($width[1]))	$thiswidth =	'width="'.$width[1].'" ';	else $thiswidth = '';
	if (isset($style[1]))	$thisstyle =	'style="'.$style[1].'" ';	else $thisstyle = '';
	if (isset($class[1]))	$thisclass =	'class="'.$class[1].'" ';	else $thisclass = '';

	$out.= esc_sql('<img '.$thissrc.$thiswidth.$thisstyle.$thisclass.$thistitle.$thisalt.'/>');
	return $out;
}

# ===END OF SAVE FILTERS============================================

# ===START OF EDIT FILTERS==========================================
#
# CONTENT - EDIT FILTERS UMBRELLA
#
# Used:	Forum Post
#		?

function sf_filter_content_edit($content)
{
	global $sfglobals;

	# 1: Convert smiley codes to images
	$content = sf_filter_display_smileys($content);

	# 2: Convert Chars
	$content = sf_filter_display_chars($content);

	# 3: Format the paragraphs (p and br onlt Richtext)
	if($sfglobals['editor']['sfeditor'] != RICHTEXT) {
		$content = sf_filter_save_linebreaks($content);
	} else {
		$content = sf_filter_display_paragraphs($content);
	}

	# 4: Parse post into appropriate editor format
	$content = sf_filter_edit_parser($content, $sfglobals['editor']['sfeditor']);

	# 5: Turn off shortcode processing to keep from messing up their display
	remove_all_shortcodes();

	return $content;
}

# ==================================================================
#
# TEXT - EDIT FILTERS UMBRELLA
#
# Used:	Text Areas
#		?

function sf_filter_text_edit($content)
{
	global $sfglobals;

	# 1: Convert Chars
	$content = sf_filter_display_chars($content);

	# 2: Format the paragraphs (p and br)
	$content = sf_filter_display_paragraphs($content);
	$content = sf_filter_save_linebreaks($content);

	# 3: Parse post into appropriate editor format
	$content = sf_filter_edit_parser($content, PLAIN);

	# 4: remove escape slashes
	$content = sf_filter_display_stripslashes($content);

	# finally htnl encode it for edit display
	$content = htmlentities($content, ENT_COMPAT, SFCHARSET);

	return $content;
}

function sf_filter_edit_parser($content, $editor)
{

	if($editor == BBCODE) {
		# load the bbcode to html parser
		include_once (SF_PLUGIN_DIR.'/forum/parsers/sf-htmltobb.php');
		$content = sf_Html2BBCode($content);
	} elseif($editor == HTML || $editor == PLAIN) {
		# load the raw to html parser
		include_once (SF_PLUGIN_DIR.'/forum/parsers/sf-htmltoraw.php');
		$content = sf_Html2Raw($content);
	}

	return $content;
}


# ===END OF EDIT FILTERS============================================

# ===START OF DISPLAY FILTERS=======================================
#
# CONTENT - DISPLAY FILTERS UMBRELLA
#
# Used:	Forum Post
#		Private Messages
#		Post Report
#		Template Tag
#		?

function sf_filter_content_display($content)
{
    global $current_user;

	# 1: Backwards compatible make links clickable
	$content = sf_filter_display_links($content);

	# 2: Convert smiley codes to images
	$content = sf_filter_display_smileys($content);

	# 3: Convert Chars
	$content = sf_filter_display_chars($content);

	# 4: Format the paragraphs
	$content = sf_filter_display_paragraphs($content);

	# 5: Format the code select Divs.
	$content = sf_filter_display_codeselect($content);

	# 6: Format image tags
	$content = sf_filter_display_images($content);

    # 7: strip shortcodes
    if (sf_get_option('sffiltershortcodes'))
    {
    	$content = sf_filter_display_shortcodes($content);
    }

    # 8: hide links
    if (!$current_user->sfviewlinks)
    {
        $content = sf_filter_display_hidelinks($content);
    }

	# 9: apply any users custom filters
	$content = apply_filters('sf_show_post_content', $content);

	return $content;
}

# ==================================================================
#
# TEXT - DISPLAY FILTERS UMBRELLA
#
# Used:	Profile Description
#		Blog Linking Link Text
#		Group Message
#		Forum Message
#		Email Messages
#		Signature Text
#		Announce Tag Heading/Text
#		Sneak Peak Message
#		Admin View Message
#		Custom Editor Messages
#		Registration/Privacy Messages
#		Custom Profile Message
#		Admins Off-Line Message
#		?

function sf_filter_text_display($content)
{
	# 1: Convert Chars
	$content = sf_filter_display_chars($content);

	# 2: Format the paragraphs
	$content = sf_filter_display_paragraphs($content);

	# 3: remove escape slashes
	$content = sf_filter_display_stripslashes($content);

	return $content;
}

# ==================================================================
#
# TITLE - DISPLAY FILTERS UMBRELLA
#
# Used:	Group Title/Description *
#		Forum Title/Description *
#		Topic Title *
#		Message Title *
#		Blog Linking Titles *
#		Email Subject *
#		Custom Meta Description/Keywords *
#		Topic Status Name/List *
#		Custom Icon Title *
#		UserGroup Name/Description *
#		Permission Name/Description *
#		Profile Form Labels *
#		?

function sf_filter_title_display($content)
{
	# 1: Convert Chars
	$content = sf_filter_display_chars($content);

	# 2: remove escape slashes
	$content = sf_filter_display_stripslashes($content);

	return $content;
}

# ==================================================================
#
# USER NAMES - DISPLAY FILTERS UMBRELLA
#
# Used:	Display Name
#		Guest Name
#		?

function sf_filter_name_display($content)
{
	# 1: Convert Chars
	$content = sf_filter_display_chars($content);

	# 2: remove escape slashes
	$content = sf_filter_display_stripslashes($content);

	return $content;
}

# ==================================================================
#
# EMAIL ADDRESS - DISPLAY FILTERS UMBRELLA
#
# Used:	Guest posts
#		User profile
#		?

function sf_filter_email_display($email)
{
	# 1: Convert Chars
	$email = sf_filter_display_chars($email);

	# 2: remove escape slashes
	$email = sf_filter_display_stripslashes($email);

	return $email;
}

# ==================================================================
#
# URL - DISPLAY FILTERS UMBRELLA
#
# Used: All URLs
#		?

function sf_filter_url_display($url)
{
	# 1: Clean url for display
	$url = sf_filter_display_cleanurl($url);

	return $url;
}

# ===START OF DISPLAY FILTERS=======================================

# ------------------------------------------------------------------
# sf_filter_display_links()
#
# Makes unanchored links clickable. This is here for backward
# compatibility with older storage of posts that incuded p tags
#
#	$content:		Unfiltered post content
# ------------------------------------------------------------------
function sf_filter_display_links($content)
{
	# dont make clickable in pre or code tags
	$content = sf_make_clickable($content);
	return $content;
}

# ------------------------------------------------------------------
# sf_convert_custom_smileys()
#
# Swaps codes for smileys if using custom images
#	$content:		Unfiltered post content
# ------------------------------------------------------------------
function sf_filter_display_smileys($content)
{
	global $sfglobals;

	# Custom
	if($sfglobals['smileyoptions']['sfsmallow'] && $sfglobals['smileyoptions']['sfsmtype']==1)
	{
		if($sfglobals['smileys'])
		{
			foreach ($sfglobals['smileys'] as $sname => $sinfo)
			{
				$content = str_replace($sinfo[1], '<img src="'.SFSMILEYS.$sinfo[0].'" title="'.$sname.'" alt="'.$sname.'" />', $content);
			}
		}
	}

	# WP versions
	if($sfglobals['smileyoptions']['sfsmallow'] && $sfglobals['smileyoptions']['sfsmtype']==3)
	{
		$content = convert_smilies($content);
		$content = str_replace("'", '"', $content);
	}

	return $content;
}

# ------------------------------------------------------------------
# sf_filter_display_chars()
#
# Converts specific chars to entities
#	$content:		Unfiltered post content
# ------------------------------------------------------------------
function sf_filter_display_chars($content)
{
	$content = convert_chars($content);

	# This simply replaces those odd 0003 chars we have seen
	$content = str_replace(chr(003), "'", $content);
	return $content;
}

# ------------------------------------------------------------------
# sf_filter_display_paragraphs()
#
# Breaks up into paragraphs - excluding syntax highlighted blocks
#	$content:		Unfiltered post content
# ------------------------------------------------------------------
function sf_filter_display_paragraphs($content)
{
	# check if syntax hoighlighted
	$sfsyntax = sf_get_option('sfsyntax');
    if($sfsyntax['sfsyntaxforum'] == true && strpos($content, 'brush-'))
	{
		$base = explode('<div class="sfcode">', $content);
		if($base)
		{
			$comp = array();
			foreach($base as $part)
			{
				if(substr(trim($part), 0, 18) == '<pre class="brush-')
				{
					$subparts = explode('</pre>', $part);
					$comp[] = '<div class="sfcode">' . $subparts[0] .'</pre></div>';
					$pos = strpos($subparts[1], '</div>');
					$subparts[1] = substr($subparts[1], ($pos+6));
					$comp[] = wpautop($subparts[1]);
					unset($subparts);
				} else {
					$comp[] = wpautop($part);
				}
			}
			$content = implode($comp);
		}
	} else {
		$content = wpautop($content);
	}

	$content = shortcode_unautop($content);

	return $content;
}

# ------------------------------------------------------------------
# sf_filter_display_codeselect()
#
# Adds the 'Select Code' button to code blocks
# ------------------------------------------------------------------
function sf_filter_display_codeselect($content)
{
	# add the 'select code' button
	$pos = strpos($content, '<div class="sfcode">');
	if($pos === false) return $content;

	# check if syntax hoighlighted
	$sfsyntax = sf_get_option('sfsyntax');
    if($sfsyntax['sfsyntaxforum'] == true && strpos($content, 'brush-'))
    {
    	return $content;
	}

	while($pos !== false)
	{
		$id = rand(100, 10000);
		$selector = '#sfcode'.$id;
		$replace = '<p><input type="button" class="sfcodeselect" name="sfselectit'.$id.'" value="'.__("Select Code", "sforum").'" onclick="sfjSelectCode(\'sfcode'.$id.'\');" /></p><div class="sfcode" id="sfcode'.$id.'">';
		$content = substr_replace ($content, $replace, $pos, 20);
		$pos = $pos + 140;
		$pos = strpos($content, '<div class="sfcode">', $pos);
	}
	return $content;
}

# ------------------------------------------------------------------
# sf_filter_display_images() and support functions
#
# Change large images to small thumbnails and embed
#	$content:		Unfiltered post content
# ------------------------------------------------------------------
function sf_filter_display_images($content)
{
	return sf_swap_IMGs($content);
}

function sf_swap_IMGs($content)
{
	$content = preg_replace_callback('/<img[^>]*>/', 'sf_check_width' , $content);
	return $content;
}

function sf_check_width($match)
{
	global $SFPATHS;

    $out = '';

	$sfimage = array();
	$sfimage = sf_get_option('sfimage');

	# is any of this needed?
	if ($sfimage['enlarge']==false && $sfimage['process']==false) return $match[0];

	$thumb = $sfimage['thumbsize'];
	if ((empty($thumb)) || ($thumb < 100)) $thumb=100;

	preg_match('/title\s*=\s*"([^"]*)"|title\s*=\s*\'([^\']*)\'/i', $match[0], $title);
	preg_match('/alt\s*=\s*"([^"]*)"|alt\s*=\s*\'([^\']*)\'/i', $match[0], $alt);
	preg_match('/width\s*=\s*"([^"]*)"|width\s*=\s*\'([^\']*)\'/i', $match[0], $width);
	preg_match('/src\s*=\s*"([^"]*)"|src\s*=\s*\'([^\']*)\'/i', $match[0], $src);
	preg_match('/style\s*=\s*"([^"]*)"|style\s*=\s*\'([^\']*)\'/i', $match[0], $style);
	preg_match('/class\s*=\s*"([^"]*)"|class\s*=\s*\'([^\']*)\'/i', $match[0], $class);

	if (isset($class[1])) return $match[0];

	if ((strpos($src[1], 'plugins/emotions')) || (strpos($src[1], 'images/smilies')) || (strpos($src[1], $SFPATHS['smileys'])))
	{
		$out = str_replace('img src', 'img class="sfsmiley" src', $match[0]);
		return $out;
	}

	# see if we can determoine if the image still exists before going further. So is it relative?
	if (empty($width[1]) && substr($src[1], 0, 7) == 'http://')
	{
		if(function_exists('curl_init'))
		{
			$fcheck = curl_init();
			curl_setopt($fcheck, CURLOPT_URL,$src[1]);
			curl_setopt($fcheck, CURLOPT_NOBODY, 1);
			curl_setopt($fcheck, CURLOPT_FAILONERROR, 1);
			curl_setopt($fcheck, CURLOPT_RETURNTRANSFER, 1);
			if(curl_exec($fcheck)===false)
			{
				return '['.__('Image Can Not Be Found', 'sforum').']';
			}
		}
	}

	if (empty($style[1]))
	{
		if ($sfimage['style'] == 'left' || $sfimage['style']=='right')
		{
			$style[1] = 'float: '.$sfimage['style'];
		} else {
			$style[1] = 'vertical-align: '.$sfimage['style'];
		}
	}

	$iclass='';
	$mclass="sfmouseother";

	switch ($style[1])
	{
		case 'float: left':
			$iclass="sfimageleft";
			$mclass="sfmouseleft";
			break;
		case 'float: right':
			$iclass="sfimageright";
			$mclass="sfmouseright";
			break;
		case 'vertical-align: baseline':
			$iclass="sfimagebaseline";
			break;
		case 'vertical-align: top':
			$iclass="sfimagetop";
			break;
		case 'vertical-align: middle':
			$iclass="sfimagemiddle";
			break;
		case 'vertical-align: bottom':
			$iclass="sfimagebottom";
			break;
		case 'vertical-align: text-top':
			$iclass="sfimagetexttop";
			break;
		case 'vertical-align: text-bottom':
			$iclass="sfimagetextbottom";
			break;
	}

	# figure out whether its relative path (same server) or a url
    $parsed = parse_url($src[1]);
    if (array_key_exists('scheme', $parsed))
    {
    	$srcfile = $src[1];  # url, so leave it alone
    } else {
  		$srcfile = $_SERVER['DOCUMENT_ROOT'].$src[1];  # relative path, so add DOCUMENT_ROOT to path
  	}
/*
	global $gis_error;
	$gis_error = '';
	set_error_handler('sf_gis_error');

	if (empty($width[1]))
	{
		$size = getimagesize($srcfile);
		restore_error_handler();
		if ($gis_error == '')
		{
			if ($size[0])
			{
				$width[1] = $size[0];
			} else {
				return '['.__('Image Can Not Be Found', 'sforum').']';
			}
		}
	}
*/
	if (isset($src[1])) $thissrc = 'src="'.$src[1].'" '; else $thissrc = '';
	if (isset($title[1])) $thistitle = 'title="'.$title[1].'" '; else $thistitle = '';
	if (isset($alt[1])) $thisalt = 'alt="'.$alt[1].'" '; else $thisalt = '';

	if ((int) $width[1] > (int)$thumb) { # is width > thumb size
		$thiswidth = 'width="'.$thumb.'" ';
		$anchor = true;
	} else if (!empty($width)) { # width is smaller than thumb, so use the width
		$thiswidth = 'width="'.$width[1].'" ';
		$mclass = '';
		$anchor = false;
	} else { # couldnt determine width, so dont output it
//		$thiswidth = '';
//		$mclass = '';
		$thiswidth = 'width="'.$thumb.'" ';
//		$anchor = false;
		$anchor = true;
	}

	if (!empty($iclass))
	{
		$thisformat = 'class="'.$iclass.'" ';
	} else {
		$thisformat = 'style="'.$style[1].'" ';
	}

	if ($anchor)
	{
		# Use highslide popup
		if($sfimage['enlarge'] == true)
		{
			$out = '<a onclick="return hs.expand(this)" class="highslide" href="'.$src[1].'" '.$thistitle.'>';
		} else {
			$out = '<a href="'.$src[1].'" '.$thistitle.'>';
		}
	}

	$out.= '<img '.$thissrc.$thiswidth.$thisformat.$thistitle.$thisalt.'/>';
	if ($gis_error) $out.= '<br /><span class="sfimgerror small">['.__("Image Error", "sforum").': '.$gis_error.']</span>';

	if ($mclass)
	{
		$out.= '<img src="'.SFRESOURCES.'mouse.png" class="'.$iclass.' '.$mclass.'" alt="" />';
	}

	if ($anchor)
	{
		$out.= '</a>';
	}

	return $out;
}

# ------------------------------------------------------------------
# sf_filter_display_stripslashes()
#
# Remov escaped slashes
#	$content:		Unfiltered post content
# ------------------------------------------------------------------
function sf_filter_display_stripslashes($content)
{
	$content = stripslashes($content);

	return $content;
}

# ------------------------------------------------------------------
# sf_filter_display_cleanurl()
#
# Cleans up url for display
#	$url:		Unfiltered url
# ------------------------------------------------------------------
function sf_filter_display_cleanurl($url)
{
	$url = esc_url($url);

	return $url;
}

# ------------------------------------------------------------------
# sf_filter_display_shortcodes()
#
# Removes non allowed shortcodes
# ------------------------------------------------------------------
function sf_filter_display_shortcodes($content)
{
	global $shortcode_tags;

	# Backup current registered shortcodes
	$orig_shortcode_tags = $shortcode_tags;

	$allowed_shortcodes = explode("\n", stripslashes(sf_get_option('sfshortcodes')));
    if ($allowed_shortcodes)
    {
        foreach ($allowed_shortcodes as $tag)
        {
            If (array_key_exists($tag, $orig_shortcode_tags)) unset($shortcode_tags[$tag]);
        }
    }

    # be sure to allow our spoilers
    unset($shortcode_tags['spoiler']);

    # strip all but allowed shortcodes
    $content = strip_shortcodes($content);

	# Restore registered shortcodes
	$shortcode_tags = $orig_shortcode_tags;

	return $content;
}

# ------------------------------------------------------------------
# sf_filter_display_hidelinks()
#
# Option: Removes links from post content
# ------------------------------------------------------------------
function sf_filter_display_hidelinks($content)
{
	$sffilters = sf_get_option('sffilters');
    $string = sf_filter_save_nohtml($sffilters['sfnolinksmsg']);
    $content = preg_replace("#(<a.*>).*(</a>)#", $string, $content);

	return $content;
}


# ==================================================================
#
# SPECIAL FILTERS - ONE OFFS
#
# The following filters are specific to one task - usually display

# ------------------------------------------------------------------
# sf_filter_signature_display() and support function
#
# Filters the display of signature images
#	$content:		Unfiltered signature content
# ------------------------------------------------------------------
function sf_filter_signature_display($content)
{
	$sfsigimagesize = sf_get_option('sfsigimagesize');
	if ($sfsigimagesize['sfsigwidth'] > 0 || $sfsigimagesize['sfsigheight'] > 0)
    {
        $content = preg_replace_callback('/<img[^>]*>/', 'sf_check_sig' , $content);
    }
	return $content;
}

function sf_check_sig($match)
{
	$sfsigimagesize = sf_get_option('sfsigimagesize');

    # get the elements of the img tags
	preg_match('/title\s*=\s*"([^"]*)"|title\s*=\s*\'([^\']*)\'/i', $match[0], $title);
	preg_match('/width\s*=\s*"([^"]*)"|width\s*=\s*\'([^\']*)\'/i', $match[0], $width);
	preg_match('/height\s*=\s*"([^"]*)"|height\s*=\s*\'([^\']*)\'/i', $match[0], $height);
	preg_match('/src\s*=\s*"([^"]*)"|src\s*=\s*\'([^\']*)\'/i', $match[0], $src);
	preg_match('/style\s*=\s*"([^"]*)"|style\s*=\s*\'([^\']*)\'/i', $match[0], $style);
	preg_match('/alt\s*=\s*"([^"]*)"|alt\s*=\s*\'([^\']*)\'/i', $match[0], $alt);

    # check for possible single quote match or double quote
    if (empty($title[1])  && !empty($title[2]))  $title[1]  = $title[2] ;
    if (empty($width[1])  && !empty($width[2]))  $width[1]  = $width[2] ;
    if (empty($height[1]) && !empty($height[2])) $height[1] = $height[2] ;
    if (empty($src[1])    && !empty($src[2]))    $src[1]    = $src[2] ;
    if (empty($style[1])  && !empty($style[2]))  $style[1]  = $style[2] ;
    if (empty($alt[1])    && !empty($alt[2]))    $alt[1]    = $alt[2] ;

    # if user defined heights are valid, just return
	if ((!isset($width[1]) || $width[1] <= $sfsigimagesize['sfsigwidth']) &&
        (!isset($height[1]) || $height[1] <= $sfsigimagesize['sfsigheight']))
    {
        return $match[0];
    }

    # insepct the image itself
	global $gis_error;
	$gis_error = '';
	set_error_handler('sf_gis_error');

    $display_width = '';
    $display_height = '';
	$size = getimagesize($src[1]);
	restore_error_handler();
	if ($gis_error == '')
	{
        # Did image exist?
		if ($size[0] && $size[1])
		{
            # check width
        	if (isset($width[1]) && ($width[1] <= $sfsigimagesize['sfsigwidth'] || $sfsigimagesize['sfsigwidth'] == 0)) # width specified and less than max allowed
            {
                $display_width = ' width="'.$width[1].'"';
            } else if ($sfsigimagesize['sfsigwidth'] > 0 && $size[0] > $sfsigimagesize['sfsigwidth']) {
                $display_width = ' width="'.$sfsigimagesize['sfsigwidth'].'"';
            }

            # check the height
        	if (isset($height[1]) && ($height[1] <= $sfsigimagesize['sfsigheight'] || $sfsigimagesize['sfsigheight'] == 0)) # height specified and less than max allowed
            {
                $display_height = ' height="'.$height[1].'"';
            } else if ($sfsigimagesize['sfsigheight'] > 0 && $size[1] > $sfsigimagesize['sfsigheight']) {
                $display_height = ' height="'.$sfsigimagesize['sfsigheight'].'"';
            }
		} else {
            # image not found, strip tags
			return '';
		}
	} else {
        # problem checking sizes, so just limit
        $display_width = ' width="'.$sfsigimagesize['sfsigwidth'].'"';
        $display_height = ' height="'.$sfsigimagesize['sfsigheight'].'"';
	}

	#
	return '<img src="'.$src[1].'"'.$display_width.$display_height.' style="'.$style[1].'" title="'.$title[1].'" alt="'.$alt[1].'" />';
}

# ------------------------------------------------------------------
# sf_filter_tooltip_display()
#
# Filters the display of topic linked post 'tooltips'
#	$content:		Unfiltered post content
#	$status:		False if post awaiting moderation
# ------------------------------------------------------------------
function sf_filter_tooltip_display($content, $status)
{
	global $current_user;

	# can the current user view this post?
	if($current_user->moderator == false && $status == 1)
	{
		$content = __("Post Awaiting Approval by Forum Administrator", "sforum");
	} else {
		$content = addslashes($content);
		$content = sf_filter_save_nohtml($content);
        # remove shortcodes to prevent messing up tooltip
        $content = strip_shortcodes($content);
		if(strlen($content) > 320)
		{
			$pos = strpos($content, ' ', 300);
			$content = substr($content, 0, $pos).'...';
		}
		$content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
		$content = str_replace('&amp;', '&', $content);

		# link urls to prevent oembed problems
		$content = esc_attr(sf_make_clickable($content));
	}

	return $content;
}

# ------------------------------------------------------------------
# sf_filter_rss_display()
#
# Filters the display of post content in rss feed
#	$content:		Unfiltered post content
# ------------------------------------------------------------------
# Used:	RSS Feeds

function sf_filter_rss_display($content)
{
    global $current_user;

	# 1: Backwards compatible make links clickable
	$content = sf_filter_display_links($content);

	# 2: Convert smiley codes to images
	$content = sf_filter_display_smileys($content);

	# 3: Convert Chars
	$content = sf_filter_display_chars($content);

	# 4: Format the paragraphs
	$content = sf_filter_display_paragraphs($content);

    # 5: strip shortcodes
    if (sf_get_option('sffiltershortcodes'))
    {
    	$content = sf_filter_display_shortcodes($content);
    }

    # 6: hide links
    if (!$current_user->sfviewlinks)
    {
        $content = sf_filter_display_hidelinks($content);
    }

	# 7: apply any users custom filters
	$content = apply_filters('sf_show_post_content', $content);

	return $content;
}

# ------------------------------------------------------------------
# sf_filter_table_prefix()
#
# Filters the prefix from table names
#	$content:		Unfiltered content
# ------------------------------------------------------------------
# Used as a filter in seach values - aids killing SQL injections
function sf_filter_table_prefix($content)
{
$long = array(
	SF_PREFIX.'commentmeta', SF_PREFIX.'comments', SF_PREFIX.'links', SF_PREFIX.'options', SF_PREFIX.'postmeta', SF_PREFIX.'posts',
	SF_PREFIX.'terms', SF_PREFIX.'term_taxonomy', SF_PREFIX.'term_relationships', SF_PREFIX.'users', SF_PREFIX.'usermeta',
	SF_PREFIX.'sfgroups', SF_PREFIX.'sfforums', SF_PREFIX.'sftopics', SF_PREFIX.'sfposts', SF_PREFIX.'sfmessages', SF_PREFIX.'sfwaiting',
	SF_PREFIX.'sftrack', SF_PREFIX.'sfsettings', SF_PREFIX.'sfnotice', SF_PREFIX.'sfusergroups', SF_PREFIX.'sfpermissions',
	SF_PREFIX.'sfdefpermissions', SF_PREFIX.'sfroles', SF_PREFIX.'sfmembers', SF_PREFIX.'sfmemberships', SF_PREFIX.'sfmeta',
	SF_PREFIX.'sfpostratings', SF_PREFIX.'sftags', SF_PREFIX.'sftagmeta', SF_PREFIX.'sflog', SF_PREFIX.'sfoptions', SF_PREFIX.'sflinks');

$short = array(
	'commentmeta', 'comments', 'links', 'options', 'postmeta', 'posts', 'terms', 'term_taxonomy', 'term_relationships', 'users', 'usermeta',
	'sfgroups', 'sfforums', 'sftopics', 'sfposts', 'sfmessages', 'sfwaiting', 'sftrack', 'sfsettings', 'sfnotice', 'sfusergroups',
	'sfpermissions', 'sfdefpermissions', 'sfroles', 'sfmembers', 'sfmemberships', 'sfmeta', 'sfpostratings', 'sftags',
	'sftagmeta', 'sflog', 'sfoptions', 'sflinks');

	return str_replace($long, $short, $content);
}

# ==================================================================
#
# CONTENT - PAGE LEVEL DISPLAY FILTERS
#
# The following filters and shortcodes are run at 'page' level after
# the forum page has been generated.

# ------------------------------------------------------------------
# sf_filter_display_spoiler()
#
# Converts the spoiler shortcode to the drop down spoiler div
# ------------------------------------------------------------------
function sf_filter_display_spoiler($atts, $content)
{
	global $spoilerID, $sfvars;

	if(!isset($spoilerID))
	{
		$spoilerID=1;
	} else {
		$spoilerID++;
	}

	$out = '';
	$out.= '<div class="sfspoiler">';

	$out.= '<div class="sfreveal">';
	$out.= '<a href="javascript:void(0);" onclick="sfjtoggleLayer(\'sfspoilercontent'.$spoilerID.'\');">'.__("Reveal Spoiler", "sforum").'</a>';
	$out.= '</div>';
	$out.= '<div class="sfspoilercontent" id="sfspoilercontent'.$spoilerID.'">';
	$out.= '<p>'.$content.'</p>';
	$out.= '</div></div>';

	return $out;
}

# ------------------------------------------------------------------
# sf_filter_syntax_display()
#
# Syntax Highlighting display - page level filter
#	$content:		Unfiltered post content
# ------------------------------------------------------------------
function sf_filter_syntax_display($content)
{
	$result = preg_replace_callback('/<pre(.*?)>(.*?)<\/pre>/imsu', 'sf_syntax_htmlentities', $content);
	return $result;
}

function sf_syntax_htmlentities ($match) {
	$attrs = $match[1];

	if (preg_match("/escaped/", $attrs)) {
		$code = $match[2];
	} else {
		$code = htmlentities($match[2]);
	}

	return "<pre$attrs>$code</pre>";
}

# ------------------------------------------------------------------
# sf_wptexturize()
#
# take control of wptexturize to stop it doing really nasty things
# to quotes.
#	$content:		Unfiltered post content
# This code is part of the "jQuery.Syntax" project, and is licensed
# under the GNU AGPLv3. See <jquery.syntax.js> for licensing details.
# Copyright 2010 Samuel Williams. All rights reserved.
# ------------------------------------------------------------------
function sf_wptexturize($content)
{
	static $static_setup = false, $default_no_texturize_tags, $default_no_texturize_shortcodes, $static_characters, $static_replacements;
	$output = '';
	$curl = '';
	$textarr = preg_split('/(<.*>|\[.*\])/Us', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
	$stop = count($textarr);

	# No need to setup these variables more than once
	if (!$static_setup)
	{
		$default_no_texturize_tags = array('pre', 'code', 'kbd', 'style', 'script', 'tt');
		$default_no_texturize_shortcodes = array('code');

		$static_characters = array('---', ' -- ', '--', ' - ', 'xn&#8211;', '...', ' (tm)');
		$static_replacements = array('&#8212;', ' &#8212; ', '&#8211;', ' &#8211; ', 'xn--', '&#8230;', ' &#8482;');

		$static_setup = true;
	}

	# Transform into regexp sub-expression used in _wptexturize_pushpop_element
	# Must do this everytime in case plugins use these filters in a context sensitive manner
	$no_texturize_tags = '(' . implode('|', apply_filters('no_texturize_tags', $default_no_texturize_tags) ) . ')';
	$no_texturize_shortcodes = '(' . implode('|', apply_filters('no_texturize_shortcodes', $default_no_texturize_shortcodes) ) . ')';

	$no_texturize_tags_stack = array();
	$no_texturize_shortcodes_stack = array();

	for ( $i = 0; $i < $stop; $i++ )
	{
		$curl = $textarr[$i];

		if ( !empty($curl) && '<' != $curl{0} && '[' != $curl{0} && empty($no_texturize_shortcodes_stack) && empty($no_texturize_tags_stack))
		{
			# This is not a tag, nor is the texturization disabled static strings
			$curl = str_replace($static_characters, $static_replacements, $curl);
			# regular expressions
		} elseif (!empty($curl)) {
			# Only call _wptexturize_pushpop_element if first char is correct tag opening
			if ('<' == $curl{0})
				_wptexturize_pushpop_element($curl, $no_texturize_tags_stack, $no_texturize_tags, '<', '>');
			elseif ('[' == $curl{0})
				_wptexturize_pushpop_element($curl, $no_texturize_shortcodes_stack, $no_texturize_shortcodes, '[', ']');
		}

		$curl = preg_replace('/&([^#])(?![a-zA-Z1-4]{1,8};)/', '&#038;$1', $curl);
		$output .= $curl;
	}
	return $output;
}

# ------------------------------------------------------------------
# esc_regex()
#
# escape regular expression matching strings so they can contain
# regex special chars
#	$str:		string to have regex special chars escaped
# ------------------------------------------------------------------
function esc_regex($str)
{
    $patterns = array('/\//', '/\^/', '/\./', '/\$/', '/\|/', '/\(/', '/\)/', '/\[/', '/\]/', '/\*/', '/\+/', '/\?/', '/\{/', '/\}/', '/\,/');
    $replace = array('\/', '\^', '\.', '\$', '\|', '\(', '\)', '\[', '\]', '\*', '\+', '\?', '\{', '\}', '\,');
    return esc_sql(preg_replace($patterns, $replace, $str));
}

# ------------------------------------------------------------------
# sf_make_clickable()
#
# make links clickable except in pre tags
#	$content:		string to make links clickable
# ------------------------------------------------------------------
function sf_make_clickable($content)
{
	# dont make clickable in pre tags
	$segments = preg_split('/(<\/?pre)/', $content, -1, PREG_SPLIT_DELIM_CAPTURE);

	# $depth = how many nested pres we're inside of
	$depth = 0;
	foreach ($segments as $index => $segment) {
		$segment = & $segments[$index];
	    if ($depth == 0 && $segment != '<pre')
	        $segment = make_clickable($segment);
	    else if ($segment == '<pre')
	        $depth++;
	    else if ($depth > 0 && $segment == '</pre')
	        $depth--;
		unset($segment);
	}
	$content = implode($segments);

	return $content;
}
?>