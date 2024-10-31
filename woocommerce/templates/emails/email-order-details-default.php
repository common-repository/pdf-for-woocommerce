<?php
defined( 'ABSPATH' ) || exit; 
$text_align = is_rtl() ? 'right' : 'left'; 
$template = $atts["type"];
$show_img = $atts["show_img"];
$show_sku = $atts["item_sku"];
$show_des = $atts["show_des"];
$show_item_totals = $atts["item_totals"];
$column = 5;
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
        <tr>
            <td data-sku="<?php echo esc_attr( $show_sku ); ?>" class="sku"style="text-align:<?php echo esc_attr( $text_align );?>;" >
                SKU 123
            </td>
            <td data-showimg="<?php echo esc_attr( $show_img ); ?>" class="thumbnail" style="text-align:<?php echo esc_attr( $text_align );?>" >
                <img style="width: 150px" src="<?php echo esc_url(YEEPDF_BUILDER_WOOCOMMERCE_URL."images/default-image.png") ?>" alt="Thumnail">
            </td>
            <td style="text-align:<?php echo esc_attr( $text_align );?>">
                YeePDF Product
            </td>
            <td data-showdes="<?php echo esc_attr( $show_des ); ?>"  class="des" style="text-align:<?php echo esc_attr( $text_align );?>">
                Lorem Ipsum is simply dummy text of the printing and typesetting industry.
            </td>
    		<td style="text-align:<?php echo esc_attr( $text_align );?>">
    		    1		
            </td>
    		<td style="text-align:<?php echo esc_attr( $text_align );?>;">
    			<span><span>$</span>29.00</span>		
            </td>
    	</tr>
        </tbody>
        <tfoot data-totals="<?php echo esc_attr( $show_item_totals ); ?>" class="woo_totals" >
            <tr class="tfoot-tr-1">
                <th scope="row" colspan="<?php echo esc_attr( $column ) ?>" style="text-align:<?php echo esc_attr( $text_align );?>" align="left"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?>:</th>
                <td style="text-align:<?php echo esc_attr( $text_align );?>;"><span><span>$</span>29.00</span></td>
            </tr>
            <tr>
                <th scope="row" colspan="<?php echo esc_attr( $column ) ?>" style="text-align:<?php echo esc_attr( $text_align );?>" align="left"><?php esc_html_e( 'Payment method', 'woocommerce' ); ?>:</th>
                <td style="text-align:<?php echo esc_attr( $text_align );?>">PayPal</td>
            </tr>
                <tr>
                <th scope="row" colspan="<?php echo esc_attr( $column ) ?>" style="text-align:<?php echo esc_attr( $text_align );?>" align="left"><?php esc_html_e( 'Total', 'woocommerce' ); ?>:</th>
                <td style="text-align:<?php echo esc_attr( $text_align );?>" align="left"><span><span>$</span>29.00</span></td>
            </tr>
        </tfoot>
    </table>
</div>