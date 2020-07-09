<?php

namespace CurrencyConverter;

class AdminArea implements AppState {

    private $storage;
    private $remoteService;
    private $renderer;
    private $currency;

    public function __construct(Storage $storage, RemoteService $remoteService, SimpleView $renderer, Currency $currency){

        $this->storage = $storage;
        $this->remoteService = $remoteService;
        $this->renderer = $renderer;
        $this->currency = $currency;
    }

    public function proceed(){
        $this->renderer->set('Settings');

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
        });

    }

    public function checkOptions(){

        $mode = '';
        if(!empty($_REQUEST[CMC_OPTION_MODE]) && ($_REQUEST[CMC_OPTION_MODE] == 'sandbox-api' || $_REQUEST[CMC_OPTION_MODE] == 'pro')){
            $mode = $_REQUEST[CMC_OPTION_MODE];
        }

        $key = '';
        if(!empty($_REQUEST[CMC_OPTION_API_KEY])){
            //add and is_guid
            $key = $_REQUEST[CMC_OPTION_API_KEY];
        }

        $this->remoteService->setMode($mode);
        $this->remoteService->setApiKey($key);

        if( $this->currency->reloadFromAPI($this->remoteService) === false ){
            add_settings_error('cc_options', 'cc', 'Can`t establish connection with coinmarketcap.com', 'error');
        }
    }

    public function coinmarketcap_options_mode(){
        $current_option = get_option( CMC_OPTION_MODE );
        if(empty($current_option)){
            $current_option = 'sandbox-api';
        }
        $options_html = '';

        foreach (array('sandbox-api' => 'Sandbox','pro'=> 'Live') as $option => $name){
            $selected_html = '';
            if($option === $current_option){
                $selected_html = 'selected="selected"';
            }
            $options_html .= "<option value='{$option}' {$selected_html}>{$name}</option>";
        }
        echo "<select name='" . CMC_OPTION_MODE . "'>{$options_html}</select>";
    }

    public function coinmarketcap_options_key(){
        $value = get_option( CMC_OPTION_API_KEY );
        if(empty($value)){
            $value = '';
        }

        echo "<input name='" . CMC_OPTION_API_KEY ."' type='text' value='{$value}'/>";
    }
}