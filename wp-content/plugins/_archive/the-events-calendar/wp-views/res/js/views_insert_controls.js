
/**
 *
 * Get the filters that have url parameters set.
 * These are the ones that we can add controls for.
 *
 **/

function wpv_get_filters_with_url_params() {
    String.prototype.startsWith = function (str){
        return this.indexOf(str) == 0;
    };
    String.prototype.endsWith = function (str){
        return this.slice(-str.length) == str;
    };

    var custom_fields = Array();
	var taxonomy = Array();

    jQuery('select').each( function(index) {
		var name = jQuery(this).attr('name');
		if (name && name.startsWith('_wpv_settings[custom-field-') && name.endsWith('_compare]')) {
			var field_name = name.slice(27, -9);
			name = name.slice(0, -8);
			name = name.replace('[', '\\[');
			
			var field_value = jQuery('input[name="' + name + 'value\\]"]').val();

		    if (field_value.search(/URL_PARAM\(([\s\S]*)\)/) != -1) {
				
				// get all the URL_PARAM, there maybe more than one per field.
				var urls = Array();
				var regEx = /URL_PARAM\(([^(]*?)\)/g;
				var match = regEx.exec(field_value);
				while(match != null) {
				
					urls.push(match[1]);
					
					match = regEx.exec(field_value);
				}
				custom_fields[field_name] = urls;
			}
		}
		if (name && name.startsWith('_wpv_settings[tax_') && name.endsWith('_relationship]')) {
			var taxonomy_name = name.slice(18, -14);
			name = '_wpv_settings\\[taxonomy-' + taxonomy_name + '-attribute-url\\]'
			
			var val = jQuery('input[name="' + name + '"]').val();
			if (val != 'undefined') {
				taxonomy[taxonomy_name] = val;
			}
		}
		
    });
	
	return {'custom_fields' : custom_fields,
			'taxonomy' : taxonomy};
}


