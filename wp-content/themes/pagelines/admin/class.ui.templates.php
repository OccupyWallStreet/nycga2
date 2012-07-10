<?php
/**
 * 
 *
 *  Template Builder 
 *
 *
 *  @package PageLines Framework
 *  @subpackage OptionsUI
 *  @since 2.0.b3
 *
 */

class PageLinesTemplateBuilder {


	/**
	 * Construct
	 */
	function __construct( $oid, $o, $setting = PAGELINES_SETTINGS ) {
		
		global $pagelines_template;
		global $pl_section_factory;
		
		$oset = array( 'setting' => $setting );
		
		$this->sc_settings = ploption('section-control', $oset);
		$this->sc_global = ploption('section-control', array('setting' => PAGELINES_TEMPLATES));
		$this->sc_namespace = sprintf('%s[section-control]', $setting);
		
		$this->template_map = get_option( PAGELINES_TEMPLATE_MAP );
		
		
		$this->factory = $pl_section_factory->sections;
		
		$this->template = $pagelines_template;
		
	}


	/**
	*
	* @TODO document
	*
	*/
	function sc_name( $ta, $sid, $field, $sub = null){
		
		
		if(isset($sub))
			return sprintf('%s[%s][%s][%s][%s]', $this->sc_namespace, $ta, $sid, $field, $sub);
		else 
			return sprintf('%s[%s][%s][%s]', $this->sc_namespace, $ta, $sid, $field);
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function sc_value( $ta, $sid, $field, $sub = null){
 
		if(isset($sub))
			return isset($this->sc_settings[$ta][$sid][$field][$sub]) ? $this->sc_settings[$ta][$sid][$field][$sub] : null;
		else 
			return isset($this->sc_settings[$ta][$sid][$field]) ? $this->sc_settings[$ta][$sid][$field] : null;
			
	}

	
	
	/**
	 * 
	 *
	 *  Template Builder (Sections Drag & Drop)
	 *
	 *
	 *  @package PageLines Framework
	 *  @subpackage Options
	 *  @since 4.0
	 *
	 */
	function draw_template_builder(){
		
			$this->do_confirms_and_hidden_fields();
		
			echo '<div class="tbuilder">';
			
				$this->draw_template_select(); 
			
				$this->do_template_builder();
				
			echo '</div>';
	}


	/**
	*
	* @TODO document
	*
	*/
	function do_confirms_and_hidden_fields(){ 
		$dtoggle = (get_option('pl_section_desc_toggle')) ? get_option('pl_section_desc_toggle') : 'show'; 
	?>
		<input type="hidden" value="<?php echo $dtoggle;?>" id="describe_toggle" class="describe_toggle" name="describe_toggle"  />	
		
<?php }

	/**
	 * 
	 *
	 *  Do Template Area Selector
	 *
	 *
	 */
	function draw_template_select(){ 
		global $pagelines_template;
		global $unavailable_section_areas;
		
		?>	
	<div class="template-selector fix">	
		<div class="template-selector-pad fix">
			<h4 class="over"><?php _e( '1. Select Template Area', 'pagelines' ); ?></h4>
			<div class="tgraph tgraph-templates">
				<div class="tgraph-pad">
					<div class="tgraph-controls">
						<div class="tgraph-controls-pad fix">
							<div id="ta-header" class="load-build tg-format tg-header"><div class="tg-pad">Header</div></div>
							<div id="ta-templates" class="tg-format tg-templates"><div class="tg-pad">Page Templates</div></div>
							<div id="ta-morefoot" class="load-build tg-format tg-morefoot <?php if(!VPRO) echo 'pro-area'; ?>">
								<div class="tg-pad">Morefoot <?php if(!VPRO) echo '<span class="protag">(Pro)</span>'; ?></div>
							</div>
							<div id="ta-footer" class="load-build tg-format tg-footer"><div class="tg-pad">Footer</div></div>
						</div>
					</div>
				</div>
			</div>
			<div class="tgraph tgraph-content">
				<div class="tgraph-pad">
					<div class="tgraph-controls">
						<div class="tgraph-controls-pad fix">
							<div class="tg-content-area">
							
								<div class="tg-rm">
									<div clas="tgc">
										<div id="ta-content" class="tg-format tg-content-templates">
											<div class="tg-pad">Content Area</div>
										</div>
									</div>
								</div>
							</div>
							<div class="tg-wrap">
								<div class="tg-sidebarwrap">
									<div class="tgc">
										<div id="ta-sidebar_wrap" class="load-build tg-format <?php if(!VPRO) echo 'pro-area'; ?>">
											<div class="tg-pad">Wrap<?php if(!VPRO) echo '<span class="protag"> (Pro)</span>'; ?></div>
										</div>
									</div>
								</div>
								<div class="tg-sidebar1">
									<div class="tg-mmr">
										<div class="tgc">
											<div id="ta-sidebar1" class="load-build tg-format">
												<div class="tg-pad">SB1</div>
											</div>
										</div>
									</div>
								</div>
								<div class="tg-sidebar2">
									<div class="tg-mml">
										<div class="tgc">
											<div id="ta-sidebar2" class="load-build tg-format <?php if(!VPRO) echo 'pro-area'; ?>">
												<div class="tg-pad">SB2<?php if(!VPRO) echo '<span class="protag"><br />(Pro)</span>'; ?></div>
											</div>
										</div>
									</div>
								</div>
					
							</div>
						</div>
					</div>
				</div>
			</div>
			
		</div>
	</div>	
	<div class="clear"></div>
	<?php
	
		$this->_sub_selector('templates', 'sel-templates-sub', __('For Which Type of Page?', 'pagelines'));
		
		$this->_sub_selector('main', 'sel-content-sub', __('Which Content Area Type?', 'pagelines'));
		
	}


	/**
	*
	* @TODO document
	*
	*/
	function _sub_selector($type = 'templates', $class, $title = '', $subtitle = ''){
		global $pagelines_template;
		
		
		// The Buttons
		$buttons = '';
		foreach($pagelines_template->map[$type]['templates'] as $template => $t){
			
			if( (!isset($t['version']) || ($t['version'] == 'pro' && VPRO)) && isset($t['name'])){
		
				if( isset($t['page_type']) && $t['page_type'] == 'page')
					$name = sprintf('<span class="sss-tag">Template | </span>%s', $t['name']);
				else
					$name = $t['name'];
			
				$buttons .= sprintf('<div id="%s" class="sss-button"><div class="sss-button-pad">%s</div></div>', join( '-', array($type, $template) ), $name);
				
			}
				
		}
		
		// Output
		printf('<div class="sub-template-selector fix %s"><div class="sub-templates fix"><h4 class="over">%s</h4>%s</div></div>', $class, $title, $buttons);
		
	}


	/**
	*
	* @TODO document
	*
	*/
	function do_template_builder(){
		
		global $pagelines_template;
		global $unavailable_section_areas;
		?>
		<div class="the_template_builder">
			<div class="the_template_builder_pad">
<?php 
			foreach($pagelines_template->map as $hook => $h){
				
				if( isset($h['templates']) ){
					
					foreach($h['templates'] as $tid => $t )
						$this->section_banks( $tid, $t, $hook, $h );	
						
				} else 
					$this->section_banks( $hook, $h );
			
			}?>
			</div>
		</div>
	<?php }
	
	/**
	 * 
	 *
	 *  Get Sortable Sections (Sections Drag & Drop)
	 *
	 *
	 *  @package PageLines Framework
	 *  @subpackage Options
	 *  @since 4.0
	 *
	 */
	function section_banks($template, $tfield, $hook = null, $hook_info = array()){
		
			$template_slug = ( isset($hook) ) ? join('-', array( $hook, $template )) : $template;
			$template_area = ( isset($hook) ) ? $hook : $template;
			
			$addl = ($template_area == 'templates') ? 'Page Template Area' : ( $template_area == 'main' ? 'Content Area' : 'Global Scope');
			$addl = ($addl != '') ? sprintf('<span class="btag grey">%s</span>', $addl) : '';
			if( !isset( $tfield['name'] ) )
				$tfield['name'] = '';
?><div id="template_data" class="<?php echo $template_slug; ?> layout-type-<?php echo $template_area;?>" title="">
		<span class="template-slug" id="<?php echo $template_slug; ?>"></span>
		<div class="ttitle fix" id="highlightme">
			<div class="ttitle-text">
				<span>Editing &rarr;</span> <?php echo $tfield['name'].' '. $addl;?> 
			</div>
			<div class="confirm_save"><div class="confirm_save_pad">&nbsp;</div></div>
		</div>
		<div id="section_map" class="template-edit-panel ">
			<h4 class='over' >2. Arrange Sections In Area With Drag &amp; Drop</h4>
			<div class="sbank template_layout">
				
				<div class="sbank-area">
					<div class="sbank-pad">
						<div class="bank_title">
							<span class="btitle">Active Sections</span>
						</div>
						<div class="sbank-wrap">
							<div class="sbank-wrap-pad">
							<ul id="sortable_template" class="connectedSortable ">
							 	<?php  $this->active_bank( $template, $tfield, $template_area, $template_slug ); ?>
							</ul>
							</div>
						</div>
					</div>
				</div>		
				
				
			</div>
			<div class="sbank available_sections">
				<div class="sbank-area">
					<div class="sbank-pad fix">
						<div class="bank_title">Available / Disabled Sections</div>
						<?php $this->passive_bank( $template, $tfield, $hook, $hook_info, $template_slug ); ?>
					</div>
				</div>
				<div class="clear"></div>
			</div>
			
		</div>
		
		<div class="clear"></div>
		<?php $this->section_setup_controls(); ?>
	</div>
<?php  }
	

	/**
	*
	* @TODO document
	*
	*/
	function active_bank( $tid, $t, $ta, $ts ){
		 
		$this->avail = $this->factory; 
		if( isset($t['sections']) && is_array($t['sections'])){
		  
			foreach($t['sections'] as $sid){

				$pieces = explode("ID", $sid);		
				$section = (string) $pieces[0];
				$clone_id = (isset($pieces[1])) ? $pieces[1] : 1;

			 	if(isset( $this->factory[$section] )){

					$s = $this->factory[$section];

					$section_args = array(
						'section'	=> $section,
						'sid'		=> $sid,
						'template'	=> $tid,
						'id'		=> 'section_' . $sid, 
						'icon'		=> $s->settings['icon'], 
						'name'		=> $s->name, 
						'desc'		=> $s->settings['description'],
						'req'		=> $s->settings['required'],
						'controls'	=> true,
						'tslug'		=> $ts,
						'tarea'		=> $ta,
						'clone'		=> $clone_id, 
						'cloning'	=> $s->settings['cloning']
					
					);

					$this->draw_section( $section_args );

		
					if(isset($this->avail[$section]))
						unset($this->avail[$section]);
		
			 	} 
			}
		}
	} 
	

	/**
	*
	* @TODO document
	*
	*/
	function passive_bank( $template, $t, $hook, $h, $template_slug ){

		// Remove the sections that aren't compatible
		$draw = array();
		foreach( $this->avail as $sid => $s){
			
			/* Flip values and keys */
			$works_with = (is_array($s->settings['workswith'])) ? array_flip( $s->settings['workswith'] ) : array();
			$fails_with = (is_array($s->settings['failswith'])) ? array_flip( $s->settings['failswith'] ) : array();
			$markup_type = (!empty($h)) ? $h['markup'] : $t['markup'];
			if(!isset( $works_with[ $template ] ) 
				&& !isset( $works_with[ $hook ]) 
				&& !isset( $works_with[ $hook.'-'.$template ] ) 
				&& !isset($works_with[$markup_type])
				|| ( 
					isset( $fails_with[ $template ] )
					|| isset($fails_with[ $hook ] )
					|| ( isset( $fails_with['pagelines_special_pages()'] ) && is_pagelines_special ( array( 'type' => $template ) ) )
				)
			)
				continue;
				
			$draw[ $sid ] = $s;			
		}
		
		// Draw in Column format
		
		$col = 1;
		$numcol = 2;
		$count = 1;
		$total = count( $draw );
		$coltotal = ( $total % 2 ) ? $total+1 : $total;
		
		if(!empty($draw)){
			foreach($draw as $sid => $s){
		
				$start_list = ( $count == 1 || ($coltotal / ($count - 1) ) == $numcol ) ? true : false;
				$end_list = ( $count == $total || ($coltotal / ($count) ) == $numcol ) ? true : false;
			
				if($start_list)
					printf('<ul id="sortable_sections" class="connectedSortable sortcolumn colnum%s">', $col);
			
				$section_args = array(
					'id'		=> 'section_' . $sid,
					'template'	=> $template,
					'sid'		=> $sid,
					'section'	=> $sid, 
					'icon'		=> $s->settings['icon'], 
					'name'		=> $s->name, 
					'desc'		=> $s->settings['description'], 
					'tslug'		=> $template_slug,
					'tarea'		=> $hook,
					'cloning'	=> $s->settings['cloning']
				);
				$this->draw_section( $section_args );
			
				if($end_list){
					printf('</ul>');
					$col++;
				}
				$count++;
			}
		} else {
			printf('<ul id="sortable_sections" class="connectedSortable nosections sortcolumn"></ul>');
		}
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function draw_section( $args ){ 
		
		$defaults = array(
			'section'		=> '',
			'sid'			=> '',
			'template'		=> '',
			'id' 			=> '',
			'icon'		 	=> '',
			'name' 			=> '',
			'desc' 			=> '',
			'controls'		=> false,
			'tslug' 		=> '',				
			'tarea' 		=> '',
			'req'			=> false, 
			'clone'			=> '1', 
			'cloning'		=> false
		);

		$a = wp_parse_args( $args, $defaults );
				
		$check_value = (bool) $this->sc_value( $a['tslug'], $a['sid'], 'hide' );			 
				
	printf('<li id="%s"><div class="section-bar %s %s">', $a['id'], ($a['req']) ? 'required-section' : '', ($check_value) ? 'hidden-section' : '');
		printf('<div class="section-bar-pad fix" style="background: url(%s) no-repeat 10px 9px;">', $a['icon']);
		
			printf('<div class="section-controls-toggle" onClick="toggleControls(this);" %s><div class="section-controls-toggle-pad">Options</div></div>', (!$a['controls']) ? 'style="display:none;"' : ''); 
			printf('<h4 class="section-bar-title">%s <span class="the_clone_id">%s</span></h4>', $a['name'], ($a['clone'] != 1) ? '#'.$a['clone'] : '' );
			printf('<span class="s-description" %s >%s</span>', $this->help_control(), $a['desc']);
			
		echo '</div></div>';	
		
		$this->inline_section_control($a); 
	
	echo '</li>';
 	
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function inline_section_control($a){

		
		// Options 
		$check_name = $this->sc_name( $a['tslug'], $a['sid'], 'hide' );
		$check_value = (bool) $this->sc_value( $a['tslug'], $a['sid'], 'hide' ); 


		printf('<div class="section-controls" %s><div class="section-controls-pad">', (!$a['controls']) ? 'style="display:none;"' : '');
					
			if($a['cloning']){
				
				$clone_js = (VPRO) ? sprintf('onClick="cloneSection(\'%s\');"', $a['id']) : '';
				
				$clone_text = (VPRO) ? 	__( 'Clone', 'pagelines' ) : __( 'Clone <span class="sss-tag">(PRO)</span>', 'pagelines' );
				
				$clone_class = (!VPRO) ?  'disabled_clone_button' : '';
				
				$clone_btn = sprintf('<div class="clone_button %s" %s><div class="clone_button_pad">%s</div></div>', $clone_class, $clone_js, $clone_text );
				
				$remove_clone = sprintf('<div class="clone_button clone_remove" style="%s" onClick="deleteSection(this, \'%s\');"><div class="clone_button_pad">Remove</div></div>', ($a['clone'] == 1) ? 'display: none;' : '', $a['id']);
				
				printf('<div class="sc_buttons">%s %s</div>', $clone_btn, $remove_clone);
			}
			
			if($this->show_sc( $a['template'] )){
				
				$clone = ($a['clone'] != 1) ? sprintf('<span class="the_clone_id">%s</span>', '#' . $a['clone']) : '';
				printf('<strong>%s %s %s</strong>', $a['name'], $clone, __( 'Settings', 'pagelines' ) );
				
				echo '<div class="section-options">';
			
					
					$checkbox = sprintf('<input class="section_control_check sc_save_check" type="checkbox" id="%1$s" name="%1$s" %2$s/>', $check_name, checked( $check_value, true, false));
					$label = sprintf('<label for="%s" class="%s">%s</label>', $check_name, '', __( 'Hide This By Default', 'pagelines' ) );
					
					printf('<div class="section-options-row">%s %s</div>', $checkbox, $label);
					
					
				echo '</div>';
				
			} else
			 	echo __('No settings in this template area.', 'pagelines');
					
					
		echo '<div class="clear"></div></div></div>';
				
	}
	
	
	/**
	 * Show section control?
	 * On some template areas, e.g. posts, single, 404, they have their own interface.. so none is needed
	 */
	function show_sc( $t ){
			
		return ( $t == 'posts' || $t == 'single' || $t == '404' ) ? false : true;
	}

	

	/**
	*
	* @TODO document
	*
	*/
	function section_setup_controls(){
		
		$onclick = "PageLinesSlideToggle('.s-description', '.describe_toggle', '.setup_control_text','Hide Section Descriptions', 'Show Section Descriptions', 'pl_section_desc_toggle');";
		
		printf('<div class="section_setup_controls fix"><span class="setup_control" onClick="%s"><span class="setup_control_text">%s Section Descriptions</span></span></div>', $onclick, ( $this->help() ) ? 'Hide' : 'Show' );
					
	}
	
	/**
	 * 
	 *
	 *  Show Section Control Option in MetaPanel
	 *
	 *
	 *  @package PageLines Framework
	 *  @subpackage Options
	 *  @since 4.0
	 *
	 */
	function section_control_interface($oid, $o){ 
		
		if(isset($_GET['page']) && $_GET['page'] == 'pagelines_meta')
			return;
		
		if( isset($o['special']) ){
			$this->template->adjust_template_type($o['special']);
			$is_special = true;
		} else 
			$is_special = false;
		
		$integration = ( $o['scontrol'] == 'integration' ) ? true : false;
		
		$template_slug = join( '-', array('templates', $this->template->template_type) );
		$main_slug = join( '-', array('main', $this->template->template_type) );

		global $metapanel_options;
		
		$editing = ($is_special) ? ucfirst($o['special']) : $metapanel_options->edit_slug;
		?>
		
		<div class="section_control_wrap">
			<div class="sc_gap fix">
				<div class="sc_gap_title"><?php echo ui_key( $editing );?> - <?php _e( 'Basic Template', 'pagelines' )?></div>
				<div class="sc_gap_pad">
					
					<div class="sc_area sc_header ntb">
						<div class="sc_area_pad fix">
							<div class="scta_head"><?php _e( 'Header', 'pagelines' )?></div>
							<?php $this->sc_inputs('header', $this->template->header, $o); ?>
						</div>
					</div>
					<div class="sc_area sc_templates">
						<div class="sc_area_pad fix">
							
							<div class="scta_head"><?php _e( 'Template', 'pagelines' )?></div>
							<?php 
							if($integration)
								printf('<div class="sc_inputs"><div class="emptyarea">%s %s</div></div>', ui_key( $editing ), __( 'Integration', 'pagelines' ) );
							else
								$this->sc_inputs($template_slug, $this->template->templates, $o ); 
							?>
						</div>
					</div>
					<div class="sc_area sc_morefoot">
						<div class="sc_area_pad fix">
							<div class="scta_head"><?php _e( 'Morefoot', 'pagelines' )?></div>
							<?php $this->sc_inputs('morefoot', $this->template->morefoot, $o ); ?>
						</div>
					</div>
					<div class="sc_area sc_footer nbb">
						<div class="sc_area_pad fix">
							<div class="scta_head"><?php _e( 'Footer', 'pagelines' )?></div>
							<?php $this->sc_inputs('footer', $this->template->footer, $o ); ?>
						</div>
					</div>
				</div>
			</div>
			<?php if(!$integration): ?>
				<div class="sc_gap fix">
				
					<div class="sc_gap_title"><?php echo ui_key( $editing );?> - <?php _e( 'Content Area', 'pagelines' )?></div>
					<div class="sc_gap_pad">
			
						<div class="sc_area sc_header ntb">
							<div class="sc_area_pad fix">
								<div class="scta_head"><?php _e( 'Content', 'pagelines' )?></div>
								<?php $this->sc_inputs($main_slug, $this->template->main, $o ); ?>
							</div>
						</div>
						<div class="sc_area sc_header">
							<div class="sc_area_pad fix">
								<div class="scta_head"><?php _e( 'Wrap', 'pagelines' )?></div>
								<?php $this->sc_inputs('sidebar_wrap', $this->template->sidebar_wrap, $o ); ?>
							</div>
						</div>
						<div class="sc_area sc_header">
							<div class="sc_area_pad fix">
								<div class="scta_head"><?php _e( 'Sidebar 1', 'pagelines' )?></div>
								<?php $this->sc_inputs('sidebar1', $this->template->sidebar1, $o ); ?>
							</div>
						</div>
						<div class="sc_area sc_header nbb">
							<div class="sc_area_pad fix">
								<div class="scta_head"><?php _e( 'Sidebar 2', 'pagelines' )?></div>
								<?php $this->sc_inputs('sidebar2', $this->template->sidebar2, $o ); ?>
							</div>
						</div>
					</div>
			
				</div>
			<?php endif; ?>
		</div>
		
	<?php }

	

	/**
	*
	* @TODO document
	*
	*/
	function sc_inputs( $template_slug, $sections, $o){
		global $post; 
		
		$is_special = (isset($o['special'])) ? true : false;
		
		// No sections in area
		if(empty($sections)){
			echo sprintf( '<div class="sc_inputs"><div class="emptyarea">%s</div></div>', __( 'Area is empty.', 'pagelines') );
			return;
		}
		
		echo '<div class="sc_inputs">';
		foreach($sections as $key => $sid){
			
			
			$pieces = explode('ID', $sid);		
			$section = (string) $pieces[0];
			$clone_id = (isset($pieces[1])) ? $pieces[1] : 1;
			
			// Get section information
			if( isset($this->factory[ $section ]) ){
				
				$section_data = $this->factory[ $section ];		
				
				$hidden_by_default = isset($this->sc_global[$template_slug][$sid]['hide']) ? $this->sc_global[$template_slug][$sid]['hide'] : null;

				$check_type = ( $hidden_by_default ) ? 'show' : 'hide';
				
				// Make the field 'key'
				$option_name = ($is_special) ? $this->sc_name( $template_slug, $sid, $o['special'], $check_type ) : meta_option_name( array($check_type, $template_slug, $sid) );
				$check_value = ($is_special) ? $this->sc_value( $template_slug, $sid, $o['special'], $check_type ) : get_pagelines_meta($option_name, $post->ID);
				
				// The name of the section
				$clone = ($clone_id != 1) ? ' #'.$clone_id : '';
				$check_label = ucfirst($check_type).' ' . $section_data->name.$clone;

				?>
				<div class="sc_wrap <?php echo 'type_'.$check_type;?>" >
					<label class="sc_button" for="<?php echo $option_name;?>">
						<span class="sc_button_pad fix" >
							<?php  
							
								printf('<span class="sc_check_wrap"><input class="sc_check" type="checkbox" id="%1$s" name="%1$s" %2$s /></span>', $option_name, checked((bool) $check_value, true, false) );
								printf('<span class="sc_label"><span class="sc_label_pad" style="background: url(%s) no-repeat 8px 5px;">%s</span></span>', $section_data->icon, $check_label); 
								
							?>
						</span>
					</label>
				</div><?php 
			}
		}
		echo '</div>';
		
	}

	

	/**
	*
	* @TODO document
	*
	*/
	function help_control(){
		if(!$this->help()) 
			return 'style="display:none"';
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function help(){
		if(  get_option('pl_section_desc_toggle') == 'hide' || get_option('pl_section_desc_toggle') == false || !get_option('pl_section_desc_toggle') )
			return false;
		else 
			return true; 
	}

}



/**
 * Get Template Setup - Drag & Drop Interface
 *
 * @since 2.0.0
 */
function templates_array(){

	$return = array();

	$return['template_setup'] = array(
		'icon'			=> PL_ADMIN_ICONS.'/dragdrop.png',
		'templates'		=> array(
			'default'	=> '',
			'type'		=> 'templates',
			'layout'	=> 'interface',
			'title'		=> __( 'Drag &amp; Drop Template Setup', 'pagelines'),					
			'shortexp'	=> __( "Use draggable sections to control the design of your site's templates.", 'pagelines'),
		)	
	);
	
	return apply_filters('pagelines_templates_opt_array', $return);	
}
