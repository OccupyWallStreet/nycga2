<?php

/*
+----------------------------------------------------------------+
+	thkBoxContent-tinymce V1.0
+	by Max Chirkov
+   required for thkBoxContent and WordPress 2.5
+----------------------------------------------------------------+
*/

// look up for the path
require_once( dirname( dirname(__FILE__) ) .'/thkBoxContent-config.php');

global $wpdb;

// check for rights
if ( !is_user_logged_in() || !current_user_can('edit_posts') ) 
	wp_die(__("You are not allowed to be here"));
	
$querystr = "SELECT $wpdb->postmeta.post_id, $wpdb->posts.post_title 
				FROM $wpdb->postmeta 
				LEFT JOIN $wpdb->posts ON
				$wpdb->posts.ID = $wpdb->postmeta.post_id
				WHERE $wpdb->postmeta.meta_value IN ('thickbox-page.php') 
				ORDER BY $wpdb->postmeta.post_id ASC";

$results = $wpdb->get_results($querystr);
	$options = '<select name="thkBC" id="thkBC" style="width: 165px;">'."\n";
foreach($results as $data){
	$options .= "\t".'<option value="'.$data->post_id.'">'.$data->post_title.'</option>'."\n";
}
	$options .= "</select>\n";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>thkBoxContent</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript">
	function init() {
		tinyMCEPopup.resizeToInnerSize();
	}
	
	function insertthkBCLink() {
		
		var tagtext;
		
		var thkBC = document.getElementById('thkBC_options');
		
		
		// who is active ?
		//if (thkBC.className.indexOf('current') != -1) {
			var page_id = document.getElementById('thkBC').value;
			var height = document.getElementById('thkBC_height').value;
			var width = document.getElementById('thkBC_width').value;
			var anchortext = document.getElementById('thkBC_anchortext').value;
			var title = document.getElementById('thkBC_title').value;
			var url = document.getElementById('thkBC_url').value;
			var type = document.getElementById('thkBC_type').value;
			var html_wrap = document.getElementById('thkBC_html_wrap').value;
			var html_id = document.getElementById('thkBC_html_id').value;
			var html_class = document.getElementById('thkBC_html_class').value;
			var inline_id = document.getElementById('thkBC_inline_id').value;
			
			var x;
			var var_arr = new Array();
			var_arr['id'] = page_id;
			var_arr['height'] = height;
			var_arr['width'] = width;
			var_arr['anchortext'] = anchortext;
			var_arr['title'] = title;
			var_arr['url'] = url;
			var_arr['type'] = type;
			var_arr['html_wrap'] = html_wrap;
			var_arr['html_id'] = html_id;
			var_arr['html_class'] = html_class;
			var_arr['inline_id'] = inline_id;
			
			if ( page_id != '' || url != '' || inline_id != '' ){
			
				tagtext = "[thkBC";
				for (x in var_arr)
				{
					if(var_arr[x] != ''){
						tagtext += " " + x + "=\"" + var_arr[x] + "\"";
					}
				}
				tagtext += "]";
			
			}else{
				tinyMCEPopup.close();
			}
		//}
	
		
		if(window.tinyMCE) {
			var selection = tinyMCE.activeEditor.selection.getContent();
			if (selection.length > 0 && type == 'inline') {
				var highlightedContent = tinyMCE.activeEditor.selection.getContent();
				var newHTML = tagtext + '<div id="' + inline_id + '"><div>' + highlightedContent + '</div></div>';
				
				window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false,  newHTML);
			}else{
				window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false,  tagtext);
			}
			//Peforms a clean up of the current editor HTML. 
			//tinyMCEPopup.editor.execCommand('mceCleanup');
			//Repaints the editor. Sometimes the browser has graphic glitches. 
			tinyMCEPopup.editor.execCommand('mceRepaint');
			tinyMCEPopup.close();
		}
		
		return;
	}
	</script>
	<base target="_self" />
