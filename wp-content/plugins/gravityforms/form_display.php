<?php

class GFFormDisplay{
    public static $submission = array();
    private static $init_scripts = array();

    const ON_PAGE_RENDER = 1;
    const ON_CONDITIONAL_LOGIC = 2;

    public static function process_form($form_id){

        //reading form metadata
        $form = RGFormsModel::get_form_meta($form_id);
        $form = RGFormsModel::add_default_properties($form);
        $lead = array();

        $field_values = RGForms::post("gform_field_values");

        $confirmation_message = "";

        $source_page_number = self::get_source_page($form_id);
        $page_number = $source_page_number;
        $target_page = self::get_target_page($form, $page_number, $field_values);

        //Loading files that have been uploaded to temp folder
        $files = GFCommon::json_decode(stripslashes(RGForms::post("gform_uploaded_files")));
        if(!is_array($files))
            $files = array();

        RGFormsModel::$uploaded_files[$form["id"]] = $files;

        $is_valid = true;

        //don't validate when going to previous page
        if(empty($target_page) || $target_page >= $page_number){
            $is_valid = self::validate($form, $field_values, $page_number);
        }

        //Upload files to temp folder when going to the next page or when submitting the form and it failed validation
        if( $target_page >= $page_number || ($target_page == 0 && !$is_valid) ){
            //Uploading files to temporary folder
            $files = self::upload_files($form, $files);
            RGFormsModel::$uploaded_files[$form["id"]] = $files;
        }

        // Load target page if it did not fail validation or if going to the previous page
        if($is_valid){
            $page_number = $target_page;
        }

        $confirmation = "";
        if($is_valid && $page_number == 0){
            $ajax = isset($_POST["gform_ajax"]);

            //adds honeypot field if configured
            if(rgar($form,"enableHoneypot"))
                $form["fields"][] = self::get_honeypot_field($form);

            $failed_honeypot = rgar($form,"enableHoneypot") && !self::validate_honeypot($form);

            if($failed_honeypot){
                //display confirmation but doesn't process the form when honeypot fails
                $confirmation = self::handle_confirmation($form, $lead, $ajax);
                $is_valid = false;
            }
            else{
                //pre submission action
                do_action("gform_pre_submission", $form);
                do_action("gform_pre_submission_{$form["id"]}", $form);

                //pre submission filter
                $form = apply_filters("gform_pre_submission_filter_{$form["id"]}", apply_filters("gform_pre_submission_filter", $form));

                //handle submission
                $confirmation = self::handle_submission($form, $lead, $ajax);

                //after submission hook
                do_action("gform_after_submission", $lead, $form);
                do_action("gform_after_submission_{$form["id"]}", $lead, $form);
            }

            if(is_array($confirmation) && isset($confirmation["redirect"])){
                header("Location: {$confirmation["redirect"]}");
                do_action("gform_post_submission", $lead, $form);
                do_action("gform_post_submission_{$form["id"]}", $lead, $form);

                exit;
            }
        }
        if(!isset(self::$submission[$form_id]))
            self::$submission[$form_id] = array();

        self::set_submission_if_null($form_id, "is_valid", $is_valid);
        self::set_submission_if_null($form_id, "form", $form);
        self::set_submission_if_null($form_id, "lead", $lead);
        self::set_submission_if_null($form_id, "confirmation_message", $confirmation);
        self::set_submission_if_null($form_id, "page_number", $page_number);
        self::set_submission_if_null($form_id, "source_page_number", $source_page_number);
    }

    private static function set_submission_if_null($form_id, $key, $val){
        if(!isset(self::$submission[$form_id][$key]))
            self::$submission[$form_id][$key] = $val;
    }

    private static function upload_files($form, $files){

        //Creating temp folder if it does not exist
        $target_path = RGFormsModel::get_upload_path($form["id"]) . "/tmp/";
        wp_mkdir_p($target_path);

        foreach($form["fields"] as $field){
            $input_name = "input_{$field["id"]}";

            //skip fields that are not file upload fields or that don't have a file to be uploaded or that have failed validation
            $input_type = RGFormsModel::get_input_type($field);
            if(!in_array($input_type, array("fileupload", "post_image")) || $field["failed_validation"] || empty($_FILES[$input_name]["name"])){
                GFCommon::log_debug("upload_files() - skipping field: {$field["label"]}({$field["id"]} - {$field["type"]})");
                continue;
            }

            $file_info = RGFormsModel::get_temp_filename($form["id"], $input_name);
            GFCommon::log_debug("upload_files() - temp file info: " . print_r($file_info, true));

            if($file_info && move_uploaded_file($_FILES[$input_name]['tmp_name'], $target_path . $file_info["temp_filename"])){
                $files[$input_name] = $file_info["uploaded_filename"];
                GFCommon::log_debug("upload_files() - file uploaded successfully:  {$file_info["uploaded_filename"]}");
            }
            else{
                GFCommon::log_error("upload_files() - file could not be uploaded: tmp_name: {$_FILES[$input_name]['tmp_name']} - target location: " . $target_path . $file_info["temp_filename"]);
            }
        }

        return $files;
    }

    public static function get_state($form, $field_values){
        $product_fields = array();
        foreach($form["fields"] as $field){
            if(GFCommon::is_product_field($field["type"]) || $field["type"] == "donation"){
                $value = RGFormsModel::get_field_value($field, $field_values, false);
                $value = self::default_if_empty($field, $value);

                switch($field["inputType"]){
                    case "calculation" :
                    case "singleproduct" :
                    case "hiddenproduct" :
                        $price = !is_array($value) || empty($value[$field["id"] . ".2"]) ? $field["basePrice"] : $value[$field["id"] . ".2"];
                        if(empty($price))
                            $price = 0;

                        $price = GFCommon::to_number($price);

                        $product_name = !is_array($value) || empty($value[$field["id"] . ".1"]) ? $field["label"] : $value[$field["id"] . ".1"];
                        $product_fields[$field["id"]. ".1"] = wp_hash($product_name);
                        $product_fields[$field["id"]. ".2"] = wp_hash($price);
                    break;

                    case "singleshipping" :
                        $price = !empty($value) ? $value : $field["basePrice"];
                        $price = GFCommon::to_number($price);

                        $product_fields[$field["id"]] = wp_hash($price);
                    break;
                    case "radio" :
                    case "select" :
                        $product_fields[$field["id"]] = array();
                        foreach($field["choices"] as $choice){
                            $field_value = !empty($choice["value"]) || rgar($field,"enableChoiceValue") ? $choice["value"] : $choice["text"];
                            if($field["enablePrice"])
                                $field_value .= "|" . GFCommon::to_number(rgar($choice,"price"));

                            $product_fields[$field["id"]][] = wp_hash($field_value);
                        }
                    break;
                    case "checkbox" :
                        $index = 1;
                        foreach($field["choices"] as $choice){
                            $field_value = !empty($choice["value"]) || $field["enableChoiceValue"] ? $choice["value"] : $choice["text"];
                            if($field["enablePrice"])
                                $field_value .= "|" . GFCommon::to_number($choice["price"]);

                            if($index % 10 == 0) //hack to skip numbers ending in 0. so that 5.1 doesn't conflict with 5.10
                                $index++;

                            $product_fields[$field["id"] . "." . $index++] = wp_hash($field_value);
                        }
                    break;

                }
            }
        }
        $hash = serialize($product_fields);
        $checksum = wp_hash(crc32($hash));
        return base64_encode(serialize(array($hash, $checksum)));
    }

    private static function has_pages($form){
        return GFCommon::has_pages($form);
    }

    private static function has_character_counter($form){

        if(!is_array($form["fields"]))
            return false;

        foreach($form["fields"] as $field){
            if(rgar($field, "maxLength") && !rgar($field, "inputMask"))
                return true;
        }

        return false;
    }


    private static function has_enhanced_dropdown($form){

        if(!is_array($form["fields"]))
            return false;

        foreach($form["fields"] as $field){
            if(in_array(RGFormsModel::get_input_type($field), array("select", "multiselect")) && rgar($field, "enableEnhancedUI"))
                return true;
        }

        return false;
    }

    private static function has_password_strength($form){

        if(!is_array($form["fields"]))
            return false;

        foreach($form["fields"] as $field){
            if($field["type"] == "password" && RGForms::get("passwordStrengthEnabled", $field))
                return true;
        }

        return false;
    }

    private static function has_other_choice($form){

        if(!is_array($form["fields"]))
            return false;

        foreach($form["fields"] as $field){
            if($field["type"] == "radio" && rgget("enableOtherChoice", $field))
                return true;
        }

        return false;
    }


    public static function get_target_page($form, $current_page, $field_values){
        $page_number = RGForms::post("gform_target_page_number_{$form["id"]}");
        $page_number = !is_numeric($page_number) ? 1 : $page_number;

        $direction = $page_number >= $current_page ? 1 : -1;

        //Finding next page that is not hidden by conditional logic
        while(RGFormsModel::is_page_hidden($form, $page_number, $field_values)){
            $page_number += $direction;
        }

        //If all following pages are hidden, submit the form
        if($page_number > self::get_max_page_number($form))
            $page_number = 0;

        return $page_number;
    }

    public static function get_source_page($form_id){
        $page_number = RGForms::post("gform_source_page_number_{$form_id}");
        return !is_numeric($page_number) ? 1 : $page_number;
    }

    public static function set_current_page($form_id, $page_number){
        self::$submission[$form_id]["page_number"] = $page_number;
    }

    public static function get_current_page($form_id){
        $page_number = isset(self::$submission[$form_id]) ? self::$submission[$form_id]["page_number"] : 1;
        return $page_number;
    }

    private static function is_page_active($form_id, $page_number){
        return intval(self::get_current_page($form_id)) == intval($page_number);
    }

    private static function get_limit_period_dates($period){
        if(empty($period))
            return array("start_date" => null, "end_date" => null);

        switch($period){
            case "day" :
                return array(
                    "start_date" => gmdate("Y-m-d"),
                    "end_date" => gmdate("Y-m-d"));
            break;

            case "week" :
                return array(
                    "start_date" => gmdate("Y-m-d", strtotime("last Monday")),
                    "end_date" => gmdate("Y-m-d", strtotime("next Sunday")));
            break;

            case "month" :
                $month_start = gmdate("Y-m-1");
                return array(
                    "start_date" => $month_start,
                    "end_date" => gmdate("Y-m-d", strtotime("{$month_start} +1 month - 1 hour")));
            break;

            case "year" :
                return array(
                    "start_date" => gmdate("Y-1-1"),
                    "end_date" => gmdate("Y-12-31"));
            break;
        }
    }

