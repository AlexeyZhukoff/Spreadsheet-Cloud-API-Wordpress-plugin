<?php
function mt_add_pages() {
    add_options_page('Spreadsheet Cloud API Options', 'Spreadsheet Cloud API Options', 8, 'spreadsheetcloudapioptions', 'mt_options_page');
}

function mt_options_page() {
    //echo "<h2>Spreadsheet Cloud API Options</h2>";

    $apiKey = 'API_Key';
    $hidden_field_name = 'mt_submit_hidden';
    $data_field_name = 'API_Key';

    $opt_val = get_option( $apiKey );

    if( $_POST[ $hidden_field_name ] == 'Y' ) {
        $opt_val = $_POST[ $data_field_name ];
        update_option( $apiKey, $opt_val );
?>
<div class="updated"><p><strong><?php _e('Options saved.', 'mt_trans_domain' ); ?></strong></p></div>
<?php
    }
    echo '<div class="wrap">';
    echo "<h2>" . __( 'Spreadsheet Cloud API Plugin Options', 'mt_trans_domain' ) . "</h2>";
    ?>

<form name="form1" method="post" action="<?= str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="<?= $hidden_field_name; ?>" value="Y">

<p><?php _e("API Key:", 'API_Key' ); ?> 
<input type="text" name="<?= $data_field_name; ?>" value="<?= $opt_val; ?>" size="50">
</p><hr />

<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Update Options', 'mt_trans_domain' ) ?>" />
</p>

</form>
</div>

<?php
 
}
?>
