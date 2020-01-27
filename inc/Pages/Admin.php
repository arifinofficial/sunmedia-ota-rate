<?php

namespace Inc\Pages;

use Inc\Base\BaseController;
use Inc\Api\CustomField;

class Admin extends BaseController
{
    public $customFields = array();
    
    public function register()
    {
        $this->customFields = new CustomField();

        add_action('admin_menu', [$this, 'add_admin_page']);
    }

    public function add_admin_page()
    {
        add_menu_page(
            'COTA Rate',
            'COTA Rate',
            'manage_options',
            'cota-rate',
            [$this, 'admin_index'],
            'dashicons-admin-site-alt3'
        );

        add_submenu_page(
            'cota-rate',
            'Rates',
            'Rates',
            'manage_options',
            'edit.php?post_type=sm_cotarate',
            NULL
        );
    }

    public function admin_index()
    {
        require_once $this->plugin_path . 'templates/admin.php';
    }

    public function adminCallback($input)
    {
        return $input;
    }

    // public function setCustomFields()
    // {
    //     $args = [
    //         [
    //             'option_group' => 'cota_options_group',
    //             'option_name' => '_cota',
    //             'callback' => 'adminCallback'
    //         ]
    //     ];

    //     $this->
    // }
}