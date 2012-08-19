<?php
/*
	Stop Spammer Registrations Plugin 
	Options Setup Page
*/
	if(!current_user_can('manage_options')) {
		die('Access Denied');
	}


	$stats=kpg_sp_get_stats();
	extract($stats);
	$options=kpg_sp_get_options();
	extract($options);

	$wordpress_api_key=get_option('wordpress_api_key');
    if (empty($wordpress_api_key)) $wordpress_api_key='';
	$ip=$_SERVER['REMOTE_ADDR'];
	$ip=check_forwarded_ip($ip);


	$nonce='';
	if (array_key_exists('kpg_stop_spammers_control',$_POST)) $nonce=$_POST['kpg_stop_spammers_control'];
	if (wp_verify_nonce($nonce,'kpgstopspam_update')) { 
		if (array_key_exists('action',$_POST)) {
			if (array_key_exists('wordpress_api_key',$_POST)) {
				$wordpress_api_key=stripslashes($_POST['wordpress_api_key']);
				if ($wordpress_api_key!='na') update_option('wordpress_api_key',$wordpress_api_key);
			} else {
				$wordpress_api_key='na';
			}
						
			if (array_key_exists('chksession',$_POST)) {
				$chksession=stripslashes($_POST['chksession']);
			} else {
				$chksession='N';
			}
			$options['chksession']=$chksession;
			
			if (array_key_exists('chkdisp',$_POST)) {
				$chkdisp=stripslashes($_POST['chkdisp']);
			} else {
				$chkdisp='N';
			}
			$options['chkdisp']=$chkdisp;
			
			if (array_key_exists('chkubiquity',$_POST)) {
				$chkubiquity=stripslashes($_POST['chkubiquity']);
			} else {
				$chkubiquity='N';
			}
			$options['chkubiquity']=$chkubiquity;
			
			if (array_key_exists('chkwplogin',$_POST)) {
				$chkwplogin=stripslashes($_POST['chkwplogin']);
			} else {
				$chkwplogin='N';
			}
			$options['chkwplogin']=$chkwplogin;
			
			if (array_key_exists('chkakismet',$_POST)) {
				$chkakismet=stripslashes($_POST['chkakismet']);
			} else {
				$chkakismet='N';
			}
			$options['chkakismet']=$chkakismet;
			
			if (array_key_exists('chkcomments',$_POST)) {
				$chkcomments=stripslashes($_POST['chkcomments']);
			} else {
				$chkcomments='N';
			}
			$options['chkcomments']=$chkcomments;
			
			if (array_key_exists('chklogin',$_POST)) {
				$chklogin=stripslashes($_POST['chklogin']);
			} else {
				$chklogin='N';
			}
			$options['chklogin']=$chklogin;
			
			if (array_key_exists('chksignup',$_POST)) {
				$chksignup=stripslashes($_POST['chksignup']);
			} else {
				$chksignup='N';
			}
			$options['chksignup']=$chksignup;
			
			if (array_key_exists('chklong',$_POST)) {
				$chklong=stripslashes($_POST['chklong']);
			} else {
				$chklong='N';
			}
			$options['chklong']=$chklong;
			
			if (array_key_exists('chkagent',$_POST)) {
				$chkagent=stripslashes($_POST['chkagent']);
			} else {
				$chkagent='N';
			}
			$options['chkagent']=$chkagent;
			
			if (array_key_exists('chkxmlrpc',$_POST)) {
				$chkxmlrpc=stripslashes($_POST['chkxmlrpc']);
			} else {
				$chkxmlrpc='N';
			}
			$options['chkxmlrpc']=$chkxmlrpc;
			
			
			if (array_key_exists('addtowhitelist',$_POST)) {
				$addtowhitelist=stripslashes($_POST['addtowhitelist']);
			} else {
				$addtowhitelist='N';
			}
			$options['addtowhitelist']=$addtowhitelist;
			
			if (array_key_exists('chkspamwords',$_POST)) {
				$chkspamwords=stripslashes($_POST['chkspamwords']);
			} else {
				$chkspamwords='N';
			}
			$options['chkspamwords']=$chkspamwords;
			
			if (array_key_exists('chkjscript',$_POST)) {
				$chkjscript=stripslashes($_POST['chkjscript']);
			} else {
				$chkjscript='N';
			}
			$options['chkjscript']=$chkjscript;
					
			if (array_key_exists('chkwpmail',$_POST)) {
				$chkwpmail=stripslashes($_POST['chkwpmail']);
			} else {
				$chkwpmail='N';
			}
			$options['chkwpmail']=$chkwpmail;
			
			if (array_key_exists('redherring',$_POST)) {
				$redherring=stripslashes($_POST['redherring']);
			} else {
				$redherring='N';
			}
			$options['redherring']=$redherring;
			
			if (array_key_exists('chkdnsbl',$_POST)) {
				$chkdnsbl=stripslashes($_POST['chkdnsbl']);
			} else {
				$chkdnsbl='N';
			}
			$options['chkdnsbl']=$chkdnsbl;
			
			if (array_key_exists('chkemail',$_POST)) {
				$chkemail=stripslashes($_POST['chkemail']);
			} else {
				$chkemail='N';
			}
			$options['chkemail']=$chkemail;
			
			if (array_key_exists('chkreferer',$_POST)) {
				$chkreferer=stripslashes($_POST['chkreferer']);
			} else {
				$chkreferer='N';
			}
			$options['chkreferer']=$chkreferer;
			
			
			if (array_key_exists('nobuy',$_POST)) {
				$nobuy=stripslashes($_POST['nobuy']);
			} else {
				$nobuy='N';
			}
			if ($nobuy!='Y') $nobuy='N';
			$options['nobuy']=$nobuy;
			
			if (array_key_exists('redir',$_POST)) {
				$redir=stripslashes($_POST['redir']);
			} else {
				$redir='N';
			}
			if ($redir!='Y') $redir='N';
			$options['redir']=$redir;
			
			
			if (array_key_exists('accept',$_POST)) {
				$accept=stripslashes($_POST['accept']);
			} else {
				$accept='N';
			}
			if ($accept!='Y') $accept='N';
			$options['accept']=$accept;
			
			if (array_key_exists('apikey',$_POST)) $apikey=stripslashes($_POST['apikey']);
			$options['apikey']=$apikey;
			if (array_key_exists('honeyapi',$_POST)) $honeyapi=stripslashes($_POST['honeyapi']);
			$options['honeyapi']=$honeyapi;
			if (array_key_exists('botscoutapi',$_POST)) $botscoutapi=stripslashes($_POST['botscoutapi']);
			$options['botscoutapi']=$botscoutapi;
			if (array_key_exists('blist',$_POST)) {
			    $blist=$_POST['blist'];
				$blist=explode("\n",$blist);
				$tblist=array();
				foreach($blist as $bl) {
					$bl=trim($bl);
					if (!empty($bl)) $tblist[]=$bl;
				}
				$options['blist']=$tblist;				
				$blist=$tblist;
			}
			if (array_key_exists('spamwords',$_POST)) {
			    $spamwords=$_POST['spamwords'];
				$spamwords=explode("\n",$spamwords);
				$tblist=array();
				foreach($spamwords as $bl) {
					$bl=trim($bl);
					if (!empty($bl)) $tblist[]=$bl;
				}
				$options['spamwords']=$tblist;				
				$spamwords=$tblist;
			}
			if (array_key_exists('wlist',$_POST)) {
			    $wlist=$_POST['wlist'];
				$wlist=explode("\n",$wlist);
				$tblist=array();
				foreach($wlist as $bl) {
					$bl=trim($bl);
					if (!empty($bl)) $tblist[]=$bl;
				}
				$options['wlist']=$tblist;				
				$wlist=$tblist;
			}
			if (array_key_exists('baddomains',$_POST)) {
			    $baddomains=$_POST['baddomains'];
				$baddomains=explode("\n",$baddomains);
				$tblist=array();
				foreach($baddomains as $bl) {
					$bl=trim($bl);
					if (!empty($bl)) $tblist[]=$bl;
				}
				$options['baddomains']=$tblist;				
				$baddomains=$tblist;
			}
			// update the freq and age options
			if (array_key_exists('sfsfreq',$_POST)) $sfsfreq=trim(stripslashes($_POST['sfsfreq']));
			if (array_key_exists('hnyage',$_POST)) $hnyage=trim(stripslashes($_POST['hnyage']));
			if (array_key_exists('botfreq',$_POST)) $botfreq=trim(stripslashes($_POST['botfreq']));
			if (array_key_exists('sfsage',$_POST)) $sfsage=trim(stripslashes($_POST['sfsage']));
			if (array_key_exists('hnylevel',$_POST)) $hnylevel=trim(stripslashes($_POST['hnylevel']));
			if (array_key_exists('botage',$_POST)) $botage=trim(stripslashes($_POST['botage']));
			if (array_key_exists('muswitch',$_POST)) $muswitch=trim(stripslashes($_POST['muswitch']));
			if (array_key_exists('rejectmessage',$_POST)) $rejectmessage=trim(stripslashes($_POST['rejectmessage']));
			if (array_key_exists('redirurl',$_POST)) $redirurl=trim(stripslashes($_POST['redirurl']));
			
			if (array_key_exists('kpg_sp_cache',$_POST)) $kpg_sp_cache=trim(stripslashes($_POST['kpg_sp_cache']));
			if (array_key_exists('kpg_sp_hist',$_POST)) $kpg_sp_hist=trim(stripslashes($_POST['kpg_sp_hist']));
			// check for numerics in the fields
			if (!is_numeric($sfsfreq)) $sfsfreq=0; 
			if (!is_numeric($hnyage)) $hnyage=0;
			if (!is_numeric($botfreq)) $botfreq=0; 
			if (!is_numeric($hnylevel)) $hnylevel=5;
			if (!is_numeric($botage)) $botage=9999; 
			if (!is_numeric($sfsage)) $sfsage=9999;	
			if (!is_numeric($kpg_sp_cache)) $kpg_sp_cache=25;	
			if (!is_numeric($kpg_sp_hist)) $kpg_sp_hist=25;	
			$options['sfsfreq']=$sfsfreq;
			$options['hnyage']=$hnyage;
			$options['botfreq']=$botfreq;
			$options['sfsage']=$sfsage;
			$options['hnylevel']=$hnylevel;
			$options['botage']=$botage;
			$options['kpg_sp_cache']=$kpg_sp_cache;
			$options['kpg_sp_hist']=$kpg_sp_hist;
			$options['redirurl']=$redirurl;
			if (empty($muswitch)) $muswitch='Y';
			if ($muswitch!='N') $muswitch='Y';
			$options['muswitch']=$muswitch;
			$options['rejectmessage']=$rejectmessage;
			if (function_exists('is_multisite') && is_multisite() && function_exists('kpg_ssp_global_unsetup') && function_exists('kpg_ssp_global_setup')) {
				if ($muswitch=='N') {
					kpg_ssp_global_unsetup();
				} else {
					kpg_ssp_global_setup();
				}
			}			
			update_option('kpg_stop_sp_reg_options',$options);
			echo "<h2>Options Updated</h2>";
		}
		extract($options);

	}
	
	
