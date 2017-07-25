var __log = function(msg) {
    msg = JSON.stringify(msg); 
    var m = $("#log").html();
    m += "\n" + msg;
    $("#log").html(m);
}


$(document).ready(function() {
    var tasks = new Vue({
        el: '#task-wrapper',
        data: {
            pagestatus: 0,
            viewtaskkey: 0,
            userinfo: null,
        },
        methods: {
            viewTask: function(event) {
                var target = event.currentTarget;
                var taskkey = $(target).attr("taskkey");
                // console.debug(taskkey);
                tasks.viewtaskkey = taskkey;
                tasks.pagestatus = 3;
            },
            gosheet: function(event) {
                var tid = tasks.userinfo.tasks[tasks.viewtaskkey].id;
                go("wechat/index/sheet", { task: tid });
            },
            goback: function(event) {
                tasks.pagestatus = 2;
            },
            canceltask: function(event) {
                var target = event.currentTarget;
                var tid = tasks.userinfo.tasks[tasks.viewtaskkey].id;
                __ajax('admin.task.assign', {taskid: tid},function (){
                    __request("wechat.api.mytasks", {}, function(res) {
                        console.debug(res);
                        tasks.userinfo = res.data;
                        if (res.data.tasks.length == 0) {
                            tasks.pagestatus = 1;
                        } else {
                            tasks.pagestatus = 2;
                        }
                    });
                });
            }
        }
    });


    __request("wechat.api.mytasks", {}, function(res) {
        console.debug(res);
        tasks.userinfo = res.data;
        if (res.data.tasks.length == 0) {
            tasks.pagestatus = 1;
        } else {
            tasks.pagestatus = 2;
        }
    });
});









