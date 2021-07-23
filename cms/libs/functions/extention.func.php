<?php
/**
 *  extention.func.php 用户自定义函数库
 *
 * @copyright			(C) 2005-2010
 * @lastmodify			2010-10-27
 */

/**
 * 通过指定keyid形式显示所有联动菜单
 * @param  $keyid 菜单主id
 * @param  $linkageid  联动菜单id,0调用顶级
 * @param  $modelid 模型id
 * @param  $fieldname  字段名称
 */
function show_linkage($keyid = 0, $linkageid = 0, $modelid = '', $fieldname='diqu') {
	$datas = $infos = $array = array();
	$keyid = intval($keyid);
	$linkageid = intval($linkageid);
	//当前菜单id
	$field_value = intval($_GET[$fieldname]);
	$urlrule = structure_filters_url($fieldname,$array,1,$modelid);
	if($keyid == 0) return false;
	$datas = getcache($keyid,'linkage');
	$infos = $datas['data'];

	foreach($infos as $k=>$v){
		if($v['parentid']==$field_value){
			$array[$k]['name'] = $v['name'];
			$array[$k]['value'] = $k;
			$array[$k]['url'] = str_replace('{$'.$fieldname.'}',$k,$urlrule);
			$array[$k]['menu'] = $field_value == $k ? '<em>'.$v['name'].'</em>' : '<a href='.$array[$k]['url'].'>'.$v['name'].'</a>' ;
		}
	}
	$all['name'] = '全部';
	$all['url'] = structure_filters_url($fieldname,array($fieldname=>''),2,$modelid);
	$all['menu'] = $field_value == '' ? '<em>'.$all['name'].'</em>' : '<a href='.$all['url'].'>'.$all['name'].'</a>';

	array_unshift($array,$all);
	return $array;
}
function structure_filters_url($fieldname,$array=array(),$type = 1,$modelid = '') {
	if(empty($array)) {
		$array = $_GET;
	} else {
		$array = array_merge($_GET,$array);
	}
	//TODO
	$fields = getcache('model_field_'.$modelid,'model');
	if(is_array($fields) && !empty($fields)) {
		ksort($fields);
		foreach ($fields as $_v=>$_k) {
			if($_k['filtertype'] || $_k['rangetype']) {
				if(strpos(URLRULE,'.html') === FALSE) $urlpars .= '&'.$_v.'={$'.$_v.'}';
				else $urlpars .= '-{$'.$_v.'}';
			}
		}
	}
	//后期增加伪静态等其他url规则管理，apache伪静态支持9个参数
	if(strpos(URLRULE,'.html') === FALSE) $urlrule =APP_PATH.'index.php?m=content&c=index&a=lists&catid={$catid}'.$urlpars.'&page={$page}' ;
	else $urlrule = APP_PATH.'list-{$catid}'.$urlpars.'-{$page}.html';
	//根据get传值构造URL
	if (is_array($array)) foreach ($array as $_k=>$_v) {
		if($_k=='page') $_v=1;
		if($type == 1) if($_k==$fieldname) continue;
		$_findme[] = '/{\$'.$_k.'}/';
		$_replaceme[] = $_v;
	}
     //type 模式的时候，构造排除该字段名称的正则
	if($type==1) $filter = '(?!'.$fieldname.'.)';
	$_findme[] = '/{\$'.$filter.'([a-z0-9_]+)}/';
	$_replaceme[] = '';		
	$urlrule = preg_replace($_findme, $_replaceme, $urlrule);	
	return 	$urlrule;
}
/**
 * 生成分类信息中的筛选菜单
 * @param $field   字段名称
 * @param $modelid  模型ID
 */
function filters($field = '',$modelid = '',$diyarr = array()) {
	$fields = getcache('model_field_'.$modelid,'model');
	$options = empty($diyarr) ?  explode("\n",$fields[$field]['options']) : $diyarr;
	$field_value = intval($_GET[$field]);
	foreach($options as $_k) {
		$v = explode("|",$_k);
		$k = trim($v[1]);
		$option[$k]['name'] = $v[0];
		$option[$k]['value'] = $k;
		$option[$k]['url'] = structure_filters_url($field,array($field=>$k),2,$modelid);
		$option[$k]['menu'] = $field_value == $k ? '<li class="item on">'.$v[0].'</li>' : '<li class="item"><a href='.$option[$k]['url'].'>'.$v[0].'</a></li>' ;
	}
	$all['name'] = '全部';
	$all['url'] = structure_filters_url($field,array($field=>''),2,$modelid);
	$all['menu'] = $field_value == '' ? '<li class="item on">'.$all['name'].'</li>' : '<li class="item"><a href='.$all['url'].'>'.$all['name'].'</a></li>';
	array_unshift($option,$all);
	return $option;
}
/**
 * 生成分类信息中的筛选菜单
 * @param $field   字段名称
 * @param $modelid  模型ID
 */
