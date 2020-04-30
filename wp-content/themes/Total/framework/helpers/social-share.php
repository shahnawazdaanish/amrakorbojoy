<?php
/**
 * Social share functions
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
 * Checks if social share is enabled.
 *
 * @since 4.0
 */
function wpex_has_social_share() {

    // Disable if password protected
    if ( post_password_required() ) {
        return;
    }

    // Disabled by default
    $bool = false;

    // Get current post ID
    $post_id = wpex_get_current_post_id();

    // Check page settings to overrides theme mods and filters
    if ( $post_id ) {

        // Meta check
        if ( $meta = get_post_meta( $post_id, 'wpex_disable_social', true ) ) {

            // Check if disabled by meta options
            if ( 'on' == $meta ) {
                return false;
            }

            // Return true if enabled via meta option
            if ( 'enable' == $meta ) {
                return true;
            }

        }

        // Dynamic template check
        if ( wpex_post_has_dynamic_template() ) {
            return true; // so that the post content module works correctly.
        }

        // Check if social share is enabled for specific post types
        if ( 'product' == get_post_type() ) {
            $bool = wpex_get_mod( 'social_share_woo', false ) ? true : false;
        } else {
            $blocks = wpex_single_blocks();
            if ( $blocks && is_array( $blocks ) ) {
                foreach ( $blocks as $block ) {
                    if ( ( 'social_share' == $block || 'share' == $block ) ) {
                        $bool = true;
                    }
                }
            }
        }

    }

    // Apply filters and return
    return apply_filters( 'wpex_has_social_share', $bool );

}

/**
 * Checks if there are any social sharing sites enabled.
 *
 * @since 1.0.0
 */
function wpex_has_social_share_sites() {
    if ( wpex_social_share_sites() ) {
        return true;
    }
}

/**
 * Checks if the social sharing style supports a custom heading.
 *
 * @since 1.0.0
 */
function wpex_social_sharing_supports_heading() {
    $bool = false;
    if ( wpex_social_share_sites() && 'horizontal' == wpex_social_share_position() ) {
        $bool = true;
    }
    $bool = apply_filters( 'wpex_social_sharing_supports_heading', $bool );
    return $bool;
}

/**
 * Returns social sharing sites
 *
 * @since 2.0.0
 */
function wpex_social_share_sites() {
    $sites = wpex_get_mod( 'social_share_sites', array( 'twitter', 'facebook', 'linkedin', 'email' ) );
    $sites = apply_filters( 'wpex_social_share_sites', $sites );
    if ( $sites && ! is_array( $sites ) ) {
        $sites = explode( ',', $sites );
    }
    return $sites;
}

/**
 * Get social links array
 *
 * @since 4.5.5
 */
function wpex_get_social_items() {
    return apply_filters( 'wpex_get_social_items', array(
        'twitter' => array(
            'li_class'   => 'wpex-twitter',
            'icon_class' => 'ticon ticon-twitter',
            'label'      => esc_html__( 'Tweet', 'total' ),
            'site'       => 'Twitter',
        ),
        'facebook' => array(
            'li_class'   => 'wpex-facebook',
            'icon_class' => 'ticon ticon-facebook',
            'label'      => esc_html__( 'Share', 'total' ),
            'site'       => 'Facebook',
        ),
        'pinterest' => array(
            'li_class'   => 'wpex-pinterest',
            'icon_class' => 'ticon ticon-pinterest',
            'label'      => esc_html__( 'Pin It', 'total' ),
            'site'       => 'Pinterest',
        ),
        'linkedin' => array(
            'li_class'   => 'wpex-linkedin',
            'icon_class' => 'ticon ticon-linkedin',
            'label'      => esc_html__( 'Share', 'total' ),
            'site'       => 'LinkedIn',
        ),
        'email' => array(
            'li_class'   => 'wpex-email',
            'icon_class' => 'ticon ticon-envelope',
            'label'      => esc_html__( 'Email', 'total' ),
            'site'       => 'Email',
        ),
    ) );
}

/**
 * Returns correct social share position
 *
 * @since 2.0.0
 */
function wpex_social_share_position() {
    $position = wpex_get_mod( 'social_share_position' );
    $position = $position ? $position : 'horizontal';
    return apply_filters( 'wpex_social_share_position', $position );
}

/**
 * Returns correct social share style
 *
 * @since 2.0.0
 */
function wpex_social_share_style() {
    $style = wpex_get_mod( 'social_share_style' );
    if ( function_exists( 'is_product' ) && is_product() ) {
        $style = wpex_get_mod( 'woo_product_social_share_style', $style );
    }
    $style = $style ? $style : 'flat'; // style can't be empty
    return apply_filters( 'wpex_social_share_style', $style );
}

