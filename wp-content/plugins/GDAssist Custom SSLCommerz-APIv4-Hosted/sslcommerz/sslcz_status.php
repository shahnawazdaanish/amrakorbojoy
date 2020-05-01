<?php
session_start();
global $wpdb;
$table_name = $wpdb->prefix . 'sslcommerz_payment';

if(isset($_GET['status_sslcz'])) {
    $payTrxID = sanitize_text_field($_GET['status_sslcz']);
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
