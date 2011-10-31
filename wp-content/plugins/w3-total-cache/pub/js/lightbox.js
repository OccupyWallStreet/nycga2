var W3tc_Lightbox = {
    window: jQuery(window),
    container: null,
    options: null,

    create: function() {
        var me = this;

        this.container = jQuery('<div class="lightbox lightbox-loading"><div class="lightbox-close">Close window</div><div class="lightbox-content"></div></div>').css({
            top: 0,
            left: 0,
            width: 0,
            height: 0,
            position: 'absolute',
            'z-index': 9991,
            display: 'none'
        });

        jQuery('#w3tc').append(this.container);

        this.window.resize(function() {
            me.resize();
        });

        this.window.scroll(function() {
            me.resize();
        });

        this.container.find('.lightbox-close').click(function() {
            me.close();
        });
    },

    open: function(options) {
        this.options = jQuery.extend({
            width: 0,
            height: 0,
            maxWidth: 0,
            maxHeight: 0,
            minWidth: 0,
            minHeight: 0,
            widthPercent: 0.6,
            heightPercent: 0.8,
            content: null,
            url: null,
            callback: null
        }, options);

        this.create();
        this.resize();

        if (this.options.content) {
            this.content(this.options.content);
        } else if (this.options.url) {
            this.load(this.options.url, this.options.callback);
        }

        W3tc_Overlay.show();
        this.container.show();
    },

    close: function() {
        this.container.remove();
        W3tc_Overlay.hide();
    },

    resize: function() {
        var width = (this.options.width ? this.options.width : this.window.width() * this.options.widthPercent);
        var height = (this.options.height ? this.options.height : this.window.height() * this.options.heightPercent);

        if (this.options.maxWidth && width > this.options.maxWidth) {
            width = this.options.maxWidth;
        } else if (width < this.options.minWidth) {
            width = this.options.minWidth;
        }

        if (this.options.maxHeight && height > this.options.maxHeight) {
            height = this.options.maxHeight;
        } else if (height < this.options.minHeight) {
            height = this.options.minHeight;
        }

        this.container.css({
            width: width,
            height: height
        });

        this.container.css({
            top: this.window.scrollTop() + this.window.height() / 2 - this.container.outerHeight() / 2,
            left: this.window.scrollLeft() + this.window.width() / 2 - this.container.outerWidth() / 2
        });

        jQuery('.lightbox-content', this.container).css({
            width: width,
            height: height
        });
    },

    load: function(url, callback) {
        this.content('');
        this.loading(true);
        var me = this;
        jQuery.get(url, {}, function(content) {
            me.loading(false);
            me.content(content);
            if (callback) {
                callback.call(this, me);
            }
        });
    },

    content: function(content) {
        return this.container.find('.lightbox-content').html(content);
    },

    width: function(width) {
        if (width === undefined) {
            return this.container.width();
        } else {
            this.container.css('width', width);
            return this.resize();
        }
    },

    height: function(height) {
        if (height === undefined) {
            return this.container.height();
        } else {
            this.container.css('height', height);
            return this.resize();
        }
    },

    loading: function(loading) {
        return (loading === undefined ? this.container.hasClass('lightbox-loader') : (loading ? this.container.addClass('lightbox-loader') : this.container.removeClass('lightbox-loader')));
    }
};

var W3tc_Overlay = {
    window: jQuery(window),
    container: null,

    create: function() {
        var me = this;

        this.container = jQuery('<div id="overlay" />').css({
            top: 0,
            left: 0,
            width: 0,
            height: 0,
            position: 'absolute',
            'z-index': 9990,
            display: 'none',
            opacity: 0.6
        });

        jQuery('#w3tc').append(this.container);

        this.window.resize(function() {
            me.resize();
        });

        this.window.scroll(function() {
            me.resize();
        });
    },

    show: function() {
        this.create();
        this.resize();
        this.container.show();
    },

    hide: function() {
        this.container.remove();
    },

    resize: function() {
        this.container.css({
            top: this.window.scrollTop(),
            left: this.window.scrollLeft(),
            width: this.window.width(),
            height: this.window.height()
        });
    }
};

function w3tc_lightbox_support_us(nonce) {
    W3tc_Lightbox.open({
        width: 500,
        height: 200,
        url: 'admin.php?page=w3tc_general&w3tc_support_us&_wpnonce=' + nonce
    });
}

var w3tc_minify_recommendations_checked = {};

