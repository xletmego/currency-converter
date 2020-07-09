<?php

namespace CurrencyConverter;

class CoinMarketCap implements RemoteService {

    private $api_url = 'sandbox-api';
    private $api_key = '';

    private $error = false;

    const SERVICE_NAME = 'CoinMarketCap api';

    public function getServiceName(){
        return self::SERVICE_NAME;
    }

    public function fetchAll(){
       $url = $this->api_url . 'cryptocurrency/listings/latest';

        $params = array(
            'start' => '1',
            'limit' => '5000',
            'convert' => 'USD',
            'aux' => 'num_market_pairs,cmc_rank,date_added,tags,platform,max_supply,circulating_supply,total_supply,platform,num_market_pairs,date_added,tags,max_supply,circulating_supply,total_supply,cmc_rank'
        );

        return $this->sendRequest($url, $params);
    }

    public function onlineConvert($id_from, $id_to, $amount){

    }

    private function sendRequest($url, $params){

        $headers = [
            'Accepts: application/json',
            'X-CMC_PRO_API_KEY: ' . $this->api_key
        ];
        $qs = http_build_query($params);
        $request = "{$url}?{$qs}";


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $request,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => 1
        ));

        $response = curl_exec($curl);
        $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if($responseCode !== 200){
            $this->riseError();
            return array();
        }

        $responseArray = @json_decode($response, true);

        if(empty($responseArray) && json_last_error() !== JSON_ERROR_NONE){
            $this->riseError();
            return array();
        }

        if(!empty($responseArray['status']) && empty($responseArray['data'])){
            $this->riseError();
            return array();
        }

        if(!empty($responseArray['data'])){
            return $responseArray['data'];
        }

        return array();
    }

    public function setMode($mode){
        $this->api_url = 'https://' . $mode . '.coinmarketcap.com/v1/';
    }

    public function setApiKey($key){
        $this->api_key = $key;
    }

    private function riseError(){
        $this->error = true;
    }

    public function hasError(){
        return $this->error;
    }
}
