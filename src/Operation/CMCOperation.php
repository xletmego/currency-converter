<?php

namespace CurrencyConverter;

class CMCOperation implements Operation{

    private $storage;
    private $converter;

    public function __construct(Storage $storage, Converter $converter){
        $this->storage = $storage;
        $this->converter = $converter;
    }

    public function getLastOperations($limit){
        $operations = $this->storage->getOperations(HISTORY_SHOW_LIMIT);
        foreach ($operations as $key => $operation) {
            $operations[$key]['to']->amount = $this->converter->calc($operation['from'], $operation['to'], $operation['from']->amount);
        }
        return $operations;

    }

    public function save(Currency $from, Currency $to, $amount){
        $this->storage->saveOperation($from->id, $to->id, $amount);
    }

}