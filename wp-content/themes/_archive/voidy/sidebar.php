<?php
global $options, $option_values;

foreach ($options as $value) {
	if($value['id'] != "voidy_temp"){
	    if (empty($option_values[ $value['id']])) {
			$$value['id'] = $value['std'];
		} else {
			$$value['id'] = $option_values[ $value['id'] ]; 
		}
	}
}
?>
<div id="sidebar1" class="sidecol">
	<ul>
	 <?php if ($voidy_sidebar_text && $voidy_hide_sidebar_text != "true") { 
		echo "<li><p>".stripslashes($voidy_sidebar_text)."</p></li>";
	} ?>
	
	<?php if ($voidy_show_email && $voidy_show_email == "true") { ?>
	<li><form action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open('http://feedburner.google.com/fb/a/mailverify?uri=<?php echo $voidy_feedburner ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true">
	<p  style="padding: 0px;"><?php _e("Get updates by email", "voidy" ); ?></p>
		<input type="text" class="textbox with-button" name="email" value="Enter your email address"
		onblur="if (this.value == '') {this.value = 'Enter your email address';}"  
		onfocus="if (this.value == 'Enter your email address') {this.value = '';}" />
		<input type="hidden" value="<?php echo $voidy_feedburner ?>" name="uri"/>
		<input type="hidden" name="loc" value="en_US"/>
		<input type="submit" value="Go" class="go" />
	</form></li>
	<?php } ?>
<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar(1) ) : else : ?>
<li>
	<h2><?php _e('Latest Posts', "voidy" ); ?></h2>
	<ul><?php wp_get_archives("type=postbypost&limit=6")?></ul>
</li>
<li>
    <h2><?php _e('Feed on', "voidy" ); ?></h2>
    <ul>
      <li class="feed"><a title="RSS Feed of Posts" href="<?php bloginfo('rss2_url'); ?>"><?php _e('Posts RSS', "voidy" ); ?></a></li>
      <li class="feed"><a title="RSS Feed of Comments" href="<?php bloginfo('comments_rss2_url'); ?>"><?php _e('Comments RSS', "voidy" ); ?></a></li>
    </ul>
  </li>
<li>
	<?php $search_text = __("Search this site", "voidy" ); ?> 
	<form method="get" id="searchform" action="<?php bloginfo('url'); ?>/"> 
	<input type="text" value="<?php echo $search_text; ?>"  
		name="s" id="s"  class="with-button"
		onblur="if (this.value == '')  
		{this.value = '<?php echo $search_text; ?>';}"  
		onfocus="if (this.value == '<?php echo $search_text; ?>')  
		{this.value = '';}" /> 
		<input type="submit" value="Go" class="go" />
	<input type="hidden" id="searchsubmit" /> 

	</form>
  </li>
<!--  
<?php wp_list_bookmarks(); ?>
-->
  <li>
    <h2>
      <?php _e('Categories', "voidy" ); ?>
    </h2>
    <ul>
      <?php wp_list_categories('title_li=');    ?>
    </ul>
  </li>
  <li>
    <h2>
      <?php _e('Monthly', "voidy" ); ?>
    </h2>
    <ul>
      <?php wp_get_archives('type=monthly&show_post_count=true'); ?>
    </ul>
  </li>
  <li>
    <h2><?php _e('Pages', "voidy" ); ?></h2>
    <ul>
      <?php wp_list_pages('title_li=' ); ?>
    </ul>
  </li>
  <!--
	<li>
      <h2>Meta</h2>
		<ul>
			<?php wp_register(); ?>
			<li><?php wp_loginout(); ?></li>
			<li><a href="http://validator.w3.org/check/referer" title="This page validates as XHTML 1.0 Transitional">Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr></a></li>
			<li><a href="http://gmpg.org/xfn/"><abbr title="XHTML Friends Network">XFN</abbr></a></li>
			<li><a href="http://wordpress.org/" title="Powered by WordPress, state-of-the-art semantic personal publishing platform.">WordPress</a></li>
			<?php wp_meta(); ?>
		</ul>			
   </li>
   -->
    <?php endif; ?>
</ul>
</div>