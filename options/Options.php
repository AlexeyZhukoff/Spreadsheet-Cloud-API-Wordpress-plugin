<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

function sclapi_mt_add_pages() {
    $page = add_options_page( 'SpreadsheetCloudAPI Options', 'SpreadsheetCloudAPI Options', 'manage_options', 'spreadsheetcloudapioptions', 'sclapi_mt_options_page' );
    add_action( 'admin_print_scripts-' . $page, 'sclapi_options_scripts' );
    add_action('admin_print_styles-'. $page, 'sclapi_options_styles');
}
function sclapi_options_scripts() {
    wp_enqueue_script( 'sclapi_options_script' );
}
function sclapi_options_styles() {
    wp_enqueue_style( 'sclapi_options_style' );
}
function sclapi_mt_options_page() {
    $hidden_field_name = 'mt_submit_hidden';
    $API_key_field_name = Sclapi_Plugin_Const::API_KEY;
    $opt_api_key = get_option( Sclapi_Plugin_Const::SCLAPI_OPTIONS )[ Sclapi_Plugin_Const::API_KEY ];
    $file_operation = sclapi_get_array_element($_POST, 'my-file-operation');
    $options = get_option( Sclapi_Plugin_Const::SCLAPI_OPTIONS );
    $show_wizard = empty( $opt_api_key );
    $need_save_option = (  sclapi_get_array_element($_POST, 'Submit') == __( 'Update', 'mt_trans_domain' ) );

    if ( ! empty( $_POST[ 'user-choise' ] ) ) {
        $show_wizard = FALSE;
        if ( $_POST['user-choise'] == 'generate' ) {
            $response = sclapi_get_newapikey();
            if ( $response[ Sclapi_Plugin_Const::RESPONSE_STATUS ] == 200 ) {
                $_POST[ $API_key_field_name ] = base64_decode( $response[ Sclapi_Plugin_Const::RESPONSE_DATA ] );
            }
            else {
                sclapi_show_header_message( $response[ Sclapi_Plugin_Const::RESPONSE_DATA ] );
                $need_save_option = FALSE;
            }
        }
    }
    if ( $need_save_option ) {
        $opt_api_key = $_POST[ $API_key_field_name ];
        if ( $_POST['user-choise'] != 'haveapikey' ) {
            $options[ Sclapi_Plugin_Const::API_KEY ] = $opt_api_key;
            update_option( Sclapi_Plugin_Const::SCLAPI_OPTIONS, $options );
            sclapi_show_header_message( Sclapi_Header_Messages::OPTIONS_SAVED );
            $show_wizard = FALSE; // = empty( $opt_api_key ); if need show wizard after erase api key
        }
    }
    $download_file = '';
    if ( ! empty( $file_operation ) ) {
        switch ( $file_operation ) {
            case Sclapi_File_Operations::UPLOAD:
                sclapi_upload_file();
                break;
            case Sclapi_File_Operations::DELETE:
                sclapi_delete_file();
                break;
            case Sclapi_File_Operations::RENAME:
                sclapi_rename_file();
                break;
            case Sclapi_File_Operations::DOWNLOAD:
                $download_response = sclapi_download_file();
                if ( $download_response[ Sclapi_Plugin_Const::RESPONSE_STATUS ] == 200 ) {
                    $download_file ='<a class="continuedownload" href="data:application/octet-stream; charset=utf-8; base64,'
                    .base64_encode( $download_response[ Sclapi_Plugin_Const::RESPONSE_DATA ] ).
                    '" download = "'.sclapi_get_array_element ( $_POST, 'filename' ).'" style="display: none"><a/>';
                }
                break;
            default:
                break;
        };
    }

    sclapi_show_options_form( $hidden_field_name, $API_key_field_name, $opt_api_key, $download_file, $show_wizard );
}
function sclapi_get_newapikey() {
    $user_email = wp_get_current_user()->user_email;
    return Spreadsheet_Request::sclapi_generate_new_API_key( $user_email );
}
function sclapi_rename_file() {
    $file_name = $_POST['filename'];
    $new_file_name = $_POST['newfilename'];
    if ( empty( $file_name ) ) {
        sclapi_show_header_message( Sclapi_Header_Messages::SELECT_RENAME );
        return;
    }
    $file_renamed = Spreadsheet_Cloud_API_Actions::sclapi_rename_file( $file_name, $new_file_name );
    if ( $file_renamed[ Sclapi_Plugin_Const::RESPONSE_STATUS ] == 200 ) {
        sclapi_show_header_message( sprintf(Sclapi_Header_Messages::FILE_RENAMED, $file_name, $new_file_name) );
    }
    else {
        sclapi_show_header_message( $file_renamed[ Sclapi_Plugin_Const::RESPONSE_DATA ] );
    }
}
function sclapi_download_file() {
    $file_name = sclapi_get_array_element ( $_POST, 'filename' );
    if ( ! empty( $file_name ) ) {
        $download_response = Spreadsheet_Cloud_API_Actions::sclapi_download_file( $file_name );
        if ( $download_response[ Sclapi_Plugin_Const::RESPONSE_STATUS ] == 200 ) {
            sclapi_show_header_message( sprintf(Sclapi_Header_Messages::FILE_DOWNLOADED, $file_name) );
            return $download_response;
        }
        else {
            sclapi_show_header_message( $download_response[ Sclapi_Plugin_Const::RESPONSE_DATA ] );
        }
    }
    else {
        sclapi_show_header_message( Sclapi_Header_Messages::SELECT_DOWNLOAD );
    }
}
function sclapi_upload_file() {
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    $file = &$_FILES['my-file-upload'];
    if ( ! empty( $file['name'] ) ) {
        $upload_response = Spreadsheet_Cloud_API_Actions::sclapi_upload_file( $file );
        if ( $upload_response[ Sclapi_Plugin_Const::RESPONSE_STATUS ] == 200 ) {
            sclapi_show_header_message( sprintf( Sclapi_Header_Messages::FILE_UPLOADED, $file['name'] ) );
        }
        else {
            sclapi_show_header_message( $upload_response[ Sclapi_Plugin_Const::RESPONSE_DATA ] );
        }
    }
    else {
        sclapi_show_header_message( Sclapi_Header_Messages::SELECT_UPLOAD );
    }
}
function sclapi_delete_file() {
    $file_name = sclapi_get_array_element ( $_POST, 'filename' );
    if ( ! empty( $file_name ) ) {
        $file_deleted = Spreadsheet_Cloud_API_Actions::sclapi_delete_file( $file_name );
        if ( $file_deleted[ Sclapi_Plugin_Const::RESPONSE_STATUS ] == 200 ) {
            sclapi_show_header_message( sprintf( Sclapi_Header_Messages::FILE_DELETED, $file_name ) );
        }
        else {
            sclapi_show_header_message( $file_deleted[ Sclapi_Plugin_Const::RESPONSE_DATA ] );
        }
    }
    else {
        sclapi_show_header_message( Sclapi_Header_Messages::SELECT_DELETE );
    }
}


