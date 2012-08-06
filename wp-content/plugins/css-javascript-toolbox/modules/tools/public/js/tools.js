/**
* @version 
*/

/**
*
*/
var CJTTools = null;

/**
*
* 
* 
*/
jQuery(function($) {
	CJTTools = {
    
		/**
		*
		*
		*
		*/
		linkAnimationImage : ajaxurl.replace('wp-admin/admin-ajax.php', 'wp-content/plugins/css-javascript-toolbox/modules/tools/public/images/link-loading.gif'),
		
		/**
		*
		*
		*
		*/
		securityToken : $('#cjsecurity').val(),
		
		/**
		*
		*
		*
		*/
		url : ajaxurl,
		
		/**
		*
		*
		*
		*/
		backup : function() {
			// Prompt saving changes.
			if (contentHash.isChanged()) {
				if (!confirm(CJTToolsLocalization.backupConfirm)) {
					return;
				}
			}
			var backupInfo = CJTTools.getBackupInfo();
			if (backupInfo != null) { // Only when request success.
				if (backupInfo.has) {
					CJTTools.showBackupForm({task : 'backup'});
				}
				else {
					CJTTools.backupDatabase();
				}			
			}
		},

		/**
		*
		*
		*
		*/		
		backupDatabase : function() {
			var data = {
				security : CJTTools.securityToken,
				action : 'cjtoolbox_tools_backup',
				backupName : prompt('Please enter Backup Name')
			};
			if (data.backupName) {
				CJTTools.linkAnimate('cjt-tools-backup');
				$.get(CJTTools.url, data).complete(function() {
					CJTTools.linkStopAnimation('cjt-tools-backup');
				}).error(function() {
					alert(CJTToolsLocalization.couldNotBackup);
				});			
			}
		},
		
		/**
		* Callback when blocks data saved.
		* @see admin.js for more details.
		*/
		blocksDataSaved : function() {
			CJTTools.cancelRestore();
		},
		
		/**
		*
		*
		*
		*/		
		cancelRestore : function() {
			var newLocation = document.location.href.replace(/\?.+/, '?page=cjtoolbox');
			CJTTools.linkAnimate('cjt-tools-restore');
			document.location.href = newLocation;
		},
		
		/**
		*
		*
		*
		*/
		getBackupInfo : function() {
			var response = null;
			var request = {
				async : false,
				data : {
					security : CJTTools.securityToken,
					action : 'cjtoolbox_tools_getBackupInfo'
				}
			};
			$.ajax(CJTTools.url, request).error(function() {
				alert(CJTToolsLocalization.serverNotResponding);
			}).success(function(responseText) {
			 	// We don't need to check error codes for now.
				response = $.parseJSON(responseText).response;
			});
			return response;
		},
		
		/**
		*
		*
		*
		*/
		linkAnimate : function(id) {
			var link = $('a#' + id);
			var animateImage = new Image(); // Neew animation image.
			var imageContainer = document.createElement('span');
			var alreadyAnimated = ($('span#' + id + '-link-animation').get(0) != null);
			if (!alreadyAnimated) {
				// Set container properties.
				imageContainer.id = id + '-link-animation';
				imageContainer.className = 'animated-link';
				imageContainer.style.display = 'inline-block';
				imageContainer.style.width = link.get(0).offsetWidth + 'px';
				imageContainer.style.height = link.get(0).offsetHeight + 'px'; 
				imageContainer.style.textAlign = 'center'; 
				// Set image properties.
				animateImage.src = CJTTools.linkAnimationImage;
				// Append image to the container.
				imageContainer.appendChild(animateImage); // Add to the container.
				// Replace link.
				link.before(imageContainer);
				link.hide();
			}
		},

		/**
		*
		*
		*
		*/		
		linkStopAnimation : function(id) {
			var link = $('a#' + id);
			var animatedImage = $('span#' + id + '-link-animation');
			animatedImage.remove();
			link.show();
		},
		
		/**
		*
		*
		*
		*/
		restore : function() {
			var backupInfo = CJTTools.getBackupInfo();
			if (backupInfo != null) { // Only when request success.
				if (backupInfo.has) {
					CJTTools.showBackupForm({task : 'restore'});
				}
				else {
					alert(CJTToolsLocalization.noBackupAvailable);
				}
			}
		},

		/**
		*
		*
		*
		*/		
		restoreDatabase : function() {
			var newLocation = document.location.href.replace(/\?.+/, '?page=cjtoolbox&restore=true');
			CJTTools.linkAnimate('cjt-tools-restore');
			document.location.href = newLocation;
		},
		
		/**
		*
		*
		*
		*/
		showBackupForm : function(requestData) {
			requestData = (requestData == undefined) ? {} : requestData;
			requestData.security = CJTTools.securityToken;
			requestData.action = 'cjtoolbox_request_template';
			requestData.name = 'tools_backupform';
			requestData.width = 500;
			requestData.height = 600;
			var formURL = CJTTools.url + '?' + $.param(requestData);
			tb_show('Current Backup', formURL);
		}
		
	}; // End CJTTools.
	
	/// Misc ///
	
	// Backup form blocks list.
	$('#cjtoolbox_tools_backup_form ul.blocks-list li.item a.block-link').live('click', function(event) {
		var link = $(this);
		var detailsToToggle = link.next();
		var list = $('#cjtoolbox_tools_backup_form ul.blocks-list li.item ul.more-details');
		// Hide all other items except this.
		list.each(function() {
			if (this !== detailsToToggle.get(0)) {
				$(this).hide();
			}
		});
		// Toggle current item.
		detailsToToggle.toggle();
	});
	
	// Register callback when data saved.
	if (/restore\=true/.test(location.search)) {
		var saveDataCb = ['CJTTools', 'blocksDataSaved']
		cjt_data_saved_callbacks.push(saveDataCb);
		// When restoring from the database clear content hash.
	}
});