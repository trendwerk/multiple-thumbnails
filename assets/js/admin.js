jQuery( function( $ ) {
	var TP_Multiple_Thumbnails;
	var TP_Selected_Thumbnail;

	$('.tp-multiple-thumbnails-thumbnail').on( 'click', '#set-post-thumbnail', function( event ) {
		event.preventDefault();
		event.stopPropagation();

		TP_Selected_Thumbnail = this;

		if( typeof( TP_Multiple_Thumbnails ) !== "undefined" )
			TP_Multiple_Thumbnails.close();

		TP_Multiple_Thumbnails = wp.media( {
			states: [
				new wp.media.controller.Library({
					title:       $( TP_Selected_Thumbnail ).closest( '.tp-multiple-thumbnails-thumbnail' ).data( 'name' ),
					library:     wp.media.query( {
						type:    'image'
					} ),
					filterable: 'uploaded',
					priority:    20
				})
			]
		} );

		TP_Multiple_Thumbnails.on( 'select', function() {
			var attachment = TP_Multiple_Thumbnails.state().get( 'selection' ).first().toJSON();
			tp_set_thumbnail( attachment.id );
		} );

		TP_Multiple_Thumbnails.open();

		return false;
	} ).on( 'click', '#remove-post-thumbnail', function( event ) {
		event.preventDefault();

		TP_Selected_Thumbnail = this;
		tp_set_thumbnail( -1 );
	} );

	function tp_set_thumbnail( thumbnail_id ) {
		var data = {
			action:       'tp_multiple_thumbnails_set',
			thumbnail_id: thumbnail_id,
			thumbnail:    $( TP_Selected_Thumbnail ).closest( '.tp-multiple-thumbnails-thumbnail' ).data( 'thumbnail' ),
			post_id:      $( 'input#post_ID' ).val()
		};

		$.post( ajaxurl, data, function( response ) {
			$( TP_Selected_Thumbnail ).closest( '.tp-multiple-thumbnails-thumbnail' ).html( response );
			TP_Selected_Thumbnail = null;
		} );
	}
} );