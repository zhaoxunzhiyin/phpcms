<?php
/**
 *  global.func.php 公共函数库
 *
 * @copyright			(C) 2005-2010
 * @lastmodify			2010-6-1
 */

/**
 * 返回经addslashes处理过的字符串或数组
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function new_addslashes($string){
	if(!is_array($string)) return addslashes($string);
	foreach($string as $key => $val) $string[$key] = new_addslashes($val);
	return $string;
}

/**
 * 返回经stripslashes处理过的字符串或数组
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function new_stripslashes($string) {
	if(!is_array($string)) return stripslashes($string);
	foreach($string as $key => $val) $string[$key] = new_stripslashes($val);
	return $string;
}

/**
 * 返回经htmlspecialchars处理过的字符串或数组
 * @param $obj 需要处理的字符串或数组
 * @return mixed
 */
function new_html_special_chars($string) {
	$encoding = 'utf-8';
	if(strtolower(CHARSET)=='gbk') $encoding = 'ISO-8859-15';
	if(!is_array($string)) return htmlspecialchars($string,ENT_QUOTES,$encoding);
	foreach($string as $key => $val) $string[$key] = new_html_special_chars($val);
	return $string;
}

function new_html_entity_decode($string) {
	$encoding = 'utf-8';
	if(strtolower(CHARSET)=='gbk') $encoding = 'ISO-8859-15';
	return html_entity_decode($string,ENT_QUOTES,$encoding);
}

function new_htmlentities($string) {
	$encoding = 'utf-8';
	if(strtolower(CHARSET)=='gbk') $encoding = 'ISO-8859-15';
	return htmlentities($string,ENT_QUOTES,$encoding);
}

// html实体字符转换
function code2html($value, $fk = false, $flags = null) {
	return html_code($value, $fk, $flags);
}
function html_code($value, $fk = false, $flags = null) {
	!$flags && $flags = ENT_QUOTES | ENT_HTML401 | ENT_HTML5;
	if ($fk) {
		// 将所有HTML实体转换为它们的适用字符
		return html_entity_decode($value, $flags, 'UTF-8');
	}
	// 将特殊的HTML实体转换回字符
	return htmlspecialchars_decode($value, $flags);
}

// 获取内容
function get_content($modelid, $id) {
	$db = pc_base::load_model('content_model');
	$db->set_model($modelid);
	$db->table_name = $db->table_name.'_data';
	$rt = $db->get_one(array('id' => $id), 'content');
	return $rt['content'];
}

// 获取内容中的缩略图
function get_content_img($value, $num = 0) {
	return get_content_url($value, 'src', 'gif|jpg|jpeg|png', $num);
}

// 获取内容中的指定标签URL地址
function get_content_url($value, $attr, $ext, $num = 0) {
	$rt = array();
	$ext = str_replace(',', '|', $ext);
	$value = preg_replace('/\.('.$ext.')@(.*)(\'|")/iU', '.$1$3', $value);
	if (preg_match_all("/(".$attr.")=([\"|']?)([^ \"'>]+\.(".$ext."))\\2/i", $value, $imgs)) {
		$imgs[3] = array_unique($imgs[3]);
		foreach ($imgs[3] as $i => $img) {
			if ($num && $i+1 > $num) {
				break;
			}
			$rt[] = dr_file(trim($img, '"'));
		}
	}
	return $rt;
}

/**
 * 提取描述信息
 */
function dr_get_description($text, $limit = 0) {
	if (!$limit) {
		$limit = 200;
	}
	return trim(str_cut(clearhtml($text), $limit));
}

/**
 * 提取关键字
 */
function dr_get_keywords($kw) {
	if (!$kw) {
		return '';
	}
	$cfg_bdqc_qcnum = pc_base::load_config('system', 'baidu_qcnum') ? pc_base::load_config('system', 'baidu_qcnum') : 10;
	if (pc_base::load_config('system', 'keywordapi')==1) {
		$baiduapi = pc_base::load_sys_class('baiduapi');
		$data = array(
			'title' => $kw,
			'content' => $kw,
		);
		$data = mb_convert_encoding(json_encode($data), 'GBK', 'UTF8');
		$baidu = $baiduapi->get_data('https://aip.baidubce.com/rpc/2.0/nlp/v1/keyword', $data, 1);
		if ($baidu && $baidu['data']['items']) {
			$n = 0;
			$resultstr = '';
			foreach ($baidu['data']['items'] as $t) {
				$resultstr .= ','.$t['tag'];
				$n++;
				if( $n >= $cfg_bdqc_qcnum ) break;
			}
		}
		return trim($resultstr, ',');
	} else if (pc_base::load_config('system', 'keywordapi')==2) {
		$XAppid = pc_base::load_config('system', 'xunfei_aid');
		$Apikey = pc_base::load_config('system', 'xunfei_skey');
		$fix = 0; //如果错误日志提示【time out|ilegal X-CurTime】，需要把$fix变量改为 100 、200、300、等等，按实际情况调试，只要是数字都行
		$XParam = base64_encode(json_encode(array(
			"type"=>"dependent",
		)));
		$XCurTime = SYS_TIME - $fix;
		$XCheckSum = md5($Apikey.$XCurTime.$XParam);
		$headers = array();
		$headers[] = 'X-CurTime:'.$XCurTime;
		$headers[] = 'X-Param:'.$XParam;
		$headers[] = 'X-Appid:'.$XAppid;
		$headers[] = 'X-CheckSum:'.$XCheckSum;
		$headers[] = 'Content-Type:application/x-www-form-urlencoded; charset=utf-8';
		$rt = json_decode(file_get_contents("http://ltpapi.xfyun.cn/v1/ke", false, stream_context_create(array(
			'http' => array(
				'method' => 'POST',
				'header' => $headers,
				'content' => http_build_query(array(
					'text' => $kw,
				)),
				'timeout' => 15*60
			)
		))), true);
		if (!$rt) {
			//showmessage('讯飞接口访问失败');
			return '';
		} elseif ($rt['code']) {
			//showmessage('讯飞接口: '.$rt['desc']);
			return '';
		} else {
			$n = 0;
			$resultstr = '';
			foreach ($rt['data']['ke'] as $t) {
				$resultstr .= ','.$t['word'];
				$n++;
				if( $n >= $cfg_bdqc_qcnum ) break;
			}
			return trim($resultstr, ',');
		}
	} else {
		$phpanalysis = pc_base::load_sys_class('phpanalysis');
		$phpanalysis = new phpanalysis('utf-8', 'utf-8', false);
		$phpanalysis->LoadDict();
		$phpanalysis->SetSource($kw);
		$phpanalysis->StartAnalysis(true);
		return $phpanalysis->GetFinallyKeywords($cfg_bdqc_qcnum);
	}
	return '';
}

/**
 * 判断存在于数组中
 */
function dr_in_array($var, $array) {
	if (!$array || !is_array($array)) {
		return 0;
	}
	return in_array($var, $array);
}

/**
 * 字符长度
 */
function dr_strlen($string) {
	if (is_array($string)) {
		return dr_count($string);
	}
	return strlen($string);
}

// 兼容统计
function dr_count($array_or_countable, $mode = COUNT_NORMAL){
	return is_array($array_or_countable) || is_object($array_or_countable) ? count($array_or_countable, $mode) : 0;
}

/**
 * 完整的文件路径
 *
 * @param   string  $url
 * @return  string
 */
function dr_file($url) {
	if (!$url || dr_strlen($url) == 1) {
		return NULL;
	} elseif (substr($url, 0, 7) == 'http://' || substr($url, 0, 8) == 'https://') {
		return $url;
	} elseif (substr($url, 0, 1) == '/') {
		return APP_PATH.substr($url, 1);
	}
	return SYS_UPLOAD_URL.$url;
}

/**
 * 文件真实地址
 *
 * @param   string  $id
 * @return  array
 */
function dr_get_file($id) {
	if (!$id) {
		return IS_DEV ? '文件参数没有值' : '';
	}
	if ($id) {
		// 表示附件id
		$info = get_attachment($id);
		if ($info['url']) {
			return $info['url'];
		}
	}
	$file = dr_file($id);
	return $file ? $file : $id;
}

/**
 * 根据附件信息获取文件地址
 *
 * @param   array   $data
 * @return  string
 */
function dr_get_file_url($data, $w = 0, $h = 0) {
	if (!$data) {
		return IS_DEV ? '文件信息不存在' : '';
	} elseif ($data['remote']) {
		$att_commons = getcache('attachment', 'commons');
		$remote = $att_commons[$data['remote']];
		if ($remote) {
			return $remote['url'].$data['attachment'];
		} else {
			return IS_DEV ? '自定义附件（'.$data['remote'].'）的配置已经不存在' : '';
		}
	} elseif ($w && $h && in_array($data['fileext'], array('jpg', 'gif', 'png', 'jpeg'))) {
		//return thumb($data['aid'], $w, $h, 0, 'crop');
		return dr_get_file($data['aid']);
	}
	return SYS_UPLOAD_URL.$data['filepath'];
}

// 获取自定义目录
function dr_get_dir_path($path) {
	if ((strpos($path, '/') === 0 || strpos($path, ':') !== false)) {
		// 相对于根目录
		return rtrim($path, DIRECTORY_SEPARATOR).'/';
	} else {
		// 在当前网站目录
		return CMS_PATH.trim($path, '/').'/';
	}
}

/**
 * 上传移动文件
 */
function dr_move_uploaded_file($tempfile, $fullname) {
	$contentType = $_SERVER['CONTENT_TYPE'] ? getenv('CONTENT_TYPE') : 0;
	if (strpos($contentType, 'multipart') !== false && strpos($_SERVER['HTTP_CONTENT_RANGE'], 'bytes') === 0) {

		// 命名一个新名称
		$value = str_replace('bytes ', '', $_SERVER['HTTP_CONTENT_RANGE']);
		list($str, $total) = explode('/', $value);
		list($str, $max) = explode('-', $str);

		// 分段名称
		$temp_file = dirname($fullname).'/'.md5($_SERVER['HTTP_CONTENT_DISPOSITION']);
		if ($total - $max < 1024) {
			// 减去误差表示分段上传完毕
			/*
			if (!move_uploaded_file($tempfile, $temp_file)) {
				// 移动失败
				unlink($temp_file);
				return false;
			}*/
			if (!file_put_contents($temp_file, file_get_contents($tempfile), FILE_APPEND)) {
				unlink($temp_file);
				return false;
			}
			// 移动最终的文件
			if (!rename($temp_file, $fullname)) {
				unlink($temp_file);
				return false;
			}
			unlink($temp_file);
			return true;
		} else {
			// 正在分段上传
			/*
			if (!move_uploaded_file($data, $temp_file)) {
				// 移动失败
				unlink($temp_file);
				return false;
			}*/
			return file_put_contents($temp_file, file_get_contents($tempfile), FILE_APPEND);
		}
	} else {
		return move_uploaded_file($tempfile, $fullname);
	}
}

/**
 * 安全过滤函数
 *
 * @param $string
 * @return string
 */
function safe_replace($string) {
	$string = str_replace('%20','',$string);
	$string = str_replace('%27','',$string);
	$string = str_replace('%2527','',$string);
	$string = str_replace('*','',$string);
	$string = str_replace('"','&quot;',$string);
	$string = str_replace("'",'',$string);
	$string = str_replace('"','',$string);
	$string = str_replace(';','',$string);
	$string = str_replace('<','&lt;',$string);
	$string = str_replace('>','&gt;',$string);
	$string = str_replace("{",'',$string);
	$string = str_replace('}','',$string);
	$string = str_replace('\\','',$string);
	return $string;
}
/**
 * 安全过滤函数
 */
function dr_safe_replace($string, $diy = null) {
	$replace = array('%20', '%27', '%2527', '*', "'", '"', ';', '<', '>', "{", '}');
	$diy && is_array($diy) && $replace = dr_array2array($replace, $diy);
	$diy && !is_array($diy) && $replace[] = $diy;
	return str_replace($replace, '', (string)$string);
}
/**
 * 安全过滤文件及目录名称函数
 */
function dr_safe_filename($string) {
	return str_replace(
		array('..', "/", '\\', ' ', '<', '>', "{", '}', ';', ':', '[', ']', '\'', '"', '*', '?'),
		'',
		(string)$string
	);
}
/**
 * 安全过滤用户名函数
 */
function dr_safe_username($string) {
	return str_replace(
		array('..', "/", '\\', ' ', "#",'\'', '"'),
		'',
		(string)$string
	);
}
/**
 * 安全过滤密码函数
 */
function dr_safe_password($string) {
    return trim($string);
}
/**
 * 将路径进行安全转换变量模式
 */
function dr_safe_replace_path($path) {
	return str_replace(
		array(
			CACHE_PATH,
			PC_PATH,
			CMS_PATH,
		),
		array(
			'CACHE_PATH/',
			'PC_PATH/',
			'CMS_PATH/',
		),
		$path
	);
}
/**
 * 两数组追加合并
 */
function dr_array2array($a1, $a2) {
	$a = array();
	$a = $a1 ? $a1 : $a;
	if ($a2) {
		foreach ($a2 as $t) {
			$a[] = $t;
		}
	}
	return $a;
}

/**
 * 两数组覆盖合并，1是老数据，2是新数据
 */
function dr_array22array($a1, $a2) {
	$a = array();
	$a = $a1 ? $a1 : $a;
	if ($a2) {
		foreach ($a2 as $i => $t) {
			$a[$i] = $t;
		}
	}
	return $a;
}
/**
 * 静态生成时权限认证字符(加密)
 */
