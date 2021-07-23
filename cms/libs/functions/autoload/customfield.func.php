<?php
defined('IN_CMS') or exit('No permission resources.');
/**
 * 获取自定义变量
 *
 * @name   customField
 * @author frontLon
 * 
 * @example $allFields = customField(); //使用当前站点的变量（必须）
 * @example $cm = $allFields[$siteid]; //使用当前站点的变量（可选）
 * @example $cm = $allFields[3]; //使用站点3的变量（可选）
 *
 * 变量使用：
 * @example	$cm[contact_name] //如果定义了cm，可以这样用
 * @example	$allFields[$siteid][contact_name]  //或者直接使用allFields，写起来比较长。
 * @example	$allFields[2]['contact_name'] //直接使用长数组，站点id为2
 *
 * @return array
 *
 */
	function customField(){
		$caches = getcache("fieldlist",'customfield');
		if(!$caches){
			//如果缓存不存在，则重新生成缓存
			$db = pc_base::load_model('customfield_model');
			$sitedb = pc_base::load_model('site_model');
			$sitelist = $sitedb->select('','siteid');
            foreach($sitelist as $slist){
				$fieldlist = $db->select("pid != 0 and siteid={$slist['siteid']}",'val,name');
				foreach($fieldlist as $key => $flist){
					$caches[$slist['siteid']][$flist['name']] = $flist['val'];
				}
			}
			setcache("fieldlist",$caches,'customfield');
		}
		return $caches;
	}
?>