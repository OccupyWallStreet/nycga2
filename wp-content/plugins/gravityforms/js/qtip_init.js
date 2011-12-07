jQuery.fn.qtip.styles.gformsstyle = { // Last part is the name of the style
   width: 275,
   background: '#F1F1F1',
   color: '#424242',
   textAlign: 'left',
   border: { width: 3, radius: 6, color: '#AAAAAA' },
   tip: 'bottomLeft'
}

// Create the tooltips only on document load
jQuery(document).ready(function()
{
   // Notice the use of the each() method to acquire access to each elements attributes
   jQuery('.tooltip').each(function()
   {

       jQuery(this).qtip({
         content: jQuery(this).attr('tooltip'), // Use the tooltip attribute of the element for the content
         show: { delay: 200, solo: true },
         hide: { when: 'mouseout', fixed: true, delay: 200, effect: 'fade' },
         style: 'gformsstyle', // custom tooltip style
         position: {
      		corner: {
         		target: 'topRight'
                ,tooltip: 'bottomLeft'
      		}
  		 }
      });
   });
});

jQuery.fn.qtip.styles.gformsstyle_left = { // Last part is the name of the style
   width: 275,
   background: '#F1F1F1',
   color: '#424242',
   textAlign: 'left',
   border: { width: 3, radius: 6, color: '#AAAAAA' },
   tip: 'bottomRight'
}

// Create the tooltips only on document load
jQuery(document).ready(function()
{
   // Notice the use of the each() method to acquire access to each elements attributes
   jQuery('.tooltip_left').each(function()
   {
      jQuery(this).qtip({
         content: jQuery(this).attr('tooltip'), // Use the tooltip attribute of the element for the content
         show: { delay: 500, solo: true },
         hide: { when: 'mouseout', fixed: true, delay: 200, effect: 'fade' },
         style: 'gformsstyle_left', // custom tooltip style
         position: {
          corner: {
               target: 'topLeft',
               tooltip: 'bottomRight'
          }
          }
      });
   });
});

jQuery.fn.qtip.styles.gformsstyle_bottomleft = { // Last part is the name of the style
   width: 275,
   background: '#F1F1F1',
   color: '#424242',
   textAlign: 'left',
   border: { width: 3, radius: 6, color: '#AAAAAA' },
   tip: 'topRight'
}

// Create the tooltips only on document load
jQuery(document).ready(function()
{
   // Notice the use of the each() method to acquire access to each elements attributes
   jQuery('.tooltip_bottomleft').each(function()
   {
      jQuery(this).qtip({
         content: jQuery(this).attr('tooltip'), // Use the tooltip attribute of the element for the content
         show: { delay: 500, solo: true },
         hide: { when: 'mouseout', fixed: true, delay: 200, effect: 'fade' },
         style: 'gformsstyle_bottomleft', // custom tooltip style
         position: {
          corner: {
               target: 'bottomLeft',
               tooltip: 'topRight'
          }
          }
      });
   });
});