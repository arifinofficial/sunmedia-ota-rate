<?php

namespace Inc\Base;

class Shortcode extends BaseController
{
    public function register()
    {
        add_shortcode('cota_single_item', [$this, 'singlePriceShortcode']);
    }

    public function singlePriceShortcode()
    {

        $args = array('post_type' => 'sm_cotarate');

        $the_query = new \WP_Query($args);
        while ($the_query->have_posts()) : $the_query->next_post();
        $id= $the_query->post->ID;
        $price = get_post_meta($id, '_cota_price', true);
       
        if (array_sum($price) != 0) {
            foreach ($this->otas as $key => $value) {
                $arr[$value] = $price[$key];
            }
        }
        endwhile;

        return json_encode($arr);
    }
}
