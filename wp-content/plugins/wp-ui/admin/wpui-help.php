<?php
/**
 * @TODO Remove this fn in the next version. Too much messy.
 */
function get_plgn_url() {
	$plgn_url = 'http';
	if ( isset( $_SERVER[ 'HTTPS'] ) && $_SERVER['HTTPS'] == 'on' )
		$plgn_url .= "s";
	$plgn_url .= "://";
	$script_url = str_ireplace('/'. basename(__FILE__), '', $_SERVER['REQUEST_URI']);
	if ( $_SERVER['SERVER_PORT'] != '80' ) {
		$plgn_url .= $_SERVER["SERVER_NAME"].":".$_SERVER['SERVER_PORT'].$script_url;
	} else {
		$plgn_url .= $_SERVER['SERVER_NAME'] . $script_url;
	}
	$plgn_url = str_ireplace( 'admin', '', $plgn_url );
	return $plgn_url;	
} // END function get_page_url.

function get_site_url() {
	$plgn_url = get_plgn_url();
	$plgn_url = str_ireplace( 'wp-content/plugins/wp-ui/', '', $plgn_url );
	return $plgn_url;	
}

// if ( isset( $_GET[ 'page' ] ) )

$section = 'reference';

if ( isset( $_GET ) && isset( $_GET[ 'section' ] ) )
	$section = $_GET[ 'section' ];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>WP UI for WordPress - Documentation</title>

</head>
<body>
<div id="wrapper">
<h2 class="page-title">WP UI - Docs</h2>
<div id="container">
	
<!-- change content as necessary -->	
	
	
<p>Welcome to WP UI for wordpress. WP UI widgets can be implemented with shortcodes as well as templates. For getting the shortcodes work straight away without any trouble, please consider the following suggestions.</p>
<p class="information" style="text-align: center;"><b><a href="http://kav.in/projects/blog/wp-ui-quick-start/" target="_blank" rel="nofollow">Click here</a> to read the WP UI Quick Start guide. </b></p>

<h2>Reference</h2>

<div class="information">
<ol>
	<li>Keep different shortcodes - e.g. wptabtitle and wptabcontent in separate lines.</li>
	<li>Avoid empty lines in between the shortcodes.</li>
	<li>It is better and easier to enter the shortcodes after you are finished with the content. </li>
</div>
</ol>
<!-- <div class="roguelist">
<h3 class="toggler">TOC</h3>
<ul>
	<li><a class="slide-to-id" href="#shortcode_basics" >Shortcode Basics</a></li>
	<li><a class="slide-to-id" href="#example-structure" >Examples shortcodes</a></li>
	<li>
		<ul>
			<li><a class="slide-to-id" href="#wptabs_args" >[wptabs] shortcode arguments</a></li>
			<li><a class="slide-to-id" href="#wptabtitle_args" >[wptabtitle] shortcode arguments</a></li>
			<li><a class="slide-to-id" href="#nested_tabs" >Nested Tabs</a></li>
		</ul>
	</li>
</ul>
</div> -->
<p>WP UI editor buttons are available for both the HTML mode editor and the Visual mode editor(TinyMCE)</p>

<div class="wp-tabs wpui-dark wpui-no-background">
<h3 class="wp-tab-title">Visual</h3>
<div class="wp-tab-content">	
<div class="kav-caption dark" style="width:412px"> <img class="wp-kav-image" src="<?php echo get_plgn_url() ?>/images/editor/tinymce.png" width="400" height="250" alt="WP UI - TinyMCE buttons" />
<p class="kav-caption-text" align="center"><strong>Image 1.1</strong> WP UI -Visual mode Editor Menu</p></div></div>

	
<h3 class="wp-tab-title">HTML Editor</h3>
<div class="wp-tab-content">
<div class="kav-caption dark" style="width:412px"><img class="wp-kav-image" style="border-right: 0px;" src="<?php echo get_plgn_url() ?>/images/editor/quicktags.png" width="400" height="250" alt="WPUI - HTML editor buttons" />
<p class="kav-caption-text" align="center"><strong>Image 1.0 </strong>WP UI - HTML mode Editor buttons</p></div><br />
</div>

