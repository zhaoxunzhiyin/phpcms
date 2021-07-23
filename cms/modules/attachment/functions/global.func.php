<?php
	/**
	 * 返回附件类型图标
	 * @param $file 附件名称
	 * @param $type png为大图标，gif为小图标
	 */
	function file_icon($file,$type = 'png') {
		$ext = fileext($file);
		if ($type!='png') {
			if (is_file(CMS_PATH.'statics/images/ext/'.$ext.'.'.$type)) {
				return IMG_PATH.'ext/'.$ext.'.'.$type;
			} else {
				return IMG_PATH.'ext/blank.'.$type;
			}
		} elseif (is_file(CMS_PATH.'statics/images/ext/'.$ext.'.png')) {
			return IMG_PATH.'ext/'.$ext.'.png';
		} else {
			return IMG_PATH.'ext/do.png';
		}
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
	 * @param $userid 用户id
	 * @param $groupid 用户组id
	 * @param $isadmin 是否为管理员模式
	 */
	function initupload($module, $catid,$args, $userid, $groupid = '8', $isadmin = '0',$userid_h5='0'){
		$grouplist = getcache('grouplist','member');
		if($isadmin==0 && !$grouplist[$groupid]['allowattachment']) return false;
		extract(geth5init($args));
		$siteid = param::get_cookie('siteid');
		$site_setting = get_site_setting($siteid);
		$file_size_limit = $site_setting['upload_maxsize'];
		if ($file_upload_limit==1) {
			$multi = 'false';
		} else {
			$multi = 'true';
		}
		$sess_id = SYS_TIME;
		$h5_auth_key = md5(pc_base::load_config('system','auth_key').$sess_id);
		$init = "$(document).ready(function(){
			layui.use(['upload', 'element', 'layer'], function () {
				var upload = layui.upload,element = layui.element,layer = layui.layer;
				upload.render({
					elem:'#file_upload',
					accept:'file',
					field:'file_upload',
					data: {H5UPLOADSESSID : '".$sess_id."',module:'".$module."',catid:'".$catid."',userid:'".$userid."',siteid:'".$siteid."',dosubmit:'1',thumb_width:'".$thumb_width."',thumb_height:'".$thumb_height."',watermark_enable:'".$watermark_enable."',attachment:'".$attachment."',image_reduce:'".$image_reduce."',filetype_post:'".$file_types_post."',h5_auth_key:'".$h5_auth_key."',isadmin:'".$isadmin."',groupid:'".$groupid."',userid_h5:'".$userid_h5."'},
					url: '".APP_PATH."index.php?m=attachment&c=attachments&a=h5upload',
					exts: '".$file_types_post."',
					size: ".$file_size_limit.",
					multiple: ".$multi.",
					number: ".$file_upload_limit.",
					before: function (obj) {
						var number = $('#fsUpload .files_row').length;
						if (number >= ".$file_upload_limit.") {
							dr_tips(0, '".str_replace('{file_num}', $file_upload_limit, L('att_upload_num'))."');
							return delete files[index];
						}
						element.progress('progress', '0%');
						layer.msg('上传中', {icon: 16, time: 0});
					},
					done: function(data){
						if(data.code == 1){
							dr_tips(data.code, data.msg);
							if(data.id == 0) {
								dr_tips(0, data.src)
								return false;
							}
							if(data.ext == 1) {
								var img = '<span class=\"checkbox\"></span><input type=\"checkbox\" class=\"checkboxes\" name=\"ids[]\" value=\"'+data.id+'\" /><a href=\"javascript:;\" onclick=\"javascript:att_cancel(this,'+data.id+',\'upload\')\" class=\"on\"><div class=\"icon\"></div><img src=\"'+data.src+'\" width=\"80\" id=\"'+data.id+'\" path=\"'+data.src+'\" filename=\"'+data.filename+'\"/><i class=\"size\">'+data.size+'</i><i class=\"name\" title=\"'+data.filename+'\">'+data.filename+'</i></a>';
							} else {
								var img = '<span class=\"checkbox\"></span><input type=\"checkbox\" class=\"checkboxes\" name=\"ids[]\" value=\"'+data.id+'\" /><a href=\"javascript:;\" onclick=\"javascript:att_cancel(this,'+data.id+',\'upload\')\" class=\"on\"><div class=\"icon\"></div><img src=\"".IMG_PATH."ext/'+data.ext+'.png\" width=\"80\" id=\"'+data.id+'\" path=\"'+data.src+'\" filename=\"'+data.filename+'\"/><i class=\"size\">'+data.size+'</i><i class=\"name\" title=\"'+data.filename+'\">'+data.filename+'</i></a>';
							}
							$.get('index.php?m=attachment&c=attachments&a=h5upload_json&aid='+data.id+'&src='+data.src+'&filename='+data.filename+'&size='+data.size);
							$('#fsUpload').append('<div id=\"attachment_'+data.id+'\" class=\"files_row on\"></div>');
							$('#attachment_'+data.id).html(img);
							$('#att-status').append('|'+data.src);
							$('#att-name').append('|'+data.filename);
						}else{
							dr_tips(data.code, data.msg);
						}
						$('#progress').hide();
						$('#progress').addClass('fade');
					},
					progress: function(n, elem, e){
						$('#progress').show();
						$('#progress').removeClass('fade');
						element.progress('progress', n + '%');
					}
				});
			});
		})";
		return $init;
	}
	/**
	 * 获取站点配置信息
	 * @param  $siteid 站点id
	 */
	function get_site_setting($siteid) {
		$siteinfo = getcache('sitelist', 'commons');
		return string2array($siteinfo[$siteid]['setting']);
	}
	/**
	 * 读取h5upload配置类型
	 * @param array $args h5上传配置信息
	 */
	function geth5init($args) {
		$siteid = get_siteid();
		$site_setting = get_site_setting($siteid);
		$site_allowext = $site_setting['upload_allowext'];
		$args = explode(',',$args);
		$arr['file_upload_limit'] = intval($args[0]) ? intval($args[0]) : '8';
		$args['1'] = ($args[1]!='') ? $args[1] : $site_allowext;
		$arr_allowext = explode('|', $args[1]);
		foreach($arr_allowext as $k=>$v) {
			$v = '*.'.$v;
			$array[$k] = $v;
		}
		$upload_allowext = implode(';', $array);
		$arr['file_types'] = $upload_allowext;
		$arr['file_types_post'] = $args[1];
		$arr['allowupload'] = intval($args[2]);
		$arr['thumb_width'] = intval($args[3]);
		$arr['thumb_height'] = intval($args[4]);
		$arr['watermark_enable'] = ($args[5]=='') ? 1 : intval($args[5]);
		$arr['attachment'] = intval($args[6]);
		$arr['image_reduce'] = intval($args[7]);
		return $arr;
	}	
	/**
	 * 判断是否为图片
	 */
	function is_images($file) {
		$ext_arr = array('jpg','gif','png','bmp','jpeg','tiff');
		$ext = fileext($file);
		return in_array($ext,$ext_arr) ? $ext_arr :false;
	}
	
	/**
	 * 判断是否为视频
	 */
	function is_video($file) {
		$ext_arr = array('rm','mpg','avi','mpeg','wmv','flv','asf','rmvb');
		$ext = fileext($file);
		return in_array($ext,$ext_arr) ? $ext_arr :false;
	}

?>