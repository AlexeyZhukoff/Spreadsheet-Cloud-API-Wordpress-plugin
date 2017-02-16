jQuery( function ($) {
    $( '.wrap' ).on( 'click', '.uploadbutton', fileSelect );
    $( '.wrap' ).on( 'click', '.downloadbutton', download );
    $( '.wrap' ).on( 'click', '.renamebutton', rename );
    $( '.wrap' ).on( 'click', '.deletebutton', deleteClk );
    $( '.wrap' ).on( 'change', '.file_input_text', upload );
    $( '.wrap' ).on( 'change', '.apikeyfield', optionsChange );
    $( '.wrap' ).on( 'change', '.showcreateexample', optionsChange );
    $( '.wrap' ).on( 'click', '.filename', selectFile );
    $( '.wrap' ).on( 'click', '.createexamplespan', createExampleClick );

    $( '.uploadbutton' ).attr( 'disabled', $( '.filename' ).attr( 'disabled' ) );

    if ( '<?php echo $continue_operation ?>' == '<?php echo File_Operations::CONTINUE_DOWNLOAD; ?>' ) {
        endDownload();
    }
    function createExampleClick() {
        $( '.showcreateexample' ).click();
    }
    function selectFile() {
        if ($( '.filename' ).val() != null ) {
            $( '.downloadbutton' ).attr( 'disabled', false );
            $( '.renamebutton' ).attr( 'disabled', false );
            $( '.deletebutton' ).attr( 'disabled', false );
        }
    }
    function optionsChange() {
        $( '.updateoptions' ).attr( 'disabled', false );
    }
    function fileSelect() {
        $( '.file_input_text' ).click();
    }
    function upload() {
        $( '.my_file_operation' ).val( '<?php echo File_Operations::UPLOAD; ?>' );
        $( '.autorization_manager' ).submit();
    }
    function download() {
        $( '.my_file_operation' ).val( '<?php echo File_Operations::DOWNLOAD; ?>' );
        $( '.autorization_manager' ).submit();
    }
    function rename() {
        if ($( '.filename' ).val() == null ) {
            $( '.my_file_operation' ).val( '<?php echo File_Operations::RENAME; ?>' );
            $( '.autorization_manager' ).submit();
        }
        var fileArray = $( '.filename' ).val().split( '.' );
        var renamedFileName = fileArray[0];
        var fileExtension = fileArray[1];
        var dialog = $( '.renamedialog' )[0];
        $( '.file_new_name' ).val( renamedFileName );
        dialog.show();

        $( '.closedg' ).click( function ( event ) {
            event.preventDefault();
            var fileNoExists = true;
            var newFileName = $( '.file_new_name' ).val() + '.' + fileExtension;
            $( '.filename option' ).each( function ( index, element ) {
                if ( $( element ).text() == newFileName ) {
                    $( '.existtext' ).text( 'File ' + newFileName + ' already exists.' );
                    $( '.existtext' ).show();
                    fileNoExists = false;
                    return;
                }
            } );
            if ( fileNoExists ) {
                dialog.close();
                $( '.file_new_name' ).val( newFileName );
                $( '.my_file_operation' ).val( '<?php echo File_Operations::RENAME; ?>' );
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
            $( '.my_file_operation' ).val( '<?php echo File_Operations::DELETE; ?>' );
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