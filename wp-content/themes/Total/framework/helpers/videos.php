<?php
/**
 * Helper functions for getting and displaying videos
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 * @version 4.9.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return correct video embed url
 *
 * @since 4.3
 */
function wpex_get_video_embed_url( $url = '' ) {

	// Return if no url
	if ( ! $url ) {
		return;
	}

	// Sanitize youtube links
	if ( strpos( $url, 'youtu' ) !== false ) {

		// Covert only if not already in correct format
		if ( strpos( $url, 'embed' ) === false ) {

			// Check for youtu.be
			$url = str_replace( 'youtu.be/', 'youtube.com/watch?v=', $url );

			// Convert url
			$url_string = parse_url( $url, PHP_URL_QUERY );
			parse_str( $url_string, $args );
			if ( ! empty ( $args['v'] ) ) {
				$url = 'youtube.com/embed/' . $args['v'];
			}

		}

	}

	// Sanitize vimeo links
	elseif ( strpos( $url, 'vimeo' ) !== false ) {

		// Covert only if not already in correct format
		if ( strpos( $url, 'player.vimeo' ) === false ) {

			// Get the ID
			$video_id = str_replace( 'http://vimeo.com/', '', $url );
			if ( ! is_numeric( $video_id ) ) {
				$video_id = str_replace( 'https://vimeo.com/', '', $url );
			} elseif ( ! is_numeric( $video_id ) ) {
				$video_id = str_replace( 'http://www.vimeo.com/', '', $url );
			} elseif ( ! is_numeric( $video_id ) ) {
				$video_id = str_replace( 'https://www.vimeo.com/', '', $url );
			}

			// Return embed URL
			if ( is_numeric( $video_id ) ) {
				$url = 'player.vimeo.com/video/'. $video_id;
			}

		}

	}

	// Escape URL and set to correct URL scheme
	$url = $url ? set_url_scheme( esc_url( $url ) ) : '';

	// Add parameters
	$params = apply_filters( 'wpex_get_video_embed_url_params', array(), $url );
	$params_string = '';

	// Add params
	if ( $params ) {

		if ( false === strpos( $url, '?' ) ) {
			$url = $url . '?cparams=1';
		}

		// Loop through and check vendors
		foreach ( $params as $vendor => $params ) {

			// Youtube fixes
			$vendor = ( 'youtube' == $vendor ) ? 'yout' : $vendor;

			// Check initial video url for vendor (youtube/vimeo/etc)
			if ( false !== strpos( $url, $vendor ) ) {

				// Loop through and add params to variable
				foreach ( $params as $key => $val ) {

					$params_string .= '&' . esc_attr( $key ) . '=' . esc_attr( $val );

				}

			}

		}

	}

	// Return url
	return $url . $params_string;

}

/**
 * Adds the sp-video class to iFrames
 *
 * @since 1.0.0
 */
function wpex_add_sp_video_to_oembed( $oembed ) {
	return str_replace( '<iframe', '<iframe class="sp-video"', $oembed );
}

/**
 * Returns post video oEmbed url
 *
 * @since 4.0
 */
function wpex_get_post_video_oembed_url( $post_id = '' ) {

	// Define video
	$video = '';

	// Get correct ID
	$post_id = $post_id ? $post_id : get_the_ID();

	// Check post video meta
	if ( $meta = get_post_meta( $post_id, 'wpex_post_video', true ) ) {
		$video = $meta;
	}

	// Check oembed meta
	elseif ( $meta = get_post_meta( $post_id, 'wpex_post_oembed', true ) ) {
		$video = $meta;
	}

	// Return video
	return $video;

}

/**
 * Echo post video
 *
 * @since 2.0.0
 */
function wpex_post_video( $post_id ) {
	echo wpex_get_post_video( $post_id );
}

/**
 * Returns post video
 *
 * @since 2.0.0
 */
function wpex_get_post_video( $post_id = '' ) {

	// Define video variable
	$video = '';

	// Get correct ID
	$post_id = $post_id ? $post_id : get_the_ID();

	// Embed
	if ( $meta = get_post_meta( $post_id, 'wpex_post_video_embed', true ) ) {
		$video = $meta;
	}

	// Check for self-hosted first
	elseif ( $meta = get_post_meta( $post_id, 'wpex_post_self_hosted_media', true ) ) {
		$video = $meta;
	}

	// Check for wpex_post_video custom field
	elseif ( $meta = get_post_meta( $post_id, 'wpex_post_video', true ) ) {
		$video = $meta;
	}

	// Check for post oembed
	elseif ( $meta = get_post_meta( $post_id, 'wpex_post_oembed', true ) ) {
		$video = $meta;
	}

	// Check old redux custom field last
	elseif ( $meta = get_post_meta( $post_id, 'wpex_post_self_hosted_shortcode_redux', true ) ) {
		$video = $meta;
	}

	// Apply filters & return
	return apply_filters( 'wpex_get_post_video', $video );

}

