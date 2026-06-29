<?php

/**
 * debug.class.php   debug类
 */

use ReflectionFunction;
use ReflectionMethod;
use Throwable;

class debug {

	private $_is_404 = 0;

	private $exceptionCaughtByExceptionHandler = null;

	public $ob_level;

	public $logDeprecations = true;

	static private $logs = [];

	protected $viewPath;

	protected static $isColored = false;

	protected static $foreground_colors = [
		'black'        => '0;30',
		'dark_gray'    => '1;30',
		'blue'         => '0;34',
		'dark_blue'    => '0;34',
		'light_blue'   => '1;34',
		'green'        => '0;32',
		'light_green'  => '1;32',
		'cyan'         => '0;36',
		'light_cyan'   => '1;36',
		'red'          => '0;31',
		'light_red'    => '1;31',
		'purple'       => '0;35',
		'light_purple' => '1;35',
		'yellow'       => '0;33',
		'light_yellow' => '1;33',
		'light_gray'   => '0;37',
		'white'        => '1;37',
	];

	protected static $background_colors = [
		'black'      => '40',
		'red'        => '41',
		'green'      => '42',
		'yellow'     => '43',
		'blue'       => '44',
		'magenta'    => '45',
		'cyan'       => '46',
		'light_gray' => '47',
	];

	protected static $lastWrite;

	public static $info = array();
	public static $sqls = array();
	public static $files = array();
	public static $errors = array();
	public static $trace = array();
	public static $stoptime;
	/**
	 *在脚本结束处调用获取脚本结束时间的微秒值
	 */
	public static function stop() {
		self::$stoptime = microtime(true);
	}
	/**
	 *返回同一脚本中两次获取时间的差值
	 */
	public static function spent() {
		return round((self::$stoptime - SYS_START_TIME) , 4);
		//计算后以4舍5入保留4位返回
	}
	/**
	 * 添加调试消息
	 * @param	string	$msg	调试消息字符串
	 * @param	int		$type	消息的类型
	 * @param	int		$start_time	开始时间，用于计算SQL耗时
	 */
	public static function addmsg($msg, $type=0, $start_time=0) {
		switch($type) {
			case 0:
				self::$info[] = $msg;
			break;
			case 1:
				self::$sqls[] = htmlspecialchars($msg).'; [ RunTime:'.number_format(microtime(true)-$start_time, 6).'s ]';
			break;
			case 2:
				self::$errors[] = $msg;
			break;
			case 3:
				self::$files[] = $msg;
			break;
			case 4:
				self::$trace[] = $msg;
			break;
		}
	}
	// 自定义调试的消息 使用方法 debug::trace('msg'); 
	public static function trace($msg) {
		self::addmsg($msg, 4);
	}
	/**
	 * 获取debug信息
	 */
	public static function get_debug() {
		return array(
			'base' => self::$info,
			'files' => self::$files,
			'errors' => self::$errors,
			'sqls' => self::$sqls,
			'trace' => self::$trace,
		);
	}
	/**
	 * 获取文件加载信息
	 */
	private static function getFileInfo() {
		// 系统默认显示信息
		$get_files = get_included_files();
		foreach ($get_files as $key=>$file) {
			self::addmsg($file.' ( '.number_format(filesize($file)/1024,2).' KB )', 3);
		}
	}
	/**
	 * 获取环境基本信息
	 */
	private static function getBaseInfo() {
		// 系统默认显示信息
		$baseinfo_arr = array(
			1=> ' 服务器信息： '.$_SERVER['SERVER_SOFTWARE'],
			2=> ' 请求信息: '.date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']).' '.$_SERVER['SERVER_PROTOCOL'].' '.$_SERVER['REQUEST_METHOD'].' : '.$_SERVER["REQUEST_URI"],
			3=> ' 内存消耗：' . number_format((memory_get_usage() - SYS_START_MEM) / 1024, 2) . 'kb',
			4=> ' 文件加载: '.count(self::$files).' , SQL: '.count(self::$sqls).' , '.' 错误: '.count(self::$errors).' , '.' 调试: '.count(self::$trace).' ', 
			5=> ' 运行时间: '.self::spent().'s [ 吞吐率：' . (self::spent() > 0 ? number_format(1 / self::spent(), 2) : '∞') . 'req/s ]',
		);
		foreach ($baseinfo_arr as $key=>$info) {
			self::addmsg($info);
		}
	}
	/**
	 * 输出调试消息
	 */
	public static function message() { 
		self::stop();
		self::getFileInfo();
		self::getBaseInfo();
		pc_base::load_sys_class('service')->assign('page_trace', self::get_debug());
		pc_base::load_sys_class('service')->admin_display('debug', 'admin');
	}
	
