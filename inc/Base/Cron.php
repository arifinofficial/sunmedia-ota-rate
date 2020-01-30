<?php

namespace Inc\Base;

use Inc\Api\HotelApi\HotelApiPrice;

class Cron extends BaseController
{
    protected $codeOta = ['Agoda', 'BookingCom', 'HotelsCom2'];

    public function register()
    {
        $getStatus = get_option('_cota_cron_price');

        if ($getStatus == '1') {
            if( !wp_next_scheduled( 'event_cota_price' ) ) {  
                wp_schedule_event( time(), 'daily', 'event_cota_price' );  
            }
            add_action('event_cota_price', [$this, 'updateEventPrice']);  
        } else {
            if( wp_next_scheduled( 'event_cota_price' ) ) {  
                $timestamp = wp_next_scheduled( 'event_cota_price' );
                wp_unschedule_event( $timestamp, 'event_cota_price' ); 
            }
        }
    }

    public function updateEventPrice()
    {
        $args = ['post_type' => 'sm_cotarate'];

        $api = new HotelApiPrice();
        $in = date('Y-m-d');
        $tomorrow = strtotime('+1 day');
        $out = date('Y-m-d', $tomorrow);

        $the_query = new \WP_Query($args);

        //loop from db
        while ($the_query->have_posts()) : $the_query->next_post();
        $id= $the_query->post->ID;
    
        $key = get_post_meta($id, '_cota_fetch_hotel_key', true);

        $res = $api->callApi($key, $in, $out);

        $arr = [];

        $rates = json_decode($res, true)['result']['rates'];
        $filter = array_filter($rates, [$this, 'filterOta']);
        usort($filter, function($item1, $item2){
            return $item1['name'] <=> $item2['name'];
        });

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
                    
                }else{
                    $push['code'] = $value;
                    $push['name'] = $this->otas[$i];
                    $push['rate'] = 0;
                    $push['tax'] = 0;

                    array_push($filter, $push);
                    usort($filter, function($a, $b){
                        return $a['code'] <=> $b['code'];
                    });
                    array_pop($filter);
                }

                $i++;
            }

            $arr2 = [];
            foreach ($filter as $cost) {
                $arr2[] = $cost['rate'] + $cost['tax'];
            }

            // notify if property price higher than ota price
            $this->notify = new Notify();
            $this->notify->mail($this->to, $this->subject, $this->body);

            // echo '<pre>';
            // print_r('IF </br> </br>');
            // print_r($arr2);
            // echo '</pre>';
            // die;

            update_post_meta($id, '_cota_price', $arr2);
            // return;
        } else {
            $priceProperty = get_post_meta($id, '_cota_set_property_price', true);
            $countOta = count($this->otas);
            for ($i=0; $i < $countOta; $i++) { 
                
                foreach ($this->codeOta as $name) {
                    
                    if ($filter[$i]['code'] == $name) {
                        // notify if property price higher than ota price
                        if ($priceProperty > $filter[$i]['rate'] + $filter[$i]['tax']) {
                            $this->notify = new Notify();
                            $this->notify->mail($this->to, $this->subject, $this->body);
                        }
                        $arr[] = $filter[$i]['rate'] + $filter[$i]['tax'];
                    } 
                }
            }
    
            // echo '<pre>';
            // print_r('ELSE </br> </br>');
            // print_r($arr);
            // echo '</pre>';
            // die;
            update_post_meta($id, '_cota_price', $arr);
            // return;
        }
        
        endwhile;
    }

    public function filterOta($item)
    {
        return $item['name'] == 'Agoda.com' || $item['name'] == 'Booking.com' || $item['name'] == 'Hotels.com';
    }
}