    public static function get_form($form_id, $display_title=true, $display_description=true, $force_display=false, $field_values=null, $ajax=false, $tabindex = 1){


        //looking up form id by form name
        if(!is_numeric($form_id))
            $form_id = RGFormsModel::get_form_id($form_id);

        //reading form metadata
        $form = RGFormsModel::get_form_meta($form_id, true);
        $form = RGFormsModel::add_default_properties($form);

        //disable ajax if form has a reCAPTCHA field (not supported).
        if($ajax && self::has_recaptcha_field($form))
            $ajax = false;

        $is_postback = false;
        $is_valid = true;
        $confirmation_message = "";
        $page_number = 1;

        //If form was submitted, read variables set during form submission procedure
        $submission_info = isset(self::$submission[$form_id]) ? self::$submission[$form_id] : false;
        if($submission_info){
            $is_postback = true;
            $is_valid = rgar($submission_info, "is_valid") || rgar($submission_info, "is_confirmation");
            $form = $submission_info["form"];
            $lead = $submission_info["lead"];
            $confirmation_message = rgget("confirmation_message", $submission_info);

            if($is_valid && !RGForms::get("is_confirmation", $submission_info)){

                if($submission_info["page_number"] == 0){
                    //post submission hook
                    do_action("gform_post_submission", $lead, $form);
                    do_action("gform_post_submission_{$form["id"]}", $lead, $form);
                }
                else{
                    //change page hook
                    do_action("gform_post_paging", $form, $submission_info["source_page_number"], $submission_info["page_number"]);
                    do_action("gform_post_paging_{$form["id"]}", $form, $submission_info["source_page_number"], $submission_info["page_number"]);
                }
            }
        }
        else if(!current_user_can("administrator")){
            RGFormsModel::insert_form_view($form_id, $_SERVER['REMOTE_ADDR']);
        }

        if(rgar($form,"enableHoneypot"))
            $form["fields"][] = self::get_honeypot_field($form);

        //Fired right before the form rendering process. Allow users to manipulate the form object before it gets displayed in the front end
        $form = apply_filters("gform_pre_render_$form_id", apply_filters("gform_pre_render", $form, $ajax), $ajax);

        if($form == null)
            return "<p>" . __("Oops! We could not locate your form.", "gravityforms") . "</p>";

        $has_pages = self::has_pages($form);

        //calling tab index filter
        GFCommon::$tab_index = apply_filters("gform_tabindex_{$form_id}", apply_filters("gform_tabindex", $tabindex, $form), $form);

        //Don't display inactive forms
        if(!$force_display && !$is_postback) {

            $form_info = RGFormsModel::get_form($form_id);
            if(!$form_info->is_active)
                return "";

            // If form requires login, check if user is logged in
            if(rgar($form, "requireLogin")) {
                if(!is_user_logged_in())
                    return empty($form["requireLoginMessage"]) ? "<p>" . __("Sorry. You must be logged in to view this form.", "gravityforms"). "</p>" : "<p>" . GFCommon::gform_do_shortcode($form["requireLoginMessage"]) . "</p>";
            }

        }

        // show the form regardless of the following validations when force display is set to true
        if(!$force_display) {

            $form_schedule_validation = self::validate_form_schedule($form);

            // if form schedule validation fails AND this is not a postback, display the validation error
            // if form schedule validation fails AND this is a postback, make sure is not a valid submission (enables display of confirmation message)
            if( ($form_schedule_validation && !$is_postback) || ($form_schedule_validation && $is_postback && !$is_valid) )
                return $form_schedule_validation;

            $entry_limit_validation = self::validate_entry_limit($form);

            // refer to form schedule condition notes above
            if( ($entry_limit_validation && !$is_postback) || ($entry_limit_validation && $is_postback && !$is_valid) )
                return $entry_limit_validation;

        }

        $form_string = "";

        //When called via a template, this will enqueue the proper scripts
        //When called via a shortcode, this will be ignored (too late to enqueue), but the scripts will be enqueued via the enqueue_scripts event
        self::enqueue_form_scripts($form, $ajax);

        if(empty($confirmation_message)){
            $wrapper_css_class = GFCommon::get_browser_class() . " gform_wrapper";

            if(!$is_valid)
                $wrapper_css_class .=" gform_validation_error";

            //Hidding entire form if conditional logic is on to prevent "hidden" fields from blinking. Form will be set to visible in the conditional_logic.php after the rules have been applied.
            $style = self::has_conditional_logic($form) ? "style='display:none'" : "";
            $custom_wrapper_css_class = !empty($form["cssClass"]) ? " {$form["cssClass"]}_wrapper": "";
            $form_string .= "
                <div class='{$wrapper_css_class}{$custom_wrapper_css_class}' id='gform_wrapper_$form_id' " . $style . ">";

            $action = add_query_arg(array());
            $default_anchor = $has_pages || $ajax ? true : false;
            $use_anchor = apply_filters("gform_confirmation_anchor_{$form["id"]}", apply_filters("gform_confirmation_anchor", $default_anchor));
            if($use_anchor !== false){
                $form_string .="<a id='gf_$form_id' name='gf_$form_id' class='gform_anchor' ></a>";
                $action .= "#gf_$form_id";
            }
            $target = $ajax ? "target='gform_ajax_frame_{$form_id}'" : "";

            $form_css_class = !empty($form["cssClass"]) ? "class='{$form["cssClass"]}'": "";

            $action = esc_attr($action);
            $form_string .= apply_filters("gform_form_tag_{$form_id}", apply_filters("gform_form_tag", "<form method='post' enctype='multipart/form-data' {$target} id='gform_{$form_id}' {$form_css_class} action='{$action}'>", $form), $form);

            if($display_title || $display_description){
                $form_string .= "
                        <div class='gform_heading'>";
                if($display_title){
                    $form_string .= "
                            <h3 class='gform_title'>" . $form['title'] . "</h3>";
                }
                if($display_description){
                    $form_string .= "
                            <span class='gform_description'>" . rgar($form,'description') ."</span>";
                }
                $form_string .= "
                        </div>";
            }

            $current_page = self::get_current_page($form_id);
            if($has_pages && !IS_ADMIN){
                $page_count = self::get_max_page_number($form);

                if($form["pagination"]["type"] == "percentage"){
                    $form_string .= self::get_progress_bar($form, $form_id,$confirmation_message);
                }
                else if($form["pagination"]["type"] == "steps"){
                    $form_string .="
                    <div id='gf_page_steps_{$form_id}' class='gf_page_steps'>";

                    for($i=0, $count = sizeof($form["pagination"]["pages"]); $i<$count; $i++){
                        $step_number = $i+1;
                        $active_class = $step_number == $current_page ? " gf_step_active" : "";
                        $first_class = $i==0 ? " gf_step_first" : "";
                        $last_class = $i+1 == $count ? " gf_step_last" : "";
                        $complete_class = $step_number < $current_page ? " gf_step_completed" : "";
                        $previous_class = $step_number + 1 == $current_page ? " gf_step_previous" : "";
                        $next_class = $step_number - 1 == $current_page ? " gf_step_next" : "";
                        $pending_class = $step_number > $current_page ? " gf_step_pending" : "";
                        $classes = "gf_step" . $active_class . $first_class . $last_class . $complete_class . $previous_class . $next_class . $pending_class;

                        $classes = GFCommon::trim_all($classes);

                        $form_string .="
                        <div id='gf_step_{$form_id}_{$step_number}' class='{$classes}'><span class='gf_step_number'>{$step_number}</span>&nbsp;{$form["pagination"]["pages"][$i]}</div>";
                    }

                    $form_string .="
                        <div class='gf_step_clear'></div>
                    </div>";
                }
            }


            if($is_postback && !$is_valid){
                $validation_message = "<div class='validation_error'>" . __("There was a problem with your submission.", "gravityforms") . " " . __("Errors have been highlighted below.", "gravityforms") . "</div>";
                $form_string .= apply_filters("gform_validation_message_{$form["id"]}", apply_filters("gform_validation_message", $validation_message, $form), $form);
            }

            $form_string .= "
                        <div class='gform_body'>";

            //add first page if this form has any page fields
            if($has_pages){
                $style = self::is_page_active($form_id, 1) ? "" : "style='display:none;'";
                $class = !empty($form["firstPageCssClass"]) ? " {$form["firstPageCssClass"]}" : "";
                $form_string .= "<div id='gform_page_{$form_id}_1' class='gform_page{$class}' {$style}>
                                    <div class='gform_page_fields'>";
            }

            $description_class = rgar($form,"descriptionPlacement") == "above" ? "description_above" : "description_below";

            $form_string .= "
                            <ul id='gform_fields_$form_id' class='gform_fields {$form['labelPlacement']} {$description_class}'>";

                            if(is_array($form['fields']))
                            {
                                foreach($form['fields'] as $field){
                                    $field["conditionalLogicFields"] = self::get_conditional_logic_fields($form, $field["id"]);
                                    $form_string .= self::get_field($field, RGFormsModel::get_field_value($field, $field_values), false, $form, $field_values);
                                }
                            }
            $form_string .= "
                            </ul>";

             if($has_pages){
                $previous_button = self::get_form_button($form["id"], "gform_previous_button_{$form["id"]}", $form["lastPageButton"], __("Previous", "gravityforms"), "button gform_previous_button", __("Previous Page", "gravityforms"), self::get_current_page($form_id) -1);
                $form_string .= "</div>" . self::gform_footer($form, "gform_page_footer " . $form['labelPlacement'], $ajax, $field_values, $previous_button, $display_title, $display_description, $is_postback) . "
                            </div>"; //closes gform_page
             }

             $form_string .= "</div>"; //closes gform_body

             //suppress form footer for multi-page forms (footer will be included on the last page
             if(!$has_pages)
                $form_string .= self::gform_footer($form, "gform_footer " . $form['labelPlacement'], $ajax, $field_values, "", $display_title, $display_description, $is_postback);

             $form_string .= "
                </form>
                </div>";

            if($ajax && $is_postback){
                global $wp_scripts;

                $form_string = "<!DOCTYPE html><html><head>" .
                                "<meta charset='UTF-8' /></head><body class='GF_AJAX_POSTBACK'>" . $form_string . "</body></html>";
            }

            if($ajax && !$is_postback){
                $spinner_url = apply_filters("gform_ajax_spinner_url_{$form_id}", apply_filters("gform_ajax_spinner_url", GFCommon::get_base_url() . "/images/spinner.gif", $form), $form);
                $scroll_position = array('default' => '', 'confirmation' => '');

                if($use_anchor !== false) {
                    $scroll_position['default'] = is_numeric($use_anchor) ? "jQuery(document).scrollTop(" . intval($use_anchor) . ");" : "jQuery(document).scrollTop(jQuery('#gform_wrapper_{$form_id}').offset().top);";
                    $scroll_position['confirmation'] = is_numeric($use_anchor) ? "jQuery(document).scrollTop(" . intval($use_anchor) . ");" : "jQuery(document).scrollTop(jQuery('#gforms_confirmation_message').offset().top);";
                }

                $form_string .="
                <iframe style='display:none;width:0px; height:0px;' src='about:blank' name='gform_ajax_frame_{$form_id}' id='gform_ajax_frame_{$form_id}'></iframe>
                <script type='text/javascript'>" . apply_filters("gform_cdata_open", "") . "" .
                    "function gformInitSpinner_{$form_id}(){" .
                        "jQuery('#gform_{$form_id}').submit(function(){" .
                            "jQuery('#gform_submit_button_{$form_id}').attr('disabled', true).after('<' + 'img id=\"gform_ajax_spinner_{$form_id}\"  class=\"gform_ajax_spinner\" src=\"{$spinner_url}\" alt=\"\" />');" .
                            "jQuery('#gform_wrapper_{$form_id} .gform_previous_button').attr('disabled', true); " .
                            "jQuery('#gform_wrapper_{$form_id} .gform_next_button').attr('disabled', true).after('<' + 'img id=\"gform_ajax_spinner_{$form_id}\"  class=\"gform_ajax_spinner\" src=\"{$spinner_url}\" alt=\"\" />');" .
                        "} );" .
                    "}" .
                    "jQuery(document).ready(function($){" .
                        "gformInitSpinner_{$form_id}();" .
                        "jQuery('#gform_ajax_frame_{$form_id}').load( function(){" .
                            "var contents = jQuery(this).contents().find('*').html();" .
                            "var is_postback = contents.indexOf('GF_AJAX_POSTBACK') >= 0;" .
                            "if(!is_postback){return;}" .
                            "var form_content = jQuery(this).contents().find('#gform_wrapper_{$form_id}');" .
                            "var is_redirect = contents.indexOf('gformRedirect(){') >= 0;".
                            "jQuery('#gform_submit_button_{$form_id}').removeAttr('disabled');" .
                            "if(form_content.length > 0){" .
                                "jQuery('#gform_wrapper_{$form_id}').html(form_content.html());" .
                                "{$scroll_position['default']}" .
                                "if(window['gformInitDatepicker']) {gformInitDatepicker();}" .
                                "if(window['gformInitPriceFields']) {gformInitPriceFields();}" .
                                "var current_page = jQuery('#gform_source_page_number_{$form_id}').val();".
                                "gformInitSpinner_{$form_id}();" .
                                "jQuery(document).trigger('gform_page_loaded', [{$form_id}, current_page]);" .
                            "}" .
                            "else if(!is_redirect){" .
                                "var confirmation_content = jQuery(this).contents().find('#gforms_confirmation_message').html();" .
                                "if(!confirmation_content){".
                                    "confirmation_content = contents;".
                                "}" .
                                "setTimeout(function(){" .
                                    "jQuery('#gform_wrapper_{$form_id}').replaceWith('<' + 'div id=\'gforms_confirmation_message\' class=\'gform_confirmation_message_{$form_id}\'' + '>' + confirmation_content + '<' + '/div' + '>');" .
                                    "{$scroll_position['confirmation']}" .
                                    "jQuery(document).trigger('gform_confirmation_loaded', [{$form_id}]);" .
                                "}, 50);" .
                            "}" .
                            "else{" .
                                "jQuery('#gform_{$form_id}').append(contents);" .
                                "if(window['gformRedirect']) gformRedirect();" .
                            "}" .
                            "jQuery(document).trigger('gform_post_render', [{$form_id}, current_page]);" .
                        "} );" .
                    "} );" . apply_filters("gform_cdata_close", "") . "</script>";
            }

            $is_first_load = !$is_postback;

            if((!$ajax || $is_first_load)) {

                self::register_form_init_scripts($form, $field_values);

                if(apply_filters("gform_init_scripts_footer", false)){
                    add_action("wp_footer",            create_function('', 'GFFormDisplay::footer_init_scripts(' . $form['id'] . ');'), 20);
                    add_action("gform_preview_footer", create_function('', 'GFFormDisplay::footer_init_scripts(' . $form['id'] . ');'));
                }
                else{
                    $form_string .= self::get_form_init_scripts($form);
                    $form_string .= "<script type='text/javascript'>" . apply_filters("gform_cdata_open", "") . " jQuery(document).ready(function(){jQuery(document).trigger('gform_post_render', [{$form_id}, {$current_page}]) } ); " . apply_filters("gform_cdata_close", "") . "</script>";
                }
            }

            return apply_filters('gform_get_form_filter',$form_string);
        }
        else{
            $progress_confirmation = "";

			//check admin setting for whether the progress bar should start at zero
        	$start_at_zero = rgars($form, "pagination/display_progressbar_on_confirmation");
			//check for filter
            $start_at_zero = apply_filters("gform_progressbar_start_at_zero", $start_at_zero, $form);

            //show progress bar on confirmation
            if($start_at_zero && $has_pages && !IS_ADMIN && ($form["confirmation"]["type"] == "message" && $form["pagination"]["type"] == "percentage") && $form["pagination"]["display_progressbar_on_confirmation"])
            {
                $progress_confirmation = self::get_progress_bar($form, $form_id,$confirmation_message);
                if($ajax)
                {
                    $progress_confirmation = "<!DOCTYPE html><html><head><meta charset='UTF-8' /></head><body class='GF_AJAX_POSTBACK'>" . $progress_confirmation . $confirmation_message . "</body></html>";
                }
                else
                {
                 	$progress_confirmation = $progress_confirmation . $confirmation_message;
                }
            }
            else
            {
                //return regular confirmation message
                if($ajax)
                {
                    $progress_confirmation = "<!DOCTYPE html><html><head><meta charset='UTF-8' /></head><body class='GF_AJAX_POSTBACK'>" . $confirmation_message . "</body></html>";
                }
                else
                {
                 $progress_confirmation = $confirmation_message;
                }
            }

            return $progress_confirmation;
        }
    }

