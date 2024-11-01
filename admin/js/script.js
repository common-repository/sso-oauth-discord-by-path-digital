function pd_update_redirect_url( el ){
		
	var url = jQuery( el ).find( 'option:selected' ).data( 'url' );
	jQuery( '#pd_redirect_url' ).val( url );
	
}

function pd_copy_to_clipboard( el ){
	
	var textbox = jQuery( el ).prev( 'input' )[0];
	textbox.select();
	textbox.setSelectionRange( 0, 99999 ); 
	var copied = textbox.value;
	
	navigator.clipboard.writeText( copied ).then( function(){
		
		jQuery( el ).addClass( 'pd-copied' );
		
	} );
	
	setTimeout( function(){
		
		jQuery( el ).removeClass( 'pd-copied' );
		
	}, 1000);
	
}

function blockUser( id, el ){
	
	var button_text = 'Unblock';
	
	jQuery.ajax( {
		type: 'POST',
		dataType: 'json',
		url: ajaxurl,
		data: { 'user': id, 'action': 'ban_user' },
		success: function( response ){
			var result = response.result;
			if( result == 'unblocked' )
				button_text = 'Block';
			
			jQuery( el ).text( button_text );
				
		}
	} );
	
}

function forceLogout( id, el ){
	
	jQuery.ajax( {
		type: 'POST',
		dataType: 'json',
		url: ajaxurl,
		data: { 'user': id, 'action': 'force_logout' },
		success: function( response ){
			var updated = response.updated;
			if( updated )
				jQuery( el ).parents( 'tr' ).remove();
				
		}
	} );
	
}