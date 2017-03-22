<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>SpreadsheetCloudAPI shortcode generator</title>
        <?php 
            wp_print_scripts('sclapi_generatorform_script'); 
            wp_print_styles('sclapi_generatorform_style'); 
        ?>
    </head>
    <body>
        <form id="sclapi-generator" class="sclapi-generator">
            <fieldset class="sclapi-container">
                <legend class="parameters-header">Shortcode parameters</legend>
                <input type="hidden" class="shortcode" name="shortcode" value="sclapi ">
                <span>Command:</span><select class="command" name="command"><option value="GetHTMLRange">GetHTMLRange</option><option>GetImage</option><option>GetImageBytes</option></select><br />
                <span>File Name:</span><?php echo Spreadsheet_Cloud_API_Actions::get_files_list(1); ?><br />
                <span class="sheet-span">Sheet Index or Sheet Name: </span><input type="text" class="sheet" name="sheet" placeholder="0 or Sheet1" value="0"/><br />
                <span>Range:</span><input type="text" class="range" name="range" placeholder="A1:B2 or empty for a used range"/><br />
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