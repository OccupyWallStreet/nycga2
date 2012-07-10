<?php
/*
	Section: BrandNav
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Branding and Nav Inline
	Class Name: PageLinesBrandNav
	Depends: PageLinesNav
	Workswith: header
*/

/**
 * BrandNav Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PageLinesBrandNav extends PageLinesNav {

	/**
	* PHP that always loads no matter if section is added or not.
	*/	
	function section_persistent(){
			register_nav_menus( array( 'brandnav' => __( 'BrandNav Section Navigation', 'pagelines' ) ) );
	}
	
	/**
	*
	* @TODO document
	*
	*/
	function section_styles(){
		if(ploption('enable_drop_down')){
			
			wp_register_style('superfish', self::$nav_url . '/style.superfish.css', array(), CORE_VERSION, 'screen');
		 	wp_enqueue_style( 'superfish' );
		
			wp_enqueue_script( 'superfish', self::$nav_url . '/script.superfish.js', array('jquery'), '1.4.8', true );
			wp_enqueue_script( 'bgiframe', self::$nav_url . '/script.bgiframe.js', array('jquery'), '2.1', true );	
		}
	}

	/**
	* Section template.
	*/
 	function section_template() { 
	
			pagelines_main_logo( $this->id ); 
			
			
		if(has_action('brandnav_after_brand')){
			pagelines_register_hook( 'brandnav_after_brand', 'brandnav' ); // Hook
		
		} else {
		
		?>
		
			<div class="brandnav-nav main_nav fix">		
<?php 	
				wp_nav_menu( array('menu_class'  => 'main-nav tabbed-list'.pagelines_nav_classes(), 'container' => null, 'container_class' => '', 'depth' => 3, 'theme_location'=>'brandnav', 'fallback_cb'=>'pagelines_nav_fallback') );

				
				pagelines_register_hook( 'brandnav_after_nav', 'brandnav' ); // Hook
?>
			</div>
		<div class="clear"></div>
<?php 	}
	}


		/**
		*
		* @TODO document
		*
		*/
		function section_head(){

			$arrows = (ploption('drop_down_arrows') == 'on') ? 1 : 0;
			$shadows = (ploption('drop_down_shadow') == 'on') ? 1 : 0;

			if(ploption('enable_drop_down')): ?>

	<script type="text/javascript"> /* <![CDATA[ */ jQuery(document).ready(function() {  jQuery('div.brandnav-nav ul.sf-menu').superfish({ delay: 100, speed: 'fast', autoArrows:  <?php echo $arrows;?>, dropShadows: <?php echo $shadows;?> });  }); /* ]]> */ </script>			

	<?php 
			endif;
	}
}
