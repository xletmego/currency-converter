<?php

namespace CurrencyConverter;

class PageArea implements AppState{

    private $storage;
    private $converter;
    private $operation;

    public function __construct(Storage $storage, Converter $converter, $operation){
        $this->storage = $storage;
        $this->converter = $converter;
        $this->operation = $operation;
    }

    public function proceed(){
        add_action( 'wp_body_open', array($this, 'render') );
    }

    public function render(){

        global $page_id;

        $home_page_id = get_option('page_on_front');

        if($home_page_id != $page_id){
            return;
        }

        $this->registerStyles();
        $this->registerJS();
        $this->renderConverterHTML();

    }

    private function registerStyles(){
        wp_enqueue_style( 'skeletonCss',    PLUGIN_URL_PATH. '/assets/css/skeleton.css');
        wp_enqueue_style( 'converterCss',    PLUGIN_URL_PATH. '/assets/css/converter.css');
        wp_enqueue_style('RalewayFont', 'https://fonts.googleapis.com/css2?family=Raleway:ital,wght@1,300&display=swap');
    }

    private function registerJS(){
        wp_enqueue_script('jquery');
        wp_register_script('converterJS', PLUGIN_URL_PATH. '/assets/js/converter.js');
        wp_localize_script('converterJS', 'converterJS',
            array(
                'ajaxurl' => admin_url( 'admin-ajax.php' )
            )
        );

        wp_enqueue_script( 'converterJS' );
    }

    private function createOptionsHTML(){
        $storage = new DBStorage();
        $data = $storage->getAllCurrencies();
        $html = '';
        foreach ($data as $currency){
            $html .= "<option value='{$currency->id}'>{$currency->name}</option>";
        }
        return $html;
    }

    private function renderConverterHTML(){
        $currencyOptions = $this->createOptionsHTML();
        $adminUrl = admin_url( 'admin-ajax.php');

        $html = "
            <div id='currency-converter' ajaxurl='{$adminUrl}'>
                <div class='container'>
                    <h4> Currency converter</h4>
                    <div class='row'>
                        <div class='six columns'>
                            <select name='from' class='u-full-width'>
                                {$currencyOptions}
                            </select>
                        </div>
                        <div class='six columns'>
                            <select name='to' class='u-full-width'>
                                {$currencyOptions}
                            </select>
                        </div>
                    </div>
                    
                    <div class='row'>
                        <div class='twelve columns text-center'>
                            <h6 class='converter-result'></h6>
                        </div>
                       
                    </div>
                    
                    
                    <div class='row'>
                        <div class='six columns'>
                            <input class='u-full-width' name='amount' type='text' value='' placeholder='Enter Amount to Convert'/>
                        </div>
                        <div class='one-third column u-pull-right' >
                            <button id='converter-btn' class='button button-primary u-full-width'>
                                Convert
                            </button>
                        </div>
                    </div>
                    <div id='operations' class='row'>

                    </div>
                </div>
            </div>  
        ";

        echo $html;
    }



}
