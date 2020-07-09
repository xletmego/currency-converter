<?php

namespace CurrencyConverter;

class SimpleView implements Renderer {

    private $tplName;
    private $tplDir;


    public function __construct(){
        $this->tplDir = __DIR__ . '/Templates/';
    }

    public function set($name = ''){
        $this->tplName = $name . 'TPL';
    }

    public function display(){
        $filename =  $this->tplDir . $this->tplName. '.php';
        if(file_exists($filename) === true){
            include_once $filename;
        }
    }

}
