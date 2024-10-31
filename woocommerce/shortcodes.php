<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class Yeepdf_Addons_Woocommerce_Shortcodes {
	protected static $args = array();
	protected static $order_id = null;
    function __construct(){
        add_filter('yeepdf_shortcodes', array("Yeepdf_Addons_Woocommerce_Shortcodes","yeepdf_shortcodes"),50);
        self::set_shortcodes();
    }
    public function set_order_id($order_id){
        self::$order_id = $order_id;
        self::set_shortcodes();
    }
    public static function yeepdf_shortcodes($shortcodes){
        $shortcode_woo = array();
        $shortcode_woo["Order Details"] = array(
            "yeepdf_woo_order_id"=>"Order ID",
            "yeepdf_woo_order_number"=>"Order Number",
            "yeepdf_woo_order_link"=>"Order URL",
            "yeepdf_woo_order_date"=>"Order Date",
            "yeepdf_woo_order_subtotal"=>"Order Sub-Total",
            "yeepdf_woo_order_total"=>"Order Total",
            "yeepdf_woo_order_total_words" => "Order Total (Words)",
            "yeepdf_woo_order_total_number"=>"Order Total Number",
            "yeepdf_woo_order_fee"=>"Order Fee",
            "yeepdf_woo_order_refunds"=>"Order Refunds",
            "yeepdf_woo_order_count"=>"Order Count",
            "yeepdf_woo_order_quantity_count"=>"Order Quantity Total",
            "yeepdf_woo_order_total_tax"=>"Order Tax",
            "yeepdf_woo_order_discount_total"=>"Order Discount Total",
            "yeepdf_woo_order_status"=>"Order Status",
            "yeepdf_woo_order_currency"=>"Order Currency",
            "yeepdf_woo_order_payment_method"=>"Payment Method",
            "yeepdf_woo_order_shipping_method"=>"Shipping Method",
            "yeepdf_woo_order_detail"=>"Order Detail",
            "yeepdf_woo_order_total_detail"=>"Order Total Detail",
            "yeepdf_woo_order_addresses"=>"Order Addesses",
            "yeepdf_woo_item_download"=>"Order Item Download",
            "yeepdf_woo_store_address"=>"Store Address",
            "yeepdf_woo_order_meta meta_key='change_key'"=>"Order meta",
            "yeepdf_woo_order_product_meta meta_key='change_key'"=>"Order Product meta",
            "yeepdf_woo_order_user_meta meta_key='change_key'"=>"Order User meta",
            "yeepdf_woo_order_signature"=>"Signature",
        );
        $shortcode_woo["Billing"] = array(
            //Billing
            "yeepdf_woo_order_billing"=>"Billing Addeess",
            "yeepdf_woo_billing_firstname"=>"Billing First Name",
            "yeepdf_woo_billing_lastname"=>"Billing Last Name",
            "yeepdf_woo_billing_fullname"=>"Billing Full Name",
            "yeepdf_woo_billing_address"=>"Billing Address",
            "yeepdf_woo_billing_city"=>"Billing City",
            "yeepdf_woo_billing_company"=>"Billing Company",
            "yeepdf_woo_billing_address_1"=>"Billing Address 1",
            "yeepdf_woo_billing_address_2"=>"Billing Address 2",
            "yeepdf_woo_billing_state"=>"Billing State",
            "yeepdf_woo_billing_postcode"=>"Billing Postcode",
            "yeepdf_woo_billing_country"=>"Billing Country",
            "yeepdf_woo_billing_phone"=>"Billing Phone",
            "yeepdf_woo_billing_email"=>"Billing Email",
        );
            $shortcode_woo["Shipping"] = array(
            //Shipping
            "yeepdf_woo_order_shipping"=>"Shipping Address",
            "yeepdf_woo_shipping_firstname"=>"Shipping First Name",
            "yeepdf_woo_shipping_lastname"=>"Shipping Last Name",
            "yeepdf_woo_shipping_fullname"=>"Shipping Full Name",
            "yeepdf_woo_shipping_address"=>"Shipping Address",
            "yeepdf_woo_shipping_city"=>"Shipping City",
            "yeepdf_woo_shipping_company"=>"Shipping Company",
            "yeepdf_woo_shipping_address_1"=>"Shipping Address 1",
            "yeepdf_woo_shipping_address_2"=>"Shipping Address 2",
            "yeepdf_woo_shipping_state"=>"Shipping State",
            "yeepdf_woo_shipping_postcode"=>"Shipping Postcode",
            "yeepdf_woo_shipping_country"=>"Shipping Country",
            "yeepdf_woo_shipping_phone"=>"Shipping Phone",
            "yeepdf_woo_shipping_map_url"=>"Shipping Map URL",
            );
            $shortcode_woo["Customer"] = array(
            //customer
            "yeepdf_woo_customer_id"=>"Customer ID",
            "yeepdf_woo_customer_note"=>"Customer Note",
            "yeepdf_woo_customer_order_notes"=>"Customer Order Notes",
            "yeepdf_woo_customer_ip_address"=>"Customer IP",
            "yeepdf_woo_customer_user_agent"=>"Customer Agent",
            );
            $shortcode_woo["User"] = array(
            ///user
            "yeepdf_woo_user_my_account_url"=>"My Account URL",
            "yeepdf_woo_user_login"=>"User Login",
            "yeepdf_woo_user_set_password"=>"User Set Password",
            "yeepdf_woo_user_set_password_url"=>"User Set Password Link",
            );
        $shortcodes["WooCommerce"] = apply_filters("yeepdf_shortcodes_woocommerce",$shortcode_woo);
        return $shortcodes;
    }
    public static function set_shortcodes($args= array()){
        self::$args = $args;
        $shortcodes = self::yeepdf_shortcodes(array());
        foreach($shortcodes["WooCommerce"] as $group){
            foreach($group as $shortcode_k => $shortcode_v){
                $shortcode_key = explode(" ",$shortcode_k);
                $shortcode_k = $shortcode_key[0];
                add_shortcode($shortcode_k,array("Yeepdf_Addons_Woocommerce_Shortcodes","shortcodes"),10,3); 
            }
        }
    }
    public static function shortcodes($atts, $content="", $shortcode ="" ){
        $order_id = self::$order_id;
        $data_atts = shortcode_atts( array(
			'type' => 'default',
			'show_img' => "hidden",
			'item_sku' => "hidden",
			'item_totals' => "yes",
			'show_des' => "hidden",
			'meta_key' => "",
			'table_border_color' => "#e5e5e5",
			'show_total_price' => ""
		), $atts );
        if(!isset($order_id)){
            $order_demo_id = get_option( "_yeepdf_woocommerce_demo");
            if($order_demo_id !=""){
                $order = wc_get_order( $order_demo_id );
            }
        }else{
            $order = wc_get_order( $order_id ); 
        }
        switch ($shortcode) {
            case 'yeepdf_woo_store_address':
                ob_start();
                ?>
                <address class="address">
                    <?php echo wp_kses_post( get_option( "woocommerce_store_address", "YeePDF Address" ) )?>
                    <?php if ( get_option( "woocommerce_store_city") != "") : ?>
                        <br/><?php echo esc_html( get_option( "woocommerce_store_city") ) ?>
                    <?php endif; ?>
                    <?php if ( get_option( "woocommerce_default_country") != "") : ?>
                        <br/><?php echo esc_html( get_option( "woocommerce_default_country") ) ?>
                    <?php endif; ?>
                    <?php if ( get_option( "woocommerce_store_postcode") != "") : ?>
                        <?php echo esc_html( get_option( "woocommerce_store_postcode") ) ?>
                    <?php endif; ?>
                </address>
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                return $html;
                break;
            case 'yeepdf_woo_user_my_account_url':
                return make_clickable( esc_url( wc_get_page_permalink( 'myaccount' ) ) );
                break;
            case 'yeepdf_woo_order_id':
                if(isset($order) && $order != null){
                    return $order->get_id();
                }else{
                    return 1;
                }
                break;
            case 'yeepdf_woo_order_number':
                if(isset($order) && $order != null){
                    return $order->get_order_number();
                }else{
                    return 1;
                }
                break;
            case 'yeepdf_woo_order_link':
                if(isset($order) && $order != null){
                    return $order->get_view_order_url();
                }else{
                    return get_home_url().'/my-account/view-order/1';
                }
                break;
            case 'yeepdf_woo_order_date':
                if(isset($order) && $order != null){
                    return wc_format_datetime( $order->get_date_created() );
                }else{
                    return date(get_option('date_format'));
                }
                break;
            case 'yeepdf_woo_order_status':
                if(isset($order) && $order != null){
                    return $order->get_status();
                }else{
                    return 'Completed';
                }
                break;
            case 'yeepdf_woo_order_currency':
                if(isset($order) && $order != null){
                    return $order->get_currency();
                }else{
                    return "USD";
                }
                break;
            case 'yeepdf_woo_order_payment_method':
                if(isset($order) && $order != null){
                    return $order->get_payment_method_title();
                }else{
                    return "PayPal";
                }
                break;
            case 'yeepdf_woo_order_shipping_method':
                if(isset($order) && $order != null){
                    return $order->get_shipping_method();
                }else{
                    return "Standard";
                }
                break;
            case 'yeepdf_woo_order_subtotal':
                if(isset($order) && $order != null){
                    return $order->get_subtotal(); 
                }else{
                    return "48.00";
                }
                break;
            case 'yeepdf_woo_order_total':
                if(isset($order) && $order != null){
                    return  $order->get_total(); 
                }else{
                    return  "48.00";
                }
                break;
            case 'yeepdf_woo_order_total_number':
                if(isset($order) && $order != null){
                    return  $order->get_total(); 
                }else{
                    return  "48.00";
                }
                break;
            case 'yeepdf_woo_order_fee':
                $order_fee_total = 0;
                if(isset($order) && $order != null){
                    foreach ( $order->get_fees() as $fee_id => $fee ) {
                        $order_fee_total += $fee->get_total();
                    }  
                }
                return $order_fee_total;
                break;
            case 'yeepdf_woo_order_total_tax':
                if(isset($order) && $order != null){
                    return $order->get_total_tax(); 
                }else{
                    return 0;
                }
                break;
            case 'yeepdf_woo_order_discount_total':
                if(isset($order) && $order != null){
                    return $order->get_total_discount(); 
                }else{
                    return 0;
                }
                break;
            case 'yeepdf_woo_order_count':
                if(isset($order) && $order != null){
                    return wc_orders_count("completed");  
                }else{
                    return "999";
                }
                break;
            case 'yeepdf_woo_order_quantity_count':
                $total_quantity = 0;
                if(isset($order) && $order != null){
                    foreach ( $order->get_items() as $item_id => $item ) {
                        $quantity = $item->get_quantity();
                        $total_quantity += $quantity;
                    }   
                }
                return $total_quantity;
                break;
            case 'yeepdf_woo_order_refunds':
                $refund = 0;
                if(isset($order) && $order != null){
                    $totals = $order->get_order_item_totals();
                        foreach ( $totals as $index => $value ) {
                            if ( strpos( $index, 'refund' ) !== false ) {
                            $refund= $order->get_total_refunded();
                            break;
                        }
                    }
                }
                return $refund;
                break;
            case 'yeepdf_woo_order_detail':
                if(isset($order) && $order != null){
                    return self::product_details($data_atts,$order);
                }else{
                    return self::product_details($data_atts);
                }
                break;
            case 'yeepdf_woo_order_total_detail':
                if(isset($order) && $order != null){
                    return self::order_total($data_atts,$order);
                }else{
                    return self::order_total($data_atts);
                }
                break;
            case 'woo_builder_order_shiping':
                if(isset($order) && $order != null){
                    return self::order_shipping($order);
                }else{
                    return self::order_shipping();
                }
                break;
            case 'yeepdf_woo_item_download':
                ob_start();
                if(isset($order) && $order != null){
                    $downloads = $order->get_downloadable_items();
                    if(count($downloads) > 0 ){
                        include YEEPDF_BUILDER_WOOCOMMERCE_PATH."woocommerce/templates/emails/email-downloads.php";
                    }
                }else{
                    include YEEPDF_BUILDER_WOOCOMMERCE_PATH."woocommerce/templates/emails/email-downloads-default.php";
                }
                $html = ob_get_contents();
                ob_end_clean();
                return $html;
                break;
            case 'yeepdf_woo_order_meta':
                if(isset($order) && $order != null){
                    return $order->get_meta($data_atts["meta_key"]);
                }else{
                    return "Order meta ".$data_atts["meta_key"];
                }
                break;
            case 'yeepdf_woo_order_product_meta':
                if(isset($order) && $order != null){
                    $order_items    = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
                    $order_data ="";
                    foreach ( $order_items as $item_id => $item ) { 
                        $order_data = wc_get_order_item_meta( $item_id, $data_atts["meta_key"], true ); 
                        break;
                    }
				return $order_data;
                }else{
                    return "Product meta ".$data_atts["meta_key"];
                }
                break;
            case 'yeepdf_woo_order_user_meta':
                if(isset($order) && $order != null){
                    $users = $order->get_user();
                    if( isset( $users->data->{$data_atts["meta_key"]} )){
                        return $users->data->{$data_atts["meta_key"]};
                    }else{
                        if(isset($users->ID)){
                            $user_id = $users->ID;
                            return get_user_meta($user_id,$data_atts["meta_key"], true );
                        }else{
                            return "";
                        }
                    }
                }else{
                    return "User meta ".$data_atts["meta_key"]; 
                }
                break;
            case 'yeepdf_woo_order_signature':
                if(isset($order) && $order != null){
                    if( $order->get_meta('woocommerce_signature_name_data') != "") {
                        return '<img src="'.esc_url($order->get_meta('woocommerce_signature_name_data')).'" alt="Signature">';
                    }
                    return esc_html__("No Signature","pdf-customizer-for-woocommerce");
                }else{
                    return '<img src="'.esc_url(YEEPDF_BUILDER_WOOCOMMERCE_URL.'images/default-image.png').'" style="width: 200px; height: 100px;" alt="Signature">';
                }
                break;
            case 'yeepdf_woo_order_total_words':
                if(isset($order) && $order != null){
                    return self::convert_number_to_words($order->get_total());
                }else{
                    return self::convert_number_to_words(12345);
                }
                break;
            //Addresses
            case 'yeepdf_woo_order_addresses':
                if(isset($order) && $order != null){
                    return self::order_addresses($order);
                }else{
                    return self::order_addresses();
                }
                break;
            //Billing
            case 'yeepdf_woo_order_billing':
                if(isset($order) && $order != null){
                    return self::order_billing($order);
                }else{
                    return self::order_billing();
                }
                break;
            case 'yeepdf_woo_billing_firstname':
                if(isset($order) && $order != null){
                    return $order->get_billing_first_name(); 
                }else{
                    return "Tayler";
                }
                break;
            case 'yeepdf_woo_billing_lastname':
                if(isset($order) && $order != null){
                    return $order->get_billing_last_name(); 
                }else{
                    return "Holder";
                }
                break;
            case 'yeepdf_woo_billing_fullname':
                if(isset($order) && $order != null){
                    return $order->get_formatted_billing_full_name(); 
                }else{
                    return "Tayler Holder";
                }
                break;
            case 'yeepdf_woo_billing_address':
                if(isset($order) && $order != null){
                    return $order->get_formatted_billing_address();
                }else{
                    return self::order_billing();
                }
                break;
            case 'yeepdf_woo_billing_city':
                if(isset($order) && $order != null){
                    return $order->get_billing_city(); 
                }else{
                    return "City Name";
                }
                break;
            case 'yeepdf_woo_billing_company':
                if(isset($order) && $order != null){
                    return $order->get_billing_company(); 
                }else{
                    return "YeeAdd-ons";
                }
                break;
            case 'yeepdf_woo_billing_address_1':
                if(isset($order) && $order != null){
                    return $order->get_billing_address_1(); 
                }else{
                    return "7400 Edwards Rd";
                }
                break;
            case 'yeepdf_woo_billing_address_2':
                if(isset($order) && $order != null){
                    return $order->get_billing_address_2(); 
                }else{
                    return "7422 Edwards Rd";
                }
                break;
            case 'yeepdf_woo_billing_state':
                if(isset($order) && $order != null){
                    return $order->get_billing_state(); 
                }else{
                    return "Mayville";
                }
                break;
            case 'yeepdf_woo_billing_postcode':
                if(isset($order) && $order != null){
                    return $order->get_billing_postcode(); 
                }else{
                    return "511000";
                }
                break;
            case 'yeepdf_woo_billing_phone':
                if(isset($order) && $order != null){
                    return $order->get_billing_phone(); 
                }else{
                    return "(820) 555-999";
                }
                break;
            case 'yeepdf_woo_billing_email':
                if(isset($order) && $order != null){
                    return $order->get_billing_email(); 
                }else{
                    return get_option( 'admin_email' );
                }
                break;
            case 'yeepdf_woo_billing_country':
                if(isset($order) && $order != null){
                    return $order->get_billing_country(); 
                }else{
                    return "US";
                }
                break;
            //Shipping
            case 'yeepdf_woo_order_shipping':
                if(isset($order) && $order != null){
                    return self::order_shipping($order);
                }else{
                    return self::order_shipping();
                }
                break;
            case 'yeepdf_woo_shipping_firstname':
                if(isset($order) && $order != null){
                    return $order->get_shipping_first_name(); 
                }else{
                    return "Tayler";
                }
                break;
            case 'yeepdf_woo_shipping_lastname':
                if(isset($order) && $order != null){
                    return $order->get_shipping_last_name(); 
                }else{
                    return "Holder";
                }
                break;
            case 'yeepdf_woo_shipping_fullname':
                if(isset($order) && $order != null){
                    return $order->get_formatted_shipping_full_name(); 
                }else{
                    return "Tayler Holder";
                }
                break;
            case 'yeepdf_woo_shipping_address':
                if(isset($order) && $order != null){
                    return $order->get_billing_country(); 
                }else{
                    return self::order_shipping();
                }
                //return $order->get_formatted_shipping_address();
                break;
            case 'yeepdf_woo_shipping_company':
                if(isset($order) && $order != null){
                    return $order->get_shipping_company(); 
                }else{
                    return "YeeAdd-ons";
                }
                break;
            case 'yeepdf_woo_shipping_address_1':
                if(isset($order) && $order != null){
                    return $order->get_shipping_address_1(); 
                }else{
                    return "7400 Edwards Rd";
                }
                break;
            case 'yeepdf_woo_shipping_address_2':
                if(isset($order) && $order != null){
                    return $order->get_shipping_address_2(); 
                }else{
                    return "7422 Edwards Rd";
                }
                break;
            case 'yeepdf_woo_shipping_city':
                if(isset($order) && $order != null){
                    return $order->get_shipping_city(); 
                }else{
                    return "Mayville";
                }
                break;
            case 'yeepdf_woo_shipping_state':
                if(isset($order) && $order != null){
                    return $order->get_shipping_state(); 
                }else{
                    return "Mayville";
                }
                break;
            case 'yeepdf_woo_shipping_postcode':
                if(isset($order) && $order != null){
                    return $order->get_shipping_postcode(); 
                }else{
                    return "511000";
                }
                break;
            case 'yeepdf_woo_shipping_country':
                if(isset($order) && $order != null){
                    return $order->get_shipping_country(); 
                }else{
                    return "US";
                }
                break;
            case 'yeepdf_woo_shipping_phone':
                if(isset($order) && $order != null){
                    return $order->get_shipping_phone(); 
                }else{
                    return "(820) 555-999";
                }
                break;
            case 'yeepdf_woo_shipping_map_url':
                if(isset($order) && $order != null){
                    return $order->get_shipping_address_map_url(); 
                }else{
                    return "https://www.google.com/maps/";
                }
                break;
            //customer
            case 'yeepdf_woo_customer_id':
                if(isset($order) && $order != null){
                    return $order->get_customer_id(); 
                }else{
                    return "1";
                }
                break;    
            case 'yeepdf_woo_customer_note':
                if(isset($order) && $order != null){
                    return $order->get_customer_note(); 
                }else{
                    return "Customer note message";
                }
                break;
            case 'yeepdf_woo_customer_order_notes':
                if(isset($order) && $order != null){
                    $notes = $order->get_customer_order_notes();
                    return self::customer_notes($notes);
                }else{
                    return '<p>This is some customer note , just some dummy text nothing to see here</p>';
                }
                break;
            case 'yeepdf_woo_customer_ip_address':
                if(isset($order) && $order != null){
                    return $order->get_customer_ip_address(); 
                }else{
                    return "192.168.1.1";
                }
                break;
            case 'yeepdf_woo_customer_user_agent':
                if(isset($order) && $order != null){
                    return $order->get_customer_user_agent(); 
                }else{
                    return "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36";
                }
                break;
            //Customer User
            case 'yeepdf_woo_user_id':
                if(isset($args["user_id"])){
                    return $args["user_id"];
                }else{
                    $current_user = wp_get_current_user();
				    return $current_user->user_login;
                }
                break;
            case "yeepdf_woo_user_login":
                if(isset($args["user_login"])){
                    return $args["user_login"];
                }else{
                    $current_user = wp_get_current_user();
				    return $current_user->user_login;
                }
                break;
            case 'yeepdf_woo_user_set_password_url':
                if(isset($args["reset_key"])){
                    return esc_url( add_query_arg( array( 'key' => $args["reset_key"], 'id' => $args["user_id"] ), wc_get_endpoint_url( 'lost-password', '', wc_get_page_permalink( 'myaccount' ) ) ) );
                }else{
                    return esc_url( wc_get_endpoint_url( 'lost-password', '', wc_get_page_permalink( 'myaccount' ) ) ) ;
                }
                break;
            case 'yeepdf_woo_user_set_password':
                if(isset($args["reset_key"])){
                    return '<a class="link" href="'. esc_url( add_query_arg( array( 'key' => $args["reset_key"], 'id' => $args["user_id"] ), wc_get_endpoint_url( 'lost-password', '', wc_get_page_permalink( 'myaccount' ) ) ) ).'">'.esc_html__( 'Click here to reset your password', 'woocommerce' ).'</a>';
                }else{
                    return '<a class="link" href="#">'.esc_html__( 'Click here to reset your password', 'woocommerce' ).'</a>';
                }
                break;
            default:
                return apply_filters( "yeepdf_shortcodes_woocommerce_action", $shortcode,$order);
                break;
        }
    }
    public static function product_details( $atts = null,  $order = null ){
        $args = self::$args;
        ob_start();
        if(isset($order) && $order != null){
            include YEEPDF_BUILDER_WOOCOMMERCE_PATH."woocommerce/templates/emails/email-order-details.php";
        }else{
            include YEEPDF_BUILDER_WOOCOMMERCE_PATH."woocommerce/templates/emails/email-order-details-default.php";
        }
		$html = ob_get_contents();
        ob_end_clean();
        return $html;
	}
	public static function order_total( $atts =null, $order = null){
		$args = self::$args;
        $sent_to_admin = false;
        $plain_text = false;
        $email = "";
        foreach( $args as $key => $woo_datas ){
            $$key = $woo_datas;
        }
        ob_start();
        if(isset($order) && $order != null){
            include YEEPDF_BUILDER_WOOCOMMERCE_PATH."woocommerce/templates/emails/order-total.php";
        }else{
            include YEEPDF_BUILDER_WOOCOMMERCE_PATH."woocommerce/templates/emails/order-total-default.php";
        }
		$html = ob_get_contents();
        ob_end_clean();
        return $html;
	}
	public static function order_addresses($order = null){
		$args = self::$args;
        $sent_to_admin = false;
        $plain_text = false;
        $email = "";
        foreach( $args as $key => $woo_datas ){
            $$key = $woo_datas;
        }
		ob_start();
        if(isset($order) && $order != null){
		    include YEEPDF_BUILDER_WOOCOMMERCE_PATH."woocommerce/templates/emails/email-addresses.php";
        }else{
            include YEEPDF_BUILDER_WOOCOMMERCE_PATH."woocommerce/templates/emails/email-addresses-default.php";
        }
		$html = ob_get_contents();
        ob_end_clean();
        return $html;
	}
	public static function order_billing($order = null){
		$args = self::$args;
        $sent_to_admin = false;
        $plain_text = false;
        $email = "";
        foreach( $args as $key => $woo_datas ){
            $$key = $woo_datas;
        }
		ob_start();
        if(isset($order) && $order != null){
            $address    = $order->get_formatted_billing_address();
            ?>
            <div class="address">
				<?php echo wp_kses_post( $address ? $address : esc_html__( 'N/A', 'woocommerce' ) ); ?>
				<?php if ( $order->get_billing_phone() ) : ?>
					<br/><?php echo wc_make_phone_clickable( $order->get_billing_phone() ); ?>
				<?php endif; ?>
				<?php if ( $order->get_billing_email() ) : ?>
					<br/><?php echo esc_html( $order->get_billing_email() ); ?>
				<?php endif; ?>
			</div>
            <?php
        }else{
            ?>
            <div class="address">
                Tayler Holder<br>
                YeeMail<br>
                7400 Edwards Rd<br>
                Edwards Rd<br>
                (820) 555-999
			</div>
            <?php
        }
		$html = ob_get_contents();
        ob_end_clean();
        return $html;
	}
	public static function order_shipping($order = null){
		$args = self::$args;
        $sent_to_admin = false;
        $plain_text = false;
        $email = "";
        foreach( $args as $key => $woo_datas ){
            $$key = $woo_datas;
        }
		ob_start();
        if(isset($order) && $order != null){
            $shipping   = $order->get_formatted_shipping_address();
            if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() && $shipping ) : 
                ?>
                <div class="address">
                    <?php echo wp_kses_post( $shipping ); ?>
                    <?php if ( $order->get_shipping_phone() ) : ?>
                        <br /><?php echo wc_make_phone_clickable( $order->get_shipping_phone() ); ?>
                    <?php endif; ?>
                </div>
                <?php
            endif;
        }else{
            ?>
            <div class="address">
                Tayler Holder<br>
                YeeMail<br>
                7400 Edwards Rd<br>
                Edwards Rd<br>
                (820) 555-999
			</div>
            <?php
        }
		$html = ob_get_contents();
        ob_end_clean();
        return $html;
	}
	public static function customer_notes($notes){
		ob_start();
		foreach ($notes as $note){
			?>
			<p>
			<?php echo wp_filter_post_kses($note->comment_content); ?>
			</p>
			<?php
		}
		$html = ob_get_contents();
        ob_end_clean();
        return $html;
	}
    public static function convert_number_to_words($number) {
        $hyphen      = ' ';
        $conjunction = ' and ';
        $separator   = ',';
        $negative    = 'negative ';
        $decimal     = ' point ';
        $dictionary  = array(
            0                   => 'zero',
            1                   => 'one',
            2                   => 'two',
            3                   => 'three',
            4                   => 'four',
            5                   => 'five',
            6                   => 'six',
            7                   => 'seven',
            8                   => 'eight',
            9                   => 'nine',
            10                  => 'ten',
            11                  => 'eleven',
            12                  => 'twelve',
            13                  => 'thirteen',
            14                  => 'fourteen',
            15                  => 'fifteen',
            16                  => 'sixteen',
            17                  => 'seventeen',
            18                  => 'eighteen',
            19                  => 'nineteen',
            20                  => 'twenty',
            30                  => 'thirty',
            40                  => 'fourty',
            50                  => 'fifty',
            60                  => 'sixty',
            70                  => 'seventy',
            80                  => 'eighty',
            90                  => 'ninety',
            100                 => 'hundred',
            1000                => 'thousand',
            1000000             => 'million',
            1000000000          => 'billion',
            1000000000000       => 'trillion',
            1000000000000000    => 'quadrillion',
            1000000000000000000 => 'quintillion'
        );
        if (!is_numeric($number)) {
            return false;
        }
        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }
        if ($number < 0) {
            return $negative . self::convert_number_to_words(abs($number));
        }
        $string = $fraction = null;
        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }
        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . self::convert_number_to_words($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = self::convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= self::convert_number_to_words($remainder);
                }
                break;
        }
        if (null !== $fraction && is_numeric($fraction)) {
            if($fraction != 0){
                $string .= $decimal;
                $words = array();
                foreach (str_split((string) $fraction) as $number) {
                    $words[] = $dictionary[$number];
                }
                $string .= implode(' ', $words);
            }
        }
        return $string;
    }
}
new Yeepdf_Addons_Woocommerce_Shortcodes;