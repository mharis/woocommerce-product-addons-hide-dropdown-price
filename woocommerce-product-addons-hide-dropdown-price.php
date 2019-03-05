<?php
/**
 * Plugin Name: WooCommerce Product Addons - Hide Price from Dropdown
 * Plugin URI:
 * Description: Hides price from dropdown of WooCommerce Product addons.
 * Version: 1.0.0
 * Author: Haris Zulfiqar
 * Author URI: https://hariszulfiqar.com
 *
 * @package WooCommerce
 * @since 1.0.0
 * @author Haris Zulfiqar <haris@hariszulfiqar.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Removes product addons price from the product addons dropdown.
 *
 * @since 1.0.0
 * @author Haris Zulfiqar <haris@hariszulfiqar.com>
 * @param string $price The default price string.
 * @return string
 */
function hariszulfiqar_woocommerce_product_addons_hide_price( $price ) {
	$price = '';

	return $price;
}
add_filter( 'woocommerce_product_addons_option_price', 'hariszulfiqar_woocommerce_product_addons_hide_price', 10, 4 );

/**
 * Removes product addons with price from the WooCommerce cart.
 *
 * @since 1.0.0
 * @author Haris Zulfiqar <haris@hariszulfiqar.com>
 */
function hariszulfiqar_woocommerce_product_addons_hide_default_addons_price() {
	remove_filter( 'woocommerce_get_item_data', array( $GLOBALS['Product_Addon_Cart'], 'get_item_data' ), 10 );
}
add_action( 'init', 'hariszulfiqar_woocommerce_product_addons_hide_default_addons_price', 10 );

/**
 * Adds product addons to the cart without the price.
 *
 * @since 1.0.0
 * @author Haris Zulfiqar <haris@hariszulfiqar.com>
 * @param array $other_data Product cart data.
 * @param array $cart_item Product items, addons, etc.
 * @return array
 */
function hariszulfiqar_woocommerce_product_addons_add_to_cart( $other_data, $cart_item ) {
	if ( ! empty( $cart_item['addons'] ) ) {
		foreach ( $cart_item['addons'] as $addon ) {
			$price = isset( $cart_item['addons_price_before_calc'] ) ? $cart_item['addons_price_before_calc'] : $addon['price'];
			$name  = $addon['name'];

			if ( 0 === $addon['price'] ) {
				$name .= '';
			} elseif ( 'percentage_based' === $addon['price_type'] && 0 === $price ) {
				$name .= '';
			} elseif ( 'percentage_based' !== $addon['price_type'] && $addon['price'] && apply_filters( 'woocommerce_addons_add_price_to_name', '__return_true' ) ) {
				$name .= '';
			} else {
				$_product = new WC_Product( $cart_item['product_id'] );
				$_product->set_price( $price * ( $addon['price'] / 100 ) );
				$name .= '';
			}

			$other_data[] = array(
				'name'    => $name,
				'value'   => $addon['value'],
				'display' => isset( $addon['display'] ) ? $addon['display'] : '',
			);
		}
	}

	return $other_data;
}
add_filter( 'woocommerce_get_item_data', 'hariszulfiqar_woocommerce_product_addons_add_to_cart', 10, 2 );


/**
 * Injects CSS styles on product page to hide the product addons price from the total table.
 *
 * @since 1.0.0
 * @author Haris Zulfiqar <haris@hariszulfiqar.com>
 */
function hariszulfiqar_woocommerce_product_addons_hide_price_from_totals() {
	if ( class_exists( 'WooCommerce' ) && is_product() ) :
	?>
		<style type="text/css">
			.product-addon-totals .wc-pao-col2 {
				display: none;
			}
		</style>
<?php
	endif;
}
add_action( 'wp_head', 'hariszulfiqar_woocommerce_product_addons_hide_price_from_totals' );

/**
 * Hide price from order details.
 *
 * @return boolean
 */
function hariszulfiqar_woocommerce_product_addons_hide_price_from_order_details() {
	return false;
}
add_filter( 'woocommerce_addons_add_price_to_name', 'hariszulfiqar_woocommerce_product_addons_hide_price_from_order_details' );
