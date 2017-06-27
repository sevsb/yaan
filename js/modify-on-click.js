
$(document).ready(function() {
    $(".modify-on-click .moc-display").click(function() {
        __moc_show_modify(this);
    });

    $('.modify-on-click .moc-modify .moc-modify-cancel').click(function() {
        __moc_show_display(this);
    });

    function install_modify_on_click() {
        $(".modify-on-click").each(function() {
            if (!$(this).is("[moc-title]") || !$(this).is("[moc-action]")) {
                return;
            }

            var title = $(this).attr("moc-title");

            var h1 = $('<div class="moc-display">' + title + '</div>');
            var h2 = $('<div class="input-group moc-modify hidden"></div>');
            var h3 = $('<input type="text" class="form-control moc-modify-input" />');
            var h4 = $('<div class="btn btn-default input-group-addon moc-modify-confirm"><i class="fa fa-check"></i></div>');
            var h5 = $('<div class="btn btn-default input-group-addon moc-modify-cancel"><i class="fa fa-times"></i></div>');

            h1.on("click", function() { __moc_show_modify(this); });
            h4.on("click", function() { __moc_confirm(this); });
            h5.on("click", function() { __moc_show_display(this); });

            h2.prepend(h3, h4, h5);
            $(this).prepend(h1, h2);
        });
    }

    install_modify_on_click();
});

function __moc_show_modify(obj) {
    var mod = $(obj).parent().children(".moc-modify");
    // console.debug(mod);

    var cnt = $(obj).html();
    mod.children(".moc-modify-input").val(cnt);

    $(obj).addClass("hidden");
    mod.removeClass("hidden");
    mod.children('.moc-modify-input').focus();
}

function __moc_show_display(obj) {
    var dis = $(obj).parent().parent().children(".moc-display");
    $(obj).parent().addClass("hidden");
    dis.removeClass("hidden");
}


function __moc_confirm(obj) {
    var val = $(obj).parent().children(".moc-modify-input").val();
    var action = $(obj).parent().parent().attr("moc-action");
    var extra = null;
    if ($(obj).parent().parent().is("[moc-extra]")) {
        extra = $(obj).parent().parent().attr("moc-extra");
    }
    // console.debug(action);
    __ajax_and_reload(action, {value: val, extra: extra});
}



