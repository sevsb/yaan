$(document).ready(function() {
    wx.ready(function () {
        // 点击底边栏的拍照功能，添加照片
        $('.#add_img').on('click', function() {
            wx.chooseImage({
                count: 1, // 默认9
                sizeType: ['original'], // ['original', 'compressed'] 可以指定是原图还是压缩图，默认二者都有
                sourceType: ['camera'], // ['album', 'camera'] 可以指定来源是相册还是相机，默认二者都有
                success: function (res) {
                    var localId = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                    var data = new Array();
                    data['cardNumber'] = new Date().getTime();
                    data['localId'] = localId;
                    addPhoto(data);
                }
            });
            $('#add_photo_modal').modal('show');
        });
    });
});

/** 
 * 添加照片
 * @param data 应为从JS-SDK中拿到的相片数据和填写的备注
 *             因为目前是Demo，所以现在只是card元素的总和，为了添加预览的删除功能。
 */
function addPhoto(data) {
    alert(data['cardNumber']);
    alert(data['localId']);
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
