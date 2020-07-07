<?php
namespace CurrencyConverter;

interface Converter{
    public function calc(Currency $from, Currency $to, $amount = 0);
}
