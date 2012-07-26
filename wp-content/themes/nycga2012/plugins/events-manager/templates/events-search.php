<?php 
/* 
 * By modifying this in your theme folder within plugins/events-manager/templates/events-search.php, you can change the way the search form will look.
 * To ensure compatability, it is recommended you maintain class, id and form name attributes, unless you now what you're doing. 
 * You also must keep the _wpnonce hidden field in this form too.
 */
?>

<div class="em-events-search">
  <?php 
	$s_default = __('Search Events', 'dbem'); 
	$s = !empty($_REQUEST['search']) ? $_REQUEST['search']:$s_default;
	if( empty($_REQUEST['country']) && empty($_REQUEST['page']) ){
		$country = get_option('dbem_location_default_country');
	}elseif( empty($_REQUEST['country']) ){
		$country = $_REQUEST['country'];
	}
	//convert scope to an array in event of pagination
	if(!empty($_REQUEST['scope']) && !is_array($_REQUEST['scope'])){ $_REQUEST['scope'] = explode(',',$_REQUEST['scope']); }
	?>
  <form action="" method="get" class="em-events-search-form">
  
  <?php do_action('em_template_events_search_form_header'); ?>
  
  <table cellpadding="0" cellspacing="0" border="0">
	  <thead>
		  <tr>
			  <td><?php _e('Keyword','dbem'); ?></td>
			  <td><?php _e('Between','dbem'); ?></td>
			  <td><?php _e('And','dbem'); ?></td>
			  <td><?php _e('Category','dbem'); ?></td>
			  <td><?php _e('Group','dbem'); ?></td>
			  <td></td>
		  </tr>
	  </thead>
	  <tbody>
		  <tr>
			  <td class="text-search"><?php /* This general search will find matches within event_name, event_notes, and the location_name, address, town, state and country. */ ?>
			    <input type="text" name="search" class="em-events-search-text" value="<?php echo $s; ?>" onFocus="if(this.value=='<?php echo $s_default; ?>')this.value=''" onBlur="if(this.value=='')this.value='<?php echo $s_default; ?>'" /><!-- END General Search --> </td>
			  <td class="start-date-search"><input type="text" size="10" id="em-date-start-loc" />
			    <input type="hidden" id="em-date-start" name="scope[0]" value="<?php if( !empty($_REQUEST['scope'][0]) ) echo $_REQUEST['scope'][0]; ?>" /><!--//END: From Date--></td>
			  <td class="end-date-search"><input type="text" size="10" id="em-date-end-loc" />
			    <input type="hidden" id="em-date-end" name="scope[1]" value="<?php if( !empty($_REQUEST['scope'][1]) ) echo $_REQUEST['scope'][1]; ?>" /><!-- END Date Search --></td>
			  <td class="category-search"><select name="category" class="em-events-search-category">
			      <option value=''><?php _e('All Categories','dbem'); ?></option>
			      <?php foreach(EM_Categories::get(array('orderby'=>'category_name')) as $EM_Category): ?>
			      <option value="<?php echo $EM_Category->id; ?>" <?php echo (!empty($_REQUEST['category']) && $_REQUEST['category'] == $EM_Category->id) ? 'selected="selected"':''; ?>><?php echo $EM_Category->name; ?></option>
			      <?php endforeach; ?>
			    </select><!-- END Category Search --></td>
			  <td class="group-search"><?php if ( ! bp_is_group() && bp_has_groups('type=alphabetical&per_page=0&page=0') ) : //Jessica ?><select name="group" class="em-events-search-category">
		      <option value=''>
		      <?php _e('All Groups','dbem'); ?>
		      </option>
		      <option value='my' <?php echo (!empty($_REQUEST['group']) && $_REQUEST['group'] == 'my') ? 'selected="selected"':''; ?>>
		      <?php _e('My Groups','dbem'); ?>
		      </option>
		      <?php while ( bp_groups() ) : bp_the_group(); ?>
		      <option value="<?php echo bp_group_id(); ?>" <?php echo (!empty($_REQUEST['group']) && $_REQUEST['group'] == bp_get_group_id()) ? 'selected="selected"':''; ?>><?php echo bp_group_name(); ?></option>
		      <?php endwhile; ?>
		    </select><!-- END Group Search -->
		    <?php endif; //Jessica end ?>
    <?php if (bp_is_group()) : ?>
    <input type="hidden" name="group" value="<?php echo bp_get_current_group_id() ?>" />
    <?php endif; ?></td>
			  <td><?php do_action('em_template_events_search_form_ddm'); ?>
    <?php do_action('em_template_events_search_form_footer'); ?>
    <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('search_events'); ?>" />
    <input type="submit" value="<?php _e('Search','dbem'); ?>" class="em-events-search-submit" />
  </form></td>
		  </tr>
	  </tbody>
  </table>
    
   
    <span class="em-events-search-dates">
    
    
    
    
    </span> 
    
    
    <?php ?>
    
</div>
<script type="text/javascript">
	jQuery(document).ready( function($){
		$('.em-events-search-form select[name=country]').change( function(){
			$('.em-events-search select[name=state]').html('<option><?php _e('Loading...','dbem'); ?></option>');
			$('.em-events-search select[name=region]').html('<option><?php _e('Loading...','dbem'); ?></option>');
			var data = {
				_wpnonce : '<?php echo wp_create_nonce('search_states'); ?>',
				action : 'search_states',
				country : $(this).val(),
				return_html : true
			};
			$('.em-events-search select[name=state]').load( EM.ajaxurl, data );
			data.action = 'search_regions';
			data._wpnonce = '<?php echo wp_create_nonce('search_regions'); ?>';
			$('.em-events-search select[name=region]').load( EM.ajaxurl, data );
		});

		$('.em-events-search-form select[name=region]').change( function(){
			$('.em-events-search select[name=state]').html('<option><?php _e('Loading...','dbem'); ?></option>');
			var data = {
				_wpnonce : '<?php echo wp_create_nonce('search_states'); ?>',
				action : 'search_states',
				region : $(this).val(),
				country : $('.em-events-search-form select[name=country]').val(),
				return_html : true
			};
			$('.em-events-search select[name=state]').load( EM.ajaxurl, data );
		});
		
		//in order for this to work, you need the above classes to be present in your theme
		$('.em-events-search-form').submit(function(){
	    	if( this.search.value=='<?php echo $s_default; ?>'){
	    		this.search.value = '';
	    	}
	    	if( $('#em-wrapper .em-events-list').length == 1 ){
				$(this).ajaxSubmit({
					url : EM.ajaxurl,
				    data : {
						_wpnonce : '<?php echo wp_create_nonce('search_states'); ?>',
						action : 'search_states',
						country : $(this).val(),
						return_html : true
					},
				    beforeSubmit: function(form) {
						$('.em-events-search-form :submit').val('<?php _e('Searching...','dbem'); ?>');
				    },
				    success : function(responseText) {
						$('.em-events-search-form :submit').val('<?php _e('Search','dbem'); ?>');
						$('#em-wrapper .em-events-list').replaceWith(responseText);
				    }
				});
	    	} 
		});
	});	
</script>