function dr_html_auth($is = 0) {
	$cache = pc_base::load_sys_class('cache');
	$name = md5(ip());
	if ($is) {
		// 存储值
		return $cache->set_auth_data($name, 1);
	} else {
		// 读取判断
		$rt = $cache->get_auth_data($name);
		if ($rt) {
			return 1; // 有效
		} else {
			return 0;
		}
	}
}
/**
 * 删除目录及目录下面的所有文件
 *
 * @param   string  $dir        路径
 * @param   string  $is_all     包括删除当前目录
 * @return  bool    如果成功则返回 TRUE，失败则返回 FALSE
 */
function dr_dir_delete($path, $del_dir = FALSE, $htdocs = FALSE, $_level = 0) {
	// Trim the trailing slash
	$path = rtrim($path, '/\\');
	if ( ! $current_dir = @opendir($path)) {
		return FALSE;
	}
	while (FALSE !== ($filename = @readdir($current_dir))) {
		if ($filename !== '.' && $filename !== '..') {
			$filepath = $path.DIRECTORY_SEPARATOR.$filename;
			if (is_dir($filepath) && $filename[0] !== '.' && ! is_link($filepath)) {
				dr_dir_delete($filepath, $del_dir, $htdocs, $_level + 1);
			} else {
				unlink($filepath);
			}
		}
	}
	closedir($current_dir);
	$_level > 0  && rmdir($path); // 删除子目录
	return $del_dir && $_level == 0 ? rmdir($path) : TRUE;
}
/**
 * xss过滤函数
 *
 * @param $string
 * @return string
 */
function remove_xss($string) { 
    $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $string);

    $parm1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');

    $parm2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');

    $parm = array_merge($parm1, $parm2); 

	for ($i = 0; $i < sizeof($parm); $i++) { 
		$pattern = '/'; 
		for ($j = 0; $j < strlen($parm[$i]); $j++) { 
			if ($j > 0) { 
				$pattern .= '('; 
				$pattern .= '(&#[x|X]0([9][a][b]);?)?'; 
				$pattern .= '|(&#0([9][10][13]);?)?'; 
				$pattern .= ')?'; 
			}
			$pattern .= $parm[$i][$j]; 
		}
		$pattern .= '/i';
		$string = preg_replace($pattern, ' ', $string); 
	}
	return $string;
}

/**
 * 过滤ASCII码从0-28的控制字符
 * @return String
 */
function trim_unsafe_control_chars($str) {
	$rule = '/[' . chr ( 1 ) . '-' . chr ( 8 ) . chr ( 11 ) . '-' . chr ( 12 ) . chr ( 14 ) . '-' . chr ( 31 ) . ']*/';
	return str_replace ( chr ( 0 ), '', preg_replace ( $rule, '', $str ) );
}

/**
 * 格式化文本域内容
 *
 * @param $string 文本域内容
 * @return string
 */
function trim_textarea($string) {
	$string = nl2br ( str_replace ( ' ', '&nbsp;', $string ) );
	return $string;
}

/**
 * 将文本格式成适合js输出的字符串
 * @param string $string 需要处理的字符串
 * @param intval $isjs 是否执行字符串格式化，默认为执行
 * @return string 处理后的字符串
 */
function format_js($string, $isjs = 1) {
	$string = addslashes(str_replace(array("\r", "\n", "\t"), array('', '', ''), $string));
	return $isjs ? 'document.write("'.$string.'");' : $string;
}

/**
 * 转义 javascript 代码标记
 *
 * @param $str
 * @return mixed
 */
 function trim_script($str) {
	if(is_array($str)){
		foreach ($str as $key => $val){
			$str[$key] = trim_script($val);
		}
 	}else{
 		$str = preg_replace ( '/\<([\/]?)script([^\>]*?)\>/si', '&lt;\\1script\\2&gt;', $str );
		$str = preg_replace ( '/\<([\/]?)iframe([^\>]*?)\>/si', '&lt;\\1iframe\\2&gt;', $str );
		$str = preg_replace ( '/\<([\/]?)frame([^\>]*?)\>/si', '&lt;\\1frame\\2&gt;', $str );
		$str = str_replace ( 'javascript:', 'javascript：', $str );
 	}
	return $str;
}
/**
 * 清除HTML标记
 *
 * @param	string	$str
 * @return  string
 */
function clearhtml($str) {
	$str = str_replace(
		array('&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array(' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $str
	);
	$str = preg_replace("/\<[a-z]+(.*)\>/iU", "", $str);
	$str = preg_replace("/\<\/[a-z]+\>/iU", "", $str);
	$str = str_replace(array(PHP_EOL, chr(13), chr(10), '&nbsp;'), '', $str);
	$str = strip_tags($str);

	return trim($str);
}
/**
 * 跳转地址
 */
function redirect($url = '', $method = 'auto', $code = NULL) {
	switch ($method) {
		case 'refresh':
			header('Refresh:0;url='.$url);
			break;
		default:
			header('Location: '.$url, TRUE, $code);
			break;
	}
	exit;
}
/**
 * 获取当前页面完整URL地址
 */
function get_url() {
	$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' || isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 1 && $_SERVER['HTTPS'] == 'on' || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' || isset($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) == 'on' ? 'https://' : 'http://';
	$php_self = $_SERVER['PHP_SELF'] ? safe_replace($_SERVER['PHP_SELF']) : safe_replace($_SERVER['SCRIPT_NAME']);
	$path_info = isset($_SERVER['PATH_INFO']) ? safe_replace($_SERVER['PATH_INFO']) : '';
	$relate_url = isset($_SERVER['REQUEST_URI']) ? safe_replace($_SERVER['REQUEST_URI']) : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.safe_replace($_SERVER['QUERY_STRING']) : $path_info);
	return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
}
/**
 * 当前URL
 */
function now_url($url, $siteid = 1, $ismobile = 1, $ishtml = 1) {
	$siteinfo = getcache('sitelist', 'commons');
	if ($ishtml) {
		if ($ismobile) {
			return $url ? $url : $siteinfo[$siteid]['mobile_domain'];
		} else {
			return $url ? $url : $siteinfo[$siteid]['domain'];
		}
	} else {
		return FC_NOW_URL;
	}
}
/**
 * 基于本地存储的加解密算法
 */
function dr_authcode($string, $operation = 'DECODE') {
	$cache = pc_base::load_sys_class('cache');
	if (!$string) {
		return '';
	}
	is_array($string) && $string = dr_array2string($string);
	if ($operation == 'DECODE') {
		// 解密
		return $cache->get_auth_data($string);
	} else {
		// 加密
		$cache->set_auth_data(md5($string), $string);
		return md5($string);
	}
}
/**
 * 字符截取
 *
 * @param	string	$str
 * @param	intval	$length
 * @param	string	$dot
 * @return	string
 */
function str_cut($string, $length, $dot = '...') {
	if (!$string || strlen($string) <= $length || !$length) {
		return $string;
	}
	if (function_exists('mb_substr')) {
		$strcut = mb_substr($string, 0, $length);
	} else {
		$n = $tn = $noc = 0;
		$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);
		while ($n < strlen($string)) {
			$t = ord($string[$n]);
			if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1;
				$n++;
				$noc++;
			} elseif (194 <= $t && $t <= 223) {
				$tn = 2;
				$n += 2;
				$noc += 2;
			} elseif (224 <= $t && $t <= 239) {
				$tn = 3;
				$n += 3;
				$noc += 2;
			} elseif (240 <= $t && $t <= 247) {
				$tn = 4;
				$n += 4;
				$noc += 2;
			} elseif (248 <= $t && $t <= 251) {
				$tn = 5;
				$n += 5;
				$noc += 2;
			} elseif ($t == 252 || $t == 253) {
				$tn = 6;
				$n += 6;
				$noc += 2;
			} else {
				$n++;
			}
			if ($noc >= $length) {
				break;
			}
		}
		if ($noc > $length) {
			$n -= $tn;
		}
		$strcut = substr($string, 0, $n);
		$strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);
	}
	$strcut == $string && $dot = '';
	return $strcut . $dot;
}

// 获取访客ip地址
function ip() {
	if (getenv('HTTP_CLIENT_IP')) {
		$client_ip = getenv('HTTP_CLIENT_IP');
	} elseif(getenv('HTTP_X_FORWARDED_FOR')) {
		$client_ip = getenv('HTTP_X_FORWARDED_FOR');
	} elseif(getenv('REMOTE_ADDR')) {
		$client_ip = getenv('REMOTE_ADDR');
	} else {
		$client_ip = $_SERVER['REMOTE_ADDR'];
	}
	// 验证规范
	if (!preg_match('/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/', $client_ip)) {
		$client_ip = '';
	}
	$client_ip = str_replace(array(",", '(', ')', ',', chr(13), PHP_EOL), '', $client_ip);
	$client_ip = trim($client_ip);
	return $client_ip;
}

// ip转为实际地址
function ip2address($ip) {
	$ip_area = pc_base::load_sys_class('ip_area');
	return $ip_area->address($ip);
}

// 当前ip实际地址
function ip_address_info() {
	$ip_area = pc_base::load_sys_class('ip_area');
	return $ip_area->address(ip());
}

function get_cost_time() {
	$microtime = microtime ( TRUE );
	return $microtime - SYS_START_TIME;
}
/**
 * 程序执行时间
 *
 * @return	int	单位ms
 */
function execute_time() {
	$stime = explode ( ' ', SYS_START_TIME );
	$etime = explode ( ' ', microtime () );
	return number_format ( ($etime [1] + $etime [0] - $stime [1] - $stime [0]), 6 );
}

/**
* 产生随机字符串
*
* @param    int        $length  输出长度
* @param    string     $chars   可选的 ，默认为 0123456789
* @return   string     字符串
*/
function random($length, $chars = '0123456789') {
	$hash = '';
	$max = strlen($chars) - 1;
	mt_srand();
	for($i = 0; $i < $length; $i++) {
		$hash .= $chars[mt_rand(0, $max)];
	}
	return substr(md5($hash), 0, $length);
}

/**
* 将字符串转换为数组
*
* @param	string	$data	字符串
* @return	array	返回数组格式，如果，data为空，则返回空数组
*/
function string2array($data) {
	$data = trim($data);
	if($data == '') return array();
	if(strpos($data, 'array')===0){
		@eval("\$array = $data;");
	}else{
		if(strpos($data, '{\\')===0) $data = stripslashes($data);
		$array=json_decode($data,true);
		if(strtolower(CHARSET)=='gbk'){
			$array = mult_iconv("UTF-8", "GBK//IGNORE", $array);
		}
	}
	return $array;
}
/**
* 将数组转换为字符串
*
* @param	array	$data		数组
* @param	bool	$isformdata	如果为0，则不使用new_stripslashes处理，可选参数，默认为1
* @return	string	返回字符串，如果，data为空，则返回空
*/
function array2string($data, $isformdata = 1) {
	if($data == '' || empty($data)) return '';
	
	if($isformdata) $data = new_stripslashes($data);
	if(strtolower(CHARSET)=='gbk'){
		$data = mult_iconv("GBK", "UTF-8", $data);
	}
	return addslashes(json_encode($data,JSON_FORCE_OBJECT));
}
/**
 * 根据文件扩展名获取文件预览信息
 */
function dr_file_preview_html($value, $target = 0) {
	$ext = trim(strtolower(strrchr($value, '.')), '.');
	if (in_array($ext, array('jpg', 'gif', 'png', 'jpeg'))) {
		$value = dr_file($value);
		$url = $target ? $value.'" target="_blank' : 'javascript:dr_preview_image(\''.$value.'\');';
		return '<a href="'.$url.'"><img src="'.$value.'"></a>';
	} elseif ($ext == 'mp4') {
		$value = dr_file($value);
		$url = $target ? $value.'" target="_blank' : 'javascript:dr_preview_video(\''.$value.'\');';
		return '<a href="'.$url.'"><img src="'.IMG_PATH.'ext/mp4.png'.'"></a>';
	} elseif (is_file(CMS_PATH.'statics/images/ext/'.$ext.'.png')) {
		$file = IMG_PATH.'ext/'.$ext.'.png';
		$url = $target ? $value.'" target="_blank' : 'javascript:dr_preview_url(\''.dr_file($value).'\');';
		return '<a href="'.$url.'"><img src="'.$file.'"></a>';
	} else {
		$file = IMG_PATH.'ext/url.png';
		$url = $target ? $value.'" target="_blank' : 'javascript:dr_preview_url(\''.$value.'\');';
		return '<a href="'.$url.'"><img src="'.$file.'"></a>';
	}
}
/**
* 统一返回json格式并退出程序
*/
function dr_json($code, $msg, $data = array()){
	echo dr_array2string(dr_return_data($code, $msg, $data));exit;
}
/**
 * 将数组转换为字符串
 *
 * @param	array	$data	数组
 * @return	string
 */
function dr_array2string($data) {
	return $data ? json_encode($data, JSON_UNESCAPED_UNICODE) : '';
}
/**
 * 将字符串转换为数组
 *
 * @param   string  $data   字符串
 * @return  array
 */
function dr_string2array($data) {
	if (is_array($data)) {
		return $data;
	} elseif (!$data) {
		return array();
	}
	$rt = json_decode($data, true);
	if ($rt) {
		return $rt;
	}
	return unserialize(stripslashes($data));
}
/**
 * 附件信息
 */
