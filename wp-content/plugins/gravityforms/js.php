<script type="text/javascript">
//-------------------------------------------------
//INITIALIZING PAGE
//-------------------------------------------------
var gforms_dragging = 0;

jQuery(document).ready(function() {
    jQuery('#gform_fields').sortable({
        axis: 'y',
        cancel: '#field_settings',
        start: function(event, ui){gforms_dragging = ui.item[0].id;}
    });
    InitializeForm(form);
});


function UpdateFormProperty(name, value){
    jQuery("#gform_" + name).html(value);
}

function UpdateLabelPlacement(){
    var placement = jQuery("#form_label_placement").val();
    jQuery("#gform_fields").removeClass("top_label").removeClass("left_label").removeClass("right_label").addClass(placement);
}

function ToggleLimitEntry(isInit){
    var speed = isInit ? "" : "slow";

    if(jQuery("#gform_limit_entries").is(":checked")){
        jQuery("#gform_limit_entries_container").show(speed);
    }
    else{
        jQuery("#gform_limit_entries_container").hide(speed);
    }
}

function ToggleSchedule(isInit){
    var speed = isInit ? "" : "slow";

    if(jQuery("#gform_schedule_form").is(":checked")){
        jQuery("#gform_schedule_form_container").show(speed);
    }
    else{
        jQuery("#gform_schedule_form_container").hide(speed);
    }
}

function ToggleCategory(isInit){
    var speed = isInit ? "" : "slow";

    if(jQuery("#gfield_category_all").is(":checked")){
        jQuery("#gfield_settings_category_container").hide(speed);
         SetFieldProperty("displayAllCategories", true);
         SetFieldProperty("choices", new Array()); //reset selected categories
    }
    else{
        jQuery("#gfield_settings_category_container").show(speed);
        SetFieldProperty("displayAllCategories", false);
    }
}

function ToggleQueryString(isInit){
    var speed = isInit ? "" : "slow";
    if(jQuery('#form_redirect_use_querystring').is(":checked")){
        jQuery('#form_redirect_querystring_container').show(speed);
    }
    else{
        jQuery('#form_redirect_querystring_container').hide(speed);
        jQuery("#form_redirect_querystring").val("");
    }

}
function ToggleInputName(isInit){
    var speed = isInit ? "" : "slow";
    if(jQuery('#field_prepopulate').is(":checked")){
        jQuery('#field_input_name_container').show(speed);
    }
    else{
        jQuery('#field_input_name_container').hide(speed);
        jQuery("#field_input_name").val("");
    }

}


function ToggleConfirmation(isInit){

    var isRedirect = jQuery("#form_confirmation_redirect").is(":checked");
    var isPage = jQuery("#form_confirmation_show_page").is(":checked");

    if(isRedirect){
        show_element = "#form_confirmation_redirect_container";
        hide_element = "#form_confirmation_message_container, #form_confirmation_page_container";
    }
    else if(isPage){
        show_element = "#form_confirmation_page_container";
        hide_element = "#form_confirmation_message_container, #form_confirmation_redirect_container";
    }
    else{
        show_element = "#form_confirmation_message_container";
        hide_element = "#form_confirmation_page_container, #form_confirmation_redirect_container";
    }

    var speed = isInit ? "" : "slow";

    jQuery(hide_element).hide(speed);
    jQuery(show_element).show(speed);

}

function ToggleButton(isInit){
    var isText = jQuery("#form_button_text").is(":checked");
    show_element = isText ? "#form_button_text_container" : "#form_button_image_container"
    hide_element = isText ? "#form_button_image_container"  : "#form_button_text_container";

    var speed = isInit ? "" : "slow";

    jQuery(hide_element).hide(speed);
    jQuery(show_element).show(speed);

}


function ToggleCustomField(isInit){

    var isExisting = jQuery("#field_custom_existing").is(":checked");
    show_element = isExisting ? "#field_custom_field_name_select" : "#field_custom_field_name_text"
    hide_element = isExisting ? "#field_custom_field_name_text"  : "#field_custom_field_name_select";

    var speed = isInit ? "" : "";

    jQuery(hide_element).hide(speed);
    jQuery(show_element).show(speed);

}

function ToggleAutoresponder(){
    if(jQuery("#form_autoresponder_enabled").is(":checked"))
        jQuery("#form_autoresponder_container").show("slow");
    else
        jQuery("#form_autoresponder_container").hide("slow");
}
function DuplicateTitleMessage(){
    jQuery("#please_wait_container").hide();
    alert('<?php _e("The form title you have entered is already taken. Please enter an unique form title", "gravityforms"); ?>');
}

