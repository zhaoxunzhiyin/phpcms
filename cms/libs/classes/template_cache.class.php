<?php
/**
 *  模板解析缓存
 */
final class template_cache {

	private $_code;

	/**
	 * 编译模板
	 *
	 * @param $module	模块名称
	 * @param $template	模板文件名
	 * @param $istag	是否为标签模板
	 * @return unknown
	 */

	public function template_compile($module, $template, $style = 'default') {
		if (!$template) {
			pc_base::load_sys_class('service')->show_error('模板文件没有设置');
		}
		if(strpos($module, '/')=== false) {
		$tplfile = $_tpl = TPLPATH.$style.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$template.'.html';
		} elseif (strpos($module, 'yp/') !== false) {
			$module = str_replace('/', DIRECTORY_SEPARATOR, $module);
			$tplfile = $_tpl = TPLPATH.$style.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$template.'.html';
		}
		if ($style != 'default' && !file_exists ( $tplfile )) {
			$style = 'default';
			$tplfile = TPLPATH.'default'.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$template.'.html';
		}
		if (! file_exists ( $tplfile )) {
			if (IS_MOBILE) {
				if ($module=='mobile') {
					$pc = TPLPATH.$style.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.$template.'.html';
				} else {
					$pc = TPLPATH.$style.DIRECTORY_SEPARATOR.str_replace('mobile_', '', $module).DIRECTORY_SEPARATOR.$template.'.html';
				}
				if (is_file($pc)) {
					pc_base::load_sys_class('service')->show_error('移动端模板文件不存在', 'templates'.DIRECTORY_SEPARATOR.$style.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$template.'.html', $pc);
				}
			}
			pc_base::load_sys_class('service')->show_error('模板文件不存在', 'templates'.DIRECTORY_SEPARATOR.$style.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$template.'.html');
		}
		$content = @file_get_contents ( $tplfile );

		$filepath = CACHE_PATH.'caches_template'.DIRECTORY_SEPARATOR.$style.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR;
		if(!is_dir($filepath)) {
			mkdir($filepath, 0777, true);
		}
		$compiledtplfile = $filepath.$template.'.php';
		$content = $this->template_parse($content);
		$strlen = file_put_contents ( $compiledtplfile, $content );
		chmod ( $compiledtplfile, 0777 );
		return $strlen;
	}

	/**
	 * 更新模板缓存
	 *
	 * @param $tplfile	模板原文件路径
	 * @param $compiledtplfile	编译完成后，写入文件名
	 * @return $strlen 长度
	 */
	public function template_refresh($tplfile, $compiledtplfile) {
		$str = @file_get_contents ($tplfile);
		$str = $this->template_parse ($str);
		$strlen = file_put_contents ($compiledtplfile, $str );
		chmod ($compiledtplfile, 0777);
		return $strlen;
	}

