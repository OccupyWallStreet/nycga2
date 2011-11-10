<?php
add_action('wp_print_scripts', 'bpge_js_all');
function bpge_js_all() {
    global $bp;
    $bpge = bp_get_option('bpge');
    /*
    $bp->action_variable[0] = extras
    $bp->action_variable[1] = fields | pages | fields-manage | pages-manage
    */
    if (is_admin())
        wp_enqueue_script('BPGE_ADMIN_JS', WP_PLUGIN_URL.'/buddypress-groups-extras/_inc/admin-scripts.js', array('jquery') );
        
    if ( $bp->current_component == bp_get_groups_root_slug() && $bp->is_single_item && 'admin' == $bp->current_action && $bp->action_variables[0] == 'extras' ){
        wp_enqueue_script('BPGE_EXTRA_JS', WP_PLUGIN_URL.'/buddypress-groups-extras/_inc/extra-scripts.js', array('jquery') );
        // localize js string
        add_action('wp_head', 'bpge_js_localize', 5);
        wp_enqueue_script('jquery-ui-sortable');
    }

    if ($bpge['re'] == 1 && $bp->current_component == bp_get_groups_root_slug() && $bp->is_single_item && 'admin' == $bp->current_action && $bp->action_variables[0] == 'extras' && $bp->action_variables[1] == 'pages-manage' ){
        wp_enqueue_script('tiny_mce', bp_get_root_domain() . '/wp-includes/js/tinymce/tiny_mce.js', false, '20110720');
        add_action('wp_head','bpge_js_richeditor');
    }
    
}

function bpge_js_richeditor(){
   global $bp, $wpdb;
   $language = ( '' == get_locale() ) ? 'en' : strtolower( substr(get_locale(), 0, 2) );

    if ( 'en' != $locale )
        include_once(ABSPATH . WPINC . '/js/tinymce/langs/wp-langs.php');
    echo "<script type='text/javascript'>\n$lang\n</script>\n";

    $options['buttons'] = 'bold,italic,underline,|,blockquote,|,bullist,numlist,|,undo,redo,|,indent,outdent,|,link,unlink,|,justifyleft,justifycenter,justifyright,|,forecolor,backcolor,|,image,hr,|,removeformat,code';

    $initArray = array (
        'mode' => 'exact',
        'elements' => 'post_content',
        'theme' => 'advanced',
        'theme_advanced_buttons1' => $options['buttons'],
        'theme_advanced_buttons2' => "",
        'theme_advanced_buttons3' => "",
        'theme_advanced_toolbar_location' => "top",
        'theme_advanced_toolbar_align' => "left",
        'theme_advanced_statusbar_location' => 'bottom',
        'theme_advanced_resizing' => 'true',
        'theme_advanced_resize_horizontal' => false,
        'theme_advanced_disable' => '',
        'force_p_newlines' => false,
        'force_br_newlines' => true,
        'forced_root_block' => "p",
        'gecko_spellcheck' => true,
        'skin' => 'default',//or wp_theme|o2k7 for future
        'content_css' => '',
        'directionality' => 'ltr',// or rtl for future
        'save_callback' => "brstonewline",
        'entity_encoding' => "raw",
        'plugins' => '',
        //'extended_valid_elements' => "a[name|href|title],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style],blockquote[cite]",'.$le;
        'language' => $language,
    );

    $params = array();
    foreach ( $initArray as $k => $v )
        $params[] = $k . ':"' . $v . '"    ';
    $res = join(',', $params); ?>
    
    <script type="text/javascript">/* <![CDATA[ */
        function brstonewline(element_id, html, body) { html = html.replace(/<br\s*\/>/gi, "\n"); return html; }
        function insertHTML(html) { tinyMCE.execCommand("InsertContent",false, html); }

        tinyMCEPreInit = {
            base : "<?php echo bp_get_root_domain() ?>/wp-includes/js/tinymce",
            suffix : "",
            query : "ver=20110720",
            reInit : {<?php echo $res ?>},
            go : function() {
                var t = this, 
                sl = tinymce.ScriptLoader,
                ln = t.reInit.language,
                th = t.reInit.theme,
                pl = t.reInit.plugins;
                sl.markDone(t.base + '/langs/' + ln + '.js');
                sl.markDone(t.base + '/themes/' + th + '/langs/' + ln + '.js');
                sl.markDone(t.base + '/themes/' + th + '/langs/' + ln + '_dlg.js');
                tinymce.each(pl.split(','), function(n) {
                    if (n && n.charAt(0) != '-') {
                        sl.markDone(t.base + '/plugins/' + n + '/langs/' + ln + '.js');
                        sl.markDone(t.base + '/plugins/' + n + '/langs/' + ln + '_dlg.js');
                    }
                });
            },
            load_ext : function(url,lang) {
                var sl = tinymce.ScriptLoader;
                sl.markDone(url + '/langs/' + lang + '.js');
                sl.markDone(url + '/langs/' + lang + '_dlg.js');
            }
        };

        var subBtn = document.getElementById("submit");
        if (subBtn != null) {
            subBtn.onclick=function() {
                var inst = tinyMCE.getInstanceById("post_content");
                document.getElementById("post_content").value = inst.getContent();
                document.getElementById("group-settings-form").submit();
                return false;
            }
        }
        tinyMCEPreInit.go();
        tinyMCE.init(tinyMCEPreInit.reInit);
        /* ]]> */</script>
    <?php
}

function bpge_js_localize(){
    echo '<script type="text/javascript">
    var bpge = {
        enter_options: "'. __('Please enter options for this Field','bpge') .'",
        option_text: "'. __('Option','bpge') .'",
        remove_it: "'. __('Remove It','bpge') .'"
    };
    </script>';
}

add_action('wp_print_styles', 'bpge_css_all');
function bpge_css_all() {
    global $bp;
        
    if ( $bp->current_component == bp_get_groups_root_slug() && $bp->is_single_item ){
        if (file_exists(WP_PLUGIN_DIR.'/buddypress-groups-extras/_inc/extra-styles.css')){
            wp_enqueue_style('BPGE_EXTRA_CSS', WP_PLUGIN_URL.'/buddypress-groups-extras/_inc/extra-styles.css');
        }else{
            wp_enqueue_style('BPGE_EXTRA_CSS', WP_PLUGIN_URL.'/buddypress-groups-extras/_inc/extra-styles-dev.css');
        }
    }
        
}

add_action('admin_head', 'bpge_css_admin');
function bpge_css_admin(){
    global $post_type; 
    
    if ($_GET['post_type'] == 'gpages' || $post_type == 'gpages' || $_GET['page'] == 'bpge-admin') {
        echo "<link type='text/css' rel='stylesheet' href='" . WP_PLUGIN_URL.'/buddypress-groups-extras/_inc/admin-styles.css' . "' />";
    }
}
