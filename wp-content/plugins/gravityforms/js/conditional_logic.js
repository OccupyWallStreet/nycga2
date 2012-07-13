
var __gf_timeout_handle;

function gf_apply_rules(formId, fields, isInit){
    var rule_applied = 0;
    for(var i=0; i < fields.length; i++){
        gf_apply_field_rule(formId, fields[i], isInit, function(){
            rule_applied++;
            if(rule_applied == fields.length){
                jQuery(document).trigger('gform_post_conditional_logic', [formId, fields, isInit]);
                if(window["gformCalculateTotalPrice"])
                    window["gformCalculateTotalPrice"](formId);
            }
        });
    }
}

function gf_check_field_rule(formId, fieldId, isInit, callback){

    //if conditional logic is not specified for that field, it is supposed to be displayed
    if(!window["gf_form_conditional_logic"][formId] || !window["gf_form_conditional_logic"][formId]["logic"][fieldId])
        return "show";

    var conditionalLogic = window["gf_form_conditional_logic"][formId]["logic"][fieldId];

    var action = gf_get_field_action(formId, conditionalLogic["section"]);

    //If section is hidden, always hide field. If section is displayed, see if field is supposed to be displayed or hidden
    if(action != "hide")
        action = gf_get_field_action(formId, conditionalLogic["field"]);

    return action;
}

function gf_apply_field_rule(formId, fieldId, isInit, callback){

    action = gf_check_field_rule(formId, fieldId, isInit, callback);

    gf_do_field_action(formId, action, fieldId, isInit, callback);

    var conditionalLogic = window["gf_form_conditional_logic"][formId]["logic"][fieldId];
    //perform conditional logic for the next button
    if(conditionalLogic["nextButton"]){
        action = gf_get_field_action(formId, conditionalLogic["nextButton"]);
        gf_do_next_button_action(formId, action, fieldId, isInit);
    }

}

function gf_get_field_action(formId, conditionalLogic){
    if(!conditionalLogic)
        return "show";

    var matches = 0;
    for(var i = 0; i < conditionalLogic["rules"].length; i++){
        var rule = conditionalLogic["rules"][i];
        if(gf_is_match(formId, rule))
            matches++;
    }

    var action;
    if( (conditionalLogic["logicType"] == "all" && matches == conditionalLogic["rules"].length) || (conditionalLogic["logicType"] == "any"  && matches > 0) )
        action = conditionalLogic["actionType"];
    else
        action = conditionalLogic["actionType"] == "show" ? "hide" : "show";

    return action;
}

function gf_is_match(formId, rule){

    var inputs = jQuery("#input_" + formId + "_" + rule["fieldId"] + " input");

    if(inputs.length > 0){
        //handling checkboxes
        for(var i=0; i< inputs.length; i++){
            var fieldValue = gf_get_value(jQuery(inputs[i]).val());

            //find specific checkbox item
            if(fieldValue != rule["value"])
                continue;

            //blank value if item isn't checked
            if(!jQuery(inputs[i]).is(":checked"))
                fieldValue = "";

            if(gf_matches_operation(fieldValue, rule["value"], rule["operator"]))
                return true;
        }
    }
    else{
        //handling all other fields (non-checkboxes)
        var val = jQuery("#input_" + formId + "_" + rule["fieldId"]).val();

        //transform regular value into array to support multi-select (which returns an array of selected items)
        var values = (val instanceof Array) ? val : [val];

        for(var i=0; i < values.length; i++){
            var fieldValue = gf_get_value(values[i]);
            if(gf_matches_operation(fieldValue, rule["value"], rule["operator"]))
                return true;
        }
    }
    return false;
}

function gf_try_convert_float(text){
    var format = window["gf_number_format"] == "decimal_comma" ? "decimal_comma" : "decimal_dot";

    if(gformIsNumeric(text, format)){
        var decimal_separator = format == "decimal_comma" ? "," : ".";
        return gformCleanNumber(text, "", "", decimal_separator);
    }

    return text;
}

