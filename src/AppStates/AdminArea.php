<?php

namespace CurrencyConverter;

class AdminArea implements AppState {

    private $storage;
    private $remoteService;
    private $renderer;
    private $currency;
    private $modes = array('sandbox-api' => 'Use sandbox mode','pro'=> 'Live mode');
    private $convertTypes = array('online' => '(Online) Use CoinMarketCapAPI', 'offline' => '(Offline) Use preloaded currency rates');

    public function __construct(Storage $storage, RemoteService $remoteService, SimpleView $renderer, Currency $currency){

        $this->storage = $storage;
        $this->remoteService = $remoteService;
        $this->renderer = $renderer;
        $this->currency = $currency;
    }

    public function proceed(){
        $this->renderer->setTemplate('Settings');

        add_action('admin_menu', function (){
            add_options_page(
                'Currency converter settings',
                'Currency converter settings',
                'manage_options',
                'cc_options_page',
                array($this->renderer, 'display')
            );
        });


        add_action('admin_init', function (){

            add_settings_section(
                'cc_options',
                'CoinMarketCap API Settings',
                null,
                'cc_options_page'
            );

            register_setting(
                'cc_options',
                CMC_OPTION_MODE,
                array('type' => 'text', 'sanitize_callback'=> array($this, 'checkOptions'))
            );
            add_settings_field(
                CMC_OPTION_MODE,
                'Mode',
                array($this, 'coinmarketcap_options_mode'),
                'cc_options_page',
                'cc_options'
            );

            register_setting('cc_options', CMC_OPTION_API_KEY, array('type' => 'text'));
            add_settings_field(
                CMC_OPTION_API_KEY,
                'API key',
                array($this, 'coinmarketcap_options_key'),
                'cc_options_page',
                'cc_options'
            );

            register_setting('cc_options', CMC_CONVERT_TYPE, array('type' => 'text'));
            add_settings_field(
                CMC_CONVERT_TYPE,
                'Conversion type',
                function (){
                    $convert_type = get_option( CMC_CONVERT_TYPE );
                    if(empty($convert_type)){
                        $convert_type = 'offline';
                    }

                    $optionsHTML = $this->createSelectOptionsHTML($this->convertTypes, $convert_type);

                    echo "<select name='" . CMC_CONVERT_TYPE . "' class='regular-text'>{$optionsHTML}</select>";
                },
                'cc_options_page',
                'cc_options'
            );

        });

    }

    public function checkOptions(){

        $mode = '';
        if(!empty($_REQUEST[CMC_OPTION_MODE]) && array_key_exists($_REQUEST[CMC_OPTION_MODE], $this->modes) === true){
            $mode = $_REQUEST[CMC_OPTION_MODE];
        }

        $key = '';
        if(!empty($_REQUEST[CMC_OPTION_API_KEY]) && $this->isGUID($_REQUEST[CMC_OPTION_API_KEY])){
            $key = $_REQUEST[CMC_OPTION_API_KEY];
        }

        $this->remoteService->setMode($mode);
        $this->remoteService->setApiKey($key);

        if( $this->currency->reloadFromAPI($this->remoteService) === false ){
            add_settings_error('cc_options', 'cc', 'Can`t establish connection with coinmarketcap.com', 'error');
            return '';
        }
        return $mode;
    }

    public function coinmarketcap_options_mode(){
        $current_option = get_option( CMC_OPTION_MODE );
        if(empty($current_option)){
            $current_option = 'sandbox-api';
        }

        $optionsHTML = $this->createSelectOptionsHTML($this->modes, $current_option);

        echo "<select name='" . CMC_OPTION_MODE . "' class='regular-text'>{$optionsHTML}</select>";
    }

    public function coinmarketcap_options_key(){
        $value = get_option( CMC_OPTION_API_KEY );
        if(empty($value)){
            $value = '';
        }

        echo "<input name='" . CMC_OPTION_API_KEY ."' type='text' placeholder='xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx' value='{$value}' class='regular-text'/>";
    }

    private function createSelectOptionsHTML(array $array, $selectedKey = ''){
        $optionsHTML = '';

        foreach ($array as $option => $name){
            $selectedHTML = '';
            if($option === $selectedKey){
                $selectedHTML = 'selected="selected"';
            }
            $optionsHTML .= "<option value='{$option}' {$selectedHTML}>{$name}</option>";
        }
        return $optionsHTML;
    }

    private function isGUID($guid){

        if(strlen($guid) != 36) {
            return false;
        }
        if(preg_match("/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/i", $guid)) {
            return true;
        }
        return true;
    }
}