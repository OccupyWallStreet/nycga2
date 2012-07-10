<?php


class PageLinesSupportPanel {

	/**
     * PHP5 Constructor
     */
	function __contruct(){ }
	
	function draw(){

		$dash = new PageLinesDashboard;
		
		
		
		$view = $this->get_welcome_billboard();

		// PageLines Plus
		$args = array(
			'title'			=> __( 'PageLines Support', 'pagelines' ), 
			'data'			=> $this->support_array(), 
			'icon'			=> PL_ADMIN_ICONS . '/balloon-white.png', 
			'excerpt-trim'	=> false,
			'format'		=> 'button-links'
		);
		
		$view .= $dash->wrap_dashboard_pane('tips', $args);
		
		// PageLines Plus
		$args = array(
			'title'			=> __( 'Other PageLines Resources', 'pagelines' ), 
			'data'			=> $this->resources_array(), 
			'icon'			=> PL_ADMIN_ICONS . '/toolbox.png', 
			'excerpt-trim'	=> false,
			'format'		=> 'button-links'
		);
		
		$view .= $dash->wrap_dashboard_pane('tips', $args);
		
		return $view;
	}
	
	function get_welcome_billboard(){
		
		ob_start();
		?>
		
		<div class="admin_billboard">
			<div class="admin_billboard_pad fix">
					<h3 class="admin_header_main">
					 PageLines Resources <span class="spamp">&amp;</span> Support
					</h3>
					<div class='admin_billboard_text'>
					 Tons of options for fast and professional support.
					</div>
			</div>
		</div>
		<?php 
		
		$bill = ob_get_clean();
		
		
		return apply_filters('pagelines_welcome_billboard', $bill);
	}
	
	function support_array(){
		
		$data = array(
			'story3'	=> array(
				'title'		=> __( 'PageLines Live - Technical Community Chat (Plus Only)', 'pagelines' ),
				'text'		=> __( 'Talk to others in the PageLines community and get instant help from Live Moderators.', 'pagelines' ), 
				'img'		=> PL_ADMIN_ICONS . '/dash-live.png', 
				'link'		=> 'http://www.pagelines.com/live/', 
			),
			'story4'	=> array(
				'title'		=> __( 'PageLines Documentation', 'pagelines' ), 
				'text'		=> __( 'Docs for everything you want to do with PageLines.', 'pagelines' ), 
				'img'		=> PL_ADMIN_ICONS . '/dash-docs.png', 
				'link'		=> 'http://www.pagelines.com/wiki/', 
			),
			'story1'	=> array(
				'title'		=> __( 'PageLines Forum', 'pagelines' ), 
				'text'		=> __( 'Find answers to common technical issues. Post questions and get responses from PageLines experts.', 'pagelines' ), 
				'img'		=> PL_ADMIN_ICONS . '/dash-forum.png', 
				'link'		=> 'http://www.pagelines.com/forum/', 
			),
			'vids'	=> array(
				'title'		=> __( 'PageLines Videos and Training', 'pagelines' ), 
				'text'		=> __( 'Check out the latest videos on how to use PageLines fast and effectively via YouTube.', 'pagelines' ), 
				'img'		=> PL_ADMIN_ICONS . '/dash-video.png', 
				'link'		=> 'http://www.youtube.com/user/pagelines/videos?view=1', 
			)
		);
		
		return $data;
		
	}

	function resources_array(){
		
		$data = array(
			'aff'	=> array(
				'title'		=> __( 'Affiliate Program', 'pagelines' ), 
				'text'		=> __( 'Earn up to $130 (33%) on each referral to PageLines! Get started in 5 minutes.', 'pagelines' ), 
				'img'		=> PL_ADMIN_ICONS . '/dash-aff.png', 
				'link'		=> 'http://www.pagelines.com/partners', 
			),
			'pros'	=> array(
				'title'		=> __( 'PageLines Pros', 'pagelines' ), 
				'text'		=> __( 'The Pros are PageLines experts who you can pay to help customize your website.', 'pagelines' ), 
				'img'		=> PL_ADMIN_ICONS . '/dash-pros.png', 
				'link'		=> 'http://www.pagelines.com/pros', 
			),
			'dev'	=> array(
				'title'		=> __( 'Developer Center', 'pagelines' ), 
				'text'		=> __( 'Resources for professionals and developers using PageLines. Access to Beta releases and more.', 'pagelines' ), 
				'img'		=> PL_ADMIN_ICONS . '/dash-dev.png', 
				'link'		=> 'http://developer.pagelines.com', 
			),
			'trans'	=> array(
				'title'		=> __( 'Translation Center', 'pagelines' ), 
				'text'		=> __( 'Get PageLines in your language or collaborate on a translation.', 'pagelines' ), 
				'img'		=> PL_ADMIN_ICONS . '/dash-trans.png', 
				'link'		=> 'http://www.pagelines.com/translate/', 
			),
			
		);
		
		return $data;
		
	}
	
	
	

}