function gf_matches_operation(val1, val2, operation){
    val1 = val1 ? val1.toLowerCase() : "";
    val2 = val2 ? val2.toLowerCase() : "";

    switch(operation){
        case "is" :
            return val1 == val2;
        break;

        case "isnot" :
            return val1 != val2;
        break;

        case ">" :
            val1 = gf_try_convert_float(val1);
            val2 = gf_try_convert_float(val2);

            return val1 > val2;
        break;

        case "<" :
            val1 = gf_try_convert_float(val1);
            val2 = gf_try_convert_float(val2);

            return val1 < val2;
        break;

        case "contains" :
            return val1.indexOf(val2) >=0;
        break;

        case "starts_with" :
            return val1.indexOf(val2) ==0;
        break;

        case "ends_with" :
            var start = val1.length - val2.length;
            if(start < 0)
                return false;

            var tail = val1.substring(start);
            return val2 == tail;
        break;
    }
    return false;
}

function gf_get_value(val){
    if(!val)
        return "";

    val = val.split("|");
    return val[0];
}

function gf_do_field_action(formId, action, fieldId, isInit, callback){
    var conditional_logic = window["gf_form_conditional_logic"][formId];
    var dependent_fields = conditional_logic["dependents"][fieldId];

    for(var i=0; i < dependent_fields.length; i++){
        var targetId = fieldId == 0 ? "#gform_submit_button_" + formId : "#field_" + formId + "_" + dependent_fields[i];

        //calling callback function on the last dependent field, to make sure it is only called once
        do_callback = (i+1) == dependent_fields.length ? callback : null;

        gf_do_action(action, targetId, conditional_logic["animation"], conditional_logic["defaults"][dependent_fields[i]], isInit, do_callback);
    }
}

function gf_do_next_button_action(formId, action, fieldId, isInit){
    var conditional_logic = window["gf_form_conditional_logic"][formId];
    var targetId = "#gform_next_button_" + formId + "_" + fieldId;

    gf_do_action(action, targetId, conditional_logic["animation"], null, isInit);
}

function gf_do_action(action, targetId, useAnimation, defaultValues, isInit, callback){

    if(action == "show"){
        if(useAnimation && !isInit){
            if(jQuery(targetId).length > 0)
                jQuery(targetId).slideDown(callback);
            else if(callback)
                callback();

        }
        else{
            jQuery(targetId).show();
            if(callback){
                callback();
            }
        }
    }
    else{
        //if field is not already hidden, reset its values to the default
        var child = jQuery(targetId).children().first();

        if(!gformIsHidden(child)){
            gf_reset_to_default(targetId, defaultValues);
        }

        if(useAnimation && !isInit){
            if(jQuery(targetId).length > 0)
                jQuery(targetId).slideUp(callback);
            else if(callback)
                callback();
        }
        else{
            jQuery(targetId).hide();
            if(callback)
                callback();
        }
    }
}

function gf_reset_to_default(targetId, defaultValue){

    //cascading down conditional logic to children to suppport nested conditions
    //text fields and drop downs
    var target = jQuery(targetId).find('select, input[type="text"], input[type="number"], textarea');

    var target_index = 0;

    target.each(function(){
        var val = "";
        if(jQuery.isArray(defaultValue)){
            val = defaultValue[target_index];
        }
        else if(jQuery.isPlainObject(defaultValue)){
            val = defaultValue[jQuery(this).attr("name")];
        }
        else if(defaultValue){
            val = defaultValue;
        }

        jQuery(this).val(val).trigger('change');
        target_index++;
    });

    //checkboxes and radio buttons
    var elements = jQuery(targetId).find('input[type="radio"], input[type="checkbox"]');

    elements.each(function(){

        //is input currently checked?
        var isChecked = jQuery(this).is(':checked') ? true : false;

        //does input need to be marked as checked or unchecked?
        var doCheck = defaultValue ? jQuery.inArray(jQuery(this).attr('id'), defaultValue) > -1 : false;

        //if value changed, trigger click event
        if(isChecked != doCheck){
            //setting input as checked or unchecked appropriately
            jQuery(this).prop("checked", doCheck);

            //need to set the prop again after the click is triggered
            jQuery(this).trigger('click').prop('checked', doCheck);
        }
    });
}

