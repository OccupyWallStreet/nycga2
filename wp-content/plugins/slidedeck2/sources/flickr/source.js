(function($){
    window.FlickrSource = {
        elems: {},
        
        initialize: function(){
            var self = this;
            
            this.elems.form = $('#slidedeck-update-form');
            
            this.slidedeck_id = $('#slidedeck_id').val();
            
            // Prevent enter key from submitting text field for adding flickr tags
            this.elems.form.delegate('#flickr-add-tag-field', 'keydown', function(event){
                if( 13 == event.keyCode){
                    event.preventDefault();
                    $('.flickr-tag-add').click();
                    return false;
                }
                return true;
            });
            
            // Flickr Tags adder
            self.elems.form.delegate('.flickr-tag-add', 'click', function(event){
                event.preventDefault();
                
                var currentEntry = $('#flickr-add-tag-field').val();
                if( currentEntry ){
                    $('#flickr-add-tag-field').val('');
                    
                    var tags = currentEntry.split(',');
                    
                    for (var i=0; i < tags.length; i++) {
                        var tag = $.trim(tags[i]);
                        var newTag = '<span>';
                        newTag += '<a href="#delete" class="delete">X</a> ';
                        newTag += tag;
                        newTag += '<input type="hidden" name="flickr_tags[]" value="' + tag + '" />';
                        newTag += '</span> ';
                        
                        $('#flickr-tags-wrapper').append( newTag );
                        SlideDeckPreview.ajaxUpdate();
                    };
                    
                }
            });
            
            // Flickr Tags Delete
            self.elems.form.delegate('#flickr-tags-wrapper .delete', 'click', function(event){
                event.preventDefault();
                $(this).parents('span').remove();
                
                if (self.elems.form.timer)
                    clearTimeout(self.elems.form.timer);
                
                // Set delay timer so a check isn't done on every single key stroke
                self.elems.form.timer = setTimeout(function(){
                    SlideDeckPreview.ajaxUpdate();
                }, 500 );
                
            });
        }
    };
    
    var ajaxOptions = [
        "options[flickr_tags_mode]",
        "options[flickr_recent_or_favorites]"
    ];
    for(var o in ajaxOptions){
        SlideDeckPreview.ajaxOptions.push(ajaxOptions[o]);
    }
    
    $(document).ready(function(){
        FlickrSource.initialize();
    });
})(jQuery);
