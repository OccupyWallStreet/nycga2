<?php

	$homepage_show_type_posts = get_option('dev_network_homepage_show_type_posts');
	$homepage_featured_start_page = get_option('dev_network_homepage_featured_start_page');
	$homepage_items_per_page = get_option('dev_network_homepage_items_per_page');
	$homepage_label_first = get_option('dev_network_homepage_label_first');
	$homepage_label_last = get_option('dev_network_homepage_label_last');
	$homepage_label_prev = get_option('dev_network_homepage_label_prev');
	$homepage_label_next = get_option('dev_network_homepage_label_next');
	$homepage_prev_next = get_option('dev_network_homepage_prev_next');
	$homepage_show_thumbnails = get_option('dev_network_homepage_show_thumbnails');
	if ($homepage_show_thumbnails == '') { $homepage_show_thumbnails = 'yes'; } // this will make this by default when the theme is installed
	$homepage_how_many = get_option('dev_network_homepage_how_many');
	$homepage_how_far = get_option('dev_network_homepage_how_far');
	
	if (!$homepage_how_many) { $homepage_how_many = 12; }
	if (!$homepage_how_far) { $homepage_how_far = 0; }
	
?>

<script type="text/javascript">

jq(document).ready( function() {

	jq('#articleBox').pajinate({
		start_page : <?php if ($homepage_featured_start_page) { echo $homepage_featured_start_page - 1; } else { echo "0"; } ?>,
		items_per_page : <?php if ($homepage_items_per_page) { echo $homepage_items_per_page; } else { echo "12"; } ?>,
		item_container_id : '.articles',
		nav_label_first : '<?php if ($homepage_label_first) { echo $homepage_label_first; } else { echo "<< first"; } ?>',
		nav_label_last : '<?php if ($homepage_label_last) { echo $homepage_label_last; } else { echo "last >>"; } ?>',
		nav_label_prev : '<?php if ($homepage_label_prev) { echo $homepage_label_prev; } else { echo "< prev"; } ?>',
		nav_label_next : '<?php if ($homepage_label_next) { echo $homepage_label_next; } else { echo "next >"; } ?>'	
	});
	
});	

</script>

<div id="articleBox">
	<ul class="articles">
		<?php 
		
			$totalnumber = 0;
						
			if ($homepage_show_type_posts == "Blog Posts") { 

					// Proceed with multi-site blog posts				
					get_recent_posts($homepage_how_many, $homepage_how_far, $homepage_show_thumbnails);
					
			} 
			else {
					
				if (is_multisite()) {
						if (multisite_count_recent_posts($homepage_how_many, $homepage_how_far, $homepage_show_thumbnails) == false) {					
							get_recent_posts($homepage_how_many, $homepage_how_far, $homepage_show_thumbnails);
						} else {

							$totalnumber = multisite_recent_posts($homepage_how_many, $homepage_how_far, $homepage_show_thumbnails);

						}

					} else {

						get_recent_posts($homepage_how_many, $homepage_how_far, $homepage_show_thumbnails);

					}
			}
			
		?>
	</ul>
	<div class="clear"></div>
	<?php if (($totalnumber > 0) || ($totalnumber > $homepage_items_per_page) || ($homepage_items_per_page != '')) { ?>
	<div class="page_navigation"></div>	
	<div class="clear"></div>
	<?php } 
	?>
	
</div> <!-- featured article box -->

