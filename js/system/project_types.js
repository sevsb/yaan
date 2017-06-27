$(document).ready(function() {
    
    $('.save_btn').click(function (){
        var types = [];
        $('.setting_elf').each(function (){
            type = new Object();
            type.id = $(this).find(".id").html();
            type.title = $(this).find(".title").val();
            types.push(type);
        });
        console.log(types);
        //return;
        __ajax('system.update_project_types',{types: types},true);
    });
    
    
    $('.do_new').click(function (){
        var title = $('.new_title').val();
        console.log(title);
        if (title == '') {
            return;
        }
        __ajax('system.new_project_type',{title: title},true);
    });
        
    $('.do_del').click(function (){
        var showid = $(this).parents('.setting_elf').find('.id').html();
        showid = showid - 1
        console.log(showid);
        //return;
        __ajax('system.del_project_type',{id: showid},true);
    });
    

    
    
});

