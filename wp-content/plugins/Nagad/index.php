<?php
/*
	Plugin Name: Nagad
	Plugin URI: http://www.nagad.com.bd
	Description: Nagad Online Payment Gateway
	Author: Shahnawaz Ahmed
	Email:sdaanish@live.com
	Author URI: http://shahnawaz.website
	Version: 1.0.0
	Copyright: © 20018-2019 Shahnawaz.
	*/
if (!defined('ABSPATH')) exit; // Exit if accessed directly

add_action('admin_menu', 'main_nagad');

add_action('plugins_loaded', array(Nagad_response_url::get_instance(), 'setup'));
add_action('plugins_loaded', array(Nagad_status_url::get_instance(), 'setup'));
//add_action('plugins_loaded', array(Sslc_cancel_url::get_instance(), 'setup'));
//add_action('plugins_loaded', array(Sslc_ipn_url::get_instance(), 'setup'));

global $wpdb;
$table_name = $wpdb->prefix . 'nagad_payment';

if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
    $charset_collate = 'ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
    $sql = "CREATE TABLE $table_name (
            `tran_sys_id` bigint(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `trxid` varchar(50) NOT NULL,
            `reference_id` varchar(250) NULL,
            `tran_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `tran_status` varchar(50) NOT NULL,
            `total_amount` float(10) NOT NULL,
            `cus_name` varchar(50) NULL,
            `cus_email` varchar(150) NULL,
            `cus_phone` varchar(20) NULL,
            `cus_address` varchar(1000) NULL,
            `cus_country` varchar(50) NULL,
            `cus_city` varchar(50) NULL,
            `cus_postcode` varchar(50) NULL,
            `ipn_status` varchar(150) NULL
      ) $charset_collate;";

    //echo $sql."<br>";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function main_nagad()
{
    // Add a new top-level menu (ill-advised):
    add_menu_page(__('NAGAD', 'menu-nagad'), __('NAGAD', 'menu-nagad'), 'manage_options', 'nagad', 'NagadAdmin');
    if (get_option('orderhistory') == "on") {
        add_menu_page(
            'Nagad Order History', // page title
            'Nagad Order History', // menu title
            'manage_options', // capability
            'nagad-order-list', // menu slug
            'wpse_NagadordertList_render', // callback function
            '',
            6
        );
    }
}

// mt_toplevel_page() displays the page content for the custom Test Toplevel menu
function NagadAdmin()
{
    echo "<h1>" . __('Nagad Configuration', 'menu-nagad') . "</h1>";
    include_once('form.php');
    if (get_option('n_enable')  == "on") {
//        if (!file_exists(get_template_directory() . "/sslcommerz_init.php")) {
//            copy(plugin_dir_path(__FILE__) . "sslcommerz/sslcommerz_init.php", get_template_directory() . "/sslcommerz_init.php");
//        }
//        if (!file_exists(get_template_directory() . "/SSLCommerz.png")) {
//            copy(plugin_dir_path(__FILE__) . "images/SSLCommerz.png", get_template_directory() . "/SSLCommerz.png");
//        }
        //          if(!file_exists(get_template_directory()."/sslcommerz_success.php"))
        // {
        //     copy(plugin_dir_path( __FILE__ )."sslcommerz/sslcommerz_success.php", get_template_directory()."/sslcommerz_success.php");
        // }
        // if(!file_exists(get_template_directory()."/sslcommerz_fail.php"))
        // {
        //     copy(plugin_dir_path( __FILE__ )."sslcommerz/sslcommerz_fail.php", get_template_directory()."/sslcommerz_fail.php");
        // }
        // if(!file_exists(get_template_directory()."/sslcommerz_cancel.php"))
        // {
        //     copy(plugin_dir_path( __FILE__ )."sslcommerz/sslcommerz_cancel.php", get_template_directory()."/sslcommerz_cancel.php");
        // }

        // echo get_template_directory()."======".get_template()."=========".get_template_directory_uri();
    }

    // echo plugin_dir_path( __FILE__ );
    // print_r(get_plugin_data( __FILE__ ));
    // $plugin_data = get_plugin_data( __FILE__ );
    // echo $plugin_name = $plugin_data['TextDomain'];
}
function getNagadData()
{
    $setData = array();
    $setData['activity']         = get_option('n_enable');
    $setData['title']             = get_option('n_title');
    $setData['description']     = get_option('n_description');
    $setData['publickey']         = get_option('n_publickey');
    $setData['privatekey']         = get_option('n_privatekey');
    $setData['merchantid']         = get_option('n_merchantid');
    $setData['testmode']         = get_option('n_testmode');
    $setData['orderhistory']     = get_option('n_orderhistory');
//    if (get_option('testmode') == "on") {
//        $setData['SslcURL'] = "https://sandbox.sslcommerz.com/gwprocess/v3/api.php";
//        $setData['SslcValURL'] = "https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php";
//    } else {
//        $setData['SslcURL'] = "https://securepay.sslcommerz.com/gwprocess/v3/api.php";
//        $setData['SslcValURL'] = "https://securepay.sslcommerz.com/validator/api/validationserverAPI.php";
//    }
    return $setData;
}

?>
<?php

function wpse_NagadordertList_render()
{
    global $title;

    wp_register_style('stylesheet_1', "//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css");
    wp_register_style('stylesheet_2', "//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css");
    wp_register_style('stylesheet_3', "//cdn.datatables.net/plug-ins/1.10.12/features/searchHighlight/dataTables.searchHighlight.css");

    print '<div class="wrap">';
    print "<h1>$title</h1>";

    $file = plugin_dir_path(__FILE__) . "included.html";

    if (file_exists($file)) {
        require $file;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'nagad_payment';

    $results = $wpdb->get_results('SELECT * FROM ' . $table_name, ARRAY_A);

    // echo '<pre>';
    // print_r($results);
    // echo '</pre>';
    ?>
    <?php
    wp_register_script('script_1', "//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js");
    wp_register_script('script_2', "//cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js");
    wp_register_script('script_3', "//cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js");
    wp_register_script('script_4', "//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js");
    wp_register_script('script_5', "//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js");
    wp_register_script('script_6', "//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js");
    wp_register_script('script_7', "//cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js");
    wp_register_script('script_8', "//cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js");
    wp_register_script('script_9', "//cdn.datatables.net/plug-ins/1.10.12/features/searchHighlight/dataTables.searchHighlight.min.js");
    wp_register_script('script_10', "//bartaz.github.io/sandbox.js/jquery.highlight.js");

    wp_enqueue_script('script_1');
    wp_enqueue_script('script_2');
    wp_enqueue_script('script_3');
    wp_enqueue_script('script_4');
    wp_enqueue_script('script_5');
    wp_enqueue_script('script_6');
    wp_enqueue_script('script_7');
    wp_enqueue_script('script_8');
    wp_enqueue_script('script_9');
    wp_enqueue_script('script_10');

    wp_enqueue_style('stylesheet_1');
    wp_enqueue_style('stylesheet_2');
    wp_enqueue_style('stylesheet_3');
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            // alert("hello");

            $('#example').DataTable({
                // aLengthMenu: [[10, 25, 50, 100, 200, 500, -1], [10, 25, 50, 100, 200, 500, "ALL"]],
                searchHighlight: true,
                dom: 'Blfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
            });
        });
    </script>
    <div class='myTableWrp'>
        <table id='example' class='display nowrap' cellspacing='0' width='100%'>
            <thead>
                <tr style="background:#F2BF73;">
                    <th>SL No.</th>
                    <th>Transaction ID</th>
                    <th>Date</th>
                    <th>Status</th>
                    <!-- <th>Card Type</th> -->
                    <th>Product</th>
                    <th>Amount (BDT)</th>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <!--<th>Service</th>-->
                    <th>Postcode</th>
                    <th>City</th>
                    <th>Country</th>
                    <th>IPN Status</th>
                </tr>
            </thead>
            <tfoot>
                <tr style="background:#F5CC7A;">
                    <th>SL No.</th>
                    <th>Transection ID</th>
                    <th>Date</th>
                    <th>Status</th>
                    <!-- <th>Card Type</th> -->
                    <th>Product</th>
                    <th>Amount (BDT)</th>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <!--<th>Service</th>-->
                    <th>Postcode</th>
                    <th>City</th>
                    <th>Country</th>
                    <th>IPN Status</th>
                </tr>
            </tfoot>
            <tbody>
                <?php
                if (!empty($results)) {
                    $i = 0;
                    $total_amt = 0.0;
                    foreach ($results as $row) {
                        ?>
                        <tr>
                            <td><?php echo ++$i; ?></td>
                            <td><?php echo $row['trxid']; ?></td>
                            <td><?php echo $row['tran_date']; ?></td>
                            <td><?php echo $row['tran_status']; ?></td>
                            <!-- <td><?php echo $row['card_type']; ?></td> -->
                            <?php //echo $row['card_no']; 
                            ?>
                            <td><?php echo $row['product_name']; ?></td>
                            <td><strong><?php echo number_format($row['total_amount']); ?></strong></td>
                            <td><strong><?php echo $row['cus_name']; ?></strong></td>
                            <td><?php echo $row['cus_email']; ?></td>
                            <td><?php echo $row['cus_phone']; ?></td>
                            <td><?php echo $row['cus_address']; ?></td>
                            <td><?php echo $row['cus_postcode']; ?></td>
                            <td><?php echo $row['cus_city']; ?></td>
                            <td><?php echo $row['cus_country']; ?></td>
                            <td><?php echo $row['ipn_status']; ?></td>
                        </tr>
                        <?php
                        $total_amt += $row['total_amount'] . " TK";
                    }
                }
                ?>
            </tbody>
        </table>
        <div style="text-align:center">
            <h3>Total Amount: <?php echo number_format($total_amt, 2); ?></h3>
        </div>
    </div>
<?php
}

class Nagad_response_url
{
    protected static $instance = NULL;
    public function __construct()
    { }
    public static function get_instance()
    {
        NULL === self::$instance and self::$instance = new self;
        return self::$instance;
    }
    public function setup()
    {
        add_action('init', array($this, 'rewrite_rules'));
        add_filter('query_vars', array($this, 'query_vars'), 10, 1);
        add_action('parse_request', array($this, 'parse_request'), 10, 1);
        register_activation_hook(__FILE__, array($this, 'flush_rules'));
    }
    public function rewrite_rules()
    {
        add_rewrite_rule('nagad_resp/?$', 'index.php?merchant', 'top');
    }
    public function flush_rules()
    {
        $this->rewrite_rules();
        flush_rewrite_rules();
    }
    public function query_vars($vars)
    {
        $vars[] = 'merchant';
        return $vars;
    }
    public function parse_request($wp)
    {
        if (array_key_exists('merchant', $wp->query_vars)) {
            include plugin_dir_path(__FILE__) . 'nagad/nagad_verify.php';
            exit();
        }
    }
}


class Nagad_status_url
{
    protected static $instance = NULL;
    public function __construct()
    { }
    public static function get_instance()
    {
        NULL === self::$instance and self::$instance = new self;
        return self::$instance;
    }
    public function setup()
    {
        add_action('init', array($this, 'rewrite_rules'));
        add_filter('query_vars', array($this, 'query_vars'), 10, 1);
        add_action('parse_request', array($this, 'parse_request'), 10, 1);
        register_activation_hook(__FILE__, array($this, 'flush_rules'));
    }
    public function rewrite_rules()
    {
        add_rewrite_rule('nagad_status/?$', 'index.php?status_nagad', 'top');
    }
    public function flush_rules()
    {
        $this->rewrite_rules();
        flush_rewrite_rules();
    }
    public function query_vars($vars)
    {
        $vars[] = 'status_nagad';
        return $vars;
    }
    public function parse_request($wp)
    {
        if (array_key_exists('status_nagad', $wp->query_vars)) {
            include plugin_dir_path(__FILE__) . 'nagad/nagad_status.php';
            exit();
        }
    }
}

class Nagad_fail_url
{
    protected static $instance = NULL;
    public function __construct()
    { }
    public static function get_instance()
    {
        NULL === self::$instance and self::$instance = new self;
        return self::$instance;
    }
    public function setup()
    {
        add_action('init', array($this, 'rewrite_rules'));
        add_filter('query_vars', array($this, 'query_vars'), 10, 1);
        add_action('parse_request', array($this, 'parse_request'), 10, 1);
        register_activation_hook(__FILE__, array($this, 'flush_rules'));
    }
    public function rewrite_rules()
    {
        add_rewrite_rule('sslcfail/?$', 'index.php?sslcfail', 'top');
    }
    public function flush_rules()
    {
        $this->rewrite_rules();
        flush_rewrite_rules();
    }
    public function query_vars($vars)
    {
        $vars[] = 'sslcfail';
        return $vars;
    }
    public function parse_request($wp)
    {
        if (array_key_exists('sslcfail', $wp->query_vars)) {
            include plugin_dir_path(__FILE__) . 'sslcommerz/sslcommerz_fail.php';
            exit();
        }
    }
}
class Nagad_cancel_url
{
    protected static $instance = NULL;
    public function __construct()
    { }
    public static function get_instance()
    {
        NULL === self::$instance and self::$instance = new self;
        return self::$instance;
    }
    public function setup()
    {
        add_action('init', array($this, 'rewrite_rules'));
        add_filter('query_vars', array($this, 'query_vars'), 10, 1);
        add_action('parse_request', array($this, 'parse_request'), 10, 1);
        register_activation_hook(__FILE__, array($this, 'flush_rules'));
    }
    public function rewrite_rules()
    {
        add_rewrite_rule('sslccancel/?$', 'index.php?sslccancel', 'top');
    }
    public function flush_rules()
    {
        $this->rewrite_rules();
        flush_rewrite_rules();
    }
    public function query_vars($vars)
    {
        $vars[] = 'sslccancel';
        return $vars;
    }
    public function parse_request($wp)
    {
        if (array_key_exists('sslccancel', $wp->query_vars)) {
            include plugin_dir_path(__FILE__) . 'sslcommerz/sslcommerz_cancel.php';
            exit();
        }
    }
}
class Nagad_ipn_url
{
    protected static $instance = NULL;
    public function __construct()
    { }
    public static function get_instance()
    {
        NULL === self::$instance and self::$instance = new self;
        return self::$instance;
    }
    public function setup()
    {
        add_action('init', array($this, 'rewrite_rules'));
        add_filter('query_vars', array($this, 'query_vars'), 10, 1);
        add_action('parse_request', array($this, 'parse_request'), 10, 1);
        register_activation_hook(__FILE__, array($this, 'flush_rules'));
    }
    public function rewrite_rules()
    {
        add_rewrite_rule('sslcipn/?$', 'index.php?sslcipn', 'top');
    }
    public function flush_rules()
    {
        $this->rewrite_rules();
        flush_rewrite_rules();
    }
    public function query_vars($vars)
    {
        $vars[] = 'sslcipn';
        return $vars;
    }
    public function parse_request($wp)
    {
        if (array_key_exists('sslcipn', $wp->query_vars)) {
            include plugin_dir_path(__FILE__) . 'sslcommerz/sslcommerz_ipn.php';
            exit();
        }
    }
}
// add_action('wp_footer','slider_option');
// add_action( 'admin_menu', 'main' );
?>