function get_attachment($id) {
	$att_db = pc_base::load_model('attachment_model');
	if (!$id) {
		return null;
	}
	if (is_numeric($id)) {
		$id = (int)$id;
		$data = $att_db->get_one(array('aid'=>$id));
	} else {
		$data = $att_db->get_one(array('filepath'=>str_replace(SYS_UPLOAD_URL, '', $id)));
	}
	if (!$data) {
		$data = array(
			'aid' => $id,
			'url' => dr_get_file($id),
			'file' => '',
			'remote' => 'webimg',
			'fileext' => trim(strtolower(strrchr($id, '.')), '.'),
		);
		$data['filepath'] = date('Y/md/', SYS_TIME).md5($attach['aid']).'.'.$attach['fileext'];
		return $data;
	}

	$data['file'] = SYS_UPLOAD_PATH.$data['filepath'];

	// 文件真实地址
	if ($data['remote']) {
		$att_commons = getcache('attachment', 'commons');
		$remote = $att_commons[$data['remote']];
		if (!$remote) {
			// 远程地址无效
			$data['url'] = $data['file'] = '自定义附件（'.$data['remote'].'）的配置已经不存在';
			return $data;
		} else {
			$data['file'] = $remote['value']['path'].$data['filepath'];
		}
	}

	// 附件属性信息
	$data['attachinfo'] = dr_string2array($data['attachinfo']);

	$data['url'] = dr_get_file_url($data);

	return $data;
}
/**
* 数组转码
*
*/
function mult_iconv($in_charset,$out_charset,$data){
    if(substr($out_charset,-8)=='//IGNORE'){
        $out_charset=substr($out_charset,0,-8);
    }
    if(is_array($data)){
        foreach($data as $key => $value){
            if(is_array($value)){
                $key=iconv($in_charset,$out_charset.'//IGNORE',$key);
                $rtn[$key]=mult_iconv($in_charset,$out_charset,$value);
            }elseif(is_string($key) || is_string($value)){
                if(is_string($key)){
                    $key=iconv($in_charset,$out_charset.'//IGNORE',$key);
                }
                if(is_string($value)){
                    $value=iconv($in_charset,$out_charset.'//IGNORE',$value);
                }
                $rtn[$key]=$value;
            }else{
                $rtn[$key]=$value;
            }
        }
    }elseif(is_string($data)){
        $rtn=iconv($in_charset,$out_charset.'//IGNORE',$data);
    }else{
        $rtn=$data;
    }
    return $rtn;
}
/**
 * 格式化输出文件大小
 *
 * @param   int $fileSize   大小
 * @param   int $round      保留小数位
 * @return  string
 */
function format_file_size($fileSize, $round = 2) {
	if (!$fileSize) {
		return 0;
	}
	$i = 0;
	$inv = 1 / 1024;
	$unit = array(' Bytes', ' KB', ' MB', ' GB', ' TB', ' PB', ' EB', ' ZB', ' YB');
	while ($fileSize >= 1024 && $i < 8) {
		$fileSize *= $inv;
		++$i;
	}
	$temp = sprintf("%.2f", $fileSize);
	$value = $temp - (int) $temp ? $temp : $fileSize;
	return round($value, $round) . $unit[$i];
}
/**
* 转换字节数为其他单位
*
*
* @param	string	$filesize	字节大小
* @return	string	返回大小
*/
function sizecount($filesize) {
	return format_file_size($filesize);
}
/**
* 字符串加密、解密函数
*
*
* @param	string	$txt		字符串
* @param	string	$operation	ENCODE为加密，DECODE为解密，可选参数，默认为ENCODE，
* @param	string	$key		密钥：数字、字母、下划线
* @param	string	$expiry		过期时间
* @return	string
*/
function sys_auth($string, $operation = 'ENCODE', $key = '', $expiry = 0) {
	$ckey_length = 4;
	$key = md5($key != '' ? $key : pc_base::load_config('system', 'auth_key'));
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(strtr(substr($string, $ckey_length), '-_', '+/')) : sprintf('%010d', $expiry ? $expiry + SYS_TIME : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.rtrim(strtr(base64_encode($result), '+/', '-_'), '=');
	}
}
/**
* 语言文件处理
*
* @param	string		$language	标示符
* @param	array		$pars	转义的数组,二维数组 ,'key1'=>'value1','key2'=>'value2',
* @param	string		$modules 多个模块之间用半角逗号隔开，如：member,guestbook
* @return	string		语言字符
*/
function L($language = 'no_language',$pars = array(), $modules = '') {
	if(!defined('ROUTE_M')) {
		return $language;
	}
	static $LANG = array();
	static $LANG_MODULES = array();
	static $lang = '';
	if(defined('IN_ADMIN')) {
		$lang = SYS_STYLE ? SYS_STYLE : 'zh-cn';
	} else {
		$lang = pc_base::load_config('system','lang');
	}
	if(!$LANG) {
		require_once PC_PATH.'languages'.DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.'system.lang.php';
		if(defined('IN_ADMIN')) require_once PC_PATH.'languages'.DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.'system_menu.lang.php';
		if(file_exists(PC_PATH.'languages'.DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.ROUTE_M.'.lang.php')) require_once PC_PATH.'languages'.DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.ROUTE_M.'.lang.php';
	}
	if(!empty($modules)) {
		$modules = explode(',',$modules);
		foreach($modules AS $m) {
			if(!isset($LANG_MODULES[$m])) require_once PC_PATH.'languages'.DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.$m.'.lang.php';
		}
	}
	if(!array_key_exists($language,$LANG)) {
		return $language;
	} else {
		$language = $LANG[$language];
		if($pars) {
			foreach($pars AS $_k=>$_v) {
				$language = str_replace('{'.$_k.'}',$_v,$language);
			}
		}
		return $language;
	}
}

/**
 * 模板调用
 *
 * @param $module
 * @param $template
 * @param $istag
 * @return unknown_type
 */
function template($module = 'content', $template = 'index', $style = '') {
	!defined('ISMOBILE') && define('ISMOBILE', 0);
	!defined('IS_HTML') && define('IS_HTML', 0);
	if(strpos($module, 'plugin/')!== false) {
		$plugin = str_replace('plugin/', '', $module);
		return p_template($plugin, $template,$style);
	}
	$module = str_replace('/', DIRECTORY_SEPARATOR, $module);
	if(!empty($style) && preg_match('/([a-z0-9\-_]+)/is',$style)) {
	} elseif (empty($style) && !defined('STYLE')) {
		if(defined('SITEID')) {
			$siteid = SITEID;
		} else {
			$siteid = param::get_cookie('siteid');
		}
		if (!$siteid) $siteid = 1;
		$sitelist = getcache('sitelist', 'commons');
		if(!empty($siteid)) {
			$style = $sitelist[$siteid]['default_style'];
		}
	} elseif (empty($style) && defined('STYLE')) {
		$style = STYLE;
	} else {
		$style = 'default';
	}
	if(!$style) $style = 'default';
	$template_cache = pc_base::load_sys_class('template_cache');
	$compiledtplfile = CMS_PATH.'caches'.DIRECTORY_SEPARATOR.'caches_template'.DIRECTORY_SEPARATOR.$style.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$template.'.php';
	if(file_exists(PC_PATH.'templates'.DIRECTORY_SEPARATOR.$style.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$template.'.html')) {
		if(!file_exists($compiledtplfile) || (@filemtime(PC_PATH.'templates'.DIRECTORY_SEPARATOR.$style.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$template.'.html') > @filemtime($compiledtplfile))) {
			$template_cache->template_compile($module, $template, $style);
		}
	} else {
		$compiledtplfile = CMS_PATH.'caches'.DIRECTORY_SEPARATOR.'caches_template'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$template.'.php';
		if(!file_exists($compiledtplfile) || (file_exists(PC_PATH.'templates'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$template.'.html') && filemtime(PC_PATH.'templates'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$template.'.html') > filemtime($compiledtplfile))) {
			$template_cache->template_compile($module, $template, 'default');
		} elseif (!file_exists(PC_PATH.'templates'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$template.'.html')) {
			showmessage('Template does not exist.'.DIRECTORY_SEPARATOR.$style.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$template.'.html');
		}
	}
	return $compiledtplfile;
}

/**
 * 输出自定义错误
 *
 * @param $errno 错误号
 * @param $errstr 错误描述
 * @param $errfile 报错文件地址
 * @param $errline 错误行号
 */

function my_error_handler($errno, $errstr, $errfile, $errline) {
	if($errno==8) return '';
	$errfile = str_replace(CMS_PATH,'',$errfile);
	if(pc_base::load_config('system','errorlog')) {
		error_log('<?php exit;?>'.date('Y-m-d H:i:s',SYS_TIME).' | '.$errno.' | '.str_pad($errstr,30).' | '.$errfile.' | '.$errline."\r\n", 3, CACHE_PATH.'error_log.php');
	} else {
		$str = '<div style="font-size:12px;text-align:left; border-bottom:1px solid #9cc9e0; border-right:1px solid #9cc9e0;padding:1px 4px;color:#000000;font-family:Arial, Helvetica,sans-serif;"><span>errorno:' . $errno . ',str:' . $errstr . ',file:<font color="blue">' . $errfile . '</font>,line' . $errline .'<br /><a href="http://faq.cms.cn/?type=file&errno='.$errno.'&errstr='.urlencode($errstr).'&errfile='.urlencode($errfile).'&errline='.$errline.'" target="_blank" style="color:red">Need Help?</a></span></div>';
		echo $str;
	}
}

/*
 * 重新日志记录函数
 */
function log_message($level, $message) {
	$path = CACHE_PATH.'caches_error/caches_data/';
	create_folder($path);
	$filepath = $path.'log-'.date('Y-m-d',SYS_TIME).'.php';
	$msg = '';
	if (! is_file($filepath)) {
		$newfile = true;
		$msg .= "<?php defined('IN_CMS') || exit('No direct script access allowed'); ?>\n\n";
	}
	if (! $fp = @fopen($filepath, 'ab')) {
		return false;
	}
	$date = date('Y-m-d H:i:s',SYS_TIME);
	$msg .= strtoupper($level) . ' - ' . $date . ' --> ' . $message . "\n";
	flock($fp, LOCK_EX);
	$result = null;
	for ($written = 0, $length = strlen($msg); $written < $length; $written += $result) {
		if (($result = fwrite($fp, substr($msg, $written))) === false) {
			break;
		}
	}
	flock($fp, LOCK_UN);
	fclose($fp);
	if (isset($newfile) && $newfile === true) {
		chmod($filepath, 0644);
	}
	return is_int($result);
}

/**
 * 提示信息页面跳转，跳转地址如果传入数组，页面会提示多个地址供用户选择，默认跳转地址为数组的第一个值，时间为5秒。
 * showmessage('登录成功', array('默认跳转地址'=>'http://www.kaixin100.cn'));
 * @param string $msg 提示信息
 * @param mixed(string/array) $url_forward 跳转地址
 * @param int $ms 跳转等待时间
 */
function showmessage($msg, $url_forward = 'goback', $ms = 1250, $dialog = '', $returnjs = '') {
	if(defined('IN_ADMIN')) {
		include(admin::admin_tpl('showmessage', 'admin'));
	} else {
		include(template('content', 'message'));
	}
	exit;
}
/**
 * 查询字符是否存在于某字符串
 *
 * @param $haystack 字符串
 * @param $needle 要查找的字符
 * @return bool
 */
function str_exists($haystack, $needle)
{
	return !(strpos($haystack, $needle) === FALSE);
}

/**
 * 取得文件扩展
 *
 * @param $filename 文件名
 * @return 扩展名
 */
function fileext($filename) {
	return strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
}

/**
 * 加载模板标签缓存
 * @param string $name 缓存名
 * @param integer $times 缓存时间
 */
function tpl_cache($name,$times = 0) {
	$filepath = 'tpl_data';
	$info = getcacheinfo($name, $filepath);
	if (SYS_TIME - $info['filemtime'] >= $times) {
		return false;
	} else {
		return getcache($name,$filepath);
	}
}

/**
 * 写入缓存，默认为文件缓存，不加载缓存配置。
 * @param $name 缓存名称
 * @param $data 缓存数据
 * @param $filepath 数据路径（模块名称） caches/cache_$filepath/
 * @param $type 缓存类型[file,memcache,apc]
 * @param $config 配置名称
 * @param $timeout 过期时间
 */
function setcache($name, $data, $filepath='', $type='file', $config='', $timeout=0) {
	if(!preg_match("/^[a-zA-Z0-9_-]+$/", $name)) return false;
	if($filepath!="" && !preg_match("/^[a-zA-Z0-9_-]+$/", $filepath)) return false;
	pc_base::load_sys_class('cache_factory','',0);
	if($config) {
		$cacheconfig = pc_base::load_config('cache');
		$cache = cache_factory::get_instance($cacheconfig)->get_cache($config);
	} else {
		$cache = cache_factory::get_instance()->get_cache($type);
	}

	return $cache->set($name, $data, $timeout, '', $filepath);
}

/**
 * 读取缓存，默认为文件缓存，不加载缓存配置。
 * @param string $name 缓存名称
 * @param $filepath 数据路径（模块名称） caches/cache_$filepath/
 * @param string $config 配置名称
 */
function getcache($name, $filepath='', $type='file', $config='') {
	if(!preg_match("/^[a-zA-Z0-9_-]+$/", $name)) return false;
	if($filepath!="" && !preg_match("/^[a-zA-Z0-9_-]+$/", $filepath)) return false;
	pc_base::load_sys_class('cache_factory','',0);
	if($config) {
		$cacheconfig = pc_base::load_config('cache');
		$cache = cache_factory::get_instance($cacheconfig)->get_cache($config);
	} else {
		$cache = cache_factory::get_instance()->get_cache($type);
	}
	return $cache->get($name, '', '', $filepath);
}

/**
 * 删除缓存，默认为文件缓存，不加载缓存配置。
 * @param $name 缓存名称
 * @param $filepath 数据路径（模块名称） caches/cache_$filepath/
 * @param $type 缓存类型[file,memcache,apc]
 * @param $config 配置名称
 */
