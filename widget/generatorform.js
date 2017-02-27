jQuery(function ($) {
    $.get( "getfilelist.php", function ( htmlFilesList ) {
        $( '.filelist' ).replaceWith( htmlFilesList );
        commandChangeCore( $( '.command' ) );
    } )

    $( 'body' ).on( 'change', '.command', commandChange );

    $( document ).ready( function () {
        $( '#insert-button' ).click(function () {
            var shortcode = '[';
            $( 'form .sclapi-container' ).find( 'input:not(:disabled), select:not(:disabled)' ).each( function () {
                var attName = $( this ).attr( 'name' ), attValue = $( this ).val(), attResult = '';
                if ( attName === 'exportgridlines' ) {
                    if ( $( this ).prop( 'checked' ) ) {
                        attValue = 'true';
                    }
                    else {
                        attValue = undefined;
                    }
                }
                if ( attValue != undefined && attValue.length != 0 && attName != 'shortcode' ) {
                    attResult = attName + '="' + attValue + '" ';
                }
                if ( attName === 'shortcode' ) {
                    if ( shortcode.length > 1 ) {
                        shortcode += ']<br />[';
                    }
                    attResult = attValue;
                }
                shortcode += attResult;
            } )
            shortcode += ']';

            tinyMCEPopup.editor.execCommand( 'mceInsertContent', false, shortcode );
            tinyMCEPopup.close();
        } )

        $( '#cancel-button' ).click( function () {
            tinyMCEPopup.close();
        } )
    } )
    function commandChange() {
        var command = $( this );
        commandChangeCore( command );
    }
    function commandChangeCore( command ) {
        disableCommandParameters( command.closest( 'fieldset' ), command.val() === 'GetHTMLRange' );
    }
    function disableCommandParameters( form, disabled ) {
        form.find( '.export-gridlines' ).attr( 'disabled', !disabled );
        form.find( '.object-index' ).attr( 'disabled', disabled );
        form.find( '.picture-type' ).attr( 'disabled', disabled );
        form.find( '.height' ).attr( 'disabled', disabled );
        form.find( '.width' ).attr( 'disabled', disabled );
    }
} )