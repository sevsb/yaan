$(document).ready(function() {
    // 点击底边栏的拍照功能，添加照片
    $('.navbar_bottom').on('click', function() {
        var number = $('.card').length;
        if(number < 10) {
            addPhoto(number);
        }
    });
});

/** 
 * 添加照片
 * @param json 应为从JS-SDK中拿到的相片数据和填写的备注
 *             因为目前是Demo，所以现在只是card元素的总和，为了添加预览的删除功能。
 */
function addPhoto(json) {
    var cardObject =  '<div class="card" id="card_'+json+'">';
        cardObject += '    <div class="card_body">';
        // TODO: 为了更好的显示竖版照片，应该在这里比较图片的长宽值，然后选择应该是max-height或者max-width
        cardObject += '        <img class="card_img" src="/bigsword/yaan/images/default_images/0'+json+'.jpg">';
        cardObject += '    </div>';
        cardObject += '    <div class="card_footer dropup" >';
        cardObject += '        <div class="card_title">这放八个字比较好...</div>';
        cardObject += '        <button class="card_dropdown_btn" id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
        cardObject += '            <span class="glyphicon glyphicon-option-vertical" aria-hidden="true"></span>';
        cardObject += '        </button>';
        cardObject += '        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel" style="min-width: 0;">';
        cardObject += '            <li><a href="#">修改</a></li>';
        cardObject += '            <li class="delete_btn" number="'+json+'"><a href="###">删除</a></li>';
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
