$(document).ready(function (){
var task_tmp_name = '';
console.log('new_page enter.');
console.log('province_code enter.' + province_code);
$("#distpicker").distpicker('destroy');
$("#distpicker").distpicker({
  province: province_code,
  city: city_code,
  district: district_code
});
                    
$('.dropdownLi').click(function (){
    chooseproject = $(this).find("a").html();
    muffinid = $(this).attr('muffinid');
    $('#dropdownMenu1').html(chooseproject);
    update_title();
});

$('select').click(function (){
    update_title();
});

$('.do_new').click(function (){
    manage_task('new');
});

$('.do_new_and_more').click(function (){
    manage_task('new_and_more');
});

$('.do_modify').click(function (){
    manage_task('modify');
});


});
var update_title = function (){
    console.log(muffinid)
    if (muffinid != '' || muffinid != 0) {
        var projecttitle = $('#dropdownMenu1').html();
        projecttitle = projecttitle.replace(/\s+/g,"");
    }else {
        projecttitle = '';
    }
    var fourthloc = $('#fourthloc').val();
    var province_code = $('#province').val();
    var city_code = $('#city').val();
    var district_code = $('#district').val();
    var provinces = $("#distpicker").distpicker('getDistricts');
    var citys = $("#distpicker").distpicker('getDistricts', province_code);
    var districts = $("#distpicker").distpicker('getDistricts', city_code);
    province_title = province_code != ''? provinces[province_code] : '';
    city_title = province_code != '' && city_code != '' ? citys[city_code] : '';
    district_title = province_code != '' && city_code != '' && district_code != '' ? districts[district_code] : '';
    task_tmp_name = (projecttitle) + city_title + district_title
    $('#title').val(task_tmp_name + fourthloc);
};

var manage_task = function (act) {
    var title = $('#title').val();
    var content = $('#content').val();
    var address = $('#address').val();
    var fourthloc = $('#fourthloc').val();
    var province_code = $('#province').val();
    var city_code = $('#city').val();
    var district_code = $('#district').val();
    
    if (province_code == '' || city_code == '') {
        alert('请选择1级区域和2级区域');
        return false;
    }
    if (muffinid == 0 || muffinid == null || muffinid == '') {
        alert('请选择所属项目！');
        return false;
    }
    if (title == '' || address == '' ) {
        alert('请保证内容完整！');
        return false;
    }
    
    var provinces = $("#distpicker").distpicker('getDistricts');
    var citys = $("#distpicker").distpicker('getDistricts', province_code);
    var districts = $("#distpicker").distpicker('getDistricts', city_code);
    var loc = new Object();
    var province = new Object();
    var city = new Object();
    var district = new Object();

    province.code = province_code;
    province.title = province_code != ''? provinces[province_code] : null;
    city.code = city_code;
    city.title = province_code != '' && city_code != '' ? citys[city_code] : null;
    district.code = district_code;
    district.title = province_code != '' && city_code != '' && district_code != '' ? districts[district_code] : null;
    loc.province = province;
    loc.city = city;
    loc.district = district;
    loc.fourthloc = fourthloc;
    loc = JSON.stringify(loc); 
    
    if (act == 'new') {
        __ajax('admin.task.add',{
          muffinid: muffinid,
          title: title,
          content: content,
          address: address,
          loc: loc
        },function (data){
            console.log(data);
            ret = data.ret;
            if (ret == 'success'){
                document.location.href = '?admin/task/index&projectmuffinid=' +　muffinid;
            }
        });
    }else if (act == 'new_and_more') {
        __ajax('admin.task.add',{
          muffinid: muffinid,
          title: title,
          content: content,
          address: address,
          loc: loc
        },true);
    }else if (act == 'modify') {
        taskid = get_request('taskid');
        __ajax('admin.task.modify',{
          taskid: taskid,
          muffinid: muffinid,
          title: title,
          content: content,
          address: address,
          loc: loc
        },function (data){
            console.log(data);
            ret = data.ret;
            if (ret == 'success'){
                document.location.href = '?admin/task/index&projectmuffinid=' + muffinid;
            }
        });
    }
    
}