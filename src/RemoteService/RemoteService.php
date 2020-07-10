<?php

namespace CurrencyConverter;

interface RemoteService {
    public function fetchAll();
    public function convert($from, $to_symbol, $amount);
    public function hasError();
}