    public static function footer_init_scripts($form_id){
        global $_init_forms;

        $form = RGFormsModel::get_form_meta($form_id);
        $form_string = self::get_form_init_scripts($form);
        $current_page = self::get_current_page($form_id);
        $form_string .= "<script type='text/javascript'>" . apply_filters("gform_cdata_open", "") . " jQuery(document).ready(function(){jQuery(document).trigger('gform_post_render', [{$form_id}, {$current_page}]) } ); " . apply_filters("gform_cdata_close", "") . "</script>";

        if(!isset($_init_forms[$form_id])){
            echo $form_string;
            if(!is_array($_init_forms))
                $_init_forms = array();

            $_init_forms[$form_id] = true;
        }
    }

    public static function add_init_script($form_id, $script_name, $location, $script){
        $key = $script_name . "_" . $location;

        if(!isset(self::$init_scripts[$form_id]))
            self::$init_scripts[$form_id] = array();

        //add script if it hasn't been added before
        if(!array_key_exists($key, self::$init_scripts[$form_id]))
            self::$init_scripts[$form_id][$key] = array("location" => $location, "script" => $script);
    }

    private static function get_form_button($form_id, $button_input_id, $button, $default_text, $class, $alt, $target_page_number){
        $tabindex = GFCommon::get_tabindex();
        $input_type='submit';
        $onclick="";
        if(!empty($target_page_number)){
            $onclick = "onclick='jQuery(\"#gform_target_page_number_{$form_id}\").val(\"{$target_page_number}\"); jQuery(\"#gform_{$form_id}\").trigger(\"submit\",[true]); '";
            $input_type='button';
        }

        if($button["type"] == "text" || empty($button["imageUrl"])){
            $button_text = !empty($button["text"]) ? $button["text"] : $default_text;
            $button_input = "<input type='{$input_type}' id='{$button_input_id}' class='{$class}' value='" . esc_attr($button_text) . "' {$tabindex} {$onclick}/>";
        }
        else{
            $imageUrl = $button["imageUrl"];
            $button_input= "<input type='image' src='{$imageUrl}' id='{$button_input_id}' class='gform_image_button' alt='{$alt}' {$tabindex} {$onclick}/>";
        }
        return $button_input;
    }

    private static function gform_footer($form, $class, $ajax, $field_values, $previous_button, $display_title, $display_description){
        $form_id = $form["id"];
        $footer = "
        <div class='" . $class ."'>";
        $button_input = self::get_form_button($form["id"], "gform_submit_button_{$form["id"]}", $form["button"], __("Submit", "gravityforms"), "button gform_button", __("Submit", "gravityforms"), 0);
        $button_input = apply_filters("gform_submit_button", $button_input, $form);
        $button_input = apply_filters("gform_submit_button_{$form_id}", $button_input, $form);
        $footer .= $previous_button . " " . $button_input;

        if($ajax){
            $footer .= "<input type='hidden' name='gform_ajax' value='" . esc_attr("form_id={$form_id}&amp;title={$display_title}&amp;description={$display_description}") . "' />";
        }

        $current_page = self::get_current_page($form_id);
        $next_page = $current_page + 1;
        $next_page = $next_page > self::get_max_page_number($form) ? 0 : $next_page;
        $field_values_str = is_array($field_values) ? http_build_query($field_values) : "";
        $files_input = "";
        if(!empty(RGFormsModel::$uploaded_files[$form_id])){
            $files = GFCommon::json_encode(RGFormsModel::$uploaded_files[$form_id]);
            $files_input = "<input type='hidden' name='gform_uploaded_files' id='gform_uploaded_files_{$form_id}' value='" . str_replace("'", "&#039;", $files) . "' />";
        }

        $footer .="
            <input type='hidden' class='gform_hidden' name='is_submit_{$form_id}' value='1' />
            <input type='hidden' class='gform_hidden' name='gform_submit' value='{$form_id}' />
            <input type='hidden' class='gform_hidden' name='gform_unique_id' value='" . esc_attr(RGFormsModel::get_form_unique_id($form_id)) . "' />
            <input type='hidden' class='gform_hidden' name='state_{$form_id}' value='" . self::get_state($form, $field_values) . "' />
            <input type='hidden' class='gform_hidden' name='gform_target_page_number_{$form_id}' id='gform_target_page_number_{$form_id}' value='" . $next_page . "' />
            <input type='hidden' class='gform_hidden' name='gform_source_page_number_{$form_id}' id='gform_source_page_number_{$form_id}' value='" . $current_page . "' />
            <input type='hidden' name='gform_field_values' value='{$field_values_str}' />
            {$files_input}
        </div>";

        return $footer;
    }

    private static function get_max_page_number($form){
        $page_number = 0;
        foreach($form["fields"] as $field){
            if($field["type"] == "page"){
               $page_number++;
            }
        }
        return $page_number == 0 ? 0 : $page_number + 1;
    }


    private static function get_honeypot_field($form){
        $max_id = self::get_max_field_id($form);
        $labels = self::get_honeypot_labels();
        return array("type" => "honeypot", "label" => $labels[rand(0, 3)], "id" => $max_id + 1, "cssClass" => "gform_validation_container", "description" => "This field is for validation purposes and should be left unchanged.");
    }

    private static function get_max_field_id($form){
        $max = 0;
        foreach($form["fields"] as $field){
            if(floatval($field["id"]) > $max)
                $max = floatval($field["id"]);
        }
        return $max;
    }

    private static function get_honeypot_labels(){
        return array("Name", "Email", "Phone", "Comments");
    }

    public static function is_empty($field, $form_id=0){
        switch(RGFormsModel::get_input_type($field)){
            case "post_image" :
            case "fileupload" :
                $input_name = "input_" . $field["id"];

                $file_info = RGFormsModel::get_temp_filename($form_id, $input_name);
                return !$file_info && empty($_FILES[$input_name]['name']);

            case "list" :
                $value = rgpost("input_" . $field["id"]);
                if(is_array($value)){
                    //empty if all inputs are empty (for inputs with the same name)
                    foreach($value as $input){
                        if(strlen(trim($input)) > 0 )
                            return false;
                    }
                }

                return true;
            case 'singleproduct':
                $quantity_id = $field["id"] . ".3";
                $quantity = rgpost($quantity_id, $value);

        }

        if(is_array($field["inputs"]))
        {
            foreach($field["inputs"] as $input){
                $value = rgpost("input_" . str_replace('.', '_', $input["id"]));
                if(is_array($value) && !empty($value)){
                    return false;
                }

                $strlen = strlen(trim($value));
                if(!is_array($value) && strlen(trim($value)) > 0)
                    return false;
            }
            return true;
        }
        else{
            $value = rgpost("input_" . $field["id"]);
            if(is_array($value)){
                //empty if any of the inputs are empty (for inputs with the same name)
                foreach($value as $input){
                    if(strlen(trim($input)) <= 0 )
                        return true;
                }
                return false;
            }
            else if($field["enablePrice"]){
                list($label, $price) = explode("|", $value);
                $is_empty = (strlen(trim($price)) <= 0);
                return $is_empty;
            }
            else{
                $is_empty = (strlen(trim($value)) <= 0) || ($field["type"] == "post_category" && $value < 0);
                return $is_empty;
            }
        }
    }

    private static function clean_extensions($extensions){
        $count = sizeof($extensions);
        for($i=0; $i<$count; $i++){
            $extensions[$i] = str_replace(".", "",str_replace(" ", "", $extensions[$i]));
        }
        return $extensions;
    }

    private static function validate_range($field, $value){

        if( !GFCommon::is_numeric($value, rgar($field, "numberFormat")) )
            return false;

        $number = GFCommon::clean_number($value, rgar($field, "numberFormat"));
        if( (is_numeric($field["rangeMin"]) && $number < $field["rangeMin"]) ||
            (is_numeric($field["rangeMax"]) && $number > $field["rangeMax"])
        )
            return false;
        else
            return true;
    }

    private static function validate_honeypot($form){
        $honeypot_id = self::get_max_field_id($form);
        return rgempty("input_{$honeypot_id}");
    }

    public static function handle_submission($form, &$lead, $ajax=false){

        //creating entry in DB
        RGFormsModel::save_lead($form, $lead);

        //reading entry that was just saved
        $lead = RGFormsModel::get_lead($lead["id"]);

        do_action('gform_entry_created', $lead, $form);

        //if Akismet plugin is installed, run lead through Akismet and mark it as Spam when appropriate
        $is_spam = false;
        if(GFCommon::akismet_enabled($form['id']) && GFCommon::is_akismet_spam($form, $lead)){
            $is_spam = true;
        }

        if(!$is_spam){
            GFCommon::create_post($form, $lead);
            //send auto-responder and notification emails
            self::send_emails($form, $lead);
        }
        else{
            //marking entry as spam
            RGFormsModel::update_lead_property($lead["id"], "status", "spam", false, true);
            $lead["status"] = "spam";
        }

        //display confirmation message or redirect to confirmation page
        return self::handle_confirmation($form, $lead, $ajax);
    }

    public static function handle_confirmation($form, $lead, $ajax=false){

        if($form["confirmation"]["type"] == "message"){
            $default_anchor = self::has_pages($form) ? 1 : 0;
            $anchor = apply_filters("gform_confirmation_anchor_{$form["id"]}", apply_filters("gform_confirmation_anchor", $default_anchor)) ? "<a id='gf_{$form["id"]}' name='gf_{$form["id"]}' class='gform_anchor' ></a>" : "";
            $nl2br = rgar($form["confirmation"],"disableAutoformat") ? false : true;
            $confirmation = empty($form["confirmation"]["message"]) ? "{$anchor} " : "{$anchor}<div id='gforms_confirmation_message' class='gform_confirmation_message_{$form["id"]}'>" . GFCommon::replace_variables($form["confirmation"]["message"], $form, $lead, false, true, $nl2br) . "</div>";
        }
        else{
            if(!empty($form["confirmation"]["pageId"])){
                $url = get_permalink($form["confirmation"]["pageId"]);
            }
            else{
                $url = GFCommon::replace_variables(trim($form["confirmation"]["url"]), $form, $lead, false, true);
                $url_info = parse_url($url);
                $query_string = $url_info["query"];
                $dynamic_query = GFCommon::replace_variables(trim($form["confirmation"]["queryString"]), $form, $lead, true);
                $query_string .= empty($url_info["query"]) || empty($dynamic_query) ? $dynamic_query : "&" . $dynamic_query;

                if(!empty($url_info["fragment"]))
                    $query_string .= "#" . $url_info["fragment"];

                $url = $url_info["scheme"] . "://" . $url_info["host"];
                if(!empty($url_info["port"]))
                    $url .= ":{$url_info["port"]}";

                $url .= rgar($url_info,"path");
                if(!empty($query_string)){
                    $url .= "?{$query_string}";
                }

            }

            if(headers_sent() || $ajax){
                //Perform client side redirect for AJAX forms, of if headers have already been sent
                $confirmation = self::get_js_redirect_confirmation($url, $ajax);
            }
            else{
                $confirmation = array("redirect" => $url);
            }
        }

        $confirmation = apply_filters("gform_confirmation_{$form["id"]}", apply_filters("gform_confirmation", $confirmation, $form, $lead, $ajax), $form, $lead, $ajax);

        if(!is_array($confirmation)){
            $confirmation = GFCommon::gform_do_shortcode($confirmation); //enabling shortcodes
        }
        else if(headers_sent() || $ajax){
            //Perform client side redirect for AJAX forms, of if headers have already been sent
            $confirmation = self::get_js_redirect_confirmation($confirmation["redirect"], $ajax); //redirecting via client side
        }

        return $confirmation;
    }

