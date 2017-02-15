<?php
class Plugin_Const{
    const SHORTCODE_NAME = 'sclapi';
    const EXAMPLE_SHORTCODE_NAME = 'sclapiexample';
    const API_KEY = 'API_Key';
    const GET_NEW_API_KEY = 'getnewapikey';
    const SHOW_CREATE_EXAMPLE = 'showcreateexample';
    const USER_FILE_LIST = 'userfileslist';
    const ACTION_TYPE = 'actiontype';
    const FULL_ACTION_TYPE = 'action';
    const EXAMPLE_ACTION_TYPE = 'example';
    const RESPONSE_DATA = 'data';
    const RESPONSE_STATUS = 'status';
    const SCLAPI_OPTIONS = 'sclapi_options';
}
class Header_Messages{
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
class Commands{
    const GetHtmlRange = 'GetHTMLRange';
    const GetImage = 'GetImage';
    const GetImageBytes = 'GetImageBytes';
}
class Parameters{
    const COMMAND = 'command';
    const FILE_NAME = 'filename';
    const NEW_FILE_NAME = 'newfilename';
    const SHEET_INDEX = 'sheetindex';
    const SHEET_NAME = 'sheetname';
    const START_ROW_INDEX = 'startrowindex';
    const START_COLUMN_INDEX = 'startcolumnindex';
    const EndRowIndex = 'endrowindex';
    const EndColumnIndex = 'endcolumnindex';
    const Range = 'range';
    const ExportDrawingObjects = 'exportdrawingobjects';
    const ExportGridlines = 'exportgridlines';
    const ObjectIndex = 'objectindex';
    const Scale = 'scale';
    const PictureType = 'picturetype';
    const Height = 'height';
    const Width = 'width';
    const WPP = 'wpp';
}
class Picture_Type{
    const Picture = 'picture';
    const Chart = 'chart';
    const Shape = 'shape';
    const ConnectionShape = 'connectionshape';
    const GroupShape = 'groupshape';
}
class File_Operations{
    const Upload = 'Upload';
    const Delete = 'Delete';
    const Rename = 'Rename';
    const Download = 'Download';
    const ContinueDownload = 'continuedownload';
}
?>

