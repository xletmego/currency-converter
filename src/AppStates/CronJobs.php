<?php

namespace CurrencyConverter;


class CronJobs implements AppState{

    private $remoteService;
    private $storage;
    private $currency;

    public function __construct(RemoteService $remoteService, Storage $storage, Currency $currency){
        $this->remoteService = $remoteService;
        $this->storage = $storage;
        $this->currency = $currency;
    }

    public function proceed(){

        $this->remoteService->setMode(get_option(CMC_OPTION_MODE, ''));
        $this->remoteService->setApiKey(get_option(CMC_OPTION_API_KEY, ''));

        $this->currency->reloadFromAPI($this->remoteService);
    }

}
