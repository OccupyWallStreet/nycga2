<?php
/*
	Section: Footer Columns Sidebar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A 5 column widgetized sidebar in the footer
	Class Name: PageLinesFootCols
	Workswith: morefoot, footer
	Persistant: true
*/

/**
 * Footer Columns Sidebar Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PageLinesFootCols extends PageLinesSection {

	public $markup_start;
	public $markup_end;
	
	/**
	* PHP that always loads no matter if section is added or not.
	*/	
	function section_persistent(){
		
		$per_row = (ploption('footer_num_columns')) ? ploption('footer_num_columns') : 5;
		
		$this->markup_start = sprintf( '<div class="pp%s footcol"><div class="footcol-pad">', $per_row );
		$this->markup_end 	= '</div></div>';
		
	
		pagelines_register_sidebar(array(
			'name'=>$this->name,
			'description'	=> __('Use this sidebar if you want to use widgets in your footer columns instead of the default.', 'pagelines'),
		    'before_widget' => $this->markup_start,
		    'after_widget' 	=> $this->markup_end,
		    'before_title' 	=> '<h3 class="widget-title">',
		    'after_title' 	=> '</h3>'
		) );
		
		register_nav_menus( array(
			'footer_nav' => __( 'Page Navigation in Footer Columns', 'pagelines' )
		) );
	
		
	}

	/**
	* Section template.
	*/
	function section_template() { 
		
		$default = array();
		
		if(ploption('footer_logo') && VPRO)
			$default[] = sprintf( '<a href="%s" class="home" title="%s"><img src="%s" alt="%s"/></a>',  home_url(),  __('Home', 'pagelines'), ploption('footer_logo'),  get_bloginfo('name') );
		else 
			$default[] = sprintf( '<h3 class="site-title"><a class="home" href="%s" title="%s">%s</a></h3>', home_url(), __('Home', 'pagelines'), get_bloginfo('name') );
			
		$default[] = sprintf( '<h3 class="widget-title">%s</h3>%s',
				__('Pages','pagelines'), 
				wp_nav_menu( array('menu_class' => 'footer-links list-links', 'theme_location'=>'footer_nav', 'depth' => 1, 'echo' => false) )
			);
			
		$default[] = sprintf( '<h3 class="widget-title">%s</h3><ul class="latest_posts">%s</ul>',
				__('The Latest','pagelines'), 
				$this->recent_post()
			);
			
		$default[] = sprintf( '<h3 class="widget-title">%s</h3><div class="findent footer-more">%s</div>',
				__('More','pagelines'), 
				ploption('footer_more')
			);
			
		$default[] = sprintf( '<div class="findent terms">%s</div>',
				ploption('footer_terms')
			);
		
		
		ob_start(); // dynamic sidebar always outputs
	
		if (!dynamic_sidebar($this->name) ) {
		
			foreach($default as $key => $c){
				printf($this->markup_start, '', ''); 
				echo $c;
				echo $this->markup_end;
			}
			
		}		
		
		printf('<div class="fcolumns ppfull pprow"><div class="fcolumns-pad fix">%s</div></div><div class="clear"></div>', ob_get_clean());
		
	}


	/**
	*
	* @TODO document
	*
	*/
	function recent_post(){
		$out = '';
		foreach( get_posts('numberposts=1&offset=0') as $key => $p ){
			$out .= sprintf(
				'<li class="list-item fix"><div class="list_item_text"><h5><a class="list_text_link" href="%s"><span class="list-title">%s</span></a></h5><div class="list-excerpt">%s</div></div></li>', 
				get_permalink( $p->ID ), 
				$p->post_title, 
				custom_trim_excerpt($p->post_content, 12)
			);
		}
		
		return $out;
	}

}
