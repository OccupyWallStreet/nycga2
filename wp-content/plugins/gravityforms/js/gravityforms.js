

//------------------------------------------------
//---------- CURRENCY ----------------------------
//------------------------------------------------
function Currency(currency){
    this.currency = currency;

    this.toNumber = function(text){
        if(this.isNumeric(text))
            return parseFloat(text);

        //converting to a string if a number as passed
        text = text + " ";

        //Removing symbol in unicode format (i.e. &#4444;)
        text = text.replace(/&.*?;/, "", text);

        //Removing symbol from text
        text = text.replace(this.currency["symbol_right"], "");
        text = text.replace(this.currency["symbol_left"], "");


        //Removing all non-numeric characters
        var clean_number = "";
        var is_negative = false;
        for(var i=0; i<text.length; i++){
            var digit = text.substr(i,1);
            if( (parseInt(digit) >= 0 && parseInt(digit) <= 9) || digit == this.currency["decimal_separator"] )
                clean_number += digit;
            else if(digit == '-')
                is_negative = true;
        }

        //Removing thousand separators but keeping decimal point
        var float_number = "";
        var decimal_separator = this.currency && this.currency["decimal_separator"] ? this.currency["decimal_separator"] : ".";

        for(var i=0; i<clean_number.length; i++)
        {
            var char = clean_number.substr(i,1);
            if (char >= '0' && char <= '9')
                float_number += char;
            else if(char == decimal_separator){
                float_number += ".";
            }
        }

        if(is_negative)
            float_number = "-" + float_number;

        return this.isNumeric(float_number) ? parseFloat(float_number) : false;
    };

    this.toMoney = function(number){
        if(!this.isNumeric(number))
            number = this.toNumber(number);

        if(number === false)
            return "";

        number = number + "";
        negative = "";
        if(number[0] == "-"){
            negative = "-";
            number = parseFloat(number.substr(1));
        }
        money = this.numberFormat(number, this.currency["decimals"], this.currency["decimal_separator"], this.currency["thousand_separator"]);

        var symbol_left = this.currency["symbol_left"] ? this.currency["symbol_left"] + this.currency["symbol_padding"] : "";
        var symbol_right = this.currency["symbol_right"] ? this.currency["symbol_padding"] + this.currency["symbol_right"] : "";
        money =  negative + this.htmlDecode(symbol_left) + money + this.htmlDecode(symbol_right);
        return money;
    };

    this.numberFormat = function(number, decimals, dec_point, thousands_sep){
        number = (number+'').replace(',', '').replace(' ', '');
        var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep, dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',

        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };

        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }

        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }

        return s.join(dec);
    }

    this.isNumeric = function(number){
        return !isNaN(parseFloat(number)) && isFinite(number);
    };


    this.htmlDecode = function(text) {
        var c,m,d = text;

        // look for numerical entities &#34;
        var arr=d.match(/&#[0-9]{1,5};/g);

        // if no matches found in string then skip
        if(arr!=null){
            for(var x=0;x<arr.length;x++){
                m = arr[x];
                c = m.substring(2,m.length-1); //get numeric part which is refernce to unicode character
                // if its a valid number we can decode
                if(c >= -32768 && c <= 65535){
                    // decode every single match within string
                    d = d.replace(m, String.fromCharCode(c));
                }else{
                    d = d.replace(m, ""); //invalid so replace with nada
                }
            }
        }
        return d;
    };
}


//------------------------------------------------
//---------- MULTI-PAGE --------------------------
//------------------------------------------------
function gformDeleteUploadedFile(formId, fieldId){
    var parent = jQuery("#field_" + formId + "_" + fieldId);

    //hiding preview
    parent.find(".ginput_preview").hide();


    //displaying file upload field
    parent.find("input[type=\"file\"]").removeClass("gform_hidden");

    //displaying post image label
    parent.find(".ginput_post_image_file").show();

    //clearing post image meta fields
    parent.find("input[type=\"text\"]").val('');

    //removing file from uploaded meta
    var files = jQuery.secureEvalJSON(jQuery('#gform_uploaded_files_' + formId).val());
    if(files){
        files["input_" + fieldId] = null;
        jQuery('#gform_uploaded_files_' + formId).val(jQuery.toJSON(files));
    }
}


//------------------------------------------------
//---------- PRICE -------------------------------
//------------------------------------------------
var _gformPriceFields = new Array();
var _anyProductSelected;