?>

<div class="wrap">
  <h2>Stop Spammers Plugin Options</h2>
  <?php
	if ($addtowhitelist=='Y'&&in_array($ip,$wlist)) {
		echo "<h3>Your current IP is in your white list. This will keep you from being locked out in the future</h3>";
	}
	
	
	if ($nobuy!='Y') {
?>
  <div style="position:relative;float:right;width:35%;background-color:ivory;border:#333333 medium groove;padding:4px;margin-left:4px;">
    <p>This plugin is free and I expect nothing in return. If you would like to support my programming, you can buy my book of short stories.</p>
    <p>Some plugin authors ask for a donation. I ask you to spend a very small amount for something that you will enjoy. eBook versions for the Kindle and other book readers start at 99&cent;. The book is much better than you might think, and it has some very good science fiction writers saying some very nice things. <br/>
      <a target="_blank" href="http://www.blogseye.com/buy-the-book/">Error Message Eyes: A Programmer's Guide to the Digital Soul</a></p>
    <p>A link on your blog to one of my personal sites would also be appreciated.</p>
    <p><a target="_blank" href="http://www.WestNyackHoney.com">West Nyack Honey</a> (I keep bees and sell the honey)<br />
      <a target="_blank" href="http://www.cthreepo.com/blog">Wandering Blog</a> (My personal Blog) <br />
      <a target="_blank" href="http://www.cthreepo.com">Resources for Science Fiction</a> (Writing Science Fiction) <br />
      <a target="_blank" href="http://www.jt30.com">The JT30 Page</a> (Amplified Blues Harmonica) <br />
      <a target="_blank" href="http://www.harpamps.com">Harp Amps</a> (Vacuum Tube Amplifiers for Blues) <br />
      <a target="_blank" href="http://www.blogseye.com">Blog&apos;s Eye</a> (PHP coding) <br />
      <a target="_blank" href="http://www.cthreepo.com/bees">Bee Progress Beekeeping Blog</a> (My adventures as a new beekeeper) </p>
  </div>
  <?php
	}
