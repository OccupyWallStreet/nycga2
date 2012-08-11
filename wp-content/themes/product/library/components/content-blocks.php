<?php 	
		$blockone_header = get_option('dev_product_blockone_header');
		$blocktwo_header = get_option('dev_product_blocktwo_header');
		$blockthree_header = get_option('dev_product_blockthree_header');
		
		$blockone_description = get_option('dev_product_blockone_description');
		$blocktwo_description = get_option('dev_product_blocktwo_description');
		$blockthree_description = get_option('dev_product_blockthree_description');
		
		$blockone_image = get_option('dev_product_blockone_image');
		$blocktwo_image = get_option('dev_product_blocktwo_image');
		$blockthree_image = get_option('dev_product_blockthree_image');
		
		$blockone_image_title = get_option('dev_product_blockone_image_title');
		$blocktwo_image_title = get_option('dev_product_blocktwo_image_title');
		$blockthree_image_title = get_option('dev_product_blockthree_image_title');
		
		$blockone_link = get_option('dev_product_blockone_link');
		$blocktwo_link = get_option('dev_product_blocktwo_link');
		$blockthree_link = get_option('dev_product_blockthree_link');
		
		$blockone_link_title = get_option('dev_product_blockone_link_title');
		$blocktwo_link_title = get_option('dev_product_blocktwo_link_title');
		$blockthree_link_title = get_option('dev_product_blockthree_link_title');
?>

<div id="content-block-wrapper">
	<div class="content-block">
		<h4><?php echo stripslashes($blockone_header); ?></h4>
		<?php if ($blockone_image != ""){?>
		<div class="content-image">
			<img src="<?php echo $blockone_image; ?>" alt="<?php echo $blockone_image_title; ?>"/>
		</div>
		<?php } ?>
		<p><?php echo stripslashes($blockone_description); ?></p>
		<?php if ($blockone_link_title != ""){?>
<a href="<?php echo $blockone_link; ?>" rel="bookmark" title="<?php echo $blockone_link_title; ?>" class="button"><?php echo $blockone_link_title; ?></a>
<?php
}
?>
	</div>
		<div class="content-block">
					<h4><?php echo stripslashes($blocktwo_header); ?></h4>
						<?php if ($blocktwo_image != ""){?>
					<div class="content-image">
						<img src="<?php echo $blocktwo_image; ?>" alt="<?php echo $blocktwo_image_title; ?>"/>
					</div>
							<?php } ?>
					<p><?php echo stripslashes($blocktwo_description); ?></p>
	<?php if ($blocktwo_link_title != ""){?>
			<a href="<?php echo $blocktwo_link; ?>" rel="bookmark" title="<?php echo $blocktwo_link_title; ?>" class="button"><?php echo $blocktwo_link_title; ?></a>
			<?php
			}
			?>
			</div>
			<div class="content-block-end">
						<h4><?php echo stripslashes($blockthree_header); ?></h4>
							<?php if ($blockthree_image != ""){?>
						<div class="content-image">
							<img src="<?php echo $blockthree_image; ?>" alt="<?php echo $blockthree_image_title; ?>"/>
						</div>
								<?php } ?>
						<p><?php echo stripslashes($blockthree_description); ?></p>
	<?php if ($blockthree_link_title != ""){?>
				<a href="<?php echo $blockthree_link; ?>" rel="bookmark" title="<?php echo $blockthree_link_title; ?>" class="button"><?php echo $blockthree_link_title; ?></a>
				<?php
				}
				?>
				</div>
			<div class="clear"></div>
</div>