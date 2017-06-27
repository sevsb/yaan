$(document).ready(function() {

    $("#do-login").click(function() {
        
        $.ajax({
            url: "ajax.php?action=" + "login.get_salt" ,
            type: 'post',
            data: '',
            success: function (data) {
                console.debug(data);
                data = eval("(" + data + ")");
                
                var salt = data.ret;
                var email = $("#email").val();
                var password = $("#password").val();
                var cipher = md5(email + salt + password);

                console.debug("email = " + email);
                console.debug("password = " + password);
                console.debug("salt = " + salt);
                console.debug("cipher = " + cipher);

                __ajax("login.login", {email: email, cipher: cipher}, function(data) {
                    console.log(data.refer);
                    document.location.href = "?";
                    //document.location.href = data.refer;
                });
            }
        });
    });
});
