( function ( $ ) {

	if ( typeof vc === 'undefined' || typeof vc.shortcode_view === 'undefined' ) {
		return false;
	}

	/**
	 * Shortcode vcex_heading
	 */
	window.VcexHeadingView = vc.shortcode_view.extend( {
		changeShortcodeParams: function ( model ) {
			window.VcexHeadingView.__super__.changeShortcodeParams.call( this, model );
			var inverted_value;
			if ( _.isString( model.getParam( 'text' ) ) ) {
				if ( 'custom' == model.getParam( 'source' ) ) {
					if ( model.getParam( 'text' ).match(/^#E\-8_/) ) {
						this.$el.find( '.vcex-heading-text > span' ).html( '' );
					} else {
						this.$el.find( '.vcex-heading-text > span' ).html( ': ' + model.getParam( 'text' ) );
					}
				} else {
					inverted_value = _.invert( this.params.source.value );
					this.$el.find( '.vcex-heading-text > span' ).html( ': ' + inverted_value[ model.getParam( 'source' ) ] );
				}
			}
		}
	} );

	/**
	 * Shortcode vcex_image
	 */
	window.VcexImageView = vc.shortcode_view.extend( {

		changeShortcodeParams: function ( model ) {

			window.VcexImageView.__super__.changeShortcodeParams.call( this, model );

			var self = this;

			var source = _.isString( model.getParam( 'source' ) ) ? model.getParam( 'source' ) : '';
			var image_data;
			var $thumbnail;

			if ( source ) {

				if ( 'external' == source ) {
					image_data = model.getParam( 'external_image' );
				} else if ( 'custom_field' ==  source ) {
					image_data = model.getParam( 'custom_field_name' );
				} else {
					image_data = model.getParam( 'image_id' );
				}

				$.ajax( {
					type: 'POST',
					url: window.ajaxurl,
					data: {
						action: 'vcex_image_preview',
						content: image_data,
						size: 'thumbnail',
						post_id: vc_post_id,
						image_source: source,
						_vcnonce: window.vcAdminNonce
					},
					dataType: 'html',
					context: self
				} ).done( function( url ) {
					updateThumbnail( url );
				} );

			}

			function updateThumbnail( url ) {
				if ( url ) {
					$thumbnail = self.$el.find( '.vcex_wpb_image_holder' );
					if ( ! $thumbnail.length ) {
						self.$el.find( '.wpb_element_wrapper' ).append( '<p class="vcex_wpb_image_holder"></p>' );
						$thumbnail = self.$el.find( '.vcex_wpb_image_holder' );
					}
					$thumbnail.html( '<img src="' + url + '" style="max-height:150px" />' );
				} else {
					self.$el.find( '.vcex_wpb_image_holder' ).remove();
				}
			}

		}

	} );

} ) ( window.jQuery );