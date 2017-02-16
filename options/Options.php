<?php
function mt_add_pages() {
    add_options_page( 'SpreadsheetCloudAPI Options', 'SpreadsheetCloudAPI Options', 'manage_options', 'spreadsheetcloudapioptions', 'mt_options_page' );
}

function mt_options_page() {
    $hidden_field_name = 'mt_submit_hidden';
    $apikey_field_name = Plugin_Const::API_KEY;
    $opt_api_key = get_option( Plugin_Const::SCLAPI_OPTIONS )[ Plugin_Const::API_KEY ];
    $opt_create_example =  get_option( Plugin_Const::SCLAPI_OPTIONS )[ Plugin_Const::SHOW_CREATE_EXAMPLE ];
    $fileoperation = $_POST['my_file_operation'];

    //echo '<pre>'.print_r($_POST,1).'</pre>';
    //echo '<pre>'.print_r($_FILES,1).'</pre>';
    //echo '<pre>'.print_r($options,1).'</pre>';

    $options = get_option( Plugin_Const::SCLAPI_OPTIONS );
    if ( $_POST[ $hidden_field_name ] == 'Y' && empty( $fileoperation ) ) {
        $needsaveoption = TRUE;
        if ( $opt_create_example != ( $_POST[ Plugin_Const::SHOW_CREATE_EXAMPLE ]=='on' ) ) {
            $opt_create_example = $_POST[ Plugin_Const::SHOW_CREATE_EXAMPLE ]=='on';
            $options[ Plugin_Const::SHOW_CREATE_EXAMPLE ] = $opt_create_example;
            update_option( Plugin_Const::SCLAPI_OPTIONS, $options ); 
        }
        if ( ! empty( $_POST[ Plugin_Const::GET_NEW_API_KEY ] ) && empty( $_POST[ $apikey_field_name ] ) ) {
            $response = get_newapikey();
            if ( $response[ Plugin_Const::RESPONSE_STATUS ] == 200 ) {
                $_POST[ $apikey_field_name ] = base64_decode( $response[ Plugin_Const::RESPONSE_DATA ] );
            }
            else {
                show_header_message( $response[ Plugin_Const::RESPONSE_DATA ] );
                $needsaveoption = FALSE;
            }
        }
        if ( $needsaveoption ) {
            $opt_api_key = $_POST[ $apikey_field_name ];
            $options[ Plugin_Const::API_KEY ] = $opt_api_key;
            
            if ( ! empty( $opt_api_key ) ) {
                $options[ Plugin_Const::USER_FILE_LIST ] = Spreadsheet_Cloud_API_Actions::get_files_list(1);
            } else {
                $options[ Plugin_Const::USER_FILE_LIST ] = '<select class="filename" name="filename" size="1"></select>';
            } 
            update_option( Plugin_Const::SCLAPI_OPTIONS, $options );
            show_header_message( Header_Messages::OPTIONS_SAVED );
        }
    }
    
    $continueoperation = '';
    $downloadfilebits = '';
    if ( ! empty( $fileoperation ) ) {
        switch ( $fileoperation ) {
            case File_Operations::UPLOAD:
                upload_file();
                break;
            case File_Operations::DELETE:
                delete_file();
                break;
            case File_Operations::RENAME:
                rename_file();
                break;
            case File_Operations::DOWNLOAD:
                $downloadresponse = download_file();
                if ( $downloadresponse[ Plugin_Const::RESPONSE_STATUS ] == 200 ) {
                    $continueoperation = File_Operations::CONTINUE_DOWNLOAD;
                    $downloadfilebits = base64_encode( $downloadresponse[ Plugin_Const::RESPONSE_DATA ] );
                }
                break;
            default:
                break;
        };
    }

    $createexample = '';
    if ( $opt_create_example ) {
        $createexample = 'checked="checked"';
    }
    show_options_form( $hidden_field_name, $apikey_field_name, $opt_api_key, $continueoperation, $downloadfilebits, $_POST['filename'], $createexample );
}
function get_newapikey() {
    $useremail = wp_get_current_user()->user_email;
    return Spreadsheet_Request::generate_new_API_key( $useremail );
}
function rename_file() {
    $filename = $_POST['filename'];
    $newfilename = $_POST['newfilename'];
    if ( empty( $filename ) ) {
        show_header_message( Header_Messages::SELECT_RENAME );
        return;
    }
    $filerenamed = Spreadsheet_Cloud_API_Actions::rename_file( $filename, $newfilename );
    if ( $filerenamed[ Plugin_Const::RESPONSE_STATUS ] == 200 ) {
        show_header_message( sprintf(Header_Messages::FILE_RENAMED, $filename, $newfilename) );
    }
    else {
        show_header_message( $filerenamed[ Plugin_Const::RESPONSE_DATA ] );
    }
}
function download_file() {
    $filename = $_POST['filename'];
    if ( ! empty( $filename ) ) {
        $downloadresponse = Spreadsheet_Cloud_API_Actions::download_file( $filename );
        if ( $downloadresponse[ Plugin_Const::RESPONSE_STATUS ] == 200 ) {
            show_header_message( sprintf(Header_Messages::FILE_DOWNLOADED, $filename) );
            return $downloadresponse;
        }
        else {
            show_header_message( $downloadresponse[ Plugin_Const::RESPONSE_DATA ] );
        }
        update_sclapi_option( Plugin_Const::USER_FILE_LIST, Spreadsheet_Cloud_API_Actions::get_files_list(1) );
    }
    else {
        show_header_message( Header_Messages::SELECT_DOWNLOAD );
    }
}
function upload_file() {
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    $file = &$_FILES['my_file_upload'];
    if ( ! empty( $file['name'] ) ) {
        $uploadresponse = Spreadsheet_Cloud_API_Actions::upload_file( $file );
        if ( $uploadresponse[ Plugin_Const::RESPONSE_STATUS ] == 200 ) {
            show_header_message( sprintf( Header_Messages::FILE_UPLOADED, $file['name'] ) );
        }
        else {
            show_header_message( $uploadresponse[ Plugin_Const::RESPONSE_DATA ] );
        }
        update_sclapi_option( Plugin_Const::USER_FILE_LIST, Spreadsheet_Cloud_API_Actions::get_files_list(1) );
    }
    else {
        show_header_message( Header_Messages::SELECT_UPLOAD );
    }
}
function delete_file() {
    $filename = $_POST['filename'];
    if ( ! empty( $filename ) ) {
        $filedeleted = Spreadsheet_Cloud_API_Actions::delete_file( $filename );
        if ( $filedeleted[ Plugin_Const::RESPONSE_STATUS ] == 200 ) {
            show_header_message( sprintf( Header_Messages::FILE_DELETED, $filename ) );
            update_sclapi_option( Plugin_Const::USER_FILE_LIST, Spreadsheet_Cloud_API_Actions::get_files_list(1) );
        }
        else {
            show_header_message( $filedeleted[ Plugin_Const::RESPONSE_DATA ] );
        }
    }
    else {
        show_header_message( Header_Messages::SELECT_DELETE );
    }
}

