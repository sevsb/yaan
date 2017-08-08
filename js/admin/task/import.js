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
                console.log(dd);
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
                show_content += '</tr>';
                for (var i in dd) {
                    var row = dd[i];
                    show_content += '<tr>';
                    show_content += '<td>' + row['A'] + '</td>';
                    show_content += '<td>' + row['B'] + '</td>';
                    show_content += '<td>' + row['C'] + '</td>';
                    show_content += '<td>' + row['D'] + '</td>';
                    show_content += '<td>' + row['E'] + '</td>';
                    show_content += '<td>' + row['F'] + '</td>';
                    show_content += '<td>' + row['G'] + '</td>';
                    show_content += '<td>' + row['H'] + '</td>';
                    show_content += '<td>' + row['I'] + '</td>';
                    show_content += '</tr>';
                }
                show_content += '</table>';
                $('.import_data_show').html(show_content);
            });
        };
        reader.readAsDataURL(import_file);
        return true;

    });

});

