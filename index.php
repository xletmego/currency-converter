<?php
/*
Plugin Name: Currency converter
Plugin URI:
Description: Crypto currencies converter
Version: 0.1
Author: xletmego@gmail.com
*/


namespace CurrencyConverter;

define('PLUGIN_NAME', 'CurrencyConverter');
define('PLUGIN_FOLDER', plugin_dir_path( __DIR__ ) . PLUGIN_NAME);
define('PLUGIN_URL_PATH', plugin_dir_url(__FILE__));
define('HISTORY_SHOW_LIMIT', 10);
define('CRON_TASK_INTERVAL_NAME','coinmarketcap_update_interval');
define('CMC_OPTION_MODE','coinmarketcap_mode');
define('CMC_OPTION_API_KEY', 'coinmarketcap_key');


require_once PLUGIN_FOLDER  . '/files.php';

register_activation_hook(__FILE__, function(){
    $app = new App();
    $app->install();
});
register_deactivation_hook(__FILE__, function(){
    $app = new App();
    $app->uninstall();
});


$app = new App();
$app->init();




