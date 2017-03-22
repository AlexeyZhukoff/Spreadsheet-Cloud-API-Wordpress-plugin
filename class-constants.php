<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

class Sclapi_Plugin_Const{
    const SHORTCODE_NAME = 'sclapi';
    const API_KEY = 'API_Key';
    const GET_NEW_API_KEY = 'getnewapikey';
    const RESPONSE_DATA = 'data';
    const RESPONSE_STATUS = 'status';
    const SCLAPI_OPTIONS = 'sclapi_options';
}
class Sclapi_Header_Messages{
    const OPTIONS_SAVED = 'Options have been saved';
    const SELECT_RENAME = 'Select a file to be renamed';
    const FILE_RENAMED = 'File <i>%1$s</i> has been renamed to <i>%2$s</i>.';
    const FILE_DOWNLOADED = 'File <i>%s</i> has been downloaded';
    const SELECT_DOWNLOAD = 'Select a file to be downloaded';
    const FILE_UPLOADED = 'File <i>%s</i> has been uploaded';
    const SELECT_UPLOAD = 'Select a file to be uploaded';
    const FILE_DELETED = 'File <i>%s</i> has been deleted';
    const SELECT_DELETE = 'Select a file to be deleted';
}
class Sclapi_Commands{
    const GET_HTML_RANGE = 'GetHTMLRange';
    const GET_IMAGE = 'GetImage';
    const GET_IMAGE_BYTES = 'GetImageBytes';
}
class Sclapi_Parameters{
    const COMMAND = 'command';
    const FILE_NAME = 'filename';
    const NEW_FILE_NAME = 'newfilename';
    const SHEET_INDEX = 'sheetindex';
    const SHEET_NAME = 'sheetname';
    const START_ROW_INDEX = 'startrowindex';
    const START_COLUMN_INDEX = 'startcolumnindex';
    const END_ROW_INDEX = 'endrowindex';
    const END_COLUMN_INDEX = 'endcolumnindex';
    const RANGE = 'range';
    const EXPORT_DRAWING_OBJECTS = 'exportdrawingobjects';
    const EXPORT_GRID_LINES = 'exportgridlines';
    const OBJECT_INDEX = 'objectindex';
    const SCALE = 'scale';
    const PICTURE_TYPE = 'picturetype';
    const HEIGHT = 'height';
    const WIDTH = 'width';
    const WPP = 'wpp';
}
class Sclapi_Picture_Type{
    const PICTURE = 'picture';
    const CHART = 'chart';
    const SHAPE = 'shape';
    const CONNECTION_SHAPE = 'connectionshape';
    const GROUP_SHAPE = 'groupshape';
}
class Sclapi_File_Operations{
    const UPLOAD = 'Upload';
    const DELETE = 'Delete';
    const RENAME = 'Rename';
    const DOWNLOAD = 'Download';
}
?>

