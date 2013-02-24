<?php
//error_reporting(E_ALL);

require_once 'style-helper-functions.php';

global $cap;


/**
* This function creates front-end styles
* @param It didn't accepts any parameters
*/
function get_css(){
    global $cap;
    global $pagenow;
    if($cap->menu_x == ""){
        $cap->menu_x = 'left';
    }
    $switch_css = cc_switch_css();
    extract($switch_css);
?>
/* > Global Elements
-------------------------------------------------------------- */

body {
    background: none #<?php echo $body_bg_color;?>;
    color:#<?php echo $font_color;?>;
    font-family:Arial,Tahoma,Verdana,sans-serif;
    font-size:12px;
    line-height:170%;
    margin:0 auto;
    max-width:100%;
    min-width:100%;
    padding:0 !important;
    width:100%;
    <?php
    switch ($cap->bg_body_img_pos){
        case __('left','cc'):
            echo 'background-position: left top;';
            break;
        case __('right','cc'):
            echo 'background-position: right top;';
            break;
        case __('center','cc'):
            echo 'background-position: center top;';
            break;
        default:
            echo 'background-position: center top;';
            break;
    }
    ?>
    <?php if($cap->bg_body_img_fixed){?>
    background-attachment: fixed;
    <?php } ?>
}
body.activity-permalink {
    min-width: 100%;
    max-width: 100%;
}
#outerrim{
    margin: 0 auto;
}
<?php 
$site_width = '';
$units = 'px';
if($cap->cc_responsive_enable){
    $site_width = '1200';
} else if($cap->website_width){
    $site_width = $cap->website_width;
    $units = $cap->website_width_unit;
} else {
    $site_width = '1000';
}
get_content_width($site_width);

?>
#innerrim {
    width: <?php echo $site_width . $units;?>;
    float: none;
    margin: 0 auto;
}
.v_line {
    border-right: 1px solid #<?php echo $container_alt_bg_color;?>;
    height: 100%;
    position: absolute;
    width: 0;
}
.v_line_left {
    margin-left: <?php echo $cap->leftsidebar_width?>;
}
.single .v_line_right {
    right: <?php echo $cap->rightsidebar_width?>;
}
h1, h2, h3, h4, h5, h6 {
    margin: 0 0 12px 0;
}
h1 {color:#<?php echo $font_color;?>;margin-bottom: 25px;line-height: 170%}
h2 {color:#<?php echo $font_color;?>; margin-top: -8px;margin-bottom: 25px;line-height: 170%}
h3 {color:#<?php echo $font_color;?>}
h1, h1 a, h1 a:hover, h1 a:focus {font-size: 28px}
h2, h2 a, h2 a:hover, h2 a:focus {font-size: 24px}
h3, h3 a, h3 a:hover, h3 a:focus {font-size: 1.5em; margin-top: 3px;}
h4, h4 a, h4 a:hover, h4 a:focus {font-size: 16px;margin-bottom: 15px}
h5, h5 a, h5 a:hover, h5 a:focus {font-size: 14px;margin-bottom: 0}
h6, h6 a, h6 a:hover, h6 a:focus {font-size: 12px;margin-bottom: 0}
a {font-style:normal;color: #<?php echo $link_color;?>;text-decoration: none;padding: 1px 0}
a:hover, a:active {color: #<?php echo $font_color;?>}
a:focus {outline: none}
.clear {clear: left}
h1 a, h2 a, h3 a, h4 a, h5 a, h6 a,
h1 a:hover, h2 a:hover, h3 a:hover, h4 a:hover, h5 a:hover, h6 a:hover,
h1 a:focus, h2 a:focus, h3 a:focus, h4 a:focus, h5 a:focus, h6 a:focus {
    text-decoration: none;background-color: transparent;
}
p, em {
    font-size: 13px;
    margin-bottom: 15px;
}
em {font-style: italic}
p:last-child {margin-bottom: 0}
sub {
    line-height: 100%;
    font-size: 60%;
    font-family: Arial, Helvetica, sans-serif;
    vertical-align:bottom;
}
sup {
    line-height: 100%;
    font-size: 60%;
    font-family: Arial, Helvetica, sans-serif;
    vertical-align:top;
}
hr {
    background-color:#<?php echo $font_color;?>;
    border:0 none;
    clear:both;
    height:1px;
    margin: 20px 0;
}
blockquote {
    padding: 10px 20px;
    background-color: #<?php echo $container_alt_bg_color;?>;
}
blockquote, blockquote p, blockquote a, blockquote a:hover, blockquote a:focus,
blockquote h1, blockquote h2, blockquote h3,
blockquote h4, blockquote h5, blockquote h6 {
    font-family: georgia, times, serif;
    font-size: 16px;
    font-style:italic;
}
img.avatar {
    border:1px solid #<?php echo $container_alt_bg_color;?>;
    float:left;
}
span.cc_blockquote {
    width:30%;
    padding:2%;
    background-color: #<?php echo $container_alt_bg_color;?>;
}
span.cc_blockquote_left {
    float: left;
}
span.cc_blockquote_right {
    float: right;
}
span.cc_blockquote, span.cc_blockquote p, span.cc_blockquote a {
    font-family: times, serif;
    font-family: times, serif !important;
    font-size: 19px;
    font-size: 19px !important;
    font-style: italic;
}
ol {list-style: decimal outside none;}
ul {list-style: circle outside none;}



/* > Admin Bar
-------------------------------------------------------------- */

body#cc.activity-permalink #wp-admin-bar .padder,
body#cc #wp-admin-bar .padder {
    max-width:100%;
    min-width:100%;
}
#wp-admin-bar {
    font-size:11px;
    height:25px;
    left:0;
    position:fixed;
    top:0;
    width:100%;
    z-index:1000;
}
#wp-admin-bar a {
    background-color: transparent;
    text-decoration: none;
}

/* > Header
-------------------------------------------------------------- */

#header {
position: relative;
    color: #<?php echo $font_color;?>;
    <?php if($cap->header_img == ''){?>
        background: url(<?php echo get_template_directory_uri() ?>/images/default-header.png);
    <?php } ?>
    -moz-border-radius-bottomleft: 6px;
    -webkit-border-bottom-left-radius: 6px;
    border-bottom-left-radius: 6px;
    -moz-border-radius-bottomright: 6px;
    -webkit-border-bottom-right-radius: 6px;
    border-bottom-right-radius: 6px;
    margin-bottom: 12px;
    padding-top: 30px;
    background-repeat: no-repeat;
    z-index: 9;
    margin-bottom: 50px;
}
div.row-fluid [class*="span"] {
	min-height: 10px;
}
#header #search-bar {
	margin-top: 25px;
	float: right;
	width: 390px;
	text-align: right;
	padding: 10px;
}
#logo{
    padding: 10px;
    float: left;
}
#header div#logo h1, #header div#logo h4 {
    left: 20px;
    line-height: 150%;
    margin: 0 0 -5px;
    top: 35px;
    font-size: 28px;
}
#header #search-bar .padder {
    padding: 10px 0;
}
#header #search-bar input[type=text] {
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
    border-radius: 3px;
    border: 1px inset #<?php echo $container_alt_bg_color;?>;
    padding: 2px;
    margin-right: 4px;
}
#header #search-bar input[type=submit] {
    font-size: 13px;
    padding: 1px 4px;
    margin-left: 4px;
    margin-top: 2px;
}
label.accessibly-hidden {
    display: none;
}
#header div#logo h1 a, #header div#logo h4 a {
    color: #<?php echo $font_color;?>;
    font-size: 37px;
	line-height: 130%;
}

/* > Navigation
-------------------------------------------------------------- */

ul#nav {
    background:url("") no-repeat scroll 0 0 transparent;
    bottom:2px;
    list-style:none outside none;
    margin:15px 0 0;
    max-width:100%;
    min-width:100%;
    padding:45px 0 5px 0;
    position:relative;
    left: 20px;
    right: 15px;
}

ul#nav li {
    float:left;
    margin:0;
    padding:6px 28px 0 0;
}

ul#nav li a {
    -moz-background-inline-policy:continuous;
    -moz-border-radius-topleft:3px;
    border-top-left-radius:3px;
    -webkit-border-top-left-radius:3px;
    -moz-border-radius-topright:3px;
    border-top-right-radius:3px;
    -webkit-border-top-right-radius:3px;
    background:none repeat scroll 0 0 transparent;
    color:#<?php echo $font_color;?>;
    display:block;
    font-size:13px;
    font-weight:bold;
    padding:0;
}

ul#nav li.selected, ul#nav li.selected a, ul#nav li.current_page_item a {
    background:none repeat scroll 0 0;
    color: #<?php echo $link_color;?>;
}

ul#nav a:focus {outline: none}

#nav-home {
    float:left;
}
<?php if($cap->menu_x ==__("right",'cc')){?>
    #nav-home {
        float: right;
    }
<?php } ?>
#nav-community {
    float:left;
}


/* > Container
-------------------------------------------------------------- */

div#container {
    border-radius:6px;
    -moz-border-radius:6px;
    -webkit-border-radius:6px;
    background-color: #<?php echo $container_bg_color;?>;
    background-image:none;
    border:none;
    overflow:hidden;
    position:relative;
}

body.activity-permalink div#container {
    background: #<?php echo $container_bg_color;?>;
    border:none;
}

/* > Sidebar
-------------------------------------------------------------- */
#sidebar-me, #sidebar-login-form {
    margin: 0 0 20px 10px;
}

/*.right-sidebar-padder {padding: 30px 15px 30px 20px}*/

/*.left-sidebar-padder {padding:30px 15px 30px 20px}*/

div#sidebar {
    -moz-background-clip:border;
    -moz-background-inline-policy:continuous;
    -moz-background-origin:padding;
    -moz-border-radius-topright:6px;
    -webkit-border-top-right-radius:6px;
    border-top-right-radius:6px;
    background:transparent;
    border-left:none;
    float:right;
    margin-left:-224px;
    margin-top: 0px;
    padding-top: 15px;
}

div.widgetarea {
    -moz-background-clip:border;
    -moz-background-inline-policy:continuous;
    -moz-background-origin:padding;
    background:transparent;
}

div#sidebar div#sidebar-me img.avatar, div.widgetarea div#sidebar-me img.avatar {
    float: left;
    margin: 0 10px 15px 0;
}

div#sidebar div#sidebar-me h4, div.widgetarea div#sidebar-me h4 {
    font-size: 16px;
    margin: 0 0 8px 0;
    font-weight: normal;
}


div#sidebar ul#bp-nav, div.widgetarea ul#bp-nav {
    clear: left;
    margin: 15px -16px;
}
div#sidebar ul#bp-nav li, div.widgetarea  ul#bp-nav li {
    padding: 10px 15px;
}

div#leftsidebar h3.widgettitle, div#sidebar h3.widgettitle, div.widgetarea h3.widgettitle,div.span3 h3.widgettitle  {
    -moz-border-radius:4px 4px 4px 4px;
    -webkit-border-radius:4px 4px 4px 4px;
    border-radius:4px;
    background:none repeat scroll 0 0 #<?php echo $container_alt_bg_color;?>;
    clear:left;
    color:#<?php echo $font_color;?>;
    font-size:12px;
    margin:0 8px 12px 0px;
    padding:5px 10px;
    width:182px;
    font-family: arial, helvetica, sans-serif;
}

div#leftsidebar h3.widgettitle a, div#sidebar h3.widgettitle a, div.widgetarea h3.widgettitle a {
    clear:left;
    color:#<?php echo $font_color;?>;
    background-color: transparent;
    text-decoration: none;
    font-size:12px;
    font-family: arial, helvetica, sans-serif;
}
div#leftsidebar h3.widgettitle a:hover, div#leftsidebar h3.widgettitle a:focus,
div#sidebar h3.widgettitle a:hover, div#sidebar h3.widgettitle a:focus,
div.widgetarea h3.widgettitle a:hover, div.widgetarea h3.widgettitle a:focus {
    color:#<?php echo $link_color;?>;
    background-color: transparent;
    text-decoration: none;
}
div#leftsidebar div#item-header-avatar img.avatar, div#sidebar div#item-header-avatar img.avatar {
    margin-bottom:20px;
}
div#item-header .row-fluid{
    position: relative;
}

div#sidebar h3.widgettitle p, div.widgetarea h3.widgettitle p {
    padding: 5px 10px;
    font-size: 12px;
    color:#<?php echo $font_color;?>;
    clear: left;
}

div#sidebar .widget_search, div.widgetarea .widget_search {
    margin-top: 0;
}
div#sidebar .widget_search input[type=text], div.widgetarea .widget_search input[type=text]{
    width: 110px;
    padding: 2px;
}

div#sidebar ul#recentcomments li, div#sidebar .widget_recent_entries ul li, div.widgetarea  ul#recentcomments li, div.widgetarea .widget_recent_entries ul li{
    margin-bottom: 5px;
}

div#sidebar ul.item-list img.avatar, div.widgetarea  ul.item-list img.avatar{
    width: 25px;
    height: 25px;
    margin-right: 10px;
}
div#sidebar div.item-avatar{display: inline;}
div#sidebar div.item-avatar img, div.widgetarea  div.item-avatar img{
    width: 40px;
    height: 40px;
}

div#sidebar .avatar-block, div.widgetarea .avatar-block{overflow: hidden}

.avatar-block img.avatar {margin-right: 4px}

div#sidebar ul.item-list div.item-title, div.widgetarea ul.item-list div.item-title{
    font-size:12px;
    line-height:140%;
}

div#sidebar div.item-options, div.widgetarea div.item-options{
    background:none repeat scroll 0 0 transparent;
    font-size:11px;
    margin:-12px 0 10px -14px;
    padding:5px 15px;
    text-align:left;
}
div.widgetarea #groups-list-options.item-options,
.widgetarea .widget_archive select{
    margin-top: 10px;
}

div#sidebar div.item-meta, div#sidebar div.item-content, div.widgetarea div.item-meta, div.widgetarea div.item-content{
    font-size: 11px;
}

div#sidebar div.tags div#tag-text, div.widgetarea div.tags div#tag-text{
    font-size: 1.4em;
    line-height: 140%;
    padding-top: 10px;
}

div#sidebar ul , div.widgetarea ul {
    text-align:left;
    margin-left: 0;
}

.widget li.cat-item {
    margin-bottom: 0px;
}
.widget li.current-cat a, div.widget ul li.current_page_item a {
    color:#<?php echo $link_color;?>;
}

.cc-widget, #header .span3{
    /*width:30% !important;*/
    /*float:left;*/
    text-align:left !important;
    /*margin:20px 2% 20px 0 !important;*/
    -moz-border-radius: 6px !important;
    -webkit-border-radius: 6px !important;
    border-radius: 6px !important;
    background-color: #<?php echo $container_bg_color;?> !important;
    padding:1% !important;
    overflow: hidden;
}
#footer .cc-widget a.button, #header .cc-widget a.button{
    color: #<?php echo $container_bg_color;?>;
}
#header .cc-widget{
    margin-top: 0px !important;
}
.widget li.current-cat, div.widget ul li.current_page_item{
    background:transparent;
    margin-left:-8px;
    padding:2px 8px 0 8px;
    width:100%;
}

div#leftsidebar {
    -moz-background-inline-policy:continuous;
    -moz-border-radius-topleft:6px;
    -webkit-border-top-left-radius:6px;
    border-top-left-radius:6px;
    background:transparent;
    border-left:0 none;
    border-right:none;
    float:left;
    /*margin-right:-225px;*/
    margin-top: 0px;
    position:relative;
    width:225px;
}
.paddersidebar{ padding: 30px 10px; }

div#sidebar div.item-options a.selected,
div#leftsidebar div.item-options a.selected, div.widgetarea {
    color:#<?php echo $font_color;?>;
}

/* > Content
-------------------------------------------------------------- */

div#content {
    -moz-border-radius:6px;
    -webkit-border-radius:6px;
    border-radius:6px;
    margin: 0;
    float:left;
}

div#content .padder {
    border-radius: 0px; 
    border-left: none;
    border-right: none;
    min-height: 300px;
    padding: 30px 10px 10px 10px;
    overflow: hidden;
}

div#content .left-menu {
    float: left;
    width: 170px;
}

div#content .main-column {
    margin-left: 190px;
}
div#content  div.cc_slider{
    margin-left: 0px;
    background: #EDEDED;
}
.achievements #content, .single-bp_doc #content{
    width: 75%;
}
/* > Item Headers (Profiles, Groups)
-------------------------------------------------------------- */

div#item-header {
    overflow: hidden;
}

div#content div#item-header {
    margin-top:0;
    overflow:hidden;
}
div#item-header div#item-header-content {
    width: 53%;
    float: left;
    margin-left: 20px;
}

div#item-header h2 {
    font-size: 28px;
    margin: -5px 0 15px 0;
    line-height: 120%;
}
div#item-header h2 a {
    font-size: 1em;
}

div#item-header img.avatar {
    float: left;
    margin: 0 15px 25px 0;
}

div#item-header h2 {margin-bottom: 5px}

div#item-header span.activity, div#item-header h2 span.highlight {
    vertical-align: middle;
    font-size: 13px;
    font-weight: normal;
    line-height: 170%;
    margin-bottom: 7px;
    color:#<?php echo $font_color;?>;
}

div#item-header h2 span.highlight {font-size: 16px;color:#<?php echo $font_color;?>}
div#item-header h2 span.highlight span {
    position: relative;
    top: -2px;
    right: -2px;
    font-weight: bold;
    font-size: 11px;
    background: #<?php echo $link_color;?>;
    color: #<?php echo $body_bg_color;?>;
    padding: 1px 4px;
    margin-bottom: 2px;
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
    border-radius: 3px;
    vertical-align: middle;
    cursor: pointer;
    display: none;
}

div#item-header div#item-meta {
    font-size: 14px;
    color: #<?php echo $font_color;?>;
    padding-bottom: 25px;
    overflow: hidden;
    margin: 15px 0 5px 0;
}

div#item-header div#item-actions {
    position: absolute;
    right: 0;
    width: 20%;
    margin: 0 15px 15px 15px;
    text-align: right;
}
div#item-header div#item-actions h3 {
    font-size: 12px;
    margin: 0 0 5px 0;
}

div#item-header ul {
    overflow: hidden;
    margin-bottom: 15px;
}

div#item-header ul h5, div#item-header ul span, div#item-header ul hr {
    display: none;
}

div#item-header ul li {
    float: none;
}

div#item-header ul img.avatar, div#item-header ul.avatars img.avatar {
    width: 30px;
    height: 30px;
    margin: 2px;
}

div#item-header div.generic-button, div#item-header a.button {
    float: left;
    margin: 10px 5px 0 0;
}

div#item-header div#message.info {
    line-height: 80%;
}

div#item-header-avatar{
    width: 170px;
    height: 160px;
    float: left;
}
#member-list h5,
#member-list .activity,
#member-list .action{
    margin-left: 10px;
}
/* > Item Lists (Activity, Friend, Group lists, Widgets)
-------------------------------------------------------------- */