/**
 * Returns the social share heading
 *
 * @since 2.0.0
 */
function wpex_social_share_heading() {
    $heading = esc_html__( 'Share This', 'total' ); // default heading
    $heading = wpex_get_translated_theme_mod( 'social_share_heading', $heading ); // global customizer setting
    if ( function_exists( 'is_product' ) && is_product() ) {
        $heading = wpex_get_translated_theme_mod( 'woo_product_social_share_heading', $heading );
    }
    return apply_filters( 'wpex_social_share_heading', $heading );
}

/**
 * Check if social share labels should display
 *
 * @since 4.9.8
 */
function wpex_social_share_has_labels() {
    $bool = wpex_get_mod( 'social_share_label', true );
    if ( function_exists( 'is_product' ) && is_product() ) {
        $bool = wpex_get_mod( 'woo_social_share_label', true );
    }
    return apply_filters( 'wpex_social_share_has_labels', $bool );
}

/**
 * Return social share data
 *
 * @since 4.5.5.1
 */
function wpex_get_social_share_data( $post_id = '', $sites = '' ) {

    $post_id = $post_id ? $post_id : wpex_get_current_post_id();
    $sites   = $sites ? $sites : wpex_social_share_sites();
    $url     = apply_filters( 'wpex_social_share_url', wpex_get_current_url() );

    $data = array();

    // Singular data
    if ( $post_id ) {

        $title = wpex_get_esc_title();

        if ( in_array( 'pinterest', $sites ) || in_array( 'linkedin', $sites ) ) {

            $summary = wpex_get_excerpt( array(
                'post_id'         => $post_id,
                'length'          => 30,
                'echo'            => false,
                'ignore_more_tag' => true,
                'more'            => '',
                'context'         => 'social_share',
            ) );

        }

    }

    // Most likely an archive
    else {
        $title   = get_the_archive_title();
        $summary = get_the_archive_description();
    }

    // Share source
    $source = apply_filters( 'wpex_social_share_data_source', home_url( '/' ) );
    $data['source'] = rawurlencode( esc_url( $source ) );

    // Share url
    $url = apply_filters( 'wpex_social_share_data_url', $url );
    $data['url'] = rawurlencode( esc_url( $url ) );

    // Share title
    $title = apply_filters( 'wpex_social_share_data_title', $title );
    $data['title'] = html_entity_decode( wp_strip_all_tags( $title ) );

    // Thumbnail
    if ( is_singular() && has_post_thumbnail() ) {
        $image = apply_filters( 'wpex_social_share_data_image', wp_get_attachment_url( get_post_thumbnail_id( $post_id ) ) );
        $data['image'] = rawurlencode( esc_url( $image ) );
    }

    // Add twitter handle
    if ( $handle = wpex_get_mod( 'social_share_twitter_handle' ) ) {
        $data['twitter-handle'] = esc_attr( $handle );
    }

    // Share summary
    if ( ! empty( $summary ) ) {
        $summary = apply_filters( 'wpex_social_share_data_summary', wp_strip_all_tags( strip_shortcodes( $summary ) ) );
        $data['summary'] = rawurlencode( html_entity_decode( $summary ) );
    }

    // Get WordPress SEO meta share values
    if ( defined( 'WPSEO_VERSION' ) ) {
        if ( $twitter_title = get_post_meta( $post_id, '_yoast_wpseo_twitter-title', true ) ) {
            $data['twitter-title'] = html_entity_decode( wp_strip_all_tags( $twitter_title ) );
        }
        if ( $twitter_desc = get_post_meta( $post_id, '_yoast_wpseo_twitter-description', true ) ) {
            if ( $twitter_title ) {
                $data['twitter-title'] = html_entity_decode( wp_strip_all_tags( $twitter_title . ': ' . $twitter_desc ) );
            } else {
                $data['twitter-title'] = $data['title'] . ':' . html_entity_decode( wp_strip_all_tags( $twitter_desc ) );
            }
        }
    }

    // Email data
    if ( in_array( 'email', $sites ) ) {
        $data['email-subject'] = apply_filters( 'wpex_social_share_data_email_subject', esc_html__( 'I wanted you to see this link', 'total' ) );
        $body = esc_html__( 'I wanted you to see this link', 'total' ) . ' '. rawurlencode( esc_url( $url ) );
        $data['email-body'] = apply_filters( 'wpex_social_share_data_email_body', $body );
    }

    // Specs
    $data['specs'] = 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600';

    return apply_filters( 'wpex_get_social_share_data', $data );

}