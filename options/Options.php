<?php
function mt_add_pages() {
    add_options_page( 'SpreadsheetCloudAPI Options', 'SpreadsheetCloudAPI Options', 'manage_options', 'spreadsheetcloudapioptions', 'mt_options_page' );
}

function mt_options_page() {
    $hidden_field_name = 'mt_submit_hidden';
    $API_key_field_name = Plugin_Const::API_KEY;
    $opt_api_key = get_option( Plugin_Const::SCLAPI_OPTIONS )[ Plugin_Const::API_KEY ];
    $file_operation = $_POST['my-file-operation'];
    $options = get_option( Plugin_Const::SCLAPI_OPTIONS );
    $show_wizard = TRUE;
    $need_save_option = ($_POST[ 'Submit' ] == __( 'Update', 'mt_trans_domain' ));
    if ( ! empty( $_POST[ 'user-choise' ] ) ) {
        $show_wizard = FALSE;
        if ( $_POST['user-choise'] == 'generate' ) {
            $response = get_newapikey();
            if ( $response[ Plugin_Const::RESPONSE_STATUS ] == 200 ) {
                $_POST[ $API_key_field_name ] = base64_decode( $response[ Plugin_Const::RESPONSE_DATA ] );
            }
            else {
                show_header_message( $response[ Plugin_Const::RESPONSE_DATA ] );
                $need_save_option = FALSE;
                $show_wizard = TRUE;
            }
        }
    }
    if ( $need_save_option ) {
        $opt_api_key = $_POST[ $API_key_field_name ];
        if ( $_POST['user-choise'] != 'haveapikey' ) {
            $options[ Plugin_Const::API_KEY ] = $opt_api_key;
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

    show_options_form( $hidden_field_name, $API_key_field_name, $opt_api_key, $continue_operation, $download_file_bits, $_POST['filename'], $show_wizard );
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
        }
        else {
            show_header_message( $file_deleted[ Plugin_Const::RESPONSE_DATA ] );
        }
    }
    else {
        show_header_message( Header_Messages::SELECT_DELETE );
    }
}

function show_header_message( $message ) {
    echo '<div class="updated"><p><strong>';
    _e( $message, 'mt_trans_domain' ); 
    echo '</strong></p></div>';
}
function show_options_form( $hidden_field_name, $API_key_field_name, $opt_api_key, $continue_operation, $download_file, $file_name, $show_wizard) {
    $have_API_key = '';
    $unhave_API_key = 'style="display: none"';
    if ( empty( $opt_api_key ) && ( $show_wizard == TRUE ) ) {
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