// AJAX Functions

jQuery(document).ready( function() {
	var j = jQuery;

	/**** Page Load Actions **********************/

	/* Link filter and scope set. */
	bp_init_objects( [ 'links' ] );

	/* Clear cookies on logout */
	j('a.logout').click( function() {
		j.cookie('bp-links-scope', null );
		j.cookie('bp-links-filter', null );
		j.cookie('bp-links-extras', null );
	});

	/**** Directory ******************************/

	/* When the category filter select box is changed, re-query */
	j('select#links-category-filter').change( function() {
		var extras;
		var el_cat = j('select#links-category-filter');
		if ( el_cat.val().length ) {
			extras = 'category-' + el_cat.val();
		}

		bp_filter_request( 'links', j.cookie('bp-links-filter'), j.cookie('bp-links-scope'), 'div.links', j('#links_search').val(), 1, extras );

		return false;
	});

	/**** Lightbox ****************************/

	j("a.link-play").live('click',
		function() {

			var link = j(this).attr('id')
			link = link.split('-');

			j.post( ajaxurl, {
				action: 'link_lightbox',
				'cookie': encodeURIComponent(document.cookie),
				'link_id': link[2]
			},
			function(response)
			{
				var rs = bpl_split_response(response);

				if ( rs[0] >= 1 ) {
					j.fn.colorbox({
						html: rs[1],
						maxWidth: '90%',
						maxHeight: '90%'
					});
				}
			});

			return false;
		}
	);

	/**** Voting ******************************/

	j("div.link-vote-panel a.vote").live('click',
		function() {

			bpl_get_loader().toggle();

			var link = j(this).attr('id')
			link = link.split('-');

			j.post( ajaxurl, {
				action: 'link_vote',
				'cookie': encodeURIComponent(document.cookie),
				'_wpnonce': j("input#_wpnonce-link-vote").val(),
				'up_or_down': link[1],
				'link_id': link[2]
			},
			function(response)
			{
				var rs = bpl_split_response(response);

				j("div#link-vote-panel-" + link[2]).fadeOut(200,
					function() {
						bpl_remove_msg();

						if ( rs[0] <= -1 ) {
							bpl_list_item_msg(link[2], 'error', rs[1]);
						} else if ( rs[0] == 0 ) {
							bpl_list_item_msg(link[2], 'updated', rs[1]);
						} else {
							bpl_list_item_msg(link[2], 'updated', rs[1]);
							j("div.link-vote-panel div#vote-total-" + link[2]).html(rs[2]);
							j("div.link-vote-panel span#vote-count-" + link[2]).html(rs[3]);
						}

						j("div#link-vote-panel-" + link[2]).fadeIn(200);
					}
				);

				bpl_get_loader().toggle();
			});

			return false;
		}
	);

	/** Share Link Buttons **************************************/

	j("div.link-share-button a").live('click', function() {

		var tid = j(this).attr('id').split('-');
		var object = tid[1];
		var object_id = tid[2];
		
		var button = j(this).parent();
		var pid = button.attr('id').split('-');
		var link_id = pid[1];
		
		var loader = bpl_get_loader('link-share-loader-' + link_id);

		loader.toggle();

		var nonce = j(this).attr('href').split('?_wpnonce=');
		nonce = nonce[1].split('&');
		nonce = nonce[0];

		j.post( ajaxurl, {
			action: 'link_share',
			'cookie': encodeURIComponent(document.cookie),
			'link_id': link_id,
			'object': object,
			'object_id': object_id,
			'_wpnonce': nonce
		},
		function(response)
		{
			var rs = bpl_split_response(response);

			bpl_remove_msg();

			if ( rs[0] >= 1 ) {
				
				j('ul#link-list li div.link-share-panel').remove();
				j('ul#link-list li div.link-share-button').fadeIn(200);

				button.after(rs[1]);
				var panel = j('ul#link-list li div.link-share-panel');
				
				button.fadeOut(400, function () {
					panel.fadeIn(400, function() {

						// local vars
						var pnl_object, pnl_object_id, pnl_object_action;

						// re/setup local vars
						function setup( obj, obj_id ) {
							pnl_object = obj;
							pnl_object_id = obj_id;
							pnl_object_action = 'link_share_save_' + obj;
						}
						setup('profile', null);

						// handle share toggle radio
						j('input[name=link-share-where]').change( function() {
							// remove current panel
							j('select#link-share-' + pnl_object).val(-1);
							j('fieldset#link-share-' + pnl_object + '-set').fadeOut(400);
							// show new object
							setup( j(this).val(), null );
							j('fieldset#link-share-' + pnl_object + '-set').fadeIn(400);
						});
						
						// handle object select change
						j('select.link-share-object-select').change( function() {
							setup( j(this).attr('id').split('-')[2], j(this).val() );
						});

						// handle submit button
						j('input[name=link-share-save]').click( function() {
							j.post( ajaxurl, {
								action: pnl_object_action,
								'cookie': encodeURIComponent(document.cookie),
								'link_id': link_id,
								'object_id': pnl_object_id,
								'_wpnonce': j('input[name=link-share-nonce]').val()
							},
							function(response)
							{
								var rsx = bpl_split_response(response);

								bpl_remove_msg();

								if ( rsx[0] >= 1 ) {
									bpl_list_item_msg(link_id, 'updated', rsx[1]);
									button.children('a.link-share').addClass('link-share-active');
								} else {
									bpl_list_item_msg(link_id, 'error', rsx[1]);
								}

								panel.fadeOut(400, function() {
									panel.remove();
									button.fadeIn(400);
								});
							});
							return false;
						});

						// handle cancel button
						j('input[name=link-share-cancel]').click( function() {
							panel.fadeOut(400, function() {
								button.fadeIn(400);
								panel.remove();
							});
							return false;
						});

						// handle remove button
						j('input[name=link-share-remove]').click( function() {
							j.post( ajaxurl, {
								action: 'share_link_remove_' + object,
								'cookie': encodeURIComponent(document.cookie),
								'link_id': link_id,
								'object_id': object_id,
								'_wpnonce': j('input[name=link-share-nonce]').val()
							},
							function(response)
							{
								var rsx = bpl_split_response(response);

								bpl_remove_msg();

								if ( rsx[0] >= 1 ) {
									bpl_list_item_msg(link_id, 'updated', rsx[1]);
								} else {
									bpl_list_item_msg(link_id, 'error', rsx[1]);
								}

								panel.fadeOut(400, function() {
									button.fadeIn(400);
									panel.remove();
								});
							});
							return false;
						} );
					});
				});
			} else {
				bpl_list_item_msg(link_id, 'error', rs[1]);
			}

			loader.toggle();
		});
		return false;
	} );


	/*** Helpers **************************************************************/

	function bpl_get_loader(id)
	{
		var x_id = (id) ? '#' + id : null;
		return j('.ajax-loader' + x_id);
	}

	function bpl_split_response(str)
	{
		return str.split('[[split]]');
	}

	function bpl_remove_msg()
	{
		j('#message').remove();
	}
	
	function bpl_list_item_msg(lid, type, msg)
	{
		j('ul#link-list li#linklistitem-' + lid).prepend('<div id="message" class="' + type + ' fade"><p>' + msg + '</p></div>');
	}

});
