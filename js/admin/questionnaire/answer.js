$(function() {
	$("#add_answer").click(function() {
		//alert($('#answer').serialize());
        //var answers = $('#answer').serializeArray();
        //console.log(answers);
        var answer_list = [];
        var nid = get_request("id");
        $('.question').each(function (){
            var id = $(this).attr('qid');
            var title = $(this).attr('qtitle');
            var notes = $(this).attr('qnotes');
            var type = $(this).attr('qtype');
            var value = null;
            if (type == 'radio') {
                value = $(this).find('input:radio:checked').val();
            }else if (type == 'check') {
                value = [];
                $(this).find('input:checkbox:checked').each(function (){
                    value.push($(this).val());
                });
            }else if (type == 'star') {
                value = $(this).find('input').val();
            }else if (type == 'range') {
                value = $(this).find('input').val();
            }else if (type == 'text') {
                value = $(this).find('input').val();
            }
            var o = new Object();
            o.id = id;
            o.title = title;
            o.notes = notes;
            o.type = type;
            o.value = value;
            answer_list.push(o);
        });

        answer_list = JSON.stringify(answer_list);
        console.log(answer_list);
        console.log(nid);
        //return;
        
        __ajax('admin.questionnaire.addAnswer', {id: nid, answer_list: answer_list},function (data) {
            console.log(data);
            alert(data.ret);
        });
        return;
		$.post("index.php?admin/questionnaire/addAnswer", $("#answer").serialize(), function(data){
           alert("提交成功");
        });
	});
});