?>
  <p><a href="options-general.php?page=stopspammerstats">View History and Cache</a> </p>
  <?php
	if (function_exists('is_multisite') && is_multisite()) {
		global $blog_id;
		if (!isset($blog_id)||$blog_id!=1) {
			if ($muswitch=='Y') {
				?>
  <h3>Stop Spammers is configured so that settings are available only on the Main Blog.</h3>
  <?php
				return;
			}		
		}
	}
   $nonce=wp_create_nonce('kpgstopspam_update');
	if ($spmcount>0) {
?>
  <h3>Stop Spammers has stopped <?php echo $spmcount; ?> spammers since installation</h3>
  <?php 
}
	if ($spcount>0) {
?>
  <h3>Stop Spammers has stopped <?php echo $spcount; ?> spammers since last cleared</h3>
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
  <p>There are <a href='edit-comments.php?comment_status=moderated'><?php echo $num; ?></a> spam comments waiting to be moderated</p>
  <?php 
	}
?>
  <p style="font-weight:bold;">The Stop Spammers Plugin is installed and working correctly.</p>
  <p style="font-weight:bold;">Version 3.7</p>
  <p>Eliminates 99% of spam registrations and  comments. Checks all attempts to leave spam against <a href="http://www.stopforumspam.com/">Stop Forum Spam</a>, <a href="http://www.projecthoneypot.org/">Project Honeypot</a>, and <a href="http://www.botscout.com/">BotScout</a>, DNSBL lists such as Spamhaus.org, known spammer hosts such  as Ubiquity Servers, disposable email addresses, very long email address and  names, and HTTP_ACCEPT header. Checks for robots that hit your site too fast,  and puts a fake comment and login screen where only spammers will find them. In  all the plugin uses 15 different strategies to block spammers. </p>
  <p style="font-weight:bold;">How the plugin works: </p>
  <p>This plugin checks against StopForumSpam.com, Project Honeypot and BotScout to to prevent spammers from registering or making comments. 
    The Stop Spammer Registrations plugin works by checking the IP address, email and user id of anyone who tries to register, login, or leave a comment. This effectively blocks spammers who try to register on blogs or leave spam. It checks a users credentials against up to three databases: <a href="http://www.stopforumspam.com/">Stop Forum Spam</a>, <a href="http://www.projecthoneypot.org/">Project Honeypot</a>, and <a href="http://www.botscout.com/">BotScout</a>. Optionally checks against Akismet for Logins and Registrations. </p>
  <p>Optionally the plugin will also check for disposable email addresses, check for the lack of a HTTP_ACCEPT header, and check against several DNSBL lists such as Spamhaus.org. It also checks against spammer hosts like Ubiquity-Nobis, XSServer, Balticom, Everhost, FDC, Exetel, Virpus and other servers, which are a major source of Spam Comments. </p>
  <p>Rejects very long email addresses and very long author names since spammers can't resist putting there message everywhere. It also rejects form POST data where there is no HTTP_REFERER header, because spammers often forget to include the referring site information in their software.</p>
  <p>The plugin will install a &quot;Red Herring&quot; comment form that will be invisible to normal users. Spammers will find this form and try to do their dirty deed using it. This results in the IP address being added to the deny list. This feature is turned off by default because the form might screw up your theme. Turn the option on and check your theme. If the form (a one pixel box) changes your theme presentation then turn the feature off. I highly recommend that you try this option. It stops a ton of spam.</p>
  <p>The plugin can check how long it takes a  spammer to read the comment submit form and then post the comment. If this  takes less than 5 seconds, then the commenter is a spammer. A human cannot fill  out email, comment, and then submit the comment in less than 5 seconds. This is the best way to stop spam. </p>
  <p><span style="font-weight:bold;">Limitations: </span></p>
  <p>StopForumSpam.com limits checks to 10,000 per day for each IP so the plugin may stop validating on very busy sites. I have not seen this happen, yet. The plugin will not stop spam that has not been reported to the various databases. You will always get some comments from spammers who are not yet reported. You can help others and yourself by reporting spam. If you do not report spam, the spammer will keep hitting you. This plugin works best with Akismet. Akismet works well, but clutters the database with spam comments that need to be deleted regularly, and Akismet does not work with spammer registrations. </p>
  <p style="font-weight:bold;">API Keys: </p>
  <p> API Keys are NOT required for the plugin to work. Stop Forum Spam does not require a key so this plugin will work immediately without a key. The API key for<a href="http://www.stopforumspam.com/"> Stop Forum Spam</a> is only used for reporting spam. In order to use the <a href="http://www.projecthoneypot.org/">Project HoneyPot</a> or <a href="http://www.botscout.com/">BotScout</a> spam databases you will need to register at those sites and get a free API key. </p>
  <p><span style="font-weight:bold;">History: </span></p>
  <p>The Stop Spammer Registrations plugin keeps a count of the spammers that it has blocked and displays this on the WordPress dashboard. It also displays the last hits on email or IP and it also shows a history of the times it has made a check, showing rejections, passing emails and errors. When there is data to display there will also be a button to clear out the data. You can control the size of the list and clear the history. </p>
  <p><span style="font-weight:bold;">Cache: </span></p>
  <p>The Stop Spammer Registrations plugin keeps track of a number of spammer emails and IP addresses in a cache to avoid pinging databases more often than necessary. The results are saved and displayed. You can control the length of the cache list and clear it at any time. </p>
  <p><span style="font-weight:bold;">Reporting Spam : </span></p>
  <p>On the comments moderation page, the plugin adds extra options to check comments against the various databases and to report to the Stop Forum Spam database. You will need a Stop Forum Spam API key in order to report spam/ </p>
  <p><span style="font-weight:bold;">Network MU Installation Option : </span></p>
  <p> If you are running a networked WPMU system of blogs, you can optionally control this plugin from the control panel of the main blog. By checking the 'Networked ON' radio button, the individual blogs will not see the options page. The API keys will only have to entered in one place and the history will only appear in one place, making the plugin easier to use for administrating many blogs. The comments, however, still must be maintained from each blog. The Network buttons only appear if you have a Networked installation.</p>
  <p><strong>Debugging:</strong></p>
  <p>If the plugin is having trouble it drops a file in the plugin&rsquo;s directory named sfs_debug_output.txt. If the file is produced it will appear at the bottom of this page along with a button to delete it. Common errors include many which are not really an issue. From time to time you will see an error that reports that PHP could not redirect a page. This happens on some systems where the spammer is rejected, but the access denied screen does not appear correctly. I believe this is caused by data in the spam comment.</p>
  <p>If you do not want to see any errors you can search in the plugin code for the line that contains &quot;debug=true;&quot; and change the true to false, so the line is &quot;debug=false;&quot; (a semicolon at the end is required by PHP). I have set the plugin to report problems by default (debug=true;). </p>
  <p>Please report any errors that you see. </p>
  <p><span style="font-weight:bold;">Requirements : </span></p>
  <p>The plugin uses the WP_Http class to query the spam databases. Normally, if WordPress is working, then this class can access the databases. If, however, the system administrator has turned off the ability to open a url, then the plugin will not work. Sometimes placing a php.ini file in the blog's root directory with the line 'allow_url_fopen=On' will solve this.</p>
  <p>The Stop Spammer Registrations plugin is ON when it is installed and enabled. To turn it off just disable the plugin from the plugin menu.. </p>
  <p>You may see your own email in the cache as spammers try to use it to leave comments. You may have to white list your own email if that is the case, to keep the plugin from locking you out.</p>
  <p>There is a button that allows you check access to the StopForumSpam database from the plugin Options page. This will tell you if the host allows opening of remote URL addresses. Please check your network access to the StopForumSpam database before reporting that the plugin doesn't work. The problem may be your host configuration. </p>
  <hr/>
  <h4>For questions and support please check my website <a href="http://www.blogseye.com/i-make-plugins/stop-spammer-registrations-plugin/">BlogsEye.com</a>.</h4>
  <p>&nbsp;</p>
  <script type="text/javascript" >
