<?php
session_start();
global $wp;
$img = get_template_directory_uri() . "/SSLCommerz.png";
$store_id = get_option('store');
$store_pass = get_option('password');
$mode = get_option('testmode');
$current_url = home_url(add_query_arg(array(), $wp->request));
$_SESSION['CUS_HISTORY']['SITE_URL'] = $current_url;

if ($mode == 'on') {
    $request_url = 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php';
} else {
    $request_url = 'https://securepay.sslcommerz.com/gwprocess/v4/api.php';
}
$post_data = array();
$post_data['store_id'] = $store_id;
$post_data['store_passwd'] = $store_pass;
$post_data['currency'] = 'BDT';
$post_data['total_amount'] = '10'; // str_replace(',', '', $amount);
$post_data['tran_id'] = $trxid = "sslc" . uniqid();

$post_data['cus_name'] = $name;
$post_data['cus_email'] = $email;
$post_data['cus_phone'] = $phone;
$post_data['cus_add1'] = 'adsfadsf';
$post_data['cus_country'] = $country;
$post_data['cus_city'] = $city;
$post_data['cus_postcode'] = '12';
$post_data['product_category'] = 'donation';
$post_data['product_name'] = 'donation';
$post_data['product_profile'] = 'non-physical-goods';
$post_data['emi_option'] = '0';
$post_data['shipping_method'] = 'No';
$post_data['num_of_item'] = '1';


$post_data['success_url'] = get_site_url() . "/index.php?sslcsuccess";
$post_data['fail_url'] = get_site_url() . "/index.php?sslcfail";
$post_data['cancel_url'] = get_site_url() . "/index.php?sslccancel";

// var_dump($post_data);

$handle = curl_init();
curl_setopt($handle, CURLOPT_URL, $request_url);
curl_setopt($handle, CURLOPT_POST, 1);
curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
$content = curl_exec($handle);
$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);


if ($code == 200 && !(curl_errno($handle))) {
    curl_close($handle);
    $sslcommerzResponse = $content;
# PARSE THE JSON RESPONSE
    $sslcz = json_decode($sslcommerzResponse, true);
    // var_dump($sslcz);
    if (isset($sslcz['status']) && $sslcz['status'] == 'SUCCESS') {

        if (isset($sslcz['GatewayPageURL']) && $sslcz['GatewayPageURL'] != '') {
            if (isset($trxid)) {
                // echo $trxid;
                global $wpdb;
                $table_name = $wpdb->prefix . 'sslcommerz_payment';
                $field_Data = array(
                    'trxid' => $trxid,
                    'tran_status' => 'Pending',
                    'card_type' => '',
                    'total_amount' => $amount,
                    'product_name' => $post_data['product_name'],
                    'cus_name' => $name,
                    'cus_email' => $email,
                    'cus_phone' => $phone,
                    'cus_address' => 'adfadsf',
                    'cus_country' => $country,
                    'cus_city' => $city,
                    'cus_postcode' => '1',
                );
// $field_Data_type = array('%s', '%s', '%s', '%s', '%f', '%s', '%s','%s','%s','%s');
                $insert = $wpdb->insert($table_name, $field_Data);
//var_dump($insert);
//$wpdb->print_error();
echo $wpdb->last_error;
            }
            $_SESSION['CUS_HISTORY']['TRANID'] = $trxid;
            $_SESSION['CUS_HISTORY']['CUS_NAME'] = $_POST['cus_name'];
            $_SESSION['CUS_HISTORY']['CUS_EMAIL'] = $_POST['cus_email'];
            $_SESSION['CUS_HISTORY']['CUS_PHONE'] = $_POST['cus_phone'];
            $_SESSION['CUS_HISTORY']['CUS_ADD'] = $_POST['cus_add'];
            $_SESSION['CUS_HISTORY']['CUS_COUNTRY'] = $_POST['cus_country'];
            $_SESSION['CUS_HISTORY']['CUS_STATE'] = $_POST['cus_city'];
            $_SESSION['CUS_HISTORY']['CUS_CURRENCY'] = $_POST['currency'];

//            echoResp('adfads');
            echoResp($sslcz['GatewayPageURL']);
//                    echo '<meta http-equiv="refresh" content="0; url=' . $sslcz['GatewayPageURL'] . '" />';
#header("Location: " . $sslcz['GatewayPageURL']);
            exit;
        } else {
            echoResp("No redirect URL found", true);
        }
    } else {
        echoResp("Invalid Credentials", true);
        exit;
        echo "Invalid Credential!";
    }
} else {
    curl_close($handle);
    echoResp("FAILED TO CONNECT WITH SSLCOMMERZ API", true);
    exit;
}

function echoResp($data, $error=false) {
    if(is_string($data)) {
        echo json_encode([
           'status' => $error ? 'fail' : 'success',
           'data' => $data
        ]);
    } else if(is_array($data)) {
        $data['status'] = $error ? 'fail' : 'success';
        echo json_encode($data);
    }
    wp_die();
}
?>