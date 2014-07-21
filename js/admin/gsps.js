
var notice = window.notice || {};

notice = function() {
	
	// 显示对话框，传入参数id
	function _showDialog(cid) {
		$("body").append("<div id='overlay' />");
		var h = $().height();
		var w = $().width();
		$("#overlay").height(h);
		$("#overlay").width(w);
		
		var $c = $("#" + cid);
		var box_h = $c.height();
		var box_w = $c.width();
		$c.css({
			top : $().scrollTop() + ($(window).height() - box_h) / 2 + "px", 
			left : (w - box_w) / 2 + "px"
		});
		$c.show();
	}
	
	// 隐藏对话框
	function _hideDialog(cid) {
		$("#" + cid).hide();
		$("#overlay").remove();
	}
	
	// 生成html内容所在容器，返回id信息
	function _prepareContent(html) {
		var _id = "";
		return _id
	}
	
	return {
		noticeById : function(cid) {
			_showDialog(cid);
		}, 
		
		noticeByHTML : function(html) {
			_showDialog(_prepareContent(html));
		}, 
		
		bookmark : function() {
			var $inputs = $("#bookmark_dialog input");
			$inputs.eq(0).val(document.location.href);
			$inputs.eq(1).val(document.title);
			_showDialog("bookmark_dialog");
			
			$inputs.eq(2).click(function() {
				
				var params = {
					action : "a", 
					url : $inputs.eq(0).val(), 
					name : $inputs.eq(1).val()
				};
				$.ajax({
					type : "get", 
					url : "/cgi-bin/gsps/diy/operate_bookmarks.cgi", 
					data : params, 
					success : function(data) {
						if(data.indexOf("ok!") > -1) {
							alert("添加成功！您可以在系统首页、发布助手收藏夹中查看。");
							notice.cancelBookmark();
						} else {
							alert("发生错误，请重试！");
						}
					}, 
					error : function(xhr, s, ex) {
						//alert(xhr.responseText);
						//alert("发生错误，请重试！");
					}
				});
			});
			
			$inputs.eq(3).click(function() {
				notice.cancelBookmark();
			});
			return false;
		}, 
		
		cancelBookmark : function() {
			_hideDialog("bookmark_dialog");
		}
	}
	
}();

$(document).ready(function() {	
	
	// 文档页调整分割条显示方式
	if (/document/i.test(document.location)) {
		// 分割条长度
		//$("#separator").height($(".left_col").height() + 120);
		
		// 显示方式
		$("#separator > img").toggle();
		var $ct = $(".left_col");
		$("#menutree").css({"position" : "absolute", "display" : "none"})
				.css("left", $("#main").position().left + 7 + "px")
				.css("top", $("#separator").position().top);
	} else {
		var l_h = $("#menutree").height() - $("#menubar").height() - $("#navbar").height() - 10;
		var r_h = $("#content").height();
		//$("#separator").height(l_h > r_h ? l_h : r_h);
	}
	
	// 为分割条绑定事件
	$("#separator").bind("click", function() {
		var $mt = $("#menutree");
		var $ct = $("#content");
		
		$("#separator > img").toggle();
		$mt.toggle();
		
		if (!/document/.test(document.location)) {
			if ($mt.css("display") === "none") {
				$ct.width($ct.width() + 160);
		        $ct.css({"margin-left":"0px"});
				$.cookie("separator", 1);
			} else {
				$ct.width($ct.width() - 160);
		        $ct.css({"margin-left":"160px"});
				$.cookie("separator", null);
			}
		} 
	});
	
	if ($.cookie("separator") == 1) {
		$("#separator > img").hide();
		$("#menutree").hide();
		$("#content").width($("#content").width() + 160);
		$("#content").css({"margin-left":"0px"});
	}
	
	// 为导航切换绑定事件
	$("#menulist").children().each(function(i) {
		$(this).bind("click", function(){
			$("#menulist").children().removeClass("selected");
			$(this).addClass("selected");
			if (typeof(tabChange) == 'function') {
				if (!tabChange($(this))){
					return false;
				}
			}
			$("#navbar").children().hide().eq(i).css("display", "");
		});
	});

    $('.ui-checkboxes').each(function(){
            $(this).buttonset();
    });

    //$( ".ui-input-button" ).button();

    $('.ui-radioes').each(function(){
            $(this).buttonset();
    });
	
	// 绑定功能菜单点击收起的事件
	$("#menutree h1").click(function() {
		$(this).next("div.menu-itemCont").toggle();
	});
	// 退出发布系统
	
});
