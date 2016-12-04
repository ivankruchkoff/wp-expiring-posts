/* globals AdminExpiringPosts */

jQuery(document).ready( function($) {

	// exp-timestamp is not on this page, don't run script
	if ( ! document.getElementById( 'exp-timestamp' ) ) {
		return;
	}

	//append Expired to the post status editor
	$( '#post_status' )
		.append( $('<option>')
					.val(AdminExpiringPosts.expired_status)
					.text(AdminExpiringPosts.expired_text)
		).val( AdminExpiringPosts.post_status );

	// Set button text to "Update" instead of "Publish" when post is expired
	if ( 'expired' == AdminExpiringPosts.post_status ) {
		$( '#post-status-display' ).text( AdminExpiringPosts.expired_text );

		$( '#save-post' )
			.val(AdminExpiringPosts.save_text)
			.on('click', setTimeout( function() {
				saveButton.value = AdminExpiringPosts.save_text;
				}, 10 )
			);
	}

	var $timestamp = $( '#exp-timestamp' ),
		$editTimestamp = $( '#exp-edit-timestamp' ),
		$expDisable = $('#expiry-disable' ),
		expEnableValue = $expDisable.is(':checked');

	$timestamp.find('[name^=expiry]').each(function(){
		$(this).attr('data-initial-value', $(this).val());
	} );

	// Cancel Button
	$('.exp-cancel-timestamp' ).on( 'click', expCancelTimestamp );

	// Save Button
	$( '.exp-save-timestamp' ).on( 'click', expSaveTimestamp );

	// show / hide time adjustor and Edit button
	$editTimestamp.on( 'click', openEditExpirationDate );

	function expCancelTimestamp() {
		$timestamp.find('[name^=expiry]').each(function(){
			$(this).val($(this).attr('data-initial-value'));
		});

		$expDisable.attr( 'checked', expEnableValue );
		expSaveTimestamp();
	}

	function expSaveTimestamp() {console.log($expDisable);
		if ( $expDisable.is( ':checked' ) ) {
			$timestamp
				.empty()
				.append(AdminExpiringPosts.expires_text)
				.append($('<b />').text('never'));

			return closeEditExpirationDate();
		}

		var year = $('#expiry_year').val(),
			month = $('#expiry_month').val(),
			day = $('#expiry_day').val(),
			hour = $('#expiry_hour').val(),
			minute = $('#expiry_minute').val(),
			newDate = new Date( year, month - 1, day, hour, minute );

		if(!newDate) {
			$('#exp-timestampdiv').addClass('form-invalid');
		} else {
			$('#exp-timestampdiv').removeClass('form-invalid');
		}

		closeEditExpirationDate();

		$timestamp
			.empty().append(
				AdminExpiringPosts.expires_text
			).append(
				$('<b />').text($( '#expiry_month option:selected' ).attr('data-short') + ' ' +
						year + ', ' +
						day + ' @ ' +
						hour + ':' +
						minute
				)
			);

		return false;
	}

	function closeEditExpirationDate() {
		$( '#exp-timestampdiv' ).slideUp( "normal" );
		$editTimestamp.show();
	}

	function openEditExpirationDate() {
		$( '#exp-timestampdiv' ).slideDown( "normal" );
		$editTimestamp.hide();
		return false;
	}

});