</div><!-- End .wp-tabs -->



<hr />
<p align="left">&nbsp;</p>
<div id="shortcode_basics">
<h3 align="left" id="example-structure">Example shortcode structures</h3>
<div class="wp-tabs wpui-quark wpui-no-background">
<h3 class="wp-tab-title">Tabs</h3>
<div class="wp-tab-content">
<pre class="dark-pre">
[wptabs style="wpui-quark"]
  [wptabtitle]Tab 1[/wptabtitle]
    [wptabcontent] Contents of the Tab 1 [/wptabcontent]
  [wptabtitle]Tab 2[/wptabtitle]
    [wptabcontent] Contents of the Tab 2 [/wptabcontent]
  [wptabtitle]Tab 3[/wptabtitle]
    [wptabcontent] Contents of the Tab 3 [/wptabcontent]
[/wptabs]	
</pre>
</div>
<h3 class="wp-tab-title">Accordion</h3>
<div class="wp-tab-content">
<p>Notice the argument <code>type="accordion"</code>, that being the main difference against the tabs.</p>
<pre class="dark-pre">
[wptabs type="accordion" style="wpui-dark"]
[wptabtitle]Tab 1[/wptabtitle]
  [wptabcontent] Contents of the Tab 1 [/wptabcontent]
[wptabtitle]Tab 2[/wptabtitle]
  [wptabcontent] Contents of the Tab 2 [/wptabcontent]
[wptabtitle]Tab 3[/wptabtitle]
  [wptabcontent] Contents of the Tab 3 [/wptabcontent]
[/wptabs]	
</pre>	
</div>
<h3 class="wp-tab-title">Spoilers</h3>
<div class="wp-tab-content">
<pre class="light-pre">
[wpspoiler name="Fancy Slider"] Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur euismod, elit non tempor ornare.... [/wpspoiler]
</pre>	</div>
<h3 class="wp-tab-title">Dialogs</h3>
<div class="wp-tab-content">
<pre class="light-pre">	
[wpdialog title="The second dialog" hide="slide"] Text inside or what you want to show inside the dialog..  [/wpdialog]
</pre>
</div>
</div>



<div class="wp-spoiler wpui-sevin">
<h3 class="wp-spoiler-title">Tabs and accordions</h3>
<div class="wp-spoiler-content"><div id="tab_name">
<h3>Step 1 . Tab's name - Shortcode : [wptabtitle]</h3>
<p>You can use the shortcode [wptabtitle] to define the tab's name. This is the name that is displayed on the clickable tab.</p>
<p>Example follows:</p>
<pre>[wptabtitle]Tab 1[/wptabtitle]</pre>
<p>This assigns the tab's name as "Tab 1" and assigns the content or following [wptabcontent] to this tab.</p>
<p>&nbsp;</p>
</div><!-- end #tab_name -->
<div id="tab_content">
<h3>Step 2 . Tab content - Shortcode [wptabcontent]</h3>
<p>You can use the tab's content with the buttons labeled Tab Contents( Visual and HTML ). Refer to image 1.0 and 1.1 - labelled ( 2 ).</p>
<p>[wptabcontent] shortcode is used to define the content of the tab's panel - one that is displayed when clicking the tab.</p>
<p>Example : </p>
<pre>[wptabcontent]Contents of the Tab 1. Remember we defined a wptabtitle shortcode before with a same name? And all the other awesome, cool stuff that i'd love to explain about! [/wptabcontent]</pre>
<p>wptabtitle and wptabcontent shortcodes act as a pair, you can define as many as necessary. Think of this as a file and wptabtitle as a tag.</p>
<p>&nbsp;</p>
<h3>Repeat :)</h3>
<p>Now for each additional tab, repeat the steps 1 and 2. Most of the times it is easier to use shortcodes. </p>
<p>&nbsp;</p>
</div><!-- end #tab_contents -->