function SaveForm(){
    jQuery("#please_wait_container").show();

    form.title = jQuery("#form_title_input").val();
    form.description = jQuery("#form_description_input").val();
    form.labelPlacement = jQuery("#form_label_placement").val();

    form.confirmation.message = jQuery("#form_confirmation_message").val();
    form.confirmation.url = jQuery("#form_confirmation_url").val() == "http://" ? "" : jQuery("#form_confirmation_url").val();
    form.confirmation.pageId = jQuery("#form_confirmation_page").val();
    form.confirmation.queryString = jQuery("#form_redirect_querystring").val();

    if(jQuery("#form_confirmation_redirect").is(":checked") && form.confirmation.url.length > 0){
        form.confirmation.type = "redirect";
        form.confirmation.pageId = 0;
        form.confirmation.message = "";
    }
    else if(jQuery("#form_confirmation_show_page").is(":checked") && form.confirmation.pageId > 0){
        form.confirmation.type = "page";
        form.confirmation.message = "";
        form.confirmation.url = "";
        form.confirmation.queryString = "";
    }
    else{
        form.confirmation.type = "message";
        form.confirmation.url = "";
        form.confirmation.pageId = 0;
        form.confirmation.queryString = "";
    }

    form.button.type = jQuery("#form_button_text").is(":checked") ? "text" : "image";
    form.button.text = jQuery("#form_button_text_input").val();
    form.button.imageUrl = jQuery("#form_button_image_url").val();
    form.cssClass = jQuery("#form_css_class").val();
    form.postAuthor = jQuery('#field_post_author').val();
    form.useCurrentUserAsAuthor = jQuery('#gfield_current_user_as_author').is(":checked");
    form.postCategory = jQuery('#field_post_category').val();
    form.postStatus = jQuery('#field_post_status').val();

    form.limitEntries = jQuery("#gform_limit_entries").is(":checked");
    if(form.limitEntries){
        form.limitEntriesCount = jQuery("#gform_limit_entries_count").val();
        form.limitEntriesMessage = jQuery("#form_limit_entries_message").val();
    }
    else{
        form.limitEntriesCount = "";
        form.limitEntriesMessage = "";
    }

    form.scheduleForm = jQuery("#gform_schedule_form").is(":checked");
    if(form.scheduleForm){
        form.scheduleStart = jQuery("#gform_schedule_start").val();
        form.scheduleStartHour = jQuery("#gform_schedule_start_hour").val();
        form.scheduleStartMinute = jQuery("#gform_schedule_start_minute").val();
        form.scheduleStartAmpm = jQuery("#gform_schedule_start_ampm").val();
        form.scheduleEnd = jQuery("#gform_schedule_end").val();
        form.scheduleEndHour = jQuery("#gform_schedule_end_hour").val();
        form.scheduleEndMinute = jQuery("#gform_schedule_end_minute").val();
        form.scheduleEndAmpm = jQuery("#gform_schedule_end_ampm").val();
        form.scheduleMessage = jQuery("#gform_schedule_message").val();
    }
    else{
        form.scheduleStart = "";
        form.scheduleStartHour = "";
        form.scheduleStartMinute = "";
        form.scheduleStartAmpm = "";
        form.scheduleEnd = "";
        form.scheduleEndHour = "";
        form.scheduleEndMinute = "";
        form.scheduleEndAmpm = "";
        form.scheduleMessage = "";
    }

    SortFields();

    var mysack = new sack("<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php" );
    mysack.execute = 1;
    mysack.method = 'POST';
    mysack.setVar( "action", "rg_save_form" );
    mysack.setVar( "rg_save_form", "<?php echo wp_create_nonce("rg_save_form") ?>" );
    mysack.setVar( "id", form.id );
    mysack.setVar( "form", jQuery.toJSON(form) );
    mysack.encVar( "cookie", document.cookie, false );
    mysack.onError = function() { alert('<?php _e("Ajax error while setting post template", "gravityforms") ?>' )};
    mysack.runAJAX();

    return true;
}

function EndInsertForm(formId){
     jQuery("#please_wait_container").hide();

     jQuery("#edit_form_link").attr("href", "?page=gf_edit_forms&id=" + formId);
     jQuery("#notification_form_link").attr("href", "?page=gf_edit_forms&view=notification&id=" + formId);
     jQuery("#preview_form_link").attr("href", jQuery("#preview_form_link").attr("href").replace("{formid}",formId));

     jQuery("#after_insert_dialog").modal(
        {
        close:false,
        onOpen: function (dialog) {
          dialog.overlay.fadeIn('slow', function () {
            dialog.container.slideDown('slow', function () {
              dialog.data.fadeIn('slow');
            });
          });
        }});

}

function EndUpdateForm(formId){
     jQuery("#please_wait_container").hide();

     jQuery("#after_update_dialog").slideDown();
     setTimeout(function(){jQuery('#after_update_dialog').slideUp();}, 50000);
}

function SortFields(){
    var fields = new Array();
    jQuery(".gfield").each(function(){
        id = this.id.substr(6);
        fields.push(GetFieldById(id));
    }
    );
    form.fields = fields;
}
function StartDeleteField(element){
    DeleteField(jQuery(element)[0].id.split("_")[2]);
}

function DeleteField(fieldId){

    if(form.id == 0 || confirm('<?php _e("Warning! Deleting this field will also delete all entry data associated with it. \'Cancel\' to stop. \'OK\' to delete", "gravityforms"); ?>')){

        var mysack = new sack("<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php" );
        mysack.execute = 1;
        mysack.method = 'POST';
        mysack.setVar( "action", "rg_delete_field" );
        mysack.setVar( "rg_delete_field", "<?php echo wp_create_nonce("rg_delete_field") ?>" );
        mysack.setVar( "form_id", form.id );
        mysack.setVar( "field_id", fieldId );
        mysack.encVar( "cookie", document.cookie, false );
        mysack.onError = function() { alert('<?php _e("Ajax error while deleting field.", "gravityforms") ?>' )};
        mysack.runAJAX();

        return true;
    }
}
function EndDeleteField(fieldId){

    for(var i=0; i<form.fields.length; i++){
        if(form.fields[i].id == fieldId){
            form.fields.splice(i, 1);

            //moving field_settings outside the field before it is deleted
            jQuery("#field_settings").insertBefore("#gform_fields");

            jQuery('#field_' + fieldId).hide('slow', function(){jQuery('#field_' + fieldId).remove();});

            HideSettings("field_settings");
            return;
        }
    }
}


