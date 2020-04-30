( function( $ ) {

    'use strict';

	if ( typeof wpex === 'undefined' ) {
		console.log( 'wpex is not defined' );
		return;
	}

	if ( ! $( 'div.infinite-scroll-nav' ).length ) {
		console.log( '.infinite-scroll-nav element not found' );
		return;
	}

    function wpexInfiteScroll() {

		var $container = $( '#blog-entries' );

		$container.infinitescroll( wpexInfiniteScroll, function( newElements ) {

			var $newElems = $( newElements ).css( 'opacity', 0 );

			$newElems.imagesLoaded( function() {

				if ( $container.hasClass( 'blog-masonry-grid' ) ) {
					$container.isotope( 'appended', $newElems );
					$newElems.css( 'opacity', 0 );
				}

				if ( typeof retinajs !== 'undefined' && $.isFunction( retinajs ) ) {
					retinajs();
				}

				$newElems.animate( {
					opacity: 1
				} );

				$container.trigger( 'wpexinfiniteScrollLoaded', [$newElems] );

				if ( typeof( wpex[ 'sliderPro' ] ) !== 'undefined' ) {
					wpex.sliderPro( $newElems );
				}

				if ( typeof( wpex[ 'equalHeights' ] ) !== 'undefined' ) {
					$( '.blog-equal-heights' ).wpexEqualHeights( {
						children : '.blog-entry-inner'
					} );
				}

				if ( typeof( $.fn.mediaelementplayer ) !== 'undefined' ) {
					$newElems.find( 'audio, video' ).mediaelementplayer();
				}

			} );

		} );

    }

    $( window ).on( 'load', function() {
		wpexInfiteScroll();
	} );

} ) ( jQuery );