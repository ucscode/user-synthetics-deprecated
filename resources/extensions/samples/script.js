"use strict";

$(function() {
	
	let form_group = 'col-lg-2 col-md-3 mb-2 col-sm-4 col-6';
	
	function __exec__(form, func) {
		uss.meth.preloader();
		$.ajax({
			url: uss.srv.sample_ajax,
			data: $(form).serialize(),
			method: 'POST',
			success: function(response) {
				uss.meth.preloader(0);
				var result = uss.meth.JSONParser(response);
				if( !result ) return uss.meth.modal( uss.prop.error[1] );
				uss.meth.modal(result.message).then(function() {
					if( typeof func == 'function' ) return func(result);
					else if( result.status ) {
						uss.meth.preloader();
						window.location.reload();
					}
				});
			}
		});
	}
	
	$("[data-php]").each(function() {
		
		let self = this;
		let eid = '#' + self.id;
		let vue;
		
		switch( self.id ) {
			
			case "menu":
					vue = new Vue({
						el: eid,
						data: (function() {
							let preset = {};
							$(self).find("[v-model]").each(function() {
								preset[ this.getAttribute('v-model') ] = this.value;
							});
							preset['form_group'] = form_group;
							preset['uss_method'] = 'add';
							preset['parent'] = '';
							return preset;
						})(),
						watch: {
							uss_class: function(val) {
								this.uss_method = 'add';
								this.parent = '';
							},
							parent: function(val) {
								this.uss_method = (val.trim() == '') ? 'add' : 'add_submenu';
							}
						}
					});
					$(vue.$el).find("form").on('submit', function() {
						__exec__(this);
					});
				break;
				
			case "page":
					vue = new Vue({
						el: eid, 
						data: (function() {
							let preset = {};
							$(self).find("[v-model]").each(function() {
								preset[ this.getAttribute('v-model') ] = this.value;
							});
							preset['form_group'] = form_group;
							return preset;
						})()
					});
					$(vue.$el).find("form").on('submit', function() {
						__exec__(this);
					});
				break;
				
			case "customize":
					let hook = $("#v-hook");
					vue = new Vue({
						el: eid,
						data: {
							css_code: $(self).find("textarea").val()
						},
						watch: {
							css_code: function(val) {
								hook.text( val );
							}
						}
					});
					
				break;
				
			case "head_foot":
					vue = new Vue({
						el: eid,
						data: (function() {
							let preset = {}
							$(self).find("[v-model]").each(function() {
								preset[ this.getAttribute('v-model') ] = this.value;
							});
							return preset;
						})()
					});
					$(vue.$el).find("form").on('submit', function() {
						__exec__(this);
					});
				break;
		};
		
	});
	
	$('[data-bs-toggle="popover"]').each(function() {
		new bootstrap.Popover(this);
	});
	
});