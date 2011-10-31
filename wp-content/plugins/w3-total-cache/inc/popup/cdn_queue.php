<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/popup/common/header.php'; ?>

<p>This tool lists the pending file uploads and deletions.</p>
<p id="w3tc-options-menu">
	<a href="#cdn_queue_upload" rel="#cdn_queue_upload" class="tab<?php if ($cdn_queue_tab == 'upload'): ?> tab-selected<?php endif; ?>">Upload queue</a> |
	<a href="#cdn_queue_delete" rel="#cdn_queue_delete" class="tab<?php if ($cdn_queue_tab == 'delete'): ?> tab-selected<?php endif; ?>">Delete queue</a> |
	<a href="#cdn_queue_purge" rel="#cdn_queue_purge" class="tab<?php if ($cdn_queue_tab == 'purge'): ?> tab-selected<?php endif; ?>">Purge queue</a>
</p>

<div id="cdn_queue_upload" class="tab-content"<?php if ($cdn_queue_tab != 'upload'): ?> style="display: none;"<?php endif; ?>>
<?php if (! empty($queue[W3TC_CDN_COMMAND_UPLOAD])): ?>
	<table class="table queue">
		<tr>
			<th>Local Path</th>
			<th>Remote Path</th>
			<th>Last Error</th>
			<th>Date</th>
			<th>Delete</th>
		</tr>
		<?php foreach ((array) $queue[W3TC_CDN_COMMAND_UPLOAD] as $result): ?>
		<tr>
			<td><?php echo htmlspecialchars($result->local_path); ?></td>
			<td><?php echo htmlspecialchars($result->remote_path); ?></td>
			<td><?php echo htmlspecialchars($result->last_error); ?></td>
			<td align="center"><?php echo htmlspecialchars($result->date); ?></td>
			<td align="center">
				<a href="admin.php?page=w3tc_cdn&amp;w3tc_cdn_queue&amp;cdn_queue_tab=upload&amp;cdn_queue_action=delete&amp;cdn_queue_id=<?php echo $result->id; ?>&amp;_wpnonce=<?php echo $nonce; ?>" class="cdn_queue_delete">Delete</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<p>
		<a href="admin.php?page=w3tc_cdn&amp;w3tc_cdn_queue&amp;cdn_queue_tab=upload&amp;cdn_queue_action=empty&amp;cdn_queue_type=<?php echo W3TC_CDN_COMMAND_UPLOAD; ?>&amp;_wpnonce=<?php echo $nonce; ?>" class="cdn_queue_empty">Empty upload queue</a>
	</p>
<?php else: ?>
	<p class="empty">Upload queue is empty</p>
<?php endif; ?>
</div>

<div id="cdn_queue_delete" class="tab-content"<?php if ($cdn_queue_tab != 'delete'): ?> style="display: none;"<?php endif; ?>>
<?php if (! empty($queue[W3TC_CDN_COMMAND_DELETE])): ?>
	<table class="table queue">
		<tr>
			<th>Local Path</th>
			<th>Remote Path</th>
			<th>Last Error</th>
			<th width="25%">Date</th>
			<th width="10%">Delete</th>
		</tr>
		<?php foreach ((array) $queue[W3TC_CDN_COMMAND_DELETE] as $result): ?>
		<tr>
			<td><?php echo htmlspecialchars($result->local_path); ?></td>
			<td><?php echo htmlspecialchars($result->remote_path); ?></td>
			<td><?php echo htmlspecialchars($result->last_error); ?></td>
			<td align="center"><?php echo htmlspecialchars($result->date); ?></td>
			<td align="center">
				<a href="admin.php?page=w3tc_cdn&amp;w3tc_cdn_queue&amp;cdn_queue_tab=delete&amp;cdn_queue_action=delete&amp;cdn_queue_id=<?php echo $result->id; ?>&amp;_wpnonce=<?php echo $nonce; ?>" class="cdn_queue_delete">Delete</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<p>
		<a href="admin.php?page=w3tc_cdn&amp;w3tc_cdn_queue&amp;cdn_queue_tab=delete&amp;cdn_queue_action=empty&amp;cdn_queue_type=<?php echo W3TC_CDN_COMMAND_DELETE; ?>&amp;_wpnonce=<?php echo $nonce; ?>" class="cdn_queue_empty">Empty delete queue</a>
	</p>
<?php else: ?>
	<p class="empty">Delete queue is empty</p>
<?php endif; ?>
</div>

<div id="cdn_queue_purge" class="tab-content"<?php if ($cdn_queue_tab != 'purge'): ?> style="display: none;"<?php endif; ?>>
<?php if (! empty($queue[W3TC_CDN_COMMAND_PURGE])): ?>
	<table class="table queue">
		<tr>
			<th>Local Path</th>
			<th>Remote Path</th>
			<th>Last Error</th>
			<th width="25%">Date</th>
			<th width="10%">Delete</th>
		</tr>
		<?php foreach ((array) $queue[W3TC_CDN_COMMAND_PURGE] as $result): ?>
		<tr>
			<td><?php echo htmlspecialchars($result->local_path); ?></td>
			<td><?php echo htmlspecialchars($result->remote_path); ?></td>
			<td><?php echo htmlspecialchars($result->last_error); ?></td>
			<td align="center"><?php echo htmlspecialchars($result->date); ?></td>
			<td align="center">
				<a href="admin.php?page=w3tc_cdn&amp;w3tc_cdn_queue&amp;cdn_queue_tab=purge&amp;cdn_queue_action=delete&amp;cdn_queue_id=<?php echo $result->id; ?>&amp;_wpnonce=<?php echo $nonce; ?>" class="cdn_queue_delete">Delete</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<p>
		<a href="admin.php?page=w3tc_cdn&amp;w3tc_cdn_queue&amp;cdn_queue_tab=purge&amp;cdn_queue_action=empty&amp;cdn_queue_type=<?php echo W3TC_CDN_COMMAND_PURGE; ?>&amp;_wpnonce=<?php echo $nonce; ?>" class="cdn_queue_empty">Empty purge queue</a>
	</p>
<?php else: ?>
	<p class="empty">Purge queue is empty</p>
<?php endif; ?>
</div>

<?php include W3TC_INC_DIR . '/popup/common/footer.php'; ?>