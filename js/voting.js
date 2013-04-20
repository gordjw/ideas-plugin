// Voting.js

jQuery( document ).ready( function() {
	jQuery(".vote-up").click( function() {
		var data = {
			action: 'vote_up',
			// Post ID
			id: jQuery(this).attr('data-id'),
			nonce: ajaxnonce
		};

		jQuery.post(ajaxurl, data, function(response) {
			if( response ) {
				jQuery(".current-votes").text(response);
			}
		});
	});

	jQuery(".vote-down").click( function() {
		var data = {
			action: 'vote_down',
			// Post ID
			id: jQuery(this).attr('data-id'),
			nonce: ajaxnonce
		};

		jQuery.post(ajaxurl, data, function(response) {
			if( response ) {
				jQuery(".current-votes").text(response);
			}
		});
	});
});