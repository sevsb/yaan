$(document).ready(function() {

    var refresh_sheet_data = function(data) {
        console.debug(data);
        userlist.userlist = data.data;
    };
    var _flag = 0;
    _flag = get_request('flag');
    console.log('flag:' + _flag);
    var userlist = new Vue({
        el: '#user-infos',
        data: {
            userlist: null,
            viewuserkey: 0,
            showuserinfoview: false,
            flag: _flag
        },
        methods: {
            viewUser: function(event) {
                console.log)('viewUSer');
                var target = event.currentTarget;
                var userkey = $(target).attr("userkey");
                userlist.viewuserkey = userkey;
                userlist.showuserinfoview = true;
            },
            closeUserView: function(event) {
                userlist.showuserinfoview = false;
            }
        },
        updated: function() {            
            console.log("更新完成");
                $('#mytable1').dataTable().destroy();
                $('#mytable1').dataTable({
                  "oLanguage": {
                    "sLengthMenu": "每页显示 _MENU_ 条记录",
                    "sZeroRecords": "对不起，查询不到任何相关数据",
                    "sInfo": "当前显示 _START_ 到 _END_ 条，共 _TOTAL_条记录",
                    "sInfoEmtpy": "找不到相关数据",
                    "sInfoFiltered": "数据表中共为 _MAX_ 条记录)",
                    "sProcessing": "正在加载中...",
                    "sSearch": "搜索",
                    "oPaginate": {
                      "sFirst": "第一页",
                      "sPrevious":" 上一页 ",
                      "sNext": " 下一页 ",
                      "sLast": " 最后一页 "
                      },
                    }
                });
            }   
    });

    __request("admin.investigator.listall", { }, refresh_sheet_data);

});

