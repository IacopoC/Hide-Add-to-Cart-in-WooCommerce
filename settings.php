<?php


if (! defined('ABSPATH')) {
    exit();
}

// Setting API of option page


add_action( 'admin_menu', 'ic_hd_add_admin_menu' );
add_action( 'admin_init', 'ic_hd_settings_init' );

// Submenu page in WooCommerce menu
function ic_hd_add_admin_menu() { 

	add_submenu_page( 'woocommerce', 'IC Hide Add to Cart and prices', 'IC Hide Add to Cart and prices', 'manage_options', 'ic_hide_add_to_cart', 'ic_hd_options_page' );

}


function ic_hd_settings_init() { 

	register_setting( 'pluginPage', 'ic_settings' );

	add_settings_section(
		'ic_pluginPage_section', 
		__( 'Settings of the plugin', 'ic-hide-add-to-cart' ), 
		'ic_hd_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'ic_checkbox_field_0', 
		__( 'Turn off WooCommerce', 'ic-hide-add-to-cart' ), 
		'ic_hd_checkbox_field_0_render', 
		'pluginPage', 
		'ic_pluginPage_section' 
	);


	add_settings_field( 
		'ic_select_field_2', 
		__( 'Turn off WooCommerce by category', 'ic-hide-add-to-cart' ), 
		'ic_hd_select_field_2_render', 
		'pluginPage', 
		'ic_pluginPage_section' 
	);


	add_settings_field( 
		'ic_checkbox_field_3', 
		__( 'Turn off products prices', 'ic-hide-add-to-cart' ), 
		'ic_hd_checkbox_field_3_render', 
		'pluginPage', 
		'ic_pluginPage_section' 
	);

	add_settings_field( 
		'ic_select_field_3', 
		__( 'Turn off products prices by category', 'ic-hide-add-to-cart' ), 
		'ic_hd_select_field_4_render', 
		'pluginPage', 
		'ic_pluginPage_section' 
	);



}

// Checkbox for disable WooCommerce
function ic_hd_checkbox_field_0_render() { 

	$options = get_option( 'ic_settings' );
	?>
	<input type='checkbox' name='ic_settings[ic_checkbox_field_0]' <?php if(isset($options['ic_checkbox_field_0'])) { checked( $options['ic_checkbox_field_0'], 1 ); } ?> value='1'>
	<label><?php _e('Check to disable all Add to Cart buttons','ic-hide-add-to-cart') ?></label>
	<?php

}


// Checkbox category option function 
function ic_hd_select_field_2_render() { 

	$options = get_option( 'ic_settings' );
	global $woocommerce; 

// Loop through woocommerce categories	
	$args = array( 
				
					'taxonomy' => 'product_cat',
					'orderby'   =>'name',
					'parent'  => 0
				 );

	$product_name = get_categories($args);

// Check if the array is empty
	if (empty($product_name)) {
			_e('You have no category set for products','ic-hide-add-to-cart');
	}

	?>
	
	<?php foreach ($product_name as $term) {

		?>
		<fieldset>
		<input type='checkbox' name='ic_settings[ic_select_field_2][]' <?php if(isset($options['ic_select_field_2'])) { checked( in_array($term->term_id, $options['ic_select_field_2']), true); }?> value='<?php echo $term->term_id; ?>'>
		<label><?php echo $term->name; ?></label>
		</fieldset>
		<?php	

	}
	
}

// Checkbox for hide prices in WooCommerce
function ic_hd_checkbox_field_3_render() { 

	$options = get_option( 'ic_settings' );
	?>
	<input type='checkbox' name='ic_settings[ic_checkbox_field_3]' <?php if(isset($options['ic_checkbox_field_3'])) { checked( $options['ic_checkbox_field_3'], 1 ); } ?> value='1'>
	<label><?php _e('Check to disable all prices tags','ic-hide-add-to-cart') ?></label>
	<?php

}


// Checkbox category option function for prices 
function ic_hd_select_field_4_render() { 

	$options = get_option( 'ic_settings' );
	global $woocommerce; 

// Loop through WooCommerce categories	
	$args = array( 
				
					'taxonomy' => 'product_cat',
					'orderby'   =>'name',
					'parent'  => 0
				 );

	$product_name = get_categories($args);
	
// Check if the array is empty
	if (empty($product_name)) {
			_e('You have no category set for products','ic-hide-add-to-cart');
	}

	?>
	
	<?php foreach ($product_name as $term) {

		?>
		<fieldset>
		<input type='checkbox' name='ic_settings[ic_select_field_3][]' <?php if(isset($options['ic_select_field_3'])) { checked( in_array($term->term_id, $options['ic_select_field_3']), true); }?> value='<?php echo $term->term_id; ?>'>
		<label><?php echo $term->name; ?></label>
		</fieldset>
		<?php	
		
	}
	
}


function ic_hd_settings_section_callback() { 

	echo __( 'Check the following options to hide Add to Cart buttons and prices', 'ic-hide-add-to-cart' );

}


function ic_hd_options_page() { 

 // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
 
    // Add error/update messages
 
    // check if the user have submitted the settings
    // wordpress will add the "settings-updated" $_GET parameter to the url
    if (isset($_GET['settings-updated'])) {
        // add settings saved message with the class of "updated"
        add_settings_error('ic_hd_messages', 'ic_hd_message', __('Settings Saved', 'ic-hide-add-to-cart'), 'updated');
    }
 
    // Show error/update messages
    settings_errors('ic_hd_messages');


	?>
	<form action='options.php' method='post'>

		<h2>IC Hide Add to Cart and prices in WooCommerce</h2>

		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>

	</form>
	<?php
}


// Footer admin custom text for plugin page

function ic_hd_footer_admin_text () {

  $current_screen = get_current_screen();

    if( $current_screen ->id === "woocommerce_page_ic_hide_add_to_cart" ) {

		$custom_footer_text = sprintf(__('Thanks for using <a href="%s" target="_blank">IC Hide Add to Cart and prices in WooCommerce</a>','ic-hide-add-to-cart'), __('https://wordpress.org/plugins/ic-hide-add-to-cart-and-prices-in-woocommerce/'));

	return $custom_footer_text;

	}

}

add_filter('admin_footer_text', 'ic_hd_footer_admin_text');
