<?php
defined('IN_CMS') or exit('No permission resources.');
class index {
	private $input,$cache,$_userid,$_username,$_groupid,$siteid,$grouplist,$isadmin,$roleid,$filename,$imageAllowFiles,$catcherAllowFiles,$videoAllowFiles,$musicAllowFiles,$fileAllowFiles,$imageManagerAllowFiles,$fileManagerAllowFiles,$videoManagerAllowFiles,$musicManagerAllowFiles,$config;
	function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		$this->roleid = defined('IS_ADMIN') && IS_ADMIN ? param::get_session('roleid') : 0;
		$this->isadmin = $this->roleid ? 1 : 0;
		$this->_userid = $this->isadmin ? (param::get_session('userid') ? param::get_session('userid') : param::get_cookie('userid')) : param::get_cookie('_userid');
		$this->_username = param::get_cookie('_username');
		$this->_groupid = param::get_cookie('_groupid') ? param::get_cookie('_groupid') : 8;
	}
	/**
	 * 404 页面
	 */
	public function init() {
		if (IS_DEV) {
			$uri = $this->input->get('uri', true);
			$msg = '没有找到这个页面: '.$uri;
		} else {
			$msg = L('没有找到这个页面');
		}
		param::goto_404_page($msg);
	}
	/**
	 * 跳转地址安全检测
	 */
	public function jump() {
		if(intval($this->input->get('siteid'))) {
			$siteid = intval($this->input->get('siteid'));
		} else if(defined('SITE_ID') && SITE_ID!=1) {
			$siteid = SITE_ID;
		} else {
			$siteid = get_siteid();
		}
		$siteid = $GLOBALS['siteid'] = max($siteid,1);
		define('SITEID', $siteid);
		//SEO
		$SEO = seo($siteid, 0, L('安全中心'));
		$go = $this->input->get('go');
		$link = $this->cache->get_auth_data($go, 1);
		if (!$link) {
			dr_redirect(WEB_PATH.'index.php?m=404', 'refresh');exit;
		}
		$arr = parse_url($link);
		$host = $arr['host'];
		$default_style = dr_site_info('default_style', $siteid);
		if(!$default_style) $default_style = 'default';

		pc_base::load_sys_class('service')->assign([
			'SEO' => $SEO,
			'siteid' => $siteid,
			'_userid' => $this->_userid,
			'_username' => $this->_username,
			'_groupid' => $this->_groupid,
			'link' => $link,
			'host' => $host,
		]);
		pc_base::load_sys_class('service')->display('404','jump',$default_style);
	}
	/**
	 * 百度编辑器处理接口
	 */
	public function ueditor() {
		pc_base::load_app_class('uploader', '404', 0);
		$this->siteid = intval($this->input->get('siteid'));
		if(!$this->siteid) $this->siteid = get_siteid();
		$this->grouplist = getcache('grouplist', 'member');
		$this->filename = (SYS_ATTACHMENT_FILE ? $this->siteid.'/' : '').dr_site_value('filename', $this->siteid);
		$this->imageAllowFiles = dr_site_value('imageAllowFiles', $this->siteid) ? '.'.dr_site_value('imageAllowFiles', $this->siteid) : '';
		$this->imageAllowFiles = str_replace('|','|.',$this->imageAllowFiles);
		$this->imageAllowFiles = explode('|',$this->imageAllowFiles);
		$this->catcherAllowFiles = dr_site_value('catcherAllowFiles', $this->siteid) ? '.'.dr_site_value('catcherAllowFiles', $this->siteid) : '';
		$this->catcherAllowFiles = str_replace('|','|.',$this->catcherAllowFiles);
		$this->catcherAllowFiles = explode('|',$this->catcherAllowFiles);
		$this->videoAllowFiles = dr_site_value('videoAllowFiles', $this->siteid) ? '.'.dr_site_value('videoAllowFiles', $this->siteid) : '';
		$this->videoAllowFiles = str_replace('|','|.',$this->videoAllowFiles);
		$this->videoAllowFiles = explode('|',$this->videoAllowFiles);
		$this->musicAllowFiles = dr_site_value('musicAllowFiles', $this->siteid) ? '.'.dr_site_value('musicAllowFiles', $this->siteid) : '';
		$this->musicAllowFiles = str_replace('|','|.',$this->musicAllowFiles);
		$this->musicAllowFiles = explode('|',$this->musicAllowFiles);
		$this->fileAllowFiles = dr_site_value('fileAllowFiles', $this->siteid) ? '.'.dr_site_value('fileAllowFiles', $this->siteid) : '';
		$this->fileAllowFiles = str_replace('|','|.',$this->fileAllowFiles);
		$this->fileAllowFiles = explode('|',$this->fileAllowFiles);
		$this->imageManagerAllowFiles = dr_site_value('imageManagerAllowFiles', $this->siteid) ? '.'.dr_site_value('imageManagerAllowFiles', $this->siteid) : '';
		$this->imageManagerAllowFiles = str_replace('|','|.',$this->imageManagerAllowFiles);
		$this->imageManagerAllowFiles = explode('|',$this->imageManagerAllowFiles);
		$this->fileManagerAllowFiles = dr_site_value('fileManagerAllowFiles', $this->siteid) ? '.'.dr_site_value('fileManagerAllowFiles', $this->siteid) : '';
		$this->fileManagerAllowFiles = str_replace('|','|.',$this->fileManagerAllowFiles);
		$this->fileManagerAllowFiles = explode('|',$this->fileManagerAllowFiles);
		$this->videoManagerAllowFiles = dr_site_value('videoManagerAllowFiles', $this->siteid) ? '.'.dr_site_value('videoManagerAllowFiles', $this->siteid) : '';
		$this->videoManagerAllowFiles = str_replace('|','|.',$this->videoManagerAllowFiles);
		$this->videoManagerAllowFiles = explode('|',$this->videoManagerAllowFiles);
		$this->musicManagerAllowFiles = dr_site_value('musicManagerAllowFiles', $this->siteid) ? '.'.dr_site_value('musicManagerAllowFiles', $this->siteid) : '';
		$this->musicManagerAllowFiles = str_replace('|','|.',$this->musicManagerAllowFiles);
		$this->musicManagerAllowFiles = explode('|',$this->musicManagerAllowFiles);

		$this->config = array (
			'siteid'=>$this->siteid,
			'module'=>trim($this->input->get('module')),
			'catid'=>intval($this->input->get('catid')),
			'userid'=>intval($this->_userid),
			'isadmin'=>intval($this->isadmin),
			'roleid'=>$this->roleid,
			'groupid'=>$this->_groupid,
			'is_wm'=>intval($this->input->get('is_wm')),
			'is_esi'=>intval($this->input->get('is_esi')),
			'attachment'=>intval($this->input->get('attachment')),
			'image_reduce'=>intval($this->input->get('image_reduce')),
			/* 上传图片配置项 */
			'imageActionName'=>'uploadimage', /* 执行上传图片的action名称 */
			'imageFieldName'=>'upfile', /* 提交的图片表单名称 */
			'imageMaxSize'=>(intval(dr_site_value('imageMaxSize', $this->siteid)) ? intval(dr_site_value('imageMaxSize', $this->siteid)) : intval(dr_site_value('upload_maxsize', $this->siteid))) * 1024 *1024, /* 上传大小限制，单位B */
			'imageAllowFiles'=>$this->imageAllowFiles, /* 上传图片格式显示 */
			'imageCompressEnable'=>false, /* 是否压缩图片,默认是true */
			'imageCompressBorder'=>1600, /* 图片压缩最长边限制 */
			'imageInsertAlign'=>'none', /* 插入的图片浮动方式 */
			'imageUrlPrefix'=>'', /* 图片访问路径前缀 */
			'imagePathFormat'=>$this->filename, /* 上传保存路径,可以自定义保存路径和文件名格式 */
										/* {filename} 会替换成原文件名,配置这项需要注意中文乱码问题 */
										/* {rand:6} 会替换成随机数,后面的数字是随机数的位数 */
										/* {time} 会替换成时间戳 */
										/* {yyyy} 会替换成四位年份 */
										/* {yy} 会替换成两位年份 */
										/* {mm} 会替换成两位月份 */
										/* {dd} 会替换成两位日期 */
										/* {hh} 会替换成两位小时 */
										/* {ii} 会替换成两位分钟 */
										/* {ss} 会替换成两位秒 */
										/* 非法字符 \ =>* ? ' < > | */
										/* 具请体看线上文档=>fex.baidu.com/ueditor/#use-format_upload_filename */

			/* 涂鸦图片上传配置项 */
			'scrawlActionName'=>'uploadscrawl', /* 执行上传涂鸦的action名称 */
			'scrawlFieldName'=>'upfile', /* 提交的图片表单名称 */
			'scrawlPathFormat'=>$this->filename, /* 上传保存路径,可以自定义保存路径和文件名格式 */
			'scrawlMaxSize'=>intval(dr_site_value('upload_maxsize', $this->siteid)) * 1024 *1024, /* 上传大小限制，单位B */
			'scrawlUrlPrefix'=>'', /* 图片访问路径前缀 */
			'scrawlInsertAlign'=>'none',

			/* 抓取远程图片配置 */
			'catcherLocalDomain'=>array('127.0.0.1', 'localhost', 'img.baidu.com'),
			'catcherActionName'=>'catchimage', /* 执行抓取远程图片的action名称 */
			'catcherFieldName'=>'source', /* 提交的图片列表表单名称 */
			'catcherPathFormat'=>$this->filename, /* 上传保存路径,可以自定义保存路径和文件名格式 */
			'catcherUrlPrefix'=>'', /* 图片访问路径前缀 */
			'catcherMaxSize'=>(intval(dr_site_value('catcherMaxSize', $this->siteid)) ? intval(dr_site_value('catcherMaxSize', $this->siteid)) : intval(dr_site_value('upload_maxsize', $this->siteid))) * 1024 *1024, /* 上传大小限制，单位B */
			'catcherAllowFiles'=>$this->catcherAllowFiles, /* 抓取图片格式显示 */

			/* 上传视频配置 */
			'videoActionName'=>'uploadvideo', /* 执行上传视频的action名称 */
			'videoFieldName'=>'upfile', /* 提交的视频表单名称 */
			'videoPathFormat'=>$this->filename, /* 上传保存路径,可以自定义保存路径和文件名格式 */
			'videoUrlPrefix'=>'', /* 视频访问路径前缀 */
			'videoMaxSize'=>(intval(dr_site_value('videoMaxSize', $this->siteid)) ? intval(dr_site_value('videoMaxSize', $this->siteid)) : intval(dr_site_value('upload_maxsize', $this->siteid))) * 1024 *1024, /* 上传大小限制，单位B，默认100MB */
			'videoAllowFiles'=>$this->videoAllowFiles, /* 上传视频格式显示 */

			/* 上传音频配置 */
			'musicActionName'=>'uploadmusic', /* 执行上传音频的action名称 */
			'musicFieldName'=>'upfile', /* 提交的音频表单名称 */
			'musicPathFormat'=>$this->filename, /* 上传保存路径,可以自定义保存路径和文件名格式 */
			'musicUrlPrefix'=>'', /* 音频访问路径前缀 */
			'musicMaxSize'=>(intval(dr_site_value('musicMaxSize', $this->siteid)) ? intval(dr_site_value('musicMaxSize', $this->siteid)) : intval(dr_site_value('upload_maxsize', $this->siteid))) * 1024 *1024, /* 上传大小限制，单位B，默认20MB */
			'musicAllowFiles'=>$this->musicAllowFiles, /* 上传音频格式显示 */

			/* 上传文件配置 */
			"fileActionName"=>"uploadfile", /* controller里,执行上传视频的action名称 */
			"fileFieldName"=>"upfile", /* 提交的文件表单名称 */
			"filePathFormat"=>$this->filename, /* 上传保存路径,可以自定义保存路径和文件名格式 */
			"fileUrlPrefix"=>'', /* 文件访问路径前缀 */
			"fileMaxSize"=>(intval(dr_site_value('fileMaxSize', $this->siteid)) ? intval(dr_site_value('fileMaxSize', $this->siteid)) : intval(dr_site_value('upload_maxsize', $this->siteid))) * 1024 *1024, /* 上传大小限制，单位B，默认50MB */
			"fileAllowFiles"=>$this->fileAllowFiles, /* 上传文件格式显示 */

			/* 上传Word配置 */
			"wordActionName"=>"uploadword", /* controller里,执行上传视频的action名称 */
			"wordFieldName"=>"upfile", /* 提交的文件表单名称 */
			"wordPathFormat"=>$this->filename, /* 上传保存路径,可以自定义保存路径和文件名格式 */
			"wordUrlPrefix"=>'', /* 文件访问路径前缀 */
			"wordMaxSize"=>(intval(dr_site_value('fileMaxSize', $this->siteid)) ? intval(dr_site_value('fileMaxSize', $this->siteid)) : intval(dr_site_value('upload_maxsize', $this->siteid))) * 1024 *1024, /* 上传大小限制，单位B，默认50MB */
			"wordAllowFiles"=>$this->fileAllowFiles, /* 上传文件格式显示 */

			/* 列出指定目录下的图片 */
			'imageManagerActionName'=>'listimage', /* 执行图片管理的action名称 */
			'imageManagerListPath'=>'', /* 指定要列出图片的目录 */
			'imageManagerListSize'=>intval(dr_site_value('imageManagerListSize', $this->siteid)), /* 每次列出文件数量 */
			'imageManagerUrlPrefix'=>'', /* 图片访问路径前缀 */
			'imageManagerInsertAlign'=>'none', /* 插入的图片浮动方式 */
			'imageManagerAllowFiles'=>$this->imageAllowFiles, /* 列出的文件类型 */

			/* 列出指定目录下的文件 */
			'fileManagerActionName'=>'listfile', /* 执行文件管理的action名称 */
			'fileManagerListPath'=>'', /* 指定要列出文件的目录 */
			'fileManagerUrlPrefix'=>'', /* 文件访问路径前缀 */
			'fileManagerListSize'=>intval(dr_site_value('fileManagerListSize', $this->siteid)), /* 每次列出文件数量 */
			'fileManagerAllowFiles'=>$this->fileAllowFiles, /* 列出的文件类型 */

			/* 列出指定目录下的视频 */
			'videoManagerActionName'=>'listvideo', /* 执行文件管理的action名称 */
			'videoManagerListPath'=>'', /* 指定要列出文件的目录 */
			'videoManagerUrlPrefix'=>'', /* 文件访问路径前缀 */
			'videoManagerListSize'=>intval(dr_site_value('videoManagerListSize', $this->siteid)), /* 每次列出文件数量 */
			'videoManagerAllowFiles'=>$this->videoAllowFiles, /* 列出的文件类型 */

			/* 列出指定目录下的音频 */
			'musicManagerActionName'=>'listmusic', /* 执行文件管理的action名称 */
			'musicManagerListPath'=>'', /* 指定要列出文件的目录 */
			'musicManagerUrlPrefix'=>'', /* 文件访问路径前缀 */
			'musicManagerListSize'=>intval(dr_site_value('musicManagerListSize', $this->siteid)), /* 每次列出文件数量 */
			'musicManagerAllowFiles'=>$this->musicAllowFiles /* 列出的文件类型 */
		);
		$action = $this->input->get('action');
		$error = $this->check_upload_auth();
		if (!$error) {
			switch ($action) {
				case 'config':
					$result = dr_array2string($this->config);
					break;

				/* 上传图片 */
				case 'uploadimage':
				/* 上传涂鸦 */
				case 'uploadscrawl':
				/* 上传视频 */
				case 'uploadvideo':
				/* 上传音频 */
				case 'uploadmusic':
				/* 上传文件 */
				case 'uploadfile':
				/* 上传文件 */
				case 'uploadword':
					$base64 = "upload";
					switch ($action) {
						case 'uploadimage':
							$config = array(
								'siteid'=>$this->config['siteid'],
								'module'=>$this->config['module'],
								'catid'=>$this->config['catid'],
								'userid'=>$this->config['userid'],
								'isadmin'=>$this->config['isadmin'],
								'groupid'=>$this->config['groupid'],
								'is_wm'=>$this->config['is_wm'],
								'is_esi'=>$this->config['is_esi'],
								'attachment'=>intval($this->config['attachment']),
								'image_reduce'=>$this->config['image_reduce'],
								"pathFormat" => $this->config['imagePathFormat'],
								"maxSize" => $this->config['imageMaxSize'],
								"allowFiles" => $this->config['imageAllowFiles']
							);
							$fieldName = $this->config['imageFieldName'];
							break;
						case 'uploadscrawl':
							$config = array(
								'siteid'=>$this->config['siteid'],
								'module'=>$this->config['module'],
								'catid'=>$this->config['catid'],
								'userid'=>$this->config['userid'],
								'isadmin'=>$this->config['isadmin'],
								'groupid'=>$this->config['groupid'],
								'is_wm'=>$this->config['is_wm'],
								'is_esi'=>$this->config['is_esi'],
								'attachment'=>$this->config['attachment'],
								'image_reduce'=>$this->config['image_reduce'],
								"pathFormat" => $this->config['scrawlPathFormat'],
								"maxSize" => $this->config['scrawlMaxSize'],
								"allowFiles" => $this->config['scrawlAllowFiles'],
								"oriName" => "scrawl.png"
							);
							$fieldName = $this->config['scrawlFieldName'];
							$base64 = "base64";
							break;
						case 'uploadvideo':
							$config = array(
								'siteid'=>$this->config['siteid'],
								'module'=>$this->config['module'],
								'catid'=>$this->config['catid'],
								'userid'=>$this->config['userid'],
								'isadmin'=>$this->config['isadmin'],
								'groupid'=>$this->config['groupid'],
								'is_wm'=>$this->config['is_wm'],
								'is_esi'=>$this->config['is_esi'],
								'attachment'=>$this->config['attachment'],
								'image_reduce'=>$this->config['image_reduce'],
								"pathFormat" => $this->config['videoPathFormat'],
								"maxSize" => $this->config['videoMaxSize'],
								"allowFiles" => $this->config['videoAllowFiles']
							);
							$fieldName = $this->config['videoFieldName'];
							break;
						case 'uploadmusic':
							$config = array(
								'siteid'=>$this->config['siteid'],
								'module'=>$this->config['module'],
								'catid'=>$this->config['catid'],
								'userid'=>$this->config['userid'],
								'isadmin'=>$this->config['isadmin'],
								'groupid'=>$this->config['groupid'],
								'is_wm'=>$this->config['is_wm'],
								'is_esi'=>$this->config['is_esi'],
								'attachment'=>$this->config['attachment'],
								'image_reduce'=>$this->config['image_reduce'],
								"pathFormat" => $this->config['musicPathFormat'],
								"maxSize" => $this->config['musicMaxSize'],
								"allowFiles" => $this->config['musicAllowFiles']
							);
							$fieldName = $this->config['musicFieldName'];
							break;
						case 'uploadword':
							$config = array(
								'siteid'=>$this->config['siteid'],
								'module'=>$this->config['module'],
								'catid'=>$this->config['catid'],
								'userid'=>$this->config['userid'],
								'isadmin'=>$this->config['isadmin'],
								'groupid'=>$this->config['groupid'],
								'is_wm'=>$this->config['is_wm'],
								'is_esi'=>$this->config['is_esi'],
								'attachment'=>$this->config['attachment'],
								'image_reduce'=>$this->config['image_reduce'],
								"pathFormat" => $this->config['wordPathFormat'],
								"maxSize" => $this->config['wordMaxSize'],
								"allowFiles" => $this->config['wordAllowFiles']
							);
							$fieldName = $this->config['wordFieldName'];
							break;
						case 'uploadfile':
						default:
							$config = array(
								'siteid'=>$this->config['siteid'],
								'module'=>$this->config['module'],
								'catid'=>$this->config['catid'],
								'userid'=>$this->config['userid'],
								'isadmin'=>$this->config['isadmin'],
								'groupid'=>$this->config['groupid'],
								'is_wm'=>$this->config['is_wm'],
								'is_esi'=>$this->config['is_esi'],
								'attachment'=>$this->config['attachment'],
								'image_reduce'=>$this->config['image_reduce'],
								"pathFormat" => $this->config['filePathFormat'],
								"maxSize" => $this->config['fileMaxSize'],
								"allowFiles" => $this->config['fileAllowFiles']
							);
							$fieldName = $this->config['fileFieldName'];
							break;
					}

					/* 生成上传实例对象并完成上传 */
					$up = new uploader($fieldName, $config, $base64);

					/* 返回数据 */
					if ($action=='uploadword') {
						$cache_file = $up->getFileInfo();
						if ($cache_file['url']) {
							$html = readWordToHtml(str_replace(SYS_UPLOAD_URL, SYS_UPLOAD_PATH, $cache_file['url']), $config['module'], $config['isadmin'], $config['userid'], $config['catid'], $config['siteid'], $config['is_wm'], $config['attachment'], $config['image_reduce'], md5(FC_NOW_URL.$this->input->get_user_agent().$this->input->ip_address().$config['userid']));
							$result = dr_array2string(array(
								'status'=> 1,
								'msg'=> '',
								'data'=> $html
							));
						} else {
							$result = dr_array2string(array(
								'status'=> 0,
								'msg'=> $up->getFileInfo()['state']
							));
						}
					} else {
						$result = dr_array2string($up->getFileInfo());
					}
					break;

				/* 上传截图 */
				case 'uploadscreen':
					$result = dr_array2string(array(
						'state'=> '本图标功能已禁用，请使用截图软件截图后，再粘贴进编辑器中'
					));
					break;

				/* 列出图片 */
				case 'listimage':
				/* 列出文件 */
				case 'listfile':
				/* 列出视频 */
				case 'listvideo':
				/* 列出音频 */
				case 'listmusic':
					/* 判断类型 */
					switch ($action) {
						/* 列出文件 */
						case 'listfile':
							$config = array(
								'siteid'=>$this->config['siteid'],
								'module'=>$this->config['module'],
								'catid'=>$this->config['catid'],
								'userid'=>$this->config['userid'],
								'isadmin'=>$this->config['isadmin'],
								'roleid'=>$this->config['roleid'],
								'groupid'=>$this->config['groupid'],
								'is_wm'=>$this->config['is_wm'],
								'is_esi'=>$this->config['is_esi'],
								'attachment'=>$this->config['attachment'],
								'image_reduce'=>$this->config['image_reduce']
							);
							$siteid = $this->config['siteid'];
							$catid = $this->config['catid'];
							$userid = $this->config['userid'];
							$isadmin = $this->config['isadmin'];
							$roleid = $this->config['roleid'];
							$groupid = $this->config['groupid'];
							$allowFiles = $this->config['fileManagerAllowFiles'];
							$listSize = $this->config['fileManagerListSize'];
							$path = $this->config['fileManagerListPath'];
							break;
						/* 列出视频 */
						case 'listvideo':
							$config = array(
								'siteid'=>$this->config['siteid'],
								'module'=>$this->config['module'],
								'catid'=>$this->config['catid'],
								'userid'=>$this->config['userid'],
								'isadmin'=>$this->config['isadmin'],
								'roleid'=>$this->config['roleid'],
								'groupid'=>$this->config['groupid'],
								'is_wm'=>$this->config['is_wm'],
								'is_esi'=>$this->config['is_esi'],
								'attachment'=>$this->config['attachment'],
								'image_reduce'=>$this->config['image_reduce']
							);
							$siteid = $this->config['siteid'];
							$catid = $this->config['catid'];
							$userid = $this->config['userid'];
							$isadmin = $this->config['isadmin'];
							$roleid = $this->config['roleid'];
							$groupid = $this->config['groupid'];
							$allowFiles = $this->config['videoManagerAllowFiles'];
							$listSize = $this->config['videoManagerListSize'];
							$path = $this->config['videoManagerListPath'];
							break;
						/* 列出音频 */
						case 'listmusic':
							$config = array(
								'siteid'=>$this->config['siteid'],
								'module'=>$this->config['module'],
								'catid'=>$this->config['catid'],
								'userid'=>$this->config['userid'],
								'isadmin'=>$this->config['isadmin'],
								'roleid'=>$this->config['roleid'],
								'groupid'=>$this->config['groupid'],
								'is_wm'=>$this->config['is_wm'],
								'is_esi'=>$this->config['is_esi'],
								'attachment'=>$this->config['attachment'],
								'image_reduce'=>$this->config['image_reduce']
							);
							$siteid = $this->config['siteid'];
							$catid = $this->config['catid'];
							$userid = $this->config['userid'];
							$isadmin = $this->config['isadmin'];
							$roleid = $this->config['roleid'];
							$groupid = $this->config['groupid'];
							$allowFiles = $this->config['musicManagerAllowFiles'];
							$listSize = $this->config['musicManagerListSize'];
							$path = $this->config['musicManagerListPath'];
							break;
						/* 列出图片 */
						case 'listimage':
						default:
							$config = array(
								'siteid'=>$this->config['siteid'],
								'module'=>$this->config['module'],
								'catid'=>$this->config['catid'],
								'userid'=>$this->config['userid'],
								'isadmin'=>$this->config['isadmin'],
								'roleid'=>$this->config['roleid'],
								'groupid'=>$this->config['groupid'],
								'is_wm'=>$this->config['is_wm'],
								'is_esi'=>$this->config['is_esi'],
								'attachment'=>$this->config['attachment'],
								'image_reduce'=>$this->config['image_reduce']
							);
							$siteid = $this->config['siteid'];
							$catid = $this->config['catid'];
							$userid = $this->config['userid'];
							$isadmin = $this->config['isadmin'];
							$roleid = $this->config['roleid'];
							$groupid = $this->config['groupid'];
							$allowFiles = $this->config['imageManagerAllowFiles'];
							$listSize = $this->config['imageManagerListSize'];
							$path = $this->config['imageManagerListPath'];
					}
					$allowFiles = explode('.', join("", $allowFiles));
					if (!$allowFiles[0]) {
						unset($allowFiles[0]);
					}

					/* 获取参数 */
					$size = $this->input->get('size') ? html2code($this->input->get('size')) : $listSize;
					$start = $this->input->get('start') ? html2code($this->input->get('start')) : 0;
					$end = $start + $size;

					/* 获取文件列表 */
					$thisdb = pc_base::load_model('attachment_model');
					$where = array('fileext'=>$allowFiles, 'module<>'=>'member', 'siteid'=>$siteid);
					if ($isadmin && $roleid && cleck_admin($roleid)) {$where2 = array();} else {$where2 = array('isadmin'=>(int)$isadmin, 'userid'=>(int)$userid);}
					$where = dr_array22array($where, $where2);
					$data = $thisdb->select($where,'*','','uploadtime desc,aid desc');
					$total = $thisdb->count($where);
					$files = array();
					if ($data) {
						$index = 0;
						foreach ($data as $t) {
							if ($index >= $start && $index < $end) {
								$files[] = array(
									'id'=> $t['aid'],
									'url'=> dr_get_file_url($t),
									'name'=> $t['filename'],
									'original'=> $t['filename'],
									'mtime'=> $t['uploadtime']
								);
							}
							$index++;
						}
					}

					if (!$total) {
						$result = dr_array2string(array(
							"state" => "no match file",
							"list" => array(),
							"start" => $start,
							"total" => 0
						));
					} else {
						/* 返回数据 */
						$result = dr_array2string(array(
							"state" => "SUCCESS",
							"list" => $files,
							"start" => $start,
							"total" => $total
						));
					}
					break;

				/* 抓取远程文件 */
				case 'catchimage':
					set_time_limit(0);
					/* 上传配置 */
					$config = array(
						'siteid'=>$this->config['siteid'],
						'module'=>$this->config['module'],
						'catid'=>$this->config['catid'],
						'userid'=>$this->config['userid'],
						'isadmin'=>$this->config['isadmin'],
						'groupid'=>$this->config['groupid'],
						'is_wm'=>$this->config['is_wm'],
						'is_esi'=>$this->config['is_esi'],
						'attachment'=>$this->config['attachment'],
						'image_reduce'=>$this->config['image_reduce'],
						"pathFormat" => $this->config['catcherPathFormat'],
						"maxSize" => $this->config['catcherMaxSize'],
						"allowFiles" => $this->config['catcherAllowFiles'],
						"oriName" => "remote.png"
					);
					$fieldName = $this->config['catcherFieldName'];

					/* 抓取远程图片 */
					if (intval($this->config['is_esi'])) {
						$list = array();
						if ($this->input->post($fieldName)) {
							$source = $this->input->post($fieldName);
						} else {
							$source = $this->input->get($fieldName);
						}
						foreach ($source as $imgUrl) {
							$item = new uploader($imgUrl, $config, "remote");
							$info = $item->getFileInfo();
							array_push($list, array(
								"state" => $info["state"],
								"url" => $info["url"],
								"size" => $info["size"],
								"title" => html2code($info["title"]),
								"original" => html2code($info["original"]),
								"source" => html2code($imgUrl)
							));
						}

						/* 返回抓取数据 */
						$result = dr_array2string(array(
							'state'=> count($list) ? 'SUCCESS':'ERROR',
							'list'=> $list
						));
					}
					break;

				/* 删除文件 */
				case 'deletefile':
					$userid = $this->config['userid'];
					$isadmin = $this->config['isadmin'];
					$roleid = $this->config['roleid'];
					if ($userid) {
						if ($isadmin && $roleid && cleck_admin($roleid)) {
							/* 获取路径 */
							$aid = $this->input->post('id');
							if ($aid) {
								/* 删除数据 */
								$data['aid'] = $aid;
								$rt = pc_base::load_sys_class('upload')->_delete_file($data);
								if (!$rt['code']) {
									$result = dr_array2string(array(
										'code'=> '0',
										'state'=> $rt['msg']
									));
								}
								$result = dr_array2string(array(
									'code'=> '1',
									'state'=> '删除成功。'
								));
							} else {
								$result = dr_array2string(array(
									'code'=> '0',
									'state'=> '所选附件不存在。'
								));
							}
						} else {
							$result = dr_array2string(array(
								'code'=> '0',
								'state'=> '需要超级管理员账号操作'
							));
						}
					} else {
						$result = dr_array2string(array(
							'code'=> '0',
							'state'=> '请登录在操作'
						));
					}
					break;

				default:
					$result = dr_array2string(array(
						'state'=> '请求地址出错'
					));
					break;
			}
		} elseif ($action == 'config') {
			$result = dr_array2string($this->config);
		} else {
			if ($action == 'uploadword') {
				$result = dr_array2string(array(
					'status'=> 0,
					'msg'=> $error ? $error : '请登录在操作'
				));
			} else {
				$result = dr_array2string(array(
					'state'=> $error ? $error : '请登录在操作'
				));
			}
		}

		/* 输出结果 */
		if ($this->input->get('callback')) {
			if (preg_match("/^[\w_]+$/", $this->input->get('callback'))) {
				echo html2code($this->input->get('callback')) . '(' . $result . ')';
			} else {
				echo dr_array2string(array(
					'state'=> 'callback参数不合法'
				));
			}
		} else {
			echo $result;
		}
		exit;
	}

	// 验证权限脚本
	private function check_upload_auth() {

		$error = '';
		if (defined('SYS_CSRF') && SYS_CSRF && csrf_hash() != (string)$_GET['token']) {
			$error = L('跨站验证禁止上传文件');
		} elseif ($this->isadmin && !$this->_userid) {
			$error = L('请登录在操作');
		} elseif ($this->isadmin || IS_ADMIN) {
			return;
		} elseif (!$this->isadmin && !$this->grouplist[$this->_groupid]['allowattachment']) {
			$error = L('您的用户组不允许上传文件');
		} elseif (!$this->isadmin && $this->check_upload($this->_userid)) {
			$error = L('用户存储空间已满');
		}

		// 挂钩点 验证格式
		$rt2 = pc_base::load_sys_class('hooks')::trigger_callback('check_upload_auth', $this->_userid, $this->isadmin, $this->_groupid, $error);
		if ($rt2 && isset($rt2['code'])) {
			$error = $rt2['code'] ? '' : $rt2['msg'];
		}

		if ($error) {
			return L($error);
		}

		return;
	}

	// 验证附件上传权限，直接返回1 表示空间不够
	private function check_upload($uid) {
		if ($this->isadmin) {
			return;
		}
		// 获取用户总空间
		$total = abs((int)$this->grouplist[$this->_groupid]['filesize']) * 1024 * 1024;
		if ($total) {
			// 判断空间是否满了
			$filesize = $this->get_member_filesize($uid);
			if ($filesize >= $total) {
				return 1;
			}
		}
		return;
	}

	// 用户已经使用附件空间
	private function get_member_filesize($uid) {
		$db = pc_base::load_model('attachment_model');
		$db->query('SELECT sum(filesize) as filesize FROM `'.$db->dbprefix('attachment').'` where userid='.intval($uid).' and isadmin='.intval($this->isadmin));
		$row = $db->fetch_array();
		return intval($row[0]['filesize']);
	}
}
?>