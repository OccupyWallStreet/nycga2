<?php
/**
Template page for single dataset

The following variables are usable:

	$dataset: contains all data of the dataset
	$backurl: contains the url back to the overview page
	
	You can check the content of a variable when you insert the tag <?php var_dump($variable) ?>
*/
?>
<?php if ( $backurl ) : ?>
<p><a href='<?php echo $backurl ?>'><?php _e('Back to list', 'projectmanager') ?></a></p>
<?php endif; ?>

<?php if ( $dataset ) : ?>
<fieldset class='dataset'><legend><?php printf(__( 'Details of %s', 'projectmanager' ), $dataset->name) ?></legend>
	<?php if ( $dataset->image != '' ) : ?>
	<div class='image'><img src='<?php echo $dataset->imgURL ?>' title='<?php echo $dataset->name ?>' alt='<?php echo $dataset->name ?>' /></div>
	<?php endif; ?>
	<dl><?php $projectmanager->printDatasetMetaData( $dataset, array('output' => 'dl', 'show_all' => true) ) ?></dl>
</fieldset>
<?php endif; ?>
