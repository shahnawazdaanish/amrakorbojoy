<?php
/**
 * Child theme functions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development
 * and http://codex.wordpress.org/Child_Themes), you can override certain
 * functions (those wrapped in a function_exists() call) by defining them first
 * in your child theme's functions.php file. The child theme's functions.php
 * file is included before the parent theme's file, so the child theme
 * functions would be used.
 *
 * Text Domain: wpex
 * @link http://codex.wordpress.org/Plugin_API
 *
 */

/**
 * Load the parent style.css file
 *
 * @link http://codex.wordpress.org/Child_Themes
 */

function total_child_enqueue_parent_theme_style()
{

    // Dynamically get version number of the parent stylesheet (lets browsers re-cache your stylesheet when you update your theme)
    $theme = wp_get_theme('Total');
    $version = $theme->get('Version');

    // Load the stylesheet
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css', array(), $version);
    wp_enqueue_style('parent-style4', get_stylesheet_directory_uri() . '/fonts/material-icon/css/material-design-iconic-font.min.css', array(), $version);
    wp_enqueue_style('parent-style3', get_stylesheet_directory_uri() . '/vendor/nouislider/nouislider.min.css', array(), $version);
    wp_enqueue_style('parent-style2', get_stylesheet_directory_uri() . '/form.css', array(), $version);
    wp_enqueue_script('parent-script1', get_stylesheet_directory_uri() . '/vendor/nouislider/nouislider.min.js', array(), $version);
    wp_enqueue_script('parent-script2', get_stylesheet_directory_uri() . '/vendor/wnumb/wNumb.js', array(), $version);
    wp_enqueue_script('parent-script3', get_stylesheet_directory_uri() . '/vendor/jquery-validation/dist/jquery.validate.min.js', array(), $version);
    wp_enqueue_script('parent-script4', get_stylesheet_directory_uri() . '/vendor/jquery-validation/dist/additional-methods.min.js', array(), $version);
    wp_enqueue_script('parent-script5', get_stylesheet_directory_uri() . '/script.js', array(), $version);


    wp_localize_script('mylib', 'WPURLS', array('siteurl' => get_option('siteurl')));
    wp_localize_script('parent-script5', 'WPURLS', array('adminurl' => admin_url('admin-ajax.php')));

}

add_action('wp_enqueue_scripts', 'total_child_enqueue_parent_theme_style');


add_action('wp_ajax_donation_submit', 'users_details_callback');
add_action('wp_ajax_nopriv_donation_submit', 'users_details_callback');

function users_details_callback()
{
    global $wpdb;

    $request = $_POST;
    $response = array();

    $name = sanitize_text_field($request['name']);
    $email = sanitize_text_field($request['email']);
    $phone = sanitize_text_field($request['phone_number']);
    $city = sanitize_text_field($request['city']);
    $country = sanitize_text_field($request['country']);
    $payment = sanitize_text_field($request['payment']);
    $amount = sanitize_text_field($request['amount']);
    if ($name == '' || !is_email($email) || $phone == '') {
        echo json_encode(['eroor']);
        wp_die();
    }

    if ($payment == 'sslcz') {
        include_once "SSLCommerz.php";
        wp_die();
    } else if ($payment == 'nagad') {
        require_once "Nagad.php";
        $nagad = new Nagad();
        $nagad->initiatePayment($amount, $name, $email, $phone, $city, $country);
        wp_die();
    } else {
        echo json_encode(['eroor']);
        wp_die();
    }


    echo json_encode($request);
    wp_die();
}
