<?php
defined('IN_CMS') or exit('No permission resources.');
//模型原型存储路径
define('FORMGUIDE_MODEL_PATH',PC_PATH.'modules'.DIRECTORY_SEPARATOR.'formguide'.DIRECTORY_SEPARATOR.'fields'.DIRECTORY_SEPARATOR);
//模型缓存路径
define('FORMGUIDE_CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
/**
 * 更新form表单模型类
 */
class formguide {

	public function __construct() {
		
	}
	
	/**
	 * 更新模型缓存方法
	 */
	public function public_cache() {
		require FORMGUIDE_MODEL_PATH.'fields.inc.php';
		//更新内容模型类：表单生成、入库、更新、输出
		$classtypes = array('form','input','update','output');
		foreach($classtypes as $classtype) {
			$cache_data = file_get_contents(FORMGUIDE_MODEL_PATH.'formguide_'.$classtype.'.class.php');
			$cache_data = str_replace('}?>','',$cache_data);
			foreach($fields as $field=>$fieldvalue) {
				if(file_exists(FORMGUIDE_MODEL_PATH.$field.DIRECTORY_SEPARATOR.$classtype.'.inc.php')) {
					$cache_data .= file_get_contents(FORMGUIDE_MODEL_PATH.$field.DIRECTORY_SEPARATOR.$classtype.'.inc.php');
				}
			}
			$cache_data .= "\r\n } \r\n?>";
			file_put_contents(FORMGUIDE_CACHE_MODEL_PATH.'formguide_'.$classtype.'.class.php',$cache_data);
			@chmod(FORMGUIDE_CACHE_MODEL_PATH.'formguide_'.$classtype.'.class.php',0777);
		}
		return true;
	}
}
?>