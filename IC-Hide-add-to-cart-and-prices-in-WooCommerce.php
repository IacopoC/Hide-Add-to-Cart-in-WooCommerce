<?php
/*
Plugin Name: IC Hide Add to Cart and prices in WooCommerce
Plugin URI: http://iacopocutino.it/ic-hide-add-to-cart-plugin/
Description: A simple plugin useful to hide add to cart buttons and prices from WooCommerce sites.
Author: Iacopo C
Version: 1.0
Author URI: http://iacopocutino.it
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see http://www.gnu.org/licenses/gpl-3.0.html.
*/

if (! defined('ABSPATH')) {
    exit();
}

// add settings page

require ('settings.php');

// Check if WooCommerce is active and or if Multisite is active
 
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) 
    || array_key_exists('woocommerce/woocommerce.php', get_site_option('active_sitewide_plugins')) ) {


// Deactivate WooCommerce buttons for every product

$checkbox_allproducts = isset(get_option('ic_settings')['ic_checkbox_field_0']);

 if ( $checkbox_allproducts == '1')   {

function ic_hd_product_is_purchasable( $purchasable ){
        $purchasable = false;
    return $purchasable;
}
add_filter( 'woocommerce_is_purchasable', 'ic_hd_product_is_purchasable', 10, 2 );

}

// Shut down WooCommerce by category

 if( isset(get_option( 'ic_settings' )['ic_select_field_2'])) {

  function ic_hd_categories_off($purchasable, $product){

  	$not_purchasable_cat_ids = get_option( 'ic_settings' )['ic_select_field_2'];

  	$categories = get_the_terms($product->ID, 'product_cat');
   
    foreach($categories as $category) 
    	{
        if( in_array( $category->term_id, $not_purchasable_cat_ids ) )
        {
            return false;
        } 
        return true;
    }
  
    }
  
  add_filter( 'woocommerce_is_purchasable', 'ic_hd_categories_off', 10, 2 );


}

// Hide prices in Woocommerce

$checkbox_prices = isset(get_option('ic_settings')['ic_checkbox_field_3']);

 if ( $checkbox_prices == '1') {

 
function ic_hd_remove_prices( $price, $product ) {

  $price = '';

  return $price;
}

add_filter( 'woocommerce_variable_sale_price_html', 'ic_hd_remove_prices', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 'ic_hd_remove_prices', 10, 2 );
add_filter( 'woocommerce_get_price_html', 'ic_hd_remove_prices', 10, 2 );

 }


}
?>
