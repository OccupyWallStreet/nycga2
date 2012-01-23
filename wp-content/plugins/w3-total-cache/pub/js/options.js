function w3tc_popup(url, name, width, height) {
    if (width === undefined) {
        width = 800;
    }
    if (height === undefined) {
        height = 600;
    }

    return window.open(url, name, 'width=' + width + ',height=' + height + ',status=no,toolbar=no,menubar=no,scrollbars=yes');
}

function w3tc_input_enable(input, enabled) {
    jQuery(input).each(function() {
        var me = jQuery(this);
        if (enabled) {
            me.removeAttr('disabled');
        } else {
            me.attr('disabled', 'disabled');
        }

        if (enabled) {
            me.next('[type=hidden]').remove();
        } else {
            var t = me.attr('type');
            if ((t != 'radio' && t != 'checkbox') || me.is(':checked')) {
                me.after(jQuery('<input />').attr({
                    type: 'hidden',
                    name: me.attr('name')
                }).val(me.val()));
            }
        }
    });
}

function w3tc_minify_js_file_clear() {
    if (!jQuery('#js_files :visible').size()) {
        jQuery('#js_files_empty').show();
    } else {
        jQuery('#js_files_empty').hide();
    }
}

function w3tc_minify_css_file_clear() {
    if (!jQuery('#css_files :visible').size()) {
        jQuery('#css_files_empty').show();
    } else {
        jQuery('#css_files_empty').hide();
    }
}

function w3tc_mobile_groups_clear() {
    if (!jQuery('#mobile_groups li').size()) {
        jQuery('#mobile_groups_empty').show();
    } else {
        jQuery('#mobile_groups_empty').hide();
    }
}

function w3tc_referrer_groups_clear() {
    if (!jQuery('#referrer_groups li').size()) {
        jQuery('#referrer_groups_empty').show();
    } else {
        jQuery('#referrer_groups_empty').hide();
    }
}

function w3tc_minify_js_file_add(theme, template, location, file) {
    var append = jQuery('<li><table><tr><th>&nbsp;</th><th>File URI:</th><th>Template:</th><th colspan="3">Embed Location:</th></tr><tr><td>' + (jQuery('#js_files li').size() + 1) + '.</td><td><input class="js_enabled" type="text" name="js_files[' + theme + '][' + template + '][' + location + '][]" value="" size="70" \/></td><td><select class="js_file_template js_enabled"></select></td><td><select class="js_file_location js_enabled"><optgroup label="Blocking:"><option value="include">Embed in &lt;head&gt;</option><option value="include-body">Embed after &lt;body&gt;</option><option value="include-footer">Embed before &lt;/body&gt;</option></optgroup><optgroup label="Non-Blocking:"><option value="include-nb">Embed in &lt;head&gt;</option><option value="include-body-nb">Embed after &lt;body&gt;</option><option value="include-footer-nb">Embed before &lt;/body&gt;</option></optgroup></select></td><td><input class="js_file_delete js_enabled button" type="button" value="Delete" /> <input class="js_file_verify js_enabled button" type="button" value="Verify URI" /></td></tr></table><\/li>');
    append.find('input:text').val(file);
    var select = append.find('.js_file_template');
    for (var i in minify_templates[theme]) {
        select.append(jQuery('<option />').val(i).html(minify_templates[theme][i]));
    }
    select.val(template);
    jQuery(append).find('.js_file_location').val(location);
    jQuery('#js_files').append(append).find('li:last input:first').focus();
    w3tc_minify_js_file_clear();
}

function w3tc_minify_css_file_add(theme, template, file) {
    var append = jQuery('<li><table><tr><th>&nbsp;</th><th>File URI:</th><th colspan="2">Template:</th></tr><tr><td>' + (jQuery('#css_files li').size() + 1) + '.</td><td><input class="css_enabled" type="text" name="css_files[' + theme + '][' + template + '][include][]" value="" size="70" \/></td><td><select class="css_file_template css_enabled"></select></td><td><input class="css_file_delete css_enabled button" type="button" value="Delete" /></td><td><input class="css_file_verify css_enabled button" type="button" value="Verify URI" /></td></tr></table><\/li>');
    append.find('input:text').val(file);
    var select = append.find('.css_file_template');
    for (var i in minify_templates[theme]) {
        select.append(jQuery('<option />').val(i).html(minify_templates[theme][i]));
    }
    select.val(template);
    jQuery('#css_files').append(append).find('li:last input:first').focus();
    w3tc_minify_css_file_clear();
}

function w3tc_minify_js_theme(theme) {
    jQuery('#js_themes').val(theme);
    jQuery('#js_files :text').each(function() {
        var input = jQuery(this);
        if (input.attr('name').indexOf('js_files[' + theme + ']') != 0) {
            input.parents('li').hide();
        } else {
            input.parents('li').show();
        }
    });
    w3tc_minify_js_file_clear();
}

function w3tc_minify_css_theme(theme) {
    jQuery('#css_themes').val(theme);
    jQuery('#css_files :text').each(function() {
        var input = jQuery(this);
        if (input.attr('name').indexOf('css_files[' + theme + ']') != 0) {
            input.parents('li').hide();
        } else {
            input.parents('li').show();
        }
    });
    w3tc_minify_css_file_clear();
}

function w3tc_cdn_get_cnames() {
    var cnames = [];

    jQuery('#cdn_cnames input[type=text]').each(function() {
        var cname = jQuery(this).val();

        if (cname) {
            var match = /^\*\.(.*)$/.exec(cname);

            if (match) {
                cnames = [];
                for (var i = 1; i <= 10; i++) {
                    cnames.push('cdn' + i + '.' + match[1]);
                }
                return false;
            }

            cnames.push(cname);
        }
    });

    return cnames;
}

