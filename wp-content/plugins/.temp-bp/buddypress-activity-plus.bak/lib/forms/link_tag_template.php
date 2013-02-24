<div class="bpfb_final_link">
	<?php if ($image) { ?>
	<div class="bpfb_link_preview_container">
		<a href="<?php echo $url;?>"><img src="<?php echo $image; ?>" /></a>
	</div>
	<?php } ?>
	<div class="bpfb_link_contents">
		<div class="bpfb_link_preview_title"><?php echo $title;?></div>
		<div class="bpfb_link_preview_url">
			<a href="<?php echo $url;?>"><?php echo $url;?></a>
		</div>
		<div class="bpfb_link_preview_body"><?php echo $body;?></div>
	</div>
</div>