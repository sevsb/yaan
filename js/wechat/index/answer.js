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
        vue_wx_view.photo_list = data.photo_list;
        vue_wx_view.answerid = data.answerid;
        return;
    };
    

    //答卷的主体
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
        },
        methods: {
            show_pic_dialog: function (question){
                console.log(question);
                answer_show.flag = 1;
                vue_wx_view.question = question;
                vue_wx_view.flag = 1;
                vue_wx_view.qst_photosList = vue_wx_view.photo_list[question.id] ? vue_wx_view.photo_list[question.id] : [];
                //vue_wx_view.qst_photosList = vue_wx_view.photo_list[question.id];
                console.log(vue_wx_view.qst_photosList);
            },
            radio_click: function (question, question_opt){
                console.log('radio_click');
                var question_id = question.id;
                var value = question_opt.value;
                
                __request('wechat.api.update_answer', {taskid: taskid, question_id: question_id, qtype: 'radio', value: value}, refresh_data);
                return;
            },
            check_click: function (question){
                console.log('check_click');
                var question_id = question.id;
                var value = [];
                $('#question_' + question_id).find('.check_input:checked').each(function (){
                    var v = $(this).val();
                    value.push(v);
                });
                value = value.join(",");

                __request('wechat.api.update_answer', {taskid: taskid, question_id: question_id, qtype: 'check', value: value}, refresh_data);
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
                
                __request('wechat.api.update_answer', {taskid: taskid, question_id: question_id, qtype: 'range', value: score}, refresh_data);
                return;
            },
            text_blur: function (question){
                console.log('text_blur');
                var question_id = question.id;
                var __self = $('#question_' + question_id).find('input');
                var value = __self.val();
                
                __request('wechat.api.update_answer', {taskid: taskid, question_id: question_id, qtype: 'text', value: value}, refresh_data);
                return;
            },
            showSumbitSheetModal: function() {
                $('#submit_sheet_modal').modal('show');
            },
            sumbitSheet: function() {
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

                        __request('wechat.api.update_answer', {taskid: taskid, question_id: question_id, qtype: 'star', value: score}, refresh_data);
                        return;
                    }
                });
            });
        }
    });
    
    
    //上传图片模块
    var vue_wx_view = new Vue({
        el: '#wx_view',
        data: {
            flag: 0,
            pageStatus: 0,
            card_book_loading: 0,
            uploading_alert: 0,
            upload_success_alert: 0,
            upload_fail_alert: 0,
            imgRoot: __imgRoot,
            photosList: [],
            qst_photosList: [],
        },
        methods: {
            setPageStatus: function(PageStatus) {
                // v-if 的选择渲染会出现闪现现象
                switch(PageStatus) {
                    case 0:
                        setTimeout(function() {
                            $('.card_book_loading').css('display','flex');
                            $('.card_book').css('display','none');
                        }, 0);
                        return;
                    case 1:
                        setTimeout(function() {
                            $('.card_book_loading').css('display','none');
                            $('.card_book').css('display','flex');
                        }, 0);
                        return;
                    default :
                        return;
                }
            },
            goBack: function() {
                answer_show.flag = 0;
                vue_wx_view.flag = 0;
            },
            refreshPhotosList: function (id) {
                vue_wx_view.photo_list[id] = vue_wx_view.qst_photosList;
            },
            modifyPhoto: function(imgUrl) {
                qst_photosList = vue_wx_view.qst_photosList;
                vue_modify_photo_modal.showModifyPhotoModal(qst_photosList.getPhotoByImgUrl(imgUrl));
            },
            deletePhoto: function(imgUrl) {
                //qst_photosList = vue_wx_view.qst_photosList;
                vue_delete_photo_modal.showDeletePhotoModal(imgUrl);
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
        }
    });
    
    //添加图片提示框
    var vue_add_photo_modal = new Vue({
        el: '#add_photo_modal',
        data: {
            imgUrl: '',
            imgData: '',
            imgContent: '',
        },
        methods: {
            showAddPhotoModal: function(imgUrl, imgData, imgContent = ''){
                vue_add_photo_modal.imgUrl = imgUrl;
                vue_add_photo_modal.imgData = imgData;
                vue_add_photo_modal.imgContent = imgContent;
                $('#add_photo_modal').modal('show');
            },
            updatePhoto: function(){
                $('.uploading_alert').fadeIn(100);
                $('#add_photo_modal').modal('hide');
                var photo = {};
                wx.getLocation({
                    type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
                    success: function (res) {
                        photo.imgLocation = {};
                        photo.imgLocation.latitude = res.latitude;
                        photo.imgLocation.longitude = res.longitude;
                        photo.imgLocation.accuracy =  res.accuracy;
                    }
                });
                photo.imgContent = $('#add_photo_modal_img_content').val();
                __ajax('wechat.index.updateImg', {
                    imgData: vue_add_photo_modal.imgData
                }, function (data) {
                    console.log(data);
                    //return;
                    if(data.ret == 'success'){
                        photo.imgUrl = data.imgUrl;
                        vue_wx_view.qst_photosList.push(photo);
                        __ajax('wechat.index.new_updatePhotosList', {
                            questionid: vue_wx_view.question.id,
                            answerid: vue_wx_view.answerid,
                            photosList: vue_wx_view.qst_photosList,
                        }, function (data) {
                            if(data.ret == 'success'){
                                $('.uploading_alert').hide();
                                $('.upload_success_alert').show();
                                vue_wx_view.refreshPhotosList(vue_wx_view.question.id);
                                setTimeout(function(){
                                    $('.upload_success_alert').fadeOut(1000);;
                                },2000)
                            }else {
                                alert(data.info);
                                $('.uploading_alert').hide();
                                $('.upload_fail_alert').show();
                                setTimeout(function(){
                                    $('.upload_fail_alert').fadeOut(1000);;
                                },2000)
                            }
                        });
                    }else {
                        alert(data.info);
                    }
                });
            }
        }
    });
    
    //修改图片提示框
    var vue_modify_photo_modal = new Vue({
        el: '#modify_photo_modal',
        data: {
            photo: {},
        },
        methods: {
            showModifyPhotoModal: function(photo){
                vue_modify_photo_modal.photo = photo;
                $('#modify_photo_modal').modal('show');
            },
            modifyPhotoContent: function(){
                vue_modify_photo_modal.photo.imgContent = $('#modify_photo_modal_img_content').val();
                vue_wx_view.qst_photosList.updatePhotoByImgObject(vue_modify_photo_modal.photo);
                console.log(vue_wx_view.qst_photosList);
                __ajax('wechat.index.new_updatePhotosList', {
                    questionid: vue_wx_view.question.id,
                    answerid: vue_wx_view.answerid,
                    photosList: vue_wx_view.qst_photosList,
                }, function (data) {
                    if(data.ret == 'success'){
                        vue_wx_view.refreshPhotosList();
                        $('#modify_photo_modal').modal('hide');
                    }else {
                        alert(data.info);
                    }
                });
            }
        }
    });
    
    //删除图片提示框
    var vue_delete_photo_modal = new Vue({
        el: '#delete_photo_modal',
        data: {
            imgUrl: '',
        },
        methods: {
            showDeletePhotoModal: function(imgUrl){
                vue_delete_photo_modal.imgUrl = imgUrl;
                $('#delete_photo_modal').modal('show');
            },
            deletePhoto: function() {
                __ajax('wechat.index.deleteImg', {
                    imgName: vue_delete_photo_modal.imgUrl
                }, function (data) {
                    if(data.ret == 'success'){
                        vue_wx_view.qst_photosList.deletePhotoByImgUrl(vue_delete_photo_modal.imgUrl);
                        __ajax('wechat.index.new_updatePhotosList', {
                            questionid: vue_wx_view.question.id,
                            answerid: vue_wx_view.answerid,
                            photosList: vue_wx_view.qst_photosList,
                        }, function (data) {
                            if(data.ret == 'success'){
                                vue_wx_view.refreshPhotosList();
                            }else {
                                alert(data.info);
                            }
                        });
                    }else {
                        alert(data.info);
                    }
                });
            },
        }
    });
    
    __request('wechat.api.get_answer_by_taskid',{taskid: taskid}, refresh_data);
    
});   


Array.prototype.getPhotoByImgUrl = function(imgUrl) {
    if(imgUrl === undefined) {
        return -1;
    }

    for(var i = 0; i < this.length; i++) {
        if(this[i].imgUrl == imgUrl) {
            return this[i];
        }
    }
    return -1;
};
Array.prototype.deletePhotoByImgUrl = function(imgUrl) {
    if(imgUrl === undefined) {
        return -1;
    }

    for(var i = 0; i < this.length; i++) {
        if(this[i].imgUrl == imgUrl) {
            return this.splice(i, 1);
        }
    }
    return -1;
};
Array.prototype.updatePhotoByImgObject = function(photoObject) {
    if(photoObject === undefined) {
        return -1;
    }

    for(var i = 0; i < this.length; i++) {
        if(this[i].imgUrl == photoObject.imgUrl) {
            return this.splice(i, 1, photoObject);
        }
    }
    return -1;
};