div.widget-title ul.item-list li{
    background:none;
    border-bottom:medium none;
    font-size:12px;
    margin-bottom:8px;
    padding:0;
}
div.widget-title ul.item-list li.selected {
    background:none;
    border:none;
    font-size:12px;
    color:#<?php echo $link_color;?>;
}
div.widget-title ul.item-list li.selected a {
    color:#<?php echo $font_color;?>;
}

ul.item-list {
    width: 100%;
}

ul.item-list li {
    position: relative;
    padding: 15px 0 20px 0;
    border-bottom: 1px solid #<?php echo $container_alt_bg_color;?>;
    list-style: none outside none;
}
ul.single-line li {border: none}
body.activity-permalink ul.item-list li {padding-top: 0;border-bottom:none}

ul.item-list li img.avatar {
    float: left;
    margin: 0px 10px 10px 0;
}
div.widget ul.item-list li img.avatar {
    width:25px;
    height:25px;
    margin: 3px 10px 10px 0;
}
ul.item-list li div.item-title, ul.item-list li h4 {
    float: none;
    font-size: 1em;
    font-weight: normal;
    margin: 0 0 0 10px;
}
div.widget ul.item-list li div.item-title, div.widget  ul.item-list li h4 {
    float:left;
    width:100%;
}

ul.item-list li div.item-title span {
    font-size: 12px;
    color: #<?php echo $font_color;?>;
}

ul.item-list li div.item-desc {
    margin: 0 0 0 10px;
    font-size: 0.6em;
    float: none;
    color: #<?php echo $font_color;?>;
}

ul.item-list li div.action {
    position: absolute;
    top: 15px;
    right: 15px;
    text-align: right;
    width: 50%;
}

.item-meta{
    float:left;
    width:87%;
    margin-left:10px;
}

#groups-list .item-meta{
    float: none;
    margin-left: 10px;
}


ul.item-list li div.meta {
    color:#<?php echo $font_color;?>;
    font-size:1em;
    margin-top: 4px;
}

ul.item-list li h5 span.small {
    font-weight: normal;
    font-size: 11px;
}

#groups-list div.item {
	margin-left: 60px;
}

ul.item-list.displaymode-grid li,ul#groups-list.displaymode-grid li{
    display: inline-block;
    width: 150px;
    margin: 2%;
    overflow: hidden;
    vertical-align: top;
    background: none repeat scroll 0 0 #EDEDED;
    border-radius: 11px 11px 11px 11px;
    padding: 15px 15px 5px 15px;
}
ul.item-list li .hoverblock{
    display: none;
    position: absolute;
    top: 0;
    left: 0;
    background: #EDEDED;
    overflow: hidden;
    width: 180px;
    height: 170px;
    font-size: 0.9em;
}
ul.item-list li .hoverblock p{
    font-size: 1.5em;
}
ul.item-list li .hoverblock .hoverblockcontainer{
    padding: 15px;
}
ul.item-list.displaymode-grid li:hover .hoverblock{
    display: block;
}
ul.item-list.displaymode-grid li .item-avatar{display: block;}

ul.item-list.displaymode-grid li div.item-title,
ul.item-list.displaymode-grid li h4{
    width: 100%;
    text-overflow: ellipsis;
    overflow:hidden;
    white-space: nowrap;
    text-align: center;
}
ul.item-list.displaymode-grid li img.avatar{
    float: none;
    text-align: center;
    height: 150px;
    display: block;
    width: 150px
}
ul.item-list.displaymode-grid li div.item-desc{
    width: 100%;
    margin: 0;
}
ul.item-list.displaymode-grid li div.action{width: auto;position: static;}
ul.item-list.displaymode-grid li div.action a{
    font-size: 11px
}
ul.item-list.displaymode-grid li .item-meta,ul.item-list.displaymode-grid li .item-meta span.activity{width: auto;float: none}

#whats-new-content #whats-new-options{
    height: 0;
    overflow: hidden;
}


/* > Item Tabs
-------------------------------------------------------------- */

div.item-list-tabs {
    background:none repeat scroll 0 0 transparent;
    border-bottom:4px solid #<?php echo $container_alt_bg_color;?>;
    clear:left;
    margin: 0;
    overflow:hidden;
    padding-top:15px;
}
div.item-list-tabs ul {
    width: 100%;
}
div.item-list-tabs ul li {
    float: left;
    margin: 0px 5px;
    list-style-type: none;
}
div.item-list-tabs ul li.selected {
    background:none;
}
/*div.item-list-tabs#subnav ul li {
    margin-top: 0;
}*/
div.item-list-tabs ul li:first-child {
    margin-left: 20px;
}
div.item-list-tabs ul li.last, #members-displaymode-select {
    float: right;
    text-align: right;
/*
    margin: 7px 20px 0 0;*/
}
#groups-order-select, #groups-displaymode-select{
    float: right;
    text-align: right;
}
.item-list-tabs.row-fluid{
    width: auto;
}
div.item-list-tabs ul li.last select {
    max-width: 175px;
}
div.item-list-tabs ul li a,
div.item-list-tabs ul li span {
    display: block;
    padding: 4px 8px;
}
div.item-list-tabs ul li a {
        text-decoration: none;
        background-color: transparent;
}
div.item-list-tabs ul li a:hover,
div.item-list-tabs ul li a:focus {
        color: #<?php echo $font_color;?>;
}
div.item-list-tabs ul li span {
        color: #<?php echo $font_color;?>;
}
div.item-list-tabs ul li a span {
        background: none repeat scroll 0 0 #<?php echo $container_alt_bg_color;?>;
        border-radius: 3px 3px 3px 3px;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
        color: inherit;
        display: inline;
        font-size: 11px;
        padding: 2px 4px;
}
div.item-list-tabs ul li.selected a span {
        background: none repeat scroll 0 0 #<?php echo $container_bg_color;?>;
}
div.item-list-tabs ul li.selected a, div.item-list-tabs ul li.current a {
    -moz-border-radius-topleft:6px;
    -moz-border-radius-topright:6px;
    -webkit-border-top-left-radius:6px;
    -webkit-border-top-right-radius:6px;
    border-top-left-radius:6px;
    border-top-right-radius:6px;
    background-color:#<?php echo $container_alt_bg_color;?>;
    color:#<?php echo $font_color;?> !important;
    font-weight: normal;
    margin-top:0;
}
.item-list-tabs .span3{
    text-align: right;
}
ul li.loading a {
    background-image: url(<?php echo get_template_directory_uri() ?>/images/ajax-loader.gif );
    background-position: 92% 50%;
    background-repeat: no-repeat;
    padding-right: 30px !important;
    z-index: 1000;
}

form#send_message_form input#send:focus,
div.ac-reply-content input.loading,
div#whats-new-submit input#aw-whats-new-submit.loading{
    background-image: url(<?php echo get_template_directory_uri() ?>/images/ajax-loader.gif );
    background-position: 5% 50%;
    background-repeat: no-repeat;
    padding-left: 20px;
}

div#item-nav ul li.loading a {
    background-position: 88% 50%;
}
#item-nav a{
    color:#<?php echo $link_color;?>;
}
#subnav a{
    color:#<?php echo $font_color;?>;
}
#item-nav a:hover{
    color:#<?php echo $font_color;?>;
}
#subnav a:hover{
    color:#<?php echo $link_color;?>;
}
#groups-dir-list{
    clear: both;
}

div.item-list-tabs#object-nav {
    margin-top: 0;
}

div#subnav.item-list-tabs  {
    background:none repeat scroll 0 0 #<?php echo $container_alt_bg_color;?>;
    border-bottom: medium none;
    margin: 0;
    min-height: 26px;
    padding: 10px 20px 0 10px;
    overflow: hidden;

}
div#subnav.item-list-tabs ul li.selected a, div#subnav.item-list-tabs ul li.current a  {
    background-color:#<?php echo $container_bg_color;?>;
}
div.item-list-tabs ul li.feed a {
    background: url(<?php echo get_template_directory_uri() ?>/_inc/images/rss.png ) center left no-repeat;
    padding-left: 20px;
}
/*div#subnav.item-list-tabs ul li.displaymode{margin-top: -4px;float: right;margin-right: 10px;}*/
/*div#subnav.item-list-tabs ul li.displaymode.last{margin-right: 20px}*/


/*--A lot of sub menu items in BuddyPress menu--*/
.item-list-tabs .next, 
.item-list-tabs .prev{
    display:none;
    padding:2px 6px 5px 6px;
    float:left;
    border:0;
    font:normal 18px Helvetica;
    color: #<?php echo $link_color;?>;
    background: none repeat scroll 0 0 #<?php echo $container_bg_color;?>;
    -moz-border-radius-topleft: 6px;
    -moz-border-radius-topright: 6px;
    -webkit-border-top-left-radius: 6px;
    -webkit-border-top-right-radius: 6px;
    border-top-left-radius: 6px;
    border-top-right-radius: 6px;
}
.item-list-tabs .next{
    float:right;
    margin:0 0 0 4px;
}
.item-list-tabs .prev{
    margin:0 4px 0 0;
}
.item-list-tabs .next:hover, 
.item-list-tabs .prev:hover{
    color: #<?php echo $font_color;?>;
    background-color:#<?php echo $container_alt_bg_color;?>;
    cursor: pointer;
}
.item-list-tabs div.bp-nav-wrap {
    overflow:hidden;
    text-align:left;
    margin:0;
    padding:0;
    width: 90%;
}
.item-list-tabs div ul{
    width:3000px;
    overflow:hidden;
    margin:0;
    padding:0;
    margin-left:0;
}

/* > Item Body
-------------------------------------------------------------- */

.item-body {
    margin: 20px 0;
}

.activity{
    width:100%;
}

span.activity, div#message p {
    background:none;
    border:none;
    color:#<?php echo $font_color;?>;
    display:inline-block;
    font-size:1em;
    font-weight:normal;
    margin-top:6px;
    padding:3px 0 3px 0;
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
    border-radius: 3px;
    line-height: 120%;
    width:80%;
}
div.widget span.activity {
    -moz-border-radius:3px 3px 3px 3px;
    -webkit-border-radius:3px;
    border-radius:3px;
    background:none repeat scroll 0 0 transparent;
    border-bottom:1px solid #<?php echo $container_alt_bg_color;?>;
    border-right:medium none;
    color:#<?php echo $font_color;?>;
    display:inline-block;
    float:left;
    font-size:11px;
    font-weight:normal;
    margin-bottom:0px;
    margin-left:0px;
    margin-top:0;
    width:100%;
    padding:3px 0;
}

#footer div.widget span.activity, #header div.widget span.activity {
    margin-left:0;
}


/* > Directories (Members, Groups, Blogs, Forums)
-------------------------------------------------------------- */

div.dir-search input[type=text] {
    padding: 4px 3px 1px 3px;
    line-height: 100%;
    font-size: 12px;
}

.readmore{
    float:right;
}
.read-more-link br{
    display: none;
}

body.forum #subnav{
    padding-top: 10px !important;
}
body.forum #subnav ul li{
    margin-top: -6px !important;
}
div.profile{
   margin: 20px;
}

/* > Group specific styles
-------------------------------------------------------------- */
#item-actions li{
    float: right !important;
    list-style: none outside none;
}
#groups-directory-form h3, #members-directory-form h3{
    float: left;
    width: 68%;
    margin-left: 10px;
}
#groups-directory-form h3 .button,#members-directory-form h3 .button {
    float: none;
}
#group-dir-search.dir-search{
    width: 30%;
    float: left;
}

/* > Pagination
-------------------------------------------------------------- */

div.pagination {
    background:none repeat scroll 0 0 #<?php echo $body_bg_color;?>;
    border-bottom:medium none;
    color:#<?php echo $font_color;?>;
    font-size: 11px;
    height: 16px;
    margin: 0;
    padding: 10px 20px;
}

div.pagination#user-pag, .friends div.pagination,
.mygroups div.pagination, .myblogs div.pagination, noscript div.pagination {
    background: none;
    border: none;
    padding: 8px 15px;
}

div.pagination .pag-count {
    float: left;
    margin-left: 20px;
}

div.pagination .pagination-links {
    float: right;
}
div.pagination .pagination-links span,
div.pagination .pagination-links a {
    font-size: 12px;
    padding: 0 5px;
}
div.pagination .pagination-links a:hover {
    font-weight: bold;
}

div#pag-bottom {
    background:none repeat scroll 0 0 transparent;
    margin-top:0;
}

/* > Error / Success Messages
-------------------------------------------------------------- */

div#message {
    margin: 15px 0;
    background: #<?php echo $container_alt_bg_color; ?>;
    border: #ececec;
}
div#message.updated {clear: both}

div#message p {
    padding: 10px 15px;
    font-size: 12px;
    display:block;
}
div#message.error p {
    background: #e41717 !important;
    color: #<?php echo $body_bg_color;?> !important;
    border-color: #a71a1a;
    clear: left;
}
div#message.updated p {
    background:none;
    border:none;
    color:#<?php echo $font_color;?>;
}

form.standard-form#signup_form div div.error {
    color: #<?php echo $body_bg_color;?>;
    background: #e41717;
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
    border-radius: 3px;
    padding: 6px;
    width: 90%;
    margin: 0 0 10px 0;
}
<?php if(isset( $_GET['activated'] ) || ( 'themes.php' == $pagenow ) || is_admin()){?>
#message2, #message0 {
    display: none;
}
<?php } ?>
/* > Buttons
-------------------------------------------------------------- */

a.comment-edit-link, a.comment-reply-link, a.button, input[type="submit"], input[type="button"], ul.button-nav li a, div.generic-button a {
    -moz-border-radius: 4px;
    -webkit-border-radius: 4px;
    border-radius: 4px;
    background: none repeat scroll 0 0 #<?php echo $font_color;?>;
    border-bottom: 1px solid #aaaaaa;
    border-right: 1px solid #aaaaaa;
    border-top: none;
    border-left: none;
    color: #<?php echo $container_bg_color;?>;
    font-family: arial, sans-serif;
    font-size: 12px;
    cursor: pointer;
    font-weight: normal;
    padding: 3px 5px;
    text-decoration: none;
    text-shadow: none;
    vertical-align: top;
    margin-top: 2px;
}

a.comment-edit-link:hover, a.comment-edit-link:focus, a.comment-reply-link:hover, a.comment-reply-link:focus, a.button:focus, a.button:hover, input[type="submit"]:hover, input[type="button"]:hover,
ul.button-nav li a:hover, div.generic-button a:hover, ul.button-nav li a:focus, div.generic-button a:focus {
    background:none repeat scroll 0 0 #<?php echo $link_color;?>;
    border-color:#aaaaaa;
    border-style:none solid solid none;
    border-width:medium 1px 1px medium;
    color:#<?php echo $container_bg_color;?>;
    cursor:pointer;
    font-size:12px;
    font-weight:normal;
    padding:3px 5px;
    text-decoration:none;
    vertical-align: top;
    outline: none;
}

/* Buttons that are disabled */
div.pending a, a.disabled, a.requested {
    border-bottom:1px solid #888888;
    border-right:1px solid #888888;
    border-top:none;
    border-left:none;
    color:#<?php echo $container_bg_color;?>;
    background:none repeat scroll 0 0 #888888;
    cursor:default;
}

div.pending a:hover, a.disabled:hover, a.requested:hover {
    border-bottom:1px solid #888888;
    border-right:1px solid #888888;
    border-top:none;
    border-left:none;
    color:#<?php echo $container_bg_color;?>;
    background:none repeat scroll 0 0 #888888;
    cursor:default;
}

div.accept, div.reject {
    float: left;
    margin-left: 10px;
}

ul.button-nav li {
    float: left;
    margin: 0 10px 10px 0;
}
ul.button-nav li.current a {
    font-weight: bold;
    color:#<?php echo $container_bg_color;?>;
}

div#item-buttons div.generic-button {
        margin: 0 12px 12px 0;
}

ul.acfb-holder li{
    float: none;
}
input#send-to-input{
    width: 75%;
}


/* > AJAX Loaders
-------------------------------------------------------------- */

.ajax-loader {
    background: url(<?php echo get_template_directory_uri() ?>/images/ajax-loader.gif ) center left no-repeat !important;
    padding: 8px;
    display: none;
    z-index: 1000;
}

a.loading {
    background-image: url(<?php echo get_template_directory_uri() ?>/images/ajax-loader.gif ) !important;
    background-position: 95% 50% !important;
    background-repeat: no-repeat !important;
    padding-right: 25px !important;
    z-index: 1000;
}

/* > Input Forms
-------------------------------------------------------------- */

form.standard-form, form#searchform{
    margin-left: 10px;
}
form.standard-form textarea, form.standard-form input[type=text],
form.standard-form select, form.standard-form input[type=password],
.dir-search input[type=text] {
    border: 1px inset #ccc;
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
    border-radius: 3px;
    padding: 6px;
    font: inherit;
    font-size: 14px;
    color: #888;
}
form.standard-form select {
    padding: 3px;
}

form.standard-form input[type=password] {
    margin-bottom: 5px;
}

form.standard-form label, form.standard-form span.label {
    display: block;
    font-weight: bold;
    margin: 15px 0 5px 0;
}
form.standard-form div.checkbox label,
form.standard-form div.radio label {
    font-weight: normal;
    margin: 5px 0 0 0;
    font-size: 14px;
    color: #888;
}

form.standard-form#sidebar-login-form label {
    margin-top: 5px;
}

form.standard-form input[type=text] {
    width: 75%;
}
form.standard-form#sidebar-login-form input[type=text],
form.standard-form#sidebar-login-form input[type=password] {
    padding: 4px;
    width: 95%;
}

form.standard-form #basic-details-section input[type=password],
form.standard-form #blog-details-section input#signup_blog_url {
    width: 35%;
}

form.standard-form#signup_form input[type=text],
form.standard-form#signup_form textarea {
    width: 90%;
}
form.standard-form#signup_form div.submit {float: right}
div#signup-avatar img {margin: 0 15px 10px 0}

form.standard-form textarea {
    width: 75%;
    height: 120px;
}
form.standard-form textarea#message_content {
    height: 200px;
}

form.standard-form#send-reply textarea {
    width: 90%;
}

form.standard-form p.description {
    font-size: 11px;
    color: #888;
    margin: 5px 0;
}

form.standard-form div.submit {
    padding: 15px 0;
    clear: both;
}
form.standard-form div.submit input {
    margin-right: 15px;
}

form.standard-form div.radio ul {
    margin: 10px 0 15px 38px;
    list-style: disc;
}
form.standard-form div.radio ul li {
    margin-bottom: 5px;
}

form.standard-form a.clear-value {
    display: block;
    margin-top: 5px;
    outline: none;
}

form.standard-form #basic-details-section, form.standard-form #blog-details-section,
form.standard-form #profile-details-section {
    float: left;
    width: 48%;
}
form.standard-form #profile-details-section {float: right}
form.standard-form #blog-details-section {
    clear: left;
}

form.standard-form input:focus, form.standard-form textarea:focus, form.standard-form select:focus {
    background: #fafafa;
    color: #666666;
}

