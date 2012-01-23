(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('vipersvideoquicktags');

	tinymce.create('tinymce.plugins.VipersVideoQuicktags', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			if ( typeof VVQButtonClick == 'undefined' ) return;

			ed.addButton('vvqYouTube', {
				title : 'vipersvideoquicktags.youtube',
				image : url + '/../../buttons/youtube.png',
				onclick : function() {
					VVQButtonClick('youtube');
				}
			});
			ed.addButton('vvqGoogleVideo', {
				title : 'vipersvideoquicktags.googlevideo',
				image : url + '/../../buttons/googlevideo.png',
				onclick : function() {
					VVQButtonClick('googlevideo');
				}
			});
			ed.addButton('vvqDailyMotion', {
				title : 'vipersvideoquicktags.dailymotion',
				image : url + '/../../buttons/dailymotion.png',
				onclick : function() {
					VVQButtonClick('dailymotion');
				}
			});
			ed.addButton('vvqVimeo', {
				title : 'vipersvideoquicktags.vimeo',
				image : url + '/../../buttons/vimeo.png',
				onclick : function() {
					VVQButtonClick('vimeo');
				}
			});
			ed.addButton('vvqVeoh', {
				title : 'vipersvideoquicktags.veoh',
				image : url + '/../../buttons/veoh.png',
				onclick : function() {
					VVQButtonClick('veoh');
				}
			});
			ed.addButton('vvqViddler', {
				title : 'vipersvideoquicktags.viddler',
				image : url + '/../../buttons/viddler.png',
				onclick : function() {
					VVQButtonClick('viddler');
				}
			});
			ed.addButton('vvqMetacafe', {
				title : 'vipersvideoquicktags.metacafe',
				image : url + '/../../buttons/metacafe.png',
				onclick : function() {
					VVQButtonClick('metacafe');
				}
			});
			ed.addButton('vvqBlipTV', {
				title : 'vipersvideoquicktags.bliptv',
				image : url + '/../../buttons/bliptv.png',
				onclick : function() {
					VVQButtonClick('bliptv');
				}
			});
			ed.addButton('vvqFlickrVideo', {
				title : 'vipersvideoquicktags.flickrvideo',
				image : url + '/../../buttons/flickrvideo.png',
				onclick : function() {
					VVQButtonClick('flickrvideo');
				}
			});
			ed.addButton('vvqSpike', {
				title : 'vipersvideoquicktags.spike',
				image : url + '/../../buttons/spike.png',
				onclick : function() {
					VVQButtonClick('spike');
				}
			});
			ed.addButton('vvqMySpace', {
				title : 'vipersvideoquicktags.myspace',
				image : url + '/../../buttons/myspace.png',
				onclick : function() {
					VVQButtonClick('myspace');
				}
			});
			ed.addButton('vvqFLV', {
				title : 'vipersvideoquicktags.flv',
				image : url + '/../../buttons/flv.png',
				onclick : function() {
					VVQButtonClick('flv');
				}
			});
			ed.addButton('vvqQuicktime', {
				title : 'vipersvideoquicktags.quicktime',
				image : url + '/../../buttons/quicktime.png',
				onclick : function() {
					VVQButtonClick('quicktime');
				}
			});
			ed.addButton('vvqVideoFile', {
				title : 'vipersvideoquicktags.videofile',
				image : url + '/../../buttons/videofile.png',
				onclick : function() {
					VVQButtonClick('videofile');
				}
			});
		},

		/**
		 * Creates control instances based in the incomming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
		createControl : function(n, cm) {
			return null;
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : "Viper's Video Quicktags",
				author : 'Viper007Bond',
				authorurl : 'http://www.viper007bond.com/',
				infourl : 'http://www.viper007bond.com/wordpress-plugins/vipers-video-quicktags/',
				version : "6.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('vipersvideoquicktags', tinymce.plugins.VipersVideoQuicktags);
})();