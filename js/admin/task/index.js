$(document).ready(function (){
    
del_id = null;

$('.do_del').click(function (){
    console.log(del_id);
    __ajax("admin.task.del", {del_id: del_id}, true);
});

$('.del_btn').click(function (){
    del_id = $(this).parents('.task_elf').attr('muffinid');
});

$('.accept_btn').click(function (){
    taskid = $(this).parents('.task_elf').attr('taskid');
});
$('.investigator_div').click(function (){
    $(this).parents('.modal-body').find('.investigator_div').removeClass('choosed');
    $(this).addClass('choosed');
});


$('.do_accept').click(function (){
    userid = $('.choosed').attr('userid');
    console.log("taskid:" + taskid);
    console.log("userid:" + userid);
    if (userid == '' || userid == undefined) {
        return false;
    }
    __ajax('admin.task.assign', {taskid : taskid , userid: userid} ,true )
});
$('.cancel_accept').click(function (){
    userid = null;
    console.log("taskid:" + taskid);
    console.log("userid:" + userid);
    __ajax('admin.task.assign', {taskid : taskid , userid: userid} ,true )
});
});