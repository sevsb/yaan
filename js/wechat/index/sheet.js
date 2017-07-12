$(document).ready(function() {
    wx.ready(function () {
        // 点击底边栏的拍照功能，添加照片
        $('.navbar_bottom').on('click', function() {
            wx.chooseImage({
                count: 1, // 默认9
                sizeType: ['original', 'compressed'], // ['original', 'compressed'] 可以指定是原图还是压缩图，默认二者都有
                sourceType: ['camera'], // ['album', 'camera'] 可以指定来源是相册还是相机，默认二者都有
                success: function (res) {
                    var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                    addPhoto(localIds);
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
    var cardNumber = new Date();
    var cardObject =  '<div class="card" id="card_'+cardNumber+'">';
        cardObject += '    <div class="card_body">';
        // TODO: 为了更好的显示竖版照片，应该在这里比较图片的长宽值，然后选择应该是max-height或者max-width
        cardObject += '        <img class="card_img" src="'+data+'">';
        cardObject += '    </div>';
        cardObject += '    <div class="card_footer dropup" >';
        cardObject += '        <div class="card_title">这放八个字比较好...</div>';
        cardObject += '        <button class="card_dropdown_btn" id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
        cardObject += '            <span class="glyphicon glyphicon-option-vertical" aria-hidden="true"></span>';
        cardObject += '        </button>';
        cardObject += '        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel" style="min-width: 0;">';
        cardObject += '            <li><a href="#">修改</a></li>';
        cardObject += '            <li class="delete_btn" number="'+cardNumber+'"><a href="###">删除</a></li>';
        cardObject += '        </ul>';
        cardObject += '    </div>';
        cardObject += '</div>';
    $('#card_book').append(cardObject);

    // 为新添的元素绑定删除事件
    $('.delete_btn').on('click', function() {
        var number = $(this).attr('number')
        removePhoto(number);
    });
}

/** 
 * 删除照片
 * @param number 照片编号
 */
function removePhoto(number) {
    $('#card_'+number).remove();
}
