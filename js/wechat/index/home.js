var __log = function(msg) {
    msg = JSON.stringify(msg); 
    var m = $("#log").html();
    m += "\n" + msg;
    $("#log").html(m);
}

$(document).ready(function() {
    document.addEventListener("touchstart", function() {}, false);

    var tasks = new Vue({
        el: '#task-wrapper',
        data: {
            pageanchor: 0,
            pagestatus: 0,
            tabstatus: 1,
            viewtaskkey: 0,
            userinfo: null,
        },
        methods: {
            isOnShow: function(status) {
                switch(tasks.tabstatus) {
                    case 0:
                        return true;
                    case 1:
                        if (status == STATUS_ASSIGNED) {
                            return true;
                        } else {
                            return false;
                        }
                    case 2:
                        if (status == STATUS_NOTREVIEW) {
                            return true;
                        } else {
                            return false;
                        }
                    case 3:
                        if (status == STATUS_REJECT) {
                            return true;
                        } else {
                            return false;
                        }
                    case 4:
                        if (status == STATUS_PASS) {
                            return true;
                        } else {
                            return false;
                        }
                    default:
                        return false;
                }
            },
            viewTask: function(event) {
                var target = event.currentTarget;
                var taskkey = $(target).attr("taskkey");
                // console.debug(taskkey);
                tasks.viewtaskkey = taskkey;
                tasks.pagestatus = 3;
                tasks.pageanchor = document.body.scrollTop;
                setTimeout(function() {
                    document.body.scrollTop = $('#task-info')[0].offsetTop;
                }, 0);
            },
            isAssigned: function(status) {
                if (status == STATUS_ASSIGNED) {
                    return true;
                } else {
                    return false;
                }
            },
            gosheet: function(event) {
                var tid = tasks.userinfo.tasks[tasks.viewtaskkey].id;
                go("wechat/index/sheet", { task: tid });
            },
            goback: function(event) {
                tasks.pagestatus = 2;
                setTimeout(function() {
                    document.body.scrollTop = tasks.pageanchor;
                }, 0);
            },
            showcancelmodal: function(event) {
                $('#cancel_task_modal').modal('show');
            },
            canceltask: function(event) {
                var target = event.currentTarget;
                var userid = null;
                var tid = tasks.userinfo.tasks[tasks.viewtaskkey].id;
                __ajax('admin.task.assign', {taskid: tid, userid: userid},function (data){
                    console.log(data);
                    $('#cancel_task_modal').modal('hide');
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
