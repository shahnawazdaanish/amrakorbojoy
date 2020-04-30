<?php
/*
	Plugin Name: SSLCommerz
	Plugin URI: http://www.sslommerz.com.bd
	Description: SSLCommerz Online Payment Gateway
	Author: Prabal Mallick
	Email:prabal.mallick@sslwireless.com
	Author URI: http://example.com
	Version: 1.0.0
	Copyright: © 20018-2019 SSL Wireless.
	*/
if (!defined('ABSPATH')) exit; // Exit if accessed directly

add_action('admin_menu', 'main');

add_action('plugins_loaded', array(Sslc_success_url::get_instance(), 'setup'));
add_action('plugins_loaded', array(Sslc_fail_url::get_instance(), 'setup'));
add_action('plugins_loaded', array(Sslc_cancel_url::get_instance(), 'setup'));
add_action('plugins_loaded', array(Sslc_ipn_url::get_instance(), 'setup'));

global $wpdb;
$table_name = $wpdb->prefix . 'sslcommerz_payment';

if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
    $charset_collate = 'ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
    $sql = "CREATE TABLE $table_name (
            `tran_sys_id` bigint(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `trxid` varchar(50) NOT NULL,
            `tran_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `tran_status` varchar(50) NOT NULL,
            `card_type` varchar(100) NULL,
            -- `card_no` varchar(50) NOT NULL,
            `total_amount` float(10) NOT NULL,
            `product_name` varchar(300) NOT NULL,
            `cus_name` varchar(50) NOT NULL,
            `cus_email` varchar(150) NOT NULL,
            `cus_phone` varchar(20) NOT NULL,
            -- `nid_pass` varchar(40) NULL,
            `cus_address` varchar(1000) NOT NULL,
            `cus_country` varchar(50) NOT NULL,
            `cus_city` varchar(50) NOT NULL,
            `cus_postcode` varchar(50) NOT NULL,
            `ipn_status` varchar(150) NULL
      ) $charset_collate;";

    //echo $sql."<br>";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function main()
{
    // Add a new top-level menu (ill-advised):
    add_menu_page(__('SSLCOMMERZ', 'menu-sslcommerz'), __('SSLCOMMERZ', 'menu-sslcommerz'), 'manage_options', 'sslcommerz', 'SSLCommerz');
    if (get_option('orderhistory') == "on") {
        add_menu_page(
            'Order History', // page title
            'Order History', // menu title
            'manage_options', // capability
            'order-list', // menu slug
            'wpse_ordertList_render', // callback function
            '',
            6
        );
    }
}

// mt_toplevel_page() displays the page content for the custom Test Toplevel menu
function SSLCommerz()
{
    echo "<h1>" . __('SSLCommerz Configuration', 'menu-sslcommerz') . "</h1>";
    include_once('form.php');
    if (get_option('enable')  == "on") {
        if (!file_exists(get_template_directory() . "/sslcommerz_init.php")) {
            copy(plugin_dir_path(__FILE__) . "sslcommerz/sslcommerz_init.php", get_template_directory() . "/sslcommerz_init.php");
        }
        if (!file_exists(get_template_directory() . "/SSLCommerz.png")) {
            copy(plugin_dir_path(__FILE__) . "images/SSLCommerz.png", get_template_directory() . "/SSLCommerz.png");
        }
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
function getSSLData()
{
    $setData = array();
    $setData['activity']         = get_option('enable');
    $setData['title']             = get_option('title');
    $setData['description']     = get_option('description');
    $setData['storeid']         = get_option('store');
    $setData['storepass']         = get_option('password');
    $setData['testmode']         = get_option('testmode');
    $setData['orderhistory']     = get_option('orderhistory');
    if (get_option('testmode') == "on") {
        $setData['SslcURL'] = "https://sandbox.sslcommerz.com/gwprocess/v3/api.php";
        $setData['SslcValURL'] = "https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php";
    } else {
        $setData['SslcURL'] = "https://securepay.sslcommerz.com/gwprocess/v3/api.php";
        $setData['SslcValURL'] = "https://securepay.sslcommerz.com/validator/api/validationserverAPI.php";
    }
    return $setData;
}

?>
<?php

function wpse_ordertList_render()
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
    $table_name = $wpdb->prefix . 'sslcommerz_payment';

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

class Sslc_success_url
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
        add_rewrite_rule('sslcsuccess/?$', 'index.php?sslcsuccess', 'top');
    }
    public function flush_rules()
    {
        $this->rewrite_rules();
        flush_rewrite_rules();
    }
    public function query_vars($vars)
    {
        $vars[] = 'sslcsuccess';
        return $vars;
    }
    public function parse_request($wp)
    {
        if (array_key_exists('sslcsuccess', $wp->query_vars)) {
            include plugin_dir_path(__FILE__) . 'sslcommerz/sslcommerz_success.php';
            exit();
        }
    }
}

class Sslc_fail_url
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
class Sslc_cancel_url
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
class Sslc_ipn_url
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