// change the menu position based on the scroll positon
window.onscroll = function()
{
    if( window.XMLHttpRequest ) {
        if (document.documentElement.scrollTop > 160 || self.pageYOffset > 160) {
            jQuery('#floatMenu').css('position','fixed');
            jQuery('#floatMenu').css('top','5px');
        } else if (document.documentElement.scrollTop < 112 || self.pageYOffset < 112) {
            jQuery('#floatMenu').css('position','absolute');
            jQuery('#floatMenu').css('top','112px');
        }
    }
}