function sclapi_show_header_message( $message ) {
    echo '<div class="updated"><p><strong>';
    _e( $message, 'mt_trans_domain' ); 
    echo '</strong></p></div>';
}
function sclapi_show_options_form( $hidden_field_name, $API_key_field_name, $opt_api_key, $download_file, $show_wizard) {
    $options_style = '';
    $file_manager_style = '';
    $wizard_style = '';

    if ( $show_wizard == FALSE ) {
        if ( empty( $opt_api_key ) ) {
            $file_manager_style = 'style="display: none"';
        }
        $wizard_style = 'style="display: none"';
    } else {
        $options_style = 'style="display: none"';
        $file_manager_style = 'style="display: none"';
    }

    $options_header = __( 'SpreadsheetCloudAPI Plugin Options', 'mt_trans_domain' );
    $options_action = str_replace( '%7E', '~', $_SERVER['REQUEST_URI'] );
    $options_API_key = __( "API Key:", Sclapi_Plugin_Const::API_KEY );
    $options_update = __( 'Update', 'mt_trans_domain' );
    $service_file_list = Spreadsheet_Cloud_API_Actions::sclapi_get_files_list(3);

    include ( SPREADSHEEETCLOUDAPI__PLUGIN_DIR.'\options\options.html' );
}

function sclapi_get_array_element($elements_array, $element_key){
    if(array_key_exists($element_key, $elements_array)){
        return $elements_array[$element_key];
    }
    return '';
}
?>