form#send-invite-form {
    margin-top: 20px;
}
div#invite-list {
    height: 400px;
    overflow: scroll;
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
    border-radius: 3px;
    padding: 5px;
    background: #<?php echo $container_bg_color;?>;
    width: 160px;
    border: 1px solid #<?php echo $body_bg_color;?>;
    margin: 10px 0;
}
form#signup_form div.register-section select{
    width:245px !important;
}

p.edit-post-link{
    margin-left: 55px;
    margin-top: 10px;
}
/* > Data Tables
-------------------------------------------------------------- */

table {
    width: 100%;
    margin: 0 0 15px 0;
}
table thead tr {
    background: #<?php echo $body_bg_color;?>;
}
table#message-threads {
    width: auto;
}

table.profile-fields {margin-bottom: 20px}

div#sidebar table , div.widgetarea table {
    margin: 0 0;
    width: 100%;
}

table tr td, table tr th {
    text-align:left;
    padding: 5px 7px 3px 7px;
    vertical-align: middle;
    border-bottom: 1px solid #<?php echo $body_bg_color;?>;
}
table tr td.label {
    border-right: 1px solid #<?php echo $body_bg_color;?>;
    font-weight: bold;
    width: 25%;
}

table tr td.thread-info p {margin: 0}

table tr td.thread-info p.thread-excerpt {
    color: #<?php echo $font_color;?>;
    font-size: 11px;
    margin-top: 3px;
}

div#sidebar table td, table.forum td , div.widgetarea table td, table.forum td {text-align: center}

table tr.alt, table tr th {
    background: #<?php echo $body_bg_color;?>;
}

table.notification-settings {
    margin-bottom: 20px;
    text-align: left;
}
table.notification-settings th.icon, table.notification-settings td:first-child {display: none}
table.notification-settings th.title {width: 80%}
table.notification-settings .yes, table.notification-settings .no {width: 40px;text-align: center}

table.forum {
    margin: -1px -20px 20px -20px;
    width: auto;
}
table.forum tr:first-child {
    background: #<?php echo $container_bg_color;?>;
}

table.forum tr.sticky td {
    background: #bbbbbb;
    border-top: 1px solid #<?php echo $body_bg_color;?>;
    border-bottom: 1px solid #<?php echo $body_bg_color;?>;
}

table.forum tr.closed td.td-title {
    padding-left: 35px;
    background-image: url(<?php echo get_template_directory_uri() ?>/_inc/images/closed.png);
    background-position: 15px 50%;
    background-repeat: no-repeat;
}

table.forum td p.topic-text {
    color: #<?php echo $font_color;?>;
    font-size: 11px;
}

table.forum tr > td:first-child, table.forum tr > th:first-child {
    padding-left: 15px;
}

table.forum tr > td:last-child, table.forum tr > th:last-child {
    padding-right: 15px;
}

table.forum tr th#th-title, table.forum tr th#th-poster,
table.forum tr th#th-group, table.forum td.td-poster,
table.forum td.td-group, table.forum td.td-title {text-align: left}

table.forum td.td-freshness {
    font-size: 11px;
    color: #888888;
    text-align: center;
}

table.forum tr th#th-freshness{
    text-align: center;
}

table.forum td img.avatar {
    margin-right: 5px;
}

table.forum td.td-poster, table.forum td.td-group  {
    min-width: 130px;
}

table.forum th#th-title {
    width: 40%;
}

table.forum th#th-postcount {
    width: 1%;
}
table#message-threads tr .thread-options{
    padding: 0;
}
/* > Activity Stream Posting
-------------------------------------------------------------- */

form#whats-new-form {
    margin-bottom: 3px;
    border-bottom: 1px solid #<?php echo $body_bg_color;?>;
    overflow: hidden;
    padding-bottom: 20px;
}
#item-body form#whats-new-form {
    margin-top: 20px;
    border: none;
}

.home-page form#whats-new-form {
    border-bottom: none;
    padding-bottom: 0;
}

form#whats-new-form h5 {
    margin: 0 0 5px 10px;
    font-weight: normal;
    font-size: 12px;
    color: #<?php echo $font_color;?>;
    float: left;
    width: 80%;
}

form#whats-new-form #whats-new-avatar {
    float: left;
    width: 52px;
}

form#whats-new-form #whats-new-content {
   float: left;
   width: 80%;
   margin-left: 10px;
}

form#whats-new-form #whats-new-textarea {
    padding: 8px;
    border: 1px inset #777777;
    background: #ffffff;
    margin-bottom: 10px;
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
    border-radius: 3px;
}
form#whats-new-form textarea {
    width: 100%;
    height: 60px;
    font-size: 14px;
    font-family: inherit;
    color: #555;
    border: none;
    margin: 0;
    padding: 0;
    resize: none;
}

form#whats-new-form #whats-new-options select {
    max-width: 200px;
}

form#whats-new-form #whats-new-submit {
    float: right;
    margin: 0;
}

/* > Activity Stream Listing
-------------------------------------------------------------- */

ul.activity-list li {
    padding: 8px 0 0 0;
    overflow: hidden;
    border-top: 1px solid #<?php echo $container_alt_bg_color;?>;
}
ul.activity-list > li:first-child {
    padding-top: 5px;
}

ul.activity-list li.has-comments {
    padding-bottom: 15px;
}

.activity-list li.mini {
    position: relative;
    font-size: 11px;
    min-height: 35px;
    padding: 10px 0;
}
.activity-list li.mini div.activity-meta {
    margin: 5px 0 10px 0;
}

.activity-list li.mini div.activity-meta a {
    padding: 3px 8px;
}

.activity-list li.mini .activity-avatar img.avatar,
.activity-list li.mini .activity-avatar img.FB_profile_pic {
    margin-left: 0;
}
.activity-list li.activity_comment .activity-avatar img.avatar, .activity-list li.activity_comment .activity-avatar img.FB_profile_pic {
    height:30px;
    margin-left:0;
    width:30px;
}

body.activity-permalink .activity-list li .activity-avatar img.avatar,
body.activity-permalink .activity-list li .activity-avatar img.FB_profile_pic {
    width: 100px;
    height: 100px;
    margin-left: 0;
}

.activity-list li.mini .activity-content {
	overflow: auto;
	margin-right: 0;
	padding: 0 0 0 8px;
	width: auto;
	margin-left: 0px;
}
.activity-list li.mini .activity-content p {
    margin: 0;
    float: left;
}

body.activity-permalink .activity-list li.mini .activity-meta {
    position:absolute;
    right:5px;
    top:45px;
}

.activity-list li.mini .activity-comments {
    clear: left;
    font-size: 12px;
    margin-top: 8px;
}

.activity-list li .activity-inreplyto {
    display:none;
    background:none;
    color:#<?php echo $font_color;?>;
    font-size:11px;
    margin-bottom:15px;
    margin-left:80px;
    padding-left:0;
}
.activity-list li .activity-inreplyto > p {
    margin: 0;
    display: inline;
}

.activity-list li .activity-inreplyto blockquote,
.activity-list li .activity-inreplyto div.activity-inner {
    background: none;
    border: none;
    display: inline;
    padding: 0;
    margin: 0;
    overflow: hidden;
}

ul.item-list.activity-list li .activity-avatar img {
width: 50px;
height: 50px;
margin: 0;
}

.activity-list .activity-content {
    -moz-border-radius:6px 6px 6px 6px;
    -webkit-border-radius:6px;
    border-radius:6px;
    background:none;
	margin: 10px 0 10px 72px;
    min-height:15px;
    padding-bottom:10px;
}

body.activity-permalink .activity-list li .activity-content {
    background:none;
    border:medium none;
    margin-left:110px;
    margin-right:0;
    margin-top:17px;
    min-height:58px;
}
body.activity-permalink .activity-list li .activity-header > p {
    background: none;
    margin-left: -35px;
    padding: 0 0 0 38px;
    height: auto;
    margin-bottom: 0;
}

.activity-list .activity-content .activity-header,
.activity-list .activity-content .comment-header {
    color: #<?php echo $font_color;?>;
	line-height: 140%;
	padding: 5px 2px 5px 2px;
	min-height: 20px;
	overflow: auto;
}

.activity-list li.mini .activity-content .activity-header,
.activity-list li.mini .activity-content .comment-header {
	padding-top: 2px;
}

.activity-list .activity-content .activity-header img.avatar {
    float: none !important;
    margin: 0 5px -8px 0 !important;
}
span.highlight {
    border:none;
    color:#<?php echo $link_color;?>;
    margin-right:3px;
}

span.highlight:hover {
    background:none !important;
    border:none;
    color:#<?php echo $font_color;?>;
    color:#<?php echo $font_color;?> !important;
}

.activity-list .activity-content a:first-child:focus {outline: none}

.activity-list .activity-content span.time-since {
    color: #<?php echo $font_color;?>;
}

.activity-list .activity-content span.activity-header-meta a {
    background: none;
    padding: 0;
    font-size: 11px;
    margin: 0;
    border: none;
    color: #<?php echo $font_color;?>;
}
.activity-list .activity-content span.activity-header-meta a:hover {
    color: inherit;
}

body.activity-permalink .activity-content .activity-inner,
body.activity-permalink .activity-content blockquote {
    margin-top: 5px;
}

/* Backwards compatibility. */
.activity-inner > .activity-inner {margin: 0 !important}
.activity-inner > blockquote {margin: 0 !important}

.activity-list .activity-content img.thumbnail {
    float: left;
    margin: 0 10px 5px 0;
    border: 1px solid #<?php echo $body_bg_color;?>;
}

.activity-list li.load-more {
    -moz-border-radius:4px 4px 4px 4px;
    -webkit-border-radius:4px;
    border-radius:4px;
    background:none repeat scroll 0 0 transparent !important;
    border-bottom:medium none;
    border-right:medium none;
    font-size:1.2em;
    margin:15px 0 !important;
    padding:10px 15px !important;
    text-align:left;
}
.activity-list li.load-more a {
    color: #<?php echo $link_color;?>;
}

/* - additional to activity- */

.activity-list .activity-content .activity-inner, 
.activity-list .activity-content blockquote {
    -moz-border-radius: 6px;
    -webkit-border-radius: 6px;
    border-radius: 6px;
    background: none repeat scroll 0 0 #<?php echo $details_hover_bg_color;?>;
    border: 1px solid #<?php echo $container_alt_bg_color;?>;
    color: #<?php echo $font_color;?>;
    margin: 3px 0 15px 0;
    overflow: hidden;
    padding: 10px 20px;
}

.activity-list .activity-avatar {
	margin: 10px;
	overflow: auto;
	float: left;
}

.activity-list .activity-content .comment-header {
    color:#<?php echo $font_color;?>;
    line-height:170%;
    margin: 0;
    min-height:16px;
    padding-top:4px;
}
.activity-header a:hover {
    color:#<?php echo $font_color;?>;
}

.activity-list div.activity-meta a {
    -moz-border-radius:4px;
    -webkit-border-radius:4px;
    border-radius:4px;
    background:none repeat scroll 0 0 #<?php echo $font_color;?>;
    border-bottom:1px solid #<?php echo $font_color;?>;
    border-right:1px solid #<?php echo $font_color;?>;
    color:#<?php echo $container_bg_color;?>;
    margin-right:3px;
    padding:4px 8px;
    font-size:11px;
    text-decoration: none;
    font-family: arial, sans-serif;
}
.activity-list div.activity-meta a:hover,
.activity-list div.activity-meta a:focus {
    background:none repeat scroll 0 0 #<?php echo $link_color;?>;
    color:#<?php echo $container_bg_color;?>;
}
.activity-filter-selector {
    text-align: right;
}



/* > Activity Stream Comments
-------------------------------------------------------------- */

div.activity-meta {
    clear: left;
    margin: 0;
}

div.activity-comments {
    margin:0 0 0 70px;
    overflow:hidden;
    position:relative;
    width:auto;
}

body.activity-permalink div.activity-comments {
    width: auto;
    margin-left: 100px;
    background: none;
}

div.activity-comments > ul {
    -moz-border-radius:6px;
    -webkit-border-radius: 6px;
    border-radius: 6px;
    background:none;
    padding: 0 10px 0;
}

div.activity-comments ul, div.activity-comments ul li {
    border: none;
    list-style: none;
}

div.activity-comments ul {
    border-radius: 0 0 0 0;
    clear: left;
    margin-left: 2%;
}


div.activity-comments ul li {
    background: none repeat scroll 0 0 #<?php echo $details_hover_bg_color;?>;
    border: 1px solid #<?php echo $container_alt_bg_color;?>;
    -moz-border-radius:6px 6px 6px 6px;
    -webkit-border-radius:6px;
    border-radius:6px;
    margin-bottom: 10px;
    padding: 10px;
    margin-left: 1%;
}

body.activity-permalink div.activity-comments ul li {
    border-width: 1px;
    padding: 10px;
}

div.activity-comments ul li p:last-child {
    margin-bottom: 0;
}

div.activity-comments ul li:last-child {
    margin-bottom: 0;
}

div.activity-comments ul li > ul {
    margin-left: 54px;
    margin-top: 5px;
}
body.activity-permalink div.activity-comments ul li > ul {
    margin-top: 15px;
}


div.acomment-avatar img {
    border:1px solid #<?php echo $body_bg_color;?> !important;
    float:left;
    margin-right:10px;
}

div.activity-comments div.acomment-content {
    font-size: 11px;
    background:none repeat scroll 0 0 transparent;
    color:#<?php echo $font_color;?>;
    margin:10px 10px 10px 0;
    overflow:hidden;
    padding:4px 0;
}
div.acomment-options {
    margin-left: 63px;
}

div.acomment-content .time-since {display: none}
div.acomment-content .activity-delete-link {display: none}
div.acomment-content .comment-header {display: none}

body.activity-permalink div.activity-comments div.acomment-content {
    font-size: 14px;
}

div.activity-comments div.acomment-meta {
    font-size: 13px;
    color: #<?php echo $font_color;?>;
}

div.activity-comments form.ac-form {
    display: none;
    margin: 10px 0 10px 33px;
    background:none repeat scroll 0 0 #ededed;
    border:medium none;
    -moz-border-radius: 4px;
    -webkit-border-radius: 4px;
    border-radius: 4px;
    padding: 8px;
    width: 80%;
}
div.activity-comments li form.ac-form {
    margin-right: 15px;
}

div.activity-comments form.root {
    margin-left: 0;
}

div.activity-comments div#message {
    margin-top: 15px;
    margin-bottom: 0;
}

div.activity-comments form.loading {
    background-image: url(<?php echo get_template_directory_uri() ?>/images/ajax-loader.gif);
    background-position: 2% 95%;
    background-repeat: no-repeat;
}

div.activity-comments form .ac-textarea {
    padding: 8px;
    border: 1px inset #cccccc;
    background: #ffffff !important;
    margin-bottom: 10px;
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
    border-radius: 3px;
}
div.activity-comments form textarea {
    width: 100%;
    font-family: inherit;
    font-size: 11px;
    color: #<?php echo $font_color;?>;
    height: 60px;
    border: none;
    padding: 0;
}
div.activity-comments form input {
    margin-top: 5px;
}

div.activity-comments form div.ac-reply-avatar {
    float: left;
}
div.ac-reply-avatar img {
    border: 1px solid #<?php echo $body_bg_color;?> !important;
}

div.activity-comments form div.ac-reply-content {
    margin-left: 44px;
    padding-left: 15px;
    color: #<?php echo $font_color;?>;
    font-size: 11px;
}

div.activity-comments div.acomment-avatar img {
    border-width:1px !important;
    float:left;
    margin-right:10px;
}

ul.button-nav, .button-nav li{
    list-style: none;
}
/* > Private Message Threads
-------------------------------------------------------------- */

table#message-threads tr.unread td {
    background: #<?php echo $container_bg_color;?>;
    border-top: 1px solid #<?php echo $body_bg_color;?>;
    border-bottom: 1px solid #<?php echo $body_bg_color;?>;
    font-weight: bold;
}
table#message-threads tr.unread td span.activity {
    background: #<?php echo $body_bg_color;?>;
}

li span.unread-count, tr.unread span.unread-count {
    background: #<?php echo $container_bg_color;?>;
    padding: 2px 8px;
    color: #<?php echo $font_color;?>;
    font-weight: bold;
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
    border-radius: 3px;
}
div.item-list-tabs ul li a span.unread-count {
    padding: 1px 6px;
    color: #<?php echo $font_color;?>;
}

div.messages-options-nav {
    font-size: 11px;
    background: #<?php echo $container_bg_color;?>;
    text-align: right;
    margin: 0 -20px;
    padding: 5px 15px;
}

div#message-thread div.message-box {
    margin: 0 -20px;
    padding: 15px;
}
div#message-thread div.alt {
    background: #<?php echo $container_bg_color;?>;
}

div#message-thread p#message-recipients {
    margin: 10px 0 20px 0;
}

div#message-thread img.avatar {
    float: left;
    margin: 0 10px 0 0;
    vertical-align: middle;
}

div#message-thread strong {
    margin: 0;
    font-size: 16px;
    margin-left: 15px;
}

div#message-thread strong span.activity {
    margin: 4px 0 0 10px;
}

div#message-thread div.message-metadata {
    overflow: hidden;
}

div#message-thread div.message-content {
    margin-left: 45px;
}

div#message-thread div.message-options {
    text-align: right;
}

/* > Group Forum Topics
-------------------------------------------------------------- */

ul#topic-post-list {
    margin: 15px -20px;
    width: auto;
}
ul#topic-post-list li {
    padding: 15px;
    position: relative;
}

ul#topic-post-list li.alt {
/*background: #ededed;*/
}

ul#topic-post-list li div.poster-meta {
    margin-bottom: 10px;
    color: #<?php echo $font_color;?>;
}

ul#topic-post-list li div.post-content {
    margin-left: 54px;
}

div.admin-links {
    position: absolute;
    top: 15px;
    right: 25px;
    color: #<?php echo $font_color;?>;
    font-size: 11px;
}
div#topic-meta div.admin-links {
    bottom: 0;
    margin-top: -52px;
    right: 0;
}

div#topic-meta {
    position: relative;
    padding: 5px 0;
}
div#topic-meta h3 {
    font-size: 20px;
    padding-bottom: 20px;
}


div#new-topic-post {
    margin: 0;
    padding: 1px 0 0 0;
}

div.poster-name a {
    color:#<?php echo $font_color;?>;
}

div.object-name a {
    color:#<?php echo $font_color;?>;
}


/* > Extra BuddyPress Styles
-------------------------------------------------------------- */

ul#friend-list li {
    height: 53px;
}

ul#friend-list li div.item-meta {
    width: 70%;
}




/* > WordPress Blog Styles
-------------------------------------------------------------- */

