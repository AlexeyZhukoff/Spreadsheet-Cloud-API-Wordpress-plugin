<?php
class Spreadsheet_Request {
    #region Fields
    //const BASE_URI = 'http://localhost:54306/api/spreadsheet';
    //const BASE_WP_URI = 'http://localhost:54306/wpusers/getapikey';
    const BASE_URI = 'http://spreadsheetcloudapi.azurewebsites.net/api/spreadsheet';
    const BASE_WP_URI = 'http://spreadsheetcloudapi.azurewebsites.net/wpusers/getapikey';
    const SCHEME = "amx";
    const EXAMPLE_API_KEY = "24c95646ebd272ff55856413befc97ae";
    #endregion

    public static function generate_new_API_key( $mail ) {
        if ( empty( $mail ) )
            return null;

        $params = array( 'cemail' => base64_encode( $mail ) );
        $header = ['Content-type: application/json',];

        $request = curl_init();
        curl_setopt_array( $request, [
            CURLOPT_URL => self::BASE_WP_URI.'?'.http_build_query( $params ),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true
        ]);

        $response = curl_exec( $request );
        $info = curl_getinfo( $request );

        curl_close( $request );
        
        return array( Plugin_Const::RESPONSE_STATUS => $info['http_code'], Plugin_Const::RESPONSE_DATA => $response );
    }

    #region public interface
    public static function upload_file( $file ) {
        if ( empty( $file ) )
            return;
        $request = curl_init();
        
        $file_array = array();
        $file_array[ $file['name'] ] = $file['tmp_name'];
        
        self::curl_custom_postfields( $request, array(), $file_array ); 
        curl_setopt(
            $request,
            CURLOPT_URL,
            self::BASE_URI.'/upload'
        );
        $response = curl_exec( $request );
        $info = curl_getinfo( $request );

        curl_close( $request );

        return array( Plugin_Const::RESPONSE_STATUS => $info['http_code'], Plugin_Const::RESPONSE_DATA => $response );
    }
    public static function download_file( $params ) {
        return self::get( $params, '/download' );
    }
    public static function delete_file( $params ) {
        return self::delete( $params, '/deletefile' );
    }
    public static function rename_file( $params ) {
        return self::post( $params, '/renamefile' );
    }
    public static function get_files_list() {
        return self::get_without_params( '/getfilelist' );
    }
    public static function get_HTML( $params ) {
        return self::get( $params, '/exporttohtml' );
    }
    public static function get_pictures( $params ) {
        return self::get( $params, '/getpictures' );
    }
    #endregion

