<?php

namespace Inc\Base;

class CustomPostType
{
    public function register()
    {
        add_action('init', [$this, 'customPostType']);
    }

    public function customPostType()
    {
        $supports = array(
            'title',
            'thumbnail',
            'post-formats',
        );
    
        $labels = array(
            'name' => __('Cota Rates', 'plural'),
            'singular_name' => __('Cota Rate', 'singular'),
            'add_new_item' => __('Add New Rate'),
            'add_new' => __('Add New Rate'),
            'edit_item' => __('Edit Rate'),
        );
    
        $args = array(
            'supports' => $supports,
            'labels' => $labels,
            'public' => false,
            'query_var' => true,
            'show_in_menu' =>false,
            'show_ui' => true,
            'rewrite'     => array('slug' => 'cota-rates-cpt'),
        );
            
    
        register_post_type('sm_cotarate', $args);
    }
}