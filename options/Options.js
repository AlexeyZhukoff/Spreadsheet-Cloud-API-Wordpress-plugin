jQuery(function ($) {
    $('.wrap').on('click', '.uploadbutton', fileselect);
    $('.wrap').on('click', '.downloadbutton', download);
    $('.wrap').on('click', '.renamebutton', rename);
    $('.wrap').on('click', '.deletebutton', deleteclk);
    $('.wrap').on('change', '.file_input_text', upload);

    if ("<?= $continueoperation ?>" == "<?= FileOperations::ContinueDownload; ?>")
        enddownload();

    function fileselect() {
        $('.file_input_text').click();
    }
    function upload() {
        $('.my_file_operation').val("<?= FileOperations::Upload; ?>");
        $('.user_file_manager').submit();
    }
    function download() {
        $('.my_file_operation').val("<?= FileOperations::Download; ?>");
        $('.user_file_manager').submit();
    }
    function rename() {
        if ($('.filename').val() == null) {
            $('.my_file_operation').val("<?= FileOperations::Rename; ?>");
            $('.user_file_manager').submit();
        }
        var filearray = $('.filename').val().split('.');
        var renamedfilename = filearray[0];
        var fileextension = filearray[1];
        var dialog = $('.renamedialog')[0];
        $('.file_new_name').val(renamedfilename);
        dialog.show();

        $('.closedg').click(function () {
            var filenotexists = true;
            var newfilename = $('.file_new_name').val() + '.' + fileextension;
            $('.filename option').each(function (index, element) {
                if ($(element).text() == newfilename) {
                    $('.existtext').show();
                    filenotexists = false;
                    return;
                }
            });
            if (filenotexists) {
                dialog.close();
                $('.file_new_name').val(newfilename);
                $('.my_file_operation').val("<?= FileOperations::Rename; ?>");
                $('.user_file_manager').submit();
            }
            else {
                return;
            }
        });
        document.querySelector('.canceldg').onclick = function () {
            dialog.close();
        };

    }
    function deleteclk() {
        $('.my_file_operation').val("<?= FileOperations::Delete; ?>");
        $('.user_file_manager').submit();
    }
    function enddownload() {
        var element = document.createElement('a');
        element.setAttribute('href', 'data:application/octet-stream; charset=utf-8; base64,' + '<?= $downloadfile; ?>');
        element.setAttribute('download', "<?= $filename; ?>");
        element.click();
        document.body.removeChild(element);
    }
})