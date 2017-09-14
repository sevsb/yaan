$(function() {
    var _flag = 0;
    var taskid = $('#taskid').val();
    var naireid = get_request("id");
    
    //获取整体数据
    var refresh_data = function (data){
        var data = data.data;
        console.log(data);
        question_list = data.question_list;
        answer_show.naire = data.questionnaire;
        answer_show.question_list = question_list;
        answer_show.assoc_question_list = data.assoc_question_list;
        
        return;
    };
    
    var show_assoc_question = function (question, question_opt){
        __question = $('#question_' + question.id);
        
        question_optionid_list = [];                //选项集合
        question_choosed_optionid_list = [];        //被选中选项集合
        
        __question.find('input').each(function (){
            var oid = $(this).attr('option_id');
            question_optionid_list.push(oid);
        });
        __question.find('input:checked').each(function (){
            var oid = $(this).attr('option_id');
            question_choosed_optionid_list.push(oid);
        });

        //判断是否是父类题
        var parent_question_flag = false;
        for (var j in answer_show.assoc_question_list) {
            for (var m in question_optionid_list) {
                if (j == question_optionid_list[m]) {
                    parent_question_flag = true;
                }
            }
        }
        
        if (parent_question_flag == false) {
            return;
        }
        
        //判断父类题是否被选中关联选项，如选中获取要展示的option_id集合
        console.log('This is a parent question!');
        var child_question_show = false;
        var child_question_list = [];
        for (var j in answer_show.assoc_question_list) {
            for (var m in question_choosed_optionid_list) {
                //console.log(question_choosed_optionid_list[m]);
                if (j == question_choosed_optionid_list[m]) {
                    child_question_show = true;
                    child_question_list.push(j);
                }
            }
        }
        console.log('child_question_show:' + child_question_show);
        console.log(child_question_list);
        if (child_question_show) {      //展示
            if (child_question_list.length > 0) {
                for (var y in child_question_list) {
                    var yy = child_question_list[y];
                    for (var z in answer_show.assoc_question_list[yy]) {
                        var show_qid = answer_show.assoc_question_list[yy][z];
                        console.log(show_qid);
                        $('#question_' + show_qid).removeClass('hidden');
                    }
                }
            }
        } else {                        //隐藏
            for (var oo in question_optionid_list) {
                var ooo = question_optionid_list[oo];
                console.log(ooo);
                if (answer_show.assoc_question_list.hasOwnProperty(ooo)){
                    var questions = answer_show.assoc_question_list[ooo];
                    for (var q in questions) {
                        $('#question_' + questions[q]).addClass('hidden');
                    }
                }
            }
        }
    };
    
    //答卷的主题
    var answer_show = new Vue({
        el: '#answer_show',
        data: {
            all_data: [],
            naire: [],
            question_list: [],
            answer_list: [],
            assoc_question_list: [],
            flag: _flag,
            pageStatus: 1,
            imgRoot: [],
            photosList: [],
        },
        methods: {
            show_pic_dialog: function (){
                this.flag = 2;
                //this.setPageStatus(1);
                $('.card_book_loading').css('display','none');
                $('.card_book').css('display','flex');
                return;
            },
            setPageStatus: function(PageStatus) {
                // v-if 的选择渲染会出现闪现现象
                switch(PageStatus) {
                    case 0:
                        $('.card_book_loading').css('display','flex');
                        $('.card_book').css('display','none');
                        return;
                    case 1:
                        $('.card_book_loading').css('display','none');
                        $('.card_book').css('display','flex');
                        return;
                    default :
                        return;
                }
            },
            radio_click: function (question, question_opt){
                console.log('radio_click');
                var question_id = question.id;
                var value = question_opt.value;
                show_assoc_question(question, question_opt);
                //__request('wechat.api.update_answer', {taskid: taskid, question_id: question_id, qtype: 'radio', value: value}, refresh_data);
                return;
            },
            check_click: function (question, question_opt){
                console.log('check_click');
                var question_id = question.id;
                var value = [];
                $('#question_' + question_id).find('.check_input:checked').each(function (){
                    var v = $(this).val();
                    value.push(v);
                });
                value = value.join(",");
                show_assoc_question(question, question_opt);
                //__request('wechat.api.update_answer', {taskid: taskid, question_id: question_id, qtype: 'check', value: value}, refresh_data);
                return;
            },
            star_click: function (){
                console.log('star_click clicked!');
            },
            range_change: function (question){
                console.log('range_change');
                var question_id = question.id;
                var __self = $('#question_' + question_id).find('input');
                var score = __self.val();
                __self.parent().next('label').html(score + '分');
                
               // __request('wechat.api.update_answer', {taskid: taskid, question_id: question_id, qtype: 'range', value: score}, refresh_data);
                return;
            },
            text_blur: function (question){
                console.log('text_blur');
                var question_id = question.id;
                var __self = $('#question_' + question_id).find('input');
                var value = __self.val();
                
                //__request('wechat.api.update_answer', {taskid: taskid, question_id: question_id, qtype: 'text', value: value}, refresh_data);
                return;
            },
            /*refreshPhotosList: function() {
                vue_wx_view.photosList = __photosList;
            },
            addPhoto: function() {
                wx.chooseImage({
                    count: 1, // 默认9
                    sizeType: ['compressed'], // ['original', 'compressed'] 可以指定是原图还是压缩图，默认二者都有
                    sourceType: ['camera'], // ['album', 'camera'] 可以指定来源是相册还是相机，默认二者都有
                    success: function (res) {
                        var photo = [];
                        var imgData;
                        var localId = res.localIds[0]; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                        wx.getLocalImgData({
                            localId: localId,
                            success: function (res) {
                                imgData = res.localData;
                                // 解决 iOS 下 MIME type 为 image/jgp 的问题
                                imgData = imgData.replace('image/jgp', 'image/jpeg');
                                // 解决 Android 下缺少 MIME type 的问题
                                if(imgData.substr(0, 23).search('data:image/jpeg;base64,') < 0){
                                    imgData = 'data:image/jpeg;base64,' + imgData;
                                }
                                vue_add_photo_modal.showAddPhotoModal(localId, imgData);
                            }
                        });
                    }
                });
            },
            modifyPhoto: function(imgUrl) {
                vue_modify_photo_modal.showModifyPhotoModal(__photosList.getPhotoByImgUrl(imgUrl));
            },
            deletePhoto: function(imgUrl) {
                vue_delete_photo_modal.showDeletePhotoModal(imgUrl);
            },*/
            showSumbitSheetModal: function() {
                $('#submit_sheet_modal').modal('show');
            },
            sumbitSheet: function() {
                $('#submit_sheet_modal').modal('hide');
                return;
                __ajax('wechat.index.sumbitSheet', {
                    taskId: taskid,
                }, function (data) {
                    if(data.ret == 'success'){
                        answer_show.goBack();
                    } else {
                        alert(data.info);
                    }
                });
            },
            goBack: function() {
                window.location.href = location.origin+'/?wechat/index/home';
            }, 
        },
        updated: function() {
            $(".star_input").each(function (){

                var star_count = $(this).attr('star_count');
                $(this).raty({
                    hints: ["1", "2", "3", "4", "5"],
                    path:"/bigsword/yaan/images",
                    score: star_count,
                    click: function (score, evt) {
                        $(this).attr('star_count', score);
                        $(this).val(score);
                        $(this).parent().next('label').html(score + '分');

                        var question_id = $(this).parents('.question_elf').attr('question_id');
                        console.log('raty clicked, score :' + score + ', question_id : ' + question_id);

                        //__request('wechat.api.update_answer', {taskid: taskid, question_id: question_id, qtype: 'star', value: score}, refresh_data);
                        return;
                    }
                });
            });
        }
    });
    
    __request('admin.questionnaire.get_preview_by_naireid',{naireid: naireid}, refresh_data);
    
});   
    


    
 /*var __imgRoot = '{:$imgRoot}';
    var __taskId = {:$taskId};
    var __paperId = {:$paperId};
    var __userId = {:$userId};
    var __answerId = 0;
    // FIXME: 应该将photo与photosList写成一个类
    var __photosList = [];
    wx.config({
        debug: false,
        appId: '{:$signPackage["appid"]}',
        timestamp: {:$signPackage["timestamp"]},
        nonceStr: '{:$signPackage["noncestr"]}',
        signature: '{:$signPackage["signature"]}',
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
                'openCard', 'getLocalImgData' ]
    });*/
    


