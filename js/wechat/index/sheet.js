var Photo = {
    imgUrl: '',
    imgContent: '',
    imgLocation: {
        latitude: '',
        longitude: '',
        accuracy: '',
    },
}

$(document).ready(function() {
    wx.ready(function () {
        // 点击底边栏的拍照功能，添加照片
        $('#add_img').on('click', function() {
            wx.chooseImage({
                count: 1, // 默认9
                sizeType: ['compressed'], // ['original', 'compressed'] 可以指定是原图还是压缩图，默认二者都有
                sourceType: [ 'album', 'camera'], // ['album', 'camera'] 可以指定来源是相册还是相机，默认二者都有
                success: function (res) {
                    var photo = new Photo();
                    var imgData;
                    var localId = res.localIds[0]; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                    // Android 获取的DATA是没有‘data:image/jpeg;base64,’前缀的，iOS 获取的 MIME type 为‘image/jpg’
                    // 而且菊花的MATE7在 alert 后会死机，需要考虑是否加上  的判断？
                    if(!window.__wxjs_is_wkwebview) {
                        alert('只支持水果机的新内核WKWebview');
                        return;
                    } else {
                        wx.getLocalImgData({
                            localId: localId,
                            success: function (res) {
                                imgData = res.localData;
                            }
                        });
                        wx.getLocation({
                            type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
                            success: function (res) {
                                photo.imgLocation.latitude = res.latitude;
                                photo.imgLocation.longitude = res.longitude;
                                photo.imgLocation.accuracy = res.accuracy;
                            }
                        });
                        // 初始化模态框中元素
                        $('#add_photo_modal_img').attr('src', data['localId']); // img元素中iPhone目前是可以正常显示的
                        $('#add_photo_modal_img_content').val('');
                        $('#add_photo_modal').modal('show');
                        $('#add_photo_submit').on('click', function() {
                            photo.imgContent = $('#add_photo_modal_img_content').val();
                            // 上传图片
                            __ajax('wechat.index.updateImg', {
                            imgData: imgData
                            }, function (data) {
                                ret = data.ret;
                                photo.imgUrl = ret;
                                alert(ret);
                            });
                            $('#add_photo_modal').modal('hide');
                        })
                    }
                }
            });
        });
    });
});



/** 
 * 添加照片
 * @param data 应为从JS-SDK中拿到的相片数据和填写的备注
 *             因为目前是Demo，所以现在只是card元素的总和，为了添加预览的删除功能。
 */
function addPhoto(data) {
    // 制作卡片元素
    var cardObject =  '<div class="card" id="card_'+data['cardNumber']+'">';
        cardObject += '    <div class="card_body" style="background:url('+data['localId']+') no-repeat center; background-size: cover;">';
        cardObject += '    </div>';
        cardObject += '    <div class="card_footer dropup" >';
        cardObject += '        <div class="card_title">这放八个字比较好...</div>';
        cardObject += '        <button class="card_dropdown_btn" id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
        cardObject += '            <span class="glyphicon glyphicon-option-vertical" aria-hidden="true"></span>';
        cardObject += '        </button>';
        cardObject += '        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel" style="min-width: 0;">';
        cardObject += '            <li><a href="#">修改</a></li>';
        cardObject += '            <li class="delete_btn" id="delete_'+data['cardNumber']+'"><a href="###">删除</a></li>';
        cardObject += '        </ul>';
        cardObject += '    </div>';
        cardObject += '</div>';
    $('#card_book').append(cardObject);
}
