$(document).ready(function() {
    $("#do-save").click(function() {
        var password = $("#password").val();
        __ajax("login.update_forget", {password: password}, function(data) {
            $(".smail").addClass("hidden");
            $(".smail-tip").removeClass("hidden");
        });
    });
});
