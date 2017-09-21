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
    
    //刷新视图
    var refresh_view = function () {
        console.log(answer_show.question_list);
        
        for (var i in answer_show.question_list) {
            if (answer_show.question_list[i].is_parent == 0) {
                answer_show.question_list[i].status = 'show';
            }else {
                answer_show.question_list[i].status = 'show';
                var __question = answer_show.question_list[i];
                
                while (__question.is_parent != 0) {
                    var parent_question_id = __question.parent_question_option.parent_question_id;
                    var parent_question_opt_id = __question.parent_question_option.parent_option_id;
                    
                    for (var j in answer_show.question_list[parent_question_id].options){
                        if (answer_show.question_list[parent_question_id].options[j].id == parent_question_opt_id && answer_show.question_list[parent_question_id].options[j].status == 'nochecked'){
                            answer_show.question_list[i].status = 'hide';
                        }
                    }
                    __question = answer_show.question_list[parent_question_id];
                }
            }
        }
    }
    
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
            card_book_loading: 0,
            pageStatus: 1,
            imgRoot: [],
            photosList: [],
        },
        methods: {
            show_pic_dialog: function (){
                this.flag = 1;
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
                var options = answer_show.question_list[question.id].options;
                
                for (var i in answer_show.question_list[question.id].options) {
                    if (answer_show.question_list[question.id].options[i].id == question_opt.id) {
                        answer_show.question_list[question.id].options[i]['status'] = 'checked';
                    }else {
                        answer_show.question_list[question.id].options[i]['status'] = 'nochecked';
                    }
                }
                
                refresh_view();
                return;
            },
            check_click: function (question, question_opt){
                var opt = $('#option_' + question_opt.id) ;
                var chk = opt.prop('checked');
                
                for (var i in answer_show.question_list[question.id].options) {
                    if (answer_show.question_list[question.id].options[i].id == question_opt.id) {
                        if (chk) {
                            answer_show.question_list[question.id].options[i]['status'] = 'checked';
                        }else {
                            answer_show.question_list[question.id].options[i]['status'] = 'nochecked';
                        }
                    }
                }

                refresh_view();
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
                answer_show.question_list[question.id].answer_value = score;
                refresh_view();
                return;
            },
            text_blur: function (question){
                console.log('text_blur');
                var question_id = question.id;
                var __self = $('#question_' + question_id).find('input');
                var value = __self.val();
                answer_show.question_list[question.id].answer_value = value;
                refresh_view();
                return;
            },
            goBack: function (){
                console.log('goback');
                answer_show.flag = 0;  
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
                        answer_show.question_list[question_id].answer_value = score;
                        refresh_view();
                        return;
                    }
                });
            });
        }
    });
    
    __request('admin.questionnaire.get_preview_by_naireid',{naireid: naireid}, refresh_data);
    
});   
    

/*foreach ($question_list as $qid => $question) {
    if (empty($question['is_parent'])) {
        $question_list[$qid]['status'] = 'show';
    } else {
        $question_list[$qid]['status'] = 'show';
        $__question = $question;
        while($__question['is_parent']) {
            logging::d("WHILE_start", "WHILE_startWHILE_startWHILE_startWHILE_startWHILE_start");
            logging::d("WHILE_start", json_encode($__question));
            $parent_question_id = $__question['parent_question_option']['parent_question_id'];
            $parent_question_opt_id = $__question['parent_question_option']['parent_option_id'];
            
            foreach ($question_list[$parent_question_id]["options"] as $opt){
                if ($opt['id'] == $parent_question_opt_id && $opt['status'] == 'nochecked'){
                    logging::d("WHILE", "opt['id']:" . $opt['id']);
                    logging::d("WHILE", "opt['status']:" . $opt['status']);
                    $question_list[$qid]['status'] = 'hide';
                }
            }
            $__question = $question_list[$parent_question_id];
            logging::d("WHILE", "now_questionis:".json_encode($__question));
        }
    }
}
*/