function w3tc_cdn_cnames_assign() {
    var li = jQuery('#cdn_cnames li'), size = li.size();

    if (size > 1) {
        li.eq(0).find('.cdn_cname_delete').show();
    } else {
        li.eq(0).find('.cdn_cname_delete').hide();
    }

    jQuery(li).each(function(index) {
        var label = '';

        if (size > 1) {
            switch (index) {
                case 0:
                    label = '(reserved for CSS)';
                    break;

                case 1:
                    label = '(reserved for JS in <head>)';
                    break;

                case 2:
                    label = '(reserved for JS after <body>)';
                    break;

                case 3:
                    label = '(reserved for JS before </body>)';
                    break;
            }
        }

        jQuery(this).find('span').text(label);
    });
}

function w3tc_cloudflare_api_request(action, value, nonce) {
    var email = jQuery('#cloudflare_email');
    var key = jQuery('#cloudflare_key');
    var zone = jQuery('#cloudflare_zone');

    if (!email.val()) {
        alert('Please enter CloudFlare E-Mail.');
        email.focus();
        return false;
    }

    if (!key.val()) {
        alert('Please enter CloudFlare API key.');
        key.focus();
        return false;
    }

    if (!zone.val()) {
        alert('Please enter CloudFlare zone.');
        zone.focus();
        return false;
    }

    jQuery.post('admin.php?page=w3tc_general', {
        w3tc_cloudflare_api_request: 1,
        email: email.val(),
        key: key.val(),
        zone: zone.val(),
        action: action,
        value: value,
        _wpnonce: nonce
    }, function(data) {
        alert(data.result ? 'OK' : 'Request failed. Error: ' + data.error);
    }, 'json');

    return true;
}

function w3tc_toggle(name, check) {
    if (check === undefined) {
        check = true;
    }

    var id = '#' + name, cls = '.' + name;

    jQuery(cls).click(function() {
        var checked = check;

        jQuery(cls).each(function() {
            var _checked = jQuery(this).is(':checked');

            if ((check && !_checked) || (!check && _checked)) {
                checked = !check;

                return false;
            }
        });

        if (checked) {
            jQuery(id).attr('checked', 'checked');
        } else {
            jQuery(id).removeAttr('checked');
        }
    });

    jQuery(id).click(function() {
        var checked = jQuery(this).is(':checked');
        jQuery(cls).each(function() {
            if (checked) {
                jQuery(this).attr('checked', 'checked');
            } else {
                jQuery(this).removeAttr('checked');
            }
        });
    });
}

function w3tc_beforeupload_bind() {
    jQuery(window).bind('beforeunload', w3tc_beforeunload);
}

function w3tc_beforeupload_unbind() {
    jQuery(window).unbind('beforeunload', w3tc_beforeunload);
}

function w3tc_beforeunload() {
    return 'Are you sure you wish to navigate away from this page without saving your changes?';
}

