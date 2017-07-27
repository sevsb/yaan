$(document).ready(function (){
    
del_id = null;

$('.do_del').click(function (){
    console.log(del_id);
    __ajax("admin.project.del", {del_id: del_id}, true);
});

$('.del_btn').click(function (){
    del_id = $(this).parents('.project_elf').attr('muffinid');
});

$('.update_status_btn').click(function (){
    var sid = $(this).attr('sid');
    var muffinid = $(this).parents('.project_elf').attr('muffinid')
    console.log(sid);
    console.log(muffinid);
    __ajax('admin.project.update_status', {muffinid: muffinid, sid: sid}, true)
});

});