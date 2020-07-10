<?php
namespace CurrencyConverter;

class CMCCurrency implements Currency{

    public $id;
    public $name = '';
    public $slug = '';
    public $usd_rate = 0;
    public $amount = 0;
    public $symbol = '';

    private $storage;

    public function __construct(Storage $storage){
        $this->storage = $storage;
    }

    public function retrieve($id = ''){
        return $this->storage->getCurrency($id);
    }

    public function save(){
        $this->storage->setCurrency($this);
    }

    public function reloadFromAPI(RemoteService $remoteService){

        $data = $remoteService->fetchAll();

        if($remoteService->hasError() === true){
            return false;
        }

        foreach ($data as $array){
            $currency = $this->createFromArray($array);
            $currency->save();
        }

        return true;
    }

    private function createFromArray(array $meta_data = array()){
        $currency = new CMCCurrency($this->storage);

        if(!empty($meta_data['id']) && is_numeric($meta_data['id'])){
            $currency->id = intval($meta_data['id']);
        }

        if(!empty($meta_data['name'])){
            $currency->name = $meta_data['name'];
        }

        if(!empty($meta_data['slug'])){
            $currency->slug = $meta_data['slug'];
        }

        if(!empty($meta_data['symbol'])){
            $currency->symbol = $meta_data['symbol'];
        }

        if(
            !empty($meta_data['quote']) && !empty($meta_data['quote']['USD'])
            && !empty($meta_data['quote']['USD']['price']) && is_float($meta_data['quote']['USD']['price'])
        ){
            $currency->usd_rate = $meta_data['quote']['USD']['price'];
        }
        return $currency;
    }

}