function InitializeForm(form){

    //initializing form settings
    jQuery("#form_title_input").val(form.title);
    jQuery("#gform_title").html(form.title);

    jQuery("#form_description_input").val(form.description);
    jQuery("#gform_description").html(form.description);

    jQuery("#form_label_placement").val(form.labelPlacement);

    if(!form.confirmation)
        form.confirmation = new Confirmation();

    var isRedirect = (form.confirmation.type == "redirect" && form.confirmation.url.length > 0) ? true : false;
    var isPage = (form.confirmation.type == "page" || (form.confirmation.type == "redirect" && form.confirmation.url.length == 0 && form.confirmation.pageId > 0)) ? true : false;

    jQuery("#form_confirmation_redirect").attr("checked", isRedirect);
    jQuery("#form_confirmation_show_page").attr("checked", isPage);
    jQuery("#form_confirmation_show_message").attr("checked", !isRedirect && !isPage);

    jQuery("#form_confirmation_message").text(form.confirmation.message);
    jQuery("#form_confirmation_url").val(form.confirmation.url == "" ? "http://" : form.confirmation.url);
    jQuery("#form_confirmation_page").val(form.confirmation.pageId);

    var hasQueryString = (form.confirmation.queryString != undefined && form.confirmation.queryString.length > 0);
    jQuery("#form_redirect_querystring").val(hasQueryString ? form.confirmation.queryString : "");
    jQuery("#form_redirect_use_querystring").attr("checked", hasQueryString);
    ToggleQueryString(true);

    if(!form["button"])
        form["button"] = new Button();

    jQuery("#form_button_text").attr("checked", form.button.type != "image");
    jQuery("#form_button_image").attr("checked", form.button.type == "image");
    jQuery("#form_button_text_input").val(form.button.text);
    jQuery("#form_button_image_url").val(form.button.imageUrl);
    jQuery("#form_css_class").val(form.cssClass);
    jQuery("#gform_limit_entries").attr("checked", form.limitEntries ? true : false);
    jQuery("#gform_schedule_form").attr("checked", form.scheduleForm ? true : false);
    jQuery("#gform_limit_entries_count").val(form.limitEntriesCount);
    jQuery("#form_limit_entries_message").val(form.limitEntriesMessage);
    jQuery("#gform_schedule_start").val(form.scheduleStart);
    jQuery("#gform_schedule_end").val(form.scheduleEnd);
    jQuery("#gform_schedule_message").val(form.scheduleMessage);
    jQuery("#gform_schedule_start_hour").val(form.scheduleStartHour ? form.scheduleStartHour : "12");
    jQuery("#gform_schedule_start_minute").val(form.scheduleStartMinute ? form.scheduleStartMinute : "00");
    jQuery("#gform_schedule_start_ampm").val(form.scheduleStartAmpm ? form.scheduleStartAmpm : "am");
    jQuery("#gform_schedule_end_hour").val(form.scheduleEndHour ? form.scheduleEndHour : "12");
    jQuery("#gform_schedule_end_minute").val(form.scheduleEndMinute ? form.scheduleEndMinute : "00");
    jQuery("#gform_schedule_end_ampm").val(form.scheduleEndAmpm ? form.scheduleEndAmpm : "am");

    if(form.postStatus)
        jQuery('#field_post_status').val(form.postStatus);

    if(form.postAuthor)
        jQuery('#field_post_author').val(form.postAuthor);

    //default to checked
    if(form.useCurrentUserAsAuthor == undefined)
        form.useCurrentUserAsAuthor = true;

    jQuery('#gfield_current_user_as_author').attr('checked', form.useCurrentUserAsAuthor);

    if(form.postCategory)
        jQuery('#field_post_category').val(form.postCategory);

    jQuery("#gform_heading").bind("click", function(){FieldClick(this);});
    jQuery(".gfield").bind("click", function(){FieldClick(this);});

    jQuery("#field_settings, #form_settings").tabs({selected:0});

    ToggleButton(true);
    ToggleConfirmation(true);
    ToggleSchedule(true);
    ToggleLimitEntry(true);
    InitializeFields();
}

