(function() {
    tinymce.create ( "tinymce.plugins.SclapiInsertShortcode", {
        init:function ( a, b ) {
            a.addCommand ( "sclapiInsert", function() {
                a.windowManager.open ( {
                    file: b + "/generatorform.html",
                    width: 420,
                    height: 356,
                    inline: 1
                })
            });
           
            a.addButton ( "sclapi_insert_shortcode", {
                title: "Generate SCLAPI shortcode",
                cmd: "sclapiInsert",
                image: b + "/images/icon.png"
            });
        },
 
        getInfo:function() {
            return {
                longname:"Insert SCLAPI shortcode",
                author:"sclapi",
                authorurl:"http://sclapi.com",
                version:"1.0"
            }
        }
    });
     
    tinymce.PluginManager.add ( "sclapi_insert_shortcode", tinymce.plugins.SclapiInsertShortcode )
} )();