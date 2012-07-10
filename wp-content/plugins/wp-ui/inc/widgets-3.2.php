<?php
// Widget with Rich text editing support. Pre 3.3 


class wpui_core_widget extends WP_Widget {
	
	private $rich_editing, $wpui_opts;

	function wpui_core_Widget() {
		$widget_ops = array( 'classname' => 'wpui_core', 'description' => 'WP UI Widget' );
		$control_ops = array( 'width' => 600, 'height' => 250, 'id_base' => 'wpui_core' );
		$this->WP_Widget( 'wpui_core', 'WP UI Widget', $widget_ops, $control_ops );
		
		$this->wpui_opts = get_option( 'wpUI_options' );		
		$this->rich_editing = (isset( $this->wpui_opts ) && isset( $this->wpui_opts['widget_rich_text'])) ? true : 'false';
		
		
		add_action( 'admin_print_scripts-widgets.php' , array( &$this, 'add_tabs' ));
		// add_action( 'admin_footer-widgets.php' , array( &$this, 'mod_tinymce' ));
	}

	function add_tabs() {
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-tabs' );
	}

	function mod_tinymce() {
	 add_filter('tiny_mce_before_init', create_function('$a', '
	    $a["theme"] = "advanced";
	    $a["onpageload"] = "";
	    $a["width"] = "100";
	    $a["height"] = "300";
	    $a["mode"] = "specific_textareas";
	    $a["editor_selector"] = "wpui-active-edit";
	    $a["plugins"] = "safari,inlinepopups,spellchecker";
	    $a["forced_root_block"] = false;
	    $a["force_br_newlines"] = true;
	    $a["force_p_newlines"] = false;
	    $a["convert_newlines_to_brs"] = true;
	   	$a["setup"] = \'function(ed) { ed.onChange.add(function(ed, l){  tinymce.triggerSave(); })   }\';		
	    return $a;'));		
		wp_tiny_mce( false );	
	}
	
	function widget( $args, $instance ) {
		extract( $args );
		echo $before_widget;
		$title = apply_filters('widget_title', $instance['title'] );
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		
		$itemz = $instance[ 'item' ];
		$typez = isset( $instance[ 'type'] ) ? $instance[ 'type' ] : 'tabs';

		if ( isset( $instance[ 'style' ] ) && $instance['style'] != 'default' )
			$stylez = $instance[ 'style' ];

		if ( $typez == 'tabs' || $typez == 'accordion' ) {  
			echo '<div class="wp-' . $typez . ' ' . $stylez . '">';
			foreach( $itemz as $item ) {
				if ( $item['title'] == '' ) continue;
				echo '<h3 class="wp-tab-title">' . do_shortcode( $item[ 'title' ] ) . '</h3>';
				echo '<div class="wp-tab-content">' . do_shortcode( $item[ 'panel' ] ) . '</div>';
			}
			echo '</div>';	
		} else {
			foreach( $itemz as $item ) {
				if ( $item['title'] == '' ) continue;
				echo '<div class="wp-spoiler ' . $stylez . '">';
				echo '<h3 class="ui-collapsible-header"><span class="ui-icon"></span>' . do_shortcode( $item[ 'title' ] ) . '</h3>';
				echo '<div class="ui-collapsible-content">' . do_shortcode( $item[ 'panel' ] ) . '</div>';
				echo '<div><!-- end .wp-spoiler -->';				
			}	
			
		}	
		
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance = $new_instance;
		return $instance;
	}

	function form( $instance ) {
		$defaults = array( 
			'title' 	=> '', 
			'type'		=>	'tabs',
			'number'	=>	'4',
			'style'		=>	'wpui-light',
			'item'		=>	array()
		);
		
		// prevent undefined index notice.
		for ( $i = 0; $i <= 4; $i++ ) {
			$defaults['item'][$i] = array('title' => '', 'panel' => '' );
		}		
		
		if ( !isset( $instance[ 'number' ] ) )
			$instance[ 'number' ] = $defaults[ 'number' ];
 		for ( $i = 0; $i <= $instance[ 'number' ]; $i++ ) {
			if ( ! isset( $instance[ 'item' ][$i]) )
			$instance['item'][$i] = array('title' => '', 'panel' => '' );
		}
		
		
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<div class="wpui-widget-left-block">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e("Title"); ?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<p>		
			<label>Type and number of panels</label>
			<select id="<?php echo $this->get_field_id( 'type' ) ?>" name="<?php echo $this->get_field_name( 'type' );  ?>">
				<option value="tabs" <?php selected( $instance['type'], 'tabs') ?>>Tabs</option>
				<option value="accordion" <?php selected( $instance['type'], 'accordion') ?> >Accordion</option>
				<option value="spoiler" <?php selected( $instance['type'], 'spoiler') ?> >Spoilers</option>				
			</select>
			<select id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ) ?>">
				<?php for( $a=2; $a < 10; $a++ ) { ?>
					<option value="<?php echo $a ?>" <?php selected( $instance[ 'number' ], $a ) ?>><?php echo $a ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<?php $skins_list = wpui_get_skins_list(); ?>
		<select id="<?php echo $this->get_field_id( 'style' ) ?>" name="<?php echo $this->get_field_name( 'style' ) ?>">	
			<option value="default">Default</option>
			<?php	

			foreach( $skins_list as $skin=>$name ) {
				if ( stristr( $skin, 'startoptgroup' ) ) {
					echo '<optgroup label="' . $name . '">';
				} else if ( stristr( $skin, 'endoptgroup') ) {
					echo '</optgroup>';
				} else {
				$sel = ( $instance[ 'style' ] == $skin ) ? ' selected="selected"' : '';
				echo '<option value="' . $skin . '"' . $sel . '>' . $name . '</option>';
				}
			}
			?>		
		</select>
		</p>
		<!-- <h3>Notes</h3> -->
		<!-- <ul>If the rich text editor does not display straight away, please try saving the widget once. Reloading the page always does wonders.
		</ul>
		<ul>You might get an occasional flickering when widget is added, this is normal.</ul> -->
		</div>
		<div class="wpui-accordion">
			
		<?php for( $k=1; $k <= $instance['number' ]; $k++ ) { ?>
		<h3 class="wpui-hide-handle">Panel <?php echo $k ?></h3>
		<div class="wp-tab-content" style="position : relative;">
			<p>
			<label for="<?php echo $this->get_field_id( 'item-' . $k . '-title' ) ?>">Title</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'item' . $k . '-title' ) ?>" name="<?php echo $this->get_field_name( 'item][' . $k . '][title]' ) ?>" value="<?php echo $instance[ 'item' ][ $k ][ 'title' ] ?>">
			<label for="<?php echo $this->get_field_id('item-' . $k  . '-panel') ?>" >Contents of Panel <?php echo $k ?></label>
			<textarea cols="5" rows="15" class="widefat wpui-edit" id="<?php echo $this->get_field_id( 'item-' . $k . '-panel' ); ?>" name="<?php echo $this->get_field_name( 'item][' . $k  . '][panel]' ); ?>"><?php echo $instance[ 'item'][$k]['panel'] ?></textarea>
			
		</p>
		</div>
		<?php } ?>
		</div><!-- end .wpui-accordion -->
		
		<script type="text/javascript">
		function array_diff( arr1, arr2 ) {
			var same = [],diff = [];
			for ( var i =0; i < arr2.length; i++ ) {
				same[ arr2[i] ] = true;
			} 
			for( var k =0; k < arr1.length; k++ ) {
				if ( ! same[ arr1[k] ] )
					diff.push( arr1[k]);
			}
			return diff;			
		}
	
		
		// All ready.
		jQuery( function() {
		
			
			jQuery( 'h3.wpui-hide-handle' ).each(function() {
				jQuery( this ).appendTo( jQuery( this ).next() ).hide();
			});
			
			jQuery( '.wpui-accordion' ).each(function() {
				numPanel = jQuery( this ).children( 'div.wp-tab-content' ).length;
				
			if ( jQuery( this ).find( 'ul.ui-tabs-nav' ).length == 0 )	
				jQuery( this ).prepend( '<ul class="ui-tabs-nav wpui-widget-ul" />' );	
							
				liStr = '';
				for ( i = 1; i <= numPanel; i++ ) {
					thisID = "panel-" + i;
						liStr += ( '<li><a href="#' + thisID + '">' + "Panel <span class='panel-num'>" + i + '</span></a></li>' );
				
					jQuery( this )
						.children( '.wp-tab-content' )
						.eq( i - 1 )
						.attr( "id" , thisID );					
					
				}
				
				// jQuery( this  ).find( 'h3.wpui-hide-handle' ).remove();
				
				jQuery( this )
					.children( 'ul.ui-tabs-nav' )
					.html( liStr );
			});
			
		
			$tabs = jQuery( '.wpui-accordion' ).tabs();	
			$tabs.tabs( "option", "disabled", true );
		
			
			$tabs.tabs();

			jQuery( 'a.widget-action' )
					.live('click', function( e ) {
					e.stopPropagation();
						
						
					
					jQuery( this )
						.closest( 'div.widget' )
						.find( 'ul.ui-tabs-nav' )
						.find( 'li a' ).eq(0 ).click(); 
				
					return false;	
				});
		
		
			
		
		
			
		}); // document.ready
		
		
		</script>
		<style type="text/css">
.wpui-hide-handle{background-color:whiteSmoke;background-image:-ms-linear-gradient(top,#f9f9f9,#f5f5f5);background-image:-moz-linear-gradient(top,#f9f9f9,#f5f5f5);background-image:-o-linear-gradient(top,#f9f9f9,#f5f5f5);background-image:-webkit-gradient(linear,left top,left bottom,from(#f9f9f9),to(#f5f5f5));background-image:-webkit-linear-gradient(top,#f9f9f9,#f5f5f5);background-image:linear-gradient(top,#f9f9f9,#f5f5f5);-moz-box-shadow:inset 0 1px 0 #fff;-webkit-box-shadow:inset 0 1px 0 #fff;box-shadow:inset 0 1px 0 #fff;-moz-border-radius:3px;-khtml-border-radius:3px;-webkit-border-radius:3px;border-radius:3px;border:1px solid #DFDFDF;-moz-border-radius:3px;-khtml-border-radius:3px;-webkit-border-radius:3px;border-radius:3px;padding:5px;margin-bottom :0px;}
.wp-tab-content{padding:10px;}
div.wpui-widget-left-block{/* background :red;*/
width:180px;display :inline-block;vertical-align :top;}
div.wpui-accordion{display :inline-block;width :400px;margin-left :20px;}
div.ui-tabs-hide{display :none;}
.wpui-widget-ul{/* background :red;*/
/* padding:5px 0;*/
text-align :center;}
.wpui-widget-ul li{/* background :black;*/
color :#FFF;padding :5px;margin:5px;display :inline-block;}
.wpui-widget-ul li:hover{background :#DFDFDF;-moz-border-radius :3px;-webkit-border-radius :3px;-o-border-radius :3px;border-radius :3px;color :#FFF;text-shadow :0 1px 0 #FFF;}
.wpui-widget-ul li a{color :#333;text-decoration :none;}
.wpui-widget-ul li.ui-tabs-selected{background:-moz-linear-gradient(top,#F2F2F2,#DADADA);background:-webkit-linear-gradient(top,#F2F2F2,#DADADA);background:-o-linear-gradient(top,#F2F2F2,#DADADA);border:1px solid white;color:#333;text-shadow:0 1px 0 white;-moz-border-radius :3px;-webkit-border-radius :3px;-o-border-radius :3px;border-radius :3px;-moz-box-shadow :0 -1px 0 #EEE inset,0 1px 2px rgba(0,0,0,0.3);-webkit-box-shadow :0 -1px 0 #EEE inset,0 1px 2px rgba(0,0,0,0.3);-o-box-shadow :0 -1px 0 #EEE inset,0 1px 2px rgba(0,0,0,0.3);box-shadow :0 -1px 0 #EEE inset,0 1px 2px rgba(0,0,0,0.3);}
.wpui-widget-ul li.ui-tabs-selected .panel-num{background:#D00;box-shadow :0 2px 0 rgba( 0,0,0,0.3 ) inset;color:white;text-shadow:0 1px 0 black;padding:3px 7px;border-radius:15px;}


		</style>
	<?php 
	}


} // END class wpui_core_Widget

function wpui_core_widget_func() {
	register_widget( 'wpui_core_widget' );
}

add_action( 'widgets_init', 'wpui_core_widget_func' );



class wpui_posts_Widget extends WP_Widget {

	function wpui_posts_Widget() {
		$widget_ops = array( 'classname' => 'wpui-posts', 'description' => 'Implement your posts automatically as tabs, sliders, accordions or spoilers.' );
		$control_ops = array( 'width' => 500, 'height' => 250, 'id_base' => 'wpui-posts' );
		$this->WP_Widget( 'wpui-posts', 'WP UI Posts', $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );
		echo $before_widget;
		$title = apply_filters('widget_title', $instance['title'] );
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		
		if ( ! isset( $instance[ 'type' ] ) ) return;


		$inst_args = '';
		
		if ( $instance['type' ] == 'cat' || $instance['type'] == 'tag' ) {
			$inst_args .= ' ' . $instance[ 'type' ] . '="' . $instance[ 'selected' ] . '"';
		} else {
			$inst_args .= ' get="' . $instance[ 'type' ] . '"';
		}
		
		if ( $instance[ 'wid_type' ] != 'tabs' ) {
			$inst_args .= ' type="accordion"';
		}
		
		if ( $instance[ 'template' ] != '' ) {
			$inst_args .= ' template="' . $instance[ 'template' ] . '"';
		}
		if (  isset( $instance[ 'style' ] ) && $instance[ 'style' ] != 'default' ) {
			$inst_args .= ' style="' . $instance[ 'style' ] . '"';
		}  
		if ( isset( $instance[ 'names' ] ) && $instance['names'] != '' ) {
			$inst_args .= ' tab_names="' . $instance[ 'names' ] . '"';
		}

		echo do_shortcode( '[wptabposts' . $inst_args . ']' );
		
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance = $new_instance;
		// foreach ( array('title') as $val ) {
		// 	$instance[$val] = strip_tags( $new_instance[$val] );
		// }
		return $instance;
	}

	function form( $instance ) {
		$defaults = array( 
			'title' 	=> '',
			'type'		=>	'cat',
			'wid_type'	=>	'tabs',
			'selected'	=>	'',
			'names'		=>	'',
			'template'	=>	1,
			'style'		=>	'default'
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<div class="wpui-left-block">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e("Title"); ?>:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
		</p>
		<p>
			<select id="<?php echo $this->get_field_id( 'wid_type' ) ?>" name="<?php echo $this->get_field_name( 'wid_type' ); ?>">
				<option value="tabs" <?php selected( $instance['wid_type'], 'tabs') ?>>Tabs</option>
				<option value="accordion"<?php selected( $instance['wid_type'], 'accordion') ?>>Accordion</option>
			</select>
		</p>
		<p>
			<?php $skins_list = wpui_get_skins_list(); ?>
		<select id="<?php echo $this->get_field_name( 'style' ) ?>" name="<?php echo $this->get_field_id( 'style' ) ?>">	
			<option value="default">Default</option>
			<?php	
			foreach( $skins_list as $skin=>$name ) {
				if ( stristr( $skin, 'startoptgroup' ) ) {
					echo '<optgroup label="' . $name . '">';
				} else if ( stristr( $skin, 'endoptgroup') ) {
					echo '</optgroup>';
				} else {
				if ( $instance[ 'style' ] == $skin ) $sel = ' selected="selected"'; 
				echo '<option value="' . $skin . '"' . $sel . '>' . $name . '</option>';
				}
			}	

			 ?>		
		</select>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_name( 'template' ); ?>">Template no.</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('template') ?>" name="<?php echo $this->get_field_name( 'template' ) ?>" value="<?php echo $instance['template'] ?>"/>	
			
		</p>
		</div>
		
		
		<div class="wpui-search-posts wpui-right-block">
			<input type="text" length="10" id="wpui-search-field" name="wpui-search-field" value="" class="widefat" />
			<select class="wpui-search-type" id="<?php echo $this->get_field_id( 'type' ) ?>" name="<?php echo $this->get_field_name('type') ?>" value="<?php echo $instance['type'] ?>">
				<option value="cat" <?php selected( $instance['type'], 'cat'); ?>>Categories</option>
				<option value="tag" <?php selected( $instance['type'], 'tag'); ?>>Tag</option>
				<option value="recent" <?php selected( $instance['type'], 'recent'); ?>>Recent</option>
				<option value="popular" <?php selected( $instance['type'], 'popular'); ?>>Popular</option>
				<option value="random" <?php selected( $instance['type'], 'random'); ?>>Random</option>
			</select>
			<input type="text" id="wpui-search-number" class="widefat" name="wpui-search-number" style="width : 30px;" value="5" />
			<?php $wpuiTNonce = wp_create_nonce( 'wpui-editor-tax-nonce' ); ?>
			<input type="hidden" id="wpui-editor-tax-nonce" value="<?php echo $wpuiTNonce; ?>">	
			<input type="text" class="wpui-selected" id="<?php echo $this->get_field_id( 'selected' ); ?>" name="<?php echo $this->get_field_name('selected'); ?>" value="<?php echo $instance['selected'] ?>"/>
			<input type="button" id="wpui-fake-submit" value="Search" class="button-secondary" style="width : 280px; border-radius : 3px; margin:5px 0;" />
			<div class="wpui-search-results">
				<ul class="wpui-search-results-list"><li>Type your query and search.</li></ul>
			</div>	
			<label for="<?php echo $this->get_field_name('names') ?>">Names for the tabs, separated by commas. This should match the number of posts selected. </label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('names') ?>" name="<?php echo $this->get_field_name( 'names' ) ?>" value="<?php echo $instance['names'] ?>" />

		</div>
		<div style="clear : both;"></div>
<style type="text/css">
div.wpui-left-block {float: left;width : 150px;padding : 10px;}
div.wpui-right-block {	float: right;	width : 310px;	padding : 10px;	clear : right;}
#wpui-search-field { width : 170px; }
.wpui-search-results { background : #FFF; border : 1px solid #DFDFDF; height : 185px; margin : 0 5px 5px; overflow : auto; position : relative; overflow : auto;}
.wpui-search-results ul.wpui-search-results-list { list-style : none;} 
.wpui-search-results ul.wpui-search-results-list li { border-bottom: 1px solid #F1F1F1; padding : 4px 6px; position : relative; cursor : pointer; margin-bottom : 0;}
.wpui-search-results ul.wpui-search-results-list li.selected {  background : #DDD; font-weight : bold !important; }
.wpui-search-results ul.wpui-search-results-list li a { text-decoration : none; color : #777; text-shadow: 0 1px 0 #FFF; display : block; width : 300px; overflow : hide;}
span.info { position : absolute; top : 0; right : 0;  height: 100%; padding : 4px; }		

</style>
<script type="text/javascript">

	var wpuiGetPosts = function( el ) {
		var searchTerm = jQuery( el ).closest( '#wpui-search-field' ).val(), searchType=jQuery( el ).siblings( 'select.wpui-search-type' ).val(), searchNum = jQuery( el ).closest( '#wpui-search-number' ).val(), wpuiQuery;
		
		searchNum = searchNum || 5;
		
		if ( searchTerm == '' || searchType == '' ) return false;

		wpuiQuery = {
			action : 'wpui_query_meta',
			search : searchTerm,
			type : searchType,
			number : searchNum,
			_ajax_tax_nonce : jQuery( '#wpui-editor-tax-nonce' ).val()
		}

		jQuery.post( ajaxurl, wpuiQuery, function( data ) {
			jQuery( el ).next('div.wpui-search-results')
			.find( 'ul' ).html(data);
			
			thisVal = '';
			jQuery( el ).next('div.wpui-search-results')
			.find( 'ul li' )
			.unbind( 'click' )
			.bind( 'click', function() {
				if ( jQuery( this ).hasClass( 'no-select') ) return false; 
				thisVal = jQuery(this).find('a').attr('rel').replace( /(post|cat|tag)\-/, '');
				jQuery( this ).toggleClass( 'selected' );
				thisVal += ',';
				alSel = jQuery( el ).prev().val();
				if ( alSel.match( thisVal ) )
					alSel = alSel.replace( thisVal, '' );
				else
					alSel += thisVal;
				
				jQuery( el ).prev().val( alSel );
				return false;
			});
		
		});

	};




jQuery( function() {		
	jQuery( '#wpui-fake-submit' ).live( 'click', function( e ) {
		wpuiGetPosts( this );	
		return false;
	});	
	
	jQuery( 'select.wpui-search-type' ).bind('change', function() {
		jQuery( this ).siblings( '.wpui-selected' ).val('');
		jQuery( this ).siblings('input#wpui-fake-submit').click();
	});

});
</script><?php 
	}
}

function wpui_posts_widget_func() {
	register_widget( 'wpui_posts_Widget' );
}

add_action( 'widgets_init', 'wpui_posts_widget_func' );

?>