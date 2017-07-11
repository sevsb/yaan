$(document).ready(function() {
    $("#distpicker").distpicker('destroy');
    $("#distpicker").distpicker({
        province: '省份名',
        city: '城市名',
        district: '区名',
        autoSelect: true,
        placeholder: false
    });

    $("#viewtask").click(function() {
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

        provice.code = province_code;
        provice.title = provinces[province_code];
        city.code = city_code;
        city.title = citys[city_code];
        district.code = district_code;
        district.title = districts[district_code];
        loc.provice = provice;
        loc.city = city;
        loc.district = district;
        loc = JSON.stringify(loc); 

        var url = "?wechat/api/tasks&loc=" + loc;
        document.location.href = url;

    });
});