	/**
	 * 排除部分错误提示
	 */
	public function errorHandler($severity, $message, $file = null, $line = null)
	{
		if ($this->isDeprecationError($severity)) {
			if ($this->isSessionSidDeprecationError($message, $file, $line)) {
				return true;
			}

			if ($this->isImplicitNullableDeprecationError($message, $file, $line)) {
				return true;
			}

			if (! $this->logDeprecations || $_ENV['CODEIGNITER_SCREAM_DEPRECATIONS'] ?? $_SERVER['CODEIGNITER_SCREAM_DEPRECATIONS'] ?? getenv('CODEIGNITER_SCREAM_DEPRECATIONS')) {
				throw new \ErrorException($message, 0, $severity, $file, $line);
			}

			return $this->handleDeprecationError($message, $file, $line);
		}

		if ((error_reporting() & $severity) !== 0) {
			throw new \ErrorException($message, 0, $severity, $file, $line);
		}

		return false; // return false to propagate the error to PHP standard error handler
	}

	/**
	 * 错误日志增加最后执行的sql语句
	 *
	 * @param \Throwable $exception
	 */
	public function exceptionHandler(Throwable $exception) {

		$message = $this->_cn_msg($exception->getMessage());

		if (CI_DEBUG) {
			// 传入对象到日志中
			log_message('critical', $exception);
		}

		// ajax 返回
		if (IS_AJAX || IS_API) {
			// 调试模式不屏蔽敏感信息
			$file = $exception->getFile();
			if (strpos($file, CACHE_PATH.'caches_template') !== false) {
				$message = '模板标签写法错误：'.$message;
				$arr = pc_base::load_sys_class('service')->get_view_files();
				if ($arr) {
					$one = current($arr);
					$message.= '（'.CI_DEBUG ? $one['path'] : basename($one['path']).'）';
				}
			}
			if (CI_DEBUG) {
				$message.= '<br>错误文件：'.$file.'（'.$exception->getLine().'）';
				$message.= '<br>访问地址：'.pc_base::load_sys_class('service')->now_php_url();
			} else {
				$message = str_replace([PC_PATH, CMS_PATH], ['/', '/'], $message);
			}
			dr_exit_msg(0, $message);
		}

		$this->render($exception);

		exit(1);
	}

	/**
	 * 错误输出结果
	 */
	protected function render(\Throwable $exception)
	{

		$message = $this->_cn_msg($exception->getMessage());
		if (empty($message)) {
			$message = '(null)';
		}


		// 调试模式不屏蔽敏感信息
		if (CI_DEBUG) {
			if ($this->_is_404) {
				//,404页面不显示路径
			} else {
				$message.= '<br>'.$this->_rp_file($exception->getFile()).'（'.$exception->getLine().'）';
			}
		} else {
			$message = str_replace([PC_PATH, CMS_PATH], ['/', '/'], $message);
		}

		if (strpos($message, 'The action you requested is not allowed') !== false) {
			dr_exit_msg(0, '提交验证超时，请重试', 'CSRFVerify');
		}

		// ajax 返回
		if (IS_AJAX || IS_API) {
			dr_exit_msg(0, $message);
		}

		$this->viewPath = TEMPPATH.'errors/';
		// Determine possible directories of error views
		$path = $this->viewPath;

		$path .= (is_cli() ? 'cli' : 'html') . DIRECTORY_SEPARATOR;

		// Determine the views
		$view = $this->determineView($exception, $path);

		// Check if the view exists
		if (is_file($path . $view)) {
			$viewFile = $path . $view;
		}

		if (! isset($viewFile)) {
			echo 'The error view files were not found. Cannot render exception trace.';
			exit(1);
		}

		if (ob_get_level() > $this->ob_level + 1) {
			ob_end_clean();
		}

		echo(function () use ($exception, $viewFile, $message): string {
			$vars = $this->collectVars($exception);
			extract($vars, EXTR_SKIP);
			$file = $this->_rp_file($exception->getFile());
			$is_template = false;
			$line_template = 0;
			if (strpos($file, CACHE_PATH.'caches_template') !== false) {
				list($a, $b) = explode('on line ', $this->_cn_msg($exception->getMessage()));
				if (is_numeric($b)) {
					$line_template = $b;
				}
				$message = '模板标签写法错误：'.$message;
				$is_template = pc_base::load_sys_class('service')->get_view_files();
			}
			ob_start();
			include $viewFile;
			return ob_get_clean();
		})();
	}

