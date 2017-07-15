var __log = function(msg) {
    msg = JSON.stringify(msg); 
    var m = $("#log").html();
    m += "\n" + msg;
    $("#log").html(m);
}

var cityname = function(latitude, longitude, callback) {
    $.ajax({
        url: 'http://api.map.baidu.com/geocoder/v2/?ak=' + baiduak + '&callback=renderReverse&location=' + latitude + ',' + longitude + '&output=json&pois=1',
        type: "get",
        dataType: "jsonp",
        jsonp: "callback",
        success: function (data) {
            var province = data.result.addressComponent.province;
            var cityname = data.result.addressComponent.city;
            var district = data.result.addressComponent.district;
            var street = data.result.addressComponent.street;
            var street_number = data.result.addressComponent.street_number;
            var formatted_address = data.result.formatted_address;
            var data = {
                province: province,
                city: cityname,
                district: district,
            };
            if (typeof callback == "function") {
                callback(data);
            }
        }
    });
}

$(document).ready(function() {
    var refresh_cityname = function(data) {
        console.debug(data);
        __log(data);
        __log(data.province);
        __log(data.city);
        __log(data.district);

        $("#province").val(data.province);
        $("#province").trigger("change");
        $("#city").val(data.city);
        $("#city").trigger("change");
        $("#district").val(data.district);
        $("#district").trigger("change");

        $("#current-location").html(data.province + data.city + data.district);

        $("#viewtask").trigger("click");
    }

    var refresh_location = function(lat, lon) {
        cityname(lat, lon, refresh_cityname);
    }

    var get_location = function() {

        if (typeof(wx) != 'undefined' && isWechatBrowser()) {
            wx.ready(function () {
                wx.getLocation({
                    type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
                    success: function (res) {
                        var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
                        var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
                        var speed = res.speed; // 速度，以米/每秒计
                        var accuracy = res.accuracy; // 位置精度
                        // alert(latitude+', '+longitude+', '+speed+', '+accuracy);
                        refresh_location(latitude, longitude);
                    }
                });
            });
        } else if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                console.debug(position);
                var latitude = position.coords.latitude;
                var longitude = position.coords.longitude;
                // alert(latitude+', '+longitude); // 36.1958, 120.5155
                refresh_location(latitude, longitude);
            }, function (error) {
                console.debug(error);
                show_my_tasks();
            });
        }
    };
    get_location();

    $("#distpicker").distpicker('destroy');
    $("#distpicker").distpicker({
        province: '省份名',
        city: '城市名',
        district: '区名',
        autoSelect: true,
        placeholder: false, 
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

                tasks.taskinfo = tasks.tasks[taskkey];
                tasks.viewtaskkey = taskkey;
                tasks.showtasklist = false;
                tasks.showtaskinfo = true;
            },
            accept: function(event) {
                var tid = tasks.tasks[tasks.viewtaskkey].id;
                // console.debug(tid);
                __request("wechat.api.accept", { task: tid }, function(data) {
                    // console.debug(data);
                    tasks.tasks = data;
                });
            },
            gosheet: function(event) {
                var tid = tasks.tasks[tasks.viewtaskkey].id;
                go("wechat/index/sheet", { task: tid });
            },
            goback: function(event) {
                tasks.showtasklist = true;
                tasks.showtaskinfo = false;
            }
        }
    });


    $("#viewtask").click(function() {
        var province = $('#province').val();
        var city = $('#city').val();
        var district = $('#district').val();
        var loc = {
            province: province,
            city: city,
            district: district
        };
        loc = JSON.stringify(loc); 

        __request("wechat.api.tasks", { loc: loc }, function(data) {
            // console.debug(data);
            tasks.tasks = data;
            tasks.showtasklist = true;
            tasks.showtaskinfo = false;
        });
    });

    var show_my_tasks = function() {
        var province = "";
        var city = "";
        var district = "";
        var loc = {
            province: province,
            city: city,
            district: district
        };
        loc = JSON.stringify(loc); 

        __request("wechat.api.tasks", { loc: loc }, function(data) {
            // console.debug(data);
            tasks.tasks = data;
            tasks.showtasklist = true;
            tasks.showtaskinfo = false;
        });
    };

});









