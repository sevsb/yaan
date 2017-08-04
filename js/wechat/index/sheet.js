$(document).ready(function() {
    document.addEventListener("touchstart", function() {}, false);

    __ajax('wechat.index.initData', {
        paperId: __paperId,
        userId: __userId,
    }, function (data) {
        if(data.ret == 'success') {
            __answerId = data.info.answerId;
            __photosList = data.info.photosList;
            vue_wx_view.setPageStatus(1);
            vue_wx_view.refreshPhotosList();
        } else {
            alert(data.info);
        }
    });

    var vue_wx_view = new Vue({
        el: '#wx_view',
        data: {
            pageStatus: 0,
            imgRoot: __imgRoot,
            photosList: __photosList,
            imgUrl: '',
            imgData: '',
            imgContent: '',
            photo: {},
        },
        methods: {
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
            refreshPhotosList: function() {
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
                                vue_wx_view.showAddPhotoModal(localId, imgData);
                            }
                        });
                    }
                });
            },
            showAddPhotoModal: function(imgUrl, imgData, imgContent = ''){
                vue_wx_view.imgUrl = imgUrl;
                vue_wx_view.imgData = imgData;
                vue_wx_view.imgContent = imgContent;
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
                    imgData: vue_wx_view.imgData
                }, function (data) {
                    if(data.ret == 'success'){
                        photo.imgUrl = data.imgUrl;
                        __photosList.push(photo);
                        __ajax('wechat.index.updatePhotosList', {
                            answerId: __answerId,
                            photosList: __photosList,
                        }, function (data) {
                            if(data.ret == 'success'){
                                $('.uploading_alert').hide();
                                $('.upload_success_alert').show();
                                vue_wx_view.refreshPhotosList();
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
            },
            showModifyPhotoModal: function(imgUrl){
                photo = __photosList.getPhotoByImgUrl(imgUrl)
                vue_wx_view.photo = photo;
                $('#modify_photo_modal').modal('show');
            },
            modifyPhotoContent: function(){
                vue_wx_view.photo.imgContent = $('#modify_photo_modal_img_content').val();
                __photosList.updatePhotoByPhoto(vue_wx_view.photo);
                __ajax('wechat.index.updatePhotosList', {
                    answerId: __answerId,
                    photosList: __photosList,
                }, function (data) {
                    if(data.ret == 'success'){
                        vue_wx_view.refreshPhotosList();
                        $('#modify_photo_modal').modal('hide');
                    }else {
                        alert(data.info);
                    }
                });
            },
            showDeletePhotoModal: function(imgUrl){
                photo = __photosList.getPhotoByImgUrl(imgUrl)
                vue_wx_view.photo = photo;
                $('#delete_photo_modal').modal('show');
            },
            deletePhoto: function() {
                __ajax('wechat.index.deleteImg', {
                    imgName: vue_wx_view.photo
                }, function (data) {
                    if(data.ret == 'success'){
                        __photosList.deletePhotoByPhoto(vue_wx_view.photo);
                        __ajax('wechat.index.updatePhotosList', {
                            answerId: __answerId,
                            photosList: __photosList,
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
            goBack: function() {
                window.location.href = location.origin+'/?wechat/index/home';
            },
            showSubmitSheetModal: function(){
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
        }
    });
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
Array.prototype.deletePhotoByPhoto = function(photoObject) {
    if(photoObject === undefined) {
        return -1;
    }

    for(var i = 0; i < this.length; i++) {
        if(this[i].imgUrl == photoObject.imgUrl) {
            return this.splice(i, 1);
        }
    }
    return -1;
};
Array.prototype.updatePhotoByPhoto = function(photoObject) {
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