    #region Helper
    private static function get_API_key() {
        $action_type = get_option( Plugin_Const::SCLAPI_OPTIONS )[ Plugin_Const::ACTION_TYPE ];
        if ( $action_type == Plugin_Const::EXAMPLE_ACTION_TYPE ) {
            return self::EXAMPLE_API_KEY;  
        }
        return get_option( Plugin_Const::SCLAPI_OPTIONS )[ Plugin_Const::API_KEY ];
    }
    private static function generate_header( $content_length, $content_type ) {
        $API_key = self::get_API_key();

        if ( is_null( $content_type ) )
            $content_type = 'application/json';

        $header = [
            'Content-type: '.$content_type,
            'Authorization: '.self::SCHEME.' '.$API_key,
        ];
        if ( ! empty( $content_length ) || ! is_null( $content_length ) ) {
            $header[] = 'Content-Length: '.$content_length;
        }        
        return $header;
    }
    private static function put( $params, $url ) {
        if ( empty( $params ) )
            return null;

        $json = json_encode( $params );

        $header = self::generate_header( strlen( $json ), null );

        $request = curl_init();

        curl_setopt_array( $request, [
            CURLOPT_URL => self::BASE_URI.$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_POSTFIELDS => $json
        ]);
        $response = curl_exec( $request );
        $info = curl_getinfo( $request );

        curl_close( $request );

        return array( Plugin_Const::RESPONSE_STATUS => $info['http_code'], Plugin_Const::RESPONSE_DATA => $response );
    }
    private static function post( $params, $url ) {
        if ( empty( $params ) )
            return null;

        $json = json_encode( $params );

        $header = self::generate_header( null, null );

        $request = curl_init();

        curl_setopt_array( $request, [
            CURLOPT_URL => self::BASE_URI.$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_FOLLOWLOCATION => true
        ]);

        $response = curl_exec( $request );
        $info = curl_getinfo( $request );

        curl_close( $request );

        return array( Plugin_Const::RESPONSE_STATUS => $info['http_code'], Plugin_Const::RESPONSE_DATA => $response );
    }
    private static function delete( $params, $url ) {
        if ( empty( $params ) )
            return null;

        $file_name = "=".$params["filename"];
        $header = self::generate_header( strlen( $file_name ), 'application/x-www-form-urlencoded' );
        
        $request = curl_init();

        curl_setopt_array( $request, [
            CURLOPT_URL => self::BASE_URI.$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_POSTFIELDS => $file_name,
            CURLOPT_FOLLOWLOCATION => true
        ]);

        $response = curl_exec( $request );
        $info = curl_getinfo( $request );

        curl_close( $request );
        
        return array( Plugin_Const::RESPONSE_STATUS => $info['http_code'], Plugin_Const::RESPONSE_DATA => $response );
    }
    private static function get( $params, $url ) {
        if ( empty( $params ) )
            return null;
        
        $header = self::generate_header( null, null );

        $request = curl_init();
        curl_setopt_array( $request, [
            CURLOPT_URL => self::BASE_URI.$url.'?'.http_build_query( $params ),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true
        ]);

        $response = curl_exec( $request );
        $info = curl_getinfo( $request );

        curl_close( $request );
        
        return array( Plugin_Const::RESPONSE_STATUS => $info['http_code'], Plugin_Const::RESPONSE_DATA => $response );
    }
    private static function get_without_params( $url ) {
        $header = self::generate_header( null, null );

        $request = curl_init();
        curl_setopt_array( $request, [
            CURLOPT_URL => self::BASE_URI.$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true
        ]);

        $response = curl_exec( $request );
        $info = curl_getinfo( $request );

        curl_close( $request );
        
        return array( Plugin_Const::RESPONSE_STATUS => $info['http_code'], Plugin_Const::RESPONSE_DATA => $response );
    }
    private static function curl_custom_postfields( $ch, array $assoc = array(), array $files = array() ) {
        // invalid characters for "name" and "filename"
        static $disallow = array( "\0", "\"", "\r", "\n" );

        // build normal parameters
        foreach ( $assoc as $k => $v ) {
            $k = str_replace( $disallow, "_", $k );
            $body[] = implode( "\r\n", array(
                "Content-Disposition: form-data; name=\"{$k}\"",
                "",
                filter_var( $v ), 
            ));
        }
        // build file parameters
        foreach( $files as $k => $v ) {
            
            $data = file_get_contents( $v );
            
            $v = call_user_func( "end", explode( DIRECTORY_SEPARATOR, $v ) );
            $k = str_replace( $disallow, "_", $k );
            $v = str_replace( $disallow, "_", $v );
            $body[] = implode( "\r\n", array(
                "Content-Disposition: form-data; filename=\"{$k}\"; name=\"{$v}\"",
                "Content-Type: application/octet-stream",
                "",
                $data, 
            ));
        }
    
        // generate safe boundary 
        do {
            $boundary = "---------------------" . md5( mt_rand() . microtime() );
        } while( preg_grep( "/{$boundary}/", $body ) );
    
        // add boundary for each parameters
        array_walk( $body, function( &$part ) use ( $boundary ) {
            $part = "--{$boundary}\r\n{$part}";
        });
    
        // add final boundary
        $body[] = "--{$boundary}--";
        $body[] = "";
    
        // set options
        return @curl_setopt_array( $ch, array(
            CURLOPT_POST       => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode( "\r\n", $body ),
            CURLOPT_HTTPHEADER => array(
                "Authorization: amx ".get_option( Plugin_Const::SCLAPI_OPTIONS )[ Plugin_Const::API_KEY ],
                "Expect: 100-continue",
                "Content-Type: multipart/form-data; boundary={$boundary}", // change Content-Type
            ),
        ));
    }
    #endregion
}