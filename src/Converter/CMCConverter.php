<?php


namespace CurrencyConverter;

class CMCConverter implements Converter{

    private $remoteService;

    public function __construct(RemoteService $remoteService){

        $this->remoteService = $remoteService;

    }

    public function calc(Currency $from, Currency $to, $amount = 0){

        if(get_option(CMC_CONVERT_TYPE, '') === 'online'){
            $price = $this->online($from, $to, $amount);
        } else {
            $price = $this->offline($from, $to, $amount);
        }

        return $price;
    }

    private function offline(Currency $from, Currency $to, $amount = 0){
        if($to->usd_rate === 0){
            return 0;
        }

        return ($from->usd_rate * $amount) / $to->usd_rate;
    }

    private function online(Currency $from, Currency $to, $amount = 0){

        $this->remoteService->setMode(get_option(CMC_OPTION_MODE, ''));
        $this->remoteService->setApiKey(get_option(CMC_OPTION_API_KEY, ''));

        $result = $this->remoteService->convert($from->id, $to->symbol, $amount);
        if($this->remoteService->hasError()){
            $result = $this->offline($from, $to, $amount);
        }

        return $result;

    }

}