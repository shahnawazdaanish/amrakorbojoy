<?php
session_start();
$mode = get_option('n_testmode');
$pub_key = get_option('n_publickey');
$priv_key = get_option('n_privatekey');
$merchant_id = get_option('nmerchantid');
global $wpdb;
$table_name = $wpdb->prefix . 'nagad_payment';

if(isset($_GET['status_nagad'])) {
    $payTrxID = sanitize_text_field($_GET['status_nagad']);
    if ($payTrxID) {
        $results = $wpdb->get_results("SELECT * FROM $table_name WHERE trxid = '$payTrxID' ", ARRAY_A);
        $payment = null;
        if($results) {
            $payment = $results[0];
        }
        include_once "invoice.php";
    } else {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
    }
}
