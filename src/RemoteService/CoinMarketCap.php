<?php

namespace CurrencyConverter;

class CoinMarketCap implements RemoteService {

    private $api_url = 'sandbox-api';
    private $api_key = '';

    private $error = false;
    private $errorMessage = '';

    const SERVICE_NAME = 'CoinMarketCap api';

    public function getServiceName(){
        return self::SERVICE_NAME;
    }

    public function fetchAll(){
       $url = $this->api_url . 'cryptocurrency/listings/latest';

        $params = array(
            'start' => '1',
            'limit' => CMC_API_RECORDS_LIMIT,
            'convert' => 'USD',
        );

        return $this->sendRequest($url, $params);
    }

    public function convert($from, $to_symbol, $amount){
        $url = $this->api_url . 'tools/price-conversion';
        $params = array(
            'id' => $from,
            'amount' => $amount,
            'convert' => $to_symbol,
        );
        $data = $this->sendRequest($url, $params);
        $price = 0;

        if($this->hasError() === false
            && (!empty($data["quote"]) && !empty($data["quote"][$to_symbol]) && !empty($data["quote"][$to_symbol]['price']))){
            $price = $data["quote"][$to_symbol]['price'];
        }
        return $price;
    }

    public function ping(){
        $url = $this->api_url . 'key/info';
        return $this->sendRequest($url, array());
    }

    public function setMode($mode){
        $this->api_url = 'https://' . $mode . '.coinmarketcap.com/v1/';
    }

    public function setApiKey($key){
        $this->api_key = $key;
    }

    public function hasError(){
        return $this->error;
    }

    public function getLastErrorMessage(){
        return $this->errorMessage;
    }

    private function sendRequest($url, $params){

        $headers = array(
            'Accepts' => 'application/json',
            'X-CMC_PRO_API_KEY' => $this->api_key
        );
        $qs = http_build_query($params);
        $request = "{$url}?{$qs}";

        $wpGetData = wp_remote_get( $request ,
            array(
                'timeout' => CMC_REQUEST_TIMEOUT,
                'headers' => $headers
            ));

        if($wpGetData instanceof \WP_Error){
            $errors = implode(',', $wpGetData->get_error_messages());
            $this->riseError($errors);
            return array();
        }

        $code = $this->getServiceCode($wpGetData);
        $body = $this->getServiceAnswer($wpGetData);
        $message = $this->getWPMessage($wpGetData);
        $statusMessage = $this->getServiceStatus($body);

        if(!empty($statusMessage)){
            $message = $statusMessage;
        }

        if($code !== 200){
            $this->riseError("CoinMarketCap Error: {$message}");
        }

        return $body;
    }

    private function getServiceAnswer($wpGetData){
        $body = @json_decode($wpGetData['body'], true);

        if(empty($body) && json_last_error() !== JSON_ERROR_NONE){
            return array();
        }

        return $body;
    }

    private function getServiceCode($wpGetData){
        return $wpGetData["response"]["code"];
    }

    private function getWPMessage($wpGetData){
        return $wpGetData["response"]["message"];
    }

    private function getServiceStatus($body){
        $status = '';
        if(!empty($body) && !empty($body['status']) && !empty($body['status']['error_message'])){
            $status = $body['status']['error_message'];
        }
        return $status;
    }

    private function riseError($msg = ''){
        $this->error = true;
        $this->setErrorMessage($msg);
    }

    private function setErrorMessage($msg){
        $this->errorMessage = $msg;
    }
}
