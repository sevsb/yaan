$(document).ready(function() {
    $("#do-send").click(function() {
        var email = $("#email").val();
        __ajax("login.forget", {email: email}, function(data) {
            $(".smail").addClass("hidden");
            $(".smail-tip").removeClass("hidden");
        });
    });
});
