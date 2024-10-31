<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$text_align = is_rtl() ? 'right' : 'left';
?><table id="addresses" class="yeemail-woocommerce-style" cellspacing="0" cellpadding="0" style="width: 100%; vertical-align: top; padding:0;" border="0">
	<tr>
		<td style="text-align:<?php echo esc_attr( $text_align ); ?>; border:0; padding:0; padding-right:15px;" valign="top" width="50%">
			<h2><?php esc_html_e( 'Billing address', 'woocommerce' ); ?></h2>
			<address class="address">
                Tayler Holder<br>
                YeeMail<br>
                6854 Edwards Rd<br>
                Edwards Rd<br>
                (820) 555-999
			</address>
		</td>
        <td style="text-align:<?php echo esc_attr( $text_align ); ?>; padding:0;" valign="top" width="50%">
            <h2><?php esc_html_e( 'Shipping address', 'woocommerce' ); ?></h2>
            <address class="address">
                Tayler Holder<br>
                YeeMail<br>
                6854 Edwards Rd<br>
                Edwards Rd<br>
                (820) 555-999
            </address>
        </td>
	</tr>
</table>
