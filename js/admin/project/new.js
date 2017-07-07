$(document).ready(function (){
  var type = null;
  
  $( "#datepicker" ).datepicker();
  $( "#datepicker" ).datepicker( "option", "dateFormat", 'yy-mm-dd' );
  
  $('.upload_cover_btn').click(function (){
    $('#uploadcover').click();
    return false;
  });
  
  $('#cover_show').click(function (){
    $('#uploadcover').click();
    return false;
  });
  
  $('.dropdownLi').click(function (){
    type = $(this).find("a").html();
    $('#dropdownMenu1').html(type);
  });

  
  $("#uploadcover").change(function() {
        if (typeof FileReader == 'undefined') {
            alert("您的浏览器不支持上传，请更换浏览器重试！");
            return false;
        }

        var file = this.files[0];
        if (!/image\/\w+/.test(file.type)) {
            alert("文件不是图像类型！");
            return false;
        }

        $(".upload_cover_btn").addClass("hide");
        $("#cover_show").removeClass("hide");

        var reader = new FileReader();
        reader.onload = function(e) {
            var img_src = e.target.result;
            $("#cover_show").attr("src", img_src);
            // upload_image(img_src,function (data) {
            //     data = eval("(" + data + ")");
            //     console.debug(data);

            //     if (data.status == 'success') {
            //         var img_drone = "<div class='img_pre'><img src='" + img_src +"' filename=" + data.info + "><button class='del_me btn btn-danger center-block'>删除</button></div>";
            //         $('.previews').append(img_drone);
            //         return;
            //     }
            //     if (data.status == 'fail') {
            //         if (data.info == 'token_fail') {
            //             refresh_picservice_token();
            //         }
            //         alert(data.info);
            //         return;
            //     }
            // });
        }
        reader.readAsDataURL(file);
        return true;
    });
    var uploadpaperfile = null;
  $("#uploadpaperfile").change(function() {
        if (typeof FileReader == 'undefined') {
            alert("您的浏览器不支持上传，请更换浏览器重试！");
            return false;
        }
        var whitelist = ["application/msword", 
        "application/vnd.ms-excel", 
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        "application/vnd.ms-excel.sheet.macroEnabled.12", 
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document"];

        uploadpaperfile = this.files[0];
        var typeflag = false;
        for (var id in whitelist) {
            if (whitelist[id] == uploadpaperfile.type) {
                typeflag = true;
            }
        }
        if (typeflag == false) {
            alert("文件不是支持的类型！");
            return false;
        }
        var reader = new FileReader();
        reader.onload = function(e) {
            uploadfilefileURL = e.target.result;
        }
        reader.readAsDataURL(uploadpaperfile);
        return true;
        
        
  });
  
  $('.do_new').click(function (){
    var project_id = $('#project_id').val();
    var title = $('#title').val();
    var description = $('#description').val();
    var maintext = $('#text').val();
    var cover = $("#cover_show").attr("src");
    var limit_time = $('#datepicker').val();
    limit_time = Date.parse(limit_time) / 1000 - 28800;
    
    console.log("project_id:" + project_id);
    console.log("title:" + title);
    console.log("description:" + description);
    console.log("text:" + maintext);
    console.log("limit_time:" + limit_time);
    //console.log("uploadpaperfile:" + uploadfilefileURL);
    //console.log(uploadfilefileURL);
    
    //return;
    __ajax('admin.project.add',{
      project_id: project_id,
      title: title, 
      description: description, 
      maintext: maintext, 
      cover: cover, 
      limit_time: limit_time, 
      paperfile: uploadfilefileURL, 
      type: type
    },function (data){
        console.log(data);
        ret = data.ret;
        if (ret == 'success'){
            document.location.href = '?admin/project/index';
        }
        
    });
  });
});