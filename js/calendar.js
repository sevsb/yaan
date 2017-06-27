var date = new Date();
var chooseDate = new Array();
var chooseDays = new Array();

function updateCalendar(date) {

    year = date.getFullYear();
    month = date.getMonth();
    $('#year').html(year);
    $('#month').html(month + 1);
    drawCalendarBody(year, month);

}

function drawCalendarBody(year, month) {

    __ajax("duty.get_duty", {id: staff_id}, function (data) {
        info = data.info;
        vacations = info.vacation;
        vacations = JSON.parse(vacations);
        console.log(vacations);
        var startDay = new Date(year, month, 1).getDay();
        var endDay = new Date(year, month + 1, 0).getDay();
        var mouthDays = new Date(year, month + 1, 0).getDate();
        var countCol = 0;
        var html = '';
        //console.log(startDay);
        //console.log(endDay);
        //console.log(mouthDays);
        html += '<tr>';
        for (var i = 0; i < startDay; i++) {
            html += '<td></td>';
            countCol++;
        }

        for (var dayNumber = 1; dayNumber <= mouthDays; dayNumber++) {
            thisday = year + "-" + (month + 1) + "-" + dayNumber ;
            timestamp = Date.parse(thisday) / 1000;
            bg_color = '';
            if (duty_type == 1) {
                var d = new Date(timestamp * 1000);
                var wd = d.getDay();
                //console.log(duty_rule);
                if(duty_rule.length > 0){
                    for (i in duty_rule ) {
                        if(parseInt(duty_rule[i]) === parseInt(wd)){
                            bg_color = '#ccc';
                        }
                    }
                }
            } else if (duty_type == 2) {
                //console.log("------------------");
                worktime = parseInt(duty_rule[0]);
                resttime = parseInt(duty_rule[1]);
                starttime = duty_rule[2];
                regular = worktime + resttime;
                diff_days = (timestamp - starttime) / 86400;
                if (diff_days >= 0) {
                    extra_day = diff_days % regular;
                    //console.log(extra_day);
                    if (extra_day >= worktime) {
                        bg_color = '#ccc';
                    }
                }
                //console.log(worktime);
                //console.log(resttime);
                //console.log(starttime);
                //console.log(regular);
                //console.log(diff_days);
            }
            for (var event_date in vacations) {
                if (event_date == timestamp) {
                    var type = vacations[event_date].type;
                    var bg_color = event_settings[type].color;
                }
            }

            
            html += '<td style="background-color:' + bg_color + ' ;" class="calendar_day" thisday="' + thisday + '" day="' + dayNumber + '" timestamp="' + timestamp + '">';
            html += '<div class="">' + dayNumber + '</div>';

            html += '</td>';
            countCol++;
            if (countCol == 7) {
                countCol = 0;
                html += '</tr><tr>';
            }
        }

        for (var i = endDay; i < 6; i++) {
            html += '<td></td>';
            countCol++;
        }
        html += '</tr>';

        $('#calendar_body').html(html);
    });
    

}