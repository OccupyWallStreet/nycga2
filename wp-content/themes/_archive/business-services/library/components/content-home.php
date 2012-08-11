<?php
	if(is_home()) {?>
	<?php
		$backgroundimage = get_option('dev_businessservices_feature_image');
	?>
		<div id="main-header">
		<div id="main-header-inner">
				<?php
				if ($backgroundimage == ""){
					?>
					<div style="background: url(<?php bloginfo('template_directory'); ?>/library/styles/colour-images/advert01.jpg) no-repeat center center;  width: 960px; height: 230px;"></div>
					<?php
				}
				else {
					?>
					<div style="background: url(<?php echo $backgroundimage; ?>) no-repeat center center;  width: 960px; height: 230px;"></div>
					<?php
				}
				?>
		</div>
		</div>	
		<?php
$home_title = get_option('dev_businessservices_header_title');
$home_description = get_option('dev_businessservices_header_description');
$home_link_title = get_option('dev_businessservices_header_link');
$home_link = get_option('dev_businessservices_header_linktext');
		?>
		<div id="inner-header-signup">
		  <div id="inner-header-signup-content">
		    <div id="inner-header-signup-content-inside">
				<?php
				if ($home_title == ""){
					$home_title = "Set this title in theme options";
				}
				?>
				<h2 class="pagetitle"><?php echo stripslashes($home_title); ?></h2>
				<p><?php echo stripslashes($home_description); ?></p>
				<?php
				if ($home_link_title == ""){
					$home_link_title = "Set this title in theme options";
				}
				?>
<a href="<?php echo $home_link; ?>" class="button"><?php echo stripslashes($home_link_title); ?></a>
		    </div>
		  </div>
		</div>
		</div>
	<?php
} else {
	?>
		<?php
$page_title = get_option('dev_businessservices_page_title');
$page_description = get_option('dev_businessservices_page_description');
		?>
	<div id="main-header">
	  <div id="main-header-inner">
	    <div id="main-header-content-single">
	 		<h2 class="pagetitle"><?php echo stripslashes($page_title); ?></h2>
			<p><?php echo stripslashes($page_description); ?></p>
	    </div>
	  </div>
	</div>
	<div class="noline" id="inner-header-signup">
	  <div id="inner-header-signup-content">
	    <div id="inner-header-signup-content-inside">
	      <div id="social-inner-single">
	      </div>
	    </div>
	  </div>
	</div>				
	<?php } ?>	