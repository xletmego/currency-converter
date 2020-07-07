<?php

namespace CurrencyConverter;

class AdminArea implements AppState {

    private $storage;
    private $remoteService;

    public function __construct(Storage $storage, RemoteService $remoteService ){

        $this->storage = $storage;
        $this->remoteService = $remoteService;

    }

    public function proceed(){
        add_action('admin_menu', array($this, 'add_to_settings_menu'));
        add_action('admin_init', array($this, 'register_options'));
    }


    public function add_to_settings_menu() {
        add_options_page(
            'Currency converter settings',
            'Currency converter settings',
            'manage_options',
            'cc_options_page',
            array($this, 'render_settings_page')
        );
    }

    public function render_settings_page(){
        ?>
        <h2>CurrencyConverter</h2>
        <form action="options.php" method="post">
            <?php

            settings_fields( 'cc_options' );
            do_settings_sections( 'cc_options_page' );
            ?>
            <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
        </form>
        <?php
    }

    public function register_options(){
        add_settings_section(
            'cc_options',
            'CoinMarketCap API Settings',
            null,
            'cc_options_page'
        );

        register_setting(
                'cc_options',
                CMC_OPTION_MODE,
                array('type' => 'text')
        );
        add_settings_field(
            CMC_OPTION_MODE,
            'Mode',
            array($this, 'coinmarketcap_options_mode'),
            'cc_options_page',
            'cc_options'
        );

        register_setting('cc_options', CMC_OPTION_API_KEY, array('type' => 'text'));
        add_settings_field(
            CMC_OPTION_API_KEY,
            'API key',
            array($this, 'coinmarketcap_options_key'),
            'cc_options_page',
            'cc_options'
        );
    }

    public function coinmarketcap_options_mode(){
        $current_option = get_option( CMC_OPTION_MODE );
        if(empty($current_option)){
            $current_option = 'sandbox-api';
        }
        $options_html = '';

        foreach (array('sandbox-api' => 'Sandbox','pro'=> 'Live') as $option => $name){
            $selected_html = '';
            if($option === $current_option){
                $selected_html = 'selected="selected"';
            }
            $options_html .= "<option value='{$option}' {$selected_html}>{$name}</option>";
        }
        echo "<select name='" . CMC_OPTION_MODE . "'>{$options_html}</select>";
    }

    public function coinmarketcap_options_key(){
        $value = get_option( CMC_OPTION_API_KEY );
        if(empty($value)){
            $value = '';
        }

        echo "<input name='" . CMC_OPTION_API_KEY ."' type='text' value='{$value}'/>";
    }
}