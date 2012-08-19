<?php
/*
	Stop Spammer Registrations Plugin 
	History and Stats Page
*/
	$stats=kpg_sp_get_stats();
	extract($stats);
	$options=kpg_sp_get_options();
	extract($options);
	$nonce='';
	if (array_key_exists('kpg_stop_spammers_control',$_POST)) $nonce=$_POST['kpg_stop_spammers_control'];
	if (wp_verify_nonce($nonce,'kpgstopspam_update')) { 
		if (array_key_exists('kpg_stop_clear_cache',$_POST)) {
			// clear the cache
			$badems=array();
			$badips=array();
			$goodips=array();
			$stats['badems']=$badems;
			$stats['badips']=$badips;
			$stats['goodips']=$goodips;
			update_option('kpg_stop_sp_reg_stats',$stats);
			echo "<h2>Cache Cleared</h2>";
		}
		if (array_key_exists('kpg_stop_clear_hist',$_POST)) {
			// clear the cache
			$hist=array();
			$spcount=0;
			$stats['hist']=$hist;
			$stats['spcount']=$spcount;
			update_option('kpg_stop_sp_reg_stats',$stats);
			echo "<h2>History Cleared</h2>";
		}
		if (array_key_exists('kpg_stop_add_black_list',$_POST)) {
			$bbbb=$_POST['kpg_stop_add_black_list'];
			if (!in_array($bbbb,$blist)&&!in_array($bbbb,$wlist)) {
				$blist[]=$bbbb;
				$options['blist']=$blist;
				update_option('kpg_stop_sp_reg_options',$options);
				echo "<h2>$bbbb Added to Black List</h2>";
			}
		}
		if (array_key_exists('kpg_stop_add_white_list',$_POST)) {
			$bb=$_POST['kpg_stop_add_white_list'];
			if (!in_array($bb,$wlist)) {
				$wlist[]=$bb;
				$options['wlist']=$wlist;
				update_option('kpg_stop_sp_reg_options',$options);
				echo "<h2>$bb Added to White List</h2>";
			}
		}
		if (array_key_exists('kpg_stop_delete_log',$_POST)) {
			// clear the cache
			$f=dirname(__FILE__)."/../sfs_debug_output.txt";
			if (file_exists($f)) {
			    unlink($f);
				echo "<h2>Deleted Error Log File</h2>";
			}
		}
}
	$nonce=wp_create_nonce('kpgstopspam_update');

?>
<div class="wrap">
  <form method="post" name="kpg_ssp_bl" action="">
    <input type="hidden" name="kpg_stop_spammers_control" value="<?php echo $nonce;?>" />
    <input type="hidden" name="kpg_stop_add_black_list" value="" />
  </form>
  <form method="post" name="kpg_ssp_wl" action="">
    <input type="hidden" name="kpg_stop_spammers_control" value="<?php echo $nonce;?>" />
    <input type="hidden" name="kpg_stop_add_white_list" value="" />
  </form>
<script type="text/javascript" >
function addblack(ip) {
	document.kpg_ssp_bl.kpg_stop_add_black_list.value=ip;
	document.kpg_ssp_bl.submit();
	return false;
}
function addwhite(ip) {
	document.kpg_ssp_wl.kpg_stop_add_white_list.value=ip;
	document.kpg_ssp_wl.submit();
	return false;
}
</script>
  <h2>Stop Spammers Plugin Stats Version 3.7</h2>
 <?php 

	$nag='';
	if ($spmcount>0) {
		if ($spmcount>3000) {
			$nag="<br/> This plugin is really working hard for you. Don't you think that it's time to <a target=\"_blank\" href=\"http://www.blogseye.com/buy-the-book/\">buy the book</a>?</p>";
		}
		if ($spmcount>8000) {
			$nag="<p> WOW! This plugin is great. It's time to <a target=\"_blank\" href=\"http://www.blogseye.com/buy-the-book/\">buy the book</a> (99&cent; cheap)</p>";
		}
		if ($spmcount>15000) {
			$nag="<p> AMAZING! Look at all the spammers stopped. Please <a target=\"_blank\" href=\"http://www.blogseye.com/buy-the-book/\">buy the book</a> (99&cent; cheap)</p>";
		}
		if ($spmcount>30000) {
			$nag="<p> You know, if you already bought the book, I have written others that you can buy. Please <a target=\"_blank\" href=\"http://www.blogseye.com/buy-the-book/\">buy a book</a>.</p>";
		}
		if ($spmcount>40000) {
			$nag="<p><a target=\"_blank\" href=\"http://www.blogseye.com/buy-the-book/\">Oh well...</a></p>";
		}
?>
 <h3>Stop Spammers has stopped <?php echo $spmcount; ?> spammers since <?php echo $spmdate; ?>.</h3>
  <?php echo $nag; ?>
 
<?php 
}
	if ($spcount>0) {
?>
  <h3>Stop Spammers has stopped <?php echo $spcount; ?> spammers since <?php echo $spdate; ?>.</h3>
<?php 
	}
	$num_comm = wp_count_comments( );
	$num = number_format_i18n($num_comm->spam);
	if ($num_comm->spam>0) {	
?>
  <p>There are <a href='edit-comments.php?comment_status=spam'><?php echo $num; ?></a> spam comments waiting for you to report them</p>
<?php 
	}
	$num_comm = wp_count_comments( );
	$num = number_format_i18n($num_comm->moderated);
	if ($num_comm->moderated>0) {	
?>
  <p>There are <a href='edit-comments.php?comment_status=moderated'><?php echo $num; ?></a> comments waiting to be moderated</p>
<?php 
	}
