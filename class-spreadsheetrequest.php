<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

class Spreadsheet_Request {
    #region Fields
    const BASE_URI = 'http://spreadsheetcloudapi.azurewebsites.net/api/spreadsheet';
    const BASE_WP_URI = 'http://spreadsheetcloudapi.azurewebsites.net/wpusers/getapikey';
    const SCHEME = "amx";
    #endregion

    #region public interface
    public static function sclapi_generate_new_API_key( $mail ) {
        if ( empty( $mail ) )
            return null;

        $params = array( 'cemail' => base64_encode( $mail ) );
        $header = array('Content-Type' => 'application/json');

        try {
            $request = wp_remote_get( 
                self::BASE_WP_URI.'?'.http_build_query( $params ), 
                array( 'timeout' => 120, 
                'httpversion' => '1.1', 
                'headers' => $header ) );
        } catch ( Exception $e ) {
            return array( Sclapi_Plugin_Const::RESPONSE_STATUS => 434, Sclapi_Plugin_Const::RESPONSE_DATA => $e );
        }

        if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ){
            return array( Sclapi_Plugin_Const::RESPONSE_STATUS => $request['response']['code'], Sclapi_Plugin_Const::RESPONSE_DATA => $request['body'] );
        }
    }
    public static function sclapi_upload_file( $file ) {
        if ( empty( $file ) )
            return;

        $k = $file['name'];
        $v = $file['tmp_name'];
        $file_data = file_get_contents( $v );
        $v = call_user_func( "end", explode( DIRECTORY_SEPARATOR, $v ) );
        $k = str_replace( $disallow, "_", $k );
        $v = str_replace( $disallow, "_", $v );
        $body[] = implode( "\r\n", array(
                "Content-Disposition: form-data; filename=\"{$k}\"; name=\"{$v}\"",
                "Content-Type: application/octet-stream",
                "",
                $file_data, 
            ));

        do {
            $boundary = "---------------------" . md5( mt_rand() . microtime() );
        } while( preg_grep( "/{$boundary}/", $body ) );
    
        array_walk( $body, function( &$part ) use ( $boundary ) {
            $part = "--{$boundary}\r\n{$part}";
        });
    
        $body[] = "--{$boundary}--";
        $body[] = "";

        $header = self::sclapi_generate_header( NULL, null );
        $header['Expect'] = '100-continue';
        $header['Content-Type'] = "multipart/form-data; boundary={$boundary}";
        
        try {
            $request = wp_remote_post( 
                self::BASE_URI.$url.'/upload', 
                array( 'timeout' => 120, 
                'httpversion' => '1.1', 
                'headers' => $header, 
                'body' => implode( "\r\n", $body ),
                ) );

        } catch ( Exception $e ) {
            return array( Sclapi_Plugin_Const::RESPONSE_STATUS => 434, Sclapi_Plugin_Const::RESPONSE_DATA => $e );
        }

        if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ){
            return array( Sclapi_Plugin_Const::RESPONSE_STATUS => $request['response']['code'], Sclapi_Plugin_Const::RESPONSE_DATA => $request['body'] );
        }
    }
    public static function sclapi_rename_file( $params ) {
        return self::sclapi_post( $params, '/renamefile' );
    }
    public static function sclapi_download_file( $params ) {
        return self::sclapi_get( $params, '/download' );
    }
    public static function sclapi_delete_file( $params ) {
        return self::sclapi_delete( $params, '/deletefile' );
    }
    public static function sclapi_get_files_list() {
        return self::sclapi_get_without_params( '/getfilelist' );
    }
    public static function sclapi_get_HTML( $params ) {
        return self::sclapi_get( $params, '/exporttohtml' );
    }
    public static function sclapi_get_pictures( $params ) {
        return self::sclapi_get( $params, '/getpictures' );
    }
    #endregion

    #region Helper
    private static function sclapi_get_API_key() {
        return get_option( Sclapi_Plugin_Const::SCLAPI_OPTIONS )[ Sclapi_Plugin_Const::API_KEY ];
    }
    private static function sclapi_post( $params, $url ) {
        if ( empty( $params ) )
            return null;

        $json = json_encode( $params );

        $header = self::sclapi_generate_header( null, null );

        try {
            $request = wp_remote_post( 
                self::BASE_URI.$url, 
                array( 'timeout' => 120, 
                'httpversion' => '1.1', 
                'headers' => $header, 
                'body' => $json ) );

        } catch ( Exception $e ) {
            return array( Sclapi_Plugin_Const::RESPONSE_STATUS => 434, Sclapi_Plugin_Const::RESPONSE_DATA => $e );
        }

        if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ){
            return array( Sclapi_Plugin_Const::RESPONSE_STATUS => $request['response']['code'], Sclapi_Plugin_Const::RESPONSE_DATA => $request['body'] );
        }
    }
    private static function sclapi_delete( $params, $url ) {
        if ( empty( $params ) )
            return null;

        $file_name = "=".$params["filename"];
        
        $header = self::sclapi_generate_header( strlen( $file_name ), 'application/x-www-form-urlencoded' );

        try {
            $request = wp_remote_request( 
                self::BASE_URI.$url, 
                    array( 
                        'timeout' => 120, 
                        'httpversion' => '1.1', 
                        'headers' => $header, 
                        'body' => $file_name,
                        'method' => 'DELETE' )
                );
        } catch ( Exception $e ) {
            return array( Sclapi_Plugin_Const::RESPONSE_STATUS => 434, Sclapi_Plugin_Const::RESPONSE_DATA => $e );
        }
        if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ){
            return array( Sclapi_Plugin_Const::RESPONSE_STATUS => $request['response']['code'], Sclapi_Plugin_Const::RESPONSE_DATA => $request['body'] );
        }
    }
    private static function sclapi_generate_header( $content_length, $content_type ) {
        $API_key = self::sclapi_get_API_key();

        if ( is_null( $content_type ) )
            $content_type = 'application/json';

        $header = array(
        'Authorization' => self::SCHEME .' '. $API_key,
        'Content-Type' => $content_type,
        );
        if ( ! empty( $content_length ) || ! is_null( $content_length ) ) {
            $header['Content-Length'] = $content_length;
        }        

        return $header;
    }
    private static function sclapi_get_without_params( $url ) {
        $header = self::sclapi_generate_header( null, null );
        try {
            $request = wp_remote_get( self::BASE_URI.$url, array( 'timeout' => 120, 'httpversion' => '1.1', 'headers' => $header, 'body' => 'grant_type=client_credentials' ) );
        } catch ( Exception $e ) {
            return array( Sclapi_Plugin_Const::RESPONSE_STATUS => 434, Sclapi_Plugin_Const::RESPONSE_DATA => $e );
        }
        if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ){
            return array( Sclapi_Plugin_Const::RESPONSE_STATUS => $request['response']['code'], Sclapi_Plugin_Const::RESPONSE_DATA => $request['body'] );
        }
    }
    private static function sclapi_get( $params, $url ) {
        if ( empty( $params ) )
            return null;
        $header = self::sclapi_generate_header( null, null );

        try {
            $request = wp_remote_get( 
                self::BASE_URI.$url.'?'.http_build_query( $params ), 
                array( 'timeout' => 120, 
                'httpversion' => '1.1', 
                'headers' => $header, 
                'body' => 'grant_type=client_credentials' ) );
        } catch ( Exception $e ) {
            return array( Sclapi_Plugin_Const::RESPONSE_STATUS => 434, Sclapi_Plugin_Const::RESPONSE_DATA => $e );
        }

        if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ){
            return array( Sclapi_Plugin_Const::RESPONSE_STATUS => $request['response']['code'], Sclapi_Plugin_Const::RESPONSE_DATA => $request['body'] );
        }
    }
    #endregion
}