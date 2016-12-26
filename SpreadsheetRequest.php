<?php
class SpreadsheetRequest {
    #region Fields
    //const baseUri = 'http://localhost:54306/odata';
    //const baseUri = 'http://localhost:54306/api/spreadsheet';
    const baseUri = 'http://spreadsheetcloud.azurewebsites.net/api/spreadsheet';
    const scheme = "amx";
    #endregion

    #region CellValue
    public static function getCellValue($params) {
        return self::get($params, '/getcellvalue');
    }

    public static function getCellValues($params) {
        return self::post($params, '/getcellvalues');
    }

    public static function setCellValue($params) {
        return self::put($params, "/setcellvalue");
    }
    #endregion

    #region Formula
    public static function setFormula($params) {
        return self::put($params, '/setformula');
    }

    public static function getFormula($params) {
        return self::get($params, '/getformula');
    }
    #endregion
    public static function getHtml($params) {
        return self::get($params, '/exporttohtml');
    }
    #region Worksheet
    public static function createWorksheet($params) {
        return self::post($params, '/createworksheet');
    }
    public static function renameWorksheet($params) {
        return self::put($params, '/renameworksheet');
    }
    public static function deleteWorksheet($params) {
        return self::delete($params, '/deleteworksheet');
    }
    public static function getWorksheets($params) {
        return self::get($params, '/getsheetnames');
    }
    public static function getPictures($params) {
        return self::get($params, '/getpictures');
    }
    #endregion

    #region Convert
    public static function convertDocument($params) {
        return self::post($params, '/convertdocument');
    }
    #endregion

    #region SearchText
    public static function searchText($params) {
        return self::get($params, '/searchtext');
    }
    #endregion

    #region Load/Unload Document (start/end session)
    public static function loadDocument($params) {
        return self::get($params, '/loaddocument');
    }
    public static function closeDocument($params) {
        return self::get($params, '/closedocument');
    }
    #endregion
    public static function getStyles($params) {
        return self::get($params, '/getcellstyle');
    }
    public static function setStyles($params) {
        return self::put($params, '/setcellstyle');
    }
    public static function setRangeStyles($params) {
        return self::put($params, '/setrangestyle');
    }
    public static function mergeCells($params) {
        return self::put($params, '/mergecells');
    }
    public static function unMergeCells($params) {
        return self::put($params, '/unmergecells');
    }
    public static function hideRow($params) {
        return self::put($params, '/hideRow');
    }
    public static function hideColumn($params) {
        return self::put($params, '/hidecolumn');
    }
    public static function unHideRow($params) {
        return self::put($params, '/unhiderow');
    }
    public static function unHideColumn($params) {
        return self::put($params, '/unhidecolumn');
    }
    public static function getHiddenRows($params) {
        return self::get($params, '/gethiddenrows');
    }
    public static function getHiddenColumns($params) {
        return self::get($params, '/gethiddencolumns');
    }
    public static function getMergedCells($params) {
        return self::get($params, '/getmergedcells');
    }
    public static function setRowHeight($params) {
        return self::put($params, '/setrowheight');
    }
    public static function setColumnWidth($params) {
        return self::put($params, '/setcolumnwidth');
    }
    public static function getRowHeight($params) {
        return self::get($params, '/getrowheight');
    }
    public static function getColumnWidth($params) {
        return self::get($params, '/getcolumnwidth');
    }
    public static function insertRows($params) {
        return self::put($params, '/insertrows');
    }
    public static function insertColumns($params) {
        return self::put($params, '/insertcolumns');
    }
    public static function deleteRows($params) {
        return self::delete($params, '/deleterows');
    }
    public static function deleteColumns($params) {
        return self::delete($params, '/deletecolumns');
    }
    public static function insertCells($params) {
        return self::put($params, '/insertcells');
    }
    public static function deleteCells($params) {
        return self::delete($params, '/deletecells');
    }

    #region File I/0
    public static function uploadFile($file) {
        if (empty($file))
            return;

        $request = curl_init();
        $apiKey = SpreadsheetSDK::getInstance()->getApiKey();
        $header = [
            'Authorization: '.self::scheme.' '.$apiKey,
            'Content-type: multipart/form-data',
        ];


        curl_setopt_array($request, [
            CURLOPT_URL => self::baseUri.'/upload',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                'file' => $file
            ]
        ]);
        $response = curl_exec($request);
        $info = curl_getinfo($request);

        curl_close($request);

        return array('status' => $info['http_code'], 'data' => $response);
    }

    public static function downloadFile($json) {
        return self::get($json, '/download');
    }
    #endregion

    #region Helper
    private static function generateHeader($json) {
        
        $apiKey = get_option( 'API_Key' );

        $header = [
            'Content-type: application/json',
            'Authorization: '.self::scheme.' '.$apiKey,
        ];
        if (!empty($json) || !is_null($json)) {
            $header[] = 'Content-Length: '.strlen($json);
        }        
        return $header;
    }

    private static function put($params, $url) {
        if (empty($params))
            return null;

        $json = json_encode($params);

        $header = self::generateHeader($json);

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

        $header = self::generateHeader(null);

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

        $json = json_encode($params);

        $header = self::generateHeader(null);

        $request = curl_init();

        curl_setopt_array($request, [
            CURLOPT_URL => self::baseUri.$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_FOLLOWLOCATION => true
        ]);

        $response = curl_exec($request);
        $info = curl_getinfo($request);

        curl_close($request);

        return array('status' => $info['http_code'], 'data' => $response);
    }

    private static function get($params, $url) {
        if (empty($params))
            return null;
        
        $header = self::generateHeader(null);

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
    #endregion
}