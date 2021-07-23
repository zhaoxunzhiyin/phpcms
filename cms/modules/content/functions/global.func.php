<?php
/**
 * 生成Tag URL
 * @param $tid
 * @return string
 */
function tag_url($keyword, $siteid){
	return APP_PATH.'index.php?m=content&c=tag&a=lists&tag='.urlencode($keyword).'&siteid='.$siteid;
}
?>