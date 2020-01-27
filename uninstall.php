<?php 
/**
 * @package Sun Media Cota Rate
 * @author Arifin N <arifinofficial@outlook.com>
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

$fields = get_posts(array('post_type' => 'sm_cotarate', 'numberposts' => -1));

foreach ($fields as $field) {
    wp_delete_post($field->ID, true);
}