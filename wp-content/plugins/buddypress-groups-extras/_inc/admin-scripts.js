jQuery(document).ready(function(){

    /*
     * Predefined fields navigation
     */
    // show fields in a set
    jQuery('ul.sets a.display_fields').click(function(e){
        e.preventDefault();
        var field_id = jQuery(this).parent().parent().attr('id').split('_');field_id = field_id[1];
        jQuery('ul.sets li ul#fields_'+field_id).toggleClass('show');
    });
    
    // delete set of fields with all fields
    jQuery('ul.sets a.field_delete').click(function(e){
        e.preventDefault();
        var field_id = jQuery(this).parent().parent().attr('id').split('_');field_id = field_id[1];

        // @TODO : HERE WILL BE AJAX REQUEST TO DELETE THAT FIELD
        jQuery('ul.sets li#set_'+field_id).fadeOut('fast',function(){
            jQuery(this).remove();
        });
        
    });
    
    /*
     * Groups checkboxes
    */
    var status = jQuery('input[type=checkbox].bpge_allgroups').attr('checked');

    if ( status && status == 'checked'){
        jQuery('input[type=checkbox].bpge_allgroups').change( function(){
            jQuery('input[type=checkbox].bpge_groups').removeAttr('checked');
            jQuery('input[type=checkbox].bpge_allgroups').removeAttr('checked');
        });
    }

    if ( !status || status == ''){
        jQuery('input[type=checkbox].bpge_allgroups').change( function(){
            jQuery('input[type=checkbox].bpge_groups').attr('checked', 'checked');
            jQuery('input[type=checkbox].bpge_allgroups').attr('checked', 'checked');
        });
    }

    jQuery('input[type=checkbox].bpge_groups').change( function(){
        jQuery('input[type=checkbox].bpge_allgroups').removeAttr('checked');
    });


});
