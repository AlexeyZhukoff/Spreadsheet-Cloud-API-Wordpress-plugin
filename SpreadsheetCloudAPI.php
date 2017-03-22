<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

/*
Plugin Name: SpreadsheetCloudAPI
Plugin URI: http://wordpress.org/plugins/SpreadsheetCloudAPI
Description: SpreadsheetCloudAPI (Sclapi) plugin is an easy-to-use tool for using your spreadsheet files in WordPress blogs. To get started with a plugin, go to the Sclapi plugin options and enter an <a target="_blank" href="http://spreadsheetadmin.azurewebsites.net/">existing</a> or generate a new API key.
Author: SpreadsheetCloudAPI Inc.
Author URI: http://spreadsheetadmin.azurewebsites.net/
Version: 1.0
License: GPLv2 or later
*/
/*
    Copyright 2017 SpreadsheetCloudAPI Inc. (email: scloudapi@gmail.com)

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


add_action( 'admin_init', array( 'Spreadsheet_Cloud_API_Actions', 'admin_init' ) );
add_action( 'admin_menu', 'sclapi_mt_add_pages' );
add_action( 'init', 'sclapi_custom_button' );
add_action( 'wp_ajax_sclapigeneratewindow', 'sclapi_generate_ajax' );
add_action( 'admin_head', 'sclapi_generate_admin_ajax' );
add_shortcode( Sclapi_Plugin_Const::SHORTCODE_NAME, array( 'Spreadsheet_Cloud_API_Actions', 'get_action' ) );
?>
