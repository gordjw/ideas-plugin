// Voting.js

jQuery( document ).ready( function() {
	jQuery(".vote-up").click( function() {
		var id = jQuery(this).attr('data-id');

		var data = {
			action: 'vote_up',
			// Post ID
			id: id,
			nonce: ajaxnonce
		};

		jQuery.post(ajaxurl, data, function(response) {
			if( response ) {
				console.log(response);

				response = jQuery.parseJSON(response);
				jQuery("#post-"+id+" .current-votes").text(response.votes);
				jQuery(".alerts").html("<div class='" + response.result + "'>" + response.message + "</div>").fadeIn(300).delay(4000).fadeOut(1000);
			}
		});
	});

	jQuery(".vote-down").click( function() {
		var id = jQuery(this).attr('data-id');

		var data = {
			action: 'vote_down',
			// Post ID
			id: id,
			nonce: ajaxnonce
		};

		jQuery.post(ajaxurl, data, function(response) {
			if( response ) {
				console.log(response);
				response = jQuery.parseJSON(response);
				jQuery("#post-"+id+" .current-votes").text(response.votes);
				jQuery(".alerts").html("<div class='" + response.result + "'>" + response.message + "</div>").fadeIn(300).delay(4000).fadeOut(1000);
			}
		});
	});
});