function delcache($name, $filepath='', $type='file', $config='') {
	if(!preg_match("/^[a-zA-Z0-9_-]+$/", $name)) return false;
	if($filepath!="" && !preg_match("/^[a-zA-Z0-9_-]+$/", $filepath)) return false;
	pc_base::load_sys_class('cache_factory','',0);
	if($config) {
		$cacheconfig = pc_base::load_config('cache');
		$cache = cache_factory::get_instance($cacheconfig)->get_cache($config);
	} else {
		$cache = cache_factory::get_instance()->get_cache($type);
	}
	return $cache->delete($name, '', '', $filepath);
}

/**
 * 读取缓存，默认为文件缓存，不加载缓存配置。
 * @param string $name 缓存名称
 * @param $filepath 数据路径（模块名称） caches/cache_$filepath/
 * @param string $config 配置名称
 */
function getcacheinfo($name, $filepath='', $type='file', $config='') {
	if(!preg_match("/^[a-zA-Z0-9_-]+$/", $name)) return false;
	if($filepath!="" && !preg_match("/^[a-zA-Z0-9_-]+$/", $filepath)) return false;
	pc_base::load_sys_class('cache_factory');
	if($config) {
		$cacheconfig = pc_base::load_config('cache');
		$cache = cache_factory::get_instance($cacheconfig)->get_cache($config);
	} else {
		$cache = cache_factory::get_instance()->get_cache($type);
	}
	return $cache->cacheinfo($name, '', '', $filepath);
}

/**
 * 目录扫描
 *
 * @param   string  $source_dir     Path to source
 * @param   int $directory_depth    Depth of directories to traverse
 *                      (0 = fully recursive, 1 = current dir, etc)
 * @param   bool    $hidden         Whether to show hidden files
 * @return  array
 */
