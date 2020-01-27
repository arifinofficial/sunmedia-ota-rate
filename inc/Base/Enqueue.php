<?php

namespace Inc\Base;

use Inc\Base\BaseController;

class enqueue extends BaseController
{
    public function register()
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueue'));
    }

    public function enqueue()
    {
        wp_enqueue_style('style', $this->plugin_url . 'assets/css/style.css');
        wp_enqueue_script('app', $this->plugin_url . 'assets/js/app.js');

        if ('sm_cotarate' == get_post_type()) {
            wp_dequeue_script( 'autosave' );
        }
    }
}