jQuery(function() {
    // general page
    w3tc_toggle('enabled');

    jQuery('.button-tweet').live('click', function() {
        window.open('http://twitter.com/?status=' + encodeURIComponent('YES! I optimized my #wordpress site\'s #performance using the W3 Total Cache #plugin by @w3edge. Check it out! http://j.mp/A69xX'), '_blank');
    });

    jQuery('.button-rating').live('click', function() {
        window.open('http://wordpress.org/extend/plugins/w3-total-cache/', '_blank');
    });

    // browsercache page
    w3tc_toggle('browsercache_expires');
    w3tc_toggle('browsercache_cache_control');
    w3tc_toggle('browsercache_etag');
    w3tc_toggle('browsercache_w3tc');
    w3tc_toggle('browsercache_compression');
    w3tc_toggle('browsercache_replace');

    // minify page
    w3tc_input_enable('.html_enabled', jQuery('#html_enabled:checked').size());
    w3tc_input_enable('.js_enabled', jQuery('#js_enabled:checked').size());
    w3tc_input_enable('.css_enabled', jQuery('#css_enabled:checked').size());

    w3tc_minify_js_theme(jQuery('#js_themes').val());
    w3tc_minify_css_theme(jQuery('#css_themes').val());

    jQuery('#html_enabled').click(function() {
        w3tc_input_enable('.html_enabled', this.checked);
    });

    jQuery('#js_enabled').click(function() {
        w3tc_input_enable('.js_enabled', jQuery(this).is(':checked'));
    });

    jQuery('#css_enabled').click(function() {
        w3tc_input_enable('.css_enabled', jQuery(this).is(':checked'));
    });

    jQuery('.js_file_verify,.css_file_verify').live('click', function() {
        var file = jQuery(this).parents('li').find(':text').val();
        if (file == '') {
            alert('Empty URI');
        } else {
            var url = '';
            if (/^https?:\/\//.test(file)) {
                url = file;
            } else {
                url = '/' + file;
            }
            w3tc_popup(url, 'file_verify');
        }
    });

    jQuery('.js_file_template').live('change', function() {
        jQuery(this).parents('li').find(':text').attr('name', 'js_files[' + jQuery('#js_themes').val() + '][' + jQuery(this).val() + '][' + jQuery(this).parents('li').find('.js_file_location').val() + '][]');
    });

    jQuery('.css_file_template').live('change', function() {
        jQuery(this).parents('li').find(':text').attr('name', 'css_files[' + jQuery('#css_themes').val() + '][' + jQuery(this).val() + '][include][]');
    });

    jQuery('.js_file_location').live('change', function() {
        jQuery(this).parents('li').find(':text').attr('name', 'js_files[' + jQuery('#js_themes').val() + '][' + jQuery(this).parents('li').find('.js_file_template').val() + '][' + jQuery(this).val() + '][]');
    });

    jQuery('.js_file_delete').live('click', function() {
        var parent = jQuery(this).parents('li');
        if (parent.find('input[type=text]').val() == '' || confirm('Are you sure you want to remove this JS file?')) {
            parent.remove();
            w3tc_minify_js_file_clear();
            w3tc_beforeupload_bind();
        }

        return false;
    });

    jQuery('.css_file_delete').live('click', function() {
        var parent = jQuery(this).parents('li');
        if (parent.find('input[type=text]').val() == '' || confirm('Are you sure you want to remove this CSS file?')) {
            parent.remove();
            w3tc_minify_css_file_clear();
            w3tc_beforeupload_bind();
        }

        return false;
    });

    jQuery('#js_file_add').click(function() {
        w3tc_minify_js_file_add(jQuery('#js_themes').val(), 'default', 'include', '');
    });

    jQuery('#css_file_add').click(function() {
        w3tc_minify_css_file_add(jQuery('#css_themes').val(), 'default', '');
    });

    jQuery('#js_themes').change(function() {
        w3tc_minify_js_theme(jQuery(this).val());
    });

    jQuery('#css_themes').change(function() {
        w3tc_minify_css_theme(jQuery(this).val());
    });

    jQuery('#minify_form').submit(function() {
        var js = [], css = [], invalid_js = [], invalid_css = [], duplicate = false, query_js = [], query_css = [];

        jQuery('#js_files :text').each(function() {
            var v = jQuery(this).val(), n = jQuery(this).attr('name'), c = v + n, g = '';
            var match = /js_files\[([a-z0-9_\/]+)\]/.exec(n);
            if (match) {
                g = '[' + jQuery('#js_themes option[value=' + match[1] + ']').text() + '] ' + v;
            }
            if (v != '') {
                for (var i = 0; i < js.length; i++) {
                    if (js[i] == c) {
                        duplicate = true;
                        break;
                    }
                }

                js.push(c);

                var qindex = v.indexOf('?');
                if (qindex != -1) {
                    if (!/^(https?:)?\/\//.test(v)) {
                        query_js.push(g);
                    }
                    v = v.substr(0, qindex);
                } else if (!/\.js$/.test(v)) {
                    invalid_js.push(g);
                }
            }
        });

        jQuery('#css_files :text').each(function() {
            var v = jQuery(this).val(), n = jQuery(this).attr('name'), c = v + n, g = '';
            var match = /css_files\[([a-z0-9_\/]+)\]/.exec(n);
            if (match) {
                g = '[' + jQuery('#css_themes option[value=' + match[1] + ']').text() + '] ' + v;
            }
            if (v != '') {
                for (var i = 0; i < css.length; i++) {
                    if (css[i] == c) {
                        duplicate = true;
                        break;
                    }
                }

                css.push(c);

                var qindex = v.indexOf('?');
                if (qindex != -1) {
                    if (!/^(https?:)?\/\//.test(v)) {
                        query_css.push(g);
                    }
                    v = v.substr(0, qindex);
                } else if (!/\.css$/.test(v)) {
                    invalid_css.push(g);
                }
            }
        });

        if (jQuery('#js_enabled:checked').size()) {
            if (invalid_js.length && !confirm('The following files have invalid JS file extension:\r\n\r\n' + invalid_js.join('\r\n') + '\r\n\r\nAre you confident these files contain valid JS code?')) {
                return false;
            }

            if (query_js.length) {
                alert('We recommend using the entire URI for files with query string (GET) variables. You entered:\r\n\r\n' + query_js.join('\r\n'));
                return false;
            }
        }

        if (jQuery('#css_enabled:checked').size()) {
            if (invalid_css.length && !confirm('The following files have invalid CSS file extension:\r\n\r\n' + invalid_css.join('\r\n') + '\r\n\r\nAre you confident these files contain valid CSS code?')) {
                return false;
            }

            if (query_css.length) {
                alert('We recommend using the entire URI for files with query string (GET) variables. You entered:\r\n\r\n' + query_css.join('\r\n'));
                return false;
            }
        }

        if (duplicate) {
            alert('Duplicate files have been found in your minify settings, please check your settings and re-save.');
            return false;
        }

        return true;
    });

    // CDN
    jQuery('.w3tc-tab').click(function() {
        jQuery('.w3tc-tab-content').hide();
        jQuery(this.rel).show();
    });

    jQuery('#cdn_export_library').click(function() {
        w3tc_popup('admin.php?page=w3tc_cdn&w3tc_cdn_export_library&_wpnonce=' + jQuery(this).metadata().nonce, 'cdn_export_library');
    });

    jQuery('#cdn_import_library').click(function() {
        w3tc_popup('admin.php?page=w3tc_cdn&w3tc_cdn_import_library&_wpnonce=' + jQuery(this).metadata().nonce, 'cdn_import_library');
    });

    jQuery('#cdn_queue').click(function() {
        w3tc_popup('admin.php?page=w3tc_cdn&w3tc_cdn_queue&_wpnonce=' + jQuery(this).metadata().nonce, 'cdn_queue');
    });

    jQuery('#cdn_rename_domain').click(function() {
        w3tc_popup('admin.php?page=w3tc_cdn&w3tc_cdn_rename_domain&_wpnonce=' + jQuery(this).metadata().nonce, 'cdn_rename_domain');
    });

    jQuery('#cdn_purge').click(function() {
        w3tc_popup('admin.php?page=w3tc_cdn&w3tc_cdn_purge&_wpnonce=' + jQuery(this).metadata().nonce, 'cdn_purge');
    });

    jQuery('.cdn_export').click(function() {
        var metadata = jQuery(this).metadata();
        w3tc_popup('admin.php?page=w3tc_cdn&w3tc_cdn_export&cdn_export_type=' + metadata.type + '&_wpnonce=' + metadata.nonce, 'cdn_export_' + metadata.type);
    });

    jQuery('#cdn_test').click(function() {
        var me = jQuery(this);
        var metadata = me.metadata();
        var cnames = w3tc_cdn_get_cnames();
        var params = {
            w3tc_cdn_test: 1,
            _wpnonce: metadata.nonce
        };

        switch (metadata.type) {
            case 'ftp':
                jQuery.extend(params, {
                    engine: 'ftp',
                    'config[host]': jQuery('#cdn_ftp_host').val(),
                    'config[user]': jQuery('#cdn_ftp_user').val(),
                    'config[path]': jQuery('#cdn_ftp_path').val(),
                    'config[pass]': jQuery('#cdn_ftp_pass').val(),
                    'config[pasv]': jQuery('#cdn_ftp_pasv:checked').size()
                });

                if (cnames.length) {
                    params['config[domain][]'] = cnames;
                }
                break;

            case 's3':
                jQuery.extend(params, {
                    engine: 's3',
                    'config[key]': jQuery('#cdn_s3_key').val(),
                    'config[secret]': jQuery('#cdn_s3_secret').val(),
                    'config[bucket]': jQuery('#cdn_s3_bucket').val()
                });

                if (cnames.length) {
                    params['config[cname][]'] = cnames;
                }
                break;

            case 'cf':
                jQuery.extend(params, {
                    engine: 'cf',
                    'config[key]': jQuery('#cdn_cf_key').val(),
                    'config[secret]': jQuery('#cdn_cf_secret').val(),
                    'config[bucket]': jQuery('#cdn_cf_bucket').val(),
                    'config[id]': jQuery('#cdn_cf_id').val()
                });

                if (cnames.length) {
                    params['config[cname][]'] = cnames;
                }
                break;

            case 'cf2':
                jQuery.extend(params, {
                    engine: 'cf2',
                    'config[key]': jQuery('#cdn_cf2_key').val(),
                    'config[secret]': jQuery('#cdn_cf2_secret').val(),
                    'config[origin]': jQuery('#cdn_cf2_origin').val(),
                    'config[id]': jQuery('#cdn_cf2_id').val()
                });

                if (cnames.length) {
                    params['config[cname][]'] = cnames;
                }
                break;

            case 'rscf':
                jQuery.extend(params, {
                    engine: 'rscf',
                    'config[user]': jQuery('#cdn_rscf_user').val(),
                    'config[key]': jQuery('#cdn_rscf_key').val(),
                    'config[location]': jQuery('#cdn_rscf_location').val(),
                    'config[container]': jQuery('#cdn_rscf_container').val(),
                    'config[id]': jQuery('#cdn_rscf_id').val()
                });

                if (cnames.length) {
                    params['config[cname][]'] = cnames;
                }
                break;

            case 'azure':
                jQuery.extend(params, {
                    engine: 'azure',
                    'config[user]': jQuery('#cdn_azure_user').val(),
                    'config[key]': jQuery('#cdn_azure_key').val(),
                    'config[container]': jQuery('#cdn_azure_container').val()
                });

                if (cnames.length) {
                    params['config[cname][]'] = cnames;
                }
                break;

            case 'mirror':
                jQuery.extend(params, {
                    engine: 'mirror'
                });

                if (cnames.length) {
                    params['config[domain][]'] = cnames;
                }
                break;

            case 'netdna':
                jQuery.extend(params, {
                    engine: 'netdna',
                    'config[apiid]': jQuery('#cdn_netdna_apiid').val(),
                    'config[apikey]': jQuery('#cdn_netdna_apikey').val()
                });

                if (cnames.length) {
                    params['config[domain][]'] = cnames;
                }
                break;

            case 'cotendo':
                var zones = [], zones_val = jQuery('#cdn_cotendo_zones').val();

                if (zones_val) {
                    zones = zones_val.split(/[\r\n,;]+/);
                }

                jQuery.extend(params, {
                    engine: 'cotendo',
                    'config[username]': jQuery('#cdn_cotendo_username').val(),
                    'config[password]': jQuery('#cdn_cotendo_password').val()
                });

                if (zones.length) {
                    params['config[zones][]'] = zones;
                }

                if (cnames.length) {
                    params['config[domain][]'] = cnames;
                }
                break;

            case 'edgecast':
                jQuery.extend(params, {
                    engine: 'edgecast',
                    'config[account]': jQuery('#cdn_edgecast_account').val(),
                    'config[token]': jQuery('#cdn_edgecast_token').val()
                });

                if (cnames.length) {
                    params['config[domain][]'] = cnames;
                }
                break;
        }

        var status = jQuery('#cdn_test_status');
        status.removeClass('w3tc-error');
        status.removeClass('w3tc-success');
        status.addClass('w3tc-process');
        status.html('Testing...');

        jQuery.post('admin.php?page=w3tc_general', params, function(data) {
            status.addClass(data.result ? 'w3tc-success' : 'w3tc-error');
            status.html(data.error);
        }, 'json');
    });

    jQuery('#cdn_create_container').live('click', function() {
        var me = jQuery(this);
        var metadata = me.metadata();
        var cnames = w3tc_cdn_get_cnames();
        var container_id = null;
        var params = {
            w3tc_cdn_create_container: 1,
            _wpnonce: metadata.nonce
        };

        switch (metadata.type) {
            case 's3':
                jQuery.extend(params, {
                    engine: 's3',
                    'config[key]': jQuery('#cdn_s3_key').val(),
                    'config[secret]': jQuery('#cdn_s3_secret').val(),
                    'config[bucket]': jQuery('#cdn_s3_bucket').val(),
                    'config[bucket_location]': jQuery('#cdn_s3_bucket_location').val()
                });

                if (cnames.length) {
                    params['config[cname][]'] = cnames;
                }
                break;

            case 'cf':
                container_id = jQuery('#cdn_cf_id');

                jQuery.extend(params, {
                    engine: 'cf',
                    'config[key]': jQuery('#cdn_cf_key').val(),
                    'config[secret]': jQuery('#cdn_cf_secret').val(),
                    'config[bucket]': jQuery('#cdn_cf_bucket').val(),
                    'config[bucket_location]': jQuery('#cdn_cf_bucket_location').val()
                });

                if (cnames.length) {
                    params['config[cname][]'] = cnames;
                }
                break;

            case 'cf2':
                container_id = jQuery('#cdn_cf2_id');

                jQuery.extend(params, {
                    engine: 'cf2',
                    'config[key]': jQuery('#cdn_cf2_key').val(),
                    'config[secret]': jQuery('#cdn_cf2_secret').val(),
                    'config[origin]': jQuery('#cdn_cf2_origin').val(),
                    'config[bucket_location]': jQuery('#cdn_cf2_bucket_location').val()
                });

                if (cnames.length) {
                    params['config[cname][]'] = cnames;
                }
                break;

            case 'rscf':
                container_id = jQuery('#cdn_cnames input[type=text]:first');

                jQuery.extend(params, {
                    engine: 'rscf',
                    'config[user]': jQuery('#cdn_rscf_user').val(),
                    'config[key]': jQuery('#cdn_rscf_key').val(),
                    'config[location]': jQuery('#cdn_rscf_location').val(),
                    'config[container]': jQuery('#cdn_rscf_container').val()
                });

                if (cnames.length) {
                    params['config[cname][]'] = cnames;
                }
                break;

            case 'azure':
                jQuery.extend(params, {
                    engine: 'azure',
                    'config[user]': jQuery('#cdn_azure_user').val(),
                    'config[key]': jQuery('#cdn_azure_key').val(),
                    'config[container]': jQuery('#cdn_azure_container').val()
                });

                if (cnames.length) {
                    params['config[cname][]'] = cnames;
                }
                break;
        }

        var status = jQuery('#cdn_create_container_status');
        status.removeClass('w3tc-error');
        status.removeClass('w3tc-success');
        status.addClass('w3tc-process');
        status.html('Creating...');

        jQuery.post('admin.php?page=w3tc_general', params, function(data) {
            status.addClass(data.result ? 'w3tc-success' : 'w3tc-error');
            status.html(data.error);

            if (container_id && container_id.size() && data.container_id) {
                container_id.val(data.container_id);
            }
        }, 'json');
    });

    jQuery('#cloudflare_purge_cache').click(function() {
        if (confirm('Purging your site\'s cache will remove all cache files. It may take up to 48 hours for the cache to completely rebuild on CloudFlare\'s global network. Are you sure you want to purge the cache?')) {
            var nonce = jQuery(this).metadata().nonce;
            w3tc_cloudflare_api_request('fpurge_ts', 1, nonce);
        }
    });

    jQuery('#memcached_test').click(function() {
        var status = jQuery('#memcached_test_status');
        status.removeClass('w3tc-error');
        status.removeClass('w3tc-success');
        status.addClass('w3tc-process');
        status.html('Testing...');
        jQuery.post('admin.php?page=w3tc_general', {
            w3tc_test_memcached: 1,
            servers: jQuery('#memcached_servers').val(),
            _wpnonce: jQuery(this).metadata().nonce
        }, function(data) {
            status.addClass(data.result ? 'w3tc-success' : 'w3tc-error');
            status.html(data.error);
        }, 'json');
    });

    jQuery('.minifier_test').click(function() {
        var me = jQuery(this);
        var metadata = me.metadata();
        var params = {
            w3tc_test_minifier: 1,
            _wpnonce: metadata.nonce
        };

        switch (metadata.type) {
            case 'yuijs':
                jQuery.extend(params, {
                    engine: 'yuijs',
                    path_java: jQuery('#minify_yuijs_path_java').val(),
                    path_jar: jQuery('#minify_yuijs_path_jar').val()
                });
                break;

            case 'yuicss':
                jQuery.extend(params, {
                    engine: 'yuicss',
                    path_java: jQuery('#minify_yuicss_path_java').val(),
                    path_jar: jQuery('#minify_yuicss_path_jar').val()
                });
                break;

            case 'ccjs':
                jQuery.extend(params, {
                    engine: 'ccjs',
                    path_java: jQuery('#minify_ccjs_path_java').val(),
                    path_jar: jQuery('#minify_ccjs_path_jar').val()
                });
                break;
        }

        var status = me.next();
        status.removeClass('w3tc-error');
        status.removeClass('w3tc-success');
        status.addClass('w3tc-process');
        status.html('Testing...');

        jQuery.post('admin.php?page=w3tc_general', params, function(data) {
            status.addClass(data.result ? 'w3tc-success' : 'w3tc-error');
            status.html(data.error);
        }, 'json');
    });

    // CDN cnames
    jQuery('#cdn_cname_add').click(function() {
        jQuery('#cdn_cnames').append('<li><input type="text" name="cdn_cnames[]" value="" size="60" /> <input class="button cdn_cname_delete" type="button" value="Delete" /> <span></span></li>');
        w3tc_cdn_cnames_assign();
    });

    jQuery('.cdn_cname_delete').live('click', function() {
        var p = jQuery(this).parent();
        if (p.find('input[type=text]').val() == '' || confirm('Are you sure you want to remove this CNAME?')) {
            p.remove();
            w3tc_cdn_cnames_assign();
            w3tc_beforeupload_bind();
        }
    });

    jQuery('#cdn_form').submit(function() {
        var cnames = [], ret = true;

        jQuery('#cdn_cnames input[type=text]').each(function() {
            var cname = jQuery(this).val();

            if (cname) {
                if (jQuery.inArray(cname, cnames) != -1) {
                    alert('CNAME "' + cname + '" already exists.');
                    ret = false;

                    return false;
                } else {
                    cnames.push(cname);
                }
            }
        });

        return ret;
    });

    // support tabs
    jQuery('#support_more_files').live('click', function() {
        jQuery(this).before('<input type="file" name="files[]" /><br />');

        return false;
    });

    jQuery('#support_form').live('submit', function() {
        var url = jQuery('.required #support_url');
        var name = jQuery('.required #support_name');
        var email = jQuery('.required #support_email');
        var phone = jQuery('.required #support_phone');
        var subject = jQuery('.required #support_subject');
        var description = jQuery('.required #support_description');
        var wp_login = jQuery('.required #support_wp_login');
        var wp_password = jQuery('.required #support_wp_password');
        var ftp_host = jQuery('.required #support_ftp_host');
        var ftp_login = jQuery('.required #support_ftp_login');
        var ftp_password = jQuery('.required #support_ftp_password');

        if (url.size() && url.val() == '') {
            alert('Please enter the address of your site in the Site URL field.');
            url.focus();
            return false;
        }

        if (name.size() && name.val() == '') {
            alert('Please enter your name in the Name field.');
            name.focus();
            return false;
        }

        if (email.size() && !/^[a-z0-9_\-\.]+@[a-z0-9-\.]+\.[a-z]{2,5}$/.test(email.val().toLowerCase())) {
            alert('Please enter valid email address in the E-Mail field.');
            email.focus();
            return false;
        }

        if (phone.size() && !/^[0-9\-\. \(\)\+]+$/.test(phone.val())) {
            alert('Please enter your phone in the phone field.');
            phone.focus();
            return false;
        }

        if (subject.size() && subject.val() == '') {
            alert('Please enter subject in the subject field.');
            subject.focus();
            return false;
        }

        if (description.size() && description.val() == '') {
            alert('Please describe the issue in the issue description field.');
            description.focus();
            return false;
        }

        if (wp_login.size() && wp_login.val() == '') {
            alert('Please enter an administrator login. Remember you can create a temporary one just for this support case.');
            wp_login.focus();
            return false;
        }

        if (wp_password.size() && wp_password.val() == '') {
            alert('Please enter WP Admin password, be sure it\'s spelled correctly.');
            wp_password.focus();
            return false;
        }

        if (ftp_host.size() && ftp_host.val() == '') {
            alert('Please enter SSH or FTP host for your site.');
            ftp_host.focus();
            return false;
        }

        if (ftp_login.size() && ftp_login.val() == '') {
            alert('Please enter SSH or FTP login for your server. Remember you can create a temporary one just for this support case.');
            ftp_login.focus();
            return false;
        }

        if (ftp_password.size() && ftp_password.val() == '') {
            alert('Please enter SSH or FTP password for your FTP account.');
            ftp_password.focus();
            return false;
        }

        return true;
    });

    jQuery('#support_request_type').live('change', function() {
        var request_type = jQuery(this);

        if (request_type.val() == '') {
            alert('Please select request type.');
            request_type.focus();

            return false;
        }

        var type = request_type.val(), action = '';

        switch (type) {
            case 'bug_report':
            case 'new_feature':
                action = 'support_form';
                break;

            case 'email_support':
            case 'phone_support':
            case 'plugin_config':
            case 'theme_config':
            case 'linux_config':
                action = 'support_payment';
                break;
        }

        if (action) {
            jQuery('#support_container').html('<div id="support_loading">Loading...</div>').load('admin.php?page=w3tc_support&w3tc_' + action + '&request_type=' + type + '&_wpnonce=' + request_type.metadata().nonce);

            return false;
        }

        return true;
    });

    jQuery('#support_cancel').live('click', function() {
        jQuery('#support_container').html('<div id="support_loading">Loading...</div>').load('admin.php?page=w3tc_support&w3tc_support_select&_wpnonce=' + jQuery(this).metadata().nonce);
    });

    // mobile tab
    jQuery('#mobile_form').submit(function() {
        var error = false;

        jQuery('#mobile_groups li').each(function() {
            if (jQuery(this).find(':checked').size()) {
                var group = jQuery(this).find('.mobile_group').text();
                var theme = jQuery(this).find(':selected').val();
                var redirect = jQuery(this).find('input[type=text]').val();
                var agents = jQuery.trim(jQuery(this).find('textarea').val()).split("\n");

                jQuery('#mobile_groups li').each(function() {
                    if (jQuery(this).find(':checked').size()) {
                        var compare_group = jQuery(this).find('.mobile_group').text();
                        if (compare_group != group) {
                            var compare_theme = jQuery(this).find(':selected').val();
                            var compare_redirect = jQuery(this).find('input[type=text]').val();
                            var compare_agents = jQuery.trim(jQuery(this).find('textarea').val()).split("\n");

                            if (compare_redirect == '' && redirect == '' && compare_theme != '' && compare_theme == theme) {
                                alert('Duplicate theme "' + compare_theme + '" found in the group "' + group + '".');
                                error = true;
                                return false;
                            }

                            if (compare_redirect != '' && compare_redirect == redirect) {
                                alert('Duplicate redirect "' + compare_redirect + '" found in the group "' + group + '".');
                                error = true;
                                return false;
                            }

                            jQuery.each(compare_agents, function(index, value) {
                                if (jQuery.inArray(value, agents) != -1) {
                                    alert('Duplicate stem "' + value + '" found in the group "' + compare_group + '".');
                                    error = true;
                                    return false;
                                }
                            });
                        }
                    }
                });

                if (error) {
                    return false;
                }
            }
        });

        if (error) {
            return false;
        }
    });

    jQuery('#mobile_add').click(function() {
        var group = prompt('Enter group name (only "0-9", "a-z", "_" symbols are allowed).');

        if (group !== null) {
            group = group.toLowerCase();
            group = group.replace(/[^0-9a-z_]+/g, '_');
            group = group.replace(/^_+/, '');
            group = group.replace(/_+$/, '');

            if (group) {
                var exists = false;

                jQuery('.mobile_group').each(function() {
                    if (jQuery(this).html() == group) {
                        alert('Group already exists!');
                        exists = true;
                        return false;
                    }
                });

                if (!exists) {
                    var li = jQuery('<li id="mobile_group_' + group + '"><table class="form-table"><tr><th>Group name:</th><td><span class="mobile_group_number">' + (jQuery('#mobile_groups li').size() + 1) + '.</span> <span class="mobile_group">' + group + '</span> <input type="button" class="button mobile_delete" value="Delete group" /></td></tr><tr><th><label for="mobile_groups_' + group + '_enabled">Enabled:</label></th><td><input type="hidden" name="mobile_groups[' + group + '][enabled]" value="0" /><input id="mobile_groups_' + group + '_enabled" type="checkbox" name="mobile_groups[' + group + '][enabled]" value="1" checked="checked" /></td></tr><tr><th><label for="mobile_groups_' + group + '_theme">Theme:</label></th><td><select id="mobile_groups_' + group + '_theme" name="mobile_groups[' + group + '][theme]"><option value="">-- Pass-through --</option></select><br /><span class="description">Assign this group of user agents to a specific them. Leaving this option "Active Theme" allows any plugins you have (e.g. mobile plugins) to properly handle requests for these user agents. If the "redirect users to" field is not empty, this setting is ignored.</span></td></tr><tr><th><label for="mobile_groups_' + group + '_redirect">Redirect users to:</label></th><td><input id="mobile_groups_' + group + '_redirect" type="text" name="mobile_groups[' + group + '][redirect]" value="" size="60" /><br /><span class="description">A 302 redirect is used to send this group of users to another hostname (domain); recommended if a 3rd party service provides a mobile version of your site.</span></td></tr><tr><th><label for="mobile_groups_' + group + '_agents">User agents:</label></th><td><textarea id="mobile_groups_' + group + '_agents" name="mobile_groups[' + group + '][agents]" rows="10" cols="50"></textarea><br /><span class="description">Specify the user agents for this group.</span></td></tr></table></li>');
                    var select = li.find('select');

                    jQuery.each(mobile_themes, function(index, value) {
                        select.append(jQuery('<option />').val(index).html(value));
                    });

                    jQuery('#mobile_groups').append(li);
                    w3tc_mobile_groups_clear();
                    window.location.hash = '#mobile_group_' + group;
                    li.find('textarea').focus();
                }
            } else {
                alert('Empty group name!');
            }
        }
    });

    jQuery('.mobile_delete').live('click', function() {
        if (confirm('Are you sure want to delete this group?')) {
            jQuery(this).parents('#mobile_groups li').remove();
            w3tc_mobile_groups_clear();
            w3tc_beforeupload_bind();
        }
    });

    w3tc_mobile_groups_clear();

    // referrer tab
    jQuery('#referrer_form').submit(function() {
        var error = false;

        jQuery('#referrer_groups li').each(function() {
            if (jQuery(this).find(':checked').size()) {
                var group = jQuery(this).find('.referrer_group').text();
                var theme = jQuery(this).find(':selected').val();
                var redirect = jQuery(this).find('input[type=text]').val();
                var agents = jQuery.trim(jQuery(this).find('textarea').val()).split("\n");

                jQuery('#referrer_groups li').each(function() {
                    if (jQuery(this).find(':checked').size()) {
                        var compare_group = jQuery(this).find('.referrer_group').text();
                        if (compare_group != group) {
                            var compare_theme = jQuery(this).find(':selected').val();
                            var compare_redirect = jQuery(this).find('input[type=text]').val();
                            var compare_agents = jQuery.trim(jQuery(this).find('textarea').val()).split("\n");

                            if (compare_redirect == '' && redirect == '' && compare_theme != '' && compare_theme == theme) {
                                alert('Duplicate theme "' + compare_theme + '" found in the group "' + group + '".');
                                error = true;
                                return false;
                            }

                            if (compare_redirect != '' && compare_redirect == redirect) {
                                alert('Duplicate redirect "' + compare_redirect + '" found in the group "' + group + '".');
                                error = true;
                                return false;
                            }

                            jQuery.each(compare_agents, function(index, value) {
                                if (jQuery.inArray(value, agents) != -1) {
                                    alert('Duplicate stem "' + value + '" found in the group "' + compare_group + '".');
                                    error = true;
                                    return false;
                                }
                            });
                        }
                    }
                });

                if (error) {
                    return false;
                }
            }
        });

        if (error) {
            return false;
        }
    });

    jQuery('#referrer_add').click(function() {
        var group = prompt('Enter group name (only "0-9", "a-z", "_" symbols are allowed).');

        if (group !== null) {
            group = group.toLowerCase();
            group = group.replace(/[^0-9a-z_]+/g, '_');
            group = group.replace(/^_+/, '');
            group = group.replace(/_+$/, '');

            if (group) {
                var exists = false;

                jQuery('.referrer_group').each(function() {
                    if (jQuery(this).html() == group) {
                        alert('Group already exists!');
                        exists = true;
                        return false;
                    }
                });

                if (!exists) {
                    var li = jQuery('<li id="referrer_group_' + group + '"><table class="form-table"><tr><th>Group name:</th><td><span class="referrer_group_number">' + (jQuery('#referrer_groups li').size() + 1) + '.</span> <span class="referrer_group">' + group + '</span> <input type="button" class="button referrer_delete" value="Delete group" /></td></tr><tr><th><label for="referrer_groups_' + group + '_enabled">Enabled:</label></th><td><input type="hidden" name="referrer_groups[' + group + '][enabled]" value="0" /><input id="referrer_groups_' + group + '_enabled" type="checkbox" name="referrer_groups[' + group + '][enabled]" value="1" checked="checked" /></td></tr><tr><th><label for="referrer_groups_' + group + '_theme">Theme:</label></th><td><select id="referrer_groups_' + group + '_theme" name="referrer_groups[' + group + '][theme]"><option value="">-- Pass-through --</option></select><br /><span class="description">Assign this group of referrers to a specific them. Leaving this option "Active Theme" allows any plugins you have (e.g. referrer plugins) to properly handle requests for these referrers. If the "redirect users to" field is not empty, this setting is ignored.</span></td></tr><tr><th><label for="referrer_groups_' + group + '_redirect">Redirect users to:</label></th><td><input id="referrer_groups_' + group + '_redirect" type="text" name="referrer_groups[' + group + '][redirect]" value="" size="60" /><br /><span class="description">A 302 redirect is used to send this group of users to another hostname (domain); recommended if a 3rd party service provides a referrer version of your site.</span></td></tr><tr><th><label for="referrer_groups_' + group + '_referrers">Referrers:</label></th><td><textarea id="referrer_groups_' + group + '_referrers" name="referrer_groups[' + group + '][referrers]" rows="10" cols="50"></textarea><br /><span class="description">Specify the referrers for this group.</span></td></tr></table></li>');
                    var select = li.find('select');

                    jQuery.each(referrer_themes, function(index, value) {
                        select.append(jQuery('<option />').val(index).html(value));
                    });

                    jQuery('#referrer_groups').append(li);
                    w3tc_referrer_groups_clear();
                    window.location.hash = '#referrer_group_' + group;
                    li.find('textarea').focus();
                }
            } else {
                alert('Empty group name!');
            }
        }
    });

    jQuery('.referrer_delete').live('click', function() {
        if (confirm('Are you sure want to delete this group?')) {
            jQuery(this).parents('#referrer_groups li').remove();
            w3tc_referrer_groups_clear();
            w3tc_beforeupload_bind();
        }
    });

    w3tc_referrer_groups_clear();

    // add sortable
    if (jQuery.ui && jQuery.ui.sortable) {
        jQuery('#js_files,#css_files').sortable({
            axis: 'y',
            stop: function() {
                jQuery(this).find('li').each(function(index) {
                    jQuery(this).find('td:eq(0)').html((index + 1) + '.');
                });
            }
        });

        jQuery('#cdn_cnames').sortable({
            axis: 'y',
            stop: w3tc_cdn_cnames_assign
        });

        jQuery('#mobile_groups').sortable({
            axis: 'y',
            stop: function() {
                jQuery('#mobile_groups .mobile_group_number').each(function(index) {
                    jQuery(this).html((index + 1) + '.');
                });
            }
        });

        jQuery('#referrer_groups').sortable({
            axis: 'y',
            stop: function() {
                jQuery('#referrer_groups .referrer_group_number').each(function(index) {
                    jQuery(this).html((index + 1) + '.');
                });
            }
        });
    }

    // show hide rules
    jQuery('.w3tc-show-rules').click(function() {
        var btn = jQuery(this), rules = btn.parent().find('.w3tc-rules');

        if (rules.is(':visible')) {
            rules.css('display', 'none');
            btn.val('view code');
        } else {
            rules.css('display', 'block');
            btn.val('hide code');
        }
    });

    // check for unsaved changes
    jQuery('#w3tc input,#w3tc select,#w3tc textarea').live('change', function() {
        var ignore = false;
        jQuery(this).parents().andSelf().each(function() {
            if (jQuery(this).hasClass('w3tc-ignore-change') || jQuery(this).hasClass('lightbox')) {
                ignore = true;
                return false;
            }
        });

        if (!ignore) {
            w3tc_beforeupload_bind();
        }
    });

    jQuery('.w3tc-button-save').click(w3tc_beforeupload_unbind);
});
