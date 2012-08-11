jQuery(document).ready(function(){
	jQuery('#bpe-categories-form #addcat').live( 'click', function() {

		var name = jQuery( "input#cat_name" ).val();
		var slug = jQuery( "input#cat_slug" ).val();
		var cat_id = jQuery( "input#cat_id" ).val();
		var nonce = jQuery( "input#_wpnonce_save_category" ).val();
		
		jQuery('.submit span.ajax-loader').css('display', 'inline-block');

		jQuery.post( ajaxurl, {
			'action': 'bpe_add_category',
			'cookie': encodeURIComponent(document.cookie),
			'name': name,
			'slug': slug,
			'cat_id': cat_id,
			'_wpnonce': nonce
		},
		function(response) {
			response = jQuery.parseJSON(response);
			
			jQuery('#ajax-response').empty().html(response.message);
			jQuery('input#cat_name,input#cat_slug,input#cat_id').val('');
			if(response.type == 'success' ) {
				if(response.action == 'edit') {
					var vars = response.category.split('§');
					jQuery('#editcat-'+ vars[0]).text(vars[2]);
					jQuery('#catslug-'+ vars[0]).text(vars[1]);
					jQuery('input#addcat').val('Add Category');
					jQuery('.clear-form').hide();
				} else {
					jQuery('#the-list').append(response.category);
				}
			}			

			jQuery('.submit span.ajax-loader').css('display', 'none');
		});
		
		return false;
	});

	jQuery('.bpe-delete-category').live( 'click', function() {
		var link = jQuery(this);
		var id = link.attr('id');
		var nonce = link.attr('href');

		id = id.split('-');
		id = id[1];

		var events = parseInt( jQuery('#num-1').text() ) + parseInt( jQuery('#num-'+ id ).text() );
		
		nonce = nonce.split('_wpnonce=');
		nonce = nonce[1].split('&');
		nonce = nonce[0];

		link.addClass('bpe-loading');

		jQuery.post( ajaxurl, {
			'action': 'bpe_delete_category',
			'cookie': encodeURIComponent(document.cookie),
			'id': id,
			'_wpnonce': nonce
		},
		function(response) {
			response = jQuery.parseJSON(response);
			
			jQuery('#ajax-response').empty().html(response.message);

			if(response.type == 'success') {
				jQuery('#cat-'+ response.id).parent().parent().remove();
				jQuery('#num-1').empty().text( String( events ) );
			}			

			link.removeClass('bpe-loading');
		});
		
		return false;
	});

	jQuery('.bpe-edit-category').live( 'click', function() {
		var link = jQuery(this);

		var id = link.attr('id');
		id = id.split('-');
		id = id[1];

		var name = link.text();
		var slug = jQuery('#catslug-'+ id).text();
		
		jQuery('input#cat_name').val(name);
		jQuery('input#cat_slug').val(slug);
		jQuery('input#cat_id').val(id);
		jQuery('input#addcat').val('Edit Category');
		jQuery('.clear-form').show();
		
		return false;
	});

	jQuery('.clear-form').live( 'click', function() {
		jQuery('input#cat_name,input#cat_slug,input#cat_id').val('');
		jQuery('input#addcat').val('Add Category');
		jQuery('.clear-form').hide();
		
		return false;
	});
});