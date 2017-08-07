$(document).ready(function (){
__refresh_broadcast_loctions();
del_id = null;

$('.do_del').click(function (){
    console.log(del_id);
    __ajax("admin.task.del", {del_id: del_id}, true);
});

$('.del_btn').click(function (){
    del_id = $(this).parents('.task_elf').attr('muffinid');
});

$('.accept_btn').click(function (){
    taskid = $(this).parents('.task_elf').attr('taskid');
});
$('.investigator_div').click(function (){
    $(this).parents('.modal-body').find('.investigator_div').removeClass('choosed');
    $(this).addClass('choosed');
});


$('.do_accept').click(function (){
    userid = $('.choosed').attr('userid');
    console.log("taskid:" + taskid);
    console.log("userid:" + userid);
    if (userid == '' || userid == undefined) {
        return false;
    }
    __ajax('admin.task.assign', {taskid : taskid , userid: userid} ,true )
});
$('.cancel_accept').click(function (){
    userid = null;
    console.log("taskid:" + taskid);
    console.log("userid:" + userid);
    __ajax('admin.task.assign', {taskid : taskid , userid: userid} ,true )
});

$(document).on('click', '.eraser_btn', function (){
    $(this).parents('.broad_cast_loc_div').remove();
});

$('.broadcast_btn').click(function (){
  $('.broadcast_area_list').html('');
  taskid = $(this).parents('.task_elf').attr('taskid');
  console.debug('taskid:' + taskid);
  var broadcast_loc = loctions[taskid];
  console.debug(broadcast_loc);
  if (broadcast_loc == null) {
      console.log('more_loc_add...');
      var add_item = "<div class='broad_cast_loc_div'>";
      add_item += "<label for='location'>请选择市区<span style='color: red;'>&nbsp* <small>1级和2级区域必选</small></span></label>";
      add_item += "<div data-toggle='distpicker' class='distpicker' id='distpicker1' data-value-type='code'>";
      add_item += "<select class='province'></select>";
      add_item += "<select class='city' ></select>";
      add_item += "<select class='district' ></select>";
      add_item += "<span class='btn btn-danger eraser_btn'>删除</span></div>";
      add_item += "</div>";
      $('.broadcast_area_list').append(add_item);
      $('.distpicker').distpicker(); 
  } else {
      for (var x in broadcast_loc) {
          console.log(broadcast_loc[x]);
          console.log(x);
          var province_code = broadcast_loc[x].province.code;
          var city_code = broadcast_loc[x].city.code;
          var district_code = broadcast_loc[x].district.code;
          //console.log(province_code);
          var pickerId = 'distpicker_' + x;

          var add_item = "<div class='broad_cast_loc_div'>";
          add_item += "<label for='location'>请选择市区<span style='color: red;'>&nbsp* <small>1级和2级区域必选</small></span></label>";
          add_item += "<div data-toggle='distpicker' class='distpicker' id='" + pickerId + "' data-value-type='code'>";
          add_item += "<select class='province'></select>";
          add_item += "<select class='city' ></select>";
          add_item += "<select class='district' ></select>";
          add_item += "<span class='btn btn-danger eraser_btn'>删除</span></div>";
          add_item += "</div>";
          $('.broadcast_area_list').append(add_item);
          $('#' + pickerId).distpicker({
            province: province_code,
            city: city_code,
            district: district_code
          }); 
      }

  }
  
});

$('.more_loc_btn').click(function (){
    console.log('more_loc_add...');
    var add_item = "<div class='broad_cast_loc_div'>";
    add_item += "<label for='location'>请选择市区<span style='color: red;'>&nbsp* <small>1级和2级区域必选</small></span></label>";
    add_item += "<div data-toggle='distpicker' class='distpicker' id='distpicker1' data-value-type='code'>";
    add_item += "<select class='province'></select>";
    add_item += "<select class='city' ></select>";
    add_item += "<select class='district' ></select>";
    add_item += "<span class='btn btn-danger eraser_btn'>删除</span></div>";
    add_item += "</div>";
    $('.broadcast_area_list').append(add_item);
    $('.distpicker').distpicker();
});

$('.save_locs_btn').click(function (){
    var me_broadcast_loctions = [];
    $('#broadCastModal').find('.broad_cast_loc_div').each(function (){
        var province_code = $(this).find('.province').val();
        var city_code = $(this).find('.city').val();
        var district_code = $(this).find('.district').val();

        var provinces = $(".distpicker").distpicker('getDistricts');
        var citys = $(".distpicker").distpicker('getDistricts', province_code);
        var districts = $(".distpicker").distpicker('getDistricts', city_code);
        var loc = new Object();
        var province = new Object();
        var city = new Object();
        var district = new Object();

        province.code = province_code;
        province.title = province_code != ''? provinces[province_code] : null;
        city.code = city_code;
        city.title = province_code != '' && city_code != '' ? citys[city_code] : null;
        district.code = district_code;
        district.title = province_code != '' && city_code != '' && district_code != '' ? districts[district_code] : null;
        loc.province = province;
        loc.city = city;
        loc.district = district;
        me_broadcast_loctions.push(loc); 
    });
    console.debug('taskid:' + taskid);
    console.log(me_broadcast_loctions);
    me_broadcast_loctions = JSON.stringify(me_broadcast_loctions); 

    projectmuffinid = get_request('projectmuffinid');
    __ajax('admin.task.update_broadcast_area',{
        taskid: taskid,
        broadcast_loctions: me_broadcast_loctions
    },true);
});



});
var __refresh_broadcast_loctions = function () {
    __request("admin.task.load_all_broadcast_areas", {}, function(res) {
        console.debug(res);
        loctions = res;
    });
    //loctions = [];
    //loctions[141] = null;
}
