<?php
/**
 * 输出xml头部信息
 */
function wmlHeader() {
	echo "<?xml version=\"1.0\" encoding=\"".CHARSET."\"?>\n";
}
/**
 * 生成Tag URL
 */
function mobile_tag_url($keyword, $siteid){
	$sitelist = getcache('sitelist','commons');
	$siteid = isset($_GET['siteid']) && (intval($_GET['siteid']) > 0) ? intval(trim($_GET['siteid'])) : (param::get_cookie('siteid') ? param::get_cookie('siteid') : 1);
	return $sitelist[$siteid]['mobile_domain'].'index.php?m=mobile&c=tag&a=lists&tag='.urlencode($keyword).'&siteid='.$siteid;
}
/**
 * 解析手机分类url路径
 */
function list_url($url, $catid = '') {
	$sitelist = getcache('sitelist','commons');
	$siteid = isset($_GET['siteid']) && (intval($_GET['siteid']) > 0) ? intval(trim($_GET['siteid'])) : (param::get_cookie('siteid') ? param::get_cookie('siteid') : 1);
	$categorys = getcache('category_content_'.$siteid,'commons');
	$CAT = $categorys[$catid];
	$setting = string2array($CAT['setting']);
	$ishtml = $setting['ishtml'];
	if ($sitelist[$siteid]['mobilehtml']==1) {
		if ($sitelist[$siteid]['mobile_domain']) {
			if ($ishtml==1) {
				return str_replace($sitelist[$siteid]['domain'],$sitelist[$siteid]['mobile_domain'],$url);
			} else {
				//if (defined('IS_MOBILE') && IS_MOBILE) {
					return $sitelist[$siteid]['mobile_domain'].'index.php?m=mobile&c=index&a=lists&catid='.$catid;
				//} else {
					//return APP_PATH.'index.php?m=mobile&c=index&a=lists&catid='.$catid;
				//}
			}
		} else {
			if ($ishtml==1) {
				return str_replace($sitelist[$siteid]['domain'],'/mobile/',$url);
			} else {
				//if (defined('IS_MOBILE') && IS_MOBILE) {
					return $sitelist[$siteid]['mobile_domain'].'index.php?m=mobile&c=index&a=lists&catid='.$catid;
				//} else {
					//return APP_PATH.'index.php?m=mobile&c=index&a=lists&catid='.$catid;
				//}
			}
		}
	} else {
		//if (defined('IS_MOBILE') && IS_MOBILE) {
			return $sitelist[$siteid]['mobile_domain'].'index.php?m=mobile&c=index&a=lists&catid='.$catid;
		//} else {
			//return APP_PATH.'index.php?m=mobile&c=index&a=lists&catid='.$catid;
		//}
	}
}

/**
 * 解析手机内容url路径
 */
function show_url($url, $catid = '', $id = '') {
	$sitelist = getcache('sitelist','commons');
	$siteid = isset($_GET['siteid']) && (intval($_GET['siteid']) > 0) ? intval(trim($_GET['siteid'])) : (param::get_cookie('siteid') ? param::get_cookie('siteid') : 1);
	$categorys = getcache('category_content_'.$siteid,'commons');
	$CAT = $categorys[$catid];
	$setting = string2array($CAT['setting']);
	$content_ishtml = $setting['content_ishtml'];
	if (strstr($url, 'javascript:alert')) {
		return $url;
	}
	if ($sitelist[$siteid]['mobilehtml']==1) {
		if ($sitelist[$siteid]['mobile_domain']) {
			if ($content_ishtml==1) {
				return str_replace($sitelist[$siteid]['domain'],$sitelist[$siteid]['mobile_domain'],$url);
			} else {
				//if (defined('IS_MOBILE') && IS_MOBILE) {
					return $sitelist[$siteid]['mobile_domain'].'index.php?m=mobile&c=index&a=show&catid='.$catid.'&id='.$id;
				//} else {
					//return APP_PATH.'index.php?m=mobile&c=index&a=show&catid='.$catid.'&id='.$id;
				//}
			}
		} else {
			if ($content_ishtml==1) {
				return str_replace($sitelist[$siteid]['domain'],'/mobile/',$url);
			} else {
				//if (defined('IS_MOBILE') && IS_MOBILE) {
					return $sitelist[$siteid]['mobile_domain'].'index.php?m=mobile&c=index&a=show&catid='.$catid.'&id='.$id;
				//} else {
					//return APP_PATH.'index.php?m=mobile&c=index&a=show&catid='.$catid.'&id='.$id;
				//}
			}
		}
	} else {
		//if (defined('IS_MOBILE') && IS_MOBILE) {
			return $sitelist[$siteid]['mobile_domain'].'index.php?m=mobile&c=index&a=show&catid='.$catid.'&id='.$id;
		//} else {
			//return APP_PATH.'index.php?m=mobile&c=index&a=show&catid='.$catid.'&id='.$id;
		//}
	}
}

