<?php

namespace Inc\Api\HotelApi;

class HotelApiPrice
{
    public function callApi($key, $in, $out)
    {
        $url = "https://data.xotelo.com/api/rates?hotel_key=$key&chk_in=$in&chk_out=$out";
        // print_r($url);
        // die;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }
}