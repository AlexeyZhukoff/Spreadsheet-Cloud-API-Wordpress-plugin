<?php
class Commands{
    const ShortcodeName = "sclapi";
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

