$(document).ready(function() {
    var sheetlist = new Vue({
        el: '#sheetlist',
        data: {
            sheetlist: null,
            viewsheetkey: 0,
            showviewsheet: false,
        },
        methods: {
            viewSheet: function(event) {
                var target = event.currentTarget;
                var sheetkey = $(target).attr("sheet");
                sheetlist.viewsheetkey = sheetkey;
                sheetlist.showviewsheet = true;
            }
        }
    });

    __request("admin.sheet.sheetlist", { }, function(data) {
        console.debug(data);
        sheetlist.sheetlist = data.data;
    });

});

