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
$template = $atts["type"];
$show_img = $atts["show_img"];
$show_sku = $atts["item_sku"];
$show_des = $atts["show_des"];
$show_item_totals = $atts["item_totals"];
$column = 5;
$order_items    = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
?>
<div class="yeepdf-order-detail">
	<table class="td yeepdf-table yeepdf-woocommerce-table yeepdf-order-detail-<?php echo esc_attr($template) ?>">
		<thead>
			<tr>
				<?php 
                if($show_sku !="yes" ){ 
                    $column--;
                }
                if($show_img !="yes" ){ 
                    $column--;
                }
                if($show_des !="yes" ){ 
                    $column--;
                }   
                ?>
                <th data-sku="<?php echo esc_attr( $show_sku ); ?>" class="sku" scope="col" style="text-align:<?php echo esc_attr( $text_align );?>;"><?php esc_html_e("SKU","woocommerce") ?></th>
                <th data-showimg="<?php echo esc_attr( $show_img ); ?>" class="thumbnail" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Thumbnail', "woocommerce" ); ?></th>
                <th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
                <th data-showdes="<?php echo esc_attr( $show_des ); ?>"  class="td des" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Descriptions', "pdf-for-woocommerce" ); ?></th>
                <th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
                <th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ( $order_items as $item_id => $item ) {
				$product = $item->get_product(); 
				if(!$product){
					continue;
				}
				$qty          = $item->get_quantity();
				$refunded_qty = $order->get_qty_refunded_for_item( $item_id );
				if ( $refunded_qty ) {
					$qty_display = '<del>' . esc_html( $qty ) . '</del> <ins>' . esc_html( $qty - ( $refunded_qty * -1 ) ) . '</ins>';
				} else {
					$qty_display = esc_html( $qty );
				}
				?>
				<tr>
					<td data-sku="<?php echo esc_attr( $show_sku ); ?>" class="sku" >
						<?php echo esc_attr( $product->get_sku() ) ?>
					</td>
					<td data-showimg="<?php echo esc_attr( $show_img ); ?>"  class="thumbnail" >
						<?php if( $product->get_image_id() > 0 ) { ?>
						<img style="width: 150px" src="<?php echo esc_url(wp_get_attachment_url( $product->get_image_id() )) ?>" alt="Thumnail">
						<?php } ?>
					</td>
					<td class="product">
						<?php 
						echo esc_html($item->get_name());
						wc_display_item_meta($item);
						?>
					</td>
					<td data-showdes="<?php echo esc_attr( $show_des ); ?>"  class="des">
						<?php 
							echo $product->get_short_description(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</td>
					<td class="">
						<?php echo esc_attr($qty_display) ?>
					</td>
					<td class="">
						<?php printf("%s", $product->get_price_html() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</td>
				</tr>
				<?php
			}
			?>
		</tbody>
		<tfoot data-totals="<?php echo esc_attr( $show_item_totals ); ?>" class="woo_totals" >
			<?php
			$item_totals = $order->get_order_item_totals();
			if ( $item_totals ) {
				$i = 0;
				foreach ( $item_totals as $total ) {
					$i++;
					?>
				<tr class="tfoot-tr-<?php echo esc_attr( $i ) ?>">
                    <th scope="row" colspan="<?php echo esc_attr( $column ) ?>" style="text-align:<?php echo esc_attr( $text_align );?>" align="left"><?php echo wp_kses_post( $total['label'] ); ?></th>
                    <td style="text-align:<?php echo esc_attr( $text_align );?>;"><?php echo wp_kses_post( $total['value'] ); ?></td>
                </tr>
					<?php
				}
			}
			if ( $order->get_customer_note() ) {
				?>
				<tr>
					<th class="td" colspan="<?php echo esc_attr( $column ) ?>"  style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Note:', "pdf-customizer-for-woocommerce" ); ?></th>
					<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
				</tr>
				<?php
			}
			?>
		</tfoot>
	</table>
</div>