function dr_dir_map($source_dir, $directory_depth = 0, $hidden = FALSE) {

	if ($fp = opendir($source_dir)) {

		$filedata = array();
		$new_depth = $directory_depth - 1;
		$source_dir = rtrim($source_dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

		while (FALSE !== ($file = readdir($fp))) {
			if ($file === '.' OR $file === '..'
				OR ($hidden === FALSE && $file[0] === '.')
				OR !is_dir($source_dir.$file)) {
				continue;
			}
			if (($directory_depth < 1 OR $new_depth > 0)
				&& is_dir($source_dir.$file)) {
				$filedata[$file] = dr_dir_map($source_dir.DIRECTORY_SEPARATOR.$file, $new_depth, $hidden);
			} else {
				$filedata[] = $file;
			}
		}
		closedir($fp);
		return $filedata;
	}

	return FALSE;
}

/**
 * 文件扫描
 *
 * @param   string  $source_dir     Path to source
 * @param   int $directory_depth    Depth of directories to traverse
 *                      (0 = fully recursive, 1 = current dir, etc)
 * @param   bool    $hidden         Whether to show hidden files
 * @return  array
 */
function dr_file_map($source_dir) {

	if ($fp = opendir($source_dir)) {

		$filedata = array();
		$source_dir = rtrim($source_dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

		while (FALSE !== ($file = readdir($fp))) {
			if ($file === '.' OR $file === '..'
				OR $file[0] === '.'
				OR !is_file($source_dir.$file)) {
				continue;
			}
			$filedata[] = $file;
		}
		closedir($fp);
		return $filedata;
	}

	return FALSE;
}

/**
 * 数据返回统一格式
 */
function dr_return_data($code, $msg = '', $data = array()) {
	return array(
		'code' => $code,
		'msg' => $msg,
		'data' => $data,
	);
}

/**
 * 生成sql语句，如果传入$in_cloumn 生成格式为 IN('a', 'b', 'c')
 * @param $data 条件数组或者字符串
 * @param $front 连接符
 * @param $in_column 字段名称
 * @return string
 */
function to_sqls($data, $front = ' AND ', $in_column = false) {
	if($in_column && is_array($data)) {
		$ids = '\''.implode('\',\'', $data).'\'';
		$sql = "$in_column IN ($ids)";
		return $sql;
	} else {
		if ($front == '') {
			$front = ' AND ';
		}
		if(is_array($data) && count($data) > 0) {
			$sql = '';
			foreach ($data as $key => $val) {
				$sql .= $sql ? " $front `$key` = '$val' " : " `$key` = '$val' ";
			}
			return $sql;
		} else {
			return $data;
		}
	}
}

/**
 * 分页函数
 *
 * @param $num 信息总数
 * @param $curr_page 当前分页
 * @param $perpage 每页显示数
 * @param $urlrule URL规则
 * @param $array 需要传递的数组，用于增加额外的方法
 * @return 分页
 */
function pages($num, $curr_page, $perpage = 20, $urlrule = '', $array = array(),$setpages = 10) {
	if(defined('URLRULE') && $urlrule == '') {
		$urlrule = URLRULE;
		$array = $GLOBALS['URL_ARRAY'];
	} elseif($urlrule == '') {
		$urlrule = url_par('page={$page}');
	}
	$multipage = '';
	if($num > $perpage) {
		$page = $setpages+1;
		$offset = ceil($setpages/2-1);
		$pages = ceil($num / $perpage);
		if (defined('IN_ADMIN') && !defined('PAGES')) define('PAGES', $pages);
		$from = $curr_page - $offset;
		$to = $curr_page + $offset;
		$more = 0;
		if($page >= $pages) {
			$from = 2;
			$to = $pages-1;
		} else {
			if($from <= 1) {
				$to = $page-1;
				$from = 2;
			}  elseif($to >= $pages) {
				$from = $pages-($page-2);
				$to = $pages-1;
			}
			$more = 1;
		}
		$multipage .= '<a class="a1">'.$num.L('page_item').'</a>';
		if($curr_page>0) {
			$multipage .= ' <a href="'.pageurl($urlrule, $curr_page-1, $array).'" class="a1">'.L('previous').'</a>';
			if($curr_page==1) {
				$multipage .= ' <span>1</span>';
			} elseif($curr_page>6 && $more) {
				$multipage .= ' <a href="'.pageurl($urlrule, 1, $array).'">1</a>..';
			} else {
				$multipage .= ' <a href="'.pageurl($urlrule, 1, $array).'">1</a>';
			}
		}
		for($i = $from; $i <= $to; $i++) {
			if($i != $curr_page) {
				$multipage .= ' <a href="'.pageurl($urlrule, $i, $array).'">'.$i.'</a>';
			} else {
				$multipage .= ' <span>'.$i.'</span>';
			}
		}
		if($curr_page<$pages) {
			if($curr_page<$pages-5 && $more) {
				$multipage .= ' ..<a href="'.pageurl($urlrule, $pages, $array).'">'.$pages.'</a> <a href="'.pageurl($urlrule, $curr_page+1, $array).'" class="a1">'.L('next').'</a>';
			} else {
				$multipage .= ' <a href="'.pageurl($urlrule, $pages, $array).'">'.$pages.'</a> <a href="'.pageurl($urlrule, $curr_page+1, $array).'" class="a1">'.L('next').'</a>';
			}
		} elseif($curr_page==$pages) {
			$multipage .= ' <span>'.$pages.'</span> <a href="'.pageurl($urlrule, $curr_page, $array).'" class="a1">'.L('next').'</a>';
		} else {
			$multipage .= ' <a href="'.pageurl($urlrule, $pages, $array).'">'.$pages.'</a> <a href="'.pageurl($urlrule, $curr_page+1, $array).'" class="a1">'.L('next').'</a>';
		}
	}
	return $multipage;
}

/**
 * 分页函数
 *
 * @param $num 信息总数
 * @param $curr_page 当前分页
 * @param $perpage 每页显示数
 * @param $urlrule URL规则
 * @param $array 需要传递的数组，用于增加额外的方法
 * @return 分页
 */
function mobilepages($num, $curr_page, $perpage = 20, $urlrule = '', $array = array(),$setpages = 10) {
	if(defined('URLRULE') && $urlrule == '') {
		$urlrule = URLRULE;
		$array = $GLOBALS['URL_ARRAY'];
	} elseif($urlrule == '') {
		$urlrule = url_par('page={$page}');
	}
	if(defined('SITEID')) {
		$siteid = SITEID;
	} else {
		$siteid = param::get_cookie('siteid');
	}
	if (!$siteid) $siteid = 1;
	$sitelist = getcache('sitelist', 'commons');
	if ($sitelist[$siteid]['mobilehtml']==1 && defined('ISHTML')) {
		//if (substr($sitelist[$siteid]['mobile_domain'],0,-1)) {
			$mobile_root = substr($sitelist[$siteid]['mobile_domain'],0,-1);
		//} else {
			//$mobile_root = pc_base::load_config('system','mobile_root');
		//}
	}
	$multipage = '';
	if($num > $perpage) {
		$page = $setpages+1;
		$offset = ceil($setpages/2-1);
		$pages = ceil($num / $perpage);
		if (defined('IN_ADMIN') && !defined('PAGES')) define('PAGES', $pages);
		$from = $curr_page - $offset;
		$to = $curr_page + $offset;
		$more = 0;
		if($page >= $pages) {
			$from = 2;
			$to = $pages-1;
		} else {
			if($from <= 1) {
				$to = $page-1;
				$from = 2;
			}  elseif($to >= $pages) {
				$from = $pages-($page-2);
				$to = $pages-1;
			}
			$more = 1;
		}
		$multipage .= '<a class="a1">'.$num.L('page_item').'</a>';
		if($curr_page>0) {
			$multipage .= ' <a href="'.$mobile_root.pageurl($urlrule, $curr_page-1, $array).'" class="a1">'.L('previous').'</a>';
			if($curr_page==1) {
				$multipage .= ' <span>1</span>';
			} elseif($curr_page>6 && $more) {
				$multipage .= ' <a href="'.$mobile_root.pageurl($urlrule, 1, $array).'">1</a>..';
			} else {
				$multipage .= ' <a href="'.$mobile_root.pageurl($urlrule, 1, $array).'">1</a>';
			}
		}
		for($i = $from; $i <= $to; $i++) {
			if($i != $curr_page) {
				$multipage .= ' <a href="'.$mobile_root.pageurl($urlrule, $i, $array).'">'.$i.'</a>';
			} else {
				$multipage .= ' <span>'.$i.'</span>';
			}
		}
		if($curr_page<$pages) {
			if($curr_page<$pages-5 && $more) {
				$multipage .= ' ..<a href="'.$mobile_root.pageurl($urlrule, $pages, $array).'">'.$pages.'</a> <a href="'.$mobile_root.pageurl($urlrule, $curr_page+1, $array).'" class="a1">'.L('next').'</a>';
			} else {
				$multipage .= ' <a href="'.$mobile_root.pageurl($urlrule, $pages, $array).'">'.$pages.'</a> <a href="'.$mobile_root.pageurl($urlrule, $curr_page+1, $array).'" class="a1">'.L('next').'</a>';
			}
		} elseif($curr_page==$pages) {
			$multipage .= ' <span>'.$pages.'</span> <a href="'.$mobile_root.pageurl($urlrule, $curr_page, $array).'" class="a1">'.L('next').'</a>';
		} else {
			$multipage .= ' <a href="'.$mobile_root.pageurl($urlrule, $pages, $array).'">'.$pages.'</a> <a href="'.$mobile_root.pageurl($urlrule, $curr_page+1, $array).'" class="a1">'.L('next').'</a>';
		}
	}
	return $multipage;
}
/**
 * 返回分页路径
 *
 * @param $urlrule 分页规则
 * @param $page 当前页
 * @param $array 需要传递的数组，用于增加额外的方法
 * @return 完整的URL路径
 */
function pageurl($urlrule, $page, $array = array()) {
	if(strpos($urlrule, '~')) {
		$urlrules = explode('~', $urlrule);
		$urlrule = $page < 2 ? $urlrules[0] : $urlrules[1];
	}
	$findme = array('{$page}');
	$replaceme = array($page);
	if (is_array($array)) foreach ($array as $k=>$v) {
		$findme[] = '{$'.$k.'}';
		$replaceme[] = $v;
	}
	$url = str_replace($findme, $replaceme, $urlrule);
	$url = str_replace(array('http://','https://','//','~'), array('~','~','/',SITE_PROTOCOL), $url);
	return $url;
}

/**
 * URL路径解析，pages 函数的辅助函数
 *
 * @param $par 传入需要解析的变量 默认为，page={$page}
 * @param $url URL地址
 * @return URL
 */
function url_par($par, $url = '') {
	if($url == '') $url = get_url();
	$pos = strpos($url, '?');
	if($pos === false) {
		$url .= '?'.$par;
	} else {
		$querystring = substr(strstr($url, '?'), 1);
		parse_str($querystring, $pars);
		$query_array = array();
		foreach($pars as $k=>$v) {
			if($k != 'page') $query_array[$k] = $v;
		}
		$querystring = http_build_query($query_array).'&'.$par;
		$url = substr($url, 0, $pos).'?'.$querystring;
	}
	return $url;
}

/**
 * 判断email格式是否正确
 * @param $email
 */
function is_email($email) {
	return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}

/**
 * iconv 编辑转换
 */
if (!function_exists('iconv')) {
	function iconv($in_charset, $out_charset, $str) {
		$in_charset = strtoupper($in_charset);
		$out_charset = strtoupper($out_charset);
		if (function_exists('mb_convert_encoding')) {
			return mb_convert_encoding($str, $out_charset, $in_charset);
		} else {
			pc_base::load_sys_func('iconv');
			$in_charset = strtoupper($in_charset);
			$out_charset = strtoupper($out_charset);
			if ($in_charset == 'UTF-8' && ($out_charset == 'GBK' || $out_charset == 'GB2312')) {
				return utf8_to_gbk($str);
			}
			if (($in_charset == 'GBK' || $in_charset == 'GB2312') && $out_charset == 'UTF-8') {
				return gbk_to_utf8($str);
			}
			return $str;
		}
	}
}

/**
 * 代码广告展示函数
 * @param intval $siteid 所属站点
 * @param intval $id 广告ID
 * @return 返回广告代码
 */
function show_ad($siteid, $id) {
	$siteid = intval($siteid);
	$id = intval($id);
	if(!$id || !$siteid) return false;
	$p = pc_base::load_model('poster_model');
	$r = $p->get_one(array('spaceid'=>$id, 'siteid'=>$siteid), 'disabled, setting', '`id` ASC');
	if ($r['disabled']) return '';
	if ($r['setting']) {
		$c = string2array($r['setting']);
	} else {
		$r['code'] = '';
	}
	return $c['code'];
}

/**
 * 获取当前的站点ID
 */
function get_siteid() {
	static $siteid;
	if (!empty($siteid)) return $siteid;
	if (IS_ADMIN) {
		if ($d = param::get_cookie('siteid')) {
			$siteid = $d;
		} else {
			return '';
		}
	} else {
		$data = getcache('sitelist', 'commons');
		if(!is_array($data)) return '1';
		$site_url = SITE_PROTOCOL.SITE_HURL;
		foreach ($data as $v) {
			if ($v['url'] == $site_url.'/') $siteid = $v['siteid'];
		}
	}
	if (empty($siteid)) $siteid = 1;
	return $siteid;
}

/**
 * 获取用户昵称
 * 不传入userid取当前用户nickname,如果nickname为空取username
 * 传入field，取用户$field字段信息
 */
function get_nickname($userid='', $field='') {
	$return = '';
	if(is_numeric($userid)) {
		$member_db = pc_base::load_model('member_model');
		$memberinfo = $member_db->get_one(array('userid'=>$userid));
		if(!empty($field) && $field != 'nickname' && isset($memberinfo[$field]) &&!empty($memberinfo[$field])) {
			$return = $memberinfo[$field];
		} else {
			$return = isset($memberinfo['nickname']) && !empty($memberinfo['nickname']) ? $memberinfo['nickname'].'('.$memberinfo['username'].')' : $memberinfo['username'];
		}
	} else {
		if (param::get_cookie('_nickname')) {
			$return .= '('.param::get_cookie('_nickname').')';
		} else {
			$return .= '('.param::get_cookie('_username').')';
		}
	}
	return $return;
}

/**
 * 获取用户信息
 * 不传入$field返回用户所有信息,
 * 传入field，取用户$field字段信息
 */
function get_memberinfo($userid, $field='') {
	if(!is_numeric($userid)) {
		return false;
	} else {
		static $memberinfo;
		if (!isset($memberinfo[$userid])) {
			$member_db = pc_base::load_model('member_model');
			$memberinfo[$userid] = $member_db->get_one(array('userid'=>$userid));
		}
		if(!empty($field) && !empty($memberinfo[$userid][$field])) {
			return $memberinfo[$userid][$field];
		} else {
			return $memberinfo[$userid];
		}
	}
}

/**
 * 通过 username 值，获取用户所有信息
 * 获取用户信息
 * 不传入$field返回用户所有信息,
 * 传入field，取用户$field字段信息
 */
function get_memberinfo_buyusername($username, $field='') {
	if(empty($username)){return false;}
	static $memberinfo;
	if (!isset($memberinfo[$username])) {
		$member_db = pc_base::load_model('member_model');
		$memberinfo[$username] = $member_db->get_one(array('username'=>$username));
	}
	if(!empty($field) && !empty($memberinfo[$username][$field])) {
		return $memberinfo[$username][$field];
	} else {
		return $memberinfo[$username];
	}
}

/**
 * 获取用户头像
 * @param $uid 默认为userid
 * @param $size 头像大小
 */
function get_memberavatar($uid, $size = '') {
	$db = pc_base::load_model('member_model');
	$memberinfo = $db->get_one(array('userid'=>$uid));
	$att_db = pc_base::load_model('attachment_model');
	$avatar_db = $att_db->get_one(array('aid'=>$memberinfo['avatar']));
	pc_base::load_sys_class('image');
	$image = new image();
	if ($avatar_db) {
		if ($size) {
			$avatar = $image->thumb(SYS_AVATAR_URL.$avatar_db['filepath'], $size, $size, 0, 'auto', 1);
		} else {
			$avatar = SYS_AVATAR_URL.$avatar_db['filepath'];
		}
	} else {
		$avatar = IMG_PATH.'member/nophoto.gif';
	}
	return $avatar;
}

/**
 * 调用关联菜单
 * @param $linkageid 联动菜单id
 * @param $id 生成联动菜单的样式id
 * @param $defaultvalue 默认值
 */
function menu_linkage($linkageid = 0, $id = 'linkid', $defaultvalue = 0) {
	$linkageid = intval($linkageid);
	if (!$linkageid) {$linkageid = 1;}
	$datas = array();
	$datas = getcache($linkageid,'linkage');
	if (!$datas) {$linkageid = 1;}
	$datas = getcache($linkageid,'linkage');
	$infos = $datas['data'];

	if($datas['style']=='1') {
		$title = $datas['title'];
		$container = 'content'.random(3).date('is');
		if(!defined('DIALOG_INIT_1')) {
			define('DIALOG_INIT_1', 1);
			$string .= '<script type="text/javascript" src="'.JS_PATH.'dialog.js"></script>';
			//TODO $string .= '<link href="'.CSS_PATH.'dialog.css" rel="stylesheet" type="text/css">';
		}
		if(!defined('LINKAGE_INIT_1')) {
			define('LINKAGE_INIT_1', 1);
			$string .= '<script type="text/javascript" src="'.JS_PATH.'linkage/js/pop.js"></script>';
		}
		$var_div = $defaultvalue && (ROUTE_A=='edit' || ROUTE_A=='account_manage_info'  || ROUTE_A=='info_publish' || ROUTE_A=='orderinfo') ? menu_linkage_level($defaultvalue,$linkageid,$infos) : $datas['title'];
		$var_input = $defaultvalue && (ROUTE_A=='edit' || ROUTE_A=='account_manage_info'  || ROUTE_A=='info_publish') ? '<input type="hidden" name="info['.$id.']" value="'.$defaultvalue.'">' : '<input type="hidden" name="info['.$id.']" value="">';
		$string .= '<div name="'.$id.'" value="" id="'.$id.'" class="ib">'.$var_div.'</div>'.$var_input.' <input type="button" name="btn_'.$id.'" class="button" value="'.L('linkage_select').'" onclick="open_linkage(\''.$id.'\',\''.$title.'\','.$container.',\''.$linkageid.'\')">';
		$string .= '<script type="text/javascript">';
		$string .= 'var returnid_'.$id.'= \''.$id.'\';';
		$string .= 'var returnkeyid_'.$id.' = \''.$linkageid.'\';';
		$string .=  'var '.$container.' = new Array(';
		foreach($infos AS $k=>$v) {
			if($v['parentid'] == 0) {
				$s[]='new Array(\''.$v['linkageid'].'\',\''.$v['name'].'\',\''.$v['parentid'].'\')';
			} else {
				continue;
			}
		}
		$s = implode(',',$s);
		$string .=$s;
		$string .= ')';
		$string .= '</script>';
		
	} elseif($datas['style']=='2') {
		if(!defined('LINKAGE_INIT_1')) {
			define('LINKAGE_INIT_1', 1);
			$string .= '<script type="text/javascript" src="'.JS_PATH.'linkage/js/jquery.ld.js"></script>';
		}
		$default_txt = '';
		if($defaultvalue) {
				$default_txt = menu_linkage_level($defaultvalue,$linkageid,$infos);
				$default_txt = '["'.str_replace(' > ','","',$default_txt).'"]';
		}
		$string .= $defaultvalue && (ROUTE_A=='edit' || ROUTE_A=='account_manage_info'  || ROUTE_A=='info_publish') ? '<input type="hidden" name="info['.$id.']"  id="'.$id.'" value="'.$defaultvalue.'">' : '<input type="hidden" name="info['.$id.']"  id="'.$id.'" value="">';

		for($i=1;$i<=$datas['setting']['level'];$i++) {
			$string .='<select class="pc-select-'.$id.'" name="'.$id.'-'.$i.'" id="'.$id.'-'.$i.'" width="100"><option value="">请选择菜单</option></select> ';
		}

		$string .= '<script type="text/javascript">
					$(function(){
						var $ld5 = $(".pc-select-'.$id.'");					  
						$ld5.ld({ajaxOptions : {"url" : "'.APP_PATH.'api.php?op=get_linkage&act=ajax_select&keyid='.$linkageid.'"},defaultParentId : 0,style : {"width" : 120}})	 
						var ld5_api = $ld5.ld("api");
						ld5_api.selected('.$default_txt.');
						$ld5.bind("change",onchange);
						function onchange(e){
							var $target = $(e.target);
							var index = $ld5.index($target);
							$("#'.$id.'-'.$i.'").remove();
							$("#'.$id.'").val($ld5.eq(index).show().val());
							index ++;
							$ld5.eq(index).show();								}
					})
		</script>';
			
	} else {
		$title = $defaultvalue ? $infos[$defaultvalue]['name'] : $datas['title'];
		$colObj = random(3).date('is');
		$string = '';
		if(!defined('LINKAGE_INIT')) {
			define('LINKAGE_INIT', 1);
			$string .= '<script type="text/javascript" src="'.JS_PATH.'linkage/js/mln.colselect.js"></script>';
			if(defined('IN_ADMIN')) {
				$string .= '<link href="'.JS_PATH.'linkage/style/admin.css" rel="stylesheet" type="text/css">';
			} else {
				$string .= '<link href="'.JS_PATH.'linkage/style/css.css" rel="stylesheet" type="text/css">';
			}
		}
		$string .= '<input type="hidden" name="info['.$id.']" value="1"><div id="'.$id.'"></div>';
		$string .= '<script type="text/javascript">';
		$string .= 'var colObj'.$colObj.' = {"Items":[';

		foreach($infos AS $k=>$v) {
			$s .= '{"name":"'.$v['name'].'","topid":"'.$v['parentid'].'","colid":"'.$k.'","value":"'.$k.'","fun":function(){}},';
		}

		$string .= substr($s, 0, -1);
		$string .= ']};';
		$string .= '$("#'.$id.'").mlnColsel(colObj'.$colObj.',{';
		$string .= 'title:"'.$title.'",';
		$string .= 'value:"'.$defaultvalue.'",';
		$string .= 'width:100';
		$string .= '});';
		$string .= '</script>';
	}
	return $string;
}

/**
 * 联动菜单层级
 */

function menu_linkage_level($linkageid,$keyid,$infos,$result=array()) {
	if(array_key_exists($linkageid,$infos)) {
		$result[]=$infos[$linkageid]['name'];
		return menu_linkage_level($infos[$linkageid]['parentid'],$keyid,$infos,$result);
	}
	krsort($result);
	return implode(' > ',$result);
}
/**
 * 通过catid获取显示菜单完整结构
 * @param  $menuid 菜单ID
 * @param  $cache_file 菜单缓存文件名称
 * @param  $cache_path 缓存文件目录
 * @param  $key 取得缓存值的键值名称
 * @param  $parentkey 父级的ID
 * @param  $linkstring 链接字符
 */
function menu_level($menuid, $cache_file, $cache_path = 'commons', $key = 'catname', $parentkey = 'parentid', $linkstring = ' > ', $result=array()) {
	$menu_arr = getcache($cache_file, $cache_path);
	if (array_key_exists($menuid, $menu_arr)) {
		$result[] = $menu_arr[$menuid][$key];
		return menu_level($menu_arr[$menuid][$parentkey], $cache_file, $cache_path, $key, $parentkey, $linkstring, $result);
	}
	krsort($result);
	return implode($linkstring, $result);
}
/**
 * 通过id获取显示联动菜单
 * @param  $linkageid 联动菜单ID
 * @param  $keyid 菜单keyid
 * @param  $space 菜单间隔符
 * @param  $tyoe 1 返回间隔符链接，完整路径名称 3 返回完整路径数组，2返回当前联动菜单名称，4 直接返回ID
 * @param  $result 递归使用字段1
 * @param  $infos 递归使用字段2
 */
function get_linkage($linkageid, $keyid, $space = '>', $type = 1, $result = array(), $infos = array()) {
	if($space=='' || !isset($space))$space = '>';
	if(!$infos) {
		$datas = getcache($keyid,'linkage');
		$infos = $datas['data'];
	}
	if($type == 1 || $type == 3 || $type == 4) {
		if(array_key_exists($linkageid,$infos)) {
			$result[]= ($type == 1) ? $infos[$linkageid]['name'] : (($type == 4) ? $linkageid :$infos[$linkageid]);
			return get_linkage($infos[$linkageid]['parentid'], $keyid, $space, $type, $result, $infos);
		} else {
			if(count($result)>0) {
				krsort($result);
				if($type == 1 || $type == 4) $result = implode($space,$result);
				return $result;
			} else {
				return $result;
			}
		}
	} else {
		return $infos[$linkageid]['name'];
	}
}
/**
 * IE浏览器判断
 */

function is_ie() {
	$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
	if((strpos($useragent, 'opera') !== false) || (strpos($useragent, 'konqueror') !== false)) return false;
	if(strpos($useragent, 'msie ') !== false) return true;
	return false;
}


/**
 * 文件下载
 * @param $filepath 文件路径
 * @param $filename 文件名称
 */

function file_down($filepath, $filename = '') {
	if(!$filename) $filename = basename($filepath);
	if(is_ie()) $filename = rawurlencode($filename);
	$filetype = fileext($filename);
	$filesize = sprintf("%u", filesize($filepath));
	if(ob_get_length() !== false) @ob_end_clean();
	header('Pragma: public');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: pre-check=0, post-check=0, max-age=0');
	header('Content-Transfer-Encoding: binary');
	header('Content-Encoding: none');
	header('Content-type: '.$filetype);
	header('Content-Disposition: attachment; filename="'.$filename.'"');
	header('Content-length: '.$filesize);
	readfile($filepath);
	exit;
}

/**
 * 判断字符串是否为utf8编码，英文和半角字符返回ture
 * @param $string
 * @return bool
 */
function is_utf8($string) {
	return preg_match('%^(?:
					[\x09\x0A\x0D\x20-\x7E] # ASCII
					| [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
					| \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
					| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
					| \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
					| \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
					| [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
					| \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
					)*$%xs', $string);
}

/**
 * 组装生成ID号
 * @param $modules 模块名
 * @param $contentid 内容ID
 * @param $siteid 站点ID
 */
function id_encode($modules,$contentid, $siteid) {
	return urlencode($modules.'-'.$contentid.'-'.$siteid);
}

/**
 * 解析ID
 * @param $id 评论ID
 */
function id_decode($id) {
	return explode('-', $id);
}

/**
 * 对用户的密码进行加密
 * @param $password
 * @param $encrypt //传入加密串，在修改密码时做认证
 * @return array/password
 */
function password($password, $encrypt='') {
	$pwd = array();
	$pwd['encrypt'] =  $encrypt ? $encrypt : create_randomstr();
	$pwd['password'] = md5(md5(trim($password)).$pwd['encrypt']);
	return $encrypt ? $pwd['password'] : $pwd;
}
/**
 * 生成随机字符串
 * @param string $lenth 长度
 * @return string 字符串
 */
function create_randomstr($lenth = 10) {
	return random($lenth, '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ');
}

/**
 * 检查密码长度是否符合规定
 *
 * @param STRING $password
 * @return 	TRUE or FALSE
 */
function is_password($password) {
	$strlen = strlen($password);
	if($strlen >= 6 && $strlen <= 20) return true;
	return false;
}

 /**
 * 检测输入中是否含有错误字符
 *
 * @param char $string 要检查的字符串名称
 * @return TRUE or FALSE
 */
function is_badword($string) {
	$badwords = array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n","#");
	foreach($badwords as $value){
		if(strpos($string, $value) !== FALSE) {
			return TRUE;
		}
	}
	return FALSE;
}

/**
 * 检查用户名是否符合规定
 *
 * @param STRING $username 要检查的用户名
 * @return 	TRUE or FALSE
 */
function is_username($username) {
	$strlen = strlen($username);
	if(is_badword($username) || !preg_match("/^[a-zA-Z0-9_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]+$/", $username)){
		return false;
	} elseif ( 20 < $strlen || $strlen < 2 ) {
		return false;
	}
	return true;
}

/**
 * 检查id是否存在于数组中
 *
 * @param $id
 * @param $ids
 * @param $s
 */
function check_in($id, $ids = '', $s = ',') {
	if(!$ids) return false;
	$ids = explode($s, $ids);
	return is_array($id) ? array_intersect($id, $ids) : in_array($id, $ids);
}

/**
 * 对数据进行编码转换
 * @param array/string $data       数组
 * @param string $input     需要转换的编码
 * @param string $output    转换后的编码
 */
function array_iconv($data, $input = 'gbk', $output = 'utf-8') {
	if (!is_array($data)) {
		return iconv($input, $output, $data);
	} else {
		foreach ($data as $key=>$val) {
			if(is_array($val)) {
				$data[$key] = array_iconv($val, $input, $output);
			} else {
				$data[$key] = iconv($input, $output, $val);
			}
		}
		return $data;
	}
}

/**
 * 生成缩略图函数
 * @param  $img 图片id或者路径
 * @param  $width  缩略图宽度
 * @param  $height 缩略图高度
 * @param  $water 是否水印
 * @param  $mode 图片模式
 * @param  $webimg 剪切网络图片
 */
function thumb($img, $width = 100, $height = 100 ,$water = 0, $mode = 'auto', $webimg = 0) {
    if (!$img) {
        return IMG_PATH.'nopic.gif';
    }
	if ($img || $webimg) {
		// 强制缩略图水印
		$config = siteinfo((SITEID ? SITEID : (SITE_ID ? SITE_ID : get_siteid())));
		$site_setting = string2array($config['setting']);
		if ($site_setting['thumb']) {
			$water = 1;
		}
		pc_base::load_sys_class('image');
		$image = new image();
		return $image->thumb($img, $width, $height, $water, $mode, $webimg);
	}
	$file = dr_file($img);
    return $file ? $file : IMG_PATH.'nopic.gif';
}

/**
 * 栏目面包屑导航
 *
 * @param   intval  $catid  栏目id
 * @param   string  $symbol 面包屑间隔符号
 * @param   string  $url    是否显示URL
 * @param   string  $html   格式替换
 * @return  string
 */
function dr_catpos($catid, $symbol = ' > ', $url = true, $html= '') {
	if (!$catid) {
		return '';
	}
	$cat = array();
	$siteids = getcache('category_content','commons');
	$siteid = $siteids[$catid];
	$cat = getcache('category_content_'.$siteid,'commons');
	if (!isset($cat[$catid])) {
		return '';
	}
	$name = array();
	$array = explode(',', $cat[$catid]['arrparentid']);
	$array[] = $catid;
	foreach ($array as $id) {
		$setting = string2array($cat[$id]['setting']);
		if ($id && $cat[$id] && $setting['iscatpos'] && !$setting['disabled']) {
			$murl = $cat[$id]['url'];
			$name[] = $url ? ($html ? str_replace(array('[url]', '[name]'), array($murl, $cat[$id]['catname']), $html) : '<a href=".'.$murl.'">'.$cat[$id]['catname'].'</a>') : $cat[$id]['catname'];
		}
	}
	return implode($symbol, array_unique($name));
}

/**
 * 栏目面包屑导航
 *
 * @param   intval  $catid  栏目id
 * @param   string  $symbol 面包屑间隔符号
 * @param   string  $url    是否显示URL
 * @param   string  $html   格式替换
 * @return  string
 */
function dr_mobile_catpos($catid, $symbol = ' > ', $url = true, $html= '') {
	if (!$catid) {
		return '';
	}
	$cat = array();
	$siteids = getcache('category_content','commons');
	$siteid = $siteids[$catid];
	$cat = getcache('category_content_'.$siteid,'commons');
	if (!isset($cat[$catid])) {
		return '';
	}
	$siteurl = siteurl($cat[$catid]['siteid']);
	$sitemobileurl = sitemobileurl($cat[$catid]['siteid']);
	$name = array();
	$array = explode(',', $cat[$catid]['arrparentid']);
	$array[] = $catid;
	foreach ($array as $id) {
		$setting = string2array($cat[$id]['setting']);
		if ($id && $cat[$id] && $setting['iscatpos'] && !$setting['disabled']) {
			$murl = str_replace($siteurl, $sitemobileurl, $cat[$id]['url']);
			$name[] = $url ? ($html ? str_replace(array('[url]', '[name]'), array($murl, $cat[$id]['catname']), $html) : '<a href="'.$murl.'">'.$cat[$id]['catname'].'</a>') : $cat[$id]['catname'];
		}
	}
	return implode($symbol, array_unique($name));
}

/**
 * 当前路径
 * 返回指定栏目路径层级
 * @param $catid 栏目id
 * @param $symbol 栏目间隔符
 */
function catpos($catid, $symbol=' > '){
	$category_arr = array();
	$siteids = getcache('category_content','commons');
	$siteid = $siteids[$catid];
	$category_arr = getcache('category_content_'.$siteid,'commons');
	if(!isset($category_arr[$catid])) return '';
	$pos = '';
	$siteurl = siteurl($category_arr[$catid]['siteid']);
	$arrparentid = array_filter(explode(',', $category_arr[$catid]['arrparentid'].','.$catid));
	foreach($arrparentid as $catid) {
		$setting = string2array($category_arr[$catid]['setting']);
		if ($catid && $category_arr[$catid] && $setting['iscatpos'] && !$setting['disabled']) {
			$url = $category_arr[$catid]['url'];
			if(strpos($url, '://') === false) $url = $siteurl.$url;
			$pos .= '<a href="'.$url.'">'.$category_arr[$catid]['catname'].'</a>'.$symbol;
		}
	}
	return $pos;
}

/**
 * 当前路径
 * 返回指定栏目路径层级
 * @param $catid 栏目id
 * @param $symbol 栏目间隔符
 */
function mobilecatpos($catid, $symbol=' > '){
	$category_arr = array();
	$siteids = getcache('category_content','commons');
	$siteid = $siteids[$catid];
	$category_arr = getcache('category_content_'.$siteid,'commons');
	if(!isset($category_arr[$catid])) return '';
	$pos = '';
	$siteurl = siteurl($category_arr[$catid]['siteid']);
	$sitemobileurl = sitemobileurl($category_arr[$catid]['siteid']);
	$arrparentid = array_filter(explode(',', $category_arr[$catid]['arrparentid'].','.$catid));
	foreach($arrparentid as $catid) {
		$setting = string2array($category_arr[$catid]['setting']);
		if ($catid && $category_arr[$catid] && $setting['iscatpos'] && !$setting['disabled']) {
			$url = $category_arr[$catid]['url'];
			if(strpos($url, '://') === false) $url = $sitemobileurl.$url;
			$pos .= '<a href="'.str_replace($siteurl,$sitemobileurl,$url).'">'.$category_arr[$catid]['catname'].'</a>'.$symbol;
		}
	}
	return $pos;
}

/**
 * 根据catid获取子栏目数据的sql语句
 * @param string $module 缓存文件名
 * @param intval $catid 栏目ID
 */

function get_sql_catid($file = 'category_content_1', $catid = 0, $module = 'commons') {
	$category = getcache($file,$module);
	$catid = intval($catid);
	if(!isset($category[$catid])) return false;
	return $category[$catid]['child'] ? " `catid` IN(".$category[$catid]['arrchildid'].") " : " `catid`=$catid ";
}

/**
 * 获取子栏目
 * @param $parentid 父级id
 * @param $type 栏目类型
 * @param $self 是否包含本身 0为不包含
 * @param $siteid 站点id
 */
function subcat($parentid = NULL, $type = NULL,$self = '0', $siteid = '') {
	if (empty($siteid)) $siteid = get_siteid();
	$category = getcache('category_content_'.$siteid,'commons');
	if (isset($category) && is_array($category)) {
		foreach($category as $id=>$cat) {
			if($cat['siteid'] == $siteid && ($parentid === NULL || $cat['parentid'] == $parentid) && ($type === NULL || $cat['type'] == $type)) $subcat[$id] = $cat;
			if($self == 1 && $cat['catid'] == $parentid && !$cat['child'])  $subcat[$id] = $cat;
		}
	}
	return $subcat;
}

/**
 * 获取内容地址
 * @param $catid   栏目ID
 * @param $id      文章ID
 * @param $allurl  是否以绝对路径返回
 */
function go($catid,$id, $allurl = 0) {
	static $category;
	if(empty($category)) {
		$siteids = getcache('category_content','commons');
		$siteid = $siteids[$catid];
		$category = getcache('category_content_'.$siteid,'commons');
	}
	$id = intval($id);
	if(!$id || !isset($category[$catid])) return '';
	$modelid = $category[$catid]['modelid'];
	if(!$modelid) return '';
	$db = pc_base::load_model('content_model');
	$db->set_model($modelid);
	$r = $db->get_one(array('id'=>$id), '`url`');
	if (!empty($allurl)) {
		if (strpos($r['url'], '://')===false) {
			$site = siteinfo($category[$catid]['siteid']);
			$r['url'] = substr($site['domain'], 0, -1).$r['url'];
		}
	}

	return $r['url'];
}

/**
 * 将附件地址转换为绝对地址
 * @param $path 附件地址
 */
function atturl($path) {
	if(strpos($path, ':/')) {
		return $path;
	} else {
		$sitelist = getcache('sitelist', 'commons');
		$siteid =  get_siteid();
		$siteurl = $sitelist[$siteid]['domain'];
		$domainlen = strlen($sitelist[$siteid]['domain'])-1;
		$path = $siteurl.$path;
		$path = substr_replace($path, '/', strpos($path, '//',$domainlen),2);
		return 	$path;
	}
}

/**
 * 判断模块是否安装
 * @param $m	模块名称
 */
function module_exists($m = '') {
	if ($m=='admin') return true;
	$modules = getcache('modules', 'commons');
	if (!$modules) return '';
	$modules = array_keys($modules);
	return in_array($m, $modules);
}

/**
 * 生成SEO
 * @param $siteid       站点ID
 * @param $catid        栏目ID
 * @param $title        标题
 * @param $description  描述
 * @param $keyword      关键词
 */
function seo($siteid, $catid = '', $title = '', $description = '', $keyword = '') {
	if (!empty($title))$title = strip_tags($title);
	if (!empty($description)) $description = strip_tags($description);
	if (!empty($keyword)) $keyword = str_replace(' ', ',', strip_tags($keyword));
	$sites = getcache('sitelist', 'commons');
	$site = $sites[$siteid];
	$cat = array();
	if (!empty($catid)) {
		$siteids = getcache('category_content','commons');
		$siteid = $siteids[$catid];
		$categorys = getcache('category_content_'.$siteid,'commons');
		$cat = $categorys[$catid];
		$cat['setting'] = string2array($cat['setting']);
	}
	$seo['site_title'] =isset($site['site_title']) && !empty($site['site_title']) ? $site['site_title'] : $site['name'];
	$seo['keyword'] = !empty($keyword) ? $keyword : $site['keywords'];
	$seo['description'] = isset($description) && !empty($description) ? $description : (isset($cat['setting']['meta_description']) && !empty($cat['setting']['meta_description']) ? $cat['setting']['meta_description'] : (isset($site['description']) && !empty($site['description']) ? $site['description'] : ''));
	$seo['title'] =  (isset($title) && !empty($title) ? $title.' - ' : '').(isset($cat['setting']['meta_title']) && !empty($cat['setting']['meta_title']) ? $cat['setting']['meta_title'].' - ' : (isset($cat['catname']) && !empty($cat['catname']) ? $cat['catname'].' - ' : ''));
	foreach ($seo as $k=>$v) {
		$seo[$k] = str_replace(array("\n","\r"),	'', $v);
	}
	return $seo;
}

/**
 * 获取站点的信息
 * @param $siteid   站点ID
 */
function siteinfo($siteid) {
	static $sitelist;
	if (empty($sitelist)) $sitelist = getcache('sitelist', 'commons');
	if (!$sitelist) return '';
	return isset($sitelist[$siteid]) ? $sitelist[$siteid] : '';
}

// 判断是否是移动端终端
function is_mobile($siteid) {
	if($siteid) {
		$config = siteinfo($siteid);
		$not_pad = $config['not_pad'];
	} else {
		$not_pad = '';
	}
	if ($not_pad) {
		// 判断是否为平板，将排除为移动端
		$clientkeywords = array(
			'ipad',
		);
		// 从HTTP_USER_AGENT中查找关键字
		if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))){
			return false;
		}
	}
	if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
		// 如果有HTTP_X_WAP_PROFILE则一定是移动设备
		return true;
	} elseif (isset ($_SERVER['HTTP_USER_AGENT'])) {
		// 判断手机发送的客户端标志,兼容性有待提高
		$clientkeywords = array('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','xiaomi','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile');
		// 从HTTP_USER_AGENT中查找手机浏览器的关键字
		if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))){
			return true;
		}
	}
	// 协议法，因为有可能不准确，放到最后判断
	if (isset ($_SERVER['HTTP_ACCEPT'])) {
		// 如果只支持wml并且不支持html那一定是移动设备
		// 如果支持wml和html但是wml在html之前则是移动设备
		if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
		{
			return true;
		}
	}
	return false;
}