div.post {
    margin:2px 0 0px 0;
    overflow: hidden;
}
div.post h2.pagetitle, div.post h2.posttitle {
    margin: 0px 0 20px 0;
    line-height: 120%;
}
.navigation, .paged-navigation, .comment-navigation {
    overflow: hidden;
    font-style:normal;
    font-weight:normal;
    font-size: 13px;
    padding: 5px 0;
    margin: 5px 0 25px 0;
}
div.post p {margin: 0 0 20px 0}
div.post ul, div.post ol, div.post dl {margin: 0 0 15px 20px}
div.post ul, div.page ul {list-style: circle outside none;margin: 0 0 15px 20px}
div.post ol, div.page ol {list-style: decimal outside none;margin: 0 0 15px 20px}
div.post ol ol {list-style: upper-alpha outside none}
div.post dl {margin-left: 0}
div.post dt {
    border-bottom:1px solid #<?php echo $body_bg_color;?>;
    font-size:14px;
    font-weight:bold;
    overflow:hidden;
}
div.post dd {
    -moz-border-radius:0 0 6px 6px;
    border-radius:0 0 6px 6px;
    -webkit-border-bottom-left-radius: 6px;
    -webkit-border-bottom-right-radius: 6px;
    background:none repeat scroll 0 0 #<?php echo $container_bg_color;?>;
    font-size:11px;
    line-height:12px;
    margin:0 0 15px;
    padding:4px;
}

div.post pre, div.post code p {
    padding: 15px;
    background: #<?php echo $container_bg_color;?>;
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
    border-radius: 3px;
}

div.post code {font-family: "Monaco", courier, sans-serif}
div.post blockquote {
    quotes: none;
    font-style:italic;
    padding:0 3em;
    font-family: georgia, times, serif;
    font-size: 16px;
    line-height: 150%;
}

div.post table {
    border-collapse:collapse;
    border-spacing:0;
    border: 1px solid #<?php echo $body_bg_color;?>;
}
div.post table th {border-top: 1px solid #<?php echo $body_bg_color;?>;text-align: left}
div.post table td {border-top: 1px solid #<?php echo $body_bg_color;?>}

div.post div.post-content {
	margin-left: 20px;
	width: 86%;
	float: left;
	padding: 0px 5px 10px 15px;
}
.left-right-sidebar div.post div.post-content {
	width: 79%;
}
.search-result div.post div.post-content{
    width: 100%; 
}
#activate-page.page, #register-page.page{
    margin-left: 20px;
}
#activate-page.page #activation-form, #register-page #signup_form{
    margin-left: 0;
}
div.post p.date, div.post p.postmetadata, div.comment-meta {
    color: #<?php echo $font_color;?>;
    font-size: 12px;
    padding: 3px 0;
    margin: 10px 0;
    border-bottom: none;
    border-top: 1px solid #<?php echo $container_alt_bg_color;?>;
}
div.post p.date a, div.post p.postmetadata a, div.comment-meta a, div.comment-options a {
    font-size: 12px;
}

div.post p.date a:hover, div.post p.postmetadata a:hover, div.comment-meta a:hover, div.comment-options a:hover {
    color: #<?php echo $font_color;?>;
    font-size: 12px;
}

div.post p.date em {
    font-style: normal;
}

div.post p.postmetadata {
    margin-top: 15px;
    clear: left;
    overflow: hidden;
}

div.post .tags {float: left}
div.post .comments {float: right}

div.post img {margin: 15px 0;border: none;border: none !important}
div.post img.wp-smiley {padding: 0 !important;margin: 0 !important;border: none;float: none !important;clear: none !important}

div.post img.centered, img.aligncenter {
    display: block;
    margin-left: auto;
    margin-right: auto;
}

div.post img.alignright {
    padding: 4px;
    margin: 0 0 2px 7px;
    display: inline;
}

div.post img.alignleft {
    padding: 0 12px 12px 0;
    margin: 0 7px 2px 0;
    display: inline;
}

div.post .aligncenter, div.post div.aligncenter {
    display: block;
    margin-left: auto;
    margin-right: auto;
}

div.post .wp-caption {
    border: 1px solid #<?php echo $body_bg_color;?>;
}

div.post .wp-caption img {
    margin: 0;
    padding: 0;
    border: 0 none;
}

div.post img.size-full {
       height: auto;
       max-width: 100%;
}

div.author-box, div.comment-avatar-box {
    width:50px;
    float:left;
}

div.author-box p,
div.author-box a,
div.comment-avatar-box p,
div.comment-avatar-box a {
    font-size: 10px;
    font-style: normal;
    line-height: 120%;
    margin: 5px 0 0;
    text-align: center;
    width: 50px;
}

div.post div.author-box img {
    float: none;
    border: 1px solid #<?php echo $body_bg_color;?>;
    margin: 0;
    background:none repeat scroll 0 0 transparent;
    float: none;
    padding:0;
    width:50px;
}



/* > WordPress & BuddyPress Comment Styles
-------------------------------------------------------------- */

div#comments nav {
    height: auto;
    overflow: auto;
    padding-bottom: 15px;
}

div.nav-previous {
    width: 50%;
    float: left;
    text-align: left;
}
div.nav-next {
    float: left;
    width: 50%;
    text-align: right;
}

div.comment-avatar-box img {
    float: none;
    border: 1px solid #<?php echo $body_bg_color;?>;
    margin: 16px 0 0 4px;
    background:none repeat scroll 0 0 transparent;
    float: none;
    padding:0;
}

div.comment-content {
    margin-left: 75px;
    min-height: 110px;
}

#trackbacks {
    margin-top: 30px;
}

#comments h3, #trackbacks h3, #respond h3 {
    font-size: 20px;
    margin: 5px 0 15px 0;
    font-weight: normal;
    color: #<?php echo $font_color;?>;
}

#comments span.title, #trackbacks span.title {
    color: #<?php echo $font_color;?>;
}

div.post ol.commentlist,
div.page ol.commentlist {
    list-style: none outside none;
    margin-left: 0;
}

div.post ol.commentlist ul,
div.page ol.commentlist ul {
    list-style: disc inside none;
    margin-left: 0px;
    padding-bottom: 12px;
}

ol.commentlist li {
    margin: 0 0 20px 0;
    border-top: 1px solid #<?php echo $container_alt_bg_color;?>;
}

.commentlist ul li {
    padding: 0 12px;
    background: #<?php echo $details_hover_bg_color;?>;
}
.commentlist ul ul li {
    padding: 0 12px;
    background: #<?php echo $container_bg_color;?>;
}
.commentlist ul ul ul li {
    padding: 0;
}

div.comment-meta {
    border-top: none;
    padding-top: 0;
}

div.comment-meta h5 {
    font-weight: normal;
}

div.comment-meta em {
    float: right;
}

div.post .commentlist div.comment-content ol {
    list-style: decimal outside none;
    margin-bottom: 0;
    padding-bottom: 6px;
}

div.post .commentlist div.comment-content ul {
    list-style: circle outside none;
    margin-bottom: 0;
    padding-bottom: 6px;
}

div.post .commentlist div.comment-content li {
    border: none;
    margin-bottom: 0;
}

p.form-allowed-tags {
    display: none;
}

#comments textarea {
    width: 90%;
}

/* > Additional WP comment styles
-------------------------------------------------- */


div.comment-author img.avatar {
        margin: 4px 12px 12px -45px;
}
div.comment-body div.commentmetadata {
        margin-top:0;
}
div.comment-body div.comment-author {
        padding-top:6px;
}
div.reply {
    height: 32px;
}
div.comment-body {
    margin-bottom: 12px;
    margin-left: 45px;
}
div.post div.commentmetadata a.comment-edit-link {
    float:right;
    line-height: 120%;
    padding: 3px 5px;
}

ul.children li.comment {
    margin-left: 26px;
}

div.post .commentlist div.comment-body ol {
    list-style: decimal outside none;
    margin-bottom: 0;
    padding-bottom: 6px;
}

div.post .commentlist div.comment-body ul {
    list-style: circle outside none;
    margin-bottom: 0;
    padding-bottom: 6px;
}

.commentlist div.comment-body li {
border:none;
margin: 0;
}

#blog-page .title-center{
    text-align: center !important;
}


/* > Footer
-------------------------------------------------------------- */

#footer{
    text-align:left;
    text-shadow:none;
    margin-top:8px;
    -moz-border-radius: 6px;
    -webkit-border-radius: 6px;
    border-radius: 6px;
    margin-bottom: 8px;
    padding: 0px;
}
#footer div.credits, #footer a.credits, #footer a.credits:hover, #footer a.credits:focus {
    text-align: center;
    text-decoration: none;
    background-color: transparent;
    color: #<?php echo $font_alt_color;?>
}
#footer span.credits {
    text-align: center;
}

#footer div.span3 h3.widgettitle, #header div.span3 h3.widgettitle,
#footer div.span3 h3.widgettitle a, #header div.span3 h3.widgettitle {
    width:100%;
    -moz-border-radius:0 !important;
    -webkit-border-radius:0 !important;
    border-radius:0 !important;
    margin-left: -15px !important;
    padding-left:15px !important;
}
div#content div.widgetarea h3.widgettitle,
div#content div.widgetarea h3.widgettitle a {
    background: none !important;
}


/* > Widgets
-------------------------------------------------------------- */

.widget {
    margin-bottom: 20px;
}

div.widget ul li, div.span3 ul li {
    background:none repeat scroll 0 0 transparent;
    border-bottom:medium none;
    min-height:20px;
    margin-bottom:5px;
    list-style: none outside none;
    padding: 5px 10px 0;
}
div.widget ul#groups-list li{
    min-height:50px;
    width:100%;
    margin-bottom:0 !important;
}
ul#groups-list li{
    padding: 10px 0;
}
div.widget ul#members-list li {
    min-height:64px;
    width:189px;
    margin-bottom:0 !important;
}
div.widget ul li.vcard a {
    float: left;
}
li.vcard, div.widget ul.item-list li {
    padding:0px !important;
    clear: both;
}
div.widget ul.item-list li .item-avatar{
    width: 25px;
    height: 25px;
    margin-right: 10px;
}
div.widget ul.item-list li .item{
    width: 100%;
    margin-right: -35px;
}
div.widget_bp_core_whos_online_widget .item-avatar{
    margin: 5px;
}
div.widget ul.item-list li .item-avatar,
div.widget ul.item-list li .item {
    float: left;
}
div.widget ul#blog-post-list li{
    border-bottom:1px solid #<?php echo $body_bg_color;?>;
}

div.widget ul#blog-post-list li,
div.widget ul#blog-post-list li p,
div.widget ul#blog-post-list li a,
div.widget ul#blog-post-list li div {
    height:auto;
    background:none;
}

div.widget ul#blog-post-list li a{
    font-weight:normal;
}
div.widget_pages ul li {
    min-height:20px;
    height:auto;
    line-height:150%;
    padding-top:4px;
}
div.widget_tag_cloud div {
    padding:8px 10px 8px 0;
}
div.widget ul.children,
div.widget ul.children ul {
    margin-left: 12px;
    margin-top: 4px;
}
div.widget ul li a {
}
div.widget ul li a:hover {
}
div.widget ul li.recentcomments a {
    font-weight:normal;
}
div.widget ul li.recentcomments a:hover {
    font-weight:normal;
}
select#cat {
    width:100%;
}
div.widget ul.item-list li div.item-title {
    margin-top: 3px;
}
div.widget ul li a.rsswidget {
    line-height:17px;
}
div.textwidget {
    padding: 0 10px 10px 0;
}
#header .row-fluid .span4,
#content .row-fluid .span4,
#footer .row-fluid .span4{
    margin-left: 10px;
    width: 30%;
    float: left;
}
#header .row-fluid h3.widgettitle,
#content .row-fluid h3.widgettitle,
#footer .row-fluid .span4 h3.widgettitle{
    width: 95%;
}
#community-nav ul li {
    margin-left: 10px;
    margin-top: 10px;
}
/* =Calendar Widget
-------------------------------------------------------------- */
div.widget table thead tr {
    background:none repeat scroll 0 0 #<?php echo $container_bg_color;?>;
}
div.widget table tr td, div.widget table tr th {
    padding: 3px 5px;
    vertical-align: middle;
    border:none;
}
div#sidebar div#calendar_wrap, div.widgetarea div#calendar_wrap{
    margin-left:5px;
}

/* =Menu Top
-------------------------------------------------------------- */

div#header div.menu-top {
    font-size: 13px;
    margin-left: 0;
    width: 100%;
}

div.menu-top.menu ul {
    list-style: none;
    margin: 0;
    float: right;
}

div.menu-top li {
    float: left;
    position: relative;
    list-style:none outside none;
    margin:4px 4px 0 0;
}

div.menu-top a {
    color: #<?php echo $link_color;?>;
    display: block;
    line-height: 30px;
    padding: 0 15px 2px 15px;
    text-decoration: none;
    background-color: transparent;
}

div.menu-top ul ul {
    display:none;
    float:left;
    left:0;
    position:absolute;
    top:27px;
    width:180px;
    z-index:1000000;
}

div.menu-top ul li ul li {
    min-width: 180px;
    z-index:1000000;
    margin-top:0px !important;
}

div.menu-top ul ul ul {
    left: 100%;
    top: 0;
}

div.menu-top ul ul a {
    background: #<?php echo $body_bg_color;?>;
    color: #<?php echo $link_color;?>;
    line-height: 1em;
    padding: 10px 15px;
    width: 160px;
    height: auto;
}

div.menu-top li:hover > a,
div.menu-top ul ul:hover > a {
    color: #<?php echo $font_color;?>;
}

div.menu-top ul.children li:hover > a,
div.menu-top ul.sub-menu li:hover > a {
    background: #<?php echo $details_hover_bg_color;?> !important;
    color: #<?php echo $font_color;?>;
    border-radius:0px;
}

div.menu-top ul li:hover > ul {
    display: block;
}
div.menu-top ul li.current_page_item > a,
div.menu-top ul li.current-menu-ancestor > a,
div.menu-top ul li.current-menu-item > a,
div.menu-top li.selected > a,
div.menu-top ul li.current-menu-parent > a,
div.menu-top ul li.current_page_item > a:hover,
div.menu-top ul li.current-menu-item > a:hover {
    background:none repeat scroll 0 0 #<?php echo $body_bg_color;?>;
    color:#<?php echo $font_color;?>;
}
* html div.menu-top ul li.current_page_item a,
* html div.menu-top ul li.current-menu-ancestor a,
* html div.menu-top ul li.current-menu-item a,
* html div.menu-top ul li.current-menu-parent a,
* html div.menu-top ul li a:hover {
    color: #<?php echo $font_color;?>;
}




/* =Menu
-------------------------------------------------------------- */

#access {
    background:#<?php echo $details_bg_color;?>;
    display:block;
    float:left;
    padding-top:6px;
    width:100%;
    position: absolute;
    bottom: 0;
    margin: 0 0 -40px 0;
}
#access ul li {
	margin-right: 4px;
}
#access .menu-header,
div.menu {
    font-size: 13px;
    margin-left: 12px;
    width: 100%;
}

#access .menu-header ul,
div.menu ul {
    list-style: none;
    margin: 0;
}
div.menu ul {
    float:left;
}
#access .menu-header li,
div.menu li {
    list-style:none outside none;
    -moz-border-radius-topleft: 6px;
    -moz-border-radius-topright: 6px;
    -webkit-border-top-left-radius:6px;
    -webkit-border-top-right-radius:6px;
    border-top-left-radius:6px;
    border-top-right-radius:6px;
    float: left;
    position: relative;
}
div.menu .span2{
    width: auto;
    margin-left: 0;
}
#access a {
    color: #<?php echo $font_color;?>;
    display: block;
    line-height: 30px;
    padding: 0 15px 2px 15px;
    -moz-border-radius:6px 6px 0 0;
    -webkit-border-top-left-radius:6px;
    -webkit-border-top-right-radius:6px;
    border-top-left-radius:6px;
    border-top-right-radius:6px;
    text-decoration: none;
    background-color: transparent;
}
#access ul ul {
    -moz-box-shadow:0 3px 3px rgba(0, 0, 0, 0.2);
    -webkit-box-shadow:0 3px 3px rgba(0, 0, 0, 0.2);
    box-shadow:0 3px 3px rgba(0, 0, 0, 0.2);
    display:none;
    float:left;
    left:0;
    position:absolute;
    top:27px;
    width:180px;
    z-index:1000000;
}
#access ul li ul li {
    min-width: 180px;
    z-index:1000000;
    margin-top:0px !important;
}
#access ul ul ul {
    left: 100%;
    top: 0;
}
#access ul ul a {
    -moz-border-radius:0px !important;
    -webkit-border-radius:0px !important;
    border-radius:0px !important;
    background: #<?php echo $body_bg_color;?>;
    color: #<?php echo $font_color;?>;
    line-height: 1em;
    padding: 10px 15px;
    width: 160px;
    height: auto;
}
#access li:hover > a,
#access ul ul :hover > a {
    background: #<?php echo $body_bg_color;?>;
    color: #<?php echo $font_color;?>;
}
#access ul.children li:hover > a,
#access ul.sub-menu li:hover > a {
    background: #<?php echo $details_hover_bg_color;?> !important;
    color: #<?php echo $font_color;?>;
    -moz-border-radius:0px;
    -webkit-border-radius:0px;
    border-radius:0px;
}

#access ul li:hover > ul {
    display: block;
}
#access ul li.current_page_item > a,
#access ul li.current-menu-ancestor > a,
#access ul li.current-menu-item > a,
#access li.selected > a,
#access ul li.current-menu-parent > a,
#access ul li.current_page_item > a:hover,
#access ul li.current-menu-item > a:hover {
    background:none repeat scroll 0 0 #<?php echo $body_bg_color;?>;
    color:#<?php echo $font_color;?>;
}
* html #access ul li.current_page_item a,
* html #access ul li.current-menu-ancestor a,
* html #access ul li.current-menu-item a,
* html #access ul li.current-menu-parent a,
* html #access ul li a:hover {
    color: #<?php echo $font_color;?>;
}

/* =Slider
-------------------------------------------------------------- */
div#cc_slider-top {
    -moz-border-radius:6px;
    -webkit-border-radius:6px;
    border-radius:6px;
	background:#<?php echo $container_bg_color;?>;
    background-repeat:repeat-y;
    border:medium none;
    width: 100%;
    height: 249px;
	overflow: hidden;
	margin-bottom: 12px;
}
div.cc_slider {
    margin-bottom: 0;
    overflow: hidden;
    margin-left: 0;
    margin-top: -1px;
}
div.cc_slider.cc_slider_shortcode {
    margin-bottom: 12px;
}
.slidershadow {
	height: 34px;
	margin-top: -12px;
}

