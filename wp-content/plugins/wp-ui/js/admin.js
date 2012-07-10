var wpui_jqui_custom_theme_warning = false;

jQuery(document).ready(function() {
	jQuery( 'div.actions h4' ).next('ul').hide();
	jQuery( 'div.actions h4' ).click(function() {
		jQuery( this ).next( 'ul' ).slideToggle(); 
	});
	jQuery( '#optionsform' ).wrap('<div class="tabwrap" />');

	// $admin_tabs = jQuery('#optionsform #options-wrap').wptabs({
	// 	 					h3Class: 'h3',
	// 	 					effect: 'fade',
	// 	 					topNav: false,
	// 	 					botNav: true,
	// 	 					cookies : false,
	// 						hashchange : false,
	// 						wpuiautop : false,
	// 						collapsibleTabs : false
	// });

	jQuery( '.tab-bottom-nav a, .tab-top-nav a' ).removeClass('ui-button');	

		
		jQuery( '#wpui_styles_preview' )
		.attr( 'href', initOpts.wpUrl + '/wp-admin/admin-ajax.php?action=WPUIstyles' )
		.colorbox({
			iframe : true,
			innerWidth : "80%",
			innerHeight : "80%",
			title : false
		});
				
		jQuery( '#jqui_styles_preview' )
		.attr( 'href', initOpts.wpUrl + '/wp-admin/admin-ajax.php?action=JQUIstyles' )
		.colorbox({
			iframe : true,
			innerWidth : "80%",
			innerHeight : "80%",
			fastIframe : false,
			title : false
			
		});
	
		jQuery('#wpui_styles_preview, #jqui_styles_preview').bind('cbox_open', function(){ 
		    jQuery('body').css({overflow:'hidden'}); 
		}).bind('cbox_closed', function(){ 

		    jQuery('body').css({overflow:'auto'}); 
		});
	
	
		jQuery( 'a.thickbox' ).colorbox({
			transition : 'elastic',
			title : '<span>Current Thumbnail Image</span>'
		});

		
		window.ste_skins = function(skin_name) {
			jQuery('#tab_scheme option').each(function() {
				if ( jQuery(this).attr("value") == skin_name )
					jQuery(this).attr( 'selected', 'selected' );
			});
			tb_remove();
			jQuery( 'p.submit input.button-primary' ).click();
		};

		jQuery('a.wpui_options_help').click(function() {
			jQuery('#contextual-help-link').click();
			return false;
		});
		
		jQuery( '.wpui-clean-cache' ).click(function() {
			tisNonce = jQuery( this ).next('input').val();
			var data = {
				action : 'wpui_clean_cache',
				Cnonce : tisNonce
			}, response;			
			
			jQuery.post( ajaxurl, data, function( response ) {
				// console.log( response ); 
				jQuery.wpuiHiddenInfo( response );
				
			});	
			
			return false;
		});
		
				
		jQuery( '.wpui-clean-meta' ).click(function() {
			tisNonce = jQuery( this ).next('input').val();
			var data = {
				action : 'wpui_clean_postmeta',
				Cnonce : tisNonce
			}, response;			
			
			jQuery.post( ajaxurl, data, function( response ) {
				// console.log( response ); 
				jQuery.wpuiHiddenInfo( response );
			});	
			
			return false;
		});
		
		jQuery( '.wpui-no-bleeding' ).click(function() {
			isCheck = jQuery( '#bleeding_edge' ).is(":checked");
			jQuery( '#bleeding_edge' ).attr( "checked", ( isCheck ) ? "null" : "checked" );	
			jQuery( 'p.submit input.button-primary' ).click();
			return false;
		});
		
		
		
		/*
		 *	Check the fields
		 */
		
		emptyFields = false;
		emptyObs = [];
		defaVals = {
				excerpt_length : 'more',
				post_template_1 : "<h2 class=\"wpui-post-title\">{$title}</h2>\n<div class=\"wpui-post-meta\">{$date} |  {$author}</div>\n<div class=\"wpui-post-thumbnail\">{$thumbnail}</div>\n<div class=\"wpui-post-content\">{$excerpt}</div>\n<p class=\"wpui-readmore\"><a class=\"ui-button ui-widget ui-corner-all\" href=\"{$url}\" title=\"Read more from {$title}\">Read More...</a></p>",
				post_template_2 : "<div class=\"wpui-post-meta\">{$date}</div>\n<div class=\"wpui-post-thumbnail\">{$thumbnail}</div>\n<div class=\"wpui-post-content\">{$excerpt}</div>\n<p class=\"wpui-readmore\"><a href=\"{$url}\" title=\"Read more from {$title}\">Read More...</a></p>"
			
			 };
		
		
		
		newObjs = jQuery( '#post_template_1, #post_template_2, #excerpt_length' );
		
		wpui_fieldCount = 0;

		newObjs.each(function( i ) {
			if ( jQuery( this ).val() == '' ) {
				emptyFields = true;
				emptyObs[ wpui_fieldCount ] = jQuery( this ).attr('id');
				wpui_fieldCount++;
			}
		});
		
		// Somebody call Sherlock, our options are missing!!!
		if ( ! jQuery( 'input[type="checkbox"]' ).is(':checked' ) ) {
		jQuery( '#wpui-cap' ).after('<div id="setting-error-settings_updated" class="updated settings-error"><p style="color: red"><strong>Options are missing. <a href="#" class="correct-wpui-options">Click here to correct this issue</a>.</strong></p></div>' );				
			
			jQuery( '.correct-wpui-options' ).click(function() {
				jQuery('input[name="wpUI_options[reset]"]').click();
				return false;
			});
			
			
		} else if ( emptyObs.length > 0 ) {
		jQuery( '#wpui-cap' ).after('<div id="setting-error-settings_updated" class="updated settings-error"><p style="color: red"><strong>Some of the Essential option fields are empty. <a href="#" class="correct-wpui-options">Click here to correct this issue</a>.</strong></p></div>' );	
			
			
			jQuery( '.correct-wpui-options' ).click(function() {
				var clickOK = confirm("This will fill the required option fields that are empty and save the options.");
				
				if ( clickOK ) {
				
				for( i=0; i < emptyObs.length; i++ ) {
					jQuery( '#' + emptyObs[ i ] ).val(defaVals[ emptyObs[ i ] ] );
				}
				jQuery( 'p.submit input.button-primary' ).click();			
				}
				return false;
				});			
		}


		/**
		 *
		 */
		// jQuery( '#posts table.form-table tr:last' )
		
		
		jQuery( '<tr valign="top" />')
			.append( '<td colspan="2" />' )
			.find( 'td' )
			.append( '<span class="wpui-add-templates-notify">Now don\'t forget to save the options!</span><a href="#" class="wpui-add-templates button-secondary" style="float : right; clear: right;">Add another template</a>' )
			.end()
			.insertAfter(  jQuery( 'table.form-table' ).eq( 4 ).find( 'tr:last' ) );
	
		jQuery( '.wpui-add-templates-notify' )
			.css({
				color : 'red',
				fontWeight : 'bold'				
			})
			.hide();
	
		jQuery( '.wpui-add-templates' ).click(function() {
			$thisTR = jQuery( this ).parent().parent();
			
			prevLength = jQuery( 'textarea[id^=post_template_]');
			lastEl = prevLength.last().attr('id').replace(/post_template_/,'');
			
			lArr = [];
			for( i = 0; i < lastEl; i++ ) {
				prevLength = parseInt( lastEl, 10 ) + 1;
				if ( ! jQuery( '#post_template_' + i ).length )
					prevLength = i;
				// console.log( prevLength );
			}
		
					
			$thisTR
				.prev()
				.clone()
				.find('th[scope=row]')
				.text('Template ' + prevLength )
				.end()
				.find( 'textarea' )
				.attr({
					id : 'post_template_' + prevLength,
					name : 'wpUI_options[post_template_' + prevLength + ']'
				})
				.end()
				.insertAfter( 
					jQuery( '#post_template_' + ( prevLength - 1 ) )
						.parent()
						.parent()
					
					);
				
				jQuery( '.wpui-add-templates-notify' )
					.effect( "pulsate",{ times : 6 }, 1200 )
					.fadeOut( 1200 );
				
				if ( wpUIOpts.bleeding == 'on' )
					jQuery( '.ktabs' ).ktabs( 'fixheight' );
				
			return false;
						
		}); // end add templates click
	

		if ( wpUIOpts.bleeding == 'off' ) {
			$admin_tabs = jQuery('#optionsform #options-wrap').addClass('d-tabs').wptabs({
				 					h3Class: 'h3',
				 					effect: 'fade',
				 					topNav: false,
				 					botNav: true,
				 					cookies : false,
									hashchange : false,
									wpuiautop : false,
									collapsibleTabs : false
			});
		} else {
			jQuery( '#optionsform table.form-table:not(:last) input[type=checkbox]').wpuiToggleSwitch();
			jQuery( '#optionsform table.form-table:last input[type=checkbox]').wpuiToggleSwitch({
				classes : 'advanced'
			});
			
			// jQuery( '#optionsform table.form-table:last').wpuiToggleSwitch();



			
			setTimeout(function() {
				$admin_tabs = jQuery('#optionsform #options-wrap').ktabs({
								// direction	: 'vertical',
								// mode		: 'vertical',
								easing : 'easeInQuart',
								scrollTop : true,
								elements : {
									header : 'h3',
									content : '.form-table'
								},
								autoPlayConf : {
									navigation:false
								}
							});
				
				jQuery( "<span class='toggle_slider_settings' />" )
					.append( '<a title="Experimental - Click to toggle mode. Shift+Click to toggle direction." href="#"></a>' )
					.appendTo( $admin_tabs );
				
				jQuery( 'span.toggle_slider_settings a').click(function( e ) {
					if ( typeof( e.shiftKey ) != 'undefined' && e.shiftKey ) {
						jQuery( '.ktabs' ).ktabs( 'direction', 'shuffle' );
					} else {
						jQuery( '.ktabs' ).ktabs( 'mode', 'shuffle' );
					}					
					return false;					
					
				});

				$admin_tabs.live( 'ktabscreate', function() {
					jQuery( '#optionsform input[type=text]' ).addClass( 'textinput' );	

					jQuery( '#optionsform select' ).selectBox();
				});


				}, 500);

			
		}

		
	
	
/**
 *	Little bit outdated contextual help. :(
 */			

		var context = new Array;

		context[0] = "<p style='background:#FFF; text-align: center; padding:4px; border: 1px solid #AAA'>Click on each tab for help on respective sections.</p><h3>WP UI - General options</h3><p>Enable/disable the plugin components.  This panel provides the following options.</p><h4><strong>Tabs</strong></h4><p>Uncheck the box to disable tabs. <em>Default is enabled</em>. Tabs are navigational widgets that are used to split context into alternative views. See the demo page for more information.</p><p><strong>Accordion</strong></p><p>Uncheck the box to disable accordions. <em>Default is enabled</em>. Accordions are vertically stacked list of items each of which can be clicked to expand the content associated with that item.</p><p><strong>Editor Buttons</strong></p><p>Wordpress post editor buttons makes it easy to insert the tabs into posts. Buttons are available for both Visual and HTML(recommended) mode editors.</p><p><strong>Navigation</strong></p>The tabs only navigation buttons, enables us to move through tabs sequentially without actually clicking one. Default : Bottom navigation buttons are enabled. </p><p><strong>Sliders</strong></p><p>Collapsibles/sliders/spoilers - you can call'em whatever you like! Content is hidden at load and is shown when user clicks the toggler. Use one , You've got a neat slider. Use multiple, you get smooth collapsible panels.</p>";
		
		
		context[1] = "<p style='background:#FFF; text-align: center; padding:4px; border: 1px solid #AAA'>Click on each tab for help on respective sections.</p><h3>WP UI - Style options</h3><h4>Load all styles.</h4><p>If enabled, all styles are loaded with the page and widgets with multiple styles can be shown at the same time.</p><p>Using the default style for the tabs/accordion. For e.g.</p><pre style='background:#FFF;  padding:4px; border-bottom: 1px dotted #AAA'>[wptabs] ..content.. [/wptabs]</pre><p>To use a different styled tabs on the same page, example:<pre style='background:#FFF; padding:4px; border-bottom: 1px dotted #AAA'>[wptabs style='wpui-dark'] ..Content..[/wptabs]</pre><h4>Tabs styles</h4><p>Choose the default styles for the tabs/accordion/sliders. Use the <code class='button-secondary'>visualize and select</code> button to interactively choose through a demo.</p> <blockquote><p><strong><em>Note: </em></strong>The visualize and select is only available for Bundled custom CSS3 styles. Check out the <a href=\"http://jqueryui.com/themeroller/#themeGallery\" title=\"jQuery UI themes\" target=\"_blank\">jQuery UI styles here</a>. </p> <p><em><strong>Note</strong> : As for this version, it is not recommended to use widgets with WP UI custom style and jQuery theme on the same page. jQuery UI themes may cause multiple style inconsistencies, like extra large font size, broken tab layouts. And moreover this varies with different wordpress themes.</em></p></blockquote> <h4>IE gradients</h4> <p>Choose whether to enable Internet Explorer gradients support, using microsoft<code> filter: </code>. A seperate stylesheet is additionally served for IE.</p>";
		
		
		context[2] = "<p style='background:#FFF; text-align: center; padding:4px; border: 1px solid #AAA'>Click on each tab for help on respective sections.</p><h3>WP UI - Effects options</h3><h4>Effects</h4><p>Two effects are available for now, slide and fade. Choose the default effect here.</p><p>Each tabset can have different tab effects, by defining through the shortcode. For e.g.</p><pre style='background:#FFF;  padding:4px; border-bottom: 1px dotted #AAA'>[wptabs effect='fade'] ..content.. [/wptabs]</pre><h4>Effects speed</h4><p>Effects speed is time, in which animating effect is run.  It can be a value in microseconds - 200, 600 or slow and fast. For a swift animation, limt the value within 1000ms. </p><h4>Tabs auto rotation</h4><p>Tabs can be set to  automatically rotate at specified intervals by passing the <code>rotate</code> attribute on the tabs wrapping shortcode. For eg.</p>	<pre style='background:#FFF;  padding:4px; border-bottom: 1px dotted #AAA'>[wptabs rotate=&quot;6000&quot;] ..content.. [/wptabs]<br />[wptabs rotate=&quot;10s&quot;] ..content.. [/wptabs]</pre><p>In the first example, tabs will be rotated i.e switched every 6 seconds ( 6000 is 6s in microseconds ). In the second example, rotate interval is 10s, so tab switch will occur every 10th second.</p>";
		
		
		context[3] = '<p style="background:#FFF; text-align: center; padding:4px; border: 1px solid #AAA">Click on each tab for help on respective sections.</p><h3>WP UI - Text options</h3><h4>Text replacements for the WP UI interface</h4><p>Enter a different value to override the default text - <br /> For tabs</p><ol>  <li>Button for switching to Previous tab</li>  <li>Button for switching to Next tab</li></ol><p>and for WP-spoilers aka Collapsibles/sliders.</p><ol> <li>Collapsible/spoilers Show (hidden) content text.</li><li>Collapsible/spoilers Hide (shown) content text.</li></ol>';
		
		
		
		context[4] = '<p style="background:#FFF; text-align: center; padding:4px; border: 1px solid #AAA">Click on each tab for help on respective sections.</p><h3>Posts</h3><h4>Template for the posts and accordion</h4><p>The html structure here is used for the posts that are displayed within tabs and accordions.</p><h4>Templates for the sliders and dialogs</h4><p>The structure here is the template structure for the post displayed within Dialogs and sliders.</p><h4>Relative time</h4><p>When enabled, relative time is displayed, Example : <code>9 days ago</code>, <code>2 millenia ago</code></p><h4>Excerpt length</h4><p>By default, excerpt of the post is displayed, that is what is upto the more tag. Tweak this to display more text.</p><p>Want to display the whole content? Replace the {$excerpt} with {$content} in the first textbox.</p><h4>Dialog width</h4><p>Default width of the dialog panels.</p>';
		
		
		context[5] = '<p style="background:#FFF; text-align: center; padding:4px; border: 1px solid #AAA">Click on each tab for help on respective sections.</p><h3>Advanced options</h3> <h4>Custom CSS</h4> Use this tab to output additional CSS. For example, this might be for a simple layout fix, or maybe your own skin. <h4>Alternative Shortcodes</h4> When enabled,  it is possible to use shorter codes , e.g <div><ul>	<li>[<strong>tabs</strong>] instead of [wptabs]</li><li>[<strong>tabname</strong>] instead of [wptabtitle]</li><li>[<strong>tabcont</strong>] instead of [wptabcontent]</li><li>[<strong>wslider</strong>] instead of [wpspoiler]</li></ul></div><h4><span style="color: #800000;">Disable jQuery loading</span></h4><div>Please be careful about this option. When checked, jquery will not be loaded by wp-ui. Thereby widgets will not be rendered, when globally jQuery/UI is not available.</div><h4>Cookies!</h4>Cookies are used to store information about the browser state. In our case jQuery UI tabs are able to remember the selected tabs across page reloads and re-visit.<h4>Linking and history</h4>With this option enabled, you can link to the tabs and have them activated on click without reload. History support, i.e. users can click the back button to re open the previous tabs.';




		// if ( /\?page=wpUI-options/gm.test(window.location.href)) {
		// 
		// var cTab = $admin_tabs.children('.ui-tabs').tabs('option', 'selected');
		// 
		// jQuery(".metabox-prefs").html(context[cTab]);
		// 
		// for( i = 0; i<context.length; i++ ) {
		// 	$admin_tabs.bind("tabsshow, tabsselect", function(event, ui) {
		// 		index = ui.index;
		// 		jQuery('.metabox-prefs').html(context[index]);
		// 	});
		// }
		// 
		// }	


});

jQuery.wpuiHiddenInfo = function( content ) {
	

	if (jQuery( '#hidden-info' ).data( 'active' )) {
		jQuery( '#hidden-info' ).slideToggle().html('');
	}
		jQuery( '#hidden-info' )
			.html( content )
			.slideToggle( 'slow' )
			.data( 'active', true );
	
	jQuery( '#hidden-info' ).dblclick(function() {
		tClear();
		return false;
	});
	
	
	var tClear = function() {
		if ( jQuery('#hidden-info').data( 'active' ) ) 
		jQuery('#hidden-info').slideToggle('slow').html('').removeData('active');	
	};
			
	setTimeout( function() {
		tClear();
	}, 6000 );
		
			
	return this;
};

