jQuery(function ($) {
    $( '.wrap' ).on( 'click', '.uploadbutton', fileselect );
    $( '.wrap' ).on( 'click', '.downloadbutton', download );
    $( '.wrap' ).on( 'click', '.renamebutton', rename );
    $( '.wrap' ).on( 'click', '.deletebutton', deleteclk );
    $( '.wrap' ).on('change', '.file_input_text', upload );
    $( '.wrap' ).on('change', '.apikeyfield', optionschange );
    $( '.wrap' ).on('change', '.showcreateexample', optionschange );
    $( '.wrap' ).on( 'click', '.filename', selectfile );
    $( '.wrap' ).on( 'click', '.createexamplespan', createexampleclick );

    $( '.uploadbutton' ).attr( 'disabled', $( '.filename' ).attr( 'disabled' ) );

    if ( '<?php echo $continue_operation ?>' == '<?php echo File_Operations::CONTINUE_DOWNLOAD; ?>' ) {
        enddownload();
    }
    function createexampleclick() {
        $( '.showcreateexample' ).click();
    }
    function selectfile() {
        if ($( '.filename' ).val() != null ) {
            $( '.downloadbutton' ).attr( 'disabled', false );
            $( '.renamebutton' ).attr( 'disabled', false );
            $( '.deletebutton' ).attr( 'disabled', false );
        }
    }
    function optionschange() {
        $( '.updateoptions' ).attr( 'disabled', false );
    }
    function fileselect() {
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
        var filearray = $( '.filename' ).val().split( '.' );
        var renamedfilename = filearray[0];
        var fileextension = filearray[1];
        var dialog = $( '.renamedialog' )[0];
        $( '.file_new_name' ).val( renamedfilename );
        dialog.show();

        $( '.closedg' ).click( function ( event ) {
            event.preventDefault();
            var filenotexists = true;
            var newfilename = $( '.file_new_name' ).val() + '.' + fileextension;
            $( '.filename option' ).each( function ( index, element ) {
                if ( $( element ).text() == newfilename ) {
                    $( '.existtext' ).text( 'File ' + newfilename + ' already exists.' );
                    $( '.existtext' ).show();
                    filenotexists = false;
                    return;
                }
            });
            if ( filenotexists ) {
                dialog.close();
                $( '.file_new_name' ).val( newfilename );
                $( '.my_file_operation' ).val( '<?php echo File_Operations::RENAME; ?>' );
                $( '.autorization_manager' ).submit();
            }
            else {
                return;
            }
        });
        document.querySelector( '.canceldg' ).onclick = function () {
            dialog.close();
        };
    }
    function deleteclk() {
        var choice = confirm( 'Do you really want to delete the "' + $( '.filename' ).val() + '" file?' );
        if (choice) {
            $( '.my_file_operation' ).val( '<?php echo File_Operations::DELETE; ?>' );
            $( '.autorization_manager' ).submit();
        }
    }
    function enddownload() {
        var element = document.createElement( 'a' );
        element.setAttribute( 'href', 'data:application/octet-stream; charset=utf-8; base64,' + '<?php echo $download_file; ?>' );
        element.setAttribute( 'download', '<?php echo $file_name; ?>' );
        element.click();
        document.body.removeChild(element);
    }
})