div.cc_slider .featured{
    width:100%;
    padding-right:248px;
    position:relative;
    height:250px;
    float: left;
    margin-bottom: 20px;
}
div.cc_slider div.featured{
    margin-bottom: 0px;
}
div.cc_slider .featured .ui-tabs-panel a{
    display: block;
    width: 100%;
}
div.cc_slider .featured .ui-tabs-panel a img{
    width: 100%;
    <?php if ( $cap->slideshow_style == "default" ) { ?>
    	border-radius: 6px 0 0 6px;
    <?php } else { ?>
    	border-radius: 6px;
    <?php } ?>	
}
div.cc_slider ul.ui-tabs-nav {
    list-style: none outside none;
    margin: 0;
    padding: 1px;
    position: absolute;
    right: 0;
    top: 0;
    width: 25%;
}
div.cc_slider ul.ui-tabs-nav li{
    padding: 0px 2px 1px 13px;
    font-size:12px;
    color:#<?php echo $font_color;?>;
    height: 62px;
    background:none transparent;
    border: none;
    float:none;
    margin: 0;
}
div.cc_slider ul.ui-tabs-nav li img {
    float:left;margin:2px 5px 2px 0;
    background:#<?php echo $container_bg_color;?>;
    padding:2px;
    border:1px solid #<?php echo $container_alt_bg_color;?>;
}
div.cc_slider ul.ui-tabs-nav li span{
    font-size:13px;
    line-height:19px;
}
div.cc_slider li.ui-tabs-nav-item a{
    display:block;
    height:60px;
    color:#<?php echo $font_color;?> !important;
    background:#<?php echo $container_bg_color;?>;
    font-weight: normal;
    line-height:20px;
    padding: 0 2px;
    width:100%;
    overflow: hidden;
}
div.cc_slider a, div.cc_slider a:hover, div.cc_slider a:focus {
    text-decoration: none;
    background-color: transparent;
}
div.cc_slider li.ui-tabs-nav-item a:hover{
    background:#<?php echo $details_hover_bg_color;?>;
}
div.cc_slider ul.ui-tabs-nav li.ui-tabs-active{
    background:url(<?php echo get_template_directory_uri() ?>/images/<?php cc_color_scheme();?>/selected-item.png) top left no-repeat transparent;
}
div.cc_slider ul.ui-tabs-nav li.ui-tabs-active a{
    background:#<?php echo $container_alt_bg_color;?>;
}
div.cc_slider .featured .ui-tabs-panel{
    height: 250px;
    overflow:hidden;
    background:#<?php echo $container_bg_color;?>;
    position:relative;
    padding:0;
    border: medium none;
    border-radius: 0 0 0 0;
}
div#cc_slider-top div.cc_slider .featured .ui-tabs-panel{
    margin-left: 0;
}
div.cc_slider .featured .ui-tabs-panel .info{
    position:absolute;
    top:170px;
    left:0;
    height:80px;
    background: url(<?php echo get_template_directory_uri() ?>/images/slideshow/transparent-bg.png);
    margin-left: 0;
    width:100%;
    border-radius: 0 0 0 6px;
}
div.cc_slider .featured .info h2 > a{
    font-size:18px;
    color: #ffffff;
    color: #ffffff !important;
    overflow:hidden;
    font-family: arial, sans-serif;
}
div.cc_slider .featured .info h2 {
    padding:2px 2px 2px 5px;
    margin:0;
    line-height:100%;
    overflow:hidden;
}
div.cc_slider .featured .info p{
    margin:0 5px;
    font-size:13px;
    line-height:15px;
    color:#ffffff;
    font-family: arial, sans-serif;
}
div.cc_slider .featured .info a{
    color:#<?php echo $body_bg_color;?>;color:#<?php echo $body_bg_color;?> !important;
    padding-left:0;
}
div.cc_slider .featured .info a:hover{
}
div.cc_slider .featured .ui-tabs-hide{
    display:none;
}

div.cc_slider .ui-tabs {
    padding: 0;
    position: relative;
}

div.cc_slider .ui-corner-all {
    border: medium none;
    border-radius: 0 0 0 0;
}

div.cc_slider .ui-widget-header {
    background: none repeat scroll 0 0 transparent;
    border: medium none;
    font-weight: normal;
}
<?php if($cap->slideshow_style == 'full width' || $cap->slideshow_style == __('full width','cc') || $cap->slideshow_style == 'full-width-image' ){?>
div#cc_slider-top div.cc_slider .featured .ui-tabs-panel{
    width: 100%;
}
<?php }

 ?>
div.post img {
    margin: 0 0 1px 0;
}


/* =list posts templates
-------------------------------------------------------------- */

.listposts {
    width:100%;
}

.list-posts-all {
    width:100%;
    margin-bottom:25px;
}



/* =list posts img mouse over effect
-------------------------------------------------------------- */
.boxgrid {
    -moz-background-clip: border;
    -moz-background-inline-policy: continuous;
    -moz-background-origin: padding;
    -moz-border-radius: 6px;
    -webkit-border-radius: 6px;
    border-radius: 6px;
    width: 222px;
    height: 160px;
    float: left;
    background: #161613;
    border: solid 1px #777;
    overflow: hidden;
    position: relative;
    float:left;
    margin: 20px 5px 0 0;
}

#content .boxgrid img {
    -moz-background-clip: border;
    -moz-background-inline-policy: continuous;
    -moz-background-origin: padding;
    -moz-border-radius: 6px;
    -webkit-border-radius: 6px;
    border-radius: 6px;
    position: absolute;
    top: 0;
    left: 0;
    border: 0;
}

.boxgrid p, .boxgrid p a {
    padding: 0 0 0 5px;
    color: #ffffff;
    font: 11px Arial, sans-serif;
}
div.boxgrid h3 > a {color:#ffffff;font:12px Arial, sans-serif;letter-spacing:0;font-weight: bold;padding-left:0px}
.boxgrid h3 {margin: 5px 5px 5px 0px}

.boxcaption {
    -moz-background-clip: border;
    -moz-background-inline-policy: continuous;
    -moz-background-origin: padding;
    -moz-border-radius:  0 0 6px 6px;
    -webkit-border-bottom-left-radius: 6px;
    -webkit-border-bottom-right-radius: 6px;
    border-bottom-left-radius: 6px;
    border-bottom-right-radius: 6px;
    float: left;
    position: absolute;
    background: #000;
    height: 80px;
    width: 100%;
    opacity: .8;
/* For IE 5-7 */
    filter: progid:DXImageTransform.Microsoft.Alpha(Opacity=80);
/* For IE 8 */
    -MS-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=80)";
}
.captionfull .boxcaption {
    top: 0;
    left: 0;
}
.caption .boxcaption {
    top: 0;
    left: 0;
}
.cover{
    margin-top:170px;
}

.boxgrid {
    border:1px solid #<?php echo $body_bg_color;?> !important;
}


/* =list posts - posts-img-left-content-right
-------------------------------------------------------------- */

div.posts-img-left-content-right {
    padding:20px 0 0 0;
}

div.posts-img-left-content-right img.wp-post-image {
    border:1px solid #<?php echo $body_bg_color;?>;
    float:left;
    margin-bottom:0;
    margin-right:25px;
    margin-top:2px;
}

div.posts-img-left-content-right h3 a {
    font-size:20px;
}

div.posts-img-left-content-right a{
}


/* =list posts - posts-img-right-content-left
-------------------------------------------------------------- */

div.posts-img-right-content-left {
    padding:20px 0 0 0;
    float:right;
}

div.posts-img-right-content-left img.wp-post-image {
    float:right;
    border:1px solid #<?php echo $body_bg_color;?>;
    margin-bottom:0;
    margin-top:2px;
    margin-left:25px;
}

div.posts-img-right-content-left h3 a {
    font-size:20px;
}

div.posts-img-right-content-left a{
}


/* =list posts - posts-img-over-content
-------------------------------------------------------------- */

div.posts-img-over-content {
    float:left;
    padding:20px 0 0;
    width:33%;
}

div.posts-img-over-content img.wp-post-image {
    border:1px solid #<?php echo $body_bg_color;?>;
    margin-bottom:12px;
    margin-right:25px;
    margin-top:2px;
}

div.posts-img-over-content h3 a {
    font-size:20px;
}

div.posts-img-over-content h3 {
    width:222px;
    max-width:222px;
    padding-top:8px;
    border-top:1px solid #<?php echo $body_bg_color;?>;
}


div.posts-img-over-content a{
}

div.posts-img-over-content p{
    padding-right:20px;
    text-align: justify;
}


/* =list posts - posts-img-under-content
-------------------------------------------------------------- */

div.posts-img-under-content {
    float:left;
    padding:20px 0 0;
    width:242px;
}

div.posts-img-under-content img.wp-post-image {
    border:1px solid #<?php echo $body_bg_color;?>;
    margin-bottom:0;
    margin-right:25px;
    margin-top:5px;
}

div.posts-img-under-content h3 a {
    font-size:20px;
}

div.posts-img-under-content h3 {
    width:222px;
    max-width:222px;
    padding-top:8px;
    border-top:1px solid #<?php echo $body_bg_color;?>;
}

div.posts-img-under-content a{
}

div.posts-img-under-content p {
    padding-right:0;
    width:222px;
}

/* Single Post Templatres
*
*/


/* =single single-img-left-content-right
-------------------------------------------------------------- */

div.single-img-left-content-right {
    padding:5px 0 0 0;
}

div.single-img-left-content-right img.wp-post-image {
    border:1px solid #<?php echo $body_bg_color;?>;
    float:left;
    margin-bottom:20px;
    margin-right:25px;
    margin-top:5px;
}

div.single-img-left-content-right h3 a {
    font-size:20px;
}

div.single-img-left-content-right a{
}


/* =single single-img-right-content-left
-------------------------------------------------------------- */

div.single-img-right-content-left {
    padding:20px 0 0 0;
    float:right;
}

div.single-img-right-content-left img.wp-post-image {
    float:right;
    border:1px solid #<?php echo $body_bg_color;?>;
    margin-bottom:20px;
    margin-top:5px;
    margin-left:25px;
}

div.single-img-right-content-left h3 a {
    font-size:20px;
}

div.single-img-right-content-left a{
}


/* =single single-img-over-content
-------------------------------------------------------------- */

div.single-img-over-content {
    padding:20px 0 0 0;
}

div.single-img-over-content img.wp-post-image {
    border:1px solid #<?php echo $body_bg_color;?>;
    margin-bottom:20px;
    margin-right:25px;
    margin-top:5px;
}

div.single-img-over-content h3 a {
    font-size:20px;
}

div.single-img-over-content a{
}


/* =single single-img-under-content
-------------------------------------------------------------- */

div.single-img-under-content {
    padding:20px 0 0 0;
}

div.single-img-under-content img.wp-post-image {
    border:1px solid #<?php echo $body_bg_color;?>;
    margin-bottom:20px;
    margin-right:25px;
    margin-top:5px;
}

div.single-img-under-content h3 a {
    font-size:20px;
}

div.single-img-under-content a{
}


/* =column shortcodes
-------------------------------------------------------------- */

.full_width_col {
    width:99.6%;
    margin:0 0.4% 20px 0;
}

.half_col_left {
    float:left;
    margin:0 1.4% 20px 0;
    padding:0;
    width:48%;
}

.half_col_right {
    float:right;
    margin:0 0.4% 20px 1.4%;
    padding:0;
    width:48%;
}
.third_col {
    float:left;
    margin:0 3.3% 20px 0;
    padding:0;
    width:31%;
}
.third_col_right {
    float:right;
    margin:0 0.4% 20px 0;
    padding:0;
    width:31%;
}


/*= Accordion (accordion)
-------------------------------------------------------------- */

.accordion {
    width: 100%;
    border-bottom: solid 1px #c4c4c4;
    clear:both;
    margin-top:20px;
}
.accordion h3 {
    background:url(<?php echo get_template_directory_uri() ?>/images/arrow-square.gif) no-repeat scroll 4px 50% #FFFFFF;
    border-color:#C4C4C4;
    border-style:solid solid none;
    border-width:1px 1px medium;
    cursor:pointer;
    margin:0;
    padding:7px 24px;
}
.accordion h3:hover {
    background-color: #<?php echo $body_bg_color;?>;
}
.accordion h3.active {
    background:url('<?php echo get_template_directory_uri() ?>/images/arrow-square-on.gif') no-repeat scroll #<?php echo $body_bg_color;?>;
    background-position:4px 50%;
}
.accordion p {
    margin-bottom: 0;
}
.accordion div {
    background: #ffffff;
    margin: 0px !important;
    padding: 20px;
    border-left: solid 1px #c4c4c4;
    border-right: solid 1px #c4c4c4;
}
.accordion div div {
    background: #ffffff;
    margin: 15px 0 0 !important;
    padding: 0;
    border-left: none;
    border-right: none;
}
.accordion h4{
    line-height:170%;
    background-color:#ffffff;
    color:#888888;
    border:1px solid #c4c4c4;
    font-size:21px;
    padding:2px 5px;
}
.accordion div p{
    margin-bottom: 10px;
}
.accordion br{
    line-height: 0px;
}
.accordion br:last-of-type{
    display: none;
}

div.announcement {
    float:right;
    height:60px;
    padding:10px;
    position:absolute;
    right:354px;
    text-align:center;
    top:120px;
    width:230px;
    font-size:30px;
    line-height:170%;
}

div.announcement a {
    font-size:30px;
    line-height:170%;
}

/* =Images
-------------------------------------------------------------- */

#content .gallery {
    margin: 0 auto 18px;
}
#content .gallery .gallery-item {
    float: left;
    margin-top: 0;
    text-align: center;
    width: 33%;
}
#content .gallery img {
    border: none;
    margin-top:20px;
}
#content .gallery .gallery-caption {
    color: #<?php echo $font_color;?>;
    font-size: 12px;
    margin: 0 0 20px;
}
#content .gallery dl {
    margin: 0;
}
#content .gallery br+br {
    display: none;
}

/* =single attachment images should be centered */

#content .attachment img {
    display: block;
    margin: 0 auto;
}

/* =Search View
-------------------------------------------------------------- */

body.search div.post div.post-content, body.search div.comment-content {
    margin-left: 0;
}

div.search-result {
    margin-bottom: 30px;
}

body.search div#message p {
    padding: 10px 0;
}

body.search ul.item-list li div.item-title {
    font-size: 20px;
    margin-bottom:5px;
    font-weight: bold;
}

h2.content-title {
    border-bottom: 1px solid #<?php echo $container_bg_color;?>;
}
div.search-result {
    background: none repeat scroll 0 0 #<?php echo $container_alt_bg_color;?>;
    margin-bottom: 22px;
    padding: 20px;
}

textarea {resize: vertical}



/* =THEME OPTIONS
-------------------------------------------------------------- */



<?php if($cap->v_line_color != ''): ?>
/** ***
colour of the vertical lines  **/
.v_line {
    border-color: #<?php echo $cap->v_line_color;?>;
}
<?php endif;?>

<?php if($cap->bg_body_color || $cap->bg_body_img):?>
/** ***
body background colour, image and repeat  **/

body {
    <?php if($cap->bg_body_color){?>
        background-color: <?php if($cap->bg_body_color != 'transparent') {?>#<?php } ?><?php echo $cap->bg_body_color; ?>;
    <?php } ?>
    <?php if($cap->bg_body_img){?>
        background-image:url(<?php echo $cap->bg_body_img?>);
    <?php } ?>
    <?php
    switch ($cap->bg_body_img_repeat){
        case __('no repeat','cc'):
            echo 'background-repeat: no-repeat;';
            break;
        case 'x':
            echo 'background-repeat: repeat-x;';
            break;
        case 'y':
            echo 'background-repeat: repeat-y;';
            break;
        case 'x+y':
            echo 'background-repeat: repeat;';
            break;
        }
?>
}
<?php endif;?>


<?php if($cap->bg_body_color != "" && $cap->bg_body_color != __("transparent",'cc')){?>
    /** ***
    Adapting to body background colour  **/

    div.item-list-tabs ul li.selected a, div.item-list-tabs ul li.current a,
    div.pagination, div#subnav.item-list-tabs,
    div#leftsidebar h3.widgettitle, div#sidebar h3.widgettitle, div.widgetarea h3.widgettitle,
    div#leftsidebar h3.widgettitle a, div#sidebar h3.widgettitle a, div.widgetarea h3.widgettitle a,
    div#footer .cc-widget h3.widgettitle, #header .cc-widget h3.widgettitle, div#footer .cc-widget h3.widgettitle a, #header .cc-widget h3.widgettitle a   {
        background-color: #<?php echo $cap->bg_body_color?>;
    }

    .boxgrid {
        border-color: #<?php echo $cap->bg_body_color?>;
    }
<?php } ?>

<?php if($cap->bg_container_nolines == __('hide','cc') ) {?>
    /** ***
    hide the vertical lines in the container  **/
    .v_line {display: none}
<?php }?>

<?php if($cap->bg_container_color != '' || $cap->bg_container_img != '' || $cap->container_corner_radius != ''): ?>
/** ***
container background colour, image, repeat, corner radius and line correction  **/

div#container, body.activity-permalink div#container {
    <?php if($cap->bg_container_color ){?>
        background-color: <?php if($cap->bg_container_color != 'transparent' && $cap->bg_container_color != __('transparent','cc')) {?>#<?php } ?><?php echo $cap->bg_container_color;?>;
    <?php } ?>

    <?php if($cap->bg_container_img){?>
        background-image:url(<?php echo $cap->bg_container_img?>);
        <?php
                switch ($cap->bg_container_img_repeat)
                {
                case __('no repeat','cc'):
                    ?>background-repeat: no-repeat;<?php
                    break;
                case 'x':
                    ?>background-repeat: repeat-x;<?php
                    break;
                case 'y':
                    ?>background-repeat: repeat-y;<?php
                    break;
                case 'x+y':
                    ?>background-repeat: repeat;<?php
                    break;
                } ?>
    <?php   } ?>

    <?php if($cap->container_corner_radius ==__('not rounded','cc') ) {?>
        -moz-border-radius: 0px;
        -webkit-border-radius: 0px;
        border-radius: 0px;
        }
        div#leftsidebar, div#sidebar {
        -moz-border-radius: 0px;
        -webkit-border-radius: 0px;
        border-radius: 0px;
    <?php } ?>

}
<?php endif;?>

<?php if($cap->bg_container_color != '' || $cap->bg_container_img != '' || $cap->container_corner_radius != ''): ?>
/** ***
adapting footer widgets to container background colour, image, repeat and corner radius - if it is NOT specified extra for the footer! **/

    <?php if($cap->bg_container_color && !$cap->bg_footer_color){?>
        div#footer .cc-widget, div#header .cc-widget , #footer .cc-widget-right, #header .cc-widget-right {
            background-color: <?php if($cap->bg_container_color != __('transparent','cc') && $cap->bg_container_color != 'transparent') {?>#<?php echo $cap->bg_container_color; } else {?>transparent<?php }?>;
        }
    <?php } ?>

    <?php if($cap->bg_container_img && !$cap->bg_footer_img){?>
        div#footer .cc-widget, div#header .cc-widget , #footer .cc-widget-right, #header .cc-widget-right {
            background-image:url(<?php echo $cap->bg_container_img?>);
                <?php switch ($cap->bg_container_img_repeat) {
                    case __('no repeat','cc'):
                        echo 'background-repeat: no-repeat;';
                        break;
                    case 'x':
                        echo 'background-repeat: repeat-x;';
                        break;
                    case 'y':
                        echo 'background-repeat: repeat-y;';
                        break;
                    case 'x+y':
                        echo 'background-repeat: repeat;';
                        break;
                } ?>
        }
    <?php } ?>

    <?php if($cap->container_corner_radius == __('not rounded','cc') ) {?>
        #footer, div#footer .cc-widget, div#header .cc-widget , #footer .cc-widget-right, #header .cc-widget-right {
            -moz-border-radius: 0px;
            -webkit-border-radius: 0px;
            border-radius: 0px;
        }
        div#cc_slider-top{
        -moz-border-radius:0px;
        -webkit-border-radius:0px;
        border-radius:0px;
        }
    <?php } ?>

