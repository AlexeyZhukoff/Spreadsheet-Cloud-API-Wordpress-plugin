<?php
 /**
 * @package SpreadsheetCloudAPI
 * @version 1.0
 */
/*
Plugin Name: SpreadsheetCloudAPI
Plugin URI: http://sclapi.com
Description: SpreadsheetCloudAPI (Sclapi) plugin is an easy-to-use tool for using your spreadsheet files in WordPress blogs. To get started with a plugin, go to the Sclapi plugin options and enter an <a target="_blank" href="http://spreadsheetadmin.azurewebsites.net/">existing</a> or generate a new API key.
Author: 
Version: 1.0
License: GPLv2 or later
Text Domain: sclapi
*/
/*  Copyright 2017 SpreadsheetCloudAPI Ltd. (email: scloudapi@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define( 'SPREADSHEEETCLOUDAPI__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once( SPREADSHEEETCLOUDAPI__PLUGIN_DIR . 'class-spreadsheetcloudapiactions.php' );
require_once( SPREADSHEEETCLOUDAPI__PLUGIN_DIR . 'class-constants.php' );
require_once( SPREADSHEEETCLOUDAPI__PLUGIN_DIR . 'class-spreadsheetrequest.php' );
require_once( SPREADSHEEETCLOUDAPI__PLUGIN_DIR . '\options\options.php' );
require_once( SPREADSHEEETCLOUDAPI__PLUGIN_DIR . '\widget\generator.php' );


register_activation_hook( __FILE__, array( 'Spreadsheet_Cloud_API_Actions', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'Spreadsheet_Cloud_API_Actions', 'plugin_deactivation' ) );

add_action( 'init', array( 'Spreadsheet_Cloud_API_Actions', 'init' ) );
add_action( 'admin_menu', 'mt_add_pages' );
add_action( 'init', 'sclapi_custom_button' );

add_shortcode( Plugin_Const::SHORTCODE_NAME, array( 'Spreadsheet_Cloud_API_Actions', 'GetAction' ) );
add_shortcode( Plugin_Const::EXAMPLE_SHORTCODE_NAME, array( 'Spreadsheet_Cloud_API_Actions', 'GetExampleAction' ) );

?>
