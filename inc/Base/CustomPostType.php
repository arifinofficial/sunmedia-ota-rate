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
            'name' => _x('Cota Rates', 'plural'),
            'singular_name' => _x('Cota Rate', 'singular'),
        );
    
        $args = array(
            'supports' => $supports,
            'labels' => $labels,
            'query_var' => true,
            'show_in_menu' =>false,
            'show_ui' => true,
            'rewrite'     => array('slug' => 'cota-rates-cpt'),
        );
            
    
        register_post_type('sm_cotarate', $args);
    }
}