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
                show_content += '<td></td>';
                show_content += '<td>地市</td>';
                show_content += '<td>区县</td>';
                show_content += '<td>乡村/街道(第四级)</td>';
                show_content += '<td>详细地址</td>';
                show_content += '<td>联系人</td>';
                show_content += '<td>联系电话</td>';
                show_content += '<td>备注</td>';
                show_content += '</tr>';
                for (var i in dd) {
                    var row = dd[i];
                    dd[i]['full_address'] = row['B'] + row['C'] + row['D'] + row['E'] + row['F'];
                    show_content += '<tr class="task_row">';
                    show_content += '<td class="title">' + row['A'] + '</td>';
                    show_content += '<td class="province">' + row['B'] + '</td>';
                    show_content += '<td class="city">' + row['C'] + '</td>';
                    show_content += '<td class="district">' + row['D'] + '</td>';
                    show_content += '<td class="fourthloc">' + row['E'] + '</td>';
                    show_content += '<td class="address">' + row['F'] + '</td>';
                    show_content += '<td class="person">' + row['G'] + '</td>';
                    show_content += '<td class="tel">' + row['H'] + '</td>';
                    show_content += '<td class="content">' + row['I'] + '</td>';
                    show_content += '</tr>';
                }
                show_content += '</table>';
                show_content += '<div class="btn btn-success do_import_btn">导入</div>';
                $('.import_data_show').html(show_content);

                for (var i in dd) {
                    var full_address = dd[i]['full_address'];
                    var city = dd[i]['C'];

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
                            console.log(i);
                            var url = 'http://api.map.baidu.com/geocoder/v2/?ak=' + baiduak + '&callback=renderReverse&location=' + latitude + ',' + longitude + '&output=json&pois=1';
                            $.ajax({
                                url: url,
                                async: false,
                                type: "get",
                                dataType: "jsonp",
                                jsonp: "callback",
                                success: function(data) {
                                    adcode = data.result.addressComponent.adcode;
                                    console.log(i);
                                    console.log(adcode);
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
                import_task_list = dd;
            });
        };
        reader.readAsDataURL(import_file);
        return true;

    });
    
    $(document).on("click", ".do_import_btn", function (){
        console.log('Do_import_btn clicked...');
        console.log(import_task_list);
    });
});

