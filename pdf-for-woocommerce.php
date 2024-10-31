<?php
/**
 * Plugin Name: PDF Invoices for WooCommerce + Drag And Drop Template Builder
 * Requires Plugins: woocommerce
 * Description: WooCommerce PDF Customizer is a helpful tool that helps you build and customize the PDF Templates for WooCommerce.
 * Text Domain: pdf-for-woocommerce
 * Domain Path: /languages
 * Version: 4.0.1
 * Requires PHP: 5.6
 * Author: add-ons.org
 * Author URI: https://add-ons.org/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
define( 'YEEPDF_BUILDER_WOOCOMMERCE_URL', plugin_dir_url( __FILE__ ) );
define( 'YEEPDF_BUILDER_WOOCOMMERCE_PATH', plugin_dir_path( __FILE__ ) );
if(!class_exists('Yeepdf_Creator_Builder')) {
    require 'vendor/autoload.php';
    if(!defined('YEEPDF_CREATOR_BUILDER_PATH')) {
        define( 'YEEPDF_CREATOR_BUILDER_PATH', plugin_dir_path( __FILE__ ) );
    }
    if(!defined('YEEPDF_CREATOR_BUILDER_URL')) {
        define( 'YEEPDF_CREATOR_BUILDER_URL', plugin_dir_url( __FILE__ ) );
    }
    class Yeepdf_Creator_Builder {
        function __construct(){
            $dir = new RecursiveDirectoryIterator(YEEPDF_CREATOR_BUILDER_PATH."backend");
            $ite = new RecursiveIteratorIterator($dir);
            $files = new RegexIterator($ite, "/\.php/", RegexIterator::MATCH);
            foreach ($files as $file) {
                if (!$file->isDir()){
                    require_once $file->getPathname();
                }
            }
            include_once YEEPDF_CREATOR_BUILDER_PATH."libs/phpqrcode.php";
            include_once YEEPDF_CREATOR_BUILDER_PATH."frontend/index.php";
        }
    }
    new Yeepdf_Creator_Builder;
}
class Yeepdf_Creator_Woocommerce_Builder { 
    function __construct(){
        include YEEPDF_BUILDER_WOOCOMMERCE_PATH."woocommerce/backend/pdf_table.php";
        include YEEPDF_BUILDER_WOOCOMMERCE_PATH."woocommerce/shortcodes.php";
        include YEEPDF_BUILDER_WOOCOMMERCE_PATH."woocommerce/backend/index.php";
        include YEEPDF_BUILDER_WOOCOMMERCE_PATH."woocommerce/emails/index.php";
        include YEEPDF_BUILDER_WOOCOMMERCE_PATH."woocommerce/demo/demo.php";
        add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array($this,'add_link') );
        register_activation_hook( __FILE__, array($this,'activation') );
        include YEEPDF_BUILDER_WOOCOMMERCE_PATH."superaddons/check_purchase_code.php";
        new Superaddons_Check_Purchase_Code( 
            array(
                "plugin" => "pdf-for-woocommerce/pdf-for-woocommerce.php",
                "id"=>"1708",
                "pro"=>"https://add-ons.org/plugin/woocommerce-pdf-customizer-invoices-packing-slips/",
                "plugin_name"=> "PDF For WooCommerce",
                "document"=>"https://pdf.add-ons.org/document/"
            )
        );
    }
    function add_link( $actions ) {
        $actions[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=wc-settings&tab=settings_pdfs') ) .'">'.esc_html__("Settings","pdf-customizer-for-woocommerce").'</a>'; 
        $actions[] = '<a target="_blank" href="https://pdf.add-ons.org/document/" target="_blank">'.esc_html__( "Document", "pdf-for-wpforms" ).'</a>';
        $actions[] = '<a target="_blank" href="https://add-ons.org/supports/" target="_blank">'.esc_html__( "Supports", "pdf-for-wpforms" ).'</a>';
        return $actions;
    }
    function activation() {
        global $wpdb;
        //install data
        include_once(ABSPATH.'wp-admin/includes/plugin.php');
        $pdf_creator = $wpdb->prefix.'vc_pdf_invoices';
        if( $wpdb->get_var("SHOW TABLES LIKE '$pdf_creator'") != $pdf_creator ) {
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE $pdf_creator (
                id INT NOT NULL AUTO_INCREMENT,
                enable INT NULL,
                template_id INT NULL,
                label VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                name VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                enable_order VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                attachments VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                password VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                my_account_buttons VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                my_account_order_status VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                conditional_logic INT NULL,
                conditional_logic_datas VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                PRIMARY KEY (id)
            ) $charset_collate;";
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );
            //install template
            $string = file_get_contents(YEEPDF_BUILDER_WOOCOMMERCE_PATH."woocommerce/demo/woocommerce-demo.json");
            $id_map_pdf = array();
            $datas_templates = explode("\n", $string);
            foreach( $datas_templates as $datas_template ){
                $settings_datas = explode("|||yeepdf_data|||",$datas_template);
                if(count($settings_datas) > 1){
                    $template_content = $settings_datas[1];
                    $settings_data = explode(",",$settings_datas[0]);
                    foreach ($settings_data as $setting){
                        $main_settings = explode(":",$setting);
                        switch($main_settings[0]){
                            case "type":
                                $type = $main_settings[1];
                            break;
                            case "title":
                                $title = $main_settings[1];
                            break;
                        }
                    }
                    $my_template = array(
                        'post_title'    => $title,
                        'post_content'  => "",
                        'post_status'   => 'publish',
                        'post_type'     => 'yeepdf'
                    );
                    $id_template = wp_insert_post( $my_template );
                    $id_map_pdf[$type] = $id_template;
                    add_post_meta($id_template,"data_email",$template_content);
                }
            }
            //install settings woo
            foreach($id_map_pdf as $type => $template_id){
                if($type == "invoice"){
                    $atts_data = array(
                        "enable" => 1,
                        "label" => "Invoice",
                        "template_id" => $template_id,
                        "name" => "[yeepdf_woo_order_id]-invoice",
                        "enable_order" => json_encode(array("on-hold","processing_order","completed")),
                        "attachments" => json_encode(array("on-hold","processing_order","completed")),
                        "my_account_buttons"=>"available",
                        "my_account_order_status"=>json_encode(array("on-hold","processing_order","completed")),
                        "conditional_logic" => 0
                    );
                }else{
                    $atts_data = array(
                        "enable" => 1,
                        "label" => "Packing Slip",
                        "template_id" => $template_id,
                        "name" => "[yeepdf_woo_order_id]-packing-slip",
                        "enable_order" => json_encode(array("on-hold","processing_order","completed")),
                        "attachments" => json_encode(array()),
                        "my_account_buttons"=>"never",
                        "my_account_order_status"=>json_encode(array("on-hold","processing_order","completed")),
                        "conditional_logic" => 0
                    );
                }
                $wpdb->insert(
                    $pdf_creator,
                    $atts_data
                );
            }
        }
        
    }
}
new Yeepdf_Creator_Woocommerce_Builder;
if(!class_exists('Superaddons_List_Addons')) {  
    include YEEPDF_BUILDER_WOOCOMMERCE_PATH."add-ons.php"; 
}