<div id="tabs_wrap">
<h3>Step 3 - Final - Wrapping</h3>
<p>Once you are done with the tabset( wptabtitle and wptabcontent shortcodes), finally you can wrap it with the [wptabs] shortcode. Refer to ( 3 ) in Images 1.0 and 1.1 .</p>
<p>It is as easy as the previous ones. </p>
<pre align="left">
[wptabs]
 [wptabtitle]First Tab[/wptabtitle]
   [wptabcontent] Contents of the first tab. [/wptabcontent]
 [wptabtitle]Second Tab[/wptabtitle]
   [wptabcontent]Content of the second tab. Roughly parallel another content[/wptabcontent]
[/wptabs]
</pre>
<p>Wow! There is your tabset, ready to rock! Now please save the post, and view it on the blog!</p>
<h3>Where are my accordions?</h3>
<p>Accordions and tabs share the same shortcode structure. the argument - <code>type="accordion" </code> to wptabs initializes the accordion. </p>
<pre class="dark-pre">[wptabs type="accordion"] ... rest same as tabs. </pre>
</div><!-- end #tabs_wrap -->
<a class="close-spoiler ui-button ui-corner-all" href="#">Close this!</a>
</div><!-- .ui-coll-content tabs and accordions -->
</div><!-- end wpspoiler-->


<hr />

<h2>Advanced</h2>

<p>Arguments for the shortcodes</p>

<div class="wp-spoiler wpui-sevin">
<h3 class="wp-spoiler-title">Arguments for tabs and accordion shortcodes</h3>
<div class="wp-spoiler-content">
	<table>
		<caption><strong>[<span>wptabtitle</span>]</strong></caption>
		<thead>
			<tr>
				<th>Arguments</th>
				<th>Values</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>load</td>
				<td>URL to the page your want to load through AJAX.</td>
			</tr>
			<tr>
				<td>post</td>
				<td>ID of the post you want to load into the tab</td>
			</tr>
			<tr>
				<td>page</td>
				<td>ID or name of the page you want to load into the tab.</td>
			</tr>
			<tr>
				<td>cat</td>
				<td>ID or name of the category to load posts from.</td>
			</tr>
			<tr>
				<td>tag</td>
				<td>Name or ID of the Tag to load posts from.</td>
			</tr>
			<tr>
				<td>number</td>
				<td>Number of posts to load from the category or tag, if given.</td>
			</tr>
			<tr>
				<td>before_post</td>
				<td>This appears before the post</td>
			</tr>
			<tr>
				<td>after_post</td>
				<td>This appears after the post</td>
			</tr>
		</tbody>
	</table>		



			<table>
				<caption><strong>[<span>wptabcontent</span>]</strong></caption>
				<thead>
					<tr>
						<th>Arguments</th>
						<th>Values</th>
					</tr>
				</thead>
				<tbody>
					<tr>		
			<!-- wptabcontent shortcode -->
				<td>none yet</td>
				<td>wptabcontent shortcode handles the tab's contents should follow [<span>wptabtitle</span>] shortcode, <em>except when the latter is used with post related arguments</em>, <code>post</code>, <code>page</code>, <code>cat</code>, <code>tag</code>.</td>
			</tr>		


				</tbody>
			</table>



			<table>
				<caption><strong>[<span>wptabs</span>]</strong></caption>
				<thead>
					<tr>
						<th>Arguments</th>
						<th>Values</th>
					</tr>
				</thead>
				<tbody>

			<tr>
				<td>type</td>
				<td>Tabs or accordion. Choose type="accordion".</td>
			</tr>
			<tr>
				<td>style</td>
				<td>Any of the accepted stylename values, given just below the table.<br /><code>[<span>wptabs style="wpui-achu"</span>]</code></td>
			</tr>
			<tr>
				<td>mode</td>
				<td>Define mode="vertical" for vertically oriented tabs.</td>
			</tr>		

			<tr>
				<td>effect</td>
				<td>Effect to be used with the tabs. Accepted values are "fade" or "slide". <br /><code>[<span>wptabs effect="fade"</span>]</code></td>
			</tr>
			<tr>
				<td>style</td>
				<td>Any of the accepted stylename values, given just below the table.<br /><code>[<span>wptabs style="wpui-achu"</span>]</code></td>
			</tr>
			<tr>
				<td>rotate</td>
				<td>Tabs auto rotation. It's value need to be in microseconds eg:4000 or 4s ( 4 seconds ). <br /><code> [<span>wptabs rotate="6000"</span>] </code> is same as <br /> <code> [<span>wptabs rotate="6s"</span>]  </code></td>
			</tr>
			<tr>
				<td>Position</td>
				<td>Position of the tabs. <code>position="bottom"</code> moves the tabs to the bottom</td>
			</tr>


		</tbody>
	</table>	
	
	
