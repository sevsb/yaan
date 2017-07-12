$(document).ready(function() {
    wx.ready(function () {
        wx.getLocation({
            type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
            success: function (res) {
                var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
                var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
                var speed = res.speed; // 速度，以米/每秒计
                var accuracy = res.accuracy; // 位置精度
                alert(latitude+', '+longitude+', '+speed+', '+accuracy);
            }
        });
    });

    $("#distpicker").distpicker('destroy');
    $("#distpicker").distpicker({
        province: '省份名',
        city: '城市名',
        district: '区名',
        autoSelect: true,
        placeholder: false
    });

    var tasks = new Vue({
        el: '#taskinfos',
        data: {
            showmytasks: false,
            showtasklist: false,
            showtaskinfo: false,
            viewtaskkey: 0,
            tasks: null,
            mytasks: null,
        },
        methods: {
            viewTask: function(event) {
                var target = event.currentTarget;
                var taskkey = $(target).attr("taskkey");
                console.debug(target);

                tasks.taskinfo= tasks.tasks[taskkey];
                tasks.viewtaskkey = taskkey;
                tasks.showmytasks = false;
                tasks.showtasklist = false;
                tasks.showtaskinfo = true;
            },
            accept: function(event) {
                var tid = tasks.tasks[tasks.viewtaskkey].id;
                console.debug(tid);
                __request("wechat.api.accept", { task: tid }, function(data) {
                    console.debug(data);
                    tasks.tasks = data;
                });
            },
            gosheet: function(event) {
                var tid = tasks.tasks[tasks.viewtaskkey].id;
                go("wechat/index/sheet", { task: tid });
            }
        }
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

        __request("wechat.api.tasks", { loc: loc }, function(data) {
            console.debug(data);
            tasks.tasks = data;
            tasks.showmytasks = true;
            tasks.showtasklist = true;
            tasks.showtaskinfo = false;
        });
    });

    __request("wechat.api.mytasks", {}, function(data) {
        console.debug(data);
        tasks.mytasks = data;
        tasks.showmytasks = true;
        tasks.showtasklist = false;
        tasks.showtaskinfo = false;
    });


});









