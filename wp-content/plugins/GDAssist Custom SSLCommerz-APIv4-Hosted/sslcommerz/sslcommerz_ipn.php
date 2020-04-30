<?php
    $mode = get_option('testmode');
    $store_id = get_option('store');
    $store_pass = get_option('password');
    global $wpdb;
    $table_name = $wpdb->prefix . 'sslcommerz_payment';
    if($mode == 'on')
    {
        $valid_url = 'https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php';
    }
    else
    {
        $valid_url = 'https://securepay.sslcommerz.com/validator/api/validationserverAPI.php';
    }
    function _SSLCOMMERZ_hash_varify($store_passwd = "") 
    {
        if (isset($_POST) && isset($_POST['verify_sign']) && isset($_POST['verify_key'])) 
        {
            # NEW ARRAY DECLARED TO TAKE VALUE OF ALL POST
            $pre_define_key = explode(',', $_POST['verify_key']);
            $new_data = array();
            if (!empty($pre_define_key)) 
            {
                foreach ($pre_define_key as $value) 
                {
                    if (isset($_POST[$value])) 
                    {
                        $new_data[$value] = ($_POST[$value]);
                    }
                }
            }
            # ADD MD5 OF STORE PASSWORD
            $new_data['store_passwd'] = md5($store_passwd);

            # SORT THE KEY AS BEFORE
            ksort($new_data);
            $hash_string = "";
            foreach ($new_data as $key => $value) 
            {
                $hash_string .= $key . '=' . ($value) . '&';
            }

            $hash_string = rtrim($hash_string, '&');

            if (md5($hash_string) == $_POST['verify_sign']) 
            {
                return true;
            } 
            else 
            {
                return false;
            }
        } 
        else
        {
            return false;
        }
    }

    if($_POST['status'] == 'VALIDATED' || $_POST['status'] == 'VALID')
    {
        if(isset($_POST['val_id']) || isset($_POST['tran_id']))
        {
            if (_SSLCOMMERZ_hash_varify($store_pass)) 
            {
                $val_id = urlencode($_POST['val_id']);
                $store_id = urlencode($store_id);
                $store_passwd = urlencode($store_pass);
                $trx = $_POST['tran_id'];

                $results = $wpdb->get_results("SELECT * FROM $table_name WHERE trxid = '$trx' ", ARRAY_A);

                $requested_url = ($valid_url . "?val_id=" . $val_id . "&store_id=" . $store_id . "&store_passwd=" . $store_passwd . "&v=1&format=json");

                $handle = curl_init();
                curl_setopt($handle, CURLOPT_URL, $requested_url);
                curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($handle);
                $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

                if ($code == 200 && !( curl_errno($handle))) 
                {
                    # TO CONVERT AS ARRAY
                    # $result = json_decode($result, true);
                    # $status = $result['status'];  
                    # TO CONVERT AS OBJECT
        
                    $gw_data = json_decode($result);
                    $card_type = $gw_data->card_type;
                    $currency_amount = $gw_data->currency_amount;
                    $amount = $gw_data->amount;
                    $currency_type = $gw_data->currency_type;

                    if($results[0]['total_amount'] == trim($currency_amount))
                    { 
                        if($gw_data->status =='VALIDATED' || $gw_data->status =='VALID') 
                        { 
                            if($results[0]['tran_status'] == 'Pending')
                            {
                                if($_POST['card_type'] != "")
                                {           
                                    $ipn = 'IPN Triggered';           
                                    $wpdb->query( $wpdb->prepare("UPDATE $table_name SET tran_status = %s,card_type = %s, ipn_status = %s WHERE trxid = %s",'Processing', $card_type."(".$currency_type.")", $ipn, $_POST['tran_id']));

                                    $msg =  "Hash Validation Success.";
                                }
                                else
                                {
                                    $msg =  "Card Type Empty or Mismatched";
                                }
                            }
                            else
                            {
                                $msg = "Payment already Processing.";
                            }
                        }
                        else
                        {
                            $msg=  "Status not ".$gw_data->status;
                        }
                    }
                    else
                    {
                        $msg =  "Your Paid Amount is Mismatched.";
                    }
                }
            }
            else
            {
                $msg =  "Hash Validation Failed.";
            }
        }
    }
    elseif($_POST['status'] == 'FAILED')
    {
        $ipn = 'IPN Triggered';           
        $wpdb->query( $wpdb->prepare("UPDATE $table_name SET tran_status = %s, ipn_status = %s WHERE trxid = %s",'Failed', $ipn, $_POST['tran_id']));
    }
    elseif($_POST['status'] == 'CANCELLED')
    {
        $ipn = 'IPN Triggered';           
        $wpdb->query( $wpdb->prepare("UPDATE $table_name SET tran_status = %s, ipn_status = %s WHERE trxid = %s",'Cancelled', $ipn, $_POST['tran_id']));
    }
    else
    {
        $msg =  "No IPN Request Received.";
    }

    echo $msg;
?>