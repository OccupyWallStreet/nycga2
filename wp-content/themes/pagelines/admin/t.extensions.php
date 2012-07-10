<?php


class PageLinesCoreExtensions {

	/**
     * PHP5 Constructor
     */
	function __contruct(){ }
	
	function draw(){

		$dash = new PageLinesDashboard;
		
		// PageLines Plus
		$args = array(
			'title'			=> __( 'Available Plus Extensions', 'pagelines' ),
			'data'			=> PageLines_RSS::get_dash_rss( array( 'feed' => 'http://api.pagelines.com/rss/plus.php', 'items' => 15 ) ), 
			'icon'			=> PL_ADMIN_ICONS . '/plusbtn.png', 
			'excerpt-trim'	=> false, 
			'format'		=> 'plus-extensions'
		); 
		
		$view = $this->get_welcome_billboard();

		$view .= $dash->wrap_dashboard_pane('tips', $args);
		
		return $view;
	}
	
	/**
     * Get Welcome Billboard
     *
     * Used to produce the content at the top of the theme Welcome page.
     *
     * @uses        CHILD_URL (constant)
     * @internal    uses 'pagelines_welcome_billboard' filter
     *
     * @return      mixed|void
     */
	function get_welcome_billboard(){
		
		ob_start();
		?>
		
		<div class="admin_billboard">
			<div class="admin_billboard_pad fix">
					<h3 class="admin_header_main"><?php _e( 'Free Extensions With Plus', 'pagelines' ); ?></h3>
					<div class='admin_billboard_text'>
						<?php _e( 'With PageLines Plus, you get all PageLines-built extensions and more every month!', 'pagelines' ); ?>
					</div>
			</div>
		</div>
		<?php 
		
		$bill = ob_get_clean();
		
		
		return apply_filters('pagelines_welcome_billboard', $bill);
	}
}