	/**
	 * 中文翻译输出的错误信息
	 */
	private function _cn_msg($message) {

		if (!$message) {
			return $message;
		}

		if (strpos($message, 'Unable to connect to the database') !== false) {
			$message.= '<br>无法连接到数据库，检查数据库是否启动或者数据库配置文件不对，caches/configs/database.php';
		} elseif (strpos($message, 'Unclosed \'{\'') !== false) {
			$message.= '<br>循环体或者if语句，缺少结束语句，{ }没有成对出现';
		} elseif (strpos($message, 'Cannot access offset of type string on string') !== false) {
			$message.= '<br>此变量是字符串，不能使用数组的方式调用他，检查下代码语法';
		} elseif (strpos($message, 'Call to undefined function') !== false) {
			$message.= '<br>'.str_replace('Call to undefined function', '函数没有定义', $message);
		} elseif (strpos($message, 'open_basedir restriction in effect') !== false) {
			$message.= '<br>目录被限制读取，需要设置.users.ini文件中的目录白名单';
		} elseif (strpos($message, 'Undefined constant') !== false) {
			$message.= '<br>'.str_replace('Undefined constant', '变量或者常量没有定义', $message);
		} elseif (preg_match("/Table '(.+)' doesn't exist/", $message, $mt)) {
			$message.= '<br>数据库表'.$mt[1].'不存在，表丢失或者表没有创建成功';
		} elseif (preg_match("/Unknown column '(.+)' in 'field list'/", $message, $mt)) {
			$message.= '<br>表中没有字段'.$mt[1].'，字段没有被创建';
		} elseif (preg_match("/Access level to (.+) must be protected \(as in class (.+)\) or weaker/U", $message, $mt)) {
			$message.= '<br>'.$mt[1].'在类'.$mt[2].'中已经被定义过更高级别的权限，请删除本文件的定义代码';
		} elseif (preg_match("/Creation of dynamic property (.+) is deprecated/", $message, $mt)) {
			$message.= '<br>动态属性被废除'.$mt[1].'，请预先定义';
		} elseif (preg_match("/Failed opening required '(.+)'/", $message, $mt)) {
			$message.= '<br>文件'.$mt[1].'不存在，文件丢失或者文件没有创建成功';
		} elseif (preg_match("/syntax error, unexpected token (.+)/", $message, $mt)) {
			$message.= '<br>PHP语法错误 或者 模板标签语法错误，检查上下行代码是否写对';
		} elseif (preg_match("/Cannot declare class (.+), because the name is already in use/", $message, $mt)) {
			$message.= '<br>类名'.$mt[1].'重复，全文搜索下哪个地方被重复命名了';
		} elseif (preg_match("/Controller method is not found: (.+)/", $message, $mt)) {
			$message.= '<br>检查此文件中是否有'.$mt[1].'方法名：'.PC_PATH.'modules/'.ROUTE_M.'/'.ROUTE_C.'.php';
			$this->_is_404 = 1;
		} elseif (preg_match("/Controller or its method is not found:(.+)/", $message, $mt)) {
			$message.= '<br>检查此文件是否存在：'.PC_PATH.'modules/'.ROUTE_M.'/'.ROUTE_C.'.php，检查地址是否正确，注意控制器文件';
			$this->_is_404 = 1;
		} elseif (preg_match("/count\(\): Argument #1 \((.+)\) must be of type Countable\|array/", $message, $mt)) {
			$message.= '<br>需要将count函数改为dr_count';
		}

		return $message;
	}

