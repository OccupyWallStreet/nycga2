/* Tabbed section widget */

var swtTabsOptions = {
    panelTemplate: '<div><div class="olt-swt-designer-top"><label for="#{idprefix}-#{tabid}-title">Title:</label> <input id="#{idprefix}-#{tabid}-title" class="olt-swt-designer-tab-title" name="#{nameprefix}[#{tabid}][title]" type="text" value="New Tab" /> <p class="olt-swt-designer-tabs-controls olt-swt-designer-delete-tab"><a href="#"><span class="ui-icon ui-icon-trash" style="float:left;margin-right:.3em;margin-top: -2px;"></span>Delete this tab</a></p></div><div class="olt-sw-body"><p class="olt-sw-body-help"><strong>Formatting Help:</strong> You may use HTML in this widget, and it is probably a good idea to wrap the content in your own <code>&lt;div&gt;</code> to aid styling. Shortcodes are also allowed, but please beware not all of them will function properly on archive pages.</p><textarea  rows="16" cols="20" name="#{nameprefix}[#{tabid}][body]"></textarea></div>',
    tabTemplate: '<li class="olt-swt-designer-tab" id="#{idprefix}-id-#{tabid}"><a href="#{href}" id="#{idprefix}-#{tabid}-title-link">#{label}</a></li>',
    add: function(event, ui) {
        var $ = jQuery;
        var t = $(this).tabs();
        var idprefix = t.parent().siblings('input[name=idprefix]:first').val();
        var nameprefix = t.parent().siblings('input[name=nameprefix]:first').val();
        var tabid = t.data('inc');
                    
        // Do a search and replace for #{idprefix} #{nameprefix} and #{tabid}
        $(ui.tab).parent().attr('id',$(ui.tab).parent().attr('id').replace(/#\{idprefix\}/g, idprefix).replace(/#\{nameprefix\}/g, nameprefix).replace(/#\{tabid\}/g, tabid));
        $(ui.tab).attr('id',$(ui.tab).attr('id').replace(/#\{idprefix\}/g, idprefix).replace(/#\{nameprefix\}/g, nameprefix).replace(/#\{tabid\}/g, tabid));
        $(ui.panel).html($(ui.panel).html().replace(/#\{idprefix\}/g, idprefix).replace(/#\{nameprefix\}/g, nameprefix).replace(/#\{tabid\}/g, tabid));
        
        // Reattach events
        $('.olt-swt-designer-tab-title', ui.panel).bind('change',function(){
            var val = $(this).val();
            var id  = $(this).attr('id');
            
            $('#'+id+'-link').text(val);
        });
        
        // Move "add" link to the end
        $(this).find('.olt-swt-designer-add-tab:first').remove()
            .appendTo($('.ui-tabs-nav',this));
        
        t.tabs('select', ui.index);
    },
    selected: 0
};

var swtSortableOptions = {
    axis:'x',
    items: 'li:not(.olt-swt-designer-tabs-controls)',
    update: function(){
        var $ = jQuery;
        
        var field = $(this).parents('.olt-swt-designer-wrapper:first').siblings('.olt-swt-order:first');
        
        field.val($(this).sortable('serialize',{'key':'order'}));
    }
};

function OLTSWTInit(t){
    var $ = jQuery;
    
    if(typeof(t) == 'undefined'){
        t = $('.olt-swt-designer-main');
    }
    
    t.tabs(swtTabsOptions).find('.ui-tabs-nav').sortable(swtSortableOptions);
        
    t.each(function(i,e){
        if(typeof($(e).data('inc')) == 'undefined'){
            var inc = $(e).tabs('length');
            $(e).data('inc',inc);
        }
    });
    
    // $.live doesn't work on 'change' events yet (remember to re-bind)
    $('.olt-swt-designer-tab-title',t).bind('change',function(){
        var val = $(this).val();
        var id  = $(this).attr('id');
        
        $('#'+id+'-link').text(val);
    });
    
    return t;
}

jQuery(document).ready(function($){
    OLTSWTInit();
    
    $('.olt-swt-designer-add-tab>a').live('click', function(){
        var t = OLTSWTInit($(this).parents('.olt-swt-designer-main:first'));
        var idprefix = t.siblings('input[name=idprefix]:first').val();
        var inc = t.data('inc');
        t.tabs('add','#'+idprefix+'-tab-'+inc, 'New Tab');
        t.data('inc',inc+1);
        return false;
    });
    
    $('.olt-swt-designer-delete-tab>a').live('click',function(){
        var t = $(this).parents('.olt-swt-designer-main:first').tabs();
        var i = t.tabs('option', 'selected');
        i=(i<0)?0:i; // fix a weird bug in jQuery UI, no idea why it returns -1 sometimes
        t.tabs('remove', i);
        return false;
    });
        
    // Option page
    $('head').append('<link id="swt-preview-style" rel="stylesheet" href="" type="text/css" media="all" />');

    var stylesheet = $('#swt-preview-style');
    
    $('#swt-theme-preview-wrapper').tabs();
    
    $('#swt-theme').change(function(){
        if($(this).val() == 'none')
            stylesheet.attr('href', '');
        else
            stylesheet.attr('href', stylesheet_url + '?theme=' + $(this).val() + '&scope=%23swt-theme-preview');
    }).change();
    
    $('#swt-theme-preview-link').click(function(){
        $('#swt-theme-preview').slideDown();
        return false;
    });
    
    $('#swt-theme-preview-hide-link').click(function(){
        $('#swt-theme-preview').slideUp();
        return false;
    });
    
    $('#swt-scope-help-link').click(function(){
        $('#swt-scope-help').slideDown();
        return false;
    });
    
    $('#swt-scope-help-hide-link').click(function(){
        $('#swt-scope-help').slideUp();
        return false;
    });
    
    $('#swt-scope-detect').click(function(){
        // Hide the button
        $(this).hide();
        
        // Start scope detect
        $('body').append('<iframe id="swt-scope-detect-iframe" style="display:none" />');
        
        // Grab iframe and message box
        var iframe = $('#swt-scope-detect-iframe');
        var output = $(this).siblings('#swt-scope-detect-message').empty().show();
        
        var scopes;
        var links_copy;
        
        var scopeTest = function(){            
            output.append('<div class="message">Initializing scope detect...</div>');
            
            if(typeof(links) == 'undefined' || links.length < 1) {
                output.append('<div class="error">Cannot find JavaScript variable "links"...</div>');
                getResult();
                return false;
            }
            
            // Reset vars
            scopes = [];
            links_copy = links.slice();
            iframe.unbind('load');
            
            // Kick off the test
            testNext();
        };
        
        var testNext = function(event){
            if(typeof(event) != 'undefined') {
                var items = $(this).contents().find('.swt-wrapper');
                var safe = false;
                
                if(items.length < 1) {
                    output.append('<div class="warning">No widget instances found, skipping...</div>');
                } else {
                    if(items.length < 2) {
                        output.append('<div class="warning">Only one widget instance found, this might lead to inaccurate result...</div>');
                        safe = true;
                    }
                    
                    items.each(function() {
                        var parents = $(this).parents();
                        var chain = [];
                        
                        parents.each(function() {
                            var id = $(this).attr('id');
                            if(id != '') chain.push(id);
                        });
                        
                        if(safe) chain.shift();
                        
                        scopes.push(chain);
                    });
                }
            }
                        
            if(links_copy.length < 1) {
                getResult();
            } else {
                link = links_copy.pop();
                output.append('<div class="message">Trying '+link+' ...</div>');
                iframe.attr('src',link).load(testNext);
            }
        };
        
        var getResult = function() {
            if(scopes.length < 1) {
                output.append('<div class="error">Scope detection has failed. Your settings are unchanged.</div>');
            } else {        
                var seed = scopes.shift();
                var result = [];
                
                for(var i=0;i<seed.length;i++){
                    var common = true;
                    var element = seed[i];
                    
                    for(var j=0;j<scopes.length;j++){
                        if($.inArray(element, scopes[j]) == -1){
                            common = false;
                            break;
                        }
                    }
                    
                    if(common){
                        result.push('#'+element);
                    }
                }
                
                result.reverse().push('.swt-outter');
                
                $('#swt-scope').val(result.join(' '));
                
                output.append('<div class="success">Scope detection has completed. Your optimal CSS scope is <strong>&quot;'+result.join(' ')+'&quot;</strong> and it has been filled in for you.<br /><strong>Don\'t forget to save your changes!</strong></div>');
            }
            
            $('#swt-scope-detect').show();
        };
        
        scopeTest(iframe, output);
        
        return false;
    });
});