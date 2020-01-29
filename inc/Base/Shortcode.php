<?php

namespace Inc\Base;

class Shortcode extends BaseController
{
    public function register()
    {
        add_shortcode('cota_single_item', [$this, 'singlePriceShortcode']);
        add_shortcode('cota_advance_item', [$this, 'advancePriceShortcode']);
    }

    public function singlePriceShortcode()
    {
        $args = ['post_type' => 'sm_cotarate'];
        $the_query = new \WP_Query($args);

        // get setting option
        $setting = get_option('_cota_front_single_lower');

        // checking setting _cota_front_single_lower
        $arr = [];
        $i = 0;

        // loop 
        while ($the_query->have_posts()) : $the_query->next_post();
        $id= $the_query->post->ID;
        
        $nameProperty = get_the_title($id);
        $prices = get_post_meta($id, '_cota_price', true);
        $priceProperty = get_post_meta($id, '_cota_set_property_price', true);

        // checking price must be != 0
        if (array_sum($prices) != 0) {
            if ($setting == 1) {
                // $rates = [];
                    $arr[$i]['property']['name'] = $nameProperty;
                    $arr[$i]['property']['rate'] = $priceProperty;
                foreach ($this->otas as $key => $value) {
                    $arr[$i]['ota-rates'][$value] = $prices[$key];
                }
                $i++;
            } else {
                $arr[$i]['property']['name'] = $nameProperty;
                $arr[$i]['property']['rate'] = $priceProperty;
                foreach ($this->otas as $key => $value) {
                    $arr[0]['ota-rates'][$value] = $prices[$key];
                }

                return json_encode($arr);
            }
        }
        endwhile;
        
        //remove if ota less then property price
        foreach ($arr as $key => $arrCount) {
           foreach ($arrCount['ota-rates'] as $otaRates) {
               if ($arrCount['property']['rate'] > $otaRates) {
                continue 2;
               }
           }

           // set from last data
           $arrTotal = $arr[$key];
        }

        return json_encode($arrTotal);
    }

    public function advancePriceShortcode()
    {
        $args = ['post_type' => 'sm_cotarate'];
        $the_query = new \WP_Query($args);

        // get setting option
        $setting = get_option('_cota_advance_price');

        // checking setting _cota_advance_price
        $arr = [];
        $i = 0;

        // loop 
        while ($the_query->have_posts()) : $the_query->next_post();
        $id= $the_query->post->ID;
        
        $nameProperty = get_the_title($id);
        $prices = get_post_meta($id, '_cota_price', true);
        $priceProperty = get_post_meta($id, '_cota_set_property_price', true);

        // checking price must be != 0
        if (array_sum($prices) != 0) {
            
                // $rates = [];
                    $arr[$i]['property']['name'] = $nameProperty;
                    $arr[$i]['property']['rate'] = $priceProperty;
                foreach ($this->otas as $key => $value) {
                    $arr[$i]['ota-rates'][$value] = $prices[$key];
                }
                $i++;
        }
        endwhile;

        if ($setting == 1) {
            //remove if ota less then property price
            foreach ($arr as $key => $arrCount) {
                foreach ($arrCount['ota-rates'] as $otaRates) {
                    if ($arrCount['property']['rate'] > $otaRates) {
                    continue 2;
                    }
                }
                $arrTotal[] = $arr[$key];
            }
            return json_encode($arrTotal);
        } else {
            return json_encode($arr);
        }
    }
}
