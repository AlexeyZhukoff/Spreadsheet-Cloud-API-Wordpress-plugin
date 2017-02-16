jQuery( function ($) {
    $( '.wrap' ).on( 'click', '.upload-button', fileSelect );
    $( '.wrap' ).on( 'click', '.download-button', download );
    $( '.wrap' ).on( 'click', '.rename-button', rename );
    $( '.wrap' ).on( 'click', '.delete-button', deleteClk );
    $( '.wrap' ).on( 'change', '.file-input-text', upload );
    $( '.wrap' ).on( 'change', '.api-key-field', optionsChange );
    $( '.wrap' ).on( 'change', '.show-create-example', optionsChange );
    $( '.wrap' ).on( 'click', '.filename', selectFile );
    $( '.wrap' ).on( 'click', '.create-example-span', createExampleClick );

    $( '.upload-button' ).attr( 'disabled', $( '.filename' ).attr( 'disabled' ) );

    if ( '<?php echo $continue_operation ?>' === '<?php echo File_Operations::CONTINUE_DOWNLOAD; ?>' ) {
        endDownload();
    }
    function createExampleClick() {
        $( '.show-create-example' ).click();
    }
    function selectFile() {
        if ($( '.filename' ).val() != null ) {
            $( '.download-button' ).attr( 'disabled', false );
            $( '.rename-button' ).attr( 'disabled', false );
            $( '.delete-button' ).attr( 'disabled', false );
        }
    }
    function optionsChange() {
        $( '.update-options' ).attr( 'disabled', false );
    }
    function fileSelect() {
        $( '.file-input-text' ).click();
    }
    function upload() {
        $( '.my-file-operation' ).val( '<?php echo File_Operations::UPLOAD; ?>' );
        $( '.autorization_manager' ).submit();
    }
    function download() {
        $( '.my-file-operation' ).val( '<?php echo File_Operations::DOWNLOAD; ?>' );
        $( '.autorization_manager' ).submit();
    }
    function rename() {
        if ($( '.filename' ).val() == null ) {
            $( '.my-file-operation' ).val( '<?php echo File_Operations::RENAME; ?>' );
            $( '.autorization_manager' ).submit();
        }
        var fileArray = $( '.filename' ).val().split( '.' );
        var renamedFileName = fileArray[0];
        var fileExtension = fileArray[1];
        var dialog = $( '.renamedialog' )[0];
        $( '.file-new-name' ).val( renamedFileName );
        dialog.show();

        $( '.closedg' ).click( function ( event ) {
            event.preventDefault();
            var fileNoExists = true;
            var newFileName = $( '.file-new-name' ).val() + '.' + fileExtension;
            $( '.filename option' ).each( function ( index, element ) {
                if ( $( element ).text() === newFileName ) {
                    $( '.exist-text' ).text( 'File ' + newFileName + ' already exists.' );
                    $( '.exist-text' ).show();
                    fileNoExists = false;
                    return;
                }
            } );
            if ( fileNoExists ) {
                dialog.close();
                $( '.file-new-name' ).val( newFileName );
                $( '.my-file-operation' ).val( '<?php echo File_Operations::RENAME; ?>' );
                $( '.autorization_manager' ).submit();
            }
            else {
                return;
            }
        } );

        document.querySelector( '.canceldg' ).onclick = function () {
            dialog.close();
        };
    }
    function deleteClk() {
        var choice = confirm( 'Do you really want to delete the "' + $( '.filename' ).val() + '" file?' );
        if ( choice ) {
            $( '.my-file-operation' ).val( '<?php echo File_Operations::DELETE; ?>' );
            $( '.autorization_manager' ).submit();
        }
    }
    function endDownload() {
        var element = document.createElement( 'a' );
        element.setAttribute( 'href', 'data:application/octet-stream; charset=utf-8; base64,' + '<?php echo $download_file; ?>' );
        element.setAttribute( 'download', '<?php echo $file_name; ?>' );
        element.click();
        document.body.removeChild(element);
    }
} )