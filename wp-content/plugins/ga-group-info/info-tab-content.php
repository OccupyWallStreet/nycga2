<?php
// Get full set of options
// put them into a multi-dimensional array
gait_display_info_content();

function gait_display_info_content(){
    global $bp;
    $data = array(
	'Name' => 'Indigo Montoya',
	'Who did you kill?' => 'My Father',
	'What should we prepare for?' => 'to die.'
    );
    ?>
<div class="extra-data">
    <?php foreach ( $data as $label=>$value ) { ?>
	<h4 title=""><?php echo $label; ?></h4>
	<p><?php echo $value; ?></p>
    <?php } ?>
</div>
<?php }