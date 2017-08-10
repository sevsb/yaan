$(document).ready(function (){

    $('#input_file_btn').change(function (){
        console.log('Start to upload import file ...');
        if (typeof FileReader == 'undefined') {
            alert("您的浏览器不支持上传，请更换浏览器重试！");
            return false;
        }
        var whitelist = ["application/msword", "application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application/vnd.ms-excel.sheet.macroEnabled.12"];
        var import_file = this.files[0];
        var typeflag = false;
        for (var id in whitelist) {
            if (whitelist[id] == import_file.type) {
                typeflag = true;
            }
        }
        if (typeflag == false) {
            alert("文件不是支持的类型！");
            return false;
        }
        
        var reader = new FileReader();
        reader.onload = function(e) {
            import_file_URL = e.target.result;
            __request('admin.task.process_import_file', {import_file: import_file_URL},function (dd){

                var show_content = '';
                show_content += '<table class="table">';
                show_content += '<tr>';
                show_content += '<td>店面名称(任务名称)</td>';
                show_content += '<td>省份</td>';
                show_content += '<td>地市</td>';
                show_content += '<td>区县</td>';
                show_content += '<td>乡村/街道(第四级)</td>';
                show_content += '<td>详细地址</td>';
                show_content += '<td>联系人</td>';
                show_content += '<td>联系电话</td>';
                show_content += '<td>备注</td>';
                show_content += '<td>区域编码</td>';
                show_content += '</tr>';
                for (var i in dd) {
                    var row = dd[i];
                    dd[i]['full_address'] = row['B'] + row['C'] + row['D'] + row['E'] + row['F'];
                    show_content += '<tr class="task_row" ino="' + i + '">';
                    show_content += '<td class="title">' + row['A'] + '</td>';
                    show_content += '<td class="province">' + row['B'] + '</td>';
                    show_content += '<td class="city">' + row['C'] + '</td>';
                    show_content += '<td class="district">' + row['D'] + '</td>';
                    show_content += '<td class="fourthloc">' + row['E'] + '</td>';
                    show_content += '<td class="address">' + row['F'] + '</td>';
                    show_content += '<td class="person">' + row['G'] + '</td>';
                    show_content += '<td class="tel">' + row['H'] + '</td>';
                    show_content += '<td class="content">' + row['I'] + '</td>';                    show_content += '<td class="adcode"></td>';
                    show_content += '</tr>';
                }
                show_content += '</table>';
                show_content += '<div class="btn btn-success get_adcode_btn">获取区域编码</div>';
                show_content += "<div style='margin-left: 15px;' class='hide btn btn-primary do_import_btn'>点击导入</div>";
                $('.import_data_show').html(show_content);

                for (var i in dd) {
                    var full_address = dd[i]['full_address'];
                    var city = dd[i]['C'];
                    __get_adcode(full_address, city, i);
                }
                import_task_list = dd;
                console.log(adcode_list);

            });
        };
        reader.readAsDataURL(import_file);
        return true;

    });
    
    $(document).on("click", ".get_adcode_btn", function (){
        console.log('get_adcode_btn clicked...');
        //console.log(import_task_list);
        for (var i in import_task_list) {
            import_task_list[i]['adcode'] = adcode_list[i];
        }
        $('.task_row').each(function (){
            var i = $(this).attr('ino');
            var adcode_show = import_task_list[i]['adcode'];
            $(this).find('.adcode').html(adcode_show);
        });
        $('.do_import_btn').removeClass('hide');
    });
    
    $(document).on("click", ".do_import_btn", function (){
        var flag = true;
        $('.adcode').each(function (){
            if ($(this).html() == '') {
                flag = false;
            }
        });
        if (!flag) {
            alert('编码获取失败，请重新获取！');
            return false;
        }
        console.log('do_import_btn clicked...');
        var muffinid = get_request('muffinid');
        console.log('muffinid muffinid...' + muffinid);
        var import_task_list_final = [];
        for (var i in import_task_list) {
            import_task_list_final[i] = new Object;
            import_task_list_final[i].title = import_task_list[i]['A'];
            import_task_list_final[i].address = import_task_list[i]['F'];
            import_task_list_final[i].content = import_task_list[i]['G'] + ' ' + import_task_list[i]['H'] + ' ' + import_task_list[i]['I'];
            var adcode = import_task_list[i]['adcode'];
            var fourthloc = import_task_list[i]['E'];
            if ( adcode.substring(0,3) == '110' || adcode.substring(0,3) == '120' || adcode.substring(0,3) == '310' || adcode.substring(0,3) == '500' ) {
                var province_code = adcode.substring(0,3) + '000';
                var city_code = adcode.substring(0,3) + '100';
                var district_code = adcode;
            } else {
                var province_code = adcode.substring(0,2) + '0000';
                var city_code = adcode.substring(0,4) + '00';
                var district_code = adcode;
            }
            
            var location = new Object;
            var province = new Object;
            var city = new Object;
            var district = new Object;
            province.code = province_code;
            city.code = city_code;
            district.code = district_code;
            
            province.title = import_task_list[i]['B'];;
            city.title = import_task_list[i]['C'];;
            district.title = import_task_list[i]['D'];;
            
            location.province = province;
            location.city = city;
            location.district = district;
            location.adcode = adcode;
            location.fourthloc = fourthloc;
            console.log(location);
            location = JSON.stringify(location); 
            console.log(location);
            import_task_list_final[i].location = location;
        }
        console.log(import_task_list_final);

        __ajax('admin.task.do_import', {muffinid : muffinid, task_list : import_task_list_final}, function (data){
            console.log(data);
            if (data.ret == 'success') {
                document.location.href = '?admin/task/index&projectmuffinid=' + muffinid + '&flag=1';
            }
        });
    });
    
    
});

var __get_adcode = function (full_address, city, i) {
    adcode_list = [];
    var url = 'http://api.map.baidu.com/geocoder/v2/?address=' + full_address + '&output=json&ak=' + baiduak + '&city=' + city + '&callback=renderReverse';
    console.log(i);
    $.ajax({
        url: url, 
        async: false,
        type: "get",
        dataType: "jsonp",
        jsonp: "callback",
        success: function(data) {
            var longitude = data.result.location.lng;
            var latitude = data.result.location.lat;
            var url = 'http://api.map.baidu.com/geocoder/v2/?ak=' + baiduak + '&callback=renderReverse&location=' + latitude + ',' + longitude + '&output=json&pois=1';
            $.ajax({
                url: url,
                async: false,
                type: "get",
                dataType: "jsonp",
                jsonp: "callback",
                success: function(data) {
                    var adcode = data.result.addressComponent.adcode;
                    adcode_list[i] = adcode;
                },
                fail: function(data) {
                    console.log(data);
                }
            });
        },
        fail: function(data) {
            console.log(data);
        }
    });
                    
    
}