    private static function get_js_redirect_confirmation($url, $ajax){
        $confirmation = "<script type=\"text/javascript\">" . apply_filters("gform_cdata_open", "") . " function gformRedirect(){document.location.href='$url';}";
        if(!$ajax)
            $confirmation .="gformRedirect();";

        $confirmation .= apply_filters("gform_cdata_close", "") . "</script>";

        return $confirmation;
    }

    public static function send_emails($form, $lead){
        $disable_user_notification = apply_filters("gform_disable_user_notification_{$form["id"]}", apply_filters("gform_disable_user_notification", false, $form, $lead), $form, $lead);
        if(!$disable_user_notification){
            GFCommon::send_user_notification($form, $lead);
        }

        $disable_admin_notification = apply_filters("gform_disable_admin_notification_{$form["id"]}", apply_filters("gform_disable_admin_notification", false, $form, $lead), $form, $lead);
        if(!$disable_admin_notification){
            GFCommon::send_admin_notification($form, $lead);
        }
    }

    public static function checkdate($month, $day, $year){
        if(empty($month) || !is_numeric($month) || empty($day) || !is_numeric($day) || empty($year) || !is_numeric($year) || strlen($year) != 4)
            return false;

        return checkdate($month, $day, $year);
    }

    public static function validate(&$form, $field_values, $page_number=0){

        // validate form schedule
        if(self::validate_form_schedule($form))
            return false;

        // validate entry limit
        if(self::validate_entry_limit($form))
            return false;

        foreach($form["fields"] as &$field){

            //If a page number is specified, only validates fields that are on current page
            if($page_number > 0 && $field["pageNumber"] != $page_number)
                continue;

            //ignore validation if field is hidden or admin only
            if(RGFormsModel::is_field_hidden($form, $field, $field_values) || $field["adminOnly"])
                continue;

            $value = RGFormsModel::get_field_value($field);

            //display error message if field is marked as required and the submitted value is empty
            if($field["isRequired"] && self::is_empty($field, $form["id"])){
                $field["failed_validation"] = true;
                $field["validation_message"] = empty($field["errorMessage"]) ? __("This field is required.", "gravityforms") : $field["errorMessage"];
            }
            //display error if field does not allow duplicates and the submitted value already exists
            else if($field["noDuplicates"] && RGFormsModel::is_duplicate($form["id"], $field, $value)){
                $field["failed_validation"] = true;

                $input_type = RGFormsModel::get_input_type($field);
                switch($input_type){
                    case "date" :
                        $default_message = __("This date has already been taken. Please select a new date.", "gravityforms");
                    break;

                    default:
                        $default_message = is_array($value) ? __("This field requires an unique entry and the values you entered have been already been used.", "gravityforms") :
                                                              sprintf(__("This field requires an unique entry and '%s' has already been used", "gravityforms"), $value);
                    break;
                }

                $field["validation_message"] =  apply_filters("gform_duplicate_message_{$form["id"]}", apply_filters("gform_duplicate_message", $default_message, $form), $form);

            }
            else{
                if(self::failed_state_validation($form["id"], $field, $value)){
                    $field["failed_validation"] = true;
                    $field["validation_message"] = in_array($field["inputType"], array("singleproduct", "singleshipping", "hiddenproduct")) ? __("Please enter a valid value.", "gravityforms") : __("Invalid selection. Please select one of the available choices.", "gravityforms");
                }
                else{
                    switch(RGFormsModel::get_input_type($field)){
                        case "password" :
                            $password =  $_POST["input_" . $field["id"]];
                            $confirm = $_POST["input_" . $field["id"] . "_2"];
                            if($password != $confirm){
                                $field["failed_validation"] = true;
                                $field["validation_message"] = __("Your passwords do not match.", "gravityforms");
                            }
                            else if(rgar($field,"passwordStrengthEnabled") && !rgempty("minPasswordStrength",$field) && !empty($password)){
                                $strength = $_POST["input_" . $field["id"] . "_strength"];

                                $levels = array("short" => 1, "bad" => 2, "good" => 3, "strong" => 4);
                                if($levels[$strength] < $levels[$field["minPasswordStrength"]]){
                                    $field["failed_validation"] = true;
                                    $field["validation_message"] = empty($field["errorMessage"]) ? __("Your password does not meet the required strength. <br/>Hint: To make it stronger, use upper and lower case letters, numbers and symbols like ! \" ? $ % ^ & ).", "gravityforms") : $field["errorMessage"];
                                }
                            }
                        break;

                        case "name" :
                            if($field["isRequired"] && $field["nameFormat"] != "simple")
                            {
                                $first = $_POST["input_" . $field["id"] . "_3"];
                                $last = $_POST["input_" . $field["id"] . "_6"];
                                if(empty($first) || empty($last)){
                                    $field["failed_validation"] = true;
                                    $field["validation_message"] = empty($field["errorMessage"]) ? __("This field is required. Please enter the first and last name.", "gravityforms") : $field["errorMessage"];
                                }
                            }

                        break;

                        case "address" :
                            if($field["isRequired"])
                            {
                                $street = $_POST["input_" . $field["id"] . "_1"];
                                $city = $_POST["input_" . $field["id"] . "_3"];
                                $state = $_POST["input_" . $field["id"] . "_4"];
                                $zip = $_POST["input_" . $field["id"] . "_5"];
                                $country = $_POST["input_" . $field["id"] . "_6"];
                                if(empty($street) || empty($city) || empty($zip) || (empty($state) && !$field["hideState"] ) || (empty($country) && !$field["hideCountry"])){
                                    $field["failed_validation"] = true;
                                    $field["validation_message"] = empty($field["errorMessage"]) ? __("This field is required. Please enter a complete address.", "gravityforms") : $field["errorMessage"];
                                }
                            }

                        break;

                        case "creditcard" :
                            $card_number = rgpost("input_" . $field["id"] . "_1");
                            $expiration_date = rgpost("input_" . $field["id"] . "_2");
                            $security_code = rgpost("input_" . $field["id"] . "_3");

                            if(rgar($field, "isRequired") && (empty($card_number) || empty($security_code) || empty($expiration_date[0]) || empty($expiration_date[1]))){
                                $field["failed_validation"] = true;
                                $field["validation_message"] = empty($field["errorMessage"]) ? __("Please enter your credit card information.", "gravityforms") : $field["errorMessage"];
                            }
                            else if(!empty($card_number)){
                                $card_type = GFCommon::get_card_type($card_number);
                                $security_code = rgpost("input_" . $field["id"] . "_3");

                                if(empty($security_code)){
                                    $field["failed_validation"] = true;
                                    $field["validation_message"] = __("Please enter your card's security code.", "gravityforms");
                                }
                                else if(!$card_type){
                                    $field["failed_validation"] = true;
                                    $field["validation_message"] = __("Invalid credit card number.", "gravityforms");
                                }
                                else if(!GFCommon::is_card_supported($field, $card_type["slug"])){
                                    $field["failed_validation"] = true;
                                    $field["validation_message"] = $card_type["name"] . " " . __("is not supported. Please enter one of the supported credit cards.", "gravityforms");
                                }
                            }
                        break;

                        case "email" :
                            if(!rgblank($value) && !GFCommon::is_valid_email($value)){
                                $field["failed_validation"] = true;
                                $field["validation_message"] = empty($field["errorMessage"]) ? __("Please enter a valid email address.", "gravityforms"): $field["errorMessage"];
                            }
                            else if(rgget("emailConfirmEnabled", $field) && !empty($value)){
                                $confirm = rgpost("input_" . $field["id"] . "_2");
                                if($confirm != $value){
                                    $field["failed_validation"] = true;
                                    $field["validation_message"] = __("Your emails do not match.", "gravityforms");
                                }
                            }
                        break;

                        case "donation" :
                        case "price" :

                            if(!class_exists("RGCurrency"))
                                require_once("currency.php");

                            $donation = GFCommon::to_number($value);
                            if(!rgblank($value) && ($donation === false || $donation < 0)){
                               $field["failed_validation"] = true;
                               $field["validation_message"] = empty($field["errorMessage"]) ? __("Please enter a valid amount.", "gravityforms") : $field["errorMessage"];

                            }
                        break;

                        case "number" :

                            if(!rgblank($value) && !self::validate_range($field, $value) && !GFCommon::has_field_calculation($field)) {
                                $field["failed_validation"] = true;
                                $field["validation_message"] = empty($field["errorMessage"]) ? GFCommon::get_range_message($field) : $field["errorMessage"];
                            }
                            else if($field["type"] == "quantity" && intval($value) != $value){
                                $field["failed_validation"] = true;
                                $field["validation_message"] = empty($field["errorMessage"]) ? __("Please enter a valid quantity. Quantity cannot contain decimals.", "gravityforms") : $field["errorMessage"];
                            }

                        break;

                        case "phone" :

                            $regex = '/^\D?(\d{3})\D?\D?(\d{3})\D?(\d{4})$/';
                            if($field["phoneFormat"] == "standard" && !empty($value) && !preg_match($regex, $value)){
                                $field["failed_validation"] = true;
                                if(!empty($field["errorMessage"]))
                                    $field["validation_message"] = $field["errorMessage"];
                            }
                        break;

                        case "date" :
                            if(is_array($value) && rgempty(0, $value) && rgempty(1, $value)&& rgempty(2, $value))
                                $value = null;

                            if(!empty($value)){
                                $format = empty($field["dateFormat"]) ? "mdy" : $field["dateFormat"];
                                $date = GFCommon::parse_date($value, $format);

                                if(empty($date) || !self::checkdate($date["month"], $date["day"], $date["year"])){
                                    $field["failed_validation"] = true;
                                    $format_name = "";
                                    switch($format){
                                        case "mdy" :
                                            $format_name = "mm/dd/yyyy";
                                        break;
                                        case "dmy" :
                                            $format_name = "dd/mm/yyyy";
                                        break;
                                        case "dmy_dash" :
                                            $format_name = "dd-mm-yyyy";
                                        break;
                                        case "dmy_dot" :
                                            $format_name = "dd.mm.yyyy";
                                        break;
                                        case "ymd_slash" :
                                            $format_name = "yyyy/mm/dd";
                                        break;
                                        case "ymd_dash" :
                                            $format_name = "yyyy-mm-dd";
                                        break;
                                        case "ymd_dot" :
                                            $format_name = "yyyy.mm.dd";
                                        break;
                                    }
                                    $message = $field["dateType"] == "datepicker" ? sprintf(__("Please enter a valid date in the format (%s).", "gravityforms"), $format_name) : __("Please enter a valid date.", "gravityforms");
                                    $field["validation_message"] = empty($field["errorMessage"]) ? $message : $field["errorMessage"];
                                }
                            }
                        break;

                        case "time" :

                            //create variable values if time came in one field
                            if(!is_array($value) && !empty($value)){
                                preg_match('/^(\d*):(\d*) ?(.*)$/', $value, $matches);
                                $value = array();
                                $value[0] = $matches[1];
                                $value[1] = $matches[2];
                            }

                            $hour = $value[0];
                            $minute = $value[1];

                            if(empty($hour) && empty($minute))
                                break;

                            $is_valid_format = is_numeric($hour) && is_numeric($minute);

                            $min_hour = rgar($field, "timeFormat") == "24" ? 0 : 1;
                            $max_hour = rgar($field, "timeFormat") == "24" ? 23 : 12;

                            if(!$is_valid_format || $hour < $min_hour || $hour > $max_hour || $minute < 0 || $minute >= 60)
                            {
                                $field["failed_validation"] = true;
                                $field["validation_message"] = empty($field["errorMessage"]) ? __("Please enter a valid time." , "gravityforms"): $field["errorMessage"];
                            }
                        break;

                        case "website" :
                            if(empty($value) || $value == "http://"){
                                $value = "";
                                if($field["isRequired"]){
                                    $field["failed_validation"] = true;
                                    $field["validation_message"] = empty($field["errorMessage"]) ? __("This field is required.", "gravityforms") : $field["errorMessage"];
                                }
                            }

                            if(!empty($value) && !GFCommon::is_valid_url($value)){
                                $field["failed_validation"] = true;
                                $field["validation_message"] = empty($field["errorMessage"]) ? __("Please enter a valid Website URL (i.e. http://www.gravityforms.com).", "gravityforms") : $field["errorMessage"];
                            }
                        break;

                        case "captcha" :
                            switch($field["captchaType"]){
                                case "simple_captcha" :
                                    if(class_exists("ReallySimpleCaptcha")){
                                        $prefix = $_POST["input_captcha_prefix_{$field["id"]}"];
                                        $captcha_obj = GFCommon::get_simple_captcha();

                                        if(!$captcha_obj->check($prefix, str_replace(" ", "", $value))){
                                            $field["failed_validation"] = true;
                                            $field["validation_message"] = empty($field["errorMessage"]) ? __("The CAPTCHA wasn't entered correctly. Go back and try it again.", "gravityforms") : $field["errorMessage"];
                                        }

                                        //removes old files in captcha folder (older than 1 hour);
                                        $captcha_obj->cleanup();
                                    }
                                break;

                                case "math" :
                                    $prefixes = explode(",", $_POST["input_captcha_prefix_{$field["id"]}"]);
                                    $captcha_obj = GFCommon::get_simple_captcha();

                                    //finding first number
                                    $first = 0;
                                    for($first=0; $first<10; $first++){
                                        if($captcha_obj->check($prefixes[0], $first))
                                            break;
                                    }

                                    //finding second number
                                    $second = 0;
                                    for($second=0; $second<10; $second++){
                                        if($captcha_obj->check($prefixes[2], $second))
                                            break;
                                    }

                                    //if it is a +, perform the sum
                                    if($captcha_obj->check($prefixes[1], "+"))
                                        $result = $first + $second;
                                    else
                                        $result = $first - $second;


                                    if(intval($result) != intval($value)){
                                        $field["failed_validation"] = true;
                                        $field["validation_message"] = empty($field["errorMessage"]) ? __("The CAPTCHA wasn't entered correctly. Go back and try it again.", "gravityforms") : $field["errorMessage"];
                                    }

                                    //removes old files in captcha folder (older than 1 hour);
                                    $captcha_obj->cleanup();

                                break;

                                default :
                                    if(!function_exists("recaptcha_get_html")){
                                        require_once(GFCommon::get_base_path() . '/recaptchalib.php');
                                    }

                                    $privatekey = get_option("rg_gforms_captcha_private_key");
                                    $resp = recaptcha_check_answer ($privatekey,
                                            $_SERVER["REMOTE_ADDR"],
                                            $_POST["recaptcha_challenge_field"],
                                            $_POST["recaptcha_response_field"]);

                                    if (!$resp->is_valid) {
                                        $field["failed_validation"] = true;
                                        $field["validation_message"] = empty($field["errorMessage"]) ? __("The reCAPTCHA wasn't entered correctly. Go back and try it again.", "gravityforms") : $field["errorMessage"];
                                    }
                            }
                        break;

                        case "fileupload" :
                        case "post_image" :
                            $info = pathinfo($_FILES["input_" . $field["id"]]["name"]);
                            $allowedExtensions = self::clean_extensions(explode(",", strtolower($field["allowedExtensions"])));
                            $extension = strtolower(rgget("extension",$info));

                            if(empty($field["allowedExtensions"]) && in_array($extension, array("php", "asp", "exe", "com", "htaccess"))){
                                $field["failed_validation"] = true;
                                $field["validation_message"] = empty($field["errorMessage"]) ? __("The uploaded file type is not allowed.", "gravityforms")  : $field["errorMessage"];
                            }
                            else if(!empty($field["allowedExtensions"]) && !empty($info["basename"]) && !in_array($extension, $allowedExtensions)){
                                $field["failed_validation"] = true;
                                $field["validation_message"] = empty($field["errorMessage"]) ? sprintf(__("The uploaded file type is not allowed. Must be one of the following: %s", "gravityforms"), strtolower($field["allowedExtensions"]) )  : $field["errorMessage"];
                            }
                        break;

                        case "calculation" :
                        case "singleproduct" :
                        case "hiddenproduct" :
                            $quantity_id = $field["id"] . ".3";
                            $quantity = rgget($quantity_id, $value);

                            if($field["isRequired"] && rgblank($quantity) && !rgar($field, "disableQuantity") ){
                                $field["failed_validation"] = true;
                                $field["validation_message"] = rgempty("errorMessage", $field) ? __("This field is required.", "gravityforms") : rgar($field, "errorMessage");
                            }
                            else if(!empty($quantity) && (!is_numeric($quantity) || intval($quantity) != floatval($quantity)) ) {
                                $field["failed_validation"] = true;
                                $field["validation_message"] = __("Please enter a valid quantity", "gravityforms");
                            }

                        break;

                        case "radio" :

                            if(rgar($field, 'enableOtherChoice') && $value == 'gf_other_choice')
                                $value = rgpost("input_{$field['id']}_other");

                            if($field["isRequired"] && rgar($field, 'enableOtherChoice') && $value == GFCommon::get_other_choice_value()) {
                                $field["failed_validation"] = true;
                                $field["validation_message"] = empty($field["errorMessage"]) ? __("This field is required.", "gravityforms") : $field["errorMessage"];
                            }

                        break;
                    }
                }
            }

            $custom_validation_result = apply_filters("gform_field_validation", array("is_valid"=> rgar($field, "failed_validation") ? false : true, "message"=>rgar($field, "validation_message")), $value, $form, $field);
            $custom_validation_result = apply_filters("gform_field_validation_{$form["id"]}", $custom_validation_result, $value, $form, $field);
            $custom_validation_result = apply_filters("gform_field_validation_{$form["id"]}_{$field["id"]}", $custom_validation_result, $value, $form, $field);

            $field["failed_validation"] = rgar($custom_validation_result, "is_valid") ? false : true;
            $field["validation_message"] = rgar($custom_validation_result, "message");
        }

        $is_valid = true;
        foreach($form["fields"] as $f){
            if(rgar($f,"failed_validation")){
                $is_valid = false;
                break;
            }
        }

        $validation_result = apply_filters("gform_validation_{$form["id"]}", apply_filters("gform_validation", array("is_valid" => $is_valid, "form" => $form)) );
        $is_valid = $validation_result["is_valid"];
        $form = $validation_result["form"];

        return $is_valid;
    }