?>

<p><a href="#" onclick="window.location.href=window.location.href;return false;">Refresh</a> - <a href="options-general.php?page=stopspammersoptions">View Options</a>
</p>
<?php
	if (count($hist)==0) {
		echo "<p>No Activity Recorded.</p>";
	} else {
  ?>
  <hr/>
  <h3>Recent Activity</h3>
  <form method="post" action="">
    <input type="hidden" name="kpg_stop_spammers_control" value="<?php echo $nonce;?>" />
    <input type="hidden" name="kpg_stop_clear_hist" value="true" />
    <input value="Clear Recent Activity" type="submit">
  </form>
  </p>
		<table style="background-color:#eeeeee;" cellspacing="2">
		<tr style="background-color:ivory;text-align:center;"><td>date/time</td><td>email</td><td>IP</td><td>user id</td><td>script</td><td>reason
<?php
	if (function_exists('is_multisite') && is_multisite()) {
?>		
		</td><td>blog</td>
<?php
}
?>		
		</tr>
<?php
		foreach($hist as $key=>$data) {
			//$hist[$now]=array($ip,$email,$author,$sname,'begin');
			$em=strip_tags(trim($data[1]));
			$dt=strip_tags($key);
			$ip=$data[0];
			$au=strip_tags($data[2]);
			$id=strip_tags($data[3]);
			if (empty($au)) $au=' -- ';
			if (empty($em)) $em=' -- ';
			$reason=$data[4];
			$blog=1;
			if (count($data)>5) $blog=$data[5];
			if (empty($blog)) $blog=1;
			if(empty($reason)) $reason="passed";
			echo "<tr style=\"background-color:white;\">
				<td style=\"font-size:.8em;padding:2px;\">$dt</td>
				<td style=\"font-size:.8em;padding:2px;\">$em</td>
				<td style=\"font-size:.8em;padding:2px;\">$ip"; 
		    if (strpos($reason,'passed')!==false && ($id=='/'||strpos($id,'login')!==false) && !in_array($ip,$blist) && !in_array($ip,$wlist)) {
				$skull = plugins_url( 'includes/sk.jpg', dirname(__FILE__) );
				echo "<a href=\"\" onclick=\"return addblack('$ip');\" title=\"Add to Black List\" alt=\"Add to Black List\" ><img src=\"$skull\" width=\"12px\" /></a>";
			}
			echo "</td><td style=\"font-size:.8em;padding:2px;\">$au</td>
				<td style=\"font-size:.8em;padding:2px;\">$id</td>
				<td style=\"font-size:.8em;padding:2px;\">$reason</td>";
			if (function_exists('is_multisite') && is_multisite()) {
				// switch to blog and back
				switch_to_blog($blog);
				$num_comm = wp_count_comments( );
				restore_current_blog();
				$snum = number_format_i18n($num_comm->spam);
				$mnum = number_format_i18n($num_comm->moderated );
				$anum = number_format_i18n($num_comm->total_comments);

				$blogname=get_blog_option( $blog, 'blogname' );
				$blogadmin=esc_url( get_admin_url($blog) );
				if (substr($blogadmin,strlen($blogadmin)-1)=='/') $blogadmin=substr($blogadmin,0,strlen($blogadmin)-1);
				echo "<td style=\"font-size:.8em;padding:2px;\" align=\"center\">";
				echo "$blogname: c<a href=\"$blogadmin/edit-comments.php/\">($anum)</a>,&nbsp; 
				p<a href=\"$blogadmin/edit-comments.php?comment_status=moderated\">($mnum)</a>,&nbsp; 
				s<a href=\"$blogadmin/edit-comments.php?comment_status=spam\">($snum)</a>";
				echo "</td>";
			}
			echo "</tr>";
		}
	?>
		</table>
<?php
		
	
   }
   if (count($badems)==0&&count($badips)==0&&count($goodips)==0) {
?>
 	<p>Nothing in the cache.</p>
  
<?php
   } else {
?>
  <h3>Cached Values</h3>
  <table><tr><td>
  <form method="post" action="">
    <input type="hidden" name="kpg_stop_spammers_control" value="<?php echo $nonce;?>" />
    <input type="hidden" name="kpg_stop_clear_cache" value="true" />
    <input value="Clear the Cache" type="submit">
  </form>
  </td></tr></table>
  <table align="center" width="60%">
    <tr>
	<?php
		if (count($badems)>0) {
	?>
	<td width="35%" align="center">Rejected Emails</td>
	<?php
		}
	?>
	<?php
		if (count($badips)>0) {
	?>
      <td width="30%" align="center">Rejected IPs</td>
	<?php
		}
	?>
	<?php
		if (count($goodips)>0) {
	?>
      <td width="30%" align="center">Good IPs</td>
	<?php
		}
	?>
    </tr>
    <tr>
	<?php
		if (count($badems)>0) {
	?>
      <td style="border:1px solid black;font-size:.75em;padding:3px;" valign="top"><?php
		foreach ($badems as $key => $value) {
			//echo "$key; Date: $value<br/>\r\n";
			$key=urldecode($key);
			echo "<a href=\"http://www.stopforumspam.com/search?q=$key\" target=\"_stopspam\">$key: $value</a><br/>";
		}
	?></td>
	<?php
		}
	?>
	<?php
		if (count($badips)>0) {
	?>

      <td  style="border:1px solid black;font-size:.75em;padding:3px;" valign="top"><?php
		foreach ($badips as $key => $value) {
			//echo "$key; Date: $value<br/>\r\n";
			echo "<a href=\"http://www.stopforumspam.com/search?q=$key\" target=\"_stopspam\">$key: $value</a><br/>";
		}
	?></td>
 	<?php
		}
	?>
	<?php
		if (count($goodips)>0) {
	?>

      <td  style="border:1px solid black;font-size:.75em;padding:3px;" valign="top"><?php
		foreach ($goodips as $key => $value) {
			//echo "$key; Date: $value<br/>\r\n";
			echo "<a href=\"http://www.stopforumspam.com/search?q=$key\" target=\"_stopspam\">$key: $value</a><br/>";
		}
	?></td>
 	<?php
		}
	?>
   </tr>
  </table>
  <?PHP
} 
	$options=kpg_sp_get_options();
	extract($options);
 
 	$ip=$_SERVER['REMOTE_ADDR'];
	$ip=check_forwarded_ip($ip);

	if ($addtowhitelist=='Y'&&in_array($ip,$wlist)) {
		echo "<h3>Your current IP is in your white list. This will keep you from being locked out in the future</h3>";
	}



	if (function_exists('is_multisite') && is_multisite()) {
	?>
	<p>If you are looking for the list of spam on the blogs, I've broken that out into a separate plugin. 
	It works with this plugin and allows you to report spam from a single page for all blogs. It is less likely to time out than the old version.
	Please try it out:  <a href="http://wordpress.org/extend/plugins/mu-manage-comments-plugin/" target="_blank">MU Manage Comments Plugin</a></p>
	<?php
	}
?>
  <?php
     $f=dirname(__FILE__)."/../sfs_debug_output.txt";
	 if (file_exists($f)) {
	    ?>
<h3>Error Log</h3>
<p>If debugging is turned on, the plugin will drop a record each time it encounters a PHP error. 
Most of these errors are not fatal and do not effect the operation of the plugin. Almost all come from the unexpected data that
spammers include in their effort to fool us. The author's goal is to eliminate any and
all errors. These errors should be corrected. Fatal errors should be reported to the author at www.blogseye.com.</p>

		
<form method="post" action="">
    <input type="hidden" name="kpg_stop_spammers_control" value="<?php echo $nonce;?>" />
    <input type="hidden" name="kpg_stop_delete_log" value="true" />
    <input value="Delete Error Log File" type="submit">
</form>

<pre>
<?php readfile($f); ?>
</pre>
<?php
	 }
?>

</div>
