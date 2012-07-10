<?php
/**
 * 
 *
 *  Layout Control Interface
 *
 *
 *  @package PageLines Framework
 *  @subpackage OptionsUI
 *  @since 2.0.b3
 *
 */

class PageLinesLayoutControl {


	/**
	 * Construct
	 */
	function __construct() {
		
		
	}

	/**
	 * 
	 *
	 *  Main Layout Drag and Drop
	 *
	 *
	 *  @package PageLines Framework
	 *  @subpackage Options
	 *  @since 2.0.b3
	 *
	 */
	function draw_layout_control($optionid, $option_settings){ ?>
		<div class="layout_controls selected_template">


			<div id="layout-dimensions" class="template-edit-panel">
				<div class="select-edit-layout">
					<div class="layout-selections layout-builder-select fix">
						<div class="layout-selections-pad fix">
							<div class="layout-overview">Select Layout To Edit</div>
							<?php


							global $pagelines_layout;
							foreach(get_the_layouts() as $layout):

								$the_last_edited = (pagelines_sub_option('layout', 'last_edit')) ? pagelines_sub_option('layout', 'last_edit') : 'one-sidebar-right';

								$load_layout = ($the_last_edited == $layout) ? true : false;

							?>
							<div class="layout-select-item">
								<span class="layout-image-border <?php if($load_layout) echo 'selectedlayout';?>">
									<span class="layout-image <?php echo $layout;?>">&nbsp;</span>
								</span>
								<input type="radio" class="layoutinput" name="<?php pagelines_option_name('layout', 'last_edit'); ?>" value="<?php echo $layout;?>" <?php if($load_layout) echo 'checked';?> />
							</div>
							<?php endforeach;?>
						</div>
					</div>	
				</div>
				<?php

			foreach(get_the_layouts() as $layout):

			$buildlayout = new PageLinesLayout($layout);
				?>
			<div class="layouteditor <?php echo $layout;?> <?php if($buildlayout->layout_map['last_edit'] == $layout) echo 'selectededitor';?>">
					<div class="layout-main-content" style="width:<?php echo $buildlayout->builder->bwidth;?>px">

						<div id="innerlayout" class="layout-inner-content" >
							<?php if($buildlayout->west->id != 'hidden'):?>
							<div id="<?php echo $buildlayout->west->id;?>" class="ui-layout-west innerwest loelement locontent"  style="width:<?php echo $buildlayout->west->bwidth;?>px">
								<div class="loelement-pad">
									<div class="loelement-info">
										<div class="layout_text"><?php echo $buildlayout->west->text;?></div>
										<div class="width "><span><?php echo $buildlayout->west->width;?></span>px</div>
									</div>
								</div>
							</div>
							<?php endif;?>
							<div id="<?php echo $buildlayout->center->id;?>" class="ui-layout-center loelement locontent innercenter">
								<div class="loelement-pad">
									<div class="loelement-info">
										<div class="layout_text"><?php echo $buildlayout->center->text;?></div>
										<div class="width "><span><?php echo $buildlayout->center->width;?></span>px</div>
									</div>
								</div>
							</div>
							<?php if( $buildlayout->east->id != 'hidden'):?>
							<div id="<?php echo $buildlayout->east->id;?>" class="ui-layout-east innereast loelement locontent" style="width:<?php echo $buildlayout->east->bwidth;?>px">
								<div class="loelement-pad">
									<div class="loelement-info">
										<div class="layout_text"><?php echo $buildlayout->east->text;?></div>
										<div class="width "><span><?php echo $buildlayout->east->width;?></span>px</div>
									</div>
								</div>
							</div>
							<?php endif;?>
							<div id="contentwidth" class="ui-layout-south loelement locontent" style="background: #fff;">
								<div class="loelement-pad"><div class="loelement-info"><div class="width"><span><?php echo $buildlayout->content->width;?></span>px</div></div></div>
							</div>
							<div id="top" class="ui-layout-north loelement locontent"><div class="loelement-pad"><div class="loelement-info">Content Area</div></div></div>
						</div>
						<div class="margin-west loelement"><div class="loelement-pad"><div class="loelement-info">Margin<div class="width"></div></div></div></div>
						<div class="margin-east loelement"><div class="loelement-pad"><div class="loelement-info">Margin<div class="width"></div></div></div></div>

					</div>


					<div class="layoutinputs">
						<div class="layoutinputs-pad fix">
							<?php 
							
								// Content Width
								$id = 'input-content-width';
								$value = $buildlayout->content->width;
								$name = get_pagelines_option_name('layout', 'content_width');
							
								// Output
								$input = OptEngine::input_text($id, $name, $value, 'small-text', 'text', 'readonly');
								echo OptEngine::input_label_inline($id, $input, 'Global Content Width (px)', 'lbl-layout');
								
								// Main Column
								$id = 'input-maincolumn-width';
								$value = $buildlayout->main_content->width;
								$name = get_pagelines_option_name('layout', $layout, 'maincolumn_width');
								
								// Output
								
								$input = OptEngine::input_text($id, $name, $value, 'small-text', 'text', 'readonly');
								echo OptEngine::input_label_inline($id, $input, 'Main Column Width (px)', 'lbl-layout');
								
								// Sidebar 1
								$id = 'input-primarysidebar-width';
								$value = $buildlayout->sidebar1->width;
								$name = get_pagelines_option_name('layout', $layout, 'primarysidebar_width');
						
								// Output
								
								$input = OptEngine::input_text($id, $name, $value, 'small-text', 'text', 'readonly');
								echo OptEngine::input_label_inline($id, $input, 'Sidebar1 Width (px)', 'lbl-layout');
								
								// Responsive
								$id = 'input-responsive-width';
								$value = ($buildlayout->content->width / $buildlayout->builder->width) * 100;
								$name = get_pagelines_option_name('layout', 'responsive_width');
						
								// Output
								$input = OptEngine::input_text($id, $name, $value, 'small-text', 'text', 'readonly');
								echo OptEngine::input_label_inline($id, $input, 'Content Percent (%)', 'lbl-layout');
								
							
							?>
						</div>
					</div>
			</div>
			<?php endforeach;?>

		</div>
	</div>
	<?php }
	

