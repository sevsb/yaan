$(document).ready(function() {
    var sheetlist = new Vue({
        el: '#sheetlist',
        data: {
            sheetlist: null,
            viewsheetkey: 0,
            showviewsheet: false,
        },
        methods: {
            viewSheet: function(event) {
                var target = event.currentTarget;
                var sheetkey = $(target).attr("sheet");
                sheetlist.viewsheetkey = sheetkey;
                sheetlist.showviewsheet = true;
            }
        }
    });

    __request("admin.sheet.sheetlist", { }, function(data) {

        var mapurl = "http://api.map.baidu.com/staticimage/v2?ak=fqiHuU0wMsAxwzQfphk0PvLdrLB3flrZ&mcode=666666&center=LONGITUDE,LATITUDE&width=300&height=200&zoom=13&markers=LONGITUDE,LATITUDE";
        for (var k in data.data) {
            for (var k1 in data.data[k].answers[0].reply.data) {
                var latitude1 = data.data[k].answers[0].reply.data[k1].uploadloc.latitude;
                var longitude1 = data.data[k].answers[0].reply.data[k1].uploadloc.longitude;
                data.data[k].answers[0].reply.data[k1].uploadloc.mapurl = mapurl.replace(new RegExp(/LONGITUDE/g), longitude1).replace(new RegExp(/LATITUDE/g), latitude1);

                if (typeof(data.data[k].answers[0].reply.data[k1].exifloc.latitude) != "undefined") {
                    var latitude2 = data.data[k].answers[0].reply.data[k1].exifloc.latitude;
                    var longitude2 = data.data[k].answers[0].reply.data[k1].exifloc.longitude;
                    data.data[k].answers[0].reply.data[k1].exifloc.mapurl = mapurl.replace(new RegExp(/LONGITUDE/g), longitude1).replace(new RegExp(/LATITUDE/g), latitude1);
                } else {
                    data.data[k].answers[0].reply.data[k1].exifloc.mapurl = "";
                }
            }
        }
        console.debug(data.data);
        sheetlist.sheetlist = data.data;
    });

});

