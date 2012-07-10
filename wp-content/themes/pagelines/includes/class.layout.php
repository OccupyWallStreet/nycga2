<?php
/**
 * PageLinesLayout
 *
 * Class for managing content layout
 *
 * @package     PageLines Framework
 * @subpackage  Layout
 *
 * @since       1.0
 */
class PageLinesLayout {

	// BUILD THE PAGELINES OBJECT

		/**
		*
		* @TODO document
		*
		*/
		function __construct($layout_mode = null) {
			
			$this->builder = new stdClass;			
			$this->clip = new stdClass;
			$this->sidebar_wrap = new stdClass;
			$this->column_wrap = new stdClass;
			$this->dynamic_grid = new stdClass;
			$this->margin = new stdClass;
			$this->content = new stdClass;
			$this->gutter = new stdClass;
			$this->sidebar1 = new stdClass;
			$this->sidebar2 = new stdClass;
			$this->main_content = new stdClass;
			$this->hidden = new stdClass;			

			$this->builder->width = 1400;
			/*
				Get the layout map from DB, or use default
			*/
			$this->get_layout_map();
			
			/*
				If layout mode isn't set, then use the saved default mode.
			*/
			if( isset($layout_mode) )
				$this->layout_mode = $layout_mode;
			elseif ( isset($this->layout_map['saved_layout'])  && !empty($this->layout_map['saved_layout']) )
				$layout_mode = $this->layout_map['saved_layout'];	
			else
				$layout_mode = ( ploption( 'layout_default' ) ) ? ploption( 'layout_default' ) : 'one-sidebar-right';
		
			$this->build_layout($layout_mode);
		}
		

		/**
		*
		* @TODO document
		*
		*/
		function build_layout($layout_mode){
			
			/*
				Set the current pages layout
			*/
			$this->layout_mode = $layout_mode;
			
			/*
				Get number of columns
			*/
			$this->set_columns();
			
			/*
				Set layout dimensions
			*/
			$this->set_layout_data();
			
			/*
				Set wrap dimensions for use on page
			*/
			$this->set_wrap_dimensions();
			
			/*
				Set scaled dimensions and convert for use in the JS builder
			*/
			$this->set_builder_dimensions();
			
			
		}
		

		/**
		*
		* @TODO document
		*
		*/
		function set_columns(){
			if($this->layout_mode == 'two-sidebar-center' || $this->layout_mode == 'two-sidebar-left' || $this->layout_mode == 'two-sidebar-right')
				$this->num_columns = 3;
			elseif($this->layout_mode == 'one-sidebar-left' || $this->layout_mode == 'one-sidebar-right')
				$this->num_columns = 2;
			else 
				$this->num_columns = 1;
		}
		


		/**
		*
		* @TODO document
		*
		*/
		function get_layout_map(){
			
			$db_layout_map = ploption('layout');
			
			$this->layout_map = ( $db_layout_map && is_array($db_layout_map) ) ? $db_layout_map : $this->default_layout_setup();
			
			
			
		}
		

		

		/**
		*
		* @TODO document
		*
		*/
		function default_layout_setup(){
		
			$this->content->width = 1100;
			$this->content->percent = $this->get_content_percent($this->content->width);
			
			$this->gutter->width = 20;
			
			$def_main_two = 780;
			$def_sb_two = 320;
			
			$def_main_three = 620;
			$def_sb_three = 240;
			
			$default_map = array(
					'saved_layout' 			=> 'one-sidebar-right',
					'last_edit' 			=> 'one-sidebar-right',
					'content_width' 		=> $this->content->width,
					'responsive_width' 		=> $this->content->percent,
					'one-sidebar-right' 	=> array(	
							'maincolumn_width' 		=> $def_main_two,
							'primarysidebar_width'	=> $def_sb_two,
							'gutter_width' 			=> $this->gutter->width, 
							'content_width'			=> $this->content->width
						), 
					'one-sidebar-left' 	=> array(	
							'maincolumn_width' 		=> $def_main_two,
							'primarysidebar_width'	=> $def_sb_two,
							'gutter_width' 			=> $this->gutter->width, 
							'content_width'			=> $this->content->width
						),
					'two-sidebar-right' 	=> array(	
							'maincolumn_width' 		=> $def_main_three,
							'primarysidebar_width'	=> $def_sb_three,
							'gutter_width' 			=> $this->gutter->width, 
							'content_width'			=> $this->content->width 
						),
					'two-sidebar-left' 	=> array(	
							'maincolumn_width' 		=> $def_main_three,
							'primarysidebar_width'	=> $def_sb_three,
							'gutter_width' 			=> $this->gutter->width, 
							'content_width'			=> $this->content->width
						),
					'two-sidebar-center' 	=> array(	
							'maincolumn_width' 		=> $def_main_three,
							'primarysidebar_width'	=> $def_sb_three,
							'gutter_width' 			=> $this->gutter->width, 
							'content_width'			=> $this->content->width
						),
					'fullwidth' 	=> array(	
							'maincolumn_width' 		=> $this->content->width,
							'primarysidebar_width'	=> 0, 
							'gutter_width' 			=> 0, 
							'content_width'			=> 0
						)
				);
				
		
			return $default_map;
		}


