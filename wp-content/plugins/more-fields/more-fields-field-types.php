<?php

	/*
	**	To overwrite these default field types, use the 'more_fields_field_types' filter
	**
	*/	
	
	function more_fields_field_types () {
		$dir = plugins_url() . '/more-fields/images/';
		
		
		$f = array();

		/*
		**	TEXT
		*/
		$f['text']['label'] = __('Text', 'more-plugins');
		$f['text']['html_item'] = "<label for='%key%'>%title%</label> <input class='%class%' type='text' id='%key%' name='%key%' value='%value%' /> %caption%";
		$f['text']['comment'] = __('Creates a simple one line text input box.', 'more-plugins');

		/*
		**	TEXTAREA
		*/
		$f['textarea']['label'] = __('Textarea', 'more-plugins');
		$f['textarea']['html_item'] = "<label for='%key%'>%title%</label> <textarea class='%class%' id='%key%' name='%key%'>%value%</textarea> %caption%";
		$f['textarea']['comment'] = __('Creates a plain text box.', 'more-plugins');
		
		/*
		**	WYSIWYG
		*/	
		$f['wysiwyg']['label'] = __('WYSIWYG', 'more-plugins');

		$f['wysiwyg']['html_before'] = '
		   <script type="text/javascript">
			   /* <![CDATA[ */
			   jQuery(document).ready( function () { 
				   jQuery("#%key%").addClass("mceEditor");
				   		if ( typeof( tinyMCE ) == "object" && typeof( tinyMCE.execCommand ) == "function" ) {
							tinyMCE.settings.advanced += ",|,add_image,add_video,add_audio,add_media";	
				   			tinyMCE.execCommand("mceAddControl", false, "%key%");
				   }
			   }); 
			   /* ]]> */
		   </script>
			   <div style="width: 100%" class="mf_field_wysiwyg">
			   <label class="mf_wysiwig" for="%key%">%title%</label>
		   <textarea class="%class% %key%" name="%key%" id="%key%">%value%' . "\n";
		   
	//	$f['wysiwyg']['html_before'] << wp_editor($post->post_content, 'content', array('dfw' => true) );
// ed.settings.theme_advanced_buttons1 += ',|,add_image,add_video,add_audio,add_media';		   
//		$f['wysiwyg']['html_before'] = '<textarea class="%class% %key%" name="%key%" id="%key%">' . "\n";
		add_action('mf_the_editor', 'the_editor', 10, 2);
		// function the_editor($content, $id = 'content', $prev_id = 'title', $media_buttons = true, $tab_index = 2) {
//		$f['wysiwyg']['actions'] = array('mf_the_editor' => array( '%value%', '%key%'));
		$f['wysiwyg']['html_item'] = "";
		$f['wysiwyg']['html_after'] = "</textarea></div>\n %caption%";
		$f['wysiwyg']['comment'] = __('Creates text box that allows formatted input. WYSIWYG - What you see is what you get', 'more-plugins');

		/*
		**	SELECT
		*/	
		$f['select']['label'] = __('Select', 'more-plugins');
		$f['select']['html_before'] = "<label for='%key%'>%title%</label> <select class='%class%' id='%key%' name='%key%'>\n";
		$f['select']['html_item'] = "<option value='%value%' %selected%>%value%</option>";
		$f['select']['html_after'] = "</select>\n %caption% \n";
		$f['select']['html_selected'] = 'selected="selected"';
		$f['select']['values'] = true; 
		$f['select']['comment'] = __('Creates a select list, with elements with the names/values as specified below', 'more-plugins');

		/*
		**	RADIOBUTTON
		*/
		$f['radio']['label'] =  __('Radio', 'more-plugins');
		$f['radio']['html_before'] = "<label for='%key%'>%title%</label>";
		$f['radio']['html_item'] = "<label class='mf_radio'><input class='%class%' type='radio' name='%key%' value='%value%' %selected%> %value%</label>";
		$f['radio']['html_after'] = "%caption%";
		$f['radio']['html_selected'] = 'checked="checked"';
		$f['radio']['values'] = true;
		$f['radio']['comment'] = __('Creates a radio button list, where the user can only select one value. The list of values is specifed below.', 'more-plugins');

		/*
		**	CHECKBOX
		*/
		$f['checkbox']['label'] =  __('Checkbox', 'more-plugins');
		$f['checkbox']['html_item'] = "<label class='mf_check'><input class='%class%' type='checkbox' id='%key%' name='%key%' %selected% value='1'> %title%</label>";
		$f['checkbox']['html_selected'] = 'checked="checked"';
		$f['checkbox']['html_after'] = "%caption%";
		$f['checkbox']['values'] = false;
		$f['checkbox']['comment'] = __('Creates single checkbox, which key is either on/off.', 'more-plugins');

		/*
		**	FILE LIST
		*/		
		$f['file-list']['label'] = __('File list', 'more-plugins');
		$f['file-list']['html_item'] = "		
			<input type='hidden' id='%key%' name='%key%' value='%value%'>
			<div class='mf_file_list_show' id='mf_file_list_show_%key%'>
				<a href='%value%'>%value%</a> <input type='button' class='button file_list_update' id='mf_file_list_edit_button_%key%' value='Edit' />
			</div>
			<div class='mf_file_list_edit' id='mf_file_list_edit_%key%'>
				<label class='mf_filelist' for='%key%'>%title%</label>
				<select class='%class% mf_file_list_select' type='checkbox' id='%key%_temp' name='%key%' %selected%></select> 
				<input type='button' class='button file_list_update' id='mf_file_list_update_button_%key%' value='Update list' /> 
			</div>
			";
		$f['file-list']['html_selected'] = 'checked="checked"';
		$f['file-list']['html_after'] = '%caption%';
		$f['file-list']['values'] = true;
		$f['file-list']['comment'] = __('Select files from posts media library.', 'more-plugins');
				
		/*
		**	FILE LIST THUMB
		*/		
		$f['file-list-thumb']['label'] = __('File list w. Thumbnails', 'more-plugins');
		$f['file-list-thumb']['html_item'] = "		
			<input type='hidden' id='%key%' name='%key%' value='%value%'>
			<label class='mf_filelist' for='%key%'>%title%</label>
<!--
			<div class='mf_file_list_show_old' id='mf_file_list_show_%key%'>
				<input type='button' class='button file_list_update' id='mf_file_list_edit_button_%key%' value='Edit' />
			</div>
-->
			<div class='mf_file_list_edit' id='mf_file_list_edit_%key%'>
				<a href='#' class='mf_file_list_update file_list_update' id='mf_file_list_edit_button_%key%'>Update list</a> <select class='%class% mf_file_list_select' type='checkbox' id='%key%_temp' name='%key%' %selected%><option value=''>Reload to list files</option></select> <img  id='mf_file_list_thumb_%key%' class='mf_file_list_thumb' src='%file_list_thumb%' alt='' /> 
			</div>
			";
		$f['file-list-thumb']['html_selected'] = 'checked="checked"';
		$f['file-list-thumb']['html_after'] = '%caption%';
		$f['file-list-thumb']['values'] = true;
		$f['file-list-thumb']['comment'] = __('Select files from posts media library with added thumbnail', 'more-plugins');

	/*
	****	HTML5
	*/

		/*
		**	COLOR
		*/
		$text = __('<code class="mf_color"><a href="http://www.w3.org/TR/css3-color/#html4">color keyword</a></code>, rgb(a) or HEX', 'more-types');
		$f['color']['label'] = __('Color', 'more-plugins');
		$f['color']['html_after'] = "<label class='mf_color' for='%key%'>%title%</label> <input type='color' class='mf_update_on_edit mf_color input-color' id='%key%' name='%key%' value='%value%' /> <b>%value%</b> <small>$text</small> %caption%";
		$f['color']['values'] = false;
		$f['color']['comment'] = __('HTML5 input type, full functionality available in modern browsers. Alternative input values can be <code class="mf_color"><a href="http://www.w3.org/TR/css3-color/#html4">color keyword</a></code>, <code>rgba(255, 255, 255, 1.0)</code>, <code>#ffffff</code> or <code>#fff</code>', 'more-plugins');

		/*
		**	RANGE
		*/
		$text = __('Range', 'more-types');
		$f['range']['label'] = __('Number range', 'more-plugins');
		$f['range']['html_after'] = "<label class='mf_range' for='%key%'>%title%</label> <input class='mf_update_on_edit mf_range input-range' type='range' min='%min%' max='%max%' id='%key%' name='%key%' value='%value%' /> <b>%value%</b> %caption%";
		$f['range']['values'] = true;
		$f['range']['comment'] = __('HTML5 input type, full functionality available in modern browsers. Alternative input: numerical in the range specified below.', 'more-plugins');

		/*
		**	NUMBER
		*/
		$text = __('Number', 'more-types');
		$f['number']['label'] = __('Number', 'more-plugins');
		$f['number']['html_after'] = "<label class='mf_number' for='%key%'>%title%</label> <input class='mf_update_on_edit mf_number input-number' type='number' min='%min%' max='%max%' id='%key%' name='%key%' value='%value%' /> <b>%value%</b> %caption%";
		$f['number']['values'] = true;
		$f['number']['comment'] = __('HTML5 input type, full functionality available in modern browsers. Alternative input: numerical in the range specified below.', 'more-plugins');

		/*
		**	TIME
		*/
		$text = __('<code>HH:ii</code>, i.e <code>13:37</code>', 'more-types');
		$f['time']['label'] = __('Time', 'more-plugins');
		$f['time']['html_after'] = "<label class='mf_time' for='%key%'>%title%</label> <input class='mf_update_on_edit mf_time input-time' type='time' id='%key%' name='%key%' value='%value%' /> <b>%value%</b> <small>$text</small> %caption%";
		$f['time']['values'] = false;
		$f['time']['comment'] = __('HTML5 input type, full functionality available in modern browsers. Alternative input: HH:ii, i.e <code>13:37</code>', 'more-plugins');		

		/*
		**	MONTH
		*/
		$text = __('<code>YYYY-mm</code>, i.e <code>2010-07</code>', 'more-types');
		$f['month']['label'] = __('Month', 'more-plugins');
		$f['month']['html_after'] = "<label class='mf_month' for='%key%'>%title%</label> <input class='mf_update_on_edit mf_month input-month' type='month' id='%key%' name='%key%' value='%value%' /> <b>%value%</b> <small>$text</small>  %caption%";
		$f['month']['values'] = false;
		$f['month']['comment'] = __('HTML5 input type, full functionality available in modern browsers. Alternative input: YYYY-mm, i.e <code>2010-07</code>', 'more-plugins');

		/*
		**	WEEK
		*/
		$text = __('<code>YYYY-WWW</code>, i.e <code>2010-W29</code>', 'more-types');
		$f['week']['label'] = __('Week', 'more-plugins');
		$f['week']['html_after'] = "<label class='mf_week' for='%key%'>%title%</label> <input class='mf_update_on_edit mf_week input-week' type='week' id='%key%' name='%key%' value='%value%' /> <b>%value%</b> <small>$text</small> %caption%";
		$f['week']['values'] = false;
		$f['week']['comment'] = __('HTML5 input type, full functionality available in modern browsers. Alternative input: YYYY-WWW, i.e <code>2010-W27</code>', 'more-plugins');		

		/*
		**	DATE
		*/
		$text = __('<code>YYYY-mm-dd</code>, i.e <code>2010-07-19</code>', 'more-types');
		$f['date']['label'] = __('Date', 'more-plugins');
		$f['date']['html_after'] = "<label class='mf_date' for='%key%'>%title%</label> <input class='mf_update_on_edit mf_date input-date' type='date' id='%key%' name='%key%' value='%value%' /> <b>%value%</b> <small>$text</small> %caption%";
		$f['date']['values'] = false;
		$f['date']['comment'] = __('HTML5 input type, full functionality available in modern browsers. Alternative input: YYYY-mm-dd, i.e <code>2010-07-10</code>', 'more-plugins');

		
		return $f;
	}	
	
	function more_fields_write_css () {
		$dir = plugins_url() . '/more-fields/images/';
		$css = '

		.mf_field_wrapper
		{
			margin: 0 6px 0;
			overflow: hidden;
		}

			input[type=range]
			{
				float: left;
				margin-right: .5em;
			}

			.mf_field_wrapper label,
			.mf_field_wrapper small
			{
				display: block;
				clear: left;
			}
			
				.mf_field_wrapper label
				{
					font-weight: bold;
					padding: .5em 0;
				}

				.mf_field_wrapper small
				{
					color: #999;
					margin-top: 6px;
				}
				
					.mf_field_wrapper small+p.mf_caption
					{
						margin-top: 3px !important;
					}
					
					.mf_field_wrapper small code.mf_color a
					{
						text-decoration: none;
					}
					
			.mf_field_wrapper label.mf_check,
			.mf_field_wrapper label.mf_radio
			{
				font-weight: normal;
				float: left;
				clear: none;
				margin-right: .5em;
				padding-left: 0;
			}

			.mf_field_wrapper p.mf_caption
			{
				margin-left: 0 !important;
				clear: both;
				color: #999;
			}

		.mf_text
		{ 
			width: 95%; 
			margin-left: 1px; 
		}
		.mf_textarea
		{ 
			width: 95%; 
			margin-left: 1px; 
			height: 150px;
		}
		.mf_select
		{
			width: 96%; 
		}
		.wrap #post-body h2
		{
			clear: none;
		}

		.mf_field_wrapper .mf_field_wysiwyg 
		{
			overflow: hidden;
		}

			.inside .mceResize
			{
				margin-top: -45px !important;
			}
			.inside .mceEditor iframe
			{
				border-left: 1px solid #dfdfdf;
				border-right: 1px solid #dfdfdf;
				box-sizing: border-box;
				-moz-box-sizing:border-box; /* Mozilla: Change Box Model Behaviour */
			}

		.mf_file_list_edit select
		{
			float: left;
			width: 150px;
			margin: 2px 3px 0 3px;
		}
		
		.mf_file_list_edit .mf_file_list_thumb
		{
			height: 25px;
			background: url(' . $dir . '/img-list-thumb.png) no-repeat;
		}

		.mf_file_list_edit .mf_file_list_update
		{
			background: url(' . $dir . '/update.png) no-repeat;
			text-indent: -9999px;
			display: block;
			float: left;
			width: 27px;
			height: 21px;
			margin: 2px 0 0 0;
		}
		
			.mf_file_list_update:hover
			{
				background: url(' . $dir . '/update-hover.png) no-repeat;
			}
			
			.mf_file_list_update:active
			{
				background: url(' . $dir . '/update-down.png) no-repeat;
			}
			
		.mf_field_wysiwyg table
		{
			background: #fff;
		}

		';
		return $css;
	}
	function more_fields_write_js () {
		$dir = plugins_url() . '/more-fields/images/';

		$js = "
		
		// Get the attachement thumbnail
		function more_fields_update_thumb(name, id) {
			var url  = '" . get_option('siteurl') . "/wp-admin/admin-ajax.php';
			var data = {
				action: 'more_fields_file_list_thumb',
				thiss: name,
				post_id: id
			};
			jQuery.post(url, data, function(response) {
				var json = eval(\"(\" + response + \")\");
				html = (json['url']) ? '<img class=\'mf_thumb\' src=\'' + json['url'] + '\'>' : '';
				if (!json['url']) json['url'] = '" . $dir . "/img-list-thumb.png';
				jQuery('#mf_file_list_thumb_' + json['thiss']).attr('src', json['url']);
			});		
		}
		
		
		// Show the file list box
		jQuery('.mf_file_list_show').each(function(one){
	//		if (!jQuery('#' + this.id.replace('mf_file_list_show_', '')).val()) {
	//			jQuery('#' + this.id).hide();
	//			jQuery('#' + this.id.replace('show', 'edit')).show();
	//		} else {
	//			jQuery('#' + this.id).show();
	//			jQuery('#' + this.id.replace('show', 'edit')).hide();			
	//		}
		});

		// Bind the file list field type
		jQuery('.file_list_update').bind('click', function(){
			var regexp = /edit/;
			if (regexp.test(this.id)) { 
				jQuery('#' + this.id.replace('edit_button', 'edit')).show();
				jQuery('#' + this.id.replace('edit_button', 'show')).hide();
				var clicked = this.id.replace('mf_file_list_edit_button_', '');
			} else {
				var clicked = this.id.replace('mf_file_list_update_button_', '');
			}
			var url  = '" . get_option('siteurl') . "/wp-admin/admin-ajax.php';			
			var data = {
				action: 'more_fields_file_list',
				clicked: clicked,
				post_id: jQuery('#post_ID').val()
			};
			jQuery.post(url, data, function(response) {
				var html = '<option></option>';
				var htmlth = html;
				var json = eval(\"(\" + response + \")\");
				jQuery.each(json['data'], function(i, file){
					html = html + '<option value=\"' + file['guid'] + '\">' + file['post_title'] + ' (' + file['post_mime_type']  + ')</option>';				
					htmlth = htmlth + '<option value=\"' + file['ID'] + '\">' + file['post_title'] + ' (' + file['post_mime_type']  + ')</option>';				
				});
				if (jQuery('#mf_file_list_thumb_' + clicked)) jQuery('#' + json['clicked'] + '_temp').html(htmlth);
				else jQuery('#' + json['clicked'] + '_temp').html(html);
			});
			return false;
		});
		
		// Bind the file list select list to set the real id
		jQuery('.mf_file_list_select').bind('change', function(){
			if (this.value) jQuery('#' + this.id.replace('_temp', '')).val(this.value);
			more_fields_update_thumb(this.name, this.value);
		});
		
		jQuery()
		
		
		";
		return $js;
	}
	

?>