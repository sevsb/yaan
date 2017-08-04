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
                this.photosList = __photosList;
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
                __ajax('wechat.index.deleteImg', {
                    imgName: imgUrl
                }, function (data) {
                    if(data.ret == 'success'){
                        __photosList.deletePhotoByImgUrl(imgUrl);
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
            showAddPhotoModal: function(imgUrl, imgData, imgContent = ''){
                this.imgUrl = imgUrl;
                this.imgData = imgData;
                this.imgContent = imgContent;
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
                    imgData: this.imgData
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
            }
        }
    });

    var vue_modify_photo_modal = new Vue({
        el: '#modify_photo_modal',
        data: {
            photo: {},
        },
        methods: {
            showModifyPhotoModal: function(photo){
                this.photo = photo;
                $('#modify_photo_modal').modal('show');
            },
            modifyPhotoContent: function(){
                this.photo.imgContent = $('#modify_photo_modal_img_content').val();
                __photosList.updatePhotoByImgUrl(this.photo);
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
            }
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
Array.prototype.updatePhotoByImgUrl = function(photoObject) {
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
