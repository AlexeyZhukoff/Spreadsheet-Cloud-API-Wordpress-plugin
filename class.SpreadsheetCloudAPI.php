<?php
class SpreadsheetCloudAPIActions {
    public static function init() {
	}
    public static function plugin_activation() {
	}
    
    private static function bail_on_activation( $message, $deactivate = true ) {
		if ( $deactivate ) {
			$plugins = get_option( 'active_plugins' );
			$SpreadsheetCloudAPI = plugin_basename( SPREADSHEEETCLOUDAPI__PLUGIN_DIR . 'SpreadsheetCloudAPI.php' );
			$update  = false;
			foreach ( $plugins as $i => $plugin ) {
				if ( $plugin === $SpreadsheetCloudAPI ) {
					$plugins[$i] = false;
					$update = true;
				}
			}

			if ( $update ) {
				update_option( 'active_plugins', array_filter( $plugins ) );
			}
		}
		exit;
	}
    public static function plugin_deactivation( ) {
	}
   	public static function deactivate_key( $key ) {
	}
    public function GetAction ($atts) {
        $command = $atts[Parameters::Command];
        switch ( $command ){
            case Commands::GetHtmlRange:
                $response = self::GetHTMLRange($atts);
                break;
            case Commands::GetImage:
                $response = self::GetImage($atts);
                break;
            case Commands::GetImageBytes:
                $response = self::GetImageBytes($atts);
                break;
            default:
                $response = 'Method error!';
                break;
        };
        return $response;
    }

    public function UploadFile($file){
        return SpreadsheetRequest::uploadFile($file);
    }
    public function DownloadFile($filename){
        $params = array(Parameters::FileName => $filename);
        $downloadresponse = SpreadsheetRequest::downloadfile($params);
        return $downloadresponse;
    }
    public function DeleteFile($filename){
        $params = array(Parameters::FileName => $filename);
        return SpreadsheetRequest::deletefile($params);
    }
    public function RenameFile($filename, $newfilename){
        $params = array(Parameters::FileName => $filename,
        Parameters::NewFileName => $newfilename,);
        return SpreadsheetRequest::renamefile($params);
    }

    function GetHTMLRange ($atts) {
        $params = self::ExtractGetHTMLRangeParams($atts);
        $output = SpreadsheetRequest::getHtml($params);
        if($output['status'] != 200){
            return "Error";
        } else
            return self::FixHTMLStyle($output['data']);
    }
    function FixHTMLStyle($HtmlCode){
        $style = "<style>
        .initial-style table {
            border: initial;
        }
        .initial-style table td {
            border: initial;
            padding: initial;
        }
        </style>
        <div class=\"";
        return $style."initial-style \"".">".$HtmlCode."</div>";
    }
    function ExtractGetHTMLRangeParams($atts){
        extract(shortcode_atts(array(
            Parameters::FileName                =>'',
            Parameters::SheetIndex              =>NULL,
            Parameters::Range                   =>'',
            Parameters::SheetName               =>'',
            Parameters::StartRowIndex           =>NULL,
            Parameters::StartColumnIndex        =>NULL,
            Parameters::EndRowIndex             =>NULL,
            Parameters::EndColumnIndex          =>NULL,
            Parameters::ExportDrawingObjects    =>'true',
            Parameters::ExportGridlines    =>'false'), $atts));
        $params = array(
        Parameters::FileName => $atts[Parameters::FileName], 
        Parameters::SheetIndex => $atts[Parameters::SheetIndex], 
        Parameters::Range => $atts[Parameters::Range],
        Parameters::SheetName => $atts[Parameters::SheetName],
        Parameters::StartRowIndex => $atts[Parameters::StartRowIndex],
        Parameters::StartColumnIndex => $atts[Parameters::StartColumnIndex],
        Parameters::EndRowIndex => $atts[Parameters::EndRowIndex],
        Parameters::EndColumnIndex => $atts[Parameters::EndColumnIndex],
        Parameters::ExportDrawingObjects => $atts[Parameters::ExportDrawingObjects],
        Parameters::ExportGridlines => $atts[Parameters::ExportGridlines],
        Parameters::WPP => 'true');
        return $params;
    }

