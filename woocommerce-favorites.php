<?php
/*
Plugin Name: WooCommerce Favorites
Plugin URI:  https://objectiv.co
Description: Users can easily favorite products for their WooCommerce store
Version:     1.0
Author:      Objectiv
Author URI:  https://objectiv.co
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: obj-favorites
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FILE', __FILE__ );
define( 'PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'VERSION', 1.0 );

include_once( PLUGIN_DIR . '/includes/obj-class-main.php' );

$obj_main;
$obj_main = new Obj_Main();