/**
 * Echo post video HTML
 *
 * @since 2.0.0
 */
function wpex_post_video_html( $video = '' ) {
	echo wpex_get_post_video_html( $video );
}

/**
 * Returns post video HTML
 *
 * @since 2.0.0
 */
function wpex_get_post_video_html( $video = '' ) {

	// Get video
	$video = $video ? $video : wpex_get_post_video();

	// Return if video is empty
	if ( empty( $video ) ) {
		return;
	}

	// Check post format for standard post type
	if ( 'post' == get_post_type() && 'video' != get_post_format() ) {
		return;
	}

	// Check if self-hosted to speed things up
	$self_hosted = ( $video == get_post_meta( get_the_ID(), 'wpex_post_self_hosted_media', true ) ) ? true : false;

	// Check first if it's an iFrame and if so return right away
	if ( strpos( $video, 'iframe' ) !== false ) {

		// Add responsive wrap to youtube/vimeo iframes
		if ( strpos( $video, 'youtu' ) !== false || strpos( $video, 'vimeo' ) !== false ) {
			if ( apply_filters( 'wpex_responsive_video_wrap', true ) ) {
				return '<div class="responsive-video-wrap">' . $video . '</div>';
			} else {
				return $video;
			}
		}

	}

	// Get oembed code if not self hosted
	$oembed = ! $self_hosted ? wpex_video_oembed( $video ) : '';

	// Return oembed
	if ( $oembed ) {

		if ( apply_filters( 'wpex_responsive_video_wrap', true ) ) {
			return '<div class="responsive-video-wrap">' . $oembed . '</div>';
		} else {
			return $oembed;
		}

	}

	// Display self-hosted video
	else {

		$video = is_numeric( $video ) ? wp_get_attachment_url( $video ) : $video;

		if ( filter_var( esc_url( $video ), FILTER_VALIDATE_URL ) && function_exists( 'wp_video_shortcode' ) ) {
			return wp_video_shortcode( array( 'src' => $video, 'width' => '9999' ) );
		} else {
			return do_shortcode( wp_strip_all_tags( $video ) );
		}

	}

}

/**
 * Generate custom oEmbed output
 *
 * @since 3.6.0
 */
function wpex_video_oembed( $video = '', $classes = '', $params = array() ) {

	// Video required
	if ( ! $video ) {
		return;
	}

	// Define output
	$output = '';

	// Sanitize URl
	$video = esc_url( $video );

	// Fetch oemebed output
	$html = wp_oembed_get( $video );

	// Return if there is an error fetching the oembed code
	if ( is_wp_error( $html ) ) {
		return;
	}

	// Add classes
	if ( $classes ) {

		// Class attribute already added already via filter
		if ( strpos( 'class="', $html ) ) {
			$html = str_replace( 'class="', 'class="'. $classes .' ', $html );
		}

		// No class attribute found so lets add new one with our custom classes
		else {
			$html = str_replace( '<iframe', '<iframe class="'. $classes .'"', $html );
		}

	}

	// Apply filters for params
	$params = apply_filters( 'wpex_video_oembed_params', $params );

	// Add params
	if ( $params ) {

		// Define empty params string
		$params_string = '';

		// Loop through and check vendors
		foreach ( $params as $vendor => $params ) {

			// Youtube fixes
			$vendor = ( 'youtube' == $vendor ) ? 'yout' : $vendor;

			// Check initial video url for vendor (youtube/vimeo/etc)
			if ( strpos( $video, $vendor ) ) {

				// Loop through and add params to variable
				foreach ( $params as $key => $val ) {
					$params_string .= '&' . esc_attr( $key ) . '=' . esc_attr( $val );
				}

			}

		}

		// Add params
		if ( $params_string ) {
			$html = str_replace( '?feature=oembed', '?feature=oembed'. $params_string, $html );
		}

	}

	// Return output
	return $html;

}