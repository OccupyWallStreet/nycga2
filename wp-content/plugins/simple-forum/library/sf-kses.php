<?php
/*
Simple:Press
KSES - Alowed Forum Post Tags
$LastChangedDate: 2010-04-18 09:22:20 -0700 (Sun, 18 Apr 2010) $
$Rev: 3920 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sf_kses_array()
{
	global $allowedforumtags, $allowedforumprotocols;

    $allowedforumprotocols = array ('http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet', 'clsid');

	$allowedforumtags = array(
	'address' 	 => array(),
	'a' 		 => array('class' => array(), 'href' => array(), 'id' => array(), 'title' => array(), 'rel' => array(), 'rev' => array(), 'name' => array(), 'target' => array()),
	'abbr' 		 => array('class' => array(), 'title' => array()),
	'acronym' 	 => array('title' => array()),
    'article'    => array('align' => array (), 'class' => array (), 'dir' => array (), 'lang' => array(), 'style' => array (), 'xml:lang' => array()),
	'aside'      => array('align' => array (), 'class' => array (), 'dir' => array (), 'lang' => array(), 'style' => array (), 'xml:lang' => array()),
	'b' 		 => array(),
	'big' 		 => array(),
	'blockquote' => array('id' => array(), 'cite' => array(), 'class' => array(), 'lang' => array(), 'xml:lang' => array()),
	'br' 		 => array('class' => array()),
	'caption' 	 => array('align' => array(), 'class' => array()),
	'cite' 		 => array('class' => array(), 'dir' => array(), 'lang' => array(), 'title' => array()),
	'code' 		 => array('style' => array()),
	'dd' 		 => array(),
    'details'    => array('align' => array (), 'class' => array (), 'dir' => array (), 'lang' => array(), 'open' => array (), 'style' => array (), 'xml:lang' => array()),
	'div' 		 => array('align' => array(), 'class' => array(), 'dir' => array(), 'lang' => array(), 'style' => array(), 'xml:lang' => array()),
	'dl' 		 => array(),
	'dt' 		 => array(),
	'em' 		 => array(),
	'embed' 	 => array('height' => array(), 'name' => array(), 'pallette' => array(), 'src' => array(), 'type' => array(), 'width' => array()),
	'figure'     => array('align' => array (), 'class' => array (), 'dir' => array (), 'lang' => array(), 'style' => array (), 'xml:lang' => array()),
    'figcaption' => array('align' => array (), 'class' => array (), 'dir' => array (), 'lang' => array(), 'style' => array (), 'xml:lang' => array()),
	'font' 		 => array('color' => array(), 'face' => array(), 'size' => array()),
	'footer'     => array('align' => array (), 'class' => array (), 'dir' => array (), 'lang' => array(), 'style' => array (), 'xml:lang' => array()),
	'header'     => array('align' => array (), 'class' => array (), 'dir' => array (), 'lang' => array(), 'style' => array (), 'xml:lang' => array()),
	'hgroup'     => array('align' => array (), 'class' => array (), 'dir' => array (), 'lang' => array(), 'style' => array (), 'xml:lang' => array()),
	'h1' 		 => array('align' => array(), 'class' => array(), 'id'    => array(), 'style' => array()),
	'h2' 		 => array('align' => array(), 'class' => array(), 'id'    => array(), 'style' => array()),
	'h3' 		 => array('align' => array(), 'class' => array(), 'id'    => array(), 'style' => array()),
	'h4' 		 => array('align' => array(), 'class' => array(), 'id'    => array(), 'style' => array()),
	'h5' 		 => array('align' => array(), 'class' => array(), 'id'    => array(), 'style' => array()),
	'h6' 		 => array('align' => array(), 'class' => array(), 'id'    => array(), 'style' => array()),
	'hr' 		 => array('align' => array(), 'class' => array(), 'noshade' => array(), 'size' => array(), 'width' => array()),
	'i' 		 => array(),
	'img' 		 => array('alt' => array(), 'align' => array(), 'border' => array(), 'class' => array(), 'height' => array(), 'hspace' => array(), 'longdesc' => array(), 'vspace' => array(), 'src' => array(), 'style' => array(), 'width' => array()),
	'ins' 		 => array('datetime' => array(), 'cite' => array()),
	'kbd' 		 => array(),
	'label' 	 => array('for' => array()),
	'li' 		 => array('align' 	=> array(), 'class' => array()),
    'menu'       => array('class' => array (), 'style' => array (), 'type' => array ()),
    'nav'        => array('align' => array (), 'class' => array (), 'dir' => array (), 'lang' => array(), 'style' => array (), 'xml:lang' => array()),
	'object' 	 => array('classid' => array(), 'codebase' => array(), 'codetype' => array(), 'data' => array(), 'declare' => array(), 'height' => array(), 'name' => array(), 'param' => array(), 'standby' => array(), 'type' => array(), 'usemap' => array(), 'width' => array()),
	'param' 	 => array('id' => array(), 'name' => array(), 'type' => array(), 'value' => array(), 'valuetype' => array()),
	'p' 		 => array('class' => array(), 'align' => array(), 'dir' => array(), 'lang' => array(), 'style' => array(), 'xml:lang' => array()),
	'pre' 		 => array('class' => array(), 'style' => array(), 'width' => array()),
	'q' 		 => array('cite' => array()),
	's' 		 => array(),
    'section'    => array('align' => array (), 'class' => array (), 'dir' => array (), 'lang' => array(), 'style' => array (), 'xml:lang' => array()),
	'span' 		 => array('class' => array(), 'dir' => array(), 'align' => array(), 'lang' => array(), 'style' => array(), 'title' => array(), 'xml:lang' => array()),
	'strike' 	 => array(),
	'strong' 	 => array(),
	'sub' 		 => array(),
	'summary'    => array('align' => array (), 'class' => array (), 'dir' => array (), 'lang' => array(), 'style' => array (), 'xml:lang' => array()),
	'sup' 		 => array(),
	'table' 	 => array('align' => array(), 'bgcolor' => array(), 'border' => array(), 'cellpadding' => array(), 'cellspacing' => array(), 'class' => array(), 'dir' => array(), 'id' => array(), 'rules' => array(), 'style' => array(), 'summary' => array(), 'width' => array()),
	'tbody' 	 => array('align' => array(), 'char' => array(), 'charoff' => array(), 'valign' => array()),
	'td' 		 => array('abbr' => array(), 'align' => array(), 'axis' => array(), 'bgcolor' => array(), 'char' => array(), 'charoff' => array(), 'class' => array(), 'colspan' => array(), 'dir' => array(), 'headers' => array(), 'height' => array(), 'nowrap' => array(), 'rowspan' => array(), 'scope' => array(), 'style' => array(), 'valign' => array(), 'width' => array()),
	'tfoot' 	 => array('align' => array(), 'char' => array(), 'class' => array(), 'charoff' => array(), 'valign' => array()),
	'th' 		 => array('abbr' => array(), 'align' => array(), 'axis' => array(), 'bgcolor' => array(), 'char' => array(), 'charoff' => array(), 'class' => array(), 'colspan' => array(), 'headers' => array(), 'height' => array(), 'nowrap' => array(), 'rowspan' => array(), 'scope' => array(), 'valign' => array(), 'width' => array()),
	'thead' 	 => array('align' => array(), 'char' => array(), 'charoff' => array(), 'class' => array(), 'valign' => array()),
	'title' 	 => array(),
	'tr' 		 => array('align' => array(), 'bgcolor' => array(), 'char' => array(), 'charoff' => array(), 'class' => array(), 'style' => array(), 'valign' => array()),
	'tt' 		 => array(),
	'u' 		 => array(),
	'ul' 		 => array('class' => array(), 'style' => array(), 'type' => array()),
	'ol' 		 => array('class' => array(), 'start' => array(), 'style' => array(), 'type' => array()),
	'var' 		 => array());
}

?>