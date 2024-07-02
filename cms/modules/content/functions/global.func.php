<?php
/**
 * 生成Tag URL
 * @param $tid
 * @return string
 */
function tag_url($keyword, $siteid = '', $catid = '', $modelid = -1){
	!$siteid && $siteid = get_siteid();
	if ($modelid >= 0) {
		return APP_PATH.'index.php?m=search&c=index&a=init&typeid='.intval($modelid).'&siteid='.$siteid.'&q='.urlencode($keyword);
	}
	if ($catid) {
		return APP_PATH.'index.php?m=content&c=search&a=init&catid='.$catid.'&info%5Bcatid%5D='.$catid.'&info%5Btypeid%5D=0&info%5Btitle%5D='.urlencode($keyword);
	}
	return APP_PATH.'index.php?m=content&c=tag&a=lists&tag='.urlencode($keyword).'&siteid='.$siteid;
}
?>