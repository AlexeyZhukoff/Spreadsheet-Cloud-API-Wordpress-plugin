jQuery(function ($) {
    $.get( "createexample.php", function ( createExample ) {
        if ( createExample ) {
            $( '#create-example-button' ).attr( 'style', 'float:right' );
        }
        else {
            $( '#create-example-button' ).attr( 'style', 'display:none' );
        }
    } )

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

        $( '#create-example-button' ).click( function () {
            if ($( '.parameters-header' ).text() != 'Test shortcode parameters' ) {
                $( '.parameters-header' ).text( 'Test shortcode parameters' );
                $( '.shortcode' ).attr( 'value', 'sclapiexample ' );
                $( '.filename' ).replaceWith( '<select class="examplefilename" name="filename" size="1"><option value="example.xlsx">example.xlsx</option></select>' );
                $( '.range' ).attr( 'value', 'A1:E7' );
                $( '.sheet-index' ).attr( 'readonly', true );
                $( '.sheet-name' ).attr( 'readonly', true );
                $( '.object-index' ).attr( 'readonly', true );
                $( '.picture-type' ).attr( 'readonly', true );
                exampleCommandChangeCore( $( '.command' ) );
                $( 'body' ).on( 'change', '.command', exampleCommandChange );
            }
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
    function exampleCommandChange() {
        exampleCommandChangeCore( $( this ) );
    }
    function exampleCommandChangeCore( command ) {
        commandChangeCore( command );
        var sheetindex = command.closest( 'fieldset' ).find( '.sheet-index' );
        if ( command.val() === 'GetHTMLRange' ) {
            sheetindex.val(0);
        }
        else {
            sheetindex.val(1);
        }
    }
} )