<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class Yeepdf_Demo_Woocommerce {
	function __construct() { 
		add_action("builder_yeepdfs",array($this,"builder_yeepdfs"),1);
        
	}
	function builder_yeepdfs(){
        $args = array(
            array(
            "json"=>YEEPDF_BUILDER_WOOCOMMERCE_URL."woocommerce/demo/woocommerce-1.json",
            "img"=>YEEPDF_BUILDER_WOOCOMMERCE_URL."woocommerce/demo/images/1.png",
            "title" => "WooCommerce Template 1",
            "id"=> 26,
            ),
            array(
            "json"=>YEEPDF_BUILDER_WOOCOMMERCE_URL."woocommerce/demo/woocommerce-2.json",
            "img"=>YEEPDF_BUILDER_WOOCOMMERCE_URL."woocommerce/demo/images/2.png",
            "title" => "WooCommerce Template 2",
            "id"=> 27,
            ),
            array(
            "json"=>YEEPDF_BUILDER_WOOCOMMERCE_URL."woocommerce/demo/woocommerce-3.json",
            "img"=>YEEPDF_BUILDER_WOOCOMMERCE_URL."woocommerce/demo/images/3.png",
            "title" => "WooCommerce Template 3",
            "id"=> 33,
            ),
            array(
            "json"=>YEEPDF_BUILDER_WOOCOMMERCE_URL."woocommerce/demo/woocommerce-4.json",
            "img"=>YEEPDF_BUILDER_WOOCOMMERCE_URL."woocommerce/demo/images/4.png",
            "title" => "WooCommerce Template 4",
            "id"=> 34,
            ),
            array(
            "json"=>YEEPDF_BUILDER_WOOCOMMERCE_URL."woocommerce/demo/woocommerce-5.json",
            "img"=>YEEPDF_BUILDER_WOOCOMMERCE_URL."woocommerce/demo/images/5.png",
            "title" => "WooCommerce Template 5",
            "id"=> 35,
            ), 
            array(
            "json"=>YEEPDF_BUILDER_WOOCOMMERCE_URL."woocommerce/demo/woocommerce-6.json",
            "img"=>YEEPDF_BUILDER_WOOCOMMERCE_URL."woocommerce/demo/images/6.png",
            "title" => "WooCommerce Template 6",
            "id"=> 16,
            ), 
            array(
            "json"=>YEEPDF_BUILDER_WOOCOMMERCE_URL."woocommerce/demo/woocommerce-7.json",
            "img"=>YEEPDF_BUILDER_WOOCOMMERCE_URL."woocommerce/demo/images/7.png",
            "title" => "WooCommerce Template 7",
            "id"=> 92,
            ),       
        );
        foreach ($args as $value) {
            Yeepdf_Settings_Builder_PDF_Backend::item_demo($value);
        }
	}
}
new Yeepdf_Demo_Woocommerce;