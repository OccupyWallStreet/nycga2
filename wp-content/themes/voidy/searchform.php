<?php $search_text = __("Search this site", "voidy" ); ?> 
<form method="get" id="searchform" action="<?php bloginfo('url'); ?>/"> 
<input type="text" value="<?php echo $search_text; ?>"  
	name="s" id="s"  class="with-button"
	onblur="if (this.value == '')  
	{this.value = '<?php echo $search_text; ?>';}"  
	onfocus="if (this.value == '<?php echo $search_text; ?>')  
	{this.value = '';}" /> 
	<input type="submit" value="Go" class="go" />
<input type="hidden" id="searchsubmit" /> 
</form>