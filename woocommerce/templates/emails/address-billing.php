<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$text_align = is_rtl() ? 'right' : 'left';
$address    = $order->get_formatted_billing_address();
?>
<address class="address">
		<?php echo wp_kses_post( $address ? $address : esc_html__( 'N/A', "woocommerce" ) ); ?>
		<?php if ( $order->get_billing_phone() ) : ?>
			<br/><?php echo wc_make_phone_clickable( $order->get_billing_phone() ); ?>
		<?php endif; ?>
		<?php if ( $order->get_billing_email() ) : ?>
			<br/><?php echo esc_html( $order->get_billing_email() ); ?>
		<?php endif; ?>
	</address>