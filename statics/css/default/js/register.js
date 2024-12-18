$(function(){
	$("#username").blur(function(){
		checkname();
	});
	$("#email").blur(function(){
		checkemail();
	});
	$("#nickname").blur(function(){
		checknickname();
	});
})

function checkpass(){
	if($("#password").val().length < 6){
		layer.msg('密码不能低于6位', {icon:2,time: 1000});
		return false;
	}
	return true;
}

function checkpwdconfirm(){
	if($("#password").val() != $("#pwdconfirm").val()){
		layer.msg('两次密码不一致', {icon:2,time: 1000});
		return false;
	}
	return true;
}

function checkall(){
	if(!(checkname() && checkemail() && checknickname())){
		return false;
	}
	if(!(checkpass() && checkpwdconfirm())) {
		return false;
	}
	if($("#code").val() == ''){
		layer.msg('验证码不能为空', {icon:2,time: 1000});
		return false;
	}
	if($("input[name='agree']:checked").val()!=1){
		layer.msg('你必须同意注册协议', {icon:2,time: 1000});
		return false;
	}
	return true;
} 