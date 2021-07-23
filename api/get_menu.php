<?php
/**
 * 获取联动菜单接口
 */
defined('IN_CMS') or exit('No permission resources.'); 
if(!$input->get('callback') || !$input->get('act'))  showmessage(L('error'));

switch($input->get('act')) {
	case 'ajax_getlist':
		ajax_getlist();
	break;
	
	case 'ajax_getpath':
		ajax_getpath($input->get('parentid'),$input->get('keyid'),$input->get('callback'),$input->get('path'));
	break;	
	case 'ajax_gettopparent':
		ajax_gettopparent($input->get('id'),$input->get('keyid'),$input->get('callback'),$input->get('path'));
	break;		
}


/**
 * 获取地区列表
 */
function ajax_getlist() {
	$cachefile = safe_getcache($input->get('cachefile'));
	$path = safe_getcache($input->get('path'));
	$title = $input->get('title');
	$key = $input->get('key');
	$infos = getcache($cachefile,$path);
	$where_id = intval($input->get('parentid'));
	$parent_menu_name = ($where_id==0) ? '' : trim($infos[$where_id][$key]);
	is_array($infos)?null:$infos = array();
	foreach($infos AS $k=>$v) {
		if($v['parentid'] == $where_id) {
			if ($v['parentid']) $parentid = $infos[$v['parentid']]['parentid'];
			$s[]=iconv(CHARSET,'utf-8',$v['catid'].','.trim($v[$key]).','.$v['parentid'].','.$parent_menu_name.','.$parentid);
		}
	}
	if(is_array($s)) {
		if(count($s)>0) {
			$jsonstr = json_encode($s);
			echo trim_script($input->get('callback')).'(',$jsonstr,')';
			exit;			
		} else {
			echo trim_script($input->get('callback')).'()';exit;			
		}
	} else {
		echo trim_script($input->get('callback')).'()';exit;			
	}
}

/**
 * 获取地区父级路径路径
 * @param $parentid 父级ID
 * @param $keyid 菜单keyid
 * @param $callback json生成callback变量
 * @param $result 递归返回结果数组
 * @param $infos
 */
function ajax_getpath($parentid,$keyid,$callback,$path = 'commons',$result = array(),$infos = array()) {
	$path = safe_getcache($path);
	$keyid = safe_getcache($keyid);
	$parentid = intval($parentid);
	if(!$infos) {
		$infos = getcache($keyid,$path);
	}
	if(array_key_exists($parentid,$infos)) {
		$result[]=iconv(CHARSET,'utf-8',trim($infos[$parentid]['catname']));
		return ajax_getpath($infos[$parentid]['parentid'],$keyid,$callback,$path,$result,$infos);
	} else {
		if(is_array($result)) {
			if(count($result)>0) {
				krsort($result);
				$jsonstr = json_encode($result);
				echo trim_script($callback).'(',$jsonstr,')';
				exit;
			} else {
				$result[]=iconv(CHARSET,'utf-8',$datas['title']);
				$jsonstr = json_encode($result);
				echo trim_script($callback).'(',$jsonstr,')';
				exit;
			}
		} else {
			$result[]=iconv(CHARSET,'utf-8',$datas['title']);
			$jsonstr = json_encode($result);
			echo trim_script($callback).'(',$jsonstr,')';
			exit;
		}
	}
}
/**
 * 获取地区顶级ID
 * Enter description here ...
 * @param  $linkageid 菜单id
 * @param  $keyid 菜单keyid
 * @param  $callback json生成callback变量
 * @param  $infos 递归返回结果数组
 */
function ajax_gettopparent($id,$keyid,$callback,$path,$infos = array()) {
	$path = str_replace(array('/', '//'), '', $path);
	$keyid = str_replace(array('/', '//'), '', $keyid);
	$id = intval($id);
	if(!$infos) {
		$infos = getcache($keyid,$path);
	}
	if($infos[$id]['parentid']!=0) {
		return ajax_gettopparent($infos[$id]['parentid'],$keyid,$callback,$path,$infos);
	} else {
		echo trim_script($callback).'(',$id,')';
		exit;		
	}
}
function safe_getcache($str) {
	$str = str_replace(array("'",'#','=','`','$','%','&',';','..'), '', $str);
	$str = preg_replace('/(\/){1,}|(\\\){1,}/', '', $str);
	return $str;
}
?>