</div>
</div>
<div class="wp-spoiler wpui-sevin">
<h3 class="wp-spoiler-title">Arguments related to the post functionality</h3>
<div class="wp-spoiler-content">
	<table border="0">
		<caption><strong>[<span>wptabposts</span>]</strong> Tabs with multiple posts.</caption>
		<thead>
		<tr>
			<th>Arguments</th>
			<th>Value</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td>All arguments</td>
			<td>This shortcode is basically the <code>[<span>wptabs</span>]</code> shortcode, so all the options apply.</td>
		</tr>
		</tbody>
	</table>
</div>
</div>
<div class="wp-spoiler wpui-sevin">
<h3 class="wp-spoiler-title">Arguments related to spoiler</h3>
<div class="wp-spoiler-content">
	<table border="0">
		<caption><strong>[<span>wpspoiler</span>]</strong> Spoilers (single) | Collapsible (multiple) </caption>
		<thead>
		<tr>
			<th>Arguments</th>
			<th>values</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td>name</td>
			<td>Name/Title of the spoiler.  </td>
		</tr>
		<tr>
			<td>style</td>
			<td>Any of the accepted stylename values, given just below.<br /><code>[<span>wptabs style="wpui-achu"</span>]</code></td>
		</tr>	
		<tr>
			<td>fade</td>
			<td>Fade(animate opacity) on open/close. <br /><code>[<span>wpspoiler fade="true"</span>]</code></td>
		</tr>	
		<tr>
			<td>slide</td>
			<td>Slide on open/close. <br /><code>[<span>wpspoiler fade="true"</span>]</code></td>
		</tr>	
		<tr>
			<td>speed</td>
			<td>Animation speed in milliseconds. Greater the value, slower the animation.</td>
		</tr>
		<tr>
			<td>closebtn</td>
			<td>Inserts a close button at end of the spoiler, with value as the label.<br /> <code> [<span>wpspoiler closebtn="Click to close me"</span>]</code> </td>
		</tr>	
		<tr>
			<td>showText</td>
			<td>Text or HTML show on the closed spoiler i.e when content is hidden. <br /> <code> [<span>wpspoiler showText="Click to show"</span>] </code></td>
		</tr>
		<tr>
			<td>hideText</td>
			<td>Text or HTML show on the open spoiler i.e when content is visible. <br /> <code> [<span>wpspoiler hideText="Click to hide"</span>] </code></td>
		</tr>
		<tr>
			<td>open</td>
			<td>When this is set to true, Spoiler is open ( the content is visible ) at page load.</code></td>
		</tr>
		<tr>
			<td>post</td>
			<td>ID of the post. This is loaded into the spoiler. When a post is specified, the post title is used as the name Argument. <br /> <code> [<span>wpspoiler post="1171"</span>] </code></td>
		</tr>
		<tr>
			<td>before_post</td>
			<td>This appears before the post</td>
		</tr>
		<tr>
			<td>after_post</td>
			<td>This appears after the post</td>
		</tr>

		</tbody>
	</table>	
