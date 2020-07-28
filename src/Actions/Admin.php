<?php

namespace CurrencyConverter;

class Admin implements Action {

    private $storage;
    private $remoteService;
    private $renderer;
    private $currency;
    private $modes = array(
        'sandbox-api' => 'Use sandbox mode',
        'pro-api'=> 'Live mode'
    );
    private $convertTypes = array(
        'online' => '(Online) Use CoinMarketCapAPI',
        'offline' => '(Offline) Use preloaded currency rates'
    );

    public function __construct(Storage $storage, RemoteService $remoteService, Renderer $renderer, Currency $currency){

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
                array('type' => 'text', 'sanitize_callback'=> array($this, 'checkConnection'))
            );
            add_settings_field(
                CMC_OPTION_MODE,
                'Mode',
                function (){
                    echo $this->createSelectHTML(CMC_OPTION_MODE,$this->modes, 'sandbox-api');
                },
                'cc_options_page',
                'cc_options'
            );

            register_setting('cc_options', CMC_OPTION_API_KEY, array('type' => 'text'));
            add_settings_field(
                CMC_OPTION_API_KEY,
                'API key',
                function (){
                    echo $this->createInputHTML(CMC_OPTION_API_KEY, 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx');
                },
                'cc_options_page',
                'cc_options'
            );

            register_setting('cc_options', CMC_CONVERT_TYPE, array('type' => 'text'));
            add_settings_field(
                CMC_CONVERT_TYPE,
                'Conversion type',
                function (){
                    echo $this->createSelectHTML(CMC_CONVERT_TYPE, $this->convertTypes, 'offline');
                },
                'cc_options_page',
                'cc_options'
            );

        });

    }

    public function checkConnection(){

        $mode = '';
        if(!empty($_REQUEST[CMC_OPTION_MODE]) && array_key_exists($_REQUEST[CMC_OPTION_MODE], $this->modes) === true){
            $mode = $_REQUEST[CMC_OPTION_MODE];
        }

        $key = '';
        if(!empty($_REQUEST[CMC_OPTION_API_KEY]) && $this->isGUID(trim($_REQUEST[CMC_OPTION_API_KEY]))){
            $key = trim($_REQUEST[CMC_OPTION_API_KEY]);
        }

        $this->remoteService->setMode($mode);
        $this->remoteService->setApiKey($key);

        $this->remoteService->ping();
        if($this->remoteService->hasError()){
            $this->showOptionsError($this->remoteService->getLastErrorMessage());
            return $mode;
        }
        return $mode;
    }

    private function showOptionsError($msg){
        add_settings_error('cc_options', 'cc', $msg, 'error');
    }

    private function createInputHTML($wpOptionName, $placeholder = ''){
        $value = get_option( $wpOptionName );
        if(empty($value)){
            $value = '';
        }
        return "<input name='{$wpOptionName}' type='text' placeholder='{$placeholder}' value='{$value}' class='regular-text'/>";
    }

    private function createSelectHTML($wpOptionName, array $options,  $defaultValue){
        $currentValue = get_option( $wpOptionName );
        if(empty($currentValue)){
            $currentValue = $defaultValue;
        }

        $optionsHTML = $this->createSelectOptionsHTML($options, $currentValue);

        return "<select name='{$wpOptionName}' class='regular-text'>{$optionsHTML}</select>";
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