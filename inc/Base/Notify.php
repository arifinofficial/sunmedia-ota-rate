<?php

namespace Inc\Base;

class Notify
{
    public function mail($to, $subject, $body, $headers = [])
    {
        if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
            wp_mail($to, $subject, $body, $headers);
        }
        return;
    }
}