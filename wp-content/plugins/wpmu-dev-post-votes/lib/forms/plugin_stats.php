<div class="wrap">
	<h2>Post Voting Statistics</h2>

<?php do_action('wdpv-stats-before_any_stats');?>

<?php if (is_array($overall)) { ?>
<?php do_action('wdpv-stats-before_stat_table');?>
<table class="wp-list-table widefat fixed posts">
	<thead>
		<tr>
			<?php if (WP_NETWORK_ADMIN) { ?><th>Blog</th><?php } ?>
			<th>Title</th>
			<th>Total</th>
			<th>Votes up</th>
			<th>Votes down</th>
		</tr>
	</thead>
<?php
	foreach ($overall as $post) {
		if (WP_NETWORK_ADMIN) {
			$data = get_blog_post($post['blog_id'], $post['post_id']);
			if (!$data) continue;
			$blog_name = get_blog_option($post['blog_id'], 'blogname');
			$blog_url = get_blog_option($post['blog_id'], 'siteurl');
		}
		$title = WP_NETWORK_ADMIN ? $data->post_title : $post['post_title'];
		$permalink = WP_NETWORK_ADMIN ? get_blog_permalink($post['blog_id'], $post['post_id']) : get_permalink($post['ID']);
		$results = $this->model->get_stats($post['post_id'], $post['blog_id'], $post['site_id']);
?>
		<tr>
			<?php if (WP_NETWORK_ADMIN) { ?><td><b><a href="<?echo $blog_url;?>"><?php echo $blog_name; ?></a></b></td><?php } ?>
			<td><b><a href="<?php echo $permalink;?>"><?php echo $title;?></a></b></td>
			<td><?php echo $post['total'];?></td>
			<td><?php echo $results['up'];?></td>
			<td><?php echo $results['down'];?></td>
		</tr>
<?php } // end foreach?>
	<tfoot>
		<tr>
			<?php if (WP_NETWORK_ADMIN) { ?><th>Blog</th><?php } ?>
			<th>Title</th>
			<th>Total</th>
			<th>Votes up</th>
			<th>Votes down</th>
		</tr>
	</tfoot>
</table>
<?php do_action('wdpv-stats-after_stat_table');?>

<?php } ?>

<?php do_action('wdpv-stats-after_any_stats');?>

</div>