<?php
defined( 'ABSPATH' ) || exit;
$text_align = is_rtl() ? 'right' : 'left';
?>
<h2 class="woocommerce-order-downloads__title"><?php esc_html_e( 'Downloads', 'woocommerce' ); ?></h2>
<table class="yeepdf-woocommerce-table yeemail-table" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
	<thead>
		<tr>
			<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_attr_e( "Product", "woocommerce" ) ?></th>
			<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_attr_e( "Expires", "woocommerce" ) ?></th>
			<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_attr_e( "Download", "woocommerce" ) ?></th>
		</tr>
	</thead>
		<tr>
            <td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;">
            <?php esc_attr_e( "YeeMail Product", "yeemail" ) ?>
            </td>
            <td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;">
            <?php esc_attr_e( "Never", "yeemail" ) ?>
            </td>
            <td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;">
                <a href="#"><?php esc_attr_e( "Link Download Product", "yeemail" ) ?></a>
            </td>
		</tr>
</table>
