jQuery(function ($) {
    $.get("createexample.php", function (createexample) {
        if (createexample == 1) {
            $("#create_example").attr('style', 'float:right');
        }
        else {
            $("#create_example").attr('style', 'display:none');
        }
    })

    $.get("getfilelist.php", function (htmlFilesList) {
        $(".filelist").replaceWith(htmlFilesList);
        CommandChangeCore($(".command"));
    })

    $('body').on('change', '.command', commandChange);

    $(document).ready(function () {
        $("#insert_button").click(function () {
            var shortcode = "[";
            $("form .sclapi-container").find("input:not(:disabled), select:not(:disabled)").each(function () {
                var att_name = $(this).attr("name"), att_value = $(this).val(), att_result = '';
                if (att_name == "exportgridlines") {
                    if ($(this).prop("checked"))
                        att_value = "true";
                    else att_value = undefined;
                }
                if (att_value != undefined && att_value.length != 0 && att_name != "shortcode") {
                    att_result = att_name + '="' + att_value + '" ';
                }
                if (att_name == "shortcode") {
                    if (shortcode.length > 1) {
                        shortcode += "]<br />[";
                    }
                    att_result = att_value;
                }
                shortcode += att_result;
            })
            shortcode += ']';

            tinyMCEPopup.editor.execCommand('mceInsertContent', false, shortcode);
            tinyMCEPopup.close();
        })

        $("#create_example").click(function () {
            $(".parametersheader").text("Example shortcode parameters");
            $(".shortcode").attr("value", "sclapiexample ");
            $(".filename").replaceWith('<select class="examplefilename" name="filename" size="1"><option value="example.xlsx">example.xlsx</option></select>');
            $(".range").attr("value", "A1:E7");
            $(".sheetindex").attr("readonly", true);
            $(".sheetname").attr("readonly", true);
            exampleCommandChangeCore($(".command"));
            $('body').on('change', '.command', exampleCommandChange);
        })

        $("#cancel_button").click(function () {
            tinyMCEPopup.close();
        })
    })
    function commandChange() {
        var command = $(this);
        CommandChangeCore(command);
    }
    function CommandChangeCore(command) {
        disableCommandParameters(command.closest("fieldset"), command.val() == "GetHTMLRange");
    }
    function disableCommandParameters(form, disabled) {
        form.find(".exportgridlines").attr('disabled', !disabled);
        form.find(".objectindex").attr('disabled', disabled);
        form.find(".picturetype").attr('disabled', disabled);
        form.find(".height").attr('disabled', disabled);
        form.find(".width").attr('disabled', disabled);
    }
    function exampleCommandChange() {
        exampleCommandChangeCore($(this));
    }
    function exampleCommandChangeCore(command) {
        CommandChangeCore(command);
        var sheetindex = command.closest("fieldset").find(".sheetindex");
        if (command.val() == "GetHTMLRange") {
            sheetindex.val(0);
        }
        else {
            sheetindex.val(1);
        }
    }
})