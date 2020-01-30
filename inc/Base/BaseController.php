<?php

namespace Inc\Base;

class BaseController
{
    public $plugin_path;
    public $plugin_url;
    public $otas = ['Agoda', 'Booking.com', 'Hotels.com'];
    // temporary
    public $to = 'arifin.sunmedia@gmail.com';
    public $subject = 'Cota - Price lower than OTA';
    public $body = 'Price lower than OTA please check the price on the Website.';
    public $headers = [];

    public function __construct()
    {
        $this->plugin_path = plugin_dir_path(dirname(__FILE__, 2));
        $this->plugin_url = plugin_dir_url(dirname(__FILE__, 2));
    }
}