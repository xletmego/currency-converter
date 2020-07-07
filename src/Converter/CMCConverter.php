<?php


namespace CurrencyConverter;

class CMCConverter implements Converter{

    public function __construct(){

    }

    public function calc(Currency $from, Currency $to, $amount = 0){
        if($to->usd_rate === 0){
            return 0;
        }

        return ($from->usd_rate * $amount) / $to->usd_rate;
    }

}