function SetDefaultValues(field){
    switch(field.type){
        case "section" :
            field.inputs = null;
            field["displayOnly"] = true;
            break;
        case "name" :
            field.label = "<?php _e("Name", "gravityforms"); ?>";
            field.id = parseFloat(field.id);
            switch(field.nameFormat)
            {
                case "extended" :
                    field.inputs = [new Input(field.id + 0.2, '<?php echo apply_filters("gform_name_prefix", __("Prefix", "gravityforms")); ?>'), new Input(field.id + 0.3, '<?php echo apply_filters("gform_name_first",__("First", "gravityforms")); ?>'), new Input(field.id + 0.6, '<?php echo apply_filters("gform_name_last",__("Last", "gravityforms")); ?>'), new Input(field.id + 0.8, '<?php echo apply_filters("gform_name_suffix",__("Suffix", "gravityforms")); ?>')];
                break;
                case "simple" :
                    field.inputs = null;
                break;
                default :
                    field.inputs = [new Input(field.id + 0.3, '<?php echo apply_filters("gform_name_first",__("First", "gravityforms")); ?>'), new Input(field.id + 0.6, '<?php echo apply_filters("gform_name_last",__("Last", "gravityforms")); ?>')];
                break;
            }
            break;

        case "checkbox" :
            field.inputs = null;
            field.choices = new Array(new Choice("<?php _e("First Choice", "gravityforms"); ?>"), new Choice("<?php _e("Second Choice", "gravityforms"); ?>"), new Choice("<?php _e("Third Choice", "gravityforms"); ?>"));
            field.inputs = [new Input(field.id + 0.1, '<?php _e("First Choice", "gravityforms"); ?>'), new Input(field.id + 0.2, '<?php _e("Second Choice", "gravityforms"); ?>'), new Input(field.id + 0.3, '<?php _e("Third Choice", "gravityforms"); ?>')];
            break;
        case "radio" :
            field.inputs = null;
            field.choices = new Array(new Choice("<?php _e("First Choice", "gravityforms"); ?>"), new Choice("<?php _e("Second Choice", "gravityforms"); ?>"), new Choice("<?php _e("Third Choice", "gravityforms"); ?>"));
            break;
         case "select" :
            field.inputs = null;
            field.choices = new Array(new Choice("<?php _e("First Choice", "gravityforms"); ?>"), new Choice("<?php _e("Second Choice", "gravityforms"); ?>"), new Choice("<?php _e("Third Choice", "gravityforms"); ?>"));
            break;
        case "address" :
            field.label = "<?php _e("Address", "gravityforms"); ?>";
            field.inputs = [new Input(field.id + 0.1, '<?php echo apply_filters("gform_address_street",__("Street Address", "gravityforms")); ?>'), new Input(field.id + 0.2, '<?php echo apply_filters("gform_address_street2",__("Address Line 2", "gravityforms")); ?>'), new Input(field.id + 0.3, '<?php echo apply_filters("gform_address_city",__("City", "gravityforms")); ?>'),
                            new Input(field.id + 0.4, '<?php echo apply_filters("gform_address_state",__("State / Province", "gravityforms")); ?>'), new Input(field.id + 0.5, '<?php echo apply_filters("gform_address_zip",__("Zip / Postal Code", "gravityforms")); ?>'), new Input(field.id + 0.6, '<?php echo apply_filters("gform_address_country",__("Country", "gravityforms")); ?>')];
            break;
        case "email" :
            field.inputs = null;
            field.label = "<?php _e("Email", "gravityforms"); ?>";
            break;
        case "number" :
            field.inputs = null;
            field.label = "<?php _e("Number", "gravityforms"); ?>";
            break;
        case "phone" :
            field.inputs = null;
            field.label = "<?php _e("Phone", "gravityforms"); ?>";
            field.phoneFormat = "standard";
            break;
        case "date" :
            field.inputs = null;
            field.label = "<?php _e("Date", "gravityforms"); ?>";
            break;
        case "time" :
            field.inputs = null;
            field.label = "<?php _e("Time", "gravityforms"); ?>";
            break;
        case "website" :
            field.inputs = null;
            field.label = "<?php _e("Website", "gravityforms"); ?>";
            break;
        case "fileupload" :
            field.inputs = null;
            field.label = "<?php _e("File", "gravityforms"); ?>";
            break;
        case "hidden" :
            field.inputs = null;
            field.label = "<?php _e("Hidden Field", "gravityforms"); ?>";
            break;
        case "post_title" :
            field.inputs = null;
            field.label = "<?php _e("Post Title", "gravityforms"); ?>";
            break;
        case "post_content" :
            field.inputs = null;
            field.label = "<?php _e("Post Body", "gravityforms"); ?>";
            break;
        case "post_excerpt" :
            field.inputs = null;
            field.label = "<?php _e("Post Excerpt", "gravityforms"); ?>";
            field.size="small";
            break;
        case "post_tags" :
            field.inputs = null;
            field.label = "<?php _e("Post Tags", "gravityforms"); ?>";
            field.size = "large";
            break;
        case "post_custom_field" :
            field.inputs = null;
            field.label = "<?php _e("Post Custom Field", "gravityforms"); ?>";
            break;
        case "post_category" :
            field.label = "<?php _e("Post Category", "gravityforms"); ?>";
            field.inputs = null;
            field.choices = new Array();
            field.displayAllCategories = true;
            break;
        case "post_image" :
            field.label = "<?php _e("Post Image", "gravityforms"); ?>";
            field.inputs = null;
            field["allowedExtensions"] = "jpg, jpeg, png, gif";
            break;
        case "captcha" :
            field.inputs = null;
            field["displayOnly"] = true;
            field.label = "Captcha";
            break;
        default :
            field.inputs = null;
            break;
        break;
     }
}

function CreateField(id, type){
     var field = new Field(id, type);
     SetDefaultValues(field);
     return field;
}

function AddCaptchaField(){
    for(var i=0; i<form.fields.length; i++){
        if(form.fields[i].type == "captcha"){
            alert("<?php _e("Only one reCAPTCHA field can be added to the form.", "gravityforms"); ?>");
            return;
        }
    }
    StartAddField('captcha');
}

function StartAddField(type){
    var field = CreateField(form.nextFieldId++, type);

    var mysack = new sack("<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php" );
    mysack.execute = 1;
    mysack.method = 'POST';
    mysack.setVar( "action", "rg_add_field" );
    mysack.setVar( "rg_add_field", "<?php echo wp_create_nonce("rg_add_field") ?>" );
    mysack.setVar( "field", jQuery.toJSON(field) );
    mysack.encVar( "cookie", document.cookie, false );
    mysack.onError = function() { alert('<?php _e("Ajax error while adding field", "gravityforms") ?>' )};
    mysack.runAJAX();

    return true;
}

