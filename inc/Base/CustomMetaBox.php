<?php

namespace Inc\Base;

use Inc\Api\HotelApi\HotelApiPrice;
use Inc\Base\Notify;
use Inc\Api\Currency\Currency;

class CustomMetaBox extends BaseController
{
    protected $codeOta = ['Agoda', 'BookingCom', 'HotelsCom2'];
    public $api;
    public $notify;
    public $usdToIdr;

    public function register()
    {
        add_action('add_meta_boxes', [$this, 'addMetaBox']);
        add_action('save_post', [$this, 'savePriceFields']);
        add_action('save_post', [$this, 'saveFetchData']);
        add_action('save_post', [$this, 'saveSetProperty']);
        add_action('save_post', [$this, 'savePositionProperty']);
        add_action('save_post', [$this, 'saveIdBookingEngine']);
    }

    public function addMetaBox()
    {
        add_meta_box('cota_meta_box_price_ota', 'OTA Price', [$this, 'cotaPriceCallback'], 'sm_cotarate', 'normal', 'default');
        add_meta_box('cota_meta_box_use_api', 'Fetch Data', [$this, 'cotaFetchData'], 'sm_cotarate', 'normal', 'default');
        add_meta_box('cota_meta_box_set_rate', 'Set Rate Property', [$this, 'cotaSetRateProperty'], 'sm_cotarate', 'normal', 'default');
        add_meta_box('cota_meta_box_position', 'Position Property', [$this, 'cotaSetPositionProperty'], 'sm_cotarate', 'side', 'default');
        add_meta_box('cota_meta_id_booking_engine', 'ID Booking Engine', [$this, 'cotaSetIdBookingEngine'], 'sm_cotarate', 'side', 'default');
    }

    public function cotaPriceCallback($post)
    {
        foreach ($this->otas as $key => $ota) {
            wp_nonce_field('savePriceFields', "price_field_nonce[$key][$ota]");

            $value = get_post_meta($post->ID, '_cota_price', true);

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

    public function cotaSetRateProperty($post)
    {
        wp_nonce_field('saveSetProperty', "set_property_field_nonce");

        $value = get_post_meta($post->ID, '_cota_set_property_price', true);

        echo '<label style="margin-right:10px;" for="set_property">Set Property Price</label>';
        echo '<input type="number" name="set_property" id="set_property" value="'.esc_attr($value).'">';
    }

    public function cotaSetPositionProperty($post)
    {
        wp_nonce_field('savePosition', "set_position_field_nonce");

        $value = get_post_meta($post->ID, '_cota_position_property', true);

        echo '<label for="set_position">Set Position Property</label>';
        echo '<input type="number" style="width:100%" name="set_position" id="set_position" value="'.esc_attr($value).'">';
    }

    public function cotaSetIdBookingEngine($post)
    {
        wp_nonce_field('saveIdBookingEngine', "set_id_booking_engine_field_nonce");

        $value = get_post_meta($post->ID, '_cota_id_booking_engine', true);

        echo '<label for="set_id_booking_engine">Set ID Booking Engine</label>';
        echo '<input type="number" style="width:100%" name="set_id_booking_engine" id="set_id_booking_engine" value="'.esc_attr($value).'">';
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
       
        update_post_meta($post_id, '_cota_price', $arr);
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
        } else {
            delete_post_meta($post_id, '_cota_fetch');
        }
    }

    public function saveSetProperty($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!isset($_POST['set_property'])) {
            return;
        }

        if (!isset($_POST['set_property_field_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['set_property_field_nonce'], 'saveSetProperty')) {
            return;
        }

        $price = sanitize_text_field($_POST['set_property']);
        
        update_post_meta($post_id, '_cota_set_property_price', $price);
    }

    public function savePositionProperty($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!isset($_POST['set_position'])) {
            return;
        }

        if (!isset($_POST['set_position_field_nonce'])) {
            return;
        }

        $position = sanitize_text_field($_POST['set_position']);
        
        update_post_meta($post_id, '_cota_position_property', $position);
    }

    public function saveIdBookingEngine($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!isset($_POST['set_id_booking_engine'])) {
            return;
        }

        if (!isset($_POST['set_id_booking_engine_field_nonce'])) {
            return;
        }

        $id_booking_engine = sanitize_text_field($_POST['set_id_booking_engine']);
        
        update_post_meta($post_id, '_cota_id_booking_engine', $id_booking_engine);
    }

