<?php
function mt_add_pages() {
    add_options_page('Spreadsheet Cloud API Options', 'Spreadsheet Cloud API Options', 8, 'spreadsheetcloudapioptions', 'mt_options_page');
}

function mt_options_page() {
    $apiKey = PluginConst::APIKey;
    $hidden_field_name = 'mt_submit_hidden';
    $apikey_field_name = PluginConst::APIKey;
    $opt_api_key = get_option( $apiKey );
    $opt_create_example = get_option( PluginConst::ShowCreateExample );

    //echo '<pre>'.print_r($_POST,1).'</pre>';
    //echo '<pre>'.print_r($_FILES,1).'</pre>';

    if( $_POST[ $hidden_field_name ] == 'Y' ) {
        $needsaveoption = TRUE;
        if($opt_create_example != ($_POST[ PluginConst::ShowCreateExample ]=='on')){
            $opt_create_example = $_POST[ PluginConst::ShowCreateExample ]=='on';
            update_option( PluginConst::ShowCreateExample, $opt_create_example ); 
        }
        if( !empty($_POST[ 'oauthaut' ]) && empty($_POST[ $apikey_field_name ]) ) {
            $response = get_newapikey();
            if($response[PluginConst::ResponseStatus] == 200){
                $_POST[ $apikey_field_name ] = base64_decode($response[PluginConst::ResponseData]);
            }
            else{
                show_header_message($response[PluginConst::ResponseData]);
                $needsaveoption = FALSE;
            }
        }
        if($needsaveoption){
            $opt_api_key = $_POST[ $apikey_field_name ];
            update_option( $apiKey, $opt_api_key );
            update_option( PluginConst::UserFileList, SpreadsheetCloudAPIActions::GetFileList(1));
            show_header_message('Options saved.');
        }
    }

    $fileoperation = $_POST['my_file_operation'];
    $continueoperation = '';
    $downloadfilebits = '';
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
                $downloadresponse = download_file();
                if($downloadresponse[PluginConst::ResponseStatus] == 200){
                    $continueoperation = FileOperations::ContinueDownload;
                    $downloadfilebits = base64_encode($downloadresponse[PluginConst::ResponseData]);
                }
                break;
            default:
                break;
        };
    }

    $createexample = '';
    if ($opt_create_example){
        $createexample = 'checked="checked"';
    }
    show_options_form($hidden_field_name, $apikey_field_name, $opt_api_key, $continueoperation, $downloadfilebits, $_POST['filename'], $createexample);
}
function get_newapikey(){
    $useremail = wp_get_current_user()->user_email;
    return SpreadsheetRequest::GenerateNewAPIKey($useremail);
}
function rename_file(){
    $filename = $_POST['filename'];
    $newfilename = $_POST['newfilename'];
    if(empty($filename)){
        show_header_message('Please select file to rename.');
        return;
    }
    $filerenamed = SpreadsheetCloudAPIActions::RenameFile($filename, $newfilename);
    if($filerenamed[PluginConst::ResponseStatus] == 200){
        show_header_message('File <i>'.$filename.'</i> renamed to <i>'.$newfilename.'</i>.');
    }
    else{
        show_header_message($filerenamed[PluginConst::ResponseData]);
    }
}
function download_file(){
    $filename = $_POST['filename'];
    if(!empty($filename)){
        $downloadresponse = SpreadsheetCloudAPIActions::DownloadFile($filename);
        if($downloadresponse[PluginConst::ResponseStatus] == 200){
            show_header_message('File <i>'.$filename.'</i> is downloaded.');
            return $downloadresponse;
        }
        else {
            show_header_message($downloadresponse[PluginConst::ResponseData]);
        }
        update_option( PluginConst::UserFileList, SpreadsheetCloudAPIActions::GetFileList(1));
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
        if($uploadresponse[PluginConst::ResponseStatus] == 200){
            show_header_message('File <i>'.$file['name'].'</i> is uploaded.');
        }
        else {
            show_header_message($uploadresponse[PluginConst::ResponseData]);
        }
        update_option( PluginConst::UserFileList, SpreadsheetCloudAPIActions::GetFileList(1));
    }
    else {
        show_header_message('Please select file to upload.');
    }
}
function delete_file(){
    $filename = $_POST['filename'];
    if(!empty($filename)){
        $filedeleted = SpreadsheetCloudAPIActions::DeleteFile($filename);
        if($filedeleted[PluginConst::ResponseStatus] == 200){
            show_header_message('File <i>'.$filename.'</i> is deleted.');
            update_option( PluginConst::UserFileList, SpreadsheetCloudAPIActions::GetFileList(1));
        }
        else {
            show_header_message($filedeleted[PluginConst::ResponseData]);
        }
    }
    else {
        show_header_message('Please select file to delete.');
    }
}
function show_header_message($message){
    echo '<div class="updated"><p><strong>';
    _e($message, 'mt_trans_domain' ); 
    echo '</strong></p></div>';
}
function show_options_form($hidden_field_name, $apikey_field_name, $opt_api_key, $continueoperation, $downloadfile, $filename, $createexample){
    $haveapikey = '';
    $unhaveapikey = 'style="display: none"';
    if(empty($opt_api_key)){
        $haveapikey = 'style="display: none"';
        $unhaveapikey = '';
    }
    $optionsheader = __( 'Spreadsheet Cloud API Plugin Options', 'mt_trans_domain' );
    $optionsaction = str_replace( '%7E', '~', $_SERVER['REQUEST_URI']);
    $optionsapikey = __("API Key:", PluginConst::APIKey );
    $optionsupdate = __('Update Options', 'mt_trans_domain' );
    $servicefilelist = SpreadsheetCloudAPIActions::GetFileList(3);
    include (SPREADSHEEETCLOUDAPI__PLUGIN_DIR.'\options\Options.html');
}
?>
