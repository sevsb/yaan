$(document).ready(function() {

//console.log("ahaha");
    var refresh_sheet_data = function(data) {
        console.debug(data);
        //return;
        sheet_lsit = data.data.sheets;
        all_options = data.data.all_options;
        //answer_sheet_list = data.data.answer_sheets;
        //return ;
        var mapurl = "http://api.map.baidu.com/staticimage/v2?ak=" + baiduak + "&mcode=666666&center=LONGITUDE,LATITUDE&width=300&height=200&zoom=13&markers=LONGITUDE,LATITUDE";
        
        for (var k in sheet_lsit) {

            for (var k1 in sheet_lsit[k].answers[0].reply.data) {
                //console.log(k1);
                //console.log(sheet_lsit[k].answers[0].reply.data[k1].uploadloc);
                if ((sheet_lsit[k].answers[0].reply.data[k1].uploadloc) !== null) {
                    var latitude1 = sheet_lsit[k].answers[0].reply.data[k1].uploadloc.latitude;
                    var longitude1 = sheet_lsit[k].answers[0].reply.data[k1].uploadloc.longitude;
                    sheet_lsit[k].answers[0].reply.data[k1].uploadloc.mapurl = mapurl.replace(new RegExp(/LONGITUDE/g), longitude1).replace(new RegExp(/LATITUDE/g), latitude1);
                }
                if (typeof(sheet_lsit[k].answers[0].reply.data[k1].exifloc.latitude) != "undefined") {
                    var latitude2 = sheet_lsit[k].answers[0].reply.data[k1].exifloc.latitude;
                    var longitude2 = sheet_lsit[k].answers[0].reply.data[k1].exifloc.longitude;
                    sheet_lsit[k].answers[0].reply.data[k1].exifloc.mapurl = mapurl.replace(new RegExp(/LONGITUDE/g), longitude1).replace(new RegExp(/LATITUDE/g), latitude1);
                } else {
                    sheet_lsit[k].answers[0].reply.data[k1].exifloc.mapurl = "";
                }
            }
            
            //有答卷的img_list的mapurl拆分
            if (sheet_lsit[k].questions !== false) {
                //console.log(sheet_lsit[k].questions);
                for (var m in sheet_lsit[k].questions) {
                    //console.log(sheet_lsit[k].questions[m].img_list);
                    if(sheet_lsit[k].questions[m].img_list.length != 0){
                        //console.log(sheet_lsit[k].questions[m].img_list);
                        for (var n in sheet_lsit[k].questions[m].img_list) {
                            console.log(sheet_lsit[k].questions[m].img_list[n]);
                            //continue;
                            var latitude1 = sheet_lsit[k].questions[m].img_list[n].imgLocation.latitude;
                            var longitude1 = sheet_lsit[k].questions[m].img_list[n].imgLocation.longitude;
                            sheet_lsit[k].questions[m].img_list[n].mapurl = mapurl.replace(new RegExp(/LONGITUDE/g), longitude1).replace(new RegExp(/LATITUDE/g), latitude1);
                        }
                    }
                }
            }
        
            
        }
        //console.debug(sheet_lsit);
        sheetlist.sheetlist = sheet_lsit;
        sheetlist.all_options = all_options;
    };

    var sheetlist = new Vue({
        el: '#sheetlist',
        data: {
            sheetlist: null,
            viewsheetkey: 0,
            viewreplykey: 0,
            viewquestionkey: 0,
            all_options: [],
            viewimageurl: null,
            uploadmapurl: null,
            exifmapurl: null,
            showviewsheet: false,
            showviewimage: false,
            showviewimage_new: false,
            now_photo_list: [],
            apiquestions: [],
            now_question_id: 0,
        },
        methods: {
            viewSheet: function(event) {
                var target = event.currentTarget;
                var sheetkey = $(target).attr("sheet");
                var taskid = $(target).attr("task_id");
                console.log(taskid);
                __request('wechat.api.get_answer_by_taskid', {taskid: taskid}, function (data){
                    sheetlist.apiquestions = data.data.question_list;
                    sheetlist.viewsheetkey = sheetkey;
                    sheetlist.showviewsheet = true;
                    console.log(sheetlist.apiquestions);
                });
                
                
            },
            closeViewSheet: function(event) {
                sheetlist.showviewsheet = false;
                sheetlist.showviewimage = false;
            },
            closeViewImage: function(event) {
                sheetlist.showviewimage = false;
            },
            closeViewImage_new: function(event) {
                sheetlist.showviewimage_new = false;
            },
            viewNextImage: function(event) {
                if (sheetlist.viewreplykey < sheetlist.sheetlist[sheetlist.viewsheetkey].answers[0].reply.data.length - 1) {
                    sheetlist.viewreplykey++;
                }
            },
            viewNextImage_new: function(event) {
                if (sheetlist.viewreplykey < sheetlist.sheetlist[sheetlist.viewsheetkey].questions[sheetlist.viewquestionkey].img_list.length - 1) {
                    sheetlist.viewreplykey++;
                }
            },
            viewPrevImage: function(event) {
                if (sheetlist.viewreplykey > 0) {
                    sheetlist.viewreplykey--;
                }
            },
            viewPrevImage_new: function(event) {
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
            viewImage_new: function(viewsheetkey, questionid, datakey) {
                sheetlist.viewsheetkey = viewsheetkey;
                sheetlist.viewquestionkey = questionid;
                sheetlist.viewreplykey = datakey;
                sheetlist.showviewimage_new = true;
                return;
            },
            pass: function(event) {
                var sid = sheetlist.sheetlist[sheetlist.viewsheetkey].info.id;
                __request("admin.sheet.review", { sheet: sid, review: "PASS" }, refresh_sheet_data);
            },
            reject: function(event) {
                var sid = sheetlist.sheetlist[sheetlist.viewsheetkey].info.id;
                __request("admin.sheet.review", { sheet: sid, review: "REJECT" }, refresh_sheet_data);
            }
        },
        updated: function() {           
            console.log("updated...");
            $('#mytable').DataTable().destroy();
             
            $('#mytable').dataTable({
              "oLanguage": {
                "sLengthMenu": "每页显示 _MENU_ 条记录",
                "sZeroRecords": "对不起，查询不到任何相关数据",
                "sInfo": "当前显示 _START_ 到 _END_ 条，共 _TOTAL_条记录",
                "sInfoEmtpy": "找不到相关数据",
                "sInfoFiltered": "数据表中共为 _MAX_ 条记录)",
                "sProcessing": "正在加载中...",
                "sSearch": "搜索",
                "oPaginate": {
                  "sFirst": "第一页",
                  "sPrevious":" 上一页 ",
                  "sNext": " 下一页 ",
                  "sLast": " 最后一页 "
                  },
                }
            });
            
            $(".star_input").each(function (){
                var star_count = $(this).attr('star_count');
                $(this).raty({
                    hints: ["1", "2", "3", "4", "5"],
                    path:"/bigsword/yaan/images",
                    score: star_count,
                    readOnly: true,
                    click: function (score, evt) {}
                });
            });
            
        },   
    });

    __request("admin.sheet.sheetlist", { }, refresh_sheet_data);
    //__request("admin.sheet.answer_list", { }, refresh_sheet_data);

});

