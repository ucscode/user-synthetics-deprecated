"use strict";

$(function() {

	$("form").each(function() {
		
		switch( this.id ) {
			
			case '__basic':
				let fileObj = {};
				$(this).find("[data-filer]").each(function() {
					fileObj[ this.tagName.toLowerCase() ] = this;
				});
				fileObj.button.addEventListener('click', () => { 
					fileObj.input.click(); 
				});
				fileObj.input.addEventListener('change', function() {
					let file = this.files[0];
					if( !file ) return uss.meth.modal("No image was uploaded!");
					let filereader = new FileReader();
					filereader.addEventListener('load', function() {
						fileObj.img.src = this.result;
					});
					filereader.readAsDataURL(file);
				});
				break;
				
			
		}
		
		
		//! --------------- [ SUBMIT FORM ] ---------------------
		
		$(this).on('submit', function(e) {
			e.preventDefault();
			let formdata = new FormData(this);
			uss.meth.preloader();
			$.ajax({
				method: 'POST',
				url: uss.srv.ins + '/admin/ajax.php?action=ajax/settings',
				data: formdata,
				async: true,
				processData: false,
				contentType: false,
				cache: false,
				success: function(response) {
					uss.meth.preloader(0);
					let result = uss.meth.JSONParser(response);
					if( !result ) return uss.meth.modal( uss.prop.error[1] );
					uss.meth.modal( result.message );
				}
			});
		});
		
		//! ----------------- [ /SUBMIT FORM ] -----------------------
		
	});

});