</head>
<body id="link" onload="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';document.getElementById('thkBC').focus();" style="display: none">
<!-- <form onsubmit="insertLink();return false;" action="#"> -->
	<form name="thkBoxContent" action="#">
	<div class="tabs">
		<ul>
			<li id="thkBC_tab1" class="current"><span><a href="javascript:mcTabs.displayTab('thkBC_tab1','thkBC_panel');" onmousedown="return false;"><?php _e("Existing Content", 'thkBC'); ?></a></span></li>
			<li id="thkBC_tab2"><span><a href="javascript:mcTabs.displayTab('thkBC_tab2','thkBC_custom_panel');" onmousedown="return false;"><?php _e("Custom URL", 'thkBC'); ?></a></span></li>
			<li id="thkBC_tab3"><span><a href="javascript:mcTabs.displayTab('thkBC_tab3','thkBC_html_panel');" onmousedown="return false;"><?php _e("Markup", 'thkBC'); ?></a></span></li>
			<li id="thkBC_tab4"><span><a href="javascript:mcTabs.displayTab('thkBC_tab4','thkBC_help_panel');" onmousedown="return false;"><?php _e("Help/Like", 'thkBC'); ?></a></span></li>
		</ul>
	</div>
	
	<div id="thkBC_options" class="panel_wrapper" style="height:195px">
		<!-- thkBC panel -->
		<div id="thkBC_panel" class="panel current">
		<br />
		<table border="0" cellpadding="4" cellspacing="0" style="width:100%;">
         <tr>
            <td nowrap="nowrap"><label for="thkBC"><?php _e("Select ThickBox Content:", 'thkBC'); ?></label></td>
		    <td><?php echo $options;?>            </td>
		</tr>
		<tr>
		  <td><label for="thkBC_anchortext"><?php _e("Link Anchor Text:", 'thkBC'); ?></label></td>
		  <td><input type="text" name="thkBC_anchortext" value="Link Anchor Text" id="thkBC_anchortext" size="30" /></td>
		</tr>
		<tr>
		  <td><label for="thkBC_title"><?php _e("ThickBox Title:", 'thkBC'); ?></label></td>
		  <td><input type="text" name="thkBC_title" value="ThickBox Title" id="thkBC_title" size="30" /></td>
		</tr>
		<tr>
			<td style="text-align:center"><label for="thkBC_height"><?php _e("Height:", 'thkBC'); ?></label> <input type="text" name="height" value="300" id="thkBC_height" size="5" />px
			</td>
			<td style="text-align:center"><label for="thkBC_width"><?php _e("Width:", 'thkBC'); ?></label><input type="text" name="width" value="500" id="thkBC_width" size="5" />px
			</td>
			</tr>
			<tr>
			<td colspan="2" style="text-align:center">	
				<small>Use numeric values only.</small>			</td>
          </tr>
		  <tr>
		  <td>
		  	<label for="thkBC_type"><?php _e("Show as:", 'thkBC'); ?></label>
		  </td>
		  <td>
		  	<select name="thkBC_type" id="thkBC_type" style="width: 165px;">
			  <option value="iframe">iFramed Content</option>
			  <option value="ajax">Ajax Content</option>
			  <option value="inline">Inline Content</option>
			</select>
		  </td>
		  </tr>
		  <tr>
		  	<td><label for="thkBC_inline_id"><?php _e("ID of the inline HTML:", 'thkBC'); ?></label></td>
			<td><input type="text" name="thkBC_inline_id" value="" id="thkBC_inline_id" size="30" /></td>
		  </tr>
		  <tr>
			<td colspan="2" style="text-align: center; color:#800000;">(ID required if Inline Ccontent selected. If Ajax selected, enter <strong>local URL</strong> in the Custom URL field.)
		    </td>
          </tr>
        </table>
		</div>
		<!-- end thkBC panel -->
		
		<!-- thkBC_custom panel -->
		<div id="thkBC_custom_panel" class="panel">
		<br />
		<table border="0" cellpadding="4" cellspacing="0" style="width:100%; text-align:center">
         <tr>
            <td colspan="2">
			Content from this URL will appear in the ThickBox window.
			</td>
		</tr>
		<tr>
			<td><label for="thkBC_url"><?php _e("Enter URL:", 'thkBC'); ?></label></td>
		    <td><input type="text" name="thkBC_url" value="" id="thkBC_url" size="30" /></td>
		</tr>
		  <td colspan="2"><small>Leave blank if you want to use existing page selected in the "Existing Content" tab.</small></td>
          </tr>
        </table>
		</div>
		<!-- end thkBC_custom panel -->
		
		<!-- thkBC_html panel -->
		<div id="thkBC_html_panel" class="panel">
		<br />
		<table border="0" cellpadding="4" cellspacing="0" style="width:100%;">
         <tr>
            <td>
				<label for="thkBC_html_wrap"><?php _e("Wrap link into tags:", 'thkBC'); ?></label>
			</td>
			<td>
				<select name="thkBC_html_wrap" id="thkBC_html_wrap" style="width: 165px;">
			  		<option value="">None</option>
			  		<option value="p">&lt;p&gt;</option>
					<option value="div">&lt;div&gt;</option>
					<option value="span">&lt;span&gt;</option>
					<option value="li">&lt;li&gt;</option>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center"><small>ID and Class(es) will be applied to the &lt;a&gt; tag.</small></td>
		</tr>
		<tr>
			<td><label for="thkBC_html_id"><?php _e("Custom id:", 'thkBC'); ?></label></td>
		    <td><input type="text" name="thkBC_html_id" value="" id="thkBC_html_id" size="30" /></td>
		</tr>
		<tr>
			<td><label for="thkBC_html_class"><?php _e("Custom class(es):", 'thkBC'); ?></label></td>
		    <td><input type="text" name="thkBC_html_class" value="" id="thkBC_html_class" size="30" /></td>
		</tr>
		<tr>
		  <td colspan="2" style="text-align: center;"><small>Multiple classes serparate with spaces <br /> (i.e.: thkbx_link read_more).</small></td>
          </tr>
        </table>
		</div>
		<!-- end thkBC_html panel -->
		
		<!-- thkBC_help panel -->
		<div id="thkBC_help_panel" class="panel">
		<br />
		<table border="0" cellpadding="4" cellspacing="0" style="width:100%; text-align:center">
         <tr>
            <td colspan="2">
			<strong><a href="http://www.phoenixhomes.com/files/plugins/thkboxcontent/video_demo.swf" target="_blank">ThickBox Content Demo Video</a></strong>
			</td>
		</tr>
		<tr>
            <td colspan="2">
			<a href="http://jquery.com/demo/thickbox/" target="_blank">Official ThickBox Page</a>
			</td>
		</tr>
		<tr>
		  <td colspan="2"><a href="http://www.phoenixhomes.com/tech/thickbox-content" target="_blank">Comments and feedback</a> are appreciated.<br/> Thank you for using!</td>
          </tr>
		  <tr>
		  <td colspan="2" style="text-align:center">
			<iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.phoenixhomes.com%2Ftech%2Fthickbox-content&amp;layout=standard&amp;show_faces=false&amp;width=450&amp;action=like&amp;colorscheme=light&amp;height=35" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:200px; height:35px;" allowTransparency="true"></iframe>
		  </td>
		  </tr>
        </table>
		</div>
		<!-- end thkBC_help panel -->
		
	</div>

	<div class="mceActionPanel">
		<div style="float: left">
			<input type="button" id="cancel" name="cancel" value="<?php _e("Cancel", 'thkBC'); ?>" onclick="tinyMCEPopup.close();" />
		</div>


		<div style="float: right">
			<input type="submit" id="insert" name="insert" value="<?php _e("Insert", 'thkBC'); ?>" onclick="insertthkBCLink();" />
		</div>
	</div>
</form>
</body>
</html>
<?php

?>
