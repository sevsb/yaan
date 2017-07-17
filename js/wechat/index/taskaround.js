var __log = function(msg) {
    return;
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
            var adcode = data.result.addressComponent.adcode;
            var data = {
                province: province,
                city: cityname,
                district: district,
                adcode: adcode,
            };
            if (typeof callback == "function") {
                callback(data);
            }
        }
    });
}

$(document).ready(function() {
    __log("1");
    var refresh_cityname = function(data) {
        console.debug(data);
        __log(data);
        __log(data.province);
        __log(data.city);
        __log(data.district);
        __refresh_tasks({province: data.province, city: data.city, district: data.district, adcode: data.adcode});
    }

    var refresh_location = function(lat, lon) {
        cityname(lat, lon, refresh_cityname);
    }

    var get_location = function() {
        __log("4");
        __log("wx_appId = " + wx_appId);
        __log("wx_timestamp = " + wx_timestamp);
        __log("wx_nonceStr = " + wx_nonceStr);
        __log("wx_signature = " + wx_signature);

        if (typeof(wx) != 'undefined' && isWechatBrowser()) {
            wx.config({
                debug: false, // 如果不需要获取 ticket 成功的 alert 就改成 false
                appId: wx_appId,
                timestamp: wx_timestamp,
                nonceStr: wx_nonceStr,
                signature: wx_signature,
                jsApiList : [ 'checkJsApi', 'onMenuShareTimeline',
                    'onMenuShareAppMessage', 'onMenuShareQQ',
                    'onMenuShareWeibo', 'hideMenuItems',
                    'showMenuItems', 'hideAllNonBaseMenuItem',
                    'showAllNonBaseMenuItem', 'translateVoice',
                    'startRecord', 'stopRecord', 'onRecordEnd',
                    'playVoice', 'pauseVoice', 'stopVoice',
                    'uploadVoice', 'downloadVoice', 'chooseImage',
                    'previewImage', 'uploadImage', 'downloadImage',
                    'getNetworkType', 'openLocation', 'getLocation',
                    'hideOptionMenu', 'showOptionMenu', 'closeWindow',
                    'scanQRCode', 'chooseWXPay',
                    'openProductSpecificView', 'addCard', 'chooseCard',
                    'openCard' ]
            });
            wx.ready(function () {
                wx.getLocation({
                    type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
                    success: function (res) {
                        var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
                        var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
                        var speed = res.speed; // 速度，以米/每秒计
                        var accuracy = res.accuracy; // 位置精度
                        // alert(latitude+', '+longitude+', '+speed+', '+accuracy);true

                        var url = "http://api.map.baidu.com/geoconv/v1/?coords=" + longitude + "," + latitude + "&from=1&to=5&ak=" + baiduak;
                        __log(url);
                        $.ajax({
                            url: url,
                            type: "get",
                            dataType: "jsonp",
                            jsonp: "callback",
                            success: function(data) {
                                __log(data);
                                if (data.status != 0) {
                                    tasks.pagestatus = 1;
                                    return;
                                }

                                var x = data.result[0].x;
                                var y = data.result[0].y;
                                refresh_location(y, x);

                            },
                            fail: function(data) {
                                tasks.pagestatus = 1;
                            }
                        });
                    },
                    cancel: function(res) {
                        tasks.pagestatus = 1;
                    },
                    fail: function(res) {
                        tasks.pagestatus = 1;
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
                // tasks.pagestatus = 1;

                __refresh_tasks({province: "山东省", city: "青岛市", district: "崂山区"});
            });
        } else {
            tasks.pagestatus = 1;
        }
    };
    __log("3");
    get_location();

    var tasks = new Vue({
        el: '#task-wrapper',
        data: {
            pagestatus: 0,
            viewtaskkey: 0,
            tasks: null,
        },
        methods: {
            viewTask: function(event) {
                var target = event.currentTarget;
                var taskkey = $(target).attr("taskkey");

                tasks.taskinfo = tasks.tasks[taskkey];
                tasks.viewtaskkey = taskkey;
                tasks.pagestatus = 4;
            },
            accept: function(event) {
                var tid = tasks.tasks[tasks.viewtaskkey].id;
                // console.debug(tid);
                __request("wechat.api.accept", { task: tid }, function(res) {
                    // console.debug(res);

                    $("#toast").removeClass("hidden");
                    setTimeout(function() {
                        $("#toast").addClass("hidden");
                    }, 2000);

                    tasks.tasks = res.data;
                    if (res.data.length == 0) {
                        tasks.pagestatus = 2;
                    } else {
                        tasks.pagestatus = 3;
                    }
                });
            },
            goback: function(event) {
                tasks.pagestatus = 3;
            }
        }
    });


    var __refresh_tasks = function(data) {
        var loc = {
            province: data.province,
            city: data.city,
            district: data.district,
            adcode: data.adcode
        };
        loc = JSON.stringify(loc); 

        __request("wechat.api.taskaround", { loc: loc }, function(res) {
            console.debug(res);
            tasks.tasks = res.data;
            if (res.data.length == 0) {
                tasks.pagestatus = 2;
            } else {
                tasks.pagestatus = 3;
            }
        });
    };
});









