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