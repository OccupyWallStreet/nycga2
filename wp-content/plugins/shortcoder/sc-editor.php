<?php
/**
 * Shortcoder include for inserting and editing shortcodes in post and pages
 * v1.0
 **/
 
if ( ! isset( $_GET['inline'] ) )
	define( 'IFRAME_REQUEST' , true );

// Load WordPress Administration Bootstrap
require_once('../../../wp-admin/admin.php');

if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
    wp_die(__('You do not have permission to edit posts.'));

// Load all created shortodes
$sc_options = get_option('shortcoder_data');

if(empty($sc_options))
	die();
?>

<html>
<head>
<title>Shortcodes created</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.min.js"></script>
<style type="text/css">
body{
	font: 13px Arial, Helvetica, sans-serif;
	padding: 10px;
}
h2{
	font-size: 23px;
	font-weight: normal;
}
h4{
	margin: 0px 0px 10px;
}
hr{
	border-width: 0px;
	margin: 10px -10px;
	border-bottom: 1px solid #dfdfdf;
}
.sc_wrap{
	border: 1px solid #dfdfdf;
	-moz-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
	-webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
	-moz-border-radius: 10px;
	border-radius: 10px;
}
.sc_shortcode{
	border-bottom: 1px solid #ccc;
	padding: 0px;
	-moz-border-radius: 10px;
	border-radius: 10px;
}
.sc_shortcode_name{
	cursor: pointer;
	padding: 10px;
}
.sc_params{
	border: 1px solid #dfdfdf;
	background: #F9F9F9;
	margin: 2px 10px 10px;
	padding: 10px;
	display: none;
	-moz-border-radius: 5px;
	border-radius: 5px;
}
.sc_insert{
	-moz-border-radius:20px;
	-webkit-border-radius:20px;
	border-radius:20px;
	-moz-box-shadow:1px 1px 2px rgba(0, 0, 0, 0.5);
	-webkit-box-shadow:1px 1px 2px rgba(0, 0, 0, 0.5);
	box-shadow:1px 1px 2px rgba(0, 0, 0, 0.5);
	background: -moz-linear-gradient(19% 65% 90deg,#0087B4, #0099CC, #0099CC 51%);
	background: -webkit-gradient(linear, 0% 45%, 0% 60%, from(#0099CC), to(#0087B4));
	color:#FFFFFF;
	padding:2px 15px;
	text-shadow:0 1px 1px rgba(0, 0, 0, 0.5);
	border: 1px solid #267ed4;
	font-weight: bold;
}

.sc_insert:hover{
	background: -moz-linear-gradient(19% 65% 90deg,#0082AD, #0099CC, #0099CC 51%);
	background: -webkit-gradient(linear, 0% 45%, 0% 60%, from(#0099CC), to(#0082AD));
	color: #f1f1f1;
}
input[type=text], textarea{
	padding: 5px;
	border: 1px solid #ccc;
	box-shadow: inset 1px 1px 1px rgba(0,0,0,0.1);
	-moz-box-shadow: inset 1px 1px 1px rgba(0,0,0,0.1);
	-webkit-box-shadow: inset 1px 1px 1px rgba(0,0,0,0.1);
	-moz-border-radius: 5px;
	border-radius: 5px;
	width: 120px;
	margin: 0px 25px 10px 0px;
	cursor: pointer;
}
.sc_toggle{
	background: url(images/toggle-arrow.png) no-repeat;
	float: right;
	width: 16px;
	height: 16px;
	opacity: 0.4;
}

.sc_share_iframe{
	background: #FFFFFF;
	border: 1px solid #dfdfdf;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
	-moz-box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
	-webkit-box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
	box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
}
.sc_credits{
	background: url(images/aw.png) no-repeat;
	padding-left: 23px;
	color: #8B8B8B;
	margin-left: -5px;
	font-size: 13px;
	text-decoration: none;
}
</style>
<script type="text/javascript">
$(document).ready(function(){
	
	$('.sc_shortcode_name').append('<span class="sc_toggle"></span>');
	
	$('.sc_insert').click(function(){
		var params = '';
		var scname = $(this).attr('data-name');
		var sc = '';
		
		$(this).parent().children().find('input[type="text"]').each(function(){
			if($(this).val() != ''){
				attr = $(this).attr('data-param');
				val = $(this).val();
				params += attr + '="' + val + '" ';
			}
		});
		
		if(wsc(scname)){
			name = '"' + scname + '"';
		}else{
			name = scname;
		}
		sc = '[sc:' + name + ' ' + params + ']';
		parent.send_to_editor(sc);
	});
	
	$('.sc_share_bar img').mouseenter(function(){
		$this = $(this);
		$('.sc_share_iframe').remove();
		$('body').append('<iframe class="sc_share_iframe"></iframe>');
		$('.sc_share_iframe').css({
			position: 'absolute',
			top: $this.offset()['top'] - $this.attr('data-height') - 15,
			left: $this.offset()['left'] - $this.attr('data-width')/2 ,
			width: $this.attr('data-width'),
			height: $this.attr('data-height'),
		}).attr('src', $this.attr('data-url')).hide().fadeIn();
	
	});
	
	$('.sc_shortcode_name').click(function(e){
		$('.sc_params').slideUp();
		if($(this).next('.sc_params').is(':visible')){
			$(this).next('.sc_params').slideUp();
		}else{
			$(this).next('.sc_params').slideDown();
		}
	})
	
});

var sc_closeiframe = function(){
	$('.sc_share_iframe').remove();
}

function wsc(s){
	if(s == null)
		return '';
	return s.indexOf(' ') >= 0;
}
</script>
</head>
<body>
<?php sc_admin_buttons('fbrec'); ?>
<h2><img src="images/shortcoder.png" align="absmiddle" alt="Shortcoder" width="35px"/> List of Shortcodes created</h2>

<div class="sc_wrap">
<?php
foreach($sc_options as $key=>$value){
	if($key != '_version_fix'){
		echo '<div class="sc_shortcode"><div class="sc_shortcode_name">' . $key;
		echo '</div>';
		preg_match_all('/%%[^%\s]+%%/', $value['content'], $matches);
		echo '<div class="sc_params">';
		if(!empty($matches[0])){
			echo '<h4>Available parameters: </h4>';
			$temp = array();
			foreach($matches[0] as $k=>$v){
				$cleaned = str_replace('%', '', $v);
				if(!in_array($cleaned, $temp)){
					array_push($temp, $cleaned);
					echo '<label>' . $cleaned . ': <input type="text" data-param="' . $cleaned . '"/></label> ';
				}
			}
			echo'<hr/>';
		}else{
			echo 'No parameters avaialble - ';
		}
		echo '<input type="button" class="sc_insert cupid-blue" data-name="' . $key . '" value="Insert Shortcode"/>';
		echo '</div>';
		echo '</div>';
	}
}
?>
</div>

<p class="sc_share_bar" align="center">
<img class="sc_donate" src="images/donate.png" data-width="300" data-height="220" data-url="<?php echo SC_URL . 'js/share.php?i=1'; ?>"/>
&nbsp;&nbsp;&nbsp;
<img class="sc_share" src="images/share.png" data-width="350" data-height="85" data-url="<?php echo SC_URL . 'js/share.php?i=2'; ?>"/>
</p>

<p align="center"><a class="sc_credits" href="http://www.aakashweb.com/" target="_blank">a Aakash Web plugin</a></p>


<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=vaakash"></script>
</body>
</html>