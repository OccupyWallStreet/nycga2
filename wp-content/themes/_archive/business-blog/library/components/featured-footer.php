<div class="box">
<div class="topbox">
	
			<?php
	$links_title = get_option('dev_businessblog_links_title');	
	$linkone = get_option('dev_businessblog_linkone');	
	$linkone_title = get_option('dev_businessblog_linkone_title');	
	$linkone_text = get_option('dev_businessblog_linkone_text');
	$linktwo = get_option('dev_businessblog_linktwo');	
	$linktwo_title = get_option('dev_businessblog_linktwo_title');	
	$linktwo_text = get_option('dev_businessblog_linktwo_text');
	$linkthree = get_option('dev_businessblog_linkthree');	
	$linkthree_title = get_option('dev_businessblog_linkthree_title');	
	$linkthree_text = get_option('dev_businessblog_linkthree_text');
	$linkfour = get_option('dev_businessblog_linkfour');	
	$linkfour_title = get_option('dev_businessblog_linkfour_title');	
	$linkfour_text = get_option('dev_businessblog_linkfour_text');
	$linkfive = get_option('dev_businessblog_linkfive');	
	$linkfive_title = get_option('dev_businessblog_linkfive_title');	
	$linkfive_text = get_option('dev_businessblog_linkfive_text');
			?>
				<?php
						if ($links_title == ""){
							$links_title = "Set up links under theme options";
						}
						?>
<div class="contentbox" id="network">
	<div id="useful-links">
	<h3><?php echo stripslashes($links_title); ?></h3>
	<?php
	if ($linkone != ""){
	?>
	<ul id="all-links">
		<li><h2><a href="<?php echo $linkone; ?>"><?php echo stripslashes($linkone_title); ?></a></h2>
		<p><?php echo stripslashes($linkone_text); ?></p>
		</li>
		
				<li><h2><a href="<?php echo $linktwo; ?>"><?php echo stripslashes($linktwo_title); ?></a></h2>
				<p><?php echo stripslashes($linktwo_text); ?></p>
				</li>
				
					<li><h2><a href="<?php echo $linkthree; ?>"><?php echo stripslashes($linkthree_title); ?></a></h2>
					<p><?php echo stripslashes($linkthree_text); ?></p>
					</li>
					
						<li><h2><a href="<?php echo $linkfour; ?>"><?php echo stripslashes($linkfour_title); ?></a></h2>
						<p><?php echo stripslashes($linkfour_text); ?></p>
						</li>
						
							<li><h2><a href="<?php echo $linkfive; ?>"><?php echo stripslashes($linkfive_title); ?></a></h2>
							<p><?php echo stripslashes($linkfive_text); ?></p>
							</li>
	</ul>
	<?php
}
	?>
	</div>
	<div id="rss-social-network">
	
						<?php if ( is_active_sidebar( 'rss-sidebar' ) ) : ?>
								<?php dynamic_sidebar( 'rss-sidebar' ); ?>
											<?php endif; ?>
	</div>
</div>
</div>
</div>
	
