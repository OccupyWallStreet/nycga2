var sampleTags = ['c++', 'java', 'php', 'coldfusion', 'javascript', 'asp', 'ruby', 'python', 'c', 'scala', 'groovy', 'haskell', 'perl', 'erlang', 'apl', 'cobol', 'go', 'lua'];

jQuery(function($) {

  
  $.each(gfcpt_tag_inputs.tag_inputs, function() {
    $(this.input).tagit({
      availableTags: gfcpt_tag_taxonomies[this.taxonomy],
      removeConfirmation: true,
      allowSpaces: true,
      animate:false
    });
  });

});

