<?php
namespace CurrencyConverter;

class CMCCurrency implements Currency{

    public $id;
    public $name = '';
    public $slug = '';
    public $usd_rate = 0;
    public $amount = 0;


    public static function populateFromArray(array $meta_data = array()){
        $currency = new CMCCurrency();

        if(!empty($meta_data['id']) && is_numeric($meta_data['id'])){
            $currency->id = intval($meta_data['id']);
        }

        if(!empty($meta_data['name'])){
            $currency->name = $meta_data['name'];
        }

        if(!empty($meta_data['slug'])){
            $currency->slug = $meta_data['slug'];
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
