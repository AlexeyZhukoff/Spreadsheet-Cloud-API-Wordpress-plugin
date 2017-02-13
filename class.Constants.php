<?php
class PluginConst{
    const ShortcodeName = "sclapi";
    const ExampleShortcodeName = "sclapiexample";
    const APIKey = 'API_Key';
    const GetNewAPIKey = 'getnewapikey';
    const ShowCreateExample = 'showcreateexample';
    const UserFileList = 'userfileslist';
    const ActionType = 'actiontype';
    const FullActionType = 'action';
    const ExampleActionType = 'example';
    const ResponseData = 'data';
    const ResponseStatus = 'status';
}
class HeaderMessages{
    const OptionsSaved = 'Options have been saved';
    const NoSelectRename = 'Select a file to be renamed';
    const FileRenamed = 'File <i>%1$s</i> has been renamed to <i>%2$s</i>.';
    const FileDownloaded = 'File <i>%s</i> has been downloaded';
    const NoSelectDownload = 'Select a file to be downloaded';
    const FileUploaded = 'File <i>%s</i> has been uploaded';
    const NoSelectUpload = 'Select a file to be uploaded';
    const FileDeleted = 'File <i>%s</i> has been deleted';
    const NoSelectDelete = 'Select a file to be deleted';
}
class Commands{
    const GetHtmlRange = "GetHTMLRange";
    const GetImage = "GetImage";
    const GetImageBytes = "GetImageBytes";
}
class Parameters{
    const Command = "command";
    const FileName = "filename";
    const NewFileName = "newfilename";
    const SheetIndex = "sheetindex";
    const SheetName = "sheetname";
    const StartRowIndex = "startrowindex";
    const StartColumnIndex = "startcolumnindex";
    const EndRowIndex = "endrowindex";
    const EndColumnIndex = "endcolumnindex";
    const Range = "range";
    const ExportDrawingObjects = "exportdrawingobjects";
    const ExportGridlines = "exportgridlines";
    const ObjectIndex = "objectindex";
    const Scale = "scale";
    const PictureType = "picturetype";
    const Height = "height";
    const Width = "width";
    const WPP = "wpp";
}
class PictureType{
    const Picture = "picture";
    const Chart = "chart";
    const Shape = "shape";
    const ConnectionShape = "connectionshape";
    const GroupShape = "groupshape";
}
class FileOperations{
    const Upload = "Upload";
    const Delete = "Delete";
    const Rename = "Rename";
    const Download = "Download";
    const ContinueDownload = "continuedownload";
}
?>

