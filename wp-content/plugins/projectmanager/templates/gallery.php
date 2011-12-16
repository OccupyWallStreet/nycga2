<?php
/**
Template page for dataset gallery

The following variables are usable:

	$project: contains data for the project
	$datasets: contains all datasets for current selection
	$pagination: contains the pagination
	
	You can check the content of a variable when you insert the tag <?php var_dump($variable) ?>
*/
?>

<?php if ( isset($_GET['show']) ) : ?>
 	<?php do_action('projectmanager_dataset', array('id' => $_GET['show'], 'echo' => 1), true) ?>
<?php else: ?>
	
<?php if ( $project->selections ) do_action('projectmanager_selections'); ?>

<?php if ( $datasets ) : $i = 0; ?>
<div class='dataset_gallery'>
	<?php foreach ( $datasets AS $dataset ) : $i++; ?>
	
	<div class='gallery-item' style='width: <?php echo $project->dataset_width ?>%;'>
		<div class="gallery-image" style="margin: 0 <?php echo $project->dataset_width/2 ?>%;">
			<?php if ( !empty($dataset->image) ) : ?>
			<a href="<?php echo $dataset->URL ?>"><img src="<?php echo $dataset->thumbURL ?>" alt="<?php echo $dataset->name ?>" title="<?php echo $dataset->name ?>" /></a>
			<?php endif; ?>
	
			<p class='caption'><a href="<?php echo $dataset->URL ?>"><?php echo $dataset->name ?></a></p>
		</div>
	</div>
	
	<?php if ( 0 == $i % $project->gallery_num_cols ) : ?>
	<br style="clear: both;" />
	<?php endif; ?>

	<?php endforeach; ?>
</div>

<br style='clear: both;' />

<p class='page-numbers'><?php echo $pagination ?></p>

<?php else : ?>
<p class='error'><?php _e( 'Nothing found', 'projectmanager') ?></p>
<?php endif; ?>

<?php endif; ?>