	/**
	 * 解析模板
	 *
	 * @param $str	模板内容
	 * @return ture
	 */
	public function template_parse($str) {
		if (!$str) {
			return '';
		}
		if (function_exists('my_parser_view_rule')) {
			$str = my_parser_view_rule($str);
		}
		// 注释内容
		$str = preg_replace('#{note}(.+){/note}#Us', '', $str);
		// 保护代码
		$this->_code = [];
		$str = preg_replace_callback('#{code}(.+){/code}#Us', function ($match) {
			$key = count($this->_code);
			$this->_code[$key] = $match[1];
			return '<!--cms'.$key.'-->';
		}, $str);
		// 3维数组变量
		$str = preg_replace('#{\$(\w+?)\.(\w+?)\.(\w+?)\.(\w+?)}#i', "<?php echo \$\\1['\\2']['\\3']['\\4']; ?>", $str);
		// 2维数组变量
		$str = preg_replace('#{\$(\w+?)\.(\w+?)\.(\w+?)}#i', "<?php echo \$\\1['\\2']['\\3']; ?>", $str);
		// 1维数组变量
		$str = preg_replace('#{\$(\w+?)\.(\w+?)}#i', "<?php echo \$\\1['\\2']; ?>", $str);
		// 3维数组变量
		$str = preg_replace('#\$(\w+?)\.(\w+?)\.(\w+?)\.(\w+?)#Ui', "\$\\1['\\2']['\\3']['\\4']", $str);
		// 2维数组变量
		$str = preg_replace('#\$(\w+?)\.(\w+?)\.(\w+?)#Ui', "\$\\1['\\2']['\\3']", $str);
		// 1维数组变量
		$str = preg_replace('#\$(\w+?)\.(\w+?)#Ui', "\$\\1['\\2']", $str);
		// 引入模板
		$str = preg_replace("/\{template\s+(.+)\}/", "<?php if (\$fn_include = pc_base::load_sys_class('service')->_include(\\1)) include(\$fn_include); ?>", $str);
		// 加载指定文件到模板
		$str = preg_replace(array('#{\s*load\s+"([\$\-_\/\w\.]+)"\s*}#Uis', "/\{load\s+(.+)\}/", '#{\s*include\s+"([\$\-_\/\w\.]+)"\s*}#Uis', "/\{include\s+(.+)\}/"), "<?php if (\$fn_include = pc_base::load_sys_class('service')->_load(\"\\1\")) include(\$fn_include); ?>", $str);
		// php标签
		$str = preg_replace("/\{php\s+(.+)\}/", "<?php \\1?>", $str);
		// if判断语句
		$str = preg_replace("/\{if\s+(.+?)\}/", "<?php if(\\1) { ?>", $str);
		$str = preg_replace("/\{else\}/", "<?php } else { ?>", $str);
		$str = preg_replace(array("/\{else\sif\s+(.+?)\}/", "/\{elseif\s+(.+?)\}/"), "<?php } else if (\\1) { ?>", $str);
		$str = preg_replace("/\{\/if\}/", "<?php } ?>", $str);
		//for 循环
		$str = preg_replace("/\{for\s+(.+?)\}/", "<?php for(\\1) { ?>", $str);
		$str = preg_replace("/\{\/for\}/", "<?php } ?>", $str);
		// 类库函数
		$str = preg_replace('#{([A-Za-z_]+)::(.+)\((.*)\)}#Ui', "<?php echo pc_base::load_sys_class('\\1')->\\2(\\3); ?>", $str);
		//++ --
		$str = preg_replace("/\{\+\+(.+?)\}/", "<?php ++\\1; ?>", $str);
		$str = preg_replace("/\{\-\-(.+?)\}/", "<?php ++\\1; ?>", $str);
		$str = preg_replace("/\{(.+?)\+\+\}/", "<?php \\1++; ?>", $str);
		$str = preg_replace("/\{(.+?)\-\-\}/", "<?php \\1--; ?>", $str);
		// 循环语句
		$str = preg_replace(array('#{\s?loop\s+\$(.+?)\s+\$(\w+?)\s?\$(\w+?)\s?}#i', '#{\s?loop\s+\$(.+?)\s+\$(\w+?)\s?=>\s?\$(\w+?)\s?}#i'), "<?php \$n=1; if (isset(\$\\1) && is_array(\$\\1) && \$\\1) { \$key_\\3=-1;\$count_\\3=dr_count(\$\\1);foreach (\$\\1 as \$\\2=>\$\\3) { \$key_\\3++; \$is_first=\$key_\\3==0 ? 1 : 0;\$is_last=\$count_\\3==\$key_\\3+1 ? 1 : 0; ?>", $str);
		$str = preg_replace('#{\s?loop\s+\$(.+?)\s+\$(\w+?)\s?}#i', "<?php \$n=1; if (isset(\$\\1) && is_array(\$\\1) && \$\\1) { \$key_\\2=-1;\$count_\\2=dr_count(\$\\1);foreach (\$\\1 as \$\\2) { \$key_\\2++; \$is_first=\$key_\\2==0 ? 1 : 0;\$is_last=\$count_\\2==\$key_\\2+1 ? 1 : 0;?>", $str);
		$str = preg_replace(array('#{\s?loop\s+(.+?)\s+\$(\w+?)\s?\$(\w+?)\s?}#i', '#{\s?loop\s+(.+?)\s+\$(\w+?)\s?=>\s?\$(\w+?)\s?}#i'), "<?php \$n=1; if(\\1 && is_array(\\1)) { \$key_\\3=-1;\$count_\\3=dr_count(\\1);foreach(\\1 as \$\\2=>\$\\3) { \$key_\\3++; \$is_first=\$key_\\3==0 ? 1 : 0;\$is_last=\$count_\\3==\$key_\\3+1 ? 1 : 0; ?>", $str);
		$str = preg_replace('#{\s?loop\s+(.+?)\s+\$(\w+?)\s?}#i', "<?php \$n=1;if(\\1 && is_array(\\1)) { \$key_\\2=-1;\$count_\\2=dr_count(\\1);foreach(\\1 as \$\\2) { \$key_\\2++; \$is_first=\$key_\\2==0 ? 1 : 0;\$is_last=\$count_\\2==\$key_\\2+1 ? 1 : 0; ?>", $str);
		$str = preg_replace('#{\s?\/loop\s?}#i', "<?php \$n++;}}unset(\$n); ?>", $str);
		// PHP常量
		$str = preg_replace('#{([A-Z_0-9]+)}#', "<?php echo \\1; ?>", $str);
		// PHP变量
		$str = preg_replace("/\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/", "<?php echo \\1;?>", $str);
		$str = preg_replace("/\{\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/", "<?php echo \\1;?>", $str);
		$str = preg_replace("/\{(\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}/", "<?php echo \\1;?>", $str);
		$str = preg_replace('#{\$(.+?)}#i', "<?php echo \$\\1; ?>", $str);
		// PHP函数
		$str = preg_replace_callback("/\{(\\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff]+)\}/s", array($this, 'addquote'), $str);
		$str = preg_replace("/\{([A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*)\}/s", "<?php echo \\1;?>", $str);
		$str = preg_replace_callback("/\{pc:(\w+)\s+([^}]+)\}/i", array($this, 'pc_tag_callback'), $str);
		$str = preg_replace_callback("/\{\/pc\}/i", array($this, 'end_pc_tag'), $str);
		$str = preg_replace('#{([a-z_0-9]+)\((.*)\)}#Ui', "<?php echo \\1(\\2); ?>", $str);
		// 结果为空时
		$str = preg_replace('#{\s?empty\s?}#i', "<?php } } else { ?>", $str);
		$str = preg_replace('#{\s?\/empty\s?}#i', "<?php } ?>", $str);
		// 恢复代码
		if ($this->_code) {
			foreach ($this->_code as $key => $code) {
				$str = str_replace('<!--cms'.$key.'-->', $code, $str);
			}
		}
		$str = "<?php defined('IN_CMS') or exit('No permission resources.'); ?>" . $str;
		return $str;
	}