	/**
	 * Checks to see if any errors have happened during shutdown that
	 * need to be caught and handle them.
	 *
	 * @codeCoverageIgnore
	 */
	public function shutdownHandler() {
		$error = error_get_last();
		if ($error === null) {
			return;
		}
		list($type, $message, $file, $line) = [$error['type'], $error['message'], $error['file'], $error['line']];
		if ($this->exceptionCaughtByExceptionHandler) {
			$message .= "\n【Previous Exception】\n"
				. get_class($this->exceptionCaughtByExceptionHandler) . "\n"
				. $this->exceptionCaughtByExceptionHandler->getMessage() . "\n"
				. $this->exceptionCaughtByExceptionHandler->getTraceAsString();
		}
		if (in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE], true)) {
			$this->exceptionHandler(new \ErrorException($message, 0, $type, $file, $line));
		}
	}

	/**
	 * 替换模板文件显示完整路径
	 */
	private function _rp_file($file) {

		if (strpos((string)$file, '.cache.php') !== false && strpos((string)$file, '_DS_') !== false) {
			$file = str_replace([CACHE_PATH.'caches_template/', '_DS_', '.cache.php'], ['', '/', ''], $file);
		}

		return $file;
	}

	// 错误日志记录
	public static function Log($level, $message, array $context = []) {

		if ($level == 'debug' && defined('IS_DEBUG') && IS_DEBUG) {
			return;
		}

		if (is_object($message)) {
			$msg = $message->getMessage();
			$code = md5($msg);
			if (is_array( static::$logs) && in_array($code, static::$logs)) {
				return;
			}

			static::$logs[] = $code;

			$context['trace'] = $message->getTraceAsString();
			$context['sql'] = implode('、', self::get_debug()['sqls']);
			$context['url'] = FC_NOW_URL;
			$context['user'] = dr_safe_replace($_SERVER['HTTP_USER_AGENT']);
			$context['referer'] = dr_safe_url($_SERVER['HTTP_REFERER'], true);

			return pc_base::load_sys_class('input')->log($level, $msg."\n#SQL：{sql}\n#URL：{url}\n#AGENT：{user}\n".($context['referer'] ? "#REFERER：{referer}\n" : "")."{trace}\n", $context);
		}

		$message.= '---'.FC_NOW_URL.PHP_EOL;
		return pc_base::load_sys_class('input')->log($level, $message, $context);
	}

	/**
	 * Determines the view to display based on the exception thrown,
	 * whether an HTTP or CLI request, etc.
	 *
	 * @return string The path and filename of the view file to use
	 */
	protected function determineView($exception, string $templatePath): string {
		$view = 'production.php';
		$templatePath = rtrim($templatePath, '\\/ ') . DIRECTORY_SEPARATOR;

		if (str_ireplace(['off', 'none', 'no', 'false', 'null'], '', ini_get('display_errors'))) {
			$view = 'error_exception.php';
		}

		// 404 Errors
		if ($exception instanceof OutOfBoundsException) {
			return 'error_404.php';
		}

		// Allow for custom views based upon the status code
		if (is_file($templatePath . 'error_' . $exception->getCode() . '.php')) {
			return 'error_' . $exception->getCode() . '.php';
		}

		return $view;
	}

	/**
	 * Gathers the variables that will be made available to the view.
	 */
	protected function collectVars($exception): array {
		$trace = $exception->getTrace();
		return [
			'title' => get_class($exception),
			'type' => get_class($exception),
			'message' => $exception->getMessage(),
			'file' => $exception->getFile(),
			'line' => $exception->getLine(),
			'trace' => $trace,
		];
	}

	/**
	 * Creates a syntax-highlighted version of a PHP file.
	 *
	 * @return bool|string
	 */
	public static function highlightFile(string $file, int $lineNumber, int $lines = 15) {
		if (empty($file) || ! is_readable($file)) {
			return false;
		}

		// Set our highlight colors:
		if (function_exists('ini_set')) {
			ini_set('highlight.comment', '#767a7e; font-style: italic');
			ini_set('highlight.default', '#c7c7c7');
			ini_set('highlight.html', '#06B');
			ini_set('highlight.keyword', '#f1ce61;');
			ini_set('highlight.string', '#869d6a');
		}

		try {
			$source = file_get_contents($file);
		} catch (Throwable $e) {
			return false;
		}

		$source = str_replace(["\r\n", "\r"], "\n", $source);
		$source = explode("\n", highlight_string($source, true));

		if (PHP_VERSION_ID < 80300) {
			$source = str_replace('<br />', "\n", $source[1]);
			$source = explode("\n", str_replace("\r\n", "\n", $source));
		} else {
			// We have to remove these tags since we're preparing the result
			// ourselves and these tags are added manually at the end.
			$source = str_replace(['<pre><code>', '</code></pre>'], '', $source);
		}

		// Get just the part to show
		$start = max($lineNumber - (int) round($lines / 2), 0);

		// Get just the lines we need to display, while keeping line numbers...
		$source = array_splice($source, $start, $lines, true);

		// Used to format the line number in the source
		$format = '% ' . strlen((string) ($start + $lines)) . 'd';

		$out = '';
		// Because the highlighting may have an uneven number
		// of open and close span tags on one line, we need
		// to ensure we can close them all to get the lines
		// showing correctly.
		$spans = 1;

		foreach ($source as $n => $row) {
			$spans += substr_count($row, '<span') - substr_count($row, '</span');
			$row = str_replace(["\r", "\n"], ['', ''], $row);

			if (($n + $start + 1) === $lineNumber) {
				preg_match_all('#<[^>]+>#', $row, $tags);

				$out .= sprintf(
					"<span class='line highlight'><span class='number'>{$format}</span> %s\n</span>%s",
					$n + $start + 1,
					strip_tags($row),
					implode('', $tags[0])
				);
			} else {
				$out .= sprintf('<span class="line"><span class="number">' . $format . '</span> %s', $n + $start + 1, $row) . "\n";
			}
		}

		if ($spans > 0) {
			$out .= str_repeat('</span>', $spans);
		}

		return '<pre><code>' . $out . '</code></pre>';
	}

	private function isDeprecationError(int $error): bool {
		$deprecations = E_DEPRECATED | E_USER_DEPRECATED;

		return ($error & $deprecations) !== 0;
	}

	/**
	 * Handles session.sid_length and session.sid_bits_per_character deprecations
	 * in PHP 8.4.
	 */
	private function isSessionSidDeprecationError(string $message, ?string $file = null, ?int $line = null): bool {
		if (
			PHP_VERSION_ID >= 80400
			&& strpos($message, 'session.sid_') !== false
		) {
			log_message(
				'warning',
				'[DEPRECATED] {message} in {errFile} on line {errLine}.',
				[
					'message' => $message,
					'errFile' => IS_DEV ? ($file ? $file : '') : self::cleanPath($file ? $file : ''),
					'errLine' => $line ? $line : 0,
				]
			);

			return true;
		}

		return false;
	}

	/**
	 * Workaround to implicit nullable deprecation errors in PHP 8.4.
	 *
	 * "Implicitly marking parameter $xxx as nullable is deprecated,
	 *  the explicit nullable type must be used instead"
	 *
	 * @TODO remove this before v4.6.0 release
	 */
	private function isImplicitNullableDeprecationError(string $message, ?string $file = null, ?int $line = null): bool {
		if (
			PHP_VERSION_ID >= 80400
			&& strpos($message, 'the explicit nullable type must be used instead') !== false
			// Only Kint and Faker, which cause this error, are logged.
			&& (substr($message, 0, strlen('Kint\\')) === 'Kint\\' || substr($message, 0, strlen('Faker\\')) === 'Faker\\')
		) {
			log_message(
				'warning',
				'[DEPRECATED] {message} in {errFile} on line {errLine}.',
				[
					'message' => $message,
					'errFile' => IS_DEV ? ($file ? $file : '') : self::cleanPath($file ? $file : ''),
					'errLine' => $line ? $line : 0,
				]
			);

			return true;
		}

		return false;
	}

	/**
	 * @return true
	 */
	private function handleDeprecationError(string $message, ?string $file = null, ?int $line = null): bool {
		// Remove the trace of the error handler.
		$trace = array_slice(debug_backtrace(), 2);

		log_message(
			'warning',
			"[DEPRECATED] {message} in {errFile} on line {errLine}.\n{trace}",
			[
				'message' => $message,
				'errFile' => IS_DEV ? ($file ? $file : '') : self::cleanPath($file ? $file : ''),
				'errLine' => $line ? $line : 0,
				'trace'   => self::renderBacktrace($trace),
			]
		);

		return true;
	}

	private static function renderBacktrace(array $backtrace): string {
		$backtraces = [];

		foreach ($backtrace as $index => $trace) {
			$frame = $trace + ['file' => '[internal function]', 'line' => '', 'class' => '', 'type' => '', 'args' => []];

			if ($frame['file'] !== '[internal function]') {
				$frame['file'] = sprintf('%s(%s)', $frame['file'], $frame['line']);
			}

			unset($frame['line']);
			$idx = $index;
			$idx = str_pad((string) ++$idx, 2, ' ', STR_PAD_LEFT);

			$args = implode(', ', array_map(static function ($value): string {
				switch (true) {
					case is_object($value):
						return sprintf('Object(%s)', get_class($value));

					case is_array($value):
						return $value !== [] ? '[...]' : '[]';

					case $value === null:
						return 'null';

					case is_resource($value):
						return sprintf('resource (%s)', get_resource_type($value));

					default:
						return var_export($value, true);
				}
			}, $frame['args']));

			$backtraces[] = sprintf(
				'%s %s: %s%s%s(%s)',
				$idx,
				IS_DEV ? $frame['file'] : self::cleanPath($frame['file']),
				$frame['class'],
				$frame['type'],
				$frame['function'],
				$args
			);
		}

		return implode("\n", $backtraces);
	}

	/**
	 * This makes nicer looking paths for the error output.
	 *
	 * @deprecated Use dedicated `clean_path()` function.
	 */
	public static function cleanPath(string $file): string {
		switch (true) {
			case strpos($file, PC_PATH) === 0:
				$file = 'PC_PATH' . DIRECTORY_SEPARATOR . substr($file, strlen(PC_PATH));
				break;

			case strpos($file, CACHE_PATH) === 0:
				$file = 'CACHE_PATH' . DIRECTORY_SEPARATOR . substr($file, strlen(CACHE_PATH));
				break;

			case strpos($file, CONFIGPATH) === 0:
				$file = 'CONFIGPATH' . DIRECTORY_SEPARATOR . substr($file, strlen(CONFIGPATH));
				break;

			case defined('CMS_PATH') && strpos($file, CMS_PATH) === 0:
				$file = 'CMS_PATH' . DIRECTORY_SEPARATOR . substr($file, strlen(CMS_PATH));
				break;
		}

		return $file;
	}

	/**
	 * Describes memory usage in real-world units. Intended for use
	 * with memory_get_usage, etc.
	 */
	public static function describeMemory(int $bytes): string {
		if ($bytes < 1024) {
			return $bytes . 'B';
		}

		if ($bytes < 1048576) {
			return round($bytes / 1024, 2) . 'KB';
		}

		return round($bytes / 1048576, 2) . 'MB';
	}

	public static function error(string $text, string $foreground = 'light_red', string $background = null) {
		// Check color support for STDERR
		$stdout = static::$isColored;
		static::$isColored = static::hasColorSupport(STDERR);

		if ($foreground || $background) {
			$text = static::color($text, $foreground, $background);
		}

		static::fwrite(STDERR, $text . PHP_EOL);

		// return STDOUT color support
		static::$isColored = $stdout;
	}

	public static function hasColorSupport($resource): bool {
		if (isset($_SERVER['NO_COLOR']) || getenv('NO_COLOR') !== false) {
			return false;
		}

		if (getenv('TERM_PROGRAM') === 'Hyper') {
			return true;
		}

		if (static::is_windows()) {
			return static::streamSupports('sapi_windows_vt100_support', $resource)
				|| isset($_SERVER['ANSICON'])
				|| getenv('ANSICON') !== false
				|| getenv('ConEmuANSI') === 'ON'
				|| getenv('TERM') === 'xterm';
		}

		return static::streamSupports('stream_isatty', $resource);
	}

	public static function streamSupports(string $function, $resource): bool {
		if (is_cli()) {
			// In the current setup of the tests we cannot fully check
			// if the stream supports the function since we are using
			// filtered streams.
			return function_exists($function);
		}

		return function_exists($function) && @$function($resource); // @codeCoverageIgnore
	}

	public static function color(string $text, string $foreground, string $background = null, string $format = null): string {
		if (! static::$isColored || $text === '') {
			return $text;
		}

		if (! array_key_exists($foreground, static::$foreground_colors)) {
			throw 'Invalid foreground color: '.$foreground.'.';
		}

		if ($background !== null && ! array_key_exists($background, static::$background_colors)) {
			throw 'Invalid background color: '.$background.'.';
		}

		$newText = '';

		// Detect if color method was already in use with this text
		if (strpos($text, "\033[0m") !== false) {
			$pattern = '/\\033\\[0;.+?\\033\\[0m/u';

			preg_match_all($pattern, $text, $matches);
			$coloredStrings = $matches[0];

			// No colored string found. Invalid strings with no `\033[0;??`.
			if ($coloredStrings === []) {
				return $newText . self::getColoredText($text, $foreground, $background, $format);
			}

			$nonColoredText = preg_replace(
				$pattern,
				'<<__colored_string__>>',
				$text
			);
			$nonColoredChunks = preg_split(
				'/<<__colored_string__>>/u',
				$nonColoredText
			);

			foreach ($nonColoredChunks as $i => $chunk) {
				if ($chunk !== '') {
					$newText .= self::getColoredText($chunk, $foreground, $background, $format);
				}

				if (isset($coloredStrings[$i])) {
					$newText .= $coloredStrings[$i];
				}
			}
		} else {
			$newText .= self::getColoredText($text, $foreground, $background, $format);
		}

		return $newText;
	}

	private static function getColoredText(string $text, string $foreground, string $background, string $format): string {
		$string = "\033[" . static::$foreground_colors[$foreground] . 'm';

		if ($background !== null) {
			$string .= "\033[" . static::$background_colors[$background] . 'm';
		}

		if ($format === 'underline') {
			$string .= "\033[4m";
		}

		return $string . $text . "\033[0m";
	}

	public static function write(string $text = '', string $foreground = null, string $background = null) {
		if ($foreground || $background) {
			$text = static::color($text, $foreground, $background);
		}

		if (static::$lastWrite !== 'write') {
			$text = PHP_EOL . $text;
			static::$lastWrite = 'write';
		}

		static::fwrite(STDOUT, $text . PHP_EOL);
	}

	public static function newLine(int $num = 1) {
		// Do it once or more, write with empty string gives us a new line
		for ($i = 0; $i < $num; $i++) {
			static::write();
		}
	}

	protected static function fwrite($handle, string $string) {
		if (! is_cli()) {
			// @codeCoverageIgnoreStart
			echo $string;

			return;
			// @codeCoverageIgnoreEnd
		}

		fwrite($handle, $string);
	}

	protected static function is_windows(?bool $mock = null): bool {
		static $mocked;

		if (func_num_args() === 1) {
			$mocked = $mock;
		}

		return $mocked ?? DIRECTORY_SEPARATOR === '\\';
	}
}