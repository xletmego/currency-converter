<?php
namespace CurrencyConverter;


class App {

    private $storage;
    private $remoteService;
    private $converter;
    private $operation;
    private $currency;
    private $renderer;

    public function __construct(){

        $this->storage = new DBStorage();
        $this->remoteService = new CoinMarketCap();
        $this->renderer = new SimpleRender();

        $this->converter = new CMCConverter($this->remoteService);
        $this->currency = new CMCCurrency($this->storage);
        $this->operation = new CMCOperation($this->storage, $this->converter);

    }

    public function init(){

        if( wp_doing_cron() === true) {

            $action = new Cron($this->remoteService, $this->storage, $this->currency);

        } else if( wp_doing_ajax() === true ) {

            $action = new Ajax($this->converter, $this->operation, $this->currency);

        } else if(is_admin() === true){


            $action = new Admin($this->storage, $this->remoteService, $this->renderer, $this->currency);

        } else {

            $action = new Costumer($this->storage, $this->converter, $this->operation, $this->renderer);

        }

        $action->proceed();

    }

    public function install(){
        $this->storage->install();

        $intervals = wp_get_schedules();

        if(array_key_exists(CRON_TASK_INTERVAL_NAME, $intervals) === false) {

            add_filter('cron_schedules', function ($schedules) {
                $schedules[CRON_TASK_INTERVAL_NAME] = array(
                    'interval' => 60 * 5,
                    'display' => '5 min'
                );
                return $schedules;
            });

        }
        wp_schedule_event( time(), CRON_TASK_INTERVAL_NAME, array($this, 'init'));
    }

    public function uninstall(){
        $this->storage->uninstall();
        wp_clear_scheduled_hook(array($this, 'init'));
    }

}