<?php
/*
Simple:Press
Base functions
$LastChangedDate: 2011-04-26 05:08:45 -0700 (Tue, 26 Apr 2011) $
$Rev: 5980 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# = MEMBERS TABLE HANDLERS ====================

# ------------------------------------------------------------------
# sf_get_member_row()
#
# returns the members table content for specified user.
# NOTE: This us returned as an array - columns that require ot are
# NOT unserialized.
#	$userid:		User to lookup
# ------------------------------------------------------------------
function sf_get_member_row($userid)
{
	global $wpdb;

	$member = $wpdb->get_row("SELECT * FROM ".SFMEMBERS." WHERE user_id = $userid", ARRAY_A);
	return $member;
}

# ------------------------------------------------------------------
# sf_get_member_list()
#
# returns specified columns from members table for specified user.
# NOTE: This us returned as an array - columns that require it are
# returned unserialized.
#	$userid:		User to lookup
#	$itemlist:		comma-space delimited list of columns
# ------------------------------------------------------------------
function sf_get_member_list($userid, $itemlist)
{
	global $wpdb;

	$member = $wpdb->get_row("SELECT $itemlist FROM ".SFMEMBERS." WHERE user_id = $userid", ARRAY_A);
	if (isset($member['buddies'])) $member['buddies'] = unserialize($member['buddies']);
	if (isset($member['newposts'])) $member['newposts'] = unserialize($member['newposts']);
	if (isset($member['subscribe'])) $member['subscribe'] = unserialize($member['subscribe']);
	if (isset($member['watches'])) $member['watches'] = unserialize($member['watches']);
	if (isset($member['posts_rated'])) $member['posts_rated'] = unserialize($member['posts_rated']);
	if (isset($member['admin_options'])) $member['admin_options'] = unserialize($member['admin_options']);
	if (isset($member['user_options'])) $member['user_options'] = unserialize($member['user_options']);
	if (isset($member['avatar'])) $member['avatar'] = unserialize($member['avatar']);

	return $member;
}

# ------------------------------------------------------------------
# sf_get_member_item()
#
# returns a specified column from members table for specified user.
# NOTE: This us returned as an var - columns that require it are
# returned unserialized.
#	$userid:		User to lookup
#	$item:			column name
# ------------------------------------------------------------------
function sf_get_member_item($userid, $item)
{
	global $wpdb;

	$thisitem = $wpdb->get_var("SELECT $item FROM ".SFMEMBERS." WHERE user_id = $userid");
	if($item == 'buddies') $thisitem = unserialize($thisitem);
	if($item == 'newposts') $thisitem = unserialize($thisitem);
	if($item == 'subscribe') $thisitem = unserialize($thisitem);
	if($item == 'watches') $thisitem = unserialize($thisitem);
	if($item == 'posts_rated') $thisitem = unserialize($thisitem);
	if($item == 'admin_options') $thisitem = unserialize($thisitem);
	if($item == 'user_options') $thisitem = unserialize($thisitem);
	if($item == 'avatar') $thisitem = unserialize($thisitem);

	return $thisitem;
}

# ------------------------------------------------------------------
# sf_update_member_item()
#
# updates a specified column from members table for specified user.
# NOTE: Data requiring serialization must be passed as an array
# 'checktime' and 'lastvisit' are set to now() by the update code
#	$userid:		User to lookup
#	$itemname:		column name
#	$itemdata:		singe var or array
# ------------------------------------------------------------------
function sf_update_member_item($userid, $itemname, $itemdata)
{
	global $wpdb, $current_user, $sfglobals;

	# hive off for cache updating if current user
	if ($current_user->ID == $userid)
	{
		$thisitem = $itemdata;
	}

	# those items requiring serialisation
	if ($itemname == 'buddies' ||
		$itemname == 'newposts' ||
		$itemname == 'subscribe' ||
		$itemname == 'avatar' ||
		$itemname == 'watches' ||
		$itemname == 'posts_rated' ||
		$itemname == 'user_options')
	{
		# serialize data if not already serialized
		$itemdata="'".maybe_serialize($itemdata)."'";
	}

	# those string based items requiring to be quoted for the SQL update
	if ($itemname == 'display_name' ||
		$itemname == 'signature')
	{
		$itemdata = "'".$itemdata."'";
	}

	# set 'lastvisit' to cached checktime if user is current user
	if($itemname == 'lastvisit')
	{
		$check = sf_get_member_item($userid, 'checktime');
		if(empty($check))
		{
			$itemdata="'" . current_time('mysql') . "'";
		} else {
			$itemdata= "'".$check."'";
		}
		if ($current_user->ID == $userid)
		{
			$thisitem = $itemdata;
		}
	}

	# set 'checktime' to 'now'
	if($itemname == 'checktime')
	{
		if ($current_user->ID == $userid)
		{
			$itemdata = "'" . current_time('mysql') . "'";
			$thisitem = "'" . current_time('mysql') . "'";
		} else {
			$itemdata= "'" . current_time('mysql') . "'";
		}
	}

	# save differently if admin options - temporary fix
	if($itemname == 'admin_options')
	{
		$itemdata = maybe_serialize($itemdata);
		$wpdb->update(SFMEMBERS, array($itemname => $itemdata), array('user_id' => $userid));
	} else {
		$sql = "UPDATE ".SFMEMBERS." SET $itemname = $itemdata WHERE user_id=".$userid;
		$updateditem = $wpdb->query($sql);
	}

	# make sure member data cache is updated if this update is for the current user
	if ($current_user->ID == $userid)
	{
		$sfglobals['member'][$itemname] = $thisitem;
	}

	return $updateditem;
}

# = NOTICE TABLE HANDLERS =====================
function get_sfnotice($item)
{
	global $wpdb;

	$id=$_SERVER['REMOTE_ADDR'];
	$message = $wpdb->get_var("SELECT message FROM ".SFNOTICE." WHERE id='$id' AND item='$item'");
	return $message;
}

function update_sfnotice($item, $message)
{
	global $wpdb;

	$message = esc_sql($message);
	$id=$_SERVER['REMOTE_ADDR'];

	# we need to deete if already existing entry
	delete_sfnotice();
	$wpdb->query("INSERT INTO ".SFNOTICE." (id, item, message, ndate) VALUES ('$id', '$item', '$message', now())");
	$wpdb->flush();

	return;
}

function delete_sfnotice()
{
	global $wpdb;

	$id=$_SERVER['REMOTE_ADDR'];
	$wpdb->query("DELETE FROM ".SFNOTICE." WHERE id='$id'");
	$wpdb->flush();
	return;
}

# = SETTINGS TABLE HANDLERS ===================
function get_sfsetting($setting)
{
	global $wpdb;

	$value = $wpdb->get_var("SELECT setting_value FROM ".SFSETTINGS." WHERE setting_name = '$setting'");
	if(empty($value))
	{
		return -1;
	} else {
		return $value;
	}
}

function add_sfsetting($setting_name, $setting_value = '')
{
	global $wpdb;

	$check = get_sfsetting($setting_name);
	if($check == -1)
	{
		$setting_name = esc_sql($setting_name);
		$setting_value = esc_sql($setting_value);
		$wpdb->query("INSERT INTO ".SFSETTINGS." (setting_name, setting_value, setting_date) VALUES ('$setting_name', '$setting_value', now())");
		$wpdb->flush();
	} else {
		update_sfsetting($setting_name, $setting_value);
	}
	return;
}

function update_sfsetting($setting_name, $setting_value)
{
	global $wpdb;

	if (is_string($setting_value)) $setting_value = trim($setting_value);

	# If the new and old values are the same, no need to update.
	$oldvalue = get_sfsetting($setting_name);
	if ($setting_value == $oldvalue)
	{
		return false;
	}

	if (($oldvalue == -1) || (empty($oldvalue)))
	{
		add_sfsetting($setting_name, $setting_value);
		return true;
	}

	$setting_value = esc_sql($setting_value);
	$setting_name = esc_sql($setting_name);
	$wpdb->query("UPDATE ".SFSETTINGS." SET setting_value = '$setting_value', setting_date = now() WHERE setting_name = '$setting_name'");
	if($wpdb->rows_affected == 1)
	{
		return true;
	}
	return false;
}

function delete_sfsetting($setting_name)
{
	global $wpdb;
	# Get the ID, if no ID then return
	$setting_id = $wpdb->get_var("SELECT setting_id FROM ".SFSETTINGS." WHERE setting_name = '$setting_name'");
	if (!$setting_id) return false;
	$wpdb->query("DELETE FROM ".SFSETTINGS." WHERE setting_name = '$setting_name'");
	return true;
}

# ------------------------------------------------------------------
# sf_clean_controls()
#
# Cleans up sfsettings, sfnotice and sfcontrols in options
# ------------------------------------------------------------------
function  sf_clean_controls()
{
	global $wpdb;

	delete_sfsetting($_SERVER['REMOTE_ADDR'].'login');

	$controls = sf_get_option('sfcontrols');

	# clean 404 flag
	$controls['fourofour'] = false;

	# do this just once a day
	if(date('j') != $controls['dayflag'])
	{
		$wpdb->query("DELETE FROM ".SFSETTINGS." WHERE setting_date < DATE_SUB(CURDATE(), INTERVAL 24 HOUR);");
		$wpdb->query("DELETE FROM ".SFNOTICE." WHERE ndate < DATE_SUB(CURDATE(), INTERVAL 24 HOUR);");
		$controls['dayflag'] = date('j');
	}
	sf_update_option('sfcontrols', $controls);

	return;
}

# = META TABLE HANDLERS ====================

# ------------------------------------------------------------------
# sf_add_sfmeta()
#
# Adds a new record to the sfmeta table
#	$type:		The type of the meta record
#	$key:		The unique key name
#	$value:		value - array expected and will serialize
# ------------------------------------------------------------------
function sf_add_sfmeta($type, $key, $value)
{
	global $wpdb;

	if(empty($type) || empty($key) || empty($value)) return false;

	# Check if already exists
	$sql = 	"SELECT meta_id FROM ".SFMETA.
			" WHERE meta_type='".$type."' AND meta_key='".$key."'";

	$check = $wpdb->get_var($sql);

	# so - does it?
	if($check)
	{
		# yes - so needs to be an update call
		sf_update_sfmeta($type, $key, $value, $check);
	} else {
		$sql =  "INSERT INTO ".SFMETA.
				"(meta_type, meta_key, meta_value)
				VALUES
				('".$type."', '".$key."', '".$value."')";
		$wpdb->query($sql);
	}
	return;
}

# ------------------------------------------------------------------
# sf_update_sfmeta()
#
# Updates a record in the sfmeta table
#	$type:		The type of the meta record
#	$key:		The unique key name
#	$value:		value - array expected and will serialize
#	$id:		The meta records ID
# ------------------------------------------------------------------
function sf_update_sfmeta($type, $key, $value, $id)
{
	global $wpdb;

	$sql =	"UPDATE ".SFMETA." SET
			 meta_type='".$type."',
			 meta_key='".$key."',
			 meta_value='".$value."'
			 WHERE meta_id=".$id;

	if($wpdb->query($sql))
	{
		return true;
	} else {
		return false;
	}
}

# ------------------------------------------------------------------
# sf_get_sfmeta()
#
# Gets a record(s) from the sfmeta table
#	$type:		The type of the meta record
#	$key:		The unique key name - can be false to get all of type
#	$id:		If set then returns by id (one row regardless of $key)
# ------------------------------------------------------------------
function sf_get_sfmeta($type, $key=false, $id=0)
{
	global $wpdb;

	$WHERE = " meta_type='".$type."'";

	if($id != 0)
	{
		$WHERE .= " AND meta_id=".$id;
	} else {
		if($key)
		{
			$WHERE .= " AND meta_key='".$key."'";
		}
	}

	$sql =  "SELECT * FROM ".SFMETA.
			" WHERE ".$WHERE.
			" ORDER BY meta_id";

	$records = $wpdb->get_results($sql, ARRAY_A);
	return $records;
}

# ------------------------------------------------------------------
# sf_delete_sfmeta()
#
# Deletes a record in the sfmeta table
#	$id:		The meta records ID
# ------------------------------------------------------------------
function sf_delete_sfmeta($id)
{
	global $wpdb;

	$sql = 	"DELETE FROM ".SFMETA.
			" WHERE meta_id=".$id;

	$wpdb->query($sql);
	return;
}

# = SEARCH STRING HANDLERS ====================
function sf_deconstruct_search_parameter($term, $type)
{
	global $sfvars;

	if($type == 6)
	{
		$newterm = __("Topic Status", "sforum").': '.sf_get_topic_status_from_forum($sfvars['forumid'], $term);
	} elseif ($type == 8 || $type == 9)
	{
		$newterm = sf_deconstruct_search_for_display($term, $type);
	} elseif ($sfvars['searchinclude'] == 4)
	{
		$newterm = __("Tag", "sforum").': '.str_replace('%', ' ', $term);
	} else {
		$newterm = str_replace('%', ' ', $term);

	}
	return $newterm;
}

function sf_deconstruct_search_for_display($term, $type)
{
	global $wpdb;

	if ($type == 8 || $type == 9)
	{
		$name = sf_filter_name_display(sf_get_member_item($term, 'display_name'));

		if($type == 8)
		{
			$newterm = __("Topics in which", "sforum").' '.$name.' '.__("has posted", "sforum");
		} else {
			$newterm = __("Topics started by", "sforum").' '.$name;
		}
	} else {
		$newterm = sf_deconstruct_search_parameter($term, $type);
	}
	return $newterm;
}

function sf_construct_search_term($term, $type)
{
	# get the search terms(s)
	$term = sf_deconstruct_search_parameter($term, $type);

	switch($type)
	{
		case 1:
			$searchterm = $term;
			break;

		case 2:
			$term = str_replace(' ', ' +', $term);
			$searchterm.= '+'.$term;
			break;

		case 3:
			$searchterm = '"'.$term.'"';
			break;
	}
	return $searchterm;
}

# = RSS DATA FILTER ===========================
function sf_rss_filter($text)
{
  echo convert_chars(ent2ncr($text));
}

function sf_rss_excerpt($text)
{
    $rssopt = sf_get_option('sfrss');
	$max = $rssopt['sfrsswords'];
	if ($max == 0) return $text;
	$bits = explode(" ", $text);
	$text = '';
	$end = '';
	if (count($bits) < $max)
	{
		$max = count($bits);
	} else {
		$end = '...';
	}
	$text = "";
	for ($x=0; $x<$max; $x++)
	{
		$text.= $bits[$x].' ';
	}
	return $text.$end;
}

# = GENERAL TOP MESSAGE DISPLAY ===============
function sf_message($message)
{
	$comp = explode('@', $message);
	if(count($comp) > 1)
	{
		$mtype = $comp[0];
		if($mtype == 1)
		{
			$icon = '<img class="sficon" src="'. SFRESOURCES .'failure.png" alt="" />';
			$class= "sfmessagefail";
			$message = $comp[1];
		} else if ($mtype == 0)
		{
			$icon = '<img class="sficon" src="'. SFRESOURCES .'success.png" alt="" />';
			$class= "sfmessage";
			$message = $comp[1];
		}
	} else {
			$icon = '';
			$class= "sfmessage";
	}

	$out = '<div id="sfcomm" class="'.$class.'">' . $icon . $message . '</div>'."\n";

	# inline script to display main message at top of screen
	add_action( 'wp_footer', 'sfjs_display_main_message' );

	return $out;
}

# inline function to dislay message
function sfjs_display_main_message() {
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	sfjmDisplay();
});
</script>
<?php
}

# = JAVASCRIPT CHECK ==========================
function sf_js_check()
{
	return '<noscript><div class="sfregmessage">'.__("This forum requires Javascript to be enabled for posting content", "sforum").'</div></noscript>'."\n";
}

# = COOKIE HANDLING ===========================
function sf_write_guest_cookie($guestname, $guestemail)
{
	$cookiepath = '/';
	setcookie('guestname_' . COOKIEHASH, $guestname, time() + 30000000, $cookiepath, false);
	setcookie('guestemail_' . COOKIEHASH, $guestemail, time() + 30000000, $cookiepath, false);
	setcookie('sflast_' . COOKIEHASH, time(), time() + 30000000, $cookiepath, false);

	return;
}

# = SPAM MATH HANDLING ========================
function sf_math_spam_build()
{
	$spammath[0] = rand(1, 12);
	$spammath[1] = rand(1, 12);

	# Calculate result
	$result = $spammath[0] + $spammath[1];

	# Add name of the weblog:
	$result .= get_bloginfo('name');
	# Add date:
	$result .= date('j') . date('ny');
	# Get MD5 and reverse it
	$enc = strrev(md5($result));
	# Get only a few chars out of the string
	$enc = substr($enc, 26, 1) . substr($enc, 10, 1) . substr($enc, 23, 1) . substr($enc, 3, 1) . substr($enc, 19, 1);

	$spammath[2] = $enc;

	return $spammath;
}

function sf_spamcheck()
{
	$spamcheck = array();
	$spamcheck[0]=false;

	# Check dummy input field
	if(array_key_exists ('url', $_POST))
	{
		if(!empty($_POST['url']))
		{
			$spamcheck[0]=true;
			$spamcheck[1]= __('1@Form not filled by human hands!', "sforum");
			return $spamcheck;
		}
	}

	# Check math question
	$correct = sf_esc_str($_POST['sfvalue2']);
	$test = sf_esc_str($_POST['sfvalue1']);
	$test = preg_replace('/[^0-9]/','',$test);

	if($test == '')
	{
		$spamcheck[0]=true;
		$spamcheck[1]= __('1@No answer was given to the math question', "sforum");
		return $spamcheck;
	}

	# Add name of the weblog:
	$test .= get_bloginfo('name');
	# Add date:
	$test .= date('j') . date('ny');
	# Get MD5 and reverse it
	$enc = strrev(md5($test));
	# Get only a few chars out of the string
	$enc = substr($enc, 26, 1) . substr($enc, 10, 1) . substr($enc, 23, 1) . substr($enc, 3, 1) . substr($enc, 19, 1);

	if($enc != $correct)
	{
		$spamcheck[0]=true;
		$spamcheck[1]= __('1@The answer to the math question was incorrect', "sforum");
		return $spamcheck;
	}
	return $spamcheck;
}

# = CENTRAL EMAIL ROUTINE =====================
function sf_send_email($mailto, $mailsubject, $mailtext, $replyto='')
{
	$sfmail=array();
	$sfmail = sf_get_option('sfmail');
	if ($sfmail['sfmailuse']) {
		add_filter('wp_mail_from', 'sf_mail_filter_from', 100);
		add_filter('wp_mail_from_name', 'sf_mail_filter_name', 100);
	}

	$email_sent = array();
	if($replyto<>'')
	{
		$header = "MIME-Version: 1.0\n".
		"From: wordpress@" . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . "\n" .
		"Reply-To: {$replyto}\n" .
		"Content-Type: text/plain; charset=\"" . get_settings('blog_charset') . "\"\n";
		$email = wp_mail($mailto, $mailsubject, $mailtext, $header);
	} else {
		$email = wp_mail($mailto, $mailsubject, $mailtext);
	}

	if ($email == false)
	{
		$email_sent[0] = false;
		$email_sent[1] = __('Email Notification Failed', "sforum");
	} else {
		$email_sent[0] = true;
		$email_sent[1] = __('Email Notification Sent', "sforum");
	}

	return $email_sent;
}

function sf_esc_int($checkval)
{
	$actual = '';
	if (isset($checkval))
	{
		if (is_numeric($checkval))
		{
			$actual = $checkval;
		}
		$checklen = strlen(strval($actual));
		if ($checklen != strlen($checkval))
		{
			die(__("A Suspect Request has been Rejected", "sforum"));
		}
	}
	return $actual;
}

function sf_esc_str($string)
{
	if (get_magic_quotes_gpc())  # prevents duplicate backslashes
  	{
    	$string = stripslashes($string);
  	}
   	$string = esc_sql($string);

  	return $string;
}


function sf_split_button_label($text, $pos=10)
{
	$label=array();
	$label=explode(' ', $text);
	$label[$pos].='&#x0A;';
	$text=implode(' ', $label);
	return str_replace('&#x0A; ', '&#x0A;', $text);
}

function sf_split_label($text, $pos=10)
{
	$label=array();
	$label=explode(' ', $text);
	$label[$pos].='<br />';
	$text=implode(' ', $label);
	return $text;
}

function splice($text, $pos=10, $method)
{
	$label=array();
	$label=explode(' ', $text);
	switch($method)
	{
		case 0:
			$rep ='&#x0A;';
			break;
		case 1:
			$rep ='<br />';
			break;
		case 2:
			$rep = "\n";
	}
	$label[$pos].=$rep;
	$text=implode(' ', $label);
	return str_replace($rep.' ', $rep, $text);
}


function sf_gis_error ($errno, $errstr, $errfile, $errline, $errcontext)
{
	global $gis_error;

	if($errno == E_WARNING || $errno == E_NOTICE)
	{
		$gis_error = __('Unable to validate image details', 'sforum');
	}

}

# ------------------------------------------------------------------
# sf_register_math()
#
# Filter Call
# Sets up the spam math on registration form
# ------------------------------------------------------------------
function sf_register_math()
{
	$sflogin = array();
	$sflogin = sf_get_option('sflogin');
	if($sflogin['sfregmath'])
	{
		$spammath = sf_math_spam_build();

		$out ='<input type="hidden" size="30" name="url" value="" /></p>'."\n";
		$out.='<p><strong>'.__("Math Required!", "sforum").'</strong><br />'."\n";
		$out.=sprintf(__("What is the sum of: <strong> %s + %s </strong>", "sforum"), $spammath[0], $spammath[1]).'&nbsp;&nbsp;&nbsp;'."\n";
		$out.='<input type="text" tabindex="3" size="7" id="sfvalue1" name="sfvalue1" value="" /></p>'."\n";
		$out.='<input type="hidden" name="sfvalue2" value="'.$spammath[2].'" />'."\n";
		echo $out;
	}
	return;
}

# ------------------------------------------------------------------
# sf_register_error()
#
# Filter Call
# Sets up the spam math error is required
#	$errors:	registration errors array
# ------------------------------------------------------------------
function sf_register_error($errors)
{
	global $ISFORUM;

	$sflogin = array();
	$sflogin = sf_get_option('sflogin');

	if($sflogin['sfregmath'])
	{
		$spamtest=sf_spamcheck();
		if($spamtest[0] == true)
		{
			$errormsg = str_replace('1@', '<b>ERROR</b>: ', $spamtest[1]);

			if($ISFORUM == false)
			{
				$errors->add('Bad Math', $errormsg);
			} else {
				$errors['math_check'] = $errormsg;
			}
		}
	}
	return $errors;
}

if (!function_exists('post_password_required')):
function post_password_required( $post = null ) {
	$post = get_post($post);

	if ( empty($post->post_password) )
		return false;

	if ( !isset($_COOKIE['wp-postpass_' . COOKIEHASH]) )
		return true;

	if ( $_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password )
		return true;

	return false;
}
endif;

# new spf options routines
function sf_load_alloptions() {
	global $wpdb;

    # see if options table exists first
	$optionstable = $wpdb->query("SHOW TABLES LIKE '".SF_PREFIX."sfoptions'");
	if (empty($optionstable)) return '';

	$alloptions_db = $wpdb->get_results("SELECT option_name, option_value FROM ".SF_PREFIX."sfoptions");
	$alloptions = array();
	foreach ((array) $alloptions_db as $opt)
	{
		$alloptions[$opt->option_name] = $opt->option_value;
	}

	return $alloptions;
}

function sf_get_option($option_name)
{
	global $wpdb, $SFALLOPTIONS;

	# sucky hack, but need to handle getting options pre-upgrade
	if (empty($SFALLOPTIONS))
	{
		# if the option exists in wp option table, must be upgrade where we havent moved to spf options table yet
		$opt = get_option($option_name);
		if (!empty($opt)) return $opt;

		# options not loaded yet, so load them
		$SFALLOPTIONS = sf_load_alloptions();
	}

	if($SFALLOPTIONS && array_key_exists($option_name, $SFALLOPTIONS))
	{
		$value = sf_opcheck($SFALLOPTIONS[$option_name]);
		$value = maybe_unserialize($value);
	} else {
		$value = false;
	}

	return $value;
}

# Option record sanitiser - ensures value (true/false)
function sf_opcheck($value=false)
{
	if(isset($value) && (!empty($value) || $value == 0))
	{
		return $value;
	} else {
		return false;
	}
}

function sf_update_option($option_name, $newvalue)
{
	global $wpdb, $SFALLOPTIONS;

	$oldvalue = sf_get_option($option_name);
	if ($newvalue === $oldvalue) return false;

	if (!isset($SFALLOPTIONS[$option_name]))
	{
		sf_add_option($option_name, $newvalue);
		return true;
	}

	$newvalue = maybe_serialize($newvalue);
	$wpdb->update(SFOPTIONS, array('option_value' => $newvalue), array('option_name' => $option_name) );

	if ($wpdb->rows_affected == 1)
	{
		$SFALLOPTIONS[$option_name] = $newvalue;
		return true;
	}

	return false;
}

function sf_add_option($option_name, $value = '')
{
	global $wpdb, $SFALLOPTIONS;

    # see if options table exists first
	$optionstable = $wpdb->query("SHOW TABLES LIKE '".SF_PREFIX."sfoptions'");
	if (empty($optionstable)) return '';

	# make sure $SFALLOPTIONS has been populated before we try and use it
	if (empty($SFALLOPTIONS))
	{
		$SFALLOPTIONS = sf_load_alloptions();
	}

	# Make sure the option doesn't already exist - call update if it does
	if (array_key_exists($option_name, $SFALLOPTIONS)) sf_update_option($option_name, $value);

	$value = maybe_serialize($value);
	$wpdb->insert(SFOPTIONS, array('option_name' => $option_name, 'option_value' => $value));
	$SFALLOPTIONS[$option_name] = $value;

	return;
}

function sf_delete_option($option_name)
{
	global $wpdb, $SFALLOPTIONS;

	$option = $wpdb->get_row("SELECT option_id FROM ".SFOPTIONS." WHERE option_name = '".$option_name."'");
	if (is_null($option) || !$option->option_id) return false;

	$wpdb->query( "DELETE FROM ".SFOPTIONS." WHERE option_name = '".$option_name."'" );

	unset($SFALLOPTIONS[$option_name]);

	return true;
}

# ------------------------------------------------------------------
# sf_process_hook()
#
# Processes a program hook and returns content
#	$hook:		Program Hook Function name
#	$args:		Array of arguments
# ------------------------------------------------------------------
function sf_process_hook($hook, $args)
{
	if(function_exists($hook))
	{
		if($args == '')
		{
			return call_user_func($hook);
		} else {
			return call_user_func_array($hook, $args);
		}
	}
	return '';
}

?>