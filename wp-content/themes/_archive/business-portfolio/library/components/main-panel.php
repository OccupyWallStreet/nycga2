<div id="main-wrap">
<div id="main-container">
	<div id="main-content-single">
		<?php
			$page_title = get_option('dev_businessportfolio_page_title');
			$page_description = get_option('dev_businessportfolio_page_description');
		?>
	<div id="left-content">
			<?php
				if ($page_title == ""){
					$page_title = "Add your own title under options";
				}
			?>
	<h1><?php echo stripslashes($page_title); ?></h1>
	</div>
	<div id="right-content">
			<?php
				if ($page_description == ""){
					$page_description = "Add your own description under options";
				}
			?>
<?php echo stripslashes($page_description); ?>
	</div>
	</div>
		</div>
		</div>