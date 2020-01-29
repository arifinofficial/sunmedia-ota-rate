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

        add_action('admin_init', [$this, 'registerSettings']);
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

    public function registerSettings()
    {
        register_setting('cota-settings', '_cota_front_single_lower');
        register_setting('cota-settings', '_cota_advance_price');

        add_settings_section('_cota_label_settings', 'COTA Settings', [$this, 'cotaSettingCallback'], 'cota-settings');

        add_settings_field('_cota_front_single_lower_label_field', 'Set display property from lower price?', [$this, 'cotaSettingFieldCallback'], 'cota-settings', '_cota_label_settings', ['class' => 'cota-label-setting']);
        add_settings_field('_cota_advance_price_label_field', 'Hide more expensive property?', [$this, 'cotaSettingAdvanceFieldCallback'], 'cota-settings', '_cota_label_settings', ['class' => 'cota-label-setting']);
    }

    public function cotaSettingCallback()
    {
        echo '<span>System Settings</span>';
    }

    public function cotaSettingFieldCallback()
    {
        $checked = get_option('_cota_front_single_lower');
        echo '<input type="checkbox" name="_cota_front_single_lower" value="1" '. checked($checked, '1', false) .' >';
        // echo '<input type="text" name="_cota_front_single_lower" value="testing">';
    }

    public function cotaSettingAdvanceFieldCallback()
    {
        $checked = get_option('_cota_advance_price');
        echo '<input type="checkbox" name="_cota_advance_price" value="1" '. checked($checked, '1', false) .'>';
    }
}