	/**
	*
	* @TODO document
	*
	*/
	function get_layout_selector( $oid, $o ){ ?>
		<div id="layout_selector" class="template-edit-panel">

			<div class="layout-selections layout-select-default fix">
				<div class="layout-selections-pad fix">
					<div class="layout-overview"><?php echo $o['inputlabel'];?></div>
					<?php

					global $pagelines_layout;
					$saved_layout = $pagelines_layout->layout_map['saved_layout'];
					
					foreach(get_the_layouts() as $layout): ?>
					<div class="layout-select-item">
						<span class="layout-image-border <?php if($saved_layout == $layout) echo 'selectedlayout';?>">
							<span class="layout-image <?php echo $layout;?>">&nbsp;</span>
						</span>
						<input type="radio" class="layoutinput" name="<?php pagelines_option_name('layout', 'saved_layout'); ?>" value="<?php echo $layout;?>" <?php checked($layout, $saved_layout); ?>>
					</div>
					<?php endforeach;?>
				</div>
				
			</div>
			
		</div>
		<div class="sel_layout_exp">
			<div class="sel_layout_exp_pad">
				<?php echo $o['exp'];?>
			</div>
		</div>
		<div class="clear"></div>
	<?php }


	/**
	*
	* @TODO document
	*
	*/
	function layout_control_javascript(){ ?>
<script type="text/javascript">/*<![CDATA[*/
jQuery(document).ready(function(){

		/*
			Layout Builder Control	
		*/
			// Default Layout Select
			jQuery(' .layout-select-default .layout-image-border').click(function(){
				LayoutSelectControl(this);
			});

			<?php 
				$the_last_edited = (pagelines_sub_option('layout', 'last_edit')) ? pagelines_sub_option('layout', 'last_edit') : null;

				// Set up layout object for loaded page
				$load_layout = new PageLinesLayout($the_last_edited);

				// Values for loading layout
				$layout_mode = $load_layout->layout_mode;
				$load_margin = $load_layout->margin->bwidth;
				$load_west = $load_layout->west->bwidth;
				$load_east = $load_layout->east->bwidth;
				$load_gutter = $load_layout->gutter->bwidth;

			?>
			setLayoutBuilder('<?php echo $layout_mode; ?>', <?php echo $load_margin;?>, <?php echo $load_east;?>, <?php echo $load_west;?>, 10);

			jQuery('.selected_template .layout-builder-select .layout-image-border').click(function(){
				var LayoutMode;
				var marginwidth;
				var innerwestwidth;
				var innereastwidth;
				var gtrwidth;


				// Get previous selected layout margin
				var mwidth = jQuery('.selectededitor .margin-west').width(); // substract border

				// Control selector class & visualization
				LayoutSelectControl(this);


				// For Layout Builder mode e.g. 'one-sidebar-right'
				LayoutMode = jQuery(this).parent().find('.layoutinput').val();

				deactivateCurrentBuilder();

				// Display selected builder
				jQuery('.'+LayoutMode).addClass('selectededitor');

			<?php foreach(get_the_layouts() as $layout):
			
					$mylayout = new PageLinesLayout($layout);
					$default_margin = $mylayout->margin->bwidth;
					$ewidth = $mylayout->east->bwidth;
					$wwidth = $mylayout->west->bwidth;
				?>
					if (LayoutMode == '<?php echo $layout;?>') { 
						marginwidth = mwidth + 2;
						innereastwidth = <?php echo $ewidth;?>;
						innerwestwidth = <?php echo $wwidth;?>; 
						gtrwidth = 10
					}
					
			<?php endforeach;?>

				setLayoutBuilder(LayoutMode, marginwidth, innereastwidth, innerwestwidth, gtrwidth);

			});	


});
		
/*]]>*/</script><?php }

}
