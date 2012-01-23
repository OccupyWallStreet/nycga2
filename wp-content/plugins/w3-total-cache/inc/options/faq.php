<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<h4>Table of Contents</h4>

<div id="toc">
    <ul>
    <?php foreach ($faq as $section => $entries): ?>
    <li class="col">
        <h5><?php echo strtoupper($section); ?>:</h5>
        <ul>
    	    <?php foreach ($entries as $entry): ?>
	        <li><a href="#q<?php echo $entry['index']; ?>"><?php echo $entry['question']; ?></a></li>
            <?php endforeach; ?>
        </ul>
    </li>
    <?php endforeach; ?>
    </ul>
</div>
<div id="qa">
	<hr />
    <?php foreach ($faq as $section => $entries): ?>
        <?php foreach ($entries as $entry): ?>
    	<p id="q<?php echo $entry['index']; ?>"><strong><?php echo $entry['question']; ?></strong></p>
        <?php echo $entry['answer']; ?>
    	<p align="right"><a href="#toc">back to top</a></p>
    	<?php endforeach; ?>
    <?php endforeach; ?>
</div>

<?php include W3TC_INC_DIR . '/options/common/footer.php'; ?>