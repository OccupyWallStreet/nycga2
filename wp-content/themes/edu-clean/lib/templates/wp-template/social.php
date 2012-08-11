<div class="post-social">

<?php
$plink = get_permalink();
$plink = urlencode($plink);
?>
<script type="text/javascript">
addthis_url    = '<?php echo "$plink"; ?> ';
addthis_title  = '<?php the_title(); ?>';
addthis_pub    = '';
</script>

<script type="text/javascript" src="http://s7.addthis.com/js/addthis_widget.php?v=12" ></script>


</div>