    public static function failed_state_validation($form_id, $field, $value){

        global $_gf_state;

        //if field can be populated dynamically, disable state validation
        if(rgar($field,"allowsPrepopulate")) {
            return false;
        } else if(!GFCommon::is_product_field($field["type"] && $field["type"] != "donation")) {
            return false;
        } else if (!in_array($field["inputType"], array("singleshipping", "singleproduct", "hiddenproduct", "checkbox", "radio", "select"))) {
            return false;
        }

        if(!isset($_gf_state)){
            $state = unserialize(base64_decode($_POST["state_{$form_id}"]));

            if(!$state || sizeof($state) != 2)
                return true;

            //making sure state wasn't tampered with by validating checksum
            $checksum = wp_hash(crc32($state[0]));

            if($checksum != $state[1]){
                return true;
            }

            $_gf_state = unserialize($state[0]);
        }

        if(!is_array($value)){
            $value = array($field["id"] => $value);
        }

        foreach($value as $key => $input_value){
            $state = isset($_gf_state[$key]) ? $_gf_state[$key] : false;

            //converting price to a number for single product fields and single shipping fields
            if( (in_array($field["inputType"], array("singleproduct", "hiddenproduct")) && $key == $field["id"] . ".2") || $field["inputType"] == "singleshipping")
                $input_value = GFCommon::to_number($input_value);

            $hash = wp_hash($input_value);

            if(strlen($input_value) > 0 && $state !== false && ((is_array($state) && !in_array($hash, $state)) || (!is_array($state) && $hash != $state)) ){
                return true;
            }
        }
        return false;
    }

    public static function enqueue_scripts(){
        global $wp_query;
        if(isset($wp_query->posts) && is_array($wp_query->posts)){
            foreach($wp_query->posts as $post){
                $forms = self::get_embedded_forms($post->post_content, $ajax);
                foreach($forms as $form){
                    self::enqueue_form_scripts($form, $ajax);
                }
            }
        }
    }

    public static function get_embedded_forms($post_content, &$ajax){
        $forms = array();
        if(preg_match_all('/\[gravityform +.*?((id=.+?)|(name=.+?))\]/is', $post_content, $matches, PREG_SET_ORDER)){
            $ajax = false;
            foreach($matches as $match){
                //parsing shortcode attributes
                $attr = shortcode_parse_atts($match[1]);
                $form_id = $attr["id"];
                if(!is_numeric($form_id))
                    $form_id = RGFormsModel::get_form_id($attr["name"]);

                $forms[] = RGFormsModel::get_form_meta($form_id);
                $ajax = isset($attr["ajax"]) && strtolower(substr($attr["ajax"],0, 4)) == "true";
            }
        }
        return $forms;
    }

    public static function enqueue_form_scripts($form, $ajax=false){
        if(!get_option('rg_gforms_disable_css')){
            wp_enqueue_style("gforms_css", GFCommon::get_base_url() . "/css/forms.css", null, GFCommon::$version);
        }

        if(self::has_price_field($form) || self::has_password_strength($form) || GFCommon::has_list_field($form) || GFCommon::has_credit_card_field($form) || self::has_conditional_logic($form)){
            wp_enqueue_script("gforms_gravityforms", GFCommon::get_base_url() . "/js/gravityforms.js", array("jquery"), GFCommon::$version, false);
        }

        if(self::has_conditional_logic($form)){
            wp_enqueue_script("gforms_conditional_logic_lib", GFCommon::get_base_url() . "/js/conditional_logic.js", array("jquery", "gforms_gravityforms"), GFCommon::$version);
        }

        if(self::has_date_field($form)){
            wp_enqueue_script("gforms_ui_datepicker", GFCommon::get_base_url() . "/js/jquery-ui/ui.datepicker.js", array("jquery"), GFCommon::$version, true);
            wp_enqueue_script("gforms_datepicker", GFCommon::get_base_url() . "/js/datepicker.js", array("gforms_ui_datepicker"), GFCommon::$version, true);
        }

        if(self::has_price_field($form) || self::has_password_strength($form) || GFCommon::has_list_field($form) || GFCommon::has_credit_card_field($form) || self::has_calculation_field($form)){
            wp_enqueue_script("gforms_gravityforms", GFCommon::get_base_url() . "/js/gravityforms.js", array("jquery"), GFCommon::$version, false);
        }

        if(self::has_enhanced_dropdown($form) || self::has_pages($form) || self::has_fileupload_field($form)){
            wp_enqueue_script("gforms_json", GFCommon::get_base_url() . "/js/jquery.json-1.3.js", array("jquery"), GFCommon::$version, true);
            wp_enqueue_script("gforms_gravityforms", GFCommon::get_base_url() . "/js/gravityforms.js", array("gforms_json"), GFCommon::$version, false);
        }

        if(self::has_character_counter($form)){
            wp_enqueue_script("gforms_character_counter", GFCommon::get_base_url() . "/js/jquery.textareaCounter.plugin.js", array("jquery"), GFCommon::$version, true);
        }

        if(self::has_input_mask($form)){
            wp_enqueue_script("gforms_input_mask", GFCommon::get_base_url() . "/js/jquery.maskedinput-1.3.min.js", array("jquery"), GFCommon::$version, true);
        }

        if(self::has_enhanced_dropdown($form)){
            wp_enqueue_script("gforms_chosen", GFCommon::get_base_url() . "/js/chosen.jquery.min.js", array("jquery"), GFCommon::$version, false);
        }

        do_action("gform_enqueue_scripts", $form, $ajax);
        do_action("gform_enqueue_scripts_{$form["id"]}", $form, $ajax);

        // enqueue jQuery every time form is displayed to allow "gform_post_render" js hook
        // to be available to users even when GF is not using it
        wp_enqueue_script("jquery");

    }

    private static $printed_scripts = array();

