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
    
    $(".save_btn").click(function (){
        var id = $('#id').val();
        var nickname = $('#nick').val();
        var face = $("#face").attr("src");
        
        console.log(id);
        //console.log(face);
        console.log(nickname);
        __ajax('system.update_config', {id: id, nick: nickname, face: face},true);
    });
    
});

