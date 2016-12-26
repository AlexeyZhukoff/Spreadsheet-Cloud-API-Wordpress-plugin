<?php
function sclapi_insert_shortcode($plugin_array){
    $plugin_array['sclapi_insert_shortcode'] = WP_PLUGIN_URL . '/SpreadsheetCloudAPI/widget/insertshortcode.js';
    return $plugin_array;
}
 
function sclapi_add_button($buttons){
    array_push($buttons, "|", "sclapi_insert_shortcode");
    return $buttons;
}
 
function sclapi_custom_button(){
    if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
        return;
    if( get_user_option('rich_editing') == 'true'){
        add_filter('mce_external_plugins', 'sclapi_insert_shortcode');
        add_filter('mce_buttons', 'sclapi_add_button');
    }
}
?>