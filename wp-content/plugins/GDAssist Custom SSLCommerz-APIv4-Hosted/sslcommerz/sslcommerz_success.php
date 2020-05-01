<?php
	session_start();
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
	if(isset($_POST['val_id']) || isset($_POST['tran_id']))
	{
		if (_SSLCOMMERZ_hash_varify($store_pass)) 
        {
            //print_r($_POST);
            //exit();
            $val_id = urlencode($_POST['val_id']);
            $store_id = urlencode($store_id);
            $store_passwd = urlencode($store_pass);
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
    
                $result = json_decode($result);
                
                // echo "<pre>";
                // print_r($result);
    
                # TRANSACTION INFO
    
                $status = $result->status;
                $tran_date = $result->tran_date;
                $tran_id = $result->tran_id;
                $val_id = $result->val_id;
                $amount = $result->amount;
                $currency_amount = $result->currency_amount;
                $store_amount = $result->store_amount;
                $bank_tran_id = $result->bank_tran_id;
                $card_type = $result->card_type;
                $currency_type = $result->currency_type;
    
                # ISSUER INFO
    
                $card_no = $result->card_no;
                $card_issuer = $result->card_issuer;
                $card_brand = $result->card_brand;
                $card_issuer_country = $result->card_issuer_country;
                $card_issuer_country_code = $result->card_issuer_country_code;
                
                # API AUTHENTICATION
    
                $APIConnect = $result->APIConnect;
                $validated_on = $result->validated_on;
                $gw_version = $result->gw_version;

                $trx = $_POST['tran_id'];
                $results = $wpdb->get_results("SELECT * FROM $table_name WHERE trxid = '$trx' ", ARRAY_A);

                if($results[0]['tran_status'] == 'Pending')
                {
                	$wpdb->query( $wpdb->prepare("UPDATE $table_name SET tran_status = %s, card_type = %s, total_amount = %s WHERE trxid = %s",'Processing', $card_type."(".$currency_type.")", $currency_amount, $tran_id));
                	if ($status == 'VALID' || $status == 'VALIDATED') 
		    		{
		    			$_SESSION['CUS_HISTORY']['GET_STATUS'] = $status;
		    			$_SESSION['CUS_HISTORY']['DATA_STATUS'] = 'Processing';
		    			$_SESSION['CUS_HISTORY']['AMOUNTS'] = $amount;
		    			if ( wp_redirect( $_SESSION['CUS_HISTORY']['SITE_URL'] . '?status_sslcz='.$results[0]['trxid'] ) ) {
						    exit;
						}
		    		}
                }
                else
                {
                	if ( wp_redirect( $_SESSION['CUS_HISTORY']['SITE_URL'] ) ) {
					    exit;
					}
                }

            } 
            else 
            {
                echo "Failed to connect with SSLCOMMERZ";
            }
        } 
        else 
        {
            echo "Hash validation failed.";
        }
	}
?>