function HideSettings(element_id){
    jQuery(".field_edit_icon, .form_edit_icon").removeClass("edit_icon_expanded").addClass("edit_icon_collapsed").html('<?php _e("Edit", "gravityforms") ?>');
    jQuery("#" + element_id).hide();
}

function ShowSettings(element_id){
    jQuery(".field_selected .field_edit_icon, .field_selected .form_edit_icon").removeClass("edit_icon_collapsed").addClass("edit_icon_expanded").html('<?php _e("Close", "gravityforms") ?>');
    jQuery("#" + element_id).slideDown();
}

function EndAddField(field, fieldString){

    //sets up DOM for new field
    jQuery("#gform_fields").append(fieldString);
    var newFieldElement = jQuery("#field_" + field.id);
    newFieldElement.bind("click", function(){FieldClick(this);});

    //creates new javascript field
    form.fields.push(field);

    //Unselects all fields
    jQuery(".selectable").removeClass("field_selected");

    //Closing editors
    HideSettings("field_settings");
    HideSettings("form_settings");

    //Select current field
    newFieldElement.addClass("field_selected");

    //initializes new field with default data
    SetFieldSize(field.size);

    InitializeFields();

    newFieldElement.removeClass("field_selected");
}


function StartChangeNameFormat(format){
    field = GetSelectedField();
    field["nameFormat"] = format;
    SetFieldProperty('nameFormat', format);
    jQuery("#field_settings").slideUp(function(){StartChangeFieldType(field["type"], field);});


}

function StartChangeFieldType(type, field){
    if(type == "")
        return;

    jQuery("#field_settings").insertBefore("#gform_fields");

    if(!field)
        field = GetSelectedField();

    field["type"] = type;

    var mysack = new sack("<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php" );
    mysack.execute = 1;
    mysack.method = 'POST';
    mysack.setVar( "action", "rg_change_field_type" );
    mysack.setVar( "rg_change_field_type", "<?php echo wp_create_nonce("rg_change_field_type") ?>" );
    mysack.setVar( "field", jQuery.toJSON(field));
    mysack.encVar( "cookie", document.cookie, false );
    mysack.onError = function() { alert('<?php _e("Ajax error while adding field", "gravityforms") ?>' )};
    mysack.runAJAX();

    return true;
}

function EndChangeFieldType(fieldId, fieldType, fieldString){

    jQuery("#field_" + fieldId).html(fieldString);

    var field = GetFieldById(fieldId);
    field.type = fieldType;
    SetDefaultValues(field);

    SetFieldLabel(field.label);
    SetFieldSize(field.size);
    SetFieldDefaultValue(field.defaultValue);
    SetFieldDescription(field.description);
    SetFieldRequired(field.isRequired);
    InitializeFields();

    LoadFieldSettings();
}


function InitializeFields(){
    //Border on/off logic on mouse over
    jQuery(".selectable").hover(
      function () {
        jQuery(this).addClass("field_hover");
      },
      function () {
        jQuery(this).removeClass("field_hover");
      }
    );

    jQuery(".field_delete_icon").bind("click", function(event){
        event.stopPropagation();
        }
    );


    jQuery("#field_settings, #form_settings, .captcha_message, .form_delete_icon").bind("click", function(event){event.stopPropagation();});

   UpdateLabelPlacement();
}

function FieldClick(field){

    //disable click that happens right after dragging ends
    if(gforms_dragging == field.id){
        gforms_dragging = 0;
        return;
    }

    if(jQuery(field).hasClass("field_selected"))
    {
        var element_id = field.id == "gform_heading" ? "#form_settings" : "#field_settings";
        jQuery(element_id).slideUp(function(){jQuery(field).removeClass("field_selected").addClass("field_hover"); HideSettings("field_settings");});

        return;
    }

    //unselects all fields
    jQuery(".selectable").removeClass("field_selected");

    //selects current field
    jQuery(field).removeClass("field_hover").addClass("field_selected");

    //if this is a field (not the form title), load appropriate field type settings
    if(field.id != "gform_heading"){

        //hide form settings
        HideSettings("form_settings");

        //selects current field
        LoadFieldSettings();
    }
    else{
        //hide field settings
        HideSettings("field_settings");

        //Displaying form settings
        ShowSettings("form_settings");
    }
}
function CustomFieldExists(name){
    if(!name)
        return true;

    var options = jQuery("#field_custom_field_name_select option");
    for(var i=0; i<options.length; i++)
    {
        if(options[i].value == name)
            return true;
    }
    return false;
}

