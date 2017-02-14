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


define( 'SPREADSHEEETCLOUDAPI__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once( SPREADSHEEETCLOUDAPI__PLUGIN_DIR . 'class.SpreadsheetCloudAPI.php' );
require_once( SPREADSHEEETCLOUDAPI__PLUGIN_DIR . 'class.Constants.php' );
require_once( SPREADSHEEETCLOUDAPI__PLUGIN_DIR . 'SpreadsheetRequest.php' );
require_once( SPREADSHEEETCLOUDAPI__PLUGIN_DIR . '\options\Options.php' );
require_once( SPREADSHEEETCLOUDAPI__PLUGIN_DIR . '\widget\generator.php' );


register_activation_hook( __FILE__, array( 'SpreadsheetCloudAPIActions', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'SpreadsheetCloudAPIActions', 'plugin_deactivation' ) );

add_action('init', array( 'SpreadsheetCloudAPIActions', 'init' ) );
add_action('admin_menu', 'mt_add_pages');
add_action('init', 'sclapi_custom_button');

add_shortcode(PluginConst::ShortcodeName, array('SpreadsheetCloudAPIActions', 'GetAction'));
add_shortcode(PluginConst::ExampleShortcodeName, array('SpreadsheetCloudAPIActions', 'GetExampleAction'));

?>
