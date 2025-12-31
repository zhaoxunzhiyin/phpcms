	function checkmobile($field, $value, $fieldinfo) {
		$errortips = L('please_input_mobile');
		if(defined('IS_ADMIN') && IS_ADMIN) {
			$string = "<div id='mobile_div'><input type='text' name='info[mobile]' id='mobile' value='".$value."' class='input-text'></div>";
			$this->formValidator .= '$("#'.$field.'").formValidator({onshow:"'.$errortips.'",onfocus:"'.$errortips.'"}).inputValidator({min:1,onerror:"'.$errortips.'"});';
		} elseif($value && ROUTE_A!='register') {
			$string = "<div id='mobile_div'>".$value."</div>";
		} else {
			$string = "<div id='mobile_div'><input type='text' name='info[mobile]' id='mobile' value='' class='input-text' title='".L('sms_tips')."'> 
			<div class='submit'><button onclick='get_verify()' type='button' class='hqyz'>".L('get_sms_code')."</button></div> <div id='mobileTip' class='onShow'></div>
			<br>
			</div><div id='mobile_send_div' style='display:none'>".L('sms_checkcode_send_to')."<span id='mobile_send'></span>，<span id='edit_mobile' style='display:none'><a href='javascript:void();' onclick='edit_mobile()'>".L('sms_edit_mobile')."</a>，</span> ".L('repeat_send')."<br><br>
			<div class='submit'><button type='button' id='GetVerify' onclick='get_verify()' class='hqyz'>".L('repeat_sms_code')."</button></div> <BR><BR></div>".L('receive_sms_code')."<input type='text' name='mobile_verify' id='mobile_verify' value='' class='input-text'>";
			
					$this->formValidator .= '$("#'.$field.'").formValidator({onshow:"'.$errortips.'",onfocus:"'.$errortips.'"}).inputValidator({min:1,onerror:"'.$errortips.'"});';
					$errortips = L('input_receive_sms_code');
					$this->formValidator .= '$("#mobile_verify").formValidator({onshow:"'.$errortips.'",onfocus:"'.$errortips.'"}).inputValidator({min:1,onerror:"'.$errortips.'"}).ajaxValidator({
					type : "get",
					url : "api.php",
					data :"op=sms_idcheck&action=id_code",
					datatype : "html",
					getdata:{mobile:"mobile"},
					async:"false",
					success : function(data){
						if( data == "1" ) {
							return true;
						} else {
							return false;
						}
					},
					buttons: $("#dosubmit"),
					onerror : "'.L('checkcode_wrong').'",
					onwait : "'.L('connecting_please_wait').'"
				});';
		}
			$string .= '
			<SCRIPT LANGUAGE="JavaScript">
			<!--
				var times = 90;
				var isinerval;
				function get_verify() {
					var mobile = $("#mobile").val();
					var partten = /^[0-9]+.?[0-9]*/;
					if(!partten.test(mobile) || mobile.length != 11){
						Dialog.alert("'.L('input_right_mobile').'");
						return false;
					}
					$.ajax({
						type: "POST",
						dataType: "json",
						url: "api.php?op=sms",
						data: {mobile: mobile,session_code:session_code,'.SYS_TOKEN_NAME.':"'.csrf_hash().'",random:Math.random()},
						success: function(json) {
							if (json.code) {
								$("#mobile_send").html(mobile);
								$("#mobile_div").css("display","none");
								$("#mobile_send_div").css("display","");
								times = 120;
								$("#GetVerify").attr("disabled", true);
								isinerval = setInterval("CountDown()", 1000);
							} else {
								alert(json.msg);
							}
						}
					});
					
				}
				function CountDown() {
					if (times < 1) {
						$("#GetVerify").html("'.L('get_sms_code').'").attr("disabled", false);
						$("#edit_mobile").css("display","");
						clearInterval(isinerval);
						return;
					}
					$("#GetVerify").html(times+"'.L('wait_second_repeat_sms_code').'");
					times--;
				}
				function edit_mobile() {
					$("#mobile_div").css("display","");
					$("#mobile_send_div").css("display","none");
				}
			//-->
			</SCRIPT>
			';
			return $string;
	}
