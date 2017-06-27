function get_pic_url(functionname) {
    __ajax("picservice.get_pic_url","",function (data) {
        pic_url = data.info;
        console.log("pic_url:"+pic_url);
        functionname(data);
    });
}

function check_picservice_token() {
    console.log('check_picservice_token!');
    __ajax("picservice.get_token","",function (data) {
        var now_time = Date.parse(new Date()) / 1000;
        token = data.info.token;
        expired = data.info.expired;
        console.debug('token:' + token);
        console.debug('expired:' + expired);
        console.debug('now_time:' + now_time);
        if (now_time > expired || token == null) {
            refresh_picservice_token();
        }
    });
}

function upload_image(img_src,functionname) {   //现阶段只能接受第二个必须为function(data){}
    var now_time = Date.parse(new Date()) / 1000;
    if (now_time > expired) {
        console.log('now to refresh token');
        refresh_picservice_token();
    }
    console.log('---now start to upload img---');
    console.log(token);
    console.log(expired);
    get_pic_url(function(){
        $.ajax({    //上传图片
            url: pic_url + "ajax.php?action=" + 'picservice.upload_image',
            type: 'post',
            data: {token: token ,img_src: img_src},
            success: function (data) {
                console.log(data);
                functionname(data);
            }
        });
    });
}


function refresh_picservice_token() {
    console.log('refresh_picservice_token!');
    url_path = get_url_path();
    console.log(url_path);
    
    get_pic_url(function(){
        
        __ajax('picservice.get_code',{},function (data) {
            console.debug(data);
            code = data.info.value;
            
            $.ajax({    //获取新的token
                url: pic_url + "ajax.php?action=" + 'picservice.request_token',
                type: 'post',
                data: {code: code ,host: url_path},
                success: function (data) {
                    data = eval("(" + data + ")");
                    console.debug(data);
                    if(data.ret == 'fail') {
                        alert(data.reason);
                        return;
                    }
                    token = data.token;
                    expired = data.expired;
                    __ajax("picservice.save_token",{token: token ,expired: expired});
                }
            });
        });
    });
}

