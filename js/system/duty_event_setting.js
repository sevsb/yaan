$(document).ready(function() {
    
    $('.save_btn').click(function (){
        var settings = [];
        $('.setting_elf').each(function (){
            setting = new Object();
            setting.id = $(this).find(".id").html();
            setting.color = $(this).find(".color").val();
            setting.title = $(this).find(".title").val();
            setting.type = $(this).find(".type").find(".btn-primary").attr("id");
            settings.push(setting);
        });
        console.log(settings);
        __ajax('system.update_event_settings',{settings: settings},true);
    });
    
    
    $('.do_new').click(function (){
        var title = $('.new_title').val();
        var color = $('.new_color').val();
        var type = '';
        $('.new_eve_type').each(function (){
            if ($(this).hasClass("btn-primary")){
                type = $(this).attr("id");
            }
        });
        console.log(title);
        console.log(color);
        console.log(type);
        if (title == '' || color == '' || type == '') {
            return;
        }
        __ajax('system.new_event_setting',{title: title, color: color, type: type},true);
    });
    
    $('.new_eve_type').click(function (){
        $(this).addClass("btn-primary");
        $(this).siblings().removeClass("btn-primary");
    });
    
    $('.eve_type').click(function (){
        $(this).addClass("btn-primary");
        $(this).siblings().removeClass("btn-primary");
    });
    
    
});

