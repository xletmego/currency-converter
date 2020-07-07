<?php

namespace CurrencyConverter;


interface Storage {

    public function getCurrency($id);

    public function setCurrency(Currency $currency);

    public function saveOperation($id_from, $id_to, $amount);

    public function getOperations($limit);

}