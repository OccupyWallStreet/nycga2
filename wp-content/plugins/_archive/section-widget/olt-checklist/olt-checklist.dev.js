/*
    OLT Checklist Helper Functions (JavaScript)
    
    Version 1.1.3
*/

jQuery(document).ready(function($){
    OLTChecklistPaneInit();
    OLTChecklistInit(document); // For non-pane checklists
    
    // Tabbify on tab-click
    $('div.olt-checklist-wrapper>ul.ui-tabs-nav>li>a').live('click', function(e){
        e.preventDefault();
        
        var container = $(this).parents('div.olt-checklist-wrapper:first');
        
        if(container.data('olt-checklist-tabs-initialized') != true) {
            OLTChecklistPaneInit(container);
            e.stopPropagation();
            $(e.target).click();
        }
    });
    
    // Controls wrapper
    $('div.olt-checklist-entry').live('mouseover', function(){
        $(this).children('span.olt-checklist-controls-wrapper').show();
    });
    $('div.olt-checklist-entry').live('mouseout', function(){
        $(this).children('span.olt-checklist-controls-wrapper').hide();
    });
    
    // Controls
    $('a.olt-checklist-controls-collapse').live('click', OLTChecklistCollapse);
    $('a.olt-checklist-controls-expand').live('click', OLTChecklistCollapse);
    $('a.olt-checklist-controls-select').live('click', OLTChecklistSelect);
    $('a.olt-checklist-controls-deselect').live('click', OLTChecklistSelect);
    
    // Actions for the contorls...
    
    function OLTChecklistCollapse(){
        e = $(this);
        e.toggleClass('olt-checklist-controls-collapse')
         .toggleClass('olt-checklist-controls-expand')
         .parents('.olt-checklist-entry:first').siblings('ul.children').slideToggle('fast');
        
        if(e.hasClass('olt-checklist-controls-collapse'))
            e.text('Collapse');
        else
            e.text('Expand');
        
        return false;
    }
    
    function OLTChecklistSelect(){
        e = $(this);
        e.toggleClass('olt-checklist-controls-select')
         .toggleClass('olt-checklist-controls-deselect')
         .parents('li:first').find('input:checkbox') // There must be a better way...
            .each(function(){
                this.checked = e.hasClass('olt-checklist-controls-deselect');
            });
        
        if(e.hasClass('olt-checklist-controls-select'))
            e.text('Select All');
        else
            e.text('Deselect All');
        
        return false;
    }
});

function OLTChecklistPaneInit(e){
    e = e || jQuery('div.olt-checklist-wrapper');
        
    jQuery(e).each(function(i,e){
        jQuery(e).tabs({'selected' : 0}).data('olt-checklist-tabs-initialized',true);
        OLTChecklistInit(e);
    });
}

function OLTChecklistInit(e){
    e = e || jQuery('div.olt-checklist-wrapper>ul.ui-tabs-nav');
    
    // Find all parents...
    jQuery('li:has(ul.children)>div.olt-checklist-entry',e).each(function(i,e) {
        // Add the expand/collapse + select/deselect controls,
        // but need to make sure it's not already there
        jQuery(e).not('*:has(span.olt-checklist-controls-wrapper)')
            .append(' <span class="olt-checklist-controls-wrapper"><a href="#" class="olt-checklist-controls-collapse">Collapse</a> | <a href="#" class="olt-checklist-controls-select">Select All</a></span>');
    });
}

function sectionWidgetCreateTabs(){
    // Just in case some folks forgot to undo the patch
}