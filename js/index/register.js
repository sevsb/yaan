
$(document).ready(function() {
    $("#update-face").click(function() {
        $("#face-file").click();
    });

    $("#face-file").change(function() {
        if (typeof FileReader == 'undefined') {
            alert("您的浏览器不支持上传，请更换浏览器重试！");
            return false;
        }

        var file = this.files[0];
        if (!/image\/\w+/.test(file.type)) {
            alert("文件不是图像类型！");
            return false;
        }

        var reader = new FileReader();
        reader.onload = function(e){
            $("#face").attr("src", e.target.result);
        }
        reader.readAsDataURL(file);
        return true;
    });

    $("#do-register").click(function() {
        var nick = $("#nick").val();
        var email = $("#email").val();
        var password = $("#password").val();
        var face = $("#face").attr("src");
        if (nick == "" || email == "" || password == "" || face == "images/favicon.ico") {
            alert("请补全数据。");
            return false;
        }

        __ajax("login.register", {nick: nick, email: email, password: password, face: face}, "?login");
    });



});



