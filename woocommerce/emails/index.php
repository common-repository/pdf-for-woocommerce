<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class Yeepdf_Add_Attachments_Woocommerce {
	function __construct(){
		add_filter( 'woocommerce_email_attachments', array($this,'add_pdf'), 10, 4 );
		add_filter( 'woocommerce_settings_tabs_array', array($this,"add_settings_tab"), 50 );
		add_action( 'woocommerce_settings_tabs_settings_pdfs', array($this,'settings_tab') );
		add_action( 'add_meta_boxes', array( $this, 'add_metabox'  ) );
		add_filter('woocommerce_my_account_my_orders_actions',array($this,"add_action_download"),10,2);
		add_action( 'woocommerce_update_options_settings_pdfs', array($this,'update_settings') );
		add_action("woocommerce_order_details_before_order_table",array($this,"add_buton_download_pdf_frontend"));
		add_action( 'manage_shop_order_posts_custom_column', array($this,'add_buton_link'), 10, 2 );
        add_action( 'manage_woocommerce_page_wc-orders_custom_column', array($this,'add_buton_link'), 10, 2 ); 
        //add_filter('bulk_actions-edit-shop_order', array($this,"acc_actions"),10, 2);  ///7.8 don't update
	}
	function acc_actions($actions){
        $actions['pdf_creator'] = esc_html__( 'Invoices PDF', "pdf-customizer-for-woocommerce" );
        return $actions;
    }
	public static function get_link_dowload_by_order_id($order_id){
		global $wpdb; 
		$table_name = $wpdb->prefix."vc_pdf_invoices";
		if( is_numeric($order_id) ) {
            $order = wc_get_order( $order_id );  
        }else{
            $order = $order_id;
            $order_id = $order->get_id();
        }
        $order_status  = $order->get_status();
        switch( $order_status ){
            case "processing":
            case "pending":
                $order_status = "processing_order";
                break;
        }
		$link_download_home = get_home_url();
        $link_download_pdfs = array();
		$datas= $wpdb->get_results("SELECT * FROM $table_name WHERE enable = 1", ARRAY_A);
		$order_shortcode = new Yeepdf_Addons_Woocommerce_Shortcodes();
		foreach( $datas as $data){
			$my_account_buttons = $data["my_account_buttons"];
			if( isset($data["enable_order"])){
				$available = false;
				switch( $my_account_buttons ){
					case "available":
						foreach ($order->get_meta_data() as $object) {
							$object_array = array_values((array)$object);
							foreach ($object_array as $object_item) {
								if ('_yeepdf' == $object_item['key']) {;
									if(isset($object_item['value'][$data["id"]])){
										$link_download_pdfs[$data["id"]] = $object_item['value'][$data["id"]];
									}
									break;
								}
							}
						}
						break;
					case "custom":
						$my_account_order_status = json_decode($data["my_account_order_status"]);
						if($my_account_order_status == ""){
							$my_account_order_status = array();
						}
						if(in_array($order_status,$my_account_order_status)){
							$available = true;
						}
						break;
					case "always":
						$available = true;
						break;
				}
				$enable_order = json_decode($data["enable_order"]);
				if($available){
					$name =$data["name"];
					$password =$data["password"];
					$order_shortcode->set_order_id($order_id);
					$name = do_shortcode( $name );
					$name = urlencode($name);
					$password = urlencode($password);
					$link_download = add_query_arg(array("pdf_preview"=>"preview","id"=>$data["template_id"],"download"=>"1","woo_order"=>$order_id,"pdf_name"=>$name,"pdf_password" =>$password),$link_download_home);
					$link_download_pdf = wp_nonce_url($link_download,"pdf_creator");
					$link_download_pdfs[$data["id"]] =  array("link"=>$link_download_pdf,"label"=>$data["label"],"date"=>date(get_option('date_format')));
				}
			}	
		}
		return $link_download_pdfs;
	}
	function add_buton_download_pdf_frontend($order_id){
        $link_download_pdfs = self::get_link_dowload_by_order_id($order_id);
        if( count($link_download_pdfs) > 0 ){ 
			foreach($link_download_pdfs as $id => $pdf){
				?>
				<p><strong><?php echo esc_html( $pdf["label"] ) ?>: </strong> <a href="<?php echo esc_url( $pdf["link"]) ?>"><?php esc_html_e( "Download", "pdf-for-woocommerce") ?></a></p>
				<?php
			}
		}
    }
	function add_action_download($actions,$order){
		$link_download_pdfs = self::get_link_dowload_by_order_id($order);
		$order_id = $order->get_id();
		if( count($link_download_pdfs) > 0 ){ 
			foreach($link_download_pdfs as $id => $pdf){
				$actions["yeepdf_".$order_id."_".$id] = array("url"=>$pdf["link"],"name"=>$pdf["label"]);
			}
		}
		return $actions;
	}
	public function add_metabox() {
		add_meta_box(
			'pdf_creator-woo',
			esc_html__( 'PDF', "pdf-customizer-for-woocommerce" ),
			array( $this, 'render_metabox_hight_performation' ),
			wc_get_page_screen_id( 'shop-order' ),
			'side',
			'default'
		);
    }
	function render_metabox_hight_performation($object){
		global $wpdb; 
		$table_name = $wpdb->prefix."vc_pdf_invoices";
		$order = is_a( $object, 'WP_Post' ) ? wc_get_order( $object->ID ) : $object;
		$order_id = $order->get_id();
		$order_status  = $order->get_status();
		$link_download_home = get_home_url();	
		switch( $order_status ){
			case "processing":
			case "pending":
				$order_status = "processing_order";
				break;
		}
		$datas= $wpdb->get_results("SELECT * FROM $table_name WHERE enable = 1", ARRAY_A);
		foreach( $datas as $data){
			if( isset($data["enable_order"])){
				$enable_order = json_decode($data["enable_order"]);
				if(in_array($order_status,$enable_order)){
					$name =$data["name"];
					$order_shortcode = new Yeepdf_Addons_Woocommerce_Shortcodes();
					$order_shortcode->set_order_id($order_id);
					$name = do_shortcode( $name );
					$name = urlencode($name);
					$link_download = add_query_arg(array("pdf_preview"=>"preview","id"=>$data["template_id"],"download"=>"1","woo_order"=>$order_id,"pdf_name"=>$name),$link_download_home);
					$link_preview = add_query_arg(array("pdf_preview"=>"preview","id"=>$data["template_id"],"woo_order"=>$order_id,"pdf_name"=>$name),$link_download_home);
					?>
					<h3><?php echo esc_attr( $data["label"] ) ?></h3>
					<div>
						<a class="button button-primary" href="<?php echo esc_url($link_download) ?>"><?php esc_html_e("Download","pdf-customizer-for-woocommerce") ?></a>
						<a target="_blank" class="button" href="<?php echo esc_url($link_preview) ?>"><?php esc_html_e("Preview","pdf-customizer-for-woocommerce") ?></a>
					</div>
					<?php
				}
			}	
		}
	}
	function add_buton_link( $column, $post_id ) { 
        switch ( $column ){
            case "pdf_creator":
                $link_download_pdfs = self::get_link_dowload_by_order_id($post_id);
				if( count($link_download_pdfs) > 0 ){ 
					foreach($link_download_pdfs as $id => $pdf){
						?>
						<strong><?php echo esc_html( $pdf["label"] ) ?>: </strong> <a download href="<?php echo esc_url( $pdf["link"]) ?>"><span class="dashicons dashicons-download"></span></a>
						<?php
					}
				}
                break;
        }
    }
	function add_pdf( $attachments, $email_id, $order, $email ) {
		global $wpdb; 
		$table_name = $wpdb->prefix."vc_pdf_invoices";
		$order_id = $order->get_id();
		$order_status = $order->get_status();
		switch( $order_status ){
			case "processing":
			case "pending":
				$order_status = "processing_order";
				break;
		}
		$datas= $wpdb->get_results("SELECT * FROM $table_name WHERE enable = 1", ARRAY_A);
		$yeepdf_data = array();
		$upload_dir = wp_upload_dir();
		$order_shortcode = new Yeepdf_Addons_Woocommerce_Shortcodes();
		$order_shortcode->set_order_id($order_id);
		foreach( $datas as $data){
			if( isset($data["enable_order"])){
				$enable_order = json_decode($data["enable_order"]);
				if(in_array($order_status,$enable_order)){
					$enable_attas = json_decode($data["attachments"]);
					if(in_array($order_status,$enable_attas)){
						$name =$data["name"];
						$password =$data["password"];
						$name = do_shortcode( $name );
						$name = sanitize_file_name($name);
						$password = do_shortcode( $password );
						$data_send_settings_download = array(
							"id_template"=> $data["template_id"],
							"type"=> "upload",
							"name"=> $name,
							"datas" =>array(),
							"woo_order_id" =>$order_id,
							"woo_shortcode" =>false,
							"password" =>$password,
						);
						$data_send_settings_download = apply_filters("pdf_before_render_datas",$data_send_settings_download);
						$path =Yeepdf_Create_PDF::pdf_creator_preview($data_send_settings_download);
						$attachments[] = $path;
						$link_pdf = $upload_dir['baseurl'] . '/pdfs/'.$name.".pdf";
						$yeepdf_data[$data["id"]] = array("label"=>$data["label"],"link"=>$link_pdf,"date"=>date(get_option('date_format')));
					}
				}
			}
		}
		$order->update_meta_data( '_yeepdf', $yeepdf_data );
		$order->save();
		return $attachments;
	}
	function add_settings_tab( $settings_tabs ) {
        $settings_tabs['settings_pdfs'] = esc_html__( 'PDF Invoices', "pdf-customizer-for-woocommerce" );
        return $settings_tabs;
    }
	function update_settings() {
		global $wpdb;
		$table_name =  $wpdb->prefix."vc_pdf_invoices";
		//save tempalte
		$id = sanitize_text_field($_POST["pdf_id"]);
		$logic = sanitize_text_field($_POST["logic"]);
		if($logic == 1){
			$yeepdf_logic= map_deep( $_POST["yeepdf_logic"], 'sanitize_text_field' );
		}
		$enable = 0;
		if(isset($_POST["yeepdf_woocommerce_enabled"])){
			$enable = 1;
		}
		$template_id = sanitize_text_field($_POST["template_id"]);
		$enable_order= map_deep( $_POST["enable_order"], 'sanitize_text_field' );
		$attachments= map_deep( $_POST["attachments"], 'sanitize_text_field' );
		$my_account_order_status = map_deep( $_POST["my_account_order_status"], 'sanitize_text_field' );
		$atts = array(
			"enable" => $enable,
			"label" => sanitize_text_field($_POST["label"]),
			"template_id" => $template_id,
			"name" => sanitize_text_field($_POST["name"]),
			"enable_order" => json_encode($enable_order),
			"attachments" => json_encode($attachments),
			"password" => sanitize_text_field($_POST["password"]),
			"conditional_logic" => $logic,
			"my_account_buttons"=>sanitize_text_field($_POST["my_account_buttons"]),
			"my_account_order_status"=>json_encode($my_account_order_status),
			"conditional_logic_datas" => json_encode($yeepdf_logic)
		);
		if($id < 1){
			$wpdb->insert( 
				$table_name, 
				$atts
			);
			$id = $wpdb->insert_id;
		}else{
			//update
			$id_done = $wpdb->update( 
					$table_name, 
					$atts, 
					array( 'id' => $id
							),
				);
		}
		$location = admin_url( 'admin.php?page=wc-settings&tab=settings_pdfs&subview=pdf_creator_form_settings&pdf_id='.$id);
		wp_redirect( $location );	
	}
    function settings_tab() {
		global $wpdb;
		$table_name = $wpdb->prefix."vc_pdf_invoices";
		$pro = Yeepdf_Settings_Builder_PDF_Backend::check_pro();
		$pdf_id  = isset( $_GET['pdf_id'] ) ? sanitize_text_field( $_GET['pdf_id'] ) : -1;
		$orders_types = array("new_order"=>esc_html__("New order","pdf-customizer-for-woocommerce"),
									"cancelled"=>esc_html__("Cancelled order","pdf-customizer-for-woocommerce"),
									"failed_order"=>esc_html__("Failed order","pdf-customizer-for-woocommerce"),
									"on-hold"=>esc_html__("Order on-hold","pdf-customizer-for-woocommerce"),
									"processing_order"=>esc_html__("Processing order","pdf-customizer-for-woocommerce"),
									"completed"=>esc_html__("Completed order","pdf-customizer-for-woocommerce"),
									"refunded"=>esc_html__("Refunded order","pdf-customizer-for-woocommerce"),
									"failed"=>esc_html__("Failed order","pdf-customizer-for-woocommerce"),
									"customer_invoice"=>esc_html__("Customer invoice","pdf-customizer-for-woocommerce"),
			);
		?>
		<h3><?php esc_html_e("PDF Invoices","pdf-customizer-for-woocommerce") ?></h3>
		<p><a href="admin.php?page=wc-settings&tab=settings_pdfs&subview=pdf_creator_form_settings&pdf_id=0" class="button-primary button"><?php esc_attr_e( "Add new PDF", "pdf-customizer-for-woocommerce" ) ?></a></p>
		<?php
		if($pdf_id >= 0) {
			//remove
			if ( isset($_REQUEST['action']) && $_REQUEST['action'] =="delete" && isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], "yeepdf_woocommerce_remove")) {
				$wpdb->query( 
					$wpdb->prepare( 
						"DELETE FROM $table_name WHERE id = %d",
						$pdf_id	
						)
				);
				$location = admin_url( 'admin.php?page=wc-settings&tab=settings_pdfs');
				wp_redirect( $location );
			}
			if($pdf_id == 0 ){
				$settings = array(
					"enable"=>1,
					"label"=>"",
					"template_id"=>"",
					"name"=>'[yeepdf_order_id]-invoice',
					"enable_order"=>array(),
					"attachments"=>array(),
					"password"=>"",
					"my_account_buttons"=>"available",
					"my_account_order_status"=>array(),
					"conditional_logic"=>"",
					"conditional_logic_datas"=>"",
				);
			}else{
				$settings = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = $pdf_id",ARRAY_A );
			}
			?>
			<div class="yeepdf-container-tab">
				<h3>PDF</h3>
				<div class="yeepdf-container-inner-tab">
					<input type="hidden" name="pdf_id" value="<?php echo esc_attr($pdf_id) ?>" />
					<table class="form-table">
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="yeepdf_woocommerce_enabled"><?php esc_attr_e( "Enable/Disable", "yeepdf" ) ?></label>
							</th>
							<td class="forminp">
								<fieldset>
									<legend class="screen-reader-text"><span><?php esc_attr_e( "Enable/Disable", "yeepdf" ) ?></span></legend>
									<label for="yeepdf_woocommerce_enabled">
									<input class="" type="checkbox" name="yeepdf_woocommerce_enabled" id="yeepdf_woocommerce_enabled" value="1" <?php checked(1,$settings["enable"]) ?>> <?php esc_attr_e( "Enable this PDF", "yeepdf" ) ?></label><br>
								</fieldset>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label><?php esc_html_e("Label","pdf-for-gravityforms") ?></label>
							</th>
							<td class="forminp">
								<input type="text" class="regular-text" name="label" value="<?php echo esc_attr($settings["label"]) ?>" required>
								<div class="yeepdf-settings-description">
									<label ><?php esc_html_e("Add a descriptive label to help you  differentiate between multiple PDF settings.","pdf-for-gravityforms") ?></label>
								</div>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label><?php esc_html_e("PDF Template","pdf-for-gravityforms") ?></label>
							</th>
							<td class="forminp">
							<select name="template_id" class="regular-text">
								<?php 
								$pdf_templates = get_posts(array("post_type"=>"yeepdf","numberposts"=>-1));
								foreach( $pdf_templates as $pdf_template ){
									$k = $pdf_template->ID;
								?>
								<option <?php selected( $k, $settings["template_id"]) ?> value="<?php echo esc_attr($k) ?>" ><?php echo esc_html($pdf_template->post_title) ?></option>
								<?php } ?>
							</select>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label><?php esc_html_e("PDF Name","pdf-for-gravityforms") ?></label>
							</th>
							<td class="forminp">
								<?php
								Yeepdf_Settings_Main::add_number_seletor("name",$settings["name"]);
								?>
								<div class="yeepdf-settings-description">
									<label ><?php esc_html_e("Set the filename for the generated PDF","pdf-for-gravityforms") ?></label>
								</div>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label><?php esc_html_e("Enable to","pdf-for-gravityforms") ?></label>
							</th>
							<td class="forminp">
								<div class="yeepdf-settings-boxcontent">
									<?php 
									$enable_order = $settings["enable_order"];
									if(!is_array($enable_order)){
										$enable_order = json_decode($enable_order,true);
										if(!$enable_order){
											$enable_order = array();
										}
									}
									foreach($orders_types as $key => $value){ 
										$checked = "";
										if(in_array($key,$enable_order)){
											$checked = "checked";
										}
										?>
									<input id="yeepdf_woocommerce_pdf_enable_<?php echo esc_attr($key) ?>" <?php echo esc_html( $checked ) ?> value="<?php echo esc_attr($key) ?>" type="checkbox" name="enable_order[]"> <label for="yeepdf_woocommerce_pdf_enable_<?php echo esc_attr($key) ?>"><?php echo esc_html($value) ?></label><br>
									<?php 
									} ?>
								</div>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label><?php esc_html_e("Attachment to","pdf-for-gravityforms") ?></label>
							</th>
							<td class="forminp">
								<div class="yeepdf-settings-boxcontent">
								<?php 
								foreach($orders_types as $key => $value){ 
									$checked = "";
									$attachments = $settings["attachments"];
									if(!is_array($attachments)){
										$attachments = json_decode($attachments,true);
										if(!$attachments){
											$attachments = array();
										}
									}
									if(in_array($key,$attachments)){
										$checked = "checked";
									}
									?>
								<input id="yeepdf_woocommerce_pdf_atta_<?php echo esc_attr($key) ?>" <?php echo esc_html( $checked ) ?> value="<?php echo esc_attr($key) ?>" type="checkbox" name="attachments[]"><label for="yeepdf_woocommerce_pdf_atta_<?php echo esc_attr($key) ?>"> <?php echo esc_html($value) ?></label><br>
								<?php 
								} ?>
								</div>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label><?php esc_html_e("Password","pdf-for-gravityforms") ?></label>
							</th>
							<td class="forminp">
							<?php
								if($pro){ 
									Yeepdf_Settings_Main::add_number_seletor("password",$settings["password"]);
								}else{
									esc_html_e("Upgrade to pro version","pdf-for-gravityforms");
								}
								?>
								<div class="yeepdf-settings-description">
									<label ><?php esc_html_e("You have the option to password-protect your PDF documents","pdf-for-gravityforms") ?></label>
								</div>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label><?php esc_html_e("Allow My Account invoice download","pdf-for-gravityforms") ?></label>
							</th>
							<td class="forminp">
								<select name="my_account_buttons" class="yeepdf_my_account_buttons">
									<option <?php selected( $settings["my_account_buttons"],"available" ) ?> value="available"><?php esc_html_e("Only when an invoice is already created/emailed","pdf-for-gravityforms") ?></option>
									<option <?php selected( $settings["my_account_buttons"],"custom" ) ?> value="custom"><?php esc_html_e("Only for specific order statuses (define below)","pdf-for-gravityforms") ?></option>
									<option <?php selected( $settings["my_account_buttons"],"always" ) ?> value="always"><?php esc_html_e("Always","pdf-for-gravityforms") ?></option>
									<option <?php selected( $settings["my_account_buttons"],"never" ) ?> value="never"><?php esc_html_e("Never","pdf-for-gravityforms") ?></option>
								</select>
								<div class="yeepdf-settings-boxcontent yeepdf_my_account_buttons_custom <?php echo esc_attr(($settings["my_account_buttons"] != "custom")? "hidden": "show" );  ?>">
								<?php 
								foreach($orders_types as $key => $value){ 
									$checked = "";
									$my_account_order_status = $settings["my_account_order_status"];
									if(!is_array($my_account_order_status)){
										$my_account_order_status = json_decode($my_account_order_status,true);
										if(!$my_account_order_status){
											$my_account_order_status = array();
										}
									}
									if(in_array($key,$my_account_order_status)){
										$checked = "checked";
									}
									?>
								<input id="yeepdf_woocommerce_pdf_my_account_<?php echo esc_attr($key) ?>" <?php echo esc_html( $checked ) ?> value="<?php echo esc_attr($key) ?>" type="checkbox" name="my_account_order_status[]"><label for="yeepdf_woocommerce_pdf_my_account_<?php echo esc_attr($key) ?>"> <?php echo esc_html($value) ?></label><br>
								<?php 
								} ?>
								</div>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label><?php esc_html_e("Conditional Logic","pdf-for-gravityforms") ?></label>
							</th>
							<td class="forminp">
								<div class="yeepdf-settings-boxcontent">
								<?php 
									$conditional = json_decode($settings["conditional_logic_datas"],true);
									if(!$conditional){
										$conditional = array(
											"type"=> "show",
											"logic"=> "all",
											"data"=> array()
										);
									}
									if($pro){  
									?>
									<input <?php checked($settings["conditional_logic"],1) ?> value="1"  type="checkbox" name="logic" id="pdf_creator_conditional_logic"> <label for="pdf_creator_conditional_logic"><?php esc_html_e(" Enable conditional logic","pdf-for-gravityforms") ?></label>
									<?php }else{ ?>
									<input disabled  type="checkbox">  <?php esc_html_e(" Enable conditional logic (Upgrade to pro version)","pdf-for-gravityforms") ?>
									<?php } ?>
									<div class="yeepdf-settings-description">
										<label for="gravityforms_pdf[name]"><?php esc_html_e("Add rules to dynamically enable or disable the PDF. When disabled, PDFs do not show up in the admin area, cannot be viewed, and will not be attached to notifications.","pdf-for-gravityforms") ?></label>
									</div>
								</div>
								<div class="yeepdf-popup-content <?php echo esc_attr( ($settings["conditional_logic"])?"show":"hidden" ) ?>">
									<select name="yeepdf_logic[type]" id="yeepdf-logic-type" class="yeepdf_input_min">
										<option <?php selected($conditional["type"],'show') ?> value="show"><?php esc_html_e("Enable","pdf-for-gravityforms") ?></option>
										<option <?php selected($conditional["type"],'hide') ?> value="hide"><?php esc_html_e("Disable","pdf-for-gravityforms") ?></option>
									</select>
									<span class="mid_content"><?php esc_html_e("this PDF if","pdf-for-gravityforms") ?></span>
									<select name="yeepdf_logic[logic]" id="yeepdf-logic-logic" class="yeepdf_input_min">
										<option <?php selected($conditional["logic"],'all') ?> value="all"><?php esc_html_e("All","pdf-for-gravityforms") ?></option>
										<option <?php selected($conditional["logic"],'any') ?> value="any"><?php esc_html_e("Any","pdf-for-gravityforms") ?></option>
									</select>
									<span class="mid_content"><?php esc_html_e("of the following match","pdf-for-gravityforms") ?>: </span>                   
									<div class="text-center yeepdf-logic-logic-bnt-container">
										<a href="#" class="yeepdf_condition_add button"><?php esc_html_e("Add Condition","pdf-for-gravityforms") ?></a>
									</div>
									<div class="yeepdf-popup-layout">
										<?php
											$datas_field = $shortcodes = Yeepdf_Builder_PDF_Shortcode::list_shortcodes();
											if( isset($conditional["conditional"]) && is_array($conditional["conditional"]) && count($conditional["conditional"])> 0 ){
												$i=1;
												foreach( $conditional["conditional"] as $data){
												?>
												<div class="yeepdf-logic-item">
													<div class="yeepdf-logic-item-name">
														<select class="yeepdf-logic-name" name="yeepdf_logic[conditional][<?php echo esc_attr($i) ?>][name]">
															<?php
															foreach( $shortcodes as $shortcode_k =>$shortcode_v){
																?>
																<optgroup label="<?php echo esc_html( $shortcode_k ) ?>"></optgroup>
																<?php
																	foreach( $shortcode_v as $k =>$v){
																		if(is_array($v)){
																			foreach( $v as $k_i =>$v_i){
																				if (strpos($k_i, "{") === false) { 
																					$name_shortcode = "[".$k_i."]";
																				}else{
																					$name_shortcode = $k_i;
																				}
																				?>
																				<option <?php selected($data["name"],$name_shortcode) ?> value="<?php echo esc_attr($name_shortcode) ?>"><?php echo esc_html($name_shortcode) ?></option>	
																				<?php
																			}
																		}else{
																			if (strpos($k, "{") === false) { 
																				$name_shortcode = "[".$k."]";
																			}else{
																				$name_shortcode = $k;
																			}
																			?>
																				<option <?php selected($data["name"],$name_shortcode) ?> value="<?php echo esc_attr($name_shortcode) ?>"><?php echo esc_html($name_shortcode) ?></option>	
																			<?php
																		}
																	}
																?>
																</optgroup>
																<?php
															}
															?>
														</select>
													</div>
													<div class="yeepdf-logic-item-rule">
														<select class="yeepdf-logic-rule" name="yeepdf_logic[conditional][<?php echo esc_attr($i) ?>][rule]">
															<option  value="is"><?php esc_html_e("is ==","pdf-for-gravityforms") ?></option>
															<option <?php selected($data["rule"],'isnot') ?> value="isnot"><?php esc_html_e("is not !=","pdf-for-gravityforms") ?></option>
															<option <?php selected($data["rule"],'greater_than') ?> value="greater_than"><?php esc_html_e("greater than >","pdf-for-gravityforms") ?></option>
															<option <?php selected($data["rule"],'less_than') ?> value="less_than"><?php esc_html_e("less than <","pdf-for-gravityforms") ?></option>
															<option <?php selected($data["rule"],'contains') ?> value="contains"><?php esc_html_e("contains","pdf-for-gravityforms") ?></option>
															<option <?php selected($data["rule"],'starts_with') ?> value="starts_with"><?php esc_html_e("starts with","pdf-for-gravityforms") ?></option>
															<option <?php selected($data["rule"],'ends_with') ?> value="ends_with"><?php esc_html_e("ends with","pdf-for-gravityforms") ?></option>
														</select>
													</div>
													<div class="yeepdf-logic-item-value">
														<input type="text" class="yeepdf-logic-value" name="yeepdf_logic[conditional][<?php echo esc_attr($i) ?>][value]" value="<?php echo esc_attr($data["value"]) ?>">
													</div>
													<div class="yeepdf-popup-layout-settings">
														<a class="yeepdf-popup-minus" href="#"><span class="dashicons dashicons-trash"></span></a>
													</div>
												</div>
												<?php
												$i++;
												}
											}
										?>
									</div>
								</div>
							</td>
						</tr>			
					</table>
				</div>
			</div>
			<?php
		}else{
			//show table
			$table = new YeePDF_Woocommerce_List_Table();
			$table->prepare_items();
			$table->display();
		}
	}
}
new Yeepdf_Add_Attachments_Woocommerce;