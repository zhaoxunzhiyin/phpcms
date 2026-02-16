<?php
/**
 *  global.func.php 公共函数库
 *
 * @copyright			(C) 2005-2021
 * @lastmodify			2021-06-06
 */

/**
 * 返回经addslashes处理过的字符串或数组
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function new_addslashes($string){
	if(!is_array($string)) return addslashes((string)$string);
	foreach($string as $key => $val) $string[$key] = new_addslashes($val);
	return $string;
}

/**
 * 返回经stripslashes处理过的字符串或数组
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function new_stripslashes($string) {
	if(!is_array($string)) return stripslashes((string)$string);
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
	if(!is_array($string)) return htmlspecialchars((string)$string,ENT_QUOTES,$encoding);
	foreach($string as $key => $val) $string[$key] = new_html_special_chars($val);
	return $string;
}

function new_html_entity_decode($string) {
	$encoding = 'utf-8';
	if(strtolower(CHARSET)=='gbk') $encoding = 'ISO-8859-15';
	return html_entity_decode((string)$string,ENT_QUOTES,$encoding);
}

function new_htmlentities($string) {
	$encoding = 'utf-8';
	if(strtolower(CHARSET)=='gbk') $encoding = 'ISO-8859-15';
	return htmlentities((string)$string,ENT_QUOTES,$encoding);
}

// html实体字符转换
function html2code($value) {
	return htmlspecialchars((string)$value);
}
// html实体字符转换
function code2html($value, $fk = false, $flags = '') {
	return html_code($value, $fk, $flags);
}
function html_code($value, $fk = false, $flags = '') {
	!$flags && $flags = ENT_QUOTES | ENT_HTML401 | ENT_HTML5;
	if ($fk) {
		// 将所有HTML实体转换为它们的适用字符
		return html_entity_decode((string)$value, $flags, 'UTF-8');
	}
	// 将特殊的HTML实体转换回字符
	return htmlspecialchars_decode((string)$value, $flags);
}

// 获取内容
function get_content($modelid = 0, $id = 0) {
	if (!$modelid || !$id) {
		return '';
	}
	return code2html(dr_value($modelid, $id, 'content'));
}

/**
 * 获取内容中的缩略图
 * @param $value 内容值
 * @param $num 指定获取数量
 * @return 在变量中提取img标签的图片路径到数组
 */
function get_content_img($value, $num = 0) {
	return get_content_url($value, 'src', 'gif|jpg|jpeg|png|webp', $num);
}

/**
 * 获取内容中的指定标签URL地址
 * @param $value 内容值
 * @param $attr 标签值，例如src
 * @param $ext 指定扩展名，例如jpg|gif
 * @param $num 指定获取数量
 * @return 在变量中提取img标签的图片路径到数组
 */
function get_content_url($value, $attr, $ext, $num = 0) {
	$rt = array();
	if (!$value) {
		return $rt;
	}
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
 * 提取描述信息过滤函数
 */
function dr_filter_description($value, $data = array(), $old = array()) {
	return dr_get_description($value, 0);
}

/**
 * 提取描述信息
 */
function dr_get_description($text, $limit = 0) {
	$rs = pc_base::load_sys_class('hooks')::trigger_callback('cms_get_description', $text);
	if ($rs && isset($rs['code']) && $rs['code'] && $rs['msg']) {
		$text = $rs['msg'];
	}
	if (!$limit) {
		$limit = 200;
	}
	return trim(str_cut(dr_rp(clearhtml($text), '　', ''), $limit, ''));
}

// 获取内容的搜索词
function dr_get_content_kws($value, $siteid = '', $catid = '', $modelid = -1, $mobile = 0) {
	if (is_array($value)) {
		return $value;
	} elseif (!$value) {
		return [];
	}
	!$siteid && $siteid = get_siteid();
	$rt = [];
	if ($catid) {
		$siteids = getcache('category_content','commons');
		$siteid = $siteids[$catid];
	}
	$tag = explode(',', $value);
	foreach ($tag as $t) {
		$t = trim($t);
		if ($t) {
			$rt[$t] = tag_url($t, $siteid, $catid, $modelid, $mobile);
		}
	}

	return $rt;
}

/**
 * 生成Tag URL
 */
function tag_url($keyword, $siteid = '', $catid = '', $modelid = -1, $mobile = 0){
	!$siteid && $siteid = get_siteid();
	if ($modelid >= 0) {
		return ($mobile ? dr_site_info('mobile_domain', $siteid) : APP_PATH).'index.php?m=search&c=index&a=init&typeid='.intval($modelid).'&siteid='.$siteid.'&keyword='.urlencode($keyword);
	}
	if ($catid) {
		return ($mobile ? dr_site_info('mobile_domain', $siteid) : APP_PATH).'index.php?m=content&c=search&a=init&catid='.$catid.'&info%5Bcatid%5D='.$catid.'&info%5Btypeid%5D=0&info%5Btitle%5D='.urlencode($keyword);
	}
	return ($mobile ? dr_site_info('mobile_domain', $siteid) : APP_PATH).'index.php?m=content&c=tag&a=lists&tag='.urlencode($keyword).'&siteid='.$siteid;
}

/**
 * 解析手机/终端分类url路径
 */
function list_url($url, $catid = '', $is_client = IS_CLIENT) {
	if (!$url || !$catid) {
		return '';
	}
	if (dr_cat_value($catid, 'type')==2) {
		return $url;
	}
	$input = pc_base::load_sys_class('input');
	$siteids = getcache('category_content','commons');
	$catid && $siteid = $siteids[$catid];
	!$catid && $siteid = $input->get('siteid') && (intval($input->get('siteid')) > 0) ? intval(trim($input->get('siteid'))) : get_siteid();
	if ($is_client) {
		return str_replace((string)dr_site_info('domain', $siteid), (string)CLIENT_URL, (string)$url);
	} elseif (dr_site_info('mobilehtml', $siteid)==1) {
		return str_replace((string)dr_site_info('domain', $siteid), (string)dr_site_info('mobile_domain', $siteid), (string)$url);
	} else {
		return dr_site_info('mobile_domain', $siteid).'index.php?m=content&c=index&a=lists&catid='.$catid;
	}
}

/**
 * 解析手机/终端内容url路径
 */
function show_url($url, $catid = '', $id = '', $is_client = IS_CLIENT) {
	if (!$url || !$catid || !$id) {
		return '';
	}
	if (dr_value(dr_cat_value($catid, 'modelid'), $id, 'islink')) {
		return $url;
	}
	$input = pc_base::load_sys_class('input');
	$siteids = getcache('category_content','commons');
	$catid && $siteid = $siteids[$catid];
	!$catid && $siteid = $input->get('siteid') && (intval($input->get('siteid')) > 0) ? intval(trim($input->get('siteid'))) : get_siteid();
	$setting = dr_string2array(dr_cat_value($catid, 'setting'));
	$content_ishtml = $setting['content_ishtml'];
	if ($is_client) {
		if ((strpos((string)$url,'http://') || strpos((string)$url,'https://')) && $content_ishtml) {
			return CLIENT_URL.$url;
		}
		return str_replace((string)dr_site_info('domain', $siteid), (string)CLIENT_URL, (string)$url);
	} elseif (dr_site_info('mobilehtml', $siteid)==1) {
		if ((strpos((string)$url,'http://') || strpos((string)$url,'https://')) && $content_ishtml) {
			if (!dr_site_info('mobilemode', $siteid)) {
				return SYS_MOBILE_ROOT.$url;
			} else {
				return substr(dr_site_info('mobile_domain', $siteid), 0, -1).$url;
			}
		}
		return str_replace((string)dr_site_info('domain', $siteid), (string)dr_site_info('mobile_domain', $siteid), (string)$url);
	} else {
		return dr_site_info('mobile_domain', $siteid).'index.php?m=content&c=index&a=show&catid='.$catid.'&id='.$id;
	}
}

/**
 * 字符串替换函数
 */
function dr_rp($str, $o, $t = '') {
	if (!$str || !$o) {
		return '';
	}
	return str_replace($o, $t, (string)$str);
}

/**
 * 两个变量判断是否有值并返回
 * @param $a 变量1
 * @param $b 变量2
 * @return $a 有值时返回$a 否则返回$b
 */
function dr_else_value($a, $b) {
	return dr_strlen($a) ? $a : $b;
}

/**
 * 模糊比较两个变量
 * @param $str1 变量1
 * @param $str2 变量2
 * @return 判断两个变量是否相等
 */
function dr_diff($str1, $str2) {
	if (is_array($str1) && is_array($str2)) {
		return array_diff($str1, $str2) ? false : true;
	} elseif (dr_strlen($str1) != dr_strlen($str2)) {
		return false;
	}
	return $str1 == $str2;
}

/**
 * 返回包含数组中所有键名的一个新数组
 * @param $array 指定数组
 * @param $value 具体值
 * @param $strict 严格比较
 * @return 返回包含数组中所有键名的一个新数组
 */
function dr_array_keys($array, $value = '', $strict = false) {
	if (!$array || !is_array($array)) {
		return 0;
	}
	if ($value) {
		return array_keys($array, $value, $strict);
	} else {
		return array_keys($array);
	}
}

/**
 * 返回包含数组中指定键名的对应值
 * @param $array 指定数组
 * @param $key 数组key
 * @return 返回包含数组中指定键名的对应值
 */
function dr_array_value($array, $key) {
	if (!$array || !is_array($array)) {
		return NULL;
	} elseif (is_array($key)) {
		return NULL;
	} elseif (isset($array[$key])) {
		return $array[$key];
	} else {
		return NULL;
	}
}

/**
 * 两个数组比较
 * @param $arr1 指定数组1
 * @param $arr2 指定数组2
 * @return 比较两个数组的键值,并返回交集
 */
function dr_array_intersect($arr1, $arr2) {
	if (!is_array($arr1) || !is_array($arr2)) {
		return false;
	}
	return array_intersect($arr1, $arr2);
}

/**
 * 两个数组比较
 * @param $arr1 指定数组1
 * @param $arr2 指定数组2
 * @return 比较两个数组的键名,并返回交集
 */
function dr_array_intersect_key($arr1, $arr2) {
	if (!is_array($arr1) || !is_array($arr2)) {
		return false;
	}
	return array_intersect_key($arr1, $arr2);
}

/**
 * 通过数组值查找数组key
 * @param $array 数组
 * @param $value 指定键值
 * @return 返回键值对应的键名
 */
function dr_get_array_key($array, $value) {
	if ($array && !in_array($value, $array)) {
		return false;
	}
	$new = array_flip($array);
	return isset($new[$value]) ? $new[$value] : false;
}

/**
 * 提取关键字
 */
function dr_get_keyword_data($title, $content) {
	if (!$title) {
		return dr_return_data(0, '分词接口-没有获取标题');
	}
	if (!(int)SYS_BAIDU_QCNUM) {
		return dr_return_data(0, '分词接口-没有分词数量');
	}
	if (SYS_KEYWORDAPI == 1) {
		$cfg = array('id'=>SYS_BAIDU_AID, 'ak'=>SYS_BAIDU_SKEY, 'sk'=>SYS_BAIDU_ARCRETKEY);
		if (!isset($cfg['id']) || !isset($cfg['ak']) || !isset($cfg['sk'])) {
			log_message('error', '百度ai插件-分词接口配置没有成功');
			return dr_return_data(0, '百度ai插件-分词接口配置没有成功');
		}
		require_once PC_PATH.'plugin/baiduapi/AipNlp.php';
		$client = new AipNlp($cfg['id'], $cfg['ak'], $cfg['sk']);
		$rt = $client->keyword(str_cut($title, 30), $content);
		if (isset($rt['error_code']) && $rt['error_code']) {
			CI_DEBUG && log_message('error', '错误代码（'.$rt['error_code'].'）：'.$rt['error_msg']);
			return dr_return_data(0, '错误代码（'.$rt['error_code'].'）：'.$rt['error_msg']);
		} elseif ($rt && $rt['items']) {
			$n = 0;
			$tag = array();
			foreach ($rt['items'] as $t) {
				$tag[] = $t['tag'];
				$n++;
				if( $n >= (int)SYS_BAIDU_QCNUM ) break;
			}
			return dr_return_data(1, implode(',', $tag));
			
		}
		log_message('error', '百度ai插件-没有分析出关键词');
		return dr_return_data(0, '百度ai插件-没有分析出关键词', $rt);
	} else if (SYS_KEYWORDAPI == 2) {
		$XAppid = SYS_XUNFEI_AID;
		$Apikey = SYS_XUNFEI_SKEY;
		if (!isset($XAppid) || !isset($Apikey)) {
			log_message('error', '讯飞接口-分词接口配置没有成功');
			return dr_return_data(0, '讯飞接口-分词接口配置没有成功');
		}
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
					'text' => $title,
				)),
				'timeout' => 15*60
			)
		))), true);
		if (!$rt) {
			log_message('error', '讯飞接口访问失败');
			return dr_return_data(0, '讯飞接口访问失败');
		} elseif ($rt['code']) {
			log_message('error', '讯飞接口: '.$rt['desc']);
			return dr_return_data(0, '讯飞接口: '.$rt['desc']);
		} else {
			$n = 0;
			$resultstr = '';
			foreach ($rt['data']['ke'] as $t) {
				$resultstr .= ','.$t['word'];
				$n++;
				if( $n >= (int)SYS_BAIDU_QCNUM ) break;
			}
			return dr_return_data(1, trim($resultstr, ','));
		}
		log_message('error', '讯飞接口-没有分析出关键词');
		return dr_return_data(0, '讯飞接口-没有分析出关键词', $rt);
	} else {
		$phpanalysis = pc_base::load_sys_class('phpanalysis');
		$phpanalysis = new phpanalysis('utf-8', 'utf-8', false);
		$phpanalysis->LoadDict();
		$phpanalysis->SetSource($title);
		$phpanalysis->StartAnalysis(true);
		$rt = $phpanalysis->GetFinallyKeywords((int)SYS_BAIDU_QCNUM);
		if (!$rt) {
			log_message('error', '本地接口-没有分析出关键词');
			return dr_return_data(0, '本地接口-没有分析出关键词');
		} else {
			return dr_return_data(1, $rt);
		}
	}
}

/**
 * 提取关键字
 */
function dr_get_keywords($title, $content = '') {
	if (!$title) {
		return '';
	}
	$content = isset($content) && $content ? $content : $title;
	$rs = pc_base::load_sys_class('hooks')::trigger_callback('cms_get_keywords', $title, $content);
	if ($rs && isset($rs['code']) && $rs['code'] && $rs['msg']) {
		return $rs['msg'];
	}
	$rt = dr_get_keyword_data($title, $content);
	if (!$rt['code']) {
		return '';
	}
	return $rt['msg'];
}

/**
 * 语音验证码
 */
function dr_get_merge($code) {
	if (!$code) {
		return '';
	}
	header('Content-Type: audio/mpeg');
	$str = '';
	$setting = getcache('common','commons');
	$sysadmincodevoicemodel = isset($setting['sysadmincodevoicemodel']) ? (int)$setting['sysadmincodevoicemodel'] : 0;
	if ($sysadmincodevoicemodel==1) {
		$voice = '_1';
	} else if ($sysadmincodevoicemodel==2) {
		$voice = '_2';
	} else {
		$voice = '';
	}
	for ($i = 0; $i < dr_strlen($code); $i++) {
		if (is_numeric(strtolower(substr($code,$i,1)))) {
			$file = PC_PATH.'libs/data/voice/'.mt_rand(1, 4).'_'.strtolower(substr($code,$i,1)).'.mp3';
			$size = filesize($file);
			$str .= fread(fopen($file, 'r'), $size);
		} else {
			$file = PC_PATH.'libs/data/voice/'.strtolower(substr($code,$i,1)).$voice.'.mp3';
			$size = filesize($file);
			$str .= fread(fopen($file, 'r'), $size);
		}
	}
	return $str;
}

/**
 * 是否为空白
 * @return 是否空白
 */
function dr_is_empty($value) {
	if (is_array($value)) {
		return $value ? 0 : 1;
	}
	return strlen((string)$value) ? 0 : 1;
}

/**
 * 安全url过滤
 */
function dr_safe_url($url, $is_html = false) {
	if (!$url) {
		return '';
	}
	$url = trim(pc_base::load_sys_class('security')->xss_clean((string)$url, true));
	$url = str_ireplace(['<iframe', '<', '/>'], '', $url);
	if ($is_html) {
		$url = htmlspecialchars($url);
	}
	return $url;
}

/**
 * 判断存在于数组中
 * @param $var|array 指定值或数组
 * @param $array 指定数组
 * @return 判断$var是否存在于数组$array中
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
	return strlen((string)$string);
}

// 兼容统计
function dr_count($array_or_countable, $mode = COUNT_NORMAL){
	return is_array($array_or_countable) || is_object($array_or_countable) ? count($array_or_countable, $mode) : 0;
}

// 是否是完整的url
function dr_is_url($url) {
	if (!$url) {
		return false;
	} elseif (strpos((string)$url, 'http://') === 0) {
		return true;
	} elseif (strpos((string)$url, 'https://') === 0) {
		return true;
	}
	return false;
}

/**
 * 处理带Emoji的数据，HTML转为emoji码
 * @param $msg  转换字符串
 * @return 新的字符串
 */
function dr_html2emoji($msg){
	if (!$msg) {
		return '';
	}
	if (substr($msg, 0, 1) == '"' && substr($msg, -1, 1) == '"') {
		$txt = json_decode(str_replace('|', '\\', $msg));
		if ($txt !== NULL) {
			$msg = $txt;
		}
		return trim($msg, '"');
	} else {
		return $msg;
	}
}

/**
 * 过滤emoji表情
 * @param type $str
 * @return 新的字符串
 */
function dr_clear_emoji($str){
	if (!$str) {
		return '';
	}
	return dr_clear_empty(dr_html2emoji(preg_replace_callback('/[\xf0-\xf7].{3}/', function($r) { return '';}, $str)));
}

/**
 * 字符是否包含
 * @param $string 原字符串
 * @param $key 查询的字符串
 * @return 返回$string中是否包含$key，区分大小写
 */
function dr_strpos($string, $key) {
	return strpos((string)$string, $key);
}

/**
 * 字符是否包含
 * @param $string 原字符串
 * @param $key 查询的字符串
 * @return 返回$string中是否包含$key，不区分大小写
 */
function dr_stripos($string, $key) {
	return stripos((string)$string, $key);
}

/**
 * 返回图标
 * @param $value 原定的图标
 * @return 如没有原地图标就返回默认图标
 */
function dr_icon($value) {
	return $value ? $value : 'fa fa-table';
}

/**
 * 数组随机排序，并截取数组
 * @param $arr
 * @param $num 数量
 * @return 新数组
 */
function dr_array_rand($arr, $num = 0) {
	if (!$arr or !is_array($arr)) {
		return [];
	}
	shuffle($arr);
	return $num ? dr_arraycut($arr, $num) : $arr;
}

/**
 * 数组的指定元素大小排序
 * @param $arr
 * @param $key KEY键名
 * @param $type 排序方式 asc desc
 * @return 新数组
 */
function dr_array_sort($arr, $key, $type = 'asc') {
	if (!is_array($arr)) {
		return array();
	}
	uasort($arr, function($a, $b) use ($key, $type) {
		if (!isset($a[$key])) {
			return 0;
		} elseif ($a[$key] == $b[$key]) {
			return 0;
		}
		if ($type == 'asc') {
			return ($a[$key] < $b[$key]) ? -1 : 1;
		} else {
			return ($a[$key] > $b[$key]) ? -1 : 1;
		}
	});
	return $arr;
}

function dr_get_param_var($return, $param = []) {
	if (!$param) {
		return $return;
	}
	if (!is_array($param)) {
		$param = [$param];
	}
	foreach ($param as $v) {
		$var = (!$v ? 0 : dr_safe_replace($v));
		if (isset($return[$var])) {
			$return = $return[$var];
		} else {
			return null;
		}
	}
	return $return;
}

/**
 * 完整的文件URL
 * @param $url 文件参数
 * @return 返回文件的完整url地址
 */
