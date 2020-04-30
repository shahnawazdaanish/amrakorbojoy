( function( $ ) {

    'use strict';

    function wpexLoadMore() {

        if ( typeof wpex === 'undefined' ) {
            console.log( 'Load More script can not run because wpex is not defined.' );
            return;
        }

        var $loadMore = $( '.wpex-load-more' );

        if ( ! $loadMore.length ) {
            return;
        }

        $loadMore.each( function() {

            var $button      = $( this );
            var $wrap        = $( this ).parent( '.wpex-load-more-wrap' );
            var $buttonInner = $button.find( '.theme-button-inner' );
            var loading      = false;
            var text         = wpexLocalize.loadMore.text;
            var ajaxUrl      = wpexLocalize.ajaxurl;
            var loadingText  = wpexLocalize.loadMore.loadingText;
            var failedText   = wpexLocalize.loadMore.failedText;
            var buttonData   = $button.data( 'loadmore' );
            var $grid        = $( buttonData.grid );
            var page         = 2;
            var isMasonry    = false;

            if ( 1 != buttonData.maxPages ) {
                $button.addClass( 'wpex-visible' );
            }

            var loadmoreData = buttonData;

            $wrap.css( 'min-height', $wrap.outerHeight() ); // prevent jump when showing loader icon

            if ( $grid.hasClass( 'blog-masonry-grid' ) ) {
                isMasonry = true;
            }

            $button.on( 'click', function() {

                if ( ! loading ) {

                    loading = true;

                    $wrap.addClass( 'wpex-loading' );
                    $buttonInner.text( loadingText );

                    var data = {
                        action   : 'wpex_ajax_load_more',
                        nonce    : buttonData.nonce,
                        page     : page,
                        loadmore : loadmoreData
                    };

                    $.post( ajaxUrl, data, function( res ) {

                        // Ajax request successful
                        if ( res.success ) {

                            //console.log( res.data );

                            // Increase page
                            page = page + 1;

                            // Define vars
                            var $newElements = $( res.data );
                            $newElements.css( 'opacity', 0 ); // hide until images are loaded

                            // Tweak new items
                            $newElements.each( function() {
                                var $this = $( this );

                                // Hide duplicate/sticky items
                                if ( $this.hasClass( 'sticky' ) ) {
                                    $this.addClass( 'wpex-duplicate' );
                                }

                                // Make sure isotope-entry class is added
                                if ( isMasonry ) {
                                    $this.addClass( 'isotope-entry' );
                                }

                            } );

                            $grid.append( $newElements ).imagesLoaded( function() {

                                if ( typeof retinajs !== 'undefined' && $.isFunction( retinajs ) ) {
                                    retinajs();
                                }

                                if ( typeof( wpex[ 'equalHeights' ] ) !== 'undefined' ) {
                                    wpex.equalHeights();
                                }

                                if ( isMasonry ) {
                                    //$grid.isotope().append( $newElements ).isotope( 'appended', $newElements ).isotope( 'layout' );
                                    //$grid.isotope().append( $newElements ).isotope( 'appended', $newElements );
                                    $grid.isotope( 'appended', $newElements );
                                }

                                if ( typeof( wpex[ 'overlayHovers' ] ) !== 'undefined' ) {
                                    wpex.overlayHovers();
                                }

                                if ( typeof( wpex[ 'overlaysMobileSupport' ] ) !== 'undefined' ) {
                                    wpex.overlaysMobileSupport();
                                }

                                $grid.trigger( 'wpexLoadMoreAddedHidden', [$newElements] );

                                $newElements.css( 'opacity', 1 );

                                if ( typeof( wpex[ 'sliderPro' ] ) !== 'undefined' ) {
                                    wpex.sliderPro( $newElements );
                                }

                                if ( typeof( window[ 'vcexHovers' ] ) !== 'undefined' ) {
                                    window.vcexHovers();
                                }

                                if ( typeof( $.fn.mediaelementplayer ) !== 'undefined' ) {
                                    $newElements.find( 'audio, video' ).mediaelementplayer();
                                }

                                $grid.trigger( 'wpexLoadMoreAddedVisible', [$newElements] );

                                // Reset button
                                $wrap.removeClass( 'wpex-loading' );
                                $buttonInner.text( text );

                                // Hide button
                                if ( ( page - 1 ) == buttonData.maxPages ) {
                                    $button.hide();
                                }

                                // Set loading to false
                                loading = false;

                            } ); // End images loaded

                        } // End success

                        else {

                            $buttonInner.text( failedText );

                            console.log( res );

                        }

                    } ).fail( function( xhr, textGridster, e ) {

                        console.log( xhr.responseText );

                    } );

                } // end loading check

                return false;

            } ); // End click

        } ); // End each

    }

    $( window ).on( 'load', function() {
        wpexLoadMore();
    } );

} ) ( jQuery );