function dr_filters($field = '',$modelid = '') {
	$fields = getcache('model_field_'.$modelid,'model');
	$str .= '<div class="direction js-direction" style="box-shadow: rgba(95, 101, 105, 0) 0px 12px 20px 0px; height: 32px; background: transparent;">'.PHP_EOL;
	$str .= '    <span class="name">'.$fields[$field]['name'].'：</span>'.PHP_EOL;
	$str .= '    <ul class="items">'.PHP_EOL;
    foreach(filters($field,$modelid) as $r) {
		$str .= '        '.$r['menu'].''.PHP_EOL;
	}
	$str .= '    </ul>'.PHP_EOL;
	$str .= '</div>';
	if ($_GET[$field]) {$str .= dr_filters($fields[$field]['catids'],$modelid).PHP_EOL;}
	return $str;
}
/**
 * 获取联动菜单层级
 * @param  $keyid     联动菜单分类id
 * @param  $linkageid 菜单id
 * @param  $leveltype 获取类型 parentid 获取父级id child 获取时候有子栏目 arrchildid 获取子栏目数组
 */
function get_linkage_level($keyid = 0,$linkageid = 0,$leveltype = 'parentid') {
	$child_arr = $childs = array();
	$leveltypes = array('parentid','child','arrchildid','arrchildinfo');
	$datas = getcache($keyid,'linkage');
	$infos = $datas['data'];
	if (in_array($leveltype, $leveltypes)) {
		if($leveltype == 'arrchildinfo') {
			$child_arr = explode(',',$infos[$linkageid]['arrchildid']);
			foreach ($child_arr as $r) {
				$childs[] = $infos[$r];
			}
			return $childs;
		} else {
			return $infos[$linkageid][$leveltype];
		}
	}	
}
// 根据linkageid递归到父级
function get_parent_url($modelid = '',$field = '',$linkageid=0,$array = array()){
	$modelid = intval($modelid);
	if(!$modelid || empty($field)) return false;
	$fields = getcache('model_field_'.$modelid,'model');
	$keyid = $fields[$field]['linkageid'];
	$datas = getcache($keyid,'linkage');
	$infos = $datas['data'];
	
	if(empty($linkageid)){
		$linkageid = intval($_GET[$field]);
		if(!$linkageid) return false;
	}

	$urlrule = structure_filters_url($field,array(),1,$modelid);
	$urlrule = str_replace('{$'.$field.'}',$infos[$linkageid]['parentid'],$urlrule);
	array_unshift($array,array('name'=> $infos[$linkageid]['name'],'url'=>$urlrule));
	if($infos[$linkageid]['parentid']){
		return get_parent_url($modelid,$field,$infos[$linkageid]['parentid'],$array);
	}
	return $array;
}
/**
 * 构造筛选时候的sql语句
 */
function structure_filters_sql($modelid = '',$catid = '') {
	$sql = $fieldname = $min = $max = '';
	$fieldvalue = array();
	$modelid = intval($modelid);
	$model =  getcache('model','commons');
	$fields = getcache('model_field_'.$modelid,'model');
	$categorys = getcache('category_content_'.get_siteid(),'commons');
	$fields_key = array_keys($fields);
	//TODO

	$sql = '`catid` IN ('.$categorys[$catid]['arrchildid'].') AND `status` = \'99\'';
	foreach ($_GET as $k=>$r) {
		if(in_array($k,$fields_key) && intval($r)!=0 && ($fields[$k]['filtertype'] || $fields[$k]['rangetype'])) {
			if($fields[$k]['formtype'] == 'linkage') {
				$datas = getcache($fields[$k]['linkageid'],'linkage');
				$infos = $datas['data'];
				if($infos[$r]['arrchildid']) {
					$sql .=  ' AND `'.$k.'` in('.$infos[$r]['arrchildid'].')';	
				}	
			} elseif($fields[$k]['rangetype']) {
				if(is_numeric($r)) {
					$sql .=" AND `$k` = '$r'";
				} else {
					$fieldvalue = explode('_',$r);
					$min = intval($fieldvalue[0]);
					$max = $fieldvalue[1] ? intval($fieldvalue[1]) : 999999;				
					$sql .=" AND `$k` >= '$min' AND  `$k` < '$max'";
				}
			} else {	
				$sql .=" AND `$k` = '$r'";
			}
		}
	}	
	return $sql;
}
/** 
 * 分页，如去掉则分页会有问题 
 */ 
function makeurlrule() { 
    if(strpos(URLRULE,'.html') === FALSE) { 
        return url_par('page={$'.'page}'); 
    } 
    else { 
        $url = preg_replace('/-[0-9]+.html$/','-{$page}.html',get_url()); 
        return $url; 
    } 
} 
?>