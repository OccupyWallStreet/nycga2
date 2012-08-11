var edButtons = new Array();
edButtons[edButtons.length] = 
new edButton('ed_strong'
,'bold'
,'<strong>'
,'</strong>'
,'b'
);


edButtons[edButtons.length] = 
new edButton('ed_em'
,'italic'
,'<em>'
,'</em>'
,'i'
);


edButtons[edButtons.length] = 
new edButton('ed_link'
,'link'
,''
,''
,'a'
); // special case


edButtons[edButtons.length] = 
new edButton('ed_block'
,'bquote'
,'<blockquote>'
,'</blockquote>'
,'q'
);


edButtons[edButtons.length] = 
new edButton('ed_pre'
,'code'
,'<code>'
,'</code>'
,'c'
);


edButtons[edButtons.length] =
new edButton('ed_strike'
,'strike'
,'<strike>'
,'</strike>'
,'s'
);

edButtons[edButtons.length] =
new edButton('ed_branket'
,'branket'
,'&lt;'
,'&gt;'
,'h'
);