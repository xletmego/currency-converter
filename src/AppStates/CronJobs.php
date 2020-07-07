<?php

namespace CurrencyConverter;


class CronJobs implements AppState{

    private $remoteService;
    private $storage;

    public function __construct(RemoteService $remoteService, Storage $storage){
        $this->remoteService = $remoteService;
        $this->storage = $storage;
    }

    public function proceed(){
        return $this->updateCurrencies();
    }

    public function updateCurrencies(){
        $currencies = $this->remoteService->fetchAll();
        if($this->remoteService->hasError() === true){
            return false;
        }

        foreach ($currencies as $currency){
            $this->storage->setCurrency($currency);
        }

        return true;
    }

}
