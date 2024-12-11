<?php

/**
 * Plugin Name: EZ Order Clone for WooCommerce
 * Plugin URI:  https://ezpage.eu
 * Description: A simple plugin to clone WooCommerce orders
 * Version:     1.0.1
 * Author:      EZ PAGE
 * Author URI:  https://ezpage.eu
 * License: GPLv3
 * Text Domain: ez-order-clone
 * Domain Path: /languages
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * See: <http://www.gnu.org/licenses/>.
 */

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Include the main class file
require_once plugin_dir_path( __FILE__ ) . 'includes/main-class.php';

// Load plugin textdomain for translations
function ez_order_clone_load_textdomain() {
    load_plugin_textdomain( 'ez-order-clone', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'ez_order_clone_load_textdomain' );

// Initialize the plugin
EZ_Order_Clone::init();