<?php
function mt_add_pages() {
    add_options_page('Spreadsheet Cloud API Options', 'Spreadsheet Cloud API Options', 8, 'spreadsheetcloudapioptions', 'mt_options_page');
}

function mt_options_page() {
    $apiKey = 'API_Key';
    $hidden_field_name = 'mt_submit_hidden';
    $data_field_name = 'API_Key';
    $opt_val = get_option( $apiKey );
    //echo '<pre>'.print_r($_POST,1).'</pre>';
    if( $_POST[ $hidden_field_name ] == 'Y' ) {
        $opt_val = $_POST[ $data_field_name ];
        update_option( $apiKey, $opt_val );
        //update_option( 'userFilesList', SpreadsheetCloudAPIActions::GetFileList(1));
        show_header_message('Options saved.');
    }
    if (!empty($_POST['my_file_delete']) && wp_verify_nonce($_POST['filedel_nonce'], 'my_file_delete')) {
        $filename = $_POST['filename'];
        $params = array(Parameters::FileName => $filename);
        $filedeleted = SpreadsheetRequest::deletefile($params);
        if($filedeleted['status'] == 200){
            show_header_message('File <i>'.$filename.'</i> is deleted.');
            update_option( 'userFilesList', SpreadsheetCloudAPIActions::GetFileList(1));
        }
         else {
                show_header_message($filedeleted['data']);
        }
    }
    elseif (wp_verify_nonce($_POST['fileup_nonce'], 'my_file_upload')) {
        if (!function_exists('wp_handle_upload'))
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            $file = &$_FILES['my_file_upload'];
            $uploadresponse = SpreadsheetRequest::uploadFile($file);
            if($uploadresponse['status'] == 200){
                show_header_message('File <i>'.$file['name'].'</i> is uploaded.');
            }
            else {
                show_header_message($uploadresponse['data']);
            }
            update_option( 'userFilesList', SpreadsheetCloudAPIActions::GetFileList(1));
    }
    $optionsheader = __( 'Spreadsheet Cloud API Plugin Options', 'mt_trans_domain' );
    $optionsaction = str_replace( '%7E', '~', $_SERVER['REQUEST_URI']);
    $optionsapikey = __("API Key:", 'API_Key' );
    $optionsupdate = __('Update Options', 'mt_trans_domain' );
    $optionsnoncefield = wp_nonce_field('my_file_upload', 'fileup_nonce');
    $optionsnoncedelete = wp_nonce_field('my_file_delete', 'filedel_nonce');
    $servicefilelist = SpreadsheetCloudAPIActions::GetFileList(3);
    include (SPREADSHEEETCLOUDAPI__PLUGIN_DIR.'\options\Options.html');
}
function show_header_message($message){
    echo '<div class="updated"><p><strong>';
    _e($message, 'mt_trans_domain' ); 
    echo '</strong></p></div>';
}

?>