    public function getPrice($post_id)
    {
        // set currency
        $currency = new Currency();
        $this->usdToIdr = $currency->getCurrency();
        $this->api = new HotelApiPrice();
        $in = date('Y-m-d');
        $tomorrow = strtotime('+1 day');
        $out = date('Y-m-d', $tomorrow);
        
        $key = get_post_meta($post_id, '_cota_fetch_hotel_key', true);
        
        $res = $this->api->callApi($key, $in, $out);

        $arr = [];

        $rates = json_decode($res, true)['result']['rates'];

        $filter = array_filter($rates, [$this, 'filterOta']);

        usort($filter, function ($item1, $item2) {
            return $item1['name'] <=> $item2['name'];
        });

        // Checking if data not equal 3
        if (count($filter) != count($this->codeOta)) {
            if (count($filter) == 0) {
                for ($i=0; $i < count($this->codeOta) ; $i++) {
                    $push['code'] = 'zzz';
                    $push['name'] = 'zzz';
                    $push['rate'] = 'zzz';
                    $push['tax'] = 'zzz';
                    array_push($filter, $push);
                }
            }

            if (count($filter) == 1) {
                for ($i=0; $i < 2 ; $i++) {
                    $push['code'] = 'zzz';
                    $push['name'] = 'zzz';
                    $push['rate'] = 'zzz';
                    $push['tax'] = 'zzz';
                    array_push($filter, $push);
                }
            }

            if (count($filter) == 2) {
                $push['code'] = 'zzz';
                $push['name'] = 'zzz';
                $push['rate'] = 'zzz';
                $push['tax'] = 'zzz';
                array_push($filter, $push);
            }

            $i = 0;
            foreach ($this->codeOta as $value) {
                if ($value == $filter[0]['code'] || $value == $filter[1]['code'] || $value == $filter[2]['code']) {
                } else {
                    $push['code'] = $value;
                    $push['name'] = $this->otas[$i];
                    $push['rate'] = 0;
                    $push['tax'] = 0;

                    array_push($filter, $push);
                    usort($filter, function ($a, $b) {
                        return $a['code'] <=> $b['code'];
                    });
                    array_pop($filter);
                }

                $i++;
            }

            $arr2 = [];
            foreach ($filter as $cost) {
                $arr2[] = ($cost['rate'] + $cost['tax']) * $this->usdToIdr;
            }

            // notify if property price higher than ota price
            $this->notify = new Notify();
            $this->notify->mail($this->to, $this->subject, $this->body);

            update_post_meta($post_id, '_cota_price', $arr2);
            return;
        } else {
            $priceProperty = get_post_meta($post_id, '_cota_set_property_price', true);
            $countOta = count($this->otas);
            for ($i=0; $i < $countOta; $i++) {
                foreach ($this->codeOta as $name) {
                    if ($filter[$i]['code'] == $name) {
                        // notify if property price higher than ota price
                        if ($priceProperty > $filter[$i]['rate'] + $filter[$i]['tax']) {
                            $this->notify = new Notify();
                            $this->notify->mail($this->to, $this->subject, $this->body);
                        }
                        
                        $arr[] = ($filter[$i]['rate'] + $filter[$i]['tax']) * $this->usdToIdr;
                    }
                }
            }
    
            update_post_meta($post_id, '_cota_price', $arr);
            return;
        }
    }

    public function filterOta($item)
    {
        return $item['name'] == 'Agoda.com' || $item['name'] == 'Booking.com' || $item['name'] == 'Hotels.com';
    }
}