function LoadFieldSettings(){

    //loads settings
    field = GetSelectedField();
    jQuery("#field_label").val(field.label);
    jQuery("#field_admin_label").val(field.adminLabel);
    jQuery("#field_type").val(field.type);
    jQuery("#field_size").val(field.size);
    jQuery("#field_required").attr("checked", field.isRequired == true);
    jQuery("#field_no_duplicates").attr("checked", field.noDuplicates == true);
    jQuery("#field_default_value").val(field.defaultValue == undefined ? "" : field.defaultValue);
    jQuery("#field_default_value_textarea").val(field.defaultValue == undefined ? "" : field.defaultValue);
    jQuery("#field_description").val(field.description == undefined ? "" : field.description);
    jQuery("#field_css_class").val(field.cssClass == undefined ? "" : field.cssClass);
    jQuery("#field_range_min").val(field.rangeMin);
    jQuery("#field_range_max").val(field.rangeMax);
    jQuery("#field_name_format").val(field.nameFormat);
    jQuery("#field_visibility_admin").attr("checked", field.adminOnly);
    jQuery("#field_visibility_everyone").attr("checked", !field.adminOnly);
    jQuery("#field_file_extension").val(field.allowedExtensions == undefined ? "" : field.allowedExtensions);
    jQuery("#field_phone_format").val(field.phoneFormat);
    jQuery("#field_error_message").val(field.errorMessage);
    jQuery('#field_captcha_theme').val(field.captchaTheme == undefined ? "red" : field.captchaTheme);
    jQuery('#field_captcha_language').val(field.captchaLanguage == undefined ? "en" : field.captchaLanguage);

    jQuery("#gfield_display_title").attr("checked", field.displayTitle == true);
    jQuery("#gfield_display_caption").attr("checked", field.displayCaption == true);
    jQuery("#gfield_display_description").attr("checked", field.displayDescription == true);

    var customFieldExists = CustomFieldExists(field.postCustomFieldName);
    jQuery("#field_custom_field_name_select")[0].selectedIndex = 0;

    jQuery("#field_custom_field_name_text").val("");
    if(customFieldExists)
        jQuery("#field_custom_field_name_select").val(field.postCustomFieldName);
    else
        jQuery("#field_custom_field_name_text").val(field.postCustomFieldName);

    jQuery("#field_custom_existing").attr("checked", customFieldExists);
    jQuery("#field_custom_new").attr("checked", !customFieldExists);
    ToggleCustomField(true);

    jQuery("#gfield_category_all").attr("checked", field.displayAllCategories);
    jQuery("#gfield_category_select").attr("checked", !field.displayAllCategories);
    ToggleCategory(true);

    jQuery("#field_date_input_type").val(field["dateType"] == "datefield" ? "datefield" : "datepicker");
    jQuery("#gfield_calendar_icon_url").val(field["calendarIconUrl"] == undefined ? "" : field["calendarIconUrl"]);
    jQuery('#field_date_format').val(field['dateFormat'] == "dmy" ? "dmy" : "mdy");

    SetCalendarIconType(field["calendarIconType"], true);

    ToggleDateCalendar(true);
    LoadDateInputs();

    field.allowsPrepopulate = field.allowsPrepopulate ? true : false; //needed when property is undefined

    jQuery("#field_prepopulate").attr("checked", field.allowsPrepopulate ? true : false);
    CreateInputNames(field);
    ToggleInputName(true);

    jQuery(".gfield_category_checkbox").each(function(){
        if(field["choices"]){
            for(var i=0; i<field["choices"].length; i++){
                if(this.value == field["choices"][i].value){
                    this.checked = true;
                    return;
                }
            }
        }
        this.checked = false;
    });

    if(has_entry(field.id))
        jQuery("#field_type, #field_name_format").attr("disabled", "disabled");
    else
        jQuery("#field_type, #field_name_format").attr("disabled", "");

    jQuery("#field_custom_field_name").val(field.postCustomFieldName);

    LoadFieldChoices(field);

    //displays appropriate settings
    jQuery(".field_setting").hide();
    jQuery(fieldSettings[field.type]).show();

    //hide post category drop down if post category field is in the form
    for(var i=0; i<form.fields.length; i++){
        if(form.fields[i].type == "post_category"){
            jQuery(".post_category_setting").hide();
            break;
        }
    }

    jQuery("#field_settings").appendTo(".field_selected").tabs("select", 0);
    ShowSettings("field_settings");

}

function CreateInputNames(field){
    var field_str = "";
    if(!field["inputs"] || field["type"] == "checkbox"){
        field_str = "<label for='field_input_name' class='inline'><?php _e("Parameter Name:", "gravityforms"); ?> </label>";
        field_str += "<input type='text' value=" + field["inputName"] + " id='field_input_name' onkeyup='SetInputName(this.value);'/>";
    }
    else{
        field_str = "<table><tr><td><strong>Field</strong></td><td><strong>Parameter Name</strong></td></tr>";
        for(var i=0; i<field["inputs"].length; i++){
            field_str += "<tr><td><label for='field_input_" + field["inputs"][i]["id"] + "' class='inline'>" + field["inputs"][i]["label"] + "</label></td>";
            field_str += "<td><input type='text' value='" + field["inputs"][i]["name"] + "' id='field_input_" + field["inputs"][i]["id"] + "' onkeyup=\"SetInputName(this.value, '" + field["inputs"][i]["id"] + "');\"/></td><tr>";
        }
    }

    jQuery("#field_input_name_container").html(field_str);
}

function LoadFieldChoices(field){

    //loading ui
    jQuery("#field_choices").html(GetFieldChoices(field));

    //loading bulk input
    LoadBulkChoices(field);
}
function LoadBulkChoices(field){
    if(!field.choices)
        return;

    var choices = new Array();
    for(var i=0; i<field.choices.length; i++)
        choices.push(field.choices[i].text);

    jQuery("#gfield_bulk_add_input").val(choices.join("\n"));
}

