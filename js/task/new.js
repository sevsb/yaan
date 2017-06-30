$(document).ready(function (){

$("#distpicker").distpicker('destroy');
$("#distpicker").distpicker({
    province: '省份名',
    city: '城市名',
    district: '区名',
    autoSelect: true,
    placeholder: false
});
  
  $('.do_new').click(function (){
    var title = $('#title').val();
    var content = $('#content').val();
    var address = $('#address').val();
    muffinid = get_request('muffinid');
    var province_code = $('#province').val();
    var city_code = $('#city').val();
    var district_code = $('#district').val();
    var provinces = $("#distpicker").distpicker('getDistricts');
    var citys = $("#distpicker").distpicker('getDistricts', province_code);
    var districts = $("#distpicker").distpicker('getDistricts', city_code);
    var loc = new Object();
    var provice = new Object();
    var city = new Object();
    var district = new Object();
    
    provice.code = province_code
    provice.title = provinces[province_code];
    city.code = city_code;
    city.title = citys[city_code];
    district.code = district_code;
    district.title = districts[district_code];
    loc.provice = provice;
    loc.city = city;
    loc.district = district;
    loc = JSON.stringify(loc); 
    console.log('muffinid:' + muffinid);
    console.log('title:' + title);
    console.log('content:' + content);
    console.log('address:' + address);
    console.log(loc);

    __ajax('task.add',{
      muffinid: muffinid,
      title: title,
      content: content,
      address: address,
      loc: loc

    },function (data){
        console.log(data);
        ret = data.ret;
        if (ret == 'success'){
            document.location.href = '?task/index&muffinid=' +　muffinid;
        }
        
    });
  });
});