<?php
function mt_add_pages() {
    add_options_page('Spreadsheet Cloud API Options', 'Spreadsheet Cloud API Options', 8, 'spreadsheetcloudapioptions', 'mt_options_page');
}

function mt_options_page() {
    $apiKey = 'API_Key';
    $hidden_field_name = 'mt_submit_hidden';
    $data_field_name = 'API_Key';

    $opt_val = get_option( $apiKey );

    if( $_POST[ $hidden_field_name ] == 'Y' ) {
        $opt_val = $_POST[ $data_field_name ];
        update_option( $apiKey, $opt_val );
        update_option( 'userFilesList', SpreadsheetCloudAPIActions::GetFileList());
        show_header_message('Options saved.');
    }


    if (wp_verify_nonce($_POST['fileup_nonce'], 'my_file_upload')) {
        if (!function_exists('wp_handle_upload'))
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            $file = &$_FILES['my_file_upload'];
            SpreadsheetRequest::uploadFile($file);
            show_header_message('File Uploaded.');
            update_option( 'userFilesList', SpreadsheetCloudAPIActions::GetFileList());
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

<form class="user_upload_file none" enctype="multipart/form-data" method="post" action="<?= str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field('my_file_upload', 'fileup_nonce'); ?>
<input class="file_input_text" name="my_file_upload" type="file" />
<input class="btn" type="submit" value="Upload" />
</form>

</div>

<?php
}
function show_header_message($message){
    echo '<div class="updated"><p><strong>';
    _e($message, 'mt_trans_domain' ); 
    echo '</strong></p></div>';
}

?>
