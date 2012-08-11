<!-- start feature block -->
<?php 	
		$feature_blockheader = get_option("dev_studio_feature_header");
		$feature_blockone_header = get_option('dev_studio_feature_blockone_header');
		$feature_blocktwo_header = get_option('dev_studio_feature_blocktwo_header');
		$feature_blockthree_header = get_option('dev_studio_feature_blockthree_header');
		
		$feature_blockone_description = get_option('dev_studio_feature_blockone_description');
		$feature_blocktwo_description = get_option('dev_studio_feature_blocktwo_description');
		$feature_blockthree_description = get_option('dev_studio_feature_blockthree_description');
		
		$feature_blockone_image = get_option('dev_studio_feature_blockone_image');
		$feature_blocktwo_image = get_option('dev_studio_feature_blocktwo_image');
		$feature_blockthree_image = get_option('dev_studio_feature_blockthree_image');
		
		$feature_blockone_image_title = get_option('dev_studio_feature_blockone_image_title');
		$feature_blocktwo_image_title = get_option('dev_studio_feature_blocktwo_image_title');
		$feature_blockthree_image_title = get_option('dev_studio_feature_blockthree_image_title');
		
		$feature_blockone_link = get_option('dev_studio_feature_blockone_link');
		$feature_blocktwo_link = get_option('dev_studio_feature_blocktwo_link');
		$feature_blockthree_link = get_option('dev_studio_feature_blockthree_link');
		
		$feature_blockone_link_title = get_option('dev_studio_feature_blockone_link_title');
		$feature_blocktwo_link_title = get_option('dev_studio_feature_blocktwo_link_title');
		$feature_blockthree_link_title = get_option('dev_studio_feature_blockthree_link_title');
?>
<h3><?php echo stripslashes($feature_blockheader); ?></h3><!-- feature header -->
<div id="content-blocks"><!-- start #content-blocks -->
	<div class="content-block"><!-- start feature first block -->
		<h4><?php echo stripslashes($feature_blockone_header); ?></h4>
		<p>
			<?php if ($feature_blockone_image != ""){?>
			<img src="<?php echo $feature_blockone_image; ?>" alt="<?php echo $feature_blockone_image_title; ?>" class="alignleft"/>
			<?php } ?>
		<?php echo stripslashes($feature_blockone_description); ?>
		</p>
			<?php if ($feature_blockone_link_title != ""){?>
	<a href="<?php echo $feature_blockone_link; ?>" rel="bookmark" title="<?php echo $feature_blockone_link_title; ?>" class="button"><?php echo $feature_blockone_link_title; ?></a>
	<?php
	}
	?>
	</div>
	<div class="content-block"><!-- start feature second block -->
			<h4><?php echo stripslashes($feature_blocktwo_header); ?></h4>
			<p>
					<?php if ($feature_blocktwo_image != ""){?>
				<img src="<?php echo $feature_blocktwo_image; ?>" alt="<?php echo $feature_blocktwo_image_title; ?>" class="alignleft"/>
				<?php } ?>
			<?php echo stripslashes($feature_blocktwo_description); ?>
			</p>
				<?php if ($feature_blocktwo_link_title != ""){?>
		<a href="<?php echo $feature_blocktwo_link; ?>" rel="bookmark" title="<?php echo $feature_blocktwo_link_title; ?>" class="button"><?php echo $feature_blocktwo_link_title; ?></a>
		<?php
		}
		?>
	</div>
	<div class="content-block-end"><!-- start feature third block -->
			<h4><?php echo stripslashes($feature_blockthree_header); ?></h4>
			<p>
					<?php if ($feature_blockthree_image != ""){?>
				<img src="<?php echo $feature_blockthree_image; ?>" alt="<?php echo $feature_blockthree_image_title; ?>" class="alignleft"/>
				<?php } ?>
			<?php echo stripslashes($feature_blockthree_description); ?>
			</p>
				<?php if ($feature_blockthree_link_title != ""){?>
		<a href="<?php echo $feature_blockthree_link; ?>" rel="bookmark" title="<?php echo $feature_blockthree_link_title; ?>" class="button"><?php echo $feature_blockthree_link_title; ?></a>
		<?php
		}
		?>
	</div>
	<div class="clear"></div>
</div><!-- end #content-blocks -->
<!-- end feature block -->