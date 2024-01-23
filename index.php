<?php

/*
	Plugin Name: Shipping address manager
	Description: Provides a workaround to setup WooCommerce deliver to a different address as default 
	Version: 1.0.0
	Author: Pablo Bozzolo < boctulus@gmail.com >

	Code:

	@author Pablo Bozzolo
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


/*
    Sets "Deliver to a different address" as default option (checked)
*/

if (get_option('woocommerce_ask_for_shipping_address')){
    add_filter( 'woocommerce_ship_to_different_address_checked', '__return_true' );
}

/*
    Now using JS
*/
function my_plugin_enqueue_scripts() {
    // $cfg = require __DIR__  . '/config/config.php';

    if (get_option('woocommerce_ask_for_shipping_address') && get_option('woocommerce_force_using_javascript')){
        wp_enqueue_script('jquery');
        wp_enqueue_script('my-custom-script', plugin_dir_url(__FILE__) . 'assets/js/shipping_default_opt.js', array('jquery'), '1.0', true);

        // PHP to JavaScript 
        // wp_localize_script('my-custom-script', 'my_plugin_vars', array('variable_name' => 'variable_value'));
    }
}

add_action('wp_enqueue_scripts', 'my_plugin_enqueue_scripts');


/*
    Menu
*/

// Función para agregar una opción en el panel de administración de WooCommerce
function add_custom_shipping_option() {
    add_submenu_page(
        'options-general.php',  // Cambia 'woocommerce' a 'options-general.php' para mover la página a dentro de "Settings"
        'WooCommerce Shipping',
        'WooCommerce Shipping',
        'manage_options',
        'woocommerce-shipping-settings',
        'render_shipping_settings_page'
    );
    
    // Añadir una página de menú en WooCommerce
    add_submenu_page(
        'woocommerce',
        'Shipping manager',
        'Shipping manager',
        'manage_options',
        'shipping-settings',
        'render_shipping_settings_page'
    );    
}


// Función para renderizar la página de configuración
function render_shipping_settings_page() {
    ?>
    <div class="wrap">
        <h2>WooCommerce Shipping Settings</h2>
        <form method="post" action="options.php">
            <?php
            // Agregar los campos de configuración
            settings_fields('woocommerce_shipping_options');
            do_settings_sections('woocommerce_shipping_options');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Función para registrar la configuración y los campos
function register_shipping_settings() {
    register_setting('woocommerce_shipping_options', 'woocommerce_ask_for_shipping_address', array(
        'type' => 'boolean',
        'default' => false,
    ));

    register_setting('woocommerce_shipping_options', 'woocommerce_force_using_javascript', array(
        'type' => 'boolean',
        'default' => false,
    ));

    add_settings_section('shipping_section', 'Shipping Options', 'shipping_section_callback', 'woocommerce_shipping_options');

    add_settings_field('ask_for_shipping_address', 'Ask for customer shipping address by default', 'ask_for_shipping_address_callback', 'woocommerce_shipping_options', 'shipping_section');
    add_settings_field('force_using_javascript', 'Force using Javascript', 'force_using_javascript_callback', 'woocommerce_shipping_options', 'shipping_section');
}   

// Función de devolución de llamada para la sección
function shipping_section_callback() {
    echo '<p>Configure shipping options for your WooCommerce store.</p>';
}

// Función de devolución de llamada para el campo "Ask for Custommer shipping address by default"
function ask_for_shipping_address_callback() {
    $ask_for_shipping_address = get_option('woocommerce_ask_for_shipping_address');
    echo '<label><input type="radio" name="woocommerce_ask_for_shipping_address" value="1" ' . checked($ask_for_shipping_address, 1, false) . '> Yes</label>';
    echo '<label style="margin-left: 10px;"><input type="radio" name="woocommerce_ask_for_shipping_address" value="0" ' . checked($ask_for_shipping_address, 0, false) . '> No</label>';
}

// Función de devolución de llamada para el campo "Force using Javascript"
function force_using_javascript_callback() {
    $force_using_javascript = get_option('woocommerce_force_using_javascript');
    echo '<label><input type="checkbox" name="woocommerce_force_using_javascript" value="1" ' . checked($force_using_javascript, 1, false) . '> Enable</label>';
}

// Añadir las acciones para activar las opciones
add_action('admin_menu', 'add_custom_shipping_option');
add_action('admin_init', 'register_shipping_settings');

