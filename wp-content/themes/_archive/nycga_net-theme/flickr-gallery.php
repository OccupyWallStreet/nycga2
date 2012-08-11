<?php
//**
//Flickr Gallery
//**
?>

	<style>#flickr-gallery{height:400px;}</style>
	
	<div id="flickr-gallery"></div>
	
	<script type="text/javascript">

    // Load  theme
    Galleria.loadTheme(<?php get_stylesheet_directory_uri() ?>'/_inc/js/galleria/themes/nycga/galleria.nycga.js');
    
    var flickr = new Galleria.Flickr();
			flickr.user('occupywallstreet', function(data) {
			    Galleria.run('#flickr-gallery', {
			        dataSource: data
			    });
			});
			
			Galleria.configure({
			    showImagenav: true,
			    autoplay: 5000,
			    showInfo: true,
			    showCounter:true,
			    thumbnails:false,
			    transition:"fade",
			    transitionSpeed:600,
			    imageCrop: true,
			    carousel: false,
			    responsive: true,
			    height: 400
			});

    </script>