<?php endif;?>

<?php if($cap->bg_footer_color != '' || $cap->bg_footer_img != '' || $cap->footer_height != ''): ?>
/** ***
footer WIDGETS and header WIDGETS - height, bg_color, image and repeat  **/

#footer .cc-widget, #header .cc-widget{
    <?php if($cap->bg_footer_color) {?>
        background-color: <?php if($cap->bg_footer_color != __('transparent','cc') && $cap->bg_footer_color != 'transparent') {?>#<?php echo $cap->bg_footer_color; } else { echo 'transparent';}?> !important;
    <?php } ?>
    <?php if($cap->bg_footer_img) {?>
        background-image:url(<?php echo $cap->bg_footer_img;?>);
        <?php
        switch ($cap->bg_footer_img_repeat){
        case __('no repeat','cc'):
            echo 'background-repeat: no-repeat;';
            break;
        case 'x':
            echo 'background-repeat: repeat-x;';
            break;
        case 'y':
            echo 'background-repeat: repeat-y;';
            break;
        case 'x+y':
            echo 'background-repeat: repeat;';
            break;
        }
        ?>
    <?php } ?>
    <?php if($cap->footer_height) {?>
        height:<?php echo $cap->footer_height;?>px;
    <?php } ?>
    }
<?php endif;?>

<?php if($cap->bg_footerall_color != '' || $cap->bg_footerall_img != '' || $cap->footerall_height != ''): ?>
/** ***
footer - height, color, image and repeat  **/

#footer {
    <?php if($cap->bg_footerall_color) {?>
        background-color: <?php if($cap->bg_footerall_color != __('transparent','cc') && $cap->bg_footerall_color != 'transparent') {?>#<?php echo $cap->bg_footerall_color; } else { echo 'transparent';}?>;
    <?php } ?>
    <?php if($cap->bg_footerall_img) {?>
        background-image:url(<?php echo $cap->bg_footerall_img;?>);
        <?php
        switch ($cap->bg_footerall_img_repeat)
        {
        case __('no repeat','cc'):
            ?>background-repeat: no-repeat;<?php
            break;
        case 'x':
            ?>background-repeat: repeat-x;<?php
            break;
        case 'y':
            ?>background-repeat: repeat-y;<?php
            break;
        case 'x+y':
            ?>background-repeat: repeat;<?php
            break;
        }
        ?>
    <?php } ?>
    <?php if($cap->footerall_height) {?>
        height:<?php echo $cap->footerall_height;?>px;
    <?php } ?>
    }
<?php endif;?>

<?php if($cap->bg_footer_color != '' || $cap->bg_container_color): ?>
/** ***
Adapting buttons font color in the footer widgets. Either to footer background color or to container background colour  **/

#footer .cc-widget a.button, #header .cc-widget a.button {
<?php if($cap->bg_footer_color != '' &&  $cap->bg_footer_color != 'transparent' && $cap->bg_footer_color != __('transparent','cc')) {?>
        color: #<?php echo $cap->bg_footer_color;?> !important;
    <?php } elseif ($cap->bg_container_color && $cap->bg_container_color != 'transparent' && $cap->bg_container_color != __('transparent','cc')) {?>
        color: #<?php echo $cap->bg_container_color;?> !important;
    <?php } ?>
}
<?php endif;?>

<?php if($cap->bg_container_color && $cap->bg_container_color != 'transparent'  && $cap->bg_container_color != __('transparent','cc') ){?>
/** ***
slideshow and other stuff that wants some BACKGROUND tweaking to container background colour  **/

#slider-top,
div#subnav.item-list-tabs ul li.selected a, div#subnav.item-list-tabs ul li.current a {
    background-color: #<?php echo $cap->bg_container_color;?>;
}

/** ***
buttons and widgets that want some FONT COLOR tweaking to the container background colour  **/

a.comment-edit-link, a.comment-reply-link, a.button, input[type="submit"], input[type="button"], ul.button-nav li a, div.generic-button a,
.activity-list div.activity-meta a.acomment-reply,
.activity-list div.activity-meta a  {
    color: #<?php echo $cap->bg_container_color?> !important;
}
<?php };?>

<?php if($cap->font_style){?>
/** ***
font family  **/

a, div.post p.date a, div.post p.postmetadata a, div.comment-meta a, div.comment-options a, span.highlight, #item-nav a, div.widget ul li a:hover,
body {
    font-family: <?php echo $cap->font_style?>;
}
<?php };?>

<?php if($cap->font_size){?>
/** ***
standard font size  **/

body, p, em, a,
div.post,
div.post p.date,
div.post p.postmetadata,
div.comment-meta,
div.comment-options,
div.post p.date a,
div.post p.postmetadata a,
div.comment-meta a,
div.comment-options a,
span.highlight,
#item-nav a,
div#leftsidebar h3.widgettitle,
div#sidebar h3.widgettitle,
div.widgetarea h3.widgettitle,
div.widget ul li a:hover,
#subnav a:hover,
div.widget ul#blog-post-list li a,
div.widget ul#blog-post-list li,
div.widget ul#blog-post-list li p,
div.widget ul#blog-post-list li div,
div.widget ul li.recentcomments a,
div#sidebar div#sidebar-me h4,
div.widgetarea div#sidebar-me h4,
div#item-header div#item-meta,
ul.item-list li div.item-title span,
ul.item-list li div.item-desc,
ul.item-list li div.meta,
div.item-list-tabs ul li span,
span.activity,
div#message p,
div.widget span.activity,
div.pagination,
div#message.updated p,
#subnav a,
div.widget-title ul.item-list li a,
div#item-header span.activity,
div#item-header span.highlight,
form.standard-form input:focus,
form.standard-form textarea:focus,
form.standard-form select:focus,
table tr td.label,
table tr td.thread-info p.thread-excerpt,
table.forum td p.topic-text,
table.forum td.td-freshness,
form#whats-new-form,
form#whats-new-form h5,
form#whats-new-form #whats-new-textarea,
.activity-list li .activity-inreplyto,
.activity-list .activity-content .activity-header,
.activity-list .activity-content .comment-header,
.activity-list .activity-content span.time-since,
.activity-list .activity-content span.activity-header-meta a,
.activity-list .activity-content .activity-inner,
.activity-list .activity-content blockquote,
.activity-list .activity-content .comment-header,
.activity-header a:hover,
div.activity-comments div.acomment-meta,
div.activity-comments form .ac-textarea,
div.activity-comments form textarea,
div.activity-comments form div.ac-reply-content,
li span.unread-count,
tr.unread span.unread-count,
div.item-list-tabs ul li a span.unread-count,
ul#topic-post-list li div.poster-meta,
div.admin-links,
div.poster-name a,
div.object-name a,
div.post p.date a:hover,
div.post p.postmetadata a:hover,
div.comment-meta a:hover,
div.comment-options a:hover,
#footer,
#footer a,
div.widget ul li a,
.widget li.cat-item a,
#item-nav a:hover {
    font-size: <?php echo $cap->font_size?>px;
}
<?php };?>


<?php if($cap->font_color != ""):?>
    /** ***
    font colour  **/

    body, p, em, div.post, div.post p.date, div.post p.postmetadata, div.comment-meta, div.comment-options,
    div#item-header div#item-meta, ul.item-list li div.item-title span, ul.item-list li div.item-desc,
    ul.item-list li div.meta, div.item-list-tabs ul li span, span.activity, div#message p, div.widget span.activity,
    div.pagination, div#message.updated p, #subnav a,
    h1 a:hover, h2 a:hover, h3 a:hover, h4 a:hover, h5 a:hover, h6 a:hover, h1 a:focus, h2 a:focus, h3 a:focus, h4 a:focus, h5 a:focus, h6 a:focus,
    div#item-header span.activity, div#item-header h2 span.highlight, div.widget-title ul.item-list li.selected a,
    form.standard-form input:focus, form.standard-form select:focus, table tr td.label,
    table tr td.thread-info p.thread-excerpt, table.forum td p.topic-text, table.forum td.td-freshness, form#whats-new-form,
    form#whats-new-form h5, form#whats-new-form #whats-new-textarea, .activity-list li .activity-inreplyto,
    .activity-list .activity-content .activity-header, .activity-list .activity-content .comment-header,
    .activity-list .activity-content span.time-since,
    .activity-list .activity-content .activity-inner, .activity-list .activity-content blockquote,
    .activity-list .activity-content .comment-header, div.activity-comments div.acomment-meta,
    div.activity-comments form div.ac-reply-content, li span.unread-count, tr.unread span.unread-count, div.item-list-tabs ul li a span.unread-count, ul#topic-post-list li div.poster-meta,
    div.admin-links, #comments h3, #trackbacks h3, #respond h3, #footer, div#item-header span.activity, div#item-header h2 span.highlight, #item-nav a:hover {
        color:#<?php echo $cap->font_color?>;
    }
    div#item-header h2 span.highlight, div.item-list-tabs ul li.selected a, div.item-list-tabs ul li.current a {
        color:#<?php echo $cap->font_color?> !important;
    }

/** ***
buttons and widgets that want some adapting to the font colour  **/

a.comment-edit-link, a.comment-reply-link, a.button, input[type="submit"], input[type="button"], ul.button-nav li a, div.generic-button a,
.activity-list div.activity-meta a  {
    background:#<?php echo $cap->font_color?>;
}

div#leftsidebar h3.widgettitle, div#sidebar h3.widgettitle, div.widgetarea h3.widgettitle {
    color:#<?php echo $cap->font_color?>;
}
<?php endif;?>

<?php if($cap->title_font_style != "" || $cap->title_size != "" || $cap->title_color != "" || $cap->title_weight != ""):?>
/** ***
title font style, size, weight and colour  **/

h1, h2, h1 a, h2 a, h1 a:hover, h1 a:focus, h2 a:hover, h2 a:focus {
<?php if($cap->title_font_style){?>
    font-family: <?php echo $cap->title_font_style?>;
<?php };?>
<?php if($cap->title_size){?>
    font-size: <?php echo $cap->title_size?>px;
<?php };?>
<?php if($cap->title_weight == __('bold','cc')){?>
    font-weight:bold;
<?php } elseif( $cap->title_weight == __('normal','cc')){?>
    font-weight:normal;
<?php } ;?>
}

h1, h2, h1 a, h2 a {
<?php if($cap->title_color){?>
    color:#<?php echo $cap->title_color?>;
<?php };?>
}

<?php endif;?>

<?php if($cap->subtitle_font_style != "" || $cap->subtitle_color != "" || $cap->subtitle_weight != ""):?>
/** ***
subtitle font style, weight and colour  **/

h3, h4, h5, h6, h3 a, h4 a, h5 a, h6 a {
<?php if($cap->subtitle_font_style){?>
    font-family: <?php echo $cap->subtitle_font_style?>;
<?php };?>
<?php if($cap->subtitle_color){?>
    color:#<?php echo $cap->subtitle_color?>;
<?php };?>
<?php if($cap->subtitle_weight == __('bold','cc') || $cap->subtitle_weight == 'bold'){?>
    font-weight:bold;
<?php } else {?>
    font-weight:normal;
<?php };?>
}
<?php endif;?>

<?php if($cap->link_color){?>
    /** ***
    link colour  **/

    a,
    span.highlight, #item-nav a,
    div.widget ul#blog-post-list li a,
    div.widget ul li.recentcomments a,
    .widget li.current-cat a,
    div.widget ul li.current_page_item a,
    #footer .widget li.current-cat a,#header .widget li.current-cat a ,
    #footer div.widget ul li.current_page_item a,
    #header div.widget ul li.current_page_item a,
    #subnav a:hover  {
        color:#<?php echo $cap->link_color?>;
    }

    /** ***
    buttons and widgets that want some adapting to the link colour  **/

    a.comment-edit-link:hover,
    a.comment-edit-link:focus,
    a.comment-reply-link:hover,
    a.comment-reply-link:focus,
    a.button:focus,
    a.button:hover,
    input[type="submit"]:hover,
    input[type="button"]:hover,
    ul.button-nav li a:hover,
    div.generic-button a:hover,
    ul.button-nav li a:focus,
    div.generic-button a:focus,
    .activity-list div.activity-meta a.acomment-reply,
    div.activity-meta a.fav:hover,
    a.unfav:hover,
    div#item-header h2 span.highlight span {
        background-color:#<?php echo $cap->link_color?>;
        background-color:#<?php echo $cap->link_color?> !important;
    }
<?php } ?>

<?php if($cap->link_color_hover != ""):?>
    /** ***
    link colour hover  **/

    a:hover,
    a:focus,
    div#sidebar div.item-options a.selected:hover,
    div#leftsidebar div.item-options a.selected:hover,
    form.standard-form input:focus,
    form.standard-form select:focus,
    .activity-header a:hover,
    div.post p.date a:hover,
    div.post p.postmetadata a:hover,
    div.comment-meta a:hover,
    div.comment-options a:hover,
    div.widget ul li a:hover,
    div.widget ul li.recentcomments a:hover,
    div.widget-title ul.item-list li a:hover {
        color:#<?php echo $cap->link_color_hover ?>;
    }

    <?php if ( $cap->link_color_subnav_adapt == __("link colour and hover colour",'cc') ) {?>
        #subnav a:hover, #subnav a:focus, div.item-list-tabs ul li a:hover, div.item-list-tabs ul li a:focus {
            color:#<?php echo $cap->link_color_hover ?>;
        }
    <?php } ?>

<?php endif;?>

<?php if($cap->link_underline != __("never",'cc') && $cap->link_underline != "never" && $cap->link_underline != "" ): ?>

    <?php if($cap->link_underline == __("just for mouse over",'cc') || $cap->link_underline == "just for mouse over"){
        $stylethis = 'a:hover, a:focus';
    } else {
        if($cap->link_underline == __("always",'cc') || $cap->link_underline == "always") {
          $stylethis = 'a, a:hover, a:focus';
        } else {
          $stylethis = 'a:hover, a:focus {text-decoration: none} a';
        }
    } ?>

    /** ***
    link underline  **/

    <?php echo $stylethis ?> {
        text-decoration: underline;
    }

<?php endif;?>

<?php if($cap->link_bg_color != ""):?>
    /** ***
    link BACKGROUND colour  **/

    a {
        background-color: <?php if ( $cap->link_bg_color != __('transparent','cc') && $cap->link_bg_color != 'transparent' ) {echo '#', $cap->link_bg_color; } else { echo 'transparent'; }?>;
    }
<?php endif;?>

<?php if($cap->link_bg_color_hover != ""):?>
    /** ***
    link BACKGROUND colour hover  **/

    a:hover, a:focus {
        background-color: <?php if ( $cap->link_bg_color_hover != __('transparent','cc') && $cap->link_bg_color_hover != 'transparent' ) {echo '#', $cap->link_bg_color_hover; } else { echo 'transparent'; }?>;
    }
<?php endif;?>

<?php if($cap->link_styling_title_adapt != "just the hover effect" && $cap->link_styling_title_adapt != __("just the hover effect",'cc')):?>
/** ***
    link styling titles adapt**/

    <?php if ($cap->link_hover_color != '') {
    // use the link hover colour anyway - if one is selected ?>
                h1 a:hover, h2 a:hover, h3 a:hover, h4 a:hover, h5 a:hover, h6 a:hover,
                h1 a:focus, h2 a:focus, h3 a:focus, h4 a:focus, h5 a:focus, h6 a:focus {
                    color: #<?php echo $cap->link_hover_color;?>;
                }
    <?php } ?>


    <?php switch ($cap->link_styling_title_adapt) {
        case __('link colour and hover colour','cc'):
        case 'link colour and hover colour': ?>

            h1 a, h2 a, h3 a, h4 a, h5 a, h6 a {
                color: #<?php echo $cap->link_color;?>;
            }

        <?php break;
        case 'no, only the link colour!':
        case __('no, only the link colour!','cc'): ?>

            <?php if ($cap->link_bg_color_hover || $cap->link_bg_color_hover) {?>
                h1 a:hover, h2 a:hover, h3 a:hover, h4 a:hover, h5 a:hover, h6 a:hover,
                h1 a:focus, h2 a:focus, h3 a:focus, h4 a:focus, h5 a:focus, h6 a:focus {
                    color: #<?php if (!$cap->font_color) {echo $font_color;} else {echo $cap->font_color; } ?>;
                }
            <?php } ?>

        <?php break;
        case 'link colour and hover colour':
        case __('link colour and hover colour','cc'):?>

            <?php if($cap->link_underline != "never" && $cap->link_underline != __("never",'cc')): ?>

                <?php if($cap->link_underline == "just for mouse over" || $cap->link_underline == __("just for mouse over",'cc')){
                    $stylethis = 'h1 a:hover, h2 a:hover, h3 a:hover, h4 a:hover, h5 a:hover, h6 a:hover,
                    h1 a:focus, h2 a:focus, h3 a:focus, h4 a:focus, h5 a:focus, h6 a:focus';
                } else {
                    if($cap->link_underline == "always" || $cap->link_underline == __("always",'cc')) {
                        $stylethis =    'h1 a, h2 a, h3 a, h4 a, h5 a, h6 a, h1 a:hover, h2 a:hover, h3 a:hover, h4 a:hover, h5 a:hover, h6 a:hover,
                                        h1 a:focus, h2 a:focus, h3 a:focus, h4 a:focus, h5 a:focus, h6 a:focus';
                    } else {
                        $stylethis =    'h1 a:hover, h2 a:hover, h3 a:hover, h4 a:hover, h5 a:hover, h6 a:hover,
                                        h1 a:focus, h2 a:focus, h3 a:focus, h4 a:focus, h5 a:focus, h6 a:focus {
                                        text-decoration: none;
                                        }
                                        h1 a, h2 a, h3 a, h4 a, h5 a, h6 a';
                    }
                } ?>

                /** ***
                title links underline  **/

                <?php echo $stylethis ?> {
                    text-decoration: underline;
                }

            <?php endif;?>

        <?php break;
        case 'adapt all link styles':
        case __('adapt all link styles','cc'):?>

            <?php if($cap->link_underline != "never" && $cap->link_underline != __("never",'cc')): ?>

                <?php if($cap->link_underline == "just for mouse over" || $cap->link_underline == __("just for mouse over",'cc')){
                    $stylethis = 'h1 a:hover, h2 a:hover, h3 a:hover, h4 a:hover, h5 a:hover, h6 a:hover,
                h1 a:focus, h2 a:focus, h3 a:focus, h4 a:focus, h5 a:focus, h6 a:focus';
                } else {
                    if($cap->link_underline == "always" && $cap->link_underline == __("always",'cc')) {
                        $stylethis =    'h1 a, h2 a, h3 a, h4 a, h5 a, h6 a, h1 a:hover, h2 a:hover, h3 a:hover, h4 a:hover, h5 a:hover, h6 a:hover,
                                        h1 a:focus, h2 a:focus, h3 a:focus, h4 a:focus, h5 a:focus, h6 a:focus';
                    } else {
                        $stylethis =    'h1 a:hover, h2 a:hover, h3 a:hover, h4 a:hover, h5 a:hover, h6 a:hover,
                                        h1 a:focus, h2 a:focus, h3 a:focus, h4 a:focus, h5 a:focus, h6 a:focus {
                                        text-decoration: none;
                                        }
                                        h1 a, h2 a, h3 a, h4 a, h5 a, h6 a';
                    }
                } ?>

                /** ***
                title links underline  **/

                <?php echo $stylethis ?> {
                    text-decoration: underline;
                }

            <?php endif;?>

            <?php if($cap->link_bg_color != ""):?>
                /** ***
                title links BACKGROUND colour  **/

                h1 a, h2 a, h3 a, h4 a, h5 a, h6 a {
                    background-color: <?php if ( $cap->link_bg_color != 'transparent' && $cap->link_bg_color != __('transparent','cc') ) {echo '#', $cap->link_bg_color; } else { echo 'transparent';}?>;
                }
            <?php endif;?>

            <?php if($cap->link_bg_color_hover != ""):?>
                /** ***
                title links BACKGROUND colour hover  **/

                h1 a:hover, h2 a:hover, h3 a:hover, h4 a:hover, h5 a:hover, h6 a:hover,
                h1 a:focus, h2 a:focus, h3 a:focus, h4 a:focus, h5 a:focus, h6 a:focus {
                    background-color: <?php if ( $cap->link_bg_color_hover != 'transparent' && $cap->link_bg_color_hover != __('transparent','cc') ) {echo '#', $cap->link_bg_color_hover; } else { echo 'transparent';}?>;
                }
            <?php endif;?>


        <?php break;
        case 'the background colours too':
        case __('the background colours too','cc'): ?>

            <?php if($cap->link_bg_color != ""):?>
                /** ***
                title links BACKGROUND colour  **/

                h1 a, h2 a, h3 a, h4 a, h5 a, h6 a {
                    background-color: <?php if ( $cap->link_bg_color != 'transparent' && $cap->link_bg_color != __('transparent','cc')) {echo '#', $cap->link_bg_color; } else { echo 'transparent';}?>;
                }
            <?php endif;?>

            <?php if($cap->link_bg_color_hover != ""):?>
                /** ***
                title links BACKGROUND colour hover  **/

                h1 a:hover, h2 a:hover, h3 a:hover, h4 a:hover, h5 a:hover, h6 a:hover,
                h1 a:focus, h2 a:focus, h3 a:focus, h4 a:focus, h5 a:focus, h6 a:focus {
                    background-color: <?php if ( $cap->link_bg_color_hover != 'transparent' && $cap->link_bg_color_hover != __('transparent','cc') ) {echo '#', $cap->link_bg_color_hover; } else { echo 'transparent';} ?>;
                }
            <?php endif;?>

        <?php break;
        ?>

      <?php } ?>




