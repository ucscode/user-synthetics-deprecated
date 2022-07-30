"use strict";

$(function() {

	let userprofile = [ '#profile_avatar', '#profile_email', '#profile_password' ];

	for( let id of userprofile ) {
		
		let userform = $(id).get(0);
		if( !userform ) continue;
		
		// --- [ process avatar ] ---
		
		if( id == '#profile_avatar' ) (function(userform) {
			
			let input = $(userform).find("[name='avatar']").get(0);
			let img = $(userform).find("img").get(0);
			
			let savebtn = $(userform).find("#change-avatar").click(function() {
				input.click();
			}).next().get(0);
			
			let max_avatar_size = ( !uss.srv.max_avatar_size ) ? 780 : uss.srv.max_avatar_size;
			let file;
			
			$(input).on('change', function() {
				
				let filer = new FileReader();
				file = this.files[0];
				
				if( !file ) return uss.meth.modal( "No file was uploaded" );
				else if( !(/^image\//i).test(file.type) ) return uss.meth.modal("Unaccepted file type");
				
				// -- [ manage file size ] --
				let KB = (file.size / 1024); // kilobyte
				if( KB > max_avatar_size ) {
					return uss.meth.modal( "<i class='fas fa-image'></i> - Image size should not be larger than " + max_avatar_size + "KB" );
				};
				
				// -- [ preview avatar ] --
				filer.onload = function(e) {
					img.src = this.result;
				};
				if( file ) filer.readAsDataURL( file );
				
			});
			
			$(userform).on("submit", function(e) {
				e.preventDefault();
				if( !file ) return uss.meth.modal( "<i class='fas fa-info'></i> - No changes were made to avatar" );
				let formdata = new FormData(this);
				uss.meth.preloader();
				$.ajax({
					method: 'POST',
					url: uss.srv.root + '/ajax.php?action=ajax/profile-update-avatar',
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
						if( result.status ) $("#user-dropdown-toggle img").attr('src', img.src);
					}
				});
			});
			
		})(userform);
		
		
		// --- [ process email ] ----
		
		if( id == '#profile_email' ) (function(userform) {
			
			$(userform).on('submit', function() {
				
				uss.meth.preloader();

				$.ajax({
					method: "POST",
					url: uss.srv.root + "/ajax.php?action=ajax/profile-update-profile",
					data: $(userform).serialize(),
					success: function(response) {
						uss.meth.preloader(0);
						let result = uss.meth.JSONParser(response);
						if( !result ) return uss.meth.modal( uss.prop.error[1] );
						uss.meth.modal( result.message );
					}
				});
				
			});
			
		})(userform);
		
		
		// --- [ process password ] ----
		
		if( id == '#profile_password' ) (function(userform) {
			
			let inputs = ["[name='prev-password']", "[name='new-password']", "#new-password"];
			for( let x in inputs ) inputs[x] = $(inputs[x]).get(0);
			
			$(userform).on('submit', function(e) {
				
				e.preventDefault();
				
				if( inputs[1].value != inputs[2].value ) return uss.meth.modal( "Password does not match" );
				
				uss.meth.preloader();

				$.ajax({
					method: "POST",
					url: uss.srv.root + "/ajax.php?action=ajax/profile-update-password",
					data: $(userform).serialize(),
					success: function(response) {
						uss.meth.preloader(0);
						let result = uss.meth.JSONParser(response);
						if( !result ) return uss.meth.modal( uss.prop.error[1] );
						uss.meth.modal( result.message ).then(function() {
							if( result.status ) {
								uss.meth.preloader();
								window.location.reload();
							}
						});
					}
				});
				
			});
			
		})(userform);
		
	}

});