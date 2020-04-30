/**
 * Project: Total WordPress Theme
 * Description: Initialize all scripts and add custom js
 * Author: WPExplorer
 * Theme URI: http://www.wpexplorer.com
 * Author URI: http://www.wpexplorer.com
 * License: Custom
 * License URI: http://themeforest.net/licenses
 * Version 4.9.8
 */

var wpex = {};

( function( $ ) {

	'use strict';

	wpex = {

		/**
		 * Main init function.
		 */
		init : function() {
			this.config();
			this.bindEvents();
		},

		/**
		 * Define vars for caching.
		 */
		config : function() {

			this.config = {

				// General
				$window                 : $( window ),
				$document               : $( document ),
				$head                   : $( 'head' ),
				$body                   : $( 'body' ),
				windowWidth             : $( window ).width(),
				windowHeight            : $( window ).height(),
				windowTop               : $( window ).scrollTop(),
				viewportWidth           : '',
				isRetina                : false,
				heightChanged           : false,
				widthChanged            : false,
				isRTL                   : false,

				// VC
				vcActive                : false,

				// Mobile
				isMobile                : false,
				mobileMenuStyle         : null,
				mobileMenuToggleStyle   : null,
				mobileMenuBreakpoint    : 960,

				// Main Divs
				$siteWrap               : null,
				$siteMain               : null,

				// Header
				$siteHeader             : null,
				siteHeaderStyle         : null,
				siteHeaderHeight        : 0,
				siteHeaderTop           : 0,
				siteHeaderBottom        : 0,
				verticalHeaderActive    : false,
				hasHeaderOverlay        : false,
				hasStickyHeader         : false,
				stickyHeaderStyle       : null,
				hasStickyMobileHeader   : false,
				hasStickyNavbar         : false,

				// Logo
				$siteLogo               : null,
				siteLogoHeight          : 0,
				siteLogoSrc             : null,

				// Nav
				$siteNavWrap            : null,
				$siteNav                : null,
				$siteNavDropdowns       : null,

				// Local Scroll
				$localScrollTargets     : 'li.local-scroll a, a.local-scroll, .local-scroll-link, .local-scroll-link > a',
				localScrollOffset       : 0,
				localScrollSpeed        : 600,
				localScrollEasing       : 'easeInOutCubic',
				localScrollSections     : [],

				// Topbar
				hasTopBar               : false,
				hasStickyTopBar         : false,
				$stickyTopBar           : null,
				hasStickyTopBarMobile   : false,

				// Footer
				hasFixedFooter          : false

			};

		},

		/**
		 * Bind Events.
		 */
		bindEvents : function() {
			var self = this;

			/*** Run on Document Ready ***/
			self.config.$document.on( 'ready', function() {
				self.initUpdateConfig();
				self.superfish();
				self.mobileMenu();
				self.navNoClick();
				self.hideEditLink();
				self.menuWidgetAccordion();
				self.inlineHeaderLogo(); // Header 5 logo
				self.menuSearch();
				self.headerCart();
				self.backTopLink();
				self.smoothCommentScroll();
				self.toggleBar();
				self.localScrollLinks();
				self.customSelects();
				self.lightbox();
				self.equalHeights();
				self.archiveMasonryGrids();
				self.overlaysMobileSupport();
				self.ctf7Preloader();
				self.vcAccessability();
			} );

			/*** Run on Window Load ***/
			self.config.$window.on( 'load', function() {

				// Add window loaded css tag to body
				self.config.$body.addClass( 'wpex-window-loaded' );

				// Run methods.
				self.windowLoadUpdateConfig();
				self.megaMenusWidth();
				self.megaMenusTop();
				self.flushDropdownsTop();
				self.sliderPro();
				self.parallax();
				self.stickyTopBar();
				self.vcTabsTogglesJS();
				self.overlayHovers();
				self.headerOverlayOffset(); // Add before sticky header ( important )

				// Sticky Header
				if ( self.config.hasStickyHeader ) {
					self.stickyHeaderStyle = wpexLocalize.stickyHeaderStyle;
					if ( 'standard' == self.stickyHeaderStyle || 'shrink' == self.stickyHeaderStyle || 'shrink_animated' == self.stickyHeaderStyle ) {
						self.stickyHeader();
					}
				}

				// Run methods after sticky header
				self.stickyHeaderMenu();
				self.footerReveal();  // Footer Reveal => Must run before fixed footer!!!
				self.fixedFooter();
				self.titleBreadcrumbsFix();

				// Set localScrollOffset after site is loaded to make sure it includes dynamic items including sticky elements
				self.config.localScrollOffset = self.parseLocalScrollOffset( 'init' );

				// Scroll to hash (must be last)
				if ( wpexLocalize.scrollToHash ) {
					window.setTimeout( function() {
						self.scrollToHash( self );
					}, parseInt( wpexLocalize.scrollToHashTimeout ) );
				}

			} );

			/*** Run on Window Resize ***/
			self.config.$window.resize( function() {

				// Reset
				self.config.widthChanged  = false;
				self.config.heightChanged = false;

				// Window width change
				if ( self.config.$window.width() != self.config.windowWidth ) {
					self.config.widthChanged = true;
					self.widthResizeUpdateConfig();
				}

				// Height changes
				if ( self.config.$window.height() != self.config.windowHeight ) {
					self.config.windowHeight  = self.config.$window.height(); // update height
					self.config.heightChanged = true;
				}

			} );

			/*** Run on Window Scroll ***/
			self.config.$window.scroll( function() {

				// Reset
				self.config.$hasScrolled = false;

				// Yes we actually scrolled
				if ( self.config.$window.scrollTop() != self.config.windowTop ) {
					self.config.$hasScrolled = true;
					self.config.windowTop = self.config.$window.scrollTop();
					self.localScrollHighlight();
				}

			} );

			/*** Run on Orientation Change ***/
			self.config.$window.on( 'orientationchange', function() {
				self.widthResizeUpdateConfig();
				self.archiveMasonryGrids();
			} );

		},

		/**
		 * Updates config on doc ready.
		 */
		initUpdateConfig: function() {
			var self = this;

			self.config.$body.addClass( 'wpex-docready' );

			// Check if VC is enabled
			self.config.vcActive = this.config.$body.hasClass( 'wpb-js-composer' );

			// Get Viewport width
			self.config.viewportWidth = self.viewportWidth();

			// Check if retina
			self.config.isRetina = self.retinaCheck();
			if ( self.config.isRetina ) {
				self.config.$body.addClass( 'wpex-is-retina' );
			}

			// Mobile check & add mobile class to the header
			if ( self.mobileCheck() ) {
				self.config.isMobile = true;
				self.config.$body.addClass( 'wpex-is-mobile-device' );
			}

			// Define wrap
			var $siteWrap = $( '#wrap' );
			if ( $siteWrap ) {
				self.config.$siteWrap = $siteWrap;
			}

			// Define main
			var $siteMain = $( '#main' );
			if ( $siteMain ) {
				self.config.$siteMain = $siteMain;
			}

			// Define header
			var $siteHeader = $( '#site-header' );
			if ( $siteHeader.length ) {
				self.config.siteHeaderStyle = wpexLocalize.siteHeaderStyle;
				self.config.$siteHeader = $( '#site-header' );
			}

			// Define logo
			var $siteLogo = $( '#site-logo img.logo-img' );
			if ( $siteLogo.length ) {
				self.config.$siteLogo = $siteLogo;
				self.config.siteLogoSrc = self.config.$siteLogo.attr( 'src' );
			}

			// Menu Stuff
			var $siteNavWrap = $( '#site-navigation-wrap' );
			if ( $siteNavWrap.length ) {

				// Define menu
				self.config.$siteNavWrap = $siteNavWrap;
				var $siteNav = $( '#site-navigation', $siteNavWrap );
				if ( $siteNav.length ) {
					self.config.$siteNav = $siteNav;
				}

				// Check if sticky menu is enabled
				if ( wpexLocalize.hasStickyNavbar ) {
					self.config.hasStickyNavbar = true;
				}

				// Store dropdowns
				var $siteNavDropdowns = $( '.dropdown-menu > .menu-item-has-children > ul', $siteNavWrap );
				if ( $siteNavWrap.length ) {
					self.config.$siteNavDropdowns = $siteNavDropdowns;
				}

			}

			// Mobile menu settings
			if ( wpexLocalize.hasMobileMenu ) {
				self.config.mobileMenuStyle       = wpexLocalize.mobileMenuStyle;
				self.config.mobileMenuToggleStyle = wpexLocalize.mobileMenuToggleStyle;
				self.config.mobileMenuBreakpoint  = wpexLocalize.mobileMenuBreakpoint;
			}

			// Check if fixed footer is enabled
			if ( self.config.$body.hasClass( 'wpex-has-fixed-footer' ) ) {
				self.config.hasFixedFooter = true;
			}

			// Footer reveal
			self.config.$footerReveal = $( '.footer-reveal-visible' );
			if ( self.config.$footerReveal.length && self.config.$siteWrap && self.config.$siteMain ) {
				self.config.$hasFooterReveal = true;
			}

			// Header overlay
			if ( self.config.$siteHeader && self.config.$body.hasClass( 'has-overlay-header' ) ) {
				self.config.hasHeaderOverlay = true;
			}

			// Top bar enabled
			var $topBarWrap =  $( '#top-bar-wrap' );
			if ( $topBarWrap.length ) {
				self.config.hasTopBar = true;
				if ( $topBarWrap.hasClass( 'wpex-top-bar-sticky' ) ) {
					self.config.$stickyTopBar = $topBarWrap;
					if ( self.config.$stickyTopBar.length ) {
						self.config.hasStickyTopBar = true;
						self.config.hasStickyTopBarMobile = wpexLocalize.hasStickyTopBarMobile;
					}
				}
			}

			// Sticky Header => Mobile Check (must check first)
			self.config.hasStickyMobileHeader = wpexLocalize.hasStickyMobileHeader;

			// Check if sticky header is enabled
			if ( self.config.$siteHeader && wpexLocalize.hasStickyHeader ) {
				self.config.hasStickyHeader = true;
			}

			// Vertical header
			if ( this.config.$body.hasClass( 'wpex-has-vertical-header' ) ) {
				self.config.verticalHeaderActive = true;
			}

			// Local scroll speed
			if ( wpexLocalize.localScrollSpeed ) {
				self.config.localScrollSpeed = parseInt( wpexLocalize.localScrollSpeed );
			}

			// Local scroll easing
			if ( wpexLocalize.localScrollEasing ) {
				self.config.localScrollEasing = wpexLocalize.localScrollEasing;
				if ( 'false' == self.config.localScrollEasing ) {
					self.config.localScrollEasing = 'swing';
				}
			}

			// Get local scrolling sections
			self.config.localScrollSections = self.localScrollSections();

		},

		/**
		 * Updates config on window load.
		 */
		windowLoadUpdateConfig: function() {

			this.config.windowHeight = this.config.$window.height();

			if ( this.config.$siteHeader ) {
				var siteHeaderTop = this.config.$siteHeader.offset().top;
				this.config.siteHeaderHeight = this.config.$siteHeader.outerHeight();
				this.config.siteHeaderBottom = siteHeaderTop + this.config.siteHeaderHeight;
				this.config.siteHeaderTop = siteHeaderTop;
				if ( this.config.$siteLogo ) {
					this.config.siteLogoHeight = this.config.$siteLogo.height();
				}
			}

		},

		/**
		 * Updates config whenever the window is resized.
		 */
		widthResizeUpdateConfig: function() {

			// Update main configs
			this.config.windowHeight  = this.config.$window.height();
			this.config.windowWidth   = this.config.$window.width();
			this.config.windowTop     = this.config.$window.scrollTop();
			this.config.viewportWidth = this.viewportWidth();

			// Update header height
			if ( this.config.$siteHeader ) {
				this.config.siteHeaderHeight = this.config.$siteHeader.outerHeight();
			}

			// Get logo height
			if ( this.config.$siteLogo ) {
				this.config.siteLogoHeight = this.config.$siteLogo.height();
			}

			// Vertical Header
			if ( this.config.windowWidth < 960 ) {
				this.config.verticalHeaderActive = false;
			} else if ( this.config.$body.hasClass( 'wpex-has-vertical-header' ) ) {
				this.config.verticalHeaderActive = true;
			}

			// Local scroll offset => update last
			this.config.localScrollOffset = this.parseLocalScrollOffset( 'resize' );

			// Re-run functions
			this.megaMenusWidth();
			this.overlayHovers();

		},

		/**
		 * Retina Check.
		 */
		retinaCheck: function() {
			var mediaQuery = '(-webkit-min-device-pixel-ratio: 1.5), (min--moz-device-pixel-ratio: 1.5), (-o-min-device-pixel-ratio: 3/2), (min-resolution: 1.5dppx)';
			if ( window.devicePixelRatio > 1 ) {
				return true;
			}
			if ( window.matchMedia && window.matchMedia( mediaQuery ).matches ) {
				return true;
			}
			return false;
		},

		/**
		 * Mobile Check.
		 */
		mobileCheck: function() {
			if ( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test( navigator.userAgent ) ) {
				return true;
			}
		},

		/**
		 * Viewport width.
		 */
		viewportWidth: function() {
			var e = window, a = 'inner';
			if ( ! ( 'innerWidth' in window ) ) {
				a = 'client';
				e = document.documentElement || document.body;
			}
			return e[ a+'Width' ];
		},

		/**
		 * Superfish menus.
		 */
		superfish: function() {

			if ( ! this.config.$siteNav || 'undefined' === typeof $.fn.superfish ) {
				return;
			}

			$( 'ul.sf-menu', this.config.$siteNav ).superfish( {
				delay       : wpexLocalize.superfishDelay,
				speed       : wpexLocalize.superfishSpeed,
				speedOut    : wpexLocalize.superfishSpeedOut,
				cssArrows   : false,
				disableHI   : false,
				animation   : {
					opacity : 'show'
				},
				animationOut : {
					opacity  : 'hide'
				}
			} );

		},

		 /**
		 * MegaMenus Width.
		 */
		megaMenusWidth: function() {

			if ( ! wpexLocalize.megaMenuJS || 'one' != this.config.siteHeaderStyle || ! this.config.$siteNavDropdowns || ! this.config.$siteNavWrap.is( ':visible' ) ) {
				return;
			}

			// Define megamenu
			var $megamenu = $( '.megamenu > ul', this.config.$siteNavWrap );

			// Don't do anything if there isn't any megamenu
			if ( ! $megamenu.length ) {
				return;
			}

			var $headerContainerWidth       = this.config.$siteHeader.find( '.container' ).outerWidth(),
				$navWrapWidth               = this.config.$siteNavWrap.outerWidth(),
				$siteNavigationWrapPosition = parseInt( this.config.$siteNavWrap.css( 'right' ) );

			if ( 'auto' == $siteNavigationWrapPosition ) {
				$siteNavigationWrapPosition = 0;
			}

			var $megaMenuNegativeMargin = $headerContainerWidth-$navWrapWidth-$siteNavigationWrapPosition;

			$megamenu.css( {
				'width'       : $headerContainerWidth,
				'margin-left' : -$megaMenuNegativeMargin
			} );

		},

		/**
		 * MegaMenus Top Position.
		 */
		megaMenusTop: function() {
			var self = this;
			if ( ! self.config.$siteNavDropdowns || 'one' != self.config.siteHeaderStyle ) {
				return;
			}

			var $megamenu = $( '.megamenu > ul', self.config.$siteNavWrap );

			if ( ! $megamenu.length ) {
				return;
			}

			function setPosition() {
				if ( self.config.$siteNavWrap.is( ':visible' ) ) {
					var $headerHeight = self.config.$siteHeader.outerHeight();
					var $navHeight    = self.config.$siteNavWrap.outerHeight();
					var $megaMenuTop  = $headerHeight - $navHeight;
					$megamenu.css( {
						'top' : $megaMenuTop/2 + $navHeight
					} );
				}
			}
			setPosition();

			// update on scroll
			this.config.$window.scroll( function() {
				setPosition();
			} );

			// Update on resize
			this.config.$window.resize( function() {
				setPosition();
			} );

			// Update on hover just incase
			$( '.megamenu > a', self.config.$siteNav ).hover( function() {
				setPosition();
			} );

		},

		/**
		 * FlushDropdowns top positioning.
		 */
		flushDropdownsTop: function() {
			var self = this;
			if ( ! self.config.$siteNavDropdowns || ! self.config.$siteNavWrap.hasClass( 'wpex-flush-dropdowns' ) ) {
				return;
			}

			// Set position
			function setPosition() {
				if ( self.config.$siteNavWrap.is( ':visible' ) ) {
					var $headerHeight      = self.config.$siteHeader.outerHeight();
					var $siteNavWrapHeight = self.config.$siteNavWrap.outerHeight();
					var $dropTop           = $headerHeight - $siteNavWrapHeight;
					self.config.$siteNavDropdowns.css( 'top', $dropTop/2 + $siteNavWrapHeight );
				}
			}
			setPosition();

			// Update on scroll
			this.config.$window.scroll( function() {
				setPosition();
			} );

			// Update on resize
			this.config.$window.resize( function() {
				setPosition();
			} );

			// Update on hover
			$( '.wpex-flush-dropdowns li.menu-item-has-children > a' ).hover( function() {
				setPosition();
			} );

		},

		/**
		 * Mobile Menu.
		 */
		mobileMenu: function() {
			if ( 'sidr' == this.config.mobileMenuStyle && typeof wpexLocalize.sidrSource !== 'undefined' ) {
				this.mobileMenuSidr();
			} else if ( 'toggle' == this.config.mobileMenuStyle ) {
				this.mobileMenuToggle();
			} else if ( 'full_screen' == this.config.mobileMenuStyle ) {
				this.mobileMenuFullScreen();
			}
		},

		/**
		 * Mobile Menu.
		 */
		mobileMenuSidr: function() {

			var self       = this,
				$toggleBtn = $( 'a.mobile-menu-toggle, li.mobile-menu-toggle > a' ),
				$mobileAlt = $( '#mobile-menu-alternative' ),
				$sidrMain,
				$sidrOverlay,
				$sidrMenu,
				$sidrClosebtn;

			// Add dark overlay to content
			self.config.$body.append( '<div class="wpex-sidr-overlay wpex-hidden"></div>' );
			$sidrOverlay = $( '.wpex-sidr-overlay' );

			// Add active class to toggle button
			$toggleBtn.click( function() {
				$( this ).toggleClass( 'wpex-active' );
			} );

			// Add sidr
			$toggleBtn.sidr( {
				name     : 'sidr-main',
				source   : wpexLocalize.sidrSource,
				side     : wpexLocalize.sidrSide,
				displace : wpexLocalize.sidrDisplace,
				speed    : parseInt( wpexLocalize.sidrSpeed ),
				renaming : true,
				bind     : 'click',
				onOpen: function() {

					// Change aria labels
					$toggleBtn.attr( 'aria-expanded', 'true' );
					$sidrClosebtn.attr( 'aria-expanded', 'true' );

					// Add extra classname
					$( '#sidr-main' ).addClass( 'wpex-mobile-menu' );

					// Prevent body scroll
					if ( wpexLocalize.sidrBodyNoScroll ) {
						self.config.$body.addClass( 'wpex-noscroll' );
					}

					// FadeIn Overlay
					$sidrOverlay.removeClass( 'wpex-hidden' );
					$sidrOverlay.addClass( 'wpex-custom-cursor' );

					// Swet focus styles
					self.focusOnElement( $( '#sidr-main' ) );

				},
				onClose: function() {

					// Alter aria labels
					$toggleBtn.attr( 'aria-expanded', 'false' );
					$sidrClosebtn.attr( 'aria-expanded', 'false' );

					// Remove active class
					$toggleBtn.removeClass( 'wpex-active' );

					// Remove body noscroll class
					if ( wpexLocalize.sidrBodyNoScroll ) {
						self.config.$body.removeClass( 'wpex-noscroll' );
					}

				},
				onCloseEnd: function() {

					// Remove active dropdowns
					$( '.sidr-class-menu-item-has-children.active' ).removeClass( 'active' ).find( 'ul' ).hide();

					// Re-trigger stretched rows to prevent issues if browser was resized while
					// sidr was open
					if ( 'undefined' !== typeof (window.vc_rowBehaviour) ) {
						window.vc_rowBehaviour();
					}

					// FadeOut overlay
					$sidrOverlay.removeClass( 'wpex-custom-cursor' ).addClass( 'wpex-hidden' );

				}

			} );

			// Cache sidebar elements
			$sidrMain     = $( '#sidr-main' );
			$sidrClosebtn = $( '.sidr-class-wpex-close > a', $sidrMain );

			// Make sure dropdown-menu is included in sidr-main which may not be included in certain header styles like dev header style
			$sidrMain.find( '.sidr-class-main-navigation-ul' ).addClass( 'sidr-class-dropdown-menu' );

			// Sidr dropdown toggles
			$sidrMenu = $( '.sidr-class-dropdown-menu', $sidrMain );

			// Create menuAccordion
			self.menuAccordion( $sidrMenu );

			// Insert mobile menu extras
			self.insertExtras( $( '.wpex-mobile-menu-top' ), $( '.sidr-inner', $sidrMain ), 'prepend' );
			self.insertExtras( $( '.wpex-mobile-menu-bottom' ), $( '.sidr-inner', $sidrMain ), 'append' );

			// Re-name font Icons to correct classnames
			// @todo can we optimize this? Maybe instead of renaming have list of classes to exclude from prefix in sidr.js
			$( "[class*='sidr-class-fa']", $sidrMain ).attr( 'class', function( i, c ) {
				c = c.replace( 'sidr-class-fa', 'fa' );
				c = c.replace( 'sidr-class-fa-', 'fa-' );
				return c;
			} );
			$( "[class*='sidr-class-ticon']", $sidrMain ).attr( 'class', function( i, c ) {
				c = c.replace( 'sidr-class-ticon', 'ticon' );
				c = c.replace( 'sidr-class-ticon-', 'ticon-' );
				return c;
			} );

			// Close sidr when clicking toggle
			$sidrClosebtn.on( 'click', function() {
				$.sidr( 'close', 'sidr-main' );
				$toggleBtn.focus();
				return false;
			} );

			// Close on resize past mobile menu breakpoint
			self.config.$window.resize( function() {
				if ( self.config.viewportWidth >= self.config.mobileMenuBreakpoint ) {
					$.sidr( 'close', 'sidr-main' );
				}
			} );

			// Close sidr when clicking local scroll link
			$( 'li.sidr-class-local-scroll > a', $sidrMain ).click( function() {
				var $hash = this.hash;
				if ( $.inArray( $hash, self.config.localScrollSections ) > -1 ) {
					$.sidr( 'close', 'sidr-main' );
					self.scrollTo( $hash );
					return false;
				}
			} );

			// Close sidr when clicking on overlay
			$sidrOverlay.on( 'click', function() {
				$.sidr( 'close', 'sidr-main' );
				if ( 'undefined' !== typeof (window.vc_rowBehaviour) ) {
					window.vc_rowBehaviour(); // fixes bug with clicking overlay...@todo revise/remove
				}
				return false;
			} );

			// Close when clicking esc
			$sidrMain.keydown( function( e ) {
				if ( e.keyCode === 27 ) {
					$.sidr( 'close', 'sidr-main' );
					$toggleBtn.focus();
				}
			} );

			// Remove mobile menu alternative if on page to prevent duplicate links
			if ( $mobileAlt.length ) {
				$mobileAlt.remove();
			}

		},

		/**
		 * Toggle Mobile Menu.
		 */
		mobileMenuToggle: function() {

			var self = this,
				$position = wpexLocalize.mobileToggleMenuPosition,
				$classes = 'mobile-toggle-nav wpex-mobile-menu wpex-clr wpex-togglep-'+ $position,
				$mobileAlt = $( '#mobile-menu-alternative' ),
				$mobileSearch = $( '#mobile-menu-search' ),
				$appendTo = self.config.$siteHeader,
				$toggleBtn = $( 'a.mobile-menu-toggle, li.mobile-menu-toggle > a' ),
				$mobileMenuContents,
				$mobileToggleNav;

			// Insert nav in fixed_top mobile menu
			if ( 'fixed_top' == self.config.mobileMenuToggleStyle ) {
				$appendTo = $( '#wpex-mobile-menu-fixed-top' );
				if ( $appendTo.length ) {
					$appendTo.append( '<nav class="'+ $classes +'" aria-label="Mobile menu"></nav>' );
				}
			}

			// Absolute position
			else if ( 'absolute' == $position ) {
				if ( 'navbar' == self.config.mobileMenuToggleStyle ) {
					$appendTo = $( '#wpex-mobile-menu-navbar' );
					if ( $appendTo.length ) {
						$appendTo.append( '<nav class="'+ $classes +'" aria-label="Mobile menu"></nav>' );
					}
				} else if ( $appendTo ) {
					$appendTo.append( '<nav class="'+ $classes +'" aria-label="Mobile menu"></nav>' );
				}
			}

			// Insert afterSelf
			else if ( 'afterself' == $position ) {
				$appendTo = $( '#wpex-mobile-menu-navbar' );
				$( '<nav class="'+ $classes +'" aria-label="Mobile menu"></nav>' ).insertAfter( $appendTo );
			}
			// Normal toggle insert (static)
			else {
				$( '<nav class="'+ $classes +'" aria-label="Mobile menu"></nav>' ).insertAfter( $appendTo );
			}

			// Store Nav in cache
			$mobileToggleNav = $( '.mobile-toggle-nav' );

			// Grab all content from menu and add into mobile-toggle-nav element
			if ( $mobileAlt.length ) {
				$mobileMenuContents = $( '.dropdown-menu', $mobileAlt ).html();
				$mobileAlt.remove();
			} else {
				$mobileMenuContents = $( '.main-navigation-ul', self.config.$siteNav ).html();
			}
			$mobileToggleNav.html( '<div class="mobile-toggle-nav-inner container"><ul class="mobile-toggle-nav-ul">' + $mobileMenuContents + '</ul></div>' );

			// Remove all styles
			$( '.mobile-toggle-nav-ul, .mobile-toggle-nav-ul *' ).children().each( function() {
				$( this ).removeAttr( 'style' );
			} );

			// Remove ID's for accessibility reasons
			$( '.mobile-toggle-nav-ul, .mobile-toggle-nav-ul *' ).removeAttr( 'id' );

			// Add search to toggle menu
			if ( $mobileSearch.length ) {
				$( '.mobile-toggle-nav-inner', $mobileToggleNav ).append( '<div class="mobile-toggle-nav-search"></div>' );
				$( '.mobile-toggle-nav-search' ).append( $mobileSearch );
				$mobileSearch.removeClass( 'wpex-hidden' );
			}

			// Insert mobile menu extras
			self.insertExtras( $( '.wpex-mobile-menu-top' ), $( '.mobile-toggle-nav-inner', $mobileToggleNav ), 'prepend' );
			self.insertExtras( $( '.wpex-mobile-menu-bottom' ), $( '.mobile-toggle-nav-inner', $mobileToggleNav ), 'append' );

			// Create menuAccordion
			self.menuAccordion( $mobileToggleNav );

			// On Show
			function openToggle( $button ) {
				if ( wpexLocalize.animateMobileToggle ) {
					$mobileToggleNav.stop( true, true ).slideDown( 'fast', function() {
						self.focusOnElement( $mobileToggleNav );
					} ).addClass( 'visible' );
				} else {
					$mobileToggleNav.addClass( 'visible' );
					self.focusOnElement( $mobileToggleNav );
				}
				$button.addClass( 'wpex-active' ).attr( 'aria-expanded', 'true' );
			}

			// On Close
			function closeToggle( $button ) {
				if ( wpexLocalize.animateMobileToggle ) {
					$mobileToggleNav.stop( true, true ).slideUp( 'fast' ).removeClass( 'visible' );
				} else {
					$mobileToggleNav.removeClass( 'visible' );
				}
				$mobileToggleNav.find( 'li.active > ul' ).stop( true, true ).slideUp( 'fast' );
				$mobileToggleNav.find( '.active' ).removeClass( 'active' );
				$button.removeClass( 'wpex-active' ).attr( 'aria-expanded', 'false' );
			}

			// Show/Hide
			$toggleBtn.on( 'click', function() {
				if ( $mobileToggleNav.hasClass( 'visible' ) ) {
					closeToggle( $( this ) );
				} else {
					openToggle( $( this ) );
				}
				return false;
			} );

			// Close when clicking esc
			$mobileToggleNav.keydown( function( e ) {
				if ( e.keyCode === 27 && $mobileToggleNav.hasClass( 'visible' ) ) {
					closeToggle( $toggleBtn );
					$toggleBtn.focus();
				}
			} );

			// Close on resize
			self.config.$window.resize( function() {
				if ( self.config.viewportWidth >= self.config.mobileMenuBreakpoint && $mobileToggleNav.hasClass( 'visible' ) ) {
					closeToggle( $toggleBtn );
				}
			} );

		},

		/**
		 * Overlay Mobile Menu.
		 */
		mobileMenuFullScreen: function() {

			var self           = this,
				$style         = wpexLocalize.fullScreenMobileMenuStyle || false, // prevent undefined class
				$mainMenu      = $( '#site-navigation .main-navigation-ul' ),
				$mobileMenuAlt = $( '#mobile-menu-alternative' ),
				$mobileSearch  = $( '#mobile-menu-search' ),
				menuHTML,
				$nav,
				$navUL,
				$navTransitionDuration,
				$menuButton;

			// Check and grab nav content
			if ( $mobileMenuAlt.length ) {
				menuHTML = $( '.dropdown-menu', $mobileMenuAlt ).html();
				$mobileMenuAlt.remove();
			} else if ( $mainMenu.length ) {
				menuHTML = $mainMenu.html();
			}

			// No menu, bail.
			if ( ! menuHTML ) {
				return;
			}

			// Insert new nav
			self.config.$body.append( '<div class="full-screen-overlay-nav wpex-mobile-menu wpex-clr ' + $style + '"><button class="full-screen-overlay-nav-close">&times;</button><div class="full-screen-overlay-nav-content"><div class="full-screen-overlay-nav-content-inner"><nav class="full-screen-overlay-nav-menu"><ul></ul></nav></div></div></div>' );

			$navUL = $( '.full-screen-overlay-nav-menu > ul' );

			$navUL.html( menuHTML );

			// Cache elements
			$nav        = $( '.full-screen-overlay-nav' );
			$menuButton = $( '.mobile-menu-toggle' );

			// Add initial aria attributes
			$nav.attr( 'aria-expanded', 'false' );

			// Remove all styles
			$( '.full-screen-overlay-nav, .full-screen-overlay-nav *' ).children().each( function() {
				$( this ).removeAttr( 'style' );
				$( this ).removeAttr( 'id' );
			} );

			// Add mobile menu extras
			self.insertExtras( $( '.wpex-mobile-menu-top' ), $( '.wpex-mobile-menu .full-screen-overlay-nav-content-inner' ), 'prepend' );
			self.insertExtras( $( '.wpex-mobile-menu-bottom' ), $( '.wpex-mobile-menu .full-screen-overlay-nav-content-inner' ), 'append' );

			// Add search to toggle menu
			if ( $mobileSearch.length ) {
				$navUL.append( $mobileSearch );
				$mobileSearch.wrap( '<li class="wpex-search"></li>' );
				$mobileSearch.removeClass( 'wpex-hidden' );
			}

			// Loop through parent items and add to dropdown if they have a link
			var parseDropParents = false;
			if ( ! parseDropParents ) {

				var $parents = $nav.find( 'li.menu-item-has-children > a' );

				$parents.each( function() {

					var $this = $( this );

					if ( $this && $this.attr( 'href' ) && '#' != $this.attr( 'href' ) ) {
						var $parent = $this.parent( 'li' ),
							el      = $parent.clone();
						$parent.removeClass( 'local-scroll' );
						$this.removeAttr( 'data-ls_linkto' );
						el.removeClass( 'menu-item-has-children' );
						el.find( 'ul' ).remove().end().prependTo( $this.next( 'ul' ) );
					}

				} );

				parseDropParents = true;

			}

			// Add toggle click event
			var $dropdownTargetEl = $nav.find( 'li.menu-item-has-children > a' );
			$dropdownTargetEl.on( 'click', function() {

				var $parentEl = $( this ).parent( 'li' );

				if ( ! $parentEl.hasClass( 'wpex-active' ) ) {
					var $allParentLis = $parentEl.parents( 'li' );
					$nav.find( '.menu-item-has-children' )
						.not( $allParentLis )
						.removeClass( 'wpex-active' )
						.children( 'ul' )
						.stop( true, true )
						.slideUp( 'fast' );
					$parentEl.addClass( 'wpex-active' ).children( 'ul' ).stop( true, true ).slideDown( {
						duration: 'normal',
						easing: 'easeInQuad'
					} );
				} else {
					$parentEl.removeClass( 'wpex-active' );
					$parentEl.find( 'li' ).removeClass( 'wpex-active' ); // Remove active from sub-drops
					$parentEl.find( 'ul' ).stop( true, true ).slideUp( 'fast' ); // Hide all drops
				}

				// Return false
				return false;

			} );

			// Show
			$menuButton.on( 'click', function() {

				// Toggle aria
				$nav.attr( 'aria-expanded', 'true' );
				$menuButton.attr( 'aria-expanded', 'true' );

				// Add visible class
				$nav.addClass( 'visible' );

				// Add no scroll to browser window
				self.config.$body.addClass( 'wpex-noscroll' );

				// Focus on the menu
				$navTransitionDuration = $nav.css( 'transition-duration' ) || '';
				if ( $navTransitionDuration ) {
					setTimeout( function() {
						self.focusOnElement( $nav );
					}, $navTransitionDuration.replace( 's', '' ) * 1000 );
				} else {
					self.focusOnElement( $nav );
				}

				// Return false on button click
				return false;

			} );

			// Hide overlay when clicking local scroll links
			$( '.local-scroll > a', $nav ).click( function() {
				var $hash = this.hash;
				if ( $.inArray( $hash, self.config.localScrollSections ) > -1 ) {
					onHide();
					return false;
				}
			} );

			// Hide when clicking close button
			$( '.full-screen-overlay-nav-close' ).on( 'click', function() {
				onHide();
				$menuButton.focus();
				return false;
			} );

			// Close when clicking esc
			$nav.keydown( function( e ) {
				if ( e.keyCode === 27 && $nav.hasClass( 'visible' ) ) {
					onHide();
					$menuButton.focus();
				}
			} );

			// Hide actions
			function onHide() {
				$nav.removeClass( 'visible' );
				$nav.attr( 'aria-expanded', 'false' );
				$menuButton.attr( 'aria-expanded', 'false' );
				$nav.find( 'li.wpex-active > ul' ).stop( true, true ).slideUp( 'fast' );
				$nav.find( '.wpex-active' ).removeClass( 'wpex-active' );
				self.config.$body.removeClass( 'wpex-noscroll' );
			}

		},

		/**
		 * Prevent clickin on links.
		 */
		navNoClick: function() {
			$( 'li.nav-no-click > a, li.sidr-class-nav-no-click > a' ).on( 'click', function() {
				return false;
			} );
		},

		/**
		 * Header Search.
		 */
		menuSearch: function() {
			var self      = this;
			var $toggleEl = '';
			var $wrapEl   = $( '.header-searchform-wrap' );

			// Alter search placeholder & autocomplete
			if ( $wrapEl.length ) {
				if ( $wrapEl.data( 'placeholder' ) ) {
					$wrapEl.find( 'input[type="search"]' ).attr( 'placeholder', $wrapEl.data( 'placeholder' ) );
				}
				if ( $wrapEl.data( 'disable-autocomplete' ) ) {
					$wrapEl.find( 'input[type="search"]' ).attr( 'autocomplete', 'off' );
				}
			}

			/**** Menu Search > Dropdown ****/
			if ( 'drop_down' == wpexLocalize.menuSearchStyle ) {

				$toggleEl = $( 'a.search-dropdown-toggle, a.mobile-menu-search' );
				var $searchDropdownForm = $( '#searchform-dropdown' );

				$toggleEl.click( function() {

					// Display search form
					$searchDropdownForm.toggleClass( 'show' );

					// Active menu item
					$( this ).parent( 'li' ).toggleClass( 'active' );

					// Focus
					var $transitionDuration = $searchDropdownForm.css( 'transition-duration' );
					$transitionDuration = $transitionDuration.replace( 's', '' ) * 1000;
					if ( $transitionDuration ) {
						setTimeout( function() {
							$searchDropdownForm.find( 'input[type="search"]' ).focus();
						}, $transitionDuration );
					}

					// Hide other things
					$( 'div#current-shop-items-dropdown' ).removeClass( 'show' );
					$( 'li.toggle-header-cart' ).removeClass( 'active' );

					// Return false
					return false;

				} );

				// Close on doc click
				self.config.$document.on( 'click', function( e ) {
					if ( ! $( e.target ).closest( '#searchform-dropdown.show' ).length ) {
						$toggleEl.parent( 'li' ).removeClass( 'active' );
						$searchDropdownForm.removeClass( 'show' );
					}
				} );

			}

			/**** Menu Search > Overlay Modal ****/
			else if ( 'overlay' == wpexLocalize.menuSearchStyle ) {

				$toggleEl = $( 'a.search-overlay-toggle, a.mobile-menu-search, li.search-overlay-toggle > a' );
				var $overlayEl = $( '#wpex-searchform-overlay' );
				var $inner = $overlayEl.find( '.wpex-inner' );

				$toggleEl.on( 'click', function() {
					$overlayEl.toggleClass( 'active' );
					$overlayEl.find( 'input[type="search"]' ).val( '' );
					if ( $overlayEl.hasClass( 'active' ) ) {
						var $overlayElTransitionDuration = $overlayEl.css( 'transition-duration' );
						$overlayElTransitionDuration = $overlayElTransitionDuration.replace( 's', '' ) * 1000;
						setTimeout( function() {
							$overlayEl.find( 'input[type="search"]' ).focus();
						}, $overlayElTransitionDuration );
					}
					return false;
				} );

				// Close searchforms
				$inner.click( function( e ) {
					e.stopPropagation();
				} );

				$overlayEl.click( function() {
					$overlayEl.removeClass( 'active' );
				} );

				$overlayEl.keydown( function( e ) {
					if ( e.keyCode === 27 ) {
						$overlayEl.removeClass( 'active' );
						$toggleEl.focus();
					}
				} );

			}

			/**** Menu Search > Header Replace ****/
			else if ( 'header_replace' == wpexLocalize.menuSearchStyle ) {

				$toggleEl = $( 'a.search-header-replace-toggle, a.mobile-menu-search' );
				var $headerReplace = $( '#searchform-header-replace' );

				// Show
				$toggleEl.click( function() {

					// Display search form
					$headerReplace.toggleClass( 'show' );

					// Focus
					var $transitionDuration = $headerReplace.css( 'transition-duration' );
					$transitionDuration = $transitionDuration.replace( 's', '' ) * 1000;
					if ( $transitionDuration ) {
						setTimeout( function() {
							$headerReplace.find( 'input[type="search"]' ).focus();
						}, $transitionDuration );
					}

					// Return false
					return false;

				} );

				// Close on click
				$( '#searchform-header-replace-close' ).click( function() {
					$headerReplace.removeClass( 'show' );
					return false;
				} );

				// Close on doc click
				self.config.$document.on( 'click', function( e ) {
					if ( ! $( e.target ).closest( $( '#searchform-header-replace.show' ) ).length ) {
						$headerReplace.removeClass( 'show' );
					}
				} );
			}

		},

		/**
		 * Header Cart.
		 */
		headerCart: function() {

			if ( $( 'a.wcmenucart' ).hasClass( 'go-to-shop' ) ) {
				return;
			}

			var $toggle = 'a.toggle-cart-widget, li.toggle-cart-widget > a, li.toggle-header-cart > a';

			if ( ! $( $toggle.length ) ) {
				return;
			}

			// Drop-down
			if ( 'drop_down' == wpexLocalize.wooCartStyle ) {

				var $dropdown = $( 'div#current-shop-items-dropdown' );

				// Display cart dropdown
				$( 'body' ).on( 'click', $toggle, function() {
					$( '#searchform-dropdown' ).removeClass( 'show' );
					$( 'a.search-dropdown-toggle' ).parent( 'li' ).removeClass( 'active' );
					$dropdown.toggleClass( 'show' );
					$( this ).toggleClass( 'active' );
					return false;
				} );

				// Hide cart dropdown
				$dropdown.click( function( e ) {
					if ( ! $( e.target ).is( 'a.remove_from_cart_button' ) ) {
						e.stopPropagation();
					}
				} );

				this.config.$document.click( function( e ) {
					if ( ! $( e.target ).is( 'a.remove_from_cart_button' ) ) {
						$dropdown.removeClass( 'show' );
						$( $toggle ).removeClass( 'active' );
					}
				} );

			}

			// Modal
			else if ( 'overlay' == wpexLocalize.wooCartStyle ) {

				var $overlayEl = $( '#wpex-cart-overlay' );
				var $inner     = $overlayEl.find( '.wpex-inner' );

				$( 'body' ).on( 'click', $toggle, function() {
					$overlayEl.toggleClass( 'active' );
					return false;
				} );

				// Close searchforms
				$inner.click( function( e ) {
					if ( ! $( e.target ).is( 'a.remove_from_cart_button' ) ) {
						e.stopPropagation();
					}
				} );
				$overlayEl.click( function( e ) {
					if ( ! $( e.target ).is( 'a.remove_from_cart_button' ) ) {
						$overlayEl.removeClass( 'active' );
					}
				} );

			}

		},

		/**
		 * Automatically add padding to row to offset header.
		 */
		headerOverlayOffset: function() {
			var $offset_element = $( '.add-overlay-header-offset' );
			if ( $offset_element.length ) {
				var self = this;
				var $height = self.config.siteHeaderHeight;
				if ( ! $height ) return;
				var $offset = $( '<div class="overlay-header-offset-div" style="height:'+ $height +'px"></div>' );
				$offset_element.prepend( $offset );
				self.config.$window.resize( function() {
					$offset.css( 'height', self.config.siteHeaderHeight );
				} );
			}
		},

		/**
		 * Hide post edit link.
		 */
		hideEditLink: function() {
			$( 'a.hide-post-edit', $( '#content' ) ).click( function() {
				$( 'div.post-edit' ).hide();
				return false;
			} );
		},

		/**
		 * Custom menu widget accordion.
		 */
		menuWidgetAccordion: function() {

			if ( ! wpexLocalize.menuWidgetAccordion ) {
				return;
			}

			var self = this;

			// Open toggle for active page
			$( '#sidebar .widget_nav_menu .current-menu-ancestor, .widget_nav_menu_accordion .widget_nav_menu .current-menu-ancestor', self.config.$siteMain ).addClass( 'active' ).children( 'ul' ).show();

			// Toggle items
			$( '#sidebar .widget_nav_menu, .widget_nav_menu_accordion  .widget_nav_menu', self.config.$siteMain ).each( function() {
				var $hasChildren = $( this ).find( '.menu-item-has-children' );
				$hasChildren.each( function() {
					$( this ).addClass( 'parent' );
					var $links = $( this ).children( 'a' );
					$links.on( 'click', function() {
						var $linkParent = $( this ).parent( 'li' );
						var $allParents = $linkParent.parents( 'li' );
						if ( ! $linkParent.hasClass( 'active' ) ) {
							$hasChildren.not( $allParents ).removeClass( 'active' ).children( '.sub-menu' ).slideUp( 'fast' );
							$linkParent.addClass( 'active' ).children( '.sub-menu' ).stop( true, true ).slideDown( 'fast' );
						} else {
							$linkParent.removeClass( 'active' ).children( '.sub-menu' ).stop( true, true ).slideUp( 'fast' );
						}
						return false;
					} );
				} );
			} );

		},

		/**
		 * Header 5 - Inline Logo.
		 */
		inlineHeaderLogo: function() {
			var self = this;

			// For header 5 only
			if ( 'five' != self.config.siteHeaderStyle ) {
				return;
			}

			// Define vars
			var $headerLogo        = $( '#site-header-inner > .header-five-logo', self.config.$siteHeader );
			var $headerNav         = $( '.navbar-style-five', self.config.$siteHeader );
			var $navLiCount        = $headerNav.children( '#site-navigation' ).children( 'ul' ).children( 'li' ).size();
			var $navBeforeMiddleLi = Math.round( $navLiCount / 2 ) - parseInt( wpexLocalize.headerFiveSplitOffset );

			// Insert Logo into Menu
			function onInit() {

				if ( ( self.config.viewportWidth > self.config.mobileMenuBreakpoint ) && $headerLogo.length && $headerNav.length ) {
					$( '<li class="menu-item-logo"></li>' ).insertAfter( $headerNav.find( '#site-navigation > ul > li:nth( '+ $navBeforeMiddleLi +' )' ) );
					$headerLogo.appendTo( $headerNav.find( '.menu-item-logo' ) );
				}

				$headerLogo.addClass( 'display' );

			}

			// Move logo
			function onResize() {

				var $centeredLogo = $( '.menu-item-logo .header-five-logo' );

				if ( self.config.viewportWidth <= self.config.mobileMenuBreakpoint ) {
					if ( $centeredLogo.length ) {
						$centeredLogo.prependTo( $( '#site-header-inner' ) );
						$( '.menu-item-logo' ).remove();
					}
				} else if ( ! $centeredLogo.length ) {
					onInit(); // Insert logo to menu
				}
			}

			// On init
			onInit();

			// Move logo on resize
			self.config.$window.resize( function() {
				onResize();
			} );

		},

		/**
		 * Back to top link.
		 */
		backTopLink: function() {
			var self           = this;
			var $scrollTopLink = $( 'a#site-scroll-top' );

			if ( $scrollTopLink.length ) {

				var $speed  = wpexLocalize.scrollTopSpeed ? parseInt( wpexLocalize.scrollTopSpeed ) : 1000;
				var $offset = wpexLocalize.scrollTopOffset ? parseInt( wpexLocalize.scrollTopOffset ) : 100;

				self.config.$window.scroll( function() {
					if ( $( this ).scrollTop() > $offset ) {
						$scrollTopLink.addClass( 'show' );
					} else {
						$scrollTopLink.removeClass( 'show' );
					}
				} );

				$scrollTopLink.on( 'click', function() {
					$( 'html, body' ).stop( true, true ).animate( {
						scrollTop : 0
					}, $speed, self.config.localScrollEasing );
					return false;
				} );

			}

		},

		/**
		 * Smooth Comment Scroll.
		 */
		smoothCommentScroll: function() {
			var self = this;
			$( '.single li.comment-scroll a' ).click( function() {
				var $target = $( '#comments' );
				var $offset = $target.offset().top - self.config.localScrollOffset - 20;
				self.scrollTo( $target, $offset );
				return false;
			} );
		},

		/**
		 * Togglebar toggle.
		 */
		toggleBar: function() {

			var self           = this;
			var $toggleBarWrap = $( '#toggle-bar-wrap' );

			if ( ! $toggleBarWrap.length ) {
				return;
			}

			var $toggleBtn     = $( 'a.toggle-bar-btn, a.togglebar-toggle, .togglebar-toggle > a' );
			var $toggleBtnIcon = $toggleBtn.find( '.ticon' );

			$toggleBtn.on( 'click', function() {
				if ( $toggleBtnIcon.length ) {
					$toggleBtnIcon.toggleClass( $toggleBtn.data( 'icon' ) );
					$toggleBtnIcon.toggleClass( $toggleBtn.data( 'icon-hover' ) );
				}
				$toggleBarWrap.toggleClass( 'active-bar' );
				return false;
			} );

			// Close on doc click
			self.config.$document.on( 'click', function( e ) {
				if ( ( $toggleBarWrap.hasClass( 'active-bar' ) && $toggleBarWrap.hasClass( 'close-on-doc-click' ) ) && ! $( e.target ).closest( '#toggle-bar-wrap' ).length ) {
					$toggleBarWrap.removeClass( 'active-bar' );
					if ( $toggleBtnIcon.length ) {
						$toggleBtnIcon.removeClass( $toggleBtn.data( 'icon-hover' ) ).addClass( $toggleBtn.data( 'icon' ) );
					}
				}
			} );

		},

		/**
		 * Sliders
		 */
		sliderPro: function( $context ) {

			if ( 'undefined' === typeof $.fn.sliderPro ) {
				return;
			}

			function dataValue( name, fallback ) {
				return ( typeof name !== 'undefined' ) ? name : fallback;
			}

			function getTallestEl( el ) {
				var tallest;
				var first = 1;
				el.each( function() {
					var $this = $( this );
					if ( first == 1 ) {
						tallest = $this;
						first = 0;
					} else {
						if ( tallest.height() < $this.height()) {
							tallest = $this;
						}
					}
				} );
				return tallest;
			}

			// Loop through each slider
			$( '.wpex-slider', $context ).each( function() {

				// Declare vars
				var $slider = $( this );
				var $data   = $slider.data();
				var $slides = $slider.find( '.sp-slide' );

				// Lets show things that were hidden to prevent flash
				$slider.find( '.wpex-slider-slide, .wpex-slider-thumbnails.sp-thumbnails,.wpex-slider-thumbnails.sp-nc-thumbnails' ).css( {
					'opacity' : 1,
					'display' : 'block'
				} );

				// Main checks
				var $autoHeight              = dataValue( $data.autoHeight, true );
				var $preloader               = $slider.prev( '.wpex-slider-preloaderimg' );
				var $height                  = ( $preloader.length && $autoHeight ) ? $preloader.outerHeight() : null;
				var $heightAnimationDuration = dataValue( $data.heightAnimationDuration, 600 );
				var $loop                    = dataValue( $data.loop, false );
				var $autoplay                = dataValue( $data.autoPlay, true );
				var $counter                 = dataValue( $data.counter, false );

				// Get height based on tallest item if autoHeight is disabled
				if ( ! $autoHeight && $slides.length ) {
					var $tallest = getTallestEl( $slides );
					$height = $tallest.height();
				}

				// TouchSwipe
				var $touchSwipe = true;

				if ( typeof $data.touchSwipeDesktop !== 'undefined' && ! /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test( navigator.userAgent ) ) {
					$touchSwipe = false;
				}

				// Run slider
				$slider.sliderPro( {

					//supportedAnimation      : 'JavaScript', //(CSS3 2D, CSS3 3D or JavaScript)
					aspectRatio             : -1,
					width                   : '100%',
					height                  : $height,
					responsive              : true,
					fade                    : dataValue( $data.fade, 600 ),
					fadeDuration            : dataValue( $data.animationSpeed, 600 ),
					slideAnimationDuration  : dataValue( $data.animationSpeed, 600 ),
					autoHeight              : $autoHeight,
					heightAnimationDuration : parseInt( $heightAnimationDuration ),
					arrows                  : dataValue( $data.arrows, true ),
					fadeArrows              : dataValue( $data.fadeArrows, true ),
					autoplay                : $autoplay,
					autoplayDelay           : dataValue( $data.autoPlayDelay, 5000 ),
					buttons                 : dataValue( $data.buttons, true ),
					shuffle                 : dataValue( $data.shuffle, false ),
					orientation             : dataValue( $data.direction, 'horizontal' ),
					loop                    : $loop,
					keyboard                : dataValue( $data.keyboard, false ),
					fullScreen              : dataValue( $data.fullscreen, false ),
					slideDistance           : dataValue( $data.slideDistance, 0 ),
					thumbnailsPosition      : 'bottom',
					thumbnailHeight         : dataValue( $data.thumbnailHeight, 70 ),
					thumbnailWidth          : dataValue( $data.thumbnailWidth, 70 ),
					thumbnailPointer        : dataValue( $data.thumbnailPointer, false ),
					updateHash              : dataValue( $data.updateHash, false ),
					touchSwipe              : $touchSwipe,
					thumbnailArrows         : false,
					fadeThumbnailArrows     : false,
					thumbnailTouchSwipe     : true,
					fadeCaption             : dataValue( $data.fadeCaption, true ),
					captionFadeDuration     : 600,
					waitForLayers           : true,
					autoScaleLayers         : true,
					forceSize               : dataValue( $data.forceSize, 'false' ),
					reachVideoAction        : dataValue( $data.reachVideoAction, 'playVideo' ),
					leaveVideoAction        : dataValue( $data.leaveVideoAction, 'pauseVideo' ),
					endVideoAction          : dataValue( $data.leaveVideoAction, 'nextSlide' ),
					fadeOutPreviousSlide    : true, // If disabled testimonial/content slides are bad
					autoplayOnHover         : dataValue( $data.autoplayOnHover, 'pause' ),
					init: function( e ) {

						// Remove preloader image
						$slider.prev( '.wpex-slider-preloaderimg' ).remove();

						// Add tab index and role attribute to slider arrows and buttons
						var $navItems = $slider.find( '.sp-arrow, .sp-button, .sp-nc-thumbnail-container, .sp-thumbnail-container' );

						$navItems.attr( 'tabindex', '0' );
						$navItems.attr( 'role', 'button' );

						// Add aria-label to bullets and thumbnails
						var $bullets = $slider.find( '.sp-button, .sp-thumbnail-container, .sp-thumbnail-container' );
						$bullets.each( function( index, val ) {
							var slideN = parseInt( index + 1 );
							$( this ).attr( 'aria-label', wpexSliderPro.i18n.GOTO + ' ' + slideN );
						} );

						// Add label to next arrow
						$slider.find( '.sp-previous-arrow' ).attr( 'aria-label', wpexSliderPro.i18n.PREV );

						// Add label to prev arrow
						$slider.find( '.sp-next-arrow' ).attr( 'aria-label', wpexSliderPro.i18n.NEXT );

					},
					gotoSlide: function( e ) {

						// Stop autoplay when loop is disabled and we've reached the last slide
						if ( ! $loop && $autoplay && e.index === $slider.find( '.sp-slide' ).length - 1 ) {
							$slider.data( 'sliderPro' ).stopAutoplay();
						}

						// Update counter
						if ( $counter ) {
							$slider.find( '.sp-counter .sp-active' ).text( e.index + 1 );
						}

					}

				} ); // end sliderPro

				// Get slider Data
				var slider = jQuery( this ).data( 'sliderPro' );

				// Add counter pagination
				if ( $counter ) {
					$( '.sp-slides-container', $slider ).append( '<div class="sp-counter"><span class="sp-active">' + ( parseInt( slider.getSelectedSlide() ) + 1 ) + '</span>/' + slider.getTotalSlides() + '</div>' );
				}

				// Accessability click events for bullets, arrows and no carousel thumbs
				var $navItems = $slider.find( '.sp-arrow, .sp-button, .sp-nc-thumbnail-container, .sp-thumbnail-container' );
				$navItems.keypress( function( e ) {
					if ( e.keyCode == 13 ) {
						$( this ).trigger( 'click' );
					}
				} );

				// Accessability click events for thumbnails
				var $thumbs = $( '.sp-thumbnail-container' );
				$thumbs.keypress( function( e ) {
					if ( e.keyCode == 13 ) {
						$( this ).closest( '.wpex-slider' ).sliderPro( 'gotoSlide', $( this ).index() );
					}
				} );

			} ); // End each

			// WooCommerce: Prevent clicking on Woo entry slider
			$( '.woo-product-entry-slider' ).click( function() {
				return false;
			} );

		},

		/**
		 * Advanced Parallax.
		 */
		parallax: function( $context ) {
			$( '.wpex-parallax-bg', $context ).each( function() {
				var $this = $( this );
				$this.scrolly2().trigger( 'scroll' );
				$this.css( {
					'opacity' : 1
				} );
			} );
		},

		/**
		 * Local Scroll Offset.
		 */
		parseLocalScrollOffset: function( instance ) {
			var self    = this;
			var $offset = 0;

			// Array of items to check
			var items = '.wpex-ls-offset, #wpadminbar, #top-bar-wrap-sticky-wrapper.wpex-can-sticky,#site-navigation-sticky-wrapper.wpex-can-sticky, #wpex-mobile-menu-fixed-top, .vcex-navbar-sticky-offset';

			// Return custom offset
			if ( wpexLocalize.localScrollOffset ) {
				return wpexLocalize.localScrollOffset;
			}

			// Adds extra offset via filter
			if ( wpexLocalize.localScrollExtraOffset ) {
				$offset = parseInt( $offset ) + parseInt( wpexLocalize.localScrollExtraOffset );
			}

			// Fixed header
			if ( self.config.hasStickyHeader ) {

				// Return 0 for small screens if mobile fixed header is disabled
				if ( ! self.config.hasStickyMobileHeader && self.config.windowWidth <= wpexLocalize.stickyHeaderBreakPoint ) {
					$offset = parseInt( $offset ) + 0;
				}

				// Return header height
				else {

					// Shrink header
					if ( self.config.$siteHeader.hasClass( 'shrink-sticky-header' ) ) {
						if ( 'init' == instance || self.config.$siteHeader.is( ':visible' ) ) {
							$offset = parseInt( $offset ) + parseInt( wpexLocalize.shrinkHeaderHeight );
						}
					}

					// Standard header
					else {
						$offset = parseInt( $offset ) + parseInt( self.config.siteHeaderHeight );
					}

				}

			}

			// Loop through extra items
			$( items ).each( function() {
				var $this = $( this );
				if ( $this.length && $this.is( ':visible' ) ) {
					$offset = parseInt( $offset ) + parseInt( $this.outerHeight() );
				}
			} );

			// Add 1 extra decimal to prevent cross browser rounding issues (mostly firefox)
			$offset = $offset ? $offset - 1 : 0;

			// Return offset
			return $offset;

		},

		/**
		 * Scroll to function.
		 */
		scrollTo: function( hash, offset, callback ) {

			// Hash is required
			if ( ! hash ) {
				return;
			}

			// Define important vars
			var self          = this;
			var $target       = null;
			var $page         = $( 'html, body' );
			var $isLsDataLink = false;

			// Check for local section using data-ls_id
			var localSection = self.getLocalSection( hash );

			if ( localSection ) {
				$target       = localSection;
				$isLsDataLink = true;
			}

			// Check for element with ID
			else {
				if ( typeof hash == 'string' ) {
					$target = $( hash );
				} else {
					$target = hash;
				}
			}

			// Target check
			if ( $target.length ) {

				// LocalScroll vars
				var $lsSpeed  = self.config.localScrollSpeed ? parseInt( self.config.localScrollSpeed ) : 1000,
					$lsOffset = self.config.localScrollOffset,
					$lsEasing = self.config.localScrollEasing;

				// Sanitize offset
				offset = offset ? offset : $target.offset().top - $lsOffset;

				// Update hash
				if ( hash && $isLsDataLink && wpexLocalize.localScrollUpdateHash ) {
					window.location.hash = hash;
				}

				/* @todo Remove hash on site top click
				if ( '#site_top' == hash && wpexLocalize.localScrollUpdateHash && window.location.hash ) {
					history.pushState( '', document.title, window.location.pathname);
				}*/

				// Mobile toggle Menu needs it's own code so it closes before the event fires
				// to make sure we end up in the right place
				var $mobileToggleNav = $( '.mobile-toggle-nav' );
				if ( $mobileToggleNav.hasClass( 'visible' ) ) {
					$( 'a.mobile-menu-toggle, li.mobile-menu-toggle > a' ).removeClass( 'wpex-active' );
					if ( wpexLocalize.animateMobileToggle ) {
						$mobileToggleNav.slideUp( 'fast', function() {
							$mobileToggleNav.removeClass( 'visible' );
							$page.stop( true, true ).animate( {
								scrollTop: $target.offset().top - $lsOffset
							}, $lsSpeed );
						} );
					} else {
						$mobileToggleNav.hide().removeClass( 'visible' );
						$page.stop( true, true ).animate( {
							scrollTop: $target.offset().top - $lsOffset
						}, $lsSpeed );
					}
				}

				// Scroll to target
				else {
					$page.stop( true, true ).animate( {
						scrollTop: offset
					}, $lsSpeed, $lsEasing );
				}

			}

		},

		/**
		 * Scroll to Hash.
		 */
		scrollToHash: function( self ) {

			var hash = location.hash,
				$target,
				$offset;

			// Security test
			//hash = '#<img src=x onerror=alert("not secure")>';
			//console.log( hash );

			// Hash needed
			if ( hash == '' || hash == '#' || hash == undefined ) {
				return;
			}

			// Scroll to comments
			if ( '#view_comments' == hash || '#comments_reply' == hash ) {
				$target = $( '#comments' );
				$offset = $target.offset().top - self.config.localScrollOffset - 20;
				if ( $target.length ) {
					self.scrollTo( $target, $offset );
				}
				return;
			}

			// Scroll to specific comment, fix for sticky header
			if ( self.config.hasStickyHeader && hash.indexOf( 'comment-' ) != -1 ) {
				$( '#comments .comment' ).each( function() {
					var id = $( this ).attr( 'id' );
					if ( hash.slice(1) == id ) {
						$target = $( this );
						$offset = $target.offset().top - self.config.localScrollOffset - 20;
						self.scrollTo( $target, $offset );
						return false;
					}
				} );
				return;
			}

			// Remove localscroll- from hash (older method)
			if ( hash.indexOf( 'localscroll-' ) != -1 ) {
				hash = hash.replace( 'localscroll-', '' );
			}

			// Check elements with data attributes
			var section = self.getLocalSection( hash );
			if ( section ) {
				self.scrollTo( section );
				return;
			}

		},

		/**
		 * Scroll to Hash.
		 */
		getLocalSection: function( sectionID, offset ) {
			var section;
			$( '[data-ls_id]' ).each( function() {
				var data = $( this ).data( 'ls_id' );
				if ( sectionID == data ) {
					section = $( this );
					return false;
				}
			} );
			return section;
		},

		/**
		 * Local scroll links array.
		 */
		localScrollSections: function() {
			var self = this;

			// Add local-scroll class to links in menu with localscroll- prefix (if on same page)
			// Add to $localScrollTargets
			// Add data-ls_linkto attr
			if ( self.config.$siteNav ) {

				var $navLinks    = $( 'a', this.config.$siteNav );
				var $location    = location;
				var $currentPage = $location.href;

				// Sanitize current page var
				$currentPage = $location.hash ? $currentPage.substr( 0, $currentPage.indexOf( '#' ) ) : $currentPage;

				// Loop through nav links
				$navLinks.each( function() {
					var $this = $( this );
					var $ref = $this.attr( 'href' );
						if ( $ref && $ref.indexOf( 'localscroll-' ) != -1 ) {
							$this.parent( 'li' ).addClass( 'local-scroll' );
							var $withoutHash = $ref.substr( 0, $ref.indexOf( '#' ) );
							if ( $withoutHash == $currentPage ) {
								var $hash = $ref.substring( $ref.indexOf( '#' ) + 1 );
								var $parseHash = $hash.replace( 'localscroll-', '' );
								$this.attr( 'data-ls_linkto', '#' + $parseHash );
							}
						}
				} );

			}

			// Define main vars
			var $array = [];
			var $links = $( self.config.$localScrollTargets );

			// Loop through links
			for ( var i=0; i < $links.length; i++ ) {

				// Add to array and save hash
				var $link    = $links[i];
				var $linkDom = $( $link );
				var $href    = $( $link ).attr( 'href' );
				var $hash    = $href ? '#' + $href.replace( /^.*?(#|$)/, '' ) : null;

				// Hash required
				if ( $hash && '#' != $hash ) {

					// Add custom data attribute to each
					if ( ! $linkDom.attr( 'data-ls_linkto' ) ) {
						$linkDom.attr( 'data-ls_linkto', $hash );
					}

					// Data attribute targets
					if ( $( '[data-ls_id="'+ $hash +'"]' ).length ) {
						if ( $.inArray( $hash, $array ) == -1 ) {
							$array.push( $hash );
						}
					}

					// Standard ID targets
					else if ( $( $hash ).length ) {
						if ( $.inArray( $hash, $array ) == -1 ) {
							$array.push( $hash );
						}
					}

				}

			}

			// Return array of local scroll links
			return $array;

		},

		/**
		 * Local Scroll link.
		 */
		localScrollLinks: function() {
			var self = this;

			// Local Scroll - Menus
			$( self.config.$localScrollTargets ).on( 'click', function() {
				var $this = $( this );
				var $hash = $this.attr( 'data-ls_linkto' );
				$hash = $hash ? $hash : this.hash; // Fallback
				if ( $.inArray( $hash, self.config.localScrollSections ) > -1 ) {
					$this.parent().removeClass( 'sfHover' );
					self.scrollTo( $hash );
					return false;
				}
			} );

			// Local Scroll - Logo
			$( 'a.wpex-scroll-top, .wpex-scroll-top a' ).on( 'click', function() {
				self.scrollTo( '#site_top' );
				return false;
			} );

			// Local Scroll - Woocommerce Reviews
			$( 'a.woocommerce-review-link', $( 'body.single div.entry-summary' ) ).click( function() {
				var $target = $( '.woocommerce-tabs' );
				if ( $target.length ) {
					$( '.reviews_tab a' ).click();
					var $offset = $target.offset().top - self.config.localScrollOffset;
					self.scrollTo( $target, $offset );
				}
				return false;
			} );

		},

		/**
		 * Local Scroll Highlight on scroll.
		 */
		localScrollHighlight: function() {

			// Return if disabled
			if ( ! wpexLocalize.localScrollHighlight ) {
				return;
			}

			// Define main vars
			var self = this,
				localScrollSections = self.config.localScrollSections;

			// Return if there aren't any local scroll items
			if ( ! localScrollSections.length ) {
				return;
			}

			// Define vars
			var $windowPos = this.config.$window.scrollTop(),
				$divPos,
				$divHeight,
				$higlight_link,
				$targetDiv;

			// Highlight active items
			for ( var i=0; i < localScrollSections.length; i++ ) {

				// Get section
				var $section = localScrollSections[i];

				// Data attribute targets
				if ( $( '[data-ls_id="' + $section + '"]' ).length ) {
					$targetDiv     = $( '[data-ls_id="' + $section + '"]' );
					$divPos        = $targetDiv.offset().top - self.config.localScrollOffset - 1;
					$divHeight     = $targetDiv.outerHeight();
					$higlight_link = $( '[data-ls_linkto="' + $section + '"]' );
				}

				// Standard element targets
				else if ( $( $section ).length ) {
					$targetDiv     = $( $section );
					$divPos        = $targetDiv.offset().top - self.config.localScrollOffset - 1;
					$divHeight     = $targetDiv.outerHeight();
					$higlight_link = $( '[data-ls_linkto="' + $section + '"]' );
				}

				// Higlight items
				if ( $windowPos >= $divPos && $windowPos < ( $divPos + $divHeight ) ) {
					$( '.local-scroll.menu-item' ).removeClass( 'current-menu-item' ); // prevent any sort of duplicate local scroll active links
					$higlight_link.addClass( 'active' );
					$targetDiv.addClass( 'wpex-ls-inview' );
					$higlight_link.parent( 'li' ).addClass( 'current-menu-item' );
				} else {
					$targetDiv.removeClass( 'wpex-ls-inview' );
					$higlight_link.removeClass( 'active' );
					$higlight_link.parent( 'li' ).removeClass( 'current-menu-item' );
				}

			}

			/* @todo: Highlight last item if at bottom of page or last item clicked - needs major testing now.
			var $docHeight    = this.config.$document.height();
			var windowHeight = this.config.windowHeight;
			var $lastLink = localScrollSections[localScrollSections.length-1];
			if ( $windowPos + windowHeight == $docHeight ) {
				$( '.local-scroll.current-menu-item' ).removeClass( 'current-menu-item' );
				$( "li.local-scroll a[href='" + $lastLink + "']" ).parent( 'li' ).addClass( 'current-menu-item' );
			}*/

		},

		/**
		 * Equal heights function => Must run before isotope method.
		 */
		equalHeights: function( $context ) {

			if ( 'undefined' === typeof $.fn.wpexEqualHeights ) {
				return;
			}

			// Add equal heights grid
			$( '.match-height-grid', $context ).wpexEqualHeights( {
				children: '.match-height-content'
			} );

			// Columns
			$( '.match-height-row', $context ).wpexEqualHeights( {
				children: '.match-height-content'
			} );

			// Feature Box
			$( '.vcex-feature-box-match-height', $context ).wpexEqualHeights( {
				children: '.vcex-match-height'
			} );

			// Blog entries
			$( '.blog-equal-heights', $context ).wpexEqualHeights( {
				children: '.blog-entry-inner'
			} );

			// Related entries
			$( '.related-posts', $context ).wpexEqualHeights( {
				children: '.related-post-content'
			} );

			// Row => @deprecated in v4.0
			$( '.wpex-vc-row-columns-match-height', $context ).wpexEqualHeights( {
				children: '.vc_column-inner'
			} );

			// Manual equal heights
			$( '.vc_row', $context ).wpexEqualHeights( {
				children: '.equal-height-column'
			} );
			$( '.vc_row', $context ).wpexEqualHeights( {
				children: '.equal-height-content'
			} );

		},

		/**
		 * Footer Reveal Display on Load.
		 */
		footerReveal: function() {
			var self = this;

			// Return if disabled
			if ( ! self.config.$hasFooterReveal ) {
				return;
			}

			// Footer reveal
			var $footerReveal = self.config.$footerReveal;

			function showHide() {

				// Disabled under 960
				if ( self.config.viewportWidth < 960 ) {
					if ( $footerReveal.hasClass( 'footer-reveal' ) ) {
						$footerReveal.toggleClass( 'footer-reveal footer-reveal-visible' );
						self.config.$siteWrap.css( 'margin-bottom', '' );
					}
					return;
				}

				var $hideFooter         = false,
					$footerRevealHeight = $footerReveal.outerHeight(),
					windowHeight       = self.config.windowHeight,
					$heightCheck        = 0;

				if ( $footerReveal.hasClass( 'footer-reveal' ) ) {
					$heightCheck = self.config.$siteWrap.outerHeight() + self.config.localScrollOffset;
				} else {
					$heightCheck = self.config.$siteWrap.outerHeight() + self.config.localScrollOffset - $footerRevealHeight;
				}

				// Check window height
				if ( ( windowHeight > $footerRevealHeight ) && ( $heightCheck  > windowHeight ) ) {
					$hideFooter = true;
				}

				// Footer Reveal
				if ( $hideFooter && $footerReveal.hasClass( 'footer-reveal-visible' ) ) {
					self.config.$siteWrap.css( {
						'margin-bottom': $footerRevealHeight
					} );
					$footerReveal.removeClass( 'footer-reveal-visible' );
					$footerReveal.addClass( 'footer-reveal' );
				}

				// Visible Footer
				if ( ! $hideFooter && $footerReveal.hasClass( 'footer-reveal' ) ) {
					self.config.$siteWrap.css( 'margin-bottom', '' );
					$footerReveal.removeClass( 'footer-reveal' );
					$footerReveal.removeClass( 'wpex-visible' );
					$footerReveal.addClass( 'footer-reveal-visible' );
				}

			}

			function reveal() {
				if ( $footerReveal.hasClass( 'footer-reveal' ) ) {
					if ( self.scrolledToBottom( self.config.$siteMain ) ) {
						$footerReveal.addClass( 'wpex-visible' );
					} else {
						$footerReveal.removeClass( 'wpex-visible' );
					}
				}
			}

			// Fire on init
			showHide();

			// Fire onscroll event
			self.config.$window.scroll( function() {
				reveal();
			} );

			// Fire onResize
			self.config.$window.resize( function() {
				if ( self.config.widthChanged || self.config.heightChanged ) {
					showHide();
				}
			} );

		},

		/**
		 * Set min height on main container to prevent issue with extra space below footer.
		 */
		fixedFooter: function() {
			var self = this;

			// Checks
			if ( ! self.config.$siteMain || ! self.config.hasFixedFooter ) {
				return;
			}

			function run() {

				// Set main vars
				var $mainHeight = self.config.$siteMain.outerHeight();
				var $htmlHeight = $( 'html' ).height();

				// Generate min Height
				var $minHeight = $mainHeight + ( self.config.$window.height() - $htmlHeight );

				// Add min height
				self.config.$siteMain.css( 'min-height', $minHeight );

			}

			// Run on doc ready
			run();

			// Run on resize
			self.config.$window.resize( function() {
				if ( self.config.widthChanged || self.config.heightChanged ) {
					run();
				}
			} );

		},

		/**
		 * If title and breadcrumbs don't both fit in the header switch breadcrumb style.
		 */
		titleBreadcrumbsFix: function() {
			var self = this;

			// Return if disabled
			if ( ! self.config.$body.hasClass( 'has-breadcrumbs' ) ) {
				return;
			}

			var $pageHeader = $( '.page-header' );
			var $crumbs = $( '.site-breadcrumbs.position-absolute', $pageHeader );
			if ( ! $crumbs.length || ! $crumbs.hasClass( 'has-js-fix' ) ) {
				return;
			}

			var $crumbsTrail = $( '.breadcrumb-trail', $crumbs );
			if ( ! $crumbsTrail.length ) {
				return;
			}

			var $headerInner = $( '.page-header-inner', $pageHeader );
			if ( ! $headerInner.length ) {
				return;
			}

			var $title = $( '.page-header-title > span', $headerInner );
			if ( ! $title.length ) {
				return;
			}

			function tweak_classes() {
				if ( ( $title.width() + $crumbsTrail.width() + 20 ) >= $headerInner.width() ) {
					if ( $crumbs.hasClass( 'position-absolute' ) ) {
						$crumbs.removeClass( 'position-absolute' );
						$crumbs.addClass( 'position-under-title' );
					}
				} else {
					$crumbs.removeClass( 'position-under-title' );
					$crumbs.addClass( 'position-absolute' );
				}
			}

			// Run on init
			tweak_classes();

			// Run on resize
			self.config.$window.resize( function() {
				tweak_classes();
			} );

		},

		/**
		 * Custom Selects.
		 */
		customSelects: function( $context ) {

			$( wpexLocalize.customSelects, $context ).each( function() {
				var $this   = $( this );
				var elID    = $this.attr( 'id' );
				var elClass = elID ? ' wpex-' + elID : '';
				if ( $this.is( ':visible' ) ) {
					if ( $this.attr( 'multiple' ) ) {
						$this.wrap( '<div class="wpex-multiselect-wrap' + elClass + '"></div>' );
					} else {
						$this.wrap( '<div class="wpex-select-wrap' + elClass + '"></div>' );
					}
				}
			} );

			$( '.wpex-select-wrap', $context ).append( '<span class="ticon ticon-angle-down" aria-hidden="true"></span>' );

			if ( 'undefined' !== typeof $.fn.select2 ) {
				$( '#calc_shipping_country' ).select2();
			}

		},

		/**
		 * Archive Masonry Grids.
		 */
		archiveMasonryGrids: function() {

			// Make sure scripts are loaded
			if ( 'undefined' === typeof $.fn.imagesLoaded || 'undefined' === typeof $.fn.isotope ) {
				return;
			}

			// Define main vars
			var self      = this;
			var $archives = $( '.wpex-masonry-grid,.blog-masonry-grid,div.wpex-row.portfolio-masonry,div.wpex-row.portfolio-no-margins,div.wpex-row.staff-masonry,div.wpex-row.staff-no-margins,div.wpex-row.testimonials-masonry' );

			// Loop through archives
			$archives.each( function() {

				var $container = $( this );
				var $data      = $container.data();

				// Load isotope after images loaded
				$container.imagesLoaded( function() {

					$container.isotope( {
						itemSelector       : '.isotope-entry',
						transformsEnabled  : true,
						isOriginLeft       : wpexLocalize.isRTL ? false : true,
						transitionDuration : self.pData( $data.transitionDuration, '0.4' ) + 's',
						layoutMode         : self.pData( $data.layoutMode, 'masonry' )
					} );

				} );

			} );

		},

		/**
		 * Lightbox wrapper method that calls all sub-lightbox methods.
		 * Note : This method only needs to run 1x on the site, otherwise you could end up with duplicate lightbox.
		 */
		lightbox: function( $context ) {
			this.autoLightbox();
			this.lightboxSingle( $context );
			this.lightboxInlineGallery( $context );
			this.lightboxGallery( $context );
			this.lightboxCarousels( $context );
		},

		/**
		 * Automatic Lightbox for images.
		 */
		autoLightbox: function() {
			if ( ! wpexLocalize.autoLightbox ) {
				return;
			}
			var self     = this,
				imageExt = ['bmp', 'gif', 'jpeg', 'jpg', 'png', 'tiff', 'tif', 'jfif', 'jpe'];
			$( wpexLocalize.autoLightbox ).each( function() {
				var $this = $( this );
				var href  = $this.attr( 'href' );
				var ext   = self.getUrlExtension( href );
				if ( href && imageExt.indexOf( ext ) !== -1 ) {
					if ( ! $this.parents( '.woocommerce-product-gallery' ).length ) {
						$this.addClass( 'wpex-lightbox' );
					}
				}
			} );
		},

		/**
		 * Single lightbox.
		 */
		lightboxSingle: function( $context ) {

			var self = this,
				targets = '.wpex-lightbox, .wpex-lightbox-video, .wpb_single_image.video-lightbox a, .wpex-lightbox-autodetect, .wpex-lightbox-autodetect a';

			$context = $context || $( 'body' );

			$context.on( 'click', targets, function( e ) {

				e.preventDefault();

				var $this = $( this );

				if ( ! $this.is( 'a' ) ) {
					$this = $this.find( 'a' );
				}

				if ( $this.hasClass( 'wpex-lightbox-group-item' ) ) {
					return;
				}

				var customSettings = {};
				var opts           = $this.data() || {};
				var src            = $this.attr( 'href' ) || $this.data( 'src' ) || '';
				var type           = $this.data( 'type' ) || '';
				var caption        = $this.data( 'caption' ) || '';
				var show_title     = $this.attr( 'data-show_title' ) || true;
				var oldOpts        = $this.data( 'options' ) && self.parseObjectLiteralData( $this.data( 'options' ) ) || '';

				if ( ! opts.parsedOpts ) {

					if ( oldOpts ) {

						if ( $this.data( 'type' ) && 'iframe' == $this.data( 'type' ) ) {
							if ( oldOpts.width && oldOpts.height ) {
								opts.width  = oldOpts.width;
								opts.height = oldOpts.height;
							}
						}

						if ( oldOpts.iframeType && 'video' == oldOpts.iframeType ) {
							type = '';
						}

					}

					if ( 'iframe' == type && opts.width && opts.height ) {
						opts.iframe = {
							css : {
								'width'  : opts.width,
								'height' : opts.height
							}
						};
					}

					if ( 'false' !== show_title ) {
						var title = $this.data( 'title' ) || '';
						if ( title.length ) {
							var titleClass = 'fancybox-caption__title';
							if ( caption.length ) {
								titleClass = titleClass + ' fancybox-caption__title-margin';
							}
							caption = '<div class="' + titleClass + '">' + title + '</div>' + caption;
						}
					}

					if ( caption.length ) {
						opts.caption = caption;
					}

					opts.parsedOpts = true; // prevent duplicating caption since we are storing new caption in data

				}

				if ( $this.hasClass( 'wpex-lightbox-iframe' ) ) {
					type = 'iframe'; // for use with random modules
				}

				if ( $this.hasClass( 'wpex-lightbox-inline' ) ) {
					type = 'inline'; // for use with random modules
				}

				if ( $this.hasClass( 'rev-btn' ) ) {
					type = '';
					opts = {}; // fixes rev slider issues.
				}

				$.fancybox.open( [ {
					src  : src,
					opts : opts,
					type : type
				} ], $.extend( {}, wpexLightboxSettings, customSettings ) );

			} );

		},

		/**
		 * Inline Lightbox Gallery.
		 */
		lightboxInlineGallery: function( $context ) {

			var self = this;

			$context = $context || self.config.$document;

			$context.on( 'click', '.wpex-lightbox-gallery', function( e ) {

				e.preventDefault();

				var $this   = $( this );
				var gallery = $this.data( 'gallery' ) || '';
				var items   = [];

				if ( gallery.length && 'object' === typeof gallery ) {

					$.each( gallery, function( index, val ) {
						var opts    = {};
						var title   = val.title || '';
						var caption = val.caption || '';
						if ( title.length ) {
							var titleClass = 'fancybox-caption__title';
							if ( caption.length ) {
								titleClass = titleClass + ' fancybox-caption__title-margin';
							}
							caption = '<div class="' + titleClass + '">' + title + '</div>' + caption;
						}
						if ( caption.length ) {
							opts.caption = caption;
						}
						opts.thumb = val.thumb || val.src;
						items.push( {
							src  : val.src,
							opts : opts
						} );
					} );

					$.fancybox.open( items, wpexLightboxSettings );

				}

			} );

		},

		/**
		 * Gallery lightbox
		 */
		lightboxGallery: function( $context ) {

			var self = this;
			$context = $context || self.config.$document;

			$( 'a.wpex-lightbox-group-item' ).removeClass( 'wpex-lightbox' ); // Prevent conflicts (can't be a group item and a single lightbox item

			$context.on( 'click', 'a.wpex-lightbox-group-item', function( e ) {

				e.preventDefault();

				$( '.wpex-lightbox-group-item' ).removeAttr( 'data-lb-index' ); // Remove all lb-indexes to prevent issues with filterable grids or hidden items

				var $this          = $( this );
				var $group         = $this.closest( '.wpex-lightbox-group' );
				var $groupItems    = $group.find( 'a.wpex-lightbox-group-item:visible' );
				var customSettings = {};
				var items          = [];
				var activeIndex    = 0;

				$groupItems.each( function( index ) {

					var $item      = $( this );
					var opts       = $item.data() || {};
					var src        = $item.attr( 'href' ) || $item.data( 'src' ) || '';
					var title      = '';
					var show_title = $item.attr( 'data-show_title' ) || true;
					var caption    = $item.data( 'caption' ) || '';
					var oldOpts    = $item.data( 'options' ) && self.parseObjectLiteralData( '({' + $item.data( 'options' ) + '})' ) || '';

					if ( ! opts.parsedOpts ) {

						opts.thumb = $item.data( 'thumb' ) || src;

						if ( oldOpts ) {
							opts.thumb = oldOpts.thumbnail || opts.thumb;
							if ( oldOpts.iframeType && 'video' == oldOpts.iframeType ) {
								opts.type = '';
							}
						}

						if ( 'false' !== show_title ) {
							title = $item.data( 'title' ) || $item.attr( 'title' ) || '';
							if ( title.length ) {
								var titleClass = 'fancybox-caption__title';
								if ( caption.length ) {
									titleClass = titleClass + ' fancybox-caption__title-margin';
								}
								caption = '<div class="' + titleClass + '">' + title + '</div>' + caption;
							}
						}

						if ( caption.length ) {
							opts.caption = caption;
						}

						opts.parsedOpts = true;

					}

					if ( src ) {

						$item.attr( 'data-lb-index', index );

						if ( $this[0] == $item[0] ) {
							activeIndex = index;
						}

						items.push( {
							src  : src,
							opts : opts
						} );

					}

				} );

				$.fancybox.open( items, $.extend( {}, wpexLightboxSettings, customSettings ), activeIndex );

			} );

		},

		/**
		 * Carousel Lightbox.
		 */
		lightboxCarousels: function( $context ) {

			var self = this;
			$context = $context || self.config.$document;

			$context.on( 'click', '.wpex-carousel-lightbox-item', function( e ) {

				e.preventDefault();

				var $this          = $( this );
				var $parent        = $this.parents( '.wpex-carousel' );
				var $owlItems      = $parent.find( '.owl-item' );
				var items          = [];
				var customSettings = {
					loop : true // carousels should always loop so it's not strange when clicking an item after scrolling.
				};

				$owlItems.each( function() {

					if ( ! $( this ).hasClass( 'cloned' ) ) {

						var $item = $( this ).find( '.wpex-carousel-lightbox-item' );

						if ( $item.length ) {

							var opts       = {};
							var src        = $item.attr( 'href' ) || $item.data( 'src' ) || '';
							var title      = $item.data( 'title' ) || $item.attr( 'title' ) || '';
							var caption    = $item.data( 'caption' ) || '';
							var show_title = $item.attr( 'data-show_title' ) || true;

							if ( 'false' !== show_title && title.length ) {
								var titleClass = 'fancybox-caption__title';
								if ( caption.length ) {
									titleClass = titleClass + ' fancybox-caption__title-margin';
								}
								caption = '<div class="' + titleClass + '">' + title + '</div>' + caption;
							}

							if ( caption.length ) {
								opts.caption = caption;
							}

							opts.thumb = $item.data( 'thumb' ) || src;

							items.push( {
								src  : src,
								opts : opts
							} );

						}

					}

				} );

				if ( items.length && 'object' === typeof items ) {
					var activeIndex = $this.data( 'count' ) - 1 || 0;
					$.fancybox.open( items, $.extend( {}, wpexLightboxSettings, customSettings ), activeIndex );
				}

			} );

		},

		/**
		 * Overlay Mobile Support.
		 */
		overlaysMobileSupport: function() {

			if ( ! this.config.isMobile ) {
				return;
			}

			// Remove overlays completely if mobile support is disabled
			$( '.overlay-parent.overlay-hh' ).each( function() {
				if ( ! $( this ).hasClass( 'overlay-ms' ) ) {
					$( this ).find( '.theme-overlay' ).remove();
				}
			} );

			// Prevent click on touchstart
			$( 'a.overlay-parent.overlay-ms.overlay-h, .overlay-parent.overlay-ms.overlay-h > a' ).on( 'touchstart', function( e ) {

				var $this = $( this );
				var $overlayParent = $this.hasClass( 'overlay-parent' ) ? $this : $this.parent( '.overlay-parent' );

				if ( $overlayParent.hasClass( 'wpex-touched' ) ) {
					return true;
				} else {
					$overlayParent.addClass( 'wpex-touched' );
					$( '.overlay-parent' ).not($overlayParent).removeClass( 'wpex-touched' );
					e.preventDefault();
					return false;
				}

			} );

			// Hide overlay when clicking outside
			this.config.$document.on( 'touchstart', function( e ) {
				if ( ! $( e.target ).closest( '.wpex-touched' ).length ) {
					$( '.wpex-touched' ).removeClass( 'wpex-touched' );
				}
			} );

		},

		/**
		 * Overlay Hovers.
		 */
		overlayHovers: function() {

			// Overlay title push up.
			$( '.overlay-parent-title-push-up' ).each( function() {

				// Define vars
				var $this        = $( this ),
					$title       = $this.find( '.overlay-title-push-up' ),
					$child       = $this.find( 'a' ),
					$img         = $child.find( 'img' ),
					$titleHeight = $title.outerHeight();

				// Position title
				$title.css( {
					'bottom' : - $titleHeight
				} );

				// Add height to child
				$child.css( {
					'height' : $img.outerHeight()
				} );

				// Position image
				$img.css( {
					'position' : 'absolute',
					'top'      : '0',
					'left'     : '0',
					'width'    : 'auto',
					'height'   : 'auto'
				} );

				// Animate image on hover
				$this.hover( function() {
					$img.css( {
						'top' : -20
					} );
					$title.css( {
						'bottom' : 0
					} );
				}, function() {
					$img.css( {
						'top' : '0'
					} );
					$title.css( {
						'bottom' : - $titleHeight
					} );
				} );

			} );

		},

		/**
		 * Sticky Topbar.
		 */
		stickyTopBar: function() {
			var self = this;

			// Return if disabled or not found
			if ( ! self.config.hasStickyTopBar || ! self.config.$stickyTopBar ) {
				return;
			}

			// Define vars
			var $isSticky      = false,
				$offset        = 0,
				$window        = self.config.$window,
				$stickyTopbar  = self.config.$stickyTopBar,
				$mobileSupport = self.config.hasStickyTopBarMobile,
				$brkPoint      = wpexLocalize.stickyTopBarBreakPoint,
				$mobileMenu    = $( '#wpex-mobile-menu-fixed-top' ),
				$stickyWrap    = $( '<div id="top-bar-wrap-sticky-wrapper" class="wpex-sticky-top-bar-holder not-sticky"></div>' );

			// Set sticky wrap to new wrapper
			$stickyTopbar.wrapAll( $stickyWrap );
			$stickyWrap = $( '#top-bar-wrap-sticky-wrapper' );

			// Get offset
			function getOffset() {
				$offset = 0; // Reset offset for resize
				var $wpToolbar = $( '#wpadminbar' );
				if ( $wpToolbar.is( ':visible' ) && $wpToolbar.css( 'position' ) === 'fixed' ) {
					$offset = $offset + $wpToolbar.outerHeight();
				}
				if ( $mobileMenu.is( ':visible' ) ) {
					$offset = $offset + $mobileMenu.outerHeight();
				}
				return $offset;
			}

			// Stick the TopBar
			function setSticky() {

				// Already stuck
				if ( $isSticky ) {
					return;
				}

				// Add wrap class and toggle sticky class
				$stickyWrap
					.css( 'height', $stickyTopbar.outerHeight() )
					.removeClass( 'not-sticky' )
					.addClass( 'is-sticky' );

				// Add CSS to topbar
				$stickyTopbar.css( {
					'top'   : getOffset(),
					'width' : $stickyWrap.width()
				} );

				// Set sticky to true
				$isSticky = true;

			}

			// Unstick the TopBar
			function destroySticky() {

				if ( ! $isSticky ) {
					return;
				}

				// Remove sticky wrap height and toggle sticky class
				$stickyWrap
					.css( 'height', '' )
					.removeClass( 'is-sticky' )
					.addClass( 'not-sticky' );

				// Remove topbar css
				$stickyTopbar.css( {
					'width' : '',
					'top'   : '',
				} );

				// Set sticky to false
				$isSticky = false;

			}

			// Runs on load and resize
			function initSticky() {

				if ( ! $mobileSupport && ( self.config.viewportWidth < $brkPoint ) ) {
					$stickyWrap.removeClass( 'wpex-can-sticky' );
					destroySticky();
					return;
				}

				$stickyWrap.addClass( 'wpex-can-sticky' );

				if ( $isSticky ) {

					$stickyWrap.css( 'height', $stickyTopbar.outerHeight() );

					$stickyTopbar.css( {
						'top'   : getOffset(),
						'width' : $stickyWrap.width()
					} );

				} else {

					// Set sticky based on original offset
					$offset = $stickyWrap.offset().top - getOffset();

					// Set or destroy sticky
					if ( self.config.windowTop > $offset ) {
						setSticky();
					} else {
						destroySticky();
					}

				}

			}

			// On scroll actions for sticky topbar
			function onScroll() {

				// Not allowed to sticky at this window size
				if ( ! $stickyWrap.hasClass( 'wpex-can-sticky' ) ) {
					return;
				}

				// Destroy sticky at top and prevent sticky at top (since its already at top)
				if ( 0 === self.config.windowTop ) {
					if ( $isSticky ) {
						destroySticky();
					}
					return;
				}

				// Get correct start position for sticky to start
				var $stickyWrapTop = $stickyWrap.offset().top;
				var $setStickyPos  = $stickyWrapTop - getOffset();

				// Set or destroy sticky based on offset
				if ( self.config.windowTop >= $setStickyPos ) {
					setSticky();
				} else {
					destroySticky();
				}

			}

			// Fire on init
			initSticky();

			// Fire onscroll event
			$window.scroll( function() {
				onScroll();
			} );

			// Fire onResize
			$window.resize( function() {
				initSticky();
			} );

			// Fire resize on flip
			// Destroy and re-calculate
			$window.on( 'orientationchange' , function( e ) {
				destroySticky();
				initSticky();
			} );

		},

		/**
		 * Get correct offSet for the sticky header and sticky header menu.
		 */
		stickyOffset: function() {
			var $offset       = 0;
			var $mobileMenu   = $( '#wpex-mobile-menu-fixed-top' );
			var $stickyTopbar = $( '#top-bar-wrap-sticky-wrapper.wpex-can-sticky' );
			var $wpToolbar    = $( '#wpadminbar' );

			// Offset sticky topbar
			if ( $stickyTopbar.is( ':visible' ) ) {
				$offset = $offset + $stickyTopbar.outerHeight();
			}

			// Offset mobile menu
			if ( $mobileMenu.is( ':visible' ) ) {
				$offset = $offset + $mobileMenu.outerHeight();
			}

			// Offset adminbar
			if ( $wpToolbar.is( ':visible' ) && $wpToolbar.css( 'position' ) === 'fixed' ) {
				$offset = $offset + $wpToolbar.outerHeight();
			}

			// Added offset via child theme
			if ( wpexLocalize.addStickyHeaderOffset ) {
				$offset = $offset + wpexLocalize.addStickyHeaderOffset;
			}

			// Return correct offset
			return $offset;

		},

		/**
		 * Sticky header custom start point.
		 */
		stickyHeaderCustomStartPoint: function() {
			var $startPosition = wpexLocalize.stickyHeaderStartPosition;
			if ( $.isNumeric( $startPosition ) ) {
				$startPosition = $startPosition;
			} else if ( $( $startPosition ).length ) {
				$startPosition = $( $startPosition ).offset().top;
			} else {
				$startPosition = 0;
			}
			return $startPosition;
		},

		/**
		 * New Sticky Header.
		 */
		stickyHeader: function() {
			var self           = this,
				$isSticky      = false,
				$isShrunk      = false,
				$isLogoSwapped = false;

			// Return if sticky is disabled
			if ( ! self.config.hasStickyHeader ) {
				return;
			}

			// Define header
			var $header        = self.config.$siteHeader;
			var $headerHeight  = self.config.siteHeaderHeight;
			var $headerBottom  = $header.offset().top + $header.outerHeight();

			// Add sticky wrap
			var $stickyWrap = $( '<div id="site-header-sticky-wrapper" class="wpex-sticky-header-holder not-sticky"></div>' );
			$header.wrapAll( $stickyWrap );
			$stickyWrap     = $( '#site-header-sticky-wrapper' ); // Cache newly added element as dom object

			// Define main vars for sticky function
			var $window        = self.config.$window;
			var $brkPoint      = wpexLocalize.stickyHeaderBreakPoint;
			var $mobileSupport = self.config.hasStickyMobileHeader;
			var $customStart   = self.stickyHeaderCustomStartPoint();

			// Custom sticky logo
			var $headerLogo    = self.config.$siteLogo;
			var $headerLogoSrc = self.config.siteLogoSrc;

			// Shrink support
			var maybeShrink = ( 'shrink' == self.stickyHeaderStyle || 'shrink_animated' == self.stickyHeaderStyle ) ? true : false;

			// Custom shrink logo
			var $stickyLogo = wpexLocalize.stickyheaderCustomLogo;
			if ( $stickyLogo && wpexLocalize.stickyheaderCustomLogoRetina && self.config.isRetina ) {
				$stickyLogo = wpexLocalize.stickyheaderCustomLogoRetina;
			}

			// Load images to be used for the custom sticky logo
			if ( $stickyLogo ) {
				$( '<img src="' + $stickyLogo + '">' ).appendTo( 'body' ).css( 'display', 'none' );
			}

			// Check if we are on mobile size
			function pastBreakPoint() {
				return ( self.config.viewportWidth < $brkPoint ) ? true : false;
			}

			// Check if we are past the header
			function pastheader() {
				var bottomCheck = 0;
				if ( self.config.hasHeaderOverlay ) {
					bottomCheck = $headerBottom;
				} else {
					bottomCheck = $stickyWrap.offset().top + $stickyWrap.outerHeight();
				}
				if ( self.config.windowTop > $headerBottom ) {
					return true;
				}
				return false;
			}

			// Check start position
			function start_position() {
				var $startPosition = $customStart;
				$startPosition = $startPosition ? $startPosition : $stickyWrap.offset().top;
				return $startPosition - self.stickyOffset();
			}

			// Transform
			function transformPrepare() {
				if ( $isSticky ) {
					$header.addClass( 'transform-go' ); // prevent issues when scrolling
				}
				if ( 0 === self.config.windowTop ) {
					$header.removeClass( 'transform-prepare' );
				} else if ( pastheader() ) {
					$header.addClass( 'transform-prepare' );
				} else {
					$header.removeClass( 'transform-prepare' );
				}
			}

			// Swap logo
			function swapLogo() {

				if ( ! $stickyLogo || ! $headerLogo ) {
					return;
				}

				if ( $isLogoSwapped ) {

					$headerLogo.attr( 'src', $headerLogoSrc );
					self.config.siteLogoHeight = self.config.$siteLogo.height();

					$isLogoSwapped = false;

				} else {

					$headerLogo.attr( 'src', $stickyLogo );
					self.config.siteLogoHeight = self.config.$siteLogo.height();

					$isLogoSwapped = true;

				}

			}

			// Shrink/unshrink header
			function shrink() {

				var checks = maybeShrink;

				if ( pastBreakPoint() ) {
					if ( $mobileSupport && ( 'icon_buttons' == self.config.mobileMenuToggleStyle || 'fixed_top' == self.config.mobileMenuToggleStyle ) ) {
						checks = true;
					} else {
						checks = false;
					}
				}

				if ( checks && pastheader() ) {

					if ( ! $isShrunk && $isSticky ) {
						$header.addClass( 'sticky-header-shrunk' );
						$isShrunk = true;
					}

				} else {

					$header.removeClass( 'sticky-header-shrunk' );
					$isShrunk = false;

				}

			}

			// Set sticky
			function setSticky() {

				// Already stuck
				if ( $isSticky ) {
					return;
				}

				// Custom Sticky logo
				swapLogo();

				// Add wrap class and toggle sticky class
				$stickyWrap
					.css( 'height', $headerHeight )
					.removeClass( 'not-sticky' )
					.addClass( 'is-sticky' );

				// Tweak header
				$header.removeClass( 'dyn-styles' ).css( {
					'top'       : self.stickyOffset(),
					'width'     : $stickyWrap.width()
				} );

				// Add transform go class
				if ( $header.hasClass( 'transform-prepare' ) ) {
					$header.addClass( 'transform-go' );
				}

				// Set sticky to true
				$isSticky = true;

			}

			// Destroy actions
			function destroyActions() {

				// Reset logo
				swapLogo();

				// Remove sticky wrap height and toggle sticky class
				$stickyWrap.removeClass( 'is-sticky' ).addClass( 'not-sticky' );

				// Do not remove height on sticky header for shrink header incase animation isn't done yet
				if ( ! $header.hasClass( 'shrink-sticky-header' ) ) {
					$stickyWrap.css( 'height', '' );
				}

				// Reset header
				$header.addClass( 'dyn-styles' ).css( {
					'width' : '',
					'top'   : ''
				} ).removeClass( 'transform-go' );

				// Set sticky to false
				$isSticky = false;

				// Make sure shrink header is removed
				$header.removeClass( 'sticky-header-shrunk' ); // Fixes some bugs with really fast scrolling
				$isShrunk = false;

			}

			// Destroy sticky
			function destroySticky() {

				// Already unstuck
				if ( ! $isSticky ) {
					return;
				}

				if ( $customStart ) {
					$header.removeClass( 'transform-go' );
					if ( $isShrunk ) {
						$header.removeClass( 'sticky-header-shrunk' );
						$isShrunk = false;
					}
				} else {
					$header.removeClass( 'transform-prepare' );
				}

				destroyActions();

			}

			// On load check
			function initResizeSetSticky() {

				if ( ! $mobileSupport && pastBreakPoint() ) {
					destroySticky();
					$stickyWrap.removeClass( 'wpex-can-sticky' );
					$header.removeClass( 'transform-prepare' );
					return;
				}

				//$header.addClass( 'transform-go' );
				$stickyWrap.addClass( 'wpex-can-sticky' );

				if ( $isSticky ) {

					if ( ! $header.hasClass( 'shrink-sticky-header' ) ) {
						$stickyWrap.css( 'height', self.config.siteHeaderHeight );
					}

					$header.css( {
						'top'   : self.stickyOffset(),
						'width' : $stickyWrap.width()
					} );

				} else {

					if ( self.config.windowTop > start_position() && 0 !== self.config.windowTop ) {
						setSticky();
					} else {
						destroySticky();
					}

				}

				if ( maybeShrink ) {
					shrink();
				}

			}

			// On scroll function
			function onScroll() {

				// Disable on mobile devices
				if ( ! $stickyWrap.hasClass( 'wpex-can-sticky' ) ) {
					return;
				}

				// Animate scroll with custom start
				if ( $customStart ) {
					transformPrepare();
				}

				// Destroy sticky at top
				if ( 0 === self.config.windowTop ) {
					destroySticky();
					return;
				}

				// Set or destroy sticky
				if ( self.config.windowTop >= start_position() ) {
					setSticky();
				} else {
					destroySticky();
				}

				// Shrink
				if ( maybeShrink ) {
					shrink();
				}

			}

			// Fire on init
			initResizeSetSticky();

			// Fire onscroll event
			$window.scroll( function() {
				if ( self.config.$hasScrolled ) {
					onScroll();
				}
			} );

			// Fire onResize
			$window.resize( function() {
				if ( self.config.widthChanged || self.config.heightChanged ) {
					initResizeSetSticky();
				}
			} );

			// Destroy and run onResize function on orientation change
			$window.on( 'orientationchange' , function() {
				destroySticky();
				initResizeSetSticky();
			} );

		},

		/**
		 * Sticky Header Menu.
		 */
		stickyHeaderMenu: function() {
			var self = this;

			// Return if disabled
			if ( ! self.config.hasStickyNavbar ) {
				return;
			}

			// Main vars
			var $navWrap    = self.config.$siteNavWrap,
				$isSticky   = false,
				$window     = self.config.$window,
				$stickyWrap = $( '<div id="site-navigation-sticky-wrapper" class="wpex-sticky-navigation-holder not-sticky"></div>' );

			// Define sticky wrap
			$navWrap.wrapAll( $stickyWrap );
			$stickyWrap = $( '#site-navigation-sticky-wrapper' );

			// Add offsets
			var $stickyWrapTop = $stickyWrap.offset().top,
				$stickyOffset  = self.stickyOffset(),
				$setStickyPos  = $stickyWrapTop - $stickyOffset;

			// Shrink header function
			function setSticky() {

				// Already sticky
				if ( $isSticky ) {
					return;
				}

				// Add wrap class and toggle sticky class
				$stickyWrap
					.css( 'height', self.config.$siteNavWrap.outerHeight() )
					.removeClass( 'not-sticky' )
					.addClass( 'is-sticky' );

				// Add CSS to topbar
				$navWrap.css( {
					'top'   : self.stickyOffset(),
					'width' : $stickyWrap.width()
				} );

				// Remove header dynamic styles
				self.config.$siteHeader.removeClass( 'dyn-styles' );

				// Update shrunk var
				$isSticky = true;

			}

			// Un-Shrink header function
			function destroySticky() {

				// Not shrunk
				if ( ! $isSticky ) {
					return;
				}

				// Remove sticky wrap height and toggle sticky class
				$stickyWrap
					.css( 'height', '' )
					.removeClass( 'is-sticky' )
					.addClass( 'not-sticky' );

				// Remove navbar width
				$navWrap.css( {
					'width' : '',
					'top'   : ''
				} );

				// Re-add dynamic header styles
				self.config.$siteHeader.addClass( 'dyn-styles' );

				// Update shrunk var
				$isSticky = false;

			}

			// On load check
			function initResizeSetSticky() {

				if ( self.config.viewportWidth <= wpexLocalize.stickyNavbarBreakPoint ) {
					destroySticky();
					$stickyWrap.removeClass( 'wpex-can-sticky' );
					return;
				}

				$stickyWrap.addClass( 'wpex-can-sticky' );

				if ( $isSticky ) {
					$navWrap.css( 'width', $stickyWrap.width() );
				} else {
					if ( self.config.windowTop >= $setStickyPos && 0 !== self.config.windowTop ) {
						setSticky();
					} else {
						destroySticky();
					}

				}

			}

			// Sticky check / enable-disable
			function onScroll() {

				if ( ! $stickyWrap.hasClass( 'wpex-can-sticky' ) ) {
					return;
				}

				// Destroy sticky at top and prevent sticky at top (since its already at top)
				if ( 0 === self.config.windowTop ) {
					if ( $isSticky ) {
						destroySticky();
					}
					return;
				}

				// Sticky menu
				if ( self.config.windowTop >= $setStickyPos ) {
					setSticky();
				} else {
					destroySticky();
				}

			}

			// Fire on init
			initResizeSetSticky();

			// Fire onscroll event
			$window.scroll( function() {
				if ( self.config.$hasScrolled ) {
					onScroll();
				}
			} );

			// Fire onResize
			$window.resize( function() {
				initResizeSetSticky();
			} );

			// Fire resize on flip
			$window.on( 'orientationchange' , function() {
				destroySticky();
				initResizeSetSticky();
			} );

		},

		/**
		 * Contact form 7 switch preloader for txt.
		 */
		ctf7Preloader: function() {

			// Return if disabled
			if ( ! wpexLocalize.altercf7Prealoader ) {
				return;
			}

			// Forms
			var $forms = $( 'form.wpcf7-form' );

			// Loop through forms
			$forms.each( function() {

				var $this = $( this );

				// Find button
				var $button = $this.find( '.wpcf7-submit' );

				// Hide loader if button found
				if ( $button.length ) {

					// Hide preLoader
					$this.find( '.ajax-loader' ).remove();

					// Add font awesome spinner
					var $customLoader = $( '<span class="ticon ticon-refresh ticon-spin wpex-wpcf7-loader"></span>' );
					$button.after( $customLoader );

					// Show new spinner on Send button click
					$button.on( 'click', function() {
						$customLoader.addClass( 'visible' );
					} );

					// Hide new spinner on result
					$( 'div.wpcf7' ).on( 'wpcf7:invalid wpcf7:spam wpcf7:mailsent wpcf7:mailfailed', function() {
						$customLoader.removeClass( 'visible' );
					} );

				}

			} );

		},

		/**
		 * Visual Composer Slider & Accordions.
		 */
		vcTabsTogglesJS: function() {
			var self = this;

			// Only needed when VC is enabled
			if ( ! this.config.$body.hasClass( 'wpb-js-composer' ) ) {
				return;
			}

			function onShow( e ) {

				var $target = $( e.target );
				var $tab    = '';

				if ( undefined !== $target.data( 'vc-target' ) ) {
					$tab = $( $target.data( 'vc-target' ) );
				} else {
					$tab = $( $target.attr( 'href' ) );
				}

				if ( $tab.length ) {

					// Sliders
					$tab.find( '.wpex-slider' ).each( function() {
						$( this ).sliderPro( 'update' );
					} );

					// Grids
					$tab.find( '.vcex-isotope-grid' ).each( function() {
						$( this ).isotope( 'layout' );
					} );

					// Filter links
					if ( typeof( window.vcexNavbarFilterLinks ) !== 'undefined' ) {
						window.vcexNavbarFilterLinks( $tab );
					}

				}

			}

			// Re-trigger/update things when opening VC tabs
			$( '.vc_tta-tabs' ).on( 'show.vc.tab', onShow );

			// Re-trigger slider on tabs change
			$( '.vc_tta-accordion' ).on( 'show.vc.accordion', onShow );

			// Tab clicks custom checks - due to issues with show.vc.tab not triggering on click in v5.4.3
			// Front-end only (breaks back-end tabs and not needed there apparently)
			self.config.$document.on( 'click.vc.tabs.data-api', '[data-vc-tabs]', function( e ) {

				if ( self.config.$body.hasClass( 'vc_editor' ) ) {
					return;
				}

				var $this = $( this );
				var $tab = $( $this.attr( 'href' ) );

				if ( $tab.length ) {

					// Aria labels
					var $tabsList = $this.closest( '.vc_tta-tabs-list' );
					$tabsList.find( '.vc_tta-tab > a' ).attr( 'aria-selected', 'false' );
					$this.parent( '.vc_tta-tab' ).find( '> a').attr( 'aria-selected', 'true' );

					// Sliders
					$tab.find( '.wpex-slider' ).each( function() {
						$( this ).sliderPro( 'update' );
					} );

					// Grids
					$tab.find( '.vcex-isotope-grid' ).each( function() {
						$( this ).isotope( 'layout' );
					} );

				}

			} );

		},

		/**
		 * Visual Composer Accessability fixes.
		 */
		vcAccessability: function() {

			if ( ! this.config.vcActive ) {
				return;
			}

			// Add tab index to toggles and toggle on enter
			var $toggles = $( '.vc_toggle .vc_toggle_title' );
			$toggles.each( function( index ) {
				var $this = $( this );
				$this.attr( 'tabindex', 0 );
				$this.on( 'keydown', function( e ) {
					if ( 13 == e.which ) {
						$this.trigger( 'click' );
					}
				} );
			} );

			// Add tabs arial and role attributes
			$( '.vc_tta-tabs-list' ).attr( 'role', 'tablist' );
			$( '.vc_tta-tab > a' ).attr( 'role', 'tab' ).attr( 'aria-selected', 'false' );
			$( '.vc_tta-tab.vc_active > a' ).attr( 'aria-selected', 'true' );
			$( '.vc_tta-panel-body' ).attr( 'role', 'tabpanel' );

			// Add Tab arrow navigation support
			var $tabContainers = $( '.vc_tta-container' );

			var tabClick = function( $thisTab, $allTabs, $tabPanels, i ) {
				$allTabs.attr( 'tabindex', -1 );
				$thisTab.attr( 'tabindex', 0 ).focus().click();
			};

			$tabContainers.each( function() {

				var $tabContainer = $( this ),
					$tabs         = $tabContainer.find( '.vc_tta-tab > a' ),
					$panels       = $tabContainer.find( '.vc_tta-panels' );

				$tabs.each( function( index ) {

					var $tab = $( this );

					if ( 0 == index ) {
						$tab.attr( 'tabindex', 0 );
					} else {
						$tab.attr( 'tabindex', -1 );
					}

					$tab.on( 'keydown', function( e ) {

						var $this        = $( this ),
							keyCode      = e.which,
							$nextTab     = $this.parent().next().is( 'li.vc_tta-tab' ) ? $this.parent().next().find( 'a' ) : false,
							$previousTab = $this.parent().prev().is( 'li.vc_tta-tab' ) ? $this.parent().prev().find( 'a' ) : false,
							$firstTab    = $this.parent().parent().find( 'li.vc_tta-tab:first' ).find( 'a' ),
							$lastTab     = $this.parent().parent().find( 'li.vc_tta-tab:last' ).find( 'a' );

						switch( keyCode ) {

						// Left/Up
						case 37 :
						case 38 :
							e.preventDefault();
							e.stopPropagation();
							if ( ! $previousTab) {
								tabClick( $lastTab, $tabs, $panels );
							} else {
								tabClick( $previousTab, $tabs, $panels );
							}
						break;

						// Right/Down
						case 39 :
						case 40 :
							e.preventDefault();
							e.stopPropagation();
							if ( ! $nextTab ) {
								tabClick( $firstTab, $tabs, $panels );
							} else {
								tabClick( $nextTab, $tabs, $panels );
							}
						break;

						// Home
						case 36 :
							e.preventDefault();
							e.stopPropagation();
							tabClick( $firstTab, $tabs, $panels );
							break;
						// End
						case 35 :
							e.preventDefault();
							e.stopPropagation();
							tabClick( $lastTab, $tabs, $panels );
						break;

						// Enter/Space
						case 13 :
						case 32 :
							e.preventDefault();
							e.stopPropagation();
						break;

						} // end switch

					} );

				} );

			} );

		},

		/**
		 * Creates accordion menu.
		 */
		menuAccordion: function( el ) {

			var dropDownParents = el.find( '.menu-item-has-children, .sidr-class-menu-item-has-children' );

			// Add toggle arrows
			dropDownParents.each( function() {
				var $link = $( this ).children( 'a' ),
					ariaOpen = wpexLocalize.i18n.openSubmenu.replace( '%s', $link.text() );
				$link.append( '<div class="wpex-open-submenu" tabindex="0" role="button" aria-haspopup="true" aria-expanded="false" aria-label="' + ariaOpen + '"><span class="ticon ticon-angle-down" aria-hidden="true"></span></div>' );
			} );

			// Dropdowns
			var $subToggleBtn = $( '.menu-item-has-children > a > .wpex-open-submenu, .sidr-class-menu-item-has-children > a > .wpex-open-submenu', el );

			function subDropdowns( $arrow ) {

				var $parentLi = $arrow.closest( 'li' ),
					$link     = $parentLi.children( 'a' ),
					linkText  = $link.text();

				// Close items
				if ( $parentLi.hasClass( 'active' ) ) {
					$arrow.attr( 'aria-expanded', 'false' ).attr( 'aria-label', wpexLocalize.i18n.openSubmenu.replace( '%s', linkText ) );
					$parentLi.removeClass( 'active' );
					$parentLi.find( 'li' ).removeClass( 'active' );
					$parentLi.find( 'ul' ).stop( true, true ).slideUp( 'fast' );
				}

				// Open items
				else {
					$arrow.attr( 'aria-expanded', 'true' ).attr( 'aria-label', wpexLocalize.i18n.closeSubmenu.replace( '%s', linkText ) );
					var $allParentLis = $parentLi.parents( 'li' );
					$( '.menu-item-has-children', el )
						.not( $allParentLis )
						.removeClass( 'active' )
						.children( 'ul' )
						.stop( true, true )
						.slideUp( 'fast' );
					$parentLi.addClass( 'active' ).children( 'ul' ).stop( true, true ).slideDown( 'fast' );
				}

			}

			$subToggleBtn.on( 'click', function() {
				subDropdowns( $( this ) );
				return false;
			} );

			// Toggle on enter
			$subToggleBtn.on( 'keydown', function( e ) {
				if ( ( e.keyCode === 13 && ! e.shiftKey ) ) {
					subDropdowns( $( this ) );
				}
			} );

		},

		/**
		 * Set correct focus states for custom elements
		 *
		 * @param {HTMLElement} el
		 */
		focusOnElement: function( el ) {

			var focusableItems     = el.find( 'select, input, textarea, button, a' ).filter( ':visible' ),
				firstFocusableItem = focusableItems.first(),
				lastFocusableItem  = focusableItems.last();

			// Add initial focus
			firstFocusableItem.focus();

			// Redirect last tab to first input.
			lastFocusableItem.on( 'keydown', function ( e ) {
				if ( ( e.keyCode === 9 && ! e.shiftKey ) ) {
					e.preventDefault();
					firstFocusableItem.focus();
				}
			} );

			// Redirect first shift+tab to last input.
			firstFocusableItem.on( 'keydown', function ( e ) {
				if ( ( e.keyCode === 9 && e.shiftKey ) ) {
					e.preventDefault();
					lastFocusableItem.focus();
				}
			} );

		},

		/**
		 * Parses data to check if a value is defined in the data attribute and if not returns the fallback..
		 */
		pData: function( val, fallback ) {
			return ( typeof val !== 'undefined' ) ? val : fallback;
		},

		/**
		 * make sure el exists and isn't empty.
		 */
		isEmpty: function( el ) {
			return ! el.length || ! $.trim( el.html() ).length;
		},

		/**
		 * Grabs content and inserts into another element
		 */
		insertExtras: function( el, target, method ) {
			if ( ! target.length || this.isEmpty( el ) ) {
				return;
			}

			if ( 'append' == method ) {
				target.append( el );
			}

			if ( 'prepend' == method ) {
				target.prepend( el );
			}

			el.removeClass( 'wpex-hidden' ); // remove hidden class

		},

		/**
		 * Returns extension from URL
		 */
		getUrlExtension: function( url ) {
			var ext = url.split( '.' ).pop().toLowerCase();
			var extra = ext.indexOf( '?' ) !== -1 ? ext.split( '?' ).pop() : '';
			ext = ext.replace( extra, '' );
			return ext.replace( '?', '' );
		},

		/**
		 * Check if window has scrolled to bottom of element
		 */
		scrolledToBottom: function( elem ) {
			return this.config.windowTop >= elem.offset().top + elem.outerHeight() - window.innerHeight;
		},

		/**
		 * Parses old lightbox data attribute and returns object
		 */
		parseObjectLiteralData: function( data ) {
			var properties = data.split( ',' );
			var obj = {};
			$.each(properties, function(index, item) {
				var tup = item.split(':');
				obj[tup[0]] = tup[1];
			} );
			return obj;

		}

	};

	// Start things up
	wpex.init();

} ) ( jQuery );