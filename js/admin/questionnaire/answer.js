$(function() {
    var _flag = 0;
    var taskid = $('#taskid').val();
    
    //获取整体数据
    var refresh_data = function (data){
        var data = data.data;
        console.log(data);
        question_list = data.question_list;
        answer_show.naire = data.questionnaire;
        answer_show.question_list = question_list;

        answer_show.answer_list = data.answer_list;
        answer_show.assoc_question_list = data.assoc_question_list;
    };
    
    /*var show_assoc_quesion = function (option_id){
        console.log('r_and_c clicked!');
        //var option_id =  $(this).attr('option_id');
        console.log(this.attr(('checked')));
        console.log(option_id);
        for (var i in answer_show.assoc_question_list) {
            var pid = answer_show.assoc_question_list[i].parent;
            var cid = answer_show.assoc_question_list[i].child;
            if (option_id == pid) {
                console.log(option_id + " is parent! " + cid + " will be showed!");
                $('#' + cid ).removeClass("hidden")
            } else {
                $('#' + cid ).addClass("hidden")
            }
        }
    };*/
    
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
            radio_click: function (option_id){
                /*console.log('radio_click clicked!');
                console.log(option_id);
                for (var i in answer_show.assoc_question_list) {
                    var pid = answer_show.assoc_question_list[i].parent;
                    var cid = answer_show.assoc_question_list[i].child;
                    if (option_id == pid) {
                        console.log(option_id + " is parent! " + cid + " will be showed!");
                        $('#' + cid ).removeClass("hidden")
                    } else {
                        $('#' + cid ).addClass("hidden")
                    }
                }*/
            },
            check_click: function (option_obj){
                console.log('check_click clicked!');
                option_id = option_obj.id;
                console.log(option_obj);
                console.log(option_id);
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
            },
            showSumbitSheetModal: function() {
                $('#submit_sheet_modal').modal('show');
            },
            sumbitSheet: function() {
                __ajax('wechat.index.sumbitSheet', {
                    taskId: __taskId,
                }, function (data) {
                    if(data.ret == 'success'){
                        vue_wx_view.goBack();
                    } else {
                        alert(data.info);
                    }
                });
            },
            goBack: function() {
                window.location.href = location.origin+'/?wechat/index/home';
            }, */
        },
        updated: function() {
            $(".star_input").each(function (){
                console.log('raty');
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
                        console.log(score);
                        console.log(question_id);

                        __request('wechat.api.update_answer', {taskid: taskid, question_id: question_id, qtype: 'star', value: score}, refresh_data);
                    }
                });
            });
            $('.range_input').click(function (){
                var score = $(this).val();
                $(this).parent().next('label').html(score + '分');
                
                var question_id = $(this).parents('.question_elf').attr('question_id');
                __request('wechat.api.update_answer', {taskid: taskid, question_id: question_id, qtype: 'range', value: score}, refresh_data);
            });
            
            $('.text_input').blur(function (){
                var value = $(this).val();
                var question_id = $(this).parents('.question_elf').attr('question_id');
                
                __request('wechat.api.update_answer', {taskid: taskid, question_id: question_id, qtype: 'text', value: value}, refresh_data);
            });
            
            $('.radio_input').click(function (){
                var value = $(this).val();
                var question_id = $(this).parents('.question_elf').attr('question_id');
                
                __request('wechat.api.update_answer', {taskid: taskid, question_id: question_id, qtype: 'radio', value: value}, refresh_data);
            });
            
            $('.check_input').click(function (){
                var value = [];
                
                var question_id = $(this).parents('.question_elf').attr('question_id');
                $(this).parents('.question_elf').find('.check_input:checked').each(function (){
                    var v = $(this).val();
                    value.push(v);
                });
                value = value.join(",");

                __request('wechat.api.update_answer', {taskid: taskid, question_id: question_id, qtype: 'check', value: value}, refresh_data);
            });
        }
    });
    
    __request('wechat.api.get_answer_by_taskid',{taskid: taskid}, refresh_data);
    
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
    


