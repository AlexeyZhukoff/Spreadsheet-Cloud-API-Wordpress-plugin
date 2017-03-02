<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>SpreadsheetCloudAPI shortcode generator</title>
        <script type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/jquery/jquery.js"></script>
        <script type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
        <script type="text/javascript">
            jQuery(function ($) {
                $('body').on('change', '.command', commandChange);

                $(document).ready(function () {
                    commandChangeCore($('.command'));
                    $('#insert-button').click(function () {
                        var shortcode = '[';
                        var imagemode = false;
                        $('form .sclapi-container').find('input:not(:disabled), select:not(:disabled)').each(function () {
                            var attName = $(this).attr('name'), attValue = $(this).val(), attResult = '';
                            if (attName === 'command') {
                                imagemode = attValue !== 'GetHTMLRange';
                            }
                            if (attName === 'exportgridlines') {
                                if ($(this).prop('checked') && !imagemode) {
                                    attValue = 'true';
                                }
                                else {
                                    attValue = undefined;
                                }
                            }
                            if (attName === 'objectindex' && !imagemode) {
                                attValue = undefined;
                            }
                            if (attValue != undefined && attValue.length != 0 && attName != 'shortcode') {
                                attResult = attName + '="' + attValue + '" ';
                            }
                            if (attName === 'shortcode') {
                                if (shortcode.length > 1) {
                                    shortcode += ']<br />[';
                                }
                                attResult = attValue;
                            }
                            shortcode += attResult;
                        })
                        shortcode += ']';

                        tinyMCEPopup.editor.execCommand('mceInsertContent', false, shortcode);
                        tinyMCEPopup.close();
                    })

                    $('#cancel-button').click(function () {
                        tinyMCEPopup.close();
                    })
                })
                function commandChange() {
                    var command = $(this);
                    commandChangeCore(command);
                }
                function commandChangeCore(command) {
                    disableCommandParameters(command.closest('fieldset'), command.val() === 'GetHTMLRange');
                }
                function disableCommandParameters(form, disabled) {
                    if (disabled) {
                        form.find('.export-gridlines').attr('style', '');
                        form.find('.htm-span').attr('style', '');
                        form.find('.object-index').attr('style', 'display: none');
                        form.find('.picspan').attr('style', 'display: none');
                    } else {
                        form.find('.export-gridlines').attr('style', 'display: none');
                        form.find('.htm-span').attr('style', 'display: none');
                        form.find('.object-index').attr('style', '');
                        form.find('.picspan').attr('style', '');
                    }
                }
            })
        </script>
        <style>
            fieldset {
                margin: 0 0 10px 0;
                height: auto;
                line-height: 30px;
                overflow: hidden;
                font-size: 10pt;
            }
            .parameters-header {
                font-size: 10pt;
            }
            fieldset hr {
                border: none;
                height: 1px;
                background: #dfdfdf;
            }
            fieldset input, select {
                float: right;
                font-size: 10pt;
                width: 210px;
                margin-top: 5px;
            }
            .export-gridlines {
                float: left;
                width: 20px;
                margin-top: 10px;
            }
            .htm-span {
                float: left;
                margin-left: 10px;
            }

            input.sheet-index, input.sheet-name {
                width: 80px;
                float: left;
                margin-left: 7px;
                margin-right: 5px;
            }
            input.sheet-name {
                width: 88px;
                margin-right: 0;
            }
            span.sheet-name-span {
                float: left;
                width: 110px;
                margin-left: 5px;
            }
            span.sheet-index-span {
                float: left;
                width: 90px;
            }
            fieldset span {
                float: left;
                font-size: 10pt;
                width: 125px;
            }
            #cancel-button {
                font-size: 10pt;
                background: #cfcfcf;
                box-shadow: inset 0 1px 0 rgba(255,255,255,0.6);
                border-color: #b0b0b0;
                width: auto;
            }
            #cancel-button:hover {
                background: #a4a4a4;
                color: #fff;
            }
            #insert-button {
                font-size: 10pt;
                background: #2ea2cc;
                box-shadow: inset 0 1px 0 rgba(120,200,230,0.6);
                border-color: #0074a2;
                color: #fff;
                width: auto;
                margin-right: 6px;
            }
            #insert-button:hover {
                background: #1e8cbe;
            }
        </style>
    </head>
    <body>
        <form id="sclapi-generator" class="sclapi-generator">
            <fieldset class="sclapi-container">
                <legend class="parameters-header">Shortcode parameters</legend>
                <input type="hidden" class="shortcode" name="shortcode" value="sclapi ">
                <span>Command:</span><select class="command" name="command"><option value="GetHTMLRange">GetHTMLRange</option><option>GetImage</option><option>GetImageBytes</option></select><br />
                <span>File Name:</span><?php echo get_option( 'sclapi_options' )['userfileslist']; ?><br />
                <span class="sheet-index-span">Sheet Index:  </span><input type="number" class="sheet-index" name="sheetindex" value="0" min="0" /><span class="sheet-name-span"> or Sheet Name:  </span><input type="text" class="sheet-name" name="sheetname" placeholder="Sheet1"/><br />
                <span>Range:</span><input type="text" class="range" name="range" placeholder="A1:B2"/><br />
                <hr />
                <input type="checkbox" class="export-gridlines" name="exportgridlines" /><span class="htm-span">Show Grid Lines</span>
                <span class="picspan" style="display: none">Object Index:</span><input type="number" class="object-index" name="objectindex" value="0" min="0" style="display: none" /><br />
            </fieldset>
            <div>
                <input type="button" class="button" id="insert-button" name="insert" title="Generates a shortcode and inserts it into your post" value="Insert" style="float: left" />
                <input type="button" class="button" id="cancel-button" name="cancel" value="Cancel" style="float: left" />
            </div>
        </form>
    </body>
</html>
<?php die(); ?>