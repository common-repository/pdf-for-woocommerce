<?php
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}
class Yeepdf_Buider_Woocommerece_Block{
    private $blocks = array();
    function __construct(){
        add_action("pdf_builder_block",array($this,"block_woocommerce"),200);
        add_filter( 'pdf_builder_block_html', array($this,"block_woocommerce_builder") );
        $this->blocks = $this->get_block();
    }
    function get_block(){
        $data = array(
            array(
                "type"=>"order_detail",
                "title" => esc_html__("Order Detail","pdf-customizer-for-woocommerce"),
                "icon" => "emailbuilder-icon icon-woo",
                "shortcode" => '[woo_builder_order_product_details type="default" show_img="true" img_width="250" show_img="true" item_totals="true"]'
            ),
            array(
                "type"=>"order_billing",
                "title" => esc_html__("Billing","pdf-customizer-for-woocommerce"),
                "icon" => "emailbuilder-icon icon-woo",
                "shortcode" => "[woo_builder_order_billing]"
            ),
            array(
                "type"=>"order_shipping",
                "title" => esc_html__("Shipping","pdf-customizer-for-woocommerce"),
                "icon" => "emailbuilder-icon icon-woo",
                "shortcode" => "[woo_builder_order_shiping]"
            ),
            array(
                "type"=>"customer_notes",
                "title" => esc_html__("Customer Notes","pdf-customizer-for-woocommerce"),
                "icon" => "emailbuilder-icon icon-woo",
                "shortcode" => "[woo_builder_customer_notes]"
            ),
        );
        return $data;
    }
    function block_woocommerce(){
        foreach ($this->blocks as $value) {
            ?>
            <li>
                <div class="momongaDraggable" data-type="<?php echo esc_attr($value["type"]) ?>">
                    <i class="emailbuilder-icon <?php echo esc_html($value["icon"]) ?>"></i>
                    <div class="pdfbuilder-tool-text"><?php echo esc_html($value["title"]) ?></div>
                </div>
            </li>
            <?php
        }
    }
    function block_woocommerce_builder($type){
        foreach ($this->blocks as $value) {
            $type["block"][esc_attr($value["type"])]["builder"] = '
            <div class="builder-elements">
                <div class="builder-elements-content" data-type="'.esc_attr($value["type"]).'">
                    <div class="text-content-data hidden">'.esc_attr($value["shortcode"]).'</div>
                    <div class="text-content">'.do_shortcode($value["shortcode"]).'</div>
                </div>
            </div>';
            $type["block"][esc_attr($value["type"])]["editor"]["container"]["show"]= ["text-align","padding","background","html"];
            $padding = pdfbuilder_email_global_data::$padding;
            $text_align = pdfbuilder_email_global_data::$text_align;
            $container_style = array(
                    ".builder__editor--item-background .builder__editor_color"=>"background-color",
                    ".builder__editor--item-background .image_url"=>"background-image",
                );
            $type["block"][esc_attr($value["type"])]["editor"]["container"]["style"]= array_merge($padding,$container_style,$text_align);
            $type["block"][esc_attr($value["type"])]["editor"]["inner"]["style"]= array();
            $type["block"][esc_attr($value["type"])]["editor"]["inner"]["attr"] = array(".text-content"=>array(".builder__editor--html .builder__editor--js"=>"html"),".text-content-data"=>array(".builder__editor--html .builder__editor--js"=>"html_hide"));
        }
        return $type; 
    }
}
new Yeepdf_Buider_Woocommerece_Block;