function update_sclapi_option( $optionkey, $optionvalue ) {
    $options = get_option( Plugin_Const::SCLAPI_OPTIONS );
    $options[ $optionkey ] = $optionvalue;
    update_option( Plugin_Const::SCLAPI_OPTIONS, $options );
}

function show_header_message( $message ) {
    echo '<div class="updated"><p><strong>';
    _e( $message, 'mt_trans_domain' ); 
    echo '</strong></p></div>';
}
function show_options_form( $hidden_field_name, $apikey_field_name, $opt_api_key, $continueoperation, $downloadfile, $filename, $createexample ) {
    $haveapikey = '';
    $unhaveapikey = 'style="display: none"';
    if ( empty( $opt_api_key ) ) {
        $haveapikey = 'style="display: none"';
        $unhaveapikey = '';
    }
    $optionsheader = __( 'SpreadsheetCloudAPI Plugin Options', 'mt_trans_domain' );
    $optionsaction = str_replace( '%7E', '~', $_SERVER['REQUEST_URI'] );
    $optionsapikey = __( "API Key:", Plugin_Const::API_KEY );
    $optionsupdate = __( 'Update', 'mt_trans_domain' );
    $servicefilelist = Spreadsheet_Cloud_API_Actions::get_files_list(3);
    include ( SPREADSHEEETCLOUDAPI__PLUGIN_DIR.'\options\options.html' );
}
?>