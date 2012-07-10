<?php
/*
Plugin Name: Gravity Forms CSS Ready Class Selector
Plugin URI: http://themergency.com/plugins/gravity-forms-custom-post-types/
Description: Easily select a CSS "Ready Class" for your fields within Gravity Forms
Version: 1.0.1
Author: Brad Vincent
Author URI: http://themergency.com/
License: GPL2

------------------------------------------------------------------------
Copyright 2011 Themergency

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

// Include Gravity Forms
if (!class_exists('RGForms'))
  @include_once(WP_PLUGIN_DIR . '/gravityforms/gravityforms.php');
if (!class_exists('RGFormsModel'))
  @include_once(WP_PLUGIN_DIR . '/gravityforms/forms_model.php');
if (!class_exists('GFCommon'))
  @include_once(WP_PLUGIN_DIR . '/gravityforms/common.php'); 

@include_once "gfaddoncommon.php";  

add_action('init',  array('GFReadyClassesAddon', 'init'));
add_action('admin_notices', array('GFReadyClassesAddon', 'admin_warnings'));

if (!class_exists('GFReadyClassesAddon')) {

  class GFReadyClassesAddon {
    
    public static function init() {
      add_action('gform_editor_js', array('GFReadyClassesAddon', 'render_editor_js'));
    }
    
    public static function admin_warnings() {
      return GFAddonCommon::admin_warnings('Gravity Forms Ready Classes Addon', 'GFReadyClassesAddon', '1.5');
    }
    
    /*
     * render some custom JS to get the settings to work
     */
    function render_editor_js(){
    
      $btn_url = GFAddonCommon::get_base_url() . '/btn.png';
      
      $modal_html = "
              <div id='css_ready_modal'>
                <style>
                  #css_ready_modal h4 { margin-bottom:2px; }
                  .cssr_accordian { display:none; }
                  a.cssr_acc_link { font-weight:bold; display:block; padding:5px; text-decoration:none; text-align:left; background:#888; border:solid 1px #ddd; color:#fff; }
                  a.cssr_link { text-decoration:none; margin:2px; text-align:center; padding:3px; border:solid 1px #aaa; background:#eee; display:inline-block; }
                  a.cssr_link:hover { background:#ddd; }
                  ul.cssr_ul { margin:0; padding: 0; }
                  ul.cssr_ul li { margin:2px; padding: 0; }
                </style>              
                <strong>Select a CSS ready class</strong>
                <ul class='cssr_ul'>
                
                <li>
                  <a class='cssr_acc_link' href='#'>2 Columns</a>
                  <div class='cssr_accordian'>
                    <a class='cssr_link' style='width:180px' href='#' rel='gf_left_half' title='gf_left_half'>Left Half</a>
                    <a class='cssr_link' style='width:180px' href='#' rel='gf_right_half' title='gf_right_half'>Right Half</a>
                  </div>
                </li>
                
                <li>
                  <a class='cssr_acc_link' href='#'>3 Columns</a>
                  <div class='cssr_accordian'>
                    <a class='cssr_link' style='width:115px' href='#' rel='gf_left_third' title='gf_left_third'>Left Third</a>
                    <a class='cssr_link' style='width:115px' href='#' rel='gf_middle_third' title='gf_middle_third'>Middle Third</a>
                    <a class='cssr_link' style='width:115px' href='#' rel='gf_right_third' title='gf_right_third'>Right Third</a>
                  </div>
                </li>
                
                <li>
                  <a class='cssr_acc_link' href='#'>List Layout</a>
                  <div class='cssr_accordian'>                
                    <a class='cssr_link' style='width:115px' rel='gf_list_2col' title='gf_list_2col' href='#'>2 Column List</a>
                    <a class='cssr_link' style='width:115px' rel='gf_list_3col' title='gf_list_3col' href='#'>3 Column List</a>
                    <a class='cssr_link' style='width:115px' rel='gf_list_4col' title='gf_list_4col' href='#'>4 Column List</a>
                    <a class='cssr_link' style='width:115px' rel='gf_list_5col' title='gf_list_5col' href='#'>5 Column List</a>
                    <a class='cssr_link' style='width:115px' rel='gf_list_inline' title='gf_list_inline' href='#'>Inline List</a>
                  </div>
                </li>
                
                <li>
                  <a class='cssr_acc_link' href='#'>List Heights</a>
                  <div class='cssr_accordian'>                   
                    <a class='cssr_link' style='width:115px' rel='gf_list_height_25' title='gf_list_height_25' href='#'>25px Height</a>
                    <a class='cssr_link' style='width:115px' rel='gf_list_height_50' title='gf_list_height_50' href='#'>50px Height</a>
                    <a class='cssr_link' style='width:115px' rel='gf_list_height_75' title='gf_list_height_75' href='#'>75px Height</a>
                    <a class='cssr_link' style='width:115px' rel='gf_list_height_100' title='gf_list_height_100' href='#'>100px Height</a>
                    <a class='cssr_link' style='width:115px' rel='gf_list_height_125' title='gf_list_height_125' href='#'>125px Height</a>
                    <a class='cssr_link' style='width:115px' rel='gf_list_height_150' title='gf_list_height_150' href='#'>150px Height</a>
                  </div>
                </li>
                
                <li>
                  <a class='cssr_acc_link' href='#'>Others</a>
                  <div class='cssr_accordian'>                   
                    <a class='cssr_link' style='width:180px' rel='gf_scroll_text' title='gf_scroll_text' href='#'>Scrolling Paragraph Text</a>
                    <a class='cssr_link' style='width:180px' rel='gf_hide_ampm' title='gf_hide_ampm' href='#'>Hide Time am/pm</a>
                    <a class='cssr_link' style='width:180px' rel='gf_hide_charleft' title='gf_hide_charleft' href='#'>Hide Character Counter</a>
                  </div>
                </li>
              </ul>
              Click on one or more CSS ready classes to add them.<br /> 
              Double-clicking adds the class and closes this popup.<br />
              For more help with CSS ready classes, refer to <a href='http://www.gravityhelp.com/css-ready-classes-for-gravity-forms/' target='_blank'>this documentation</a>.
              ";
      
      ?>
      <script type='text/javascript'>
      
          function removeTokenFromInput(input, tokenPos,seperator) {
              var text = input.val();
              
              var tokens = text.split(seperator);
              var newText = '';
              for(i = 0; i < tokens.length; i++)
              {
                  if (tokens[i].replace(' ','').replace(seperator,'') == '')
                      { continue; }
                  if (i != tokenPos) {
                      newText += (tokens[i].trim()+seperator);
                  }
              }
              input.val(fixTokens(newText, seperator));
          }

          function addTokenToInput(input, tokenToAdd, seperator) {
              var text = input.val().trim();
              if (text == '') {
                  input.val(tokenToAdd);
              }
              else {
                  if (!tokenExists(input, tokenToAdd, seperator))
                      { input.val(fixTokens(text + seperator + tokenToAdd, seperator)); }
              }
          }

          function fixTokens(tokens, seperator) {
              var text = tokens.trim();
              var tokens = text.split(seperator);
              var newTokens = '';
              for(i = 0; i < tokens.length; i++)
              {
                  var token = tokens[i].trim().replace(seperator,'');
                  if (token == '') { continue; }
                  newTokens += (token + seperator);
              }
              return newTokens;
          }

          function tokenExists(input, tokenToCheck, seperator) {
              var text = input.val().trim();
              if (text == '') return false;
              var tokens = text.split(seperator);
              for(i = 0; i < tokens.length; i++)
              {
                  var token = tokens[i].trim().replace(seperator,'');
                  if (token == '') { continue; }
                  if (token == tokenToCheck) {
                      return true;
                  }
              }
              return false;
          }

          jQuery(document).bind("gform_load_field_settings", function(event, field, form){
            
            if (jQuery("#css_ready_selector").length == 0) {

              //add some html after the CSS Class Name input
              var $select_link = jQuery("<a id='css_ready_selector' href='#'><img src='<?php echo $btn_url; ?>' /></a>");
              
              var $modal = jQuery("<?php echo preg_replace( '/\s*[\r\n\t]+\s*/', '', $modal_html ); ?>").hide();
              
              jQuery(".css_class_setting").append($select_link).append($modal);
              
              $select_link.click(function(e) {
                e.preventDefault();
                var $m = jQuery("#css_ready_modal");
                
                $m.find(".cssr_acc_link").unbind("click").click(function(e) {
                  e.preventDefault();
                  jQuery('.cssr_accordian:visible').slideUp();
                  jQuery(this).parent("li:first").find(".cssr_accordian").slideDown();
                });
                
                var $links = $m.find(".cssr_link");
                
                $links.unbind("click").click(function(e) {
                  e.preventDefault();
                  var css = jQuery(this).attr("rel");
                  addTokenToInput(jQuery("#field_css_class"), css, ' ');
                  SetFieldProperty('cssClass', jQuery("#field_css_class").val().trim());
                });
                
                $links.unbind("dblclick").dblclick(function(e) {
                  e.preventDefault();
                  var css = jQuery(this).attr("rel");
                  addTokenToInput(jQuery("#field_css_class"), css, ' ');
                  SetFieldProperty('cssClass', jQuery("#field_css_class").val().trim());
                  jQuery.modal.close();
                });
                
                $m.modal({
                  onOpen: function (dialog) {
                    dialog.overlay.fadeIn(function () {
                      dialog.container.slideDown(function () {
                        dialog.data.fadeIn('slow');
                      });
                    });
                  }
                });
              });            
            
            }
            
          });

      </script>
      <?php
    }
  }
  
}
?>
