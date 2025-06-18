<?php
	/**
	 * 返回附件类型图标
	 * @param $file 附件名称
	 * @param $type png为大图标，gif为小图标
	 */
	function file_icon($file,$type = 'png') {
		return WEB_PATH.'api.php?op=icon&fileext='.fileext($file);
	}
	
	/**
	 * 附件目录列表，暂时没用
	 * @param $dirpath 目录路径
	 * @param $currentdir 当前目录
	 */
	function file_list($dirpath,$currentdir) {
		$filepath = $dirpath.$currentdir;
		$list['list'] = glob($filepath.DIRECTORY_SEPARATOR.'*');
		if(!empty($list['list'])) rsort($list['list']);
		$list['local'] = str_replace(array(PC_PATH, DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR), array('',DIRECTORY_SEPARATOR), $filepath);
		return $list;
	}
	
	/**
	 * h5upload上传初始化
	 * 初始化h5upload上传中需要的参数
	 * @param $module 模块名称
	 * @param $catid 栏目id
	 * @param $args 传递参数
	 * @param $groupid 用户组id
	 * @param $isadmin 是否为管理员模式
	 */
	function initupload($module, $catid, $args, $groupid = '8', $isadmin = '0') {
		extract(geth5init($args));
		$ct = (int)pc_base::load_sys_class('input')->get('ct'); // 当已有数量
		$sess_id = SYS_TIME;
		$h5_auth_key = md5(SYS_KEY.$sess_id);
		$init = "$(document).ready(function(){
			// 初始化上传组件
			$('#file_upload').fileupload({
				disableImageResize: false,
				autoUpload: true,
				maxFileSize: " . floatval($file_size_limit) * 1024 * 1024 . ",
				acceptFileTypes: /(\.|\/)(".$file_types_post.")$/i,
				maxChunkSize: ".($chunk ? 20 * 1024 * 1024 : 0).",
				formData: {H5UPLOADSESSID : '".$sess_id."',module:'".$module."',catid:'".$catid."',h5_auth_key:'".$h5_auth_key."',isadmin:'".$isadmin."',groupid:'".$groupid."',args:'".$args."'},
				url: '".SELF."?m=attachment&c=attachments&a=h5upload&token=".csrf_hash()."',
				dataType: 'json',
				progressall: function (e, data) {
					// 上传进度条 all
					var progress = parseInt(data.loaded / data.total * 100, 10);
					$('#progress').show();
					$('#progress').removeClass('fade');
					$('#progress .progress-bar-success').attr('style', 'width: '+progress+'%');
				},
				add: function (e, data) {
					var myItems = data.originalFiles.length;
					var numItems = $('#fileupload_files .files_row').length;
					if(numItems + myItems".($ct ? ' + '.$ct : '')." > ".$file_upload_limit."){
						dr_tips(0, '".str_replace('{file_num}', $file_upload_limit, L('att_upload_num')).(CI_DEBUG ? '（可在自定义字段中设置本字段的个数值）' : '')."');
						return false;
					}
					data.submit();
				},
				done: function (e, data) {
					dr_tips(data.result.code, data.result.msg);
					$('#progress').hide();
					$('#progress').addClass('fade');
					if (data.result.code == 0) {
						return false;
					}
					var json = data.result.data;
					if (json.id == undefined || json.id == 'undefined') {
						return false;
					}
					if(json.ext == 1) {
						var img = '<span class=\"checkbox\"></span><input type=\"checkbox\" class=\"checkboxes\" name=\"ids[]\" value=\"'+json.id+'\" /><a class=\"on\"><div class=\"icon\"></div><img src=\"'+json.url+'\" width=\"80\" id=\"'+json.id+'\" path=\"'+json.url+'\" size=\"'+json.size+'\" filename=\"'+json.name+'\"/></a><i class=\"size\">'+json.size+'</i><i class=\"name\">'+json.name+'</i>';
					} else {
						var img = '<span class=\"checkbox\"></span><input type=\"checkbox\" class=\"checkboxes\" name=\"ids[]\" value=\"'+json.id+'\" /><a class=\"on\"><div class=\"icon\"></div><img src=\"".WEB_PATH."api.php?op=icon&fileext='+json.ext+'\" width=\"80\" id=\"'+json.id+'\" size=\"'+json.size+'\" path=\"'+json.url+'\" filename=\"'+json.name+'\"/></a><i class=\"size\">'+json.size+'</i><i class=\"name\">'+json.name+'</i>';
					}
					$.get('".SELF."?m=attachment&c=attachments&a=h5upload_json&aid='+json.id+'&src='+json.url+'&filename='+json.name+'&size='+json.size);
					$('#fileupload_files').append('<div class=\"col-md-2 col-sm-2 col-xs-6\"><div id=\"attachment_'+json.id+'\" class=\"files_row on\" onclick=\"javascript:att_cancel(this)\"></div></div>');
					$('#attachment_'+json.id).html(img);
					$('#att-status').append('|'+json.url);
					$('#att-name').append('|'+json.name);
					$('#att-id').append('|'+json.id);
				},
				fail: function (e, data) {
					//console.log(data.errorThrown);
					dr_tips(0, '系统故障：'+data.errorThrown);
					$('#progress').addClass('fade');
					$('#progress').hide();
				},
			});
		})";
		return $init;
	}
	/**
	 * 读取h5upload配置类型
	 * @param array $args h5上传配置信息
	 */
	function geth5init($args) {
		$args = dr_string2array(dr_authcode($args, 'DECODE'));
		$siteid = param::get_cookie('siteid');
		if(!$siteid) $siteid = get_siteid();
		/*foreach($args as $k=>$v) {
			$arr[$k] = $v;
		}*/
		$arr['siteid'] = intval($args['siteid']) ? intval($args['siteid']) : intval($siteid);
		$arr['file_upload_limit'] = intval($args['file_upload_limit']) ? intval($args['file_upload_limit']) : 10;
		$arr['file_types_post'] = $args['file_types_post'] ? $args['file_types_post'] : dr_site_value('upload_allowext', $arr['siteid']);
		$arr['file_size_limit'] = intval($args['size']) ? intval($args['size']) : (dr_site_value('upload_maxsize', $arr['siteid']) ? dr_site_value('upload_maxsize', $arr['siteid']) : 0);
		$arr['allowupload'] = intval($args['allowupload']);
		$arr['thumb_width'] = intval($args['thumb_width']);
		$arr['thumb_height'] = intval($args['thumb_height']);
		$arr['watermark_enable'] = dr_is_empty($args['watermark_enable']) ? 1 : intval($args['watermark_enable']);
		$arr['attachment'] = intval($args['attachment']);
		$arr['image_reduce'] = intval($args['image_reduce']);
		$arr['chunk'] = intval($args['chunk']);
		return $arr;
	}
?>