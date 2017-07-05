$(document).ready(function (){
    
del_id = null;

$('.do_del').click(function (){
    console.log(del_id);
    __ajax("admin.task.del", {del_id: del_id}, true);
});

$('.del_btn').click(function (){
    del_id = $(this).parents('.task_elf').attr('muffinid');
});

});