</div>
</div>
<div class="wp-spoiler wpui-sevin">
<h3 class="wp-spoiler-title">Arguments related to dialogs</h3>
<div class="wp-spoiler-content">
	<table border="0">
		<caption><strong>[<span>wpdialog</span>]</strong>Dialogs</caption>
		<thead>
		<tr>
			<th>Arguments</th>
			<th>Values</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td>title</td>
			<td>Title of the dialog.<code>[<span>wpdialog title="Information regarding Unicorns"</span>]</code></td>
		</tr>

		<tr>
			<td>width</td>
			<td>Width of the dialog, <b>without the suffixing px value</b>.<br /> <code>[<span>wpdialog width="600"</span>] </code></td>
		</tr>
		<tr>
			<td>show</td>
			<td>Open animation. <br /> <code> [<span>wpdialog show="drop"</span>]</code></td>
		</tr>
		<tr>
			<td>hide</td>
			<td>Animation when dialog is closed. <br /> <code> [<span>wpdialog hide="explode"</span>] </code> <a href="http://docs.jquery.com/UI/Effects">Click here for the values</a>.</td>
		</tr>		

		<tr>
			<td>modal</td>
			<td> <code> [<span>wpdialog modal="true"</span>]</code> makes an black transparent overlay appear.</td>
		</tr>
		<tr>
			<td>auto_open</td>
			<td>When set to <code>false</code>, dialog is not opened at page load. Instead a button is placed that can be clicked at any time to open the dialog.</td>
		</tr>

		<tr>
			<td>openlabel</td>
			<td>Can only be used with the above option, <code>auto_open</code>. Defines the label of the button.</td>
		<tr>
			<td>position</td>
			<td>Position of the dialog. <br /> <code> [<span>wpdialog position="bottom"</span>]</code>. Accepted values <code>top</code>, <code>bottom</code>, <code>left</code>, <code>right</code></td>
		</tr>
		<tr>
			<td>post</td>
			<td>ID of the post that is to be loaded into the dialog. <br /> <code> [<span>wpdialog post="1175"</span>]</code></td>
		</tr>

		<tr>
			<td>openlabel</td>
			<td>Can only be used with the above option, <code>auto_open</code>. Defines the label of the button.</td>
		<tr>
			<td>before_post</td>
			<td>This appears before the post</td>
		</tr>
		<tr>
			<td>after_post</td>
			<td>This appears after the post</td>
		</tr>	

		</tbody>
	</table>	
</div>
</div>
<!-- <div class="wp-spoiler">
<h3 class="ui-collapsible-header"></h3>
<div class="ui-collapsible-content">
	
</div>
</div> -->







<hr />
<p>&nbsp;</p>
<div id="nested_tabs">
<h3 id="nested_tabs">Nested tabs</h3>
<p>If you wish to have nested tabs (tabs within a tab), you have to use the following markup. This is rather a limitation of the wordpress shortcodes and not of this plugin.</p>
<pre>&lt;div class=&quot;wp-tabs wpui-quark&quot;&gt;<br />
	&lt;h3 class=&quot;wp-tab-title&quot;&gt;First Nested Tab&lt;/h3&gt;&lt;br&gt;<br />		&lt;div class=&quot;wp-tab-content&quot;&gt; Contents of the nested first tab.
		&lt;/div&gt;&lt;!-- end div.wp-tab-content --&gt;<br />	
	&lt;h3 class=&quot;wp-tab-title&quot;&gt;Second Nested Tab&lt;/h3&gt;<br />		&lt;div class=&quot;wp-tab-content&quot;&gt;Content of the nested second tab. &lt;/div&gt;&lt;!-- end div.wp-tab-content --&gt;
<br />&lt;/div&gt;</pre>
<p>This enables the use of nested tabs.</p>
</div><!-- end #nested_tabs -->





<!-- end of .container -->
</div>

</body>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js"></script>
<script type='text/javascript'>
/* <![CDATA[ */
var wpUIOpts = {
	enableTabs: "on",
	enableAccordion: "on",
	enableSpoilers: "on",
	enableDialogs: "on",
	enablePagination: "on",
	tabsEffect: "none",
	effectSpeed: "400",
	accordEffect: "none",
	alwaysRotate: "stop",
	tabsEvent: "click",
	collapsibleTabs: "",
	accordEvent: "click",
	topNav: "",
	accordAutoHeight: "on",
	accordCollapsible: "",
	accordEasing: "false",
	mouseWheelTabs: "false",
	bottomNav: "",
	tabPrevText: "Prev",
	tabNextText: "Next",
	spoilerShowText: "Click to show",
	spoilerHideText: "Click to hide",
	cookies: "on",
	hashChange: "on",
	docWriteFix: "on"
};
/* ]]> */
</script>
<script type="text/javascript" src="<?php echo get_plgn_url() ?>/js/wp-ui.js"></script>
<script type='text/javascript'>
/* <![CDATA[ */
var initOpts = {
	wpUrl: "<?php echo get_site_url() ?>",
	pluginUrl: "<?php echo get_plgn_url() ?>"
};
/* ]]> */
</script>

