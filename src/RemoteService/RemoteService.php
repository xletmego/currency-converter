<?php

namespace CurrencyConverter;

interface RemoteService {
    public function fetchAll();
    public function onlineConvert($id_from, $id_to, $amount);
    public function hasError();
}
