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
        $this->renderer = new SimpleView();

        $this->converter = new CMCConverter($this->remoteService);
        $this->currency = new CMCCurrency($this->storage);
        $this->operation = new CMCOperation($this->storage, $this->converter);

    }

    public function install(){
        DBStorage::install();

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
        DBStorage::uninstall();
        wp_clear_scheduled_hook(array($this, 'init'));
    }

    public function init(){

        if( wp_doing_cron() === true) {

            $appState = new CronJobs($this->remoteService, $this->storage, $this->currency);

        } else if( wp_doing_ajax() === true ) {

            $appState = new Ajax($this->converter, $this->operation, $this->currency);

        } else if(is_admin() === true){


            $appState = new AdminArea($this->storage, $this->remoteService, $this->renderer, $this->currency);

        } else {

            $appState = new PageArea($this->storage, $this->converter, $this->operation, $this->renderer);

        }

        $appState->proceed();

    }

}