		/**
		*
		* @TODO document
		*
		*/
		function get_content_percent( $content_width ){
			return ( $content_width / $this->builder->width ) * 100;
		}

		

		/**
		*
		* @TODO document
		*
		*/
		function set_layout_data(){
			
			// Text & IDs
				$this->hidden->text = '';
				$this->hidden->id = 'hidden';

				$this->main_content->text = __( 'Main Column', 'pagelines' );
				$this->main_content->id = 'layout-main-content';

				$this->sidebar1->text = 'SB1';
				$this->sidebar1->id = 'layout-sidebar-1';

				$this->sidebar2->text = 'SB2';
				$this->sidebar2->id = 'layout-sidebar-2';
			
			$this->gutter->width = 30;
			
			$this->fudgefactor = 24;
		
			$this->hidden->width = 0;
			
			$this->content->width = $this->layout_map['content_width'];
			$this->content->percent = $this->get_content_percent( $this->layout_map['content_width'] );
			
			foreach($this->layout_map as $layoutmode => $settings){
				if($this->layout_mode == $layoutmode && ($layoutmode == 'one-sidebar-right' || $layoutmode == 'one-sidebar-left')){
					
					//Account for javascript saving of other layout type
					$this->main_content->width = $settings['maincolumn_width'];
					$this->sidebar1->width = $this->content->width - $settings['maincolumn_width'];
					
				} elseif($this->layout_mode == $layoutmode && ($layoutmode == 'fullwidth')){
					
					//Account for javascript saving of other layout type
					$this->main_content->width = $this->content->width;
					$this->sidebar1->width = 0;
					
				}elseif($this->layout_mode == $layoutmode) {
				
					$this->main_content->width = $settings['maincolumn_width'];
					$this->sidebar1->width = $settings['primarysidebar_width'];
				
				}
			}
				
			$this->margin->width = ($this->builder->width - $this->content->width)/2 - ($this->fudgefactor - 1);
						
			$this->sidebar2->width = $this->content->width - $this->main_content->width - $this->sidebar1->width;
		
			$this->dynamic_grid->width = $this->content->width/12;
		
		}
		
		

		/**
		*
		* @TODO document
		*
		*/
		function set_wrap_dimensions(){
			if($this->layout_mode == 'two-sidebar-center'){
				$this->column_wrap->width = $this->main_content->width + $this->sidebar1->width;
				$this->sidebar_wrap->width = $this->sidebar2->width;
				
				$this->clip->width = ($this->main_content->width - (3 * $this->gutter->width))/2 ;
				
			}elseif($this->layout_mode == 'two-sidebar-right' || $this->layout_mode == 'two-sidebar-left'){
				
				$this->column_wrap->width = $this->main_content->width;
				$this->sidebar_wrap->width = $this->sidebar1->width + $this->sidebar2->width;
				$this->clip->width = ($this->main_content->width - (2 * $this->gutter->width))/2 ;
				
			}elseif($this->layout_mode == 'one-sidebar-right' || $this->layout_mode == 'one-sidebar-left'){
				$this->column_wrap->width = $this->main_content->width;
				$this->sidebar_wrap->width = $this->sidebar1->width;
				
				$this->clip->width = ($this->main_content->width - (2 * $this->gutter->width))/2 ;
			}else{
				$this->sidebar_wrap->width = 0;
				$this->column_wrap->width = $this->main_content->width;
				$this->clip->width = ($this->main_content->width - (1 * $this->gutter->width))/2 ;
			}
		}
		