<link rel="stylesheet" href="<?php echo get_plgn_url() ?>/wp-ui.css" type="text/css">
<link rel="stylesheet" href="<?php echo get_plgn_url() ?>/css/wpui-all.css" type="text/css">

<script type="text/javascript">

jQuery(document).ready(function() {
	// jQuery('pre').wpuihilite();
});
</script>
<style type="text/css">
#zero-hider {
	display : none !important;
}
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
}
body {
	background-color: #FFFFFD;
	margin: 0;
	padding: 0;
}
#container {
	width: 600px;
	padding: 10px;
}
a {
	color : #006699;
}
p {
	line-height: 1.5em;
}
h3 {
	color: #006699;
}
.page-title {
	background: #444;
/*	background : -webkit-linear-gradient( left, #A1216E, #D8164A, #F82913, #F98F17, #FBCC0C, #EEE810, #79C53E, #4BC574, #5FCDC2, #5B3C9C, #A8185F );
	background : -moz-linear-gradient( left, #A1216E, #D8164A, #F82913, #F98F17, #FBCC0C, #EEE810, #79C53E, #4BC574, #5FCDC2, #5B3C9C, #A8185F );
	background : -o-linear-gradient( left, #A1216E, #D8164A, #F82913, #F98F17, #FBCC0C, #EEE810, #79C53E, #4BC574, #5FCDC2, #5B3C9C, #A8185F );*/
	background : url("<?php echo get_plgn_url(); ?>/images/cpattern.png" );
	border-bottom: 1px solid #FFF;	
	color: #000;
	text-shadow : 1px 1px 0 #999;
	padding: 10px;
	margin-top: 0px;
	box-shadow: 0 -2px 3px #444 inset;
}
.wp-kav-image {
	border: 6px solid #222;
}
.wpui-image-black-border {
	border: 6px solid #222;
}
ul, ol {
	padding-left: 1.5em;
	margin-left : 1.5em;	
}

ul li, ol li {
	margin-top: 5px;
	margin-bottom: 5px;	
}

.kav-caption {
	background: #FFF;
	display: block;
	border: 1px solid #AAA;
	box-shadow: 0 2px 5px #999;
	margin: 0 auto;
}
.kav-caption-text {
	text-align:center;
	color: #000;
	margin:0px auto 10px auto;
}

