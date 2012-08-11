<?php
	$header_backgroundon = get_option('dev_businessfeature_headeron');
	$header_background = get_option('dev_businessfeature_header_image');
	$header_title = get_option('dev_businessfeature_header_title');
	$header_description = get_option('dev_businessfeature_header_description');
	$header_link = get_option('dev_businessfeature_header_link');
	$header_linktitle = get_option('dev_businessfeature_header_linktext');
	
?>		
							<div id="header">
							<div class="content-wrap">
							<div class="content-content">
								<?php
								if ($header_backgroundon != "no"){
								?>
								<?php 
								if($header_background ==""){
									?>
										<div id="header-unlogged">
										<div id="intro-background" style="height:242px;width:980px;background: #9a3600 url('<?php bloginfo('template_directory'); ?>/library/styles/colour-images/background2.jpg') no-repeat">
							
							<?php	}
							else{
								?>
								<div id="header-unlogged">
								<div id="intro-background" style="height:242px;width:980px;background: #9a3600 url('<?php echo $header_background; ?>') no-repeat">		
								<?php
							}?>
						<div id="intro-text">
							<?php
								if ($header_title){
									?>
									<h4><?php echo stripslashes($header_title); ?></h4>	
									<?php
								}
								else {
									?>
									<h4><?php _e("Add content to your site by going to appearance > theme options", 'business-feature'); ?></h4>	
									<?php
								}
								if ($header_description){
								?>
								<p><?php echo stripslashes($header_description); ?></p>
								<?php
								}
								?>
								</div>
								<?php
								if ($header_link){
									?>
											<div id="intro-button">
											<a href="<?php echo $header_link; ?>" rel="bookmark" title="<?php echo $header_linktitle; ?>" class="button"><?php echo stripslashes($header_linktitle); ?></a>
											</div>
									<?php
								}
								?>
								</div>
								</div>
								</div>
								<?php
							}
								?>
								</div>
								</div>
								</div>