$(document).ready(function() {
    var refresh_sheet_data = function(data) {
        console.debug(data);
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
    };

    var sheetlist = new Vue({
        el: '#sheetlist',
        data: {
            sheetlist: null,
            viewsheetkey: 0,
            viewreplykey: 0,
            viewimageurl: null,
            uploadmapurl: null,
            exifmapurl: null,
            showviewsheet: false,
            showviewimage: false,
        },
        methods: {
            viewSheet: function(event) {
                var target = event.currentTarget;
                var sheetkey = $(target).attr("sheet");
                sheetlist.viewsheetkey = sheetkey;
                sheetlist.showviewsheet = true;
            },
            closeViewSheet: function(event) {
                sheetlist.showviewsheet = false;
                sheetlist.showviewimage = false;
            },
            closeViewImage: function(event) {
                sheetlist.showviewimage = false;
            },
            viewNextImage: function(event) {
                if (sheetlist.viewreplykey < sheetlist.sheetlist[sheetlist.viewsheetkey].answers[0].reply.data.length - 1) {
                    sheetlist.viewreplykey++;
                }
            },
            viewPrevImage: function(event) {
                if (sheetlist.viewreplykey > 0) {
                    sheetlist.viewreplykey--;
                }
            },
            viewImage: function(event) {
                var target = event.currentTarget;
                // var src = $(target).attr("origsrc");
                var sheetkey = $(target).attr("sheetkey");
                var datakey = $(target).attr("datakey");

                sheetlist.viewreplykey = datakey;

                sheetlist.viewimageurl = sheetlist.sheetlist[sheetkey].answers[0].reply.data[datakey].image;
                sheetlist.uploadmapurl = sheetlist.sheetlist[sheetkey].answers[0].reply.data[datakey].uploadloc.mapurl;
                sheetlist.exifmapurl = sheetlist.sheetlist[sheetkey].answers[0].reply.data[datakey].exifloc.mapurl;
                sheetlist.showviewimage = true;
            },
            pass: function(event) {
                var sid = sheetlist.sheetlist[sheetlist.viewsheetkey].info.id;
                __request("admin.sheet.review", { sheet: sid, review: "PASS" }, refresh_sheet_data);
            },
            reject: function(event) {
                var sid = sheetlist.sheetlist[sheetlist.viewsheetkey].info.id;
                __request("admin.sheet.review", { sheet: sid, review: "REJECT" }, refresh_sheet_data);
            }
        }
    });

    __request("admin.sheet.sheetlist", { }, refresh_sheet_data);

});

