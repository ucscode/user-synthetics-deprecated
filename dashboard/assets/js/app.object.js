"use strict";

/*
	Created by ucscode
	
	(-_-) - https://github.com/ucscode
	
	(^_^) - https://ucscode.com
	
*/

$(function() {
	
	/* create modal application */
	
	let modalApp, accept, resolved, modalMethods = {
		'show.bs.modal': undefined, 
		'shown.bs.modal': undefined, 
		'hide.bs.modal': undefined, 
		'hidePrevented.bs.modal': undefined
	};
	
	modalApp = new Vue({ 
		el: "#the-modal", 
		data: { 
			title: window.location.hostname,
			content: '',
			confirm: true,
			accept: "Accept",
			reject: 'Close',
			modalClass: 'fade',
			dialogClass: ''
		}
	});
	
	$(modalApp.$el).find("[data-promise]").click(function() {
		accept = (this.dataset.promise == 'accept');
		if( !accept ) accept = !modalApp.$el.querySelector("[data-promise='accept']");
	});
	
	modalApp.$el.addEventListener("hidden.bs.modal", function() {
		if( typeof resolved == 'function' ) resolved(accept);
		resolved = undefined;
	});
	
	$.each(modalMethods, function(event) {
		modalApp.$el.addEventListener(event, function() {
			if( typeof modalMethods[event] == 'function' ) modalMethods[event]();
			modalMethods[event] = undefined;
		});
	});
	
	let bsm = new bootstrap.Modal( modalApp.$el );
	
	// -- [ open a modal box ] --
	
	uss.meth.modal = function(input = '') {
		
		if( input === false || input === null ) {
			return new Promise(function(next) {
				resolved = next;
				bsm.hide();
			});
		}
		
		if( uss.meth.isTrueObject(input) == false ) input = { content: input };
		
		modalApp.title = input.title ? input.title : window.location.hostname;
		
		if( typeof input.content == 'object' && input.content.nodeType === 1 && input.content.outerHTML ) {
			modalApp.content = input.content.outerHTML;
		} else modalApp.content = input.content.toString();
		
		modalApp.confirm = !!input.confirm;
		if( !Array.isArray(input.confirm) ) input.confirm = [];
		
		for( let x = 0; x < 2; x++ ) {
			let val = input.confirm[x];
			if( !val || val.trim() == '' || !['string', 'number'].includes(typeof val) ) {
				input.confirm[x] = (x == 0) ? "Accept" : "Close";
			}
		};
		
		modalApp.accept = input.confirm[0];
		modalApp.reject = input.confirm[1];
		modalApp.dialogClass = (typeof input.dialogClass == 'string' ) ? input.dialogClass : '';
		modalApp.modalClass = (typeof input.modalClass == 'string') ? input.modalClass : 'fade';
		
		if( uss.meth.isTrueObject(input.method) ) {
			$.each(input.method, function(event, func) {
				if( !Object.keys(modalMethods).includes(event) || typeof func != 'function' ) return;
				modalMethods[event] = func;
			});
		}
		
		return new Promise(function(resolve) {
			bsm._config.backdrop = bsm._config.keyboard = !modalApp.confirm;
			bsm.show();
			resolved = resolve;
		});
		
	};
	
	// -- [ convert JSON string to object ] --
	
	uss.meth.JSONParser = function( json ) {
		try {
			let result = JSON.parse( json );
			return result;
		} catch(e) {
			console.log( e );
			return false;
		};
	}
	
	uss.prop.error = [
		"The request could not be handled",
		"Internal server error"
	];
	
	// -- [ show preloader ] --
	
	uss.meth.preloader = function( show = true, bgColor, zIndex ) {
		$(".app-preload")[ show ? 'fadeIn' : 'fadeOut' ]('slow').get(0).style.zIndex = null;
		if( bgColor === undefined ) bgColor = '#000000a1';
		if( show ) {
			if( typeof bgColor == 'string' ) $(".app-preload").css("backgroundColor", bgColor);
			if( zIndex ) $(".app-preload").css("z-index", zIndex);
		};
	}
	
	uss.meth.preloader( false );
	
	
	/*
		since Array:[], Object:{} and Null are all object,
		check if element is Object of type {}
	*/
	
	uss.meth.isTrueObject = function(el) {
		return ( 
			el &&
			typeof el == 'object' && 
			el !== null && 
			!Array.isArray(el) &&
			el instanceof Object
		);
	}
	
	
	// -- [ get the query string as object from URL ] --
	
	uss.meth.getQuery = function( key ) {
		let query = {}, __string = window.location.search.slice(1);
		if( !__string ) return query;
		let __array = __string.split("&");
		$.each(__array, function(i, string) {
			if( string === '' ) return;
			var array = string.split("=");
			var key = decodeURIComponent(array[0]);
			var value = (typeof array[1] != 'undefined') ? decodeURIComponent(array[1]) : '';
			query[key] = value;
		});
		return key ? query[key] : query;
	}
	
	
	// -- [ Convert object to Query string ] --
	
	uss.meth.setQuery = function( entity, value = null ) {
		if( !entity && parseInt(entity) !== 0 ) return false;
		var query = this.getQuery();
		if( !this.isTrueObject(entity) ) {
			if( value === null ) delete query[entity];
			else query[entity] = !['number', 'string'].includes(typeof value) ? '' : value;
		} else {
			$.each(entity, function(name, value) {
				if( !['number', 'string'].includes(typeof name) ) return;
				else if( value === null ) {
					delete query[name];
					return;
				};
				query[name] = !['number', 'string'].includes(typeof value) ? '' : value;
			});
		};
		var __string = '';
		$.each(query, function(key, value) {
			var key = encodeURIComponent(key);
			var value = encodeURIComponent(value);
			var result = key + "=" + value;
			__string += result + '&';
		});
		__string = __string.slice(0, -1)
		return __string;
	}
	
	
	//! --- [ tabledata page control ] ---
	
	$("[data-uss-simple-tabledata]").each(function() {
		
		let self = this;
		let checkers = $(self).find("[data-uss-checked='td']");
		
		// -- [ pagination buttons ] --
		
		$( self ).find("[data-uss-paged]").click(function() {
			let search = uss.meth.setQuery({
				'tabledata': this.dataset.ussNavControl,
				'paged': this.dataset.ussPaged
			});
			window.location.search = search;
		});
		
		// -- [ pagination dropdown ] --
		
		$( self ).find("[data-uss-page-control] button").click(function() {
			let tablename = $(this).parents("[data-uss-page-control]").get(0).dataset.ussPageControl;
			let search = uss.meth.setQuery({
				'tabledata': tablename,
				'paged': $(this).prev().val()
			});
			window.location.search = search;
		});
		
		// -- [ table checkboxes ] --
		
		$( self ).find("[data-uss-checked]").on('change', function() {
			let input = this;
			let pos = input.dataset.ussChecked;
			let checked = $(self).find("[data-uss-checked='td']:checked");
			if( pos == 'td' ) {
				$(self).find("[data-uss-checked='th']").each(function() {
					this.checked = ( checkers.length == checked.length );
				});
			} else {
				$(self).find("[data-uss-checked]").each(function() {
					this.checked = input.checked;
				});
			};
		});
		
		// -- [ table search ] --
		
		$( self ).find("[data-uss-tabledata-search]").on('submit', function(e) {
			e.preventDefault();
			let value = $( this ).find('input').val();
			let data = {
				'paged': null,
				'search': value.trim() == '' ? null : value
			};
			data['tabledata'] = data.search ? this.dataset.ussTabledataSearch : null;
			let search = uss.meth.setQuery(data);
			window.location.search = search;
		});
		
	});
	
	
	// -- [ get selected option in a uss tabledata ] --
	
	uss.meth.get_td_option = function( TName ) {
		let select = $(`[data-uss-simple-tabledata='${TName}'] [data-uss-tabledata-options='${TName}'] select`);
		return select.val();
	};
	
	
	// -- [ get selected rows in a tabledata ] --
	
	uss.meth.get_td_rows = function( TName, checked = false, Tr = false ) {
		let selector = `[data-uss-simple-tabledata='${TName}'] table tbody td input[type='checkbox'][data-uss-checked='td']`;
		if( checked ) selector += ':checked';
		var result = $(selector);
		if( Tr ) result.each(function(k,v) {
			result[k] = $(this).parents("tr").get(0);
		});
		return result;
	};
	
	
	// -- [ form authentication ] --
	
	$("[data-v-auth],[data-v-potty]").each(function() {
		let input = document.createElement("input");
		input.type = 'hidden';
		input.name = ( this.dataset.vAuth ? this.dataset.vAuth : this.dataset.vPotty ).trim();
		input.value = $(this).text().trim();
		this.after(input);
		this.parentElement.removeChild(this);
	});
	
	// -- [ notification ] --
	
	(function() {
		
		var badge, notix = [];
		
		$("#uss-nav-notix, #uss-main-notix").each(function() {
			notix.push(this);
			let nb = $(this).find("#notix-badge").get(0);
			if( nb ) {
				badge = new Vue({
					el: nb,
					data: { counter: Number($(nb).text().trim()) }
				});
			};
			// [ deligate ]
			$(this).click(function(e) {
				e.preventDefault();
				let el = e.target;
				let href = el.href;
				while( el && !el.hasAttribute('data-x') ) el = el.parentElement;
				if( el ) clear(el.dataset.x, [$(el).parents(".notice-item").find('input').val()]);
				if( href && href.trim() != '' && href != 'javascript:void(0)' && href != '#' ) setTimeout(function() {
					window.location.href = href;
				}, 100);
			});
		});
		
		let clear = function(type, els) {
			$.ajax({
				method: 'POST',
				url: uss.srv.root + '/ajax.php?action=ajax/notifications.php',
				data: {
					'type': type,
					'values': els
				},
				success: function(response) {
					var result = uss.meth.JSONParser(response);
					if( !result ) return;
					for(let x of els) {
						for(let el of notix) {
							$(el).find(`input[value='${x}']`).parents(".notice-item").each(function() {
								if( type == 'check' ) $(this).find("._new").removeClass("_new");
								else $(this).fadeOut('fast', function() {
									this.parentElement.removeChild(this);
								});
							});
						};
					}
					badge.counter = result.data[0];
				}
			});
		};

		$("#mark-as-read").click(function() {
			let els = [];
			$("#uss-main-notix input").each(function() {
				els.push(this.value.trim());
			});
			clear('check', els);
		});

	})();
	
	
	// -- [ dismiss alert ] --
	
	$("[data-alert-dismiss]").each(function() {
		let self = this;
		let key = self.dataset.alertDismiss;
		if( !sessionStorage.getItem(key) ) {
			$(self).removeClass("d-none").find(`[data-alert-close='${key}']`).click(function() {
				sessionStorage.setItem(key, 1);
				$(self).addClass('d-none');
			});
		}
	});
	
});