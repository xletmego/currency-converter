<?php

namespace CurrencyConverter;

class PageArea implements AppState{

    private $storage;
    private $converter;
    private $operation;
    private $renderer;

    public function __construct(Storage $storage, Converter $converter, Operation $operation, SimpleView $renderer){
        $this->storage = $storage;
        $this->converter = $converter;
        $this->operation = $operation;
        $this->renderer = $renderer;
    }

    public function proceed(){

        add_action( 'wp_body_open', function (){
            global $page_id;

            $home_page_id = get_option('page_on_front');

            if($home_page_id != $page_id){
                return;
            }

            $this->render();

        });
    }

    private function render(){

        $this->registerStyles();
        $this->registerJS();

        $this->renderer->setTemplate('Converter');
        $vars = array(
            'currencyOptions' => $this->createOptionsHTML(),
            'adminUrl' => $this->getAdminURL(),
        );
        $this->renderer->setVars($vars);
        $this->renderer->display();
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

        $data = $this->storage->getAllCurrencies();
        $html = '';
        foreach ($data as $currency){
            $html .= "<option value='{$currency->id}'>{$currency->name}</option>";
        }
        return $html;
    }

    private function getAdminURL(){
        return admin_url( 'admin-ajax.php');
    }



}
