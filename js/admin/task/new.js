$(document).ready(function (){
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
});
  
$('.do_new').click(function (){
    var title = $('#title').val();
    var content = $('#content').val();
    var address = $('#address').val();
    //muffinid = get_request('muffinid');
    var province_code = $('#province').val();
    var city_code = $('#city').val();
    var district_code = $('#district').val();
    var provinces = $("#distpicker").distpicker('getDistricts');
    var citys = $("#distpicker").distpicker('getDistricts', province_code);
    var districts = $("#distpicker").distpicker('getDistricts', city_code);
    var loc = new Object();
    var province = new Object();
    var city = new Object();
    var district = new Object();
    console.log('province_code:' + province_code);
    console.log('city_code:' + city_code);
    console.log('district_code:' + district_code);
    /*if (province_code == '' || city_code == '' || district_code == '' ) {
        alert('请选择区域');
        return false;
    }*/
    province.code = province_code;
    province.title = province_code != ''? provinces[province_code] : null;
    city.code = city_code;
    city.title = province_code != '' && city_code != '' ? citys[city_code] : null;
    district.code = district_code;
    district.title = province_code != '' && city_code != '' && district_code != '' ? districts[district_code] : null;
    loc.province = province;
    loc.city = city;
    loc.district = district;
    loc = JSON.stringify(loc); 
    console.log('muffinid:' + muffinid);
    console.log('title:' + title);
    console.log('content:' + content);
    console.log('address:' + address);
    console.log(loc);
    //return false;
    if (muffinid == 0 || muffinid == null || muffinid == '') {
        alert('请选择所属项目！');
        return false;
    }
    if (title == '' || address == '' ) {
        alert('请保证内容完整！');
        return false;
    }
    //return false;
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
});

$('.do_modify').click(function (){
    var title = $('#title').val();
    var content = $('#content').val();
    var address = $('#address').val();
    taskid = get_request('taskid');
    var province_code = $('#province').val();
    var city_code = $('#city').val();
    var district_code = $('#district').val();
    var provinces = $("#distpicker").distpicker('getDistricts');
    var citys = $("#distpicker").distpicker('getDistricts', province_code);
    var districts = $("#distpicker").distpicker('getDistricts', city_code);
    var loc = new Object();
    var province = new Object();
    var city = new Object();
    var district = new Object();
    console.log('province_code:' + province_code);
    console.log('city_code:' + city_code);
    console.log('district_code:' + district_code);
    /*if (province_code == '' || city_code == '' || district_code == '' ) {
        alert('请选择区域');
        return false;
    }*/
    province.code = province_code;
    province.title = province_code != ''? provinces[province_code] : null;
    city.code = city_code;
    city.title = province_code != '' && city_code != '' ? citys[city_code] : null;
    district.code = district_code;
    district.title = province_code != '' && city_code != '' && district_code != '' ? districts[district_code] : null;
    loc.province = province;
    loc.city = city;
    loc.district = district;
    loc = JSON.stringify(loc); 
    console.log('taskid:' + taskid);
    console.log('muffinid:' + muffinid);
    console.log('title:' + title);
    console.log('content:' + content);
    console.log('address:' + address);
    console.log(loc);
    //return false;
    if (muffinid == 0 || muffinid == null || muffinid == '') {
        alert('请选择所属项目！');
        return false;
    }
    if (title == '' || address == '' ) {
        alert('请保证内容完整！');
        return false;
    }
    
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
});


});