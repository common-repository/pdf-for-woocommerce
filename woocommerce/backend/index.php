<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class Yeepdf_Woocommerce_Backend {
    private $blocks = array();
    private $shortcode;
    function __construct(){
        add_filter( 'yeepdf_builder_block_html', array($this,"block_woocommerce_builder") );
        add_action("yeepdf_head_settings",array($this,"add_head_settings"));
        add_action( 'save_post_yeepdf',array( $this, 'save_metabox' ), 10, 2 );
        add_action("yeepdf_builder_tab__editor_before",array($this,"builder_email_tab__editor"),200);
        add_filter( 'manage_shop_order_posts_columns', array($this,'add_button'),20 );
        add_filter( 'manage_woocommerce_page_wc-orders_columns', array($this,'add_button'),20 );
        add_filter("name_pdf_download",array($this,"custom_name"),10,3);
        add_filter("yeepdf_add_libs",array($this,"yeepdf_add_libs"),10);
        add_action("yeepdf_builder_tab_block_addons",array($this,"block_text_addons"),9);
        add_filter("superaddons_pdf_check_pro",array($this,"check_pro"));
    }
    function check_pro($pro){
		$check = get_option( '_redmuber_item_1708');
		if($check == "ok"){
			$pro = true;
		}
		return $pro;
	}
    function block_text_addons(){
        ?>
        <div class="builder__widget--inner">
            <div class="builder__widget_tab builder__widget_addons">
                <div class="builder__widget_tab_title"><span class="builder__widget_tab_title_t"><?php esc_attr_e( "WooCommerce", "yeemail") ?></span><span class="builder__widget_tab_title_icon dashicons dashicons-arrow-down-alt2"></span><span class="builder__widget_tab_title_icon dashicons dashicons-arrow-up-alt2"></span></div>
                <ul class="momongaPresets momongaPresets_data">
                    <?php
                    foreach ($this->get_blocks() as $value) {
                        ?>
                        <li>
                            <div class="momongaDraggable" data-type="<?php echo esc_attr($value["type"]) ?>">
                                <i class="emailbuilder-icon <?php echo esc_html($value["icon"]) ?>"></i>
                                <div class="yeemail-tool-text"><?php echo esc_html($value["title"]) ?></div>
                            </div>
                        </li>
                        <?php
                    }
                    do_action( "yeemail_builder_tab_block_addons_woocommerce");
                    ?>
                </ul>
            </div>
        </div>
        <?php
    }
    function yeepdf_add_libs($add_libs){
        if(isset($_GET["tab"]) && $_GET["tab"] == "settings_pdfs"){
            return true;
        }
        return $add_libs;
    }
    function custom_name($name,$order_id,$packing_slip) {
        $names = get_option("woocommerce_pdf_name",array("invoice"=>"invoice","packing"=>"packing-slip"));
        if( $packing_slip ){
            $name= $names["packing"];
        }else{
            $name= $names["invoice"];
        }
        return $name."-".$order_id;
    }
    function add_button($columns){
        $columns['pdf_creator'] = esc_html__( 'PDFs', "pdf-customizer-for-woocommerce" );
        return $columns;
    }
    function builder_email_tab__editor($post){
        ?>
        <div class="builder__editor--item builder__editor--item-detail-template">
            <div class="yeepdf_setting_group">
                <div class="yeepdf_setting_row">
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_html_e("Show img","pdf-for-wpforms") ?></label>
                        <div class="yeepdf_setting_input-wrapper">
                            <label class="yeepdf-switch">
                                <input class="detail-img" type="checkbox" value="yes">
                                <span class="yeepdf-slider yeepdf-round"></span>
                            </label>
                        </div>
                    </div>
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_html_e("Show total","pdf-for-wpforms") ?></label>
                        <div class="yeepdf_setting_input-wrapper">
                            <label class="yeepdf-switch">
                                <input class="detail-totals" type="checkbox" value="yes">
                                <span class="yeepdf-slider yeepdf-round"></span>
                            </label>
                        </div>
                    </div>
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_html_e("Show SKU","pdf-for-wpforms") ?></label>
                        <div class="yeepdf_setting_input-wrapper">
                            <label class="yeepdf-switch">
                                <input class="detail-sku" type="checkbox" value="yes">
                                <span class="yeepdf-slider yeepdf-round"></span>
                            </label>
                        </div>
                    </div>
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_html_e("Show Des","pdf-for-wpforms") ?></label>
                        <div class="yeepdf_setting_input-wrapper">
                            <label class="yeepdf-switch">
                                <input class="detail-des" type="checkbox" value="yes">
                                <span class="yeepdf-slider yeepdf-round"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="yeepdf_setting_group">
                <div class="yeepdf_check"><?php esc_html_e("Table Header","pdf-for-wpforms") ?></div>
                <div class="yeepdf_setting_row">
                    <div class="settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_html_e("BG Color","pdf-for-wpforms") ?></label>
                        <div class="setting_input-wrapper">
                            <input name="yeepdf_name[]" type="text" value="" class="builder__editor_color yeepdf_setting_input_header_bg">
                        </div>
                    </div>
                    <div class="settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_html_e("Text Color","pdf-for-wpforms") ?></label>
                        <div class="yeepdf_setting_input-wrapper">
                            <input name="yeepdf_name[]" type="text" value="" class="builder__editor_color yeepdf_setting_input_header_color">
                        </div>
                    </div>
                </div>
            </div>
            <div class="yeepdf_setting_group">
                <div class="yeepdf_div-block-25">
                    <div class="yeepdf_check"><?php esc_html_e("ROWS","pdf-for-wpforms") ?></div>
                </div>
                <div class="yeepdf_setting_row">
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_html_e("BG Color","pdf-for-wpforms") ?></label>
                        <div class="setting_input-wrapper">
                            <input name="yeepdf_name[]" type="text" value="" class="builder__editor_color yeepdf_setting_input_even_bg">
                        </div>
                    </div>
                    <div class="yeepdf_settings_group-wrapper">
                        <label class="yeepdf_checkbox_label"><?php esc_html_e("Text Color","pdf-for-wpforms") ?></label>
                        <div class="yeepdf_setting_input-wrapper">
                            <input name="yeepdf_name[]" type="text" value="" class="builder__editor_color yeepdf_setting_input_even_color">
                        </div>
                    </div>
                </div>
            </div>
            <div class="yeepdf_setting_group">
                <div class="check"><?php esc_html_e("Cells","pdf-for-wpforms") ?></div>
                <div class="yeepdf_setting_row">
                    <div class="yeepdf_settings_group-wrapper">
                        <label for="cell-text-align-dropdown" class="yeepdf_checkbox_label"><?php esc_html_e("Text Align","pdf-for-wpforms") ?></label>
                        <div class="yeepdf_setting_input-wrapper">
                            <select name="yeepdf_name[]" class="yeepdf_setting_input yeepdf_setting_input_cell_ta">
                                <option value="left">left</option>
                                <option value="center">center</option>
                                <option value="right">right</option>
                            </select>
                        </div>
                    </div>
                    <div class="yeepdf_settings_group-wrapper">
                        <label for="cell-padding-input" class="yeepdf_checkbox_label"><?php esc_html_e("Padding","pdf-for-wpforms") ?></label>
                        <div class="yeepdf_setting_input-wrapper">
                            <input name="yeepdf_name[]" class="yeepdf_setting_input yeepdf_setting_input_cell_padding"step="1" type="number" data-after_value="px">
                        </div>
                    </div>
                </div>
            </div>
            <div class="yeepdf_setting_group">
                    <div class="check"><?php esc_html_e("Borders","pdf-for-wpforms") ?></div>
                    <div class="yeepdf_setting_row">
                        <div class="yeepdf_settings_group-wrapper">
                            <label class="yeepdf_checkbox_label"><?php esc_html_e("Color","pdf-for-wpforms") ?></label>
                            <div class="setting_input-wrapper">
                                <input name="yeepdf_name[]" type="text" value="" class="builder__editor_color yeepdf_setting_input_tb_border_color">
                            </div>
                        </div>
                        <div class="yeepdf_settings_group-wrapper">
                            <label class="yeepdf_checkbox_label"><?php esc_html_e("Style","pdf-for-wpforms") ?></label>
                            <div class="yeepdf_setting_input-wrapper">
                                <select name="yeepdf_name[]" class="yeepdf_setting_input yeepdf_setting_input_tb_border_style">
                                    <option value="solid">solid</option>
                                    <option value="dotted">dotted</option>
                                    <option value="dashed">dashed</option>
                                    <option value="double">double</option>
                                    <option value="groove">groove</option>
                                    <option value="ridge">ridge</option>
                                    <option value="inset">inset</option>
                                    <option value="outset">outset</option>
                                    <option value="none">none</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="yeepdf_setting_row">
                        <div class="yeepdf_settings_group-wrapper">
                            <label class="yeepdf_checkbox_label"><?php esc_html_e("Width","pdf-for-wpforms") ?></label>
                            <div class="yeepdf_setting_input-wrapper">
                                <input name="yeepdf_name[]" class="setting_input yeepdf_setting_input_tb_border_width" step="1" type="number" data-after_value="px">
                            </div>
                        </div>
                        <div class="yeepdf_settings_group-wrapper">
                            <label class="yeepdf_checkbox_label"><?php esc_html_e("Collapse","pdf-for-wpforms") ?></label>
                            <div class="setting_input-wrapper">
                                <select name="yeepdf_name[]" class="yeepdf_setting_input yeepdf_setting_input_tb_border_collapse">
                                    <option value="collapse">collapse</option>
                                    <option value="separate">separate</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        <?php
    }
    function add_head_settings($post){
        $post_id= $post->ID;
        $order_id = get_option( "_yeepdf_woocommerce_demo" );
        $woocommerce_shortcodes = new Yeepdf_Addons_Woocommerce_Shortcodes;
        $woocommerce_shortcodes->set_order_id($order_id);
        ?>
        <div class="yeepdf-testting-order">
            <?php 
            $args = array('status' => array('wc-completed', 'wc-processing', 'wc-on-hold'), 'limit' => 10, 'type' => 'shop_order');
            $orders = wc_get_orders($args);
            ?>
            <select name="yeepdf_woocommerce_demo" class="builder_pdf_woo_testing">
                <option value=""><?php esc_html_e("Sample order to show","yeepdf") ?></option>
                <?php
                    $orders = wc_get_orders(array('limit' => 20));
                    foreach ( $orders as $order ) {
                        $form_id = $order->get_id();
                        $order_data = $order->get_data();
                        $lastname= $order_data["billing"]["last_name"];
                        $firstname= $order_data["billing"]["first_name"];
                        $email= $order_data["billing"]["email"];
                        ?>
                            <option <?php selected($order_id,$form_id) ?> value="<?php echo esc_attr($form_id) ?>">#<?php echo esc_html($form_id) ?> - <?php echo esc_html($lastname); ?> <?php echo esc_html($firstname); ?> <?php echo esc_html($email); ?></option>
                        <?php
                    }
                    ?>
            </select>
        </div>
        <?php
    }
    function save_metabox($post_id, $post){
        if( isset($_POST['yeepdf_woocommerce_demo'])) {
            $id = sanitize_text_field($_POST['yeepdf_woocommerce_demo']);
            update_option("_yeepdf_woocommerce_demo",$id);
        }
    }
    function get_blocks(){
        $data = array(
            array(
                "type"=>"order_detail",
                "title" => esc_html__("Order Detail","pdf-customizer-for-woocommerce"),
                "icon" => "emailbuilder-icon icon-woo",
                "shortcode" => "[yeepdf_woo_order_detail]"
            ),
            array(
                "type"=>"yeepdf_order_addesses",
                "title" => esc_html__("Billing Shipping","yeemail"),
                "icon" => "emailbuilder-icon icon-woo",
                "shortcode" => "[yeepdf_woo_order_addresses]"
            ),
            array(
                "type"=>"order_billing",
                "title" => esc_html__("Billing","pdf-customizer-for-woocommerce"),
                "icon" => "emailbuilder-icon icon-woo",
                "shortcode" => "[yeepdf_woo_order_billing]"
            ),
            array(
                "type"=>"order_shipping",
                "title" => esc_html__("Shipping","pdf-customizer-for-woocommerce"),
                "icon" => "emailbuilder-icon icon-woo",
                "shortcode" => "[yeepdf_woo_order_shipping]"
            ),
            array(
                "type"=>"order_total",
                "title" => esc_html__("Total Detail","pdf-customizer-for-woocommerce"),
                "icon" => "emailbuilder-icon icon-woo",
                "shortcode" => "[yeepdf_woo_order_total_detail]"
            ),
            array(
                "type"=>"customer_notes",
                "title" => esc_html__("Customer Notes","pdf-customizer-for-woocommerce"),
                "icon" => "emailbuilder-icon icon-woo",
                "shortcode" => "[yeepdf_woo_customer_note]"
            ),
        );
        return $data;
    }
    function block_woocommerce_builder($type){
        foreach ($this->get_blocks() as $value) {
            $data_html ="";
            $padding = Yeepdf_Global_Data::$padding;
            $margin = Yeepdf_Global_Data::$margin;
            $text_align = Yeepdf_Global_Data::$text_align;
            $container_show = array("condition");
            $inner_attr = array(".text-content"=>array(".builder__editor--html .builder__editor--js"=>"html"),".text-content-data"=>array(".builder__editor--html .builder__editor--js"=>"html_hide"));
            $inner_style = array();
            switch ($value["type"]) {
                case "order_detail":
                    $container_show[] = "detail-template";
                    $inner_style["table"]= array(
                        ".builder__editor--item-detail-template .yeepdf_setting_input_tb_border_color"     =>"border-color",
                        ".builder__editor--item-detail-template .yeepdf_setting_input_tb_border_style"     =>"border-style",
                        ".builder__editor--item-detail-template .yeepdf_setting_input_tb_border_width"     =>"border-width",
                        ".builder__editor--item-detail-template .yeepdf_setting_input_tb_border_collapse"     =>"border-collapse",
                    );
                    $inner_style["th"] = array(
                        ".builder__editor--item-detail-template .yeepdf_setting_input_header_bg"     =>"background-color",
                        ".builder__editor--item-detail-template .yeepdf_setting_input_header_color"     =>"color",
                        ".builder__editor--item-detail-template .yeepdf_setting_input_cell_ta"     =>"text-align",
                        ".builder__editor--item-detail-template .yeepdf_setting_input_cell_padding"     =>"padding",
                        ".builder__editor--item-detail-template .yeepdf_setting_input_tb_border_color"     =>"border-color",
                        ".builder__editor--item-detail-template .yeepdf_setting_input_tb_border_style"     =>"border-style",
                        ".builder__editor--item-detail-template .yeepdf_setting_input_tb_border_width"     =>"border-width",
                        ".builder__editor--item-detail-template .yeepdf_setting_input_tb_border_collapse"     =>"border-collapse"
                    );
                    $inner_style["td"] = array(
                        ".builder__editor--item-detail-template .yeepdf_setting_input_even_bg"     =>"background-color",
                        ".builder__editor--item-detail-template .yeepdf_setting_input_even_color"     =>"color",
                        ".builder__editor--item-detail-template .yeepdf_setting_input_cell_ta"     =>"text-align",
                        ".builder__editor--item-detail-template .yeepdf_setting_input_cell_padding"     =>"padding",
                        ".builder__editor--item-detail-template .yeepdf_setting_input_tb_border_color"     =>"border-color",
                        ".builder__editor--item-detail-template .yeepdf_setting_input_tb_border_style"     =>"border-style",
                        ".builder__editor--item-detail-template .yeepdf_setting_input_tb_border_width"     =>"border-width",
                        ".builder__editor--item-detail-template .yeepdf_setting_input_tb_border_collapse"     =>"border-collapse"
                    );
                    $inner_attr["th.sku"] = array(  ".builder__editor--item-detail-template .detail-sku"=>"data-sku" );
                    $inner_attr["td.sku"] = array(  ".builder__editor--item-detail-template .detail-sku"=>"data-sku" );
                    $inner_attr["th.thumbnail"] = array(  ".builder__editor--item-detail-template .detail-img"=>"data-showimg" );
                    $inner_attr["td.thumbnail"] = array(  ".builder__editor--item-detail-template .detail-img"=>"data-showimg" );
                    $inner_attr["th.des"] = array(  ".builder__editor--item-detail-template .detail-des"=>"data-showdes" );
                    $inner_attr["td.des"] = array(  ".builder__editor--item-detail-template .detail-des"=>"data-showdes" );
                    $inner_attr[".woo_totals"] = array(  ".builder__editor--item-detail-template .detail-totals"=>"data-totals" );
                    $inner_attr[".text-content-save"] = array(  ".builder__editor--item-detail-template .detail-template"=>"data-template",
                                                                ".builder__editor--item-detail-template .detail-img"=>"data-showimg",
                                                                ".builder__editor--item-detail-template .detail-totals"=>"data-totals",
                                                                ".builder__editor--item-detail-template .detail-sku"=>"data-sku",
                                                                ".builder__editor--item-detail-template .detail-des"=>"data-showdes",
                                                                ".builder__editor--item-detail-template .yeepdf_setting_input_header_bg"=>"data-header_bg",
                                                                ".builder__editor--item-detail-template .yeepdf_setting_input_header_color"=>"data-header_color",
                                                                );
                    $data_html = '<div class="text-content-save" data-template="default" data-showimg="hidden" data-totals="yes" data-sku="hidden" data-showdes="hidden" data-table_border_color="#e7e7e7"></div>';
                    break;
                default:
                    // code...
                    break;
            }
            $type["block"][esc_attr($value["type"])]["builder"] = '
            <div class="builder-elements">
                <div class="builder-elements-content" data-type="'.esc_attr($value["type"]).'" >
                    <div class="text-content-data hidden">'.esc_attr($value["shortcode"]).'</div>
                    <div class="text-content">'.do_shortcode($value["shortcode"]).'</div>
                    '.$data_html.'
                </div>
            </div>';
            $type["block"][esc_attr($value["type"])]["editor"]["container"]["show"]= $container_show;
            $type["block"][esc_attr($value["type"])]["editor"]["container"]["style"]= array();
            $type["block"][esc_attr($value["type"])]["editor"]["inner"]["style"]= $inner_style;
            $type["block"][esc_attr($value["type"])]["editor"]["inner"]["attr"] = $inner_attr;
        }
        $container_show = array("text-align","padding","background","html");
        $type["block"]["woocommerce_data"]["builder"] = '
            <div class="builder-elements">
                <div class="builder-elements-content" data-type="woocommerce_data" >
                    <div class="text-content-data hidden"></div>
                    <div class="text-content">'.esc_attr__("Choose data shortcode","pdf-customizer-for-woocommerce").'</div>
                </div>
            </div>';
        $type["block"]["woocommerce_data"]["editor"]["container"]["show"]= $container_show;
        $type["block"]["woocommerce_data"]["editor"]["container"]["style"]= array_merge($padding,$text_align);
        $type["block"]["woocommerce_data"]["editor"]["inner"]["style"]= array();
        $type["block"]["woocommerce_data"]["editor"]["inner"]["attr"] = $inner_attr;
        return $type; 
    }
}
new Yeepdf_Woocommerce_Backend;