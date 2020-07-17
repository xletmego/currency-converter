<?php

namespace CurrencyConverter;

class SimpleRender implements Renderer {

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
        if(file_exists($filename) !== true){
            //throw e
        }


        ob_start();
        include $filename;
        $template = ob_get_contents();
        ob_end_clean();

        echo $this->replaceVars($template);
    }

    private function replaceVars($template){
        $templateVars = array();
        $templateValues = array();

        foreach ($this->vars as $name => $value){
            $templateVars[] = '{{' . $name . '}}';
            $templateValues[] = $value;
        }


        return str_replace($templateVars, $templateValues, $template);


    }

}
