function wpv_view_layout_meta_html() {
    jQuery('#wpv_layout_meta_html_admin_show').hide();
    jQuery('#wpv_layout_meta_html_admin_edit').show();
    
    // scroll the edit into view.
    jQuery('html, body').animate({
         scrollTop: jQuery("#wpv_layout_meta_html_admin_edit").offset().top
     }, 500);
    
}

function wpv_view_layout_meta_html_close() {
    jQuery('#wpv_layout_meta_html_admin_show').show();
    jQuery('#wpv_layout_meta_html_admin_edit').hide();

    jQuery('#wpv_layout_meta_html_content_error').hide();
    
}


jQuery(document).ready(function($){
    jQuery('#wpv_layout_meta_html_content').keyup(function(event) {
        jQuery('#wpv_layout_meta_html_notice').show();
        show_view_changed_message();

    });
});

function wpv_layout_meta_html_generate_new() {
    jQuery('#wpv_layout_meta_html_content_old').val(jQuery('#wpv_layout_meta_html_content').val());
    jQuery('#wpv_layout_meta_html_content_old_div').show();
    jQuery('#wpv_layout_meta_html_content_error').hide();
    on_generate_wpv_layout(true);
}

function wpv_layout_meta_html_old_dismiss() {
    jQuery('#wpv_layout_meta_html_content_old_div').hide();
}