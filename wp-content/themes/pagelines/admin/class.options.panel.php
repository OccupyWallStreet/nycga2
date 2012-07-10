<?php
/**
 * 
 *  PageLines Panel UI
 *
 *  @package PageLines Framework
 *  @since 2.0
 *
 */
class PLPanel {


	/**
	*
	* @TODO document
	*
	*/
	function __construct( $settings = array() ) { }
	

	/**
	*
	* @TODO document
	*
	*/
	function the_panel( $s = array() ){ 
		global $post_ID;  
		global $pagelines_template;
		
		$defaults = array(
				'handle'	=> 'plpanel',
				'title' 	=> '',
				'tag' 		=> false,
				'type'		=> null,
				'stext' 	=> __('Save', 'pagelines'),
				'tabs' 		=> array(), 
				'hidetabs'	=> false, 
				'post_ID'	=> null, 
				'post_type'	=> null,
				'user'		=> null
			);

		$this->s = wp_parse_args($s, $defaults); // settings for post type
		
		$hide_tabs = ( count($this->s['tabs']) == 1 ) ? true : $this->s['hidetabs'];
			
		
		if(!$hide_tabs)
			$this->tabs_setup( $this->s['handle'] ); 
		
		
		?>
		<div id="plpanel" class="pl_mp">
			<?php $this->head( $this->s['title'], $this->s['tag'], $this->s['stext'] ); ?>
			<div id="<?php echo $this->s['handle'];?>" class="pagelines_metapanel fix">
				<div class="pagelines_metapanel_pad fix">
					<?php 
					
						if(!$hide_tabs)
							$this->tabn( $this->s['tabs'] );

						$this->load_tabs($this->s['type'], $this->s['tabs'], $hide_tabs, $this->s['post_ID'], $this->s['user']);
						
					?>
				</div>
			</div>
			<?php $this->panel_foot( $this->s['stext'], $this->s['post_type']); ?>
		</div>
	
<?php 
	
	}
	
	

	/**
	*
	* @TODO document
	*
	*/
	function tabs_setup( $handle = 'plpanel' ){
	
		if(!$this->s['hidetabs']):
		?>
		
		<script type="text/javascript"> 
			jQuery(document).ready(function() { 
				<?php printf('var %1$s = jQuery("#%1$s").tabs({cookie: { name: "%1$s-tabs" }, fx: { opacity: "toggle", duration: 150 }});', $handle); ?> 
			});
		</script>
	
	<?php endif;

	}
	

	/**
	*
	* @TODO document
	*
	*/
	function head( $title, $tag, $stext ){ 
		
		global $post_ID;
		$pl_link_url = ($this->s['type'] == 'meta' && $post_ID) ? esc_url( get_permalink($post_ID) ) : home_url();
		$pl_link_title = ($this->s['type'] == 'meta' && $post_ID) ? __('View Page &rarr;', 'pagelines') : __('View Site &rarr;', 'pagelines');
		?>
		
		<div class="ohead  mp_bar mp_head">
			<div class="mp_bar_pad fix ">
				<div id="the_pl_button" class="sl-pagelines sl-black superlink-wrap">
					<a class="superlink" href="<?php echo $pl_link_url; ?>/" target="_blank" title="<?php echo $pl_link_title;?>">
						<span class="superlink-pagelines">&nbsp;<span class="slpl">View Site</span></span>
					</a>
				</div>
				<div class="mp_title">
					<span class="mp_title_text"><?php echo $title; ?></span>
					 <?php if($tag):?><span class='btag'><?php echo $tag;?></span><?php endif; ?>
				</div>
			
				<div class="superlink-wrap osave-wrap">
					<input id="update" class="superlink osave" type="submit" value="<?php echo $stext; ?>"  name="update" />
				</div>
			</div>
		</div>
		
	<?php
		
	}


	/**
	*
	* @TODO document
	*
	*/
	function tabn( $tabs ){ ?> 
		
		<ul id="tabsnav" class="mp_tabs">
		
			<?php foreach( $tabs as $tab => $t):?>
				<li>
					<a class="<?php echo $tab;?>  metapanel-tabn <?php if(!$t->active) echo 'inactive-tab';?>" href="#<?php echo $tab;?>">
						<span class="metatab_icon" style="background: url(<?php echo $t->icon; ?>) no-repeat 0 0;display: block;">
							<?php 
								echo $t->name;
								
								if(!$t->active) 
									printf('<span class="tab_inactive">inactive</span>');
							
							 ?>
						</span>
					</a>
				</li>
			<?php endforeach;?>
		</ul>
	
	<?php }
	

	/**
	*
	* @TODO document
	*
	*/
	function load_tabs( $type, $tabs, $hide_tabs = false, $post_ID = null, $user = null){ ?>
		<div class="mp_panel fix <?php if( $hide_tabs ) echo 'hide_tabs';?>">
			<div class="mp_panel_pad fix">
				<div class="pagelines_metapanel_options">
					<div class="pagelines_metapanel_options_pad">
						<?php foreach( $tabs as $tab => $t ):?>
							<div id="<?php echo $tab;?>" class="pagelines_metatab">
								<div class="metatab_title" style="background: url(<?php echo $t->icon; ?>) no-repeat 10px 13px;" >
									<?php 
									
										echo $t->name;
									
										if(isset($post_ID) && !$t->active) 
											echo OptEngine::superlink(__( 'Inactive On Template', 'pagelines' ), 'black', 'right', admin_url('admin.php?page=pagelines_templates'));
											
									 	?>
								</div>
								<?php  $this->load_engine( $type, $t->options, $post_ID, $user); ?>
							</div>
						<?php endforeach;?>
					</div>
				</div>
			</div>
		</div>
	<?php }
	

	/**
	*
	* @TODO document
	*
	*/
	function load_engine( $type, $opts, $post_ID = null, $user = null ){
		
		$option_engine = new OptEngine( $type );
		
		$flag = ($type == 'meta') ? $post_ID : $user;
		
		foreach($opts as $oid => $o)
			$option_engine->option_engine($oid, $o, $flag);
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function panel_foot( $save_text, $post_type){
		?> 
		
		<div class="ohead mp_bar mp_footer ">
			<div class="mp_bar_pad fix ">
				<input type="hidden" name="_posttype" value="<?php echo $post_type; ?>" />
				<div class="superlink-wrap osave-wrap">
					<input id="update" class="superlink osave" type="submit" value="<?php echo $save_text; ?>"  name="update" />
				</div>
			</div>
		</div>
		
		<?php
	}
}
