<?php

namespace Inc\Base;

class Deactivate 
{
    public static function deactivate()
    {
        if( wp_next_scheduled( 'event_cota_price' ) ) {  
            $timestamp = wp_next_scheduled( 'event_cota_price' );
            wp_unschedule_event( $timestamp, 'event_cota_price' );
        }
    }
}