// 文件是否是图片
function is_image($value) {
	return in_array(trim(strtolower(strrchr($value, '.')), '.'), array('jpg', 'gif', 'png', 'jpeg'));
}

/**
 * 生成CNZZ统计代码
 */

function tjcode() {
	if(!module_exists('cnzz')) return false;
	$config = getcache('cnzz', 'commons');
	if (empty($config)) {
		return false;
	} else {
		return '<script src=\'http://pw.cnzz.com/c.php?id='.$config['siteid'].'&l=2\' language=\'JavaScript\' charset=\'gb2312\'></script>';
	}
}

/**
 * 生成标题样式
 * @param $style   样式
 * @param $html    是否显示完整的STYLE
 */
function title_style($style, $html = 1) {
	if(!$style) return "";
	$str = '';
	if ($html) $str = ' style="';
	$style_arr = explode(';',$style);
	if (!empty($style_arr[0])) $str .= 'color:'.$style_arr[0].';';
	if (!empty($style_arr[1])) $str .= 'font-weight:'.$style_arr[1].';';
	if ($html) $str .= '" ';
	return $str;
}

/**
 * 获取站点域名
 * @param $siteid   站点id
 */
function siteurl($siteid) {
	static $sitelist;
	if(!$siteid) return WEB_PATH;
	if(empty($sitelist)) $sitelist = getcache('sitelist', 'commons');
	if (!$sitelist) return '';
	return substr($sitelist[$siteid]['domain'],0,-1);
}
/**
 * 获取站点手机域名
 * @param $siteid   站点id
 */
