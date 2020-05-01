<?php
    // global $chk;
    if(isset($_POST['wphw_nagad_submit']))
    {
        wphw_opt();
    }
    function wphw_opt()
    {
        $enable = $_POST['enable'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $publickey = $_POST['publickey'];
        $privatekey = $_POST['privatekey'];
        $merchantid = $_POST['merchantid'];
        $testmode = $_POST['testmode'];
        $orderhistory = $_POST['orderhistory'];

        global $enb,$tit,$des,$pk,$pvk,$mid,$tst,$ordh;
        if( get_option('n_enable') != $enable){
            $enb = update_option( 'n_enable', $enable);
        }
        if( get_option('n_title') != trim($title)){
            $tit = update_option( 'n_title', trim($title));
        }
        if( get_option('n_description') != trim($description)){
            $des = update_option( 'n_description', trim($description));
        }
        if( get_option('n_publickey') != trim($publickey)){
            $pk = update_option( 'n_publickey', trim($publickey));
        }
        if( get_option('n_privatekey') != trim($privatekey)){
            $pvk = update_option( 'n_privatekey', trim($privatekey));
        }
        if( get_option('n_merchantid') != trim($merchantid)){
            $mid = update_option( 'n_merchantid', trim($merchantid));
        }
        if( get_option('n_testmode') != trim($testmode)){
            $tst = update_option( 'n_testmode', trim($testmode));
        }
        if( get_option('n_orderhistory') != trim($orderhistory)){
            $ordh = update_option( 'n_orderhistory', trim($orderhistory));
        }
    }
?>

<div class="wrap" style="margin-left: 2%;margin-top: 4%;">
    <div id="icon-options-general" class="icon32"> <br>
    </div>
    <?php if(isset($_POST['wphw_nagad_submit'])):?>
    
    <div id="message" class="updated below-h2">
        <p>Content updated successfully</p>
    </div>
    <?php endif;?>
    <div class="metabox-holder">
        <form method="post" action="">
            <table>
                <tr>
                    <td style="width: 200px;"><h4>Enable/Disable </h4></td>
                    <?php if(get_option('n_enable') != 'on'): ?>
                    <td><input type="checkbox" name="enable"></td>
                    <?php else: ?>
                    <td><input type="checkbox" name="enable" checked>Enabled</td>
                    <?php endif;?>
                </tr>
                <tr>
                    <td><h4>Title </h4></td>
                    <td><input type="text" name="title" value="<?php echo get_option('n_title');?>" style="width:600px;"><br>
                        <i>* This controls the title which the user sees during checkout</i>
                    </td>
                </tr>
                <tr>
                    <td><h4>Description </h4></td>
                    <td><textarea name="description" rows="2" cols="42"><?php echo get_option('n_description');?></textarea> <br>
                    <i>* This controls the description which the user sees during checkout</i></td>
                </tr>
                <tr>
                    <td><h4>Public Key </h4></td>
                    <td><input type="text" name="publickey" value="<?php echo get_option('n_publickey');?>" style="width:300px;"><br>
                    <i>* Merchant ID</i></td>
                </tr>
                <tr>
                    <td><h4>Private Key </h4></td>
                    <td><input type="text" name="privatekey" value="<?php echo get_option('n_privatekey');?>" style="width:300px;"><br>
                    <i>* Merchant Password! It is required at payment validation.</i></td>
                </tr>
                <tr>
                    <td><h4>Merchant ID </h4></td>
                    <td><input type="text" name="merchantid" value="<?php echo get_option('n_merchantid');?>" style="width:300px;"><br>
                    <i>* Merchant Password! It is required at payment validation.</i></td>
                </tr>
                <tr>
                    <td><h4>Test Mode</h4></td>
                    <?php if(get_option('n_testmode') != 'on'): ?>
                    <td><input type="checkbox" name="testmode"><br><i>* Sandbox can be used to test payments</i></td>
                    <?php else: ?>
                    <td><input type="checkbox" name="testmode" checked>Enabled<br><i>* Sandbox can be used to test payments</i></td>
                    <?php endif;?>
                </tr>
                <tr>
                    <td><h4>Order History</h4></td>
                    <?php if(get_option('n_orderhistory') != 'on'): ?>
                    <td><input type="checkbox" name="orderhistory"><br><i>* Sandbox can be used to test payments</i></td>
                    <?php else: ?>
                    <td><input type="checkbox" name="orderhistory" checked>Enabled<br><i>* Order History can be used to check customer payments</i></td>
                    <?php endif;?>
                </tr>
                <tr>
                    <td><h4>IPN URL</h4></td>
                    <td><span style="color:blue;font-weight: bold;"><?php echo get_site_url( null, null, null ).'/index.php?sslcipn'; ?></span><br><i>* Copy and Paste this URL to your SSLCommerz merchant panel in IPN field</i></td>
                </tr>
            </table>
            <input type="submit" name="wphw_nagad_submit" value="Save changes" class="button-primary" />
        </form>
    </div>
</div>
