<?php

/*
Plugin Name: COTA Rate
Description: For development purposes only. Sun Media Jaya! 
Author: <a href="https://www.sunmedia.co.id/">Sun Media Team</a>
Version: 0.1 Beta
*/

defined('ABSPATH') or die('Error!');
function your_function_name() {
    wp_mail('arifin.sunmedia@gmail.com', 'test', 'testing', $headers[] );    
}
add_action( 'wp_loaded', 'your_function_name', 12 );

add_action( 'wp_mail_failed', 'onMailError', 10, 1 );
function onMailError( $wp_error ) {
    echo "<pre>";
    print_r($wp_error);
    echo "</pre>";
}    
// wp_mail( 'arifinofficial@outlook.com', 'halo', 'testing');
if (file_exists(dirname(__FILE__).'/vendor/autoload.php')) {
    require_once dirname(__FILE__).'/vendor/autoload.php';
}

function plugin_activate()
{
    Inc\Base\Activate::activate();
}
register_activation_hook(__FILE__, 'plugin_activate');

function plugin_deactivate()
{
    Inc\Base\Deactivate::deactivate();
}
register_deactivation_hook(__FILE__, 'plugin_deactivate');

if (class_exists('Inc\\Init')) {
    Inc\Init::register_services();
}






