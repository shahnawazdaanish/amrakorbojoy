( function( $ ) {

    'use strict';

    $( document ).on( 'ready', function() {

    	var $modelId, $context = '';

    	// Store model ID when events change
		parent.vc.events.on( 'shortcodes:add shortcodes:update shortcodes:clone', function( model ) {
			$modelId = model.id;
		} );

		$( window ).on( 'vc_reload', function() {

			// These functions need to re-run on every reload
			if ( typeof( window[ 'vcexCarousels' ] ) !== 'undefined' ) {
				window.vcexCarousels();
			}
			if ( typeof( window[ 'vcexHovers' ] ) !== 'undefined' ) {
				window.vcexHovers();
			}
			if ( typeof( window[ 'vcexHovers' ] ) !== 'undefined' ) {
				window.vcexHovers();
			}
			if ( typeof( window[ 'vcexResponsiveCSS' ] ) !== 'undefined' ) {
				window.vcexResponsiveCSS();
			}
			if ( typeof( window[ 'vcexResponsiveText' ] ) !== 'undefined' ) {
				window.vcexResponsiveText();
			}
			if ( typeof( window[ 'vcexStickyNavbar' ] ) !== 'undefined' ) {
				window.vcexStickyNavbar();
			}
			if ( typeof( window[ 'vcexNavbarMobileSelect' ] ) !== 'undefined' ) {
				window.vcexNavbarMobileSelect();
			}
			if ( typeof( window[ 'vcexIsotopeGrids' ] ) !== 'undefined' ) {
				window.vcexIsotopeGrids();
			}
			if ( typeof( window[ 'vcexNavbarFilterLinks' ] ) !== 'undefined' ) {
				window.vcexNavbarFilterLinks();
			}

			// Re-run scripts when specific shortcodes are modified
			if ( $modelId ) {

				$context = $( '[data-model-id=' + $modelId + ']' );

				// Remove duplicate items
				vcexRemoveIframeDuplicates( $context );

				// Animated Text
				if ( $context.hasClass( 'vc_vcex_animated_text' ) ) {
					if ( typeof( window[ 'vcexAnimatedText' ] ) !== 'undefined' ) {
						window.vcexAnimatedText( $context );
					}
					return;
				}

				// Countdown
				if ( $context.hasClass( 'vc_vcex_countdown' ) ) {
					if ( typeof( window[ 'vcexCountDown' ] ) !== 'undefined' ) {
						window.vcexCountDown( $context );
					}
					return;
				}

				// Milestones
				if ( $context.hasClass( 'vc_vcex_milestone' ) ) {
					if ( typeof( window[ 'vcexMilestone' ] ) !== 'undefined' ) {
						window.vcexMilestone( $context );
					}
					return;
				}

				// Skillbars
				if ( $context.hasClass( 'vc_vcex_skillbar' ) ) {
					if ( typeof( window[ 'vcexSkillbar' ] ) !== 'undefined' ) {
						window.vcexSkillbar( $context );
					}
					return;
				}

				// Before/After images
				if ( $context.hasClass( 'vc_vcex_image_ba' ) ) {
					if ( typeof( window[ 'vcexBeforeAfter' ] ) !== 'undefined' ) {
						window.vcexBeforeAfter( $context );
					}
					return;
				}

			}

		} );

		// Used to remove duplicate elements
		function vcexRemoveIframeDuplicates( $context ) {
			var $this = $context;
			var $module = $this.children( ':first' );

			if ( ! $module.length ) {
				return;
			}

			// Shape dividers
			var $topShapeDivider = $module.find( '> .wpex-shape-divider-top' );
			if ( $module.hasClass( 'wpex-has-shape-divider-top' ) ) {
				$topShapeDivider.not( ':first' ).remove();
			} else if ( $topShapeDivider.length ) {
				$topShapeDivider.remove();
			}

			var $bottomShapeDivider = $module.find( '> .wpex-shape-divider-bottom' );
			if ( $module.hasClass( 'wpex-has-shape-divider-bottom' ) ) {
				$bottomShapeDivider.not( ':first' ).remove();
			} else if ( $bottomShapeDivider.length ) {
				$bottomShapeDivider.remove();
			}

			// Overlays
			var $overlays = $module.find( '> .wpex-bg-overlay-wrap' );
			if ( $module.hasClass( 'wpex-has-overlay' ) ) {
				$overlays.not( ':first' ).remove();
			} else if ( $overlays.length ) {
				$overlays.remove();
			}

			// Self-hosted Videos
			var $videos = $module.find( '> .wpex-video-bg-wrap' );
			if ( $module.hasClass( 'wpex-has-video-bg' ) ) {
				$videos.not( ':first' ).remove();
			} else if ( $videos.length ) {
				$videos.remove();
			}

			// Parallax
			var $parallax = $module.find( '> .wpex-parallax-bg' );
			if ( $module.hasClass( 'wpex-parallax-bg-wrap' ) ) {
				$parallax.not( ':first' ).remove();
			} else if ( $parallax.length ) {
				$parallax.remove();
			}

			// Video Backgrounds
			// Deprecated? @todo Remove & test
			var $videoOverlays = $module.find( '> .wpex-video-bg-overlay' );
			if ( $videoOverlays.length ) {
				$videoOverlays.not( ':first' ).remove();
			}
		}

	} );

} ) ( jQuery );