<?php 
	session_start();
	global $wpdb;
	$table_name = $wpdb->prefix . 'sslcommerz_payment';
	$tran_id = $_POST['tran_id'];
	if ($_POST['status'] == 'FAILED') 
	{
		$wpdb->query( $wpdb->prepare("UPDATE $table_name 
	                SET tran_status = %s
	             WHERE trxid = %s",'Failed', $tran_id));
		$_SESSION['CUS_HISTORY']['GET_STATUS'] = $_POST['status'];
		$_SESSION['CUS_HISTORY']['DATA_STATUS'] = 'Failed';
		// $_SESSION['CUS_HISTORY']['AMOUNTS'] = $amount;
		if ( wp_redirect( $_SESSION['CUS_HISTORY']['SITE_URL'] ) ) {
		    exit;
		}
	}
?>