    public static function print_form_scripts($form, $ajax){

        if(!get_option('rg_gforms_disable_css')){
            if(!wp_style_is("gforms_css", "queue")){
                wp_enqueue_style("gforms_css", GFCommon::get_base_url() . "/css/forms.css", GFCommon::$version);
                wp_print_styles(array("gforms_css"));
            }
        }

        if( (self::has_enhanced_dropdown($form) || self::has_price_field($form) || self::has_password_strength($form) || self::has_pages($form) || self::has_password_strength($form) || GFCommon::has_list_field($form) || GFCommon::has_credit_card_field($form)) && !wp_script_is("gforms_gravityforms", "queue")){
            wp_enqueue_script("gforms_gravityforms", GFCommon::get_base_url() . "/js/gravityforms.js", array("jquery"), GFCommon::$version, false);
            wp_print_scripts(array("gforms_gravityforms"));
        }

        if(self::has_conditional_logic($form) && !wp_script_is("gforms_conditional_logic_lib", "queue")){
            wp_enqueue_script("gforms_conditional_logic_lib", GFCommon::get_base_url() . "/js/conditional_logic.js", array("jquery", "gforms_gravityforms"), GFCommon::$version);
            wp_print_scripts(array("gforms_conditional_logic_lib"));
        }

        if(self::has_date_field($form) && !wp_script_is("gforms_datepicker", "queue")){
            wp_enqueue_script("gforms_ui_datepicker", GFCommon::get_base_url() . "/js/jquery-ui/ui.datepicker.js", array("jquery"), GFCommon::$version, true);
            wp_enqueue_script("gforms_datepicker", GFCommon::get_base_url() . "/js/datepicker.js", array("gforms_ui_datepicker"), GFCommon::$version, true);
            wp_print_scripts(array("gforms_datepicker"));
        }

        if(self::has_pages($form) && !wp_script_is("gforms_json", "queue")){
            wp_enqueue_script("gforms_json", GFCommon::get_base_url() . "/js/jquery.json-1.3.js", array("jquery"), GFCommon::$version, true);
            wp_print_scripts(array("gforms_json"));
        }

        if( (self::has_enhanced_dropdown($form) || self::has_price_field($form) || self::has_password_strength($form) || self::has_pages($form) || self::has_password_strength($form) || GFCommon::has_list_field($form) || GFCommon::has_credit_card_field($form)) || self::has_calculation_field($form) && !wp_script_is("gforms_gravityforms", "queue")){
            wp_enqueue_script("gforms_gravityforms", GFCommon::get_base_url() . "/js/gravityforms.js", array("jquery"), GFCommon::$version, false);
            wp_print_scripts(array("gforms_gravityforms"));
        }

        if(self::has_character_counter($form) && !wp_script_is("gforms_character_counter", "queue")){
            wp_enqueue_script("gforms_character_counter", GFCommon::get_base_url() . "/js/jquery.textareaCounter.plugin.js", array("jquery"), GFCommon::$version, true);
            wp_print_scripts(array("gforms_character_counter"));
        }

        if(self::has_input_mask($form) && !wp_script_is("gforms_input_mask", "queue")){
            wp_enqueue_script("gforms_input_mask", GFCommon::get_base_url() . "/js/jquery.maskedinput-1.3.min.js", array("jquery"), GFCommon::$version, true);
            wp_print_scripts(array("gforms_input_mask"));
        }

        if(self::has_enhanced_dropdown($form)&& !wp_script_is("gforms_chosen", "queue")){
            wp_enqueue_script("gforms_chosen", GFCommon::get_base_url() . "/js/chosen.jquery.min.js", array("jquery"), GFCommon::$version, false);
            wp_print_scripts(array("gforms_chosen"));
        }

        if(!wp_script_is("jquery", "queue")){
            wp_print_scripts(array("jquery"));
        }

        if(wp_script_is("gforms_gravityforms")) {
            require_once(GFCommon::get_base_path() . '/currency.php');
            echo '<script type="text/javascript"> var gf_global = { gf_currency_config: ' . GFCommon::json_encode(RGCurrency::get_currency(GFCommon::get_currency()))  . ' }; </script>';
        }

    }

    private static function has_conditional_logic($form){
        if(empty($form))
            return false;

        if(isset($form["button"]["conditionalLogic"]))
            return true;

        foreach($form["fields"] as $field){
            if(!empty($field["conditionalLogic"])){
                return true;
            }
            else if(isset($field["nextButton"]) && !empty($field["nextButton"]["conditionalLogic"])){
                return true;
            }
        }
        return false;
    }

    private static function get_conditional_logic($form, $field_values = array()){
        $logics = "";
        $dependents = "";
        $fields_with_logic = array();
        $default_values = array();

        foreach($form["fields"] as $field){

            //use section's logic if one exists
            $section = RGFormsModel::get_section($form, $field["id"]);
            $section_logic = !empty($section) ? rgar($section,"conditionalLogic") : null;

            $field_logic = $field["type"] != "page" ? RGForms::get("conditionalLogic", $field) : null; //page break conditional logic will be handled during the next button click

            $next_button_logic = isset($field["nextButton"]) && isset($field["nextButton"]["conditionalLogic"]) ? $field["nextButton"]["conditionalLogic"] : null;

            if(!empty($field_logic) || !empty($next_button_logic)){

                $field_section_logic = array("field" => $field_logic, "nextButton" => $next_button_logic, "section" => $section_logic);

                $logics .= $field["id"] . ": " . GFCommon::json_encode($field_section_logic) . ",";

                $fields_with_logic[] = $field["id"];

                $peers = $field["type"] == "section" ? GFCommon::get_section_fields($form, $field["id"]) : array($field);
                $peer_ids = array();

                foreach ($peers as $peer)
                    $peer_ids[] = $peer["id"];

                $dependents .= $field["id"] . ": " . GFCommon::json_encode($peer_ids) . ",";
            }

            //-- Saving default values so that they can be restored when toggling conditional logic ---
            $field_val = "";
            $input_type = RGFormsModel::get_input_type($field);

            //get parameter value if pre-populate is enabled
            if(rgar($field, "allowsPrepopulate")){
                if(is_array(rgar($field, "inputs"))){
                    $field_val = array();
                    foreach($field["inputs"] as $input){
                        $field_val["input_{$input["id"]}"] = RGFormsModel::get_parameter_value(rgar($input, "name"), $field, $field_values);
                    }
                }
                else if($input_type == "time"){
                    $parameter_val = RGFormsModel::get_parameter_value(rgar($field, "inputName"), $field, $field_values);
                    if(!empty($parameter_val) && preg_match('/^(\d*):(\d*) ?(.*)$/', $parameter_val, $matches)){
                        $field_val = array();
                        $field_val[] = esc_attr($matches[1]); //hour
                        $field_val[] = esc_attr($matches[2]); //minute
                        $field_val[] = rgar($matches,3); //am or pm
                    }
                }
                else if($input_type == "list"){
                    $parameter_val = RGFormsModel::get_parameter_value(rgar($field, "inputName"), $field, $field_values);
                    $field_val = explode(",", str_replace("|", ",", $parameter_val));
                }
                else {
                    $field_val = RGFormsModel::get_parameter_value(rgar($field, "inputName"), $field, $field_values);
                }
            }

            //use default value if pre-populated value is empty
            $field_val = self::default_if_empty($field, $field_val);

            if(is_array(rgar($field, "choices")) && $input_type != "list"){

                //radio buttons start at 0 and checkboxes start at 1
                $choice_index = $input_type == "radio" ? 0 : 1;

                foreach($field["choices"] as $choice){

                    if(rgar($choice,"isSelected") && $input_type == "select"){
                        $val = isset($choice["price"]) ? $choice["value"] . "|" . GFCommon::to_number($choice["price"]) :  $choice["value"];
                        $default_values[$field["id"]] = $val;
                    }
                    else if(rgar($choice,"isSelected")){
                        if(!isset($default_values[$field["id"]]))
                            $default_values[$field["id"]] = array();

                        $default_values[$field["id"]][] = "choice_{$field["id"]}_{$choice_index}";
                    }
                    $choice_index++;
                }
            }
            else if(!empty($field_val)){
                $default_values[$field["id"]] = $field_val;
            }
        }

        $button_conditional_script = "";

        //adding form button conditional logic if enabled
        if(isset($form["button"]["conditionalLogic"])){
            $logics .= "0: " . GFCommon::json_encode(array("field"=>$form["button"]["conditionalLogic"], "section" => null)) . ",";
            $dependents .= "0: " . GFCommon::json_encode(array(0)) . ",";
            $fields_with_logic[] = 0;

            $button_conditional_script = "jQuery('#gform_{$form['id']}').submit(" .
                                            "function(event, isButtonPress){" .
                                            "    var visibleButton = jQuery('.gform_next_button:visible, .gform_button:visible, .gform_image_button:visible');" .
                                            "    return visibleButton.length > 0 || isButtonPress == true;" .
                                            "}" .
                                        ");";
        }

        if(!empty($logics))
            $logics = substr($logics, 0, strlen($logics) - 1); //removing last comma;

        if(!empty($dependents))
            $dependents = substr($dependents, 0, strlen($dependents) - 1); //removing last comma;

        $animation = rgar($form,"enableAnimation") ? "1" : "0";
        global $wp_locale;
        $number_format = $wp_locale->number_format['decimal_point'] == "," ? "decimal_comma" : "decimal_dot";

        $str = "if(window['jQuery']){" .

                    "if(!window['gf_form_conditional_logic'])" .
                        "window['gf_form_conditional_logic'] = new Array();" .
                    "window['gf_form_conditional_logic'][{$form['id']}] = {'logic' : {" . $logics . " }, 'dependents' : {" . $dependents . " }, 'animation' : " . $animation . " , 'defaults' : " . json_encode($default_values) . " }; ".
                    "if(!window['gf_number_format'])" .
                        "window['gf_number_format'] = '" . $number_format . "';" .

                    "jQuery(document).ready(function(){" .
                        "gf_apply_rules({$form['id']}, " . json_encode($fields_with_logic) . ", true);" .
                        "jQuery('#gform_wrapper_{$form['id']}').show();" .
                        "jQuery(document).trigger('gform_post_conditional_logic', [{$form['id']}, null, true]);" .
                        $button_conditional_script .

                    "} );" .

                "} ";

        return $str;
    }


    /**
    * Enqueue and retrieve all inline scripts that should be executed when the form is rendered.
    * Use add_init_script() function to enqueue scripts.
    *
    * @param mixed $form
    */
    private static function register_form_init_scripts($form, $field_values = array()) {

        // adding conditional logic script if conditional logic is configured for this form.
        // get_conditional_logic also adds the chosen script for the enhanced dropdown option.
        // if this form does not have conditional logic, add chosen script separately
        if(self::has_conditional_logic($form)){
            self::add_init_script($form["id"], "conditional_logic", self::ON_PAGE_RENDER, self::get_conditional_logic($form, $field_values));
        }

        //adding currency config if there are any product fields in the form
        if(self::has_price_field($form)){
            self::add_init_script($form["id"], "pricing", self::ON_PAGE_RENDER, self::get_pricing_init_script($form));
        }

        if(self::has_password_strength($form)){
            $password_script = self::get_password_strength_init_script($form);
            self::add_init_script($form["id"], "password", self::ON_PAGE_RENDER, $password_script);
        }

        if(self::has_enhanced_dropdown($form) ){
            $chosen_script = self::get_chosen_init_script($form);
            self::add_init_script($form["id"], "chosen", self::ON_PAGE_RENDER, $chosen_script);
            self::add_init_script($form["id"], "chosen", self::ON_CONDITIONAL_LOGIC, $chosen_script);
        }

        if(GFCommon::has_credit_card_field($form)) {
            self::add_init_script($form["id"], "credit_card", self::ON_PAGE_RENDER, self::get_credit_card_init_script($form));
        }

        if(self::has_character_counter($form)){
            self::add_init_script($form['id'], 'character_counter', self::ON_PAGE_RENDER, self::get_counter_init_script($form));
        }

        if(self::has_input_mask($form)) {
            self::add_init_script($form['id'], 'input_mask', self::ON_PAGE_RENDER, self::get_input_mask_init_script($form));
        }

        if(self::has_calculation_field($form)) {
            self::add_init_script($form['id'], 'calculation', self::ON_PAGE_RENDER, self::get_calculations_init_script($form));
        }

    }

