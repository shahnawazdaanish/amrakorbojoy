<?php

class Nagad
{
    public function __construct()
    {
        date_default_timezone_set('Asia/Dhaka');
    }

    function generateRandomString($length = 40)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function EncryptDataWithPublicKey($data)
    {
        $nPubKey = get_option('n_publickey');
//    $pgPublicKey = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAjBH1pFNSSRKPuMcNxmU5jZ1x8K9LPFM4XSu11m7uCfLUSE4SEjL30w3ockFvwAcuJffCUwtSpbjr34cSTD7EFG1Jqk9Gg0fQCKvPaU54jjMJoP2toR9fGmQV7y9fz31UVxSk97AqWZZLJBT2lmv76AgpVV0k0xtb/0VIv8pd/j6TIz9SFfsTQOugHkhyRzzhvZisiKzOAAWNX8RMpG+iqQi4p9W9VrmmiCfFDmLFnMrwhncnMsvlXB8QSJCq2irrx3HG0SJJCbS5+atz+E1iqO8QaPJ05snxv82Mf4NlZ4gZK0Pq/VvJ20lSkR+0nk+s/v3BgIyle78wjZP1vWLU4wIDAQAB";
        $public_key = "-----BEGIN PUBLIC KEY-----\n" . $nPubKey . "\n-----END PUBLIC KEY-----";
        $key_resource = openssl_get_publickey($public_key);
        openssl_public_encrypt($data, $crypttext, $key_resource);
        return base64_encode($crypttext);
    }

    function SignatureGenerate($data)
    {
        $nPrivKey = get_option('n_privatekey');
//    $merchantPrivateKey = "MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCJakyLqojWTDAVUdNJLvuXhROV+LXymqnukBrmiWwTYnJYm9r5cKHj1hYQRhU5eiy6NmFVJqJtwpxyyDSCWSoSmIQMoO2KjYyB5cDajRF45v1GmSeyiIn0hl55qM8ohJGjXQVPfXiqEB5c5REJ8Toy83gzGE3ApmLipoegnwMkewsTNDbe5xZdxN1qfKiRiCL720FtQfIwPDp9ZqbG2OQbdyZUB8I08irKJ0x/psM4SjXasglHBK5G1DX7BmwcB/PRbC0cHYy3pXDmLI8pZl1NehLzbav0Y4fP4MdnpQnfzZJdpaGVE0oI15lq+KZ0tbllNcS+/4MSwW+afvOw9bazAgMBAAECggEAIkenUsw3GKam9BqWh9I1p0Xmbeo+kYftznqai1pK4McVWW9//+wOJsU4edTR5KXK1KVOQKzDpnf/CU9SchYGPd9YScI3n/HR1HHZW2wHqM6O7na0hYA0UhDXLqhjDWuM3WEOOxdE67/bozbtujo4V4+PM8fjVaTsVDhQ60vfv9CnJJ7dLnhqcoovidOwZTHwG+pQtAwbX0ICgKSrc0elv8ZtfwlEvgIrtSiLAO1/CAf+uReUXyBCZhS4Xl7LroKZGiZ80/JE5mc67V/yImVKHBe0aZwgDHgtHh63/50/cAyuUfKyreAH0VLEwy54UCGramPQqYlIReMEbi6U4GC5AQKBgQDfDnHCH1rBvBWfkxPivl/yNKmENBkVikGWBwHNA3wVQ+xZ1Oqmjw3zuHY0xOH0GtK8l3Jy5dRL4DYlwB1qgd/Cxh0mmOv7/C3SviRk7W6FKqdpJLyaE/bqI9AmRCZBpX2PMje6Mm8QHp6+1QpPnN/SenOvoQg/WWYM1DNXUJsfMwKBgQCdtddE7A5IBvgZX2o9vTLZY/3KVuHgJm9dQNbfvtXw+IQfwssPqjrvoU6hPBWHbCZl6FCl2tRh/QfYR/N7H2PvRFfbbeWHw9+xwFP1pdgMug4cTAt4rkRJRLjEnZCNvSMVHrri+fAgpv296nOhwmY/qw5Smi9rMkRY6BoNCiEKgQKBgAaRnFQFLF0MNu7OHAXPaW/ukRdtmVeDDM9oQWtSMPNHXsx+crKY/+YvhnujWKwhphcbtqkfj5L0dWPDNpqOXJKV1wHt+vUexhKwus2mGF0flnKIPG2lLN5UU6rs0tuYDgyLhAyds5ub6zzfdUBG9Gh0ZrfDXETRUyoJjcGChC71AoGAfmSciL0SWQFU1qjUcXRvCzCK1h25WrYS7E6pppm/xia1ZOrtaLmKEEBbzvZjXqv7PhLoh3OQYJO0NM69QMCQi9JfAxnZKWx+m2tDHozyUIjQBDehve8UBRBRcCnDDwU015lQN9YNb23Fz+3VDB/LaF1D1kmBlUys3//r2OV0Q4ECgYBnpo6ZFmrHvV9IMIGjP7XIlVa1uiMCt41FVyINB9SJnamGGauW/pyENvEVh+ueuthSg37e/l0Xu0nm/XGqyKCqkAfBbL2Uj/j5FyDFrpF27PkANDo99CdqL5A4NQzZ69QRlCQ4wnNCq6GsYy2WEJyU2D+K8EBSQcwLsrI7QL7fvQ==";
        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . $nPrivKey . "\n-----END RSA PRIVATE KEY-----";
        openssl_sign($data, $signature, $private_key, OPENSSL_ALGO_SHA256);
        return base64_encode($signature);
    }

