$(function() {
	$("#add_answer").click(function() {
		alert($('#answer').serialize());
		$.post("index.php?admin/questionnaire/addAnswer", $("#answer").serialize(), function(data){
			   alert("提交成功");
		 });
	});
});