function gformIsHidden(element){
    return element.parents('.gfield').not(".gfield_hidden_product").css("display") == "none";
}

function gformCalculateTotalPrice(formId){

    if(!_gformPriceFields[formId])
        return;

    var price = 0;

    _anyProductSelected = false; //Will be used by gformCalculateProductPrice().
    for(var i=0; i<_gformPriceFields[formId].length; i++){
        price += gformCalculateProductPrice(formId, _gformPriceFields[formId][i]);
    }

    //add shipping price if a product has been selected
    if(_anyProductSelected){
        //shipping price
        var shipping = gformGetShippingPrice(formId)
        price += shipping;
    }

    //gform_product_total filter. Allows uers to perform custom price calculation
    if(window["gform_product_total"])
        price = window["gform_product_total"](formId, price);

    //updating total
    var totalElement = jQuery(".ginput_total_" + formId);
    if(totalElement.length > 0){
        totalElement.next().val(price);
        totalElement.html(gformFormatMoney(price));
    }
}



function gformGetShippingPrice(formId){
    var shippingField = jQuery(".gfield_shipping_" + formId + " input[type=\"hidden\"], .gfield_shipping_" + formId + " select, .gfield_shipping_" + formId + " input:checked");
    var shipping = 0;
    if(shippingField.length == 1 && !gformIsHidden(shippingField)){
        if(shippingField.attr("type") && shippingField.attr("type").toLowerCase() == "hidden")
            shipping = shippingField.val();
        else
            shipping = gformGetPrice(shippingField.val());
    }

    return gformToNumber(shipping);
}

function gformCalculateProductPrice(formId, productFieldId){
    var price = gformGetBasePrice(formId, productFieldId);

    var suffix = "_" + formId + "_" + productFieldId;

    //Drop down auto-calculating labels
    jQuery(".gfield_option" + suffix + " select, .gfield_shipping_" + formId + " select").each(function(){
        var selected_price = gformGetPrice(jQuery(this).val());
        jQuery(this).children("option").each(function(){
            var label = gformGetOptionLabel(this, jQuery(this).val(), selected_price);
            jQuery(this).html(label);
        });
    });

    //Checkboxes labels with prices
    jQuery(".gfield_option" + suffix + " .gfield_checkbox input").each(function(){
        var element = jQuery(this).next();
        var label = gformGetOptionLabel(element, jQuery(this).val(), 0);
        element.html(label);
    });

    //Radio button auto-calculating lables
    jQuery(".gfield_option" + suffix + " .gfield_radio, .gfield_shipping_" + formId + " .gfield_radio").each(function(){
        var selected_price = 0;
        var selected_value = jQuery(this).find("input:checked").val();
        if(selected_value)
            selected_price = gformGetPrice(selected_value);

        jQuery(this).find("input").each(function(){
            var label_element = jQuery(this).next();
            var label = gformGetOptionLabel(label_element, jQuery(this).val(), selected_price);
            label_element.html(label);
        });
    });

    jQuery(".gfield_option" + suffix + " input:checked, .gfield_option" + suffix + " select").each(function(){
        if(!gformIsHidden(jQuery(this)))
            price += gformGetPrice(jQuery(this).val());
    });

    var quantity;
    var quantityInput = jQuery("#ginput_quantity_" + formId + "_" + productFieldId);
    if(quantityInput.length > 0){
        quantity = !gformIsNumber(quantityInput.val()) ? 0 : quantityInput.val();
    }
    else{
        quantityElement = jQuery(".gfield_quantity_" + formId + "_" + productFieldId);

        quantity = 1;
        if(quantityElement.find("input").length > 0)
            quantity = quantityElement.find("input").val();
        else if (quantityElement.find("select").length > 0)
            quantity = quantityElement.find("select").val();

        if(!gformIsNumber(quantity))
            quantity = 0
    }
    quantity = parseFloat(quantity);

    //setting global variable if quantity is more than 0 (a product was selected). Will be used when calculating total
    if(quantity > 0)
        _anyProductSelected = true;

    price = price * quantity;
    price = Math.round(price * 100) / 100;

    return price;
}