    public static function get_form_init_scripts($form) {

        $script_string = '';

        // temporary solution for output gf_global obj until wp min version raised to 3.3
        if(wp_script_is("gforms_gravityforms")) {
            require_once(GFCommon::get_base_path() . '/currency.php');
            $gf_global_obj = array( 'gf_currency_config' => RGCurrency::get_currency(GFCommon::get_currency()) );
            $gf_global_script = "if(typeof gf_global == 'undefined') var gf_global = " . json_encode($gf_global_obj) . ";";
        }

        /* rendering initialization scripts */
        $init_scripts = rgar(self::$init_scripts, $form["id"]);

        if(!empty($init_scripts)){
            $script_string =
            "<script type='text/javascript'>" . apply_filters("gform_cdata_open", "") . " ";

            $script_string .= isset($gf_global_script) ? $gf_global_script : '';

            $script_string .=
                "jQuery(document).bind('gform_post_render', function(event, formId, currentPage){" .
                    "if(formId == {$form['id']}) {";

                    foreach($init_scripts as $init_script){
                        if($init_script["location"] == self::ON_PAGE_RENDER){
                            $script_string .= $init_script["script"];
                        }
                    }

            $script_string .=
                    "} ". //keep the space. needed to prevent plugins from replacing }} with ]}
                "} );".

                "jQuery(document).bind('gform_post_conditional_logic', function(event, formId, fields, isInit){" ;
                    foreach($init_scripts as $init_script){
                        if($init_script["location"] == self::ON_CONDITIONAL_LOGIC){
                            $script_string .= $init_script["script"];
                        }
                    }

            $script_string .=

                "} );". apply_filters("gform_cdata_close", "") . "</script>";
        }

        return $script_string;
    }

    public static function get_chosen_init_script($form){
        $chosen_fields = array();
        foreach($form["fields"] as $field){
            if(rgar($field, "enableEnhancedUI"))
                $chosen_fields[] = "#input_{$form["id"]}_{$field["id"]}";
        }
        return "gformInitChosenFields('" . implode(",", $chosen_fields) . "','" . esc_attr(apply_filters("gform_dropdown_no_results_text_{$form["id"]}", apply_filters("gform_dropdown_no_results_text", __("No results matched", "gravityforms"), $form["id"]), $form["id"])) . "');";
    }

    public static function get_counter_init_script($form){

        $script = "";
        foreach($form["fields"] as $field){
            $max_length = rgar($field,"maxLength");
            $field_id = "input_{$form["id"]}_{$field["id"]}";
            if(!empty($max_length) && !rgar($field,"adminOnly"))
            {
                $field_script =
                        //******  make this callable only once ***********
                        "jQuery('#{$field_id}').textareaCount(" .
                        "    {" .
                        "    'maxCharacterSize': {$max_length}," .
                        "    'originalStyle': 'ginput_counter'," .
                        "    'displayFormat' : '#input " . __("of", "gravityforms") . " #max " . __("max characters", "gravityforms") . "'" .
                        "    } );";

                $script .= apply_filters("gform_counter_script_{$form["id"]}", apply_filters("gform_counter_script", $field_script, $form["id"], $field_id, $max_length), $form["id"], $field_id, $max_length);
            }
        }
        return $script;
    }

    public static function get_credit_card_init_script($form) {

        $script = "";

        foreach($form["fields"] as $field){

            if($field['type'] != 'creditcard')
                continue;

            $field_id = "input_{$form["id"]}_{$field["id"]}";
            $field_script = "jQuery(document).ready(function(){ { gformMatchCard(\"{$field_id}_1\"); } } );";

            if(rgar($field, "forceSSL") && !GFCommon::is_ssl() && !GFCommon::is_preview())
                $field_script = "document.location.href='" . esc_js( RGFormsModel::get_current_page_url(true) ) . "'";

            $script .= $field_script;
        }

        $card_rules = self::get_credit_card_rules();
        $script = "if(!window['gf_cc_rules']){window['gf_cc_rules'] = new Array(); } window['gf_cc_rules'] = " . GFCommon::json_encode($card_rules) . "; $script";

        return $script;
    }

    public static function get_pricing_init_script($form) {

        if(!class_exists("RGCurrency"))
            require_once("currency.php");

        return "if(window[\"gformInitPriceFields\"]) jQuery(document).ready(function(){gformInitPriceFields();} );";
    }

    public static function get_password_strength_init_script($form) {

        $field_script = "if(!window['gf_text']){window['gf_text'] = new Array();} window['gf_text']['password_blank'] = '" . __("Strength indicator", "gravityforms") . "'; window['gf_text']['password_mismatch'] = '" . __("Mismatch", "gravityforms") . "';window['gf_text']['password_bad'] = '" . __("Bad", "gravityforms") . "'; window['gf_text']['password_short'] = '" . __("Short", "gravityforms") . "'; window['gf_text']['password_good'] = '" . __("Good", "gravityforms") . "'; window['gf_text']['password_strong'] = '" . __("Strong", "gravityforms") . "';";

        foreach($form['fields'] as $field) {
            if($field['type'] == 'password' && rgar($field, 'passwordStrengthEnabled')) {
                $field_id = "input_{$form["id"]}_{$field["id"]}";
                $field_script .= "gformShowPasswordStrength(\"$field_id\");";
            }
        }

        return $field_script;
    }

    public static function get_input_mask_init_script($form) {

        $script_str = '';

        foreach($form['fields'] as $field) {

            if(!rgar($field, 'inputMask') || !rgar($field, 'inputMaskValue'))
                continue;

            $mask = rgar($field, 'inputMaskValue');
            $script = "jQuery('#input_{$form['id']}_{$field['id']}').mask('{$mask}').bind('keypress', function(e){if(e.which == 13){jQuery(this).blur();} } );";

            $script_str .= apply_filters("gform_input_mask_script_{$form['id']}", apply_filters("gform_input_mask_script", $script, $form['id'], $field['id'], $mask), $form['id'], $field['id'], $mask);
        }

        return $script_str;
    }

    public static function get_calculations_init_script($form) {
        require_once(GFCommon::get_base_path() . '/currency.php');

        $formula_fields = array();

        foreach($form['fields'] as $field) {

            if(!rgar($field, 'enableCalculation') || !rgar($field, 'calculationFormula'))
                continue;

            $formula_fields[] = array('field_id' => $field['id'], 'formula' => rgar($field, 'calculationFormula'), 'rounding' => rgar($field, 'calculationRounding'), 'numberFormat' => rgar($field, 'numberFormat') );

        }

        if(empty($formula_fields))
            return '';

        $script = 'new GFCalc(' . $form['id'] . ', ' . GFCommon::json_encode($formula_fields) . ');';

        return $script;
    }




    private static function has_date_field($form){
        if(is_array($form["fields"])){
            foreach($form["fields"] as $field){

                if(RGFormsModel::get_input_type($field) == "date")
                    return true;
            }
        }
        return false;
    }

    private static function has_price_field($form){
        $price_fields = GFCommon::get_fields_by_type($form, array("product", "donation"));
        return !empty($price_fields);
    }

    private static function has_fileupload_field($form){
        $fileupload_fields = GFCommon::get_fields_by_type($form, array("fileupload", "post_image"));
        if(is_array($form["fields"])){
            foreach($form["fields"] as $field){
                $input_type = RGFormsModel::get_input_type($field);
                if(in_array($input_type, array("fileupload", "post_image")))
                    return true;
            }
        }
        return false;
    }

    private static function has_recaptcha_field($form){
        if(is_array($form["fields"])){
            foreach($form["fields"] as $field){
                if(($field["type"] == "captcha" || $field["inputType"] == "captcha") && !in_array($field["captchaType"], array("simple_captcha", "math")))
                    return true;
            }
        }
        return false;
    }

    public static function has_input_mask($form, $field = false){

        if($field) {
            if(self::has_field_input_mask($field))
                return true;
        } else {

            if(!is_array($form["fields"]))
                return false;

            foreach($form["fields"] as $field){
                if(rgar($field, "inputMask") && rgar($field, "inputMaskValue") && !rgar($field, "enablePasswordInput"))
                    return true;
            }

        }

        return false;
    }

    public static function has_field_input_mask($field){
        if(rgar($field, "inputMask") && rgar($field, "inputMaskValue") && !rgar($field, "enablePasswordInput"))
            return true;

        return false;
    }

    public static function has_calculation_field($form) {

        if(!is_array($form["fields"]))
            return false;

        foreach($form['fields'] as $field) {
            if(GFCommon::has_field_calculation($field))
                return true;
        }
        return false;
    }

    //Getting all fields that have a rule based on the specified field id
    private static function get_conditional_logic_fields($form, $fieldId){
        $fields = array();

        //adding submit button field if enabled
        if(isset($form["button"]["conditionalLogic"])){
            $fields[] = 0;
        }

        foreach($form["fields"] as $field){

            if($field["type"] != "page" && !empty($field["conditionalLogic"])){
                foreach($field["conditionalLogic"]["rules"] as $rule){
                    if($rule["fieldId"] == $fieldId){
                        $fields[] = $field["id"];

                        //if field is a section, add all fields in the section that have conditional logic (to support nesting)
                        if($field["type"] == "section"){
                            $section_fields = GFCommon::get_section_fields($form, $field["id"]);
                            foreach($section_fields as $section_field)
                                if(!empty($section_field["conditionalLogic"]))
                                    $fields[] = $section_field["id"];
                        }
                        break;
                    }
                }
            }
            //adding fields with next button logic
            if(!empty($field["nextButton"]["conditionalLogic"])){
                foreach($field["nextButton"]["conditionalLogic"]["rules"] as $rule){
                    if($rule["fieldId"] == $fieldId && !in_array($fieldId, $fields)){
                        $fields[] = $field["id"];
                        break;
                    }
                }
            }
        }
        return $fields;
    }

    public static function get_field($field, $value="", $force_frontend_label = false, $form=null, $field_values=null){
        $custom_class = IS_ADMIN ? "" : rgget("cssClass", $field);

        if($field["type"] == "page"){
            if(IS_ADMIN && RG_CURRENT_VIEW == "entry"){
                return; //ignore page breaks in the entry detail page
            }
            else if(!IS_ADMIN){
                $next_button = self::get_form_button($form["id"], "gform_next_button_{$form["id"]}_{$field["id"]}", $field["nextButton"], __("Next", "gravityforms"), "button gform_next_button", __("Next Page", "gravityforms"), $field["pageNumber"]);
                $previous_button = $field["pageNumber"] == 2 ? "" : self::get_form_button($form["id"], "gform_previous_button_{$form["id"]}_{$field["id"]}", $field["previousButton"], __("Previous", "gravityforms"), "button gform_previous_button", __("Previous Page", "gravityforms"), $field["pageNumber"]-2);
                $style = self::is_page_active($form["id"], $field["pageNumber"]) ? "" : "style='display:none;'";
                $custom_class = !empty($custom_class) ? " {$custom_class}" : "";
                $html = "</ul>
                    </div>
                    <div class='gform_page_footer'>
                        {$previous_button} {$next_button}
                    </div>
                </div>
                <div id='gform_page_{$form["id"]}_{$field["pageNumber"]}' class='gform_page{$custom_class}' {$style}>
                    <div class='gform_page_fields'>
                        <ul class='gform_fields {$form['labelPlacement']}'>";

                return $html;
            }
        }

        if($field["type"] == "post_category") {



        }

        if(!IS_ADMIN && rgar($field,"adminOnly"))
        {
            if($field["allowsPrepopulate"])
                $field["inputType"] = "adminonly_hidden";
            else
                return;
        }

        $id = $field["id"];
        $type = $field["type"];
        $input_type = RGFormsModel::get_input_type($field);

        $error_class = rgget("failed_validation", $field) ? "gfield_error" : "";
        $admin_only_class =  rgget("adminOnly", $field) ? "field_admin_only" : "";
        $selectable_class = IS_ADMIN ? "selectable" : "";
        $hidden_class = in_array($input_type, array("hidden", "hiddenproduct")) ? "gform_hidden" : "";

        $section_class = $field["type"] == "section" ? "gsection" : "";
        $page_class = $field["type"] == "page" ? "gpage" : "";
        $html_block_class = $field["type"] == "html" ? "gfield_html" : "";
        $html_formatted_class = $field["type"] == "html" && !IS_ADMIN && !rgget("disableMargins", $field) ? "gfield_html_formatted" : "";
        $html_no_follows_desc_class = $field["type"] == "html" && !IS_ADMIN && !self::prev_field_has_description($form, $field["id"]) ? "gfield_no_follows_desc" : "";

        $calculation_class = RGFormsModel::get_input_type($field) == 'number' && GFCommon::has_field_calculation($field) ? 'gfield_calculation' : '';
        $calculation_class = RGFormsModel::get_input_type($field) == 'calculation' ? 'gfield_calculation' : '';

        $product_suffix = "_{$form["id"]}_" . rgget("productField", $field);
        $option_class = $field["type"] == "option" ? "gfield_price gfield_price{$product_suffix} gfield_option{$product_suffix}" : "";
        $quantity_class = $field["type"] == "quantity" ? "gfield_price gfield_price{$product_suffix} gfield_quantity{$product_suffix}" : "";
        $shipping_class = $field["type"] == "shipping" ? "gfield_price gfield_shipping gfield_shipping_{$form["id"]}" : "";
        $product_class = $field["type"] == "product" ? "gfield_price gfield_price_{$form["id"]}_{$field["id"]} gfield_product_{$form["id"]}_{$field["id"]}" : "";
        $hidden_product_class = $input_type == "hiddenproduct" ? "gfield_hidden_product" : "";
        $donation_class = $field["type"] == "donation" ? "gfield_price gfield_price_{$form["id"]}_{$field["id"]} gfield_donation_{$form["id"]}_{$field["id"]}" : "";
        $required_class = rgar($field, "isRequired") ? "gfield_contains_required" : "";
        $creditcard_warning_class = $input_type == "creditcard" && !GFCommon::is_ssl() ? "gfield_creditcard_warning" : "";

        $css_class = "$selectable_class gfield $error_class $section_class $admin_only_class $custom_class $hidden_class $html_block_class $html_formatted_class $html_no_follows_desc_class $option_class $quantity_class $product_class $donation_class $shipping_class $page_class $required_class $hidden_product_class $creditcard_warning_class $calculation_class";
        $css_class = apply_filters("gform_field_css_class_{$form["id"]}", apply_filters("gform_field_css_class", trim($css_class), $field, $form), $field, $form);

        $style = !empty($form) && !IS_ADMIN && RGFormsModel::is_field_hidden($form, $field, $field_values) ? "style='display:none;'" : "";

        $field_id = IS_ADMIN || empty($form) ? "field_$id" : "field_" . $form["id"] . "_$id";

        return "<li id='$field_id' class='$css_class' $style>" . self::get_field_content($field, $value, $force_frontend_label, $form == null ? 0 : $form["id"]) . "</li>";
    }