<?php endif;?>

<?php if($cap->default_homepage_hide_avatar == "hide" || $cap->default_homepage_hide_avatar == __("hide",'cc') ){?>
/** ***standard wordpress home page: hide avatar**/

body.home div.post div.post-content, div.comment-content,
body.home.bubble div.post div.post-content, body.bubble div.comment-content {
    margin-left: 0;
    width: 95%;
}

body.home div.post div.author-box,
body.home.bubble div.post div.author-box {
    display: none;
}
<?php } ?>

<?php if($cap->posts_lists_hide_avatar == "hide" || $cap->posts_lists_hide_avatar == __("hide",'cc') ){?>
/** ***
standard wordpress archive pages: hide avatar**/

body.archive div.post div.post-content, div.comment-content,
body.archive.bubble div.post div.post-content, body.bubble div.comment-content {
    margin-left: 0;
    width: 95%;
}

body.archive div.post div.author-box,
body.archive.bubble div.post div.author-box {
    display: none;
}
<?php } ?>

<?php if($cap->default_homepage_style == "bubbles" || $cap->default_homepage_style == __("bubbles",'cc') || $cap->posts_lists_style == "bubbles" || $cap->posts_lists_style == __("bubbles",'cc') ){?>
/** ***
standard wordpress home page: bubble style**/

body.bubble div.post h2.posttitle, #blog-search div.post h2.posttitle {
    line-height: 120%;
    margin: 0 0 12px;
}

<?php if($cap->default_homepage_hide_avatar == "hide" ||
         $cap->default_homepage_hide_avatar == __("hide",'cc') ||
         $cap->posts_lists_hide_avatar == "hide" ||
         $cap->posts_lists_hide_avatar == __("hide",'cc') ) {?>
    div.post span.marker {display: none}
<?php } else {?>
    div.post span.marker {
        -moz-transform: rotate(45deg);
        -webkit-transform: rotate(45deg);
        -o-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        background: none repeat scroll 0 0 #<?php echo $container_alt_bg_color;?>;
        height: 20px;
        margin: 17px 0 0 -25px;
        position: absolute;
        width: 20px;
    }
<?php } ?>

body.bubble div.post div.post-content, #blog-search div.post-content{
    border-radius: 11px;
    -moz-border-radius: 11px;
    -webkit-border-radius: 11px;
    background: none repeat scroll 0 0 #<?php echo $container_alt_bg_color;?>;
    margin-left: 20px;
    margin-right: -5px;
    padding: 15px 10px 5px 15px;
    margin-bottom:10px;
    float: left;
}
body.bubble div.post p.date, #blog-search div.post p.date{
    border-top: 1px solid #<?php echo $container_bg_color;?>;
    border-bottom: 1px solid #<?php echo $container_bg_color;?>;
}
body.bubble div.post p.postmetadata, #blog-search div.post  p.postmetadata{
    border-top: 1px solid #<?php echo $container_bg_color;?>;
}
body.bubble div.post div.author-box, #blog-search div.post div.author-box{
    margin-top: 20px;
    display: block;
}
<?php } ?>

<?php if($cap->default_homepage_hide_date == "hide" || $cap->default_homepage_hide_date == __("hide",'cc') ){?>
/** ***
standard wordpress home page: hide date, category and author**/

body.home div.post p.date {
    display: none;
}
<?php } ?>

<?php if($cap->posts_lists_hide_date == "hide" || $cap->posts_lists_hide_date == __("hide",'cc') ){?>
/** ***
standard wordpress archive pages: hide date, category and author**/

body.archive div.post p.date {
    display: none;
}
<?php } ?>

<?php if($cap->header_height){?>
/** ***
header height / navigation position **/
#header{
    height: <?php echo $cap->header_height;?>px;
}
#access {
    <!--margin-top:<?php echo $cap->header_height;?>px;-->
}
<?php } ?>

<?php if($cap->header_img != ''){?>
/** ***
header image, repeat  **/

#header {
    background-image:url(<?php echo $cap->header_img?>);
        <?php
        switch ($cap->header_img_repeat)
        {
        case 'no repeat':
        case __('no repeat','cc'):
            ?>background-repeat: no-repeat;<?php
            break;
        case 'x':
            ?>background-repeat: repeat-x;<?php
            break;
        case 'y':
            ?>background-repeat: repeat-y;<?php
            break;
        case 'x+y':
            ?>background-repeat: repeat;<?php
            break;
        default:
            ?>background-repeat: no-repeat;<?php
            break;
        }
        ?>
    <?php if($cap->header_img_x == 'center' || $cap->header_img_x == __('center','cc') ){?>
        background-position: center <?php if($cap->header_img_y){echo $cap->header_img_y;} else {echo '0';}?>px;
    <?php } elseif($cap->header_img_x == 'right' || $cap->header_img_x == __('right','cc') ){?>
        background-position: right <?php if($cap->header_img_y){echo $cap->header_img_y;} else {echo '0';}?>px;
    <?php }?>
    <?php if((!$cap->header_img_x || $cap->header_img_x == 'left' || $cap->header_img_x == __('left','cc')) && $cap->header_img_y){?>
        background-position: left <?php echo $cap->header_img_y ?>px;
    <?php } ?>
}
<?php } elseif ( get_header_image() != '' && $cap->add_custom_image_header == true ) {?>
    #header {
    background-image:url(<?php echo header_image();?>);
        <?php
        switch ($cap->header_img_repeat)
        {
        case 'no repeat':
        case __('no repeat','cc'):
            ?>background-repeat: no-repeat;<?php
            break;
        case 'x':
            ?>background-repeat: repeat-x;<?php
            break;
        case 'y':
            ?>background-repeat: repeat-y;<?php
            break;
        case 'x+y':
            ?>background-repeat: repeat;<?php
            break;
        default:
            ?>background-repeat: no-repeat;<?php
            break;
        }
        ?>
    <?php if($cap->header_img_x == 'center' || $cap->header_img_x == __('center','cc') ){?>
        background-position: center <?php if($cap->header_img_y){echo $cap->header_img_y;} else {echo '0';}?>px;
    <?php } elseif($cap->header_img_x == 'right' || $cap->header_img_x == __('right','cc') ){?>
        background-position: right <?php if($cap->header_img_y){echo $cap->header_img_y;} else {echo '0';}?>px;
    <?php }?>
    <?php if((!$cap->header_img_x || $cap->header_img_x == 'left' || $cap->header_img_x == __('left','cc') ) && $cap->header_img_y){?>
        background-position: left <?php echo $cap->header_img_y ?>px;
    <?php } ?>
}


<?php if(!is_admin()){ ?>
    .appearance_page_custom-header #headimg {
        background: #<?php echo get_background_color(); ?>;
        border: none;
        text-align: center;
    }
    #headimg h1,
    #desc {
        font-family: "Helvetica Neue", Arial, Helvetica, "Nimbus Sans L", sans-serif;
    }
    #headimg h1 {
        margin: 0;
    }
    #headimg h1 a {
        font-size: 36px;
        letter-spacing: -0.03em;
        line-height: 42px;
        text-decoration: none;
    }
    #desc {
        font-size: 18px;
        line-height: 31px;
        padding: 0 0 9px 0;
    }
    <?php
        // If the user has set a custom color for the text use that
        if ( get_header_textcolor() != HEADER_TEXTCOLOR ) :
    ?>
        #site-title a,
        #site-description {
            color: #<?php echo get_header_textcolor(); ?>;
        }
    <?php endif; ?>
    #headimg img {
        max-width: 990px;
        width: 100%;
    }
<?php } else {
    if ( HEADER_TEXTCOLOR != get_header_textcolor() ){
        // If we get this far, we have custom styles. Let's do this.
        // Has the text been hidden?
        if ( $cap->header_text == "__('off','cc')" ) { ?>
            #blog-description, #header div#logo h1 a, #header div#logo h4 a {
                position: absolute;
                left: -9000px;
            }
        <?php // If the user has set a custom color for the text use that
        } else { ?>
            #blog-description, #header div#logo h1 a, #header div#logo h4 a {
                color: #<?php if ( 'blank' == get_header_textcolor() ) {
                					echo get_header_textcolor();
								} else {
									echo $font_color;
								} ?> !important;
            }
            <?php } ?>
    <?php }

}?>

<?php } ?>

<?php if ( $cap->header_text == 'off' || $cap->header_text == __('off','cc') ) {?>
    #header div#logo h1, #header #desc, #header div#logo h4, div#blog-description {
        display: none;
    }
<?php } ?>

<?php if ( $cap->header_text_color) {?>
    #header div#logo h1 a, #header div#logo h4 a, #desc, div#blog-description {
        color:#<?php echo $cap->header_text_color ?>;
    }
<?php } ?>

<?php if($cap->searchbar_x != "" || $cap->searchbar_y != ""): ?>
    /** ***
    header search bar position  **/

    <?php if($cap->searchbar_y){?>
        #header #search-bar {
            top:<?php echo $cap->searchbar_y;?>px !important;
        }
    <?php } ?>

    <?php if($cap->searchbar_x == 'left' || $cap->searchbar_x == __('left','cc') ){?>
        #header #search-bar {
            left:0;
        }
        #header #search-bar {
            text-align: left;
        }
    <?php } ?>
<?php endif;?>

<?php if($cap->bg_menu_style != "tab style" && $cap->bg_menu_style != __("tab style",'cc') ): ?>
/** ***
menu style  **/

<?php if($cap->bg_menu_style == 'closed style' || $cap->bg_menu_style == __('closed style','cc')){?>
    #access ul li.current_page_item > a, #access ul li.current-menu-ancestor > a,
    #access ul li.current-menu-item > a, #access li.selected > a, #access ul li.current-menu-parent > a,
    #access ul li.current_page_item > a:hover, #access ul li.current-menu-item > a:hover,
    #access ul li.current_page_item, #access ul li.current-menu-item, #access li.selected,
    #access li:hover > a {
        -moz-border-radius: 6px;-webkit-border-radius:6px;border-radius:6px;
    }
    #access ul li {
        margin-bottom: 4px;
    }
    #access ul ul li {
        margin-bottom: 0px;
    }
    #access ul ul a {
        margin-bottom: 0px;
    }
<?php } ?>
<?php if($cap->bg_menu_style == 'simple' || $cap->bg_menu_style == __('simple','cc') ){?>
    div#access {
        background-color: transparent;
    }
    #access .menu-header, div.menu {
    margin-left: 0;
    padding-left: 0;
    }
    #access a {
    padding: 0 12px 2px 12px;
    }
    div#access div.menu ul li a:hover, div#access div.menu ul li a:focus,
    #access ul ul :hover > a, #access ul.children li:hover > a, #access ul.sub-menu li:hover > a,
    #access ul li.current_page_item > a, #access ul li.current-menu-ancestor > a,
    #access ul li.current_page_item > a:hover, #access ul li.current-menu-item > a:hover,
    #access ul li.current-menu-item > a, #access li.selected > a, #access ul li.current-menu-parent > a {
        color: #<?php echo $link_color ?>;
    }
<?php } ?>

<?php if($cap->bg_menu_style == 'bordered' || $cap->bg_menu_style == __('bordered','cc') ){?>
    div#access {
        background-color: transparent;
        border-top: 1px solid #<?php echo $container_bg_color ?>;
        border-bottom: 1px solid #<?php echo $container_bg_color ?>;
    }
    div#access div.menu ul li a:hover, div#access div.menu ul li a:focus,
    #access ul ul :hover > a, #access ul.children li:hover > a, #access ul.sub-menu li:hover > a,
    #access ul li.current_page_item > a, #access ul li.current-menu-ancestor > a,
    #access ul li.current_page_item > a:hover, #access ul li.current-menu-item > a:hover,
    #access ul li.current-menu-item > a, #access li.selected > a, #access ul li.current-menu-parent > a {
        color: #<?php echo $link_color ?>;
    }
<?php } ?>


<?php endif;?>

<?php if($cap->menu_x == 'right' || $cap->menu_x == __('right','cc') ){?>
/** ***
menu x-position  **/

div.menu ul {
    float: right;
}
<?php } ?>

<?php if($cap->menue_link_color ) {?>
/** ***
menu font colour  **/

#access a, #access ul ul a, #access ul.children li.selected > a,
#access ul li:hover > a, #access ul ul :hover > a,
#access ul.children li:hover > a, #access ul.sub-menu li:hover > a,
#access ul li.current_page_item > a, #access ul li.current-menu-ancestor > a,
#access ul li.current-menu-item > a, #access li.selected > a, #access ul li.current-menu-parent > a  {
    color: #<?php echo $cap->menue_link_color?>;
}
<?php } ?>

<?php if($cap->menue_link_color_current ) {?>
/** ***
menu font colour current and mouse over **/

div#access div.menu ul li a:hover,
div#access div.menu ul li a:focus,
#access ul ul *:hover > a,
#access ul.children li:hover > a,
#access ul.sub-menu li:hover > a,
#access ul li.current_page_item > a,
#access ul li.current-menu-ancestor > a,
#access ul li.current_page_item > a:hover,
#access ul li.current-menu-item > a:hover,
#access ul li.current-menu-item > a,
#access ul li.current-menu-parent > a,
#access li.selected > a {
    color: #<?php echo $cap->menue_link_color_current?>;
}


/** ***
IE browser hack for menu font colour current and mouse over  **/

* html #access ul li.current_page_item a,
* html #access ul li.current-menu-ancestor a,
* html #access ul li.current-menu-item a,
* html #access ul li.current-menu-parent a,
* html #access ul li a:hover {
    color: #<?php echo $cap->menue_link_color_current?>;
}
<?php } ?>

<?php if($cap->bg_menue_link_color != "" || $cap->menu_underline != "" || $cap->bg_menu_img != ""):?>
/** ***
menu background colour, border-bottom, image and repeat  **/

#access {
<?php if($cap->bg_menue_link_color  ){?>
    background-color: <?php if ( $cap->bg_menue_link_color != 'transparent' && $cap->bg_menue_link_color != __('transparent','cc')  ) {echo '#', $cap->bg_menue_link_color;} else {echo 'transparent';}?>;
<?php } ?>
<?php if($cap->menu_underline ){?>
    border-bottom: 1px solid #<?php echo $cap->menu_underline?>;
<?php } ?>
<?php if($cap->bg_menu_img){?>
    background-image:url(<?php echo $cap->bg_menu_img?>);
<?php } ?>

<?php
        switch ($cap->bg_menu_img_repeat)
        {
        case 'no repeat':
        case __('no repeat','cc') :
            ?>background-repeat: no-repeat;<?php
            break;
        case 'x':
            ?>background-repeat: repeat-x;<?php
            break;
        case 'y':
            ?>background-repeat: repeat-y;<?php
            break;
        case 'x+y':
            ?>background-repeat: repeat;<?php
            break;
        } ?>
}
<?php endif;?>

<?php if($cap->menu_corner_radius != ""):?>
/** ***
menu corner radius  **/

#access {
    -moz-border-radius: 6px;
    -webkit-border-radius: 6px;
    -o-border-radius: 6px;
    -ms-border-radius: 6px;
    border-radius: 6px;
<?php if($cap->menu_corner_radius == 'just the bottom ones' || $cap->menu_corner_radius == __('just the bottom ones','cc') ){?>
    -moz-border-radius-topleft:0px;
    -moz-border-radius-topright:0px;
    -webkit-border-top-left-radius:0px;
    -webkit-border-top-right-radius:0px;
    border-top-left-radius:0px;
    border-top-right-radius:0px;
<?php } ?>
<?php if($cap->menu_corner_radius == 'not rounded' || $cap->menu_corner_radius == __('not rounded','cc') ){?>
    -moz-border-radius:0px;
    -webkit-border-radius:0px;
    border-radius:0px;
<?php } ?>
}
<?php if($cap->menu_corner_radius == 'not rounded' || $cap->menu_corner_radius == __('not rounded','cc') ){?>
    #access .menu-header li, div.menu li, #access a{
        -moz-border-radius: 0;
        -webkit-border-radius: 0;
        -o-border-radius: 0;
        -ms-border-radius: 0;
        border-radius: 0;
    }
<?php } ?>
<?php endif;?>


