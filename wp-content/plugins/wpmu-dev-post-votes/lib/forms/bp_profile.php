<h4><?php _e('Recent votes', 'wdpv');?></h4>

<?php if ($recent_votes) { ?>
<ul>
	<?php foreach ($recent_votes as $vote) { ?>
	<li>
		<?php
			$url = get_blog_permalink($vote['blog_id'], $vote['post_id']);
			$post = get_blog_post($vote['blog_id'], $vote['post_id']);
			$title = $post->post_title;
		?>
		<a href="<?php echo $url;?>"><?php echo $title;?></a>
	</li>
	<?php } ?>
</ul>
<?php } else { ?>
	<p><?php _e('No recent votes', 'wdpv'); ?></p>
<?php } ?>