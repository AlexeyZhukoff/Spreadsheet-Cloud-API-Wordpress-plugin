jQuery(function ($) {
    $.get("createexample.php", function (createexample) {
        if (createexample == 1) {
            $("#create_example").attr('style', 'float:right');
        }
        else {
            $("#create_example").attr('style', 'display:none');
        }
    });
    $.get("getfilelist.php", function (htmlFilesList) {
        $(".filelist").append(htmlFilesList);
        output = $(".sclapi-container-template").find("fieldset").clone(false);
        output.attr("class", "last");
        $(".sclapi-container").append(output);
        CommandChangeCore($(".sclapi-container").find("fieldset").find(".command"));
    });
    $('body').on('change', '.range', rangeChange);
    $('body').on('change', '.command', commandChange);
    $('body').on('change', '.examplecommand', exampleCommandChange);
    $('body').on('change', '.last', fieldsetChange);

    $(document).ready(function () 
    {
        $("#insert_button").click(function () {
            var shortcode = "[";
            $("form .sclapi-container").find("input:not(:disabled), select:not(:disabled)").each(function () {
                var att_name = $(this).attr("name"),
                               att_value = $(this).val(),
                               att_result = '';
                if (att_name == "exportgridlines") {
                    if ($(this).prop("checked"))
                        att_value = "true";
                    else att_value = undefined;
                }
                if (att_value != undefined && att_value.length != 0 && att_name != "shortcode")
                    att_result = att_name + '="' + att_value + '" ';
                if (att_name == "shortcode") {
                    if (shortcode.length > 1) {
                        shortcode += "]<br />["
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
            output = $(".example-container-template").find("fieldset").clone(false);
            replacement = $(".sclapi-container").find(".last");
            if (replacement.length != 0) {
                replacement.replaceWith(output);
            }
            else {
                $(".sclapi-container").append(output);
            }
            CommandChangeCore($(".sclapi-container").find("fieldset").find(".examplecommand"));
        })

        $("#add_shortcode").click(function () {
            output = $(".sclapi-container-template").find("fieldset").clone(false);
            output.attr("class", "last");
            $(".sclapi-container").find(".last").attr("class", '');
            $(".sclapi-container").append(output);
            $('body').on('change', '.last', fieldsetChange);
        })

        $("#cancel_button").click(function () {
            tinyMCEPopup.close();
        })
    })
    function rangeChange() {
        var range = $(this);
        var form = range.closest("fieldset");
        disableIndexParameters(form, range.val().length > 0);
    }
    function commandChange() {
        var command = $(this);
        CommandChangeCore(command);
    }
    function CommandChangeCore(command) {
        disableCommandParameters(command.closest("fieldset"), command.val() == "GetHTMLRange");
    }
    function fieldsetChange() {
        $(".sclapi-container").find(".last").attr("class", '');
    }
    function disableCommandParameters(form, disabled) {
        form.find(".exportgridlines").attr('disabled', !disabled);
        form.find(".imggrouppar").find("input, select").each(function () {
            $(this).attr('disabled', disabled);
        })
        if (disabled == true) {
            form.find(".imggrouppar").attr('style', 'display:none');
            form.find(".htmlgrouppar").attr('style', '');
        }
        else {
            form.find(".imggrouppar").attr('style', '');
            form.find(".htmlgrouppar").attr('style', 'display:none');
        }
    }
    function disableIndexParameters(form, disabled) {
        form.find(".startrowindex").attr('disabled', disabled);
        form.find(".endrowindex").attr('disabled', disabled);
        form.find(".startcolumnindex").attr('disabled', disabled);
        form.find(".endcolumnindex").attr('disabled', disabled);
    }
    function exampleCommandChange() {
        var command = $(this);
        CommandChangeCore(command);
        var sheetindex = command.closest("fieldset").find(".sheet").find(".sheetindex");
        if (command.val() == "GetHTMLRange") {
            sheetindex.val(0);
        }
        else {
            sheetindex.val(1);
        }
    }
})