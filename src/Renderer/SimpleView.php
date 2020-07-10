<?php

namespace CurrencyConverter;

class SimpleView implements Renderer {

    private $tplName;
    private $tplDir;
    private $vars = array();


    public function __construct(){
        $this->tplDir = __DIR__ . '/Templates/';
    }

    public function setTemplate($name = ''){
        $this->tplName = $name . 'TPL';
    }

    public function setVars($vars){
        $this->vars = $vars;
    }

    public function display(){
        $filename =  $this->tplDir . $this->tplName. '.php';
        if(file_exists($filename) === true){
            extract($this->vars);
            include_once $filename;
        }
    }

}
