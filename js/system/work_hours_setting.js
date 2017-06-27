$(document).ready(function() {
    
    $(".save_btn").click(function (){
        var start = parseInt($('#start').val());
        var end = parseInt($('#end').val());
        if (start == '' || end == '') {
            return;
        }
        if (start > end) {
            alert('起始时间不能晚于结束时间！');
            return;
        }
        console.log(start);
        console.log(end);
        __ajax('system.set_work_hours', {start: start, end: end},true);
    });
    
});

