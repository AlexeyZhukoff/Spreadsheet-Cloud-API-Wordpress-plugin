<?php
class SpreadsheetRequest {
    #region Fields
    //const baseUri = 'http://localhost:54306/odata';
    //const baseUri = 'http://localhost:54306/api/spreadsheet';
    const baseUri = 'http://spreadsheetcloud.azurewebsites.net/api/spreadsheet';
    const scheme = "amx";
    #endregion

    public static function getHtml($params) {
        return self::get($params, '/exporttohtml');
    }
    public static function getPictures($params) {
        return self::get($params, '/getpictures');
    }

    #region File I/0
    public static function uploadFile($file) {
        if (empty($file))
            return;
        $request = curl_init();
        
        $arF = array();
        $arF[$file['name']] = $file['tmp_name'];
        
        self::curl_custom_postfields($request, array(), $arF); 
        curl_setopt(
            $request,
            CURLOPT_URL,
            self::baseUri.'/upload'
        );
        $response = curl_exec($request);
        $info = curl_getinfo($request);

        curl_close($request);

        return array('status' => $info['http_code'], 'data' => $response);
    }
    static function curl_custom_postfields($ch, array $assoc = array(), array $files = array()) {
    
        // invalid characters for "name" and "filename"
        static $disallow = array("\0", "\"", "\r", "\n");

        // build normal parameters
        foreach ($assoc as $k => $v) {
            $k = str_replace($disallow, "_", $k);
            $body[] = implode("\r\n", array(
                "Content-Disposition: form-data; name=\"{$k}\"",
                "",
                filter_var($v), 
            ));
        }
        // build file parameters
        foreach ($files as $k => $v) {
            
            $data = file_get_contents($v);
            
            $v = call_user_func("end", explode(DIRECTORY_SEPARATOR, $v));
            $k = str_replace($disallow, "_", $k);
            $v = str_replace($disallow, "_", $v);
            $body[] = implode("\r\n", array(
                "Content-Disposition: form-data; filename=\"{$k}\"; name=\"{$v}\"",
                "Content-Type: application/octet-stream",
                "",
                $data, 
            ));
        }
    
        // generate safe boundary 
        do {
            $boundary = "---------------------" . md5(mt_rand() . microtime());
        } while (preg_grep("/{$boundary}/", $body));
    
        // add boundary for each parameters
        array_walk($body, function (&$part) use ($boundary) {
            $part = "--{$boundary}\r\n{$part}";
        });
    
        // add final boundary
        $body[] = "--{$boundary}--";
        $body[] = "";
    
        // set options
        return @curl_setopt_array($ch, array(
            CURLOPT_POST       => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\r\n", $body),
            CURLOPT_HTTPHEADER => array(
                "Authorization: amx ".get_option( 'API_Key' ),
                "Expect: 100-continue",
                "Content-Type: multipart/form-data; boundary={$boundary}", // change Content-Type
            ),
        ));
    }
    #endregion

    #region Helper
    private static function generateHeader($contentlength, $contenttype) {
        $apiKey = get_option( 'API_Key' );

        if(is_null($contenttype))
            $contenttype = 'application/json';

        $header = [
            'Content-type: '.$contenttype,
            'Authorization: '.self::scheme.' '.$apiKey,
        ];
        if (!empty($contentlength) || !is_null($contentlength)) {
            $header[] = 'Content-Length: '.$contentlength;
        }        
        return $header;
    }

    private static function put($params, $url) {
        if (empty($params))
            return null;

        $json = json_encode($params);

        $header = self::generateHeader(strlen($json), null);

        $request = curl_init();

        curl_setopt_array($request, [
            CURLOPT_URL => self::baseUri.$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_POSTFIELDS => $json
        ]);
        $response = curl_exec($request);
        $info = curl_getinfo($request);

        curl_close($request);

        return array('status' => $info['http_code'], 'data' => $response);
    }

    private static function post($params, $url) {
        if (empty($params))
            return null;

        $json = json_encode($params);

        $header = self::generateHeader(null, null);

        $request = curl_init();

        curl_setopt_array($request, [
            CURLOPT_URL => self::baseUri.$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_FOLLOWLOCATION => true
        ]);

        $response = curl_exec($request);
        $info = curl_getinfo($request);

        curl_close($request);

        return array('status' => $info['http_code'], 'data' => $response);
    }

    private static function delete($params, $url) {
        if (empty($params))
            return null;

        $filename = "=".$params["filename"];
        $header = self::generateHeader(strlen($filename), 'application/x-www-form-urlencoded');
        
        $request = curl_init();

        curl_setopt_array($request, [
            CURLOPT_URL => self::baseUri.$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_POSTFIELDS => $filename,
            CURLOPT_FOLLOWLOCATION => true
        ]);

        $response = curl_exec($request);
        $info = curl_getinfo($request);

        curl_close($request);
        
        return array('status' => $info['http_code'], 'data' => $response);
    }
    public static function deletefile($params){
        return self::delete($params, '/deletefile');
    }
    private static function get($params, $url) {
        if (empty($params))
            return null;
        
        $header = self::generateHeader(null, null);

        $request = curl_init();
        curl_setopt_array($request, [
            CURLOPT_URL => self::baseUri.$url.'?'.http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true
        ]);

        $response = curl_exec($request);
        $info = curl_getinfo($request);

        curl_close($request);
        
        return array('status' => $info['http_code'], 'data' => $response);
    }
    private static function getWithoutParams($url) {
        $header = self::generateHeader(null, null);

        $request = curl_init();
        curl_setopt_array($request, [
            CURLOPT_URL => self::baseUri.$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true
        ]);

        $response = curl_exec($request);
        $info = curl_getinfo($request);

        curl_close($request);
        
        return array('status' => $info['http_code'], 'data' => $response);
    }

    public static function getFilesList() {
        return self::getWithoutParams('/getfilelist');
    }
    #endregion
}