    function HttpRequestMethod($PostURL, $PostData, $isGet = false)
    {
        $url = curl_init($PostURL);
        $posttoken = json_encode($PostData);
        $header = array(
            'Content-Type:application/json',
            'X-KM-Api-Version:v-0.2.0',
            'X-KM-IP-V4:' . $this->get_client_ip(),
            'X-KM-Client-Type:PC_WEB'
        );

        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, $isGet ? 'GET' : 'POST');
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        if(!$isGet) {
            curl_setopt($url, CURLOPT_POSTFIELDS, $posttoken);
        }
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($url, CURLOPT_SSL_VERIFYPEER, 0);

        $resultdata = curl_exec($url);
        $ResultArray = json_decode($resultdata, true);
        curl_close($url);
        return $ResultArray;

    }

    function get_client_ip()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    function DecryptDataWithPrivateKey($crypttext)
    {
        $nPrivKey = get_option('n_privatekey');
//    $merchantPrivateKey = "MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCJakyLqojWTDAVUdNJLvuXhROV+LXymqnukBrmiWwTYnJYm9r5cKHj1hYQRhU5eiy6NmFVJqJtwpxyyDSCWSoSmIQMoO2KjYyB5cDajRF45v1GmSeyiIn0hl55qM8ohJGjXQVPfXiqEB5c5REJ8Toy83gzGE3ApmLipoegnwMkewsTNDbe5xZdxN1qfKiRiCL720FtQfIwPDp9ZqbG2OQbdyZUB8I08irKJ0x/psM4SjXasglHBK5G1DX7BmwcB/PRbC0cHYy3pXDmLI8pZl1NehLzbav0Y4fP4MdnpQnfzZJdpaGVE0oI15lq+KZ0tbllNcS+/4MSwW+afvOw9bazAgMBAAECggEAIkenUsw3GKam9BqWh9I1p0Xmbeo+kYftznqai1pK4McVWW9//+wOJsU4edTR5KXK1KVOQKzDpnf/CU9SchYGPd9YScI3n/HR1HHZW2wHqM6O7na0hYA0UhDXLqhjDWuM3WEOOxdE67/bozbtujo4V4+PM8fjVaTsVDhQ60vfv9CnJJ7dLnhqcoovidOwZTHwG+pQtAwbX0ICgKSrc0elv8ZtfwlEvgIrtSiLAO1/CAf+uReUXyBCZhS4Xl7LroKZGiZ80/JE5mc67V/yImVKHBe0aZwgDHgtHh63/50/cAyuUfKyreAH0VLEwy54UCGramPQqYlIReMEbi6U4GC5AQKBgQDfDnHCH1rBvBWfkxPivl/yNKmENBkVikGWBwHNA3wVQ+xZ1Oqmjw3zuHY0xOH0GtK8l3Jy5dRL4DYlwB1qgd/Cxh0mmOv7/C3SviRk7W6FKqdpJLyaE/bqI9AmRCZBpX2PMje6Mm8QHp6+1QpPnN/SenOvoQg/WWYM1DNXUJsfMwKBgQCdtddE7A5IBvgZX2o9vTLZY/3KVuHgJm9dQNbfvtXw+IQfwssPqjrvoU6hPBWHbCZl6FCl2tRh/QfYR/N7H2PvRFfbbeWHw9+xwFP1pdgMug4cTAt4rkRJRLjEnZCNvSMVHrri+fAgpv296nOhwmY/qw5Smi9rMkRY6BoNCiEKgQKBgAaRnFQFLF0MNu7OHAXPaW/ukRdtmVeDDM9oQWtSMPNHXsx+crKY/+YvhnujWKwhphcbtqkfj5L0dWPDNpqOXJKV1wHt+vUexhKwus2mGF0flnKIPG2lLN5UU6rs0tuYDgyLhAyds5ub6zzfdUBG9Gh0ZrfDXETRUyoJjcGChC71AoGAfmSciL0SWQFU1qjUcXRvCzCK1h25WrYS7E6pppm/xia1ZOrtaLmKEEBbzvZjXqv7PhLoh3OQYJO0NM69QMCQi9JfAxnZKWx+m2tDHozyUIjQBDehve8UBRBRcCnDDwU015lQN9YNb23Fz+3VDB/LaF1D1kmBlUys3//r2OV0Q4ECgYBnpo6ZFmrHvV9IMIGjP7XIlVa1uiMCt41FVyINB9SJnamGGauW/pyENvEVh+ueuthSg37e/l0Xu0nm/XGqyKCqkAfBbL2Uj/j5FyDFrpF27PkANDo99CdqL5A4NQzZ69QRlCQ4wnNCq6GsYy2WEJyU2D+K8EBSQcwLsrI7QL7fvQ==";
        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . $nPrivKey . "\n-----END RSA PRIVATE KEY-----";
        openssl_private_decrypt(base64_decode($crypttext), $plain_text, $private_key);
        return $plain_text;
    }

    function initiatePayment($amount, $name='', $email='', $phone='', $city='', $country='') {
        $invoice_no = 'Inv' . Date('YmdH') . rand(1000, 10000);
        $MerchantID = get_option('n_merchantid');
        $DateTime = Date('YmdHis');
        $random = $this->generateRandomString();

        $PostURL = "http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0/api/dfs/check-out/initialize/" . $MerchantID . "/" . $invoice_no;
        $merchantCallbackURL = "http://localhost/amrakorbojoy/index.php";
        $SensitiveData = array(
            'merchantId' => $MerchantID,
            'datetime' => $DateTime,
            'orderId' => $invoice_no,
            'challenge' => $random
        );
        $PostData = array(
            'accountNumber' => '01711428036', //optional
            'dateTime' => $DateTime,
            'sensitiveData' => $this->EncryptDataWithPublicKey(json_encode($SensitiveData)),
            'signature' => $this->SignatureGenerate(json_encode($SensitiveData))
        );
        $Result_Data = $this->HttpRequestMethod($PostURL, $PostData);


        if (isset($Result_Data['sensitiveData']) && isset($Result_Data['signature'])) {
            if ($Result_Data['sensitiveData'] != "" && $Result_Data['signature'] != "") {

                $PlainResponse = json_decode($this->DecryptDataWithPrivateKey($Result_Data['sensitiveData']), true);

                // echo json_encode($PlainResponse);
                // exit();

                if (isset($PlainResponse['paymentReferenceId']) && isset($PlainResponse['challenge'])) {

                    $paymentReferenceId = $PlainResponse['paymentReferenceId'];
                    $randomserver = $PlainResponse['challenge'];

                    $SensitiveDataOrder = array(
                        'merchantId' => $MerchantID,
                        'orderId' => $invoice_no,
                        'currencyCode' => '050',
                        'amount' => $amount,
                        'challenge' => $randomserver
                    );

                    // $merchantAdditionalInfo = '{"no_of_seat": "1", "Service_Charge":"20"}';
                    //$merchantAdditionalInfo = '{"invoice":"20022565314173757","no_of_seat":"3","Service_Charge":"60"}';

                    // echo $merchantAdditionalInfo;
                    // exit();

                    $PostDataOrder = array(
                        'sensitiveData' => $this->EncryptDataWithPublicKey(json_encode($SensitiveDataOrder)),
                        'signature' => $this->SignatureGenerate(json_encode($SensitiveDataOrder)),
                        'merchantCallbackURL' => $merchantCallbackURL
                        // 'additionalMerchantInfo' => json_decode($merchantAdditionalInfo)
                    );

                    // echo json_encode($PostDataOrder);
                    // exit();

                    $OrderSubmitUrl = "http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0/api/dfs/check-out/complete/" . $paymentReferenceId;
                    $Result_Data_Order = $this->HttpRequestMethod($OrderSubmitUrl, $PostDataOrder);
                    if ($Result_Data_Order['status'] == "Success") {

                        global $wpdb;
                        $table_name = $wpdb->prefix . 'nagad_payment';
                        $field_Data = array(
                            'trxid' => $invoice_no,
                            'reference_id' => $paymentReferenceId,
                            'tran_status' => 'Pending',
                            'total_amount' => $amount,
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

                        if ($insert) {
                            $url = json_encode($Result_Data_Order['callBackUrl']);
                            // echo "<script>window.open($url, '_self')</script>";
                            $this->echoResp(['data' => $Result_Data_Order['callBackUrl']]);
                        } else {
                            $this->echoResp("Cannot initiate payment now, insert fail", true);
                        }
                        // echo json_encode($Result_Data_Order);

                    } else {
                        echo json_encode($Result_Data_Order);
                    }
                } else {
                    echo json_encode($PlainResponse);
                }
            }
        }
    }
    function verifyPayment($payRefID) {
        $MerchantID = get_option('n_merchantid');

        $getURL = "http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0/api/dfs/verify/payment/" . $payRefID;
//        $SensitiveData = array(
//            'merchantId' => $MerchantID,
//            'datetime' => $DateTime,
//            'orderId' => $invoice_no,
//            'challenge' => $random
//        );
//        $PostData = array(
//            'accountNumber' => '01711428036', //optional
//            'dateTime' => $DateTime,
//            'sensitiveData' => $this->EncryptDataWithPublicKey(json_encode($SensitiveData)),
//            'signature' => $this->SignatureGenerate(json_encode($SensitiveData))
//        );
        $Result_Data = $this->HttpRequestMethod($getURL, [], true);


        if (isset($Result_Data['paymentRefId']) && !empty($Result_Data['paymentRefId'])) {
            if (isset($Result_Data['issuerPaymentRefNo']) && !empty($Result_Data['issuerPaymentRefNo'])) {

                $data = [
                    'additionalMerchantInfo' => isset($Result_Data['additionalMerchantInfo']) ? $Result_Data['additionalMerchantInfo'] : '',
                    'amount' => isset($Result_Data['null']) ? $Result_Data['null'] : '',
                    'clientMobileNo' => isset($Result_Data['amount']) ? $Result_Data['amount'] : '',
                    'issuerPaymentDateTime' => isset($Result_Data['"130"']) ? $Result_Data['"130"'] : '',
                    'issuerPaymentRefNo' => isset($Result_Data['clientMobileNo']) ? $Result_Data['clientMobileNo'] : '',
                    'merchantId' => isset($Result_Data['null']) ? $Result_Data['null'] : '',
                    'merchantMobileNo' => isset($Result_Data['issuerPaymentDateTime']) ? $Result_Data['issuerPaymentDateTime'] : '',
                    'orderDateTime' => isset($Result_Data['"20191107165357"']) ? $Result_Data['"20191107165357"'] : '',
                    'orderId' => isset($Result_Data['issuerPaymentRefNo']) ? $Result_Data['issuerPaymentRefNo'] : '',
                    'paymentRefId' => isset($Result_Data['"70IZV6G7"']) ? $Result_Data['"70IZV6G7"'] : '',
                    'status' => isset($Result_Data['merchantId']) ? $Result_Data['merchantId'] : '',
                    'statusCode' => isset($Result_Data['"687450000031324"']) ? $Result_Data['"687450000031324"'] : ''
                ];

                global $wpdb;
                $table_name = $wpdb->prefix . 'nagad_payment';
                $trx = $_POST['tran_id'];
                $results = $wpdb->get_results("SELECT * FROM $table_name WHERE reference_Id = '$payRefID' ", ARRAY_A);

                if($results[0]['tran_status'] == 'Pending' && $data['status'] == "Success" && $results[0]['total_amount'] == $data['amount'])
                {
                    $wpdb->query(
                        $wpdb->prepare(
                            "UPDATE $table_name SET tran_status = %s, total_amount = %s WHERE trxid = %s",'Paid', $data['amount'], $payRefID
                        )
                    );
                    if ( wp_redirect( $_SESSION['CUS_HISTORY']['SITE_URL'] ) ) {
                        exit;
                    }
                }
                else
                {
                    if ( wp_redirect( $_SESSION['CUS_HISTORY']['SITE_URL'] ) ) {
                        exit;
                    }
                }
            }
        }
    }

    function echoResp($data, $error = false)
    {
        if (is_string($data)) {
            echo json_encode([
                'status' => $error ? 'fail' : 'success',
                'data' => $data
            ]);
        } else if (is_array($data)) {
            $data['status'] = $error ? 'fail' : 'success';
            echo json_encode($data);
        }
        wp_die();
    }
}

