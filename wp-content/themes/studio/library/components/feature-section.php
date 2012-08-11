<!-- start footer feature block -->
<?php 	
		$footfeat_blockheader = get_option("dev_studio_footfeat_header");
		$footfeat_blockone_header = get_option('dev_studio_footfeat_blockone_header');
		$footfeat_blocktwo_header = get_option('dev_studio_footfeat_blocktwo_header');
		$footfeat_blockthree_header = get_option('dev_studio_footfeat_blockthree_header');
		
		$footfeat_blockone_description = get_option('dev_studio_footfeat_blockone_description');
		$footfeat_blocktwo_description = get_option('dev_studio_footfeat_blocktwo_description');
		$footfeat_blockthree_description = get_option('dev_studio_footfeat_blockthree_description');
		
		$footfeat_blockone_image = get_option('dev_studio_footfeat_blockone_image');
		$footfeat_blocktwo_image = get_option('dev_studio_footfeat_blocktwo_image');
		$footfeat_blockthree_image = get_option('dev_studio_footfeat_blockthree_image');
		
		$footfeat_blockone_image_title = get_option('dev_studio_footfeat_blockone_image_title');
		$footfeat_blocktwo_image_title = get_option('dev_studio_footfeat_blocktwo_image_title');
		$footfeat_blockthree_image_title = get_option('dev_studio_footfeat_blockthree_image_title');
		
		$footfeat_blockone_link = get_option('dev_studio_footfeat_blockone_link');
		$footfeat_blocktwo_link = get_option('dev_studio_footfeat_blocktwo_link');
		$footfeat_blockthree_link = get_option('dev_studio_footfeat_blockthree_link');
		
		$footfeat_blockone_link_title = get_option('dev_studio_footfeat_blockone_link_title');
		$footfeat_blocktwo_link_title = get_option('dev_studio_footfeat_blocktwo_link_title');
		$footfeat_blockthree_link_title = get_option('dev_studio_footfeat_blockthree_link_title');
?>
<div id="feature-wrapper"><!-- start #feature-wrapper -->
	<div id="feature"><!-- start #feature -->
		<h3><?php echo stripslashes($footfeat_blockheader); ?></h3><!-- footer feature header -->
		<div id="content-blocks-blue"><!-- start #content-blocks-blue -->
			<div class="content-block"><!-- start footer feature first block -->
				<h4><?php echo stripslashes($footfeat_blockone_header); ?></h4>
				<p>
						<?php if ($footfeat_blockone_image != ""){?>
					<img src="<?php echo $footfeat_blockone_image; ?>" alt="<?php echo $footfeat_blockone_image_title; ?>" class="alignleft"/>
					<?php } ?>
				<?php echo stripslashes($footfeat_blockone_description); ?>
				</p>
					<?php if ($footfeat_blockone_link_title != ""){?>
			<a href="<?php echo $footfeat_blockone_link; ?>" rel="bookmark" title="<?php echo $footfeat_blockone_link_title; ?>" class="button"><?php echo $footfeat_blockone_link_title; ?></a>
			<?php
			}
			?>
			</div>
			<div class="content-block"><!-- start feature footer second block -->
					<h4><?php echo stripslashes($footfeat_blocktwo_header); ?></h4>
					<p>
						<?php if ($footfeat_blocktwo_image != ""){?>
						<img src="<?php echo $footfeat_blocktwo_image; ?>" alt="<?php echo $footfeat_blocktwo_image_title; ?>" class="alignleft"/>
						<?php } ?>
					<?php echo stripslashes($footfeat_blocktwo_description); ?>
					</p>
						<?php if ($footfeat_blocktwo_link_title != ""){?>
				<a href="<?php echo $footfeat_blocktwo_link; ?>" rel="bookmark" title="<?php echo $footfeat_blocktwo_link_title; ?>" class="button"><?php echo $footfeat_blocktwo_link_title; ?></a>
				<?php
				}
				?>
			</div>
			<div class="content-block-end"><!-- start feature footer third block -->
					<h4><?php echo stripslashes($footfeat_blockthree_header); ?></h4>
					<p>
						<?php if ($footfeat_blockthree_image != ""){?>
						<img src="<?php echo $footfeat_blockthree_image; ?>" alt="<?php echo $footfeat_blockthree_image_title; ?>" class="alignleft"/>
						<?php } ?>
					<?php echo stripslashes($footfeat_blockthree_description); ?>
					</p>
						<?php if ($footfeat_blockthree_link_title != ""){?>
				<a href="<?php echo $footfeat_blockthree_link; ?>" rel="bookmark" title="<?php echo $footfeat_blockthree_link_title; ?>" class="button"><?php echo $footfeat_blockthree_link_title; ?></a>
				<?php
				}
				?>
			</div>
			<div class="clear"></div>
		</div><!-- end #content-blocks-blue -->
	</div><!-- end #feature -->
	<div class="clear"></div>
</div><!-- end #feature-wrapper -->
<!-- end footer feature block -->