function sitemobileurl($siteid) {
	static $sitelist;
	if(!$siteid) return WEB_PATH.'index.php?m=mobile';
	if(empty($sitelist)) $sitelist = getcache('sitelist', 'commons');
	if (!$sitelist) return '';
	if(!substr($sitelist[$siteid]['mobile_domain'],0,-1)) return substr($sitelist[$siteid]['domain'],0,-1).WEB_PATH.'index.php?m=mobile';
	/*if ($sitelist[$siteid]['mobilehtml']==1 || $sitelist[$siteid]['mobile_domain']) {
		return substr($sitelist[$siteid]['mobile_domain'],0,-1);
	} else {
		return substr($sitelist[$siteid]['domain'],0,-1).WEB_PATH.'index.php?m=mobile';
	}*/
	//if ($sitelist[$siteid]['mobilehtml']==1) {
		return substr($sitelist[$siteid]['mobile_domain'],0,-1);
	//} else {
		//if (defined('IS_MOBILE') && IS_MOBILE) {
			//return substr($sitelist[$siteid]['mobile_domain'],0,-1);
		//} else {
			//return substr($sitelist[$siteid]['domain'],0,-1).WEB_PATH.'index.php?m=mobile';
		//}
	//}
}
// 验证字符串
function dr_get_csrf_token() {
	$cache = pc_base::load_sys_class('cache');
	$code = $_SESSION['pc_hash'];
	if (!$code) {
		$code = bin2hex(random_bytes(16));
		//$cache->set_auth_data('pc_hash', $code, 1);
		$_SESSION['pc_hash'] = $code;
	}
	return $code;
}
/**
 * 生成上传附件验证
 * @param $args   参数
 * @param $operation   操作类型(加密解密)
 */
function upload_key($args) {
	$pc_auth_key = md5(PC_PATH.'upload'.pc_base::load_config('system','auth_key').$_SERVER['HTTP_USER_AGENT']);
	$authkey = md5($args.$pc_auth_key);
	return $authkey;
}
/**
 * 生成验证key
 * @param $prefix   参数
 * @param $suffix   参数
 */
function get_auth_key($prefix,$suffix="") {
	if($prefix=='login'){
		$pc_auth_key = md5(PC_PATH.'login'.pc_base::load_config('system','auth_key').ip());
	}else if($prefix=='email'){
		$pc_auth_key = md5(PC_PATH.'email'.pc_base::load_config('system','auth_key'));
	}else{
		$pc_auth_key = md5(PC_PATH.'other'.pc_base::load_config('system','auth_key').$suffix);
	}
	$authkey = md5($prefix.$pc_auth_key);
	return $authkey;
}
/**
 * 文本转换为图片
 * @param string $txt 图形化文本内容
 * @param int $fonttype 无外部字体时生成文字大小，取值范围1-5
 * @param int $fontsize 引入外部字体时，字体大小
 * @param string $font 字体名称 字体请放于cms\libs\data\font下
 * @param string $fontcolor 字体颜色 十六进制形式 如FFFFFF,FF0000
 */
function string2img($txt, $fonttype = 5, $fontsize = 16, $font = '', $fontcolor = 'FF0000',$transparent = '1') {
	if(empty($txt)) return false;
	if(function_exists("imagepng")) {
		$txt = urlencode(sys_auth($txt));
		$txt = '<img src="'.APP_PATH.'api.php?op=creatimg&txt='.$txt.'&fonttype='.$fonttype.'&fontsize='.$fontsize.'&font='.$font.'&fontcolor='.$fontcolor.'&transparent='.$transparent.'" align="absmiddle">';
	}
	return $txt;
}

/**
 * 获取cms版本号
 */
function get_pc_version($type='') {
	$version = pc_base::load_config('version');
	if($type==1) {
		return $version['cms_version'];
	} elseif($type==2) {
		return $version['cms_release'];
	} else {
		return $version['cms_version'].' '.$version['cms_release'];
	}
}
/**
 * 运行钩子（插件使用）
 */
function runhook($method) {
	$time_start = getmicrotime();
	$data  = '';
	$getpclass = FALSE;
	$hook_appid = getcache('hook','plugins');
	if(!empty($hook_appid)) {
		foreach($hook_appid as $appid => $p) {
			$pluginfilepath = PC_PATH.'plugin'.DIRECTORY_SEPARATOR.$p.DIRECTORY_SEPARATOR.'hook.class.php';
			$getpclass = TRUE;
			include_once $pluginfilepath;
		}
		$hook_appid = array_flip($hook_appid);
		if($getpclass) {
			$pclass = new ReflectionClass('hook');
			foreach($pclass->getMethods() as $r) {
				$legalmethods[] = $r->getName();
			}
		}
		if(in_array($method,$legalmethods)) {
			foreach (get_declared_classes() as $class){
			   $refclass = new ReflectionClass($class);
			   if($refclass->isSubclassOf('hook')){
				  if ($_method = $refclass->getMethod($method)) {
						  $classname = $refclass->getName();
						if ($_method->isPublic() && $_method->isFinal()) {
							plugin_stat($hook_appid[$classname]);
							$data .= $_method->invoke(null);
						}
					}
			   }
			}
		}
		return $data;
	}
}

function getmicrotime() {
	list($usec, $sec) = explode(" ",microtime());
	return ((float)$usec + (float)$sec);
}

/**
 * 插件前台模板加载
 * Enter description here ...
 * @param unknown_type $module
 * @param unknown_type $template
 * @param unknown_type $style
 */
function p_template($plugin = 'content', $template = 'index',$style='default') {
	if(!$style) $style = 'default';
	$template_cache = pc_base::load_sys_class('template_cache');
	$compiledtplfile = CMS_PATH.'caches'.DIRECTORY_SEPARATOR.'caches_template'.DIRECTORY_SEPARATOR.$style.DIRECTORY_SEPARATOR.'plugin'.DIRECTORY_SEPARATOR.$plugin.DIRECTORY_SEPARATOR.$template.'.php';

	if(!file_exists($compiledtplfile) || (file_exists(PC_PATH.'plugin'.DIRECTORY_SEPARATOR.$plugin.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.'.html') && filemtime(PC_PATH.'plugin'.DIRECTORY_SEPARATOR.$plugin.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.'.html') > filemtime($compiledtplfile))) {
		$template_cache->template_compile('plugin/'.$plugin, $template, 'default');
	} elseif (!file_exists(PC_PATH.'plugin'.DIRECTORY_SEPARATOR.$plugin.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.'.html')) {
		showmessage('Template does not exist.'.DIRECTORY_SEPARATOR.'plugin'.DIRECTORY_SEPARATOR.$plugin.DIRECTORY_SEPARATOR.$template.'.html');
	}

	return $compiledtplfile;
}
/**
 * 读取缓存动态页面
 */
function cache_page_start() {
	$relate_url = isset($_SERVER['REQUEST_URI']) ? safe_replace($_SERVER['REQUEST_URI']) : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.safe_replace($_SERVER['QUERY_STRING']) : $path_info);
	define('CACHE_PAGE_ID', md5($relate_url));
	$contents = getcache(CACHE_PAGE_ID, 'page_tmp/'.substr(CACHE_PAGE_ID, 0, 2));
	if($contents && intval(substr($contents, 15, 10)) > SYS_TIME) {
		echo substr($contents, 29);
		exit;
	}
	if (!defined('HTML')) define('HTML',true);
	return true;
}
/**
 * 写入缓存动态页面
 */
function cache_page($ttl = 360, $isjs = 0) {
	if($ttl == 0 || !defined('CACHE_PAGE_ID')) return false;
	$contents = ob_get_contents();

	if($isjs) $contents = format_js($contents);
	$contents = "<!--expiretime:".(SYS_TIME + $ttl)."-->\n".$contents;
	setcache(CACHE_PAGE_ID, $contents, 'page_tmp/'.substr(CACHE_PAGE_ID, 0, 2));
}

/**
 *
 * 获取远程内容
 * @param $url 接口url地址
 * @param $timeout 超时时间
 */
function pc_file_get_contents($url, $timeout=30) {
	$stream = stream_context_create(array('http' => array('timeout' => $timeout)));
	return @file_get_contents($url, 0, $stream);
}

/**
 * 获取文件名
 */
function file_name($name) {
	strpos($name, '/') !== false && $name = trim(strrchr($name, '/'), '/');
	return substr($name, 0, strrpos($name, '.'));
}
// 获取远程附件扩展名
function get_file_ext($url) {

	$arr = array('gif', 'jpg', 'jpeg', 'png', 'webp');
	$ext = str_replace('.', '', trim(strtolower(strrchr($url, '.')), '.'));
	if ($ext && in_array($ext, $arr)) {
		return $ext; // 满足扩展名
	} elseif ($ext && strlen($ext) < 4) {
		return ''; // 表示不是图片扩展名了
	}

	foreach ($arr as $t) {
		if (stripos($url, $t) !== false) {
			return $t;
		}
	}

	return '';
}

/**
 * 调用远程数据
 *
 * @param	string	$url
 * @param	intval	$timeout 超时时间，0不超时
 * @return	string
 */
