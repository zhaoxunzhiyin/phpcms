<?php
defined('IN_CMS') or exit('No permission resources.');

/**
 * 自动任务脚本
 */
// 任务计划
pc_base::load_sys_class('hooks')::trigger('cron');
// 清理缓存数据
if (!is_file(CACHE_PATH.'run_auto_cache_time.php')) {
	$time = SYS_TIME;
	file_put_contents(CACHE_PATH.'run_auto_cache_time.php', $time);
} else {
	$time = file_get_contents(CACHE_PATH.'run_auto_cache_time.php');
}

// 多少天清理一次系统缓存
$day = max(3, SYS_CACHE_CRON);
if (SYS_TIME - $time > 3600 * 24 * $day) {
	// 缓存清理
	if (!SYS_CACHE_CLEAR) {
		// 清空系统缓存
		pc_base::load_sys_class('cache')->init()->clean();
		// 清空文件缓存
		pc_base::load_sys_class('cache_file')->clean();
	}
	// 删除缓存保留24小时内的文件
	$path = [
		CACHE_PATH.'caches_authcode/caches_data/',
		CACHE_PATH.'sessions',
		CACHE_PATH.'temp',
	];
	foreach ($path as $p) {
		if ($fp = opendir($p)) {
			while (FALSE !== ($file = readdir($fp))) {
				if ($file === '.' OR $file === '..'
					OR $file === 'index.html'
					OR $file === '.htaccess'
					OR $file[0] === '.'
					OR !is_file($p.'/'.$file)
					OR SYS_TIME - filemtime($p.'/'.$file) < 3600 * 24 // 保留24小时内的文件
				) {
					continue;
				}
				unlink($p.'/'.$file);
			}
		}
	}
	if ($fp) {
		flock ( $fp ,LOCK_UN);
		fclose( $fp );
	}
	file_put_contents(CACHE_PATH.'run_auto_cache_time.php', SYS_TIME);
	// 清理日志
	$map = dr_file_map(CACHE_PATH.'caches_error/caches_data/');
	if ($map) {
		foreach ($map as $file) {
			if (strpos($file, 'log-') !== false) {
				$file = CACHE_PATH.'caches_error/caches_data/'.$file;
				$time = filectime($file);
				if ($time && SYS_TIME - $time > 3600 * 24 * 30) {
					@unlink($file);
				}
			}
		}
	}
}
// 为插件单独执行计划
$local = pc_base::load_sys_class('service')::apps();
if ($local) {
	foreach ($local as $dir => $path) {
		if (module_exists($dir) && is_file($path.'config/cron.php')) {
			require $path.'config/cron.php';
		}
	}
}
// 自动任务执行时间
file_put_contents(CACHE_PATH.'run_time.php', dr_date(SYS_TIME));
exit('任务执行成功：Run Ok');