<?php if($cap->bg_menue_link_color_current  ){?>
/** ***
menu background colour, image and repeat of current  **/

#access ul li.current_page_item > a, #access ul li.current-menu-ancestor > a,
#access ul li.current-menu-item > a, #access li.selected > a, #access ul li.current-menu-parent > a,
#access ul li.current_page_item, #access ul li.current-menu-item, #access li.selected {
    background-color: <?php if ( $cap->bg_menue_link_color_current != 'transparent' &&  $cap->bg_menue_link_color_current != __('transparent','cc')  ) {echo '#', $cap->bg_menue_link_color_current;} else { echo 'transparent';}?>;
    <?php if($cap->bg_menu_img_current){?>
    background-image:url(<?php echo $cap->bg_menu_img_current?>);
    <?php } ?>
    <?php if($cap->bg_menu_img_current) {
        switch ($cap->bg_menu_img_current_repeat) {
        case 'no repeat':
        case __('no repeat','cc') :
            ?>background-repeat: no-repeat;<?php
        break;
        case 'x':
            ?>background-repeat: repeat-x;<?php
        break;
        case 'y':
            ?>background-repeat: repeat-y;<?php
        break;
        case 'x+y':
            ?>background-repeat: repeat;<?php
        break;
        }
    } ?>
}
<?php } ?>

<?php if($cap->bg_menue_link_color_hover){?>
/** ***
menu background colour hover and drop down list  **/

#access ul li.current_page_item a:hover,
#access ul li.current-menu-item a:hover,
#access li:hover > a, #access ul ul:hover > a,
#access ul ul li, #access ul ul a {
    background-color: <?php if ( $cap->bg_menue_link_color_hover != 'transparent' && $cap->bg_menue_link_color_hover != __('transparent','cc') ) {echo '#', $cap->bg_menue_link_color_hover;} else { echo 'transparent';}?> !important;
}
<?php } ?>

<?php if($cap->bg_menue_link_color_dd_hover ){?>
/** ***
menu background colour drop down menu item hover  **/

#access ul.children li:hover > a,
#access ul.sub-menu li:hover > a {
    background: #<?php echo $cap->bg_menue_link_color_dd_hover?> !important;
}
<?php } ?>

<?php if ( $cap->leftsidebar_width != "") {?>
    /** ***
    left sidebar width  **/

    div#leftsidebar {
        width: <?php echo $cap->leftsidebar_width ?>px;
        /*margin-right: -<?php echo $cap->leftsidebar_width ?>px;*/
    }

    div.v_line_left {
        margin-left: <?php echo $cap->leftsidebar_width ?>px;
    }

    <?php // change the width of the widget titles, which is always 41px less because of its padding..
    $old = $cap->leftsidebar_width;$wdth = $old - 41;?>

    div#leftsidebar h3.widgettitle {
        width: <?php echo $wdth ?>px;
    }

<?php } ?>

<?php if ( $cap->bg_leftsidebar_color != "" || $cap->bg_leftsidebar_img != "") { ?>
/** ***
left sidebar background colour  **/

div#leftsidebar {
    <?php if ( $cap->bg_leftsidebar_color != "" ) {?>background-color: #<?php echo $cap->bg_leftsidebar_color;} ?>;
    <?php if($cap->bg_leftsidebar_img != ""){?>
        background-image:url(<?php echo $cap->bg_leftsidebar_img ?>);
        <?php switch ($cap->bg_leftsidebar_img_repeat)
                {
                case 'no repeat':
                case __('no repeat','cc') :
                    ?>background-repeat: no-repeat;<?php
                    break;
                case 'x':
                    ?>background-repeat: repeat-x;<?php
                    break;
                case 'y':
                    ?>background-repeat: repeat-y;<?php
                    break;
                case 'x+y':
                    ?>background-repeat: repeat;<?php
                    break;
                } ?>
    <?php } ?>

}
<?php } ?>

<?php if ( $cap->rightsidebar_width != "") {?>
    /** ***
    right sidebar width  **/

    div#sidebar {
        width: <?php echo $cap->rightsidebar_width ?>px;
        margin-left: -<?php echo$cap->rightsidebar_width ?>px;
    }


    div.v_line_right {
        right: <?php echo $cap->rightsidebar_width ?>px;
    }
    <?php // change the width of the widget titles, which is always 41px less because of its padding..
    $old = $cap->rightsidebar_width;$wdth = $old - 41;?>

    div#sidebar h3.widgettitle, #leftsidebar .widgettitle{
        width: <?php echo $wdth ?>px;

    }

<?php } ?>
    #settings-form .settings-input, #profile-edit-form .field_name input{
        width: 200px;
    }
<?php if ( $cap->bg_rightsidebar_color != "" || $cap->bg_rightsidebar_img != "") {?>
/** ***
right sidebar background colour  **/

div#sidebar {
    <?php if ( $cap->bg_rightsidebar_color != "" ) {?>background-color: #<?php echo $cap->bg_rightsidebar_color;} ?>;
    <?php if($cap->bg_rightsidebar_img != ""){?>
        background-image:url(<?php echo $cap->bg_rightsidebar_img ?>);
        <?php switch ($cap->bg_rightsidebar_img_repeat)
                {
                case 'no repeat':
                case __('no repeat','cc') :
                    ?>background-repeat: no-repeat;<?php
                    break;
                case 'x':
                    ?>background-repeat: repeat-x;<?php
                    break;
                case 'y':
                    ?>background-repeat: repeat-y;<?php
                    break;
                case 'x+y':
                    ?>background-repeat: repeat;<?php
                    break;
                } ?>
    <?php } ?>

}
<?php } ?>

<?php if($cap->bg_widgettitle_style != "" || $cap->bg_widgettitle_color != "" || $cap->bg_widgettitle_img != "" ): ?>
/** ***
sidebars: widget title style, background colour and image  **/

div#leftsidebar h3.widgettitle, div#sidebar h3.widgettitle, div.widgetarea h3.widgettitle, div.span3 h3.widgettitle  {
<?php

        switch ($cap->bg_widgettitle_style) {
        case 'angled':
        case __('angled','cc') :
            ?>-moz-border-radius:0 0 0 0; -webkit-border-radius:0; border-radius:0; margin: 0 0 10px -10px; padding: 5px 31px 5px 10px;<?php
            break;
        case 'transparent':
        case __('transparent','cc') :
            ?>background: transparent;<?php
            break;
        }
?>
<?php if($cap->bg_widgettitle_color){?>
    background-color: #<?php echo $cap->bg_widgettitle_color?>;
<?php } ?>
<?php if($cap->bg_widgettitle_img){?>
    background-image:url(<?php echo $cap->bg_widgettitle_img?>);
<?php } ?>
<?php
        switch ($cap->bg_widgettitle_img_repeat)
        {
        case 'no repeat':
        case __('no repeat','cc') :
            ?>background-repeat: no-repeat;<?php
            break;
        case 'x':
            ?>background-repeat: repeat-x;<?php
            break;
        case 'y':
            ?>background-repeat: repeat-y;<?php
            break;
        case 'x+y':
            ?>background-repeat: repeat;<?php
            break;
        }
        ?>
}
/* just for the left sidebar */
div#leftsidebar h3.widgettitle, div#leftsidebar h3.widgettitle a {
<?php
        switch ($cap->bg_widgettitle_style) {
        case 'angled':
        case __('angled','cc') :
            ?>-moz-border-radius:0 0 0 0;-webkit-border-radius:0;border-radius:0;margin:0 0 12px -10px;padding:5px 22px 5px 19px;<?php
            break;
        case 'transparent':
        case __('transparent','cc') :
            ?>background: transparent;<?php
            break;
        }
        ?>
}
<?php endif;?>

<?php if($cap->widgettitle_font_size || $cap->widgettitle_font_color || $cap->widgettitle_font_style){?>
/** ***
sidebars: widget title font style, size and color **/

    div#leftsidebar h3.widgettitle, div#sidebar h3.widgettitle, div.widgetarea h3.widgettitle,
    div#leftsidebar h3.widgettitle a, div#sidebar h3.widgettitle a, div.widgetarea h3.widgettitle a {
    font-family: <?php echo $cap->widgettitle_font_style ?>;
    <?php if($cap->widgettitle_font_size != "") {?>font-size: <?php echo $cap->widgettitle_font_size;} ?>px;
    <?php if($cap->widgettitle_font_color != "") {?>color: #<?php echo $cap->widgettitle_font_color;} ?>;
}
<?php } ?>

<?php if($cap->capitalize_widgets_li == 'yes' || $cap->capitalize_widgets_li == __('yes','cc') ){?>
/** ***
widgets: capitalize fonts in lists**/

div.widget-title ul.item-list li a, div.widget ul li a {text-transform: uppercase}

<?php } ?>

<?php if($cap->capitalize_widgettitles == 'yes' || $cap->capitalize_widgettitles == __('yes','cc') ){?>
/** ***
widgets: capitalize widgettitles**/

h3.widgettitle, h3.widgettitle a {text-transform: uppercase}

<?php } ?>

<?php global $cc_post_options;?>
<?php if($cc_post_options['cc_post_template_avatar'] == '1') {?>
/** ***
Show/Hide Avatar  **/

div.post div.post-content {
    margin-left: 8px;
}
<?php } ?>

<?php if($cap->bg_content_nav_color){?>
/** ***
BuddyPress sub navigation background colour  **/

div.item-list-tabs ul li.selected a, div.item-list-tabs ul li.current a,
div.pagination, div#subnav.item-list-tabs {
    background-color: #<?php echo $cap->bg_content_nav_color?>;
}
div.item-list-tabs {
    border-bottom: 4px solid #<?php echo $cap->bg_content_nav_color?>;
}

<?php } ?>

#innerrim .slidershadow img{
    width: 100%;
}
.widget.gererator{
    margin-bottom: 0;
    padding: 12px;
    border: 1px solid #dddddd;
}
.widget.gererator .widget_content{
    font-size: 16px;
    line-height:170%;
}
.widget .footer-left-widget{
    margin-left: 34% !important;
}
#innerrim .default-homepage-last-posts{
    margin-top:-21px;
}
#innerrim .group-header-left{
    margin-left:30% !important;
}
#innerrim .facebook_like{
    border:none;
    overflow:hidden;
    width:auto;
    height:60px;
    max-width: 100%;
}
#innerrim #cc_slider_prev, #innerrim #featured_prev{
    background: #ededed;
}
#innerrim .center{
    margin-top:50px;
    margin-left: 20px;
}
.boxgrid .cover h3{
    padding-left:8px;
}
.activity-content iframe,
.activity-content object,
.activity-content embed {
    width: 100%;
}
#activity-filter-select{
    width: auto;
}
.row-fluid .span8, .span8 {
    width: 75%;
}

.row-fluid .span8 {
    width: <?php echo get_content_width($site_width) . $units;?>
} 

/*
.row-fluid.left-right-template .span8,.row-fluid.archive-width .span8{
    width: 53%;
}
.row-fluid.full-search-width .span8{
    width: 98%;
}
.row-fluid.left-right-sidebar .span8, 
.row-fluid .span8.left-right-template{
    width: 50%;
}
.row-fluid .span8.full-with {
    width: 100%;
}
.row-fluid.left-right-sidebar .span11{
    width: 90%;
}
.row-fluid.full-width .span8 {
    width: 100%;
}

*/
<?php if ( $cap->cc_responsive_enable ) { ?>
	.row-fluid.left-right-sidebar div.post div.post-content,
	.row-fluid.left-right-template div.post div.post-content, 
	.row-fluid.left-right-template #blog-search div.post-content {
	    width: 90%;
	}
<?php } ?>
[class^="rspace"], [class^="rspace"] img{
    width: 100% !important;
}
body #content #groups-displaymode-select.span4, body #content #groups-order-select.span5{
    width: 27%;
    float: right;
}
.container-fluid{
    width: 100%;
}
html {
    margin-top: 0px !important;
}
.field-visibility-settings .radio{
	list-style: none;
}
.field-visibility-settings .radio label{
	margin: 5px 0 5px 0;
}
.field-visibility-settings .radio input{
	float: left;
}
#send_reply_button.loading{
    background-image: url(<?php echo get_template_directory_uri() ?>/images/ajax-loader.gif ) !important;
    background-position: 5% 50% !important;
    background-repeat: no-repeat !important;
    padding-left: 20px !important;
}

div#content.span8.full-with { width: 100%; }

/** ***   
overwrite css area adding  **/
<?php
    do_action('cc_pro_add_styles');
} //end of get_css

/**
* This function generates dynamic styles
*/
function cc_dysplay_dynamic_css(){
	global $cap;
	ob_start();
	?>
	<style type="text/css" title="here they are">
div{
	
}
	<?php
	get_css();
	if($cap->overwrite_css){
		echo $cap->overwrite_css;
	}
	?>
	</style>
	<?php
	$dynamic_styles = ob_get_contents();
	ob_end_clean();
	echo compress($dynamic_styles);
}

/**
* This function ...
*/
function cc_style_switcher(){
	global $cap;

	if( $cap->static_css == 'no' || !defined('is_pro') && defined('CC_MAIN_CSS_FILE_PATH') && defined('CC_CUSTOM_CSS_FILE_PATH')){
		$names_arr = array(
			CC_MAIN_CSS_FILE_PATH,
			CC_CUSTOM_CSS_FILE_PATH
		);
		cc_remove_static_css_files($names_arr);
	}
	elseif( $cap->static_css == 'yes' && defined('is_pro') && function_exists('cc_create_static_css_files')){
		cc_create_static_css_files();
	}
}
add_action('cc_after_theme_settings_saved', 'cc_style_switcher');

/**
* This function ...
*/
function cc_print_styles(){
	if( defined('is_pro') && defined('CC_MAIN_CSS_FILE_PATH') && file_exists(CC_MAIN_CSS_FILE_PATH)){
		echo '<link type="text/css" rel="stylesheet" href="'.CC_MAIN_CSS_FILE_URL.'" />';
	    if(file_exists(CC_CUSTOM_CSS_FILE_PATH)){
			echo '<link type="text/css" rel="stylesheet" href="'.CC_CUSTOM_CSS_FILE_URL.'" />';
	    }
	}
	elseif( (defined('CC_MAIN_CSS_FILE_PATH') && !file_exists(CC_MAIN_CSS_FILE_PATH)) || !defined('is_pro') ){
		cc_dysplay_dynamic_css();
	}
}
add_action('wp_head', 'cc_print_styles', 100);

/**
 * Get content width
 */
function get_content_width($site_width){
    global $cap, $post;
    
	if($cap->cc_responsive_enable){
		$cap->rightsidebar_width = 225;
		$cap->leftsidebar_width = 225;
	}
		
    if(!is_page()){
        if($cap->sidebar_position == __('left','cc')){
            $site_width -= $cap->leftsidebar_width;
        } else if($cap->sidebar_position == __('right','cc')){
            $site_width -= $cap->rightsidebar_width;
        } else if($cap->sidebar_position == __('full-width','cc')){

        } else if($cap->sidebar_position == __('left and right','cc')){
            $site_width = $site_width - $cap->rightsidebar_width - $cap->leftsidebar_width;
        }
    } else {
        
        if(isset($post)){
            $tmp = get_post_meta( $post->ID, '_wp_page_template', true );
            
            if( ($tmp == 'default' && $cap->sidebar_position == __('left','cc')) || ($tmp == 'default' && $cap->sidebar_position == __('left and right','cc')) ||
            	$tmp == '_pro/tpl-left-and-right-sidebar.php' || $tmp == '_pro/tpl-search-right-and-left-sidebar.php' ||
                $tmp == '_pro/tpl-left-sidebar.php' || $tmp == '_pro/tpl-search-left-sidebar.php' ){
                $site_width -= $cap->leftsidebar_width;
            }
            if( ($tmp == 'default' && $cap->sidebar_position == __('right','cc')) || ($tmp == 'default' && $cap->sidebar_position == __('left and right','cc')) ||
            	$tmp == '_pro/tpl-left-and-right-sidebar.php' || $tmp == '_pro/tpl-search-right-and-left-sidebar.php'
                || $tmp == '_pro/tpl-right-sidebar.php' || $tmp == '_pro/tpl-search-right-sidebar.php'){
                $site_width -= $cap->rightsidebar_width;
            }
            $detect = new TK_WP_Detect();
            $component = explode('-', $detect->tk_get_page_type());
            
            if(!empty($component[2])){	
                if($component[2] == 'groups' && bp_is_group()) {
                	if( ($cap->bp_groups_sidebars == 'default' && $cap->sidebar_position ==__('left and right','cc')) || $cap->bp_groups_sidebars == 'left' || $cap->bp_groups_sidebars == __('left','cc')  
                        || $cap->bp_groups_sidebars == 'left and right'  || $cap->bp_groups_sidebars == __('left and right','cc') ){ 
                        $site_width -= $cap->leftsidebar_width;
                    } 
                    if($cap->bp_groups_sidebars == 'default' || $cap->bp_groups_sidebars == 'right' || $cap->bp_groups_sidebars == __('right','cc')  
                        || $cap->bp_groups_sidebars == 'left and right'  || $cap->bp_groups_sidebars == __('left and right','cc')){
                        $site_width -= $cap->rightsidebar_width;
                    };

                } elseif($component[2] == 'profile' || bp_is_user()) {

                    if( ($cap->bp_profile_sidebars == 'default' || $cap->sidebar_position == __('default','cc')) 
                    	&& ($cap->bp_profile_sidebars == 'left and right' || $cap->sidebar_position == __('left and right','cc') || $cap->sidebar_position == __('left','cc') || $cap->sidebar_position == 'left') 
                    	|| $cap->bp_profile_sidebars == 'left' || $cap->bp_profile_sidebars == __('left','cc') 
                        || $cap->bp_profile_sidebars == 'left and right' || $cap->bp_profile_sidebars == __('left and right','cc')  ){
                        	$site_width -= $cap->leftsidebar_width;
                    } 
                    if( ($cap->bp_profile_sidebars == "default" || $cap->bp_profile_sidebars == __("default",'cc') ) 
                        && ($cap->sidebar_position == "right" || $cap->sidebar_position == __("right",'cc') || $cap->sidebar_position == "left and right" || $cap->sidebar_position == __("left and right",'cc')) 
                        || $cap->bp_profile_sidebars == 'right' || $cap->bp_profile_sidebars == __('right','cc') 
                        || $cap->bp_profile_sidebars == 'left and right' || $cap->bp_profile_sidebars == __('left and right','cc')  ){ 
							$site_width -= $cap->rightsidebar_width;
                    }
                }  elseif($component[2] == 'members') {
                	if( $cap->sidebar_position ==__('left and right','cc') || $cap->sidebar_position ==__('left','cc') ) {
                		$site_width -= $cap->leftsidebar_width;
                	}
					if( $cap->sidebar_position ==__('left and right','cc') || $cap->sidebar_position ==__('right','cc') ) {
						$site_width -= $cap->rightsidebar_width;
					}
                } else {
                	if( $cap->sidebar_position ==__('left and right','cc') || $cap->sidebar_position ==__('left','cc') ) {
                		$site_width -= $cap->leftsidebar_width;
                	}
					if( $cap->sidebar_position ==__('left and right','cc') || $cap->sidebar_position ==__('right','cc') ) {
						$site_width -= $cap->rightsidebar_width;
					}
                } 
            } 
        }
    }
    return $site_width;
}
?>