function dr_file($url) {
	if (!$url || dr_strlen($url) == 1) {
		return '';
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
	} elseif (is_array($id)) {
		return IS_DEV ? '文件参数不能是数组' : '';
	}
	if (is_numeric($id)) {
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
 * 文件下载地址
 */
function dr_down_file($id, $name = '') {

	if (!$id) {
		return IS_DEV ? '文件参数不能为空' : '';
	} elseif (is_array($id)) {
		return IS_DEV ? '文件参数不能是数组' : '';
	}

	if (defined('IS_HTML') && IS_HTML) {
		return WEB_PATH.'index.php?m=content&c=down&a=down&id='.$id.'&name='.urlencode($name);
	}

	$sn = md5($id);
	pc_base::load_sys_class('cache')->set_auth_data('down-file-'.$sn, [
		'id' => $id,
		'name' => $name,
	]);

	return WEB_PATH.'index.php?m=content&c=down&a=down&id='.$sn;
}

/**
 * 格式化多文件数组
 * @param $value json字符
 * @param $limit 限定返回几个值
 * @return 格式化多文件数组
 */
function dr_get_files($value, $limit = '') {
	$data = array();
	$value = dr_string2array($value, $limit);
	if (!$value) {
		return $data;
	}
	foreach ($value as $i => $file) {
		if ($file) {
			$id = $file['id'] ? $file['id'] : $file['file'];
			$data[] = [
				'url' => dr_get_file($id), // 对应文件的url
				'file' => $id, // 对应文件或附件id
				'title' => $file['title'], // 对应标题
				'description' => $file['description'], // 对应描述
			];
		}
	}
	return $data;
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
		$remote = get_cache('attachment', $data['remote']);
		if ($remote) {
			return $remote['url'].$data['filepath'];
		} else {
			return IS_DEV ? '自定义附件（'.$data['remote'].'）的配置已经不存在' : '';
		}
	} elseif ($w && $h && dr_is_image($data['fileext'])) {
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

// 生成目录式手机目录
function update_mobile_webpath($path, $mobile_dirname, $dirname, $siteid = 0) {
	$nodir_arr = array('admin','api','caches','cms','html','login','statics','uploadfile');
	if($siteid==1 && in_array($mobile_dirname,$nodir_arr)) {
		return '不能使用CMS默认目录名（admin，api，caches，cms，login，html，statics，uploadfile）！';
	}
	foreach (array('api.php', 'index.php') as $file) {
		if (is_file(TEMPPATH.'web/mobile/'.$file)) {
			$dst = $path.$mobile_dirname.'/'.$file;
			dr_mkdirs(dirname($dst));
			$fix_web_dir = (isset($dirname) && $dirname ? $dirname.'/' : '').$mobile_dirname;
			$size = file_put_contents($dst, str_replace(array(
				'{FIX_WEB_DIR}'
			), array(
				$fix_web_dir
			), file_get_contents(TEMPPATH.'web/mobile/'.$file)));
			if (!$size) {
				return '文件['.$dst.']无法写入';
			}
		}
	}
	return;
}

/**
 * 上传移动文件
 */
function dr_move_uploaded_file($tempfile, $fullname) {
	$contentType = $_SERVER['CONTENT_TYPE'] ?? getenv('CONTENT_TYPE');
	if ($contentType && $_SERVER['HTTP_CONTENT_RANGE']
		&& strpos($contentType, 'multipart') !== false && strpos($_SERVER['HTTP_CONTENT_RANGE'], 'bytes') === 0) {

		// 命名一个新名称
		$value = str_replace('bytes ', '', $_SERVER['HTTP_CONTENT_RANGE']);
		list($str, $total) = explode('/', $value);
		list($str, $max) = explode('-', $str);

		// 分段名称
		$temp_file = dirname($fullname).'/'.md5($_SERVER['HTTP_CONTENT_DISPOSITION']);
		if ($total - $max < 1024) {
			// 减去误差表示分段上传完毕
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
			echo file_put_contents($temp_file, file_get_contents($tempfile), FILE_APPEND);exit;
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
	return dr_safe_replace($string);
}
/**
 * 安全过滤函数
 */
function dr_safe_replace($string, $diy = array()) {
	if (dr_is_empty($string)) {
		return '';
	}
	$replace = array('%20', '%27', '%2527', '*', "'", '"', ';', '<', '>', "{", '}');
	$diy && is_array($diy) && $replace = dr_array2array($replace, $diy);
	$diy && !is_array($diy) && $replace[] = $diy;
	return str_replace($replace, '', (string)$string);
}
/**
 * 安全过滤文件及目录名称函数
 */
function dr_safe_filename($string) {
	if (dr_is_empty($string)) {
		return '';
	}
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
	if (dr_is_empty($string)) {
		return '';
	}
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
	if (dr_is_empty($string)) {
		return '';
	} elseif (strlen((string)$string) > 100) {
		return substr((string)$string, 0, 100);
	}
	return trim((string)$string);
}
/**
 * 将路径进行安全转换变量模式
 */
function dr_safe_replace_path($path) {
	return str_replace(
		array(
			CONFIGPATH,
			CACHE_PATH,
			TPLPATH,
			PC_PATH,
			CMS_PATH,
		),
		array(
			'CONFIGPATH/',
			'CACHE_PATH/',
			'TPLPATH/',
			'PC_PATH/',
			'CMS_PATH/',
		),
		$path
	);
}
/**
 * 清除空白字符
 */
function dr_clear_empty($value) {
	return str_replace(array('　', ' '), '', trim($value));
}

/**
 * 列表字段进行排序筛选
 */
function dr_list_field_order($field) {
	if (!$field) {
		return array();
	}
	$rt = array();
	foreach ($field as $name => $m) {
		$m['use'] && $rt[$name] = $m;
	}
	return $rt;
}
function dr_list_field_value($value, $sys_field, $field) {
	foreach ($field as $t) {
		$t && $sys_field[$t['field']] = $t;
	}
	$rt = array();
	foreach ($value as $name => $t) {
		if ($t && $t['name']) {
			$rt[$name] = $sys_field[$name];
			unset($sys_field[$name]);
		}
	}
	if (!$sys_field) {
		return $rt;
	}
	foreach ($sys_field as $name => $t) {
		$rt[$name] = $t;
	}
	return $rt;
}
/**
 * 系统内置字段
 */
function sys_field($field) {
	$system = array(
		'id' => array(
			'name' => L('Id'),
			'formtype' => 'text',
			'field' => 'id',
			'setting' => array()
		),
		'dataid' => array(
			'name' => L('Id'),
			'formtype' => 'text',
			'field' => 'dataid',
			'setting' => array()
		),
		'content' => array(
			'name' => L('内容'),
			'formtype' => 'editor',
			'field' => 'content',
			'setting' => array()
		),
		'title' => array(
			'name' => L('主题'),
			'formtype' => 'title',
			'field' => 'title',
			'setting' => array()
		),
		'thumb' => array(
			'name' => L('缩略图'),
			'formtype' => 'image',
			'field' => 'thumb',
			'setting' => array()
		),
		'catid' => array(
			'name' => L('栏目'),
			'formtype' => 'text',
			'field' => 'catid',
			'setting' => array()
		),
		'userid' => array(
			'name' => L('账号Id'),
			'ismain' => 1,
			'ismember' => 1,
			'formtype' => 'text',
			'field' => 'userid',
			'setting' => array()
		),
		'username' => array(
			'name' => L('用户名'),
			'formtype' => 'text',
			'field' => 'username',
			'setting' => array()
		),
		'inputtime' => array(
			'name' => L('发布时间'),
			'formtype' => 'datetime',
			'field' => 'inputtime',
			'setting' => array()
		),
		'updatetime' => array(
			'name' => L('更新时间'),
			'formtype' => 'datetime',
			'field' => 'updatetime',
			'setting' => array()
		),
		'datetime' => array(
			'name' => L('时间'),
			'formtype' => 'datetime',
			'field' => 'datetime',
			'setting' => array()
		),
		'ip' => array(
			'name' => L('用户ip'),
			'formtype' => 'text',
			'field' => 'ip',
			'setting' => array()
		),
		'listorder' => array(
			'name' => L('排列值'),
			'formtype' => 'number',
			'field' => 'listorder',
			'setting' => array()
		),
		'hits' => array(
			'name' => L('浏览数'),
			'formtype' => 'number',
			'field' => 'hits',
			'setting' => array()
		),
	);
	$rt = array();
	foreach ($field as $name) {
		$rt[$name] = $system[$name];
	}
	return $rt;
}
/**
 * 执行函数
 */
function dr_list_function($func, $value, $param = array(), $data = array(), $field = array(), $name = '') {
	if (!$func) {
		$dfunc = array(
			'userid' => 'userid',
			'author' => 'author',
			'groupid' => 'group',
			'datetime' => 'datetime',
			'editor' => 'content',
			'image' => 'image',
			'images' => 'images',
			'file' => 'file',
			'downfiles' => 'files',
			'box' => 'checkbox_name',
			'linkage' => 'linkage_name',
		);
		$dname = array(
			'title' => 'title',
			'catid' => 'catid',
			'author' => 'author',
			'username' => 'author',
			'groupid' => 'group',
			'avatar' => 'avatar',
			'hits' => 'hits',
			'status' => 'status',
			'listorder' => 'save_text_value',
		);
		if ($name && isset($dname[$name]) && $dname[$name]) {
			$func = $dname[$name];
		} elseif ($field['formtype'] && isset($dfunc[$field['formtype']]) && $dfunc[$field['formtype']]) {
			$func = $dfunc[$field['formtype']];
		} elseif (dr_is_empty($value)) {
			return '';
		} else {
			return html2code($value);
		}
	}
	$obj = pc_base::load_sys_class('function_list');
	if (method_exists($obj, $func)) {
		return call_user_func_array(array($obj, $func), array($value, $param, $data, $field));
	} elseif (function_exists($func)) {
		return call_user_func_array($func, array($value, $param, $data, $field));
	} else {
		log_message('debug', '你没有定义字段列表回调函数：'.$func);
	}

	return html2code($value);
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
 * 判断是否为数字类型
 *
 * @param    $num     数字类型
 * @param   $lang     长度范围之外时直接范围false
 * @return  如果成功则返回 TRUE，失败则返回 FALSE
 */
function dr_is_numeric($num, $lang = 10) {
	if (dr_strlen($num) > $lang) {
		return false;
	}
	if (is_numeric($num)) {
		if (substr($num, 0, 1) == 0) {
			// 0开头的不作为数字类处理
			return false;
		}
		if (preg_match('/^[0-9]+$/', $num)) {
			return true;
		}
	}
	return false;
}
/**
 * 静态生成时权限认证字符(加密)
 * ip 运行者ip地址
 */
function dr_html_auth($ip = 0) {
	$cache = pc_base::load_sys_class('cache');
	if ($ip) {
		// 存储值
		return $cache->set_auth_data(md5('html_auth'.(strlen($ip) > 5 ? $ip : ip())), 1);
	} else {
		// 读取判断
		$rt = $cache->get_auth_data(md5('html_auth'.ip()));
		if ($rt) {
			return 1; // 有效
		} else {
			return 0;
		}
	}
}
// 验证后台用户身份权限
function cleck_admin($roleid) {
	if (is_array(dr_string2array($roleid))) {
		if (dr_in_array(1, dr_string2array($roleid))) {
			return 1;
		}
	} else {
		if ($roleid==1) {
			return 1;
		}
	}
	return 0;
}
// 判断用户前端权限
function check_member_auth($groupid, $catid, $action) {
	$priv_db = pc_base::load_model('category_priv_model');
	if (!$priv_db->get_one(array('catid'=>$catid, 'roleid'=>$groupid, 'is_admin'=>0, 'action'=>$action))) {
		return 0;
	}
	return 1;
}
/**
 * 删除目录下面的所有文件
 */
function dr_file_delete($path, $del_dir = false, $htdocs = false, $_level = 0) {
	if (!$path) {
		return false;
	}
	$path = rtrim($path, '/\\');
	if (! $currentDir = @opendir($path)) {
		return false;
	}
	while (false !== ($filename = @readdir($currentDir))) {
		if ($filename !== '.' && $filename !== '..') {
			if (is_dir($path . DIRECTORY_SEPARATOR . $filename) && $filename[0] !== '.') {
				dr_file_delete($path . DIRECTORY_SEPARATOR . $filename, $del_dir, $htdocs, $_level + 1);
			} elseif ($htdocs !== true || ! preg_match('/^(\.htaccess|index\.(html|htm|php)|web\.config)$/i', $filename)) {
				@unlink($path . DIRECTORY_SEPARATOR . $filename);
			}
		}
	}
	closedir($currentDir);
	return ($del_dir === true && $_level > 0) ? @rmdir($path) : true;
}
/**
 * 删除目录及目录下面的所有文件
 *
 * @param   string  $dir        路径
 * @param   string  $is_all     包括删除当前目录
 * @return  bool    如果成功则返回 TRUE，失败则返回 FALSE
 */
function dr_dir_delete($path, $del_dir = FALSE, $htdocs = FALSE, $_level = 0) {
	if (!$path) {
		return false;
	}
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
// 颜色选取
function color_select($name, $color, $width = '100%') {
	// 表单宽度设置
	$width = is_mobile() ? '100%' : ($width ? $width : '100%');
	// 风格
	$style = ' style="width:'.$width.(is_numeric($width) ? 'px' : '').';"';
	$id = preg_match("/\[(.*)\]/", $name, $m) ? $m[1] : $name;
	$str = load_css(JS_PATH.'jquery-minicolors/jquery.minicolors.css');
	$str .= load_js(JS_PATH.'jquery-minicolors/jquery.minicolors.min.js');
	$str.= '
	<input type="text" class="form-control color input-text" name="'.$name.'" id="dr_'.$id.'"'.$style.' value="'.$color.'">';
	$str.= '
<script type="text/javascript">
$(function(){
	$("#dr_'.$id.'").minicolors({
		control: $("#dr_'.$id.'").attr("data-control") || "hue",
		defaultValue: $("#dr_'.$id.'").attr("data-defaultValue") || "",
		inline: "true" === $("#dr_'.$id.'").attr("data-inline"),
		letterCase: $("#dr_'.$id.'").attr("data-letterCase") || "lowercase",
		opacity: $("#dr_'.$id.'").attr("data-opacity"),
		position: $("#dr_'.$id.'").attr("data-position") || "bottom left",
		change: function(t, o) {
			t && (o && (t += ", " + o), "object" == typeof console && console.log(t));
		},
		theme: "bootstrap"
	});
});
</script>';
	return $str;
}
/**
 * 附件存储策略
 * @return  string
 */
function attachment($option, $table = 0) {

	$id = isset($option['attachment']) ? $option['attachment'] : 0;
	$remote = get_cache('attachment');

	$html = '<label><select class="form-control" name="setting[attachment]">';
	if (SYS_ATTACHMENT_SAVE_ID && isset($remote[SYS_ATTACHMENT_SAVE_ID])) {
		$html.= '<option value="0"> '.L($remote[SYS_ATTACHMENT_SAVE_ID]['name']).' </option>';
	} else {
		$html.= '<option value="0"> '.L('默认存储').' </option>';
	}

	if ($remote) {
		foreach ($remote as $i => $t) {
			if (SYS_ATTACHMENT_SAVE_ID && $t['id'] == SYS_ATTACHMENT_SAVE_ID) {
				continue;
			}
			$html.= '<option value="'.$i.'" '.($i == $id ? 'selected' : '').'> '.L($t['name']).' </option>';
		}
	}

	$html.= '</select></label>';
	if ($table) {
		return '<tr>
    <td>'.L('附件存储策略').' </td>
    <td>
        '.$html.'
        <span class="help-block">'.L('远程附件存储建议设置小文件存储，推荐10MB内，大文件会导致数据传输失败').'</span>
    </td>
</tr><tr>
    <td>'.L('图片压缩大小').' </td>
    <td>
        <label><input type="text" class="form-control" value="'.$option['image_reduce'].'" name="setting[image_reduce]"></label>
        <span class="help-block">'.L('填写图片宽度，例如1000，表示图片大于1000px时进行压缩图片').'</span>
    </td>
</tr>';
	} else {
		return '<div class="form-group">
    <label class="col-md-2 control-label">'.L('附件存储策略').' </label>
    <div class="col-md-9">
        '.$html.'
        <span class="help-block">'.L('远程附件存储建议设置小文件存储，推荐10MB内，大文件会导致数据传输失败').'</span>
    </div>
</div><div class="form-group">
    <label class="col-md-2 control-label">'.L('图片压缩大小').' </label>
    <div class="col-md-9">
        <label><input type="text" class="form-control" value="'.$option['image_reduce'].'" name="setting[image_reduce]"></label>
        <span class="help-block">'.L('填写图片宽度，例如1000，表示图片大于1000px时进行压缩图片').'</span>
    </div>
</div>';
	}
}
/**
 * 附件存储策略
 * @return  string
 */
function local_attachment($option, $table = 0) {

	$id = isset($option['local_attachment']) ? $option['local_attachment'] : 0;
	$remote = get_cache('attachment');

	$html = '<label><select class="form-control" name="setting[local_attachment]">';
	if (SYS_ATTACHMENT_SAVE_ID && isset($remote[SYS_ATTACHMENT_SAVE_ID])) {
		$html.= '<option value="0"> '.L($remote[SYS_ATTACHMENT_SAVE_ID]['name']).' </option>';
	} else {
		$html.= '<option value="0"> '.L('默认存储').' </option>';
	}

	if ($remote) {
		foreach ($remote as $i => $t) {
			if (SYS_ATTACHMENT_SAVE_ID && $t['id'] == SYS_ATTACHMENT_SAVE_ID) {
				continue;
			}
			$html.= '<option value="'.$i.'" '.($i == $id ? 'selected' : '').'> '.L($t['name']).' </option>';
		}
	}

	$html.= '</select></label>';
	if ($table) {
		return '<tr>
    <td>'.L('本地附件存储策略').' </td>
    <td>
        '.$html.'
        <span class="help-block">'.L('远程附件存储建议设置小文件存储，推荐10MB内，大文件会导致数据传输失败').'</span>
    </td>
</tr><tr>
    <td>'.L('本地图片压缩大小').' </td>
    <td>
        <label><input type="text" class="form-control" value="'.$option['local_image_reduce'].'" name="setting[local_image_reduce]"></label>
        <span class="help-block">'.L('填写图片宽度，例如1000，表示图片大于1000px时进行压缩图片').'</span>
    </td>
</tr>';
	} else {
		return '<div class="form-group">
    <label class="col-md-2 control-label">'.L('本地附件存储策略').' </label>
    <div class="col-md-9">
        '.$html.'
        <span class="help-block">'.L('远程附件存储建议设置小文件存储，推荐10MB内，大文件会导致数据传输失败').'</span>
    </div>
</div><div class="form-group">
    <label class="col-md-2 control-label">'.L('本地图片压缩大小').' </label>
    <div class="col-md-9">
        <label><input type="text" class="form-control" value="'.$option['local_image_reduce'].'" name="setting[local_image_reduce]"></label>
        <span class="help-block">'.L('填写图片宽度，例如1000，表示图片大于1000px时进行压缩图片').'</span>
    </div>
</div>';
	}
}
/**
 * 联动菜单
 * @return  string
 */
function linkage($option, $table = 0) {
	$db = pc_base::load_model('linkage_model');
	$str = '<select class="form-control" name="setting[linkage]">';
	$data = $db->select();
	if ($data) {
		$linkage = isset($option['linkage']) ? $option['linkage'] : '';
		foreach ($data as $t) {
			$str.= '<option value="'.$t['code'].'" '.($linkage == $t['code'] ? 'selected' : '').'> '.$t['name'].'（'.$t['code'].'） </option>';
		}
	}
	$str.= '</select>';
	if ($table) {
		return '<tr>
    <td>'.L('选择菜单').' </td>
    <td>
        <label>'.$str.'</label>
</td>
</tr>';
	} else {
		return '<div class="form-group">
    <label class="col-md-2 control-label">'.L('选择菜单').' </label>
    <div class="col-md-9">
        <label>'.$str.'</label>
    </div>
</div>';
	}
}
/**
 * xss过滤函数
 *
 * @param $string
 * @return string
 */
function remove_xss($string) {
	return pc_base::load_sys_class('security')->xss_clean((string)$string, true);
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
 		$str = preg_replace('/\<([\/]?)script([^\>]*?)\>/si', '&lt;\\1script\\2&gt;', $str);
		$str = preg_replace('/\<([\/]?)iframe([^\>]*?)\>/si', '&lt;\\1iframe\\2&gt;', $str);
		$str = preg_replace('/\<([\/]?)frame([^\>]*?)\>/si', '&lt;\\1frame\\2&gt;', $str);
		$str = str_replace('javascript:', 'javascript：', $str);
 	}
	return $str;
}
/**
 * 存储调试信息
 * file 存储文件
 * data 打印变量
 */
function dr_debug($file, $data) {
	dr_mkdirs(CACHE_PATH.'debuglog/');
	$debug = debug_backtrace();
	file_put_contents(CACHE_PATH.'debuglog/'.dr_safe_filename($file).'.txt', var_export(array(
		'时间' => dr_date(SYS_TIME, 'Y-m-d H:i:s'),
		'终端' => (string)$_SERVER['HTTP_USER_AGENT'],
		'文件' => $debug[0]['file'],
		'行号' => $debug[0]['line'],
		'地址' => FC_NOW_URL,
		'变量' => $data,
	), true).PHP_EOL.'=========================================================='.PHP_EOL, FILE_APPEND);
}
/**
 * 转为utf8编码格式
 * $str 来源字符串
 */
function dr_code2utf8($str) {
	if (function_exists('mb_convert_encoding')) {
		return mb_convert_encoding($str, 'UTF-8', 'GBK');
	} elseif (function_exists('iconv')) {
		return iconv('GBK', 'UTF-8', $str);
	}
	return $str;
}
// 兼容性判断
if (!function_exists('gethostbyname')) {
	function gethostbyname($domain) {
		return $domain;
	}
}
// 兼容性判断
if (!function_exists('ctype_digit')) {
	function ctype_digit($num) {
		if (strpos($num, '.') !== FALSE) {
			return false;
		}
		return is_numeric($num);
	}
}
// 兼容性判断
if (!function_exists('ctype_alpha')) {
	function ctype_alpha($num) {
		if (strpos($num, '.') !== FALSE) {
			return false;
		}
		return is_numeric($num);
	}
}
// 兼容性判断
if (!function_exists('mb_strlen')) {
	function mb_strlen($str) {
		return strlen($str);
	}
}
// 兼容性判断
if (!function_exists('array_key_first')) {
	function array_key_first(array $arr) {
		foreach($arr as $key => $unused) {
			return $key;
		}
		return NULL;
	}
}
// 兼容性判断
if (!function_exists('is_php')) {
	function is_php($version) {
		static $_is_php;
		$version = (string)$version;
		if ( ! isset($_is_php[$version]))
		{
			$_is_php[$version] = version_compare(PHP_VERSION, $version, '>=');
		}
		return $_is_php[$version];
	}
}
// 兼容性判断
if (!function_exists('getallheaders')) { 
	function getallheaders() {
		$headers = [];
		foreach ($_SERVER as $name => $value) {
			if (substr($name, 0, 5) == 'HTTP_') {
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
		}
		return $headers;
	}
}
// 跳转地址安全检测
function dr_url_jump($content) {
	if (dr_is_empty($content)) {
		return '';
	}
	$setting = getcache('common','commons');
	preg_match_all('|<a.*?href="(.*?)".*?>(.*?)\</a>|', $content, $domain_arr, PREG_SET_ORDER);
	$url_whiteList_arr = explode(PHP_EOL, (string)$setting['whiteList']);
	foreach ($domain_arr as $item) {
		$isreplace = true;
		foreach ($url_whiteList_arr as $url_item) {
			$re = stripos($item[1], $url_item);
			if ($re != false) {
				$isreplace = false;
				break;
			}
		}
		if ($isreplace == true) {
			$html = dr_str_replace_once($item[1], WEB_PATH.'index.php?m=404&c=index&a=jump&go=' . md5($item[1]) . '" target="_blank" src="' . $item[1], $item[0]);
			pc_base::load_sys_class('cache')->set_auth_data(md5($item[1]), $item[1], 1);
			$content = str_replace($item[0], $html, $content);
		}
	}
	return $content;
}
//只替换一次字符串
function dr_str_replace_once($needle, $replace, $haystack) {
	$pos = strpos($haystack, $needle);
	if ($pos === false) {
		return $haystack;
	}
	return substr_replace($haystack, $replace, $pos, strlen($needle));
}
/**
 *  混淆字符串内容
 */
if (! function_exists('rndstring')) {
	function rndstring($str) {
		if (dr_is_empty($str)) {
			return '';
		}
		//最大间隔距离(如果在检测不到p标记的情况下，加入混淆字串的最大间隔距离)
		$maxpos = 1024;

		//font 的字体颜色
		$fontColor = '#FFFFFF';

		//div span p 标记的随机样式
		$st1 = chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('a'),ord('z'))).chr(mt_rand(ord('a'),ord('z'))).mt_rand(100,999);
		$st2 = chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('a'),ord('z'))).chr(mt_rand(ord('a'),ord('z'))).mt_rand(100,999);
		$st3 = chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('a'),ord('z'))).chr(mt_rand(ord('a'),ord('z'))).mt_rand(100,999);
		$st4 = chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('a'),ord('z'))).chr(mt_rand(ord('a'),ord('z'))).mt_rand(100,999);
		$rndstyle[1]['value'] = '.'.$st1.' { display:none; }';
		$rndstyle[1]['name'] = $st1;
		$rndstyle[2]['value'] = '.'.$st2.' { display:none; }';
		$rndstyle[2]['name'] = $st2;
		$rndstyle[3]['value'] = '.'.$st3.' { display:none; }';
		$rndstyle[3]['name'] = $st3;
		$rndstyle[4]['value'] = '.'.$st4.' { display:none; }';
		$rndstyle[4]['name'] = $st4;
		$mdd = mt_rand(1,4);
		$rndstyleValue = $rndstyle[$mdd]['value'];
		$rndstyleName = $rndstyle[$mdd]['name'];
		$reString = '<style> '.$rndstyleValue.' </style>'.PHP_EOL;

		//附机标记
		$rndem[1] = 'font';
		$rndem[2] = 'div';
		$rndem[3] = 'span';
		$rndem[4] = 'p';

		//读取字符串数据
		$setting = getcache('common','commons');
		if (!$setting['downmix']) {
			return $str;
		}
		$downmix = explode(PHP_EOL, (string)$setting['downmix']);
		if (!$downmix) {
			return $str;
		}
		$totalitem = 0;
		foreach($downmix as $v) {
			if (trim($v)) {
				$totalitem++;
				$rndstring[$totalitem] = trim($v);
			}
		}

		//处理要防采集的字段
		$strlen = strlen((string)$str) - 1;
		$prepos = 0;
		for ($i=0;$i<=$strlen;$i++) {
			if ($i+2 >= $strlen || $i<50) {
				$reString .= $str[$i];
			} else {
				$ntag = @strtolower($str[$i].$str[$i+1].$str[$i+2]);
				if ($ntag=='</p' || ($ntag=='<br' && $i-$prepos>$maxpos)) {
					$dd = mt_rand(1,4);
					$emname = $rndem[$dd];
					$dd = mt_rand(1, $totalitem);
					$rnstr = $rndstring[$dd];
					if ($emname!='font') {
						$rnstr = ' <'.$emname.' class="'.$rndstyleName.'">'.$rnstr.'</'.$emname.'> ';
					} else {
						$rnstr = ' <font color="'.$fontColor.'" style="display:none;">'.$rnstr.'</font> ';
					}
					$reString .= $rnstr.$str[$i];
					$prepos = $i;
				} else {
					$reString .= $str[$i];
				}
			}
		}
		return $reString;
	}
}
/**
 * 文字转换拼音
 */
