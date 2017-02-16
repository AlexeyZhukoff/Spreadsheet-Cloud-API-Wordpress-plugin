<?php
function mt_add_pages() {
    add_options_page( 'SpreadsheetCloudAPI Options', 'SpreadsheetCloudAPI Options', 'manage_options', 'spreadsheetcloudapioptions', 'mt_options_page' );
}

function mt_options_page() {
    $hidden_field_name = 'mt_submit_hidden';
    $API_key_field_name = Plugin_Const::API_KEY;
    $opt_api_key = get_option( Plugin_Const::SCLAPI_OPTIONS )[ Plugin_Const::API_KEY ];
    $opt_create_example =  get_option( Plugin_Const::SCLAPI_OPTIONS )[ Plugin_Const::SHOW_CREATE_EXAMPLE ];
    $file_operation = $_POST['my-file-operation'];

    //echo '<pre>'.print_r($_POST,1).'</pre>';
    //echo '<pre>'.print_r($_FILES,1).'</pre>';
    //echo '<pre>'.print_r($options,1).'</pre>';

    $options = get_option( Plugin_Const::SCLAPI_OPTIONS );
    if ( $_POST[ $hidden_field_name ] == 'Y' && empty( $file_operation ) ) {
        $need_save_option = TRUE;
        if ( $opt_create_example != ( $_POST[ Plugin_Const::SHOW_CREATE_EXAMPLE ]=='on' ) ) {
            $opt_create_example = $_POST[ Plugin_Const::SHOW_CREATE_EXAMPLE ]=='on';
            $options[ Plugin_Const::SHOW_CREATE_EXAMPLE ] = $opt_create_example;
            update_option( Plugin_Const::SCLAPI_OPTIONS, $options ); 
        }
        if ( ! empty( $_POST[ Plugin_Const::GET_NEW_API_KEY ] ) && empty( $_POST[ $API_key_field_name ] ) ) {
            $response = get_newapikey();
            if ( $response[ Plugin_Const::RESPONSE_STATUS ] == 200 ) {
                $_POST[ $API_key_field_name ] = base64_decode( $response[ Plugin_Const::RESPONSE_DATA ] );
            }
            else {
                show_header_message( $response[ Plugin_Const::RESPONSE_DATA ] );
                $need_save_option = FALSE;
            }
        }
        if ( $need_save_option ) {
            $opt_api_key = $_POST[ $API_key_field_name ];
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
    
    $continue_operation = '';
    $download_file_bits = '';
    if ( ! empty( $file_operation ) ) {
        switch ( $file_operation ) {
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
                $download_response = download_file();
                if ( $download_response[ Plugin_Const::RESPONSE_STATUS ] == 200 ) {
                    $continue_operation = File_Operations::CONTINUE_DOWNLOAD;
                    $download_file_bits = base64_encode( $download_response[ Plugin_Const::RESPONSE_DATA ] );
                }
                break;
            default:
                break;
        };
    }

    $create_example = '';
    if ( $opt_create_example ) {
        $create_example = 'checked="checked"';
    }
    show_options_form( $hidden_field_name, $API_key_field_name, $opt_api_key, $continue_operation, $download_file_bits, $_POST['filename'], $create_example );
}
function get_newapikey() {
    $user_email = wp_get_current_user()->user_email;
    return Spreadsheet_Request::generate_new_API_key( $user_email );
}
function rename_file() {
    $file_name = $_POST['filename'];
    $new_file_name = $_POST['newfilename'];
    if ( empty( $file_name ) ) {
        show_header_message( Header_Messages::SELECT_RENAME );
        return;
    }
    $file_renamed = Spreadsheet_Cloud_API_Actions::rename_file( $file_name, $new_file_name );
    if ( $file_renamed[ Plugin_Const::RESPONSE_STATUS ] == 200 ) {
        show_header_message( sprintf(Header_Messages::FILE_RENAMED, $file_name, $new_file_name) );
    }
    else {
        show_header_message( $file_renamed[ Plugin_Const::RESPONSE_DATA ] );
    }
}
function download_file() {
    $file_name = $_POST['filename'];
    if ( ! empty( $file_name ) ) {
        $download_response = Spreadsheet_Cloud_API_Actions::download_file( $file_name );
        if ( $download_response[ Plugin_Const::RESPONSE_STATUS ] == 200 ) {
            show_header_message( sprintf(Header_Messages::FILE_DOWNLOADED, $file_name) );
            return $download_response;
        }
        else {
            show_header_message( $download_response[ Plugin_Const::RESPONSE_DATA ] );
        }
        update_sclapi_option( Plugin_Const::USER_FILE_LIST, Spreadsheet_Cloud_API_Actions::get_files_list(1) );
    }
    else {
        show_header_message( Header_Messages::SELECT_DOWNLOAD );
    }
}
function upload_file() {
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    $file = &$_FILES['my-file-upload'];
    if ( ! empty( $file['name'] ) ) {
        $upload_response = Spreadsheet_Cloud_API_Actions::upload_file( $file );
        if ( $upload_response[ Plugin_Const::RESPONSE_STATUS ] == 200 ) {
            show_header_message( sprintf( Header_Messages::FILE_UPLOADED, $file['name'] ) );
        }
        else {
            show_header_message( $upload_response[ Plugin_Const::RESPONSE_DATA ] );
        }
        update_sclapi_option( Plugin_Const::USER_FILE_LIST, Spreadsheet_Cloud_API_Actions::get_files_list(1) );
    }
    else {
        show_header_message( Header_Messages::SELECT_UPLOAD );
    }
}
function delete_file() {
    $file_name = $_POST['filename'];
    if ( ! empty( $file_name ) ) {
        $file_deleted = Spreadsheet_Cloud_API_Actions::delete_file( $file_name );
        if ( $file_deleted[ Plugin_Const::RESPONSE_STATUS ] == 200 ) {
            show_header_message( sprintf( Header_Messages::FILE_DELETED, $file_name ) );
            update_sclapi_option( Plugin_Const::USER_FILE_LIST, Spreadsheet_Cloud_API_Actions::get_files_list(1) );
        }
        else {
            show_header_message( $file_deleted[ Plugin_Const::RESPONSE_DATA ] );
        }
    }
    else {
        show_header_message( Header_Messages::SELECT_DELETE );
    }
}

function update_sclapi_option( $option_key, $option_value ) {
    $options = get_option( Plugin_Const::SCLAPI_OPTIONS );
    $options[ $option_key ] = $option_value;
    update_option( Plugin_Const::SCLAPI_OPTIONS, $options );
}

function show_header_message( $message ) {
    echo '<div class="updated"><p><strong>';
    _e( $message, 'mt_trans_domain' ); 
    echo '</strong></p></div>';
}
function show_options_form( $hidden_field_name, $API_key_field_name, $opt_api_key, $continue_operation, $download_file, $file_name, $create_example ) {
    $have_API_key = '';
    $unhave_API_key = 'style="display: none"';
    if ( empty( $opt_api_key ) ) {
        $have_API_key = 'style="display: none"';
        $unhave_API_key = '';
    }
    $options_header = __( 'SpreadsheetCloudAPI Plugin Options', 'mt_trans_domain' );
    $options_action = str_replace( '%7E', '~', $_SERVER['REQUEST_URI'] );
    $options_API_key = __( "API Key:", Plugin_Const::API_KEY );
    $options_update = __( 'Update', 'mt_trans_domain' );
    $service_file_list = Spreadsheet_Cloud_API_Actions::get_files_list(3);
    include ( SPREADSHEEETCLOUDAPI__PLUGIN_DIR.'\options\options.html' );
}
?>