/*
    Network Latest Posts TinyMCE Plugin
    Version 1.0
    Author L'Elite
    Author URI https://laelite.info/
 */
/*  Copyright 2012  L'Elite (email : opensource@laelite.info)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
(function() {
    // Set the plugin
    tinymce.create('tinymce.plugins.nlposts', {
        init : function(ed, url) {
            // Add this button to the TinyMCE editor
            ed.addButton('nlposts', {
                // Button title
                title : 'Network Latest Posts Shortcode',
                // Button image
                image : url+'/nlposts_button.png',
                onclick : function() {
                    // Window size
                    var width = jQuery(window).width(), height = jQuery(window).height(), W = ( 720 < width ) ? 720 : width, H = ( height > 600 ) ? 600 : height;
                    W = W - 80;
                    H = H - 84;
                    tb_show( 'Network Latest Posts Shortcode', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=nlposts-form' );
                    // Load form
                    jQuery(function(){
                        // Dynamic load
                        jQuery('#TB_ajaxContent').load(url+'/nlposts_shortcode_form.php');
                    });
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        }
    });
    // Run this stuff
    tinymce.PluginManager.add('nlposts', tinymce.plugins.nlposts);
})();