function dr_text2py($str) {
	return pc_base::load_sys_class('pinyin')->result((string)$str);
}
/**
 * 将html转化为纯文字
 * str html文字提取
 * cn 是否纯中文
 */
function dr_html2text($str, $cn = false) {
	$str = clearhtml($str);
	if ($cn && preg_match_all('/[\x{4e00}-\x{9fff}]+/u', $str, $mt)) {
		return join('', $mt[0]);
	}

	$text = "";
	$start = 1;
	for ($i=0;$i<strlen($str);$i++) {
		if ($start==0 && $str[$i]==">") {
			$start = 1;
		} elseif($start==1) {
			if ($str[$i]=="<") {
				$start = 0;
				$text.= " ";
			} elseif(ord($str[$i])>31) {
				$text.= $str[$i];
			}
		}
	}

	return $text;
}
/**
 * 批量 htmlspecialchars
 */
function dr_htmlspecialchars($param) {
	if (!$param) {
		return is_array($param) ? array() : '';
	} elseif (is_array($param)) {
		foreach ($param as $a => $t) {
			if ($t && !is_array($t)) {
				$param[$a] = htmlspecialchars($t);
			}
		}
	} else {
		$param = htmlspecialchars($param);
	}
	return $param;
}
/**
 * 检查目录权限
 * $dir 目录地址
 */
