<?php

namespace Inc\Base;

use Inc\Api\HotelApi\HotelApiPrice;

class CustomMetaBox
{
    protected $otas = ['Agoda', 'Booking.com', 'Hotels.com'];
    protected $codeOta = ['Agoda', 'BookingCom', 'HotelsCom2'];
    public $api;

    public function register()
    {
        add_action('add_meta_boxes', [$this, 'addMetaBox']);
        add_action('save_post', [$this, 'savePriceFields']);
        add_action('save_post', [$this, 'saveFetchData']);
    }

    public function addMetaBox()
    {
        add_meta_box('cota_meta_box_price_ota', 'OTA Price', [$this, 'cotaPriceCallback'], 'sm_cotarate', 'normal', 'default');
        add_meta_box('cota_meta_box_use_api', 'Fetch Data', [$this, 'cotaFetchData'], 'sm_cotarate', 'normal', 'default');
    }

    public function cotaPriceCallback($post)
    {
        // var_dump($this->api->callApi());
        // die;
        foreach ($this->otas as $key => $ota) {
            wp_nonce_field('savePriceFields', "price_field_nonce[$key][$ota]" );

            $value = get_post_meta( $post->ID, '_cota_price', true);

            echo '<label style="margin-right:10px;" for="'.strtolower($ota).'_price">'. $ota .' Price</label>';
            echo '<input style="margin-right:30px;" id="'.strtolower($ota).'_price" type="number" name="price_field[]" value="'. ($value != '' ? esc_attr($value[$key]) : '') .'">';
        }
    }

    public function cotaFetchData($post)
    {
        wp_nonce_field('saveFetchData', "fetch_field_nonce");

        $value = get_post_meta($post->ID, '_cota_fetch', true);
        $valKey = get_post_meta($post->ID, '_cota_fetch_hotel_key', true);

        echo '<label style="margin-right:10px;" for="hotel_key">Hotel Key</label>';
        echo '<input style="margin-right:30px;" type="text" name="hotel_key" value="'.esc_attr($valKey).'">';
        echo '<label for="fetch_field"><input type="checkbox" value=1 name="fetch_field" '. checked($value, '1', false) .'></label>';
        echo '<span>Fetch Data From API?</span>';
    }

    public function savePriceFields($post_id)
    {
        $arr = [];

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        foreach ($this->otas as $key => $ota) {
            if (!isset($_POST['price_field_nonce'][$key][$ota])) {
                return;
            }
    
            if (!wp_verify_nonce($_POST['price_field_nonce'][$key][$ota], 'savePriceFields')) {
                return;
            }

            if (!isset($_POST['price_field'][$key])) {
                return;
            }

            sanitize_text_field($_POST['price_field'][$key]);  
            $arr[] = $_POST['price_field'][$key];
        }
        // echo '<pre>';
        //     print_r($arr);
        //     echo '</pre>';
        //     die;
        update_post_meta($post_id, '_cota_price', $arr);

        // add_action('save_post', [$this, 'savePriceFields']);
    }

    public function saveFetchData($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!isset($_POST['hotel_key'])) {
            return;
        }
        
        $hotelKey = sanitize_text_field($_POST['hotel_key']);  
        update_post_meta($post_id, '_cota_fetch_hotel_key', $hotelKey);

        if (isset($_POST['fetch_field'])) {
            if (isset($_POST['fetch_field']) && isset($_POST['hotel_key'])) {
                $this->getPrice($post_id);
            }
            update_post_meta($post_id, '_cota_fetch', $_POST['fetch_field']);
        }else{
            delete_post_meta($post_id, '_cota_fetch');
        }
    }

    protected function getPrice($post_id)
    {
        $this->api = new HotelApiPrice();
        $in = date('Y-m-d');
        $tomorrow = strtotime('+1 day');
        $out = date('Y-m-d', $tomorrow);
        
        $key = get_post_meta($post_id, '_cota_fetch_hotel_key', true);
        
        $res = $this->api->callApi($key, $in, $out);

        // echo '<pre>';
        // print_r(json_decode($res, true)['result']);
        // echo '</pre>';
        // die;

        $arr = [];
        $rates = json_decode($res, true)['result']['rates'];
        
        $filter = array_filter($rates, [$this, 'filterOta']);

        usort($filter, function($item1, $item2){
            return $item1['name'] <=> $item2['name'];
        });

        // $null = [0,0,0];
        // echo '<pre>';
        //     print_r($null);
        //     echo '</pre>';
        //     die;

        if (count($filter) != count($this->codeOta)) {

            // $push['code'] = 'BookingCom';
            // $push['name'] = 'Booking.com';
            // $push['rate'] = 'null';
            // $push['tax'] = 'null';
            // array_push($filter, $push);
            // array_unique($filter);
            // echo '<pre>';
            // print_r($filter);
            // echo '</pre>';

            // die;
            $null = [0,0,0];

            // echo '<pre>';
            // print_r($null);
            // echo '</pre>';
            // die;
            update_post_meta($post_id, '_cota_price', $null);
            return;
        }
        
        $countOta = count($this->otas);
        for ($i=0; $i < $countOta; $i++) { 
            foreach ($this->codeOta as $name) {
                // print_r($name);
                // die;
                if ($filter[$i]['code'] == $name) {
                    $arr[] = $filter[$i]['rate'] + $filter[$i]['tax'];
                } 
                // else {
                //     $tt = [22];
                //     print_r($i);
                //     die;
                //     array_splice($arr, $i, 0, $tt);
                // }
            }
        }

        // $null = [0,0,0];
        // echo '<pre>';
        //     print_r($arr);
        //     echo '</pre>';
            // die;

        update_post_meta($post_id, '_cota_price', $arr);
        return;
    }

    public function filterOta($item)
    {
        return $item['name'] == 'Agoda.com' || $item['name'] == 'Booking.com' || $item['name'] == 'Hotels.com';
    }
}