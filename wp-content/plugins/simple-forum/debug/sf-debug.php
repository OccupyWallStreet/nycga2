<?php
/*
Simple:Press
Debug
$LastChangedDate: 2010-11-02 03:00:34 -0700 (Tue, 02 Nov 2010) $
$Rev: 4866 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

add_action('in_admin_header','sf_set_admin_dev');
function sf_set_admin_dev()
{
	if(defined('SP_DEVFLAG') && SP_DEVFLAG == true)
	{
		echo('<h1 style="color: #000;">('.SFVERSION.' - Development)</h1>');
	}
}

# ------------------------------------------
# display a formatted array
# ------------------------------------------
function ashow($what)
{
	echo('<div class="sfdebug">');
	echo('<pre><code>');
	print_r($what);
	echo('</code></pre>');
	echo('</div>');
	return;
}

# ------------------------------------------
# display an individual variable
# ------------------------------------------
function vshow($what="HERE")
{
	echo('<div class="sfdebug">');
	echo('<pre>');
	echo('---'.$what.'---<br />');
	echo('</pre>');
	echo('</div>');
	return;
}

# ------------------------------------------
# starts debug mode at forum display start
# ------------------------------------------
function start_debug()
{
	if(defined('SHOWDEBUG'))
	{
		if(SHOWDEBUG)
		{
			global $wpdb, $sfqueries;

			$sfqueries = $wpdb->num_queries;
		}
	}
	return;
}

# ------------------------------------------
# ends debug mode at forum end & displays
# ------------------------------------------
function end_debug()
{
	if(defined('SHOWDEBUG'))
	{
		if(SHOWDEBUG)
		{
			global $sfqueries;
			$out = '<div class="sfdebug">';
			$out.= 'Forum used: '.(get_num_queries() - $sfqueries).' queries | ' . timer_stop(0).' seconds'.'<br />';
			if(isset($GLOBALS['sfcount'])) $out.='Query Count: '.$GLOBALS['sfcount'].'<br />';
			$out.= showglobal();
			$out.= '</div>';
		}
		return $out;
	}
	return;
}

# ------------------------------------------
# places a value in global 'sfdebug'
# ------------------------------------------
function addglobal($data)
{
	$GLOBALS['sfdebug'] = $GLOBALS['sfdebug'].$data.'<br />';
	return;
}

# ------------------------------------------
# returns global 'sfdebug' for display
# ------------------------------------------
function showglobal()
{
	return $GLOBALS['sfdebug'];
}

# ------------------------------------------
# starts partial query count
# ------------------------------------------
function start_count()
{
	global $wpdb, $sfpartqueries;

	$sfpartqueries = get_num_queries();
	return;
}

# ------------------------------------------
# ends partial query count
# ------------------------------------------
function end_count()
{
	global $wpdb, $sfpartqueries;

	$GLOBALS['sfcount'] = (get_num_queries() - $sfpartqueries);
	return;
}

# ------------------------------------------
# display SPF files included
# ------------------------------------------
function show_includes()
{
	echo('<div class="sfdebug">');
	echo('<b>SPF Files Included on this page</b><br /><br />');

	$filelist = get_included_files();
	foreach($filelist as $f)
	{
		if(strpos($f, 'simple-forum'))
		{
			echo strrchr ($f , '/' ).'<br />';
		}
	}
	echo('</div>');
	return;
}

# ------------------------------------------
# Create test control array
# ------------------------------------------
function set_control($action)
{
	global $control;

	$control[]=$action;

#	ashow(debug_backtrace());
}

# ------------------------------------------
# display test control array
# ------------------------------------------
function show_control()
{
	global $control;

	ashow($control);
}

?>