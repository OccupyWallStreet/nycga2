<?php


function wp_rss_multi_importer_template_page(){
   ?>	
	   <div class="wrap">
	<div id="poststuff">
<div class="postbox"><h3><label for="title">How to Use Templates</label></h3>

<div class="inside"><p>Many people have asked about styling their own RSS feed layouts on their sites.  While I've tried to provide many ways to do this, the first way is for me to construct various templates..which I've done and are now available in the pull-down menu on the Options Settings panel.  If you don't want to mess with other templates, just use the default template (called DEFAULT).</p>
	
<p>Some kind users have shown me other nice layouts, and so I've included those in the templates folder and are available in the pull down menu.  You might give those a try.</p>

<p><a href="http://templates.allenweiss.com" target="_blank">Go here to see what various templates look like</a>.</p>

<p>First, if you know some CSS you can change the styles of all the templates..and then save the CSS for use when the next update of the plugin happens.</p>

<p>To change the CSS, you can either FTP to your server, or an easier way is to edit the plugin's files...<a href="http://www.youtube.com/watch?v=H5HXzIBPD80" target="_blank">watch this video to see how to do this</a></p>


<p>Even more, if you know a bit of PHP coding, you can go in and make your own template.  All the templates are in the folder called Templates. I've included a file (called example.txt) that shows the foundation php code you must use with all templates.  Look through the other templates and you'll see other options you can include.</p>

<p>Also, now if you are using a template that you've changed or customized, you can save it.  Just choose a name for the template below, like (My great template), and hit save.  Then, when the plugin gets it's next update (that overwrites all the files) you can come back to this page, hit Restore, and your template will be available for use again.</p>
	
	
<p>Thank you.<br>Allen</p>
	
	</div></div></div></div>
<h3>Save Your Template</h3>
<?php
$options = get_option( 'rss_import_options' ); 
$thistemplate=$options['template'];
save_template_function($thistemplate);

}



function wp_rss_multi_importer_style_tags(){
   ?>	
	   <div class="wrap">
	<div id="poststuff">


<?php    echo '<div class="postbox"><h3><label for="title">Shortcode Parameters</label></h3><div class="inside"><h2>Customize some of the ways the feeds are presented on your page by using shortcode parameters.  Here are some examples:</h2>';
?>

<table class="widefat">
<tr><th>FEATURE CHANGE</th><th>PARAMETER</th><th>DEFAULT</th><th>EXAMPLE</th></tr>
<tr class="alternate"><td >Headline font size</td><td>hdsize</td><td>16px</td><td>[wp_rss_multi_importer hdsize="18px"]</td></tr>	
<tr><td >Headline bold weight</td><td>hdweight</td><td>400</td><td>[wp_rss_multi_importer hdweight="500"]</td></tr>		
<tr class="alternate"><td >Style of the Today and Earlier tags</td><td>testyle</td><td>color: #000000; font-weight: bold;margin: 0 0 0.8125em;</td><td>[wp_rss_multi_importer testyle="color:#cccccc"]</td></tr>	
<tr ><td>If using excerpt, symbol or word you want to indicate More..</td><td>morestyle</td><td>[...]</td><td>[wp_rss_multi_importer morestyle="more >>"]</td></tr>	
<tr class="alternate"><td >Change the width of the maximum image size</td><td>maximgwidth</td><td>150</td><td>[wp_rss_multi_importer maximgwidth="160"]</td></tr>	
<tr ><td >Change the style of the date</td><td>datestyle</td><td>font-style:italic;</td><td>[wp_rss_multi_importer datestyle="font-style:bold;"]</td></tr>	
<tr class="alternate"><td >Change how images float on a page</td><td>floattype</td><td>set by default to whatever is set in the admin options</td><td>[wp_rss_multi_importer floattype="right"]</td></tr>	
<tr ><td >Change whether the date shows or not</td><td>showdate</td><td>set to 0 to suppress the date</td><td>[wp_rss_multi_importer showdate="0"]</td></tr>	
<tr class="alternate"><td >Change whether the attribution shows or not (e.g., news source) </td><td>showgroup</td><td>set to 0 to suppress the source affiliation</td><td>[wp_rss_multi_importer showgroup="0"]</td></tr>	
<tr class="alternate"><td >Specify the cache time (to override global setting)</td><td>cachetime</td><td>set in settings option</td><td>[wp_rss_multi_importer cachetime="20"]</td></tr>

<tr ><td >Specific the number of posts per feed instead of using the general number in the settings panel</td><td>thisfeed</td><td>set to a number, as in thisfeed="4"</td><td>[wp_rss_multi_importer thisfeed="5"]</td></tr>	



	
</table>

<p>You can use combinations of parameters, too.  So, if you'd like to change the headline font size to 18px and make it a heavier bold and change the more in the excerpt to >>, just do this:   [wp_rss_multi_importer hdsize="18px" hdweight="500" morestyle=">>"] </p>
<p>If setting the style of the Today and Earlier tags, you need to enter the entire inline css - so be careful.</p>
	
<?php
echo '</div></div></div></div>';	

}




function wp_rss_multi_importer_more_page(){
   ?>	
	   <div class="wrap">
	<div id="poststuff">


<?php    echo '<div class="postbox"><h3><label for="title">Help Us Help You</label></h3><div class="inside"><p>Hi All<br>In an attempt to increase the functionality of this plugin, let me know if you have any feature requests by <a href="http://www.allenweiss.com/wp_plugin" target="_blank">going here.</a> where you can also get support.</p>';
	
	echo '<p>If you\'d like to support the development and maintenance of this plugin, you can do so by <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=M6GC7V8BARAJL" target="_blank">donating here.</a></p>';

echo '<p>If you find this plugin helpful, let others know by <a href="http://wordpress.org/extend/plugins/wp-rss-multi-importer/" target="_blank">rating it here</a>.  That way, it will help others determine whether or not they should try out the plugin.  Thank you.<br>Allen</p></div></div></div></div>';	

}