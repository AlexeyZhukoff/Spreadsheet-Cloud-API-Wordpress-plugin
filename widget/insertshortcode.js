(function(){
    tinymce.create("tinymce.plugins.SclapiInsertShortcode",{
        init:function(a, b){
            a.addCommand("sclapiInsert", function(){
                a.windowManager.open({
                    file: b + "/form.html",
                    width: 568,
                    height: 900,
                    inline: 1
                })
            });
           
            a.addButton("sclapi_insert_shortcode",{
                title: "insert_shortcode.desc",
                cmd: "sclapiInsert",
                image: b + "/images/icon.png"
            });
        },
 
        getInfo:function(){
            return{
                longname:"Insert SCLAPI shortcode",
                author:"sclapi",
                authorurl:"http://sclapi.com",
                version:"1.0"
            }
        }
    });
     
    tinymce.PluginManager.add("sclapi_insert_shortcode",tinymce.plugins.SclapiInsertShortcode)
})();