	/**
	 * 转义 // 为 /
	 *
	 * @param $var	转义的字符
	 * @return 转义后的字符
	 */
	public function addquote($matches) {
		$var = '<?php echo '.$matches[1].';?>';
		return str_replace( "\\\"", "\"", preg_replace( "/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var));
	}
	public static function pc_tag_callback($matches) {
		return self::pc_tag($matches[1],$matches[2], $matches[0]);
	}	
	/**
	 * 解析PC标签
	 * @param string $op 操作方式
	 * @param string $data 参数
	 * @param string $html 匹配到的所有的HTML代码
	 */
	public static function pc_tag($op, $data, $html) {
		preg_match_all("/([a-z]+)\=[\"]?([^\"]+)[\"]?/i", stripslashes($data), $matches, PREG_SET_ORDER);
		$arr = array('action','num','cache','page', 'pagesize', 'urlrule', 'return', 'start');
		$tools = array('json', 'xml', 'block', 'get', 'table');
		$datas = array();
		$tag_id = md5(stripslashes($html));
		//可视化条件
		$str_datas = 'op='.$op.'&tag_md5='.$tag_id;
		foreach ($matches as $v) {
			$str_datas .= $str_datas ? "&$v[1]=".($op == 'block' && strpos($v[2], '$') === 0 ? $v[2] : urlencode($v[2])) : "$v[1]=".(strpos($v[2], '$') === 0 ? $v[2] : urlencode($v[2]));
			if(in_array($v[1], $arr)) {
				${$v[1]} = $v[2];
				continue;
			}
			$datas[$v[1]] = $v[2];
		}
		$str = '';
		$num = isset($num) && intval($num) ? intval($num) : 10;
		$cache = isset($cache) && intval($cache) ? intval($cache) : 0;
		$return = isset($return) && trim($return) ? trim($return) : 'data';
		if (!isset($urlrule)) $urlrule = '';
		if (!empty($cache) && !isset($page)) {
			$str .= '$tag_cache_name = md5(implode(\'&\','.self::arr_to_html($datas).').\''.$tag_id.'\');if(!$'.$return.' = pc_base::load_sys_class(\'cache\')->get_data($tag_cache_name)){';
		}
		if (in_array($op,$tools)) {
			switch ($op) {
				case 'json':
						if (isset($datas['url']) && !empty($datas['url'])) {
							$str .= '$json = @file_get_contents(\''.$datas['url'].'\');';
							$str .= '$'.$return.' = json_decode($json, true);';
						}
					break;
					
				case 'xml':
						$str .= '$xml = pc_base::load_sys_class(\'xml\');';
						$str .= '$xml_data = @file_get_contents(\''.$datas['url'].'\');';
						$str .= '$'.$return.' = $xml->xml_unserialize($xml_data);';
					break;
					
				case 'get':
						$str .= 'pc_base::load_sys_class("get_model", "model", 0);';
						if ($datas['dbsource']) {
							$dbsource = getcache('dbsource', 'commons');
							if (isset($dbsource[$datas['dbsource']])) {
								$str .= '$get_db = new get_model('.var_export($dbsource,true).', \''.$datas['dbsource'].'\');';
							} else {
								return false;
							}
						} else {
							$str .= '$get_db = new get_model();';
						}
						$num = isset($num) && intval($num) > 0 ? intval($num) : 10;
						if (isset($start) && intval($start)) {
							$limit = intval($start).','.$num;
						} else {
							$limit = $num;
						}
						if (isset($page)) {
							$str .= '$pagesize = '.$num.';';
							$str .= '$page = intval('.$page.') ? intval('.$page.') : 1;if($page<=0){$page=1;}';
							$str .= '$offset = ($page - 1) * $pagesize;';
							$limit = '$offset,$pagesize';
							$sql = 'SELECT COUNT(*) as count FROM ('.$datas['sql'].') T';
							$str .= '$get_db->sql_query("'.$sql.'");$s = $get_db->fetch_next();$pages = pages($s[\'count\'], $page, $pagesize, $urlrule);';
						}
						
						if (!empty($cache) && isset($page)) {
							$str .= '$tag_cache_name = md5(\''.new_addslashes($datas['sql']).'\'.$page.\''.$tag_id.'\');if(!$'.$return.' = pc_base::load_sys_class(\'cache\')->get_data($tag_cache_name)){';
						}
						$str .= '$get_db->sql_query("'.$datas['sql'].' LIMIT '.$limit.'");while(($s = $get_db->fetch_next()) != false) {$a[] = $s;}$'.$return.' = $a;unset($a);';
						if (!empty($cache) && isset($page)) {
							$str .= 'if(!empty($'.$return.')){pc_base::load_sys_class(\'cache\')->set_data($tag_cache_name, $'.$return.', '.$cache.');}';
							$str .= '}';
						}
					break;
					
				case 'table':
						if (!$datas['table']) {
							return;
						}
						$str .= 'pc_base::load_sys_class("get_model", "model", 0);';
						if ($datas['dbsource']) {
							$dbsource = getcache('dbsource', 'commons');
							if (isset($dbsource[$datas['dbsource']])) {
								$str .= '$get_db = new get_model('.var_export($dbsource,true).', \''.$datas['dbsource'].'\');';
							} else {
								return;
							}
						} else {
							$str .= '$get_db = new get_model();';
						}
						$tableinfo = pc_base::load_sys_class('cache')->get_data('table-'.$datas['table']);
						if (!$tableinfo) {
							$get_fields = pc_base::load_sys_class('get_model', 'model')->get_fields($datas['table']);
							foreach ($get_fields as $i => $t) {
								$tableinfo[] = $i;
							}
							pc_base::load_sys_class('cache')->set_data('table-'.$datas['table'], $tableinfo, 36000);
						}
						if (!$tableinfo) {
							return;
						}
						$num = isset($num) && intval($num) > 0 ? intval($num) : 10;
						if (isset($start) && intval($start)) {
							$limit = intval($start).','.$num;
						} else {
							$limit = $num;
						}
						$table = pc_base::load_sys_class('get_model', 'model')->dbprefix($datas['table']);
						$where = self::_set_where_field_prefix($datas['where'], $tableinfo, $table); // 给条件字段加上表前缀
						$datas['field'] = self::_set_select_field_prefix($datas['field'], $tableinfo, $table); // 给显示字段加上表前缀
						$_order = [];
						$_order[$table] = $tableinfo;
						$sql_from = $table; // sql的from子句
						// 关联表
						if ($datas['join'] && $datas['on']) {
							$rt = self::_join_table($table, $datas, $_order, $sql_from);
							if (!$rt['code']) {
								return;
							}
							list($datas, $_order, $sql_from) = $rt['data'];
						}
						$datas['order'] = self::_set_orders_field_prefix($datas['order'], $_order); // 给排序字段加上表前缀
						
						if (!empty($cache) && isset($page)) {
							$str .= '$tag_cache_name = md5(implode(\'&\','.self::arr_to_html($datas).').$page.\''.$tag_id.'\');if(!$'.$return.' = pc_base::load_sys_class(\'cache\')->get_data($tag_cache_name)){';
						}
						if (isset($page)) {
							$str .= '$pagesize = '.$num.';';
							$str .= '$page = intval('.$page.') ? intval('.$page.') : 1;if($page<=0){$page=1;}';
							$str .= '$offset = ($page - 1) * $pagesize;';
							$limit = '$offset,$pagesize';
							$sql = 'SELECT count(*) as count FROM '.$sql_from.' '.($where ? 'WHERE '.$where : "").' ORDER BY NULL';
							$str .= '$get_db->sql_query("'.$sql.'");$row = $get_db->fetch_next();';
							$str .= '$total = (int)$row[\'count\'];';
							$str .= '$r = $get_db->sql_query("SELECT '.self::_get_select_field($datas['field'] ? $datas['field'] : '*').' FROM '.$sql_from.($where ? ' WHERE '.$where : '').($datas['order'] ? ' ORDER BY '.$datas['order'] : '').' LIMIT '.$limit.'");while(($s = $get_db->fetch_next()) != false) {$a[] = $s;}$'.$return.' = $a;unset($a);';
							if ($datas['maxlimit']) {
								$str .= 'if ($total > '.$datas['maxlimit'].') {';
								$str .= '	$total = '.$datas['maxlimit'].';';
								$str .= '	$pages = pages($total, $page, $pagesize, $urlrule);';
								$str .= '	if ($page * $pagesize > $total) {';
								$str .= '		log_message(\'debug\', \'maxlimit设置最大显示'.$datas['maxlimit'].'条，当前（\'.$total.\'）已超出\');';
								$str .= '		$'.$return.' = array();';
								$str .= '		$pages = \'\';';
								$str .= '	}';
								$str .= '}';
							} else {
								$str .= '$pages = pages($total, $page, $pagesize, $urlrule);';
							}
						} else {
							$str .= '$r = $get_db->sql_query("SELECT '.self::_get_select_field($datas['field'] ? $datas['field'] : '*').' FROM '.$sql_from.($where ? ' WHERE '.$where : '').($datas['order'] ? ' ORDER BY '.$datas['order'] : '').' LIMIT '.$limit.'");while(($s = $get_db->fetch_next()) != false) {$a[] = $s;}$'.$return.' = $a;unset($a);';
						}
						if (!empty($cache) && isset($page)) {
							$str .= 'if(!empty($'.$return.')){pc_base::load_sys_class(\'cache\')->set_data($tag_cache_name, $'.$return.', '.$cache.');}';
							$str .= '}';
						}
					break;
					
				case 'block':
					$str .= '$block_tag = pc_base::load_app_class(\'block_tag\', \'block\');';
					$str .= 'echo $block_tag->pc_tag('.self::arr_to_html($datas).');';
					break;
			}
		} else {
			if (!isset($action) || empty($action)) return false;
			if (module_exists($op) && file_exists(PC_PATH.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$op.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.$op.'_tag.class.php')) {
				$str .= '$'.$op.'_tag = pc_base::load_app_class("'.$op.'_tag", "'.$op.'");if (method_exists($'.$op.'_tag, \''.$action.'\')) {';
				if (isset($start) && intval($start)) {
					$datas['limit'] = intval($start).','.$num;
				} else {
					$datas['limit'] = $num;
				}
				if (!empty($cache) && isset($page)) {
					$str .= '$tag_cache_name = md5(implode(\'&\','.self::arr_to_html($datas).').$page.\''.$tag_id.'\');if(!$'.$return.' = pc_base::load_sys_class(\'cache\')->get_data($tag_cache_name)){';
				}
				if (isset($page)) {
					$op == 'content' && $str .= '$setting = dr_string2array(dr_cat_value($catid, \'setting\'));';
					$op == 'content' && $str .= 'if ((int)$setting[\'ishtml\']) {';
					$op == 'content' && $str .= '$pagesize = (int)$setting[\'pagesize\'] ? (int)$setting[\'pagesize\'] : 10;';
					$op == 'content' && $str .= '} else {';
					$str .= '$pagesize = defined(\'HTML\') ? 10 : '.$num.';';
					$op == 'content' && $str .= '}';
					$str .= '$page = intval('.$page.') ? intval('.$page.') : 1;if($page<=0){$page=1;}';
					$str .= '$offset = ($page - 1) * $pagesize;';
					$datas['limit'] = '$offset.",".$pagesize';
					$datas['action'] = $action;
					$op == 'content' && $str .= 'if (!defined(\'HTML\')) {';
					$str .= '$'.$op.'_total = $'.$op.'_tag->count('.self::arr_to_html($datas).');';
					if ($datas['maxlimit']) {
						$str .= 'if ($'.$op.'_total > '.$datas['maxlimit'].') {';
						$str .= '$'.$op.'_total = '.$datas['maxlimit'].';';
						$str .= '$'.$return.' = $'.$op.'_tag->'.$action.'('.self::arr_to_html($datas).');';
						$str .= '$pages = pages($'.$op.'_total, $page, $pagesize, $urlrule);';
						$str .= 'if ($page * $pagesize > $'.$op.'_total) {';
						$str .= 'log_message(\'debug\', \'maxlimit设置最大显示'.$datas['maxlimit'].'条，当前（\'.$'.$op.'_total.\'）已超出\');';
						$str .= '$'.$return.' = array();';
						$str .= '$pages = \'\';';
						$str .= '}';
						$str .= '}else{';
						$str .= '$'.$return.' = $'.$op.'_tag->'.$action.'('.self::arr_to_html($datas).');';
						$str .= '$pages = pages($'.$op.'_total, $page, $pagesize, $urlrule);';
						$str .= '}';
					} else {
						$str .= '$'.$return.' = $'.$op.'_tag->'.$action.'('.self::arr_to_html($datas).');';
						$str .= '$pages = pages($'.$op.'_total, $page, $pagesize, $urlrule);';
					}
					$op == 'content' && $str .= '} else {';
					$op == 'content' && $str .= '$'.$return.' = $'.$op.'_tag->'.$action.'('.self::arr_to_html($datas).');';;
					$op == 'content' && $str .= '}';
				} else {
					$str .= '$'.$return.' = $'.$op.'_tag->'.$action.'('.self::arr_to_html($datas).');';
				}
				if (!empty($cache) && isset($page)) {
					$str .= 'if(!empty($'.$return.')){pc_base::load_sys_class(\'cache\')->set_data($tag_cache_name, $'.$return.', '.$cache.');}';
					$str .= '}';
				}
				$str .= '}';
			} else {
				$str .= '$'.$return.' = array();';
			}
		}
		if (!empty($cache) && !isset($page)) {
			$str .= 'if(!empty($'.$return.')){pc_base::load_sys_class(\'cache\')->set_data($tag_cache_name, $'.$return.', '.$cache.');}';
			$str .= '}';
		}
		return "<"."?php if(defined('IS_ADMIN') && IS_ADMIN && !defined('HTML')) {echo \"<div class=\\\"admin_piao\\\" pc_action=\\\"".$op."\\\" data=\\\"".$str_datas."\\\"><a href=\\\"javascript:void(0)\\\" class=\\\"admin_piao_edit\\\">".($op=='block' ? L('block_add') : L('edit'))."</a>\";}".$str."?".">";
	}

	/**
	 * PC标签结束
	 */
	static private function end_pc_tag() {
		return '<?php if(defined(\'IS_ADMIN\') && IS_ADMIN && !defined(\'HTML\')) {echo \'</div>\';}?>';
	}

	/**
	 * 转换数据为HTML代码
	 * @param array $data 数组
	 */
	private static function arr_to_html($data) {
		if (is_array($data)) {
			$str = 'array(';
			foreach ($data as $key=>$val) {
				if (is_array($val)) {
					$str .= "'$key'=>".self::arr_to_html($val).",";
				} else {
					if (strpos($val, '$')===0) {
						$str .= "'$key'=>$val,";
					} else {
						$str .= "'$key'=>'".new_addslashes($val)."',";
					}
				}
			}
			return $str.')';
		}
		return false;
	}

	// 格式化查询参数
	private static function _get_select_field($field) {

		if ($field != '*') {
			$my = [];
			$array = explode(',', $field);
			foreach ($array as $t) {
				if (strpos($t, '`') !== false) {
					$my[] = $t;
					continue;
				}
			}
			if (!$my) {
				$field = '*';
			} else {
				$field = implode(',', $my);
			}
		}

		return $field;
	}

	// join 联合查询表
	public static function _join_table($main, $system, $_order, $sql_from) {

		$table = pc_base::load_sys_class('get_model', 'model')->dbprefix($system['join']); // 关联表
		$tableinfo = pc_base::load_sys_class('cache')->get_data('table-join-'.$system['join']);
		if (!$tableinfo) {
			$get_fields = pc_base::load_sys_class('get_model', 'model')->get_fields($system['join']);
			foreach ($get_fields as $i => $t) {
				$tableinfo[] = $i;
			}
			if (!$tableinfo) {
				return dr_return_data(0, '关联数据表('.$system['join'].')结构不存在');
			}
			pc_base::load_sys_class('cache')->set_data('table-join-'.$system['join'], $tableinfo, 36000);
		}

		list($a, $b) = explode(',', $system['on']);
		$b = $b ? $b : $a;
		$system['field'] = self::_set_select_field_prefix($system['field'], $tableinfo, $table); // 给显示字段加上表前缀
		$_order[$table] = $tableinfo;
		$sql_from.= ' LEFT JOIN `'.$table.'` ON `'.$main.'`.`'.$a.'`=`'.$table.'`.`'.$b.'`';
		return dr_return_data(1, 'ok', [$system, $_order, $sql_from]);
	}

	// 给条件字段加上表前缀
	public static function _set_where_field_prefix($where, $field, $prefix) {

		if (!$where) {
			return;
		}

		$where = explode(',', str_ireplace(' and ', ',', $where));
		$sql_where = [];
		foreach ($where as $i => $t) {
			$r = explode('=', $t);
			if (dr_in_array($r[0], $field)) {
				if (is_numeric($r[1])) {
					$sql_where[] = "`$prefix`.`{$r[0]}`={$r[1]}";
				} else {
					$sql_where[] = "`$prefix`.`{$r[0]}`='{$r[1]}'";
				}
			}
		}

		return implode(' AND ', $sql_where);
	}

	// 给显示字段加上表前缀
	public static function _set_select_field_prefix($select, $field, $prefix) {

		if ($select) {
			$array = explode(',', $select);
			foreach ($array as $i => $t) {

				$field_prefix = '';
				if (strpos($t, 'DISTINCT_') === 0) {
					$t = str_replace('DISTINCT_', '', $t);
					$field_prefix = 'DISTINCT ';
				}

				if (dr_in_array($t, $field)) {
					$array[$i] = $field_prefix."`$prefix`.`$t`";
				} elseif (strpos($t, '.') !== false && strpos($t, '`') === false) {
					list($a, $b) = explode('.', $t);
					if (($prefix == $a || substr($prefix, strlen(pc_base::load_sys_class('get_model', 'model')->dbprefix())) == $a)) {
						if (strpos($b, ':') !== false) {
							// 存在别名
							list($b, $cname) = explode(':', $b);
							if (dr_in_array($b, $field)) {
								$array[$i] = $field_prefix."`$prefix`.`$b` as `$cname`";
							}
						} else {
							if (dr_in_array($b, $field)) {
								$array[$i] = $field_prefix."`$prefix`.`$b`";
							}
						}
					}
				}
			}
			return implode(',', $array);
		}

		return $select;
	}

	// 给排序字段加上多表前缀
	public static function _set_orders_field_prefix($order, $fields) {

		if (!$order) {
			return NULL;
		} elseif (strtoupper($order) == 'FIXNULL') {
			// NULL排序
			return 'NULL';
		} elseif (in_array(strtoupper($order), ['RAND()', 'RAND'])) {
			// 随机排序
			return 'RAND()';
		}

		$order = urldecode($order);
		if (strpos($order, '`') !== false) {
			return $order;
		}

		// 字段排序
		$my = [];
		$array = explode(',', $order);

		foreach ($array as $i => $t) {
			$a = explode(' ', $t);
			$b = end($a);
			if (in_array(strtolower($b), ['desc', 'asc'])) {
				$a = str_replace(' '.$b, '', $t);
			} else {
				$a = $t;
				$b = '';
			}
			$b = strtoupper($b);
			foreach ($fields as $prefix => $field) {
				if (is_array($field)) {
					if (dr_in_array($a, $field)) {
						$my[$i] = "`$prefix`.`$a` ".($b ? $b : "DESC");
					}
				}
			}

		}
		if ($my) {
			return implode(',', $my);
		}

		return NULL;
	}
}
?>