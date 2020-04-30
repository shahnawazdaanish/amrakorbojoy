// @version 4.9.3

( function( $ ) {

    'use strict';

    $( document ).on( 'ready', function() {

    	if ( typeof wpex === 'undefined' ) {
			console.log( 'VC Reload script can not run because wpex is not defined.' );
			return;
		}

    	var $modelId, $context = '';

    	// Store model ID when events change
		parent.vc.events.on( 'shortcodes:add shortcodes:update shortcodes:clone', function( model ) {
			$modelId = model.id;
		} );

		$( window ).on( 'vc_reload', function() {

			if ( typeof( wpex.sliderPro ) !== 'undefined' ) {
				wpex.sliderPro();
			}

			if ( typeof( wpex.equalHeights ) !== 'undefined' ) {
				wpex.equalHeights();
			}

			// Re-run scripts when specific shortcodes are modified
			if ( $modelId ) {

				$context = $( '[data-model-id=' + $modelId + ']' );

				if ( typeof( wpex.parallax ) !== 'undefined' ) {
					wpex.parallax( $context );
				}

				if ( typeof( wpex.overlayHovers ) !== 'undefined' ) {
					wpex.overlayHovers();
				}

				if ( typeof( wpex.overlaysMobileSupport ) !== 'undefined' ) {
					wpex.overlaysMobileSupport();
				}

				if ( typeof( wpex.customSelects ) !== 'undefined' ) {
					wpex.customSelects( $context );
					return;
				}

				// Module dependent
				if ( $context.hasClass( 'vc_vc_wp_custommenu' ) && typeof( wpex[ 'menuWidgetAccordion' ] ) !== 'undefined' ) {
					wpex.menuWidgetAccordion( $context );
					return;
				}

			}

		} );

	} );

} ) ( jQuery );