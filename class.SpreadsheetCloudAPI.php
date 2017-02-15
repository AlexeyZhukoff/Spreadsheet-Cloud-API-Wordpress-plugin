<?php
class SpreadsheetCloudAPIActions {
    public static function init() {
        $options = get_option( PluginConst::SclapiOptions );
        if ( empty( $options ) ) {
            $options = array(
                PluginConst::APIKey  => '',
                PluginConst::ShowCreateExample => 1,
                PluginConst::UserFileList => '<select class="filename" name="filename" size="1"></select>',
                PluginConst::ActionType => PluginConst::FullActionType,
            );
            update_option( PluginConst::SclapiOptions, $options ); 
        }
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
    public function GetAction ( $atts ) {
        $command = $atts[Parameters::Command];
        switch ( $command ) {
            case Commands::GetHtmlRange:
                $response = self::GetHTMLRange( $atts );
                break;
            case Commands::GetImage:
                $response = self::GetImage( $atts );
                break;
            case Commands::GetImageBytes:
                $response = self::GetImageBytes( $atts );
                break;
            default:
                $response = 'Method error!';
                break;
        };
        return $response;
    }
    public function GetExampleAction ( $atts ) {
        $options = get_option( PluginConst::SclapiOptions );
        
        $options[ PluginConst::ActionType ] = PluginConst::ExampleActionType;
        update_option( PluginConst::SclapiOptions, $options );

        $response = SpreadsheetCloudAPIActions::GetAction( $atts );

        $options[ PluginConst::ActionType ] = PluginConst::FullActionType;
        update_option( PluginConst::SclapiOptions, $options );

        return $response;
    }

    public function UploadFile( $file ) {
        return SpreadsheetRequest::uploadFile( $file );
    }
    public function DownloadFile( $filename ) {
        $params = array( Parameters::FileName => $filename );
        $downloadresponse = SpreadsheetRequest::downloadfile( $params );
        return $downloadresponse;
    }
    public function DeleteFile( $filename ) {
        $params = array( Parameters::FileName => $filename );
        return SpreadsheetRequest::deletefile( $params );
    }
    public function RenameFile( $filename, $newfilename ) {
        $params = array( Parameters::FileName => $filename,
        Parameters::NewFileName => $newfilename, );
        return SpreadsheetRequest::renamefile( $params );
    }

    function GetHTMLRange( $atts ) {
        $params = self::ExtractGetHTMLRangeParams( $atts );
        $output = SpreadsheetRequest::getHtml( $params );
        if ( $output[ PluginConst::ResponseStatus ] != 200 ) {
            return "Error";
        } else
            return self::FixHTMLStyle( $output[ PluginConst::ResponseData ] );
    }
    function FixHTMLStyle( $HtmlCode ) {
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
    function ExtractGetHTMLRangeParams( $atts ) {
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

    function GetImage( $atts ) {
        $style = self::GetImageStyle( $atts );
        $imgBytes = self::GetImageBytes( $atts );
        return "<img ".$style." src='data:image/jpeg;base64,".$imgBytes."'/>";
    }
    function GetImageBytes( $atts ) {
        $params = self::ExtractGetImageParams( $atts );
        $output = SpreadsheetRequest::getPictures( $params );
        if ( $output[ PluginConst::ResponseStatus ] != 200 ) {
            return "Error";
        } else{
            $imgJSON = $output[ PluginConst::ResponseData ];
            $response = json_decode( $imgJSON, true );
            return $response[0]['PictureBytes'];;
        }
    }
    function GetImageStyle( $atts ) {
        $style = '';
        if ( $atts[Parameters::Width]<>'' )
            $style = $style.Parameters::Width.":".$atts[ Parameters::Width ].";";
        if ( $atts[ Parameters::Height ]<>'' )
            $style = $style." ".Parameters::Height.":".$atts[ Parameters::Height ]."\"";
        if ( $style<>'' )
            return "style=\"".$style;
        return '';
    }
    function ExtractGetImageParams( $atts ) {
        extract(shortcode_atts( array(
            Parameters::FileName                =>'',
            Parameters::SheetIndex              =>NULL,
            Parameters::SheetName               =>'',
            Parameters::Range                   =>'',
            Parameters::StartRowIndex           =>NULL,
            Parameters::StartColumnIndex        =>NULL,
            Parameters::EndRowIndex             =>NULL,
            Parameters::EndColumnIndex          =>NULL,
            Parameters::ObjectIndex             =>NULL,
            Parameters::Scale                   =>NULL,
            Parameters::PictureType             =>PictureType::Picture), $atts ) );
        $params = array(
        Parameters::FileName => $atts[ Parameters::FileName ], 
        Parameters::SheetIndex => $atts[ Parameters::SheetIndex ], 
        Parameters::SheetName => $atts[ Parameters::SheetName ],
        Parameters::Range => $atts[ Parameters::Range ],
        Parameters::StartRowIndex => $atts[ Parameters::StartRowIndex ],
        Parameters::StartColumnIndex => $atts[ Parameters::StartColumnIndex ],
        Parameters::EndRowIndex => $atts[ Parameters::EndRowIndex ],
        Parameters::EndColumnIndex => $atts[ Parameters::EndColumnIndex ],
        Parameters::ObjectIndex => $atts[ Parameters::ObjectIndex ],
        Parameters::Scale => $atts[ Parameters::Scale ],
        Parameters::PictureType => $atts[ Parameters::PictureType ],
        Parameters::WPP => 'true', );
        return $params;
    }
    public static function GetFileList( $size ) {
        $output = SpreadsheetRequest::getFilesList();
        $response = json_decode( $output[ PluginConst::ResponseData ], true );
        $result = '<select class="filename" name="filename" size="'.$size.'" ';
        if ( $size == 1 )  {
            $result = '<span>File Name: </span>'.$result;
        }
        $baseconnected = $output[ PluginConst::ResponseStatus ] == 200;
        if ( ! $baseconnected ) {
            $result = $result.'disabled="disabled"';
        }
        $result = $result.'>';
        if ( $baseconnected ) {
            $counter = 0;
            foreach( $response as $current ) {
                if ( $counter == 0 && $size == 1 ) {
                    $result = $result.'<option value="'.$current['Name'].'">';
                }
                else {
                    $result = $result.'<option>';
                }
                $result = $result.$current['Name'].'</option>';
                $counter = $counter + 1;
            }
        } else {
            $result = $result.'<option>Sorry, there is a database connection problem.</option><option>Please try again shortly.</option>';
        }
        $result = $result.'</select>';
        return $result;
    }
}
?>