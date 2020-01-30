<?php

namespace Inc\Api\Currency;

class Currency
{
    protected $apiKey = '6d30ae7bd9faebbc25ac';
    protected $fromCurrency = 'USD';
    protected $toCurrency = 'IDR';

    public function getCurrency()
    {
        $fromCurrency = urlencode($this->fromCurrency);
        $toCurrency = urlencode($this->toCurrency);
        $query =  "{$fromCurrency}_{$toCurrency}";

        $json = file_get_contents("https://free.currconv.com/api/v7/convert?q={$query}&compact=ultra&apiKey={$this->apiKey}");
        $obj = json_decode($json, true);

        $val = floatval($obj["$query"]);

        return $val;
    }
}