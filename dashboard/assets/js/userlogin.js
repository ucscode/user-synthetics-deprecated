"use strict";

$(function() {
	
	let userlogin = [ "#bkend-signup", "#bkend-login", "#bkend-reset", "#bkend-changepass" ];
	
	for( let id of userlogin ) {
		
		let userform = $(id).get(0); 
		if( !userform || userform.tagName != 'FORM' ) continue;
		
		// -- [ resend confirmation email ] --
		
		let remail = $(userform).parent().find("#re-mail");
		if( remail.length ) (function() {
			let typed = '';
			let submitted = false;
			let show_email_dialog = function() {
				let container = $($("[data-ref='re-mail']").get(0).cloneNode(true)).removeClass('d-none').get(0);
				uss.meth.modal({
					content: container,
					dialogClass: 'modal-dialog-centered',
					modalClass: '',
					method: {
						"shown.bs.modal": function() {
							let form = $("#the-modal [data-ref='re-mail']");
							let input = $(form).find('input[type="email"]').on('input', function() {
								typed = this.value;
							}).val(typed);
							$(form).on('submit', function(e) {
								e.preventDefault();
								if( submitted ) return;
								else submitted = true;
								uss.meth.preloader(1, undefined, 2000);
								$.ajax({
									url: uss.srv.root + '/ajax.php?action=ajax/email-resend',
									data: $(form).serialize(),
									method: 'POST',
									success: function(response) {
										uss.meth.preloader(0);
										submitted = false;
										let result = uss.meth.JSONParser(response), message;
										if( !result ) message = uss.prop.error[1];
										else message = result.message;
										uss.meth.modal(false).then(function() {
											uss.meth.modal(message).then(function() {
												//if( !result.status ) show_email_dialog();
											});
										});
									}
								});
							});
						}
					}
				})
			};
			remail.click(function() {
				show_email_dialog();
			});
		})();
		
		// -- [ /resend confirmation email ] --
		
		$(userform).on('submit', function(e) {
			
			e.preventDefault();
			
			// -- [ signup ] --
			
			if( id == '#bkend-signup' ) (function() {
				let pass = $(userform).find("#signup-password").val();
				let conpass = $(userform).find("#confirm-password").val();
				if( pass !== conpass ) return uss.meth.modal("Password does not match");
				uss.meth.preloader();
				$.ajax({
					method: 'POST',
					data: $(userform).serialize(),
					url: uss.srv.root + "/ajax.php?action=ajax/user-signup",
					success: function(response) {
						uss.meth.preloader(0);
						let result = uss.meth.JSONParser(response);
						if( !result ) return uss.meth.modal( uss.prop.error[1] );
						uss.meth.modal( result.message ).then(function() {
							if( result.status ) {
								window.location.href = result.data.redirect;
								uss.meth.preloader();
							}
						});
					}
				});
			})();
			
			
			// -- [ login ] --
			
			if( id == '#bkend-login' ) (function() {
				uss.meth.preloader();
				$.ajax({
					method: "POST",
					data: $(userform).serialize(),
					url: uss.srv.root + "/ajax.php?action=ajax/user-login",
					success: function(response) {
						uss.meth.preloader(0);
						let result = uss.meth.JSONParser(response);
						if( !result ) return uss.meth.modal( uss.prop.error[1] );
						uss.meth.modal( result.message );
						if( result.status ) {
							uss.meth.preloader();
							setTimeout(function() {
								if( !uss.authorizing ) window.location.href = uss.srv.root;
								else window.location.reload();
							}, 3000);
						};
					}
				});
			})();
			
			
			// -- [ reset password ] --
			
			if( id == '#bkend-reset' ) (function() {
				uss.meth.preloader();
				$.ajax({
					method: "POST",
					url: uss.srv.root + '/ajax.php?action=ajax/password-reset',
					data: $(userform).serialize(),
					success: function(response) {
						uss.meth.preloader(0);
						let result = uss.meth.JSONParser(response);
						if( !result ) return uss.meth.modal( uss.prop.error[1] );
						uss.meth.modal( result.message );
					}
				});
			})();
			
			
			// -- [ update password ] --
			
			if( id == '#bkend-changepass' ) (function() {
				let _reset = $("#reset-password");
				let _conf = $("#confirm-password");
				if( _reset.val() !== _conf.val() ) return uss.meth.modal("Password does not match");
				uss.meth.preloader();
				$.ajax({
					method: 'POST',
					url: uss.srv.root + "/ajax.php?action=ajax/password-update",
					data: $(userform).serialize(),
					success: function(response) {
						uss.meth.preloader(0);
						let result = uss.meth.JSONParser(response);
						if( !result ) return uss.meth.modal( uss.prop.error[1] );
						uss.meth.modal( result.message ).then(function() {
							if( result.status ) {
								uss.meth.preloader();
								window.location.href = result.data.redirect;
							}
						});
					}
				});
			})();
			
			// -- [ developed by ucscode ] --
		
		});
		
	};
	
})