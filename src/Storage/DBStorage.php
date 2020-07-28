<?php

namespace CurrencyConverter;

class DBStorage implements Storage {

    const CURRENCIES_STORAGE_NAME = 'currencies';
    const OPERATIONS_STORAGE_NAME = 'currencies_operations';
    const CURRENT_VERSION = '0.01';

    public function __construct(){

    }

    private function rowToCurrency($row, $prefix = ''){
        $currency = new CMCCurrency($this);
        foreach ($row as $key => $val){
            $var_name = str_replace($prefix, '', $key);
            $currency->$var_name = $val;
        }
        return $currency;
    }

    public function getCurrency($id){
        global $wpdb;
        $qry = $wpdb->prepare('select * from `' . self::getCurrenciesTableName() . '` where id = %d', $id);
        $row = $wpdb->get_row($qry);
        return $this->rowToCurrency($row);
    }

    public function getAllCurrencies () {
        global $wpdb;
        return $wpdb->get_results('select * from `' . self::getCurrenciesTableName() . '` order by id');
    }


    public function getOperations($limit){
        global $wpdb;

        $rows = $wpdb->get_results("
            SELECT 
                c_from.id as from_id, c_from.name as from_name, c_from.symbol as from_symbol, c_from.usd_rate as from_usd_rate, 
                c_to.id as to_id, c_to.name as to_name, c_to.symbol as to_symbol, c_to.usd_rate as to_usd_rate, op.amount as from_amount
            FROM `wp_currencies_operations` as op
            LEFT JOIN wp_currencies as c_from on op.id_from = c_from.id
            LEFT JOIN wp_currencies as c_to on op.id_to = c_to.id 
            ORDER BY op.id DESC LIMIT {$limit}
        ");

        $data = array();
        foreach ($rows as $row){
            $from = new CMCCurrency($this);
            $from->id = $row->from_id;
            $from->name = $row->from_name;
            $from->usd_rate = $row->from_usd_rate;
            $from->amount = $row->from_amount;
            $from->symbol = $row->from_symbol;

            $to = new CMCCurrency($this);
            $to->id = $row->to_id;
            $to->name = $row->to_name;
            $to->usd_rate = $row->to_usd_rate;
            $to->symbol = $row->to_symbol;

            $data[] = array(
                'from' => $from,
                'to' => $to
            );

        }
        return $data;

    }

    public function setCurrency(Currency $currency){
        global $wpdb;


        $qry = "INSERT INTO " . self::getCurrenciesTableName() . "
            (id, name, slug, symbol, usd_rate) VALUES (%d, %s, %s,%s, %f) ON DUPLICATE KEY UPDATE usd_rate = %f";

        $qry = $wpdb->prepare(
            $qry,
            $currency->id,
            $currency->name,
            $currency->slug,
            $currency->symbol,
            $currency->usd_rate,
            $currency->usd_rate
        );

        $wpdb->query($qry);

    }
    public function saveOperation($id_from, $id_to, $amount){
        global $wpdb;

        $qry = "INSERT INTO " . self::getOperationsTableName() . "
            (id_from, id_to, amount) VALUES (%d, %d, %f)";

        $qry = $wpdb->prepare(
            $qry,
            $id_from,
            $id_to,
            $amount
        );

        $wpdb->query($qry);
    }

    private static function getCurrenciesTableName(){
        global $wpdb;
        return $wpdb->base_prefix . self::CURRENCIES_STORAGE_NAME;
    }
    private static function getOperationsTableName(){
        global $wpdb;
        return $wpdb->base_prefix . self::OPERATIONS_STORAGE_NAME;
    }



    public function install(){
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $currencies_table = "
            CREATE TABLE IF NOT EXISTS `" . self::getCurrenciesTableName() . "` (
                `id` int(0) NOT NULL,
                `name` varchar(64) NULL DEFAULT '',
                `slug` varchar(16) NULL DEFAULT '',
                `symbol` varchar(8) NULL DEFAULT '',
                `usd_rate` float(255, 12)  NULL DEFAULT 0,
                PRIMARY KEY (`id`));
        ";

        dbDelta($currencies_table);
        update_option(self::getCurrenciesTableName() . '_db_version', self::CURRENT_VERSION);
        $operations_table = "
            CREATE TABLE IF NOT EXISTS `" . self::getOperationsTableName() . "`  (
                `id` int(0) NOT NULL AUTO_INCREMENT,
                `id_from` int(0) NOT NULL,
                `id_to` int(0) NOT NULL,
                `amount` float(255, 12) NULL DEFAULT 0,
                PRIMARY KEY (`id`)
            );
        ";

        dbDelta($operations_table);
        update_option(self::getOperationsTableName() . '_db_version', self::CURRENT_VERSION);
    }

    public function uninstall(){
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS " . self::getCurrenciesTableName());
        $wpdb->query("DROP TABLE IF EXISTS " . self::getOperationsTableName());
    }
}