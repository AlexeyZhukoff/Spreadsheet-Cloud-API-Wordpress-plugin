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
    //echo '<pre>'.print_r($_FILES,1).'</pre>';

    if( $_POST[ $hidden_field_name ] == 'Y' ) {
        $opt_val = $_POST[ $data_field_name ];
        update_option( $apiKey, $opt_val );
        update_option( 'userFilesList', SpreadsheetCloudAPIActions::GetFileList(1));
        show_header_message('Options saved.');
    }

    $fileoperation = $_POST['my_file_operation'];
    if(!empty($fileoperation)){
        switch ( $fileoperation ){
            case FileOperations::Upload:
                upload_file();
                break;
            case FileOperations::Delete:
                delete_file();
                break;
            case FileOperations::Rename:
                rename_file();
                break;
            case FileOperations::Download:
                download_file();
                break;
            default:
                break;
        };
    }
    show_options_form($hidden_field_name, $data_field_name, $opt_val);
}
function rename_file(){
    echo 'rename';
}
function download_file(){
    $filename = $_POST['filename'];
    if(!empty($filename)){
        $downloadresponse = SpreadsheetCloudAPIActions::DownloadFile($filename);
        if($downloadresponse['status'] == 200){
            include (SPREADSHEEETCLOUDAPI__PLUGIN_DIR.'\options\download.html');
            
            show_header_message('File <i>'.$filename.'</i> is downloaded.');
        }
        else {
            show_header_message($downloadresponse['data']);
        }
        update_option( 'userFilesList', SpreadsheetCloudAPIActions::GetFileList(1));
    }
    else {
        show_header_message('Please select file to download.');
    }
}
function upload_file(){
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    $file = &$_FILES['my_file_upload'];
    if(!empty($file['name'])){
        $uploadresponse = SpreadsheetCloudAPIActions::UploadFile($file);
        if($uploadresponse['status'] == 200){
            show_header_message('File <i>'.$file['name'].'</i> is uploaded.');
        }
        else {
            show_header_message($uploadresponse['data']);
        }
        update_option( 'userFilesList', SpreadsheetCloudAPIActions::GetFileList(1));
    }
    else {
        show_header_message('Please select file to upload.');
    }
}
function delete_file(){
    $filename = $_POST['filename'];
    if(!empty($filename)){
        $filedeleted = SpreadsheetCloudAPIActions::DeleteFile($filename);
        if($filedeleted['status'] == 200){
            show_header_message('File <i>'.$filename.'</i> is deleted.');
            update_option( 'userFilesList', SpreadsheetCloudAPIActions::GetFileList(1));
        }
        else {
            show_header_message($filedeleted['data']);
        }
    }
    else {
        show_header_message('Please select name of file to delete.');
    }
}
function show_header_message($message){
    echo '<div class="updated"><p><strong>';
    _e($message, 'mt_trans_domain' ); 
    echo '</strong></p></div>';
}
function show_options_form($hidden_field_name, $data_field_name, $opt_val){
    $optionsheader = __( 'Spreadsheet Cloud API Plugin Options', 'mt_trans_domain' );
    $optionsaction = str_replace( '%7E', '~', $_SERVER['REQUEST_URI']);
    $optionsapikey = __("API Key:", 'API_Key' );
    $optionsupdate = __('Update Options', 'mt_trans_domain' );
    $servicefilelist = SpreadsheetCloudAPIActions::GetFileList(3);
    include (SPREADSHEEETCLOUDAPI__PLUGIN_DIR.'\options\Options.html');
}
?>