		/**
		*
		* @TODO document
		*
		*/
		function set_builder_dimensions(){
			
			$this->builder->bwidth 		= $this->downscale($this->builder->width);
			$this->content->bwidth 		= $this->downscale($this->content->width);
			$this->gutter->bwidth 		= $this->downscale($this->gutter->width);
			$this->margin->bwidth 		= $this->downscale($this->margin->width);
			$this->main_content->bwidth = $this->downscale($this->main_content->width);
			$this->sidebar1->bwidth		= $this->downscale($this->sidebar1->width);
			$this->sidebar2->bwidth 	= $this->downscale($this->sidebar2->width);
				
			$this->hidden->bwidth = 0;
			
			/*
				Convert builder dimensions to dimensions the plugin understands
			*/
			$this->builder_inner_directions();
		}


		/**
		*
		* @TODO document
		*
		*/
		function builder_inner_directions(){
			if($this->layout_mode == 'two-sidebar-right'){
				
				$this->west = $this->main_content;
				$this->center = $this->sidebar1;
				$this->east = $this->sidebar2;
			}elseif($this->layout_mode == 'two-sidebar-left'){

				$this->east = $this->main_content;
				$this->west = $this->sidebar1;
				$this->center = $this->sidebar2;
			}elseif($this->layout_mode == 'two-sidebar-center'){

				$this->east = $this->sidebar2;
				$this->west = $this->sidebar1;
				$this->center = $this->main_content;
			}elseif($this->layout_mode == 'one-sidebar-right'){
				$this->east = $this->sidebar1;
				$this->west = $this->hidden;
				$this->center = $this->main_content;
			}
			elseif($this->layout_mode == 'one-sidebar-left'){
				$this->east = $this->hidden;
				$this->west = $this->sidebar1;
				$this->center = $this->main_content;
			}elseif($this->layout_mode == 'fullwidth'){
				$this->east = $this->hidden;
				$this->west = $this->hidden;
				$this->center = $this->main_content;
			}else{
				echo 'There was an issue setting layout. Please reset your settings.';
			}
		}
		

		/**
		*
		* @TODO document
		*
		*/
		function downscale($actual_pixels, $ratio = 2){
			return floor($actual_pixels / $ratio);
		}



		/**
		*
		* @TODO document
		*
		*/
		function get_layout_inline(){
			
			$l = $this->calculate_dimensions($this->layout_mode);
			$mode = '.'.$this->layout_mode.' ';
			$css = '';
			$c = $this->content->width;
			$p = $this->content->percent;
			
			// Selectors 
			
				// Setup Page Width
					$page_width_array = apply_filters( 'pl_page_width', array('body.fixed_width #page', 'body.fixed_width #footer', 'body.canvas .page-canvas') );
					$page_width_sel = join(',', $page_width_array);
				
				// Setup Content Width
					$content_width_array = apply_filters( 'pl_content_width', array('#site .content', '#footer .content') );
					$content_width_sel = join(',', $content_width_array);
				
		
			// Options 
				$layout_handling = ploption('layout_handling');
				$design_mode = ploption('site_design_mode');
			
				$contained = ($design_mode == 'fixed_width' && !pl_is_disabled('color_control')) ? true : false;
		
		
			// Set CSS for content and page width
				if( $layout_handling == 'percent'){
				
					if($contained){
						$css .= sprintf($page_width_sel . '{ width: %s%%;}', $p);
						$css .= sprintf($content_width_sel . '{ width: %s%%; }', '100');
					} else 
						$css .= sprintf($content_width_sel . '{ width: %s%%; }', $p);
				
				} elseif( $layout_handling == 'pixels' ){
					$css .= sprintf($page_width_sel . '{ max-width:%spx; }', $c);
					$css .= sprintf($content_width_sel . '{ width: 100%%; max-width:%spx;}', $c);
				}else{
					$css .= sprintf($page_width_sel . '{ max-width:%spx; }', $c);
					$css .= sprintf($content_width_sel . '{ width:%spx;}', $c);
				}
			
			// Set CSS for inner elements based on mode
				$content_id = apply_filters('pl_content_id', '#pagelines_content');
			
				$main_col_id = apply_filters('pl_main_id', '#column-main');
			
			foreach(get_the_layouts() as $mode){
			
				$l = $this->calculate_dimensions($mode);
				
				$mode_selector = '.'.$mode;
			
				$css .= sprintf('%1$s %3$s %4$s{ %2$s }', $mode_selector, $l['main'], $content_id, $main_col_id);
				$css .= sprintf('%1$s %3$s #sidebar1{ %2$s }', $mode_selector, $l['sb1'], $content_id);
				$css .= sprintf('%1$s %3$s #sidebar2{ %2$s }', $mode_selector, $l['sb2'], $content_id);
				$css .= sprintf('%1$s %3$s #column-wrap{ %2$s }', $mode_selector, $l['colwrap'], $content_id);
				$css .= sprintf('%1$s %3$s #sidebar-wrap{ %2$s }', $mode_selector, $l['sbwrap'], $content_id);
				
			}
			
			return $css;
		}
		

