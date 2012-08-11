<div class="wdqs wdqs_link wdqs-link-container">
	<p class="wdqs-link-to-source"><a target="_blank" href="<?php echo $link; ?>"><?php echo $link;?></a></p>
	<div class="wdqs-thumbnail-container">
		<?php if (is_array($images)) { ?>
		<ul class="wdqs-image-list">
		<?php foreach ($images as $img) { ?>
			<?php $img = preg_match('!^https?:!', $img) ? $img : $link . '/' . ltrim($img, '/');?>
			<li><img width="100%" src="<?php echo $img;?>" /></li>
		<?php } ?>
		</ul>
		<?php } else if ($image) { ?>
			<?php $image = preg_match('!^https?:!', $image) ? $image : $link . '/' . ltrim($image, '/');?>
			<a href="<?php echo $link;?>" target="_blank"><img src="<?php echo $image;?>" /></a>
		<?php } ?>
	</div>
	<div class="wdqs-text-container">
		<p><?php echo $text; ?></p>
	</div>
	<div style="clear:both"></div>
</div>