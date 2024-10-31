<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$text_align = is_rtl() ? 'right' : 'left';
$shipping   = $order->get_formatted_shipping_address();
if( $shipping == ""){
	$shipping = esc_html__("No shipping address set.","woocommerce");
}
?>
<address class="address"><?php echo wp_kses_post( $shipping ); ?></address>