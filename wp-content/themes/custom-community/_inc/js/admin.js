var $ = jQuery;
jQuery(document).ready(function(){
    jQuery('.dismiss-activation-message,.cc-rate-it .go-to-wordpress-repo').click(function(){
        var message_block = $('.cc-rate-it');
        send_ajax_option_update(message_block, 'dismiss_activation_message');
    });
    
    jQuery('.close').click(function(){
        var message_block = $('.slideshow_info');
        send_ajax_option_update(message_block, 'cc_dismiss_info_messages');
    });
    function send_ajax_option_update(message_block, action){
        jQuery.ajax({
            url : admin_params.ajax_url,
            type: 'post',
            data : {
                'action' : action, //'dismiss_activation_message',
                'value' : 'yes'
            },
            success : function(data){
                if(data){
                   message_block.hide() 
                }
            } 
        })
    }
    //Hide Site Width option
    if(admin_params.responsive == "1"){
        jQuery('#cap_website_width').parent().hide().prev().hide();
        jQuery('#cap_leftsidebar_width').hide().prev().hide().prev().hide();
        jQuery('#cap_rightsidebar_width').hide().prev().hide().prev().hide();
    }
    
    jQuery('#cap_posts_lists_style_taxonomy, #cap_posts_lists_style_dates, #cap_posts_lists_style_author').change(function(){
        var have_block_view = false;
        jQuery('#cap_posts_lists_style_taxonomy, #cap_posts_lists_style_dates, #cap_posts_lists_style_author').each(function(){
            if(jQuery(this).val() == admin_params.blog){
                have_block_view = true;
            }
        });
        if(have_block_view){
            jQuery('.blog-items').show();
        } else {
            jQuery('.blog-items').hide();
        }
    });
    jQuery('#cap_posts_lists_style_home').change(function(){
        var have_block_view = false;
        if(jQuery(this).val() == admin_params.blog){
            have_block_view = true;
        }
        if(have_block_view){
            jQuery('.blog-item-home, #cap_default_homepage_hide_avatar, #cap_default_homepage_last_posts, #cap_default_homepage_style, #cap_default_homepage_hide_date').show().parent().parent('p').show()
        } else {
            jQuery('.blog-item-home, #cap_default_homepage_hide_avatar, #cap_default_homepage_last_posts, #cap_default_homepage_style, #cap_default_homepage_hide_date').hide().parent().parent('p').hide()
        }
    });
    jQuery('#cap_posts_lists_style_taxonomy, #cap_posts_lists_style_dates, #cap_posts_lists_style_author, #cap_posts_lists_style_home').trigger('change');
    if(typeof CodeMirror != 'undefined'){
        var editor = CodeMirror.fromTextArea(document.getElementById("cap_overwrite_css"), {});
    }
    
    jQuery('#cap_overwrite_css').focus(function(){
        jQuery('#cap_overwrite_css').elastic();
    });
    
    jQuery('#cc_page_slider_post_type').live('blur', function(){
        var value = jQuery.trim(jQuery(this).val());
        if(value){
            var category_block = jQuery('#categories-set');
            if(jQuery(this).val() != 'post'){
                category_block.hide().find(':checkbox').attr('checked', false);
            } else {
                category_block.show()
            }
        }
    });
    
});