    private static function prev_field_has_description($form, $field_id){
        if(!is_array($form["fields"]))
            return false;

        $prev = null;
        foreach($form["fields"] as $field){
            if($field["id"] == $field_id){
                return $prev != null && !empty($prev["description"]);
            }
            $prev = $field;
        }
        return false;
    }

    public static function get_field_content($field, $value="", $force_frontend_label = false, $form_id=0){
        $id = $field["id"];
        $size = rgar($field,"size");
        $validation_message = (rgget("failed_validation", $field) && !empty($field["validation_message"])) ? sprintf("<div class='gfield_description validation_message'>%s</div>", $field["validation_message"]) : "";

        $duplicate_disabled = array('captcha', 'post_title', 'post_content', 'post_excerpt', 'total', 'shipping', 'creditcard');
        $duplicate_field_link = !in_array($field['type'], $duplicate_disabled) ? "<a class='field_duplicate_icon' id='gfield_duplicate_$id' title='" . __("click to duplicate this field", "gravityforms") . "' href='#' onclick='StartDuplicateField(this); return false;'>" . __("Duplicate", "gravityforms") . "</a>" : "";
        $duplicate_field_link = apply_filters("gform_duplicate_field_link", $duplicate_field_link);

        $delete_field_link = "<a class='field_delete_icon' id='gfield_delete_$id' title='" . __("click to delete this field", "gravityforms") . "' href='#' onclick='StartDeleteField(this); return false;'>" . __("Delete", "gravityforms") . "</a>";
        $delete_field_link = apply_filters("gform_delete_field_link", $delete_field_link);
        $field_type_title = GFCommon::get_field_type_title($field["type"]);
        $admin_buttons = IS_ADMIN ? "<div class='gfield_admin_icons'><div class='gfield_admin_header_title'>{$field_type_title} : " . __("Field ID", "gravityforms") . " {$field["id"]}</div>" . $delete_field_link . $duplicate_field_link . "<a class='field_edit_icon edit_icon_collapsed' title='" . __("click to edit this field", "gravityforms") . "'>" . __("Edit", "gravityforms") . "</a></div>" : "";

        $field_label = $force_frontend_label ? $field["label"] : GFCommon::get_label($field);
        if(rgar($field, "inputType") == "singleproduct" && !rgempty($field["id"] . ".1", $value))
            $field_label = rgar($value, $field["id"] . ".1");


        $field_id = IS_ADMIN || $form_id == 0 ? "input_$id" : "input_" . $form_id . "_$id";

        $required_div = IS_ADMIN || rgar($field, "isRequired") ? sprintf("<span class='gfield_required'>%s</span>", $field["isRequired"] ? "*" : "") : "";
        $target_input_id = "";
        $is_description_above = rgar($field, "descriptionPlacement") == "above";

        switch(RGFormsModel::get_input_type($field)){
            case "section" :
                $description = self::get_description(rgget("description", $field), "gsection_description");
                $field_content = sprintf("%s<h2 class='gsection_title'>%s</h2>%s", $admin_buttons,  esc_html($field_label), $description);
            break;

            case "page" :
                //only executed on the form editor in the admin
                $page_label = __("Page Break", "gravityforms");
                $src = GFCommon::get_base_url() . "/images/gf_pagebreak_inline.png";
                $field_content = "{$admin_buttons} <label class='gfield_label'>&nbsp;</label><img src='{$src}' alt='{$page_label}' title='{$page_label}' />";
            break;

            case "adminonly_hidden":
            case "hidden" :
            case "html" :
                $field_content = !IS_ADMIN ? "{FIELD}" : $field_content = sprintf("%s<label class='gfield_label' for='%s'>%s</label>{FIELD}", $admin_buttons, $field_id, esc_html($field_label));
            break;

            case "checkbox":
            case "radio":
                $description = self::get_description(rgget("description", $field),"gfield_description");
                if($is_description_above)
                    $field_content = sprintf("%s<label class='gfield_label'>%s%s</label>%s{FIELD}%s", $admin_buttons, esc_html($field_label), $required_div , $description, $validation_message);
                else
                    $field_content = sprintf("%s<label class='gfield_label'>%s%s</label>{FIELD}%s%s", $admin_buttons, esc_html($field_label), $required_div , $description, $validation_message);
            break;

            case "name" :
                switch(rgar($field, "nameFormat")){
                    case "simple" :
                        $target_input_id = $field_id;
                        break;

                    case "extended" :
                        $target_input_id = $field_id . "_2";
                        break;

                    default :
                        $target_input_id = $field_id . "_3";
                }

            case "address" :
                if(empty($target_input_id))
                    $target_input_id = $field_id . "_1";

            default :
                if(empty($target_input_id))
                    $target_input_id = $field_id;

                $description = self::get_description(rgget("description", $field),"gfield_description");
                if($is_description_above)
                    $field_content = sprintf("%s<label class='gfield_label' for='%s'>%s%s</label>%s{FIELD}%s", $admin_buttons, $target_input_id, esc_html($field_label), $required_div , $description, $validation_message);
                else
                    $field_content = sprintf("%s<label class='gfield_label' for='%s'>%s%s</label>{FIELD}%s%s", $admin_buttons, $target_input_id, esc_html($field_label), $required_div , $description, $validation_message);
            break;
        }

        if(RGFormsModel::get_input_type($field) == "creditcard" && !GFCommon::is_ssl() && !IS_ADMIN){
            $field_content = "<div class='gfield_creditcard_warning_message'>" . __("This page is unsecured. Do not enter a real credit card number. Use this field only for testing purposes. ", "gravityforms") . "</div>" . $field_content;
        }

        $value = self::default_if_empty($field, $value);

        $field_content = str_replace("{FIELD}", GFCommon::get_field_input($field, $value, 0, $form_id), $field_content);

        $field_content = apply_filters("gform_field_content", $field_content, $field, $value, 0, $form_id);

        return $field_content;
    }

    private static function default_if_empty($field, $value){

        if(!GFCommon::is_empty_array($value))
            return $value;

        if(IS_ADMIN){
            $value = rgget("defaultValue", $field);
        }
        else{
            $value = rgar($field, "defaultValue");
            if(!is_array($value))
                $value = GFCommon::replace_variables_prepopulate(rgget("defaultValue", $field));
        }

        return $value;
    }

    private static function get_description($description, $css_class){
        return IS_ADMIN || !empty($description) ? "<div class='$css_class'>" . $description . "</div>" : "";
    }

    public static function get_credit_card_rules() {

        $cards = GFCommon::get_card_types();
        //$supported_cards = //TODO: Only include enabled cards
        $rules = array();

        foreach($cards as $card) {
            $prefixes = explode(',', $card['prefixes']);
            foreach($prefixes as $prefix) {
                $rules[$card['slug']][] = $prefix;
            }
        }

        return $rules;
    }

    private static function get_progress_bar($form, $form_id,$confirmation_message) {

        $progress_complete = false;
        $progress_bar = "";
        $page_count = self::get_max_page_number($form);
        $current_page = self::get_current_page($form_id);
        $page_name = rgar(rgar($form["pagination"],"pages"), $current_page -1);
        $page_name = !empty($page_name) ? " - " . $page_name : "";
        $style = $form["pagination"]["style"];
        $color = $style == "custom" ? " color:{$form["pagination"]["color"]};" : "";
        $bgcolor = $style == "custom" ? " background-color:{$form["pagination"]["backgroundColor"]};" : "";

        if (!empty($confirmation_message))
        {
            $progress_complete = true;
        }
        //check admin setting for whether the progress bar should start at zero
        $start_at_zero = rgars($form, "pagination/display_progressbar_on_confirmation");
		//check for filter
        $start_at_zero = apply_filters("gform_progressbar_start_at_zero", $start_at_zero, $form);
        $progressbar_page_count = $start_at_zero ? $current_page - 1 : $current_page;
        $percent = !$progress_complete ? floor(( ($progressbar_page_count) / $page_count ) * 100) . "%" : "100%";
        $percent_number = !$progress_complete ? floor(( ($progressbar_page_count) / $page_count ) * 100) . "" : "100";

        if ($progress_complete)
        {
            $wrapper_css_class = GFCommon::get_browser_class() . " gform_wrapper";

            //add on surrounding wrapper class when confirmation page
            $progress_bar = "<div class='{$wrapper_css_class}' id='gform_wrapper_$form_id' >";
            $page_name = !empty($form["pagination"]["progressbar_completion_text"]) ? $form["pagination"]["progressbar_completion_text"] : "";
        }


        $progress_bar .="
        <div id='gf_progressbar_wrapper_{$form_id}' class='gf_progressbar_wrapper'>
            <h3 class='gf_progressbar_title'>";
        $progress_bar .= !$progress_complete ? __("Step", "gravityforms") . " {$current_page} " . __("of", "gravityforms") . " {$page_count}{$page_name}" : "{$page_name}";
        $progress_bar .= "
        </h3>
            <div class='gf_progressbar'>
                <div class='gf_progressbar_percentage percentbar_{$style} percentbar_{$percent_number}' style='width:{$percent};{$color}{$bgcolor}'><span>{$percent}</span></div>
            </div></div>";
        //close div for surrounding wrapper class when confirmation page
        $progress_bar .= $progress_complete ? "</div>" : "";

        return $progress_bar;
    }

    /**
    * Validates the form's entry limit settings. Returns the entry limit message if entry limit exceeded.
    *
    * @param array $form current GF form object
    * @return string If entry limit exceeded returns entry limit setting.
    */
    public static function validate_entry_limit($form) {

        //If form has a limit of entries, check current entry count
        if(rgar($form,"limitEntries")) {
            $period = rgar($form, "limitEntriesPeriod");
            $range = self::get_limit_period_dates($period);
            $entry_count = RGFormsModel::get_lead_count($form['id'], "", null, null, $range["start_date"], $range["end_date"]);

            if($entry_count >= $form["limitEntriesCount"])
                return empty($form["limitEntriesMessage"]) ? "<p>" . __("Sorry. This form is no longer accepting new submissions.", "gravityforms"). "</p>" : "<p>" . GFCommon::gform_do_shortcode($form["limitEntriesMessage"]) . "</p>";
        }

    }

    public static function validate_form_schedule($form) {

        //If form has a schedule, make sure it is within the configured start and end dates
        if(rgar($form, "scheduleForm")){
            $local_time_start = sprintf("%s %02d:%02d %s", $form["scheduleStart"], $form["scheduleStartHour"], $form["scheduleStartMinute"], $form["scheduleStartAmpm"]);
            $local_time_end = sprintf("%s %02d:%02d %s", $form["scheduleEnd"], $form["scheduleEndHour"], $form["scheduleEndMinute"], $form["scheduleEndAmpm"]);
            $timestamp_start = strtotime($local_time_start . ' +0000');
            $timestamp_end = strtotime($local_time_end . ' +0000');
            $now = current_time("timestamp");

            if( (!empty($form["scheduleStart"]) && $now < $timestamp_start) || (!empty($form["scheduleEnd"]) && $now > $timestamp_end))
                return empty($form["scheduleMessage"]) ? "<p>" . __("Sorry. This form is no longer available.", "gravityforms") . "</p>" : "<p>" . GFCommon::gform_do_shortcode($form["scheduleMessage"]) . "</p>";
        }

    }

}

?>