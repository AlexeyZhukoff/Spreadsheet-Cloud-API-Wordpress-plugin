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
    const OptionsSaved = 'Options saved.';
    const NoSelectRename = 'Please select file to rename.';
    const FileRenamed = 'File <i>%1$s</i> renamed to <i>%2$s</i>.';
    const FileDownloaded = 'File <i>%s</i> is downloaded.';
    const NoSelectDownload = 'Please select file to download.';
    const FileUploaded = 'File <i>%s</i> is uploaded.';
    const NoSelectUpload = 'Please select file to upload.';
    const FileDeleted = 'File <i>%s</i> is deleted.';
    const NoSelectDelete = 'Please select file to delete.';
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