function w3tc_lightbox_minify_recommendations(nonce) {
    W3tc_Lightbox.open({
        width: 1000,
        url: 'admin.php?page=w3tc_minify&w3tc_minify_recommendations&_wpnonce=' + nonce,
        callback: function(lightbox) {
            var theme = jQuery('#recom_theme').val();

            if (jQuery.ui && jQuery.ui.sortable) {
                jQuery("#recom_js_files,#recom_css_files").sortable({
                    axis: 'y',
                    stop: function() {
                        jQuery(this).find('li').each(function(index) {
                            jQuery(this).find('td:eq(1)').html((index + 1) + '.');
                        });
                    }
                });
            }

            if (w3tc_minify_recommendations_checked[theme] !== undefined) {
                jQuery('#recom_js_files :text,#recom_css_files :text').each(function() {
                    var hash = jQuery(this).parents('li').find('[name=recom_js_template]').val() + ':' + jQuery(this).val();

                    if (w3tc_minify_recommendations_checked[theme][hash] !== undefined) {
                        var checkbox = jQuery(this).parents('li').find(':checkbox');

                        if (w3tc_minify_recommendations_checked[theme][hash]) {
                            checkbox.attr('checked', 'checked');
                        } else {
                            checkbox.removeAttr('checked');
                        }
                    }
                });
            }

            jQuery('#recom_theme').change(function() {
                jQuery('#recom_js_files :checkbox,#recom_css_files :checkbox').each(function() {
                    var li = jQuery(this).parents('li');
                    var hash = li.find('[name=recom_js_template]').val() + ':' + li.find(':text').val();

                    if (w3tc_minify_recommendations_checked[theme] === undefined) {
                        w3tc_minify_recommendations_checked[theme] = {};
                    }

                    w3tc_minify_recommendations_checked[theme][hash] = jQuery(this).is(':checked');
                });

                lightbox.load('admin.php?page=w3tc_minify&w3tc_minify_recommendations&theme_key=' + jQuery(this).val() + '&_wpnonce=' + nonce, lightbox.options.callback);
            });

            jQuery('#recom_js_check').click(function() {
                if (jQuery('#recom_js_files :checkbox:checked').size()) {
                    jQuery('#recom_js_files :checkbox').removeAttr('checked');
                } else {
                    jQuery('#recom_js_files :checkbox').attr('checked', 'checked');
                }

                return false;
            });

            jQuery('#recom_css_check').click(function() {
                if (jQuery('#recom_css_files :checkbox:checked').size()) {
                    jQuery('#recom_css_files :checkbox').removeAttr('checked');
                } else {
                    jQuery('#recom_css_files :checkbox').attr('checked', 'checked');
                }

                return false;
            });

            jQuery('.recom_apply', lightbox.container).click(function() {
                var theme = jQuery('#recom_theme').val();

                jQuery('#js_files li').each(function() {
                    if (jQuery(this).find(':text').attr('name').indexOf('js_files[' + theme + ']') != -1) {
                        jQuery(this).remove();
                    }
                });

                jQuery('#css_files li').each(function() {
                    if (jQuery(this).find(':text').attr('name').indexOf('css_files[' + theme + ']') != -1) {
                        jQuery(this).remove();
                    }
                });

                jQuery('#recom_js_files li').each(function() {
                    if (jQuery(this).find(':checkbox:checked').size()) {
                        w3tc_minify_js_file_add(theme, jQuery(this).find('[name=recom_js_template]').val(), jQuery(this).find('[name=recom_js_location]').val(), jQuery(this).find('[name=recom_js_file]').val());
                    }
                });

                jQuery('#recom_css_files li').each(function() {
                    if (jQuery(this).find(':checkbox:checked').size()) {
                        w3tc_minify_css_file_add(theme, jQuery(this).find('[name=recom_css_template]').val(), jQuery(this).find('[name=recom_css_file]').val());
                    }
                });

                w3tc_minify_js_theme(theme);
                w3tc_minify_css_theme(theme);

                w3tc_input_enable('.js_enabled', jQuery('#js_enabled:checked').size());
                w3tc_input_enable('.css_enabled', jQuery('#css_enabled:checked').size());

                lightbox.close();
            });
        }
    });
}

function w3tc_lightbox_self_test(nonce) {
    W3tc_Lightbox.open({
        width: 800,
        minHeight: 300,
        url: 'admin.php?page=w3tc_general&w3tc_self_test&_wpnonce=' + nonce,
        callback: function(lightbox) {
            jQuery('.button-primary', lightbox.container).click(function() {
                lightbox.close();
            });
        }
    });
}

function w3tc_lightbox_cdn_s3_bucket_location(type, nonce) {
    W3tc_Lightbox.open({
        width: 500,
        height: 130,
        url: 'admin.php?page=w3tc_general&w3tc_cdn_s3_bucket_location&type=' + type + '&_wpnonce=' + nonce,
        callback: function(lightbox) {
            jQuery('.button', lightbox.container).click(function() {
                lightbox.close();
            });
        }
    });
}

jQuery(function() {
    jQuery('.button-minify-recommendations').click(function() {
        var nonce = jQuery(this).metadata().nonce;
        w3tc_lightbox_minify_recommendations(nonce);
        return false;
    });

    jQuery('.button-self-test').click(function() {
        var nonce = jQuery(this).metadata().nonce;
        w3tc_lightbox_self_test(nonce);
        return false;
    });

    jQuery('.button-cdn-s3-bucket-location,.button-cdn-cf-bucket-location').click(function() {
        var type = '';
        var nonce = jQuery(this).metadata().nonce;

        if (jQuery(this).hasClass('cdn_s3')) {
            type = 's3';
        } else if (jQuery(this).hasClass('cdn_cf')) {
            type = 'cf';
        }

        w3tc_lightbox_cdn_s3_bucket_location(type, nonce);
        return false;
    });
});
