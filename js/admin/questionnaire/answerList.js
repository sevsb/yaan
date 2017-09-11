$(document).ready(function (){
 
var del_id = null;

$('.do_del').click(function (){
   console.log(del_id);
   __ajax("admin.questionnaire.remove_answer", {id: del_id}, true);
});

$('.del_btn').click(function (){
   del_id = $(this).attr('aid');
});

    $('#projects_table').dataTable(
      {
      "oLanguage": {
        "sLengthMenu": "每页显示 _MENU_ 条记录",
        "sZeroRecords": "对不起，查询不到任何相关数据",
        "sInfo": "当前显示 _START_ 到 _END_ 条，共 _TOTAL_条记录",
        "sInfoEmtpy": "找不到相关数据",
        "sInfoFiltered": "数据表中共为 _MAX_ 条记录)",
        "sProcessing": "正在加载中...",
        "sSearch": "搜索",
        "oPaginate": {
          "sFirst": "第一页",
          "sPrevious":" 上一页 ",
          "sNext": " 下一页 ",
          "sLast": " 最后一页 "
          },
        }
      });
   
});