/**
 * 过滤内容为wml格式
 */
function wml_strip($string) {
    $string = str_replace(array('&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;', '&'), array(' ', '&', '"', "'", '“', '”', '—', '{<}', '{>}', '·', '…', '&amp;'), $string);
	return str_replace(array('{<}', '{>}'), array('&lt;', '&gt;'), $string);
}

function strip_selected_tags($text) {
    $tags = array('em','font','h1','h2','h3','h4','h5','h6','hr','i','ins','li','ol','p','pre','small','span','strike','strong','sub','sup','table','tbody','td','tfoot','th','thead','tr','tt','u','div','span');
    $args = func_get_args();
    $text = array_shift($args);
    $tags = func_num_args() > 2 ? array_diff($args,array($text)) : (array)$tags;
    foreach ($tags as $tag){
        if( preg_match_all( '/<'.$tag.'[^>]*>([^<]*)<\/'.$tag.'>/iu', $text, $found) ){
            $text = str_replace($found[0],$found[1],$text);
        }
    }
    return $text;
}

/**
 * 生成文章分页方法
 */

function mobile_content_pages($num, $curr_page,$pageurls, $siteid = 0, $ishtml = 0,$showremain = 1) {
	if(!$siteid) {
		$siteid = param::get_cookie('siteid');
	}
	if (!$siteid) $siteid = 1;
	$sitelist = getcache('sitelist','commons');
	if ($sitelist[$siteid]['mobilehtml']==1 && $ishtml) {
		//if (substr($sitelist[$siteid]['mobile_domain'],0,-1)) {
			$mobile_root = substr($sitelist[$siteid]['mobile_domain'],0,-1);
		//} else {
			//$mobile_root = pc_base::load_config('system','mobile_root');
		//}
	}
	$multipage = '';
	$page = 11;
	$offset = 4;
	$pages = $num;
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
		} elseif($to >= $pages) {
			$from = $pages-($page-2);
			$to = $pages-1;
		}
		$more = 1;
	}
	$multipage .='('.$curr_page.'/'.$num.')';
	if($curr_page>0) {
		$perpage = $curr_page == 1 ? 1 : $curr_page-1;
		$multipage .= '<a class="a1" href="'.$mobile_root.$pageurls[$perpage][1].'">'.L('previous').'</a>';
	}
	
	if($curr_page<$pages) {
		if($curr_page<$pages-5 && $more) {
			$multipage .= ' <a class="a1" href="'.$mobile_root.$pageurls[$curr_page+1][1].'">'.L('next').'</a>';
		} else {
			$multipage .= ' <a class="a1" href="'.$mobile_root.$pageurls[$curr_page+1][1].'">'.L('next').'</a>';
		}
	} elseif($curr_page==$pages) {
		$multipage .= ' <a class="a1" href="'.$mobile_root.$pageurls[$curr_page][1].'">'.L('next').'</a>';
	}
	if($showremain && $sitelist[$siteid]['mobilehtml']==0 || !$ishtml) $multipage .="| <a href='".$mobile_root.$pageurls[$curr_page][1]."&remains=true'>剩余全文</a>";
	return $multipage;
}
?>