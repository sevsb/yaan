$(document).ready(function() {
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
            showtasklist: false,
            showtaskinfo: false,
            viewtaskkey: 0,
            tasks: null,
        },
        methods: {
            viewTask: function(event) {
                var target = event.currentTarget;
                var taskkey = $(target).attr("taskkey");
                console.debug(target);

                tasks.taskinfo= tasks.tasks[taskkey];
                tasks.viewtaskkey = taskkey;
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
            tasks.showtasklist = true;
            tasks.showtaskinfo = false;
        });
    });



});









