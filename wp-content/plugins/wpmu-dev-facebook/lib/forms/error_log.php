<?php
	$date_fmt = get_option('date_format');
	$date_fmt = $date_fmt ? $date_fmt : 'Y-m-d';
	$time_fmt = get_option('time_format');
	$time_fmt = $time_fmt ? $time_fmt : 'H:i:s';
	$datetime_format = "{$date_fmt} {$time_fmt}";
?>
<div class="wrap">
	<h2><?php _e('Error Log', 'wdfb');?></h2>

<h3>Errors</h3>
<?php if ($errors) { ?>
<a href="<?php echo admin_url('admin.php?page=wdfb_error_log&action=purge');?>">Purge log</a>
<table class="widefat">
	<thead>
		<tr>
			<th><?php _e('Date', 'wdfb')?></th>
			<th><?php _e('User', 'wdfb')?></th>
			<th><?php _e('Area', 'wdfb')?></th>
			<th><?php _e('Type', 'wdfb')?></th>
			<th><?php _e('Info', 'wdfb')?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th><?php _e('Date', 'wdfb')?></th>
			<th><?php _e('User', 'wdfb')?></th>
			<th><?php _e('Area', 'wdfb')?></th>
			<th><?php _e('Type', 'wdfb')?></th>
			<th><?php _e('Info', 'wdfb')?></th>
		</tr>
	</tfoot>
	<tbody>
	<?php foreach ($errors as $error) { ?>
		<?php $user = get_userdata(@$error['user_id']);?>
		<tr>
			<td><?php echo date($datetime_format, $error['date']);?></td>
			<td><?php echo ((isset($user->user_login) && $user->user_login) ? $user->user_login : __('Unknown', 'wdfb'));?></td>
			<td><?php echo $error['area'];?></td>
			<td><?php echo $error['type'];?></td>
			<td><?php echo $error['info'];?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
<?php } else { ?>
	<p><i>Your error log is empty.</i></p>
<?php } ?>

<?php if (current_user_can('manage_network_options')) { ?>
<p><a href="#notices" class="wdfb_toggle_notices">Show/Hide notices</a></p>
<div id="wdfb_notices" style="display:none">
<h3>Notices</h3>
<?php if ($notices) { ?>
<table class="widefat">
	<thead>
		<tr>
			<th><?php _e('Date', 'wdfb')?></th>
			<th><?php _e('User', 'wdfb')?></th>
			<th><?php _e('Message', 'wdfb')?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th><?php _e('Date', 'wdfb')?></th>
			<th><?php _e('User', 'wdfb')?></th>
			<th><?php _e('Message', 'wdfb')?></th>
		</tr>
	</tfoot>
	<tbody>
	<?php foreach ($notices as $notice) { ?>
		<?php $user = get_userdata(@$notice['user_id']);?>
		<tr>
			<td><?php echo date($datetime_format, $notice['date']);?></td>
			<td><?php echo ((isset($user->user_login) && $user->user_login) ? $user->user_login : __('Unknown', 'wdfb'));?></td>
			<td><?php echo $notice['message'];?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
<?php } else { ?>
	<p><i>No notices.</i></p>
<?php } ?>
</div>

<script type="text/javascript">
(function ($) {
$(function () {

$(".wdfb_toggle_notices").click(function () {
	if ($("#wdfb_notices").is(":visible")) $("#wdfb_notices").hide();
	else $("#wdfb_notices").show();
	return false;
});

});
})(jQuery);
</script>
<?php } ?>

</div>