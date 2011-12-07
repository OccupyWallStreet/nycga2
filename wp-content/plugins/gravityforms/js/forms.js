function Form(){
    this.id = 0;
    this.title = "Untitled Form";
    this.description = "We would love to hear from you! Please fill out this form and we will get in touch with you shortly.";
    this.labelPlacement = "top_label";
    this.maxEntriesAllowed = 0;
    this.maxEntriesMessage = "";
    this.oneEntryPerIP = false;
    this.confirmation = new Confirmation();
    this.button = new Button();
    this.fields = new Array();
    this.nextFieldId = 1;
}

function Confirmation(){
    this.type = "message";
    this.message = "Thanks for contacting us! We will get in touch with you shortly.";
    this.url = "";
    this.pageId = "";
    this.queryString="";
}

function Button(){
    this.type = "text";
    this.text = "Submit";
    this.imageUrl = "";
}

function Field(id, type){
    this.id = id;
    this.label = "Untitled";
    this.adminLabel = "";
    this.type = type;
    this.isRequired = false;
    this.size = "medium";
    this.errorMessage = "";
    //NOTE: other properties will be added dynamically using associative array syntax
}

function Choice(text, value){
    this.text=text;
    this.value = value ? value : text;
    this.isSelected = false;
}

function Input(id, label){
    this.id = id;
    this.label = label;
    this.name = "";
}

var fieldSettings = {
    "hidden" :      ".prepopulate_field_setting, .label_setting, .default_value_setting",
    "section" :     ".label_setting, .description_setting, .visibility_setting, .css_class_setting",
    "text" :        ".prepopulate_field_setting, .error_message_setting, .label_setting, .admin_label_setting, .size_setting, .rules_setting, .visibility_setting, .duplicate_setting, .default_value_setting, .description_setting, .css_class_setting",
    "website" :     ".prepopulate_field_setting, .error_message_setting, .label_setting, .admin_label_setting, .size_setting, .rules_setting, .visibility_setting, .duplicate_setting, .default_value_setting, .description_setting, .css_class_setting",
    "phone" :       ".prepopulate_field_setting, .error_message_setting, .label_setting, .admin_label_setting, .rules_setting, .duplicate_setting, .visibility_setting, .default_value_setting, .description_setting, .phone_format_setting, .css_class_setting",
    "number" :      ".prepopulate_field_setting, .error_message_setting, .label_setting, .admin_label_setting, .size_setting, .range_setting, .rules_setting, .visibility_setting, .duplicate_setting, .default_value_setting, .description_setting, .css_class_setting",
    "date" :        ".prepopulate_field_setting, .error_message_setting, .label_setting, .admin_label_setting, .rules_setting, .date_input_type_setting, .duplicate_setting, .visibility_setting, .date_format_setting, .default_value_setting, .description_setting, .css_class_setting",
    "time" :        ".prepopulate_field_setting, .error_message_setting, .label_setting, .admin_label_setting, .rules_setting, .duplicate_setting, .visibility_setting, .description_setting, .css_class_setting",
    "textarea" :    ".prepopulate_field_setting, .error_message_setting, .label_setting, .admin_label_setting, .size_setting, .rules_setting, .visibility_setting, .duplicate_setting, .default_value_textarea_setting, .description_setting, .css_class_setting",
    "select" :      ".prepopulate_field_setting, .error_message_setting, .label_setting, .admin_label_setting, .size_setting, .choices_setting, .rules_setting,  .duplicate_setting, .visibility_setting, .description_setting, .css_class_setting",
    "checkbox" :    ".prepopulate_field_setting, .error_message_setting, .label_setting, .admin_label_setting, .choices_setting, .rules_setting, .visibility_setting, .description_setting, .css_class_setting",
    "radio" :       ".prepopulate_field_setting, .error_message_setting, .label_setting, .admin_label_setting, .choices_setting, .rules_setting, .visibility_setting, .duplicate_setting, .description_setting, .css_class_setting",
    "name" :        ".prepopulate_field_setting, .error_message_setting, .label_setting, .admin_label_setting, .name_format_setting, .rules_setting, .visibility_setting, .description_setting, .css_class_setting",
    "address" :     ".prepopulate_field_setting, .error_message_setting, .label_setting, .admin_label_setting, .rules_setting, .description_setting, .visibility_setting, .css_class_setting",
    "fileupload" :  ".error_message_setting, .label_setting, .admin_label_setting, .rules_setting, .file_extensions_setting, .visibility_setting, .description_setting, .css_class_setting",
    "email" :       ".prepopulate_field_setting, .error_message_setting, .label_setting, .admin_label_setting, .size_setting, .rules_setting, .visibility_setting, .duplicate_setting, .default_value_setting, .description_setting, .css_class_setting",
    "post_title" :  "  .prepopulate_field_setting, .error_message_setting, .admin_label_setting, .post_status_setting, .post_category_setting, .post_author_setting, .label_setting, .size_setting, .rules_setting, .default_value_setting, .description_setting, .css_class_setting",
    "post_content" :  ".prepopulate_field_setting, .error_message_setting, .admin_label_setting, .post_status_setting, .post_category_setting, .post_author_setting, .label_setting, .size_setting, .rules_setting, .default_value_textarea_setting, .description_setting, .css_class_setting",
    "post_excerpt" :  ".prepopulate_field_setting, .error_message_setting, .admin_label_setting, .post_status_setting, .post_category_setting, .post_author_setting, .label_setting, .size_setting, .rules_setting, .default_value_textarea_setting, .description_setting, .css_class_setting",
    "post_tags" :     ".prepopulate_field_setting, .error_message_setting, .admin_label_setting, .label_setting, .size_setting, .rules_setting, .default_value_setting, .description_setting, .css_class_setting",
    "post_category" : ".prepopulate_field_setting, .error_message_setting, .admin_label_setting, .post_category_checkbox_setting, .label_setting, .size_setting, .rules_setting, .duplicate_setting, .description_setting, .css_class_setting",
    "post_image" :    ".error_message_setting, .admin_label_setting, .post_image_setting, .label_setting, .rules_setting, .description_setting, .css_class_setting",
    "post_custom_field" : ".prepopulate_field_setting, .error_message_setting, .post_custom_field_setting, .label_setting, .size_setting, .rules_setting, .visibility_setting, .duplicate_setting, .default_value_setting, .description_setting, .css_class_setting",
    "captcha" :     ".captcha_language_setting, .captcha_theme_setting, .error_message_setting, .label_setting, .description_setting, .css_class_setting"
}


