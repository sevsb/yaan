$(document).ready(function() {
    var sheetlist = new Vue({
        el: '#sheetlist',
        data: {
            sheetlist: null
        },
        methods: {
            viewTask: function(event) {
                var target = event.currentTarget;
                var taskkey = $(target).attr("taskkey");
            }
        }
    });

    __request("admin.sheet.sheetlist", { }, function(data) {
        console.debug(data);
        // sheetlist.sheetlist = data;
    });

});

