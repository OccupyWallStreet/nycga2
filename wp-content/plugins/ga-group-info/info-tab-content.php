<?php
    global $bp;
    $data = groups_get_groupmeta( $bp->groups->current_group->id, $this->slug );
    ?>
<div class="extra-data">
    <?php foreach ( $data as $slug => $data ) {
	if (!empty($data['value'])) :
	$value = gait_htmlify( $data['value'] );
	?>
	<h4 title=""><?php echo $data['name']; ?></h4>
	<p><?php echo $value; ?></p>
	<?php
	endif;
    } ?>
</div>
<?php 