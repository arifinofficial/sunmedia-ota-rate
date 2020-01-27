<?php

/*
Plugin Name: COTA Rate
Description: For development purposes only. Sun Media Jaya! 
Author: <a href="https://www.sunmedia.co.id/">Sun Media Team</a>
Version: 0.1 Beta
*/

defined('ABSPATH') or die('Error!');

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






