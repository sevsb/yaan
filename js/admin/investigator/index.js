$(document).ready(function() {
    var refresh_sheet_data = function(data) {
        console.debug(data);
        userlist.userlist = data.data;
    };

    var userlist = new Vue({
        el: '#user-infos',
        data: {
            userlist: null,
            viewuserkey: 0,
            showuserinfoview: false,
        },
        methods: {
            viewUser: function(event) {
                var target = event.currentTarget;
                var userkey = $(target).attr("userkey");
                userlist.viewuserkey = userkey;
                userlist.showuserinfoview = true;
            },
            closeUserView: function(event) {
                userlist.showuserinfoview = false;
            }
        }
    });

    __request("admin.investigator.listall", { }, refresh_sheet_data);

});

