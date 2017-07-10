$(document).ready(function() {
    $('.navbar_bottom').on('click', function() {
        var number = $('.card').length;
        if(number < 10) {
            addPhoto(number);
        }
    });

    $('.delete_btn').on('click', function() {
        console.info(this.attr('number'))
        var number = $('.card').length;
        if(number < 10) {
            addPhoto(number);
        }
    });

});

function addPhoto(json) {
    console.log(json);
    var card = '<div class="card" id="card_'+json+'">';
        card += '    <div class="card_body">';
        card += '        <img class="card_img" src="/bigsword/yaan/images/default_images/0'+json+'.jpg">';
        card += '    </div>';
        card += '    <div class="card_footer dropup" >';
        card += '        <div class="card_title">这放八个字比较好...</div>';
        card += '        <button class="card_dropdown_btn" id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
        card += '            <span class="glyphicon glyphicon-option-vertical" aria-hidden="true"></span>';
        card += '        </button>';
        card += '        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel" style="min-width: 0;">';
        card += '            <li><a href="#">修改</a></li>';
        card += '            <li class="delete_btn" number="'+json+'"><a href="###">删除</a></li>';
        card += '        </ul>';
        card += '    </div>';
        card += '</div>';
    $('#card_book').append(card);

    // 为新添的元素绑定事件
    $('.delete_btn').on('click', function() {
        console.info(this.attr('number'))
        var number = $('.card').length;
        if(number < 10) {
            addPhoto(number);
        }
    });
}