function dr_check_put_path($dir) {
	if (!$dir) {
		return 0;
	} elseif (!is_dir($dir)) {
		return 0;
	}
	$size = file_put_contents($dir.'test.html', 'test');
	if ($size === false) {
		return 0;
	} else {
		unlink($dir.'test.html');
		return 1;
	}
}
if (! function_exists('clearhtml')) {
	/**
	 * 清除HTML标记
	 *
	 * @param   string  $str
	 * @return  string
	 */
	function clearhtml($str) {

		if (is_array($str) || !$str) {
			return '';
		}

		$str = strip_tags((string)$str);
		$str = code2html($str);
		$str = str_replace(
			array('&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'),
			array(' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $str
		);

		$str = preg_replace("/\<[a-z]+(.*)\>/iU", "", (string)$str);
		$str = preg_replace("/\<\/[a-z]+\>/iU", "", (string)$str);
		$str = str_replace(array(PHP_EOL, chr(13), chr(10), '&nbsp;'), '', $str);

		return trim($str);
	}
}
if (! function_exists('dr_redirect')) {
	/**
	 * 跳转地址
	 */
	function dr_redirect($url = '', $method = 'auto', $code = 0) {

		if ($url == FC_NOW_URL) {
			return; // 防止重复定向
		}

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
}
if (! function_exists('dr_redirect_safe_check')) {
	/**
	 * 跳转地址安全检测
	 */
	function dr_redirect_safe_check($url) {
		return $url;
	}
}
function dr_admin_msg($code, $msg, $url = '', $time = 3, $dialog = '', $return = false) {
	$input = pc_base::load_sys_class('input');
	if ($input->get('callback')) {
		dr_jsonp($code, $msg, $url);
	} elseif (($input->get('is_ajax') || IS_AJAX)) {
		dr_json($code, $msg, $url);
	}

	if (!is_array($url)) {
		$url = dr_safe_url($url, true);
	} else {
		$url = '';
	}
	$backurl = $url ? $url : dr_safe_url($_SERVER['HTTP_REFERER'], true);

	if ($backurl) {
		strpos(dr_now_url(), $backurl) === 0 && $backurl = '';
	} else {
		$backurl = 'javascript:history.back();';
	}

	pc_base::load_sys_class('service')->assign([
		'msg' => $msg,
		'url' => $url,
		'time' => $time,
		'mark' => $code,
		'dialog' => $dialog,
		'backurl' => $backurl,
		'meta_title' => clearhtml($msg),
		'is_msg_page' => 1,
	]);
	pc_base::load_sys_class('service')->admin_display('msg', 'admin');
	if ($return) {
		return;
	}
	exit;
}
/**
 * 前台提示信息
 */
function dr_msg($code, $msg, $url = '', $time = 3, $dialog = '', $return = false) {
	$input = pc_base::load_sys_class('input');
	if ($input->get('is_show_msg')) {
		// 强制显示提交信息而不采用ajax返回
	} else {
		if ($input->get('callback')) {
			dr_jsonp($code, $msg, $url);
		} elseif (($input->get('is_ajax') || IS_AJAX)) {
			dr_json($code, $msg, $url);
		}
	}

	if (!is_array($url)) {
		$url = dr_safe_url($url, true);
	} else {
		$url = '';
	}
	$backurl = $url ? $url : dr_safe_url($_SERVER['HTTP_REFERER'], true);

	if ($backurl) {
		strpos(dr_now_url(), $backurl) === 0 && $backurl = '';
	} else {
		$backurl = 'javascript:history.back();';
	}

	$SEO = seo(get_siteid(), 0, L(clearhtml($msg)));

	// 返回的钩子
	$rt = [
		'msg' => $msg,
		'url' => $url,
		'time' => $time,
		'mark' => $code,
		'code' => $code,
		'dialog' => $dialog,
		'backurl' => $backurl,
		'SEO' => $SEO
	];
	pc_base::load_sys_class('hooks')::trigger('cms_end', $rt);

	pc_base::load_sys_class('service')->assign($rt);
	pc_base::load_sys_class('service')->display('content', 'msg');
	if ($return) {
		return;
	}
	exit();
}
/**
 * 当前URL
 */
function dr_now_url() {
	return pc_base::load_sys_class('input')->xss_clean(FC_NOW_URL);
}
/**
 * 生成静态时的跳转提示
 */
function html_msg($code, $msg, $url = '', $note = '') {
	if (pc_base::load_sys_class('input')->get('is_ajax')) {
		dr_json($code, $msg, $url);
	}
	pc_base::load_sys_class('service')->assign([
		'msg' => $msg,
		'url' => $url,
		'note' => $note,
		'mark' => $code
	]);
	pc_base::load_sys_class('service')->admin_display('html_msg', 'admin');
	exit;
}
/**
 * 排序操作
 */
function dr_sorting($name) {
	$input = pc_base::load_sys_class('input');
	$value = $input->get('order') ? $input->get('order') : '';
	if (!$value || !$name) {
		return 'order_sorting';
	}
	if (strpos($value, $name) === 0 && strpos($value, 'asc') !== FALSE) {
		return 'order_sorting_asc';
	} elseif (strpos($value, $name) === 0 && strpos($value, 'desc') !== FALSE) {
		return 'order_sorting_desc';
	}
	return 'order_sorting';
}
// 动态加载css
function load_css($css) {
	if (!defined($css)) {
		define($css, 1);
		return '<link href=\''.$css.'\' rel=\'stylesheet\' type=\'text/css\' />'.PHP_EOL;
	}
	return '';
}
// 动态加载js
function load_js($js) {
	if (!defined($js)) {
		define($js, 1);
		return '<script type=\'text/javascript\' src=\''.$js.'\'></script>'.PHP_EOL;
	}
	return '';
}
// 动态加载script
function load_script($js) {
	if (!defined($js)) {
		define($js, 1);
		return '<script type=\'text/javascript\'>'.$js.'</script>'.PHP_EOL;
	}
	return '';
}
/**
 * 百度地图调用
 */
function dr_baidu_map($value, $zoom = 15, $width = 600, $height = 400, $ak = SYS_BDMAP_API, $class= '', $tips = '') {
	if (!$value) {
		return '没有坐标值';
	}
	$id = 'dr_map_'.rand(0, 99);
	!$ak && $ak = SYS_BDMAP_API;
	!$zoom && $zoom = 15;
	$width = $width ? $width : '100%';
	list($lngX, $latY) = explode(',', $value);

	$js = load_js((strpos(FC_NOW_URL, 'https') === 0 ? 'https' : 'http').'://api.map.baidu.com/api?v=2.0&ak='.$ak);

	return $js.'<div class="'.$class.'" id="' . $id . '" style="width:' . (strpos($width, '%') ? $width : $width. 'px').'; height:' . (strpos($height, '%') ? $height : $height. 'px') . '; overflow:hidden"></div>
	<script type="text/javascript">
	var mapObj=null;
	lngX = "' . $lngX . '";
	latY = "' . $latY . '";
	zoom = "' . $zoom . '"; 
	var mapObj = new BMap.Map("'.$id.'");
	var ctrl_nav = new BMap.NavigationControl({anchor:BMAP_ANCHOR_TOP_LEFT,type:BMAP_NAVIGATION_CONTROL_LARGE});
	mapObj.addControl(ctrl_nav);
	mapObj.enableDragging();
	mapObj.enableScrollWheelZoom();
	mapObj.enableDoubleClickZoom();
	mapObj.enableKeyboard();//启用键盘上下左右键移动地图
	mapObj.centerAndZoom(new BMap.Point(lngX,latY),zoom);
	drawPoints();
	function drawPoints(){
		var myIcon = new BMap.Icon("' . IMG_PATH . 'icon/mak.png", new BMap.Size(27, 45));
		var center = mapObj.getCenter();
		var point = new BMap.Point(lngX,latY);
		var marker = new BMap.Marker(point, {icon: myIcon});
		mapObj.addOverlay(marker);
		'.($tips ? 'mapObj.openInfoWindow(new BMap.InfoWindow("'.str_replace('"', '\'', $tips).'",{offset:new BMap.Size(0,-17)}),point);' : '').'
	}
	</script>';
}
function base64($file) {
	$base64_file = '';
	if (file_exists($file)) {
		$mime_type= mime_content_type($file);
		$base64_data = base64_encode(file_get_contents($file));
		$base64_file = 'data:'.$mime_type.';base64,'.$base64_data;
	}
	return $base64_file;
}
if(!function_exists('mime_content_type')) {

	function mime_content_type($filename) {

		$mime_types = array(

			'txt' => 'text/plain',
			'htm' => 'text/html',
			'html' => 'text/html',
			'php' => 'text/html',
			'css' => 'text/css',
			'js' => 'application/javascript',
			'json' => 'application/json',
			'xml' => 'application/xml',
			'swf' => 'application/x-shockwave-flash',
			'flv' => 'video/x-flv',

			// images
			'png' => 'image/png',
			'jpe' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'jpg' => 'image/jpeg',
			'gif' => 'image/gif',
			'bmp' => 'image/bmp',
			'ico' => 'image/vnd.microsoft.icon',
			'tiff' => 'image/tiff',
			'tif' => 'image/tiff',
			'svg' => 'image/svg+xml',
			'svgz' => 'image/svg+xml',

			// archives
			'zip' => 'application/zip',
			'rar' => 'application/x-rar-compressed',
			'exe' => 'application/x-msdownload',
			'msi' => 'application/x-msdownload',
			'cab' => 'application/vnd.ms-cab-compressed',

			// audio/video
			'mp3' => 'audio/mpeg',
			'qt' => 'video/quicktime',
			'mov' => 'video/quicktime',

			// adobe
			'pdf' => 'application/pdf',
			'psd' => 'image/vnd.adobe.photoshop',
			'ai' => 'application/postscript',
			'eps' => 'application/postscript',
			'ps' => 'application/postscript',

			// ms office
			'doc' => 'application/msword',
			'rtf' => 'application/rtf',
			'xls' => 'application/vnd.ms-excel',
			'ppt' => 'application/vnd.ms-powerpoint',

			// open office
			'odt' => 'application/vnd.oasis.opendocument.text',
			'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		);

		$ext = strtolower(array_pop(explode('.',$filename)));
		if (array_key_exists($ext, $mime_types)) {
			return $mime_types[$ext];
		}
		elseif (function_exists('finfo_open')) {
			$finfo = finfo_open(FILEINFO_MIME);
			$mimetype = finfo_file($finfo, $filename);
			finfo_close($finfo);
			return $mimetype;
		}
		else {
			return 'application/octet-stream';
		}
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
 * @param   string  $str
 * @param   string  $limit
 * @param   string  $dot
 * @return  string
 */
function str_cut($string, $limit = '100', $dot = '...') {
	if (!$string) {
		return '';
	}
	// 钩子处理
	$rs = pc_base::load_sys_class('hooks')::trigger_callback('str_cut', $string, $limit, $dot);
	if ($rs && isset($rs['code']) && $rs['code'] && $rs['msg']) {
		return $rs['msg'];
	}
	$a = 0;
	if ($limit && strpos((string)$limit, ',')) {
		list($a, $length) = explode(',', $limit);
	} else {
		$length = $limit;
	}
	$length = (int)$length;
	if (dr_strlen($string) <= $length || !$length) {
		return $string;
	}
	if (function_exists('mb_substr')) {
		$strcut = mb_substr($string, $a, $length);
	} else {
		$n = $tn = $noc = 0;
		$string = str_replace(['&amp;', '&quot;', '&lt;', '&gt;'], ['&', '"', '<', '>'], $string);
		while ($n < dr_strlen($string)) {
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

		$strcut = substr($string, $a, $n);
		$strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);
	}
	$strcut == $string && $dot = '';
	return $strcut . $dot;
}

/**
 * 单词截取
 * @param $string 字符串
 * @param $maxchar 长度限制
 * @param $end 超出的填充字符串
 * @return 处理后的值
 */
function dr_wordcut($text, $maxchar, $end = '...') {

	if (!$text) {
		return '';
	}

	if (mb_strlen($text) > $maxchar || $text == '') {
		$words = preg_split('/\s/', $text);
		$output = '';
		$i = 0;
		while (1) {
			$length = mb_strlen($output) + mb_strlen($words[$i]);
			if ($length > $maxchar) {
				break;
			} else {
				$output .= " " . $words[$i];
				++$i;
			}
		}
		$output .= $end;
	} else {
		$output = $text;
	}

	return trim((string)$output);
}

/**
 * 随机颜色
 *
 * @return  string
 */
function dr_random_color() {
	$str = '#';
	for ($i = 0; $i < 6; $i++) {
		$randNum = rand(0, 15);
		switch ($randNum) {
			case 10: $randNum = 'A';
				break;
			case 11: $randNum = 'B';
				break;
			case 12: $randNum = 'C';
				break;
			case 13: $randNum = 'D';
				break;
			case 14: $randNum = 'E';
				break;
			case 15: $randNum = 'F';
				break;
		}
		$str.= $randNum;
	}
	return $str;
}

// ip存储信息
function ip_info() {
	return pc_base::load_sys_class('input')->ip_info();
}

// 获取访客ip地址
function ip() {
	return pc_base::load_sys_class('input')->ip_address();
}

// ip转为实际地址
function ip2address($ip) {
	return pc_base::load_sys_class('input')->ip2address($ip);
}

// 当前ip实际地址
function ip_address_info() {
	return pc_base::load_sys_class('input')->ip_address_info();
}

function get_cost_time() {
	$microtime = microtime(true);
	return $microtime - SYS_START_TIME;
}
/**
 * 程序执行时间
 *
 * @return	int	单位ms
 */
function execute_time() {
	$stime = explode(' ', SYS_START_TIME);
	$etime = explode(' ', microtime());
	return number_format(($etime [1] + $etime [0] - $stime [1] - $stime [0]), 6);
}

// 生成随机验证码
function get_rand_value() {
	return rand(100000, 999999);
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
	return $hash;
}

/**
 * 能用的随机数生成
 * @param string $type 类型 alpha/alnum/numeric/nozero/unique/md5/encrypt/sha1
 * @param int    $len  长度
 * @return string
 */
function build($type = 'alnum', $len = 10) {
	switch ($type) {
		case 'alpha':
		case 'alnum':
		case 'numeric':
		case 'nozero':
			switch ($type) {
				case 'alpha':
					$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
					break;
				case 'alnum':
					$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
					break;
				case 'numeric':
					$pool = '0123456789';
					break;
				case 'nozero':
					$pool = '123456789';
					break;
			}
			return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
		case 'unique':
		case 'md5':
			return md5(uniqid(mt_rand()));
		case 'encrypt':
		case 'sha1':
			return sha1(uniqid(mt_rand(), true));
	}
}

/**
* 将字符串转换为数组
*
* @param	string	$data	字符串
* @return	array	返回数组格式，如果，data为空，则返回空数组
*/
function string2array($data) {
	return dr_string2array($data);
}
/**
* 将数组转换为字符串
*
* @param	array	$data		数组
* @param	bool	$isformdata	如果为0，则不使用new_stripslashes处理，可选参数，默认为1
* @return	string	返回字符串，如果，data为空，则返回空
*/
function array2string($data, $isformdata = 1) {
	return dr_array2string($data);
}
/**
 * 根据文件扩展名获取文件预览信息
 */
function dr_file_preview_html($value, $target = 0) {
	if (!$value) {
		return '';
	}
	$ext = trim(strtolower(strrchr($value, '.')), '.');
	$file = WEB_PATH.'api.php?op=icon&fileext='.$ext;
	if (dr_is_image($ext)) {
		$value = dr_file($value);
		$url = $target ? $value.'" target="_blank' : 'javascript:dr_preview_image(\''.$value.'\');';
		return '<a href="'.$url.'"><img src="'.$value.'"></a>';
	} elseif ($ext == 'mp4') {
		$value = dr_file($value);
		$url = $target ? $value.'" target="_blank' : 'javascript:dr_preview_video(\''.$value.'\');';
		return '<a href="'.$url.'"><img src="'.$file.'"></a>';
	} else {
		$url = $target ? $value.'" target="_blank' : 'javascript:dr_preview_url(\''.$value.'\');';
		return '<a href="'.$url.'"><img src="'.$file.'"></a>';
	}
}
// 用于附件列表查看时
function dr_file_list_preview_html($t) {
	$file = WEB_PATH.'api.php?op=icon&fileext='.$t['fileext'];
	if (dr_is_image($t['fileext'])) {
		return '<a href="javascript:dr_preview_image(\''.dr_get_file_url($t).'\');"><img src="'.dr_get_file_url($t, 50, 50).'"></a>';
	} elseif ($t['fileext'] == 'mp4') {
		return '<a href="javascript:dr_preview_video(\''.dr_get_file_url($t).'\');"><img src="'.$file.'"></a>';
	} else {
		return '<a href="javascript:dr_preview_url(\''.dr_get_file_url($t).'\');"><img src="'.$file.'"></a>';
	}
}
/**
 * 格式化复选框\单选框\选项值 字符串转换为数组
 */
function dr_format_option_array($value) {
	$data = array();
	if (!$value) {
		return $data;
	}
	$options = explode(PHP_EOL, str_replace(array(chr(13), chr(10)), PHP_EOL, $value));
	foreach ($options as $t) {
		if (strlen($t)) {
			$n = $v = '';
			if (strpos($t, '|') !== FALSE) {
				list($n, $v) = explode('|', $t);
				$v = is_null($v) || !strlen($v) ? '' : trim($v);
			} else {
				$v = $n = trim($t);
			}
			$data[htmlspecialchars($v)] = htmlspecialchars($n);
		}
	}
	return $data;
}
/**
* 统一返回json格式并退出程序
*/
function dr_json($code, $msg, $data = array(), $return = false){
	$input = pc_base::load_sys_class('input');
	// 强制显示提交信息而不采用ajax返回
	if ($input->get('is_show_msg')) {
		$url = '';
		if ($code) {
			$url = dr_redirect_safe_check(isset($data['url']) ? $data['url'] : '');
		}
		dr_msg($code, $msg, $url);
	}
	// 返回的钩子
	$rt = dr_return_data($code, $msg, $data);
	if (SYS_CSRF && IS_POST) {
		$rt['token'] = [
			'name' => SYS_TOKEN_NAME,
			'value' => csrf_hash()
		];
	}
	// 按格式返回数据
	$format = $input->get('format');
	if (isset($format) && $format) {
		switch ($format) {
			case 'jsonp':
				dr_jsonp(1, $msg, $data, $return);
				break;
			case 'text':
				pc_base::load_sys_class('hooks')::trigger('cms_end', $rt);
				echo $msg;exit;
				break;
		}
	}
	pc_base::load_sys_class('hooks')::trigger('cms_end', $rt);
	header('Content-type: application/json');
	echo dr_array2string($rt);
	if (!$return) {
		exit;
	}
}

/**
 * 统一返回jsonp格式并退出程序
 */
function dr_jsonp($code, $msg, $data = array(), $return = false){
	$callback = dr_safe_replace(pc_base::load_sys_class('input')->get('callback'));
	!$callback && $callback = 'callback';
	// 返回的钩子
	$rt = dr_return_data($code, $msg, $data);
	pc_base::load_sys_class('hooks')::trigger('cms_end', $rt);
	echo $callback.'('.dr_array2string($rt).')';
	if (!$return) {
		exit;
	}
}
/**
 * 将对象转换为数组
 *
 * @param   object  $obj    数组对象
 * @return  array
 */
function dr_object2array($obj) {
	if (!$obj) {
		return array();
	}
	$_arr = is_object($obj) ? get_object_vars($obj) : $obj;
	if ($_arr && is_array($_arr)) {
		foreach ($_arr as $key => $val) {
			$val = (is_array($val) || is_object($val)) ? dr_object2array($val) : $val;
			$arr[$key] = $val;
		}
	}
	return $arr;
}
/**
 * 数组截取
 * @param $arr 数组值
 * @param $limit 长度限制
 * @return 处理后的数组
 */
function dr_arraycut($arr, $limit) {
	if (!$arr) {
		return array();
	} elseif (!is_array($arr)) {
		return array();
	}
	$limit = (string)$limit;
	if (strpos($limit, ',')) {
		list($a, $b) = explode(',', $limit);
	} else {
		$a = 0;
		$b = $limit;
	}
	return array_slice($arr, $a, $b);
}
/**
 * 将字符串转换为数组
 *
 * @param   string  $data   字符串
 * @return  array
 */
function dr_string2array($data, $limit = '') {
	if (!$data) {
		return array();
	} elseif (is_array($data)) {
		$rt = $data;
	} else {
		$rt = json_decode($data, true);
		//if (!$rt && IS_DEV) {
			// 存在安全隐患时改为开发模式下执行
			//$rt = unserialize(stripslashes($data));
		//}
	}
	if (is_array($rt) && $limit) {
		return dr_arraycut($rt, $limit);
	}
	return $rt;
}
/**
 * 将数组转换为字符串
 *
 * @param	array	$data	数组
 * @return	string
 */
function dr_array2string($data) {
	if (!$data) {
		return '';
	}
	if (is_array($data)) {
		$str = json_encode($data, JSON_UNESCAPED_UNICODE | 320);
		if (!$str) {
			if (IS_DEV) {
				log_message('debug', 'json_encode转换失败：'.json_last_error_msg());
			}
			return '';
		}
		return $str;
	} else {
		return $data;
	}
}
/**
 * 附件信息
 */
function get_attachment($id, $update = 0) {
	$cache = pc_base::load_sys_class('cache');
	$att_db = pc_base::load_model('attachment_model');
	if (!$id) {
		return null;
	}
	if (!$update) {
		$data = $cache->get_file('attach-info-'.$id, 'attach');
		if ($data) {
			$data['url'] = dr_get_file_url($data);
			return $data;
		}
	}
	$data = $att_db->get_one(array('aid'=>(int)$id));
	if (!$data) {
		return null;
	}

	$data['file'] = SYS_UPLOAD_PATH.$data['filepath'];

	// 文件真实地址
	if ($data['remote']) {
		$remote = get_cache('attachment', $data['remote']);
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

	$cache->set_file('attach-info-'.$data['aid'], $data, 'attach');

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
 * 关键字高亮显示
 *
 * @param   string  $string     字符串
 * @param   string  $keyword    关键字
 * @return  string
 */
function dr_keyword_highlight($string, $keyword, $rule = '') {
	if (!$keyword || !$string) {
		return $string;
	}
	if (is_array($keyword)) {
		$arr = $keyword;
	} else {
		$arr = explode(' ', trim(str_replace('%', ' ', urldecode($keyword)), '%'));
	}
	if (!$arr) {
		return $string;
	}
	!$rule && $rule = '<font color=red><strong>[value]</strong></font>';
	foreach ($arr as $t) {
		$string = str_ireplace($t, str_replace('[value]', $t, $rule), $string);
	}
	return $string;
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
	$key = md5($key != '' ? $key : SYS_KEY);
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

	if($result && $operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - SYS_TIME > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
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
	if(defined('IS_ADMIN') && IS_ADMIN) {
		$lang = defined('SYS_STYLE') && SYS_STYLE ? SYS_STYLE : 'zh-cn';
	} else {
		$lang = SYS_LANGUAGE;
	}
	if(!$LANG) {
		require_once PC_PATH.'languages'.DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.'system.lang.php';
		if(defined('IS_ADMIN') && IS_ADMIN) require_once PC_PATH.'languages'.DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.'system_menu.lang.php';
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
 * @param $style
 */
function template($module = 'content', $template = 'index', $style = '') {
	if (!$template) {
		pc_base::load_sys_class('service')->show_error('模板文件没有设置');
	}
	!defined('IS_HTML') && define('IS_HTML', 0);
	if(strpos($template, '..') !== false){
		pc_base::load_sys_class('service')->show_error(L('模板文件名非法。'));
	}
	$module = str_replace('/', DIRECTORY_SEPARATOR, $module);
	if(!empty($style) && preg_match('/([a-z0-9\-_]+)/is',$style)) {
	} elseif (empty($style) && !defined('STYLE')) {
		if(defined('SITEID')) {
			$siteid = SITEID;
		} else {
			$siteid = param::get_cookie('siteid');
		}
		if (!$siteid) $siteid = get_siteid();
		if(!empty($siteid)) {
			$style = dr_site_info('default_style', $siteid);
		}
	} elseif (empty($style) && defined('STYLE')) {
		$style = STYLE;
	} else {
		$style = 'default';
	}
	if(!$style) $style = 'default';
	$style = $style.'/'.pc_base::load_sys_class('service')->get_dir();
	$template_cache = pc_base::load_sys_class('template_cache');
	$compiledtplfile = CACHE_PATH.'caches_template'.DIRECTORY_SEPARATOR.$style.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$template.'.php';
	if(file_exists(TPLPATH.$style.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$template.'.html')) {
		if(!file_exists($compiledtplfile) || (@filemtime(TPLPATH.$style.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$template.'.html') > @filemtime($compiledtplfile))) {
			$template_cache->template_compile($module, $template, $style);
		}
	} else {
		$compiledtplfile = CACHE_PATH.'caches_template'.DIRECTORY_SEPARATOR.'default/'.pc_base::load_sys_class('service')->get_dir().DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$template.'.php';
		if(!file_exists($compiledtplfile) || (file_exists(TPLPATH.'default/'.pc_base::load_sys_class('service')->get_dir().DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$template.'.html') && filemtime(TPLPATH.'default/'.pc_base::load_sys_class('service')->get_dir().DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$template.'.html') > filemtime($compiledtplfile))) {
			$template_cache->template_compile($module, $template, 'default/'.pc_base::load_sys_class('service')->get_dir());
		} elseif (!file_exists(TPLPATH.'default/'.pc_base::load_sys_class('service')->get_dir().DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$template.'.html')) {
			if (IS_DEV) {
				log_message('error', '模板文件['.TPLPATH.$style.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$template.'.html]不存在');
			}
			if (pc_base::load_sys_class('service')->is_mobile()) {
				$pc = str_replace('/mobile\\', '/pc\\', TPLPATH.$style.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$template.'.html');
				if (is_file($pc)) {
					pc_base::load_sys_class('service')->show_error('移动端模板文件不存在', TPLPATH.$style.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$template.'.html', $pc);exit;
				}
			}
			pc_base::load_sys_class('service')->show_error('模板文件不存在', TPLPATH.$style.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$template.'.html');
		}
	}
	return $compiledtplfile;
}

/**
 * 加载后台模板
 * @param string $file 文件名
 * @param string $m 模型名
 */
function admin_template($file, $m = '') {
	$m = empty($m) ? ROUTE_M : $m;
	if(empty($m)) return false;
	return PC_PATH.'modules'.DIRECTORY_SEPARATOR.$m.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$file.'.tpl.php';
}

/**
 * 动态调用模板
 * @param $id div控件的ID名
 * @param $filename 模板文件名
 * @param $siteid 站点ID
 * @param $param_str 附加URL参数
 * @return 返回ajax调用代码
 */
function dr_ajax_template($id, $filename, $param_str = '', $siteid = '') {
	$error = IS_DEV && !defined('IS_HTML') && !IS_HTML ? ', error: function(HttpRequest, ajaxOptions, thrownError) {  var msg = HttpRequest.responseText;layer.open({ type: 1, title: "'.L('系统故障').'", fix:true, shadeClose: true, shade: 0, area: [\'50%\', \'50%\'],  content: "<div style=\"padding:10px;\">"+msg+"</div>"  }); } ' : '';
	return "<script type=\"text/javascript\"> $.ajax({ type: \"GET\", url:\"".WEB_PATH."api.php?op=template&name={$filename}&siteid={$siteid}&format=jsonp&".$param_str."\", dataType: \"jsonp\", success: function(data){ $(\"#{$id}\").html(data.msg); } {$error} });</script>";
}

/**
 * 用户等级 显示星星
 *
 * @param    $num
 * @param   $starthreshold  星星数在达到此阈值(设为 N)时，N 个星星显示为 1 个月亮、N 个月亮显示为 1 个太阳。
 * @return  img标签值
 */
function dr_show_stars($num, $starthreshold = 4) {
	$str = '';
	$alt = 'alt="Rank: '.$num.'"';
	for ($i = 3; $i > 0; $i--) {
		$numlevel = intval($num / pow($starthreshold, ($i - 1)));
		$num = ($num % pow($starthreshold, ($i - 1)));
		for ($j = 0; $j < $numlevel; $j++) {
			$str.= '<img align="absmiddle" src="'.IMG_PATH.'star/star_level'.$i.'.gif" '.$alt.' />';
		}
	}
	return $str;
}

/*
 * 重写日志记录函数
 */
function log_message($level, $message, array $context = []) {
	return pc_base::load_sys_class('debug')::Log($level, $message, $context);
}

/**
 * 提示信息页面跳转，跳转地址如果传入数组，页面会提示多个地址供用户选择，默认跳转地址为数组的第一个值，时间为5秒。
 * showmessage('登录成功', array('默认跳转地址'=>'http://www.kaixin100.cn'));
 * @param string $msg 提示信息
 * @param mixed(string/array) $url_forward 跳转地址
 * @param int $ms 跳转等待时间
 */
function showmessage($msg, $url_forward = 'goback', $ms = 1250, $dialog = '', $returnjs = '', $code = 2) {
	$SEO = seo(get_siteid(), 0, L('message_tips'));
	pc_base::load_sys_class('service')->assign([
		'SEO' => $SEO,
		'msg' => $msg,
		'url_forward' => $url_forward,
		'ms' => $ms,
		'dialog' => $dialog,
		'returnjs' => $returnjs,
		'mark' => $code,
		'code' => $code,
	]);
	if(defined('IS_ADMIN') && IS_ADMIN) {
		pc_base::load_sys_class('service')->admin_display('showmessage', 'admin');
	} else {
		pc_base::load_sys_class('service')->display('content', 'message');
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
function str_exists($haystack, $needle) {
	return !(strpos($haystack, $needle) === FALSE);
}

/**
 * 取得文件扩展
 *
 * @param $filename 文件名
 * @return 扩展名
 */
function fileext($filename) {
	return str_replace('.', '', trim(strtolower(strrchr($filename, '.')), '.'));
}

/**
 * 读取缓存
 */
function get_cache(...$params) {
	return pc_base::load_sys_class('cache')->get(...$params);
}

/**
 * 写入缓存，默认为文件缓存，不加载缓存配置。
 * @param $name 缓存名称
 * @param $data 缓存数据
 * @param $filepath 数据路径（模块名称） caches/cache_$filepath/
 */
function setcache($name, $data, $filepath = ROUTE_M) {
	if(empty($filepath)) $filepath = ROUTE_M;
	if(!preg_match("/^[a-zA-Z0-9_-]+$/", $name)) return false;
	return pc_base::load_sys_class('cache')->set_file($name, $data, $filepath);
}

/**
 * 读取缓存，默认为文件缓存，不加载缓存配置。
 * @param string $name 缓存名称
 * @param $filepath 数据路径（模块名称） caches/cache_$filepath/
 */
function getcache($name, $filepath = ROUTE_M) {
	if(empty($filepath)) $filepath = ROUTE_M;
	if (preg_match("/^category_content_([0-9]+)$/", $name, $names) && $filepath == 'commons') {
		return pc_base::load_sys_class('cache')->get_file('cache', 'module/category-'.$names[1].'-data');
	}
	if(!preg_match("/^[a-zA-Z0-9_-]+$/", $name)) return false;
	return pc_base::load_sys_class('cache')->get_file($name, $filepath);
}

/**
 * 删除缓存，默认为文件缓存，不加载缓存配置。
 * @param $name 缓存名称
 * @param $filepath 数据路径（模块名称） caches/cache_$filepath/
 */
function delcache($name, $filepath = ROUTE_M) {
	if(empty($filepath)) $filepath = ROUTE_M;
	if(!preg_match("/^[a-zA-Z0-9_-]+$/", $name)) return false;
	if($filepath!="" && !preg_match("/^[a-zA-Z0-9_-]+$/", $filepath)) return false;
	return pc_base::load_sys_class('cache')->del_file($name, $filepath);
}

/**
 * 模块的clink值
 */
function module_clink($module = '', $type = '', $data = []) {
	return module_click('link', $module, $type, $data);
}

/**
 * 模块的cbottom值
 */
function module_cbottom($module = '', $type = '', $data = []) {
	return module_click('bottom', $module, $type, $data);
}

function module_click($pos, $module = '', $type = '', $data = []) {

	if (!$type) {
		// 表示模块部分
		$endfix = '';
	} else {
		$endfix = '_'.$type;
	}

	// 加载全部模块的
	$local = pc_base::load_sys_class('service')::apps(true);

	// 加载模块自身的
	if (ROUTE_M) {
		if (is_file(PC_PATH.'modules/'.ROUTE_M.'/config/c'.$pos.$endfix.'.php')) {
			$local[ROUTE_M] = [PC_PATH.'modules/'.ROUTE_M.'/'];
		} else {
			// 排除模块自身
			if (isset($local[ROUTE_M])) {
				unset($local[ROUTE_M]);
			}
		}
	}

	foreach ($local as $dir => $path) {
		$ck = 0;
		// 判断模块目录
		if (is_array($path)) {
			$ck = 1;
			$path = array_shift($path);
		} elseif (is_file($path.'config/c'.$pos.$endfix.'.php')) {
			$ck = 1;
		}
		if ($ck) {
			$_clink = require $path.'config/c'.$pos.$endfix.'.php';
			if ($_clink) {
				if (is_file($path.'classes/'.$dir.'_auth'.$endfix.'.class.php')) {
					$obj = pc_base::load_app_class($dir.'_auth'.$endfix, $dir);
					foreach ($_clink as $k => $v) {
						// 动态名称
						if (strpos($v['name'], '_') === 0
							&& method_exists($obj, substr($v['name'], 1))) {
							$_clink[$k]['name'] = call_user_func(array($obj, substr($v['name'], 1)), $module);
						}
						// check权限验证
						if (isset($v['check']) && $v['check'] && method_exists($obj, $v['check'])
							&& !call_user_func(array($obj, $v['check']), $module, [])) {
							unset($_clink[$k]);
							continue;
						}
					}
					// 权限验证
					if ($pos == 'link' && method_exists($obj, 'is_link_auth') && $obj->is_link_auth($module)) {
						$data = array_merge($data, $_clink);
					} elseif ($pos == 'bottom' && method_exists($obj, 'is_bottom_auth') && $obj->is_bottom_auth($module)) {
						$data = array_merge($data, $_clink);
					} else {
						CI_DEBUG && log_message('debug', $dir.'_auth类（'.$path.'classes/'.$dir.'_auth'.$endfix.'.class.php）没有定义is_'.$pos.'_auth或者is_'.$pos.'_auth验证失败');
					}
				} else {
					$data = array_merge($data , $_clink);
					CI_DEBUG && log_message('debug', '配置文件（'.$path.'config/c'.$pos.$endfix.'.php'.'）没有定义权限验证类（'.$path.'classes/'.$dir.'_auth'.$endfix.'.class.php）');
				}
			}
		}
	}

	if ($data) {
		foreach ($data as $i => $t) {
			$data[$i]['displayorder'] = $i + (int)$t['displayorder'];
			if (!$t['url']) {
				unset($data[$i]); // 验证url
				CI_DEBUG && !$t['url'] && log_message('error', 'c'.$pos.'（'.$t['name'].'）没有设置url参数');
				continue;
			}
			$data[$i]['url'] = urldecode($data[$i]['url']);
		}
		uasort($data, function($a, $b){
			if($a['displayorder'] == $b['displayorder']){
				return 0;
			}
			return($a['displayorder']<$b['displayorder']) ? -1 : 1;
		});
	}

	return $data;
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

	if ($source_dir && $fp = opendir($source_dir)) {

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

	if ($source_dir && $fp = opendir($source_dir)) {

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
function pages($num, $curr_page, $perpage = 10, $urlrule = '', $array = array()) {
	if(defined('URLRULE') && $urlrule == '') {
		$urlrule = URLRULE;
		$array = $GLOBALS['URL_ARRAY'];
	} elseif($urlrule == '') {
		$urlrule = url_par('page={$page}');
	}
	$first_url = '';
	if(strpos($urlrule, '~')) {
		$urlrules = explode('~', $urlrule);
		$first_url = $urlrules[0];
		$findme = array();
		$replaceme = array();
		if (is_array($array)) foreach ($array as $k=>$v) {
			$findme[] = '{$'.$k.'}';
			$replaceme[] = $v;
		}
		$first_url = str_replace($findme, $replaceme, $first_url);
		$first_url = str_replace(array('http://','https://','//','~'), array('~','~','/',SITE_PROTOCOL), $first_url);
	}
	return $num > 0 ? pc_base::load_sys_class('input')->page(pageurl($urlrule, $curr_page, $array), $num, $perpage, $curr_page, $first_url) : '';
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
		$urlrule = $urlrules[1];
	}
	$findme = array('{$page}');
	$replaceme = array('{page}');
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
	if($url == '') $url = dr_now_url();
	list($name, $value) = explode('=', $par);
	$pos = strpos($url, '?');
	if($pos === false) {
		$url .= $value ? '?'.$par : '';
	} else {
		$querystring = substr(strstr($url, '?'), 1);
		parse_str($querystring, $pars);
		$query_array = array();
		foreach($pars as $k=>$v) {
			if($k != 'page') $query_array[$k] = $v;
		}
		if ($name && $name != 'page') {
			if (is_array($name)) {
				foreach ($name as $i => $_name) {
					if (isset($value[$i]) && strlen((string)$value[$i])) {
						$query_array[$_name] = $value[$i];
					} else {
						unset($query_array[$_name]);
					}
				}
			} else {
				if (strlen((string)$value)) {
					$query_array[$name] = $value;
				} else {
					unset($query_array[$name]);
				}
			}
		}
		if (is_array($query_array)) {
			foreach ($query_array as $i => $t) {
				if (strlen((string)$t) == 0) {
					unset($query_array[$i]);
				}
			}
		}
		$querystring = trim((is_array($query_array) ? http_build_query($query_array) : ''), '&').($name == 'page' ? '&'.$par : '');
		$url = substr($url, 0, $pos).'?'.$querystring;
	}
	return $url;
}

/**
 * 判断email格式是否正确
 * @param $email
 */
function is_email($email) {
	return check_email($email);
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
		if(!is_array($data)) return SITE_ID;
		foreach ($data as $v) {
			if ($v['url'] == FC_NOW_HOST) $siteid = $v['siteid'];
		}
	}
	if (empty($siteid)) $siteid = SITE_ID;
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
		if(!empty($field) && $field != 'nickname') {
			$memberinfo[$field] = dr_member_info($userid, $field);
			if (isset($memberinfo[$field]) &&!empty($memberinfo[$field])) {
				$return = dr_member_info($userid, $field);
			}
		} else {
			$memberinfo['nickname'] = dr_member_info($userid, 'nickname');
			$memberinfo['username'] = dr_member_info($userid, 'username');
			$return = isset($memberinfo['nickname']) && !empty($memberinfo['nickname']) ? $memberinfo['nickname'].'('.$memberinfo['username'].')' : $memberinfo['username'];
		}
	} else {
		if (param::get_cookie('_nickname')) {
			$return .= '('.param::get_cookie('_nickname').')';
		} elseif (param::get_cookie('_username')) {
			$return .= '('.param::get_cookie('_username').')';
		} else {
			$return .= '(游客)';
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
	return dr_member_info($userid, $field);
}

/**
 * 通过 username 值，获取用户所有信息
 * 获取用户信息
 * 不传入$field返回用户所有信息,
 * 传入field，取用户$field字段信息
 */
function get_memberinfo_buyusername($username, $field='') {
	return dr_member_username_info($username, $field);
}

/**
 * 调用会员详细信息（自定义字段需要手动格式化）
 *
 * @param   $userid    会员userid
 * @param   $name   输出字段
 * @param   $cache  缓存时间
 * @return  用户详情数组
 */
function dr_member_info($userid, $name = '', $cache = -1) {
	if (!$userid) {
		return '';
	}
	$data = pc_base::load_sys_class('cache')->get_data('member-info-'.$userid);
	if (!$data) {
		$member_db = pc_base::load_model('member_model');
		$data = $member_db->get_one(array('userid'=>$userid));
		$member_db->set_model($data['modelid']);
		$member_modelinfo = $member_db->get_one(array('userid'=>$userid));
		if(is_array($member_modelinfo)) {
			$data = array_merge($data, $member_modelinfo);
		}
		$member_db->set_model();
		if ($data) {
			$data['avatar'] = get_memberavatar($data['userid']);
			$data['password'] = '***';
			$data['encrypt'] = '***';
		}
		SYS_CACHE && pc_base::load_sys_class('cache')->set_data('member-info-'.$userid, $data, $cache > 0 ? $cache : SYS_CACHE_SHOW * 3600);
	}
	return $name ? $data[$name] : $data;
}

/**
 * 调用会员详细信息（自定义字段需要手动格式化）
 *
 * @param   $username   会员账号
 * @param   $name   输出字段
 * @param   $cache  缓存时间
 * @return  用户详情数组
 */
function dr_member_username_info($username, $name = '', $cache = -1) {
	if (dr_is_empty($username)) {
		return '';
	}
	$data = pc_base::load_sys_class('cache')->get_data('member-info-name-'.$username);
	if (!$data) {
		$member_db = pc_base::load_model('member_model');
		$data = $member_db->get_one(array('username'=>$username));
		$member_db->set_model($data['modelid']);
		$member_modelinfo = $member_db->get_one(array('userid'=>$data['userid']));
		if(is_array($member_modelinfo)) {
			$data = array_merge($data, $member_modelinfo);
		}
		$member_db->set_model();
		if ($data) {
			$data['avatar'] = get_memberavatar($data['userid']);
			$data['password'] = '***';
			$data['encrypt'] = '***';
		}
		SYS_CACHE && pc_base::load_sys_class('cache')->set_data('member-info-name-'.$username, $data, $cache > 0 ? $cache : SYS_CACHE_SHOW * 3600);
	}
	return $name ? $data[$name] : $data;
}

if (!function_exists('dr_letter_avatar')) {
	/**
	 * 首字母头像
	 * @param $text
	 * @return string
	 */
	function dr_letter_avatar($text) {
		if (dr_is_empty($text)) {
			return '';
		}
		$total = unpack('L', hash('adler32', $text, true))[1];
		$hue = $total % 360;
		list($r, $g, $b) = dr_hsv2rgb($hue / 360, 0.3, 0.9);
		$bg = "rgb({$r},{$g},{$b})";
		$color = "#ffffff";
		$first = mb_strtoupper(mb_substr($text, 0, 1));
		$src = base64_encode('<svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="100" width="100"><rect fill="' . $bg . '" x="0" y="0" width="100" height="100"></rect><text x="50" y="50" font-size="50" text-copy="fast" fill="' . $color . '" text-anchor="middle" text-rights="admin" dominant-baseline="central">' . $first . '</text></svg>');
		$value = 'data:image/svg+xml;base64,' . $src;
		return $value;
	}
}

if (!function_exists('dr_hsv2rgb')) {
	function dr_hsv2rgb($h, $s, $v) {
		$r = $g = $b = 0;
		$i = floor($h * 6);
		$f = $h * 6 - $i;
		$p = $v * (1 - $s);
		$q = $v * (1 - $f * $s);
		$t = $v * (1 - (1 - $f) * $s);
		switch ($i % 6) {
			case 0:
				$r = $v;
				$g = $t;
				$b = $p;
				break;
			case 1:
				$r = $q;
				$g = $v;
				$b = $p;
				break;
			case 2:
				$r = $p;
				$g = $v;
				$b = $t;
				break;
			case 3:
				$r = $p;
				$g = $q;
				$b = $v;
				break;
			case 4:
				$r = $t;
				$g = $p;
				$b = $v;
				break;
			case 5:
				$r = $v;
				$g = $p;
				$b = $q;
				break;
		}
		return array(
			floor($r * 255),
			floor($g * 255),
			floor($b * 255)
		);
	}
}

/**
 * 获取后台登录信息
 */
function admin_get_log($uid) {
	$admin_login_db = pc_base::load_model('admin_login_model');
	if ($uid) {
		$row = $admin_login_db->get_one(array('uid'=>$uid));
		if (!$row) {
			$row = array(
				'uid' => $uid,
				'is_login' => 0,
				'is_repwd' => 0,
				'updatetime' => 0,
			);
			$id = $admin_login_db->insert($row, true);
			$row['id'] = $id;
		}
		$loguid = $row;
	}
	return $loguid;
}

/**
 * 获取会员登录信息
 */
function member_get_log($uid) {
	$member_login_db = pc_base::load_model('member_login_model');
	if ($uid) {
		$row = $member_login_db->get_one(array('uid'=>$uid));
		if (!$row) {
			$row = array(
				'uid' => $uid,
				'is_login' => 0,
				'is_repwd' => 0,
				'updatetime' => 0,
			);
			$id = $member_login_db->insert($row, true);
			$row['id'] = $id;
		}
		$loguid = $row;
	}
	return $loguid;
}

if (!function_exists('icon')) {
	/**
	 * 生成后缀图标
	 * @param string $icon 后缀
	 * @param null   $background
	 * @return string
	 */
	function icon($icon, $background = '') {
		header('Content-Type:image/svg+xml');
		$suffix = $icon ? $icon : 'FILE';
		$data = build_suffix_image($suffix, $background);
		$offset = 30 * 60 * 60 * 24; // 缓存一个月
		header('Cache-Control: public');
		header('Pragma: cache');
		header('Expires: ' . gmdate('D, d M Y H:i:s', SYS_TIME + $offset) . ' GMT');
		return $data;
	}
}

if (!function_exists('build_suffix_image')) {
	/**
	 * 生成文件后缀图片
	 * @param string $suffix 后缀
	 * @param null   $background
	 * @return string
	 */
	function build_suffix_image($suffix, $background = '') {
		$suffix = mb_substr(strtoupper($suffix), 0, 4);
		$total = unpack('L', hash('adler32', $suffix, true))[1];
		$hue = $total % 360;
		list($r, $g, $b) = dr_hsv2rgb($hue / 360, 0.3, 0.9);

		$background = $background ? $background : 'rgb('.$r.','.$g.','.$b.')';

		$icon = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
			<path style="fill:#E2E5E7;" d="M128,0c-17.6,0-32,14.4-32,32v448c0,17.6,14.4,32,32,32h320c17.6,0,32-14.4,32-32V128L352,0H128z"/>
			<path style="fill:#B0B7BD;" d="M384,128h96L352,0v96C352,113.6,366.4,128,384,128z"/>
			<polygon style="fill:#CAD1D8;" points="480,224 384,128 480,128 "/>
			<path style="fill:'.$background.';" d="M416,416c0,8.8-7.2,16-16,16H48c-8.8,0-16-7.2-16-16V256c0-8.8,7.2-16,16-16h352c8.8,0,16,7.2,16,16 V416z"/>
			<path style="fill:#CAD1D8;" d="M400,432H96v16h304c8.8,0,16-7.2,16-16v-16C416,424.8,408.8,432,400,432z"/>
			<g><text><tspan x="220" y="380" font-size="124" font-family="Verdana, Helvetica, Arial, sans-serif" fill="white" text-anchor="middle">'.$suffix.'</tspan></text></g>
		</svg>';
		return $icon;
	}
}

/**
 * 获取用户头像
 * @param $uid 默认为userid
 * @param $size 头像大小
 */
function get_memberavatar($uid, $size = '') {
	if (!$uid) {
		return IMG_PATH.'nophoto.gif';
	}
	$db = pc_base::load_model('member_model');
	$memberinfo = $db->get_one(array('userid'=>$uid));
	$att_db = pc_base::load_model('attachment_model');
	$avatar_db = $att_db->get_one(array('aid'=>$memberinfo['avatar']));
	if ($avatar_db) {
		if ($size) {
			$avatar = pc_base::load_sys_class('image')->thumb((SYS_ATTACHMENT_SAVE_ID ? dr_get_file_url($avatar_db) : dr_file(SYS_AVATAR_URL.$avatar_db['filepath'])), $size, $size, 0, 'auto', 1);
		} else {
			$avatar = (SYS_ATTACHMENT_SAVE_ID ? dr_get_file_url($avatar_db) : dr_file(SYS_AVATAR_URL.$avatar_db['filepath']));
		}
	} else {
		$avatar = dr_letter_avatar($memberinfo['nickname'] ? $memberinfo['nickname'] : $memberinfo['username']);
	}
	return $avatar;
}

/**
 * 调用关联菜单
 * @param $code 联动菜单代码
 * @param $id 生成联动菜单的样式id
 * @param $defaultvalue 默认值
 * @param $ck_child 强制选择最终项
 * @param $multiple 多选
 * @param $limit 最大选择数
 * @param $width 控件宽度
 * @param $collapse 折叠显示到一行
 */
function menu_linkage($code = '', $id = 'linkid', $defaultvalue = 0, $ck_child = 0, $multiple = 0, $limit = 0, $width = 0, $collapse = 0) {
	$linkage_db = pc_base::load_model('linkage_model');
	$data = $linkage_db->get_one(array('code'=>$code));
	if ($data['style']) {
		// 最大几层
		$linklevel = dr_linkage_level($code) + 1;
		$string = load_js(JS_PATH.'jquery.ld.js');
		$level = 1;
		if ($multiple) {
			// 表单宽度设置
			$width = is_mobile() ? '100%' : ($width ? $width : '100%');
			// 输出默认菜单
			$string.= '<div class="dropzone-file-area" id="linkages-'.$id.'-sort-items" style="width:'.$width.(is_numeric($width) ? 'px' : '').';text-align:left;">';
			$tpl = '<div class="linkages_'.$id.'_row" id="dr_linkages_'.$id.'_row_{id}">';
			$tpl.= '<label style="margin-right: 10px;"><a class="btn btn-sm" href="javascript:;" onclick="$(\'#dr_linkages_'.$id.'_row_{id}\').remove()"> <i class="fa fa-close"></i> </a></label>';
			$tpl.= '<input type="hidden" name="info['.$id.'][{id}]" id="dr_'.$id.'_{id}" value="{value}" />';
			$tpl.= '<input type="hidden" id="dr_'.$id.'_{id}_default" value="" />';
			$tpl.= '<span id="dr_linkages_'.$id.'_select_{id}" style="display:{display}">';
			for ($i = 1; $i <= $linklevel; $i++) {
				$style = $i > $level ? ' style="display:none"' : '';
				$tpl.= '<label style="padding-right:10px;"><select class="form-control cms-selects-'.$id.'-{id}" name="'.$id.'-'.$i.'-{id}" id="'.$id.'-'.$i.'-{id}" width="100"'.$style.'><option defaultvalue=""> -- </option></select></label>';
			}
			$tpl.= '</span>';
			$tpl.= '</div>';

			// 字段默认值
			$values = dr_string2array($defaultvalue);
			if ($values) {
				foreach ($values as $j => $value) {
					if ($value) {
						$link = dr_linkage($code, $value);
						if (!$link) {
							continue;
						}
						$pids = substr((string)$link['pids'], 2);
						$level = substr_count($pids, ',') + 1;
						$default = !$pids ? '["'.$value.'"]' : '["'.str_replace(',', '","', $pids).'","'.$value.'"]';
						$string.= '<div class="linkages_'.$id.'_row" id="dr_linkages_'.$id.'_row_'.$j.'">';
						$string.= '<label style="margin-right: 10px;"><a class="btn btn-sm" href="javascript:;" onclick="$(\'#dr_linkages_'.$id.'_row_'.$j.'\').remove()"> <i class="fa fa-close"></i> </a></label>';
						$string.= '<input type="hidden" name="info['.$id.']['.$j.']" id="dr_'.$id.'_'.$j.'" value="'.$value.'" />';
						$string.= '<input type="hidden" id="dr_'.$id.'_'.$j.'_default" value="'.addslashes($default).'" />';
						$string.= '<span id="dr_linkages_'.$id.'_select_'.$j.'" style="display:none">';
						for ($i = 1; $i <= $linklevel; $i++) {
							$style = $i > $level ? ' style="display:none"' : '';
							$string.= '<label style="padding-right:10px;"><select class="form-control cms-selects-'.$id.'-'.$j.'" name="'.$id.'-'.$i.'-'.$j.'" id="'.$id.'-'.$i.'-'.$j.'" width="100"'.$style.'><option defaultvalue=""> -- </option></select></label>';
						}
						$string.= '</span>';
						$string.= '<label class="form-control-static" id="dr_linkages_'.$id.'_cxselect_'.$j.'">'.dr_linkagepos($code, $value, ' » ').'&nbsp;&nbsp;<a href="javascript:;" onclick="dr_linkages_select_'.$id.'('.$j.')" style="color:blue">'.L('[重新选择]').'</a></label>';
						$string.= '</div>';
					}
				}
			}

			// 整体
			$key_html = '';
			$key_html.= 'if ($ld5.eq(0).show().val()=="--") {';
			for ($i = 2; $i <= $linklevel; $i++) {
				$key_html.= '
						$ld5.eq('.$i.').hide();';
			}
			$key_html.= '
					}';
			$string.= '</div>';
			$string.= '<div class="margin-top-10">	<a href="javascript:;" class="btn blue btn-sm" onClick="dr_add_linkages_'.$id.'()"> <i class="fa fa-plus"></i> '.L('添加').' </a>';
			$string.= '</div>';
			$string.= load_css(JS_PATH.'jquery-ui/jquery-ui.min.css');
			$string.= load_js(JS_PATH.'jquery-ui/jquery-ui.min.js');
			$string.= '<script type="text/javascript">
			$("#linkages-'.$id.'-sort-items").sortable();
			function dr_add_linkages_'.$id.'() {
				var num = $("#linkages-'.$id.'-sort-items .linkages_'.$id.'_row").length;
				if ('.(int)$limit.' > 0 && num >= '.(int)$limit.') {
					dr_tips(0, "'.L('最多可以选择'.$limit.'项').'");
					return;
				}
				var id=(num + 1) * 10;
				var html = "'.addslashes($tpl).'";
				html = html.replace(/\{id\}/g, id);
				html = html.replace(/\{display\}/g, "blank");
				html = html.replace(/\{value\}/g, "0");
				$("#linkages-'.$id.'-sort-items").append(html);
				dr_linkages_init_'.$id.'(id);
			}
			function dr_linkages_select_'.$id.'(id) {
				$("#dr_linkages_'.$id.'_select_"+id).show();
				$("#dr_linkages_'.$id.'_cxselect_"+id).hide();
				dr_linkages_init_'.$id.'(id);
			}
			function dr_linkages_init_'.$id.'(id) {
			  var $ld5 = $(".cms-selects-'.$id.'-"+id);					  
				$ld5.ld({ajaxOptions:{"url": "'.WEB_PATH.'api.php?op=get_linkage&code='.$code.'"},defaultParentId:0})
				var ld5_api = $ld5.ld("api");
				ld5_api.selected($("#dr_'.$id.'_"+id+"_default").val());
				$ld5.bind("change", function(e){
					var $target = $(e.target);
					var index = $ld5.index($target);
					$("#dr_'.$id.'_"+id).val($ld5.eq(index).show().val());
					index ++;
					$ld5.eq(index).show();
					'.$key_html.'
				});
				
			}
			</script>';
		} else {
			$string.= $defaultvalue && (ROUTE_A=='edit' || ROUTE_A=='account_manage_info'  || ROUTE_A=='info_publish') ? '<input type="hidden" name="info['.$id.']"  id="dr_'.$id.'" value="'.(int)$defaultvalue.'">' : '<input type="hidden" name="info['.$id.']"  id="dr_'.$id.'" value="0">'.PHP_EOL;
			$default = '';
			if ($defaultvalue) {
				$link = dr_linkage($code, $defaultvalue);
				$pids = substr($link['pids'], 2);
				$level = substr_count($pids, ',') + 1;
				$default = !$pids ? '["'.$defaultvalue.'"]' : '["'.str_replace(',', '","', $pids).'","'.$defaultvalue.'"]';
			}
			// 输出默认菜单
			$string.= '<span id="dr_linkage_'.$id.'_select" style="'.($defaultvalue ? 'display:none' : '').'">';
			for ($i = 1; $i <= $linklevel; $i++) {
				$style = $i > $level ? ' style="display:none"' : '';
				$string.= '<label style="padding-right:10px;"><select class="form-control select-'.$id.'" name="'.$id.'-'.$i.'" id="'.$id.'-'.$i.'" width="100"'.$style.'><option defaultvalue=""> -- </option></select></label>';
			}
			$string.= '<label id="dr_linkage_'.$id.'_html"></label>';
			$string.= '</span>';
			// 重新选择
			if ($defaultvalue) {
				$string.= '<div id="dr_linkage_'.$id.'_cxselect">';
				$edit_html = '<div class="form-control-static" >'.dr_linkagepos($code, $defaultvalue, ' » ').'&nbsp;&nbsp;<a href="javascript:;" onclick="dr_linkage_select_'.$id.'()" style="color:blue">'.L('[重新选择]').'</a></div>';
				$string.= $edit_html;
				$string.= '</div>';
			}
			// 输出js支持
			$key_html = '';
			$key_html.= 'if ($ld5.eq(0).show().val()=="--") {';
			for ($i = 2; $i <= $linklevel; $i++) {
				$key_html.= '
						$ld5.eq('.$i.').hide();';
			}
			$key_html.= '
					}';
			$string.= '
			<script type="text/javascript">
			function dr_linkage_select_'.$id.'() {
				$("#dr_linkage_'.$id.'_select").show();
				$("#dr_linkage_'.$id.'_cxselect").hide();
			}
			$(function(){
				var $ld5 = $(".select-'.$id.'");					  
				$ld5.ld({ajaxOptions:{"url": "'.WEB_PATH.'api.php?op=get_linkage&code='.$code.'"},inputId:"dr_linkage_'.$id.'_html",defaultParentId:0});
				var ld5_api = $ld5.ld("api");
				ld5_api.selected('.$default.');
				$ld5.bind("change", function(e){
					var $target = $(e.target);
					var index = $ld5.index($target);
					//$("#'.$id.'-'.$i.'").remove();
					var vv = $ld5.eq(index).show().val();
					$("#dr_'.$id.'").val(vv);
					index ++;
					$ld5.eq(index).show();
					'.$key_html.'
					//console.log("value="+vv);
				});
			})
			</script>';
		}
	} else {
		$string .= load_css(JS_PATH.'layui/css/layui.css');
		$string .= load_css(JS_PATH.'layui/cascader/cascader.css');
		$string .= load_js(JS_PATH.'layui/layui.js');
		$string .= load_js(JS_PATH.'layui/cascader/cascader.js');
		$string .= ($defaultvalue && (ROUTE_A=='edit' || ROUTE_A=='account_manage_info'  || ROUTE_A=='info_publish') ? '<input type="hidden" name="info['.$id.']"  id="dr_'.$id.'" value="'.($multiple ? ($defaultvalue ? $defaultvalue : '[]') : (int)$defaultvalue).'">' : '<input type="hidden" name="info['.$id.']"  id="dr_'.$id.'" value="'.($multiple ? '[]' : 0).'">').PHP_EOL;
		$string .= '<script src="'.WEB_PATH.'api.php?op=get_linkage&code='.$code.'"></script>
			<script type="text/javascript">
			$(function (){
				layui.use(\'layCascader\', function () {
					var layCascader = layui.layCascader;
					layCascader({
						elem: \'#dr_'.$id.'\',
						value: '.($multiple ? ($defaultvalue ? $defaultvalue : '[]') : '\''.(int)$defaultvalue.'\'').',
						clearable: true,
						filterable: '.($multiple ? 'false' : 'true').','.($multiple ? '
						maxSize: '.intval($limit).',
						collapseTags: '.($collapse ? 'false' : 'true').',
						minCollapseTagsNumber: 0,' : '').'
						props: {'.($multiple ? '
							multiple: true,' : '').'
							checkStrictly: '.($ck_child ? 'false' : 'true').'
						},
						options: linkage_'.$code.'
					});
				})
			});
			</script>';
	}
	return $string;
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
 * @param   intval  $id     联动菜单ID
 * @param   string  $keyid   菜单keyid
 * @param   string  $space 间隔符号
 * @param   string  $url    url地址格式，必须存在[linkage]，否则返回不带url的字符串
 * @param   string  $html   格式替换
 */
function get_linkage($id, $keyid, $space = '>', $url = '', $html = '') {
	$linkage_db = pc_base::load_model('linkage_model');
	if($space=='' || !isset($space))$space = '>';
	$link = $linkage_db->get_one(array('id'=>$keyid));
	if ($link) {
		return dr_linkagepos($link['code'], $id, $space, $url, $html);
	}
	return '';
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
function create_randomstr($lenth = 10, $chars = '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ') {
	$hash = '';
	$max = strlen($chars) - 1;
	mt_srand();
	for($i = 0; $i < $lenth; $i++) {
		$hash .= $chars[mt_rand(0, $max)];
	}
	return substr(md5($hash), 0, $lenth);
}

// 验证账号
function check_username($value) {
	$member_setting = getcache('member_setting', 'member');

	if (!$value) {
		return dr_return_data(0, L('账号不能为空'), array('field' => 'username'));
	} elseif ($member_setting['config']['preg']
		&& !preg_match($member_setting['config']['preg'], $value)) {
		// 验证账号的组成格式
		return dr_return_data(0, L('账号格式不正确'), array('field' => 'username'));
	} elseif (strpos($value, '"') !== false || strpos($value, '<') !== false || strpos($value, '>') !== false || strpos($value, '\'') !== false) {
		// 引号判断
		return dr_return_data(0, L('账号名存在非法字符'), array('field' => 'username'));
	} elseif ($member_setting['config']['userlen']
		&& mb_strlen($value) < $member_setting['config']['userlen']) {
		// 验证账号长度
		return dr_return_data(0, L('账号长度不能小于'.$member_setting['config']['userlen'].'位，当前'.mb_strlen($value).'位'), array('field' => 'username'));
	} elseif ($member_setting['config']['userlenmax']
		&& mb_strlen($value) > $member_setting['config']['userlenmax']) {
		// 验证账号长度
		return dr_return_data(0, L('账号长度不能大于'.$member_setting['config']['userlenmax'].'位，当前'.mb_strlen($value).'位'), array('field' => 'username'));
	}
	$notallow = [$member_setting['notallow']];
	$notallow[] = L('游客');
	// 后台不允许注册的词语，放在最后一次比较
	foreach ($notallow as $a) {
		if (dr_strlen($a) && strpos($value, $a) !== false) {
			return dr_return_data(0, L('账号名不允许注册'), array('field' => 'username'));
		}
	}

	return dr_return_data(1, 'ok');
}

// 验证账号的密码
function check_password($value, $username) {
	$member_setting = getcache('member_setting', 'member');

	if (!$value) {
		return dr_return_data(0, L('密码不能为空'), array('field' => 'password'));
	} elseif (!$member_setting['config']['user2pwd'] && $value == $username) {
		return dr_return_data(0, L('密码不能与账号相同'), array('field' => 'password'));
	} elseif ($member_setting['config']['pwdpreg']
		&& !preg_match(trim($member_setting['config']['pwdpreg']), $value)) {
		return dr_return_data(0, L('密码格式不正确'), array('field' => 'password'));
	} elseif ($member_setting['config']['pwdlen']
		&& mb_strlen($value) < $member_setting['config']['pwdlen']) {
		return dr_return_data(0, L('密码长度不能小于'.$member_setting['config']['pwdlen'].'位，当前'.mb_strlen($value).'位'), array('field' => 'password'));
	} elseif ($member_setting['config']['pwdmax']
		&& mb_strlen($value) > $member_setting['config']['pwdmax']) {
		return dr_return_data(0, L('密码长度不能大于'.$member_setting['config']['pwdmax'].'位，当前'.mb_strlen($value).'位'), array('field' => 'password'));
	}

	return dr_return_data(1, 'ok');
}

// 验证手机号码
function check_phone($value) {
	if (!$value) {
		return false;
	} elseif (!is_numeric($value)) {
		return false;
	} elseif (strlen($value) != 11) {
		return false;
	}
	return true;
}

// 验证邮件地址
function check_email($value) {
	if (!$value) {
		return false;
	} elseif (!preg_match('/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/', $value)) {
		return false;
	} elseif (strpos($value, '"') !== false || strpos($value, '\'') !== false) {
		return false;
	}
	return true;
}

/**
 * 检查密码长度是否符合规定
 *
 * @param STRING $password
 * @return 	TRUE or FALSE
 */
function is_password($password) {
	$member_setting = getcache('member_setting', 'member');
	$strlen = mb_strlen($password);
	if($member_setting['config']['pwdpreg'] && !preg_match(trim($member_setting['config']['pwdpreg']), $password)) return false;
	if(($member_setting['config']['pwdlen'] && $strlen < $member_setting['config']['pwdlen']) || ($member_setting['config']['pwdmax'] && $strlen > $member_setting['config']['pwdmax'])) return false;
	return true;
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
	$member_setting = getcache('member_setting', 'member');
	$strlen = mb_strlen($username);
	if(is_badword($username) || ($member_setting['config']['preg'] && !preg_match($member_setting['config']['preg'], $username))){
		return false;
	} elseif (($member_setting['config']['userlen'] && $strlen < $member_setting['config']['userlen']) || ($member_setting['config']['userlenmax'] && $strlen > $member_setting['config']['userlenmax'])) {
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
	return is_array($id) ? dr_array_intersect($id, $ids) : (is_array(dr_string2array($id)) ? dr_array_intersect(dr_string2array($id), $ids) : dr_in_array($id, $ids));
}

// 查询会员信息
function find_member_info($username) {
	$member_db = pc_base::load_model('member_model');
	$member_setting = getcache('member_setting', 'member');
	$data = $member_db->get_one(array('username'=>$username));
	if (!$data && $member_setting['login']['field']) {
		if (dr_in_array('email', $member_setting['login']['field'])
			&& check_email($username)) {
			$data = $member_db->get_one(array('email'=>$username));
		} elseif (dr_in_array('phone', $member_setting['login']['field'])
			&& check_phone($username)) {
			$data = $member_db->get_one(array('mobile'=>$username));
		}
	}
	if (!$data) {
		return array();
	}
	return $data;
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
function thumb($img, $width = 0, $height = 0, $water = 0, $mode = 'auto', $webimg = 0) {
	if (!$img) {
		return IMG_PATH.'nopic.gif';
	} elseif (is_array($img)) {
		return IS_DEV ? '文件参数不能是数组' : IMG_PATH.'nopic.gif';
	} elseif (!$width || !$height) {
		return dr_get_file($img).(IS_DEV ? '#没有设置高宽参数，将以原图输出' : '');
	} elseif (is_numeric($img) || $webimg) {
		// 强制缩略图水印
		if (dr_site_value('thumb', (defined('SITEID') && SITEID ? SITEID : get_siteid()))) {
			$water = 1;
		}
		// 钩子处理
		$rs = pc_base::load_sys_class('hooks')::trigger_callback('thumb', $img, $width, $height, $water, $mode, $webimg);
		if ($rs && isset($rs['code']) && $rs['code'] && $rs['msg']) {
			return $rs['msg'];
		}
		return pc_base::load_sys_class('image')->thumb($img, $width, $height, $water, $mode, $webimg);
	}
	$file = dr_file($img);
	if ($file && CI_DEBUG && !is_numeric($img)) {
		$file .= '#图片不是数字id号，thumb函数无法进行缩略图处理'; 
	}
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
function dr_catpos($catid, $symbol = ' > ', $url = true, $html = '') {
	if (!$catid) {
		return '';
	}
	$cat = array();
	$siteids = getcache('category_content','commons');
	$siteid = $siteids[$catid];
	$cat = get_category($siteid);
	if (!isset($cat[$catid])) {
		return '';
	}
	$siteurl = siteurl($cat[$catid]['siteid']);
	$name = array();
	$array = explode(',', $cat[$catid]['arrparentid']);
	$array[] = $catid;
	foreach ($array as $id) {
		if (!$id) {
			continue;
		}
		$setting = dr_string2array(dr_cat_value($id, 'setting'));
		if ($id && $cat[$id] && $setting['iscatpos']) {
			$murl = $cat[$id]['url'];
			if(strpos($murl, '://') === false) $murl = $siteurl.$murl;
			$name[] = $url ? ($html ? str_replace(array('[url]', '[name]'), array($murl, $cat[$id]['catname']), $html) : '<a href="'.$murl.'">'.$cat[$id]['catname'].'</a>') : $cat[$id]['catname'];
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
function dr_mobile_catpos($catid, $symbol = ' > ', $url = true, $html = '') {
	if (!$catid) {
		return '';
	}
	$cat = array();
	$siteids = getcache('category_content','commons');
	$siteid = $siteids[$catid];
	$cat = get_category($siteid);
	if (!isset($cat[$catid])) {
		return '';
	}
	$siteurl = siteurl($cat[$catid]['siteid']);
	$sitemobileurl = sitemobileurl($cat[$catid]['siteid']);
	$name = array();
	$array = explode(',', $cat[$catid]['arrparentid']);
	$array[] = $catid;
	foreach ($array as $id) {
		if (!$id) {
			continue;
		}
		$setting = dr_string2array(dr_cat_value($id, 'setting'));
		if ($id && $cat[$id] && $setting['iscatpos']) {
			$murl = str_replace($siteurl, $sitemobileurl, $cat[$id]['url']);
			if(strpos($murl, '://') === false) $murl = $sitemobileurl.$murl;
			$name[] = $url ? ($html ? str_replace(array('[url]', '[name]'), array($murl, $cat[$id]['catname']), $html) : '<a href="'.$murl.'">'.$cat[$id]['catname'].'</a>') : $cat[$id]['catname'];
		}
	}
	return implode($symbol, array_unique($name));
}
// 获取全部栏目
function get_category($siteid = '') {
	if (!$siteid) $siteid = get_siteid();
	return pc_base::load_sys_class('cache')->get_file(
		'cache',
		'module/category-'.$siteid.'-data'
	);
}
// 获取下级子栏目
function get_child($catid) {
	$siteids = getcache('category_content','commons');
	$siteid = $siteids[$catid];
	if (!$siteid) $siteid = get_siteid();
	return pc_base::load_sys_class('cache')->get_file(
		$catid,
		'module/category-'.$siteid.'-child'
	);
}
// 通过目录找id
function get_catid($dir, $siteid = '') {
	if (!$siteid) $siteid = get_siteid();
	$cats = pc_base::load_sys_class('cache')->get_file(
		'dir',
		'module/category-'.$siteid.'-data'
	);
	return isset($cats[$dir]) ? $cats[$dir] : 0;
}
/**
 * 栏目下级或者同级栏目
 */
function dr_related_cat($my) {
	$related = $parent = [];

	if (!$my) {
		$my = [
			'parentid' => '',
			'child' => '',
		];
	}

	if ($my['child']) {
		// 当存在子栏目时就显示下级子栏目
		$parent = $my['parentid'] ? dr_cat_value($my['parentid']) : $my;
		$child = get_child($my['catid']);
		foreach ($child as $catid) {
			$t = dr_cat_value($catid);
			if (!$t) {
				continue;
			}
			$t['setting'] = dr_string2array($t['setting']);
			if (!$t['setting']['isleft']) {
				continue;
			}
			$related[$t['catid']] = $t;
		}
	} elseif ($my['parentid']) {
		// 当属于子栏目时就显示同级别栏目
		$child = get_child($my['parentid']);
		if ($child) {
			foreach ($child as $catid) {
				$t = dr_cat_value($catid);
				if (!$t) {
					continue;
				}
				$t['setting'] = dr_string2array($t['setting']);
				if (!$t['setting']['isleft']) {
					continue;
				}
				$related[$t['catid']] = $t;
			}
		}
		$parent = dr_cat_value($my['parentid']);
	} else {
		// 显示顶级栏目
		$parent = [];
		$child = get_child(0);
		if ($child) {
			foreach ($child as $catid) {
				$t = dr_cat_value($catid);
				if (!$t) {
					continue;
				}
				$t['setting'] = dr_string2array($t['setting']);
				if (!$t['setting']['isleft']) {
					continue;
				}
				$related[$t['catid']] = $t;
			}
		}
	}

	return [$parent, $related];
}
/**
 * 联动菜单包屑导航
 *
 * @param   string  $code   联动菜单代码
 * @param   intval  $id     id
 * @param   string  $symbol 间隔符号
 * @param   string  $url    url地址格式，必须存在[linkage]，否则返回不带url的字符串
 * @param   string  $html   格式替换
 * @return  string
 */
function dr_linkagepos($code, $id, $symbol = ' > ', $url = '', $html = '') {
	if (!$code || !$id) {
		return '';
	}
	$url = $url ? urldecode($url) : '';
	$data = dr_linkage($code, $id, 0);
	if (!$data) {
		return '';
	}
	$name = array();
	$array = explode(',', $data['pids']);
	$array[] = $data['ii'];
	foreach ($array as $ii) {
		if ($ii) {
			$data = dr_linkage($code, $ii, 0);
			if ($url) {
				$name[] = ($html ? str_replace(array('[url]', '[name]'), array(str_replace(array('[linkage]', '{linkage}', '[id]', '{id}'), array($data['id'], $data['id'], $data['ii'], $data['ii']), $url), $data['name']), $html) : "<a href=\"".str_replace(array('[linkage]', '{linkage}', '[id]', '{id}'), array($data['id'], $data['id'], $data['ii'], $data['ii']), $url)."\">{$data['name']}</a>");
			} else {
				$name[] = $data['name'];
			}
		}
	}
	return implode($symbol, array_unique($name));
}

/**
 * 联动菜单调用
 *
 * @param   string  $code   菜单代码
 * @param   intval  $id     菜单id
 * @param   intval  $level  调用级别，1表示顶级，2表示第二级，等等
 * @param   string  $name   菜单名称，如果有显示它的值，否则返回数组
 * @return  array
 */
function dr_linkage($code, $id, $level = 0, $name = '') {
	if (!$id) {
		return false;
	}
	// id 查询
	if (is_numeric($id)) {
		$id = dr_linkage_id($code, $id);
		if (!$id) {
			return false;
		}
	}
	$data = pc_base::load_sys_class('cache')->get_file('data-'.$id, 'linkage/'.$code.'/');
	if (!$data) {
		return false;
	}
	$pids = explode(',', $data['pids']);
	if ($level == 0) {
		return $name ? $data[$name] : $data;
	}
	if (!$pids) {
		return $name ? $data[$name] : $data;
	}
	$i = 1;
	foreach ($pids as $pid) {
		if ($pid) {
			if ($i == $level) {
				$link = dr_linkage($code, $pid, 0);
				return $name ? $link[$name] : $link;
			}
			$i++;
		}
	}
	return $name ? $data[$name] : $data;
}
/**
 * 联动菜单json数据
 *
 * @param   string  $code   菜单代码
 * @param   intval  $pid    菜单父级id或者别名
 * @return  array
 */
function dr_linkage_json($code) {
	if (!$code) {
		return array();
	}
	return pc_base::load_sys_class('cache')->get_file('json', 'linkage/'.$code.'/');
}
/**
 * 联动菜单列表数据
 *
 * @param   string  $code   菜单代码
 * @param   intval  $pid    菜单父级id或者别名
 * @return  array
 */
function dr_linkage_list($code, $pid) {
	if (!$code) {
		return false;
	}
	if ($pid && !is_numeric($pid)) {
		// 别名情况时获取id号
		$pid = dr_linkage_cname($code, $pid);
	}
	return pc_base::load_sys_class('cache')->get_file('list-'.$pid, 'linkage/'.$code.'/');
}
/**
 * 联动菜单的id号获取
 *
 * @param   string  $code   菜单代码
 * @param   string  $cname  别名
 * @return  array
 */
function dr_linkage_id($code, $cname) {
	if (!$code || !$cname) {
		return false;
	}
	$ids = pc_base::load_sys_class('cache')->get_file('id', 'linkage/'.$code.'/');
	if (isset($ids[$cname]) && $ids[$cname]) {
		return $ids[$cname];
	}
	return false;
}
/**
 * 联动菜单的别名获取
 *
 * @param   string  $code   菜单代码
 * @param   int     $id     id
 * @return  array
 */
function dr_linkage_cname($code, $id) {
	if (!$code || !$id) {
		return 0;
	}
	$ids = array_flip(pc_base::load_sys_class('cache')->get_file('id', 'linkage/'.$code.'/'));
	if (isset($ids[$id]) && $ids[$id]) {
		return $ids[$id];
	}
	return 0;
}
/**
 * 联动菜单的最大层级
 *
 * @param   string  $code   菜单代码
 * @return  array
 */
function dr_linkage_level($code) {
	if (!$code) {
		return 0;
	}
	return (int)pc_base::load_sys_class('cache')->get_file('level', 'linkage/'.$code.'/');
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
function catpos($catid, $symbol = ' > ', $url = true, $html = '') {
	return dr_catpos($catid, $symbol, $url, $html);
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
function mobilecatpos($catid, $symbol = ' > ', $url = true, $html = '') {
	return dr_mobile_catpos($catid, $symbol, $url, $html);
}

/**
 * 根据catid获取子栏目数据的sql语句
 * @param string $dir 缓存目录名
 * @param intval $catid 栏目ID
 */

function get_sql_catid($dir = 'module/category-1-data', $catid = 0) {
	$category = getcache('cache', $dir);
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
	$category = get_category($siteid);
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
function dr_go($catid, $id, $allurl = 0, $mobile = 0) {
	$siteids = getcache('category_content','commons');
	$siteid = $siteids[$catid];
	$category = get_category($siteid);
	$id = intval($id);
	if(!$id || !isset($category[$catid])) return '';
	$modelid = $category[$catid]['modelid'];
	if(!$modelid) return '';
	$db = pc_base::load_model('content_model');
	$db->set_model($modelid);
	$r = $db->get_one(array('id'=>$id), '`url`');
	if (!empty($allurl)) {
		if (strpos($r['url'], '://')===false) {
			if ($mobile) {
				$r['url'] = substr(dr_site_info('mobile_domain', $category[$catid]['siteid']), 0, -1).$r['url'];
			} else {
				$r['url'] = substr(dr_site_info('domain', $category[$catid]['siteid']), 0, -1).$r['url'];
			}
		}
	}

	return $r['url'];
}

/**
 * 将附件地址转换为绝对地址
 * @param $path 附件地址
 */
function atturl($path, $mobile = 0) {
	if(strpos($path, ':/')) {
		return $path;
	} else {
		$siteid = get_siteid();
		if ($mobile) {
			$siteurl = dr_site_info('mobile_domain', $siteid);
			$domainlen = strlen(dr_site_info('mobile_domain', $siteid))-1;
		} else {
			$siteurl = dr_site_info('domain', $siteid);
			$domainlen = strlen(dr_site_info('domain', $siteid))-1;
		}
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
	return dr_in_array($m, $modules);
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
	if (!empty($title))$title = clearhtml($title);
	if (!empty($description)) $description = clearhtml($description);
	if (!empty($keyword)) $keyword = str_replace(' ', ',', clearhtml($keyword));
	$cat = array();
	if (!empty($catid)) {
		$siteids = getcache('category_content','commons');
		$siteid = $siteids[$catid];
		$cat = dr_cat_value($catid);
		$cat['setting'] = dr_string2array($cat['setting']);
	}
	$seo['site_title'] = dr_site_info('site_title', $siteid) ? dr_site_info('site_title', $siteid) : dr_site_info('name', $siteid);
	$seo['keyword'] = !empty($keyword) ? $keyword : dr_site_info('keywords', $siteid);
	$seo['description'] = isset($description) && !empty($description) ? $description : (isset($cat['setting']['meta_description']) && !empty($cat['setting']['meta_description']) ? $cat['setting']['meta_description'] : (dr_site_info('description', $siteid) ? dr_site_info('description', $siteid) : ''));
	$seo['title'] =  (isset($title) && !empty($title) ? $title.' - ' : '').(isset($cat['setting']['meta_title']) && !empty($cat['setting']['meta_title']) ? $cat['setting']['meta_title'].' - ' : (isset($cat['catname']) && !empty($cat['catname']) ? $cat['catname'].' - ' : ''));
	foreach ($seo as $k=>$v) {
		$v && $seo[$k] = str_replace(array("\n","\r"), '', $v);
	}
	return $seo;
}

/**
 * 获取站点的信息
 * @param $siteid   站点ID
 */
function siteinfo($siteid = '') {
	static $sitelist;
	!$siteid && $siteid = get_siteid();
	if (empty($sitelist)) $sitelist = getcache('sitelist', 'commons');
	if (!$sitelist) return '';
	return isset($sitelist[$siteid]) ? $sitelist[$siteid] : '';
}

// 站点信息输出
function dr_site_info($name, $siteid = '') {
	!$siteid && $siteid = get_siteid();
	return get_cache('site', $siteid, 'config', $name);
}

// 站点设置信息输出
function dr_site_value($name, $siteid = '') {
	!$siteid && $siteid = get_siteid();
	return get_cache('site', $siteid, 'param', $name);
}

// 获取栏目数据及自定义字段
function dr_cat_value($catid = '', $name = '', $c = '') {
	if (empty($catid)) {
		return '';
	}
	$siteids = getcache('category_content','commons');
	$siteid = $siteids[$catid];
	if (!$siteid) {
		return '';
	}
	if ($c) {
		$cache_dir = 'module/category-'.$siteid.'-min';
	} else {
		$cache_dir = 'module/category-'.$siteid.'-data';
	}
	$cat = pc_base::load_sys_class('cache')->get_file($catid, $cache_dir);
	if (!$cat) {
		return '';
	}
	if ($name) {
		return $cat[$name];
	}
	return $cat;
}

// 获取单页数据及自定义字段
function dr_page_value($catid, $name) {
	if (empty($catid)) {
		return '';
	}
	return get_cache('page', 'data', $catid, $name);
}

// 获取模型数据及自定义字段
function dr_value($modelid = 0, $id = 0, $name = '') {
	if (!$id) {
		return '';
	}
	if ($modelid) {
		$content_db = pc_base::load_model('content_model');
		$content_db->set_model($modelid);
		$data = $content_db->get_one(array('id'=>$id));
		if(!$data) {
			return '';
		}
		if ($data && $name && isset($data[$name])) {
			return $data[$name];
		}
		$r = $content_db->get_one(array('id' => $id), 'tableid');
		$content_db->table_name = $content_db->table_name.'_data_'.$r['tableid'];
		$data = $content_db->get_one(array('id'=>$id));
		$content_db->set_model($modelid);
		if(!$data) {
			return '';
		}
		if ($data && $name && isset($data[$name])) {
			return $data[$name];
		}
	}
	return '';
}

// 判断是否是移动端终端
function is_mobile($siteid = 0) {
	$not_pad = intval(dr_site_info('not_pad', $siteid));
	if ($not_pad) {
		// 判断是否为平板，将排除为移动端
		$clientkeywords = array(
			'ipad',
		);
		// 从HTTP_USER_AGENT中查找关键字
		if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower((string)$_SERVER['HTTP_USER_AGENT']))){
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
		if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower((string)$_SERVER['HTTP_USER_AGENT']))){
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

// 判断是否是PC端终端
function is_pc($siteid = 0) {
	return !is_mobile($siteid);
}

/**
 * 后台搜索字段过滤函数
 * @param $array 单个字段数组
 * @return 是否被搜索时可用
 */
function dr_is_admin_search_field($t) {
	if (!$t) {
		return 0;
	}
	if (!$t['issystem']) {
		return 0;
	} elseif (in_array($t['formtype'], [
		'title', 'text', 'keyword', 'textarea', 'textbtn',
		'editor', 'box', 'number', 'author', 'linkfield',
		'linkage', 'linkages'
	])) {
		return 1;
	}
	return 0;
}

/**
 * 设置upload上传的json格式cookie
 */
function upload_json($aid,$src,$filename,$size) {
	if(!SYS_ATTACHMENT_STAT) return false;
	$cache = pc_base::load_sys_class('cache');
	$arr['aid'] = intval($aid);
	$arr['src'] = trim($src);
	$arr['filename'] = urlencode((string)$filename);
	$arr['size'] = $size;
	$json_str = json_encode($arr);
	$att_arr_exist = $cache->get_data('att_json');
	$att_arr_exist_tmp = explode('||', (string)$att_arr_exist);
	if(is_array($att_arr_exist_tmp) && in_array($json_str, $att_arr_exist_tmp)) {
		return true;
	} else {
		$json_str = $att_arr_exist ? $att_arr_exist.'||'.$json_str : $json_str;
		$cache->set_data('att_json', $json_str, 3600);
		return true;			
	}
}

/**
 * 删除upload上传的json格式cookie
 */	
function upload_json_del($aid,$src,$filename,$size) {
	$cache = pc_base::load_sys_class('cache');
	$arr['aid'] = intval($aid);
	$arr['src'] = trim($src);
	$arr['filename'] = urlencode((string)$filename);
	$arr['size'] = $size;
	$json_str = json_encode($arr);
	$att_arr_exist = $cache->get_data('att_json');
	$att_arr_exist = str_replace(array($json_str,'||||'), array('','||'), (string)$att_arr_exist);
	$att_arr_exist = preg_replace('/^\|\|||\|\|$/i', '', (string)$att_arr_exist);
	$cache->set_data('att_json', $att_arr_exist, 3600);
}

function readWordToHtml($source, $module, $isadmin, $userid, $catid, $siteid, $watermark, $attachment, $image_reduce, $rid) {
	pc_base::load_sys_class('upload','',0);
	include_once PC_PATH.'plugin/phpword/autoload.php';
	$phpWord = \PhpOffice\PhpWord\IOFactory::load($source);
	$html = '';
	foreach ($phpWord->getSections() as $section) {
		foreach ($section->getElements() as $ele1) {
			$paragraphStyle = $ele1->getParagraphStyle();
			if ($paragraphStyle && $paragraphStyle->getAlignment()) {
				$html .= '<p style="text-align:'. $paragraphStyle->getAlignment() .';">';
			} else {
				$html .= '<p>';
			}
			if ($ele1 instanceof \PhpOffice\PhpWord\Element\TextRun) {
				$downloadfiles = [];
				foreach ($ele1->getElements() as $ele2) {
					if ($ele2 instanceof \PhpOffice\PhpWord\Element\Text) {
						$style = $ele2->getFontStyle();
						$fontFamily = $style->getName();
						$fontSize = $style->getSize();
						$isBold = $style->isBold();
						$styleString = '';
						$fontFamily && $styleString .= "font-family:{$fontFamily};";
						$fontSize && $styleString .= "font-size:{$fontSize}px;";
						$isBold && $styleString .= "font-weight:bold;";
						if ($styleString) {
							$html .= sprintf('<span style="%s">%s</span>',
								$styleString,
								mb_convert_encoding($ele2->getText(), 'GBK', 'UTF-8')
							);
						} else {
							$html .= mb_convert_encoding($ele2->getText(), 'GBK', 'UTF-8');
						}
					} elseif ($ele2 instanceof \PhpOffice\PhpWord\Element\Image) {
						$imageData = $ele2->getImageStringData(true);
						//$imageData = 'data:' . $ele2->getImageType() . ';base64,' . $imageData;
						$upload = new upload(trim($module),intval($catid),$siteid);
						$upload->set_userid($userid);
						$rt = $upload->base64_image(array(
							'ext' => $ele2->getImageExtension(),
							'content' => base64_decode($imageData),
							'watermark' => intval($watermark),
							'attachment' => $upload->get_attach_info(intval($attachment), intval($image_reduce)),
						));
						$data = array();
						if (defined('SYS_ATTACHMENT_CF') && SYS_ATTACHMENT_CF && $rt['data']['md5']) {
							$att_db = pc_base::load_model('attachment_model');
							$att = $att_db->get_one(array('userid'=>$userid,'filemd5'=>$rt['data']['md5'],'fileext'=>$rt['data']['ext'],'filesize'=>$rt['data']['size']));
							if ($att) {
								$data = dr_return_data($att['aid'], 'ok');
								// 删除现有附件
								// 开始删除文件
								$storage = new storage(trim($module),intval($catid),$siteid);
								$storage->delete($upload->get_attach_info((int)$attachment), $rt['data']['file']);
								$rt['data'] = get_attachment($att['aid']);
								if ($rt['data']) {
									$rt['data']['name'] = $rt['data']['filename'];
								}
							}
						}
						if (!$data) {
							$rt['data']['isadmin'] = $isadmin;
							$data = $upload->save_data($rt['data'], 'ueditor:'.$rid);
						}
						upload_json($data['code'],$rt['data']['url'],$rt['data']['name'],format_file_size($rt['data']['size']));
						$downloadfiles[] = $data['code'];
						$html .= '<img src="'.$rt['data']['url'].'" title="'.$rt['data']['name'].'" alt="'.$rt['data']['name'].'"/>';
					}
				}
				isset($downloadfiles) && $downloadfiles && pc_base::load_sys_class('cache')->set_data('downloadfiles-'.$siteid, $downloadfiles, 3600);
			}
			$html .= '</p>';
		}
	}
	$html = preg_replace('/<Object:([^"]*).bin>/i', '', code2html($html));
	return mb_convert_encoding($html, 'UTF-8', 'GBK');
}

if (! function_exists('dr_is_image')) {
	// 文件是否是图片
	function dr_is_image($value) {
		if (!$value) {
			return false;
		}
		return dr_in_array(
			strpos($value, '.') !== false ? trim(strtolower(strrchr($value, '.')), '.') : $value,
			array('jpg', 'gif', 'png', 'jpeg', 'webp', 'avif')
		);
	}
}

/**
 * 生成标题样式
 * @param $style   样式
 * @param $color   是否随机颜色
 * @param $html    是否显示完整的STYLE
 */
function title_style($style, $color = 0, $html = 1) {
	if(!$style) return $color ? ' style="color:'.dr_random_color().';"' : '';
	$str = '';
	if ($html) $str = ' style="';
	$style_arr = explode(';',$style);
	if (!empty($style_arr[0])) {
		$str .= 'color:'.$style_arr[0].';';
	} else {
		$color ? $str .= 'color:'.dr_random_color().';' : '';
	}
	if (!empty($style_arr[1])) $str .= 'font-weight:'.$style_arr[1].';';
	if ($html) $str .= '" ';
	return $str;
}

/**
 * 获取站点域名
 * @param $siteid   站点id
 */
function siteurl($siteid, $mobile = 0) {
	if ($mobile) {
		if(!$siteid) return WEB_PATH.'mobile';
		return substr((string)dr_site_info('mobile_domain', $siteid), 0, -1);
	} else {
		if(!$siteid) return WEB_PATH;
		return substr((string)dr_site_info('domain', $siteid), 0, -1);
	}
}
/**
 * 获取站点手机域名
 * @param $siteid   站点id
 */
function sitemobileurl($siteid) {
	return siteurl($siteid, 1);
}
/**
 * 全局返回消息
 */
function dr_exit_msg($code, $msg, $data = array(), $token = array()) {
	$input = pc_base::load_sys_class('input');
	ob_end_clean();
	$rt = array(
		'code' => $code,
		'msg' => $msg,
		'data' => $data,
		'token' => $token,
	);
	if ($input->get('callback')) {
		// jsonp
		header('HTTP/1.1 200 OK');
		echo ($input->get('callback') ? $input->get('callback') : 'callback').'('.dr_array2string($rt).')';
	} else if (($input->get('is_ajax') || IS_AJAX)) {
		// json
		header('HTTP/1.1 200 OK');
		echo dr_array2string($rt);
	} else {
		// html
		dr_show_error($msg);
	}
	exit;
}
// 兼容错误提示
function dr_show_error($msg) {
	$input = pc_base::load_sys_class('input');
	if (CI_DEBUG) {
		$url = '<p>'.FC_NOW_URL.'</p>';
	} else {
		$url = '<p>在index.php中开启开发者模式可以看到故障详细情况</p>';
		$msg = '您的系统遇到了故障，请联系管理员处理';
		http_response_code(404);
	}
	if (IS_AJAX) {
		$msg = dr_array2string(array(
			'code' => 0,
			'msg' => $msg
		));
		if ($input->get('callback')) {
			echo $input->get('callback').'('.$msg.')';exit;
		} else {
			echo $msg;exit;
		}
	} else {
		exit("<!DOCTYPE html><html lang=\"zh-cn\"><head><meta charset=\"utf-8\"><title>系统错误</title><style>        div.logo {            height: 200px;            width: 155px;            display: inline-block;            opacity: 0.08;            position: absolute;            top: 2rem;            left: 50%;            margin-left: -73px;        }        body {            height: 100%;            background: #fafafa;            font-family: \"Helvetica Neue\", Helvetica, Arial, sans-serif;            color: #777;            font-weight: 300;        }        h1 {            font-weight: lighter;            letter-spacing: 0.8;            font-size: 3rem;            margin-top: 0;            margin-bottom: 0;            color: #222;        }        .wrap {            max-width: 1024px;            margin: 5rem auto;            padding: 2rem;            background: #fff;            text-align: center;            border: 1px solid #efefef;            border-radius: 0.5rem;            position: relative;            word-wrap:break-word;            word-break:normal;        }        pre {            white-space: normal;            margin-top: 1.5rem;        }        code {            background: #fafafa;            border: 1px solid #efefef;            padding: 0.5rem 1rem;            border-radius: 5px;            display: block;        }        p {            margin-top: 1.5rem;        }        .footer {            margin-top: 2rem;            border-top: 1px solid #efefef;            padding: 1em 2em 0 2em;            font-size: 85%;            color: #999;        }        a:active,        a:link,        a:visited {            color: #dd4814;        }</style></head><body><div class=\"wrap\"><p>{$msg}</p>    {$url}</div></body></html>");
	}
}
// 模块字段
function sql_module($tablename, $sql, $ismain = 0) {
	if ($tablename && $sql) {
		$content_db = pc_base::load_model('content_model');
		// 更新站点模块
		if (!$ismain) {
			// 更新副表 格式: 名称_data_副表id
			for ($i = 0;; $i ++) {
				$content_db->query("SHOW TABLES LIKE '".str_replace('_data_0', '_data_'.$i, $tablename)."'");
				$table_exists = $content_db->fetch_array();
				if (!$table_exists) {
					break;
				}
				$content_db->query(str_replace('_data_0', '_data_'.$i, $sql));
			}
		}
	}
}
/**
 * 提交表单默认隐藏域
 */
function dr_form_hidden($data = array()) {
	$form = '<input name="is_form" type="hidden" value="1">'.PHP_EOL;
	$form.= '<input name="is_admin" type="hidden" value="'.(IS_ADMIN && param::get_session('roleid') && cleck_admin(param::get_session('roleid')) ? 1 : 0).'">'.PHP_EOL;
	$form.= '<input name="'.SYS_TOKEN_NAME.'" type="hidden" value="'.csrf_hash().'">'.PHP_EOL;
	if ($data) {
		foreach ($data as $name => $value) {
			$form.= '<input name="'.$name.'" id="dr_'.$name.'" type="hidden" value="'.$value.'">'.PHP_EOL;
		}
	}
	return $form;
}
/**
 * 效验安全码
 */
function csrf_hash($key = 'csrf_token') {
	if (defined('SYS_CSRF') && !SYS_CSRF) {
		return '';
	}
	$cache = pc_base::load_sys_class('cache');
	!$key && $key = 'csrf_token_'.md5(isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] ? $_SERVER['HTTP_USER_AGENT'] : '');
	$csrf_token = $cache->get_auth_data(COOKIE_PRE.ip().$key, 1, 1800);
	if (!$csrf_token) {
		$csrf_token = bin2hex(random_bytes(16));
		$cache->set_auth_data(COOKIE_PRE.ip().$key, $csrf_token, 1);
	}
	return $csrf_token;
}
// 验证字符串
function dr_get_csrf_token($key = 'pc_hash') {
	!$key && $key = 'pc_hash_'.md5(isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] ? $_SERVER['HTTP_USER_AGENT'] : '');
	$code = param::get_session(COOKIE_PRE.ip().$key);
	if (!$code) {
		$code = bin2hex(random_bytes(16));
		param::set_session(COOKIE_PRE.ip().$key, $code);
	}
	return $code;
}
// 获取已发短信验证码
function get_mobile_code($phone) {
	return pc_base::load_sys_class('cache')->get_auth_data('phone-code-'.$phone, 1, defined('SYS_CACHE_SMS') && SYS_CACHE_SMS ? SYS_CACHE_SMS : 300);
}

// 储存已发短信验证码
function set_mobile_code($phone, $code) {
	return pc_base::load_sys_class('cache')->set_auth_data('phone-code-'.$phone, $code, 1);
}
// 验证码类
function get_captcha() {
	$cache = pc_base::load_sys_class('cache');
	$code = $cache->get_auth_data('web-captcha-'.USER_HTTP_CODE, get_siteid());
	return $code;
}
// 验证码类
function check_captcha($id) {
	$input = pc_base::load_sys_class('input');
	$cache = pc_base::load_sys_class('cache');
	$data = trim((string)$input->post($id));
	if (!$data) {
		IS_DEV && log_message('debug', '图片验证码验证失败：没有输入验证码'.dr_safe_replace($input->ip_address().':'.$input->get_user_agent()));
		return false;
	}
	$code = $cache->get_auth_data('web-captcha-'.USER_HTTP_CODE, get_siteid(), 300);
	if (!$code) {
		IS_DEV && log_message('error', '图片验证码未生成（'.USER_HTTP_CODE.'）'.dr_safe_replace($input->ip_address().':'.$input->get_user_agent()));
		return false;
	} elseif (strtolower($data) == strtolower($code)) {
		$cache->del_auth_data('web-captcha-'.USER_HTTP_CODE, get_siteid());
		return true;
	}
	IS_DEV && log_message('debug', '图片验证码验证失败：你输入的是（'.$data.'），正确的是（'.$code.'）'.dr_safe_replace($input->ip_address().':'.$input->get_user_agent()));
	return false;
}
// 验证码类：只比较不删除
function check_captcha_value($data) {
	$input = pc_base::load_sys_class('input');
	$cache = pc_base::load_sys_class('cache');
	// 是否进行验证图片
	if (!$data) {
		IS_DEV && log_message('debug', '图片验证码验证失败：没有输入验证码'.dr_safe_replace($input->ip_address().':'.$input->get_user_agent()));
		return false;
	}
	$data = trim((string)$data);
	$code = $cache->get_auth_data('web-captcha-'.USER_HTTP_CODE, get_siteid(), 300);
	if (!$code) {
		IS_DEV && log_message('error', '图片验证码未生成（'.USER_HTTP_CODE.'）'.dr_safe_replace($input->ip_address().':'.$input->get_user_agent()));
		return false;
	} elseif (strtolower($data) == strtolower($code)) {
		return true;
	}
	IS_DEV && log_message('debug', '图片验证码验证失败：你输入的是（'.$data.'），正确的是（'.$code.'）'.dr_safe_replace($input->ip_address().':'.$input->get_user_agent()));
	return false;
}
/**
 * 生成上传附件验证
 * @param $args   参数
 * @param $operation   操作类型(加密解密)
 */
function upload_key($args) {
	$pc_auth_key = md5(PC_PATH.'upload'.SYS_KEY.$_SERVER['HTTP_USER_AGENT']);
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
		$pc_auth_key = md5(PC_PATH.'login'.SYS_KEY.ip());
	}else if($prefix=='email'){
		$pc_auth_key = md5(PC_PATH.'email'.SYS_KEY);
	}else{
		$pc_auth_key = md5(PC_PATH.'other'.SYS_KEY.$suffix);
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

function getmicrotime() {
	list($usec, $sec) = explode(' ', microtime());
	return ((float)$usec + (float)$sec);
}

/**
 * 读取缓存动态页面
 */
function cache_page_start() {
	if(defined('IS_ADMIN') && IS_ADMIN) return false;
	$relate_url = $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] ? $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'] : $_SERVER['PHP_SELF']);
	define('CACHE_PAGE_ID', md5($relate_url));
	$contents = getcache(CACHE_PAGE_ID, 'page_tmp');
	if($contents && intval(substr($contents, 15, 10)) > SYS_TIME) {
		exit(substr($contents, 29));
	}
}
/**
 * 写入缓存动态页面
 */
function cache_page($ttl = 360, $isjs = 0) {
	if($ttl == 0 || !defined('CACHE_PAGE_ID')) return false;
	$contents = ob_get_contents();
	if($isjs) $contents = format_js($contents);
	$contents = "<!--expiretime:".(SYS_TIME + $ttl)."-->\n".$contents;
	setcache(CACHE_PAGE_ID, $contents, 'page_tmp');
}

/**
 *
 * 获取远程内容
 * @param $url 接口url地址
 * @param $timeout 超时时间
 */
function pc_file_get_contents($url, $timeout = 30) {
	return dr_catcher_data($url, $timeout);
}

/**
 * 获取文件名
 */
function file_name($name) {
	strpos($name, '/') !== false && $name = trim(strrchr($name, '/'), '/');
	return substr($name, 0, strrpos($name, '.'));
}
// 获取远程附件扩展名
function get_image_ext($url) {

	$url = trim($url);

	if (strlen($url) > 300) {
		return '';
	}

	// 解析协议，限制只允许 http / https
	$scheme = parse_url($url, PHP_URL_SCHEME);
	if ($scheme) {
		$scheme = strtolower($scheme);
	}

	// 拦截 phar://、php://、data://、zip://、expect:// 等危险包装器
	$danger_schemes = ['phar', 'php', 'data', 'zip', 'expect', 'file'];
	if ($scheme && in_array($scheme, $danger_schemes, true)) {
		CI_DEBUG && log_message('debug', '非法图片协议：' . $scheme . ' => ' . $url);
		return '';
	}

	// 只允许 http/https（你也可以根据项目需要允许相对路径）
	if ($scheme && !in_array($scheme, ['http', 'https'], true)) {
		return '';
	}

	// 既不是 http/https 且又不是以 / 开头的相对路径，则直接拒绝
	if (!$scheme && strpos($url, '/') !== 0) {
		return '';
	}

	$arr = array('gif', 'jpg', 'jpeg', 'png', 'webp');
	// 先从后缀判断
	$ext = str_replace('.', '', trim(strtolower(strrchr($url, '.')), '.'));
	if ($ext && dr_in_array($ext, $arr)) {
		return $ext; // 满足扩展名
	} elseif ($ext && strlen($ext) < 4) {
		return ''; // 表示不是图片扩展名了
	}

	// URL 中模糊匹配常见扩展名
	foreach ($arr as $t) {
		if (stripos($url, $t) !== false) {
			return $t;
		}
	}

	// 到这里再尝试 getimagesize
	// 再次简单拦截 phar://，防止上面 parse_url 被绕过（例如部分畸形 URL）
	if (stripos($url, 'phar://') === 0) {
		return '';
	}

	// 避免抛警告，用 @ 抑制错误（可根据需要保留日志）
	$rt = @getimagesize($url);
	if ($rt && !empty($rt['mime'])) {
		foreach ($arr as $t) {
			if (stripos($rt['mime'], $t) !== false) {
				return $t;
			}
		}
	}

	CI_DEBUG && log_message('debug', '服务器无法获取远程图片的扩展名：'.dr_safe_replace($url));

	return '';
}

/**
 * 调用远程数据
 *
 * @param	string	$url
 * @param	intval	$timeout 超时时间，0不超时
 * @return	string
 */
function dr_catcher_data($url, $timeout = 0, $is_log = true, $ct = 0) {

	if (!$url) {
		return '';
	}

	// 获取本地文件
	if (strpos($url, 'file://')  === 0) {
		return file_get_contents($url);
	} elseif (strpos($url, '/')  === 0 && is_file(CMS_PATH.$url)) {
		return file_get_contents(CMS_PATH.$url);
	} elseif (!dr_is_url($url)) {
		if (CI_DEBUG && $is_log) {
			log_message('error', '获取远程数据失败['.$url.']：地址前缀要求是http开头');
		}
		return '';
	}

	// curl模式
	if (function_exists('curl_init')) {
		$ch = curl_init($url);
		if (substr($url, 0, 8) == "https://") {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true); // 从证书中检查SSL加密算法是否存在
		}
		if ($ct) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:40.0)' . 'Gecko/20100101 Firefox/40.0',
				'Accept: */*',
				'X-Requested-With: XMLHttpRequest',
				'Referer: '.$url,
				'Accept-Language: pt-BR,en-US;q=0.7,en;q=0.3',
			));
			curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
		}
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		// 最大执行时间
		$timeout && curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		$data = curl_exec($ch);
		$code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		$errno = curl_errno($ch);
		if (CI_DEBUG && $errno && $is_log) {
			log_message('error', '获取远程数据失败['.$url.']：（'.$errno.'）'.curl_error($ch));
		}
		curl_close($ch);
		if ($code == 200) {
			return $data;
		} elseif ($errno == 35) {
			// 当服务器不支持时改为普通获取方式
		} else {
			if (!$ct) {
				// 尝试重试
				if (preg_match_all('/[\x{4e00}-\x{9fa5}]/u', $url, $mt)) {
					foreach ($mt[0] as $t) {
						$url = str_replace($t, urlencode($t), $url);
					}
				}
				if (strpos($url, ' ')) {
					$url = str_replace(' ', '%20', $url);
				}
				return dr_catcher_data($url, $timeout, $is_log, 1);
			} elseif (CI_DEBUG && $code && $is_log) {
				log_message('error', '获取远程数据失败['.$url.']http状态：'.$code);
			}
			return '';
		}
	}

	//设置超时参数
	if ($timeout && function_exists('stream_context_create')) {
		// 解析协议
		$opt = [
			'http' => [
				'method'  => 'GET',
				'timeout' => $timeout,
			],
			'https' => [
				'method'  => 'GET',
				'timeout' => $timeout,
			]
		];
		if ($ct) {
			$opt['http']['header'] = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13';
			$opt['https']['header'] = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13';
		}
		$ptl = substr($url, 0, 8) == "https://" ? 'https' : 'http';
		$data = file_get_contents($url, 0, stream_context_create([
			$ptl => $opt[$ptl]
		]));
	} else {
		$data = file_get_contents($url);
	}

	return $data;
}

/**
 * 递归创建目录
 *
 * @param   string  $dir    目录名称
 * @return  bool|void
 */
function dr_mkdirs($dir, $null = true) {
	if (!$dir) {
		return false;
	}
	if (!is_dir($dir)) {
		dr_mkdirs(dirname($dir));
		if (!is_dir($dir)) {
			mkdir($dir, 0777, true);
		}
	}
}

/**
 * 二维码调用
 * @param $text 指定字符串
 * @param $thumb 中间图片
 * @param string $background 背景色
 * @param string $pcolor 定位角的颜色
 * @param string $mcolor 中间内容的颜色
 * @param $level 等级字母
 * @param $size 大小值
 * @return 生成二维码图片url
 */
function qrcode($text, $thumb = '', $background = '', $pcolor = '', $mcolor = '', $level = 'H', $size = 5) {
	$background = urlencode(dr_safe_username($background));
	$pcolor = urlencode(dr_safe_username($pcolor));
	$mcolor = urlencode(dr_safe_username($mcolor));
	return APP_PATH.'api.php?op=qrcode&thumb='.urlencode($thumb).'&text='.urlencode($text).'&background='.$background.'&pcolor='.$pcolor.'&mcolor='.$mcolor.'&size='.$size.'&level='.$level;
}

/**
 * 十六进制转RGB
 * 
 * @param string $color 16进制颜色值
 * @return array
 */
function hex2rgb($color) {
	$hexColor = dr_safe_username($color);
	$lens = strlen($hexColor);
	if ($lens != 3 && $lens != 6) {
		return false;
	}
	$newcolor = '';
	if ($lens == 3) {
		for ($i = 0; $i < $lens; $i++) {
			$newcolor .= $hexColor[$i] . $hexColor[$i];
		}
	} else {
		$newcolor = $hexColor;
	}
	$hex = str_split($newcolor, 2);
	$rgb = [];
	foreach ($hex as $key => $vls) {
		$rgb[] = hexdec($vls);
	}
	return $rgb;
}

// RGB转十六进制
function rgb2hex($r, $g = null, $b = null) {
	if (is_array($r) && dr_count($r) == 3) {
		list($r, $g, $b) = $r;
	}
	$r = intval($r);
	$g = intval($g);
	$b = intval($b);
	$r = dechex($r < 0 ? 0 : ($r > 255 ? 255 : $r));
	$g = dechex($g < 0 ? 0 : ($g > 255 ? 255 : $g));
	$b = dechex($b < 0 ? 0 : ($b > 255 ? 255 : $b));
	return '#' . (strlen($r) < 2 ? '0' : '') . $r . (strlen($g) < 2 ? '0' : '') . $g . (strlen($b) < 2 ? '0' : '') . $b;
}

// 格式化sql创建
function format_create_sql($sql) {
	if (!$sql) {
		return '';
	}
	$sql = trim(str_replace('ENGINE=InnoDB', 'ENGINE=MyISAM', $sql));
	$sql = trim(str_replace('CHARSET=utf8 ', 'CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci ', $sql));
	return $sql;
}

/**
 * 获取cms域名部分
 * @param $url 指定url
 * @return 从指定url中获取cms域名部分
 */
function dr_cms_domain_name($url) {
	if (!$url) {
		return '';
	}
	$param = parse_url($url);
	if (isset($param['host']) && $param['host']) {
		return $param['host'];
	}
	return $url;
}

/**
 * 获取域名部分
 * @param $url
 * @return 从$url中获取到域名
 */
function dr_get_domain_name($url) {
	if (!$url) {
		return '';
	}
	list($url) = explode(':', str_replace(array('https://', 'http://', '/'), '', $url));
	return $url;
}

/**
 * 按百分比分割数组
 * @param $data 数组
 * @param $num 分成几等分
 * @return 将数组按百分比等分划分
 */
function dr_save_bfb_data($data, $num = 100) {
	$cache = array();
	$count = dr_count($data);
	if ($count > $num) {
		$pagesize = ceil($count/$num);
		for ($i = 1; $i <= $num; $i ++) {
			$cache[$i] = array_slice($data, ($i - 1) * $pagesize, $pagesize);
		}
	} else {
		for ($i = 1; $i <= $count; $i ++) {
			$cache[$i] = array_slice($data, ($i - 1), 1);
		}
	}
	return $cache;
}

/**
 * 生成安全码
 */
function token($name = '') {
	if ($name) {
		return 'CMS'.md5($name.md5(SYS_TIME).rand(1, 999999));
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
		return dr_date($date, 'Y年m月d日');
	} elseif (isset($month)) {
		return dr_date($date, 'm月d日');
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
	if (!$time) {
		return '';
	}
	$time = (int)substr((string)$time, 0, 10);
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
		$str = dr_date($time, 'Y-m-d H:i:s');
	}
	return $str;
}

function mtime($time){
	if (!$time) {
		return '';
	}
	//date_default_timezone_set('PRC'); //设置成中国的时区
	$now=SYS_TIME;
	$day=dr_date($time, 'Y-m-d');
	$today=dr_date($now, 'Y-m-d');

	$dayArr=explode('-',$day);
	$todayArr=explode('-',$today);

	//距离的天数，这种方法超过30天则不一定准确，但是30天内是准确的，因为一个月可能是30天也可能是31天
	$days=($todayArr[0]-$dayArr[0])*365+(($todayArr[1]-$dayArr[1])*30)+($todayArr[2]-$dayArr[2]);
	//距离的秒数
	$secs=$now-$time;

	if($todayArr[0]-$dayArr[0]>0 && $days>3){//跨年且超过3天
		return dr_date($time, 'Y-m-d H:i:s');
	}else{
		$hour=dr_date($time, 'H');
		$minutes=dr_date($time, 'i');
		$seconds=dr_date($time, 's');
		if($days<1){//今天
			//if($secs<60)return $secs.'秒前';
			//elseif($secs<3600)return floor($secs/60)."分钟前";
			//else return floor($secs/3600)."小时前";
			return "今天".$hour.':'.$minutes;
		}else if($days<2){//昨天
			return "昨天".$hour.':'.$minutes;
		}elseif($days<3){//前天
			return "前天".$hour.':'.$minutes;
		}else{//三天前
			return dr_date('m-d H:i',$time);
		}
	}
}

function mdate($time = NULL) {
	//date_default_timezone_set('PRC'); //设置成中国的时区
	$text = '';
	$time = $time === NULL || $time > SYS_TIME ? SYS_TIME : intval($time);
	$t = SYS_TIME - $time; //时间差 （秒）
	$y = dr_date($time, 'Y')-dr_date(SYS_TIME, 'Y');//是否跨年
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
			$text = floor($time/(60*60*24)) ==1 ?'昨天' : '前天'; //昨天和前天
			break;
		case $t < 60 * 60 * 24 * 30:
			$text = dr_date($time, 'm月d日'); //一个月内
			break;
		case $t < 60 * 60 * 24 * 365&&$y==0:
			$text = dr_date($time, 'm月d日'); //一年内
			break;
		default:
			$text = dr_date($time, 'Y年m月d日'); //一年以前
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
	$dDay = intval(dr_date($cTime, 'z')) - intval(dr_date($sTime, 'z'));
	$dYear = intval(dr_date($cTime, 'Y')) - intval(dr_date($sTime, 'Y'));
	if ($dYear > 0) {
		return dr_date($sTime, $formt);
	}
	//n秒前，n分钟前，n小时前，日期
	if ($dTime < 60 ) {
		if ($dTime < 10) {
			return L('刚刚');
		} else {
			return L(intval(floor($dTime / 10) * 10).'秒前');
		}
	} elseif ($dTime < 3600 ) {
		return L(intval($dTime/60).'分钟前');
	} elseif( $dTime >= 3600 && $dDay == 0  ){
		return L(intval($dTime/3600).'小时前');
	} elseif( $dDay > 0 && $dDay<=7 ){
		return L(intval($dDay).'天前');
	} elseif( $dDay > 7 &&  $dDay <= 30 ){
		return L(intval($dDay/7).'周前');
	} elseif( $dDay > 30 && $dDay < 180){
		return L(intval($dDay/30).'个月前');
	} elseif( $dDay >= 180 && $dDay < 360){
		return L('半年前');
	} elseif ($dYear == 0) {
		return dr_date($sTime);
	} else {
		return dr_date($sTime, $formt);
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
function dr_date($time = '', $format = SYS_TIME_FORMAT, $color = '') {
	if (!$time) {
		return '';
	}
	if (!is_numeric($time)) {
		$new = strtotime(clearhtml($time));
		if (is_numeric($new)) {
			$time = $new;
		} else {
			return IS_DEV ? '参数（'.$time.'）不是时间戳格式' : '';
		}
	}
	if (!$time) {
		return '';
	}
	!$format && $format = SYS_TIME_FORMAT;
	!$format && $format = 'Y-m-d H:i:s';
	$string = date($format, $time);
	return $color && $time >= strtotime(date('Y-m-d 00:00:00')) && $time <= strtotime(date('Y-m-d 23:59:59')) ? '<font color="' . $color . '">' . $string . '</font>' : $string;
}
?>