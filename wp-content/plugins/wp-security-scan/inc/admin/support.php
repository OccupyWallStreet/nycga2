<?php
function mrt_sub2()
{
	mrt_wpss_menu_head('WP - Security Support');
	?><?php /*
<div>
    <br/>
	<p>Under Construction...</p>
    <br /><br />
	<ul>
		<li><a href='http://www.websitedefender.com/category/faq/' target="_blank">Documentation</a></li>
	</ul>
	<br /><br />
	<strong>Backup early, backup often!</strong>
	<br /><br /><br /><br /><br />
</div>
   */?>

          <div class="metabox-holder">
              <div class="postbox">
                  <h3 class="hndle"><span><?php echo __('About WebsiteDefender');?></span></h3>
                  <div class="inside">
<p><?php echo __('A secure website, free from malware, where your customers can feel safe is vital to your online success.
    Unfortunately, the number of web hacking attacks has risen dramatically. Website security is an absolute must. 
    If you do not protect your website, hackers can gain access to your website, modify your web content, install malware 
    and have your site banned from Google. They could modify scripts and gain access to your customer data and their credit card details…');?></p>

<p><?php echo __('WebsiteDefender is an online service that monitors your website for hacker activity, audits the security 
    of your web site and gives you easy to understand solutions to keep your website safe. With WebsiteDefender you can:');?></p>

<ul class="wsd_info_list">
    <li><?php echo __('Detect Malware present on your website');?></li>
    <li><?php echo __('Audit your web site for security issues');?></li>
    <li><?php echo __('Avoid getting blacklisted by Google');?></li>
    <li><?php echo __('Keep your web site content &amp; data safe');?></li>
    <li><?php echo __('Get alerted to suspicious hacker activity');?></li>
</ul>

<p><?php echo __('All via an easy-to-understand web based dashboard which gives step by step solutions!
    Sign up for your FREE account <a href="admin.php?page=wp-security-scan/securityscan.php">here</a>.');?></p>
                  </div>
              </div>
          </div>


          <div class="metabox-holder">
              <div class="postbox">
                  <h3 class="hndle"><span><?php echo __('Get Involved!');?></span></h3>
                  <div class="inside">
                      <p></p>
<ul class="wsd_info_list">
    <li>
        <span><a href="http://www.websitedefender.com/forums/" target="_blank"><?php echo __('WebsiteDefender forums');?></a></span>
    </li>
    <li>
        <span><a href="http://www.websitedefender.com/blog/" target="_blank"><?php echo __('WebsiteDefender blog');?></a></span>
    </li>
    <li>
        <span><a href="http://twitter.com/#!/websitedefender" target="_blank"><?php echo __('WebsiteDefender on Twitter');?></a></span>
    </li>
    <li>
        <span><a href="http://www.facebook.com/WebsiteDefender" target="_blank"><?php echo __('WebsiteDefender on Facebook');?></a></span>
    </li>
</ul>
    <p></p>
                  </div>
              </div>
          </div>


<?php 
	mrt_wpss_menu_footer();
}
?>