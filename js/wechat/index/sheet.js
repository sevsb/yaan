$(document).ready(function() {
    __ajax('wechat.index.initData', {
        paperId: __paperId,
        userId: __userId,
    }, function (data) {
        if(data.ret == 'success'){
            __answerId = data.info.answerId;
            __photosList = data.info.photosList;

            __ajax('wechat.index.updatePhotosList', {
                answerId: __answerId,
                photosList: __photosList,
            }, function (data) {
                if(data.ret == 'success'){
                    vue_card_book.refreshPhotosList();
                }else {
                    console.debug(data);
                }
            });
            
            vue_card_book.refreshPhotosList();
            vue_add_photo_modal.showAddPhotoModal('http://127.0.0.1/bigsword/yaan/images/default_images/00.jpg', '', '');
        }else {
            alert(data.info);
        }
    });

    var vue_card_book = new Vue({
        el: '#card_book',
        data: {
            imgRoot: __imgRoot,
            photosList: __photosList,
        },
        methods: {
            refreshPhotosList: function(){
                console.info("refreshData!!");
                this.photosList = __photosList;
            },
            addPhoto: function(event) {
                console.info("addPhoto!!");
                // wx.chooseImage({
                //     count: 1, // 默认9
                //     sizeType: ['compressed'], // ['original', 'compressed'] 可以指定是原图还是压缩图，默认二者都有
                //     sourceType: ['camera'], // ['album', 'camera'] 可以指定来源是相册还是相机，默认二者都有
                //     success: function (res) {
                //         var photo = [];
                //         var imgData;
                //         var localId = res.localIds[0]; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                //         wx.getLocalImgData({
                //             localId: localId,
                //             success: function (res) {
                //                 imgData = res.localData;
                //                 // 解决 iOS 下 MIME type 为 image/jgp 的问题
                //                 imgData = imgData.replace('image/jgp', 'image/jpeg');
                //                 // 解决 Android 下缺少 MIME type 的问题
                //                 if(imgData.substr(0, 23).search('data:image/jpeg;base64,') < 0){
                //                     imgData = 'data:image/jpeg;base64,' + imgData;
                //                 }
                //             }
                //         });
                //     }
                // });
            },
        }
    });

    var vue_add_photo_modal = new Vue({
        el: '#add_photo_modal',
        data: {
            imgUrl: '',
            imgData: '',
            imgContent: '',
        },
        methods: {
            showAddPhotoModal: function(imgUrl, imgData = '', imgContent = ''){
                this.imgUrl = imgUrl;
                this.imgData = imgData;
                this.imgContent = imgContent;
                console.info("refreshData!!");
                $('#add_photo_modal').modal('show');
            },
            updatePhoto: function(){
                var photo = [];
                // wx.getLocation({
                //     type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
                //     success: function (res) {
                //         photo['imgLocation'] = [];
                //         photo['imgLocation']['latitude'] = res.latitude;
                //         Photo['imgLocation']['longitude'] = res.longitude;
                //         Photo['imgLocation']['accuracy'] = res.accuracy;
                //     }
                // });
                // photo['imgContent'] = $('#add_photo_modal_img_content').val();

                photo['imgLocation'] = [];
                photo['imgLocation']['latitude'] = '23';
                photo['imgLocation']['longitude'] = '33';
                photo['imgLocation']['accuracy'] = '33';
                photo['imgContent'] = '';
                photo['imgUrl'] = 'http://127.0.0.1/bigsword/yaan/images/default_images/00.jpg';
                __photosList.push(photo);
                vue_card_book.refreshPhotosList();

                // // 上传图片
                // __ajax('wechat.index.updateImg', {
                // imgData: imgData
                // }, function (data) {
                //     if(data.ret == 'success'){
                //         photo['imgUrl'] = data.imgUrl;
                //         $('#add_photo_modal').modal('hide');
                //         photosList.push(photo);
                //     }else {
                //         alert(data.info);
                //     }
                // });
            }
        }
    });

    // wx.ready(function () {
    //     // 点击底边栏的拍照功能，添加照片
    //     $('#add_card_btn').on('click', function() {
    //         wx.chooseImage({
    //             count: 1, // 默认9
    //             sizeType: ['compressed'], // ['original', 'compressed'] 可以指定是原图还是压缩图，默认二者都有
    //             sourceType: ['camera'], // ['album', 'camera'] 可以指定来源是相册还是相机，默认二者都有
    //             success: function (res) {
    //                 var photo = [];
    //                 var imgData;
    //                 var localId = res.localIds[0]; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
    //                 wx.getLocalImgData({
    //                     localId: localId,
    //                     success: function (res) {
    //                         imgData = res.localData;
    //                         // 解决 iOS 下 MIME type 为 image/jgp 的问题
    //                         imgData = imgData.replace('image/jgp', 'image/jpeg');
    //                         // 解决 Android 下缺少 MIME type 的问题
    //                         if(imgData.substr(0, 23).search('data:image/jpeg;base64,') < 0){
    //                             imgData = 'data:image/jpeg;base64,' + imgData;
    //                         }
    //                     }
    //                 });
    //                 wx.getLocation({
    //                     type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
    //                     success: function (res) {
    //                         photo['imgLocation'] = [];
    //                         photo['imgLocation']['latitude'] = res.latitude;
    //                         Photo['imgLocation']['longitude'] = res.longitude;
    //                         Photo['imgLocation']['accuracy'] = res.accuracy;
    //                     }
    //                 });
    //                 // 初始化模态框中元素
    //                 $('#add_photo_modal_img').attr('src', localId); // img元素中iPhone目前是可以正常显示的
    //                 $('#add_photo_modal_img_content').val('');
    //                 $('#add_photo_modal').modal('show');
    //                 $('#add_photo_submit').on('click', function() {
    //                     photo['imgContent'] = $('#add_photo_modal_img_content').val();
    //                     // 上传图片
    //                     __ajax('wechat.index.updateImg', {
    //                     imgData: imgData
    //                     }, function (data) {
    //                         if(data.ret == 'success'){
    //                             photo['imgUrl'] = data.imgUrl;
    //                             $('#add_photo_modal').modal('hide');
    //                             addPhotoCard(photo);
    //                         }else {
    //                             alert(data.info);
    //                         }
    //                     });
    //                 });
    //             }
    //         });
    //     });
    // });
});

/**
 * 添加照片
 * @param data 为 wx.chooseImage 中的 photo 数组
 */
var addPhotoCard = function(data) {
    var cardNumber = data['imgUrl'];
    cardNumber = cardNumber.substr(0, cardNumber.length-5);
    // 制作卡片元素
    var cardObject =  '<div class="card" id="card_'+cardNumber+'">';
        cardObject += '    <div class="card_body" style="background:url('+imgRoot+data['imgUrl']+') no-repeat center; background-size: cover;">';
        cardObject += '    </div>';
        cardObject += '    <div class="card_footer dropup" >';
        cardObject += '        <div class="card_title">'+data['imgContent']+'</div>';
        cardObject += '        <button class="card_dropdown_btn" id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
        cardObject += '            <span class="glyphicon glyphicon-option-vertical" aria-hidden="true"></span>';
        cardObject += '        </button>';
        cardObject += '        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel" style="min-width: 0;">';
        cardObject += '            <li><a href="#">修改</a></li>';
        cardObject += '            <li class="delete_btn" id="delete_'+cardNumber+'"><a href="###">删除</a></li>';
        cardObject += '        </ul>';
        cardObject += '    </div>';
        cardObject += '</div>';
    $('#add_card_btn').before(cardObject);
    photosList.push(data);
}