		/**
		*
		* @TODO document
		*
		*/
		function calculate_dimensions( $layout_mode ){
			
			$save_mode = $this->layout_mode;
			
			$this->build_layout($layout_mode);
			
			$l = array();

			/* (target / context)*100 = percent-result */
			
			$l['colwrap'] = $this->get_width( $this->column_wrap->width, $this->content->width ); 
			$l['sbwrap'] = $this->get_width( $this->sidebar_wrap->width, $this->content->width );

			$l['main'] = $this->get_width( $this->main_content->width, $this->column_wrap->width );

			$l['sb2'] = $this->get_width( $this->sidebar2->width, $this->sidebar_wrap->width );

			if($layout_mode == 'two-sidebar-center')
				$l['sb1'] = $this->get_width( $this->sidebar1->width, $this->column_wrap->width ); 
			else
				$l['sb1'] = $this->get_width( $this->sidebar1->width, $this->sidebar_wrap->width );


			$this->layout_mode = $save_mode; 
			$this->build_layout($save_mode);

			return $l;
		}
		

		/**
		*
		* @TODO document
		*
		*/
		function get_width($target, $context){
			return sprintf( 'width:%s%%;', ($context != 0 ) ? ( $target / $context ) * 100 : 0 );
		}


}

//********* END OF LAYOUT CLASS *********//


/**
 * PageLines Layout Object 
 * @global object $pagelines_template
 * @since 1.0.0
 */
function build_pagelines_layout(){	
	
	global $pagelines_layout;
	global $post;
	
	$post_id = (isset($post->ID)) ? $post->ID : null;
	
	$oset = array( 'post_id' => $post_id );

	$page_layout = (ploption( '_pagelines_layout_mode', $oset)) ? ploption( '_pagelines_layout_mode', $oset) : null;
	
	$pagelines_layout = new PageLinesLayout( $page_layout );
}


/**
 * 
 *  Sets Content Width for Large images when adding media
 *
 *  @package PageLines Framework
 *  @subpackage Functions Library
 *  @since 1.2.3
 *
 */
function pagelines_current_page_content_width() {

	global $pagelines_layout;
	global $content_width;
	global $post;

	$mode = pagelines_layout_mode();
	
	$c_width = $pagelines_layout->layout_map[$mode]['maincolumn_width'];
	
	if ( !isset( $content_width ) ) $content_width = $c_width - 45;

}

/**
 * PageLines Layout Mode
 *
 * Returns Current Layout Mode
 *
 * @package     PageLines Framework
 * @subpackage  Functions Library
 *
 * @since       1.0.0
 *
 * @return      mixed
 */
function pagelines_layout_mode() {

	global $pagelines_layout;
	return $pagelines_layout->layout_mode;
	 
}

/**
*
* @TODO do
*
*/
function get_layout_mode(){
	$load_layout = new PageLinesLayout();
	$layoutmap = $load_layout->get_layout_map();
	$layout_mode = $layoutmap['layout_mode'];
	return $layout_mode;
}

	
/*
	The main content layouts available in this theme
*/
function get_the_layouts(){
	return array(
		'fullwidth', 
		'one-sidebar-right', 
		'one-sidebar-left', 
		'two-sidebar-right', 
		'two-sidebar-left', 
		'two-sidebar-center'
	);
}

/**
*
* @TODO do
*
*/
function reset_layout_to_default(){

	$dlayout = new PageLinesLayout;
	
	$layout_map = $dlayout->default_layout_setup();

	pagelines_update_option('layout', $layout_map);
}
