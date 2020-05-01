<?php
function get($string, $payment){
    if(isset($payment[$string])){
        return $payment[$string];
    } else {
        return '';
    }
}
?>

<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <style>
        body {
            background: #e6e6e6;
        }
        .bg {
            /*background-color: #6c7bee;*/
            width: 480px;
            overflow: hidden;
            margin: 0 auto;
            box-sizing: border-box;
            padding: 40px;
            font-family: 'Roboto';
            margin-top: 40px;
        }

        .card {
            background-color: #fff;
            width: 100%;
            float: left;
            margin-top: 40px;
            border-radius: 5px;
            box-sizing: border-box;
            padding: 80px 30px 25px 30px;
            text-align: center;
            position: relative;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
        }

        .card__success {
            position: absolute;
            top: -50px;
            left: 145px;
            width: 100px;
            height: 100px;
            border-radius: 100%;
            background-color: #60c878;
            border: 5px solid #fff;
        }

        .card__success i {
            color: #fff;
            line-height: 100px;
            font-size: 45px;
        }

        .card__success i:before {
            margin-top: 25px;
        }

        .card__error {
            position: absolute;
            top: -50px;
            left: 145px;
            width: 100px;
            height: 100px;
            border-radius: 100%;
            background-color: #e7391e;
            border: 5px solid #fff;
        }

        .card__error i {
            color: #fff;
            line-height: 100px;
            font-size: 45px;
        }

        .card__error i:before {
            margin-top: 25px;
        }

        .card__msg {
            text-transform: uppercase;
            color: #55585b;
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .card__submsg {
            color: #959a9e;
            font-size: 16px;
            font-weight: 400;
            margin-top: 0px;
        }

        .card__body {
            background-color: #f8f6f6;
            border-radius: 4px;
            width: 100%;
            margin-top: 30px;
            float: left;
            box-sizing: border-box;
            padding: 30px;
        }

        .card__avatar {
            width: 50px;
            height: 50px;
            border-radius: 100%;
            display: inline-block;
            margin-right: 10px;
            position: relative;
            top: 7px;
        }

        .card__recipient-info {
            display: inline-block;
        }

        .card__recipient {
            color: #232528;
            text-align: left;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .card__email {
            color: #838890;
            text-align: left;
            margin-top: 0px;
        }

        .card__price {
            color: #232528;
            font-size: 70px;
            margin-top: 25px;
            margin-bottom: 30px;
        }

        .card__price span {
            font-size: 60%;
        }

        .card__method {
            color: #a7a5a5;
            text-transform: uppercase;
            text-align: left;
            font-size: 11px;
            margin-bottom: 5px;
        }

        .card__payment {
            background-color: #fff;
            border-radius: 4px;
            width: 100%;
            height: 100px;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card__credit-card {
            width: 50px;
            display: inline-block;
            margin-right: 15px;
        }

        .card__card-details {
            display: inline-block;
            text-align: left;
        }

        .card__card-type {
            text-transform: uppercase;
            color: #232528;
            font-weight: 600;
            font-size: 12px;
            margin-bottom: 3px;
        }

        .card__card-number {
            color: #838890;
            font-size: 12px;
            margin-top: 0px;
        }

        .card__tags {
            clear: both;
            padding-top: 15px;
        }

        .card__tag {
            text-transform: uppercase;
            background-color: #f8f6f6;
            box-sizing: border-box;
            padding: 3px 5px;
            border-radius: 3px;
            font-size: 10px;
            color: #898989;
        }

    </style>
    <title>Payment Confirmation</title>
</head>
<body>

<div class="bg">

    <div class="card">

        <?php if(get('tran_status',$payment) == "Paid") {?>
            <span class="card__success"><i class="ion-checkmark"></i></span>
            <h1 class="card__msg">Donation <?php echo get('tran_status',$payment) == 'Paid' ? 'Complete' : 'Fail' ?></h1>
            <h2 class="card__submsg">Thank you for your transfer</h2>
        <?php } else { ?>
            <span class="card__error"><i class="ion-close"></i></span>
            <h1 class="card__msg">Donation <?php echo get('tran_status',$payment) == 'Paid' ? 'Complete' : 'Fail' ?></h1>
            <h2 class="card__submsg">Oops! You donation has been failed, try again</h2>
        <?php }?>


        <div class="card__body">

<!--            <img src="http://nathgreen.co.uk/assets/img/nath.jpg" class="card__avatar">-->
            <div class="card__recipient-info">
                <p class="card__recipient"><?php echo get('cus_name',$payment); ?></p>
                <p class="card__email"><?php echo get('cus_email',$payment); ?></p>
            </div>

            <?php if(!$payment) { ?>
                <h3 class="card__msg">Payment information not found</h3>
            <?php } ?>

            <?php if(get('tran_status',$payment) == "Paid") {?>
            <h1 class="card__price"><span>৳ </span><?php echo get('total_amount',$payment); ?></h1>

            <p class="card__method">Payment method</p>
            <div class="card__payment">
                <img src="<?php echo plugin_dir_url('/') . 'Nagad/images/logo.png' ; ?>"
                     class="card__credit-card">
                <div class="card__card-details">
                    <p class="card__card-type">Nagad</p>
                    <p class="card__card-number">Mobile Financial Service</p>
                </div>
            </div>
            <?php } ?>

        </div>

        <div class="card__tags">
            <span class="card__tag"><?php echo get('tran_status',$payment) == "Paid" ? 'Completed' : 'Unpaid'; ?></span>
            <span class="card__tag">#<?php echo get('trxid', $payment); ?>></span>
        </div>

        <div class="row">
            <a href="<?php echo site_url(); ?>" class="btn button button-hero">Donate again</a>
        </div>

    </div>

</div>
</body>
</html>