    function GetImage ($atts) {
        $style = self::GetImageStyle($atts);
        $imgBytes = self::GetImageBytes($atts);
        return "<img ".$style." src='data:image/jpeg;base64,".$imgBytes."'/>";
    }
    function GetImageBytes ($atts) {
        $params = self::ExtractGetImageParams($atts);
        $output = SpreadsheetRequest::getPictures($params);
        if($output['status'] != 200){
            return "Error";
        } else{
            $imgJSON = $output['data'];
            $response = json_decode($imgJSON, true);
            return $response[0]['PictureBytes'];;
        }
    }
    function GetImageStyle($atts){
        $style = '';
        if($atts[Parameters::Width]<>'')
            $style = $style.Parameters::Width.":".$atts[Parameters::Width].";";
        if($atts[Parameters::Height]<>'')
            $style = $style." ".Parameters::Height.":".$atts[Parameters::Height]."\"";
        if($style<>'')
            return "style=\"".$style;
        return '';
    }
    function ExtractGetImageParams($atts){
        extract(shortcode_atts(array(
            Parameters::FileName                =>'',
            Parameters::SheetIndex              =>NULL,
            Parameters::SheetName               =>'',
            Parameters::Range                   =>'',
            Parameters::StartRowIndex           =>NULL,
            Parameters::StartColumnIndex        =>NULL,
            Parameters::EndRowIndex             =>NULL,
            Parameters::EndColumnIndex          =>NULL,
            Parameters::ObjectIndex        =>NULL,
            Parameters::Scale        =>NULL,
            Parameters::PictureType        =>PictureType::Picture), $atts));
        $params = array(
        Parameters::FileName => $atts[Parameters::FileName], 
        Parameters::SheetIndex => $atts[Parameters::SheetIndex], 
        Parameters::SheetName => $atts[Parameters::SheetName],
        Parameters::Range => $atts[Parameters::Range],
        Parameters::StartRowIndex => $atts[Parameters::StartRowIndex],
        Parameters::StartColumnIndex => $atts[Parameters::StartColumnIndex],
        Parameters::EndRowIndex => $atts[Parameters::EndRowIndex],
        Parameters::EndColumnIndex => $atts[Parameters::EndColumnIndex],
        Parameters::ObjectIndex => $atts[Parameters::ObjectIndex],
        Parameters::Scale => $atts[Parameters::Scale],
        Parameters::PictureType => $atts[Parameters::PictureType],
        Parameters::WPP => 'true');
        return $params;
    }
    function GetFileList ($size){
        $output = SpreadsheetRequest::getFilesList();
        //echo '<pre>'.print_r($output['data'],1).'</pre>';
        $response = json_decode($output['data'], true);
        $result = '<select class="filename" name="filename" size="'.$size.'" ';
        if($size == 1){
            $result = '<span>File Name: </span>'.result;
        }
        else{
            $result = $result.'style="height: 200px; width: 321px"';
        }
        $result = $result.'>';
        $counter = 0;
        foreach($response as $current){
            if($counter == 0 && $size == 1)
                $result = $result.'<option value="'.$current['Name'].'">';
            else $result = $result.'<option>';
            $result = $result.$current['Name'].'</option>';
            $counter = $counter + 1;
        }
        $result = $result.'</select><br />';
        return $result;
    }
}
class SpreadsheetCloudAPIExamples {
    public function GetAction ($atts) {
        update_option( PluginConst::ActionType, PluginConst::ExampleActionType );
        $response = SpreadsheetCloudAPIActions::GetAction($atts);
        update_option( PluginConst::ActionType, PluginConst::FullActionType );
        return $response;
    }
}
?>