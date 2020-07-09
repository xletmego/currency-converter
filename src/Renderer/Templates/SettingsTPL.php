
<h2>CurrencyConverter</h2>
<form action="options.php" method="post">
    <?php
//    settings_errors('cc_options');
    settings_fields( 'cc_options' );
    do_settings_sections( 'cc_options_page' );
    ?>
    <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
</form>

<?php