function gformGetBasePrice(formId, productFieldId){

    var suffix = "_" + formId + "_" + productFieldId;
    var price = 0;
    var productField = jQuery("#ginput_base_price" + suffix+ ", .gfield_donation" + suffix + " input[type=\"text\"], .gfield_product" + suffix + " .ginput_amount");
    if(productField.length > 0){
        price = productField.val();

        //If field is hidden by conditional logic, don't count it for the total
        if(gformIsHidden(productField)){
            price = 0;
        }
        else if(productField.parents(".gfield_donation" + suffix).length > 0 || productField.parents(".gfield_product" + suffix).length > 0){
            //Formatting open text donation and product fields
            var currency = new Currency(window['gf_currency_config']);
            productField.val(currency.toMoney(price));
        }
    }
    else
    {
        productField = jQuery(".gfield_product" + suffix + " select, .gfield_product" + suffix + " input:checked, .gfield_donation" + suffix + " select, .gfield_donation" + suffix + " input:checked");
        var val = productField.val();
        if(val){
            val = val.split("|");
            price = val.length > 1 ? val[1] : 0;
        }

        //If field is hidden by conditional logic, don't count it for the total
        if(gformIsHidden(productField))
            price = 0;

    }

    var c = new Currency(window['gf_currency_config']);
    price = c.toNumber(price);
    return price === false ? 0 : price;
}

function gformFormatMoney(text){
    if(!window['gf_currency_config'])
        return text;

    var currency = new Currency(window['gf_currency_config']);
    return currency.toMoney(text);
}

function gformToNumber(text){
    var currency = new Currency(window['gf_currency_config']);
    return currency.toNumber(text);
}

function gformGetPriceDifference(currentPrice, newPrice){

    //getting price difference
    var diff = parseFloat(newPrice) - parseFloat(currentPrice);
    price = gformFormatMoney(diff);
    if(diff > 0)
        price = "+" + price;

    return price;
}

function gformGetOptionLabel(element, selected_value, current_price){
    element = jQuery(element);
    var price = gformGetPrice(selected_value);
    var current_diff = element.attr('price');
    var label = element.html().replace(/<span(.*)<\/span>/i, "").replace(current_diff, "");

    var diff = gformGetPriceDifference(current_price, price);
    diff = gformToNumber(diff) == 0 ? "" : " " + diff;
    element.attr('price', diff);

    //don't add <span> for drop down items (not supported)
    var label = element[0].tagName.toLowerCase() == "option" ? label + " " + diff : label + "<span class='ginput_price'>" + diff + "</span>";
    return label;
}

function gformGetProductIds(parent_class, element){
    var classes = jQuery(element).hasClass(parent_class) ? jQuery(element).attr("class").split(" ") : jQuery(element).parents("." + parent_class).attr("class").split(" ");
    for(var i=0; i<classes.length; i++){
        if(classes[i].substr(0, parent_class.length) == parent_class && classes[i] != parent_class)
            return {formId: classes[i].split("_")[2], productFieldId: classes[i].split("_")[3]};
    }
    return {formId:0, fieldId:0};
}

function gformGetPrice(text){
    var val = text.split("|");
    var currency = new Currency(window['gf_currency_config']);

    if(val.length > 1 && currency.toNumber(val[1]) !== false)
         return currency.toNumber(val[1]);

    return 0;
}

function gformIsNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}

function gformRegisterPriceField(item){

    if(!_gformPriceFields[item.formId])
        _gformPriceFields[item.formId] = new Array();

    //ignore price fields that have already been registered
    for(var i=0; i<_gformPriceFields[item.formId].length; i++)
        if(_gformPriceFields[item.formId][i] == item.productFieldId)
            return;

    //registering new price field
    _gformPriceFields[item.formId].push(item.productFieldId);
}

function gformInitPriceFields(){

    jQuery(".gfield_price").each(function(){

        var productIds = gformGetProductIds("gfield_price", this);
        gformRegisterPriceField(productIds);

       jQuery(this).find("input[type=\"text\"], select").change(function(){
           var productIds = gformGetProductIds("gfield_price", this);
           if(productIds.formId == 0)
                productIds = gformGetProductIds("gfield_shipping", this);
           gformCalculateTotalPrice(productIds.formId);
       });

       jQuery(this).find("input[type=\"radio\"], input[type=\"checkbox\"]").click(function(){
           var productIds = gformGetProductIds("gfield_price", this);
           if(productIds.formId == 0)
                productIds = gformGetProductIds("gfield_shipping", this);
           gformCalculateTotalPrice(productIds.formId);
       });
    });

    for(formId in _gformPriceFields)
        gformCalculateTotalPrice(formId);

}