function GetFieldChoices(field){
    var imagesUrl = '<?php echo self::get_base_url() . "/images"?>';
    if(field.choices == undefined)
        return "";

    var str = "";
    for(var i=0; i<field.choices.length; i++){
        var checked = field.choices[i].isSelected ? "checked" : "";
        var type = field.type == 'checkbox' ? 'checkbox' : 'radio';
        str += "<li><input type='" + type + "' class='gfield_choice_" + type + "' name='choice_selected' id='choice_selected_" + i + "' " + checked + " onclick='SetFieldChoice(" + i + ");' /><input type='text' id='choice_text_" + i + "' value=\"" + field.choices[i].text.replace("\"", "&quot;") + "\" onkeyup=\"SetFieldChoice(" + i + ");\" class='field-choice-input' />";
        str += "<img src='" + imagesUrl + "/add.png' class='add_field_choice' title='add another choice' alt='add another choice' style='cursor:pointer; margin:0 3px;' onclick=\"InsertFieldChoice(" + (i+1) + ");\" />";

        if(field.choices.length > 1 )
            str += "<img src='" + imagesUrl + "/remove.png' title='remove this choice' alt='remove this choice' class='delete_field_choice' style='cursor:pointer;' onclick=\"DeleteFieldChoice(" + i + ");\" /></li>";
    }
    return str;
}


function SetFieldChoice(index, value){
    value = jQuery("#choice_text_" + index).val();
    var element = jQuery("#choice_selected_" + index);
    isSelected = element.is(":checked");
    isRadio = element.is(":radio");

    field = GetSelectedField();
    field.choices[index].value = value;
    field.choices[index].text = value;

    //set field selections
    jQuery("#field_choices :radio, #field_choices :checkbox").each(function(index){
        field.choices[index].isSelected = this.checked;
    });

    LoadBulkChoices(field);
    UpdateFieldChoices(field.type);
}

function UpdateFieldChoices(fieldType){
    var choices = '';
    var selector = '';

    if(fieldType == "checkbox")
        field.inputs = new Array();

    for(var i=0; i<field.choices.length; i++)
    {
        switch(fieldType){
            case "select" :
                selected = field.choices[i].isSelected ? "selected='selected'" : "";
                choices += "<option value='" + field.choices[i].value + "' " + selected + ">" + field.choices[i].text + "</option>";
            break;

            case "checkbox" :
                field.inputs.push(new Input(field.id + '.' + (i+1), field.choices[i].text));
            case "radio" :
                var id = 'choice_' + field.id + '_' + i;
                checked = field.choices[i].isSelected ? "checked" : "";
                choices += "<li><input type='" + fieldType + "' " + checked + " id='" + id +"' disabled='disabled'><label for='" + id + "'>" + field.choices[i].text + "</label></li>";
            break;
        }
    }

    selector = '.gfield_' + fieldType;
    jQuery(".field_selected " + selector).html(choices);
}

function InsertFieldChoice(index){
    field = GetSelectedField();
    field.choices.splice(index, 0, new Choice(""));
    LoadFieldChoices(field);
    UpdateFieldChoices(field.type);
}

function InsertBulkChoices(choices){
    field = GetSelectedField();
    field.choices = new Array();
    for(var i=0; i<choices.length; i++)
        field.choices.push(new Choice(choices[i]));

    LoadFieldChoices(field);
    UpdateFieldChoices(field.type);
}

function DeleteFieldChoice(index){
    field = GetSelectedField();
    field.choices.splice(index, 1);
    LoadFieldChoices(field);
    UpdateFieldChoices(field.type);
}

function GetFieldType(fieldId){
    return fieldId.substr(0, fieldId.lastIndexOf("_"));
}

function GetSelectedField(){
    var id = jQuery(".field_selected")[0].id.substr(6);
    return GetFieldById(id);
}

function GetFieldById(id){
    for(var i=0; i<form.fields.length; i++){
        if(form.fields[i].id == id)
            return form.fields[i];
    }
    return null;
}

function ToggleDateCalendar(isInit){
    var speed = isInit ? "" : "slow";

    if(jQuery("#field_date_input_type").val() == "datefield"){
        jQuery("#date_picker_container").hide(speed);
        SetCalendarIconType("none");
    }
    else{
        jQuery("#date_picker_container").show(speed);
    }
}

function ToggleCalendarIconUrl(isInit){
    var speed = isInit ? "" : "slow";

    if(jQuery("#gsetting_icon_custom").is(":checked")){
        jQuery("#gfield_icon_url_container").show(speed);
    }
    else{
        jQuery("#gfield_icon_url_container").hide(speed);
        jQuery("#gfield_calendar_icon_url").val("");
        SetFieldProperty('calendarIconUrl', '');
    }
}


function SetDateFormat(format){
    SetFieldProperty('dateFormat', format);
    LoadDateInputs();
}

function LoadDateInputs(){
    var type = jQuery("#field_date_input_type").val();
    var format = jQuery("#field_date_format").val();

    if(type == "datefield"){
        if(format == "mdy")
            jQuery(".field_selected #gfield_input_date_month").remove().insertBefore(".field_selected #gfield_input_date_day");
        else
            jQuery(".field_selected #gfield_input_date_month").remove().insertAfter(".field_selected #gfield_input_date_day");

        jQuery(".field_selected .ginput_date").show();

        jQuery(".field_selected #gfield_input_datepicker").hide();
        jQuery(".field_selected #gfield_input_datepicker_icon").hide();
    }
    else{
        jQuery(".field_selected .ginput_date").hide();
        jQuery(".field_selected #gfield_input_datepicker").show();

        //Displaying or hiding the calendar icon
        if(jQuery("#gsetting_icon_calendar").is(":checked"))
            jQuery(".field_selected #gfield_input_datepicker_icon").show();
        else
            jQuery(".field_selected #gfield_input_datepicker_icon").hide();
    }
}

