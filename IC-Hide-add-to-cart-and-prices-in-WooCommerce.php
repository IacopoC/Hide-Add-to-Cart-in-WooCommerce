<?php
/*
Plugin Name: IC Hide Add to Cart and prices in WooCommerce
Plugin URI: http://iacopocutino.it/ic-hide-add-to-cart-plugin/
Description: A simple plugin useful to hide add to cart buttons and prices from WooCommerce sites. Requires WooCommerce plugin.
Author: Iacopo C
Version: 1.5
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

// Add warning banner if the plugin in active but WooCommerce is inactive

function ic_hd_error_notice() {
  
  if( !class_exists('woocommerce')) {
    ?>
    <div class="error notice">
        <p><?php _e( 'IC Hide add to Cart and prices require WooCommerce, activate WooCommerce to use this plugin', 'ic_hd_plugin' ); ?></p>
    </div>
    <?php
  }
}
add_action( 'admin_notices', 'ic_hd_error_notice' );

// Check if WooCommerce is active or if WooCommerce Multisite configuration is enabled

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || array_key_exists('woocommerce/woocommerce.php', get_site_option('active_sitewide_plugins')) ) {


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

  function ic_hd_categories_off($purchasable, $product) {

  	$not_purchasable_cat_ids = get_option( 'ic_settings' )['ic_select_field_2'];

  	$categories = get_the_terms($product->get_id(), 'product_cat');

    if(is_array($categories)) {   

      foreach($categories as $category)  {
    	  
       if( in_array( $category->term_id, $not_purchasable_cat_ids )) {
          
            return false;
          } 
          return true;
        }
  
      }
    }  
  
  add_filter( 'woocommerce_is_purchasable', 'ic_hd_categories_off', 10, 2 );


}

// Hide prices in WooCommerce


$checkbox_prices = isset(get_option('ic_settings')['ic_checkbox_field_3']);

 if ( $checkbox_prices == '1') {

 
function ic_hd_remove_prices( $price, $product ) {

  $price = __('The price is not available','ic_hd_plugin');

  return $price;
}

add_filter( 'woocommerce_variable_sale_price_html', 'ic_hd_remove_prices', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 'ic_hd_remove_prices', 10, 2 );
add_filter( 'woocommerce_get_price_html', 'ic_hd_remove_prices', 10, 2 );

 }



// Hide prices in WooCommerce per categories

 if( isset(get_option( 'ic_settings' )['ic_select_field_3'])) {

function ic_hd_remove_prices_by_categories($price) {

  global $post, $product;
	
  $categories = get_the_terms( $product->get_id(), 'product_cat' );

  $not_purchasable_cat_ids = get_option( 'ic_settings' )['ic_select_field_3'];

// if categories is not an array, display the message
  if(!is_array($categories)) {
	  
	    $price = __('The price is not available','ic_hd_plugin');
 
    	return $price;
	 } 

  foreach ( $categories as $category ) $categories[] = $category->term_id;  


    if (in_array($category->term_id, $not_purchasable_cat_ids)) { 

      $price = __('The price is not available','ic_hd_plugin'); 

      return $price;
    }
	  return $price;
 
}

add_action('woocommerce_get_price_html','ic_hd_remove_prices_by_categories');
add_action( 'woocommerce_variable_sale_price_html', 'ic_hd_remove_prices_by_categories');
add_action( 'woocommerce_variable_price_html', 'ic_hd_remove_prices_by_categories');

}
 
}



?>