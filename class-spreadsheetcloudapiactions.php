<?php
class Spreadsheet_Cloud_API_Actions {
    public static function init() {
        $options = get_option( Plugin_Const::SCLAPI_OPTIONS );
        if ( empty( $options ) ) {
            $options = array(
                Plugin_Const::API_KEY  => '',
                Plugin_Const::USER_FILE_LIST => '<select class="filename" name="filename" size="1"></select>',
            );
            update_option( Plugin_Const::SCLAPI_OPTIONS, $options ); 
        }
	}
    public static function plugin_activation() {
	}
    
    private static function bail_on_activation( $message, $deactivate = true ) {
		if ( $deactivate ) {
			$plugins = get_option( 'active_plugins' );
			$spreadsheet_cloud_API = plugin_basename( SPREADSHEEETCLOUDAPI__PLUGIN_DIR . 'spreadsheetcloudapi.php' );
			$update  = false;
			foreach ( $plugins as $i => $plugin ) {
				if ( $plugin === $spreadsheet_cloud_API ) {
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
    public function get_action ( $atts ) {
        $command = $atts[ Parameters::COMMAND ];
        switch ( $command ) {
            case Commands::GET_HTML_RANGE:
                $response = self::get_HTML_range( $atts );
                break;
            case Commands::GET_IMAGE:
                $response = self::get_image( $atts );
                break;
            case Commands::GET_IMAGE_BYTES:
                $response = self::get_image_bytes( $atts );
                break;
            default:
                $response = 'Method error!';
                break;
        };
        return $response;
    }
    public function upload_file( $file ) {
        return Spreadsheet_Request::upload_file( $file );
    }
    public function download_file( $file_name ) {
        $params = array( Parameters::FILE_NAME => $file_name );
        $download_response = Spreadsheet_Request::download_file( $params );
        return $download_response;
    }
    public function delete_file( $file_name ) {
        $params = array( Parameters::FILE_NAME => $file_name );
        return Spreadsheet_Request::delete_file( $params );
    }
    public function rename_file( $file_name, $new_file_name ) {
        $params = array( Parameters::FILE_NAME => $file_name,
        Parameters::NEW_FILE_NAME => $new_file_name, );
        return Spreadsheet_Request::rename_file( $params );
    }

    function get_HTML_range( $atts ) {
        $params = self::extract_get_HTML_range_params( $atts );
        $output = Spreadsheet_Request::get_HTML( $params );
        if ( $output[ Plugin_Const::RESPONSE_STATUS ] != 200 ) {
            return "Error";
        } else
            return self::fix_HTML_style( $output[ Plugin_Const::RESPONSE_DATA ] );
    }
    function fix_HTML_style( $HTML_code ) {
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
        return $style."initial-style \"".">".$HTML_code."</div>";
    }
    function extract_get_HTML_range_params( $atts ) {
        $params = shortcode_atts(array(
            Parameters::FILE_NAME                =>'',
            Parameters::SHEET_INDEX              =>NULL,
            Parameters::RANGE                   =>'',
            Parameters::SHEET_NAME               =>'',
            Parameters::START_ROW_INDEX           =>NULL,
            Parameters::START_COLUMN_INDEX        =>NULL,
            Parameters::END_ROW_INDEX             =>NULL,
            Parameters::END_COLUMN_INDEX          =>NULL,
            Parameters::EXPORT_DRAWING_OBJECTS    =>'true',
            Parameters::EXPORT_GRID_LINES    =>'false',
            Parameters::WPP => 'true',
            ), $atts);
        return $params;
    }

    function get_image( $atts ) {
        $style = self::get_image_style( $atts );
        $imgBytes = self::get_image_bytes( $atts );
        return "<img ".$style." src='data:image/jpeg;base64,".$imgBytes."' />";
    }
    function get_image_bytes( $atts ) {
        $params = self::extract_get_image_parameters( $atts );
        $output = Spreadsheet_Request::get_pictures( $params );
        if ( $output[ Plugin_Const::RESPONSE_STATUS ] != 200 ) {
            return "Error";
        } else{
            $imgJSON = $output[ Plugin_Const::RESPONSE_DATA ];
            $response = json_decode( $imgJSON, true );
            return $response[0]['PictureBytes'];
        }
    }
    function get_image_style( $atts ) {
        $style = '';
        if ( $atts[Parameters::WIDTH]<>'' )
            $style = $style.Parameters::WIDTH.":".$atts[ Parameters::WIDTH ].";";
        if ( $atts[ Parameters::HEIGHT ]<>'' )
            $style = $style." ".Parameters::HEIGHT.":".$atts[ Parameters::HEIGHT ]."\"";
        if ( $style<>'' )
            return "style=\"".$style;
        return '';
    }
    function extract_get_image_parameters( $atts ) {
        $params = shortcode_atts( array(
            Parameters::FILE_NAME                =>'',
            Parameters::SCALE                   =>0.1,
            Parameters::SHEET_INDEX              =>NULL,
            Parameters::SHEET_NAME               =>'',
            Parameters::RANGE                   =>'',
            Parameters::START_ROW_INDEX           =>NULL,
            Parameters::START_COLUMN_INDEX        =>NULL,
            Parameters::END_ROW_INDEX             =>NULL,
            Parameters::END_COLUMN_INDEX          =>NULL,
            Parameters::OBJECT_INDEX             =>NULL,
            Parameters::HEIGHT                   =>NULL,
            Parameters::PICTURE_TYPE             =>Picture_Type::PICTURE,
            Parameters::WPP                      =>'true',
            ), $atts );
        return $params;
    }
    public static function get_files_list( $size ) {
        $output = Spreadsheet_Request::get_files_list();
        $response = json_decode( $output[ Plugin_Const::RESPONSE_DATA ], true );
        $result = '<select class="filename" name="filename" size="'.$size.'" ';
        $base_connected = $output[ Plugin_Const::RESPONSE_STATUS ] == 200;
        if ( ! $base_connected ) {
            $result = $result.'disabled="disabled"';
        }
        $result = $result.'>';
        if ( $base_connected ) {
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