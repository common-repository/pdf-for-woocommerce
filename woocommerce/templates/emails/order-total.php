<?php
/**
 * Order details table shown in emails.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.7.0
 */
defined( 'ABSPATH' ) || exit;
$text_align = is_rtl() ? 'right' : 'left';
$item_totals = $order->get_order_item_totals();
if ( $item_totals ) {
	$i = 0;
	echo '<table class="yeepdf-table yeepdf-woocommerce-table">';
	foreach ( $item_totals as $total ) {
		$i++;
		?>
		<tr>
			<th class="td" scope="row" style="text-align:<?php echo esc_attr( $text_align ); ?>; ?>"><?php echo wp_kses_post( $total['label'] ); ?></th>
			<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; ?>"><?php echo wp_kses_post( $total['value'] ); ?></td>
		</tr>
		<?php
	}
	echo "</table>";
}