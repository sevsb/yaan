
$(document).ready(function() {
    $(".notify-switcher").click(function() {
        var checked = $(this).prop("checked");
        var action = $(this).attr("action");
        var arg = $(this).attr("arg");
        __ajax(action, {arg: arg, val: checked});
    });

    $("#update-face").click(function() {
        $("#face-file").click();
    });

    $("#face-file").change(function() {
        __file_upload(this, true, true, null);
    });

    $("#update-password").click(function() {
        var oldp = $("#oldpassword").val();
        var newp = $("#newpassword").val();
        __ajax("login.update_password", {oldp: oldp, newp: newp});
    });
});

