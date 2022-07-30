"use strict";

$(function() {
	
	let __url = uss.srv.ins + "/admin/ajax.php?action=ajax/manage-users.php";
	
	let ulist = 'users_list';
	
	function __apply( ids, action, success ) {
		uss.meth.modal({
			confirm: [action[0].toUpperCase() + action.substr(1), 'Cancel'],
			content: `Sure you want to take this action?`,
		}).then(function(sure) {
			if( !sure ) return;
			uss.meth.preloader();
			$.ajax({
				url: __url,
				data: {
					uids: ids,
					action: action,
					um_auth: uss.srv.otp
				},
				method: 'POST',
				success: function(response) {
					uss.meth.preloader(0);
					var result = uss.meth.JSONParser(response);
					if( !result ) return uss.meth.modal( uss.prop.error[1] );
					uss.meth.modal( result.message ).then(function() {
						if( typeof success == 'function' ) success(result, ids, action);
					});
				}
			});
		});
	};
	
	
	//! ---- [ For table list ] ---
	
	if( $("#uss-tabledata-table-users").length ) {
		
		let success = function(result, ids, action) {
			$.each(ids, function(i, val) {
				var row = $(`#uss-tabledata-table-users tr[data-uss-tr='${val}']`);
				if( action == 'delete' ) {
					row.hide('fast', function() {
						this.parentElement.removeChild(this);
					});
				};
			});
		};
		
		$("#uss-tabledata-apply-users").click(function() {
			let option = uss.meth.get_td_option( 'users' );
			if( !option ) return uss.meth.modal("Please select an option");
			let inputs = uss.meth.get_td_rows( 'users', true );
			if( !inputs.length ) return uss.meth.modal( "No rows were selected" );
			let vals = [];
			inputs.each(function() {
				vals.push( this.value );
			});
			__apply( vals, option );
		});

		$("#uss-tabledata-table-users").click(function(e) {
			if( e.target.hasAttribute("data-utd-action") ) {
				var uid = $(e.target).parents("[data-uid]").attr('data-uid');
				__apply( [uid], e.target.dataset.utdAction );
			}
		});
	
	};
	
	
	//! -- [ For user edit ] --
	
	if( $("#user-edit-form").length ) {
		
		$("#user-edit-form").on('submit', function(e) {
			e.preventDefault();
			uss.meth.preloader();
			$.ajax({
				url: __url,
				method: "POST",
				data: $(this).serialize(),
				success: function(response) {
					uss.meth.preloader(0);
					var result = uss.meth.JSONParser(response);
					if( !result ) return uss.meth.modal( uss.prop.error[1] );
					uss.meth.modal( result.message );
				}
			});
		});
		
		$("[data-temp-user-delete]").click(function() {
			let uid = this.dataset.tempUserDelete;
			let href = this.dataset.return;
			__apply( [uid], 'delete', function(result) {
				if( result.status ) {
					uss.meth.preloader();
					window.location.href = href;
				}
			});
		});
		
	};
	
	
	//! -- [ For user add ] --
	
	if( $("#user-add-form").length ) {
		
		
		$("#user-add-form").on('submit', function(e) {
			e.preventDefault();
			uss.meth.preloader();
			$.ajax({
				url: __url,
				method: 'POST',
				data: $(this).serialize(),
				success: function( response ) {
					uss.meth.preloader(0);
					var result = uss.meth.JSONParser( response );
					if( !result ) return uss.meth.modal( error[1] );
					uss.meth.modal( result.message ).then(function() {
						if( result.status ) {
							uss.meth.preloader();
							window.location.href = ulist;
						}
					});
				}
			});
		})
		
	}
	
});