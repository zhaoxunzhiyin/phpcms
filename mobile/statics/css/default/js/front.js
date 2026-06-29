$(function(){ 

	$("#searchselected").click(function(){ 
		$("#searchtab").toggle();
		if($(this).hasClass('searchopen')){
			$(this).removeClass("searchopen");
		}else{
			$(this).addClass("searchopen");
		}
	}); 

	$("#searchtab li").hover(function(){
		$(this).addClass("selected");
	},function(){
		$(this).removeClass("selected");
	});
	 
	$("#searchtab li").click(function(){
		$("#typeid").val($(this).attr('data') );
		$("#searchselected").html($(this).html());
		$("#searchtab").hide();
		$("#searchselected").removeClass("searchopen");
	});


	$(".nav>li").hover(function(){
		$(this).children('ul').stop(true,true).slideDown(200);
	},function(){
		$(this).children('ul').stop(true,true).slideUp(200);
	})
	
});