function SetCalendarIconType(iconType, isInit){
    if(iconType == undefined)
        iconType = "none";

    jQuery("#gsetting_icon_none").attr("checked", iconType == "none");
    jQuery("#gsetting_icon_calendar").attr("checked", iconType == "calendar");
    jQuery("#gsetting_icon_custom").attr("checked", iconType == "custom");

    SetFieldProperty('calendarIconType', iconType);
    ToggleCalendarIconUrl(isInit);
    LoadDateInputs();
}

function SetDateInputType(type){
    SetFieldProperty('dateType', type);
    ToggleDateCalendar();
    LoadDateInputs();
}

function SetPostImageMeta(){
    var displayTitle = jQuery('.field_selected #gfield_display_title').is(":checked");
    var displayCaption = jQuery('.field_selected #gfield_display_caption').is(":checked");
    var displayDescription = jQuery('.field_selected #gfield_display_description').is(":checked");
    var displayLabel = (displayTitle || displayCaption || displayDescription);

    //setting property
    SetFieldProperty('displayTitle', displayTitle);
    SetFieldProperty('displayCaption', displayCaption);
    SetFieldProperty('displayDescription', displayDescription);

    //updating UI
    jQuery('.field_selected .ginput_post_image_title').css("display", displayTitle ? "block" : "none");
    jQuery('.field_selected .ginput_post_image_caption').css("display", displayCaption ? "block" : "none");
    jQuery('.field_selected .ginput_post_image_description').css("display", displayDescription ? "block" : "none");
    jQuery('.field_selected .ginput_post_image_file').css("display", displayLabel ? "block" : "none");
}

function SetFieldProperty(name, value){
    if(value == undefined)
        value = "";

    GetSelectedField()[name] = value;
}

function SetInputName(value, inputId){
    var field = GetSelectedField();
    if(!inputId){
        field["inputName"] = value;
    }
    else{
        for(var i=0; i<field["inputs"].length; i++){
            if(field["inputs"][i]["id"] == inputId){
                field["inputs"][i]["name"] = value;
            }
        }
    }
}


function SetSelectedCategories(){
    var field = GetSelectedField();
    field["choices"] = new Array();

    jQuery(".gfield_category_checkbox").each(function(){
        if(this.checked)
            field["choices"].push(new Choice(this.name, this.value));
    });

    field["choices"].sort(function(a, b){return (a["text"] > b["text"]);});
}

function SetFieldLabel(label){
    var requiredElement = jQuery(".field_selected .gfield_required")[0];
    jQuery(".field_selected .gfield_label, .field_selected .gsection_title").text(label).append(requiredElement);
    SetFieldProperty("label", label);
}

function SetCaptchaTheme(theme, thumbnailUrl){
    var requiredElement = jQuery(".field_selected .gfield_required")[0];
    jQuery(".field_selected .gfield_captcha").attr("src", thumbnailUrl);
    SetFieldProperty("captchaTheme", theme);
}

function SetFieldSize(size){
    jQuery(".field_selected .small, .field_selected .medium, .field_selected .large").removeClass("small").removeClass("medium").removeClass("large").addClass(size);
    SetFieldProperty("size", size);
}

function SetFieldAdminOnly(isAdminOnly){
    SetFieldProperty('adminOnly', isAdminOnly);
    if(isAdminOnly)
        jQuery(".field_selected").addClass("field_admin_only");
    else
        jQuery(".field_selected").removeClass("field_admin_only");
}

function SetFieldPhoneFormat(phoneFormat){
    var instruction = phoneFormat == "standard" ? "<?php _e("Phone format:", "gravityforms"); ?> (###)###-####" : "";
    var display = phoneFormat == "standard" ? "block" : "none";

    jQuery(".field_selected .instruction").css('display', display).html(instruction);

    SetFieldProperty('phoneFormat', phoneFormat);
}

function SetFieldDefaultValue(defaultValue){
    jQuery(".field_selected > div > input, .field_selected > div > textarea").val(defaultValue);
    SetFieldProperty('defaultValue', defaultValue);
}

function SetFieldDescription(description){
    if(description == undefined)
        description = "";

    jQuery(".field_selected .gfield_description, .field_selected .gsection_description").html(description);

    SetFieldProperty('description', description);
}

function SetFieldRequired(isRequired){
    var required = isRequired ? "*" : "";
    jQuery(".field_selected .gfield_required").html(required);
    SetFieldProperty('isRequired', isRequired);
}

function LoadMessageVariables(){
    var options = "<option><?php _e("Select a field", "gravityforms"); ?></option><option value='{form_title}'><?php _e("Form Title", "gravityforms"); ?></option><option value='{date_mdy}'><?php _e("Date", "gravityforms"); ?> (mm/dd/yyyy)</option><option value='{date_dmy}'><?php _e("Date", "gravityforms"); ?> (dd/mm/yyyy)</option><option value='{ip}'><?php _e("User IP Address", "gravityforms"); ?></option><option value='{all_fields}'><?php _e("All Submitted Fields", "gravityforms"); ?></option>";

    for(var i=0; i<form.fields.length; i++)
        options += "<option value='{" + form.fields[i].label + ":" + form.fields[i].id + "}'>" + form.fields[i].label + "</option>";

    jQuery("#form_autoresponder_variable").html(options);
}



</script>