function dr_catcher_data($url, $timeout = 0) {

	// 获取本地文件
	if (strpos($url, 'file://')  === 0) {
		return file_get_contents($url);
	}

	// curl模式
	if (function_exists('curl_init')) {
		$ch = curl_init($url);
		if (substr($url, 0, 8) == "https://") {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true); // 从证书中检查SSL加密算法是否存在
		}
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// 最大执行时间
		$timeout && curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		$data = curl_exec($ch);
		$code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		$errno = curl_errno($ch);
		if ($errno) {
			//log_message('error', '获取远程数据失败['.$url.']：（'.$errno.'）'.curl_error($ch));
		}
		curl_close($ch);
		if ($code == 200) {
			return $data;
		} elseif ($errno == 35) {
			// 当服务器不支持时改为普通获取方式
		} else {
			return '';
		}
	}

	//设置超时参数
	if ($timeout && function_exists('stream_context_create')) {
		// 解析协议
		$opt = array(
			'http' => array(
				'method'  => 'GET',
				'timeout' => $timeout,
			),
			'https' => array(
				'method'  => 'GET',
				'timeout' => $timeout,
			)
		);
		$ptl = substr($url, 0, 8) == "https://" ? 'https' : 'http';
		$data = @file_get_contents($url, 0, stream_context_create(array(
			$ptl => $opt[$ptl]
		)));
	} else {
		$data = @file_get_contents($url);
	}

	return $data;
}

/**
 * 递归创建文件夹
 */
function create_folder($dir, $mode = 0777){
	if (is_dir($dir) || mkdir($dir, $mode, true)) {
		return true;
	}
	if (!create_folder(dirname($dir), $mode)) {
		return false;
	}
	return mkdir($dir, $mode, true);
}

/**
 * 二维码调用
 */
function qrcode($text, $thumb = '', $level = 'H', $size = 5) {
	return APP_PATH.'api.php?op=qrcode&thumb='.urlencode($thumb).'&text='.urlencode($text).'&size='.$size.'&level='.$level;
}

// 格式化sql创建
function format_create_sql($sql) {
	$sql = trim(str_replace('ENGINE=InnoDB', 'ENGINE=MyISAM', $sql));
	$sql = trim(str_replace('CHARSET=utf8 ', 'CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci ', $sql));
	return $sql;
}

/**
 * 生成安全码
 */
function token($name = '') {
	if ($name) {
		return 'CMS'.md5($name.rand(1, 999999));
	} else {
		return 'CMS'.strtoupper(substr((md5(SYS_TIME)), rand(0, 10), 13));
	}
}
/**
 * 生成来路随机字符
 */
function asckey() {
	$s = strtoupper(base64_encode(md5(SYS_TIME).md5(rand(0, 20215).md5(rand(0, 2015)))).md5(rand(0, 2009)));
	return substr('CMS'.str_replace('=', '', $s), 0, 43);
}

/**
 * Function dataformat
 * 时间转换
  * @param $n INT时间
 */
function dataformat($n) {
	$hours = floor($n/3600);
	$minite	= floor($n%3600/60);
	$secend = floor($n%3600%60);
	$minite = $minite < 10 ? "0".$minite : $minite;
	$secend = $secend < 10 ? "0".$secend : $secend;
	if($n >= 3600){
		return $hours.":".$minite.":".$secend;
	}else{
		return $minite.":".$secend;
	}

}

/**
 * 秒转化时间
 */
function sec2time($times){
	$result = '00:00:00';
	if ($times > 0) {
		$hour = floor($times/3600);
		$minute = floor(($times-3600 * $hour)/60);
		$second = floor((($times-3600 * $hour) - 60 * $minute) % 60);
		strlen($hour) == 1 && $hour = '0'.$hour;
		strlen($minute) == 1 && $minute = '0'.$minute;
		strlen($second) == 1 && $second = '0'.$second;
		$result = $hour.':'.$minute.':'.$second;
	}
	return $result;
}

/**
* 传入日期格式或时间戳格式时间，返回与当前时间的差距，如1分钟前，2小时前，5月前，3年前等
* @param string or int $date 分两种日期格式"2013-12-11 14:16:12"或时间戳格式"1386743303"
* @param int $type
* @return string
*/
function formattime($date = 0, $type = 1) { //$type = 1为时间戳格式，$type = 2为date时间格式
    //date_default_timezone_set('PRC'); //设置成中国的时区
    switch ($type) {
        case 1:
            //$date时间戳格式
            $second = SYS_TIME - $date;
            $minute = floor($second / 60) ? floor($second / 60) : 1; //得到分钟数
            if ($minute >= 60 && $minute < (60 * 24)) { //分钟大于等于60分钟且小于一天的分钟数，即按小时显示
                $hour = floor($minute / 60); //得到小时数
            } elseif ($minute >= (60 * 24) && $minute < (60 * 24 * 30)) { //如果分钟数大于等于一天的分钟数，且小于一月的分钟数，则按天显示
                $day = floor($minute / ( 60 * 24)); //得到天数
            } elseif ($minute >= (60 * 24 * 30) && $minute < (60 * 24 * 365)) { //如果分钟数大于等于一月且小于一年的分钟数，则按月显示
                $month = floor($minute / (60 * 24 * 30)); //得到月数
            } elseif ($minute >= (60 * 24 * 365)) { //如果分钟数大于等于一年的分钟数，则按年显示
                $year = floor($minute / (60 * 24 * 365)); //得到年数
            }
            break;
        case 2:
            //$date为字符串格式 2013-06-06 19:16:12
            $date = strtotime($date);
            $second = SYS_TIME - $date;
            $minute = floor($second / 60) ? floor($second / 60) : 1; //得到分钟数
            if ($minute >= 60 && $minute < (60 * 24)) { //分钟大于等于60分钟且小于一天的分钟数，即按小时显示
                $hour = floor($minute / 60); //得到小时数
            } elseif ($minute >= (60 * 24) && $minute < (60 * 24 * 30)) { //如果分钟数大于等于一天的分钟数，且小于一月的分钟数，则按天显示
                $day = floor($minute / ( 60 * 24)); //得到天数
            } elseif ($minute >= (60 * 24 * 30) && $minute < (60 * 24 * 365)) { //如果分钟数大于等于一月且小于一年的分钟数，则按月显示
                $month = floor($minute / (60 * 24 * 30)); //得到月数
            } elseif ($minute >= (60 * 24 * 365)) { //如果分钟数大于等于一年的分钟数，则按年显示
                $year = floor($minute / (60 * 24 * 365)); //得到年数
            }
            break;
        default:
            break;
    }
    if (isset($year)) {
        return date('Y年m月d日',$date);
    } elseif (isset($month)) {
        return date('m月d日',$date);
    } elseif (isset($day)) {
        return $day . '天前';
    } elseif (isset($hour)) {
        return $hour . '小时前';
    } elseif (isset($minute)) {
        return $minute . '分钟前';
    }
}

function formatdate($time){
	$t = SYS_TIME - $time;
	$f = array(
		'31536000'=>'年',
		'2592000'=>'个月',
		'604800'=>'星期',
		'86400'=>'天',
		'3600'=>'小时',
		'60'=>'分钟',
		'1'=>'秒'
	);
	foreach ($f as $k=>$v) {
		if (0 !=$c=floor($t/(int)$k)) {
			$str = $c.$v.'前';
		}
	}
	if (!$str) {
		$str = '刚刚';
	}
	return $str;
}

function wordtime($time) {
	$time = (int) substr($time, 0, 10);
	$int = SYS_TIME - $time;
	$str = '';
	if ($int <= 2){
		$str = sprintf('刚刚', $int);
	}elseif ($int < 60){
		$str = sprintf('%d秒前', $int);
	}elseif ($int < 3600){
		$str = sprintf('%d分钟前', floor($int / 60));
	}elseif ($int < 86400){
		$str = sprintf('%d小时前', floor($int / 3600));
	}elseif ($int < 2592000){
		$str = sprintf('%d天前', floor($int / 86400));
	}elseif ($int < 31536000){
		$str = sprintf('%d个月前', floor($int / 2592000));
	}elseif ($int < 409968000){
		$str = sprintf('%d年前', floor($int / 31536000));
	}else{
		$str = date('Y-m-d H:i:s', $time);
	}
	return $str;
}

function mtime($time){
    //date_default_timezone_set('PRC'); //设置成中国的时区
    $now=SYS_TIME;
    $day=date('Y-m-d',$time);
    $today=date('Y-m-d');
     
    $dayArr=explode('-',$day);
    $todayArr=explode('-',$today);
     
    //距离的天数，这种方法超过30天则不一定准确，但是30天内是准确的，因为一个月可能是30天也可能是31天
    $days=($todayArr[0]-$dayArr[0])*365+(($todayArr[1]-$dayArr[1])*30)+($todayArr[2]-$dayArr[2]);
    //距离的秒数
    $secs=$now-$time;
    
    if($todayArr[0]-$dayArr[0]>0 && $days>3){//跨年且超过3天
        return date('Y-m-d H:i:s',$time);
    }else{
        if($days<1){//今天
            //if($secs<60)return $secs.'秒前';
            //elseif($secs<3600)return floor($secs/60)."分钟前";
            //else return floor($secs/3600)."小时前";
            $hour=date('H',$time);
            $minutes=date('i',$time);
            $seconds=date('s',$time);
            return "今天".$hour.':'.$minutes;
        }else if($days<2){//昨天
            $hour=date('H',$time);
            $minutes=date('i',$time);
            $seconds=date('s',$time);
            return "昨天".$hour.':'.$minutes;
        }elseif($days<3){//前天
            $hour=date('H',$time);
            $minutes=date('i',$time);
            $seconds=date('s',$time);
            return "前天".$hour.':'.$minutes;
        }else{//三天前
            return date('m-d H:i',$time);
        }
    }
}

function mdate($time = NULL) {
    //date_default_timezone_set('PRC'); //设置成中国的时区
    $text = '';
    $time = $time === NULL || $time > SYS_TIME ? SYS_TIME : intval($time);
    $t = SYS_TIME - $time; //时间差 （秒）
    $y = date('Y', $time)-date('Y', SYS_TIME);//是否跨年
    switch($t){
        case $t == 0:
            $text = '刚刚';
            break;
        case $t < 60:
            $text = $t . '秒前'; // 一分钟内
            break;
        case $t < 60 * 60:
            $text = floor($t / 60) . '分钟前'; //一小时内
            break;
        case $t < 60 * 60 * 24:
            $text = floor($t / (60 * 60)) . '小时前'; // 一天内
            break;
        case $t < 60 * 60 * 24 * 3:
            $text = floor($time/(60*60*24)) ==1 ?'昨天' : '前天' ; //昨天和前天
            break;
        case $t < 60 * 60 * 24 * 30:
            $text = date('m月d日', $time); //一个月内
            break;
        case $t < 60 * 60 * 24 * 365&&$y==0:
            $text = date('m月d日', $time); //一年内
            break;
        default:
            $text = date('Y年m月d日', $time); //一年以前
            break; 
    }
    return $text;
}

/**
 * 计算两个时间戳之间相差的日时分秒
 * @param string $begin_time 开始时间戳
 * @param string $end_time  结束时间戳
 * echo timediff('2016-12-04 11:40:00',date("Y-m-d H:i:s"))
 */
function timediff($begin_time,$end_time) {
	//date_default_timezone_set('PRC');
	$begin_time = strtotime($begin_time);
	$end_time = strtotime($end_time);
	if($begin_time < $end_time){
		$starttime = $begin_time;
		$endtime = $end_time;
	}else{
		$starttime = $end_time;
		$endtime = $begin_time;
	}
	//计算天数
	$timediff = $endtime-$starttime;
	$days = intval($timediff/86400);
	//计算小时数
	$remain = $timediff%86400;
	$hours = intval($remain/3600);
	//计算分钟数
	$remain = $remain%3600;
	$mins = intval($remain/60);
	//计算秒数
	$secs = $remain%60;
	//$res = $days."天".$hours."小时".$mins."分钟".$secs."秒";
	$res = $days.'天';
	$res .= $hours.'小时';
	$res .= $mins.'分';
	//$res .= $secs.'秒';
	return $res;
}

/**
 * 友好时间显示函数
 *
 * @param	int		$time	时间戳
 * @return	string
 */
function dr_fdate($sTime, $formt = 'Y-m-d') {
	if (!$sTime) {
		return '';
	}
	//sTime=源时间，cTime=当前时间，dTime=时间差
	$cTime = SYS_TIME;
	$dTime = $cTime - $sTime;
	$dDay = intval(date('z',$cTime)) - intval(date('z',$sTime));
	$dYear = intval(date('Y',$cTime)) - intval(date('Y',$sTime));
	if ($dYear > 0) {
		return date($formt, $sTime);
	}
	//n秒前，n分钟前，n小时前，日期
	if ($dTime < 60 ) {
		if ($dTime < 10) {
			return '刚刚';
		} else {
			return intval(floor($dTime / 10) * 10).'秒前';
		}
	} elseif ($dTime < 3600 ) {
		return intval($dTime/60).'分钟前';
	} elseif( $dTime >= 3600 && $dDay == 0  ){
		return intval($dTime/3600).'小时前';
	} elseif( $dDay > 0 && $dDay<=7 ){
		return intval($dDay).'天前';
	} elseif( $dDay > 7 &&  $dDay <= 30 ){
		return intval($dDay/7).'周前';
	} elseif( $dDay > 30 && $dDay < 180){
		return intval($dDay/30).'个月前';
	} elseif( $dDay >= 180 && $dDay < 360){
		return '半年前';
	} elseif ($dYear==0) {
		return date('m月d日', $sTime);
	} else {
		return date($formt, $sTime);
	}
}

/**
 * 时间显示函数
 *
 * @param	int		$time	时间戳
 * @param	string	$format	格式与date函数一致
 * @param	string	$color	当天显示颜色
 * @return	string
 */
function dr_date($time = NULL, $format = 'Y-m-d H:i:s', $color = NULL) {
	$time = (int)$time;
	if (!$time) {
		return '';
	}
	!$format && $format = 'Y-m-d H:i:s';
	$string = date($format, $time);
	return $color && $time >= strtotime(date('Y-m-d 00:00:00')) && $time <= strtotime(date('Y-m-d 23:59:59')) ? '<font color="' . $color . '">' . $string . '</font>' : $string;
}
?>