.dark {
	background: #222;
	background : -webkit-linear-gradient( top, #666 20%, #444 25%, #333 90%, #000);
	color: #999;
	border: 1px solid #DDD;
}
.dark p {
	color: #999;
}

.ui-tabs-panel .dark-pre {
	background: #0F192A;
	color : inherit !important;
	color: #DDD !important;
}

.light-pre {
	background: #EDEDED;
	color: #222;
}
pre {
	background: #000;
	color: #FFF;
	overflow: scroll;
	padding: 5px;
}
div.roguelist {
    background: none repeat scroll 0 0 #222222;
    color: #FFFFFF;
    position: fixed;
    right: 0;
	width: 300px;
}
div.roguelist h3 {
	text-align:center;
	-moz-transform : rotate(90deg);
	-webkit-transform : rotate(90deg);
	float: left;
}
div.roguelist ul {
	margin-left:35px;
}

div.roguelist ul li > ul {
	margin-left: 10px;
}
.enclosers {
	color : #B43D3D !important;
}

.dark-pre .argsv {
	color: skyblue !important;
}
pre .values {
	color: #1DC116 !important;
}
pre .arguments {
	color: #F8BB00 !important;
}
.light-pre .argsc {
	color: #000 !important;
}
.melort {
	color : #0066CC;
}
.information {
	background : lightyellow;
	padding : 10px;
	border-radius : 10px;
	box-shadow : 3px 3px 3px #DDD;
}
ul.ui-tabs-nav li {
	margin : 0;
}

table {
	background: #F7FBFF;
	border: 1px solid white;
	text-shadow: 0 1px 0 white;
	width: 100%;
	border-collapse : collapse;
	border-width : 1px;

}

table caption {
/*	background :#EEE;*/
/*	border: 2px solid #DEDEDE;*/
	padding : 10px;
	clear: both;
}

table tbody tr.odd-table {
	background: #FFF;
}

table thead tr th,
table thead tr td {
	background : #D4F4FF;
	padding : 5px;
	text-align : center;
/*	border-bottom : 1px solid #CCC;*/
	border-top : 1px solid #FFF;
	color : #678197;
	text-shadow : 0 2px 2px #FFF;
}

table tbody tr td {
	padding : 10px;
	
	
}


table tbody td code {
	background : #FFF;
	border: 1px solid #EEE;
	padding : 2px;
	-moz-border-radius     : 2px;
	-webkit-border-radius  : 2px;
	-o-border-radius       : 2px;
	border-radius          : 2px;
}
</style>

<script type="text/javascript">
window.onload = synHilite();
function synHilite() {
	precont = document.getElementsByTagName('pre');
	for ( i = 0; i < precont.length; i++ ) {
		var matt = precont[ i ].innerHTML;
		matt = matt.replace(/\[\/?wp((tab[^\]\s]{1,8})|(dialog|spoiler))[^\]]*\]/img, "<span class='enclosers'>$&</span>" );
		precont[i].innerHTML = matt;
	}
	argvals = document.getElementsByClassName('enclosers');
	for ( i = 0; i < argvals.length; i++ ) {
		argvals[i].innerHTML = argvals[i].innerHTML.replace(/(\s{1}[\w\d^=]{1,14})=["']([^"']*)["']/img, "<span class='arguments'>$1</span>=\"<span class='values'>$2</span>\"" );
	}	
} // END fn synHilite.

jQuery('pre').each(function() {
	jQuery(this).addClass('dark-pre');
	jQuery(this).wrap('<div class="pre-tools" />');
	jQuery(this).parent().prepend('<p><strong>Code</strong>').children('p').css({ margin: '5px', paddingTop : '15px'});
	
	// jQuery('.melort').each(function() {
	// 	
	// 	jQuery(this).click(function() {
	// 		currentVal = jQuery(this).parent().parent().children('pre').attr('class');
	// 		newVal =  (currentVal == 'dark-pre' ? 'light-pre' : 'dark-pre');
	// 		newText = ( jQuery(this).text() == 'Light' ) ? 'Dark' : 'Light';
	// 		jQuery(this).parent().parent().children('pre').switchClass(currentVal, newVal, 600);
	// 		jQuery(this).text( newText );
	// 	
	// 	return false;
	// 	});
	// 	
	// });
	
});

// jQuery('.roguelist').css({ right : '-270px' });

// jQuery('div.roguelist').hover(function() {
// 	jQuery(this).stop().animate({
// 		right : '0px'
// 	}, 600);
// }, function() {
// 	jQuery(this).stop().animate({ right : '-270px'});
// });
	
jQuery( 'table tbody' ).each(function() {
	jQuery( this ).children('tr:odd').addClass( 'odd-table' );
});
	
	/*
	 *	animated Scroll to function. Gets Target from href attribute.
	 *	@params elID(element ID), speed(scrolling Speed)
	 */
	scrollIn = function(elID, speed) {
		var speed = speed=='' ? '500' : speed;
		jQuery(elID).click(function() {
			var getLink = jQuery(this).attr("href");
			var getLoc = jQuery(getLink).offset().top;
			jQuery("html:not(:animated), body:not(:animated)").animate({
				scrollTop: getLoc-20			
			}, speed);
			
			jQuery(getLink).effect('highlight', {color: 'yellow'}, 'slow');
			return false;
		});
	}	
	
	scrollIn('.slide-to-id', 500);


</script>
</html>