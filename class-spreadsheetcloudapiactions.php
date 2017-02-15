<?php
class Spreadsheet_Cloud_API_Actions {
    public static function init() {
        $options = get_option( Plugin_Const::SCLAPI_OPTIONS );
        if ( empty( $options ) ) {
            $options = array(
                Plugin_Const::API_KEY  => '',
                Plugin_Const::SHOW_CREATE_EXAMPLE => 1,
                Plugin_Const::USER_FILE_LIST => '<select class="filename" name="filename" size="1"></select>',
                Plugin_Const::ACTION_TYPE => Plugin_Const::FULL_ACTION_TYPE,
            );
            update_option( Plugin_Const::SCLAPI_OPTIONS, $options ); 
        }
	}
    public static function plugin_activation() {
	}
    
    private static function bail_on_activation( $message, $deactivate = true ) {
		if ( $deactivate ) {
			$plugins = get_option( 'active_plugins' );
			$SpreadsheetCloudAPI = plugin_basename( SPREADSHEEETCLOUDAPI__PLUGIN_DIR . 'spreadsheetcloudapi.php' );
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
        $command = $atts[ Parameters::COMMAND ];
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
        $options = get_option( Plugin_Const::SCLAPI_OPTIONS );
        
        $options[ Plugin_Const::ACTION_TYPE ] = Plugin_Const::EXAMPLE_ACTION_TYPE;
        update_option( Plugin_Const::SCLAPI_OPTIONS, $options );

        $response = Spreadsheet_Cloud_API_Actions::GetAction( $atts );

        $options[ Plugin_Const::ACTION_TYPE ] = Plugin_Const::FULL_ACTION_TYPE;
        update_option( Plugin_Const::SCLAPI_OPTIONS, $options );

        return $response;
    }

    public function upload_file( $file ) {
        return Spreadsheet_Request::upload_file( $file );
    }
    public function download_file( $filename ) {
        $params = array( Parameters::FILE_NAME => $filename );
        $downloadresponse = Spreadsheet_Request::download_file( $params );
        return $downloadresponse;
    }
    public function delete_file( $filename ) {
        $params = array( Parameters::FILE_NAME => $filename );
        return Spreadsheet_Request::delete_file( $params );
    }
    public function rename_file( $filename, $newfilename ) {
        $params = array( Parameters::FILE_NAME => $filename,
        Parameters::NEW_FILE_NAME => $newfilename, );
        return Spreadsheet_Request::rename_file( $params );
    }

    function GetHTMLRange( $atts ) {
        $params = self::ExtractGetHTMLRangeParams( $atts );
        $output = Spreadsheet_Request::get_HTML( $params );
        if ( $output[ Plugin_Const::RESPONSE_STATUS ] != 200 ) {
            return "Error";
        } else
            return self::FixHTMLStyle( $output[ Plugin_Const::RESPONSE_DATA ] );
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
            Parameters::FILE_NAME                =>'',
            Parameters::SHEET_INDEX              =>NULL,
            Parameters::Range                   =>'',
            Parameters::SHEET_NAME               =>'',
            Parameters::START_ROW_INDEX           =>NULL,
            Parameters::START_COLUMN_INDEX        =>NULL,
            Parameters::EndRowIndex             =>NULL,
            Parameters::EndColumnIndex          =>NULL,
            Parameters::ExportDrawingObjects    =>'true',
            Parameters::ExportGridlines    =>'false'), $atts));
        $params = array(
        Parameters::FILE_NAME => $atts[ Parameters::FILE_NAME ],
        Parameters::SHEET_INDEX => $atts[ Parameters::SHEET_INDEX ],
        Parameters::Range => $atts[ Parameters::Range ],
        Parameters::SHEET_NAME => $atts[ Parameters::SHEET_NAME ],
        Parameters::START_ROW_INDEX => $atts[ Parameters::START_ROW_INDEX ],
        Parameters::START_COLUMN_INDEX => $atts[ Parameters::START_COLUMN_INDEX ],
        Parameters::EndRowIndex => $atts[ Parameters::EndRowIndex ],
        Parameters::EndColumnIndex => $atts[ Parameters::EndColumnIndex ],
        Parameters::ExportDrawingObjects => $atts[ Parameters::ExportDrawingObjects ],
        Parameters::ExportGridlines => $atts[ Parameters::ExportGridlines ],
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
        $output = Spreadsheet_Request::get_pictures( $params );
        if ( $output[ Plugin_Const::RESPONSE_STATUS ] != 200 ) {
            return "Error";
        } else{
            $imgJSON = $output[ Plugin_Const::RESPONSE_DATA ];
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
            Parameters::FILE_NAME                =>'',
            Parameters::SHEET_INDEX              =>NULL,
            Parameters::SHEET_NAME               =>'',
            Parameters::Range                   =>'',
            Parameters::START_ROW_INDEX           =>NULL,
            Parameters::START_COLUMN_INDEX        =>NULL,
            Parameters::EndRowIndex             =>NULL,
            Parameters::EndColumnIndex          =>NULL,
            Parameters::ObjectIndex             =>NULL,
            Parameters::Scale                   =>NULL,
            Parameters::Picture_Type             =>Picture_Type::Picture), $atts ) );
        $params = array(
        Parameters::FILE_NAME => $atts[ Parameters::FILE_NAME ], 
        Parameters::SHEET_INDEX => $atts[ Parameters::SHEET_INDEX ], 
        Parameters::SHEET_NAME => $atts[ Parameters::SHEET_NAME ],
        Parameters::Range => $atts[ Parameters::Range ],
        Parameters::START_ROW_INDEX => $atts[ Parameters::START_ROW_INDEX ],
        Parameters::START_COLUMN_INDEX => $atts[ Parameters::START_COLUMN_INDEX ],
        Parameters::EndRowIndex => $atts[ Parameters::EndRowIndex ],
        Parameters::EndColumnIndex => $atts[ Parameters::EndColumnIndex ],
        Parameters::ObjectIndex => $atts[ Parameters::ObjectIndex ],
        Parameters::Scale => $atts[ Parameters::Scale ],
        Parameters::Picture_Type => $atts[ Parameters::Picture_Type ],
        Parameters::WPP => 'true', );
        return $params;
    }
    public static function get_files_list( $size ) {
        $output = Spreadsheet_Request::get_files_list();
        $response = json_decode( $output[ Plugin_Const::RESPONSE_DATA ], true );
        $result = '<select class="filename" name="filename" size="'.$size.'" ';
        if ( $size == 1 )  {
            $result = '<span>File Name: </span>'.$result;
        }
        $baseconnected = $output[ Plugin_Const::RESPONSE_STATUS ] == 200;
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