//-------------------------------------------
//---------- PASSWORD -----------------------
//-------------------------------------------
function gformShowPasswordStrength(fieldId){
    var password = jQuery("#" + fieldId).val();
    var confirm = jQuery("#" + fieldId + "_2").val();

    var result = gformPasswordStrength(password, confirm);

    var text = window['gf_text']["password_" + result];

    jQuery("#" + fieldId + "_strength").val(result);
    jQuery("#" + fieldId + "_strength_indicator").removeClass("blank mismatch short good bad strong").addClass(result).html(text);
}

// Password strength meter
function gformPasswordStrength(password1, password2) {
    var shortPass = 1, badPass = 2, goodPass = 3, strongPass = 4, mismatch = 5, symbolSize = 0, natLog, score;

    if(password1.length <=0)
        return "blank";

    // password 1 != password 2
    if ( (password1 != password2) && password2.length > 0)
        return "mismatch";

    //password < 4
    if ( password1.length < 4 )
        return "short";

    if ( password1.match(/[0-9]/) )
        symbolSize +=10;
    if ( password1.match(/[a-z]/) )
        symbolSize +=26;
    if ( password1.match(/[A-Z]/) )
        symbolSize +=26;
    if ( password1.match(/[^a-zA-Z0-9]/) )
        symbolSize +=31;

    natLog = Math.log( Math.pow(symbolSize, password1.length) );
    score = natLog / Math.LN2;

    if (score < 40 )
        return "bad";

    if (score < 56 )
        return "good";

    return "strong";

}

//----------------------------
//------ LIST FIELD ----------
//----------------------------
var gfield_original_title = "";
function gformAddListItem(element, max){

    if(jQuery(element).hasClass("gfield_icon_disabled"))
        return;

    var tr = jQuery(element).parent().parent();
    var clone = tr.clone();
    clone.find("input, select").val("").attr("tabindex", clone.find('input:last').attr("tabindex"));
    tr.after(clone);
    gformToggleIcons(tr.parent(), max);
    gformAdjustClasses(tr.parent());
}

function gformDeleteListItem(element, max){
    var tr = jQuery(element).parent().parent();
    var parent = tr.parent();
    tr.remove();
    gformToggleIcons(parent, max);
    gformAdjustClasses(parent);
}

function gformAdjustClasses(table){
    var rows = table.children();
    for(var i=0; i<rows.length; i++){
        var odd_even_class = (i+1) % 2 == 0 ? "gfield_list_row_even" : "gfield_list_row_odd";
        jQuery(rows[i]).removeClass("gfield_list_row_odd").removeClass("gfield_list_row_even").addClass(odd_even_class);
    }
}

function gformToggleIcons(table, max){
    var rowCount = table.children().length;
    if(rowCount == 1){
        table.find(".delete_list_item").css("visibility", "hidden");
    }
    else{
        table.find(".delete_list_item").css("visibility", "visible");
    }

    if(max > 0 && rowCount >= max){
        gfield_original_title = table.find(".add_list_item:first").attr("title");
        table.find(".add_list_item").addClass("gfield_icon_disabled").attr("title", "");
    }
    else{
        var addIcons = table.find(".add_list_item");
        addIcons.removeClass("gfield_icon_disabled");
        if(gfield_original_title)
            addIcons.attr("title", gfield_original_title);
    }
}

function gformMatchCard(id) {

    var cardType = gformFindCardType(jQuery('#' + id).val());
    var cardContainer = jQuery('#' + id).parents('.gfield').find('.gform_card_icon_container');

    if(!cardType) {

        jQuery(cardContainer).find('.gform_card_icon').removeClass('gform_card_icon_selected gform_card_icon_inactive');

    } else {

        jQuery(cardContainer).find('.gform_card_icon').removeClass('gform_card_icon_selected').addClass('gform_card_icon_inactive');
        jQuery(cardContainer).find('.gform_card_icon_' + cardType).removeClass('gform_card_icon_inactive').addClass('gform_card_icon_selected');
    }
}

function gformFindCardType(value) {

    if(value.length < 4)
        return false;

    var rules = window['gf_cc_rules'];
    var validCardTypes = new Array();

    for(type in rules) {
        for(i in rules[type]) {

            if(rules[type][i].indexOf(value.substring(0, rules[type][i].length)) === 0) {
                validCardTypes[validCardTypes.length] = type;
                break;
            }

        }
    }

    return validCardTypes.length == 1 ? validCardTypes[0].toLowerCase() : false;
}