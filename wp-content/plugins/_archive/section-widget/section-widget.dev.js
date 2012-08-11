/* Tabbed section widget */

jQuery(document).ready(function($){    
    $('.swt-wrapper').tabs();
    
    // Height fix for problematic themes (i.e. the default one)
    $('.swt-height-fix').each(function(i,e){
        var displayMode = $(e).css('display');
        var realHeight  = $(e).css('display','inline-block').height();
        $(e).css('display',displayMode).height(realHeight);
    });
});