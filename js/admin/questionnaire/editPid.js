
		$(function() {
			//			$('#myTab li:eq(1) a').tab('show');
			$('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
				// 获取已激活的标签页的名称
				var activeTab = $(e.target).text();
				// 获取前一个激活的标签页的名称
				var previousTab = $(e.relatedTarget).text();
				$(".active-tab span").html(activeTab);
				$(".previous-tab span").html(previousTab);
			});
		});
		$(function() {
			$("#nairetitlelab").click(function() {
				$(this).hide();
				$("#nairetitle").show();
				$("#nairetitle").val($("#nairetitlelab").text());
			});
		});
		$(function() {
			$("#nairetitle").blur(function() {
				$("#nairetitle").hide();
				$("#nairetitlelab").text($("#nairetitle").val());
				$(".container").children().eq(0).text($("#nairetitle").val());
				$("#nairetitlelab").show();
				$.post("index.php?admin/questionnaire/editTitle", { id:<?php echo $questionnaire->id(); ?>, title: $("#nairetitle").val(), notes:$("#nairenoteslab").text() } );
			});
		});
		$(function() {
			$("#nairenoteslab").click(function() {
				$(this).hide();
				$("#nairenotes").show();
				$("#nairenotes").val($("#nairenoteslab").text());
			});
		});
		$(function() {
			$("#nairenotes").blur(function() {
				$("#nairenotes").hide();
				$("#nairenoteslab").text($("#nairenotes").val());
				$("#nairenoteslab").show();
				$.post("index.php?admin/questionnaire/editTitle", { id:<?php echo $questionnaire->id(); ?>, title: $("#nairetitle").val(), notes:$("#nairenoteslab").text() } );
			});
		});
		$(function() {
			$("#myButtons1").click(function() {
				//var p = '<label for="name">内联的复选框和单选按钮的实例</label><div><label class="checkbox-inline"> <input type="checkbox" id="inlineCheckbox1" value="option1"> 选项 1 </label> <label class="checkbox-inline"> <input type="checkbox" id="inlineCheckbox2" value="option2"> 选项 2 </label> <label class="checkbox-inline"> <input type="checkbox" id="inlineCheckbox3" value="option3"> 选项 3 </label> </div>';
				//			var d = document.createElement("div");
				//		    d.innerHTML = "生成的div:" + count;
				//		    d.style.height = "25px";
				//		    count++;
				//				alert(p);
				//$("#preview").append(p);
				$("#editview").empty();
				var d = '<h4>添加多选</h4><form class="form-horizontal" role="form">'
					+'<div class="input-group input-group-lg input-group-sm">'
					+'<span class="input-group-addon">标题</span> <input type="text" name="title"'
					+' class="form-control" placeholder="标题">'
					+'</div>'
					+'<br>'
					+'<div class="input-group input-group-lg input-group-sm">'
					+'<span class="input-group-addon">备注</span> <input type="text" name="notes"'
					+' class="form-control" placeholder="备注">'
					+'</div>'
					+'<br>'
					+'<div class="input-group input-group-lg input-group-sm">'
					+'<span class="input-group-addon">选项</span> <input type="text" name="checktitle"'
					+' class="form-control" placeholder="选项"><span'
					+' class="input-group-addon glyphicon glyphicon-remove"></span>'
					+'</div>'
					+'<br>'
					+'<div class="input-group input-group-lg input-group-sm">'
					+'<span class="input-group-addon">选项</span> <input type="text" name="checktitle"'
					+' class="form-control" placeholder="选项"><span'
					+' class="input-group-addon glyphicon glyphicon-remove"></span>'
					+'</div>'
					+'<br>'
					+'<div class="form-group input-group-lg input-group-sm">'
					+'<label for="choiceadd"'
					+' class="col-xs-1 col-sm-1 col-md-1 col-lg-1 control-label">选项</label>'
					+'<button type="button" id="add_check_btn"'
					+' class="col-xs-10 col-sm-10 col-md-10 col-lg-10 btn btn-default">添加选项</button>'
					+'</div>'
					+'<br>'
					+'<div class="form-group input-group-lg input-group-sm">'
					+'<button type="button" id="add_check"'
					+' class="btn btn-default">添加</button>'
					+'<button type="reset" id="reset"'
					+' class="btn btn-default">重置</button>'
					+'</div>'
					+'</form>';
				$("#editview").append(d);
			});
		});
		$(function() {
			$("#myButtons2").click(function() {
				//var p = '<label for="name">内联的复选框和单选按钮的实例<div></label><label class="radio-inline"> <input type="radio" name="optionsRadiosinline" id="optionsRadios3" value="option1" checked> 选项 1 </label> <label class="radio-inline"> <input type="radio" name="optionsRadiosinline" id="optionsRadios4" value="option2"> 选项 2 </label></div>';
				//$("#preview").append(p);
				$("#editview").empty();
				var d = '<h4>添加单选</h4><form class="form-horizontal" role="form">'
					+'<div class="input-group input-group-lg input-group-sm">'
					+'<span class="input-group-addon">标题</span> <input type="text" name="title"'
					+' class="form-control" placeholder="标题">'
					+'</div>'
					+'<br>'
					+'<div class="input-group input-group-lg input-group-sm">'
					+'<span class="input-group-addon">备注</span> <input type="text" name="notes"'
					+' class="form-control" placeholder="备注">'
					+'</div>'
					+'<br>'
					+'<div class="input-group input-group-lg input-group-sm">'
					+'<span class="input-group-addon">选项</span> <input type="text" name="checktitle"'
					+' class="form-control" placeholder="选项"><span'
					+' class="input-group-addon glyphicon glyphicon-remove"></span>'
					+'</div>'
					+'<br>'
					+'<div class="input-group input-group-lg input-group-sm">'
					+'<span class="input-group-addon">选项</span> <input type="text" name="checktitle"'
					+' class="form-control" placeholder="选项"><span'
					+' class="input-group-addon glyphicon glyphicon-remove"></span>'
					+'</div>'
					+'<br>'
					+'<div class="form-group input-group-lg input-group-sm">'
					+'<label for="choiceadd"'
					+' class="col-xs-1 col-sm-1 col-md-1 col-lg-1 control-label">选项</label>'
					+'<button type="button" id="add_check_btn"'
					+' class="col-xs-10 col-sm-10 col-md-10 col-lg-10 btn btn-default">添加选项</button>'
					+'</div>'
					+'<br>'
					+'<div class="form-group input-group-lg input-group-sm">'
					+'<button type="button" id="add_radio"'
					+' class="btn btn-default">添加</button>'
					+'<button type="reset" id="reset"'
					+' class="btn btn-default">重置</button>'
					+'</div>'
					+'</form>';
				$("#editview").append(d);
			});
		});
		$(function() {
			$("#myButtons4").click(function() {
				$("#myButtons4").toggle();
			});
		});
		$(function() {
			$("#myButtons4").click(function() {
				$("#myButtons4").toggle();
			});
		});
		$(function() {
			$("#editview").on("click",".form-horizontal > .input-group > span.glyphicon-remove", function(e) {
				$(e.target).parent().next().remove();
				$(e.target).parent().remove();
			});
		});
		$(function() {
			$("#editview").on("click",".form-horizontal > .form-group > #add_check_btn", function(e) {
				$(this).parent().before('<div class="input-group input-group-lg input-group-sm"><span class="input-group-addon">选项</span> <input type="text" name="checktitle" class="form-control" placeholder="选项"><span class="input-group-addon glyphicon glyphicon-remove"></span></div><br>');
			});
		});
		$(function() {
			$('.form-group').click(function() {
				$(this).find('span.glyphicon').parent().parent().remove();
			});
		});
		$(function() {
			$("#editview").on("click",".form-horizontal > .form-group > #add_check", function(e) {
				title = $("#editview >.form-horizontal > .input-group > input[name='title']").val();
				notes = $("#editview >.form-horizontal > .input-group > input[name='notes']").val();
				var p = '<div class="col-xs-10 col-sm-10 col-md-10 col-lg-10"><label for="name"><label id="title">'+title+'</label>:<label id="notes">'+notes+'</label></label><div>';
				var options = new Array();
				$("#editview >.form-horizontal > .input-group > input[name='checktitle']").each(function(i) {
					options.push($(this).val());
					p = p+ '<label class="checkbox-inline"><input type="checkbox" id="inlineCheckbox" value="'+i+'"> '+ $(this).val() +' </label>';
				}); 
				p = p+'</div></div><div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 btn-group-vertical input-group-sm">'
					+'<button type="button" class="btn btn-default">编辑</button><button type="button" class="btn btn-default">删除</button></div>';
				$("#preview").append(p);
				$("#editview").empty();
				
				$.post("index.php?admin/questionnaire/addQuestions", 
					{ nid:<?php echo $questionnaire->id(); ?>, 'title': title, 'notes': notes, 'type':'check', 'titles': options, 'values': options }, function(data){
				     alert(data); // John
			   }, "json");
			});
		});
		$(function() {
			$("#editview").on("click",".form-horizontal > .form-group > #add_radio", function(e) {
				title = $("#editview >.form-horizontal > .input-group > input[name='title']").val();
				notes = $("#editview >.form-horizontal > .input-group > input[name='notes']").val();
				var p = '<div class="col-xs-10 col-sm-10 col-md-10 col-lg-10"><label id="title">'+title+'</label>:<label id="notes">'+notes+'</label><div>';
				var options = new Array();
				$("#editview >.form-horizontal > .input-group > input[name='checktitle']").each(function(i) {
					options.push($(this).val());
					p = p+ '<label class="radio-inline"> <input type="radio" name="optionsRadiosinline" id="optionsRadios" value="'+i+'"> '+ $(this).val() +' </label>';
				}); 
				p = p+'</div></div><div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 btn-group-vertical input-group-sm">'
					+'<button type="button" id="edit" class="btn btn-default">编辑</button><button type="button" id="del" class="btn btn-default">删除</button></div>';
				$("#preview").append(p);
				$("#editview").empty();
				
				$.post("index.php?admin/questionnaire/addQuestions", 
					{ nid:<?php echo $questionnaire->id(); ?>, 'title': title, 'notes': notes, 'type':'radio', 'titles': options, 'values': options }, function(data){
				     alert(data); // John
			   }, "json");
			});
		});
		/*$(function() {
			$("#editview >.form-horizontal > .input-group > input[name='checktitle']").blur(function() {
				var options = new Array();
				$("#editview >.form-horizontal > .input-group > input[name='checktitle']").each(function() {
					if(options.indexOf($(this).val())>0){
						$(this).val("");
						alert("该选项已存在");
						return;
					}
					if($(this).val().length>0)
						options.push($(this).val());
					alert(options.length);
				});
			});
		});*/
		$(function() {
			$("#preview").on("click",".btn-group-vertical > #edit", function(e) {

				$.post("index.php?admin/questionnaire/editQuestions", 
						{ qid:$(e.target).parent().prev().children().eq(2).text() }, function(data){
					     alert(data); // John
				   }, "json");
				
				title = $("#preview > div > #title").text();
				notes = $("#preview > div > #notes").text();
				$("#editview").empty();
				var d = '<h4>添加单选</h4><form class="form-horizontal" role="form">'
					+'<div class="input-group input-group-lg input-group-sm">'
					+'<span class="input-group-addon">标题</span> <input type="text" name="title"'
					+' class="form-control" placeholder="标题" value="'+title+'">'
					+'</div>'
					+'<br>'
					+'<div class="input-group input-group-lg input-group-sm">'
					+'<span class="input-group-addon">备注</span> <input type="text" name="notes"'
					+' class="form-control" placeholder="备注" value="'+notes+'">'
					+'</div>'
					+'<br>';

					//+'<div class="input-group input-group-lg input-group-sm">'
					//+'<span class="input-group-addon">选项</span> <input type="text" name="checktitle"'
					//+' class="form-control" placeholder="选项" value="'+title+'"><span'
					//+' class="input-group-addon glyphicon glyphicon-remove"></span>'
					//+'</div>'
					//+'<br>'
					
					
					d = d + '<div class="form-group input-group-lg input-group-sm">'
					+'<label for="choiceadd"'
					+' class="col-xs-1 col-sm-1 col-md-1 col-lg-1 control-label">选项</label>'
					+'<button type="button" id="add_check_btn"'
					+' class="col-xs-10 col-sm-10 col-md-10 col-lg-10 btn btn-default">添加选项</button>'
					+'</div>'
					+'<br>'
					+'<div class="form-group input-group-lg input-group-sm">'
					+'<button type="button" id="add_radio"'
					+' class="btn btn-default">添加</button>'
					+'<button type="reset" id="reset"'
					+' class="btn btn-default">重置</button>'
					+'</div>'
					+'</form>';
				$("#editview").append(d);
			});
		});
		$(function() {
			$("#preview").on("click",".btn-group-vertical > #del", function(e) {
				$(e.target).parent().prev().children().eq(2).text();
				$(e.target).parent().prev().remove();
				$(e.target).parent().remove();
			});
		});