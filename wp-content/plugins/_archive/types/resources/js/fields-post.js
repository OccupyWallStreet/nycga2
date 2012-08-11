jQuery(document).ready(function(){
    // Set active editor
    window.wpcfActiveEditor = false;
    jQuery('.wpcf-wysiwyg .editor_addon_wrapper .item, #postdivrich .editor_addon_wrapper .item').click(function(){
        window.wpcfActiveEditor = jQuery(this).parents('.wpcf-wysiwyg, #postdivrich').find('textarea').attr('id');
    });
});