function kpg_api_test(url) {
	var data= {
		action: 'sfs_check',
		ajax_url: url
	}
	jQuery.get(ajaxurl, data, sfs_ajax_return_check);
}
function sfs_ajax_return_check(response) {
	response=response.substring(0,response.length-1);
	alert(response);
	return false;
}
</script>
  <h4>Test Network Access:</h4>
  Use this button to test if this plugin has network access to the StopForumSpam.com database<br/>
  <form method="GET" action="">
    <p class="submit">
      <input class="button-primary" value="Test Network Access" type="submit" onclick="kpg_api_test();return false;">
  </form>
  <br/>
  <form method="post" action="">
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="kpg_stop_spammers_control" value="<?php echo $nonce;?>" />
    <?php
		if (function_exists('is_multisite') && is_multisite()) {
	?>
    <h4>Network Blog Option:</h4>
    <table align="center" cellspacing="1" style="background-color:#CCCCCC;font-size:.9em;">
      <tr bgcolor="white">
        <td width="20%" valign="top">Select how you want to control options in a networked blog environment:</td>
        <td valign="top"> Networked ON:
          <input name="muswitch" type="radio" value='Y'  <?php if ($muswitch=='Y') echo "checked=\"true\""; ?> />
          <br/>
          Networked OFF:
          <input name="muswitch" type="radio" value='N' <?php if ($muswitch!='Y') echo "checked=\"true\""; ?>  />
        </td>
        <td valign="top"> If you are running WPMU and want to control all options and logs through the main log admin panel, select on. If you select OFF, each blog will have to configure the plugin separately. </td>
      </tr>
    </table>
    <br/>
    <?php
		}
	?>
    <h4>API Keys:</h4>
    <table align="center" cellspacing="1" style="background-color:#CCCCCC;font-size:.9em;">
      <tr bgcolor="white">
        <td valign="top">Enable StopForumSpam Lookups:</td>
        <td align="center" valign="top"><input name="chksfs" type="checkbox" value="Y" <?php if ($accept=='Y') echo  "checked=\"checked\"";?>/>
        </td>
        <td valign="top">You may want to disable checking of the SFS db. By default this is on. Uncheck it to turn off.</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Your StopForumSpam.com API Key:</td>
        <td align="center" valign="top"><input size="32" name="apikey" type="text" value="<?php echo $apikey; ?>"/></td>
        <td valign="top">(optional)</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top"> Project Honeypot API Key:</td>
        <td align="center" valign="top"><input size="32" name="honeyapi" type="text" value="<?php echo $honeyapi; ?>"/></td>
        <td valign="top"> (For HTTP:bl blacklist lookup, if not blank)</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">BotScout API Key:</td>
        <td align="center" valign="top"><input size="32" name="botscoutapi" type="text" value="<?php echo $botscoutapi; ?>"/></td>
        <td valign="top">(For BotScout.com lookup, if not blank)</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Wordpress API Key:</td>
        <td align="center" valign="top"><input size="32" name="wordpress_api_key" type="text" value="<?php echo $wordpress_api_key; ?>"/></td>
        <td valign="top">(For use with Akismet DB. This will work without the Akismet plugin if you put enter your Wordpress API Key here.)</td>
      </tr>
    </table>
    <br/>
    <h4>Spam Limits:</h4>
    You can set the minimum settings to allow possible spammers to use your site. <br/>
    You may wish to forgive spammers with few incidents or no recent activity. I would recommend that to be on the safe side you should block users who appear on the spam database unless they specifically ask to be white listed. Allowed values are 0 to 9999. Only numbers are accepted.
    <table align="center" cellspacing="1" style="background-color:#CCCCCC;font-size:.9em;">
      <tr bgcolor="white">
        <td valign="top">Deny spammers found on Stop Forum Spam with more than
          <input size="3" name="sfsfreq" type="text" value="<?php echo $sfsfreq; ?>"/>
          incidents, and occurring less than
          <input size="4" name="sfsage" type="text" value="<?php echo $sfsage; ?>"/>
          days ago. </td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Deny spammers found on Project HoneyPot with incidents less than
          <input size="3" name="hnyage" type="text" value="<?php echo $hnyage; ?>"/>
          days ago, and with more than
          <input size="4" name="hnylevel" type="text" value="<?php echo $hnylevel; ?>"/>
          threat level. (25 threat level is average, threat level 5 is fairly low.)</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Deny spammers found on BotScout with more than
          <input size="3" name="botfreq" type="text" value="<?php echo $botfreq; ?>"/>
          incidents.</td>
      </tr>
    </table>
    <br/>
    <h4>Other Checks:</h4>
    <table align="center" cellspacing="1" style="background-color:#CCCCCC;font-size:.9em;">
      <tr bgcolor="white">
        <td valign="top">Block Spam missing the HTTP_ACCEPT header:</td>
        <td align="center" valign="top"><input name="accept" type="checkbox" value="Y" <?php if ($accept=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">Blocks users who have incomplete headers.</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Block with missing or invalid HTTP_REFERER:</td>
        <td align="center" valign="top"><input name="chkreferer" type="checkbox" value="Y" <?php if ($chkreferer=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">Blocks users who send form data, but the HTTP_REFERER does not match your domain.
      <tr bgcolor="white">
        <td valign="top">Check email address in addition to IP at StopForumSpam:</td>
        <td align="center" valign="top"><input name="chkemail" type="checkbox" value="Y" <?php if ($chkemail=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">Most spammers use random, faked or other people's email.
      <tr bgcolor="white">
        <td valign="top">Deny disposable email addresses:</td>
        <td align="center" valign="top"><input name="chkdisp" type="checkbox" value="Y" <?php if ($chkdisp=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">Some real comments might use disposable email, but probably not
      <tr bgcolor="white">
        <td valign="top">Check for long emails or author name:</td>
        <td align="center" valign="top"><input name="chklong" type="checkbox" value="Y" <?php if ($chklong=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">Spammers like to use long names and emails. This rejects these if the are over 64 characters in length (optional)</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Check for missing HTTP_USER_AGENT:</td>
        <td align="center" valign="top"><input name="chkagent" type="checkbox" value="Y" <?php if ($chkagent=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">Browsers always include a user agent string when they access a site. A missing user agent is usually a spammer using poorly written software or a leach who is stealing the pages from your site.
      <tr bgcolor="white">
        <td valign="top">Check session for quick responses (disabled if caching is active):</td>
        <td align="center" valign="top"><input name="chksession" type="checkbox" value="Y" <?php if ($chksession=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">Checks that the spammer is allowed to use a PHP session. If not, it denies the comment. The plugin puts a timer in the session and if the user fills the form in less than 6 seconds it is too quick to be human. (Stops the most spammers of all the methods listed here.)
      <tr bgcolor="white">
        <td valign="top">Use a Red Herring form:</td>
        <td align="center" valign="top"><input name="redherring" type="checkbox" value="Y" <?php if ($redherring=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">Places a fake comment form on web pages to trap spammers. If they bite, their IP address is added to the bad cache. Normal users should not be able to see the Red Herring form. Check your theme after enabling this feature to make sure that it does not alter your blog's presentation. (Very very good way to stop spammers.)
      <tr bgcolor="white">
        <td valign="top">Check against DNSBL lists such as Spamhaus.org:</td>
        <td align="center" valign="top"><input name="chkdnsbl" type="checkbox" value="Y" <?php if ($chkdnsbl=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">Primarily used for email spam, but might stop comment spam.
      <tr bgcolor="white">
        <td valign="top">Use JavaScript trap:</td>
        <td align="center" valign="top"><input name="chkjscript" type="checkbox" value="Y" <?php if ($chkjscript=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">Places a Javascript trap on comment forms. If a user has javascript turned off they will be denied access. Only paranoids and delusional users disable javascript.
      <tr bgcolor="white">
        <td valign="top">Blacklist searches for wp-login:</td>
        <td align="center" valign="top"><input name="chkwplogin" type="checkbox" value="Y" <?php if ($chkwplogin=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">When WordPress detects a 404 file not found for someone trying to find wp-login, this is someone probing your site to find your login so the IP is added to your bad IP cache. This is off by default.
      <tr bgcolor="white">
        <td valign="top">Check against list of Ubiquity-Nobis and other Spam Server IPs:</td>
        <td align="center" valign="top"><input name="chkubiquity" type="checkbox" value="Y" <?php if ($chkubiquity=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">Hosting companies who tolerate spammers are the source of much Comment Spam
      <tr bgcolor="white">
        <td valign="top">Check IP against the Akismet database:</td>
        <td align="center" valign="top"><input name="chkakismet" type="checkbox" value="Y" <?php if ($chkakismet=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">If the Akismet API key is set, then you may use Akismet to check logins or registrations, but not comments (optional)
      <tr bgcolor="white">
        <td valign="top">Automatically add admins to white list:</td>
        <td align="center" valign="top"><input name="addtowhitelist" type="checkbox" value="Y" <?php if ($addtowhitelist=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">Whenever an admin hits the options page his IP will be added to the white list. This prevents admins from being locked out. </td>
      </tr>
    </table>
    <br/>
    <h4>Lists:</h4>
    <table align="center" cellspacing="1" style="background-color:#CCCCCC;font-size:.9em;">
      <tr bgcolor="white">
        <td valign="top">White List:</td>
        <td align="center" valign="top"><textarea name="wlist" cols="40" rows="8"><?php 
    for ($k=0;$k<count($wlist);$k++) {
		echo $wlist[$k]."\r\n";
	}
	?>
</textarea></td>
        <td valign="top">put IP addresses or emails here that you don't want blocked. One email or IP to a line.</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Black List:</td>
        <td align="center" valign="top"><textarea name="blist" cols="40" rows="8"><?php
    for ($k=0;$k<count($blist);$k++) {
		echo $blist[$k]."\r\n";
	}
	?>
</textarea></td>
        <td valign="top">put IP addresses or emails here that want blocked. One email or IP to a line.</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top"> Blocked Email Domains:</td>
        <td align="center" valign="top"><textarea name="baddomains" cols="40" rows="8"><?php
    for ($k=0;$k<count($baddomains);$k++) {
		echo $baddomains[$k]."\r\n";
	}
	?>
</textarea></td>
        <td valign="top">Put the domains you want blocked here. e.g. dresssmall.com. This will block all comments and registrations that use this domain for emails.</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Check Spam Words:</td>
        <td valign="top"><input name="chkspamwords" type="checkbox" value="Y" <?php if ($chkspamwords=='Y') echo  "checked=\"checked\"";?>/></td>
        <td valign="top">Use the spam words list to check email and author fields. </td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Spam Words List:</td>
        <td valign="top"><textarea name="spamwords" cols="40" rows="8"><?php
    for ($k=0;$k<count($spamwords);$k++) {
		echo $spamwords[$k]."\r\n";
	}
	?>
</textarea></td>
        <td valign="top">If a word here shows up in an email address or author field then block the comment.</td>
      </tr>
    </table>
    <br/>
    <h4>Events to Check:</h4>
    You can specify which events to check. You may not want to check logins or registrations. You may wish to allow any comment and let Akismet handle things.
    <table align="center" cellspacing="1" style="background-color:#CCCCCC;font-size:.9em;">
      <tr bgcolor="white">
        <td valign="top">Check IP on wp-comment.php:</td>
        <td valign="top"><input name="chkcomments" type="checkbox" value="Y" <?php if ($chkcomments=='Y') echo  "checked=\"checked\"";?>/></td>
        <td valign="top">Check IP and email every time the wp-comments.php file is loaded.</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Check IP on wp-login.php:</td>
        <td valign="top"><input name="chklogin" type="checkbox" value="Y" <?php if ($chklogin=='Y') echo  "checked=\"checked\"";?>/></td>
        <td valign="top">Check IP and email every time the wp-login.php file is loaded.</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Check IP on wp-signup.php:</td>
        <td valign="top"><input name="chksignup" type="checkbox" value="Y" <?php if ($chksignup=='Y') echo  "checked=\"checked\"";?>/></td>
        <td valign="top">Check IP and email every time the wp-signup.php file is loaded.</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Check IP on xmlrpc.php:</td>
        <td valign="top"><input name="chkxmlrpc" type="checkbox" value="Y" <?php if ($chkxmlrpc=='Y') echo  "checked=\"checked\"";?>/></td>
        <td valign="top">Check IP and email every time the xmlrpc.php file is loaded. This will check ping backs and other remote calls</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Check IP on wp_mail:</td>
        <td valign="top"><input name="chkwpmail" type="checkbox" value="Y" <?php if ($chkwpmail=='Y') echo  "checked=\"checked\"";?>/></td>
        <td valign="top">Check IP whenever wordpress sends mail to prevent spammers from sending mail to you or anyone else.</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">
    </table>
    <br/>
    <h4>History and Cache Size:</h4>
    You can change the number of entries to keep in your history and cache. The size of these items is an issue and will cause problems with some WordPress installations. It is best to keep these small.<br/>
    <table align="center" cellspacing="1"  style="background-color:#CCCCCC;font-size:.9em;">
      <tr bgcolor="white">
        <td valign="top">Cache Size:</td>
        <td valign="top"><select name="kpg_sp_cache">
            <option value="10" <?php if ($kpg_sp_cache=='10') echo "selected=\"true\""; ?>>10</option>
            <option value="25" <?php if ($kpg_sp_cache=='25') echo "selected=\"true\""; ?>>25</option>
            <option value="50" <?php if ($kpg_sp_cache=='50') echo "selected=\"true\""; ?>>50</option>
            <option value="75" <?php if ($kpg_sp_cache=='75') echo "selected=\"true\""; ?>>75</option>
            <option value="100" <?php if ($kpg_sp_cache=='100') echo "selected=\"true\""; ?>>100</option>
          </select></td>
        <td valign="top">Select the number of items to save in the cache.</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">History Size:</td>
        <td valign="top"><select name="kpg_sp_hist">
            <option value="10" <?php if ($kpg_sp_hist=='10') echo "selected=\"true\""; ?>>10</option>
            <option value="25" <?php if ($kpg_sp_hist=='25') echo "selected=\"true\""; ?>>25</option>
            <option value="50" <?php if ($kpg_sp_hist=='50') echo "selected=\"true\""; ?>>50</option>
            <option value="75" <?php if ($kpg_sp_hist=='75') echo "selected=\"true\""; ?>>75</option>
            <option value="100" <?php if ($kpg_sp_hist=='100') echo "selected=\"true\""; ?>>100</option>
          </select></td>
        <td valign="top">Select the number of items to save in the History.</td>
      </tr>
    </table>
    <br/>
    <h4>Access Denied Message:</h4>
    <table align="center" cellspacing="1" style="background-color:#CCCCCC;font-size:.9em;">
      <tr bgcolor="white">
        <td valign="top">Spammer Message:</td>
        <td></td>
        <td valign="top"><textarea id="rejectmessage" name="rejectmessage" cols="64" rows="5"><?php echo $rejectmessage; ?></textarea></td>
        <td valign="top">This message is only visible to spammers. It only shows if spammers are rejected at the time login or comment form is displayed.</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Send spammer to another web page:</td>
        <td align="center" valign="top" width="20px"><input type="checkbox" name ="redir" value="Y" <?php if ($redir=='Y') echo "checked=\"checked\""; ?> >
        </td>
        <td valign="top"colspan="2">Check this to send spammers to the URL below.</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Redirect URL:</td>
        <td valign="top" colspan=3><input size="112" name="redirurl" type="text" value="<?php echo $redirurl; ?>"/></td>
      </tr>
      <tr bgcolor="white">
        <td valign="top" colspan="4">If you want you can send the spammer to a web page. This can be a custom page explaining terms of service, or a nasty message </td>
      </tr>
    </table>
    <br/>
    <p><strong>Remove &quot;Buy The Book&quot;:</strong>
      <input type="checkbox" name ="nobuy" value="Y" <?php if ($nobuy=='Y') echo "checked=\"checked\""; ?> >
      <br/>
      <?php 
		if ($nobuy=='Y')  {
			echo "Thanks";		
		} else {
		?>
      Check if you are tired of seeing the <a target="_blank" href="http://www.blogseye.com/buy-the-book/">Buy Keith's Book</a> links.
      <?php 
		}
	?>
    </p>
    <br/>
    <p class="submit">
      <input class="button-primary" value="Save Changes" type="submit">
    </p>
  </form>
  <p>&nbsp;</p>
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
