(function () {
    tinymce.create("tinymce.plugins.SclapiInsertShortcode", {
        init: function (a, url) {
            a.addCommand("sclapiInsert", function () {
                a.windowManager.open({
                    file: sclapi_generate_admin_ajax,
                    width: 470,
                    height: 251,
                    inline: 1
                })
            });

            a.addButton("sclapi_insert_shortcode", {
                title: "Generate SCLAPI shortcode",
                cmd: "sclapiInsert",
                image: url + "/images/icon.png"
            });
        },

        getInfo: function () {
            return {
                longname: "Insert SCLAPI shortcode",
                author: "sclapi",
                authorurl: "http://sclapi.com",
                version: "1.0"
            }
        }
    });

    tinymce.PluginManager.add("sclapi_insert_shortcode", tinymce.plugins.SclapiInsertShortcode)
})();