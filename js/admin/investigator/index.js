$(document).ready(function() {
    var refresh_sheet_data = function(data) {
        console.debug(data);
        userlist.userlist = data.data;
    };

    var userlist = new Vue({
        el: '#page-wrapper',
        data: {
            userlist: null,
        },
        methods: {
        }
    });

    __request("admin.investigator.listall", { }, refresh_sheet_data);

});

