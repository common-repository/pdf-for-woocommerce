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
	?>
<table class="yeepdf-table yeepdf-woocommerce-table">
	<tr>
		<th class="td" scope="row" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e("Subtotal","woocommerce") ?>:</th>
		<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;">$29</td>
	</tr>
	<tr>
		<th class="td" scope="row" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e("Payment method","woocommerce") ?>:</th>
		<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;">PayPal</td>
	</tr>
	<tr>
		<th class="td" scope="row" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e("Total","woocommerce") ?>:</th>
		<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;">$29</td>
	</tr>
</table>
