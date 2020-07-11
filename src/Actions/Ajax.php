<?php

namespace CurrencyConverter;

class Ajax implements Action{

    private $converter;
    private $operation;
    private $currency;

    public function __construct(Converter $converter, Operation $operation, Currency $currency){
        $this->converter = $converter;
        $this->operation = $operation;
        $this->currency = $currency;
    }

    public function proceed(){
        add_action('wp_ajax_nopriv_convert', array($this, 'convert'));
        add_action('wp_ajax_convert', array($this, 'convert'));

        add_action('wp_ajax_nopriv_loadLastOperations', array($this, 'loadLastOperations'));
        add_action('wp_ajax_loadLastOperations', array($this, 'loadLastOperations'));
    }

    public function convert(){

        $from_id = 0;
        if(!empty($_REQUEST['from_id']) && is_numeric($_REQUEST['from_id']) === true){
            $from_id = intval($_REQUEST['from_id']);
        }

        $to_id = 0;
        if(!empty($_REQUEST['to_id']) && is_numeric($_REQUEST['to_id']) === true){
            $to_id = intval($_REQUEST['to_id']);
        }

        $amount = 0;
        if(!empty($_REQUEST['amount']) && is_numeric($_REQUEST['amount'])){
            $amount = abs(floatval($_REQUEST['amount']));
        }

        $curr_from = $this->currency->retrieve($from_id);
        $curr_to = $this->currency->retrieve($to_id);

        $curr_to->amount = $this->converter->calc($curr_from, $curr_to, $amount);

        $this->operation->save($curr_from, $curr_to, $amount);

        wp_send_json_success(array('converted_amount' => $this->format_amount($curr_to->amount)));

    }

    public function loadLastOperations(){
        $operations = $this->operation->getLastOperations(HISTORY_SHOW_LIMIT);
        foreach ($operations as $key => $val){
            $operations[$key]['from']->amount = $this->format_amount($val['from']->amount);
            $operations[$key]['to']->amount = $this->format_amount($val['to']->amount);
        }
        wp_send_json_success($operations);
    }

    private function format_amount($num, $decimals = 7){
        if($num == intval($num)){
            return intval($num);
        }
        return number_format($num, $decimals, '.', '');
    }
}
