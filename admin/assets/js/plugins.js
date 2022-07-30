"use strict";

$(function() {
	
	function exec(app, response, func) {
		app.loading = false;
		var result = uss.meth.JSONParser(response);
		if( !result ) return uss.meth.modal( uss.prop.error[1] );
		uss.meth.modal( result.message ).then(function() {
			if( typeof func == 'function' ) func(result);
			else if( result.status ) {
				uss.meth.preloader();
				window.location.reload();
			}
		});
	};
	
	$(".app-card-plugin").each(function() {
		
		let self = this;
		let Name = $(self).find("input[name='plugin']").val();
		let OTP = $(self).find("input[name='otp']").val();
		
		let __url = uss.srv.admin_ajax + "?action=ajax/uss-plugins.php";
		
		let app = new Vue({
			
			el: self,
			
			data: {
				loading: false,
				active: $(self).hasClass('active')
			},
			
			methods: {
				
				state: function(e) {
					app.loading = true;
					$.ajax({
						method: 'POST',
						url: __url,
						data: {
							status: this.active ? 1 : 0,
							otp: OTP,
							plugin: Name,
							request: "state"
						},
						success: function(response) {
							exec(app, response);
						}
					});
				}, // -- ( end state ) --
				
				trash: function() {
					uss.meth.modal({
						content: "Are you sure you want to delete this plugin?",
						confirm: ["Yes", "No"]
					}).then(function(ok) {
						if( !ok ) return;
						app.loading = true;
						$.ajax({
							method: 'POST',
							url: __url,
							data: {
								request: 'trash',
								plugin: Name,
								otp: OTP
							}, 
							success: function(response) {
								exec(app, response);
							}
						});
					});
				} // -- ( end trash ) --
				
			} // -- ( end methods ) --
			
		});

	});
	
	// 
	
	$("#plug-type").on('change', function() {
		let value = this.value.trim();
		let query = uss.meth.setQuery("type", this.value != '' ? this.value : null);
		window.location.search = query;
	});

});