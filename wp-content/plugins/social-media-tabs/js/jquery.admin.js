jQuery(document).ready(function($) {
			$('#dcsmt_redirect').change(function(){
				var redirect = $('#dcsmt_redirect option:selected').val();
				$('.redirect-hide').hide();
				$('.redirect-'+redirect).show();
			});
			$('.hide-init').hide();
		});